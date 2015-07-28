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
 * ProyeccionController
 *
 * Clase controller que visualiza la proyección de el contrato de un socio
 *
 */
class ProyeccionController extends ApplicationController {

    public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	/**
	 * Metodo que carga cosas al ingresar al formulario de inicio
	 *
	 */
	public function indexAction(){
		$this->setParamToView('message', 'Indique los datos para generar amortización');
		Tag::displayTo('cuotaInicial', 0);
		Tag::displayTo('saldoPagar', 0);
		Tag::displayTo('fechaPrimeraCuota', Date::getCurrentDate());
	}

	/**
	 * Metodo que genera tabla de amortizacion
	 *
	 * @return json
	 */
	public function generarAction(){
		Core::importFromLibrary('Hfos/Tpc','Tpc.php');
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();
		try {
			$rules = array(
				'valorTotal' => array(
					'message' => 'Debe indicar el valor total',
					'filter' => 'double'
				),
				'cuotaInicial' => array(
					'message' => 'Debe indicar la cuota inicial',
					'filter' => 'double'
				),
				'saldoPagar' => array(
					'message' => 'Debe indicar el saldo a pagar',
					'filter' => 'double'
				),
				'numCuotas' => array(
					'message' => 'Debe indicar el número de cuotas',
					'filter' => 'int'
				),
				'interesCorriente' => array(
					'message' => 'Debe indicar el interés corriente',
					'filter' => 'double'
				),
				'fechaPrimeraCuota' => array(
					'message' => 'Debe indicar la fecha de la primera cuota',
					'filter' => 'date'
				)
			);
			if($this->validateRequired($rules)==false){
				foreach($this->getValidationMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

		    $valorTotal = $this->getPostParam('valorTotal', 'double');
		    if(!$valorTotal){
		        $this->addValidationMessage($rules['valorTotal']['message'],'valorTotal');
		    }
		    $cuotaInicial = $this->getPostParam('cuotaInicial', 'double');
		    if(!$cuotaInicial){
		        $this->addValidationMessage($rules['cuotaInicial']['message'],'cuotaInicial');
		    }
		    $saldoPagar = $this->getPostParam('saldoPagar', 'double');
		    if(!$saldoPagar){
		        $this->addValidationMessage($rules['saldoPagar']['message'],'saldoPagar');
		    }
		    $numCuotas = $this->getPostParam('numCuotas', 'int');
		    if(!$numCuotas){
		        $this->addValidationMessage($rules['numCuotas']['message'],'numCuotas');
		    }
		    $interesCorriente = $this->getPostParam('interesCorriente', 'double');
		    $fechaPrimeraCuota = $this->getPostParam('fechaPrimeraCuota', 'date');
		    if(!$fechaPrimeraCuota){
		        $this->addValidationMessage(
		          $rules['fechaPrimeraCuota']['message'],'fechaPrimeraCuota'
		        );
		    }
		    foreach($this->getValidationMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
		
			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('PROYECCIÓN DE AMORTIZACIÓN', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo));

			$report->setDocumentTitle('Proyección de Amortización');
			$report->setColumnHeaders(array(
				'CUOTA',
				'FECHA',
				'CUOTA FIJA',
				'CAPITAL',
				'INTERES',
				'SALDO'
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->setColumnStyle(array(0,1), new ReportStyle(array(
				'textAlign' => 'center',
				'fontSize' => 11
			)));

			$report->setColumnStyle(array(2, 3, 4, 5), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(array(2, 3, 4, 5), new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 0
			)));

			$report->setTotalizeColumns(array(2, 3, 4, 5));

			$report->start(true);

			$empresa = $this->Empresa->findFirst();

			$amortizacion = array();
			$totalValor = 0;
			$totalCapital = 0;
			$totalInteres = 0;
			$totalSaldo = 0;
			$interesUsura2 = TPC::getTasaDeMora($fechaPrimeraCuota,array("debug"=>false), $transaction);

			$dataAmortizacion = array(
				"valorFinanciacion"     => $saldoPagar,//3900000
				"valorTotalCompra"      => $valorTotal,//3900000
				"fechaCompra"           => $fechaPrimeraCuota,//"29-06-2010"
				"fechaPagoFinanciacion" => $fechaPrimeraCuota,//"30-09-2010"
				"plazoMeses"            => $numCuotas,//24
				"tasaMesVencido"        => $interesCorriente,//1.8
				"tasaMora"              => $interesUsura2,
				"debug"					=> false
			);

			$arrayAmortizacionNew = TPC::generarAmortizacion($dataAmortizacion);

			/*return array(
				'status' => 'FAILED',
				'message' => 'finish test<pre>'.print_r($arrayAmortizacionNew,true).'</pre>'
			);*/

			/**
			 * 'CUOTA',
				'FECHA',
				'CAPITAL',
				'INTERES',
				'SALDO'
			 */
			foreach($arrayAmortizacionNew as $a){
			   $report->addRow(array(
				   $a['cuota'],
				   $a['periodo'],
				   $a['cuotaFija'],
				   $a['abonoCapital'],
				   $a['intereses'],
				   $a['saldo']
			   ));
			   $totalValor+=$a['cuotaFija'];
			   $totalCapital+=$a['abonoCapital'];
			   $totalInteres+=$a['intereses'];
			   $totalSaldo+=$a['saldo'];
			}
			$report->setTotalizeValues(array(
			  2 => $totalValor,
			  3 => $totalCapital,
			  4 => $totalInteres,
			  5 => $totalSaldo
			));

			$report->finish();
			$fileName = $report->outputToFile('public/temp/proyeccion');

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

}
