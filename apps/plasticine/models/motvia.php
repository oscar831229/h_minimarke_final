<?php

class Motvia extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codmot;

	/**
	 * @var string
	 */
	protected $detalle;

	/**
	 * @var string
	 */
	protected $predeterminado;


	/**
	 * Metodo para establecer el valor del campo codmot
	 * @param integer $codmot
	 */
	public function setCodmot($codmot){
		$this->codmot = $codmot;
	}

	/**
	 * Metodo para establecer el valor del campo detalle
	 * @param string $detalle
	 */
	public function setDetalle($detalle){
		$this->detalle = $detalle;
	}

	/**
	 * Metodo para establecer el valor del campo predeterminado
	 * @param string $predeterminado
	 */
	public function setPredeterminado($predeterminado){
		$this->predeterminado = $predeterminado;
	}


	/**
	 * Devuelve el valor del campo codmot
	 * @return integer
	 */
	public function getCodmot(){
		return $this->codmot;
	}

	/**
	 * Devuelve el valor del campo detalle
	 * @return string
	 */
	public function getDetalle(){
		return $this->detalle;
	}

	/**
	 * Devuelve el valor del campo predeterminado
	 * @return string
	 */
	public function getPredeterminado(){
		return $this->predeterminado;
	}

}

