<?php

class Periodo extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $periodo;

	/**
	 * @var string
	 */
	protected $cierre;

	/**
	 * @var string
	 */
	protected $facturado;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo periodo
	 * @param integer $periodo
	 */
	public function setPeriodo($periodo){
		$this->periodo = $periodo;
	}

	/**
	 * Metodo para establecer el valor del campo cierre
	 * @param string $cierre
	 */
	public function setCierre($cierre){
		$this->cierre = $cierre;
	}

	/**
	 * Metodo para establecer el valor del campo facturado
	 * @param string $facturado
	 */
	public function setFacturado($facturado){
		$this->facturado = $facturado;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo periodo
	 * @return integer
	 */
	public function getPeriodo(){
		return $this->periodo;
	}

	/**
	 * Devuelve el valor del campo cierre
	 * @return string
	 */
	public function getCierre(){
		return $this->cierre;
	}

	/**
	 * Devuelve el valor del campo facturado
	 * @return string
	 */
	public function getFacturado(){
		return $this->facturado;
	}

}

