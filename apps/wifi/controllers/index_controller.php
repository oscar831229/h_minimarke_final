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

/**
 * IndexController
 *
 * Controlador de creación de session en wifi captivate
 *
 */
class IndexController extends ApplicationController {

	public function indexAction(){
		$request = ControllerRequest::getInstance();
		$ipAddress = $request->getClientAddress();
		$session = $this->Sessions->findFirst("ip='$ipAddress'");
		if($session!=false){
			if($session->getTimeAllow()>=time()){
				$response = ControllerResponse::getInstance();
				$response->setHeader('Location: http://www.google.com');
				return;
			}
		}
	}

	public function registerPinAction(){
		$response = ControllerResponse::getInstance();

		$codigoPin = $this->getPostParam('pin', 'alpha');
		if($codigoPin==''){
			Flash::error('Por favor indique el PIN de internet');
			return $this->routeToAction('index');
		}

		$pin = $this->Pines->findFirst("codigo='$codigoPin' AND estado='P'");
		if($pin==false){
			Flash::error('El PIN de Internet no es válido');
			return $this->routeToAction('index');
		} else {
			$pin->estado = 'U';
			if($pin->save()==false){
				foreach($pin->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				return $this->routeToAction('index');
			}
		}

		$request = ControllerRequest::getInstance();
		$ipAddress = $request->getClientAddress();
		$session = $this->Sessions->findFirst("ip='$ipAddress'");
		if($session!=false){
			if($session->getTimeAllow()>=time()){
				$host = $request->getParamServer('HTTP_HOST');
				if($host!='www.google.com'){
					$response->setHeader('Location: http://www.google.com');
					return;
				} else {
					Flash::notice('Ya tiene un PIN activo registrado, intente ingresar a internet');
					return $this->routeToAction('index');
				}
			} else {
				$session->delete();
			}
		}
		$session = new Sessions();
		$session->setIp($ipAddress);
		$session->setTimeAllow(time()+60*$pin->tiempo);
		if($session->save()==false){
			foreach($session->getMessages() as $message){
				Flash::error($message->getMessage());
			}
		} else {
			if($request->isSetPostParam('url')){
				$response->setHeader('Location: http://'.$request->getParamPost('url'));
			} else {
				$response->setHeader('Location: http://www.google.com');
			}
		}

	}

}