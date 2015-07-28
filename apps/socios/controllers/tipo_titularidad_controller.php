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
 * Tipo_TitularidadController
 *
 * Controlador de Tipos de Titularidad
 *
 */
class Tipo_TitularidadController extends HyperFormController {

	static protected $_config = array(
		'model' => 'TipoTitularidad',
		'plural' => 'Tipo de Titularidades',
		'single' => 'Tipo de Titularidad',
		'genre' => 'M',
		'tabName' => 'tipo_titularidad',
		'preferedOrder' => 'nombre ASC',
		'icon' => 'type-user.png',
		/*'ignoreButtons' => array(
			'import'
		),*/
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'auto' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 45,				
				'filters' => array('striptags', 'extraspaces')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}