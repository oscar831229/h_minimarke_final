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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

/**
 * Regimen_CuentasController
 *
 * Controlador las cuentas de contabilizacion de regimenes
 *
 */
class Regimen_CuentasController extends HyperFormController {

	static protected $_config = array(
		'model' => 'RegimenCuentas',
		'plural' => 'Contabilización por Regímen',
		'single' => 'contabilización por regimen',
		'genre' => 'M',
		'icon' => 'notebook-share.png',
		'preferedOrder' => 'regimen',
		'fields' => array(
			'regimen' => array(
				'single' => 'Tipo de Régimen',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'C' => 'COMUN',
					'G' => 'GRAN CONTRIBUYENTE',
					'S' => 'SIMPLIFICADO'
				),
				'primary' => true,
				'filters' => array('alpha')
			),
			'cta_iva10d' => array(
				'single' => 'Cuenta IVA 10% Descontable',
				'type' => 'cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_iva16d' => array(
				'single' => 'Cuenta IVA 19% Descontable',
				'type' => 'cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_iva10r' => array(
				'single' => 'Cuenta IVA 10% Retenido',
				'type' => 'cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_iva16r' => array(
				'single' => 'Cuenta IVA 19% Retenido',
				'type' => 'cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_iva10v' => array(
				'single' => 'Cuenta IVA 10% Ventas',
				'type' => 'cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_iva16v' => array(
				'single' => 'Cuenta IVA 19% Ventas',
				'type' => 'cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_iva5d' => array(
				'single' => 'Cuenta IVA 5% Descontable',
				'type' => 'cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_iva5r' => array(
				'single' => 'Cuenta IVA 5% Retenido',
				'type' => 'cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_iva5v' => array(
				'single' => 'Cuenta IVA Venta 5%',
				'type' => 'Cuenta',
				'filters' => array('alpha'),
				'notBrowse' => true,
			),
		)
	);

	public function initialize(){
		if(!Gardien::hasAppAccess('FC')){
			unset(self::$_config['fields']['cta_iva10v']);
			unset(self::$_config['fields']['cta_iva16v']);
		}
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
