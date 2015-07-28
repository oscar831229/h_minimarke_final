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
 * Conceptos_CausadosController
 *
 * Controlador de las conceptos causados en un periodo
 */
class Conceptos_CausadosController extends ApplicationController {

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
	public function indexAction(){
		$this->setParamToView('message', 'Seleccione los criterios de busqueda');
		$periodos = EntityManager::get('Periodo')->find(array('order'=>'periodo DESC')); 
		$this->setParamToView('periodos', $periodos);
	}

	/**
	 * Cambia el numero de accion de un socio
	 *
	 * 
	 */
	public function generarAction(){
	    $this->setResponse('json');

	    try {

	    	$transaction = TransactionManager::getUserTransaction();

	    	//Parametros de busqueda
		    $periodoStr= $this->getPostParam('periodo', 'int');
		    //detallado
		    $detallado= $this->getPostParam('detallado', 'int');


			$reportType	= $this->getPostParam('reportType', 'alpha');
			//$reportType	= 'html';
			
			//TITULO PRINCIPAL
			$headers[]= new ReportText('RESUMEN DE CONCEPTOS CAUSADOS '.$periodoStr, array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$headers[]= new ReportText('Fecha de emisión: '.date('Y-m-d'), array(
				'fontSize' => 13,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$i = 1;

			$report	= ReportBase::factory($reportType);
			$report->setHeader($headers);
			$report->setDocumentTitle('Resumen de Conceptos Causados');
			$report->setColumnHeaders(array(
				'#',//0
				'No. Acción',//1
				'Nombre',//2
				'CC y/o NIT',//3
				'No. Factura',//4
				'Fecha',//5
				'Concepto',//6
				'Valor Total Causado'//7
			));
			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));
			$report->setColumnStyle(array(0,1,2,3,4,5,6), new ReportStyle(array(
				'textAlign' => 'center',
				'fontSize' => 11
			)));
			$report->setColumnStyle(array(7), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11
			)));
			$report->setColumnFormat(array(7), new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 0
			)));

			$report->start(true);

			$total=0;

			//Buscamos segun periodo
			$facturaObj = EntityManager::get('Factura')->find(array("periodo='$periodoStr'"));
			foreach($facturaObj as $factura){

				$sociosId = $factura->getSociosId();

				//Get info de socio
				$socio = BackCacher::getSocios($sociosId);

				if($socio==false){
					continue;
				}

				//SUM MOVIMIENTO
				$valorTotal = 0;
				$movimiento = EntityManager::get('Movimiento')->findFirst(array("socios_id='$sociosId' AND periodo='$periodoStr'"));
				if (!$movimiento) {
					continue;
				}
				
				$detalleMovimientoObj = EntityManager::get('DetalleMovimiento')->find(array("descripcion NOT LIKE 'SALDO PERIODO%' AND movimiento_id='{$movimiento->getId()}' AND estado!='I'", 'order'=>'descripcion ASC'));
				foreach ($detalleMovimientoObj as $detalleMovimiento) 
				{
					if ($detallado) {
						$report->addRow(array(
							$i,
							$socio->getNumeroAccion(),
							$socio->getNombres().' '.$socio->getApellidos(),
							$socio->getIdentificacion(),
							$factura->getComprobContab().'-'.$factura->getNumeroContab(),
							$factura->getFechaFactura(),
							$detalleMovimiento->getDescripcion(),
							$detalleMovimiento->getTotal()
						));
					}
					
					$valorTotal += $detalleMovimiento->getTotal();
					unset($detalleMovimiento);
				}
				unset($detalleMovimientoObj);

				if (!$detallado) {
					//Add new row
					$report->addRow(array(
						$i,
						$socio->getNumeroAccion(),
						$socio->getNombres().' '.$socio->getApellidos(),
						$socio->getIdentificacion(),
						$factura->getComprobContab().'-'.$factura->getNumeroContab(),
						$factura->getFechaFactura(),
						'TOTAL FACTURA',
						$valorTotal
					));
				} else {
					if ($valorTotal>0) {
						$report->addRow(array(
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							$valorTotal
						));
						$columnaBr = new ReportRawColumn(array(
			                'value' => '',
			                'span' => 8
			            ));
						$report->addRawRow(array($columnaBr));
					}
				}
				$i++;

				$total+= $valorTotal;

				unset($factura, $valorTotal, $socios, $sociosociosId);
			}

			unset($facturaObj);

			//Total
			$report->addRow(array(
				'',
				'',
				'',
				'',
				'',
				'',
				'TOTAL CONCEPTOS CAUSADOS',
				$total
			));

			$report->finish();
			$config['file']= $report->outputToFile('public/temp/conceptos_causados');

			return array(
				'status' 	=> 'OK',
				'message' 	=> 'Se genero el informe exitosamente',
				'file'		=> 'temp/'.$config['file']
			);				

		}
		catch(Exception $e){
			return array(
			    'status' 	=> 'FAILED',
			    'message' 	=> $e->getMessage()
			);
		}
		
	}

}
