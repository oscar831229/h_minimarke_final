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
 * Codigos_MediosController
 *
 * Controlador de los codigos de formatos de medios magneticos
 *
 */
class Codigos_MediosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Magcod',
		'plural' => 'códigos de conceptos',
		'single' => 'código de concepto',
		'genre' => 'M',
		'icon' => 'report-excel.png',
		'preferedOrder' => 'codigo',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'codfor' => array(
				'single' => 'Formato',
				'type' => 'relation',
				'relation' => 'Magfor',
				'fieldRelation' => 'codfor',
				'detail' => 'nombre',
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 1000,
				'filters' => array('striptags', 'extraspaces')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
