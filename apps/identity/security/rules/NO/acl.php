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
 * Variable que controla las opciones del menu que maneja la aplicación
 *
 * @var $menuDisposition tipo Array
 */
$menuDisposition = array(
	array(
		'title' => 'Liquidación',
		'description' => 'Opciones de liquidación de nomina',
		'options' => array(
			'liquidacion'
		)
	),
	array(
		'title' => 'Básicas',
		'description' => 'Opciones de básicas',
		'options' => array(
			'empleados',
			'contratos',
			'cargos',
			'ubicacion',
			'fondos',
			'conceptos_basicos',
			'conceptos_devengos',
			'conceptos_descuentos',
			'conceptos_licencias',
			'retencion',
			'concentro',
			'settings'
		)
	)
);

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

	//Liquidacion
	'liquidacion' => array(
		'elevation' => true,
		'description' => 'Liquidación',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),

	//basicas
	'empleados' => array(
		'elevation' => true,
		'description' => 'Empleados',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'contratos' => array(
		'elevation' => true,
		'description' => 'Contratos de Empleados',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'fondos' => array(
		'elevation' => true,
		'description' => 'Fondos',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'cargos' => array(
		'elevation' => true,
		'description' => 'Fondos',
		'actions' => array(
			'index' => array(
				'description' => 'Cargos de Empleados',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'conceptos_basicos' => array(
		'elevation' => true,
		'description' => 'Conceptos Básicos de Nomina',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'conceptos_devengos' => array(
		'elevation' => true,
		'description' => 'Conceptos Devengos de Nomina',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'conceptos_descuentos' => array(
		'elevation' => true,
		'description' => 'Conceptos de Descuentos de Nomina',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'conceptos_licencias' => array(
		'elevation' => true,
		'description' => 'Conceptos de Licencias ó Suspensiones',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'retencion' => array(
		'elevation' => true,
		'description' => 'Retención',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'concentro' => array(
		'elevation' => true,
		'description' => 'Cuentas por Centro de Costo',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'ubicacion' => array(
		'elevation' => true,
		'description' => 'Ubicación de Empleados',
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
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'settings' => array(
		'elevation' => true,
		'description' => 'Configuración de Nomina',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'save' => array(
				'description' => 'Actualizar la'
			),
		)
	),
	'cuentas' => array(
		'elevation' => false,
		'description' => 'Plan de Cuentas',
		'actions' => array(
			'queryCuenta' => array(
				'description' => 'Consultar en',
			),
			'queryByName' => array(
				'sameAs' => 'queryCuenta',
			)
		)
	),

);