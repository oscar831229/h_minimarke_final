<?php

//Public Profile
$acl = array(

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
	'login' => array(
		'index' => true
	),
	'invoicing' => array(
		'index' => true,
		'onRollback' => true
	)

);