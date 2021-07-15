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

/**
 * IdentityManagerException
 *
 * Excepciones generadas por el IdentityManager
 *
 */
class IdentityManagerException extends CoreException {

}

/**
 * IdentityManager
 *
 * Administra la sesión activa del usuario autenticado y mantiene
 * con enlace periodico con el IdentityManager
 *
 */
class IdentityManager extends UserComponent {

	/**
	 * Servicio de Autenticación
	 *
	 * @var WebServiceClient
	 */
	private static $_auth = null;

	/**
	 * Obtiene el servicio de autenticación ó devuelve uno existe si ya se ha obtenido
	 *
	 * @return WebServiceClient
	 */
	private static function _getAuthService(){
		if(self::$_auth===null){
			self::$_auth = self::getService('identity.auth');
		}
		return self::$_auth;
	}

	/**
	 * Almacena en sesión la identidad activa
	 *
	 * @param string	$interface
	 * @param array		$identity
	 * @param array		$resources
	 */
	private static function _storeIdentity($interface, $identity, $resources=array()){
		if(Session::isSetData('ResourcesID')){
			$resourceIdentity = Session::get('ResourcesID');
		} else {
			$resourceIdentity = array();
		}
		if(count($resources)==0){
			$resourceIdentity[$interface]['default'] = $identity;
		} else {
			foreach($resources as $resource){
				$resourceIdentity[$interface][$resource] = $identity;
			}
		}
		Session::set('ResourcesID', $resourceIdentity);
	}

