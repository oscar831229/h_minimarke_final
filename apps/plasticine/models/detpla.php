<?php

class Detpla extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codpla;

	/**
	 * @var integer
	 */
	protected $codcar;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var integer
	 */
	protected $moneda;


	/**
	 * Metodo para establecer el valor del campo codpla
	 * @param integer $codpla
	 */
	public function setCodpla($codpla){
		$this->codpla = $codpla;
	}

	/**
	 * Metodo para establecer el valor del campo codcar
	 * @param integer $codcar
	 */
	public function setCodcar($codcar){
		$this->codcar = $codcar;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo moneda
	 * @param integer $moneda
	 */
	public function setMoneda($moneda){
		$this->moneda = $moneda;
	}


	/**
	 * Devuelve el valor del campo codpla
	 * @return integer
	 */
	public function getCodpla(){
		return $this->codpla;
	}

	/**
	 * Devuelve el valor del campo codcar
	 * @return integer
	 */
	public function getCodcar(){
		return $this->codcar;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo moneda
	 * @return integer
	 */
	public function getMoneda(){
		return $this->moneda;
	}

}

