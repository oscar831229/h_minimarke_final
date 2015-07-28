<?php

class UserIdentityController extends WebServiceController {

	public function beforeFilter(){
		if(Auth::isValid()==false){
			throw new UserIdentityException('La sesión no es válida');
		}
	}

	/**
	 * Devuelve el id del usuario autenticado
	 *
	 * @return int
	 */
	public function getUserIdAction(){
		$identity = Auth::getIdentity();
		return $identity['id'];
	}

}