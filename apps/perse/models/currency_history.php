<?php

class CurrencyHistory extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $currencies_id;

	/**
	 * @var Date
	 */
	protected $query_date;

	/**
	 * @var integer
	 */
	protected $hour;

	/**
	 * @var string
	 */
	protected $valor;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo currencies_id
	 * @param integer $currencies_id
	 */
	public function setCurrenciesId($currencies_id){
		$this->currencies_id = $currencies_id;
	}

	/**
	 * Metodo para establecer el valor del campo query_date
	 * @param Date $query_date
	 */
	public function setQueryDate($query_date){
		$this->query_date = $query_date;
	}

	/**
	 * Metodo para establecer el valor del campo hour
	 * @param integer $hour
	 */
	public function setHour($hour){
		$this->hour = $hour;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo currencies_id
	 * @return integer
	 */
	public function getCurrenciesId(){
		return $this->currencies_id;
	}

	/**
	 * Devuelve el valor del campo query_date
	 * @return Date
	 */
	public function getQueryDate(){
		return new Date($this->query_date);
	}

	/**
	 * Devuelve el valor del campo hour
	 * @return integer
	 */
	public function getHour(){
		return $this->hour;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){		
		$this->setSchema("hfos_currency");
	}

}

