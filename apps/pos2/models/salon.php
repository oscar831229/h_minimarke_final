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

class Salon extends ActiveRecord {

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
		if($this->countSalonMenusItems()){
			Flash::error('No se puede borrar el ambiente porque ha sido usado en items de menus');
			return false;
		}
		if($this->countAccountMaster()){
			Flash::error('No se puede borrar el ambiente porque ha sido usado en pedidos');
			return false;
		}
		if($this->countFactura()){
			Flash::error('No se puede borrar el ambiente porque ha sido usado en facturas/ordenes');
			return false;
		}
		if($this->estado=='A'){
			Flash::error('No se puede borrar el ambiente porque está activo');
			return false;
		}
		foreach($this->getSalonMesas() as $salonMesa){
			if($salonMesa->estado=='A'){
				Flash::error('No se puede borrar el ambiente porque hay pedidos activos');
				return false;
			}
		}
	}

	protected function afterDelete(){
		foreach($this->getSalonMesas() as $salonMesa){
			$salonMesa->delete();
		}
	}

	protected function initialize(){
		$this->hasMany('SalonMesas');
		$this->hasMany('SalonMenusItems');
		$this->hasMany('Factura');
		$this->hasMany('AccountMaster');

		//Llaves foráneas
		/*$this->addForeignKey('centro_costo', 'Centros', 'codigo', array(
			'message' => 'El centro de costo indicado no es válido'
		));*/
	}

}

