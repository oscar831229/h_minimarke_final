<?php

class Factura extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $movimiento_id;

	/**
	 * @var Date
	 */
	protected $fecha_factura;

	/**
	 * @var string
	 */
	protected $periodo;

	/**
	 * @var Date
	 */
	protected $fecha_vencimiento;

	/**
	 * @var string
	 */
	protected $saldo_vencido;

	/**
	 * @var string
	 */
	protected $saldo_mora;

	/**
	 * @var integer
	 */
	protected $dias_mora;

	/**
	 * @var string
	 */
	protected $mora_pagado;

	/**
	 * @var string
	 */
	protected $cuota_vigente;

	/**
	 * @var string
	 */
	protected $vigente_pagado;

	/**
	 * @var string
	 */
	protected $total_factura;

	/**
	 * @var string
	 */
	protected $val_ult_abono;

	/**
	 * @var Date
	 */
	protected $fec_ult_abono;

	/**
	 * @var string
	 */
	protected $sal_ant_neto;

	/**
	 * @var string
	 */
	protected $sal_ant_interes;

	/**
	 * @var string
	 */
	protected $cargo_mes;

	/**
	 * @var string
	 */
	protected $sal_actual;

	/**
	 * @var string
	 */
	protected $sal_act_mora;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var integer
	 */
	protected $invoicer_id;

	/**
	 * @var string
	 */
	protected $comprob_contab;

	/**
	 * @var string
	 */
	protected $numero_contab;

	/**
	 * @var string
	 */
	protected $total_ico;


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
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo movimiento_id
	 * @param integer $movimiento_id
	 */
	public function setMovimientoId($movimiento_id){
		$this->movimiento_id = $movimiento_id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_factura
	 * @param Date $fecha_factura
	 */
	public function setFechaFactura($fecha_factura){
		$this->fecha_factura = $fecha_factura;
	}

	/**
	 * Metodo para establecer el valor del campo periodo
	 * @param string $periodo
	 */
	public function setPeriodo($periodo){
		$this->periodo = $periodo;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_vencimiento
	 * @param Date $fecha_vencimiento
	 */
	public function setFechaVencimiento($fecha_vencimiento){
		$this->fecha_vencimiento = $fecha_vencimiento;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_vencido
	 * @param string $saldo_vencido
	 */
	public function setSaldoVencido($saldo_vencido){
		$this->saldo_vencido = $saldo_vencido;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_mora
	 * @param string $saldo_mora
	 */
	public function setSaldoMora($saldo_mora){
		$this->saldo_mora = $saldo_mora;
	}

	/**
	 * Metodo para establecer el valor del campo dias_mora
	 * @param integer $dias_mora
	 */
	public function setDiasMora($dias_mora){
		$this->dias_mora = $dias_mora;
	}

	/**
	 * Metodo para establecer el valor del campo mora_pagado
	 * @param string $mora_pagado
	 */
	public function setMoraPagado($mora_pagado){
		$this->mora_pagado = $mora_pagado;
	}

	/**
	 * Metodo para establecer el valor del campo cuota_vigente
	 * @param string $cuota_vigente
	 */
	public function setCuotaVigente($cuota_vigente){
		$this->cuota_vigente = $cuota_vigente;
	}

	/**
	 * Metodo para establecer el valor del campo vigente_pagado
	 * @param string $vigente_pagado
	 */
	public function setVigentePagado($vigente_pagado){
		$this->vigente_pagado = $vigente_pagado;
	}

	/**
	 * Metodo para establecer el valor del campo total_factura
	 * @param string $total_factura
	 */
	public function setTotalFactura($total_factura){
		$this->total_factura = $total_factura;
	}

	/**
	 * Metodo para establecer el valor del campo val_ult_abono
	 * @param string $val_ult_abono
	 */
	public function setValUltAbono($val_ult_abono){
		$this->val_ult_abono = $val_ult_abono;
	}

	/**
	 * Metodo para establecer el valor del campo fec_ult_abono
	 * @param Date $fec_ult_abono
	 */
	public function setFecUltAbono($fec_ult_abono){
		$this->fec_ult_abono = $fec_ult_abono;
	}

	/**
	 * Metodo para establecer el valor del campo sal_ant_neto
	 * @param string $sal_ant_neto
	 */
	public function setSalAntNeto($sal_ant_neto){
		$this->sal_ant_neto = $sal_ant_neto;
	}

	/**
	 * Metodo para establecer el valor del campo sal_ant_interes
	 * @param string $sal_ant_interes
	 */
	public function setSalAntInteres($sal_ant_interes){
		$this->sal_ant_interes = $sal_ant_interes;
	}

	/**
	 * Metodo para establecer el valor del campo cargo_mes
	 * @param string $cargo_mes
	 */
	public function setCargoMes($cargo_mes){
		$this->cargo_mes = $cargo_mes;
	}

	/**
	 * Metodo para establecer el valor del campo sal_actual
	 * @param string $sal_actual
	 */
	public function setSalActual($sal_actual){
		$this->sal_actual = $sal_actual;
	}

	/**
	 * Metodo para establecer el valor del campo sal_act_mora
	 * @param string $sal_act_mora
	 */
	public function setSalActMora($sal_act_mora){
		$this->sal_act_mora = $sal_act_mora;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo invoicer_id
	 * @param integer $invoicer_id
	 */
	public function setInvoicerId($invoicer_id){
		$this->invoicer_id = $invoicer_id;
	}

	/**
	 * Metodo para establecer el valor del campo comprob_contab
	 * @param string $comprob_contab
	 */
	public function setComprobContab($comprob_contab){
		$this->comprob_contab = $comprob_contab;
	}

	/**
	 * Metodo para establecer el valor del campo numero_contab
	 * @param string $numero_contab
	 */
	public function setNumeroContab($numero_contab){
		$this->numero_contab = $numero_contab;
	}

	/**
	 * Metodo para establecer el valor del campo total_ico
	 * @param string $total_ico
	 */
	public function setTotalIco($total_ico){
		$this->total_ico = $total_ico;
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
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo movimiento_id
	 * @return integer
	 */
	public function getMovimientoId(){
		return $this->movimiento_id;
	}

	/**
	 * Devuelve el valor del campo fecha_factura
	 * @return Date
	 */
	public function getFechaFactura(){
		if($this->fecha_factura){
			return new Date($this->fecha_factura);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo periodo
	 * @return string
	 */
	public function getPeriodo(){
		return $this->periodo;
	}

	/**
	 * Devuelve el valor del campo fecha_vencimiento
	 * @return Date
	 */
	public function getFechaVencimiento(){
		if($this->fecha_vencimiento){
			return new Date($this->fecha_vencimiento);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo saldo_vencido
	 * @return string
	 */
	public function getSaldoVencido(){
		return $this->saldo_vencido;
	}

	/**
	 * Devuelve el valor del campo saldo_mora
	 * @return string
	 */
	public function getSaldoMora(){
		return $this->saldo_mora;
	}

	/**
	 * Devuelve el valor del campo dias_mora
	 * @return integer
	 */
	public function getDiasMora(){
		return $this->dias_mora;
	}

	/**
	 * Devuelve el valor del campo mora_pagado
	 * @return string
	 */
	public function getMoraPagado(){
		return $this->mora_pagado;
	}

	/**
	 * Devuelve el valor del campo cuota_vigente
	 * @return string
	 */
	public function getCuotaVigente(){
		return $this->cuota_vigente;
	}

	/**
	 * Devuelve el valor del campo vigente_pagado
	 * @return string
	 */
	public function getVigentePagado(){
		return $this->vigente_pagado;
	}

	/**
	 * Devuelve el valor del campo total_factura
	 * @return string
	 */
	public function getTotalFactura(){
		return $this->total_factura;
	}

	/**
	 * Devuelve el valor del campo val_ult_abono
	 * @return string
	 */
	public function getValUltAbono(){
		return $this->val_ult_abono;
	}

	/**
	 * Devuelve el valor del campo fec_ult_abono
	 * @return Date
	 */
	public function getFecUltAbono(){
		if($this->fec_ult_abono){
			return new Date($this->fec_ult_abono);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo sal_ant_neto
	 * @return string
	 */
	public function getSalAntNeto(){
		return $this->sal_ant_neto;
	}

	/**
	 * Devuelve el valor del campo sal_ant_interes
	 * @return string
	 */
	public function getSalAntInteres(){
		return $this->sal_ant_interes;
	}

	/**
	 * Devuelve el valor del campo cargo_mes
	 * @return string
	 */
	public function getCargoMes(){
		return $this->cargo_mes;
	}

	/**
	 * Devuelve el valor del campo sal_actual
	 * @return string
	 */
	public function getSalActual(){
		return $this->sal_actual;
	}

	/**
	 * Devuelve el valor del campo sal_act_mora
	 * @return string
	 */
	public function getSalActMora(){
		return $this->sal_act_mora;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo invoicer_id
	 * @return integer
	 */
	public function getInvoicerId(){
		return $this->invoicer_id;
	}

	/**
	 * Devuelve el valor del campo comprob_contab
	 * @return string
	 */
	public function getComprobContab(){
		return $this->comprob_contab;
	}

	/**
	 * Devuelve el valor del campo numero_contab
	 * @return string
	 */
	public function getNumeroContab(){
		return $this->numero_contab;
	}

	/**
	 * Devuelve el valor del campo total_ico
	 * @return string
	 */
	public function getTotalIco(){
		return $this->total_ico;
	}

}

