<?php

class Banco extends ActiveRecord {

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
	protected $oficina;

	/**
	 * @var string
	 */
	protected $ciudad;


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
	 * Metodo para establecer el valor del campo oficina
	 * @param string $oficina
	 */
	public function setOficina($oficina){
		$this->oficina = $oficina;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad
	 * @param string $ciudad
	 */
	public function setCiudad($ciudad){
		$this->ciudad = $ciudad;
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
	 * Devuelve el valor del campo oficina
	 * @return string
	 */
	public function getOficina(){
		return $this->oficina;
	}

	/**
	 * Devuelve el valor del campo ciudad
	 * @return string
	 */
	public function getCiudad(){
		return $this->ciudad;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	public function initialize(){
	    $config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->back_db)){
			$this->setSchema($config->hfos->back_db);
		} else {
			$this->setSchema('ramocol');
		}
	}

}

