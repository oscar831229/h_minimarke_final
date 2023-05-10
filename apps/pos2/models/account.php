<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Account extends ActiveRecord
{

	public $id;
	public $account_master_id;
	public $salon_mesas_id;
	public $comanda;
	public $cuenta;
	public $asiento;
	public $menus_items_id;
	public $cantidad;
	public $cantidad_atendida;
	public $cantidad_cocina;
	public $valor;
	public $servicio;
	public $iva;
	public $impo;
	public $total;
	public $descuento;
	public $tiempo;
	public $tiempo_final;
	public $note;
	public $estado;
	public $send_kitchen;

	protected function initialize()
	{
		$this->belongsTo('AccountMaster');
		$this->belongsTo(array('account_master_id', 'cuenta'), 'AccountCuentas', array('account_master_id', 'cuenta'));
		$this->belongsTo('MenusItems');
		$this->belongsTo('SalonMesas');
		$this->hasMany('AccountModifiers');
		$this->hasMany('DetalleFactura');
	}

}
