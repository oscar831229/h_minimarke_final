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

class AccountCuentas extends ActiveRecord {

	public function initialize(){
		$this->belongsTo('AccountMaster');
		$this->belongsTo('Habitacion');
		$this->hasMany(array('account_master_id', 'cuenta'), 'Account');
		$this->hasOne(array('prefijo', 'numero', 'tipo_venta'), 'Factura', array('prefijo_facturacion', 'consecutivo_facturacion', 'tipo_venta'));
	}

}

