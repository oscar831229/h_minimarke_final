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
 * RetecomprasController
 *
 * Controlador de las retenciones de compras
 *
 */
class RetecomprasController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Retecompras',
		'plural' => 'Retenciones de compras',
		'single' => 'retencion de compra',
		'genre' => 'M',
		'preferedOrder' => 'codigo',
		'icon' => 'unidades.png',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 3,
				'maxlength' => 3,
				'primary' => true,
				'filters' => array('alpha')
			),
			'descripcion' => array(
				'single' => 'Descripción',
				'type' => 'text',
				'size' => 60,
				'maxlength' => 140,
				'filters' => array('striptags', 'extraspaces')
			),
			'cuenta' => array(
				'single' => 'Cuenta',
				'type' => 'cuenta',
				'filters' => array('cuentas')
			),
			'base_retencion' => array(
				'single' => 'Base de Retención',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 12,
				'filters' => array('int')
			),
			'porce_retencion' => array(
				'single' => '% de Retención',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 6,
				'filters' => array('double')
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
