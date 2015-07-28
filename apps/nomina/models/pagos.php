<?php

class Pagos extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $concepto;

	/**
	 * @var Date
	 */
	protected $fecha_i;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var Date
	 */
	protected $fecha_pago;

	/**
	 * @var Date
	 */
	protected $fecha_f;

	/**
	 * @var string
	 */
	protected $valor2;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo concepto
	 * @param string $concepto
	 */
	public function setConcepto($concepto){
		$this->concepto = $concepto;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_i
	 * @param Date $fecha_i
	 */
	public function setFechaI($fecha_i){
		$this->fecha_i = $fecha_i;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_pago
	 * @param Date $fecha_pago
	 */
	public function setFechaPago($fecha_pago){
		$this->fecha_pago = $fecha_pago;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_f
	 * @param Date $fecha_f
	 */
	public function setFechaF($fecha_f){
		$this->fecha_f = $fecha_f;
	}

	/**
	 * Metodo para establecer el valor del campo valor2
	 * @param string $valor2
	 */
	public function setValor2($valor2){
		$this->valor2 = $valor2;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo concepto
	 * @return string
	 */
	public function getConcepto(){
		return $this->concepto;
	}

	/**
	 * Devuelve el valor del campo fecha_i
	 * @return Date
	 */
	public function getFechaI(){
		return new Date($this->fecha_i);
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo fecha_pago
	 * @return Date
	 */
	public function getFechaPago(){
		return new Date($this->fecha_pago);
	}

	/**
	 * Devuelve el valor del campo fecha_f
	 * @return Date
	 */
	public function getFechaF(){
		return new Date($this->fecha_f);
	}

	/**
	 * Devuelve el valor del campo valor2
	 * @return string
	 */
	public function getValor2(){
		return $this->valor2;
	}

}

