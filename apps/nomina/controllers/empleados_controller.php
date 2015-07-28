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
 * EmpleadosController
 *
 * Controlador de maestro de empleados
 *
 */
class EmpleadosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Empleados',
		'plural' => 'empleados',
		'single' => 'empleado',
		'icon' => 'user-business.png',
		'genre' => 'M',
		'preferedOrder' => 'primer_apellido',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 13,
				'maxlength' => 20,
				'primary' => true,
				'filters' => array('alpha')
			),
			'cedula' => array(
				'single' => 'Documento',
				'type' => 'text',
				'size' => 15,
				'maxlength' => 20,
				'filters' => array('alpha')
			),
			'primer_apellido' => array(
				'single' => 'Primer Apellido',
				'type' => 'text',
				'size' => 20,
				'maxlength' => 20,
				'filters' => array('striptags', 'extraspaces')
			),
			'segundo_apellido' => array(
				'single' => 'Segundo Apellido',
				'type' => 'text',
				'size' => 20,
				'maxlength' => 20,
				'filters' => array('striptags', 'extraspaces')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 20,
				'maxlength' => 20,
				'filters' => array('striptags', 'extraspaces')
			),
			'direccion' => array(
				'single' => 'Dirección',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'telefono' => array(
				'single' => 'Teléfono',
				'type' => 'text',
				'size' => 20,
				'maxlength' => 20,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'fecha_nace' => array(
				'single' => 'Fecha Nacimiento',
				'type' => 'date',
				'default' => '',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('date')
			),
			'sexo' => array(
				'single' => 'Genero',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'M' => 'MASCULINO',
					'F' => 'FEMENINO'
				),
				'filters' => array('alpha')
			),
			'estado_civil' => array(
				'single' => 'Estado Civil',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notBrowse' => true,
				'notSearch' => true,
				'values' => array(
					'C' => 'CASADO',
					'S' => 'SOLTERO',
					'U' => 'UNION LIBRE',
					'V' => 'VIUDO',
					'M' => 'SEPARADO',
					'N' => 'SIN DEFINIR'
				),
				'filters' => array('alpha')
			),
			'libreta_militar' => array(
				'single' => 'Libreta Militar',
				'type' => 'text',
				'size' => 15,
				'maxlength' => 15,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('alpha')
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
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
