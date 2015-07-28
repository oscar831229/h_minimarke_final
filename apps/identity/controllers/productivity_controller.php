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

class ProductivityController extends ApplicationController {

	public function indexAction(){

	}

	public function getPanelAction(){
		$this->setResponse('view');
	}

	public function canGetMailAction(){
		$this->setResponse('json');

		$user = IdentityManager::getFrontUser();
		if($user==false){
			return array(
				'status' => 'OK',
				'notMailUser' => true
			);
		} else {
			return array(
				'status' => 'OK',
				'notMailUser' => false
			);
		}
	}

	public function requestFrontCredentialsAction(){
		$this->setResponse('view');
		$this->setParamToView('usuarios', $this->UsuariosFront->find(array("estado='A'", 'order' => 'nombre')));
	}

	public function noMailUserAction(){
		$this->setResponse('view');
	}

	public function linkAccountAction(){

		$this->setResponse('json');

		try {

			$frontAccount = $this->getPostParam('frontAccount', 'onechar');

			if($frontAccount=='Y'){

				$usuarioFrontId = $this->getPostParam('usuarioFrontId', 'int');
				if($usuarioFrontId<=0){
					return array(
						'status' => 'FAILED',
						'field' => 'usuarioFrontId',
						'message' => 'El usuario del front es requerido'
					);
				}

				$password = $this->getPostParam('password');
				if($password==''){
					return array(
						'status' => 'FAILED',
						'field' => 'password',
						'message' => 'El password del front es requerido'
					);
				}

				$password = sha1($password);

				$usuario = $this->UsuariosFront->findFirst("codusu='$usuarioFrontId' AND pass='$password' AND estado='A'");
				if($usuario==false){
					return array(
						'status' => 'FAILED',
						'field' => 'usuarioFrontId',
						'message' => 'Usuario/Clave incorrecta'
					);
				} else {
					IdentityManager::linkAccount($usuario);
				}

			} else {
				IdentityManager::createAndLinkAccount();
			}

			return array(
				'status' => 'OK'
			);

		}
		catch(IdentityManagerException $e){
			return array(
				'status' => 'FAILED',
				'field' => 'usuarioFrontId',
				'message' => $e->getMessage()
			);
		}

	}

}