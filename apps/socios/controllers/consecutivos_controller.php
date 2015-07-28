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
 * ConsecutivosController
 *
 * Controlador de los consecutivos de facturación
 *
 */
class ConsecutivosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Consecutivos',
		'plural' => 'consecutivos de facturación',
		'single' => 'consecutivo de facturación',
		'genre' => 'M',
		'preferedOrder' => 'prefijo',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'detalle' => array(
				'single' => 'Resúmen',
				'type' => 'text',
				'size' => 7,
				'maxlength' => 7,
				'readOnly' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'prefijo' => array(
				'single' => 'Prefijo',
				'type' => 'text',
				'size' => 7,
				'maxlength' => 7,
				'filters' => array('striptags', 'extraspaces')
			),
			'resolucion' => array(
				'single' => 'Resolución',
				'type' => 'text',
				'size' => 20,
				'maxlength' => 20,
				'filters' => array('alpha')
			),
			'fecha_resolucion' => array(
				'single' => 'Fecha Resolución',
				'type' => 'date',
				'default' => null,
				'filters' => array('date')
			),
			'numero_inicial' => array(
				'single' => 'Número Inicial',
				'type' => 'int',
				'size' => 7,
				'maxlength' => 7,
				'filters' => array('int')
			),
			'numero_final' => array(
				'single' => 'Número Final',
				'type' => 'int',
				'size' => 7,
				'maxlength' => 7,
				'filters' => array('int')
			),
			'numero_actual' => array(
				'single' => 'Número Actual',
				'type' => 'int',
				'size' => 7,
				'maxlength' => 7,
				'filters' => array('int')
			),
			'nota_factura' => array(
				'single' => 'Nota Factura',
				'type' => 'textarea',
				'rows' => 2,
				'cols' => 40,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags')
			),
			'nota_ica' => array(
				'single' => 'Nota Tarifa ICA',
				'type' => 'textarea',
				'rows' => 2,
				'cols' => 40,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
