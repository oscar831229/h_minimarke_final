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
 * CentrosController
 *
 * Controlador de los centros de costo
 *
 */
class CentrosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Centros',
		'plural' => 'centros de costo',
		'single' => 'centro de costo',
		'genre' => 'M',
		'icon' => 'centros.png',
		'preferedOrder' => 'nom_centro',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 7,
				'primary' => true,
				'filters' => array('int')
			),
			'nom_centro' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'responsable' => array(
				'single' => 'Responsable',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
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
				'filters' => array('alpha')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
