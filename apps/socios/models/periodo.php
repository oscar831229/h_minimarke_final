<?php

class Periodo extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $periodo;

	/**
	 * @var string
	 */
	protected $cierre;

	/**
	 * @var string
	 */
	protected $facturado;

	/**
	 * @var string
	 */
	protected $intereses_mora;

	/**
	 * @var integer
	 */
	protected $dia_factura;

	/**
	 * @var integer
	 */
	protected $dias_plazo;

	/**
	 * @var integer
	 */
	protected $consecutivos_id;


	/**
	 * Metodo para establecer el valor del campo periodo
	 * @param string $periodo
	 */
	public function setPeriodo($periodo){
		$this->periodo = $periodo;
	}

	/**
	 * Metodo para establecer el valor del campo cierre
	 * @param string $cierre
	 */
	public function setCierre($cierre){
		$this->cierre = $cierre;
	}

	/**
	 * Metodo para establecer el valor del campo facturado
	 * @param string $facturado
	 */
	public function setFacturado($facturado){
		$this->facturado = $facturado;
	}

	/**
	 * Metodo para establecer el valor del campo intereses_mora
	 * @param string $intereses_mora
	 */
	public function setInteresesMora($intereses_mora){
		$this->intereses_mora = $intereses_mora;
	}

	/**
	 * Metodo para establecer el valor del campo dia_factura
	 * @param integer $dia_factura
	 */
	public function setDiaFactura($dia_factura){
		$this->dia_factura = $dia_factura;
	}

	/**
	 * Metodo para establecer el valor del campo dias_plazo
	 * @param integer $dias_plazo
	 */
	public function setDiasPlazo($dias_plazo){
		$this->dias_plazo = $dias_plazo;
	}

	/**
	 * Metodo para establecer el valor del campo consecutivos_id
	 * @param integer $consecutivos_id
	 */
	public function setConsecutivosId($consecutivos_id){
		$this->consecutivos_id = $consecutivos_id;
	}


	/**
	 * Devuelve el valor del campo periodo
	 * @return string
	 */
	public function getPeriodo(){
		return $this->periodo;
	}

	/**
	 * Devuelve el valor del campo cierre
	 * @return string
	 */
	public function getCierre(){
		return $this->cierre;
	}

	/**
	 * Devuelve el valor del campo facturado
	 * @return string
	 */
	public function getFacturado(){
		return $this->facturado;
	}

	/**
	 * Devuelve el valor del campo intereses_mora
	 * @return string
	 */
	public function getInteresesMora(){
		return $this->intereses_mora;
	}

	/**
	 * Devuelve el valor del campo dia_factura
	 * @return integer
	 */
	public function getDiaFactura(){
		return $this->dia_factura;
	}

	/**
	 * Devuelve el valor del campo dias_plazo
	 * @return integer
	 */
	public function getDiasPlazo(){
		return $this->dias_plazo;
	}

	/**
	 * Devuelve el valor del campo consecutivos_id
	 * @return integer
	 */
	public function getConsecutivosId(){
		return $this->consecutivos_id;
	}

}

