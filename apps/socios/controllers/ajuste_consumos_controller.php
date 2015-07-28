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
 * Ajuste_SaldosController
 *
 * Clase controller que actualiza los saldos de un socio en cartera
 *
 */
class Ajuste_ConsumosController extends ApplicationController {

	public function initialize() {
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	/**
	 * Metodo que carga cosas al ingresar al formulario de inicio
	 *
	 */
	public function indexAction(){
		$this->setParamToView('message', 'Ingrese el archivo excel para ajustar el consumo de este periodo en punto de venta');
	}
	
	/**
	 * Metodo que genera tabla de amortizacion
	 *
	 * @return json
	 */
	public function generarAction(){
		Core::importFromLibrary('Hfos/Tpc','Tpc.php');
		Core::importFromLibrary('Hfos/Socios','SociosFactura.php');
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();
		try {
			
			$destination_path = Core::getInitialPath().'public/temp/';
			
			$result = 0;
			$target_path = $destination_path . basename( $_FILES['archivo']['name']); 
			if(@move_uploaded_file($_FILES['archivo']['tmp_name'], $target_path)) {
				 $result = 1; 
			} else {
				throw new Exception('No se pudo subir el archivo', 1);
			}
			
			$sociosFactura = new SociosFactura();
			
			$config = array(
				'date' => date('Y-m-d'),
				'file' => $target_path
			);
			$status = $sociosFactura->ajustarConsumos($config);
			
			if (!$status) {
				throw new Exception('No se pudo ajustar el saldo por favor revise el archivo', 1);
			}
			
			return array(
				'status' => 'OK',
				'file' => 'temp/'.$_FILES['archivo']['name']
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

	/**
	 * Metodo que borra los ajustes realizados en el periodo
	 *
	 * @return json
	 */
	public function borrarAction(){
		Core::importFromLibrary('Hfos/Socios','SociosFactura.php');
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();
		try {
			
			$sociosFactura = new SociosFactura();
			$status = $sociosFactura->borrarAjusteConsumos();
			
			if (!$status) {
				throw new Exception('No se pudo ajustar de los consumos por favor revise el archivo', 1);
			}

			return array(
				'status' => 'OK',
				'message' => 'Se borraron los ajuste de consumos del periodo correctamente'
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
