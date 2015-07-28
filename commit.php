<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

if(isset($_SERVER['argv'][1])){
	$files = array(
		'apps/pos2/controllers/application.php',
		'Library/Hfos/Application/Application.php'
	);
	foreach($files as $file){
		$contents = file_get_contents($file);
		$contents = preg_replace('/\$Last-Commit: .*\$/', '$Last-Commit: '.date('r').'$', $contents);
		file_put_contents($file, $contents);
	}
	system("hg pull -u");
	system("hg commit -m\"{$_SERVER['argv'][1]}\"");
}
