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
 * WorkspaceManagerController
 *
 * Implementa la parte del lado del servidor del entorno de trabajo del usuario
 *
 */
class WorkspaceManagerController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

	}

	/**
	 * Obtiene el estado de la aplicación
	 *
	 * @return array
	 */
	public function getApplicationStateAction(){
		$this->setResponse('json');
		try {
			$identity = IdentityManager::getActive();
			$appCode = $this->getPostParam('appCode', 'alpha');
			$response = array(
				'status' => 'OK',
				'tokenId' => $identity['tokenId'],
				'options' => $identity['options']
			);
			$userSession = $this->UserSession->findFirst("usuarios_id='{$identity['id']}' AND app_code='$appCode'");
			if($userSession==false){
				$response['state'] = 'new';
			} else {
				$response['state'] = $userSession->getState();
			}
			return $response;
		}
		catch(IdentityManagerException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	/**
	 * Establece el estado de una aplicación
	 *
	 * @return array
	 */
	public function setApplicationStateAction(){

		$this->setResponse('json');

		$code = $this->getPostParam('appCode', 'alpha');
		$state = $this->getPostParam('state');

		try {
			$identity = IdentityManager::getActive();
			$userSession = $this->UserSession->findFirst("usuarios_id='{$identity['id']}' AND app_code='$code'");
			if($userSession==false){
				$userSession = new UserSession();
				$userSession->setUsuariosId($identity['id']);
				$userSession->setAppCode($code);
			}
			$userSession->setToken($identity['tokenId']);
			$userSession->setState($state);
			$userSession->setPingTime(time());
			if($userSession->save()==false){
				foreach($userSession->getMessages() as $message){
					return array(
						'status' => 'FAILED',
						'message' => $message->getMessage()
					);
				}
			} else {
				return array(
					'status' => 'OK'
				);
			}
		}
		catch(IdentityManagerException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	/**
	 * Obtiene los recursos externos que requiere una aplicación para iniciarse
	 *
	 * @return array
	 */
	public function getSourcesAction(){
		$this->setResponse('json');
		$mode = CoreConfig::getAppSetting('mode');
		$appCode = $this->getPostParam('appCode', 'alpha');
		if(file_exists('public/javascript/hfos/app/'.$appCode)){
			$total = 0;
			$sources = array();
			if($mode=='production'){
				$size = filesize('public/javascript/hfos/production/'.$appCode.'.js');
				$sources[] = array(
					'name' => $appCode,
					'type' => 'js',
					'size' => $size
				);
				$total+=$size;
			} else {
				$jsSources = Hfos_Application::getJavascriptSources($appCode);
				foreach(array_reverse($jsSources) as $source){
					$size = filesize('public/javascript/hfos/app/'.$appCode.'/'.$source.'.js');
					$sources[] = array(
						'name' => $source,
						'type' => 'js',
						'size' => $size
					);
					$total+=$size;
				}
			}
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'No se encontró la aplicación '.$appCode
			);
		}
		if($mode=='production'){
			if(file_exists('public/css/hfos/production/'.$appCode.'.css')){
				$size = filesize('public/css/hfos/production/'.$appCode.'.css');
				$sources[] = array(
					'name' => $appCode,
					'type' => 'css',
					'size' => $size
				);
				$total+=$size;
			}
		} else {
			if(file_exists('public/css/hfos/app/'.$appCode.'.css')){
				$size = filesize('public/css/hfos/app/'.$appCode.'.css');
				$sources[] = array(
					'name' => $appCode,
					'type' => 'css',
					'size' => $size
				);
				$total+=$size;
			}
		}
		return array(
			'status' => 'OK',
			'mode' => $mode,
			'total' => $total,
			'sources' => $sources
		);
	}

	/**
	 * Obtiene los elementos que serán mostrados en el menú inicio de la aplicación
	 *
	 * @return array
	 */
	public function getStartItemsAction(){
		$this->setResponse('json');
		$recentItems = array();
		$code = CoreConfig::getAppSetting('code');
		switch($code){
			case 'CO':
				$movis = $this->Movi->find(array('columns' => 'comprob,numero', 'group' => 'comprob,numero', 'order' => 'fecha DESC', 'limit' => 7));
				if(count($movis)){
					$lastComprobs = array();
					foreach($movis as $movi){
						$comprob = BackCacher::getComprob($movi->getComprob());
						$lastComprobs[] = array(
							'key' => 'codigoComprobante='.$movi->getComprob().'&numero='.$movi->getNumero(),
							'text' => $comprob->getNomComprob().'/'.$movi->getNumero()
						);
					}
					$recentItems[] = array(
						'name' => 'Comprobantes Recientes',
						'items' => $lastComprobs,
						'icon' => 'document-library.png'
					);
				}
				break;
			case 'IN':
				$moviheads = $this->Movihead->find(array('columns' => 'comprob,numero', 'order' => 'fecha DESC', 'limit' => 7));
				if(count($moviheads)){
					foreach($moviheads as $movihead){
						$comprob = BackCacher::getComprob($movihead->getComprob());
						$tipoComprob = substr($movihead->getComprob(), 0, 1);
						if(!isset($recentItems[$tipoComprob])){
							switch($tipoComprob){
								case 'E':
									$tipo = 'Entradas al Almacén';
									$icon = 'entradas.png';
									break;
								case 'O':
									$tipo = 'Ordenes de Compra';
									$icon = 'ordenes.png';
									break;
								case 'P':
									$tipo = 'Pedidos al Almacén';
									$icon = 'order-149.png';
									break;
								case 'A':
									$tipo = 'Ajustes al Almacén';
									$icon = 'ajustes.png';
									break;
								case 'C':
									$tipo = 'Salidas Almacén';
									$icon = 'salidas.png';
									break;
								case 'R':
									$tipo = 'Transformaciones';
									$icon = 'sitemap.png';
									break;
								default:
									$tipo = 'Desconocido';
									$icon = 'entradas.png';
									break;
							}
							$recentItems[$tipoComprob] = array(
								'name' => $tipo,
								'items' => array(),
								'icon' => $icon
							);
						}
						$recentItems[$tipoComprob]['items'][] = array(
							'key' => 'codigoComprobante='.$movihead->getComprob().'&numero='.$movihead->getNumero(),
							'text' => $comprob->getNomComprob().'/'.$movihead->getNumero()
						);
					}
				}
				break;
		}
		return array(
			'status' => 'OK',
			'items' => array_values($recentItems)
		);
	}

	public function setPageCheckAction(){
		$this->setResponse('view');
		$identity = IdentityManager::getActive();
		$pageName = $this->getPostParam('pageName', 'alpha');
		if(!$this->PageChecks->count("usuarios_id='{$identity['id']}' AND name='$pageName'")){
			$pageCheck = new PageChecks();
			$pageCheck->setUsuariosId($identity['id']);
			$pageCheck->setName($pageName);
			$pageCheck->save();
		}
	}

	/**
	 * Obtiene un Poll de Mensajes para la sesión activa
	 *
	 */
	public function getMessagesAction(){
		$this->setResponse('json');

		$messages = array();
		$currentTime = time();

		//En este Namespace se guardan los timeouts de los tipos de mensajes
		if(!SessionNamespace::exists('messagesTimeout')){
			$messagesTimeout = SessionNamespace::add('messagesTimeout');
			$messagesTimeout->setTime($currentTime);
			$messagesTimeout->setMail($currentTime);
		} else {
			$messagesTimeout = SessionNamespace::get('messagesTimeout');
		}

		//Hora Actual
		if($currentTime-3600>=$messagesTimeout->getTime()){
			$messages[] = array(
				'type' => 'time',
				'message' => HfosTime::getCurrentTime()
			);
			$messagesTimeout->setTime($currentTime);
		}

		//Mensajes Nuevos
		if($currentTime-900>=$messagesTimeout->getMail()){
			if(HfosMail::haveMail()==true){
				$messages[] = array(
					'type' => 'mail',
					'unread' => HfosMail::getUnreadCount(),
					'inbox' => HfosMail::getInboxCount(),
					'trash' => HfosMail::getTrashCount(),
				);
			}
			$messagesTimeout->setMail($currentTime);
		}

		//Fin de la sesión
		$identity = IdentityManager::getActive();
		if(!$identity['id']){
			$messages[] = array(
				'type' => 'endSession'
			);
		}

		return array(
			'status' => 'OK',
			'messages' => $messages
		);
	}

	public function getProductivityAction(){

	}

}