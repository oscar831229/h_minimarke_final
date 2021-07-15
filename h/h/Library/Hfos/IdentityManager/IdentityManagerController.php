<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2011
 * @version		$Id$
 */

/**
 * IdentityManagerController
 *
 * Cada aplicación tiene un controlador llamado session que hereda de este controlador
 * Implementa las funciones de inicio, terminación y consulta de sesiones en el
 * IdentityManager (aplicación identity) desde cualquier aplicación
 *
 */
class IdentityManagerController extends WebServiceController {

	/**
	 * Crea una sesión en el IdentityManager autenticandose con login/password
	 *
	 * @param 	string $login
	 * @param 	string $password
	 * @return	boolean
	 */
	public function startAction($login, $password){
		return IdentityManager::startSession($login, $password);
	}

	/**
	 * Crea una sesión en el IdentityManager autenticandose con la huella del usuario
	 *
	 * @param 	string $login
	 * @param 	string $fingerprint
	 * @return	boolean
	 */
	public function startWithFingerprintAction($login, $fingerprint){
		return IdentityManager::startSessionFingerprint($login, $fingerprint);
	}

	/**
	 * Termina la sesión en el IdentityManager
	 *
	 */
	public function endAction(){
		return IdentityManager::endSession();
	}

	/**
	 * Obtiene los datos de la sesión activa
	 *
	 * @return array
	 */
	public function getIdentityAction(){
		return IdentityManager::getActive();
	}

	/**
	 * Permite saber si el usuario tiene una sesión activa
	 *
	 * @return boolean
	 */
	public function existsAction(){
		return IdentityManager::hasActive();
	}

}