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
 * Tipo_ContratoController
 *
 * Controlador de Tipo de Contrato
 *
 */
class Tipo_ContratoController extends HyperFormController {

	static protected $_config = array(
		'model' => 'TipoContrato',
		'plural' => 'Tipo de Contratos',
		'single' => 'Tipo de Contrato',
		'genre' => 'M',
		'tabName' => 'Tipo Contrato',
		'preferedOrder' => 'nombre ASC',
		'icon' => 'formatos.png',
		'ignoreButtons' => array(
			'import'
		),
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'auto' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'notNull' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'sigla' => array(
				'single' => 'Sigla',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 10,
				'notSearch' => true,
				'notNull' => true,
				'filters' => array('alpha')
			),
			'numero' => array(
				'single' => 'Consecutivo',
				'type' => 'int',
				'size' => 6,
				'maxlength' => 10,
				'notSearch' => true,
				'notNull' => true,
				'filters' => array('int')
			),
			'usa_formato' => array(
				'single' => 'Usa Formato?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notNull' => true,
				'notSearch' => false,
				'notBrowse' => true,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('onechar')
			),
			'formato' => array(
				'single' => 'Formato Consecutivo',
				'type' => 'text',
				'size' => 24,
				'maxlength' => 64,
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('contrato')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notNull' => true,
				'values' => array(
					'A' => 'Activo',
					'I' => 'Inactivo'
				),
				'filters' => array('onechar')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}