	/**
	 * Inicia sesión en el IdentityManager usando login/password
	 *
	 * @param	string $login
	 * @param	string $password
	 * @param 	array $resources
	 * @return	boolean
	 */
	public static function startSession($login, $password, $resources=array()){
		if($login&&$password){
			$auth = self::_getAuthService();
			$token = self::getTokenId($login);
			$identity = $auth->startSession(array(
				'token' => $token,
				'login' => $login,
				'password' => $password
			));
			if($identity===false){
				return false;
			} else {
				if($auth->hasSession($token)==true){
					self::_storeIdentity('local', $identity, $resources);
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	/**
	 * Inicia sesión en el IdentityManager usando un fingerprint de usuario
	 *
	 * @param	string $string
	 * @param	string $fingerprint
	 * @param 	array $resources
	 * @return	boolean
	 */
	public static function startSessionFingerprint($login, $fingerprint, $resources=array())
	{
		if ($fingerprint) {
			$auth = self::_getAuthService();
			$token = self::getTokenId($login);
			$identity = $auth->startSession(array(
				'token' => $token,
				'login' => $login,
				'fingerprint' => $fingerprint
			));
			if ($identity === false) {
				return false;
			} else {
				if ($auth->hasSession($token) == true) {
					self::_storeIdentity('local', $identity, $resources);
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	/**
	 * Consulta en el servicio remoto si la sesión está activa
	 *
	 * @return boolean
	 */
	private static function _checkAuthActive()
	{
		$auth = self::_getAuthService();
		if($auth->hasSession()==true){
			self::_storeIdentity('local', $auth->getIdentity());
			return true;
		} else {
			self::destroyIdentity();
			return false;
		}
	}

	/**
	 * Devuelve un hash que identifica la misma sesión para un usuario en la misma maquina
	 *
	 * @param 	string $login
	 * @return	string
	 */
	public static function getTokenId($login='')
	{
		if(!Session::isSetData('tokenId-'.$login)){
			$hash = md5('$HFOS-TOKEN-ID-' . $login . '-' . $_SERVER['REMOTE_ADDR'] . '-' . $_SERVER['HTTP_USER_AGENT'] . '$');
			Session::set('tokenId-'.$login, $hash);
		}
		return Session::get('tokenId-'.$login);
	}

	/**
	 * Consulta si hay una sesión activa
	 *
	 * @param	string $interface
	 * @return	boolean
	 */
	public static function hasActive($interface='local')
	{
		$resourceIdentity = Session::get('ResourcesID');
		if (isset($resourceIdentity[$interface])) {
			if (is_array($resourceIdentity[$interface])) {
				$numberIdentities = count($resourceIdentity[$interface]);
				if ($numberIdentities > 0) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Destruye una sesión activa
	 *
	 * @param	string $interface
	 * @param	array $resources
	 */
	public static function destroyIdentity($interface='local', $resources=array())
	{
		if(count($resources)==0){
			Session::unsetData('ResourceID');
		} else {
			if(Session::isSetData('ResourcesID')){
				$resourceIdentity = Session::get('ResourcesID');
				if(isset($resourceIdentity[$interface])){
					foreach($resources as $resource){
						unset($resourceIdentity[$interface][$resource]);
					}
				}
				Session::set('ResourcesID', $resourceIdentity);
			}
		}
	}

	/**
	 * Obtiene la identidad pública de la aplicación
	 *
	 * @return array
	 */
	public static function getPublicIdentity()
	{
		return array(
			'id' => 0,
			'sucursalId' => 0,
			'login' => '',
			'apellidos' => '',
			'nombres' => '',
			'genero' => '',
			'tokenId' => '',
			'options' => array()
		);
	}

	/**
	 * Obtiene la sesión activa en el recurso indicado
	 *
	 * @param	string $interface
	 * @param	string $resource
	 * @return	array
	 */
	public static function getActive($interface='local', $resource=null)
	{
		if (self::hasActive($interface)) {
			if($resource===null){
				$resource = Router::getController();
			}
			$resourceIdentity = Session::get('ResourcesID');
			if(isset($resourceIdentity[$interface][$resource])){
				return $resourceIdentity[$interface][$resource];
			} else {
				return $resourceIdentity[$interface]['default'];
			}
		} else {
			return self::getPublicIdentity();
		}
	}

	/**
	 * Elimina las sesiones activas y la sesión en el IdentityManager
	 *
	 * @return boolean
	 */
	static public function endSession()
	{
		$identity = self::getActive();
		$auth = self::_getAuthService();
		if ($auth->endSession($identity['tokenId']) == true) {
			GarbageCollector::freeSessionData();
			Session::unsetData('ResourcesID');
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Copia temporalmente la identidad de un usuario en caso que no haya una identidad activa
	 *
	 * @param string $userName
	 */
	static public function mimicUser($userName)
	{
		if(!self::hasActive('local')){
			$usuario = self::getModel('Usuarios')->findFirst("login='$userName' AND estado='A'");
			if($usuario==false){
				throw new IdentityManagerException('El usuario está inactivo o no existe');
			}
			$identity = array(
				'id' => $usuario->getId(),
				'sucursalId' => $usuario->getSucursalId(),
				'login' => $usuario->getLogin(),
				'apellidos' => $usuario->getApellidos(),
				'nombres' => $usuario->getNombres(),
				'genero' => $usuario->getGenero(),
				'tokenId' => self::getTokenId($usuario->getLogin()),
				'options' => array()
			);
			self::_storeIdentity('local', $identity);
		}
	}

	/**
	 * Obtiene el usuario del front asociado al usuario del back
	 *
	 * @return UsuariosFront
	 */
	public static function getFrontUser()
	{
		$identity = IdentityManager::getActive();
		$usuario = self::getModel('Usuarios')->findFirst($identity['id']);
		if($usuario==false){
			return false;
		} else {
			if($usuario->getUsuariosFrontId()>0){
				$usuarioFrontId = $usuario->getUsuariosFrontId();
				$usuarioFront = self::getModel('UsuariosFront')->findFirst("codusu='$usuarioFrontId' AND estado='A'");
				if($usuarioFront==false){
					return false;
				} else {
					return $usuarioFront;
				}
			} else {
				return false;
			}
		}
	}

	/**
	 * Enlaza una cuenta de usuario del front al del back
	 *
	 * @param UsuariosFront $usuarioFront
	 */
	public static function linkAccount(UsuariosFront $usuarioFront){
		$identity = IdentityManager::getActive();
		$usuario = self::getModel('Usuarios')->findFirst($identity['id']);
		if($usuario==false){
			throw new IdentityManagerException('No se puede asociar la identidad al perfil público');
		}
		$usuario->setUsuariosFrontId($usuarioFront->getCodusu());
		if($usuario->save()==false){
			foreach($usuario->getMessages() as $message){
				throw new IdentityManagerException('Usuario: '.$message->getMessage());
			}
		}
	}

	public static function createAndLinkAccount(){
		try {

			$transaction = TransactionManager::getUserTransaction();

			$identity = IdentityManager::getActive();
			$usuario = self::getModel('Usuarios')->findFirst($identity['id']);
			if($usuario==false){
				throw new IdentityManagerException('No se puede asociar la identidad al perfil público');
			}

			$perfilFront = self::getModel('PerfilesFront')->findFirst("detalle LIKE 'USUARIO%CONSULTA'");
			if($perfilFront==false){
				throw new IdentityManagerException('No existe el perfil de usuario de consulta en el front');
			}

			$usuarioFront = self::getModel('UsuariosFront')->findFirst("login='{$usuario->getLogin()}'");
			if($usuarioFront==false){
				$codusu = self::getModel('UsuariosFront')->maximum('codusu')+1;
				$usuarioFront = new UsuariosFront();
				$usuarioFront->setTransaction($transaction);
				$usuarioFront->setCodusu($codusu);
				$usuarioFront->setCodprf($perfilFront->getCodprf());
				$usuarioFront->setNombre(i18n::strtoupper($usuario->getNombres().' '.$usuario->getApellidos()));
				$usuarioFront->setLogin($usuario->getLogin());
				$usuarioFront->setPass(sha1(uniqid()));
				$usuarioFront->setFoto('unknown.jpg');
				$usuarioFront->setEmail($usuario->getEmail());
				$usuarioFront->setGenero($usuario->getGenero());
				$usuarioFront->getTelefono('');
				$usuarioFront->setUltlog(time());
				$usuarioFront->setEstado('A');
				if($usuarioFront->save()==false){
					foreach($usuarioFront->getMessages() as $message){
						throw new IdentityManagerException('Usuario-Front: '.$message->getMessage());
					}
				}
			}

			$usuario->setTransaction($transaction);
			$usuario->setUsuariosFrontId($usuarioFront->getCodusu());
			if($usuario->save()==false){
				foreach($usuario->getMessages() as $message){
					throw new IdentityManagerException('Usuario: '.$message->getMessage());
				}
			}

			$transaction->commit();

		}
		catch(TransactionFailed $e){

		}
	}

	/**
	 * Devuelve los códigos de las aplicaciones donde el usuario tiene acceso
	 *
	 * @param	string $interface
	 * @return	array
	 */
	public static function getUserAppRoles($interface='local'){
		if(Session::isSetData('ResourcesID')){
			$resourceIdentity = Session::get('ResourcesID');
		} else {
			$resourceIdentity = array();
		}
		if(isset($resourceIdentity[$interface]['default']['options']['userRoles'])){
			return $resourceIdentity[$interface]['default']['options']['userRoles'];
		} else {
			return array();
		}
	}

	/**
	 * Autentica el usuario en una determinada aplicación
	 *
	 * @param	string $appName
	 * @return	boolean
	 */
	public static function authOnApplication($appName)
	{
		//if(!self::hasActive($appName)){
			$identity = IdentityManager::getActive();
			if ($identity['id'] > 0) {
				$usuario = self::getModel('Usuarios')->findFirst($identity['id']);
				if($usuario==false){
					return false;
				}
			} else {
				return false;
			}
			$service = self::getService($appName.'.session');
			$success = $service->startWithFingerprint($usuario->getLogin(), $usuario->getFingerprint());
			if ($success===false) {
				throw new IdentityManagerException('No fue posible autenticarse en "' . $appName . '"');
			} else {
				self::_storeIdentity($appName, $service->getIdentity());
			}
		//}
	}

	/**
	 * Obtiene un ServiceConsumer autenticándose primero si es necesario
	 *
	 * @param	string $service
	 * @return	ServiceConsumer
	 */
	public static function getAuthedService($service)
	{
		$serviceNDI = explode('.', $service);
		if(count($serviceNDI) == 2){
			self::authOnApplication($serviceNDI[0]);
		}
		return self::getService($service);
	}

}