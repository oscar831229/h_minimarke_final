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
 * GrantComprob
 *
 * Asigna permisos a comprobantes
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright 	BH-TECK Inc. 2009-2010
 * @license 	New BSD License
 * @version 	$Id$
 */
class GrantComprob extends Script {

	public function __construct(){

		$posibleParameters = array(
			'user=s' => "--user nombre-usuario \tLogin del usuario",
			'grant=s' => "--grant permisos \tPermisos a asignar al usuario",
			'comprobs=s' => "--grant permisos \tComprobantes donde se asignarán los permisos",
			'help' => "--help \t\t\tMuestra esta ayuda"
		);

		$this->parseParameters($posibleParameters);

		if($this->isReceivedOption('help')){
			$this->showHelp($posibleParameters);
			return;
		}

		$this->checkRequired(array('user', 'grant', 'comprobs'));

		Core::setTestingMode(Core::TESTING_LOCAL);
		Core::changeApplication('identity');

		$login = $this->getOption('user', 'usuario');
		$grant = $this->getOption('grant');
		$comprobs = $this->getOption('comprobs');

		$usuario = $this->Usuarios->findFirst("login='$login'");
		if($usuario==false){
			throw new ScriptException('No existe un usuario con ese login');
		}

		if($grant=='all'){
			$perms = array('A', 'M', 'S', 'D', 'R');
		} else {
			$perms = array();
			$posiblePerms = array('add' => 'A', 'modify' => 'M', 'select' => 'S', 'delete' => 'D', 'copy' => 'R');
			foreach(explode(',', $grant) as $permToGrant){
				if(!isset($posiblePerms[$permToGrant])){
					throw new ScriptException('No está definido el tipo de permiso "'.$permToGrant.'"');
				} else {
					$perms[] = $posiblePerms[$permToGrant];
				}
			}
		}

		$listComprob = array();
		if($comprobs=='all'){
			foreach($this->Comprob->find() as $comprob){
				$listComprob[] = $comprob->getCodigo();
			}
		} else {
			foreach(explode(',', $comprobs) as $codigoComprob){
				$codigoComprob = $this->filter($codigoComprob, 'comprob');
				if($this->Comprob->count("codigo='$codigoComprob'")>0){
					$listComprob[] = $codigoComprob;
				} else {
					throw new ScriptException('No existe el tipo de comprobante "'.$codigoComprob.'"');
				}
			}
		}

		if(count($listComprob)==0){
			throw new ScriptException('No se indicaron tipos de comprobantes activos');
		}

		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->PermisosComprob->setTransaction($transaction);
			foreach($listComprob as $codigoComprob){
				$this->PermisosComprob->deleteAll("usuarios_id='{$usuario->getId()}' AND comprob='$codigoComprob'");
				foreach($perms as $perm){
					$permisoComprob = new PermisosComprob();
					$permisoComprob->setTransaction($transaction);
					$permisoComprob->setUsuariosId($usuario->getId());
					$permisoComprob->setComprob($codigoComprob);
					$permisoComprob->setPopcion($perm);
					if($permisoComprob->save()==false){
						foreach($permisoComprob->getMessages() as $message){
							throw new ScriptException($message->getMessage());
						}
					}
				}
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){

		}

	}

}

try {
	$script = new GrantComprob();
}
catch(CoreException $e){
	echo get_class($e).' : '.$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	echo 'Exception : '.$e->getMessage()."\n";
}
