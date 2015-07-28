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
 * Interes_UsuraController
 *
 * Controlador de Interes de usura/Mora
 *
 */
class Interes_UsuraController extends HyperFormController {

	static protected $_config = array(
		'model' => 'InteresUsura',
		'plural' => 'Intereses de Mora',
		'single' => 'Interés de Mora',
		'genre' => 'M',
		'tabName' => 'Interés Mora',
		'preferedOrder' => 'fecha_final DESC',
		'icon' => 'table_money.png',
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
			'fecha_inicial' => array(
				'single' => 'Fecha de Inicio',
				'type' => 'date',
				'default' => '',
				'notNull' => true,
				'filters' => array('date')
			),
			'fecha_final' => array(
				'single' => 'Fecha de Final',
				'type' => 'date',
				'default' => '',
				'notNull' => true,
				'filters' => array('date')
			),
			'interes_trimestral' => array(
				'single' => 'Interes Mora',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notNull' => true,
				'values' => array(
					'1.0' => '1%',
					'1.5' => '1,5%',
					'2.0' => '2%',
				),
				'filters' => array('int')
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
