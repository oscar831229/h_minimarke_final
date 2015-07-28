<?php

class Retecompras extends RcsRecord 
{

	/**
	 * @var integer
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $base_retencion;

	/**
	 * @var string
	 */
	protected $porce_retencion;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param integer $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo base_retencion
	 * @param string $base_retencion
	 */
	public function setBaseRetencion($base_retencion){
		$this->base_retencion = $base_retencion;
	}

	/**
	 * Metodo para establecer el valor del campo porce_retencion
	 * @param string $porce_retencion
	 */
	public function setPorceRetencion($porce_retencion){
		$this->porce_retencion = $porce_retencion;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return integer
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo base_retencion
	 * @return string
	 */
	public function getBaseRetencion(){
		return $this->base_retencion;
	}

	/**
	 * Devuelve el valor del campo porce_retencion
	 * @return string
	 */
	public function getPorceRetencion(){
		return $this->porce_retencion;
	}

}

