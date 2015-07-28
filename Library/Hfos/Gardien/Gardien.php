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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Gardien
 *
 * Contrôles de sécurité et l'accès aux options du système selon le profil de l'utilisateur.
 *
 */
class Gardien extends UserComponent
{

	private static $_roleAcls = array();

	private static $_mainAcl = null;

	private static $_userAcls = array();

	/**
	 * Indica si el usuario tiene al menos un rol asignado en una determinada aplicación
	 *
	 * @param	string $code
	 * @return	boolean
	 */
	public static function hasAppAccess($code){
		$userAppRoles = IdentityManager::getUserAppRoles();
		if(isset($userAppRoles[$code])){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Obtiene los permisos del usuario en la aplicación activa
	 *
	 * @param 	string $appName
	 * @param	integer $userId
	 * @return	array
	 */
	public static function getUserAcl($appName, $userId)
	{
		$mode = CoreConfig::getAppSetting('mode');
		if ($mode == 'production') {
			if (function_exists('apc_store')) {
				$key = Core::getInstanceName() . '-GARDIEN-' . $appName . '-' . $userId;
				$acl = apc_fetch($key);
				if ($acl === false) {
					$aclPath = KEF_ABS_PATH . 'apps/identity/security/data/' . $appName . '/' . $userId . '.php';
					if(file_exists($aclPath)){
						require $aclPath;
						apc_store($key, $acl, 14400);
						return $acl;
					}
				} else {
					return $acl;
				}
			}
		}

		if(!isset(self::$_userAcls[$appName][$userId])){
			$aclPath = KEF_ABS_PATH . 'apps/identity/security/data/' . $appName . '/' . $userId . '.php';
			if(file_exists($aclPath)){
				require $aclPath;
				self::$_userAcls[$appName][$userId] = $acl;
				return $acl;
			}
		} else {
			return self::$_userAcls[$appName][$userId];
		}

		return array();
	}

	/**
	 * Devuelve la lista de posibles accesos que están que están controlados para la aplicación activa
	 *
	 * @param	string
	 * @return	array
	 */
	public static function getAccessList($appName)
	{
		$aclPath = 'apps/identity/security/rules/' . $appName . '/acl.php';
		require KEF_ABS_PATH . $aclPath;
		return $accessList;
	}

	/**
	 * Devuelve la lista de posibles access al menu que están controladas por la aplicación activa
	 *
	 * @param	string $appName
	 * @return	array $menuDisposition
	 */
	public static function getMenuDisposition($appName)
	{
		$aclPath = 'apps/identity/security/rules/' . $appName . '/acl.php';
		require KEF_ABS_PATH . $aclPath;
		return $menuDisposition;
	}

	/**
	 * Valida si tiene permiso para entrar al controlador y acción actual
	 *
	 * @param	string $resourceName
	 * @param	string $actionName
	 * @param	string $appName
	 * @param	string $externResource
	 * @return	boolean
	 */
	public static function isAllowed($resourceName='', $actionName='', $appName='')
	{
		//return true;
		if ($resourceName == '') {
			$resourceName = Router::getController();
		}
		if ($actionName == '') {
			$actionName = Router::getAction();
		}
		if ($appName == '') {
			$appName = CoreConfig::getAppSetting('code');
		}
		$identity = IdentityManager::getActive('local', $resourceName);
		$acl = self::getUserAcl($appName, $identity['id']);
		if (isset($acl[$resourceName])) {
			if (isset($acl[$resourceName][$actionName])) {
				return $acl[$resourceName][$actionName];
			} else {
				return true;
			}
		}
		return false;
	}

	/**
	 * Obtiene información sobre un permiso
	 *
	 * @param	string $resourceName
	 * @param	string $actionName
	 * @param 	string $appName
	 * @param	array
	 */
	public static function getAccessInfo($resourceName, $actionName, $appName='')
	{
		if($appName==''){
			$appName = CoreConfig::getAppSetting('code');
		}
		$accessList = self::getAccessList($appName);
		if(isset($accessList[$resourceName])){
			if(isset($accessList[$resourceName]['actions'][$actionName])){
				$action = $accessList[$resourceName]['actions'][$actionName];
				if(isset($action['sameAs'])){
					$actionName = $action['sameAs'];
					$action = $accessList[$resourceName]['actions'][$actionName];
				}
				$description = $action['description'].' '.$accessList[$resourceName]['description'];
				return array(
					'elevation' => $accessList[$resourceName]['elevation'],
					'description' => $description
				);
			} else {
				return array(
					'elevation' => $accessList[$resourceName]['elevation'],
					'description' => $accessList[$resourceName]['description'].' ('.$actionName.')'
				);
			}
		} else {
			return array(
				'elevation' => false,
				'description' => 'Desconocido ('.$resourceName.'/'.$actionName.')'
			);
		}
	}

	/**
	 * Almacena una lista ACL de un Role
	 *
	 * @param string $roleName
	 * @param array $roleAcl
	 * @param string $roleInherit
	 */
	public static function saveRoleAcl($roleName, $roleAcl, $roleInherit=''){
		if($roleInherit!=''){
			$roleInheritAcl = self::getRoleAcl($roleInherit);
			foreach($roleInheritAcl as $access => $list){
				if(!isset($roleAcl[$access])){
					$roleAcl[$access] = $list;
				} else {
					foreach($list as $action => $value){
						$roleAcl[$access][$action] = $value;
					}
				}
			}
		}

		$acl = self::getAccessList();
		if(isset($acl)){
			foreach($acl as $access => $description){
				if(isset($roleAcl[$access])){
					foreach($description['actions'] as $action => $verbose){
						if(!isset($roleAcl[$access][$action])){
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
	 * Metodo que guarda segun el array de permisos permisos
	 *
	 * @param array $accessList
	 * @return boolean
	 */
	public static function saveAclToDb($accessList=array()){
		return true;
	}

	/**
	 * Crea los archivos de ACL basados en lo guardado en la BD
	 *
	 */
	public static function createUserAcls(){

		$aclRole = array();
		foreach(self::getModel('PerfilesPermisos')->find() as $perfilPermiso){
			$perfilesId = $perfilPermiso->getPerfilesId();
			$aplicacionesId = $perfilPermiso->getAplicacionesId();
			if(!isset($aclRole[$aplicacionesId][$perfilesId][$perfilPermiso->getController()][$perfilPermiso->getAction()])){
				$aclRole[$aplicacionesId][$perfilesId][$perfilPermiso->getController()][$perfilPermiso->getAction()] = true;
			}
		}

		$perfiles = array();
		foreach(self::getModel('Perfiles')->find() as $perfil){
			if($perfil->countPerfilesUsuarios()>0){
				if(!isset($perfiles[$perfil->getAplicacionesId()])){
					$perfiles[$perfil->getAplicacionesId()] = array();
				}
				$perfiles[$perfil->getAplicacionesId()][] = $perfil->getId();
			}
		}

		$accessByRole = array();
		foreach(self::getModel('Aplicaciones')->find() as $aplicacion){
			$aplicacionesId = $aplicacion->getId();
			if(isset($perfiles[$aplicacionesId])){
				$dataPath = 'apps/identity/security/rules/'.$aplicacion->getCodigo();
				$aclPath = $dataPath.'/acl.php';
				if(file_exists($aclPath)){
					include $aclPath;
					foreach($menuDisposition as $menu){
						foreach($menu['options'] as $menuOption){
							if(isset($accessList[$menuOption])){
								foreach($accessList[$menuOption]['actions'] as $actionName => $actionDesc){
									if(isset($actionDesc['sameAs'])){
										$actionName = $actionDesc['sameAs'];
									}
									foreach($perfiles[$aplicacionesId] as $perfilId){
										if(!isset($accessByRole[$aplicacionesId][$perfilId])){
											include $dataPath.'/base.php';
											$accessByRole[$aplicacionesId][$perfilId] = $acl;
										}
										if(isset($aclRole[$aplicacionesId][$perfilId][$menuOption][$actionName])){
											$accessByRole[$aplicacionesId][$perfilId][$menuOption][$actionName] = true;
										} else {
											$accessByRole[$aplicacionesId][$perfilId][$menuOption][$actionName] = false;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		try {

			$userAssigned = array();
			foreach($perfiles as $aplicacionId => $perfilesApp){
				$aplicacion = self::getModel('Aplicaciones')->findFirst($aplicacionId);
				$dataPath = 'apps/identity/security/data/'.$aplicacion->getCodigo();
				if(!file_exists($dataPath)){
					mkdir($dataPath);
				}
				foreach($perfilesApp as $perfilId){
					if(isset($accessByRole[$aplicacionId][$perfilId])){
						foreach(self::getModel('PerfilesUsuarios')->find("perfiles_id='$perfilId'") as $perfilUsuario){
							$userAssigned[$aplicacion->getCodigo()][$perfilUsuario->getUsuariosId()] = true;
							$userAclPath = $dataPath.'/'.$perfilUsuario->getUsuariosId().'.php';
							file_put_contents($userAclPath, '<?php $acl = '.var_export($accessByRole[$aplicacionId][$perfilId], true).';');
						}
					}
				}
			}

			foreach(self::getModel('Aplicaciones')->find("tipo='B'") as $aplicacion){
				$dataPath = 'apps/identity/security/data/'.$aplicacion->getCodigo();
				if(!file_exists($dataPath)){
					mkdir($dataPath);
				}
				$aclPath = 'apps/identity/security/rules/'.$aplicacion->getCodigo().'/base.php';
				foreach(self::getModel('Usuarios')->find("estado='A'") as $usuario){
					if(!isset($userAssigned[$aplicacion->getCodigo()][$usuario->getId()])){
						if(file_exists($aclPath)){
							include $aclPath;
							$userAclPath = $dataPath.'/'.$usuario->getId().'.php';
							file_put_contents($userAclPath, '<?php $acl = '.var_export($acl, true).';');
						}
					}
				}
			}

			GarbageCollector::freeAllMetaData();

		}
		catch(Exception $e){
			throw new GardienException('Permisos: El directorio de datos de seguridad no tiene permisos de escritura ('.$dataPath.')'.' '.$e->getMessage());
		}

	}

	/**
	 * Registra un acceso correcto a la aplicación
	 *
	 */
	public static function successAccess(){
		$controllerRequest = ControllerRequest::getInstance();
		$application = Router::getApplication();
		$ipAddress = $controllerRequest->getClientAddress();
		$ipAddress = preg_replace('/[^0-9\.]/', '', $ipAddress);
		$path = 'apps/'.$application.'/logs/failedAuth-'.$ipAddress.'.log';
		if(file_exists($path)){
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

	public static function hasPanic(){
		$application = Router::getApplication();
		$path = 'apps/'.$application.'/logs/appPanic.log';
		if(file_exists($path)){
			return true;
		} else {
			return false;
		}
	}

}