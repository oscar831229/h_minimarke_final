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

class SalonMenusItems extends ActiveRecord
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

	protected function beforeDelete()
	{
		Flash::error('No se puede borrar el item en el ambiente, solo puede inactivarlo');
		return false;
	}

	protected function beforeSave()
	{
		if($this->estado=='A'){

			if($this->descarga=='S'){
				$this->almacen = (int) $this->almacen;
				$existe = EntityManager::get('Almacenes')->count("codigo='{$this->almacen}'");
				if($existe==false){
					$this->appendMessage(new ActiveRecordMessage('El almacen asignado para descarga no existe', 'almacen'));
					return false;
				}
			}

			$this->conceptos_id = (int) $this->conceptos_id;
			if($this->conceptos_id>0){
				$existe = EntityManager::get('Conceptos')->count($this->conceptos_id);
				if($existe==false){
					$this->appendMessage(new ActiveRecordMessage('El concepto de front no es válido', 'conceptos_front_id'));
					return false;
				}
			} else {
				$this->appendMessage(new ActiveRecordMessage('El concepto de front no es válido', 'conceptos_front_id'));
				return false;
			}
		}
	}

	protected function validation()
	{

		$this->validate('InclusionIn', array(
			'field' => 'descarga',
			'domain' => array('S', 'N'),
			'message' => 'El campo "Descarga?" debe ser "SI" ó "NO"',
			'required' => true
		));

		$this->validate('InclusionIn', array(
			'field' => 'estado',
			'domain' => array('A', 'I'),
			'message' => 'El estado debe ser "ACTIVO" ó "INACTIVO"',
			'required' => true
		));

		if($this->validationHasFailed()==true){
			return false;
		}

	}

	protected function initialize()
	{
		$this->belongsTo('Salon');
		$this->belongsTo('MenusItems');

		$this->addForeignKey('salon_id', 'Salon', 'id', array(
			'message' => 'El ambiente indicado no es válido'
		));

		$this->addForeignKey('menus_items_id', 'MenusItems', 'id', array(
			'message' => 'El menú item indicado no es válido'
		));
	}

}
