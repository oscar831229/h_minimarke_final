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
 * PresupuestoController
 *
 * Controlador de presupuestos
 *
 */
class PresupuestoController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Pres',
		'plural' => 'valores de presupuesto',
		'single' => 'valor de presupuesto',
		'genre' => 'M',
		'icon' => 'statistics.png',
		'preferedOrder' => 'centro_costo',
		'fields' => array(
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
				'primary' => true,
				'filters' => array('cuentas')
			),
			'ano' => array(
				'single' => 'Año',
				'type' => 'int',
				'size' => 4,
				'maxlength' => 4,
				'primary' => true,
				'filters' => array('int')
			),
			'mes' => array(
				'single' => 'Mes',
				'type' => 'closed-domain',
				'size' => 2,
				'maxlength' => 2,
				'values' => array(
					'01' => 'ENERO',
					'02' => 'FEBRERO',
					'03' => 'MARZO',
					'04' => 'ABRIL',
					'05' => 'MAYO',
					'06' => 'JUNIO',
					'07' => 'JULIO',
					'08' => 'AGOSTO',
					'09' => 'SEPTIEMBRE',
					'10' => 'OCTUBRE',
					'11' => 'NOVIEMBRE',
					'12' => 'DICIEMBRE'
				),
				'primary' => true,
				'filters' => array('alpha')
			),
			'pres' => array(
				'single' => 'Valor',
				'type' => 'decimal',
				'size' => 12,
				'maxlength' => 15,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('double')
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}