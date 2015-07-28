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
 * KardexController
 *
 * Controlador del Kardex
 *
 */
class KardexController extends ApplicationController
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
		Tag::displayTo('almacen', '1');

		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrei();
		Tag::displayTo('fecha', $fechaCierre->getDate());

		$this->setParamToView('almacenes', $this->Almacenes->find('estado="A"', 'order: nom_almacen'));
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('finalYear', date('Y'));

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');

		try {
			$item = $this->getPostParam('item', 'alpha', 'extraspaces');
			$almacen = $this->getPostParam('almacen', 'alpha');
			$fecha = $this->getPostParam('fecha', 'date');
			
			$inve = BackCacher::getInve($item);
			if($inve==false){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique la referencia a consultar'
				);
			}
			$fileName = 'kardex-' . mt_rand(0, 10000) . '.html';
			$contents = TaticoKardex::show($item, $almacen, $fecha);
			$contents.= Tag::stylesheetLink('hfos/kardex');
			file_put_contents('public/temp/'.$fileName, $contents);
			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		} catch (TaticoException $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		} catch (Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}
