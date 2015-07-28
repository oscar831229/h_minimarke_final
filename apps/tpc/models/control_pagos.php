<?php

class ControlPagos extends RcsRecord {

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
	protected $pagado;

	/**
	 * @var integer
	 */
	protected $dias_pagado;

	/**
	 * @var string
	 */
	protected $capital;

	/**
	 * @var string
	 */
	protected $interes;

	/**
	 * @var integer
	 */
	protected $dias_corriente;

	/**
	 * @var string
	 */
	protected $mora;

	/**
	 * @var integer
	 */
	protected $dias_mora;

	/**
	 * @var Date
	 */
	protected $fecha_pago;

	/**
	 * @var string
	 */
	protected $saldo;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var integer
	 */
	protected $recibos_pagos_id;

	/**
	 * @var integer
	 */
	protected $nota_contable_id;

	/**
	 * @var integer
	 */
	protected $rc;

	/**
	 * @var integer
	 */
	protected $nota_historia_id;


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
	 * Metodo para establecer el valor del campo pagado
	 * @param string $pagado
	 */
	public function setPagado($pagado){
		$this->pagado = $pagado;
	}

	/**
	 * Metodo para establecer el valor del campo dias_pagado
	 * @param integer $dias_pagado
	 */
	public function setDiasPagado($dias_pagado){
		$this->dias_pagado = $dias_pagado;
	}

	/**
	 * Metodo para establecer el valor del campo capital
	 * @param string $capital
	 */
	public function setCapital($capital){
		$this->capital = $capital;
	}

	/**
	 * Metodo para establecer el valor del campo interes
	 * @param string $interes
	 */
	public function setInteres($interes){
		$this->interes = $interes;
	}

	/**
	 * Metodo para establecer el valor del campo dias_corriente
	 * @param integer $dias_corriente
	 */
	public function setDiasCorriente($dias_corriente){
		$this->dias_corriente = $dias_corriente;
	}

	/**
	 * Metodo para establecer el valor del campo mora
	 * @param string $mora
	 */
	public function setMora($mora){
		$this->mora = $mora;
	}

	/**
	 * Metodo para establecer el valor del campo dias_mora
	 * @param integer $dias_mora
	 */
	public function setDiasMora($dias_mora){
		$this->dias_mora = $dias_mora;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_pago
	 * @param Date $fecha_pago
	 */
	public function setFechaPago($fecha_pago){
		$this->fecha_pago = $fecha_pago;
	}

	/**
	 * Metodo para establecer el valor del campo saldo
	 * @param string $saldo
	 */
	public function setSaldo($saldo){
		$this->saldo = $saldo;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo recibos_pagos_id
	 * @param integer $recibos_pagos_id
	 */
	public function setRecibosPagosId($recibos_pagos_id){
		$this->recibos_pagos_id = $recibos_pagos_id;
	}

	/**
	 * Metodo para establecer el valor del campo nota_contable_id
	 * @param integer $nota_contable_id
	 */
	public function setNotaContableId($nota_contable_id){
		$this->nota_contable_id = $nota_contable_id;
	}

	/**
	 * Metodo para establecer el valor del campo rc
	 * @param integer $rc
	 */
	public function setRc($rc){
		$this->rc = $rc;
	}

	/**
	 * Metodo para establecer el valor del campo nota_historia_id
	 * @param integer $nota_historia_id
	 */
	public function setNotaHistoriaId($nota_historia_id){
		$this->nota_historia_id = $nota_historia_id;
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
	 * Devuelve el valor del campo pagado
	 * @return string
	 */
	public function getPagado(){
		return $this->pagado;
	}

	/**
	 * Devuelve el valor del campo dias_pagado
	 * @return integer
	 */
	public function getDiasPagado(){
		return $this->dias_pagado;
	}

	/**
	 * Devuelve el valor del campo capital
	 * @return string
	 */
	public function getCapital(){
		return $this->capital;
	}

	/**
	 * Devuelve el valor del campo interes
	 * @return string
	 */
	public function getInteres(){
		return $this->interes;
	}

	/**
	 * Devuelve el valor del campo dias_corriente
	 * @return integer
	 */
	public function getDiasCorriente(){
		return $this->dias_corriente;
	}

	/**
	 * Devuelve el valor del campo mora
	 * @return string
	 */
	public function getMora(){
		return $this->mora;
	}

	/**
	 * Devuelve el valor del campo dias_mora
	 * @return integer
	 */
	public function getDiasMora(){
		return $this->dias_mora;
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
	 * Devuelve el valor del campo saldo
	 * @return string
	 */
	public function getSaldo(){
		return $this->saldo;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo recibos_pagos_id
	 * @return integer
	 */
	public function getRecibosPagosId(){
		return $this->recibos_pagos_id;
	}

	/**
	 * Devuelve el valor del campo nota_contable_id
	 * @return integer
	 */
	public function getNotaContableId(){
		return $this->nota_contable_id;
	}

	/**
	 * Devuelve el valor del campo rc
	 * @return integer
	 */
	public function getRc(){
		return $this->rc;
	}

	/**
	 * Devuelve el valor del campo nota_historia_id
	 * @return integer
	 */
	public function getNotaHistoriaId(){
		return $this->nota_historia_id;
	}

	public function initialize(){
		$this->belongsTo('socios');
	}
}

