<?php

class PageChecks extends ActiveRecord {

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
	protected $name;

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
	 * Metodo para establecer el valor del campo usuarios_id
	 * @param integer $usuarios_id
	 */
	public function setUsuariosId($usuarios_id){
		$this->usuarios_id = $usuarios_id;
	}

	/**
	 * Metodo para establecer el valor del campo name
	 * @param string $name
	 */
	public function setName($name){
		$this->name = $name;
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
	 * Devuelve el valor del campo usuarios_id
	 * @return integer
	 */
	public function getUsuariosId(){
		return $this->usuarios_id;
	}

	/**
	 * Devuelve el valor del campo name
	 * @return string
	 */
	public function getName(){
		return $this->name;
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
	protected function initialize(){		
		$this->setSchema("hfos_workspace");
	}

}

