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
 * ActivosController
 *
 * Controlador de activos fijos
 *
 */
class ConsecutivoController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Consecutivo',
		'plural' => 'Consecutivos',
		'single' => 'Consecutivo',
		'genre' => 'M',
		'icon' => 'document-library.png',
		'preferedOrder' => 'prefijo',
		'fields' => array(
			'prefijo' => array(
				'single' => 'Prefijo',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'primary' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'numero' => array(
				'single' => 'Número',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 5,
				'filters' => array('int')
			)
		)
	);
	
	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
