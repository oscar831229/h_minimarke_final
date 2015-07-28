<?php

class Ciudades extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $paises_id;

	/**
	 * @var string
	 */
	protected $nombre;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo paises_id
	 * @param integer $paises_id
	 */
	public function setPaisesId($paises_id){
		$this->paises_id = $paises_id;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo paises_id
	 * @return integer
	 */
	public function getPaisesId(){
		return $this->paises_id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	public function initialize(){
		$this->setSchema('sociostpc');
	}

}

