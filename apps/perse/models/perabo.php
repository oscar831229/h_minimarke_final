<?php

class Perabo extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $numfol;

	/**
	 * @var string
	 */
	protected $cedula;

	/**
	 * @var string
	 */
	protected $token;

	/**
	 * @var string
	 */
	protected $ipaddress;

	/**
	 * @var string
	 */
	protected $created_at;

	/**
	 * @var string
	 */
	protected $modified_in;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo numfol
	 * @param integer $numfol
	 */
	public function setNumfol($numfol){
		$this->numfol = $numfol;
	}

	/**
	 * Metodo para establecer el valor del campo cedula
	 * @param string $cedula
	 */
	public function setCedula($cedula){
		$this->cedula = $cedula;
	}

	/**
	 * Metodo para establecer el valor del campo token
	 * @param string $token
	 */
	public function setToken($token){
		$this->token = $token;
	}

	/**
	 * Metodo para establecer el valor del campo ipaddress
	 * @param string $ipaddress
	 */
	public function setIpaddress($ipaddress){
		$this->ipaddress = $ipaddress;
	}

	/**
	 * Metodo para establecer el valor del campo created_at
	 * @param string $created_at
	 */
	public function setCreatedAt($created_at){
		$this->created_at = $created_at;
	}

	/**
	 * Metodo para establecer el valor del campo modified_in
	 * @param string $modified_in
	 */
	public function setModifiedIn($modified_in){
		$this->modified_in = $modified_in;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo numfol
	 * @return integer
	 */
	public function getNumfol(){
		return $this->numfol;
	}

	/**
	 * Devuelve el valor del campo cedula
	 * @return string
	 */
	public function getCedula(){
		return $this->cedula;
	}

	/**
	 * Devuelve el valor del campo token
	 * @return string
	 */
	public function getToken(){
		return $this->token;
	}

	/**
	 * Devuelve el valor del campo ipaddress
	 * @return string
	 */
	public function getIpaddress(){
		return $this->ipaddress;
	}

	/**
	 * Devuelve el valor del campo created_at
	 * @return string
	 */
	public function getCreatedAt(){
		return $this->created_at;
	}

	/**
	 * Devuelve el valor del campo modified_in
	 * @return string
	 */
	public function getModifiedIn(){
		return $this->modified_in;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function initialize(){
		$this->hasMany('Perdet');
	}

}

