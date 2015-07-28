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
 * UbicacionController
 *
 * Controlador de las ubicaciones de activos fijos
 *
 */
class UbicacionController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Ubicacion',
		'plural' => 'ubicaciones',
		'single' => 'ubicacion',
		'genre' => 'F',
		'icon' => 'building.png',
		'preferedOrder' => 'nom_ubica',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'nom_ubica' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
