<?php

class CuentaCobro extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var string
	 */
	protected $numero_contrato;

	/**
	 * @var string
	 */
	protected $periodo;

	/**
	 * @var Date
	 */
	protected $fecha_corte;

	/**
	 * @var string
	 */
	protected $fecha_limite_pago;

	/**
	 * @var string
	 */
	protected $valor_derecho_afiliacion;

	/**
	 * @var string
	 */
	protected $valor_cuota_inicial;

	/**
	 * @var string
	 */
	protected $valor_cuota_financiacion;

	/**
	 * @var string
	 */
	protected $saldo_derecho_afiliacion;

	/**
	 * @var string
	 */
	protected $saldo_cuota_inicial;

	/**
	 * @var string
	 */
	protected $saldo_cuota_financiacion;

	/**
	 * @var string
	 */
	protected $base_corriente;

	/**
	 * @var string
	 */
	protected $base_mora;

	/**
	 * @var integer
	 */
	protected $dias_corriente;

	/**
	 * @var string
	 */
	protected $valor_corriente;

	/**
	 * @var integer
	 */
	protected $dias_mora;

	/**
	 * @var string
	 */
	protected $valor_mora;

	/**
	 * @var string
	 */
	protected $valor_capital;

	/**
	 * @var string
	 */
	protected $pago_minimo;

	/**
	 * @var string
	 */
	protected $pago_total;

	/**
	 * @var integer
	 */
	protected $consecutivo;


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
	 * Metodo para establecer el valor del campo numero_contrato
	 * @param string $numero_contrato
	 */
	public function setNumeroContrato($numero_contrato){
		$this->numero_contrato = $numero_contrato;
	}

	/**
	 * Metodo para establecer el valor del campo periodo
	 * @param string $periodo
	 */
	public function setPeriodo($periodo){
		$this->periodo = $periodo;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_corte
	 * @param Date $fecha_corte
	 */
	public function setFechaCorte($fecha_corte){
		$this->fecha_corte = $fecha_corte;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_limite_pago
	 * @param string $fecha_limite_pago
	 */
	public function setFechaLimitePago($fecha_limite_pago){
		$this->fecha_limite_pago = $fecha_limite_pago;
	}

	/**
	 * Metodo para establecer el valor del campo valor_derecho_afiliacion
	 * @param string $valor_derecho_afiliacion
	 */
	public function setValorDerechoAfiliacion($valor_derecho_afiliacion){
		$this->valor_derecho_afiliacion = $valor_derecho_afiliacion;
	}

	/**
	 * Metodo para establecer el valor del campo valor_cuota_inicial
	 * @param string $valor_cuota_inicial
	 */
	public function setValorCuotaInicial($valor_cuota_inicial){
		$this->valor_cuota_inicial = $valor_cuota_inicial;
	}

	/**
	 * Metodo para establecer el valor del campo valor_cuota_financiacion
	 * @param string $valor_cuota_financiacion
	 */
	public function setValorCuotaFinanciacion($valor_cuota_financiacion){
		$this->valor_cuota_financiacion = $valor_cuota_financiacion;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_derecho_afiliacion
	 * @param string $saldo_derecho_afiliacion
	 */
	public function setSaldoDerechoAfiliacion($saldo_derecho_afiliacion){
		$this->saldo_derecho_afiliacion = $saldo_derecho_afiliacion;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_cuota_inicial
	 * @param string $saldo_cuota_inicial
	 */
	public function setSaldoCuotaInicial($saldo_cuota_inicial){
		$this->saldo_cuota_inicial = $saldo_cuota_inicial;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_cuota_financiacion
	 * @param string $saldo_cuota_financiacion
	 */
	public function setSaldoCuotaFinanciacion($saldo_cuota_financiacion){
		$this->saldo_cuota_financiacion = $saldo_cuota_financiacion;
	}

	/**
	 * Metodo para establecer el valor del campo base_corriente
	 * @param string $base_corriente
	 */
	public function setBaseCorriente($base_corriente){
		$this->base_corriente = $base_corriente;
	}

	/**
	 * Metodo para establecer el valor del campo base_mora
	 * @param string $base_mora
	 */
	public function setBaseMora($base_mora){
		$this->base_mora = $base_mora;
	}

	/**
	 * Metodo para establecer el valor del campo dias_corriente
	 * @param integer $dias_corriente
	 */
	public function setDiasCorriente($dias_corriente){
		$this->dias_corriente = $dias_corriente;
	}

	/**
	 * Metodo para establecer el valor del campo valor_corriente
	 * @param string $valor_corriente
	 */
	public function setValorCorriente($valor_corriente){
		$this->valor_corriente = $valor_corriente;
	}

	/**
	 * Metodo para establecer el valor del campo dias_mora
	 * @param integer $dias_mora
	 */
	public function setDiasMora($dias_mora){
		$this->dias_mora = $dias_mora;
	}

	/**
	 * Metodo para establecer el valor del campo valor_mora
	 * @param string $valor_mora
	 */
	public function setValorMora($valor_mora){
		$this->valor_mora = $valor_mora;
	}

	/**
	 * Metodo para establecer el valor del campo valor_capital
	 * @param string $valor_capital
	 */
	public function setValorCapital($valor_capital){
		$this->valor_capital = $valor_capital;
	}

	/**
	 * Metodo para establecer el valor del campo pago_minimo
	 * @param string $pago_minimo
	 */
	public function setPagoMinimo($pago_minimo){
		$this->pago_minimo = $pago_minimo;
	}

	/**
	 * Metodo para establecer el valor del campo pago_total
	 * @param string $pago_total
	 */
	public function setPagoTotal($pago_total){
		$this->pago_total = $pago_total;
	}

	/**
	 * Metodo para establecer el valor del campo consecutivo
	 * @param integer $consecutivo
	 */
	public function setConsecutivo($consecutivo){
		$this->consecutivo = $consecutivo;
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
	 * Devuelve el valor del campo numero_contrato
	 * @return string
	 */
	public function getNumeroContrato(){
		return $this->numero_contrato;
	}

	/**
	 * Devuelve el valor del campo periodo
	 * @return string
	 */
	public function getPeriodo(){
		return $this->periodo;
	}

	/**
	 * Devuelve el valor del campo fecha_corte
	 * @return Date
	 */
	public function getFechaCorte(){
		if($this->fecha_corte){
			return new Date($this->fecha_corte);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecha_limite_pago
	 * @return string
	 */
	public function getFechaLimitePago(){
		return $this->fecha_limite_pago;
	}

	/**
	 * Devuelve el valor del campo valor_derecho_afiliacion
	 * @return string
	 */
	public function getValorDerechoAfiliacion(){
		return $this->valor_derecho_afiliacion;
	}

	/**
	 * Devuelve el valor del campo valor_cuota_inicial
	 * @return string
	 */
	public function getValorCuotaInicial(){
		return $this->valor_cuota_inicial;
	}

	/**
	 * Devuelve el valor del campo valor_cuota_financiacion
	 * @return string
	 */
	public function getValorCuotaFinanciacion(){
		return $this->valor_cuota_financiacion;
	}

	/**
	 * Devuelve el valor del campo saldo_derecho_afiliacion
	 * @return string
	 */
	public function getSaldoDerechoAfiliacion(){
		return $this->saldo_derecho_afiliacion;
	}

	/**
	 * Devuelve el valor del campo saldo_cuota_inicial
	 * @return string
	 */
	public function getSaldoCuotaInicial(){
		return $this->saldo_cuota_inicial;
	}

	/**
	 * Devuelve el valor del campo saldo_cuota_financiacion
	 * @return string
	 */
	public function getSaldoCuotaFinanciacion(){
		return $this->saldo_cuota_financiacion;
	}

	/**
	 * Devuelve el valor del campo base_corriente
	 * @return string
	 */
	public function getBaseCorriente(){
		return $this->base_corriente;
	}

	/**
	 * Devuelve el valor del campo base_mora
	 * @return string
	 */
	public function getBaseMora(){
		return $this->base_mora;
	}

	/**
	 * Devuelve el valor del campo dias_corriente
	 * @return integer
	 */
	public function getDiasCorriente(){
		return $this->dias_corriente;
	}

	/**
	 * Devuelve el valor del campo valor_corriente
	 * @return string
	 */
	public function getValorCorriente(){
		return $this->valor_corriente;
	}

	/**
	 * Devuelve el valor del campo dias_mora
	 * @return integer
	 */
	public function getDiasMora(){
		return $this->dias_mora;
	}

	/**
	 * Devuelve el valor del campo valor_mora
	 * @return string
	 */
	public function getValorMora(){
		return $this->valor_mora;
	}

	/**
	 * Devuelve el valor del campo valor_capital
	 * @return string
	 */
	public function getValorCapital(){
		return $this->valor_capital;
	}

	/**
	 * Devuelve el valor del campo pago_minimo
	 * @return string
	 */
	public function getPagoMinimo(){
		return $this->pago_minimo;
	}

	/**
	 * Devuelve el valor del campo pago_total
	 * @return string
	 */
	public function getPagoTotal(){
		return $this->pago_total;
	}

	/**
	 * Devuelve el valor del campo consecutivo
	 * @return integer
	 */
	public function getConsecutivo(){
		return $this->consecutivo;
	}

}

