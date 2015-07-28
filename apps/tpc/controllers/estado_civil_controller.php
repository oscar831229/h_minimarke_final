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
 * Estado_CivilController
 *
 * Controlador de Estados Civiles
 *
 */
class Estado_CivilController extends HyperFormController {

	static protected $_config = array(
		'model' => 'EstadosCiviles',
		'plural' => 'Estados Civiles',
		'single' => 'Estado Civil',
		'genre' => 'M',
		'tabName' => 'Estados Civiles',
		'preferedOrder' => 'nombre ASC',
		'icon' => 'attibutes.png',
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
				'auto' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 20,
				'maxlength' => 30,
				'notNull' => true,
				'filters' => array('striptags', 'extraspaces')
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