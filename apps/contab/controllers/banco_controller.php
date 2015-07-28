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
 * BancoController
 *
 * Controlador de los bancos
 *
 */
class BancoController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Banco',
		'plural' => 'bancos',
		'single' => 'banco',
		'genre' => 'M',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 100,
				'filters' => array('striptags', 'extraspaces')
			),
			'oficina' => array(
				'single' => 'Oficina',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'ciudad' => array(
				'single' => 'Ciudad',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 30,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}