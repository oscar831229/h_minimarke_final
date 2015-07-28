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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

/**
 * Cuentas_CreeController
 *
 * Controlador de las cuentas CREE a usar
 *
 */
class Cuentas_CreeController extends HyperFormController {

	static protected $_config = array(
		'model' => 'CuentasCree',
		'plural' => 'Cuentas CREE',
		'single' => 'Cuenta CREE',
		'genre' => 'M',
		'icon' => 'property.png',
		'preferedOrder' => 'porce',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'porce' => array(
				'single' => 'Porcentaje',
				'type' => 'decimal',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('float')
			),
			'cuenta' => array(
				'single' => 'Cuenta',
				'type' => 'cuenta',
				'primary' => true,
				'filters' => array('cuentas')
			),
			'base' => array(
				'single' => 'Base',
				'type' => 'decimal',
				'size' => 12,
				'maxlength' => 12,
				'primary' => true,
				'filters' => array('float')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'A' => 'ACTIVO',
					'I' => 'INACTIVO'
				),
				'filters' => array('onechar')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
