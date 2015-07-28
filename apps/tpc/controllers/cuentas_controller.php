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
 * CuentasController
 *
 * Controlador de Cuentas Bancarias
 *
 */
class CuentasController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Cuentas',
		'plural' => 'Cuentas bancarias',
		'single' => 'Cuenta bancaria',
		'genre' => 'M',
		'tabName' => 'Cuentas',
		'preferedOrder' => 'banco ASC',
		'icon' => 'formatos.png',
		'ignoreButtons' => array(
			'import'
		),
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'readOnly' => true,
				'auto' => true,
				'filters' => array('int')
			),
			'banco' => array(
				'single' => 'Nombre banco',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'notNull' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'cuenta' => array(
				'single' => 'Cuenta',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 20,
				'notSearch' => true,
				'notNull' => true,
				'filters' => array('striptags')
			),
			'cuenta_contable' => array(
				'single' => 'Cuenta Contable',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notNull' => true,
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