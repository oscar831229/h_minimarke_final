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
 * EmpresaController
 *
 * Controlador de Empresa
 *
 */
class EmpresaController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Empresa',
		'plural' => 'Empresas',
		'single' => 'Empresa',
		'genre' => 'M',
		'tabName' => 'Empresa',
		'preferedOrder' => 'nombre ASC',
		'icon' => 'bank.png',
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
			'nit' => array(
				'single' => 'NIT',
				'type' => 'text',
				'size' => 20,
				'maxlength' => 20,
				'notNull' => true,
				'filters' => array('striptags')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'notNull' => true,
				'notSearch' => true,
				////'notReport' => true,
				'filters' => array('striptags', 'extraspaces')
			),
			'ciudades_id' => array(
				'single' => 'Ciudad',
				'type' => 'ciudad',
				'notBrowse' => true,
				'notSearch' => true,
				//'notReport' => true,
				'notNull' => true,
				'filters' => array('int')
			),
			'direccion' => array(
				'single' => 'Direcci&oacute;n',
				'type' => 'text',
				'size' => 30,
				'notNull' => true,
				'notSearch' => true,
				'notBrowse' => true,
				//'notReport' => true,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'telefono' => array(
				'single' => 'Tel&eacute;fono',
				'type' => 'int',
				'size' => 12,
				'maxlength' => 20,
				'notSearch' => true,
				'notBrowse' => true,
				//'notReport' => true,
				'notNull' => true,
				'filters' => array('int')
			),
			'fax' => array(
				'single' => 'Fax',
				'type' => 'int',
				'size' => 12,
				'notSearch' => true,
				'notBrowse' => true,
				//'notReport' => true,
				'maxlength' => 20,
				'filters' => array('int')
			),
			'sitweb' => array(
				'single' => 'Sitio Web',
				'type' => 'text',
				'size' => 30,
				'notSearch' => true,
				'notBrowse' => true,
				//'notReport' => true,
				'maxlength' => 120,
				'filters' => array('striptags', 'extraspaces')
			),
			'email' => array(
				'single' => 'Correo Electr&oacute;nico',
				'type' => 'text',
				'size' => 30,
				'notSearch' => true,
				'notBrowse' => true,
				//'notReport' => true,
				'maxlength' => 120,
				'filters' => array('email')
			),
			'creservas' => array(
				'single' => 'Consecutivo de Reservas',
				'type' => 'int',
				'size' => 10,
				'notSearch' => true,
				//'notReport' => true,
				'maxlength' => 10,				
				'filters' => array('int')
			),
			'crc' => array(
				'single' => 'Consecutivo de RC',
				'type' => 'int',
				'size' => 10,
				'notSearch' => true,
				//'notReport' => true,
				'maxlength' => 10,				
				'filters' => array('int')
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}