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
 * Formas_PagoController
 *
 * Controlador de los centros de costo
 *
 */
class Formas_PagoController extends HyperFormController {

	static protected $_config = array(
		'model' => 'FormasPago',
		'plural' => 'Formas de Pago',
		'single' => 'Forma de Pago',
		'genre' => 'F',
		'preferedOrder' => 'nombre ASC',
		'icon' => 'credit-card.png',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 3,
				'maxlength' => 3,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'cuenta_contable' => array(
				'single' => 'Cuenta Asociada',
				'type' => 'Cuenta',
				'filters' => array('cuentas')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'A' => 'Activo',
					'I' => 'Inactivo'
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
