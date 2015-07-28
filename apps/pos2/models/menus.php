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

class Menus extends ActiveRecord
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

	protected function afterUpdate()
	{
		return POSRcs::afterUpdate($this);
	}

	protected function beforeDelete()
	{
		if ($this->countMenusItems()) {
			Flash::error("No se puede borrar el item de menú porque ha sido usado en items de menus");
			return false;
		}
		if ($this->estado == 'A') {
			Flash::error("No se puede borrar el item de menú está activo");
			return false;
		}
	}

	protected function initialize()
	{
		$this->hasMany('MenusItems');
	}

}
