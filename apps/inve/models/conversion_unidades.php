<?php

class ConversionUnidades extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $unidad;

	/**
	 * @var string
	 */
	protected $unidad_base;

	/**
	 * @var string
	 */
	protected $factor_conversion;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo unidad
	 * @param string $unidad
	 */
	public function setUnidad($unidad){
		$this->unidad = $unidad;
	}

	/**
	 * Metodo para establecer el valor del campo unidad_base
	 * @param string $unidad_base
	 */
	public function setUnidadBase($unidad_base){
		$this->unidad_base = $unidad_base;
	}

	/**
	 * Metodo para establecer el valor del campo factor_conversion
	 * @param string $factor_conversion
	 */
	public function setFactorConversion($factor_conversion){
		$this->factor_conversion = $factor_conversion;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo unidad
	 * @return string
	 */
	public function getUnidad(){
		return $this->unidad;
	}

	/**
	 * Devuelve el valor del campo unidad_base
	 * @return string
	 */
	public function getUnidadBase(){
		return $this->unidad_base;
	}

	/**
	 * Devuelve el valor del campo factor_conversion
	 * @return string
	 */
	public function getFactorConversion(){
		return $this->factor_conversion;
	}

}

