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
 * Cod_PrestController
 *
 * Controlador de los conceptos de prestamos
 *
 */
class Cod_PrestController extends HyperFormController {

	static protected $_config = array(
		'model' => 'CodPrest',
		'plural' => 'conceptos de prestamo',
		'single' => 'concepto de prestamo',
		'genre' => 'M',
		'preferedOrder' => 'nom_prestamo',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'nom_prestamo' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'cuenta' => array(
				'single' => 'Cuenta',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
