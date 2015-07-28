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
 * Variable que controla las opciones del menu que maneja la aplicación
 *
 * @var $menuDisposition tipo Array
 */
$menuDisposition = array(
	array(
		'title' => 'Envios',
		'description' => 'Opciones de Envios',
		'options' => array(
			'recibos'
		)
	),
	array(
		'title' => 'Facturación',
		'description' => 'Opciones de Facturación',
		'options' => array(
		)
	),
	array(
		'title' => 'Informes',
		'description' => 'Informes de Socios',
		'options' => array(
		)
	),
	array(
		'title' => 'Cierres',
		'description' => 'Cierres',
		'options' => array(
			'cierre_periodo',
			'reabrir_periodo'
		)
	),
	array(
		'title' => 'Básicas',
		'description' => 'Opciones de Básicas en socios',
		'options' => array(
			'destinos',
			'terceros',
			'sucursales',
			'usuarios_sucursales',
			'settings'
		)
	)
);


/**
 *
 * Variable que maneja los permisos de un controlador en una aplicacion
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

	//Menú Envios
	'recibos' => array(
		'elevation' => true,
		'description' => 'Recibos de envio',
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

	//Menú Cierres
	'cierre_periodo' => array(
		'elevation' => true,
		'description' => 'Cerrar Periodo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'cierre' => array(
				'description' => 'generar el'
			),
		)
	),
	'reabrir_periodo' => array(
		'elevation' => true,
		'description' => 'Reabrir Periodo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'reabrir' => array(
				'description' => 'generar el'
			),
		)
	),
	

	//Menu Básicas
	'destinos' => array(
		'elevation' => true,
		'description' => 'Destinos de envio',
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
	'terceros' => array(
		'elevation' => true,
		'description' => 'Destinos de terceros',
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
	'sucursales' => array(
		'elevation' => true,
		'description' => 'Sucursales',
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
	'usuarios_sucursales' => array(
		'elevation' => true,
		'description' => 'Usuarios de Sucursales',
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
	
	'settings' => array(
		'elevation' => true,
		'description' => 'Configuración de Socios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'save' => array(
				'description' => 'Actualizar la'
			),
		)
	),

);
