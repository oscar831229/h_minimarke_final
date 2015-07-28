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
 * CentrosController
 *
 * Controlador de los centros de costo
 *
 */
class LineasController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Lineas',
		'plural' => 'líneas',
		'single' => 'línea',
		'genre' => 'F',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'almacen' => array(
				'single' => 'Almacén',
				'type' => 'relation',
				'relation' => 'Almacenes',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_almacen',
				'primary' => true,
				'filters' => array('int')
			),
			'linea' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10,
				'primary' => true,
				'filters' => array('alpha')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 30,
				'filters' => array('striptags', 'extraspaces')
			),
			'es_auxiliar' => array(
				'single' => 'Recibe Referencias?',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'values' => array(
					'S' => 'SI',
					'N' => 'NO'
				),
				'filters' => array('alpha')
			),
			'cta_compra' => array(
				'single' => 'Cuenta Entradas',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_venta' => array(
				'single' => 'Cuenta Ventas',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_consumo' => array(
				'single' => 'Cuenta Consumo Interno',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_descuento' => array(
				'single' => 'Cuenta Descuento',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_inve' => array(
				'single' => 'Cuenta Salidas',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'filters' => array('cuentas')
			),
			'cta_costo_venta' => array(
				'single' => 'Cuenta Costo Ventas',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_ret_compra' => array(
				'single' => 'Cuenta Retención Compras',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'porc_compra' => array(
				'single' => '% Retención Compras',
				'type' => 'decimal',
				'size' => 5,
				'maxlength' => 5,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),
			/*'minimo_ret' => array(
				'single' => 'Valor Mínimo Retención',
				'type' => 'decimal',
				'size' => 15,
				'maxlength' => 15,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),*/
			'cta_dev_ventas' => array(
				'single' => 'Cuenta Devolución Ventas',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_dev_compras' => array(
				'single' => 'Cuenta Devolución Compra',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'cta_hortic' => array(
				'single' => 'Cuenta Hortifrutícola',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'porc_hortic' => array(
				'single' => '% Retención Hortifrutícola',
				'type' => 'decimal',
				'size' => 5,
				'maxlength' => 5,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('float')
			),
			'impo_costo' => array(
				'single' => 'Cuenta Impoconsumo de Costo',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
			'impo_gasto' => array(
				'single' => 'Cuenta Impoconsumo de Gasto',
				'type' => 'Cuenta',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('cuentas')
			),
		)
	);

	public function initialize()
	{
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
