<?php

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
		'storeElement' => false,
		'getApplicationState' => false
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