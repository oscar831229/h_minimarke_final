<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class PerfilesController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Perfiles',
		'plural' => 'perfiles',
		'single' => 'perfil',
		'genre' => 'M',
		'icon' => 'users.png',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 70,
				'filters' => array('striptags', 'extraspaces')
			),
			'aplicaciones_id' => array(
				'single' => 'Aplicación',
				'type' => 'relation',
				'relation' => 'Aplicaciones',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'filters' => array('int')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}