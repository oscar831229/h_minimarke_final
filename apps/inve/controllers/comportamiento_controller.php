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
 * ComportamientoController
 *
 * Permite comparar el comportamiento del costo
 *
 */
class ComportamientoController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
	}

	public function indexAction(){
		$meses = array(
			'01' => 'ENERO',
			'02' => 'FEBRERO',
			'03' => 'MARZO',
			'04' => 'ABRIL',
			'05' => 'MAYO',
			'06' => 'JUNIO',
			'07' => 'JULIO',
			'08' => 'AGOSTO',
			'09' => 'SEPTIEMBRE',
			'10' => 'OCTUBRE',
			'11' => 'NOVIEMBRE',
			'12' => 'DICIEMBRE'
		);
		$periodos = array(0 => 'ACTUAL');
		foreach($this->Saldos->distinct(array("ano_mes", "conditions" => "ano_mes>0", "order" => "1 DESC")) as $anoMes){
			$mes = substr($anoMes, 4, 2);
			$periodos[$anoMes] = $meses[$mes].' '.substr($anoMes, 0, 4);
		}
		$this->setParamToView('periodos', $periodos);

		$this->setParamToView('message', 'Agregue referencias y haga en click en "Ver" para consultar sus comportamientos');z
	}

}