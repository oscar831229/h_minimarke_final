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
 * LiquidacionController
 *
 * Controlador de liquidacion quincenal
 *
 */
class LiquidacionController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$ultimoPago = $this->Empresa->findFirst()->getFCierren();
		$this->setParamToView('ultimoPago', $ultimoPago);

		$contratos = array();
		foreach($this->Contratos->find(array("estado='A'")) as $contrato){
			$empleado = BackCacher::getEmpleado($contrato->getEmpleadosId());
			$contratos[$contrato->getId()] = $empleado->getNombreCompleto();
		}
		asort($contratos);
		$this->setParamToView('contratos', $contratos);

		$this->setParamToView('message', 'Haga click en "Hacer Liquidación" para hacer la liquidación');
	}

	public function generarAction(){

		$this->setResponse('json');

		try {

			$contratoInicial = $this->getPostParam('contratoInicial', 'int');
			$contratoFinal = $this->getPostParam('contratoFinal', 'int');

			list($contratoInicial, $contratoFinal) = Utils::sortRange($contratoInicial, $contratoFinal);

			$clara = new Clara();
			$clara->setRangoContratos($contratoInicial, $contratoFinal);
			$clara->liquidarQuincenal();

		}
		catch(ClaraException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

		return array(
			'status' => 'OK'
		);
	}

}