<?php

class Saldosp extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $centro_costo;

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
	protected $pres;


	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param string $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
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
	 * Metodo para establecer el valor del campo pres
	 * @param string $pres
	 */
	public function setPres($pres){
		$this->pres = $pres;
	}


	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return string
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
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
	 * Devuelve el valor del campo pres
	 * @return string
	 */
	public function getPres(){
		return $this->pres;
	}

}

