<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class SucursalController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Sucursal',
		'plural' => 'sucursales',
		'single' => 'sucursal',
		'genre' => 'F',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 70,
				'filters' => array('striptags', 'extraspaces')
			),
			'predeterminado' => array(
				'single' => 'Predeterminado?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('alpha')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}