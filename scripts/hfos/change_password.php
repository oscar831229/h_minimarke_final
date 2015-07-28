<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Scripts
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

require 'public/index.config.php';
require KEF_ABS_PATH.'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require KEF_ABS_PATH.'Library/Kumbia/Session/Session.php';
require KEF_ABS_PATH.'Library/Kumbia/Autoload.php';

/**
 * CreateUser
 *
 * Crea un usuario con el login especificado y clave
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright 	BH-TECK Inc. 2009-2010
 * @license 	New BSD License
 * @version 	$Id$
 */
class ChangePassword extends Script {

	public function __construct(){

		$posibleParameters = array(
			'login=s' => "--login nombre-usuario \tLogin del usuario",
			'password=s' => "--password clave \tNueva clave deseada para el usuario",
			'help' => "--help \t\t\tMuestra esta ayuda"
		);

		$this->parseParameters($posibleParameters);

		if($this->isReceivedOption('help')){
			$this->showHelp($posibleParameters);
			return;
		}

		$this->checkRequired(array('login', 'password'));

		Core::setTestingMode(Core::TESTING_LOCAL);
		Core::changeApplication('identity');

		$login = $this->getOption('login', 'usuario');
		$password = $this->getOption('password');

		Rcs::disable();

		$usuario = $this->Usuarios->findFirst("login='$login'");
		if($usuario==false){
			throw new ScriptException('No existe un usuario con ese login');
		}

		$usuario->setClave(hash('tiger160,3', $usuario->getId().$password));
		if($usuario->save()==false){
			foreach($usuario->getMessages() as $message){
				echo $message->getMessage(), PHP_EOL;
			}
			throw new ScriptException('No se pudo crear el usuario');
		}

	}


}

try {
	$script = new ChangePassword();
}
catch(CoreException $e){
	echo get_class($e).' : '.$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	echo 'Exception : '.$e->getMessage()."\n";
}
