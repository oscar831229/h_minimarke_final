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
 * @copyright 	BH-TECK Inc. 2009-2014
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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @license 	New BSD License
 * @version 	$Id$
 */
class CompilePrototype extends Script
{

	private function _compile($destity, $sources)
	{
		$files = array();
		foreach ($sources as $jsSource) {
			$files[] = '--js=public/javascript/core/framework/scriptaculous/' . $jsSource;
		}
		if (PHP_OS == "Linux") {
			system('java -jar scripts/hfos/compiler.jar --language_in ECMASCRIPT5 ' . join(' ', $files) . ' --js_output_file=public/javascript/core/framework/scriptaculous/'.$destity.'.js');
		} else {
			system('/System/Library/Frameworks/JavaVM.framework/Versions/Current/Commands/java -jar scripts/hfos/compiler.jar --language_in ECMASCRIPT5 '.join(' ', $files).' --js_output_file=public/javascript/core/framework/scriptaculous/'.$destity.'.js');
		}
	}

	public function __construct()
	{
		$sources = array(
			'prototype.js',
			'scriptaculous.js',
			'builder.js',
			'effects.js',
			'dragdrop.js',
			'controls.js',
			'slider.js',
		);
		$this->_compile('protoculous', $sources, false);
	}

}

try {
	$script = new CompilePrototype();
}
catch(CoreException $e){
	echo get_class($e).' : '.$e->getConsoleMessage()."\n";
}
catch(Exception $e){
	echo 'Exception : '.$e->getMessage()."\n";
}
