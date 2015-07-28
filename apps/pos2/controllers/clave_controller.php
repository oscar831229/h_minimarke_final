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

class ClaveController extends ApplicationController {

	public function beforeFilter(){
		if(POSGardien::hasPanic()==true){
			Router::routeTo(array('controller' => 'panic'));
			return false;
		}
		parent::beforeFilter();
	}

	public function initialize(){

	}

	public function indexAction(){
		$this->setResponse('view');
	}

	public function inputAction(){

	}

	public function autenticarAction($id){
		$this->setResponse('json');
		$controllerRequest = ControllerRequest::getInstance();
		Session::setData('usuarios_id', 0);
		if($controllerRequest->isAjax()){
			$id = $this->filter($id, 'alpha');
			$usuario = $this->UsuariosPos->findFirst("clave='$id' AND estado='A'");
			if($usuario){
				Session::set('usuarios_id', $usuario->id);
				Session::set('usuarios_nombre', $usuario->nombre);
				Session::set('auth', $usuario->perfil);
				Session::set('role', $usuario->perfil);
				POSGardien::successAccess();
				return 1;
			} else {
				POSGardien::failedAccess();
				return 0;
			}
		} else {
			POSGardien::failedAccess();
			return 0;
		}
	}

	public function changeAction(){
		$this->setResponse('json');
		$newPass = $this->getPostParam('nuevo', 'alpha');
		if(strlen($newPass)==40){
			if($this->UsuariosPos->findFirst(Session::get('usuarios_id'))){
				$this->UsuariosPos->clave = $newPass;
				if($this->UsuariosPos->save()==true){
					return 1;
				} else {
					return 0;
				}
			} else {
				sleep(5);
				return 0;
			}
		} else {
			sleep(5);
			return 0;
		}
	}

}
