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
 * TipoDocumentosController
 *
 * Controlador de Tipos de Docuementos
 *
 */
class Tipo_DocumentosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'tipoDocumentos',
		'plural' => 'Tipo de Documentos',
		'single' => 'Tipo de Documento',
		'genre' => 'M',
		'tabName' => 'tipo_Documento',
		'preferedOrder' => 'nombre ASC',
		'icon' => 'attibutes.png',
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
				'readOnly' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 45,				
				'filters' => array('striptags', 'extraspaces')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}