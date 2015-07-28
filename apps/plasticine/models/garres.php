<?php

class Garres extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numres;

	/**
	 * @var integer
	 */
	protected $item;

	/**
	 * @var integer
	 */
	protected $codusu;

	/**
	 * @var integer
	 */
	protected $codcaj;

	/**
	 * @var string
	 */
	protected $fecha;

	/**
	 * @var integer
	 */
	protected $codcar;

	/**
	 * @var string
	 */
	protected $total;

	/**
	 * @var integer
	 */
	protected $numrec;

	/**
	 * @var integer
	 */
	protected $numegr;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo numres
	 * @param integer $numres
	 */
	public function setNumres($numres){
		$this->numres = $numres;
	}

	/**
	 * Metodo para establecer el valor del campo item
	 * @param integer $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
	}

	/**
	 * Metodo para establecer el valor del campo codcaj
	 * @param integer $codcaj
	 */
	public function setCodcaj($codcaj){
		$this->codcaj = $codcaj;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param string $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo codcar
	 * @param integer $codcar
	 */
	public function setCodcar($codcar){
		$this->codcar = $codcar;
	}

	/**
	 * Metodo para establecer el valor del campo total
	 * @param string $total
	 */
	public function setTotal($total){
		$this->total = $total;
	}

	/**
	 * Metodo para establecer el valor del campo numrec
	 * @param integer $numrec
	 */
	public function setNumrec($numrec){
		$this->numrec = $numrec;
	}

	/**
	 * Metodo para establecer el valor del campo numegr
	 * @param integer $numegr
	 */
	public function setNumegr($numegr){
		$this->numegr = $numegr;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo numres
	 * @return integer
	 */
	public function getNumres(){
		return $this->numres;
	}

	/**
	 * Devuelve el valor del campo item
	 * @return integer
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
	}

	/**
	 * Devuelve el valor del campo codcaj
	 * @return integer
	 */
	public function getCodcaj(){
		return $this->codcaj;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return string
	 */
	public function getFecha(){
		return $this->fecha;
	}

	/**
	 * Devuelve el valor del campo codcar
	 * @return integer
	 */
	public function getCodcar(){
		return $this->codcar;
	}

	/**
	 * Devuelve el valor del campo total
	 * @return string
	 */
	public function getTotal(){
		return $this->total;
	}

	/**
	 * Devuelve el valor del campo numrec
	 * @return integer
	 */
	public function getNumrec(){
		return $this->numrec;
	}

	/**
	 * Devuelve el valor del campo numegr
	 * @return integer
	 */
	public function getNumegr(){
		return $this->numegr;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

