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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

require 'public/index.config.php';
require KEF_ABS_PATH.'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require KEF_ABS_PATH.'Library/Kumbia/Session/Session.php';
require KEF_ABS_PATH.'Library/Kumbia/Autoload.php';

/**
 * MigrateRegimen
 *
 * Migra las cuentas de regimen
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright 	BH-TECK Inc. 2009-2012
 * @license 	New BSD License
 * @version 	$Id$
 */
class MigrateRegimen extends Script {

	public function __construct(){

		$posibleParameters = array(
			'help' => "--help \t\t\tMuestra esta ayuda"
		);

		$this->parseParameters($posibleParameters);

		if($this->isReceivedOption('help')){
			$this->showHelp($posibleParameters);
			return;
		}
		
		Core::setTestingMode(Core::TESTING_LOCAL);
		Core::changeApplication('inve');

		$comprobBase = $this->Comprob->findFirst("codigo='E01'");
		if($comprobBase==false){
			throw new ScriptException("No existe el comprobante E01");
		}
		
		$regimenes = array(
			'C' => 'COMUN',
			'G' => 'GRAN CONTRIBUYENTE',
			'S' => 'SIMPLIFICADO'
		);
		foreach($regimenes as $regimen){
			$regimenCuentas = $this->RegimenCuentas->findFirst("regimen='$regimen'");
			if($regimenCuentas==false){
				$regimenCuentas = new RegimenCuentas();
				$regimenCuentas->setRegimen($regimen);
				$regimenCuentas->setCtaIva16d($comprobBase->getCtaIva());
				$regimenCuentas->setCtaIva10d($comprobBase->getCtaIvad());
				$regimenCuentas->setCtaIva16r($comprobBase->getCtaCartera());
				$regimenCuentas->setCtaIva10r($comprobBase->getCtaIvam());
				$regimenCuentas->setCtaIva16v($comprobBase->getCtaIva16Venta());
				$regimenCuentas->setCtaIva10v($comprobBase->getCtaIva10Venta());	
				if($regimenCuentas->save()==false){
					foreach($regimenCuentas->getMessages() as $message){
						throw new ScriptException($message->getMessage());
					}
				}
			}
		}

	}


}

try {
	$script = new MigrateRegimen();
}
catch(CoreException $e){
	echo get_class($e).' : '.$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	echo 'Exception : '.$e->getMessage()."\n";
}
