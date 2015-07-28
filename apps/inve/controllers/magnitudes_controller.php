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
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * MagnitudesController
 *
 * Controlador de las magnitudes
 *
 */
class MagnitudesController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Magnitudes',
		'plural' => 'magnitudes',
		'single' => 'magnitud',
		'genre' => 'F',
		'preferedOrder' => 'nombre',
		'icon' => 'ruler-triangle.png',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 8,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 80,
				'filters' => array('alpha')
			),
			'unidad_base' => array(
				'single' => 'Unidad Base',
				'type' => 'relation',
				'relation' => 'Unidad',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_unidad',
				'filters' => array('alpha')
			),
			'divisor' => array(
				'single' => 'Divisor',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 8,
				'filters' => array('double')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
