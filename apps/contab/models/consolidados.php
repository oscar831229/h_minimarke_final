<?php

class Consolidados extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $server;

	/**
	 * @var string
	 */
	protected $uri;

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
	 * Metodo para establecer el valor del campo server
	 * @param string $server
	 */
	public function setServer($server){
		$this->server = $server;
	}

	/**
	 * Metodo para establecer el valor del campo uri
	 * @param string $uri
	 */
	public function setUri($uri){
		$this->uri = $uri;
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
	 * Devuelve el valor del campo server
	 * @return string
	 */
	public function getServer(){
		return $this->server;
	}

	/**
	 * Devuelve el valor del campo uri
	 * @return string
	 */
	public function getUri(){
		return $this->uri;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

