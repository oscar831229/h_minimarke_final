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
 * ComcierController
 *
 * Controlador de cuentas de cierre anual
 */
class ComcierController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Comcier',
		'plural' => 'cuentas de cierre',
		'single' => 'cuenta de cierre',
		'genre' => 'M',
		'icon' => 'old-versions.png',
		'preferedOrder' => 'cuentai',
		'fields' => array(
			'cuentai' => array(
				'single' => 'Cuenta Inicial',
				'type' => 'cuenta',
				'primary' => true,
				'filters' => array('cuentas')
			),
			'cuentaf' => array(
				'single' => 'Cuenta Final',
				'type' => 'cuenta',
				'filters' => array('cuentas')
			),
			'nit' => array(
				'single' => 'Tercero',
				'type' => 'tercero',
				'filters' => array('terceros')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}