<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

//Public Profile
$acl = array(

	//base
	'index' => array(
		'index' => true
	),
	'auth' => array(
		'startSession' => true,
		'hasSession' => true,
		'endSession' => true,
		'getIdentity' => true
	),
	'gardien' => array(
		'index' => true
	),
	'session' => array(
		'index' => true
	),
	'socorro' => array(
		'index' => true
	),
	'workspace' => array(
		'index' => true,
		'storeElement' => true,
		'getApplicationState' => true
	),
	'usuarios' => array(
		'config' => true
	),
	'login' => array(
		'index' => true
	),
	'magenta' => array(
		'index' => true
	),
	'productivity' => array(
		'index' => true
	),
	'mail' => array(
		'index' => true
	),
	'delivery' => array(
		'index' => true
	),
	'upgrade' => array(
		'index' => true
	),
	'welcome' => array(
		'index' => true
	),
	'invoicing' => array(
		'index' => true,
		'onRollback' => true
	)

);