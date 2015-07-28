<?php

class EstudiosPaya extends ActiveRecord {

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
	protected $colegio;

	/**
	 * @var integer
	 */
	protected $pais_colegio;

	/**
	 * @var integer
	 */
	protected $ciudad_colegio;

	/**
	 * @var Date
	 */
	protected $fecha_grado1;

	/**
	 * @var string
	 */
	protected $titulo1;

	/**
	 * @var string
	 */
	protected $universidad;

	/**
	 * @var integer
	 */
	protected $pais_universidad;

	/**
	 * @var integer
	 */
	protected $ciudad_universidad;

	/**
	 * @var Date
	 */
	protected $fecha_grado2;

	/**
	 * @var string
	 */
	protected $titulo2;

	/**
	 * @var string
	 */
	protected $otros;


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
	 * Metodo para establecer el valor del campo colegio
	 * @param string $colegio
	 */
	public function setColegio($colegio){
		$this->colegio = $colegio;
	}

	/**
	 * Metodo para establecer el valor del campo pais_colegio
	 * @param integer $pais_colegio
	 */
	public function setPaisColegio($pais_colegio){
		$this->pais_colegio = $pais_colegio;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad_colegio
	 * @param integer $ciudad_colegio
	 */
	public function setCiudadColegio($ciudad_colegio){
		$this->ciudad_colegio = $ciudad_colegio;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_grado1
	 * @param Date $fecha_grado1
	 */
	public function setFechaGrado1($fecha_grado1){
		$this->fecha_grado1 = $fecha_grado1;
	}

	/**
	 * Metodo para establecer el valor del campo titulo1
	 * @param string $titulo1
	 */
	public function setTitulo1($titulo1){
		$this->titulo1 = $titulo1;
	}

	/**
	 * Metodo para establecer el valor del campo universidad
	 * @param string $universidad
	 */
	public function setUniversidad($universidad){
		$this->universidad = $universidad;
	}

	/**
	 * Metodo para establecer el valor del campo pais_universidad
	 * @param integer $pais_universidad
	 */
	public function setPaisUniversidad($pais_universidad){
		$this->pais_universidad = $pais_universidad;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad_universidad
	 * @param integer $ciudad_universidad
	 */
	public function setCiudadUniversidad($ciudad_universidad){
		$this->ciudad_universidad = $ciudad_universidad;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_grado2
	 * @param Date $fecha_grado2
	 */
	public function setFechaGrado2($fecha_grado2){
		$this->fecha_grado2 = $fecha_grado2;
	}

	/**
	 * Metodo para establecer el valor del campo titulo2
	 * @param string $titulo2
	 */
	public function setTitulo2($titulo2){
		$this->titulo2 = $titulo2;
	}

	/**
	 * Metodo para establecer el valor del campo otros
	 * @param string $otros
	 */
	public function setOtros($otros){
		$this->otros = $otros;
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
	 * Devuelve el valor del campo colegio
	 * @return string
	 */
	public function getColegio(){
		return $this->colegio;
	}

	/**
	 * Devuelve el valor del campo pais_colegio
	 * @return integer
	 */
	public function getPaisColegio(){
		return $this->pais_colegio;
	}

	/**
	 * Devuelve el valor del campo ciudad_colegio
	 * @return integer
	 */
	public function getCiudadColegio(){
		return $this->ciudad_colegio;
	}

	/**
	 * Devuelve el valor del campo fecha_grado1
	 * @return Date
	 */
	public function getFechaGrado1(){
		return $this->fecha_grado1;
	}

	/**
	 * Devuelve el valor del campo titulo1
	 * @return string
	 */
	public function getTitulo1(){
		return $this->titulo1;
	}

	/**
	 * Devuelve el valor del campo universidad
	 * @return string
	 */
	public function getUniversidad(){
		return $this->universidad;
	}

	/**
	 * Devuelve el valor del campo pais_universidad
	 * @return integer
	 */
	public function getPaisUniversidad(){
		return $this->pais_universidad;
	}

	/**
	 * Devuelve el valor del campo ciudad_universidad
	 * @return integer
	 */
	public function getCiudadUniversidad(){
		return $this->ciudad_universidad;
	}

	/**
	 * Devuelve el valor del campo fecha_grado2
	 * @return Date
	 */
	public function getFechaGrado2(){
		return $this->fecha_grado2;
	}

	/**
	 * Devuelve el valor del campo titulo2
	 * @return string
	 */
	public function getTitulo2(){
		return $this->titulo2;
	}

	/**
	 * Devuelve el valor del campo otros
	 * @return string
	 */
	public function getOtros(){
		return $this->otros;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){		
		$this->setSource('estudios');
		$this->setSchema('payande');
	}

}

