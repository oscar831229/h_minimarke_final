<?php

class Fondos extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $clase;

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_fondo;

	/**
	 * @var string
	 */
	protected $nit;


	/**
	 * Metodo para establecer el valor del campo clase
	 * @param string $clase
	 */
	public function setClase($clase){
		$this->clase = $clase;
	}

	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_fondo
	 * @param string $nom_fondo
	 */
	public function setNomFondo($nom_fondo){
		$this->nom_fondo = $nom_fondo;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}


	/**
	 * Devuelve el valor del campo clase
	 * @return string
	 */
	public function getClase(){
		return $this->clase;
	}

	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_fondo
	 * @return string
	 */
	public function getNomFondo(){
		return $this->nom_fondo;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

}

