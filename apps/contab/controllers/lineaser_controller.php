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
 * LineaSerController
 *
 * Controlador de las lineas de servicio
 *
 */
class LineaserController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Lineaser',
		'plural' => 'líneas de servicio',
		'single' => 'línea de servicio',
		'genre' => 'F',
		'preferedOrder' => 'descripcion',
		'fields' => array(
			'linea' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12,
				'primary' => true,
				'filters' => array('double')
			),
			'descripcion' => array(
				'single' => 'Descripción',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'cta_gasto' => array(
				'single' => 'Cuenta Gasto',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_retiva' => array(
				'single' => 'Cuenta Retención IVA',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_retencion' => array(
				'single' => 'Cuenta Retención',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_cartera' => array(
				'single' => 'Cuenta de Cuentas x Pagar',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_ex1' => array(
				'single' => 'Cuenta IVA Reg. Simplificado',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_iva' => array(
				'single' => 'Cuenta IVA Otros Regímenes',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'porc_iva' => array(
				'single' => 'Porcentaje IVA',
				'type' => 'text',
				'size' => 7,
				'maxlength' => 7,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),
			/*'cta_ex2' => array(
				'single' => '',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			)*/
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
