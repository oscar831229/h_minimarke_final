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
 * Novedades_FacturaController
 *
 * Controlador de pagos automaticos de un socio
 *
 */
class Novedades_FacturaController extends HyperFormController {

	static protected $_config = array(
		'model' => 'NovedadesFactura',
		'plural' => 'Novedades de Factura',
		'single' => 'Novedad de Factura',
		'genre' => 'M',
		'preferedOrder' => 'id DESC',
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
			'periodo' => array(
				'single'	=> 'Periodo',
				'type' => 'relation',
				'relation' => 'Periodo',
				'fieldRelation' => 'periodo',
				'detail' => 'periodo',
				'maxlength' => 45,
				'notNull' => true,
				'notBrowse' => true,
				'filters' => array('int')
			),
			'cargos_fijos_id' => array(
				'single' => 'Cargo Fijo',
				'type' => 'relation',
				'relation' => 'CargosFijos',
				'fieldRelation' => 'id',
				'detail' => 'nombre',
				'maxlength' => 45,
				'notNull' => true,
				//'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('int')
			),
			'valor' => array(
				'single'	=> 'Valor',
				'type'		=> 'int',
				'size'		=> 12,
				'maxlength'	=> 15,
				'notBrowse' => true,
				'filters'	=> array('float')
			),
			'iva' => array(
				'single'	=> 'Iva',
				'type'		=> 'int',
				'size'		=> 12,
				'maxlength'	=> 15,
				'notBrowse' => true,
				'filters'	=> array('float')
			),
            'ico' => array(
                'single'	=> 'Ico',
                'type'		=> 'int',
                'size'		=> 12,
                'maxlength'	=> 15,
                'notBrowse' => true,
                'filters'	=> array('float')
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
