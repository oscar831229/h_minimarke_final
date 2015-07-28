<?php

class UserSession extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $usuarios_id;

	/**
	 * @var string
	 */
	protected $app_code;

	/**
	 * @var string
	 */
	protected $token;

	/**
	 * @var string
	 */
	protected $state;

	/**
	 * @var integer
	 */
	protected $ping_time;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo usuarios_id
	 * @param integer $usuarios_id
	 */
	public function setUsuariosId($usuarios_id){
		$this->usuarios_id = $usuarios_id;
	}

	/**
	 * Metodo para establecer el valor del campo app_code
	 * @param string $app_code
	 */
	public function setAppCode($app_code){
		$this->app_code = $app_code;
	}

	/**
	 * Metodo para establecer el valor del campo token
	 * @param string $token
	 */
	public function setToken($token){
		$this->token = $token;
	}

	/**
	 * Metodo para establecer el valor del campo state
	 * @param string $state
	 */
	public function setState($state){
		$this->state = $state;
	}

	/**
	 * Metodo para establecer el valor del campo ping_time
	 * @param integer $ping_time
	 */
	public function setPingTime($ping_time){
		$this->ping_time = $ping_time;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo usuarios_id
	 * @return integer
	 */
	public function getUsuariosId(){
		return $this->usuarios_id;
	}

	/**
	 * Devuelve el valor del campo app_code
	 * @return string
	 */
	public function getAppCode(){
		return $this->app_code;
	}

	/**
	 * Devuelve el valor del campo token
	 * @return string
	 */
	public function getToken(){
		return $this->token;
	}

	/**
	 * Devuelve el valor del campo state
	 * @return string
	 */
	public function getState(){
		return $this->state;
	}

	/**
	 * Devuelve el valor del campo ping_time
	 * @return integer
	 */
	public function getPingTime(){
		return $this->ping_time;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){		
		$this->setSchema("hfos_workspace");
	}

}

