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
 * ConceptosController
 *
 * Controlador de los conceptos de pagos y descuentos a empleados
 *
 */
class ConceptosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Concepto',
		'plural' => 'conceptos de pagos ó descuentos',
		'single' => 'concepto de pago ó descuento',
		'genre' => 'M',
		'preferedOrder' => 'nom_concepto',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'nom_concepto' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'vacaciones' => array(
				'single' => 'Base para vacaciones',
				'type' => 'closed-domain',
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
			'aportes' => array(
				'single' => 'Genera Aportes Parafiscales?',
				'type' => 'closed-domain',
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
			'prestacion' => array(
				'single' => 'Base Prima Servicios?',
				'type' => 'closed-domain',
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
			'base_iss' => array(
				'single' => 'Base para I.S.S.?',
				'type' => 'closed-domain',
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
			'retencion' => array(
				'single' => 'Causa Rentención?',
				'type' => 'closed-domain',
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
			'porc_ret' => array(
				'single' => 'Porcentaje Retención',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),
			'salario' => array(
				'single' => 'Base para Cesantias?',
				'type' => 'closed-domain',
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
			'porc_salario' => array(
				'single' => 'Porcentaje Salario',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),
			'recargo' => array(
				'single' => 'Porcentaje Recargo',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),
			'cuenta' => array(
				'single' => 'Cuenta Contable',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuenta')
			),
			'contra' => array(
				'single' => 'Contrapartida',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuenta')
			),
			'netea' => array(
				'single' => 'Base para Provisiones?',
				'type' => 'closed-domain',
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
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
