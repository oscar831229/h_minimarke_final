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
 * DocumentosController
 *
 * Controlador de los documentos
 *
 */
class DocumentosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Documentos',
		'plural' => 'documentos contables',
		'single' => 'documento contable',
		'genre' => 'M',
		'icon' => 'archives.png',
		'preferedOrder' => 'nom_documen',
		'fields' => array(
			'codigo' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'primary' => true,
				'filters' => array('documento')
			),
			'nom_documen' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 50,
				'filters' => array('striptags', 'extraspaces')
			),
			'cartera' => array(
				'single' => 'Para Cartera?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
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
