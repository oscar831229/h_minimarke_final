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
 * ConsecutivosController
 *
 * Control de Consecutivos
 *
 */
class Permisos_ComprobController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$this->setParamToView('usuarios', $this->Usuarios->find(array("estado='A'", 'order' => 'nombres')));
		$this->setParamToView('comprob', $this->Comprob->find(array('order' => 'nom_comprob')));
	}

	/**
	 *
	 * Busca los registro en permisos_comprob que pertenescan a un comprobante y un usuario
	 */
	public function loadPermisosComprobAction(){
		$this->setResponse('json');

		//Obtenemos los datos enviados por POST
		$controllerRequest = ControllerRequest::getInstance();
		$usuarioId = $controllerRequest->getParamPost('usuariosId', 'int');
		$comprob = $controllerRequest->getParamPost('comprob');

		if(!$usuarioId){
			$message = 'Por favor ingrese un usuario';
			if(!$comprob){
				$message .= ' y un comprobante';
			}
			return array(
				'status' => 'FAILED',
				'message' => $message
			);
		}

		//Buscamos y solo regresamos los id de perfiles en un array
		$permisosComprobA = Array();
		foreach($this->PermisosComprob->find("usuarios_id=".$usuarioId." AND comprob='".$comprob."' ") as $permisosComprob){
			$permisosComprobA[] = $permisosComprob->getPopcion();
		}

		if(count($permisosComprobA)){
			$message = "Se encontraron ".count($permisosComprobA)." permisos del usuario en este comprobante";
		} else {
			$message = "El usuario no tiene permisos asignados a ese comprobante";
		}

		return array(
			'status' => 'OK',
			'message' => $message,
			'pOpcion' => $permisosComprobA
		);
	}

	/**
	 *
	 * Metodo que guarda en la BD los permisos de un usuario a un comprobante
	 */
	public function savePermisosComprobAction(){
		$this->setResponse("json");

		//Obtenemos los datos enviados por POST
		$controllerRequest = ControllerRequest::getInstance();
		$usuarioId = $controllerRequest->getParamPost("usuariosId", "int");
		$comprob = $controllerRequest->getParamPost("comprob");
		$permisos = $controllerRequest->getParamPost("permisos");

		//Si no hay permisos lo ponemos empty
		if(!$permisos){
			$permisos = array();
		}

		try {

			$transaction = TransactionManager::getUserTransaction();

			$this->PermisosComprob->setTransaction($transaction);

			//borramos los permisos existentes del suuario seleccionado
			$this->PermisosComprob->deleteAll("usuarios_id=".$usuarioId." AND comprob='".$comprob."'");

			//Insertamos los nuevos perfiles a el usuario
			foreach($permisos as $data){

				$permisosComprob = new PermisosComprob();
				$permisosComprob->setTransaction($transaction);
				$permisosComprob->setUsuariosId($usuarioId);
				$permisosComprob->setComprob($comprob);
				$permisosComprob->setPopcion($data);

				if($permisosComprob->save()==false){
					foreach ($permisosComprob->getMessages() as $messages){
						$transaction->rollback($messages->getMessage());
					}
				}
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

		return array(
			'status' => 'OK',
			'message' => "Se guardaron los permisos correctamente"
		);

	}

	/**
	 *
	 * Metodo que verifica si un comprobante existe o no
	 */
	public function checkComprobAction(){
		$this->setResponse("json");

		//Obtenemos los datos enviados por POST
		$controllerRequest = ControllerRequest::getInstance();
		$comprob = $controllerRequest->getParamPost("comprob");

		if($this->Comprob->exists("codigo='".$comprob."'")){
			$status="OK";
			$msg = "El comprobante existe";
		}else{
			$status="FAILED";
			$msg = "El comprobante no existe";
		}


		return array(
				'status' => $status,
				'message' => $msg
			);
	}

}