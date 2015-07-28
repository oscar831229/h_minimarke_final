<?php

class CambioContrato extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_tpc_old;

	/**
	 * @var integer
	 */
	protected $socios_tpc_new;

	/**
	 * @var Date
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
	 * Metodo para establecer el valor del campo socios_tpc_old
	 * @param integer $socios_tpc_old
	 */
	public function setSociosTpcOld($socios_tpc_old){
		$this->socios_tpc_old = $socios_tpc_old;
	}

	/**
	 * Metodo para establecer el valor del campo socios_tpc_new
	 * @param integer $socios_tpc_new
	 */
	public function setSociosTpcNew($socios_tpc_new){
		$this->socios_tpc_new = $socios_tpc_new;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
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
	 * Devuelve el valor del campo socios_tpc_old
	 * @return integer
	 */
	public function getSociosTpcOld(){
		return $this->socios_tpc_old;
	}

	/**
	 * Devuelve el valor del campo socios_tpc_new
	 * @return integer
	 */
	public function getSociosTpcNew(){
		return $this->socios_tpc_new;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

}

