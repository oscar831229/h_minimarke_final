<?php

class Temparam extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $nit1_a;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $definitiva_a;

	/**
	 * @var string
	 */
	protected $retiro_a;

	/**
	 * @var string
	 */
	protected $dispo_a;


	/**
	 * Metodo para establecer el valor del campo nit1_a
	 * @param string $nit1_a
	 */
	public function setNit1A($nit1_a){
		$this->nit1_a = $nit1_a;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo definitiva_a
	 * @param string $definitiva_a
	 */
	public function setDefinitivaA($definitiva_a){
		$this->definitiva_a = $definitiva_a;
	}

	/**
	 * Metodo para establecer el valor del campo retiro_a
	 * @param string $retiro_a
	 */
	public function setRetiroA($retiro_a){
		$this->retiro_a = $retiro_a;
	}

	/**
	 * Metodo para establecer el valor del campo dispo_a
	 * @param string $dispo_a
	 */
	public function setDispoA($dispo_a){
		$this->dispo_a = $dispo_a;
	}


	/**
	 * Devuelve el valor del campo nit1_a
	 * @return string
	 */
	public function getNit1A(){
		return $this->nit1_a;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo definitiva_a
	 * @return string
	 */
	public function getDefinitivaA(){
		return $this->definitiva_a;
	}

	/**
	 * Devuelve el valor del campo retiro_a
	 * @return string
	 */
	public function getRetiroA(){
		return $this->retiro_a;
	}

	/**
	 * Devuelve el valor del campo dispo_a
	 * @return string
	 */
	public function getDispoA(){
		return $this->dispo_a;
	}

}

