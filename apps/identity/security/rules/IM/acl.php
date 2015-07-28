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
 *
 * Variable que controla las opciones del menu que maneja la aplicacion
 * @var $menuDisposition tipo Array
 */
$menuDisposition = array(
	array(
		'title' => 'Usuarios',
		'description' => 'Mantenimiento de usuarios y opciones permitidas',
		'options' => array(
			'usuarios',
			'sucursal',
			'perfiles',
			'perfiles_usuarios',
			'permisos_perfiles'
		)
	),
	array(
		'title' => 'Controles',
		'description' => 'Opciones de control del back-office',
		'options' => array(
			'permisos_comprob',
			'permisos_centros'
		)
	),
	array(
		'title' => 'Soporte Técnico',
		'description' => 'Opciones para apoyar el soporte técnico',
		'options' => array(
			'magenta',
			'console'
		)
	)
);


/**
 *
 * Variable que maneja los permisos de un controlador en una aplicación
 * @var $accessList tipo Array
 */

$accessList = array(

	//base
	'workspace' => array(
		'elevation' => true,
		'description' => 'Espacio de Trabajo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			),
			'storeElement' => array(
				'description' => 'Modificar el'
			),
			'getApplicationState' => array(
				'description' => 'Consultar el'
			)
		)
	),

	'productivity' => array(
		'elevation' => false,
		'description' => 'Productividad',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			),
			'canGetMail' => array(
				'description' => 'Consultar Correo Electrónico en'
			),
		)
	),

	'mail' => array(
		'elevation' => false,
		'description' => 'Correo Electrónico',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			),
			'getInbox' => array(
				'description' => 'Consultar bandeja de entrada de '
			),
			'getCompose' => array(
				'description' => 'Redactar mensajes de '
			),
			'getSent' => array(
				'description' => 'Visualizar mensajes enviados en '
			),
			'getTrash' => array(
				'description' => 'Visualizar mensajes borrados en '
			),
		)
	),

	'delivery' => array(
		'elevation' => false,
		'description' => 'Entrega de correo interno y externo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			),
			'send' => array(
				'description' => 'Enviar correos en'
			),
		)
	),

	'login' => array(
		'elevation' => false,
		'description' => 'Inició de Sesión',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			)
		)
	),

	'usuarios' => array(
		'elevation' => true,
		'description' => 'Usuarios del Sistema',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			),
			'config' => array(
				'description' => 'Datos de la Cuenta'
			)
		)
	),
	'sucursal' => array(
		'elevation' => true,
		'description' => 'Sucursales del Hotel',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'perfiles' => array(
		'elevation' => true,
		'description' => 'Perfiles',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'perfiles_usuarios' => array(
		'elevation' => true,
		'description' => 'Perfiles de usuarios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'permisos_perfiles' => array(
		'elevation' => true,
		'description' => 'Permisos de perfiles',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),

	//Controles
	'permisos_comprob' => array(
		'elevation' => true,
		'description' => 'Permisos de Comprobantes',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),
	'permisos_centros' => array(
		'elevation' => true,
		'description' => 'Permisos de Usuarios en Centros',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),

	//Magenta
	'magenta' => array(
		'elevation' => true,
		'description' => 'Mesa de Ayuda',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			)
		)
	),

	'console' => array(
		'elevation' => false,
		'description' => 'Consola Administrativa',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			)
		)
	),

);