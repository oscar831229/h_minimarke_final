<?php

class CategoriaEdad extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var integer
	 */
	protected $tipo_socios_id;

	/**
	 * @var integer
	 */
	protected $estados_socios_id;

	/**
	 * @var integer
	 */
	protected $edad_ini;

	/**
	 * @var integer
	 */
	protected $edad_fin;

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
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_socios_id
	 * @param integer $tipo_socios_id
	 */
	public function setTipoSociosId($tipo_socios_id){
		$this->tipo_socios_id = $tipo_socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo estados_socios_id
	 * @param integer $estados_socios_id
	 */
	public function setEstadosSociosId($estados_socios_id){
		$this->estados_socios_id = $estados_socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo edad_ini
	 * @param integer $edad_ini
	 */
	public function setEdadIni($edad_ini){
		$this->edad_ini = $edad_ini;
	}

	/**
	 * Metodo para establecer el valor del campo edad_fin
	 * @param integer $edad_fin
	 */
	public function setEdadFin($edad_fin){
		$this->edad_fin = $edad_fin;
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
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo tipo_socios_id
	 * @return integer
	 */
	public function getTipoSociosId(){
		return $this->tipo_socios_id;
	}

	/**
	 * Devuelve el valor del campo estados_socios_id
	 * @return integer
	 */
	public function getEstadosSociosId(){
		return $this->estados_socios_id;
	}

	/**
	 * Devuelve el valor del campo edad_ini
	 * @return integer
	 */
	public function getEdadIni(){
		return $this->edad_ini;
	}

	/**
	 * Devuelve el valor del campo edad_fin
	 * @return integer
	 */
	public function getEdadFin(){
		return $this->edad_fin;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

