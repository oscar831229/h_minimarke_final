<?php

class Permiso extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $controller;

	/**
	 * @var string
	 */
	protected $action;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo controller
	 * @param string $controller
	 */
	public function setController($controller){
		$this->controller = $controller;
	}

	/**
	 * Metodo para establecer el valor del campo action
	 * @param string $action
	 */
	public function setAction($action){
		$this->action = $action;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo controller
	 * @return string
	 */
	public function getController(){
		return $this->controller;
	}

	/**
	 * Devuelve el valor del campo action
	 * @return string
	 */
	public function getAction(){
		return $this->action;
	}

}

