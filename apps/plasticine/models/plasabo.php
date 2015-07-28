<?php

class Plasabo extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $numres;

	/**
	 * @var integer
	 */
	protected $plasticine_id;

	/**
	 * @var string
	 */
	protected $token;

	/**
	 * @var string
	 */
	protected $valor;

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
	 * Metodo para establecer el valor del campo numres
	 * @param integer $numres
	 */
	public function setNumres($numres){
		$this->numres = $numres;
	}

	/**
	 * Metodo para establecer el valor del campo plasticine_id
	 * @param integer $plasticine_id
	 */
	public function setPlasticineId($plasticine_id){
		$this->plasticine_id = $plasticine_id;
	}

	/**
	 * Metodo para establecer el valor del campo token
	 * @param string $token
	 */
	public function setToken($token){
		$this->token = $token;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
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
	 * Devuelve el valor del campo numres
	 * @return integer
	 */
	public function getNumres(){
		return $this->numres;
	}

	/**
	 * Devuelve el valor del campo plasticine_id
	 * @return integer
	 */
	public function getPlasticineId(){
		return $this->plasticine_id;
	}

	/**
	 * Devuelve el valor del campo token
	 * @return string
	 */
	public function getToken(){
		return $this->token;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
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

}

