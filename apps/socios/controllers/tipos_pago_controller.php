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
 * Tipos_PagoController
 *
 * Controlador de Tipos de Pago de Socio
 *
 */
class Tipos_PagoController extends HyperFormController {

	static protected $_config = array(
		'model' => 'TiposPago',
		'plural' => 'Tipos de Pago',
		'single' => 'Tipo de Pago',
		'genre' => 'M',
		'tabName' => 'tipos_pago',
		'preferedOrder' => 'detalle ASC',
		'icon' => 'type-user.png',
		/*'ignoreButtons' => array(
			'import'
		),*/
		'fields' => array(
			'id' => array(
				'single' => 'Id',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'auto' => true,
				'filters' => array('int')
			),
			'codigo' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 2,
				'maxlength' => 2,
				'filters' => array('int')
			),
			'detalle' => array(
				'single' => 'Detalle',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}