<?php

class Plazos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $min_dias;

	/**
	 * @var integer
	 */
	protected $max_dias;

	/**
	 * @var string
	 */
	protected $descripcion;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo min_dias
	 * @param integer $min_dias
	 */
	public function setMinDias($min_dias){
		$this->min_dias = $min_dias;
	}

	/**
	 * Metodo para establecer el valor del campo max_dias
	 * @param integer $max_dias
	 */
	public function setMaxDias($max_dias){
		$this->max_dias = $max_dias;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo min_dias
	 * @return integer
	 */
	public function getMinDias(){
		return $this->min_dias;
	}

	/**
	 * Devuelve el valor del campo max_dias
	 * @return integer
	 */
	public function getMaxDias(){
		return $this->max_dias;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

}

