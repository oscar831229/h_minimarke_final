<?php

class RecibosPagosh extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $recibo_provisional;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $ciudad_pago;

	/**
	 * @var Date
	 */
	protected $fecha_pago;

	/**
	 * @var Date
	 */
	protected $fecha_recibo;

	/**
	 * @var string
	 */
	protected $valor_pagado;

	/**
	 * @var string
	 */
	protected $valor_reserva;

	/**
	 * @var string
	 */
	protected $valor_cuoact;

	/**
	 * @var string
	 */
	protected $valor_cuoafi;

	/**
	 * @var string
	 */
	protected $valor_capital;

	/**
	 * @var string
	 */
	protected $valor_interesc;

	/**
	 * @var string
	 */
	protected $valor_interesm;

	/**
	 * @var string
	 */
	protected $valor_inicial;

	/**
	 * @var string
	 */
	protected $valor_financiacion;

	/**
	 * @var integer
	 */
	protected $cuentas_id;

	/**
	 * @var string
	 */
	protected $otros;

	/**
	 * @var string
	 */
	protected $observaciones;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $aplico;

	/**
	 * @var integer
	 */
	protected $rc;

	/**
	 * @var string
	 */
	protected $pago_posterior;

	/**
	 * @var integer
	 */
	protected $abono_reservas_id;

	/**
	 * @var integer
	 */
	protected $nota_historia_id;

	/**
	 * @var integer
	 */
	protected $cuota_saldo;

	/**
	 * @var string
	 */
	protected $deb_cre;

	/**
	 * @var integer
	 */
	protected $porc_condonar;

	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo recibo_provisional
	 * @param integer $recibo_provisional
	 */
	public function setReciboProvisional($recibo_provisional){
		$this->recibo_provisional = $recibo_provisional;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad_pago
	 * @param integer $ciudad_pago
	 */
	public function setCiudadPago($ciudad_pago){
		$this->ciudad_pago = $ciudad_pago;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_pago
	 * @param Date $fecha_pago
	 */
	public function setFechaPago($fecha_pago){
		$this->fecha_pago = $fecha_pago;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_recibo
	 * @param Date $fecha_recibo
	 */
	public function setFechaRecibo($fecha_recibo){
		$this->fecha_recibo = $fecha_recibo;
	}

	/**
	 * Metodo para establecer el valor del campo valor_pagado
	 * @param string $valor_pagado
	 */
	public function setValorPagado($valor_pagado){
		$this->valor_pagado = $valor_pagado;
	}

	/**
	 * Metodo para establecer el valor del campo valor_reserva
	 * @param string $valor_reserva
	 */
	public function setValorReserva($valor_reserva){
		$this->valor_reserva = $valor_reserva;
	}

	/**
	 * Metodo para establecer el valor del campo valor_cuoact
	 * @param string $valor_cuoact
	 */
	public function setValorCuoact($valor_cuoact){
		$this->valor_cuoact = $valor_cuoact;
	}

	/**
	 * Metodo para establecer el valor del campo valor_cuoafi
	 * @param string $valor_cuoafi
	 */
	public function setValorCuoafi($valor_cuoafi){
		$this->valor_cuoafi = $valor_cuoafi;
	}

	/**
	 * Metodo para establecer el valor del campo valor_capital
	 * @param string $valor_capital
	 */
	public function setValorCapital($valor_capital){
		$this->valor_capital = $valor_capital;
	}

	/**
	 * Metodo para establecer el valor del campo valor_interesc
	 * @param string $valor_interesc
	 */
	public function setValorInteresc($valor_interesc){
		$this->valor_interesc = $valor_interesc;
	}

	/**
	 * Metodo para establecer el valor del campo valor_interesm
	 * @param string $valor_interesm
	 */
	public function setValorInteresm($valor_interesm){
		$this->valor_interesm = $valor_interesm;
	}

	/**
	 * Metodo para establecer el valor del campo valor_inicial
	 * @param string $valor_inicial
	 */
	public function setValorInicial($valor_inicial){
		$this->valor_inicial = $valor_inicial;
	}

	/**
	 * Metodo para establecer el valor del campo valor_financiacion
	 * @param string $valor_financiacion
	 */
	public function setValorFinanciacion($valor_financiacion){
		$this->valor_financiacion = $valor_financiacion;
	}

	/**
	 * Metodo para establecer el valor del campo cuentas_id
	 * @param integer $cuentas_id
	 */
	public function setCuentasId($cuentas_id){
		$this->cuentas_id = $cuentas_id;
	}

	/**
	 * Metodo para establecer el valor del campo otros
	 * @param string $otros
	 */
	public function setOtros($otros){
		$this->otros = $otros;
	}

	/**
	 * Metodo para establecer el valor del campo observaciones
	 * @param string $observaciones
	 */
	public function setObservaciones($observaciones){
		$this->observaciones = $observaciones;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo aplico
	 * @param string $aplico
	 */
	public function setAplico($aplico){
		$this->aplico = $aplico;
	}

	/**
	 * Metodo para establecer el valor del campo rc
	 * @param integer $rc
	 */
	public function setRc($rc){
		$this->rc = $rc;
	}

	/**
	 * Metodo para establecer el valor del campo pago_posterior
	 * @param string $pago_posterior
	 */
	public function setPagoPosterior($pago_posterior){
		$this->pago_posterior = $pago_posterior;
	}

	/**
	 * Metodo para establecer el valor del campo abono_reservas_id
	 * @param integer $abono_reservas_id
	 */
	public function setAbonoReservasId($abono_reservas_id){
		$this->abono_reservas_id = $abono_reservas_id;
	}

	/**
	 * Metodo para establecer el valor del campo nota_historia_id
	 * @param integer $nota_historia_id
	 */
	public function setNotaHistoriaId($nota_historia_id){
		$this->nota_historia_id = $nota_historia_id;
	}

	/**
	 * Metodo para establecer el valor del campo abono_reservas_id
	 * @param integer $cuota_saldo
	 */
	public function setCuotaSaldo($cuota_saldo){
		$this->cuota_saldo = $cuota_saldo;
	}

	/**
	 * Metodo para establecer el valor del campo deb_cre
	 * @param string $calculos
	 */
	public function setDebCre($deb_cre){
		$this->deb_cre = $deb_cre;
	}

	/**
	 * Metodo para establecer el valor del campo porc_condonar
	 * @param float $porc_condonar
	 */
	public function setPorcCondonar($porc_condonar){
		$this->porc_condonar = $porc_condonar;
	}

	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo recibo_provisional
	 * @return integer
	 */
	public function getReciboProvisional(){
		return $this->recibo_provisional;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo ciudad_pago
	 * @return integer
	 */
	public function getCiudadPago(){
		return $this->ciudad_pago;
	}

	/**
	 * Devuelve el valor del campo fecha_pago
	 * @return Date
	 */
	public function getFechaPago(){
		if($this->fecha_pago){
			return new Date($this->fecha_pago);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecha_recibo
	 * @return Date
	 */
	public function getFechaRecibo(){
		if($this->fecha_recibo){
			return new Date($this->fecha_recibo);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo valor_pagado
	 * @return string
	 */
	public function getValorPagado(){
		return $this->valor_pagado;
	}

	/**
	 * Devuelve el valor del campo valor_reserva
	 * @return string
	 */
	public function getValorReserva(){
		return $this->valor_reserva;
	}

	/**
	 * Devuelve el valor del campo valor_cuoact
	 * @return string
	 */
	public function getValorCuoact(){
		return $this->valor_cuoact;
	}

	/**
	 * Devuelve el valor del campo valor_cuoafi
	 * @return string
	 */
	public function getValorCuoafi(){
		return $this->valor_cuoafi;
	}

	/**
	 * Devuelve el valor del campo valor_capital
	 * @return string
	 */
	public function getValorCapital(){
		return $this->valor_capital;
	}

	/**
	 * Devuelve el valor del campo valor_interesc
	 * @return string
	 */
	public function getValorInteresc(){
		return $this->valor_interesc;
	}

	/**
	 * Devuelve el valor del campo valor_interesm
	 * @return string
	 */
	public function getValorInteresm(){
		return $this->valor_interesm;
	}

	/**
	 * Devuelve el valor del campo valor_inicial
	 * @return string
	 */
	public function getValorInicial(){
		return $this->valor_inicial;
	}

	/**
	 * Devuelve el valor del campo valor_financiacion
	 * @return string
	 */
	public function getValorFinanciacion(){
		return $this->valor_financiacion;
	}

	/**
	 * Devuelve el valor del campo cuentas_id
	 * @return integer
	 */
	public function getCuentasId(){
		return $this->cuentas_id;
	}

	/**
	 * Devuelve el valor del campo otros
	 * @return string
	 */
	public function getOtros(){
		return $this->otros;
	}

	/**
	 * Devuelve el valor del campo observaciones
	 * @return string
	 */
	public function getObservaciones(){
		return $this->observaciones;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo aplico
	 * @return string
	 */
	public function getAplico(){
		return $this->aplico;
	}

	/**
	 * Devuelve el valor del campo rc
	 * @return integer
	 */
	public function getRc(){
		return $this->rc;
	}

	/**
	 * Devuelve el valor del campo pago_posterior
	 * @return string
	 */
	public function getPagoPosterior(){
		return $this->pago_posterior;
	}

	/**
	 * Devuelve el valor del campo abono_reservas_id
	 * @return integer
	 */
	public function getAbonoReservasId(){
		return $this->abono_reservas_id;
	}

	/**
	 * Devuelve el valor del campo nota_historia_id
	 * @return integer
	 */
	public function getNotaHistoriaId(){
		return $this->nota_historia_id;
	}

	/**
	 * Devuelve el valor del campo cuota_saldo
	 * @param integer $cuota_saldo
	 */
	public function getCuotaSaldo(){
		return $this->cuota_saldo;
	}

	/**
	 * Devuelve el valor del campo deb_cre
	 * @param string $calculos
	 */
	public function getDebCre(){
		return $this->deb_cre;
	}

	/**
	 * Devuelve el valor del campo porc_condonar
	 * @return float
	 */
	public function getPorcCondonar(){
		return $this->porc_condonar;
	}
}

