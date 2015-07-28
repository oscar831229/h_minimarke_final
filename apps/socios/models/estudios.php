<?php

class Estudios extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var string
	 */
	protected $institucion;

	/**
	 * @var integer
	 */
	protected $ciudad;

	/**
	 * @var Date
	 */
	protected $fecha_grado;

	/**
	 * @var string
	 */
	protected $titulo;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo institucion
	 * @param string $institucion
	 */
	public function setInstitucion($institucion){
		$this->institucion = $institucion;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad
	 * @param integer $ciudad
	 */
	public function setCiudad($ciudad){
		$this->ciudad = $ciudad;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_grado
	 * @param Date $fecha_grado
	 */
	public function setFechaGrado($fecha_grado){
		$this->fecha_grado = $fecha_grado;
	}

	/**
	 * Metodo para establecer el valor del campo titulo
	 * @param string $titulo
	 */
	public function setTitulo($titulo){
		$this->titulo = $titulo;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo institucion
	 * @return string
	 */
	public function getInstitucion(){
		return $this->institucion;
	}

	/**
	 * Devuelve el valor del campo ciudad
	 * @return integer
	 */
	public function getCiudad(){
		return $this->ciudad;
	}

	/**
	 * Devuelve el valor del campo fecha_grado
	 * @return Date
	 */
	public function getFechaGrado(){
		if($this->fecha_grado){
			return new Date($this->fecha_grado);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo titulo
	 * @return string
	 */
	public function getTitulo(){
		return $this->titulo;
	}

	public function initialize(){
		$this->belongsTo('socios_id','Socios', 'socios_id');
	}
}

