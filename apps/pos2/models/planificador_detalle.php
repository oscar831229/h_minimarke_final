<?php

class PlanificadorDetalle extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $planificador_id;

	/**
	 * @var integer
	 */
	protected $menus_items_id;

	/**
	 * @var integer
	 */
	protected $cantidad;

	/**
	 * @var string
	 */
	protected $costo;

	/**
	 * @var string
	 */
	protected $valor;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo planificador_id
	 * @param integer $planificador_id
	 */
	public function setPlanificadorId($planificador_id){
		$this->planificador_id = $planificador_id;
	}

	/**
	 * Metodo para establecer el valor del campo menus_items_id
	 * @param integer $menus_items_id
	 */
	public function setMenusItemsId($menus_items_id){
		$this->menus_items_id = $menus_items_id;
	}

	/**
	 * Metodo para establecer el valor del campo cantidad
	 * @param integer $cantidad
	 */
	public function setCantidad($cantidad){
		$this->cantidad = $cantidad;
	}

	/**
	 * Metodo para establecer el valor del campo costo
	 * @param string $costo
	 */
	public function setCosto($costo){
		$this->costo = $costo;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo planificador_id
	 * @return integer
	 */
	public function getPlanificadorId(){
		return $this->planificador_id;
	}

	/**
	 * Devuelve el valor del campo menus_items_id
	 * @return integer
	 */
	public function getMenusItemsId(){
		return $this->menus_items_id;
	}

	/**
	 * Devuelve el valor del campo cantidad
	 * @return integer
	 */
	public function getCantidad(){
		return $this->cantidad;
	}

	/**
	 * Devuelve el valor del campo costo
	 * @return string
	 */
	public function getCosto(){
		return $this->costo;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

}

