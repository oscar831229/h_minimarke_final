<?php

class CarteraEdades extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_tpc_id;

	/**
	 * @var integer
	 */
	protected $membresia;

	/**
	 * @var integer
	 */
	protected $dias;

	/**
	 * @var string
	 */
	protected $inicial;

	/**
	 * @var string
	 */
	protected $financiacion;

	/**
	 * @var string
	 */
	protected $interesm;

	/**
	 * @var string
	 */
	protected $interesc;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo socios_tpc_id
	 * @param integer $socios_tpc_id
	 */
	public function setSociosTpcId($socios_tpc_id){
		$this->socios_tpc_id = $socios_tpc_id;
	}

	/**
	 * Metodo para establecer el valor del campo membresia
	 * @param integer $membresia
	 */
	public function setMembresia($membresia){
		$this->membresia = $membresia;
	}

	/**
	 * Metodo para establecer el valor del campo dias
	 * @param integer $dias
	 */
	public function setDias($dias){
		$this->dias = $dias;
	}

	/**
	 * Metodo para establecer el valor del campo inicial
	 * @param string $inicial
	 */
	public function setInicial($inicial){
		$this->inicial = $inicial;
	}

	/**
	 * Metodo para establecer el valor del campo financiacion
	 * @param string $financiacion
	 */
	public function setFinanciacion($financiacion){
		$this->financiacion = $financiacion;
	}

	/**
	 * Metodo para establecer el valor del campo interesm
	 * @param string $interesm
	 */
	public function setInteresm($interesm){
		$this->interesm = $interesm;
	}

	/**
	 * Metodo para establecer el valor del campo interesc
	 * @param string $interesc
	 */
	public function setInteresc($interesc){
		$this->interesc = $interesc;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo socios_tpc_id
	 * @return integer
	 */
	public function getSociosTpcId(){
		return $this->socios_tpc_id;
	}

	/**
	 * Devuelve el valor del campo membresia
	 * @return integer
	 */
	public function getMembresia(){
		return $this->membresia;
	}

	/**
	 * Devuelve el valor del campo dias
	 * @return integer
	 */
	public function getDias(){
		return $this->dias;
	}

	/**
	 * Devuelve el valor del campo inicial
	 * @return string
	 */
	public function getInicial(){
		return $this->inicial;
	}

	/**
	 * Devuelve el valor del campo financiacion
	 * @return string
	 */
	public function getFinanciacion(){
		return $this->financiacion;
	}

	/**
	 * Devuelve el valor del campo interesm
	 * @return string
	 */
	public function getInteresm(){
		return $this->interesm;
	}

	/**
	 * Devuelve el valor del campo interesc
	 * @return string
	 */
	public function getInteresc(){
		return $this->interesc;
	}

}

