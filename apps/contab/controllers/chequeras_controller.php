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
 * ChequerasController
 *
 * Controlador de chequeras
 *
 */
class ChequerasController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Chequeras',
		'plural' => 'chequeras',
		'single' => 'chequera',
		'icon' => 'cheque.png',
		'genre' => 'F',
		'preferedOrder' => 'cuentas_bancos_id',
		'fields' => array(
			'id' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'cuentas_bancos_id' => array(
				'single' => 'Cuenta Bancaria',
				'type' => 'relation',
				'relation' => 'CuentasBancos',
				'fieldRelation' => 'id',
				'detail' => 'descripcion',
				'filters' => array('int')
			),
			'numero_inicial' => array(
				'single' => 'Consecutivo Inicial',
				'type' => 'text',
				'size' => 8,
				'maxlength' => 8,
				'filters' => array('int')
			),
			'numero_final' => array(
				'single' => 'Consecutivo Final',
				'type' => 'text',
				'size' => 8,
				'maxlength' => 8,
				'filters' => array('int')
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
