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
 * CentrosController
 *
 * Controlador de los centros de costo
 *
 */
class FormapagoController extends HyperFormController {

	static protected $_config = array(
		'model' => 'FormaPago',
		'plural' => 'formas de pago',
		'single' => 'forma de pago',
		'icon' => 'credit-card.png',
		'genre' => 'F',
		'preferedOrder' => 'descripcion',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 3,
				'maxlength' => 3,
				'primary' => true,
				'filters' => array('int')
			),
			'descripcion' => array(
				'single' => 'Descripción',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'cta_contable' => array(
				'single' => 'Cuenta Asociada',
				'type' => 'Cuenta',
				'filters' => array('cuentas')
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
