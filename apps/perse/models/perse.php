<?php

class Perse extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $numfol;

	/**
	 * @var string
	 */
	protected $clave;

	/**
	 * @var string
	 */
	protected $enaper;

	/**
	 * @var string
	 */
	protected $pueout;

	/**
	 * @var string
	 */
	protected $pueabo;

	/**
	 * @var integer
	 */
	protected $codusu;

	/**
	 * @var string
	 */
	protected $fecha;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo numfol
	 * @param integer $numfol
	 */
	public function setNumfol($numfol){
		$this->numfol = $numfol;
	}

	/**
	 * Metodo para establecer el valor del campo clave
	 * @param string $clave
	 */
	public function setClave($clave){
		$this->clave = $clave;
	}

	/**
	 * Metodo para establecer el valor del campo enaper
	 * @param string $enaper
	 */
	public function setEnaper($enaper){
		$this->enaper = $enaper;
	}

	/**
	 * Metodo para establecer el valor del campo pueout
	 * @param string $pueout
	 */
	public function setPueout($pueout){
		$this->pueout = $pueout;
	}

	/**
	 * Metodo para establecer el valor del campo pueabo
	 * @param string $pueabo
	 */
	public function setPueabo($pueabo){
		$this->pueabo = $pueabo;
	}

	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param string $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo numfol
	 * @return integer
	 */
	public function getNumfol(){
		return $this->numfol;
	}

	/**
	 * Devuelve el valor del campo clave
	 * @return string
	 */
	public function getClave(){
		return $this->clave;
	}

	/**
	 * Devuelve el valor del campo enaper
	 * @return string
	 */
	public function getEnaper(){
		return $this->enaper;
	}

	/**
	 * Devuelve el valor del campo pueout
	 * @return string
	 */
	public function getPueout(){
		return $this->pueout;
	}

	/**
	 * Devuelve el valor del campo pueabo
	 * @return string
	 */
	public function getPueabo(){
		return $this->pueabo;
	}

	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return string
	 */
	public function getFecha(){
		return $this->fecha;
	}

}

