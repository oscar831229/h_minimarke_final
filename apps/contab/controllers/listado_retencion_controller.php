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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Listado_RetencionController
 *
 * Listado de Retencion
 *
 */
class Listado_RetencionController extends ApplicationController {

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

		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');

	}

	public function generarAction()
	{

		$this->setResponse('json');
		try
		{
			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			if ($fechaInicial==''||$fechaFinal=='') {
				return array(
					'status' => 'FAILED',
					'message' => 'Indique las fechas inicial y final del listado de retención'
				);
			}

			$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

			$nitInicial = $this->getPostParam('nitInicial', 'terceros');
			$nitFinal = $this->getPostParam('nitFinal', 'terceros');

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('LISTADO DE RETENCIÓN', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Listado de Retención');
			$report->setColumnHeaders(array(
				'NO. DOCUMENTO',
				'NOMBRE',
				'FECHA',
				'COMPROBANTE',
				'DESCRIPCIÓN',
				'DEBITOS',
				'CRÉDITOS',
				'BASE',
				''
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

			$report->setColumnStyle(array(0, 1, 2, 3, 4), $leftColumn);

			$report->setColumnStyle(array(5, 6), $rightColumn);
			$report->setColumnFormat(array(5, 6), $numberFormat);

			$columnaTotalCuenta = new ReportRawColumn(array(
				'value' => 'TOTAL CUENTA',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$report->start(true);

			$retencion = array();
			if ($cuentaInicial!=''&&$cuentaFinal!='') {
				$conditions = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND es_auxiliar='S' AND pide_nit='S'";
			} else {
				$conditions = "es_auxiliar='S' AND pide_nit='S'";
			}
			$cuentas = $this->Cuentas->find(array($conditions, 'columns' => 'cuenta', 'order' => 'cuenta'));
			foreach ($cuentas as $cuenta)
			{
				$codigoCuenta = $cuenta->getCuenta();
				if($nitInicial!=''&&$nitFinal!=''){
					$conditions = "nit>='$nitInicial' AND nit<='$nitFinal' AND cuenta='$codigoCuenta' AND ano_mes=0";
				} else {
					$conditions = "cuenta='$codigoCuenta' AND ano_mes=0";
				}
				foreach ($this->Saldosn->find($conditions) as $saldon)
				{
					$retencion[$codigoCuenta][$saldon->getNit()] = true;
					unset($saldon);
				}
				unset($cuenta);
			}
			unset($cuentas);

			foreach ($retencion as $codigoCuenta => $nitCuentas)
			{
				$totalDebitos = 0;
				$totalCreditos = 0;
				$totalBase = 0;
				$encabezadoCuenta = false;
				$cuenta = BackCacher::getCuenta($codigoCuenta);
				foreach ($nitCuentas as $nitTercero => $one)
				{
					$tercero = BackCacher::getTercero($nitTercero);
					if ($tercero==false) {
						$tercero = new Nits();
						$tercero->setNombre('NO EXISTE TERCERO');
					}
					$movis = $this->Movi->find("nit='$nitTercero' AND cuenta='$codigoCuenta' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'");
					foreach ($movis as $movi)
					{
						if ($encabezadoCuenta==false) {
							$columnaCuenta = new ReportRawColumn(array(
								'value' => $cuenta->getCuenta().' : '.$cuenta->getNombre(),
								'style' => $leftColumnBold,
								'span' => 9
							));
							$report->addRawRow(array($columnaCuenta));
							$encabezadoCuenta = true;
						}
						$base = $movi->getBaseGrab()*$cuenta->getPorcIva();

						if ($base!=$movi->getValor()) {
							$baseMark = '*';
						} else {
							$baseMark = '';
						}
						if ($movi->getDebCre()=='D') {
							$report->addRow(array(
								$nitTercero,
								$tercero->getNombre(),
								$movi->getFecha(),
								$movi->getComprob().'-'.$movi->getNumero(),
								$movi->getDescripcion(),
								$movi->getValor(),
								0,
								//$base,
								Currency::money($movi->getBaseGrab()),
								$baseMark
							));
							$totalDebitos+=$movi->getValor();
						} else {
							$report->addRow(array(
								$nitTercero,
								$tercero->getNombre(),
								$movi->getFecha(),
								$movi->getComprob().'-'.$movi->getNumero(),
								$movi->getDescripcion(),
								0,
								$movi->getValor(),
								//$base,
								Currency::money($movi->getBaseGrab()),
								$baseMark
							));
							$totalCreditos+=$movi->getValor();
						}
						$totalBase+=$movi->getBaseGrab();
						unset($movi);
					}
					unset($movis,$one,$tercero);
				}
				if ($encabezadoCuenta==true) {
					$columnaTotalDebitos = new ReportRawColumn(array(
						'value' => $totalDebitos,
						'style' => $rightColumn,
						'format' => $numberFormat
					));
					$columnaTotalCreditos = new ReportRawColumn(array(
						'value' => $totalCreditos,
						'style' => $rightColumn,
						'format' => $numberFormat
					));
					$columnaTotalBase = new ReportRawColumn(array(
						'value' => $totalBase,
						'style' => $rightColumn,
						'format' => $numberFormat
					));
					$report->addRawRow(array(
						$columnaTotalCuenta,
						$columnaTotalDebitos,
						$columnaTotalCreditos,
						$columnaTotalBase
					));
					unset($columnaTotalDebitos);
					unset($columnaTotalCreditos);
					unset($columnaTotalBase);
				}
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/retencion');
			unset($report);
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
