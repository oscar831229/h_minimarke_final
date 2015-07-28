<?php

class MovimientoCargos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_tpc_id;

	/**
	 * @var string
	 */
	protected $periodo;

	/**
	 * @var Date
	 */
	protected $fecha_at;

	/**
	 * @var integer
	 */
	protected $numero_factura;


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
	 * Metodo para establecer el valor del campo periodo
	 * @param string $periodo
	 */
	public function setPeriodo($periodo){
		$this->periodo = $periodo;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_at
	 * @param Date $fecha_at
	 */
	public function setFechaAt($fecha_at){
		$this->fecha_at = $fecha_at;
	}

	/**
	 * Metodo para establecer el valor del campo numero_factura
	 * @param integer $numero_factura
	 */
	public function setNumeroFactura($numero_factura){
		$this->numero_factura = $numero_factura;
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
	 * Devuelve el valor del campo periodo
	 * @return string
	 */
	public function getPeriodo(){
		return $this->periodo;
	}

	/**
	 * Devuelve el valor del campo fecha_at
	 * @return Date
	 */
	public function getFechaAt(){
		return new Date($this->fecha_at);
	}

	/**
	 * Devuelve el valor del campo numero_factura
	 * @return integer
	 */
	public function getNumeroFactura(){
		return $this->numero_factura;
	}

}

