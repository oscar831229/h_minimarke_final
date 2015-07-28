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
 * Pagos_AutomaticosController
 *
 * Controlador de pagos automaticos de un socio
 *
 */
class Pagos_AutomaticosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'PagosAutomaticos',
		'plural' => 'Pagos Automaticos',
		'single' => 'Pago Automatico',
		'genre' => 'M',
		'preferedOrder' => 'socios_id ASC',
		'icon' => 'credit-card.png',
		'fields' => array(
			'id' => array(
				'single'	=> 'Código',
				'type'		=> 'int',
				'size'		=> 3,
				'maxlength'	=> 3,
				'primary'	=> true,
				'filters'	=> array('int')
			),
			'socios_id' => array(
				'single'	=> 'Socio',
				'type'		=> 'Socio',
				'notNull'	=> true,
				'filters'	=> array('int')
			),
			'numero_tarjeta' => array(
				'single'	=> 'Número de Tarjeta',
				'type'		=> 'text',
				'size'		=> 30,
				'maxlength'	=> 40,
				'notNull'	=> true,
				'notSearch'	=> true,
				'notBrowse' => true,
				'filters'	=> array('alpha')
			),
			'fecha_exp' => array(
				'single'	=> 'Fecha de Expedición',
				'type'		=> 'date',
				'default'	=>'',
				'useDummy'	=> true,
				'notNull'	=> true,
				'notBrowse' => true,
				'filters'	=> array('date')
			),
			'fecha_ven' => array(
				'single'	=> 'Fecha de Vencimiento',
				'type'		=> 'date',
				'default'	=>'',
				'useDummy'	=> true,
				'notNull'	=> true,
				'notBrowse' => true,
				'filters'	=> array('date')
			),
			'formas_pago_id' => array(
				'single'		=> 'Forma de Pago',
				'type'			=> 'relation',
				'relation'		=> 'FormaPago',
				'fieldRelation'	=> 'codigo',
				'detail'		=> 'descripcion',
				'notNull'		=> true,
				'notBrowse' 	=> true,
				'filters'		=> array('int')
			),
			'bancos_id' => array(
				'single'		=> 'Banco',
				'type'			=> 'relation',
				'relation'		=> 'Banco',
				'fieldRelation'	=> 'id',
				'detail'		=> 'nombre',
				'notNull'		=> true,
				'notSearch'		=> true,
				'notBrowse' 	=> true,
				'filters'		=> array('int')
			),
			'digito_verificacion' => array(
				'single'	=> 'Digito Verificación',
				'type'		=> 'int',
				'size'		=>10,
				'maxlength'	=>10,
				'notNull'	=> true,
				'notSearch'	=> true,
				'notBrowse' => true,
				'filters'	=> array('int')
			),
			'estado' => array(
				'single'	=> 'Estado',
				'type'		=> 'closed-domain',
				'size'		=> 1,
				'notNull'	=> true,
				'maxlength'	=> 1,
				'values'	=> array(
					'A' => 'Activo',
					'I' => 'Inactivo'
				),
				'filters'	=> array('onechar')
			)
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
