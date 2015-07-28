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

class Modifiers extends ActiveRecord
{

	protected function beforeValidation()
	{
		$this->nombre_pedido = str_ireplace(' DE ', ' ', $this->nombre);
		$this->nombre_pedido = ucwords(i18n::strtolower(trim($this->nombre_pedido)));
		$this->nombre = i18n::strtoupper(trim($this->nombre));
	}

	protected function beforeCreate()
	{
		return POSRcs::beforeCreate($this);
	}

	protected function afterCreate()
	{
		return POSRcs::afterCreate($this);
	}

	protected function beforeUpdate()
	{
		return POSRcs::beforeUpdate($this);
	}

	protected function afterUpdate(){
		return POSRcs::afterUpdate($this);
	}

	protected function beforeDelete(){
		if($this->countMenusItemsModifiers()){
			Flash::error("No se puede borrar el modificador porque ha sido usado en un items de menú");
			return false;
		}
		if($this->countAccountModifiers()){
			Flash::error("No se puede borrar el ambiente porque ha sido usado en pedidos");
			return false;
		}
		if($this->estado=='A'){
			Flash::error("No se puede borrar el modificador porque está activo");
			return false;
		}
	}

	public function getTipoDetalle(){
		$tipos = array(
			'W' => 'CON',
			'S' => 'SIN',
			'P' => 'PORCIÓN',
			'A' => 'ADICIONAL',
			'U' => 'DE USUARIO'
		);
		if(isset($tipos[$this->tipo])){
			return $tipos[$this->tipo];
		} else {
			return "";
		}
	}

	public function initialize(){
		$this->hasMany('MenusItemsModifiers');
		$this->hasMany('AccountModifiers');
	}

}
