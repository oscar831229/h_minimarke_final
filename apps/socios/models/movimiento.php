<?php

class Movimiento extends ActiveRecord {

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
	protected $factura_id;

	/**
	 * @var string
	 */
	protected $periodo;

	/**
	 * @var Date
	 */
	protected $fecha_at;

	/**
	 * @var string
	 */
	protected $saldo_anterior;

	/**
	 * @var string
	 */
	protected $mora;

	/**
	 * @var string
	 */
	protected $cargos_mes;

	/**
	 * @var string
	 */
	protected $saldo_actual;

	/**
	 * @var string
	 */
	protected $iva_mora;


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
	 * Metodo para establecer el valor del campo factura_id
	 * @param integer $factura_id
	 */
	public function setFacturaId($factura_id){
		$this->factura_id = $factura_id;
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
	 * Metodo para establecer el valor del campo saldo_anterior
	 * @param string $saldo_anterior
	 */
	public function setSaldoAnterior($saldo_anterior){
		$this->saldo_anterior = $saldo_anterior;
	}

	/**
	 * Metodo para establecer el valor del campo mora
	 * @param string $mora
	 */
	public function setMora($mora){
		$this->mora = $mora;
	}

	/**
	 * Metodo para establecer el valor del campo cargos_mes
	 * @param string $cargos_mes
	 */
	public function setCargosMes($cargos_mes){
		$this->cargos_mes = $cargos_mes;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_actual
	 * @param string $saldo_actual
	 */
	public function setSaldoActual($saldo_actual){
		$this->saldo_actual = $saldo_actual;
	}

	/**
	 * Metodo para establecer el valor del campo iva_mora
	 * @param string $iva_mora
	 */
	public function setIvaMora($iva_mora){
		$this->iva_mora = $iva_mora;
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
	 * Devuelve el valor del campo factura_id
	 * @return integer
	 */
	public function getFacturaId(){
		return $this->factura_id;
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
		if($this->fecha_at){
			return new Date($this->fecha_at);
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
	 * Devuelve el valor del campo mora
	 * @return string
	 */
	public function getMora(){
		return $this->mora;
	}

	/**
	 * Devuelve el valor del campo cargos_mes
	 * @return string
	 */
	public function getCargosMes(){
		return $this->cargos_mes;
	}

	/**
	 * Devuelve el valor del campo saldo_actual
	 * @return string
	 */
	public function getSaldoActual(){
		return $this->saldo_actual;
	}

	/**
	 * Devuelve el valor del campo iva_mora
	 * @return string
	 */
	public function getIvaMora(){
		return $this->iva_mora;
	}

	public function initialize(){
		$this->addForeignKey('socios_id', 'Socios', 'socios_id', array(
			'message' => 'El socio no es valido'
		));
		
		$this->hasOne('socios_id', 'Socios', 'socios_id');
		$this->belongsTo('id', 'Factura', 'movimiento_id');
		$this->belongsTo('id', 'DetalleMovimiento', 'movimiento_id');
	}

}

