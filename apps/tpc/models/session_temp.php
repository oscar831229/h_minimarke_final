<?php

class SessionTemp extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $sid;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $value;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo sid
	 * @param string $sid
	 */
	public function setSid($sid){
		$this->sid = $sid;
	}

	/**
	 * Metodo para establecer el valor del campo name
	 * @param string $name
	 */
	public function setName($name){
		$this->name = $name;
	}

	/**
	 * Metodo para establecer el valor del campo value
	 * @param string $value
	 */
	public function setValue($value){
		$this->value = $value;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo sid
	 * @return string
	 */
	public function getSid(){
		return $this->sid;
	}

	/**
	 * Devuelve el valor del campo name
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Devuelve el valor del campo value
	 * @return string
	 */
	public function getValue(){
		return $this->value;
	}

}

