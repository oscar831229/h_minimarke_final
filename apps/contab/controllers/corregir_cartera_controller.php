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
 * Corrgir_CarteraController
 *
 * Corrige cartera de un tercero con el movi
 *
 */
class Corregir_CarteraController extends ApplicationController
{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{
		$this->setParamToView('message', 'Indique el tercero y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');
		try
		{
			$nit = $this->getPostParam('nit', 'terceros');

			$auraUtils = new AuraUtils();
			$auraUtils->recalculateCarteraByNit($nit);

			return array(
				'status' => 'OK'
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
