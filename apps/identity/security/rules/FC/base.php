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
	'upgrade' => array(
		'index' => true
	),
	'welcome' => array(
		'index' => true
	),
	'pedidos' => array(
		'consultar' => true
	),
	'tatico' => array(
		'getPedido' => true
	),
	'invoicing' => array(
		'index' => true,
		'onRollback' => true
	),
	'referencias' => array (
		'queryByItem' => true,
		'queryByName' => true
	),
	'conceptos' => array (
		'queryByItem' => true,
		'queryByName' => true
	),
	'cuentas' => array (
		'queryByItem' => true,
		'queryByName' => true
	),
);
