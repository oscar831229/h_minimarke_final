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
 * Cargos_FijosController
 *
 * Controlador de los cargos fijos
 *
 */
class Consumo_MinimoController extends HyperFormController {

	static protected $_config = array(
		'model' => 'ConsumoMinimo',
		'plural' => 'Cargos Fijos',
		'single' => 'Cargo Fijo',
		'genre' => 'M',
		'preferedOrder' => 'nombre ASC',
		'icon' => 'cartera-2.png',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 11,
				'maxlength' => 11,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'valor' => array(
				'single' => 'Valor',
				'type' => 'decimal',
				'size' => 12,
				'maxlength' => 12,
				'notSearch' => true,
				//'notBrowse' => true,
				'filters' => array('double')
			),
			'naturaleza' => array(
				'single' => 'Naturaleza',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'C' => 'Crédito',
					'D' => 'Débito'
				),
				'filters' => array('onechar')
			),
			'cuenta_contable' => array(
				'single' => 'Cuenta Contable',
				'type' => 'Cuenta',
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cuenta_iva' => array(
				'single' => 'Cuenta Iva',
				'type' => 'Cuenta',
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cuenta_consolidar' => array(
				'single' => 'Cuenta Consolidar',
				'type' => 'Cuenta',
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'tercero_fijo' => array(
				'single' => 'Tercero Fijo',
				'type' => 'Tercero',
				'notBrowse' => true,
				'filters' => array('alpha')
			),
			'centro_costos' => array(
				'single' => 'Centro de Costos',
				'type' => 'Centro',
				'filters' => array('alpha')
			),
			'centro_costos_iva' => array(
				'single' => 'Centro de Costo del Iva',
				'type' => 'Centro',
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('alpha')
			),
			'iva' => array(
				'single' => 'Aplica Iva',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'S' => 'Si',
					'N' => 'No'
				),
				'notBrowse' => true,
				'notReport' => true,
				'filters' => array('onechar')
			),
			'porcentaje_iva' => array(
				 'single' => '% Iva',
				 'type' => 'decimal',
				 'size' => 10,
				 'maxlength' => 10,
				'notSearch' => true,
				'notBrowse' => true,
				 'filters' => array('double')
			),
			'mora' => array(
				'single' => 'Aplica Mora',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'S' => 'Si',
					'N' => 'No'
				),
				'notSearch' => true,
				'notBrowse' => true,
				'notReport' => true,
				'filters' => array('onechar')
			),
			'tipo_cargo' => array(
				'single' => 'Tipo de Cargo Fijo',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'P' => 'Periodico',
					'T' => 'Temporal'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('onechar')
			),
			'ingreso_tercero' => array(
				'single' => 'Es ingreso a tercero?',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'S' => 'Si',
					'N' => 'No'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('onechar')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'A' => 'Activo',
					'I' => 'Inactivo'
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
