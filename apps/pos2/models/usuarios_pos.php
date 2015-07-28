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

class UsuariosPos extends ActiveRecord
{

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

	protected function beforeSave()
	{
		$this->nombre = i18n::strtoupper($this->nombre);
	}

	protected function beforeDelete(){
		if ($this->countAccountMaster())
		{
			Flash::error("No se puede borrar el usuario porque ha hecho pedidos");
			return false;
		}
		if ($this->countFactura())
		{
			Flash::error("No se puede borrar el usuario porque ha hecho facturas/ordenes");
			return false;
		}
		if ($this->estado == 'A')
		{
			Flash::error("No se puede borrar el usuario porque está activo");
			return false;
		}
	}

	protected function afterDelete()
	{
		foreach ($this->getPermisos() as $permiso) {
			$permiso->delete();
		}
	}

	public function initialize()
	{
		$this->hasMany( 'id', 'AccountMaster', 'usuarios_id');
		$this->hasMany('Factura');
		$this->hasMany('Permisos');
	}

}
