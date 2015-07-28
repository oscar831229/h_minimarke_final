<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back Office
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
		if($config->application->mode=='development'||PHP_OS=='Darwin'){
			throw $e;
		} else {
			Core::importFromLibrary('Hfos', 'Socorro/Socorro.php');
			Socorro::sendReport($e);
		}
	}

}
