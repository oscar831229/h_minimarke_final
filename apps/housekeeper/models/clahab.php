<?php

class Clahab extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codcla;

	/**
	 * @var string
	 */
	protected $clase;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $observacion;

	/**
	 * @var string
	 */
	protected $uri;

	/**
	 * @var integer
	 */
	protected $numper;

	/**
	 * @var integer
	 */
	protected $tiphab;


	/**
	 * Metodo para establecer el valor del campo codcla
	 * @param integer $codcla
	 */
	public function setCodcla($codcla){
		$this->codcla = $codcla;
	}

	/**
	 * Metodo para establecer el valor del campo clase
	 * @param string $clase
	 */
	public function setClase($clase){
		$this->clase = $clase;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo observacion
	 * @param string $observacion
	 */
	public function setObservacion($observacion){
		$this->observacion = $observacion;
	}

	/**
	 * Metodo para establecer el valor del campo uri
	 * @param string $uri
	 */
	public function setUri($uri){
		$this->uri = $uri;
	}

	/**
	 * Metodo para establecer el valor del campo numper
	 * @param integer $numper
	 */
	public function setNumper($numper){
		$this->numper = $numper;
	}

	/**
	 * Metodo para establecer el valor del campo tiphab
	 * @param integer $tiphab
	 */
	public function setTiphab($tiphab){
		$this->tiphab = $tiphab;
	}


	/**
	 * Devuelve el valor del campo codcla
	 * @return integer
	 */
	public function getCodcla(){
		return $this->codcla;
	}

	/**
	 * Devuelve el valor del campo clase
	 * @return string
	 */
	public function getClase(){
		return $this->clase;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo observacion
	 * @return string
	 */
	public function getObservacion(){
		return $this->observacion;
	}

	/**
	 * Devuelve el valor del campo uri
	 * @return string
	 */
	public function getUri(){
		return $this->uri;
	}

	/**
	 * Devuelve el valor del campo numper
	 * @return integer
	 */
	public function getNumper(){
		return $this->numper;
	}

	/**
	 * Devuelve el valor del campo tiphab
	 * @return integer
	 */
	public function getTiphab(){
		return $this->tiphab;
	}

}

