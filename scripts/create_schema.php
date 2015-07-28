<?php

/**
 * Kumbia Enterprise Framework
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@loudertechnology.com so we can send you a copy immediately.
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright	Copyright (c) 2008-2011 Louder Technology COL. (http://www.loudertechnology.com)
 * @license 	New BSD License
 * @version 	$Id$
 */
require 'public/index.config.php';
require KEF_ABS_PATH.'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require KEF_ABS_PATH.'Library/Kumbia/Autoload.php';


class RemoveEmptyTags extends ActiveRecordMigration {

	public static function up(){
		foreach(Menus::findAll() as $menu){
			if($menu->countMenusItems()==0){
				$menu->destroy();
			}
		}
	}

}

class CreateSchema extends Script {

	public function __construct(){

		$posibleParameters = array(
			'application=s' => "--application nombre \tNombre de la aplicación [opcional]",
			'environment=s' => "--environment nombre \tNombre de la entorno de la conexión [opcional]",
			'file-dest=s' => "--file-dest ruta \tRuta donde se creará el esquema [opcional]",
			'force' => "--force \t\tForza a que se reescriba el esquema [opcional]",
			'help' => "--help \t\t\tMuestra esta ayuda"
		);

		$this->parseParameters($posibleParameters);

		if($this->isReceivedOption('help')){
			$this->showHelp($posibleParameters);
			return;
		}

		$application = $this->getOption('application');
		if(!$application){
			$application = 'default';
		}

		Router::setActiveApplication($application);
		Core::reloadMVCLocations();

		$modelsDir = Core::getActiveModelsDir();
		EntityManager::initModelBase($modelsDir);
		EntityManager::initModels($modelsDir);

		$config = CoreConfig::readAppConfig();
		if(!isset($config->application->mode)){
			throw new ScriptException('No se ha definido el entorno por defecto de la aplicación');
		}

		//sActiveRecordMigration::generateAll($config->application->mode);

		RemoveEmptyTags::up();
	}



}

try {
	$script = new CreateSchema();
}
catch(Exception $e){
	Script::showConsoleException($e);
}

