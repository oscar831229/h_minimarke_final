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
 * EjecucionController
 *
 * Consultas de movimiento por pantalla
 *
 */
class EjecucionController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		$fechaCierre->addMonths(1);


		$centros = array();
		$cuentas = array();
		foreach($this->Pres->find() as $pres){
			if(!isset($centros[$pres->getCentroCosto()])){
				$centro = BackCacher::getCentro($pres->getCentroCosto());
				$centros[$pres->getCentroCosto()] = $centro->getNomCentro();
			}
			if(!isset($cuentas[$pres->getCuenta()])){
				$cuenta = BackCacher::getCuenta($pres->getCuenta());
				$cuentas[$pres->getCuenta()] = $pres->getCuenta().' : '.$cuenta->getNombre();
			}
		}

		$this->setParamToView('centros', $centros);
		$this->setParamToView('cuentas', $cuentas);

		Tag::displayTo('ano', $fechaCierre->getYear());
		Tag::displayTo('mes', $fechaCierre->getMonth());
	}

}