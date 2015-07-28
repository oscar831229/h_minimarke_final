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

class Printers extends ActiveRecord {

	protected function beforeDelete(){
		if($this->countSalonMenusItems()){
			Flash::error("No se puede borrar la impresora porque está siendo usada en ambientes de menus");
			return false;
		}
		if($this->estado=='A'){
			Flash::error("No se puede borrar la impresora porque está activa");
			return false;
		}
	}

	protected function initialize(){
		$this->hasMany('SalonMenusItems');
	}

}
