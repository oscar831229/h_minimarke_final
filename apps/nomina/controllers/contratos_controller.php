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
 * Controlador de contratos de empleados
 *
 */
class ContratosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Contratos',
		'plural' => 'contratos de empleados',
		'single' => 'contrato de empleado',
		'icon' => 'user-business.png',
		'genre' => 'M',
		'preferedOrder' => 'id',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 7,
				'maxlength' => 11,
				'primary' => true,
				'filters' => array('int')
			),
			'empleados_id' => array(
				'single' => 'Empleado',
				'type' => 'relation',
				'relation' => 'Empleados',
				'fieldRelation' => 'id',
				'detail' => 'nombre_completo',
				'filters' => array('int')
			),
			'cargo' => array(
				'single' => 'Cargo',
				'type' => 'relation',
				'relation' => 'Cargos',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_cargo',
				'filters' => array('int')
			),
			'centro_costo' => array(
				'single' => 'Centro Costo',
				'type' => 'relation',
				'relation' => 'Centros',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_centro',
				'filters' => array('int')
			),
			'tipo_contrato' => array(
				'single' => 'Tipo de Contrato',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notBrowse' => true,
				'values' => array(
					'L' => 'LEY 50 MENOR 1 AÑO',
					'F' => 'FIJO MENOR 1 AÑO',
					'M' => 'FIJO MAYOR 1 AÑO',
					'I' => 'INTEGRAL',
				),
				'filters' => array('alpha')
			),
			'forma_pago' => array(
				'single' => 'Forma de Pago',
				'type' => 'relation',
				'notBrowse' => true,
				'relation' => 'FormaPago',
				'fieldRelation' => 'codigo',
				'detail' => 'descripcion',
				'filters' => array('alpha')
			),
			'fondo_ces' => array(
				'single' => 'Fondo Cesantías',
				'type' => 'relation',
				'relation' => 'Fondos',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_fondo',
				'notBrowse' => true,
				'filters' => array('int')
			),
			'eps' => array(
				'single' => 'E.P.S',
				'type' => 'relation',
				'relation' => 'Fondos',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_fondo',
				'notBrowse' => true,
				'filters' => array('int')
			),
			'fondo_pension' => array(
				'single' => 'Fondo Pensión',
				'type' => 'relation',
				'relation' => 'Fondos',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_fondo',
				'notBrowse' => true,
				'filters' => array('int')
			),
			'ubica' => array(
				'single' => 'Ubicación',
				'type' => 'relation',
				'relation' => 'Ubicacion',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_ubica',
				'notBrowse' => true,
				'filters' => array('int')
			),
			'sueldo' => array(
				'single' => 'Sueldo',
				'type' => 'decimal',
				'size' => 10,
				'maxlength' => 14,
				'decimals' => 2,
				'notSearch' => true,
				'notBrowse' => true,
				'notNull' => true,
				'filters' => array('float')
			),
			'transporte' => array(
				'single' => 'Tiene auxilio transporte?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notBrowse' => true,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			),
			'fecha_ingreso' => array(
				'single' => 'Fecha Ingreso',
				'type' => 'date',
				'default' => '',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('date')
			),
			'fecha_retiro' => array(
				'single' => 'Fecha Retiro',
				'type' => 'date',
				'default' => '',
				'notBrowse' => true,
				'notSearch' => true,
				'readOnly' => true,
				'filters' => array('date')
			),
			'ultimo_pago' => array(
				'single' => 'Último Pago',
				'type' => 'date',
				'default' => '',
				'notBrowse' => true,
				'notSearch' => true,
				'readOnly' => true,
				'filters' => array('date')
			),
			'ultimo_aumento' => array(
				'single' => 'Último Aumento',
				'type' => 'date',
				'default' => '',
				'notBrowse' => true,
				'notSearch' => true,
				'readOnly' => true,
				'filters' => array('date')
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