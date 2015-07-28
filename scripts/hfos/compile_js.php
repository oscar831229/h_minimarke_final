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
require KEF_ABS_PATH . 'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require KEF_ABS_PATH . 'Library/Kumbia/Session/Session.php';
require KEF_ABS_PATH . 'Library/Kumbia/Autoload.php';

/**
 * CompileJS
 *
 * Compila el javascript usando closure
 *
 * @category 	Kumbia
 * @package 	Scripts
 * @copyright 	BH-TECK Inc. 2009-2010
 * @license 	New BSD License
 * @version 	$Id$
 */
class CompileJS extends Script 
{

	private function _compile($destity, $sources, $absolute=false, $flags=array())
	{
		$files = array();
		foreach (array_reverse($sources) as $jsSource) {
			if ($absolute == false) {
				$files[] = '--js=public/javascript/hfos/app/'.$destity.'/'.$jsSource.'.js';
			} else {
				$files[] = '--js=public/javascript/'.$jsSource.'.js';
			}
		}
		if(PHP_OS=="Linux"){
			$javaBin = 'java';
		} else {
			$javaBin = '/System/Library/Frameworks/JavaVM.framework/Versions/1.6/Commands/java';
		}
		system($javaBin.' -jar scripts/hfos/compiler.jar --language_in ECMASCRIPT5 --externs public/javascript/hfos/externs.js '.join(' ', $files).' --js_output_file=public/javascript/hfos/production/'.$destity.'.js');
	}

	private function _compileCSS($destity, $sources){
		$path = "public/css/hfos/production/$destity.css";
		unlink($path);
		foreach($sources as $cssSource){
			if(PHP_OS=='Linux'){
				$command = "java -jar scripts/hfos/yuicompressor-2.4.8.jar public/css/$cssSource >> public/css/hfos/production/$destity.css";
			} else {
				$command = "/System/Library/Frameworks/JavaVM.framework/Versions/1.6/Commands/java -jar scripts/hfos/yuicompressor-2.4.8.jar public/css/$cssSource >> public/css/hfos/production/$destity.css";
			}
			system($command);
		}
		$contents = file_get_contents($path);
		$contents = str_replace('../img', '../../img', $contents);
		$contents = str_replace('../files', '../../files', $contents);

		$contents = str_replace('../../../../img/', '../../../img/', $contents);
		$contents = str_replace('../../../../files/', '../../../files/', $contents);
		file_put_contents($path, $contents);
	}

	private function _join($destiny, $sources){
		$files = array();
		foreach($sources as $jsSource){
			$files[] = '--js=public/javascript/'.$jsSource.'.js';
		}
		if(PHP_OS=="Linux"){
			system('java -jar scripts/hfos/compiler.jar --language_in ECMASCRIPT5 --compilation_level WHITESPACE_ONLY '.join(' ', $files).' --js_output_file=temp.js');
		} else {
			system('/System/Library/Frameworks/JavaVM.framework/Versions/1.6/Commands/java -jar scripts/hfos/compiler.jar --language_in ECMASCRIPT5 --compilation_level WHITESPACE_ONLY '.join(' ', $files).' --js_output_file=temp.js');
		}
		system('mv temp.js public/javascript/hfos/production/'.$destiny.'.js');
	}

	public function __construct(){
		Core::setTestingMode(Core::TESTING_LOCAL);
		Core::changeApplication('identity');

		//Javascript
		if(!$this->isReceivedOption('only-css')){

			if(!$this->isReceivedOption('only-kernel')){
				foreach(array('CO', 'IN', 'IM', 'FC', 'SO', 'TC') as $appName){
					$this->_compile($appName, Hfos_Application::getJavascriptSources($appName), false);
				}
				$this->_compile('kernel', Hfos_Application::getCoreSources($appName), true);
			}

			//Join with Framework
			$this->_join('kernel', array('core/base-source', 'hfos/protoculous', 'hfos/production/kernel'));
		}

		//CSS
		if(!$this->isReceivedOption('only-js')){
			foreach(array('CO', 'IN', 'NO', 'IM', 'FC', 'IM', 'SO', 'TC') as $appName){
				$this->_compileCss($appName, array('hfos/app/'.$appName.'.css'));
			}
			$this->_compileCss('style', array('style.css', 'hfos/general.css', 'hfos/style.css'));
		}

	}

}

try {
	$script = new CompileJS();
}
catch(CoreException $e){
	echo get_class($e).' : '.$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	echo 'Exception : '.$e->getMessage()."\n";
}
