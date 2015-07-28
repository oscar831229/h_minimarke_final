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
 * Conceptos_LicenciasController
 *
 * Controlador de los conceptos de licencias
 */
class Conceptos_LicenciasController extends HyperFormController {

	static protected $_config = array(
		'model' => 'ConceptosLicencias',
		'plural' => 'conceptos de licencias o suspensiones',
		'single' => 'concepto de licencia o suspensión',
		'genre' => 'M',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'tipo' => array(
				'single' => 'Tipo',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'L' => 'LICENCIA',
					'S' => 'SUSPENSIÓN'
				),
				'filters' => array('onechar')
			),
			'remunerada' => array(
				'single' => 'Remunerada?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('onechar')
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
				'filters' => array('onechar')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
