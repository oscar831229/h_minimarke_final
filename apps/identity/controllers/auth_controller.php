<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	IdentiyManager
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * AuthController
 *
 * Controlador de autenticación
 *
 */
class AuthController extends WebServiceController {

	/**
	 * Número de logins fallidos
	 *
	 * @var integer
	 */
	private $_numberFailed = 0;

	/**
	 * Identidad registrada
	 *
	 * @var mixed
	 */
	private $_identity = false;

	/**
	 * Registra la identidad y devuelve los campos que deben hacer
	 * parte de la identidad
	 *
	 * @param 	string $tokenId
	 * @param	Usuarios $usuario
	 * @return	array
	 */
	private function _registerIdentity($tokenId, Usuarios $usuario){

		//Registrar Sesión
		$sesion = $this->Sesiones->findFirst("sid='$tokenId'");
		if($sesion===false){
			$sesion = new Sesiones();
			$sesion->setSid($tokenId);
			$sesion->setUsuariosId($usuario->getId());
			$sesion->setIpaddress($_SERVER['REMOTE_ADDR']);
			$sesion->setCreado($_SERVER['REQUEST_TIME']);
		}
		$sesion->setModificado($_SERVER['REQUEST_TIME']);
		if($sesion->save()==false){
			foreach($sesion->getMessages() as $message){
				throw new AuthException($message->getMessage());
			}
		}
		$identity = array(
			'id' => $usuario->getId(),
			'tokenId' => $tokenId,
			'sucursalId' => $usuario->getSucursalId(),
			'login' => $usuario->getLogin(),
			'apellidos' => $usuario->getApellidos(),
			'nombres' => $usuario->getNombres(),
			'genero' => $usuario->getGenero()
		);

		//Opciones
		$options = array();
		foreach($this->PageChecks->find("usuarios_id='{$usuario->getId()}'") as $pageCheck){
			$options[$pageCheck->getName()] = true;
		}

		//Role Assigned
		$userRoles = array();
		foreach($this->PerfilesUsuarios->find("usuarios_id='{$usuario->getId()}'") as $perfilUsuario){
			$perfil = $perfilUsuario->getPerfiles();
			if($perfil!=false){
				$aplicacion = $perfil->getAplicaciones();
				if($aplicacion!=false){
					$userRoles[$aplicacion->getCodigo()] = true;
				}
			}
		}
		$options['userRoles'] = $userRoles;

		//Password Expired
		$password = $this->_encryptPassword($usuario, $usuario->getLogin());
		if($password===$usuario->getClave()){
			$options['passwordExpired'] = 1;
		}

		$identity['options'] = $options;
		$this->_identity = $identity;
		return $this->_identity;
	}

	/**
	 * Encripta el password basado en el SALT
	 *
	 * @param	Usuarios $usuario
	 * @param	string $password
	 * @return	string
	 */
	private function _encryptPassword(Usuarios $usuario, $password){
		return hash('tiger160,3', $usuario->getId().$password);
	}

