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
 * ConcentroController
 *
 * Controlador de cuentas por centro de costo
 *
 */
class ConcentroController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Concentro',
		'plural' => 'cuentas por centro de costo',
		'single' => 'cuenta por centro de costo',
		'genre' => 'M',
		'preferedOrder' => 'codigo',
		'fields' => array(
			'codigo' => array(
				'single' => 'Concepto',
				'type' => 'relation',
				'relation' => 'Concepto',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_concepto',
				'primary' => true,
				'filters' => array('int')
			),
			'centro_costo' => array(
				'single' => 'Centro Costo',
				'type' => 'relation',
				'relation' => 'Centros',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_centro',
				'primary' => true,
				'filters' => array('int')
			),
			'cuenta' => array(
				'single' => 'Cuenta',
				'type' => 'cuenta',
				'filters' => array('cuentas')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
