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
 * Regimen_CuentasController
 *
 * Controlador de regimen cuentas
 *
 */
class Regimen_CuentasController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'RegimenCuentas',
		'plural' => 'cuentas por regimen',
		'single' => 'cuenta por regimen',
		'genre' => 'F',
		'preferedOrder' => 'regimen',
		'icon' => 'featured.png',
		'fields' => array(
			'regimen' => array(
				'single' => 'Regimen',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'C' => 'COMUN',
					'G' => 'GRAN CONTRIBUYENTE',
					'S' => 'SIMPLIFICADO'
				),
				'primary' => true,
				'filters' => array('onechar')
			),
			'cta_iva10d' => array(
				'single' => 'Cuenta IVA Descontable 10%',
				'type' => 'Cuenta',
				'filters' => array('alpha')
			),
			'cta_iva16d' => array(
				'single' => 'Cuenta IVA Descontable 16%',
				'type' => 'Cuenta',
				'filters' => array('alpha')
			),
			'cta_iva5d' => array(
				'single' => 'Cuenta IVA Descontable 5%',
				'type' => 'Cuenta',
				'filters' => array('alpha')
			),
			'cta_iva10r' => array(
				'single' => 'Cuenta IVA Retenido 10%',
				'type' => 'Cuenta',
				'filters' => array('alpha'),
				'notBrowse' => true,
			),
			'cta_iva16r' => array(
				'single' => 'Cuenta IVA Retenido 16%',
				'type' => 'Cuenta',
				'filters' => array('alpha'),
				'notBrowse' => true,
			),
			'cta_iva5r' => array(
				'single' => 'Cuenta IVA Retenido 5%',
				'type' => 'Cuenta',
				'filters' => array('alpha'),
				'notBrowse' => true,
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
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
