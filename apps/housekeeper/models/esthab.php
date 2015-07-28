<?php

class Esthab extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codest;

	/**
	 * @var string
	 */
	protected $detalle;

	/**
	 * @var string
	 */
	protected $color;


	/**
	 * Metodo para establecer el valor del campo codest
	 * @param integer $codest
	 */
	public function setCodest($codest){
		$this->codest = $codest;
	}

	/**
	 * Metodo para establecer el valor del campo detalle
	 * @param string $detalle
	 */
	public function setDetalle($detalle){
		$this->detalle = $detalle;
	}

	/**
	 * Metodo para establecer el valor del campo color
	 * @param string $color
	 */
	public function setColor($color){
		$this->color = $color;
	}


	/**
	 * Devuelve el valor del campo codest
	 * @return integer
	 */
	public function getCodest(){
		return $this->codest;
	}

	/**
	 * Devuelve el valor del campo detalle
	 * @return string
	 */
	public function getDetalle(){
		return $this->detalle;
	}

	/**
	 * Devuelve el valor del campo color
	 * @return string
	 */
	public function getColor(){
		return $this->color;
	}

}

