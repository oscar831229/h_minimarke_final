<?php

//Public Profile
$acl = array(

	'index' => array(
		'index' => true
	),
	'gardien' => array(
		'index' => true,
		'noAccess' => true
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
	)

);