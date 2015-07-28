<?php

class DetalleRecibosPagos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $recibos_pagos_id;

	/**
	 * @var integer
	 */
	protected $formas_pago_id;

	/**
	 * @var string
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $valor;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo recibos_pagos_id
	 * @param integer $recibos_pagos_id
	 */
	public function setRecibosPagosId($recibos_pagos_id){
		$this->recibos_pagos_id = $recibos_pagos_id;
	}

	/**
	 * Metodo para establecer el valor del campo formas_pago_id
	 * @param integer $formas_pago_id
	 */
	public function setFormasPagoId($formas_pago_id){
		$this->formas_pago_id = $formas_pago_id;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param string $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo recibos_pagos_id
	 * @return integer
	 */
	public function getRecibosPagosId(){
		return $this->recibos_pagos_id;
	}

	/**
	 * Devuelve el valor del campo formas_pago_id
	 * @return integer
	 */
	public function getFormasPagoId(){
		return $this->formas_pago_id;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return string
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}
	
	public function initialize(){
	    $this->addForeignKey('recibos_pagos_id', 'RecibosPagos', 'id', array(
	    	'message' => 'El recibo de pago no es valido'
	    ));
	    $this->addForeignKey('formas_pago_id', 'FormasPago', 'id', array(
	    	'message' => 'La forma de pago no es valido'
	    ));
	    $this->hasOne('formas_pago_id','FormasPago','id');
	}

}

