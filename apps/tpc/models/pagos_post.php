<?php

class PagosPost extends ActiveRecord {

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
	protected $valor;

	/**
	 * @var Date
	 */
	protected $fecha_pago;

	/**
	 * @var integer
	 */
	protected $recibos_pagos_id;


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
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_pago
	 * @param Date $fecha_pago
	 */
	public function setFechaPago($fecha_pago){
		$this->fecha_pago = $fecha_pago;
	}

	/**
	 * Metodo para establecer el valor del campo recibos_pagos_id
	 * @param integer $recibos_pagos_id
	 */
	public function setRecibosPagosId($recibos_pagos_id){
		$this->recibos_pagos_id = $recibos_pagos_id;
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
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo fecha_pago
	 * @return Date
	 */
	public function getFechaPago(){
		return new Date($this->fecha_pago);
	}

	/**
	 * Devuelve el valor del campo recibos_pagos_id
	 * @return integer
	 */
	public function getRecibosPagosId(){
		return $this->recibos_pagos_id;
	}

}

