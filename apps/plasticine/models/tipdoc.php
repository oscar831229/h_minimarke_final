<?php

class Tipdoc extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $tipdoc;

	/**
	 * @var string
	 */
	protected $detalle;

	/**
	 * @var string
	 */
	protected $tipcoa;

	/**
	 * @var integer
	 */
	protected $tipint;


	/**
	 * Metodo para establecer el valor del campo tipdoc
	 * @param integer $tipdoc
	 */
	public function setTipdoc($tipdoc){
		$this->tipdoc = $tipdoc;
	}

	/**
	 * Metodo para establecer el valor del campo detalle
	 * @param string $detalle
	 */
	public function setDetalle($detalle){
		$this->detalle = $detalle;
	}

	/**
	 * Metodo para establecer el valor del campo tipcoa
	 * @param string $tipcoa
	 */
	public function setTipcoa($tipcoa){
		$this->tipcoa = $tipcoa;
	}

	/**
	 * Metodo para establecer el valor del campo tipint
	 * @param integer $tipint
	 */
	public function setTipint($tipint){
		$this->tipint = $tipint;
	}


	/**
	 * Devuelve el valor del campo tipdoc
	 * @return integer
	 */
	public function getTipdoc(){
		return $this->tipdoc;
	}

	/**
	 * Devuelve el valor del campo detalle
	 * @return string
	 */
	public function getDetalle(){
		return $this->detalle;
	}

	/**
	 * Devuelve el valor del campo tipcoa
	 * @return string
	 */
	public function getTipcoa(){
		return $this->tipcoa;
	}

	/**
	 * Devuelve el valor del campo tipint
	 * @return integer
	 */
	public function getTipint(){
		return $this->tipint;
	}

}

