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
 * Interes_MoraController
 *
 * Controlador de Interese de Mora de club
 *
 */
class Interes_MoraController extends HyperFormController {

	static protected $_config = array(
		'model' => 'InteresMora',
		'plural' => 'Intereses de Mora',
		'single' => 'Interes de Mora',
		'genre' => 'M',
		'tabName' => 'interes_mora',
		'preferedOrder' => 'fecha_inicial DESC',
		'icon' => 'conversion.png',
		/*'ignoreButtons' => array(
			'import'
		),*/
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'auto' => true,
				'filters' => array('int')
			),
			'fecha_inicial' => array(
				'single' => 'Fecha Inicial',
				'type' => 'date',
				'size' => 30,
				'maxlength' => 45,				
				'default' => '',
				'filters' => array('date')
			),
			'fecha_final' => array(
				'single' => 'Fecha Final',
				'type' => 'date',
				'size' => 30,
				'maxlength' => 45,				
				'default' => '',
				'filters' => array('date')
			),
			'interes_mensual' => array(
				'single' => 'Interes Mensual',
				'type' => 'decimal',
				'size' => 10,
				'maxlength' => 10,
				'filters' => array('double')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}