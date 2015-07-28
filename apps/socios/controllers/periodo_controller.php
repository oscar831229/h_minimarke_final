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
 * Formas_PagoController
 *
 * Controlador de los centros de costo
 *
 */
class PeriodoController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Periodo',
		'plural' => 'Periodos',
		'single' => 'Periodo',
		'genre' => 'M',
		'preferedOrder' => 'periodo DESC',
		'icon' => 'credit-card.png',
		'fields' => array(
			'periodo' => array(
				'single' => 'Periodo',
				'type' => 'int',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'filters' => array('int')
			),
			'consecutivos_id' => array(
				'single' => 'Consecutivos',
				'type' => 'relation',
				'relation' => 'Consecutivos',
				'fieldRelation' => 'id',
				'detail' => 'detalle',
				'maxlength' => 20,
				'notNull' => true,
				//'notSearch' => true,
				'filters' => array('int')
			),
			'cierre' => array(
				'single' => 'Cierre',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'S' => 'Si',
					'N' => 'No'
				),
				'filters' => array('onechar')
			),
			'facturado' => array(
				'single' => 'Facturado',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'S' => 'Si',
					'N' => 'No'
				),
				'filters' => array('onechar')
			),
			'intereses_mora' => array(
				'single' => 'Intereses de Mora',
				'type' => 'int',
				'size' => 11,
				'maxlength' => 11,
				'filters' => array('double')
			),
			'dia_factura' => array(
				'single' => 'Dia del periodo para Facturar',
				'type' => 'int',
				'size' => 2,
				'maxlength' => 2,
				'filters' => array('int')
			),
			'dias_plazo' => array(
				'single' => 'Dias para pago',
				'type' => 'int',
				'size' => 2,
				'maxlength' => 2,
				'filters' => array('int')
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
