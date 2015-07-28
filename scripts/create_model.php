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
 * to kumbia@kumbia.org so we can send you a copy immediately.
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright	Copyright (c) 2008-2011 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2011 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @version 	$Id$
 */
error_reporting(E_ERROR);

require 'public/index.config.php';
require KEF_ABS_PATH.'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require KEF_ABS_PATH.'Library/Kumbia/Autoload.php';

/**
 * CreateModel
 *
 * Permite crear un modelo por linea de comandos
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright	Copyright (c) 2008-2011 Louder Technology COL. (http://www.loudertechnology.com)
 * @copyright 	Copyright (c) 2005-2011 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license 	New BSD License
 * @version 	$Id$
 */
class CreateModel extends Script {

	/**
	 * Devuelve el tipo PHP asociado
	 *
	 * @param string $type
	 * @return string
	 */
	public function getPHPType($type){
		if(stripos($type, 'int')!==false){
			return 'integer';
		}
		if(stripos($type, 'int')!==false){
			return 'integer';
		}
		if(strtolower($type)=='date'){
			return 'Date';
		}
		return 'string';
	}

	public function run(){

		$posibleParameters = array(
			'table-name=s' => "--table-name nombre \tNombre de la tabla source del modelo",
			'schema=s' => "--schema nombre \tNombre del schema donde está la tabla si este difiere del schema\n\t\t\tpor defecto [opcional]",
			'application=s' => "--application nombre \tNombre de la aplicación [opcional]",
			'class-name=s' => "--class-name nombre \tNombre de la clase PHP que utilizará el modelo",
			'force' => "--force \t\tForza a que se reescriba el modelo [opcional]",
			'debug' => "--debug \t\tMuetra la traza del framework en caso que se genere una excepción [opcional]",
			'help' => "--help \t\t\tMuestra esta ayuda"
		);

		$this->parseParameters($posibleParameters);

		if($this->isReceivedOption('help')){
			$this->showHelp($posibleParameters);
			return;
		}

		$this->checkRequired(array('table-name'));

		$name = $this->getOption('table-name');
		$application = $this->getOption('application');
		$schema = $this->getOption('schema');
		if(!$application){
			$application = 'default';
		}

		$className = $this->getOption('class-name');
		if(!$className){
			$className = $name;
		}

		$className = Utils::camelize($name);
		$fileName = Utils::uncamelize($className);

		Core::setTestingMode(Core::TESTING_LOCAL);
		Core::changeApplication($application);
		if($name){
			$methodRawCode = array();
			$modelsDir = Core::getActiveModelsDir();
			$modelPath = $modelsDir.'/'.$fileName.'.php';
			if(file_exists($modelPath)){
				if(!$this->isReceivedOption('force')){
					throw new ScriptException("El archivo del modelo '$fileName.php' ya existe en el directorio de modelos");
				} else {
					try {
						require $modelPath;
						$linesCode = file($modelPath);
						$reflection = new ReflectionClass($className);
						foreach($reflection->getMethods() as $method){
							if($method->getDeclaringClass()->getName()==$className){
								$methodName = $method->getName();
								if(substr($methodName, 0, 3)!='set'){
									if(substr($methodName, 0, 3)!='get'){
										$methodRawCode[$methodName] = join('', array_slice($linesCode, $method->getStartLine()-1, $method->getEndLine()-$method->getStartLine()+1));
									}
								}
							}
						}
					}
					catch(ReflectionException $e){

					}
				}
			}
			if(!DbLoader::loadDriver()){
				throw new DbException('No se puede conectar a la base de datos');
			}
			$db = DbBase::rawConnect();
			$initialize = array();
			if($schema){
				$initialize[] = "\t\t\$this->setSchema(\"$schema\");";
			}
			if($fileName!=$name){
				$initialize[] = "\t\t\$this->setSource(\"$name\");";
			}
			$table = $name;
			if($db->tableExists($table, $schema)){
				$fields = $db->describeTable($name, $schema);
				$attributes = array();
				$setters = array();
				$getters = array();
				foreach($fields as $field){
					$type = $this->getPHPType($field['Type']);
					$attributes[] = "\t/**\n\t * @var $type\n\t */\n\tprotected \${$field['Field']};\n";
					$setterName = Utils::camelize($field['Field']);
					$setters[] = "\t/**\n\t * Metodo para establecer el valor del campo {$field['Field']}\n\t * @param $type \${$field['Field']}\n\t */\n\tpublic function set$setterName(\${$field['Field']}){\n\t\t\$this->{$field['Field']} = \${$field['Field']};\n\t}\n";
					if($type=="Date"){
						$getters[] = "\t/**\n\t * Devuelve el valor del campo {$field['Field']}\n\t * @return $type\n\t */\n\tpublic function get$setterName(){\n\t\tif(\$this->{$field['Field']}){\n\t\t\treturn new Date(\$this->{$field['Field']});\n\t\t} else {\n\t\t\treturn null;\n\t\t}\n\t}\n";
					} else {
						$getters[] = "\t/**\n\t * Devuelve el valor del campo {$field['Field']}\n\t * @return $type\n\t */\n\tpublic function get$setterName(){\n\t\treturn \$this->{$field['Field']};\n\t}\n";
					}
				}
				if(count($initialize)>0){
					$initCode = "\n\t/**\n\t * Metodo inicializador de la Entidad\n\t */\n\tprotected function initialize(){\t\t\n".join(";\n", $initialize)."\n\t}\n";
				} else {
					$initCode = "";
				}
				$code = "<?php\n";
				if(file_exists('license.txt')){
					$code.=PHP_EOL.file_get_contents('license.txt');
				}
				$code.="\nclass ".$className." extends ActiveRecord {\n\n".join("\n", $attributes)."\n\n".join("\n", $setters)."\n\n".join("\n", $getters)."$initCode\n";
				foreach($methodRawCode as $methodCode){
					$code.=$methodCode.PHP_EOL;
				}
				$code.="}\n\n";
				file_put_contents("$modelsDir/$fileName.php", $code);
			} else {
				throw new ScriptException("No existe la tabla $table");
			}
		} else {
			throw new ScriptException("Debe indicar el nombre del modelo");
		}
	}

}

try {
	$script = new CreateModel();
	$script->run();
}
catch(CoreException $e){
	ScriptColor::lookSupportedShell();
	echo ScriptColor::colorize(get_class($e).' : '.$e->getConsoleMessage()."\n", ScriptColor::LIGHT_RED);
	if($script->getOption('debug')=='yes'){
		echo $e->getTraceAsString()."\n";
	}
}
catch(Exception $e){
	echo 'Exception : '.$e->getMessage()."\n";
}
