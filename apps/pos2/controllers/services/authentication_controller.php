<?php

class AuthenticationController extends WebServiceController {

	/**
	 * Indicador de exito
	 *
	 * @var boolean
	 */
	private $_success = false;

	/**
	 * Mensaje del autenticador
	 *
	 * @var string
	 */
	private $_message = '';

	/**
	 * Establece el controlador como persistente
	 *
	 */
	public function initialize(){
		$this->setPersistance(true);
	}

	/**
	 * Valida las credenciales de un usuario
	 *
	 * @param 	int $password
	 * @return 	boolean
	 */
	public function validateCredentialsAction($password){
		$auth = new Auth(array(
			'model',
			'class' => 'Usuarios',
			'clave' => sha1($password)
		));
		$this->_success = $auth->authenticate();
		if($this->_success==false){
			$this->_message = 'Clave incorrecta';
		}
		return $this->_success;
	}

	/**
	 * Devuelve el mensaje de la autenticación
	 *
	 * @return string
	 */
	public function getMessageAction(){
		return $this->_message;
	}

	/**
	 * Obtiene la identidad de quien se logueo
	 *
	 * @return array
	 */
	public function getIdentity(){
		return Auth::getActiveIdentity();
	}

}