<?php

class PermisosComprob extends ActiveRecord {

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
	protected $comprob;

	/**
	 * @var string
	 */
	protected $popcion;


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
	 * Metodo para establecer el valor del campo comprob
	 * @param string $comprob
	 */
	public function setComprob($comprob){
		$this->comprob = $comprob;
	}

	/**
	 * Metodo para establecer el valor del campo popcion
	 * @param string $popcion
	 */
	public function setPopcion($popcion){
		$this->popcion = $popcion;
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
	 * Devuelve el valor del campo comprob
	 * @return string
	 */
	public function getComprob(){
		return $this->comprob;
	}

	/**
	 * Devuelve el valor del campo popcion
	 * @return string
	 */
	public function getPopcion(){
		return $this->popcion;
	}

}

