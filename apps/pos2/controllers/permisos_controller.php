<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

class PermisosController extends ApplicationController
{

	public $permisos = "";
	public $usuarios_id = 0;

	public function initialize()
	{
		$this->setTemplateAfter("admin_menu");
		$this->setPersistance(true);
	}

	public function indexAction()
	{
		$this->loadModel('UsuariosPos');
	}

	public function getPermsAction($id)
	{
		$this->setResponse('view');
		$this->usuarios_id = $this->filter($id, 'int');
		if($this->usuarios_id>0){
			$this->permisos = array();
			foreach($this->Permisos->find("usuarios_id='$id' AND estado='A'") as $permiso){
				$this->permisos[] = $permiso->salon_id;
			}
			$this->loadModel('Salon');
		} else {
			$this->routeToAction('noExisteUsuario');
		}
	}

	public function noExisteUsuarioAction()
	{

	}

	public function savePermAction($id)
	{
		$this->setResponse('json');
		$id = $this->filter($id, 'int');
		$permiso = $this->Permisos->findFirst("usuarios_id='{$this->usuarios_id}' AND salon_id='{$id}'");
		if($permiso==false){
			$permiso = new Permisos();
		}
		$permiso->usuarios_id = $this->usuarios_id;
		$permiso->salon_id = $id;
		if($permiso->estado=='A'){
			$permiso->estado = 'I';
		} else {
			$permiso->estado = 'A';
		}
		if($permiso->save()==true){
			return array(
				'status' => 'OK'
			);
		} else {
			return array(
				'status' => 'ERROR'
			);
		}
	}

}
