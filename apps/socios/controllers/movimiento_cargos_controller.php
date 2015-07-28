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
 * Movimiento_CargosController
 *
 * Controlador de generacion de moviminetos de cargos mensuales
 *
 */
class Movimiento_CargosController extends ApplicationController {

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
		$periodo = $this->Periodo->minimum('periodo','conditions: cierre="N"');
		$periodo = SociosCore::checkPeriodo($periodo);
		$this->setParamToView('mes',$periodo);
		$this->setParamToView('message', 'De click en Generar Movimientos de Cargos');
	}

	/**
	 * Generar movimiento de cargos fijos mensuales a socios para facturar
	 */
	public function generarAction(){
		$this->setResponse('json');
		
		$periodoNum = $this->getPostParam('periodo','int');
		if(!$periodoNum){
			return array(
				'status' => 'FAILED',
				'message' => 'Es necesario dar el periodo para generar cargos mensuales'
			);
		}
		$debug=true;
		set_time_limit(0);
		try{
			$transaction		= TransactionManager::getUserTransaction();
			
			Core::importFromLibrary('Hfos/Socios','SociosFactura.php');
			$configMovimiento	= array();
			SociosFactura::generarMovimiento($configMovimiento, $transaction);
			
			$transaction->commit();
			
			return array(
				'status' => 'OK',
				'message' => 'Los movimientos han sido generados exitosamente en el periodo '.$configMovimiento['periodoAbierto'].'.'
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
