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
 * GardienController
 *
 * Ce contrôleur met en œuvre l'erreur sorties lorsque l'utilisateu
 * n'avez pas l'autorisation pour certaines actions ou lorsque
 * l'application est en état de panique
 *
 */
class GardienController extends ApplicationController
{

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if(!$controllerRequest->isSoapRequested()){
			if($controllerRequest->isAjax()){
				View::setRenderLevel(View::LEVEL_LAYOUT);
			}
		}
		parent::initialize();
	}

	/**
	 * Acción por defecto
	 *
	 */
	public function indexAction(){

	}

	/**
	 * Consulta si el usuario activo tiene permiso para acción consultada
	 *
	 * @return array
	 */
	public function checkAction()
	{
		$this->setResponse('json');
		$controllerRequest = ControllerRequest::getInstance();
		$appCode = '';
		$externResource = false;
		if($controllerRequest->isSetPostParam('resource')){
			$resourceName = $this->getPostParam('resource');
			$actionName = 'index';
			$appCode = '';
		} else {
			$resourceName =	$this->getPostParam('externResource');
			list($appName, $resourceName, $actionName) = explode('/', $resourceName);
			switch($appName){
				case 'identity':
					$appCode = 'IM';
					break;
			}
			$externResource = true;
		}
		if(Gardien::isAllowed($resourceName, $actionName, $appCode)){
			return array(
				'status' => 'OK'
			);
		} else {
			return array(
				'status' => 'FAILED',
				'accessInfo' => Gardien::getAccessInfo($resourceName, $actionName, $appCode)
			);
		}
	}

	/**
	 * Consulta si el usuario activo tiene permiso para acción consultada
	 *
	 * @return array
	 */
	public function elevateAction()
	{
		$this->setResponse('json');
		$resourceName = $this->getPostParam('resource');
		if(strpos($resourceName, '/')!==false){
			$appName = Router::getApplication();
			$resourceName = str_replace(Core::getInstancePath().$appName, '', $resourceName);
			list($resourceName, $actionName) = explode('/', $resourceName);
		} else {
			$actionName = 'index';
		}
		$login = $this->getPostParam('login');
		$password = $this->getPostParam('password');
		if(IdentityManager::startSession($login, $password, array($resourceName))){
			if(Gardien::isAllowed($resourceName, $actionName)){
				return array(
					'status' => 'OK'
				);
			} else {
				IdentityManager::destroyIdentity(array($resourceName));
				return array(
					'status' => 'FAILED',
					'accessInfo' => Gardien::getAccessInfo($resourceName, $actionName)
				);
			}
		} else {
			return array(
				'status' => 'FAILED'
			);
		}
	}

	/**
	 * Genera una salida apropiada para el tipo de contenido solicitado
	 * indicando que no tiene acesso
	 *
	 * @param string $errorMessage
	 */
	public function noAccessAction($resourceName, $actionName, $errorMessage='')
	{

		$request = ControllerRequest::getInstance();
		$accessInfo = Gardien::getAccessInfo($resourceName, $actionName);

		$routingType = Router::getRoutingAdapterType();
		if ($routingType == 'Json'){
			$this->setResponse('json');
			return array(
				'status' => 'SECURITY',
				'resource' => $resourceName,
				'action' => $actionName,
				'accessInfo' => $accessInfo
			);
		}

		$identity = IdentityManager::getActive('local');

		$response = ControllerResponse::getInstance();
		$response->setHeader('X-Application-State: Unauthorized', true);
		$response->setHeader('X-Acl-Application: ' . Router::getApplication(), true);
		$response->setHeader('X-Acl-Description: ' . utf8_decode($accessInfo['description']), true);
		$response->setHeader('X-Acl-Elevation: ' . $accessInfo['elevation'], true);
		$response->setHeader('X-Acl-Role-Code: ' . $identity['id'], true);
		$response->setHeader('X-Acl-Resource: ' . $resourceName, true);
		$response->setHeader('X-Acl-Action: ' . $actionName, true);

		var_dump($resourceName);

		if ($routingType == 'Default') {
			if ($request->isAjax()) {
				$this->setResponse('view');
			}
			$this->setParamToView('resourceName', $resourceName);
			$this->setParamToView('actionName', $actionName);
			return;
		}

		$extraData = array(
			'application'   => Router::getApplication(),
			'role-code'     => $identity['id'],
			'role-name'     => $identity['apellidos'] . ' ' . $identity['nombres'],
			'resource-name' => $resourceName,
			'action-name'   => $actionName
		);

		throw new GardienException('No tiene permiso para ingresar a "' . $accessInfo['description'] . '"' . print_r($accessInfo, true) . " " . print_r($extraData, true));
	}

}
