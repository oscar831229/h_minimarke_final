<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Perfiles_UsuariosController extends ApplicationController{

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$this->setParamToView('usuarios', $this->Usuarios->find("estado='A'"));
		$this->setParamToView('perfiles', $this->Perfiles->find(array('order' => 'aplicaciones_id')));
	}

	/**
	 *
	 * Metodo que busca los perfiles ya asignaos a un usuario en BD
	 */
	public function loadPerfilesUsuariosAction(){

		$this->setResponse('json');

		//Obtenemos los datos enviados por POST
		$controllerRequest = ControllerRequest::getInstance();
		$usuarioId = $controllerRequest->getParamPost('usuariosId', 'int');

		if(!$usuarioId){
			return array(
				'status' => 'FAILED',
				'message' => 'Por favor ingres un usuario'
			);
		}

		//Buscamos y solo regresamos los id de perfiles en un array
		$perfilesUsuarios = Array();
		foreach($this->PerfilesUsuarios->find("usuarios_id=".$usuarioId) as $perfilUsuario){
			$perfilesUsuarios[] = $perfilUsuario->getPerfilesId();
		}

		if(count($perfilesUsuarios)){
			$message = 'Se encontraron '.count($perfilesUsuarios).' perfiles del usuario';
			$status = 'OK';
		} else {
			$message = 'El usuario no tiene perfiles asignados';
			$status = 'NOTICE';
		}

		return array(
			'status' => $status,
			'message' => $message,
			'perfiles' => $perfilesUsuarios
		);

	}

	/**
	 *
	 * Metodo que guarda los perfiles de un usuarios
	 */
	public function savePerfilesUsuariosAction(){

		$this->setResponse("json");

		//Obtenemos los datos enviados por POST
		$controllerRequest = ControllerRequest::getInstance();
		$usuarioId = $controllerRequest->getParamPost("usuariosId", "int");
		$perfiles = $controllerRequest->getParamPost("perfiles");

		//Si no hay pefiles lo ponemos empty
		if(!$perfiles){
			$perfiles = array();
		}

		try {

			$transaction = TransactionManager::getUserTransaction();

			$this->PerfilesUsuarios->setTransaction($transaction);

			//borramos los permisos existentes del suuario seleccionado
			$this->PerfilesUsuarios->deleteAll("usuarios_id=".$usuarioId);

			//Insertamos los nuevos perfiles a el usuario
			foreach($perfiles as $data){

				$perfilesUsuarios = new PerfilesUsuarios();
				$perfilesUsuarios->setTransaction($transaction);
				$perfilesUsuarios->setUsuariosId($usuarioId);
				$perfilesUsuarios->setPerfilesId($data);

				if($perfilesUsuarios->save()==false){
					foreach ($perfilesUsuarios->getMessages() as $messages){
						$transaction->rollback($messages->getMessage());
					}
				}
			}

			$transaction->commit();
			Gardien::createUserAcls();
		}
		catch(GardienException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}


		return array(
			'status' => 'OK',
			'message' => "Se guardaron los perfiles correctamente"
		);


	}
}
