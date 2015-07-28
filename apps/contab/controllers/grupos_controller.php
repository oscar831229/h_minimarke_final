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
 * GruposController
 *
 * Controlador de las ubicaciones de grupos
 *
 */
class GruposController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Grupos',
		'plural' => 'grupos de activos',
		'single' => 'grupo de activos',
		'genre' => 'M',
		'icon' => 'product.png',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'linea' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'auto' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'es_auxiliar' => array(
				'single' => 'Es Auxiliar?',
				'type' => 'closed-domain',
				'genre' => 'N',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('alpha')
			),
			'cta_compra' => array(
				'single' => 'Cuenta Compra Activos',
				'type' => 'cuenta',
				'genre' => 'F',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_inve' => array(
				'single' => 'Cuenta Ajustes por Inflación',
				'type' => 'cuenta',
				'genre' => 'F',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_ret_compra' => array(
				'single' => 'Cuenta Retención Compra',
				'type' => 'cuenta',
				'genre' => 'F',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'porc_compra' => array(
				'single' => 'Porcentaje Retención',
				'type' => 'decimal',
				'size' => 5,
				'maxlength' => 5,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('double')
			),
			'minimo_ret' => array(
				'single' => 'Valor Mínimo Retención',
				'type' => 'decimal',
				'size' => 12,
				'maxlength' => 15,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('double')
			),
			'cta_dev_ventas' => array(
				'single' => 'Cuenta Depreciación Débito',
				'type' => 'cuenta',
				'genre' => 'F',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_dev_compras' => array(
				'single' => 'Cuenta Depreciación Crédito',
				'type' => 'cuenta',
				'genre' => 'F',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
