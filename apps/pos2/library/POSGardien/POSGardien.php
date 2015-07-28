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

//require '/Users/gutierrezandresfelipe/php-axxel/Axxel/Client.php';
//require '/Users/gutierrezandresfelipe/php-axxel/Axxel/Acl.php';

/**
 * Gardien
 *
 * Contrôles de sécurité et l'accès aux options du système selon le profil de l'utilisateur.
 *
 */
class POSGardien extends UserComponent
{

	private static $_roleAcls = array();

	private static $_mainAcl = null;

	/**
	 * Valida si tiene permiso para entrar al controlador y acción actual
	 *
	 * @return boolean
	 */
	public static function isAllowed()
	{
		$role = Session::get('role');
		if ($role == '') {
			$role = 'Public';
		}

		$controllerName = Router::getController();
		$actionName = Router::getAction();

		/*$axxel = new \Axxel\Client();
		$acl = $axxel->getAcl(Core::getInstancePath() . 'pos-' . $role . '-myacl', function($acl) use ($role) {

			$acl->addRole($role);

			$aclRole = self::getRoleAcl($role);
			foreach ($aclRole as $resource => $actions) {
				$acl->addResource($resource);
				$acl->allow($role, $resource, '*');
				foreach ($actions as $action => $one) {
					$acl->allow($role, $resource, $action);
				}
			}

		});

		return $acl->isAllowed($role, $controllerName, $actionName);*/

		$aclRole = self::getRoleAcl($role);
		if (isset($aclRole[$controllerName])) {
			if (isset($aclRole[$controllerName][$actionName])) {
				if ($aclRole[$controllerName][$actionName]) {
					return true;
				} else {
					self::_routeOnUnauthorized($role, $controllerName, $actionName);
					return false;
				}
			} else {
				return true;
			}
		} else {
			 self::_routeOnUnauthorized($role, $controllerName, $actionName);
			 return false;
		}
	}

	/**
	 * Genera un enrutamiento especifico para el controlador/acción al efectuarse un acceso no autorizado
	 *
	 * @param string $role
	 * @param string $controllerName
	 * @param string $actionName
	 */
	private static function _routeOnUnauthorized($role, $controllerName, $actionName)
	{

		$appName = Router::getApplication();
		if (!is_dir('apps/' . $appName . '/logs')) {
			mkdir('apps/' . $appName . '/logs');
		}

		$log = new Logger('File', 'acl.txt');
		$log->log($role . ' ' . $controllerName.' '.$actionName);

		$routeToController = 'appmenu';
		$acl = self::getAccessList();
		if (isset($acl[$controllerName])){

			if (isset($acl[$controllerName]['require'])){
				$routeToController = $acl[$controllerName]['require'];
			}

			if (isset($acl[$controllerName]['actions'][$actionName])){
				Flash::notice('No tiene permiso para: '.$acl[$controllerName]['actions'][$actionName]);
			} else {
				Flash::notice('No tiene permiso para: '.$acl[$controllerName]['actions']['index']);
			}

			if (isset($acl[$controllerName]['unauthorized'])){
				$routeToController = $acl[$controllerName]['unauthorized'];
			}
		} else {
			Flash::notice('No tiene permiso para acceder a esta opción');
		}

		if ($controllerName != $routeToController && $actionName != ''){
			Router::routeTo(array('controller' => $routeToController, 'action' => 'index'));
		}
	}

	/**
	 * Devuelve la lista de posibles accessos que están que están controlados
	 *
	 * @return array
	 */
	public static function getAccessList(){
		if(self::$_mainAcl===null){
			$appName = Router::getApplication();
			require 'apps/'.$appName.'/security/acl.php';
			self::$_mainAcl = $accessList;
		}
		return self::$_mainAcl;
	}

	/**
	 * Devuelve la lista de acceso ACL de un usuario
	 *
	 * @param	string $roleName
	 * @return	array
	 */
	public static function getRoleAcl($roleName){
		if(!isset(self::$_roleAcls[$roleName])){
			$appName = Router::getApplication();
			$path = 'apps/'.$appName.'/security/profiles/'.$roleName.'.php';
			if(file_exists($path)){
				require $path;
				self::$_roleAcls[$roleName] = $acl;
			} else {
				self::$_roleAcls[$roleName] = array();
			}
		}
		return self::$_roleAcls[$roleName];
	}

