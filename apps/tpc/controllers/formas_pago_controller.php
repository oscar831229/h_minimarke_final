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
 * Controlador de Formas de pago
 *
 */
class Formas_PagoController extends HyperFormController {

	static protected $_config = array(
		'model' => 'FormasPago',
		'plural' => 'Formas de pago',
		'single' => 'Forma de pago',
		'genre' => 'M',
		'tabName' => 'Formas de Pago',
		'preferedOrder' => 'nombre ASC',
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
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 100,
				'notNull' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'tipo' => array(
				'single' => 'Tipo de Pago',
				'type' => 'closed-domain',
                'size' => 1,
                'maxlength' => 1,
                'values' => array(
                    'E'  => 'EFECTIVO',
                    'C'  => 'CHEQUE',
                    'TD' => 'TARJETA DÉBITO',
                    'TC' => 'TARJETA CRÉDITO'
                ),
                'notSearch' => true,
                'notBrowse' => true,
                'notNull' => true,
				'filters' => array('striptags')
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
                'notNull' => true,
				'filters' => array('onechar')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}