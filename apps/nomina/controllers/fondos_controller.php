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
 * FondosController
 *
 * Controlador de los fondos
 *
 */
class FondosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Fondos',
		'plural' => 'fondos',
		'single' => 'fondo',
		'genre' => 'M',
		'preferedOrder' => 'nom_fondo',
		'fields' => array(
			'clase' => array(
				'single' => 'Clase',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'1' => 'FONDO CESANTIAS',
					'2' => 'E.P.S',
					'3' => 'FONDOS DE PENSION',
					'4' => 'PROVISIONES/OTROS'
				),
				'filters' => array('int')
			),
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'nom_fondo' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'nit' => array(
				'single' => 'Tercero',
				'type' => 'tercero',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('terceros')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
