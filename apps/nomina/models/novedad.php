<?php

class Novedad extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $concepto;

	/**
	 * @var string
	 */
	protected $secuencia;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $veces;

	/**
	 * @var string
	 */
	protected $accion;

	/**
	 * @var string
	 */
	protected $periodicidad;

	/**
	 * @var Date
	 */
	protected $fecha_i;

	/**
	 * @var Date
	 */
	protected $fecha_f;

	/**
	 * @var string
	 */
	protected $clase_p;

	/**
	 * @var string
	 */
	protected $numero_p;

	/**
	 * @var string
	 */
	protected $por_retiro;


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
	 * Metodo para establecer el valor del campo secuencia
	 * @param string $secuencia
	 */
	public function setSecuencia($secuencia){
		$this->secuencia = $secuencia;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo veces
	 * @param string $veces
	 */
	public function setVeces($veces){
		$this->veces = $veces;
	}

	/**
	 * Metodo para establecer el valor del campo accion
	 * @param string $accion
	 */
	public function setAccion($accion){
		$this->accion = $accion;
	}

	/**
	 * Metodo para establecer el valor del campo periodicidad
	 * @param string $periodicidad
	 */
	public function setPeriodicidad($periodicidad){
		$this->periodicidad = $periodicidad;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_i
	 * @param Date $fecha_i
	 */
	public function setFechaI($fecha_i){
		$this->fecha_i = $fecha_i;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_f
	 * @param Date $fecha_f
	 */
	public function setFechaF($fecha_f){
		$this->fecha_f = $fecha_f;
	}

	/**
	 * Metodo para establecer el valor del campo clase_p
	 * @param string $clase_p
	 */
	public function setClaseP($clase_p){
		$this->clase_p = $clase_p;
	}

	/**
	 * Metodo para establecer el valor del campo numero_p
	 * @param string $numero_p
	 */
	public function setNumeroP($numero_p){
		$this->numero_p = $numero_p;
	}

	/**
	 * Metodo para establecer el valor del campo por_retiro
	 * @param string $por_retiro
	 */
	public function setPorRetiro($por_retiro){
		$this->por_retiro = $por_retiro;
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
	 * Devuelve el valor del campo secuencia
	 * @return string
	 */
	public function getSecuencia(){
		return $this->secuencia;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo veces
	 * @return string
	 */
	public function getVeces(){
		return $this->veces;
	}

	/**
	 * Devuelve el valor del campo accion
	 * @return string
	 */
	public function getAccion(){
		return $this->accion;
	}

	/**
	 * Devuelve el valor del campo periodicidad
	 * @return string
	 */
	public function getPeriodicidad(){
		return $this->periodicidad;
	}

	/**
	 * Devuelve el valor del campo fecha_i
	 * @return Date
	 */
	public function getFechaI(){
		return new Date($this->fecha_i);
	}

	/**
	 * Devuelve el valor del campo fecha_f
	 * @return Date
	 */
	public function getFechaF(){
		return new Date($this->fecha_f);
	}

	/**
	 * Devuelve el valor del campo clase_p
	 * @return string
	 */
	public function getClaseP(){
		return $this->clase_p;
	}

	/**
	 * Devuelve el valor del campo numero_p
	 * @return string
	 */
	public function getNumeroP(){
		return $this->numero_p;
	}

	/**
	 * Devuelve el valor del campo por_retiro
	 * @return string
	 */
	public function getPorRetiro(){
		return $this->por_retiro;
	}

}

