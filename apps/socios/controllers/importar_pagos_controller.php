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
 * Importar_PagosController
 *
 * Clase controller que realiza importa pagos por excel
 *
 */
class Importar_PagosController extends ApplicationController {

	public function initialize(){
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
	public function indexAction()
	{
		Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
        $periodo = SociosCore::getCurrentPeriodo();
        $y = substr($periodo, 0, 4);
		$m = substr($periodo, 4, 2);
		$fecha = "$y-$m-01";
        Tag::displayTo('fecha', $fecha);    
		$this->setParamToView('message', 'Ingrese el archivo excel para importar los pagos');
	}
	
	/**
	 * Metodo que genera tabla de amortizacion
	 *
	 * @return json
	 */
	public function generarAction()
	{
		Core::importFromLibrary('Hfos/Socios','SociosFactura.php');
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();
		
		try {

			//Comprobantes			
			$comprob = $this->getPostParam("comprob");
			$fecha = $this->getPostParam("fecha");

			$result = 0;
			$destinationPath = Core::getInitialPath().'public/temp/';
			$targetPath = $destinationPath . basename( $_FILES['archivo']['name']); 
			if (@move_uploaded_file($_FILES['archivo']['tmp_name'], $targetPath)) {
				 $result = 1; 
			} else {
				throw new Exception('No se pudo subir el archivo', 1);
			}
			
			$sociosFactura = new SociosFactura();
			
			$config = array(
				"fecha" => $fecha,
				"comprob" => $comprob,
				"file" => $targetPath
			);
			$status = $sociosFactura->importarPagos($config);
			
			if (!$status) {
				throw new SociosException('No se pudo importar los pagos por favor revise el archivo');
			}
			
			return array(
				'status' => 'OK',
				'file' => 'temp/'.$_FILES['archivo']['name']
			);
		} catch (Exception $e) {
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
	public function borrarAction()
	{
		

	}

}
