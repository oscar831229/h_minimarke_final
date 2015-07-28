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
 * Tipo_SociosController
 *
 * Controlador de Tipo de Socios
 *
 */
class Tipo_SociosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'TipoSocios',
		'plural' => 'Tipo de Socios',
		'single' => 'Tipo de Socios',
		'genre' => 'M',
		'tabName' => 'Tipo Socios',
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
				'readOnly' => true,
				'auto' => true,
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
			'tipo_contrato_id' => array(
				'single' => 'Tipo de Contrato',
				'type' => 'relation',
				'relation' => 'TipoContrato',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'notNull' => true,
				'notSearch' => true,				
				'filters' => array('int')
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