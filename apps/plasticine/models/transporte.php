<?php

class Transporte extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codtra;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $predeterminado;


	/**
	 * Metodo para establecer el valor del campo codtra
	 * @param integer $codtra
	 */
	public function setCodtra($codtra){
		$this->codtra = $codtra;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo predeterminado
	 * @param string $predeterminado
	 */
	public function setPredeterminado($predeterminado){
		$this->predeterminado = $predeterminado;
	}


	/**
	 * Devuelve el valor del campo codtra
	 * @return integer
	 */
	public function getCodtra(){
		return $this->codtra;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo predeterminado
	 * @return string
	 */
	public function getPredeterminado(){
		return $this->predeterminado;
	}

}