	/**
	 * Inicia sesión en el IdentityManager
	 *
	 * //Para el POS en modo local
	 * $auth->startSession(array('shortPassword' => '1234'));
	 *
	 * //Para el POS en modo remoto
	 * $auth->startSession(array('login' => 'admin', 'shortPassword' => '1234'));
	 *
	 * //Para el resto de aplicaciones
	 * $auth->startSession(array('login' => 'admin', 'password' => 'control'));
	 *
	 * @param	array $credentials
	 * @return	mixed
	 */
	public function startSessionAction($credentials=array()){

		//Si se indica la clave corta y normal se genera una excepción
		if(isset($credentials['shortPassword'])&&isset($credentials['password'])){
			throw new AuthException('Datos inconsistentes de autenticación');
		}

		if(!isset($credentials['token'])){
			throw new AuthException('No se indicó el token de autenticación');
		}

		//Si se indica la clave corta
		if(isset($credentials['shortPassword'])){
			$shortPassword = $this->filter($credentials['shortPassword'], 'addslaches');
			if($this->_isRemoteLogin()===false){
				//Cuando el login es remoto se hace con login y clave_corta
				if(!isset($credentials['login'])){
					throw new AuthException('Datos inconsistentes de autenticación');
				}
				$login = $this->filter($credentials['login'], 'usuario');
				$usuario = $this->Usuarios->findFirst("login='$login' AND estado='A'");
				if($usuario!==false){
					$shortPassword = $this->_encryptPassword($usuario, $shortPassword);
					if($shortPassword===$usuario->getClaveCorta()){
						return $this->_registerIdentity($credentials['token'], $usuario);
					}
				}
				$this->_reportFailedLogin();
				return false;
			} else {
				//Cuando el login es local se hace con clave_corta
				$shortPassword = $this->_encryptPassword($usuario, $shortPassword);
				$usuario = $this->Usuarios->findFirst("clave_corta='$shortPassword' AND estado='A'");
				if($usuario!==false){
					return $this->_registerIdentity($credentials['token'], $usuario);
				}
				$this->_reportFailedLogin();
				return false;
			}
		}

		if(!isset($credentials['login'])){
			throw new AuthException('Datos inconsistentes de autenticación');
		}

		//Si se indica la clave normal
		if(isset($credentials['password'])){

			$login = $this->filter($credentials['login'], 'usuario');
			$password = $this->filter($credentials['password'], 'addslaches');

			$usuario = $this->Usuarios->findFirst("login='$login' AND estado='A'");
			if($usuario!==false){
				$password = $this->_encryptPassword($usuario, $password);
				if($password===$usuario->getClave()){
					return $this->_registerIdentity($credentials['token'], $usuario);
				}
			}
			$this->_reportFailedLogin();
			return false;
		}

		//Con huella única
		if(isset($credentials['fingerprint'])){

			$login = $this->filter($credentials['login'], 'usuario');
			$fingerprint = $this->filter($credentials['fingerprint'], 'hash');

			$usuario = $this->Usuarios->findFirst("login='$login' AND fingerprint='$fingerprint' AND estado='A'");
			if($usuario!==false){
				return $this->_registerIdentity($credentials['token'], $usuario);
			}
			$this->_reportFailedLogin();
			return false;
		}

		//No se indico ni la clave corta ni la normal
		throw new AuthException('Datos inconsistentes de autenticación');

	}

	/**
	 * Termina/Destruye la sesión activa en el IdentityManager
	 *
	 * @param	string $tokenId
	 * @return	boolean
	 */
	public function endSessionAction($tokenId){
		$tokenId = $this->filter($tokenId, 'alpha');
		$sesion = $this->Sesiones->findFirst("sid='$tokenId'");
		if($sesion){
			$sesion->delete();
		}
		$this->_identity = false;
		return true;
	}

	/**
	 * Consulta si existe una sesión activa actual
	 *
	 * @param	string $tokenId
	 * @return	boolean
	 */
	public function hasSessionAction($tokenId){
		$tokenId = $this->filter($tokenId, 'alpha');
		$existeSesion = $this->Sesiones->count("sid='$tokenId'");
		if($existeSesion){
			if($this->_identity==false){
				$sesion = $this->Sesiones->findFirst("sid='$tokenId'");
				$usuario = $sesion->getUsuarios();
				if($usuario!==false){
					$this->_registerIdentity($tokenId, $usuario);
					return true;
				} else {
					return false;
				}
			}
		}
		return (bool) $existeSesion;
	}

	/**
	 * Devuelve la identidad activa en el IdentityManager
	 *
	 * @return	mixed
	 */
	public function getIdentityAction(){
		return $this->_identity;
	}

	/**
	 * Averigua si es un login remoto
	 *
	 * @return boolean
	 */
	private function _isRemoteLogin(){
		return false;
	}

	/**
	 * Reporta un login invalido a algun logger
	 *
	 */
	private function _reportFailedLogin(){
		$this->_numberFailed++;
		if($this->_numberFailed>3){
			sleep(5);
		}
	}

}