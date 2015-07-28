<?php

$baseFiles = array(
	'core/base-source.js',
	'core/validations.js'
);

$compile = array(
	'appmenu.js' => array(
		'pos2/windows.js',
		'pos2/modal.js',
		'pos2/appmenu.js',
		'pos2/clave.js',
		'pos2/hash.js'
	),
	'mesas.js' => array(
		'pos2/windows.js',
		'pos2/mesas.js'
	),
	'pedido.js' => array(
		'pos2/windows.js',
		'pos2/modal.js',
		'pos2/pedido.js',
		'pos2/numero.js'
	),
	'factura.js' => array(
	),
	'pay.js' => array(
		'pos2/pay.js',
		'core/calendar-source.js'
	),
	'check.js' => array(
	),
	'admin-login.js' => array(
		'pos2/hash.js'
	),
	'admin.js' => array(
		'pos2/admin.js',
		'pos2/tablesort.js'
	),
	'reimprimir.js' => array(
		'pos2/windows.js',
		'pos2/modal.js',
		'pos2/numero.js',
		'pos2/hash.js',
		'pos2/reimprimir.js'
	),
	'anula_factura.js' => array(
		'pos2/windows.js',
		'pos2/modal.js',
		'pos2/numero.js',
		'pos2/hash.js',
		'pos2/anula_factura.js'
	),
	'reports.js' => array(
	),
	'upgrade.js' => array(
	),
);

$proto = file_get_contents('public/javascript/core/framework/scriptaculous/protoculous.js');

foreach($compile as $nfile => $cfiles){
	$command = "/System/Library/Frameworks/JavaVM.framework/Versions/1.6/Commands/java -jar ../compiler/compiler.jar ";
	foreach($baseFiles as $file){
		$command.=" --js=public/javascript/$file ";
	}
	foreach($cfiles as $file){
		$command.=" --js=public/javascript/$file ";
	}
	$command.=" --js_output_file=public/javascript/pos2/production/$nfile";
	echo $command, "\n";
	system($command);
	$ncontent = file_get_contents("public/javascript/pos2/production/$nfile");
	file_put_contents("public/javascript/pos2/production/$nfile", $proto.$ncontent);
}

$baseFiles = array(
	'core/base-source.js',
	'core/validations.js'
);

$compile = array(
	'appmenu.js' => array(
		'pos2/windows.js',
		'pos2/modal.js',
		'pos2/appmenu.js',
		'pos2/clave.js',
		'pos2/hash.js'
	)
);