	/**
	 * Almacena una lista ACL de un Role
	 *
	 * @param string $roleName
	 * @param array $roleAcl
	 * @param string $roleInherit
	 */
	public static function saveRoleAcl($roleName, $roleAcl, $roleInherit=''){

		if ($roleInherit != '') {
			$roleInheritAcl = self::getRoleAcl($roleInherit);
			foreach( $roleInheritAcl as $access => $list) {
				if (!isset($roleAcl[$access])){
					$roleAcl[$access] = $list;
				} else {
					foreach ($list as $action => $value) {
						$roleAcl[$access][$action] = $value;
					}
				}
			}
		}

		$acl = self::getAccessList();
		if (isset($acl)){
			foreach ($acl as $access => $description){
				if (isset($roleAcl[$access])){
					foreach ($description['actions'] as $action => $verbose){
						if (!isset($roleAcl[$access][$action])){
							$roleAcl[$access][$action] = false;
						}
					}
				}
			}
		}

		$appName = Router::getApplication();
		$path = 'apps/'.$appName.'/security/profiles/'.$roleName.'.php';
		$roleAclString = '<?php $acl = array(';
		foreach($roleAcl as $key => $value){
			$roleAclString.='\''.$key.'\'=>';
			if(is_array($value)){
				$roleAclString.='array(';
				foreach($value as $subkey => $subvalue){
					if($subvalue){
						$roleAclString.= '\''.$subkey.'\'=>1,';
					} else {
						$roleAclString.= '\''.$subkey.'\'=>0,';
					}
				}
				$roleAclString.='),';
			} else {
				$roleAclString.=$value.',';
			}
		}

		$roleAclString.=');';
		$roleAclString = str_replace(',),', '),', $roleAclString);
		$roleAclString = str_replace('),)', '))', $roleAclString);
		file_put_contents($path, $roleAclString);
		self::$_roleAcls[$roleName] = $roleAcl;
	}

	/**
	 * Registra un acceso correcto a la aplicación
	 *
	 */
	public static function successAccess()
	{
		$controllerRequest = ControllerRequest::getInstance();
		$application = Router::getApplication();
		$ipAddress = $controllerRequest->getClientAddress();
		$ipAddress = preg_replace('/[^0-9\.]/', '', $ipAddress);
		$path = 'apps/' . $application . '/logs/failedAuth-'.$ipAddress.'.log';
		if (file_exists($path)) {
			unlink($path);
		}
	}

	/**
	 * Registra un accesso fallido a la aplicación
	 *
	 */
	public static function failedAccess(){
		$controllerRequest = ControllerRequest::getInstance();
		$application = Router::getApplication();
		$ipAddress = $controllerRequest->getClientAddress();
		$ipAddress = preg_replace('/[^0-9\.]/', '', $ipAddress);
		$path = 'apps/'.$application.'/logs/failedAuth-'.$ipAddress.'.log';
		if(file_exists($path)){
			$numberFailed = file_get_contents($path);
			$numberFailed++;
			if($numberFailed>30){
				if(PHP_OS=='Linux'){
					if(isset($_SERVER['HTTP_USER_AGENT'])){
						syslog(LOG_WARNING, 'HFOS Invalid user unknown from '.$ipAddress.' ('.$_SERVER['HTTP_USER_AGENT'].')');
					} else {
						syslog(LOG_WARNING, 'HFOS Invalid user unknown from '.$ipAddress.' (cli)');
					}
				}
				file_put_contents('apps/'.$application.'/logs/appPanic.log', $ipAddress);
			} else {
				file_put_contents($path, $numberFailed);
			}
		} else {
			file_put_contents($path, 1);
		}
	}

	public static function hasPanic()
	{
		$application = Router::getApplication();
		$path = 'apps/'.$application.'/logs/appPanic.log';
		if (file_exists($path)) {
			if (filemtime($path) > (time() - 900)) {
				unlink($path);
				return true;
			}
		}
		return false;
	}

}