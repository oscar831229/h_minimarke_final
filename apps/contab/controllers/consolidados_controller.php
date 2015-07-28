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
 * ConsolidadosController
 *
 * Controlador de los servidores de consolidado
 *
 */
class ConsolidadosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Consolidados',
		'plural' => 'servidores de consolidado',
		'single' => 'servidor de consolidado',
		'genre' => 'M',
		'icon' => 'centros.png',
		'preferedOrder' => 'server',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 6,
				'maxlength' => 6,
				'primary' => true,
				'filters' => array('int')
			),
			'server' => array(
				'single' => 'Host Servidor',
				'type' => 'text',
				'size' => 32,
				'maxlength' => 32,
				'filters' => array('striptags', 'extraspaces')
			),
			'uri' => array(
				'single' => 'Ruta',
				'type' => 'text',
				'size' => 32,
				'maxlength' => 64,
				'notSearch' => true,
				'filters' => array('striptags', 'extraspaces')
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
				'filters' => array('alpha')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
