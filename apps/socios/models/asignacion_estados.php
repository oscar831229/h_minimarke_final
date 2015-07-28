<?php

class AsignacionEstados extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $estados_socios_id;

	/**
	 * @var Date
	 */
	protected $fecha_ini;

	/**
	 * @var Date
	 */
	protected $fecha_fin;

	/**
	 * @var string
	 */
	protected $observaciones;


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
	 * Metodo para establecer el valor del campo estados_socios_id
	 * @param integer $estados_socios_id
	 */
	public function setEstadosSociosId($estados_socios_id){
		$this->estados_socios_id = $estados_socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_ini
	 * @param Date $fecha_ini
	 */
	public function setFechaIni($fecha_ini){
		$this->fecha_ini = $fecha_ini;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_fin
	 * @param Date $fecha_fin
	 */
	public function setFechaFin($fecha_fin){
		$this->fecha_fin = $fecha_fin;
	}

	/**
	 * Metodo para establecer el valor del campo observaciones
	 * @param string $observaciones
	 */
	public function setObservaciones($observaciones){
		$this->observaciones = $observaciones;
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
	 * Devuelve el valor del campo estados_socios_id
	 * @return integer
	 */
	public function getEstadosSociosId(){
		return $this->estados_socios_id;
	}

	/**
	 * Devuelve el valor del campo fecha_ini
	 * @return Date
	 */
	public function getFechaIni(){
		if($this->fecha_ini){
			return new Date($this->fecha_ini);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecha_fin
	 * @return Date
	 */
	public function getFechaFin(){
		if($this->fecha_fin){
			return new Date($this->fecha_fin);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo observaciones
	 * @return string
	 */
	public function getObservaciones(){
		return $this->observaciones;
	}

	public function initialize(){
	    //JOIN de Estados de socios
	    $this->hasOne('estados_socios_id','EstadosSocios','id');
	    $this->hasOne('socios_id','Socios','socios_id');
	}

}

