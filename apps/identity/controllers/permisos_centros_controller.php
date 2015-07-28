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
 * Permisos Centros Controller
 *
 * Control de Permisos de Usuarios en Centros
 *
 */
class Permisos_CentrosController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$this->setParamToView('usuarios', $this->Usuarios->find(array('order' => 'nombres')));
		$this->setParamToView('centros', $this->Centros->find(array('order' => 'nom_centro')));
	}

	/**
	 *
	 * Busca los registro en permisos_comprob que pertenescan a un centro y un usuario
	 */
	public function loadPermisosCentrosAction(){
		$this->setResponse('json');

		//Obtenemos los datos enviados por POST
		$controllerRequest = ControllerRequest::getInstance();
		$usuarioId = $controllerRequest->getParamPost('usuariosId', 'int');

		if(!$usuarioId){
			$message = 'Por favor ingrese un usuario';
			return array(
				'status' => 'FAILED',
				'message' => $message
			);
		}

		//Buscamos y solo regresamos los id de permisos de ese usuario en centros en un array
		$permisosCentrosA = Array();
		foreach($this->PermisosCentros->find("usuarios_id=".$usuarioId) as $permisosCentro){
			$permisosCentrosA[] = $permisosCentro->getCentroId();
		}

		if(count($permisosCentrosA)){
			$message = "Se encontraron ".count($permisosCentrosA)." permisos del usuario en centros";
		} else {
			$message = "El usuario no tiene permiso en ningún centro de costo";
		}

		return array(
			'status' => 'OK',
			'message' => $message,
			'pOpcion' => $permisosCentrosA
		);
	}

	/**
	 *
	 * Metodo que guarda en la BD los permisos de un usuario a un centro
	 */
	public function savePermisosCentrosAction(){
		$this->setResponse("json");

		//Obtenemos los datos enviados por POST
		$controllerRequest = ControllerRequest::getInstance();
		$usuarioId = $controllerRequest->getParamPost("usuariosId", "int");
		$permisos = $controllerRequest->getParamPost("permisos");

		//Si no hay permisos lo ponemos empty
		if(!$permisos){
			$permisos = array();
		}

		try {

			$transaction = TransactionManager::getUserTransaction();

			$this->PermisosCentros->setTransaction($transaction);

			//borramos los permisos existentes del suuario seleccionado
			$this->PermisosCentros->deleteAll("usuarios_id=".$usuarioId);

			//Insertamos los nuevos perfiles a el usuario
			foreach($permisos as $data){

				$permisosCentro = new PermisosCentros();
				$permisosCentro->setTransaction($transaction);
				$permisosCentro->setUsuariosId($usuarioId);
				$permisosCentro->setCentroId($data);

				if($permisosCentro->save()==false){
					foreach ($permisosCentro->getMessages() as $messages){
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
	 * Metodo que verifica si un centro existe o no
	 */
	public function checkComprobAction(){
		$this->setResponse("json");

		//Obtenemos los datos enviados por POST
		$controllerRequest = ControllerRequest::getInstance();
		$centro = $controllerRequest->getParamPost("centro", 'int');

		if($this->Centros->exists("codigo=".$centro."")){
			$status="OK";
			$msg = "El centro existe";
		}else{
			$status="FAILED";
			$msg = "El centro no existe";
		}


		return array(
				'status' => $status,
				'message' => $msg
			);
	}

}