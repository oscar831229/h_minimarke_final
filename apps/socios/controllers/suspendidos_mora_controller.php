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
 * Suspendidos_MoraController
 *
 * Controlador de los socios suspendidos por mora
 *
 */
class Suspendidos_MoraController extends ApplicationController {

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
	 * Reporte de suspendidos por mora
	 * 
	 */
	public function generarAction(){
	    $this->setResponse('json');

	    try {

	    	$transaction		= TransactionManager::getUserTransaction();

	    	//Parametros de busqueda
		    $periodoStr= $this->getPostParam('periodo', 'int');

			$reportType	= $this->getPostParam('reportType', 'alpha');
			//$reportType	= 'html';
			
			$headers = array();

			//TITULO PRINCIPAL
			$headers[]= new ReportText('INFORME DE SOCIOS SUSPENDIDOS POR MORA', array(
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

			$report = ReportBase::factory($reportType);
            $report->setHeader($headers);
			$report->setDocumentTitle('Informe de Socios Suspendidos por Mora');
			$report->setColumnHeaders(array(
				'NUM.',
				'NÚMERO DE ACCIÓN',
				'NOMBRE',
				'CÉDULA',
				'OBSERVACIÓN',
				'ESTADO'
			));
			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));
			$report->setColumnStyle(array(0,1,2,3,4,5), new ReportStyle(array(
				'textAlign' => 'center',
				'fontSize' => 11
			)));
			$report->start(true);


			//Buscamos segun periodo
			$suspendidosObj = EntityManager::get('Suspendidos')->find(array("periodo='$periodoStr'"));
			foreach($suspendidosObj as $suspendidos){

				$sociosId = $suspendidos->getSociosId();

				//Get info de socio
				$socio = BackCacher::getSocios($sociosId);

				if($socio==false){
					continue;
				}

				$estado = 'Desconnocido';
				if ($socio->getEstadosSocios()) {
					$estado = $socio->getEstadosSocios()->getNombre();
				}

				//Add new row
				$report->addRow(array(
					$i,
					$socio->getNumeroAccion(),
					$socio->getNombres().' '.$socio->getApellidos(),
					$socio->getIdentificacion(),
					$suspendidos->getObservacion(),
					$estado
				));
				$i++;
			}
			$report->finish();
			$config['file']= $report->outputToFile('public/temp/suspendidos_mora');

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
