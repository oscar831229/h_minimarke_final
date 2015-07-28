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
 * Lista_PreciosController
 *
 * Controlador de la lista de precios de los contratos en facturacion
 *
 */
class Lista_PreciosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'ListaPrecios',
		'plural' => 'Lista de Precios',
		'single' => 'Precio',
		'genre' => 'M',
		'preferedOrder' => 'contrato',
		'tabName' => 'General',
		'icon' => 'beans.png',
		'fields' => array(
			'contrato' => array(
				'single' 	=> 'Contrato',
				'type' 		=> 'int',
				'size'		=> 14,
				'maxlength' => 70,
				'filters' 	=> array('int'),
				'primary'	=> true
			),
			'nit' => array(
				'single' 	=> 'Tercero',
				'type' 		=> 'tercero',
				'filters' 	=> array('alpha'),
				'primary'	=> true
			),
			'referencia' => array(
				'single' 	=> 'Referencia',
				'type' 		=> 'item',
				'filters' 	=> array('alpha'),
				'primary'	=> true
			),
			'precio_venta' => array(
				'single' => 'Precio de Venta',
				'type' => 'decimal',
				'size' => 14,
				'maxlength' => 14,
				'decimals' => 2,
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('float')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'values' => array(
					'A' => 'ACTIVO',
					'I' => 'INACTIVO'
				),
				'filters' => array('alpha')
			)
		)
	);

	public function beforeNew()
	{
		Tag::displayTo('estado', 'A');
	}

	public function initialize()
	{
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
