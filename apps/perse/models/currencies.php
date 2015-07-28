<?php

class Currencies extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $code;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $name_es;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo code
	 * @param string $code
	 */
	public function setCode($code){
		$this->code = $code;
	}

	/**
	 * Metodo para establecer el valor del campo name
	 * @param string $name
	 */
	public function setName($name){
		$this->name = $name;
	}

	/**
	 * Metodo para establecer el valor del campo name_es
	 * @param string $name_es
	 */
	public function setNameEs($name_es){
		$this->name_es = $name_es;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo code
	 * @return string
	 */
	public function getCode(){
		return $this->code;
	}

	/**
	 * Devuelve el valor del campo name
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Devuelve el valor del campo name_es
	 * @return string
	 */
	public function getNameEs(){
		return $this->name_es;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){		
		$this->setSchema("hfos_currency");
	}

}

