<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Tipos_ActivosController
 *
 * Controlador de las ubicaciones de activos fijos
 *
 */
class Tipos_ActivosController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'TiposActivos',
		'plural' => 'tipos de activos',
		'single' => 'tipo de activos',
		'genre' => 'M',
		'icon' => 'chair--arrow.png',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 11,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 70,
				'filters' => array('striptags', 'extraspaces')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
