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
class CompileJS extends Script
{

	private $_sources = array(
		'admin' => array(
			'core/base-source',
			'pos2/hash',
			'pos2/admin'
		),
		'appmenu' => array(
			'core/base-source',
			'core/validations',
			'pos2/windows',
			'pos2/modal',
			'pos2/appmenu',
			'pos2/clave',
			'pos2/hash'
		),
		'order' => array(
			'core/base-source',
			'pos2/windows',
			'pos2/modal',
			'pos2/order',
			'pos2/numero',
			'pos2/keyboard'
		),
		'cancel' => array(
			'core/base-source',
			'core/validations',
		),
		'status' => array(
			'core/base-source',
			'core/validations'
		),
		'check' => array(
			'core/base-source',
			'core/validations',
			'pos2/check'
		),
		'cashouttro' => array(
			'core/base-source',
			'core/validations',
		),
		'reports' => array(
			'core/base-source',
			'core/validations',
		),
		'audit' => array(
			'core/base-source',
			'core/validations',
		),
		'pay' => array(
			'core/base-source',
			'core/validations',
			'pos2/pay',
			'pos2/modal',
			'pos2/keyboard',
			'pos2/windows'
		),
		'anula_factura' => array(
			'core/base-source',
			'pos2/windows',
			'pos2/modal',
			'pos2/numero',
			'pos2/hash',
			'pos2/anula_factura',
		),
		'reimprimir' => array(
			'core/base-source',
			'pos2/windows',
			'pos2/modal',
			'pos2/numero',
			'pos2/hash',
			'pos2/reimprimir'
		),
		'tables' => array(
			'core/base-source',
			'core/validations',
			'pos2/tables',
			'pos2/modal',
			'pos2/keyboard',
			'pos2/windows'
		),
	);

	private $_cssSources = array(
		'appmenu' => array(
			'style.css',
			'pos2/style.css',
			'pos2/clave.css',
			'pos2/appmenu.css'
		),
		'order' => array(
			'style.css',
			'pos2/style.css',
			'pos2/order.css',
			'pos2/numero.css'
		),
		'tables' => array(
			'style.css',
			'pos2/style.css',
			'pos2/tables.css'
		),
		'cancel' => array(
			'style.css',
			'pos2/style.css',
			'pos2/status.css'
		),
		'status' => array(
			'style.css',
			'pos2/style.css',
			'pos2/status.css'
		),
		'check' => array(
			'style.css',
			'pos2/style.css',
			'pos2/check.css'
		),
		'pay' => array(
			'style.css',
			'pos2/style.css',
			'pos2/pay.css',
			'pos2/numero.css'
		),
		'cashouttro' => array(
			'style.css',
			'pos2/style.css',
			'pos2/cashintro.css'
		),
		'reports' => array(
			'style.css',
			'pos2/style.css',
			'pos2/status.css'
		),
		'audit' => array(
			'style.css',
			'pos2/style.css',
			'pos2/status.css'
		),
		'admin' => array(
			'style.css',
			'pos2/style.css',
			'pos2/admin.css'
		),
		'anular' => array(
			'style.css',
			'pos2/style.css',
			'pos2/status.css',
			'pos2/numero.css',
			'pos2/anular.css'
		),
		'reimprimir' => array(
			'style.css',
			'pos2/style.css',
			'pos2/status.css',
			'pos2/numero.css',
			'pos2/reimprimir.css'
		),
	);

	private function _compile($destity, $sources)
	{
		$files = array();
		foreach ($sources as $jsSource) {
			$files[] = '--js=public/javascript/'.$jsSource.'.js';
		}
		if (PHP_OS == 'Linux') {
			$command = 'java -jar scripts/hfos/compiler.jar '.join(' ', $files).' --js_output_file=public/temp/temp.js';
		} else {
			$command = '/System/Library/Frameworks/JavaVM.framework/Versions/Current/Commands/java -jar scripts/hfos/compiler.jar '.join(' ', $files).' --js_output_file=public/temp/temp.js';
		}
		system($command);
		system('cat public/javascript/core/framework/scriptaculous/protoculous.js public/temp/temp.js > public/javascript/pos2/production/'.$destity.'.js');
		unlink('public/temp/temp.js');
	}

	private function _compileCSS($destity, $sources)
	{
		$path = "public/css/pos2/production/$destity.css";
		unlink($path);
		foreach ($sources as $cssSource) {
			if (PHP_OS=='Linux') {
				$command = "java -jar scripts/hfos/yuicompressor-2.4.8.jar public/css/$cssSource >> public/css/pos2/production/$destity.css";
			} else {
				$command = "/System/Library/Frameworks/JavaVM.framework/Versions/Current/Commands/java -jar scripts/hfos/yuicompressor-2.4.8.jar public/css/$cssSource >> public/css/pos2/production/$destity.css";
			}
			system($command);
		}
		$contents = file_get_contents(KEF_ABS_PATH . $path);
		$contents = str_replace('../img', '../../img', $contents);
		$contents = str_replace('../files', '../../files', $contents);
		file_put_contents($path, $contents);
	}

	public function __construct()
	{
		foreach ($this->_sources as $destiny => $sources) {
			$this->_compile($destiny, $sources);
		}
		foreach ($this->_cssSources as $destiny => $sources) {
			$this->_compileCSS($destiny, $sources);
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
