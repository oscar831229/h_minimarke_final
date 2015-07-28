<?php

class Consecutivo extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $prefijo;

	/**
	 * @var integer
	 */
	protected $numero;


	/**
	 * Metodo para establecer el valor del campo prefijo
	 * @param string $prefijo
	 */
	public function setPrefijo($prefijo){
		$this->prefijo = $prefijo;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}


	/**
	 * Devuelve el valor del campo prefijo
	 * @return string
	 */
	public function getPrefijo(){
		return $this->prefijo;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

}

