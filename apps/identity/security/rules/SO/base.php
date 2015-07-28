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

/**
 * Perfil base para un usuario válido en el sistema
 */
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
	'centros' => array(
		'queryByCentro' =>true,
		'queryByName' => true
	),
	'invoicing' => array(
		'index' => true,
		'adicionar' => true,
		'adicionar2' => true,
		'onRollback' => true
	),
	'terceros' => array(
		'queryByTercero' =>true,
		'queryByName' => true
	),
	'cuentas' =>  array(
		'queryByTercero' =>true,
		'queryByName' => true
	), 
	'migrar' => array(
		'prestamos' =>true
	),
	'tests' => array(
		'checkTerceros' => true
	)

);
