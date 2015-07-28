<?php

class TempData extends ActiveRecord {

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
	protected $nit;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var integer
	 */
	protected $enginer_id;

	/**
	 * @var integer
	 */
	protected $user_code;

	/**
	 * @var string
	 */
	protected $hotel;


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
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo enginer_id
	 * @param integer $enginer_id
	 */
	public function setEnginerId($enginer_id){
		$this->enginer_id = $enginer_id;
	}

	/**
	 * Metodo para establecer el valor del campo user_code
	 * @param integer $user_code
	 */
	public function setUserCode($user_code){
		$this->user_code = $user_code;
	}

	/**
	 * Metodo para establecer el valor del campo hotel
	 * @param string $hotel
	 */
	public function setHotel($hotel){
		$this->hotel = $hotel;
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
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo enginer_id
	 * @return integer
	 */
	public function getEnginerId(){
		return $this->enginer_id;
	}

	/**
	 * Devuelve el valor del campo user_code
	 * @return integer
	 */
	public function getUserCode(){
		return $this->user_code;
	}

	/**
	 * Devuelve el valor del campo hotel
	 * @return string
	 */
	public function getHotel(){
		return $this->hotel;
	}

}

