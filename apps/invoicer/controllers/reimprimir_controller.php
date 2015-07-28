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
 * ReimprimirController
 *
 * Controlador de reimpresión de facturas
 *
 */
class ReimprimirController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$this->setParamToView('consecutivos', $this->Consecutivos->find());
	}

	public function generarAction(){
		$this->setResponse('json');
		try {
			$consecutivoId = $this->getPostParam('consecutivoId', 'int');
			$numero = $this->getPostParam('numero', 'int');

			$factura = $this->Facturas->findFirst("consecutivos_id='$consecutivoId' AND numero='$numero'");
			if($factura==false){
				return array(
					'status' => 'FAILED',
					'message' => 'No existe la factura'
				);
			}

			$invoice = Invoicing::factory('IN');//inve
			$fileName = $invoice->getPrint($factura->getId());

			return array(
				'status' => 'OK',
				'message' => 'Se reimprimió la factura correctamente',
				'uri' => $fileName
			);
		}
		catch(InvoicingException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}