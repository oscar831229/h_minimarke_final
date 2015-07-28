<?php

class Bancos extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $bancos;

	/**
	 * @var string
	 */
	protected $oficina;

	/**
	 * @var string
	 */
	protected $ciudad;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo bancos
	 * @param string $bancos
	 */
	public function setBancos($bancos){
		$this->bancos = $bancos;
	}

	/**
	 * Metodo para establecer el valor del campo oficina
	 * @param string $oficina
	 */
	public function setOficina($oficina){
		$this->oficina = $oficina;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad
	 * @param string $ciudad
	 */
	public function setCiudad($ciudad){
		$this->ciudad = $ciudad;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo bancos
	 * @return string
	 */
	public function getBancos(){
		return $this->bancos;
	}

	/**
	 * Devuelve el valor del campo oficina
	 * @return string
	 */
	public function getOficina(){
		return $this->oficina;
	}

	/**
	 * Devuelve el valor del campo ciudad
	 * @return string
	 */
	public function getCiudad(){
		return $this->ciudad;
	}

}

