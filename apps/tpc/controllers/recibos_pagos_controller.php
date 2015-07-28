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
 * Recibos_PagosController
 *
 * Controlador de Recibos de Pago
 *
 */
class Recibos_PagosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'RecibosPagos',
		'plural' => 'RecibosPagos',
		'single' => 'ReciboPago',
		'genre' => 'M',
		'tabName' => 'ReciboPagos',
		'preferedOrder' => 'fecha_pago ASC',
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
			'recibo_provisional' => array(
				'single' => 'Recibo Provisional',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'filters' => array('striptags', 'extraspaces')
			),
			'socios_id' => array(
				'single' => 'Socio',
				'type' => 'socioTc',
				'size' => 30,
				'maxlength' => 40,
				'notNull' => true,
				'filters' => array('int')
			),
			'ciudad_pago' => array(
				'single' => 'Ciudad Pago',
				'type' => 'ciudad',
				'size' => 30,
				'maxlength' => 40,
				'notNull' => true,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'fecha_pago' => array(
				'single' => 'Fecha Pago',
				'type' => 'date',
				'default' => '',
				'notNull' => true,
				'filters' => array('date')
			),
			'fecha_recibo' => array(
				'single' => 'Fecha Recibo',
				'type' => 'date',
				'default' => '',
				'notNull' => true,
				'filters' => array('date')
			),
			'valor_pagado' => array(
				'single' => 'Valor Pagado',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notNull' => true,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'valor_reserva' => array(
				'single' => 'Valor Reserva',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'valor_cuoact' => array(
				'single' => 'Valor Cuaota Activación',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'valor_cuoafi' => array(
				'single' => 'Valor Cuaota Afiliación',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'valor_capital' => array(
				'single' => 'Valor Capital',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'valor_interesc' => array(
				'single' => 'Valor Interes Corriente',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'valor_interesm' => array(
				'single' => 'Valor Interes Mora',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'valor_inicial' => array(
				'single' => 'Valor Inicial',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'valor_financiacion' => array(
				'single' => 'Valor Financiación',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'cuentas_id' => array(
				'single' => 'Cuentas',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notNull' => true,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'otros' => array(
				'single' => 'Otros',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'observaciones' => array(
				'single' => 'Observaciones',
				'type' => 'text',
				'size' => 30,
				'maxlength' => 40,
				'notNull' => true,
				'notSearch'	=> true,
				'filters' => array('alpha')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notNull' => true,
				'values' => array(
					'V' => 'Normal',
					'K' => 'Capital',
					'N' => 'Nota Contable'
				),
				'filters' => array('onechar')
			),
			'aplico' => array(
				'single' => 'Aplico',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notNull' => true,
				'values' => array(
					'S' => 'Si',
					'N' => 'No'
				),
				'filters' => array('onechar')
			),
			'pago_posterior' => array(
				'single' => 'Pago Posterior',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'notNull' => true,
				'values' => array(
					'S' => 'Si',
					'N' => 'No'
				),
				'filters' => array('onechar')
			),
			'rc' => array(
				'single' => 'RC',
				'type' => 'int',
				'size' => 30,
				'readOnly' => true,
				'maxlength' => 40,
				'filters' => array('int')
			),
			'abono_reservas_id' => array(
				'single' => 'Abono Reservas Id',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,
				'filters' => array('int')
			),
			'cuota_saldo' => array(
				'single' => 'Cuota Saldo',
				'type' => 'int',
				'size' => 30,
				'maxlength' => 40,
				'notSearch'	=> true,				
				'filters' => array('int')
			),
			/*'calculos' => array(
				'single' => 'Calculos',
				'type' => 'textarea',
				'size' => 30,
				'maxlength' => 40,
				'cols' => '30',
				'rows' => '5',
				'notNull' => true,
				'notSearch'	=> true,
				'notBrowse'	=> true,
				'filters' => array('alpha')
			)*/
		)
	);
	
	public function beforeUpdate($transaction, $record)
	{
		$record->setValidar(false);
		
		$controlPagos = $this->ControlPagos->findFirst("recibos_pagos_id='{$record->getId()}'");
		if ($controlPagos) {
			$controlPagos->setSociosId($record->getSociosId());
			if ($controlPagos->getPagado()!=$record->getValorPagado()) {
				$controlPagos->setPagado($record->getValorPagado());
				
				#detalle pagos
				$detalleRP = $this->DetalleRecibosPagos->deleteAll("recibos_pagos_id='{$record->getId()}'");
				$detalleRecibosPagos = new DetalleRecibosPagos();
				$detalleRecibosPagos->setRecibosPagosId($record->getId());
				$detalleRecibosPagos->setFormasPagoId(1);
				$detalleRecibosPagos->setValor($record->getValorPagado());
				$detalleRecibosPagos->save();
			}
			
		}
		
		return true;
	}

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}
}
