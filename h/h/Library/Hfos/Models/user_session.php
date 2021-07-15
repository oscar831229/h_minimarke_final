<?php

class UserSession extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $app_name;

	/**
	 * @var string
	 */
	protected $state;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo app_name
	 * @param string $app_name
	 */
	public function setAppName($app_name){
		$this->app_name = $app_name;
	}

	/**
	 * Metodo para establecer el valor del campo state
	 * @param string $state
	 */
	public function setState($state){
		$this->state = $state;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo app_name
	 * @return string
	 */
	public function getAppName(){
		return $this->app_name;
	}

	/**
	 * Devuelve el valor del campo state
	 * @return string
	 */
	public function getState(){
		return $this->state;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){		
		$this->setSchema("hfos_workspace");
	}

}

