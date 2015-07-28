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
 * Cuentas_BancosController
 *
 * Controlador de cuentas bancarias
 *
 */
class Cuentas_BancosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'CuentasBancos',
		'plural' => 'cuentas bancarias',
		'single' => 'cuenta bancaria',
		'genre' => 'F',
		'preferedOrder' => 'descripcion',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'descripcion' => array(
				'single' => 'Descripción',
				'type' => 'text',
				'size' => 40,
				'maxlength' => 80,
				'filters' => array('striptags', 'extraspaces')
			),
			'banco_id' => array(
				'single' => 'Banco',
				'type' => 'relation',
				'relation' => 'Banco',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'filters' => array('int')
			),
			'tipo' => array(
				'single' => 'Tipo',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'A' => 'AHORROS',
					'C' => 'CORRIENTE'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('alpha')
			),
			'numero' => array(
				'single' => 'Número',
				'type' => 'text',
				'size' => 25,
				'maxlength' => 30,
				'filters' => array('striptags', 'extraspaces')
			),
			/*'es_sucursal' => array(
				'single' => 'Es Sucursal?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('alpha')
			),
			'transferencia' => array(
				'single' => 'Habilitado en Transferencia?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('alpha')
			),*/
			'cuenta' => array(
				'single' => 'Cuenta Contable',
				'type' => 'cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'centro_costo' => array(
				'single' => 'Centro de Costo',
				'type' => 'relation',
				'relation' => 'Centros',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_centro',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('int'),
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
