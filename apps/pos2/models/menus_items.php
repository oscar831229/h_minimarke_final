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

class MenusItems extends ActiveRecord
{

	public $id;
	public $menus_id;
	public $nombre;
	public $nombre_pedido;
	public $tipo;
	public $image;
	public $valor;
	public $costo;
	public $porcentaje_iva;
	public $porcentaje_impoconsumo;
	public $porcentaje_servicio;
	public $tipo_costo;
	public $codigo_referencia;
	public $descontar;
	public $cambio_precio;
	public $estado;

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

	protected function afterCreate(){
		return POSRcs::afterCreate($this);
	}

	protected function beforeSave()
	{
		if ($this->descontar == 'T' && $this->tipo_costo == 'R') {
			$this->appendMessage(new ActiveRecordMessage("Las recetas no pueden ser descargas por tragos", "descontar"));
			return false;
		}

		if ($this->tipo_costo != 'N') {
			if ($this->tipo_costo == 'R') {
 				$Recetap = EntityManager::get('Recetap');
				if($Recetap->count("almacen=1 AND numero_rec='{$this->codigo_referencia}'") == 0) {
					$this->appendMessage(new ActiveRecordMessage("Ha seleccionado descargar por receta pero la que seleccionó no existe", "tipo_costo"));
					return false;
				}
			} else {
				if ($this->tipo_costo == 'N') {
					$Inve = EntityManager::get('Inve');
					if ($Inve->count("item='{$this->codigo_referencia}' AND estado='A'") == 0) {
						$this->appendMessage(new ActiveRecordMessage("Ha seleccionado descargar por referencia pero la que seleccionó no existe ó está inactiva", "tipo_costo"));
						return false;
					}
				}
			}
		}
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
		if($this->countAccount()){
			Flash::error('No se puede borrar el item de menú porque ha sido usado en pedidos');
			return false;
		}

		if($this->countDetalleFactura()){
			Flash::error('No se puede borrar el item de menú porque ha sido usado en facturas/ordenes de servicio');
			return false;
		}

		if($this->estado=='A'){
			Flash::error('No se puede borrar el item de menú porque está activo');
			return false;
		}
	}

	protected function afterDelete()
	{
		foreach ($this->getMenusItemsModifiers() as $menuItemModifier) {
			$menuItemModifier->delete();
		}
		foreach( $this->getSalonMenusItems() as $salonMenuItem) {
			$salonMenuItem->delete();
		}
	}

	protected function validation(){

		$this->validate('InclusionIn', array(
			'field' => 'tipo',
			'domain' => array('A', 'B', 'C', 'L', 'O'),
			'message' => 'El tipo debe ser "ALIMENTOS", "BEBIDAS", "CIGARRILLOS", "LAVANDERIA" ó "OTROS"',
			'required' => true
		));

		$this->validate('InclusionIn', array(
			'field' => 'tipo_costo',
			'domain' => array('N', 'R', 'I'),
			'message' => 'El tipo de costo debe ser "RECETA", "REFERENCIA" ó "NO APLICA"',
			'required' => true
		));

		$this->validate('InclusionIn', array(
			'field' => 'descontar',
			'domain' => array('N', 'T'),
			'message' => 'El campo "descontar" debe ser "NO APLICA" ó "TRAGO"',
			'required' => true
		));

		$this->validate('InclusionIn', array(
			'field' => 'cambio_precio',
			'domain' => array('S', 'N'),
			'message' => 'El campo "Permite Cambio Precio" debe ser "SI" ó "NO"',
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
		$this->belongsTo('Menus');
		$this->hasMany('Account');
		$this->hasMany('DetalleFactura');
		$this->hasMany('MenusItemsModifiers');
		$this->hasMany('SalonMenusItems');
		$this->addForeignKey('menus_id', 'Menus', 'id', array(
			'message' => 'El menú indicado no es válido'
		));
	}

}
