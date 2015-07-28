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

class Permisos_PerfilesController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{
		$this->setParamToView('perfiles', $this->Perfiles->find());
		$this->setParamToView('aplicaciones', $this->Aplicaciones->find());
	}

	/**
	 *
	 * Accion que permite visualizar los permisos de un perfil en una plaicación en el BackOffice
	 * desde el archivo acl.php
	 */
	public function getPermisosAction(){

		$controllerRequest = ControllerRequest::getInstance();
		$perfilesId = $controllerRequest->getParamPost('perfilesId', 'int');
		$aplicacionId = $controllerRequest->getParamPost('aplicacionId', 'int');

		//Obtenemeos los permisos de ese perfil en esa aplicacion ya guardados en DB
		$permisosSaved = array();
		$permisosPerfiles = $this->PerfilesPermisos->find("perfiles_id=".$perfilesId." AND aplicaciones_id=".$aplicacionId);
		foreach($permisosPerfiles as $perfilesPermiso){
			$permisosSaved[$perfilesPermiso->getController()][$perfilesPermiso->getAction()] = true;
		}

		//Obtenemos los nombres de perfil
		$perfil = $this->Perfiles->findFirst($perfilesId);
		$this->setParamToView('perfilName', $perfil->getNombre());

		//Obtenemos los nombres de applicacion
		$app= $this->Aplicaciones->findFirst($aplicacionId);
		$this->setParamToView('appsName', $app->getNombre());

		//Obtenemos los accesList segun aplicacion
		$accessList = Gardien::getAccessList($app->getCodigo());
		if(!is_array($accessList)){
			$accessList = array();
		}
		$this->setParamToView('accessList', $accessList);

		//Obtenemos el menuDisposition segun aplicacion
		$menuDisposition = Gardien::getMenuDisposition($app->getCodigo());
		if(!is_array($menuDisposition)){
			$menuDisposition = array();
		}

		$this->setParamToView('menuDisposition', $menuDisposition);
		$this->setParamToView('pPermisosSaved', $permisosSaved);
	}

	/**
	 *
	 * Metodo que guarda en base de Datos los permisos de un perfil en una aplicación
	 */
	public function savePerfilesPermisosAction(){

		$this->setResponse('json');

		//Params
		$controllerRequest = ControllerRequest::getInstance();
		$perfilesId = $controllerRequest->getParamPost('perfilesId', 'int');
		$aplicacionId = $controllerRequest->getParamPost('aplicacionId', 'int');
		$access = $controllerRequest->getParamPost('access');

		try {

			$transaction = TransactionManager::getUserTransaction();

			$this->PerfilesPermisos->setTransaction($transaction);

			//borramos los permisos existentes
			$this->PerfilesPermisos->deleteAll("perfiles_id=".$perfilesId." AND aplicaciones_id=".$aplicacionId);

			//Insertamos los nuevos permisos de ese perfil en una aplicación
			if(is_array($access)){
				foreach($access as $data){
					$url = explode("/", $data);
					$perfilesPermisos = new PerfilesPermisos();
					$perfilesPermisos->setTransaction($transaction);
					$perfilesPermisos->setPerfilesId($perfilesId);
					$perfilesPermisos->setAplicacionesId($aplicacionId);
					$perfilesPermisos->setController("$url[0]");
					if(isset($url[1])){
						$actionS = $url[1];
					} else {
						$actionS = "";
					}
					$perfilesPermisos->setAction($actionS);
					if($perfilesPermisos->save()==false){
						foreach($perfilesPermisos->getMessages() as $messages){
							$transaction->rollback($messages->getMessage());
						}
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
			'message' => "Se guardaron los permisos correctamente"
		);
	}

	public function getPerfilesPorAppAction(){
		$this->setResponse('json');
		$aplicacionId = $this->getPostParam('aplicacionId', 'int');
		if($aplicacionId>0){
			$perfiles = $this->Perfiles->find("aplicaciones_id='$aplicacionId'");
			if(count($perfiles)){
				$perfilesApp = array();
				foreach($perfiles as $perfil){
					$perfilesApp[] = array(
						'id' => $perfil->getId(),
						'nombre' => $perfil->getNombre()
					);
				}
				return array(
					'status' => 'OK',
					'perfiles' => $perfilesApp
				);
			}
		}
		return array(
			'status' => 'FAILED',
			'message' => 'La aplicación no tiene perfiles asignados'
		);
	}

}