<?php

class EstadoActivo extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_tpc_id;

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
	 * Metodo para establecer el valor del campo socios_tpc_id
	 * @param integer $socios_tpc_id
	 */
	public function setSociosTpcId($socios_tpc_id){
		$this->socios_tpc_id = $socios_tpc_id;
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
	 * Devuelve el valor del campo socios_tpc_id
	 * @return integer
	 */
	public function getSociosTpcId(){
		return $this->socios_tpc_id;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

}

