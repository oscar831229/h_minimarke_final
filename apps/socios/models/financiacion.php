<?php

class Financiacion extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $factura_id;

	/**
	 * @var string
	 */
	protected $ultimo_abono;

	/**
	 * @var Date
	 */
	protected $fecha_ultimo;

	/**
	 * @var string
	 */
	protected $saldo_anterior;

	/**
	 * @var string
	 */
	protected $anterior_interes;

	/**
	 * @var string
	 */
	protected $cuota;

	/**
	 * @var string
	 */
	protected $saldo_actual;

	/**
	 * @var integer
	 */
	protected $nit;

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
	 * Metodo para establecer el valor del campo factura_id
	 * @param integer $factura_id
	 */
	public function setFacturaId($factura_id){
		$this->factura_id = $factura_id;
	}

	/**
	 * Metodo para establecer el valor del campo ultimo_abono
	 * @param string $ultimo_abono
	 */
	public function setUltimoAbono($ultimo_abono){
		$this->ultimo_abono = $ultimo_abono;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_ultimo
	 * @param Date $fecha_ultimo
	 */
	public function setFechaUltimo($fecha_ultimo){
		$this->fecha_ultimo = $fecha_ultimo;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_anterior
	 * @param string $saldo_anterior
	 */
	public function setSaldoAnterior($saldo_anterior){
		$this->saldo_anterior = $saldo_anterior;
	}

	/**
	 * Metodo para establecer el valor del campo anterior_interes
	 * @param string $anterior_interes
	 */
	public function setAnteriorInteres($anterior_interes){
		$this->anterior_interes = $anterior_interes;
	}

	/**
	 * Metodo para establecer el valor del campo cuota
	 * @param string $cuota
	 */
	public function setCuota($cuota){
		$this->cuota = $cuota;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_actual
	 * @param string $saldo_actual
	 */
	public function setSaldoActual($saldo_actual){
		$this->saldo_actual = $saldo_actual;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param integer $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
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
	 * Devuelve el valor del campo factura_id
	 * @return integer
	 */
	public function getFacturaId(){
		return $this->factura_id;
	}

	/**
	 * Devuelve el valor del campo ultimo_abono
	 * @return string
	 */
	public function getUltimoAbono(){
		return $this->ultimo_abono;
	}

	/**
	 * Devuelve el valor del campo fecha_ultimo
	 * @return Date
	 */
	public function getFechaUltimo(){
		if($this->fecha_ultimo){
			return new Date($this->fecha_ultimo);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo saldo_anterior
	 * @return string
	 */
	public function getSaldoAnterior(){
		return $this->saldo_anterior;
	}

	/**
	 * Devuelve el valor del campo anterior_interes
	 * @return string
	 */
	public function getAnteriorInteres(){
		return $this->anterior_interes;
	}

	/**
	 * Devuelve el valor del campo cuota
	 * @return string
	 */
	public function getCuota(){
		return $this->cuota;
	}

	/**
	 * Devuelve el valor del campo saldo_actual
	 * @return string
	 */
	public function getSaldoActual(){
		return $this->saldo_actual;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return integer
	 */
	public function getNit(){
		return $this->nit;
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

}

