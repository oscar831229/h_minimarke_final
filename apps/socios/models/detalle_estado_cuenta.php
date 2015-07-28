<?php

class DetalleEstadoCuenta extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $documento;

	/**
	 * @var string
	 */
	protected $concepto;

	/**
	 * @var string
	 */
	protected $cargos;

	/**
	 * @var string
	 */
	protected $abonos;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo documento
	 * @param string $documento
	 */
	public function setDocumento($documento){
		$this->documento = $documento;
	}

	/**
	 * Metodo para establecer el valor del campo concepto
	 * @param string $concepto
	 */
	public function setConcepto($concepto){
		$this->concepto = $concepto;
	}

	/**
	 * Metodo para establecer el valor del campo cargos
	 * @param string $cargos
	 */
	public function setCargos($cargos){
		$this->cargos = $cargos;
	}

	/**
	 * Metodo para establecer el valor del campo abonos
	 * @param string $abonos
	 */
	public function setAbonos($abonos){
		$this->abonos = $abonos;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		if($this->fecha){
			return new Date($this->fecha);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo documento
	 * @return string
	 */
	public function getDocumento(){
		return $this->documento;
	}

	/**
	 * Devuelve el valor del campo concepto
	 * @return string
	 */
	public function getConcepto(){
		return $this->concepto;
	}

	/**
	 * Devuelve el valor del campo cargos
	 * @return string
	 */
	public function getCargos(){
		return $this->cargos;
	}

	/**
	 * Devuelve el valor del campo abonos
	 * @return string
	 */
	public function getAbonos(){
		return $this->abonos;
	}

}

