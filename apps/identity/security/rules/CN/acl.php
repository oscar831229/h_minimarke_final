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
		'title' => 'Consecutivos',
		'description' => 'Opciones para mantener consecutivos',
		'options' => array(
			'consecutivo'
		)
	)
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

	//Consecutivos
	'consecutivo' => array(
		'elevation' => true,
		'description' => 'Consecutivo',
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
		'description' => 'Configuración de Consecutivos',
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