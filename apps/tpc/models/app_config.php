<?php

class AppConfig extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $numero_mes;

	/**
	 * @var integer
	 */
	protected $numero_ano;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo numero_mes
	 * @param integer $numero_mes
	 */
	public function setNumeroMes($numero_mes){
		$this->numero_mes = $numero_mes;
	}

	/**
	 * Metodo para establecer el valor del campo numero_ano
	 * @param integer $numero_ano
	 */
	public function setNumeroAno($numero_ano){
		$this->numero_ano = $numero_ano;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo numero_mes
	 * @return integer
	 */
	public function getNumeroMes(){
		return $this->numero_mes;
	}

	/**
	 * Devuelve el valor del campo numero_ano
	 * @return integer
	 */
	public function getNumeroAno(){
		return $this->numero_ano;
	}

}

