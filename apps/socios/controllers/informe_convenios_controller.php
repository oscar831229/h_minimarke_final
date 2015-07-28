<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * Informe_ConveniOscontroller
 *
 * Controlador del informe de estado de cuenta de convenios
 */
class Informe_ConveniOscontroller extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		$fechaCierre->addDays(1);

		Tag::displayTo('fechaLimite', Date::getCurrentDate());
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');

		try
		{

			$fechaLimite = $this->getPostParam('fechaLimite', 'date');
			$sociosId = $this->getPostParam('socios_id', 'int');
			$activos = $this->getPostParam('activos', 'int');

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

	  		$titulo = new ReportText('ESTADO DE CUENTA DE CONVENIOS', array(
				'fontSize' => 16,
	   			'fontWeight' => 'bold',
	   			'textAlign' => 'center'
	  		));

	 		$titulo2 = new ReportText('Documento generado hasta la fecha: '.$fechaLimite, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
	 		));

	  		$report->setHeader(array($titulo, $titulo2));
	  		$report->setDocumentTitle('Estado de cuenta de convenios');
	  		$report->setColumnHeaders(array(
	  			'COMPROB.',
	  			'NÚMERO',
	  			'CUENTA',
	  			'FECHA',
	  			'VALOR',
	  			'SALDO'
	  		));

	  		$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$leftColumn = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			));

			$leftColumnBold = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11,
				'fontWeight' => 'bold'
			));

			$rightColumn = new ReportStyle(array(
	  			'textAlign' => 'right',
	  			'fontSize' => 11,
	  		));

	  		$rightColumnBold = new ReportStyle(array(
	  			'textAlign' => 'right',
	  			'fontSize' => 11,
				'fontWeight' => 'bold'
	  		));

	  		$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$report->setColumnStyle(array(0, 1, 2, 3), $leftColumn);
	  		$report->setColumnStyle(array(4,5), $rightColumn);
			$report->setColumnFormat(array(4,5), $numberFormat);

			$report->start(true);

			$cuentaAnterior = '';
			$terceroAnterior = '';
			$totalCuenta = array();
			$totalTercero = array();
			$tercerosArray = array();

			$conditionsSocios = "socios_id>0";
			if ($sociosId>0) {
				$conditionsSocios = "socios_id='$sociosId'";
			}
			if ($activos>0) {
				$conditionsSocios .= " AND estados_socios_id=1";
			}

			$sociosObj = $this->Socios->find(array($conditionsSocios,'order'=>'numero_accion,identificacion'));
			if (!count($sociosObj)) {
				throw new Exception("No hay socios que buscar. ".$conditionsSocios);
			}

			$granTotalSaldo = 0;
			$granTotalValor = 0;
				
			foreach($sociosObj as $socios) 
			{
				$conditions = "socios_id='{$socios->getSociosId()}' AND estado='D'";

				$prestamosSociosObj = $this->PrestamosSocios->find(array($conditions, 'order' => 'fecha_prestamo'));
				if (!count($prestamosSociosObj)) {
					continue;
				}

				$columnaCuenta = new ReportRawColumn(array(
					'value' => $socios->getNumeroAccion().' / ('.$socios->getIdentificacion().') '.$socios->getNombres().' '.$socios->getApellidos(),
					'style' => $leftColumnBold,
					'span' => 15
				));
				$report->addRawRow(array($columnaCuenta));

				$totalSaldo = 0;
				$totalValor = 0;
				foreach ($prestamosSociosObj as $prestamosSocios)
				{

					$codigoCuenta = $prestamosSocios->getCuenta();

					$cuenta = BackCacher::getCuenta($codigoCuenta);
					if ($cuenta==false) {
						return array(
							'status' => 'FAILED',
							'message' => "No existe la cuenta '$codigoCuenta' en el plan contable"
						);
					}

					$codigoComprob = '';
					$numeroComprob = 0;

					$saldoConvenio = $prestamosSocios->getValorFinanciacion();

					$conditionsMovi = "cuenta='$codigoCuenta' AND
					nit='{$socios->getIdentificacion()}' AND deb_cre='C' AND fecha<='$fechaLimite'";
					$movis = $this->Movi->find(array('conditions'=>$conditionsMovi));
					foreach ($movis as $movi)
					{
						$codigoComprob = $movi->getComprob();
						$numeroComprob = $movi->getNumero();
						
						$valor = $movi->getValor();
						$saldoConvenio -= $valor;

						$row = array(
							$codigoComprob,
							$numeroComprob,
							$codigoCuenta.' / '.$cuenta->getNombre(),
							$movi->getFecha(),
							$movi->getValor(), //5
							$saldoConvenio
						);
						$report->addRow($row);
						$totalValor += $movi->getValor();

						unset($movi);
					}
					unset($movis,$conditionsMovi);
					
					$totalSaldo += $saldoConvenio;
					
					unset($codigoComprob,$numeroComprob);			
				}

				$row = array(
					'', //0
					'', //1
					'', //1
					'', //2
					$totalValor, 
					$totalSaldo
				);
				$report->addRow($row);

				$granTotalSaldo += $totalSaldo;
				$granTotalValor += $totalValor;
			}

			$columnaTotalCuenta = new ReportRawColumn(array(
				'value' => 'TOTAL CONVENIO PENDIENTE',
				'style' => $leftColumnBold,
				'span' => 6
			));

			$report->addRawRow(array($columnaTotalCuenta));

			$row = array(
				'', //0
				'', //1
				'', //2
				'', //3
				$granTotalValor,
				$granTotalSaldo
			);
			$report->addRow($row);

			$report->finish();
			$fileName = $report->outputToFile('public/temp/informe-convenios-socios');

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);	
		}

	}
}
