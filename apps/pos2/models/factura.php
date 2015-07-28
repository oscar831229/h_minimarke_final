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

class Factura extends ActiveRecord {

	public function initialize(){
		$this->belongsTo('Salon');
		$this->belongsTo('AccountMaster');
		$this->belongsTo(array('prefijo_facturacion', 'consecutivo_facturacion', 'tipo_venta'), 'AccountCuentas', array('prefijo', 'numero', 'tipo_venta'));
		$this->hasMany(array('prefijo_facturacion', 'consecutivo_facturacion', 'tipo'), 'DetalleFactura');
		$this->hasMany(array('prefijo_facturacion', 'consecutivo_facturacion', 'tipo'), 'PagosFactura');
	}

}
