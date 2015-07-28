<?php

class FacturasPagos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $facturas_id;

	/**
	 * @var integer
	 */
	protected $forma_pago;

	/**
	 * @var string
	 */
	protected $descripcion;

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
	 * Metodo para establecer el valor del campo facturas_id
	 * @param integer $facturas_id
	 */
	public function setFacturasId($facturas_id){
		$this->facturas_id = $facturas_id;
	}

	/**
	 * Metodo para establecer el valor del campo forma_pago
	 * @param integer $forma_pago
	 */
	public function setFormaPago($forma_pago){
		$this->forma_pago = $forma_pago;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
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
	 * Devuelve el valor del campo facturas_id
	 * @return integer
	 */
	public function getFacturasId(){
		return $this->facturas_id;
	}

	/**
	 * Devuelve el valor del campo forma_pago
	 * @return integer
	 */
	public function getFormaPago(){
		return $this->forma_pago;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

}

