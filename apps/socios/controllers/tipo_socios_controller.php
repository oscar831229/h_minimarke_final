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
 * Controlador de Tipos de Socios
 *
 */
class Tipo_SociosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'TipoSocios',
		'plural' => 'Tipo de Socios',
		'single' => 'Tipo de Socio',
		'genre' => 'M',
		'tabName' => 'tipo_socios',
		'preferedOrder' => 'nombre ASC',
		'icon' => 'type-user.png',
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
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 45,				
				'filters' => array('striptags', 'extraspaces')
			),
			'cuota_minima' => array(
				'single' => 'Cuota Mínima POS',
				'type' => 'int',
				'size' => 11,
				'maxlength' => 11,
				'filters' => array('double')
			),
			'mora_cuota' => array(
				'single' => '% Mora de Cuota Mínima POS',
				'type' => 'int',
				'size' => 11,
				'maxlength' => 11,
				'filters' => array('double')
			),
			'edad_ini' => array(
				'single' => 'Edad en que inicia',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 2,
				'filters' => array('int')
			),
			'edad_fin' => array(
				'single' => 'Edad en que termina',
				'type' => 'int',
				'size' => 5,
				'maxlength' => 2,
				'filters' => array('int')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'notNull' => true,
				'maxlength' => 1,
				'values' => array(
					'A' => 'Activo',
					'I' => 'Inactivo'
				),
				'filters' => array('onechar')
			),
			
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}
