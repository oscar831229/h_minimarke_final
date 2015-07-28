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

class AccountMaster extends ActiveRecord {

	protected function initialize(){
		$this->hasMany('Account');
		$this->belongsTo('SalonMesas');
		$this->belongsTo('Salon');
		$this->belongsTo('usuarios_id', 'UsuariosPos', 'id');
		$this->hasMany('AccountCuentas');
	}

}
