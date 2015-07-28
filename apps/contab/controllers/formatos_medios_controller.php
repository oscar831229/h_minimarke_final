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
 * Formatos_MediosController
 *
 * Controlador de los formatos de medios magneticos
 *
 */
class Formatos_MediosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Magfor',
		'plural' => 'formatos de medios',
		'single' => 'formato de medios',
		'genre' => 'M',
		'icon' => 'report-excel.png',
		'preferedOrder' => 'nombre',
		'fields' => array(
			'codfor' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 5,
				'maxlength' => 5,
				'primary' => true,
				'filters' => array('int')
			),
			'nombre' => array(
				'single' => 'Nombre',
				'type' => 'text',
				'size' => 50,
				'maxlength' => 1000,
				'filters' => array('striptags', 'extraspaces')
			),
			'version' => array(
				'single' => 'Versión',
				'type' => 'text',
				'size' => 3,
				'maxlength' => 3,
				'notBrowse' => true,
				'filters' => array('int')
			),
			'termen' => array(
				'single' => 'Tercero Cuantías Menores',
				'type' => 'tercero',
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('terceros')
			),
			'terexti' => array(
				'single' => 'Tercero Extranjero Inicial',
				'type' => 'text',
				'size' => 15,
				'maxlength' => 20,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('terceros')
			),
			'terextf' => array(
				'single' => 'Tercero Extranjero Final',
				'type' => 'text',
				'size' => 15,
				'maxlength' => 20,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('terceros')
			),
			'minimo' => array(
				'single' => 'Mínimo Cuantias Menores',
				'type' => 'decimal',
				'size' => 12,
				'maxlength' => 15,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'filters' => array('double')
			),
			'campo' => array(
				'single' => 'Campo Total',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notBrowse' => true,
				'notSearch' => true,
				'notReport' => true,
				'values' => array(
					'Pag' => 'VALOR DEL PAGO O ABONO',
					'Por' => 'PORCENTAJE DE PARTICIPACIÓN',
					'Ded' => 'PAGOS Ó ABONOS EN CUENTA QUE NO CONSTITUYAN COSTO Ó DEDUCCIÓN',
					'Ret' => 'RETENCIÓN QUE LE PRÁCTICARON',
					'Sal' => 'SALDO AL 31 DE DICIEMBRE',
					'Vabo' => 'VALOR ABONO O PAGO SUJETO A RETENCIÓN',
					'Val' => 'VALOR PATRIMONIAL',
					'Vimp' => 'VALOR DEL IMPUESTO DESCONTABLE (1005) Ó GENERADO (1006)',
					'Vdes' => 'VALOR SOLICITADO COMO DESCUENTO',
					'Vpag' => 'VALOR ACUMULADO DEL PAGO',
					'Vpar' => 'VALOR PARTICIPACIÓN',
					'Vret' => 'VALOR DE LA RETENCIÓN'
				),
				'filters' => array('alpha')
			),
		)
	);

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
