<?php

class Delivery extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $relay_key;

	/**
	 * @var string
	 */
	protected $created_at;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo relay_key
	 * @param string $relay_key
	 */
	public function setRelayKey($relay_key){
		$this->relay_key = $relay_key;
	}

	/**
	 * Metodo para establecer el valor del campo created_at
	 * @param string $created_at
	 */
	public function setCreatedAt($created_at){
		$this->created_at = $created_at;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo relay_key
	 * @return string
	 */
	public function getRelayKey(){
		return $this->relay_key;
	}

	/**
	 * Devuelve el valor del campo created_at
	 * @return string
	 */
	public function getCreatedAt(){
		return $this->created_at;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	public function initialize(){
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->front_db)){
			$this->setSchema($config->hfos->front_db);
		} else {
			$this->setSchema('hotel5');
		}
	}
}

