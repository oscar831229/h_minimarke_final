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

class Discount extends ActiveRecord {

	protected function beforeCreate(){
		return POSRcs::beforeCreate($this);
	}

	protected function afterCreate(){
		return POSRcs::afterCreate($this);
	}

	protected function beforeUpdate(){
		return POSRcs::beforeUpdate($this);
	}

	protected function afterUpdate(){
		return POSRcs::afterUpdate($this);
	}

	protected function beforeDelete(){
		if($this->countAccountDiscount()){
			Flash::error("No se puede borrar el descuento porque ha sido usado en pedidos");
			return false;
		}
		if($this->estado=='A'){
			Flash::error("No se puede borrar el pedido porque está activo");
			return false;
		}
	}

	protected function initialize(){
		$this->hasMany('AccountDiscount');
	}

}

