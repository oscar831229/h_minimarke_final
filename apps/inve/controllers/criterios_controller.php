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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

/**
 * CriteriosController
 *
 * Controlador de los criterios de evaluación
 *
 */
class CriteriosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Criterios',
		'plural' => 'criterios de calificación',
		'single' => 'criterio de calificación',
		'genre' => 'M',
		'icon' => 'property.png',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'prefijo' => array(
				'single' => 'Prefijo',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'filters' => array('alpha')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 70,
				'filters' => array('striptags', 'extraspaces')
			),
			'puntaje' => array(
				'single' => 'Puntaje Máximo',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 5,
				'notSearch' => true,
				'filters' => array('int')
			),
			'descripcion' => array(
				'single' => 'Descripción',
				'type' => 'textarea',
				'rows' => 2,
				'cols' => 40,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags')
			),
			'tipo' => array(
				'single' => 'Tipo',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'P' => 'PRECIO',
					'O' => 'OTRO'
				),
				'filters' => array('onechar')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'A' => 'ACTIVO',
					'I' => 'INACTIVO'
				),
				'filters' => array('onechar')
			)
		)
	);

	public function initialize(){
		$criterioPuntos = Settings::get('criterio_puntos');
		if($criterioPuntos=='N'){
			unset(self::$_config['fields']['puntaje']);
		}
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
