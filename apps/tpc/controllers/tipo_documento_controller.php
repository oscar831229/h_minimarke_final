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
 * Tipo_DocumentoController
 *
 * Controlador de Tipo de Documento
 *
 */
class Tipo_DocumentoController extends HyperFormController {

	static protected $_config = array(
		'model' => 'TipoDocumentos',
		'plural' => 'Tipo de Documentos',
		'single' => 'Tipo de Documento',
		'genre' => 'M',
		'tabName' => 'Tipo Documento',
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