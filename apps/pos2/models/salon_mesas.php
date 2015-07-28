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

class SalonMesas extends ActiveRecord {

	protected function beforeDelete(){
		if($this->estado=='A'){
			Flash::error("No se puede borrar la mesa porque hay un pedido activo");
			return false;
		}
	}

	public function initialize(){
		$this->belongsTo('Salon');
	}

}
