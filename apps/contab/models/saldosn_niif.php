<?php

class SaldosnNiif extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $ano_mes;

	/**
	 * @var string
	 */
	protected $debe;

	/**
	 * @var string
	 */
	protected $haber;

	/**
	 * @var string
	 */
	protected $saldo;

	/**
	 * @var string
	 */
	protected $base_grab;

	/**
	 * @var string
	 */
	protected $depre;

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo ano_mes
	 * @param string $ano_mes
	 */
	public function setAnoMes($ano_mes){
		$this->ano_mes = $ano_mes;
	}

	/**
	 * Metodo para establecer el valor del campo debe
	 * @param string $debe
	 */
	public function setDebe($debe){
		$this->debe = $debe;
	}

	/**
	 * Metodo para establecer el valor del campo haber
	 * @param string $haber
	 */
	public function setHaber($haber){
		$this->haber = $haber;
	}

	/**
	 * Metodo para establecer el valor del campo saldo
	 * @param string $saldo
	 */
	public function setSaldo($saldo){
		$this->saldo = $saldo;
	}

	/**
	 * Metodo para establecer el valor del campo base_grab
	 * @param string $base_grab
	 */
	public function setBaseGrab($base_grab){
		$this->base_grab = $base_grab;
	}


	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo ano_mes
	 * @return string
	 */
	public function getAnoMes(){
		return $this->ano_mes;
	}

	/**
	 * Devuelve el valor del campo debe
	 * @return string
	 */
	public function getDebe(){
		return $this->debe;
	}

	/**
	 * Devuelve el valor del campo haber
	 * @return string
	 */
	public function getHaber(){
		return $this->haber;
	}

	/**
	 * Devuelve el valor del campo saldo
	 * @return string
	 */
	public function getSaldo(){
		return $this->saldo;
	}

	/**
	 * Devuelve el valor del campo base_grab
	 * @return string
	 */
	public function getBaseGrab(){
		return $this->base_grab;
	}

	/**
	 * Asigna el valor del campo depre
	 * @param string $depre
	 */
	public function setDepre($depre){
		$this->depre = $depre;
	}

	/**
	 * Devuelve el valor del campo depre
	 * @return string
	 */
	public function getDepre(){
		return $this->depre;
	}
}
