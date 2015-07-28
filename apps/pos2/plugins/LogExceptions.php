<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class LogExceptionsPlugin extends ApplicationPlugin {

	/**
	 * Se ejecuta cuando se lanza una excepcion sin capturar
	 *
	 * @access public
	 */
	public function beforeUncaughtException($e){

		$controllerRequest = ControllerRequest::getInstance();
		$config = CoreConfig::readAppConfig();
		if($config->application->mode=='development'){
			throw $e;
		} else {

			$datos = $this->Datos->findFirst();

			$info = array(
				'appName' => ControllerBase::APP_NAME,
				'appVersion' => ControllerBase::APP_VERSION,
				'nit' => $datos->getNit(),
				'nombreCliente' => $datos->getNombreHotel(),
				'usuarioNombre' => Session::get('usuarios_nombre')
			);

			$message = get_class($e).': '.$e->getMessage().' Code='.$e->getCode().' Line='.$e->getLine().' File='.$e->getFile();
			$subject = $datos->getNit().' '.$message;
			$subject = substr(md5($subject), 0, 20);
			$text = $message."\n".print_r($e->getTrace(), true);

			return Socorro::sendEmail($subject, Socorro::getBody($info, $subject, $text));

		}

	}

}
