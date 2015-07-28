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

Core::importFromLibrary('Hfos/Socios','SociosCore.php');

/**
 * Pagos_PeriodoController
 *
 * Controlador de generacion de pagos de facturas en el periodo seleccionado
 *
 */
class Pagos_PeriodoController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
	}

	
	/**
	 * Vista principal
	 *
	 */
	public function indexAction()
	{
		$periodoStr = SociosCore::getCurrentPeriodo();
		
		$periodos = EntityManager::get('Periodo')->find(array('columns'=>'periodo','order'=>'periodo DESC')); 
		$this->setParamToView('periodos', $periodos);

		$this->setParamToView('mes',$periodoStr);
	}
	
	/**
	 * Metodo que genera la(s) factura(s)
	 *
	 */
	public function reporteAction()
	{
		$this->setResponse('json');
		
		try {

			$transaction = TransactionManager::getUserTransaction();
			
			$periodo = $this->getPostParam('periodo', 'int');
			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);
			
			$titulo = new ReportText('PAGOS DE FACTURAS EN EL PERIODO', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('PERIODO: '.$periodo, array(
				'fontSize' => 14,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$leftColumnBold = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11,
				'fontWeight' => 'bold'
			));

			$report->setHeader(array($titulo, $titulo2));

			$report->setDocumentTitle('Pago de facturas en el periodo');
			$report->setColumnHeaders(array(
				'ACCION',//0
				'IDENTIFICACION',//1
				'NOMBRE',//2
				'FECHA',//3
				'COMPROB',//4
				'DOCUMENTO',//5
				'TOTAL DEBITOS',//6
				'TOTAL CREDITOS'//7
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->setColumnStyle(array(0,1,2), new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			)));

			$report->setColumnStyle(array(3,4,5), new ReportStyle(array(
				'textAlign' => 'center',
				'fontSize' => 11
			)));

			$report->setColumnStyle(array(6,7), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(array(6,7), new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 0
			)));

			$report->setTotalizeColumns(array(6,7));

			$report->start(true);

			$empresa = EntityManager::get('DatosClub')->findFirst();

			//obtenemos los pagos realizados en un periodo
			$pagos = SociosCore::getPagosPeriodo($periodo);

			$totalPagos = array('D'=>0,'C'=>0);

			foreach ($pagos as $nit => $datos)
			{
				if (!$nit) {
					continue;
				}

				$accion = '';
				$nombre = '';
				$socios = EntityManager::get('Socios')->findFirst("identificacion='$nit'");
				if ($socios) {
					$accion = $socios->getNumeroAccion();
					$nombre = $socios->getNombres().' '.$socios->getApellidos();
				}

				$fechas = '';
				if (count($datos['fecha'])) {
					$fechas = implode(", ", $datos['fecha']);
				}

				$comprobs = '';
				if (count($datos['comprob'])) {
					$comprobs = implode(", ", $datos['comprob']);
				}

				$documentos = '';
				if (count($datos['numeroDoc'])) {
					$documentos = implode(", ", $datos['numeroDoc']);
				}				

				$pago = $datos['valor'];

				$report->addRow(array(
					$accion,
					$nit,
					$nombre,
					$fechas,
					$comprobs,
					$documentos,
					$pago['D'],
					$pago['C']
				));

				$totalPagos['D'] += $pago['D'];
				$totalPagos['C'] += $pago['C'];
			}
			
			$report->setTotalizeValues(array(
				6 => $totalPagos['D'],
				7 => $totalPagos['C']
			));

			$report->finish();
			$fileName = $report->outputToFile('public/temp/pagosPeriodo');

			return array(
				'status' => 'OK',
				'message' => 'Se realizó el reporte correctamente.',
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
