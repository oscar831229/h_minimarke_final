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
 * NicController
 *
 * Controlador de los NICs
 *
 */
class NicController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Nic',
		'plural' => 'NICs',
		'single' => 'NIC',
		'genre' => 'M',
		'icon' => 'box-label.png',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 2,
				'maxlength' => 2,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 100,
				'notNull'	=> true,
				'filters' => array('striptags', 'extraspaces')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'A' => 'Activo',
					'I' => 'Inactivo'
				),
                'notNull'	=> true,
				'filters' => array('alpha')
			)
		)
	);

	public function initialize()
	{
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}
