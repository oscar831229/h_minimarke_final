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
 * Tipos_CorrespondenciaController
 *
 * Controlador de Tipos de Correspondencia de socios
 *
 */
class Tipo_CorrespondenciaController extends HyperFormController {

	static protected $_config = array(
		'model' => 'TipoCorrespondencia',
		'plural' => 'Tipos de Correspondencia',
		'single' => 'Tipo de Correspondencia',
		'genre' => 'M',
		'tabName' => 'General',
		'preferedOrder' => 'nombre ASC',
		'icon' => 'switch.png',
		/*'ignoreButtons' => array(
			'import'
		),*/
		'fields' => array(
			'id' => array(
				'single'	=> 'Código',
				'type'		=> 'text',
				'size'		=> 6,
				'maxlength'	=> 6,
				'primary'	=> true,
				'auto'		=> true,
				'filters'	=> array('int')
			),
			'nombre' => array(
				'single'	=> 'Nombre',
				'type'		=> 'text',
				'size'		=> 30,
				'maxlength'	=> 45,
				'filters'	=> array('striptags', 'extraspaces')
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
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}
