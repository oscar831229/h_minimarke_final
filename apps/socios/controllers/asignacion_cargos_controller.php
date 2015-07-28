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
 * Asignacion_CargosController
 *
 * Controlador de los cargos fijos
 *
 */
class Asignacion_CargosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'AsignacionCargos',
		'plural' => 'Asignación de Cargos',
		'single' => 'Asignación de Cargo',
		'genre' => 'M',
		'preferedOrder' => 'id DESC',
		'icon' => 'cartera.png',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 9,
				'maxlength' => 9,
				'primary' => true,
				'filters' => array('int')
			),
			'socios_id' => array(
				'single' => 'Socio',
				'type' => 'Socio',
				'filters' => array('alpha')
			),
			/*'tipos_pago_id' => array(
				'single' => 'Tipos de Pago',
				'type' => 'relation',
				'relation' => 'TiposPago',
				'fieldRelation' => 'id',
				'detail' => 'detalle',
				'notNull' => true,
				'filters' => array('int')
			),
			'tipo' => array(
				'single' => 'Tipo',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'S' => 'Socio',
					'T' => 'Tipo de Pago'
				),
				'notBrowse' => true,
				'notReport' => true,
				'filters' => array('onechar')
			),*/
			'cargos_fijos_id' => array(
				'single' => 'Cargo Fijo',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'notNull' => true,
				'filters' => array('int')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'A' => 'Activo',
					'I' => 'Inactivo',
					'P' => 'Procesado'
				),
				'notBrowse' => true,
				'notReport' => true,
				'filters' => array('onechar')
			)
		)
	);
	
	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
