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
		'title' => 'Carta',
		'description' => 'Mantenimiento de la carta en los diferentes ambientes',
		'options' => array(
			'menus',
			'menus_items',
			'modifiers',
			/*'perfiles_usuarios',
			'permisos_perfiles'*/
		)
	),
	/*array(
		'title' => 'Receta',
		'description' => 'Opciones de receta estándar',
		'options' => array(
			'permisos_comprob',
			'permisos_centros'
		)
	)*/
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

	//Usuarios
	'menus' => array(
		'elevation' => true,
		'description' => 'Mantenimiento de Menus',
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
	'menus_items' => array(
		'elevation' => true,
		'description' => 'Items de Menús',
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
	'modifiers' => array(
		'elevation' => true,
		'description' => 'Modificadores',
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
	)

);