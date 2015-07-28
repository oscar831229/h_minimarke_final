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
 * Parametros_Basicos_CarteraController
 *
 * Controlador de los Parametros basicos de Cartera
 *
 */
class Parametros_Basicos_CarteraController extends HyperFormController {

	static protected $_config = array(
		'model' => 'ParametrosBasicosCartera',
		'plural' => 'Parametros Basicos de Cartera',
		'single' => 'Parametro Basico de Cartera',
		'genre' => 'M',
		'preferedOrder' => 'id ASC',
		'icon' => 'wrench-screwdriver.png.png',
		'ignoreButtons' => array(
			'import','new','delete'
		),
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'int',
				'size' => 3,
				'maxlength' => 3,
				'primary' => true,
				'filters' => array('int')
			),
			'comprob' => array(
				'single' => 'Comprobante',
				'type' => 'Comprob',
				'filters' => array('alpha')
			),
			'tip_doc' => array(
				'single' => 'Tipo de Documento',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'filters' => array('striptags', 'extraspaces')
			),
			'dia_venc' => array(
				'single' => 'Días de Vencimiento',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'filters' => array('int')
			),
			'base_grab' => array(
				'single' => 'Base Grabable',
				'type' => 'decimal',
				'size' => 14,
				'maxlength' => 14,
				'filters' => array('double')
			),			
			'conciliado' => array(
				'single' => 'Conciliado',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'S' => 'Si',
					'N' => 'No'
				),
				'notBrowse' => true,
				'notReport' => true,
				'filters' => array('onechar')
			),
			'comprob_ingreso' => array(
				'single' => 'Comprobante de Ingreso',
				'type' => 'Comprob',
				'filters' => array('alpha')
			),			
			'iva_mora' => array(
			     'single' => '% Iva de Mora',
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
