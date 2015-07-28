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
 * Datos_ClubController
 *
 * Controlador de Datos del Club
 *
 */
class Datos_ClubController extends HyperFormController {

	static protected $_config = array(
		'model' => 'DatosClub',
		'plural' => 'Datos del Club',
		'single' => 'Datos del Club',
		'genre' => 'M',
		'tabName' => 'datos_club',
		'preferedOrder' => 'id DESC',
		'icon' => 'home.png',
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
				'filters' => array('int')
			),
			'nit' => array(
				'single' => 'Nit',
				'type' => 'text',
				'size' => 20,
				'maxlength' => 20,
				'filters' => array('striptags')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'filters' => array('alpha')
			),
			'nomcad' => array(
				'single'	=> 'Nombre Cad',
				'type'		=> 'text',
				'size'		=> 30,
				'maxlength'	=> 40,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('alpha')
			),
			'nomger' => array(
				'single' => 'Nombre Gerente',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('alpha')
			),
			'ciudad_id' => array(
				'single' => 'Ciudad',
				'type' => 'ciudad',
				'size' => 10,
				'maxlength' => 10,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('int')
			),
			'direccion' => array(
				'single' => 'Dirección',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'filters' => array('alpha')
			),
			'telefono' => array(
				'single' => 'Teléfono',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'filters' => array('int')
			),
			'fax' => array(
				'single' => 'Fax',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('int')
			),
			'sitweb' => array(
				'single' => 'Sitio Web',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('striptags')
			),
			'email' => array(
				'single' => 'E-Mail',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('email')
			),
			'resfac' => array(
				'single' => 'Resolución de factura',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('alpha')
			),
			'fecfac' => array(
				'single'	=> 'Fecha de Factura',
				'type'		=> 'date',
				'default'	=> '',
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('date')
			),
			'prefac' => array(
				'single' => 'Consecutivo Prefactura',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('int')
			),
			'numfac' => array(
				'single' => 'Consecutivo de Factura',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'numfai' => array(
				'single' => 'Factura Inicial',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('int')
			),
			'numfaf' => array(
				'single' => 'Factura Final',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('int')
			),
			'numrec' => array(
				'single' => 'Consecutivo recibo de caja',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'numsoc' => array(
				'single' => 'Consecutivo Socios',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'imagen' => array(
				'single' => 'Imagen',
				'type' => 'image',
				'size' => 40,
				'maxlength' => 40,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('alpha')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
	
}
