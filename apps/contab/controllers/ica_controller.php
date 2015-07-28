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
 * IcaController
 *
 * Controlador de las cuentas de ica
 *
 */
class IcaController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Ica',
		'plural' => 'cuentas de ica',
		'single' => 'cuenta de ica',
		'icon' => 'old-versions.png',
		'genre' => 'F',
		'preferedOrder' => 'codigo',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'decimal',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('float')
			),
			'cuenta' => array(
				'single' => 'Cuenta Asociada',
				'type' => 'Cuenta',
				'filters' => array('cuentas')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
