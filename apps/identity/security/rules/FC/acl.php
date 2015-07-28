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

$menuDisposition = array(
	array(
		'title' => 'Facturas',
		'description' => 'Opciones para mantener movimiento contable',
		'options' => array(
			'facturas',
			'reimprimir',
			'terceros',
			'referencias',
			'invoicing'
		)
	),
	array(
		'title' => 'Básicas',
		'description' => 'Administrar parametrización de la aplicación',
		'options' => array(
			'consecutivos',
			'lista_precios',
			'settings'
		)
	),
);

$accessList = array(

	//base
	'workspace' => array(
		'elevation' => true,
		'base' => true,
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

	'facturas' => array(
		'elevation' => true,
		'description' => 'Facturas',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),

	'terceros' => array(
		'elevation' => true,
		'description' => 'Terceros',
		'actions' => array(
			'crear' => array(
				'description' => 'Adicionar ó Modificar en',
			)
		)
	),

	'referencias' => array(
		'elevation' => true,
		'description' => 'Referencias',
		'actions' => array(
			'queryByName' => array(
				'description' => 'consultar las',
			)
		)
	),

	'reimprimir' => array(
		'elevation' => true,
		'description' => 'Reimprimir Facturas',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),

	'invoicing' => array(
		'elevation' => false,
		'description' => 'Invoicing',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'save' => array(
				'description' => 'Adicionar2 en',
			),
		)
	),

	'consecutivos' => array(
		'elevation' => true,
		'description' => 'Consecutivos de Facturación',
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

	'lista_precios' => array(
		'elevation' => true,
		'description' => 'Lista de Precios',
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
		'description' => 'Configuración de Facturas',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'save' => array(
				'description' => 'Actualizar la'
			),
		)
	)

);
