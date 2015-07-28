<?php

class Prueba extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_tpc_id;

	/**
	 * @var integer
	 */
	protected $recibos_pagos_id;

	/**
	 * @var string
	 */
	protected $fecha_recibo;

	/**
	 * @var string
	 */
	protected $fecha_pago;

	/**
	 * @var string
	 */
	protected $efectivo;

	/**
	 * @var string
	 */
	protected $tipo;


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
	 * Metodo para establecer el valor del campo recibos_pagos_id
	 * @param integer $recibos_pagos_id
	 */
	public function setRecibosPagosId($recibos_pagos_id){
		$this->recibos_pagos_id = $recibos_pagos_id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_recibo
	 * @param string $fecha_recibo
	 */
	public function setFechaRecibo($fecha_recibo){
		$this->fecha_recibo = $fecha_recibo;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_pago
	 * @param string $fecha_pago
	 */
	public function setFechaPago($fecha_pago){
		$this->fecha_pago = $fecha_pago;
	}

	/**
	 * Metodo para establecer el valor del campo efectivo
	 * @param string $efectivo
	 */
	public function setEfectivo($efectivo){
		$this->efectivo = $efectivo;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
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
	 * Devuelve el valor del campo recibos_pagos_id
	 * @return integer
	 */
	public function getRecibosPagosId(){
		return $this->recibos_pagos_id;
	}

	/**
	 * Devuelve el valor del campo fecha_recibo
	 * @return string
	 */
	public function getFechaRecibo(){
		return $this->fecha_recibo;
	}

	/**
	 * Devuelve el valor del campo fecha_pago
	 * @return string
	 */
	public function getFechaPago(){
		return $this->fecha_pago;
	}

	/**
	 * Devuelve el valor del campo efectivo
	 * @return string
	 */
	public function getEfectivo(){
		return $this->efectivo;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

}

