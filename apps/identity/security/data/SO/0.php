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
	'tests' => array(
		'checkTerceros' => true
	),
  'migrar' => array(
    'syncTeceros' => true
  )

);
