<?php

class Ubicacion extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_ubica;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_ubica
	 * @param string $nom_ubica
	 */
	public function setNomUbica($nom_ubica){
		$this->nom_ubica = $nom_ubica;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_ubica
	 * @return string
	 */
	public function getNomUbica(){
		return $this->nom_ubica;
	}

}

