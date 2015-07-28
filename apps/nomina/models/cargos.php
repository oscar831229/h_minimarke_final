<?php

class Cargos extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_cargo;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_cargo
	 * @param string $nom_cargo
	 */
	public function setNomCargo($nom_cargo){
		$this->nom_cargo = $nom_cargo;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_cargo
	 * @return string
	 */
	public function getNomCargo(){
		return $this->nom_cargo;
	}

}

