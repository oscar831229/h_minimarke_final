<?php

//Public Access List
$acl = array(
	'admin' => array('index' => 1, 'login' => 1, 'logout' => 1),
	'appmenu' => array('index' => 1),
	'clave' => array('index' => 1, 'autenticar' => 1),
	'mobile' => array('index' => 1),
	'keyboard' => array('index' => 1),
	'context' => array('index' => 1),
	'upgrade' => array('index' => 1),
	'numero' => array('index' => 1),
	'socorro' => array('index' => 1),
	'updater' => array('index' => 1),
	'panic' => array('index' => 1),
	'firefox' => array('index' => 1),
	'cata' => array('index' => 1),
	'spool' => array('index' => 1),
	'invoice' => array('save' => 1),
	'sincronizar_terceros' => array(
		'sincronizar' => 1
	)
);
