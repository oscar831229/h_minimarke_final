<?php

class DetalleAbonoReservas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $abono_reservas_id;

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
	 * Metodo para establecer el valor del campo abono_reservas_id
	 * @param integer $abono_reservas_id
	 */
	public function setAbonoReservasId($abono_reservas_id){
		$this->abono_reservas_id = $abono_reservas_id;
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
	 * Devuelve el valor del campo abono_reservas_id
	 * @return integer
	 */
	public function getAbonoReservasId(){
		return $this->abono_reservas_id;
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
		$this->addForeignKey('abono_reservas_id','AbonoReservas','id', array(
			'message' => 'La reserva no es valida'
		));
		$this->addForeignKey('formas_pago_id', 'FormasPago', 'id', array(
			'message' => 'La forma de pago no es valido'
		));
		$this->hasOne('formas_pago_id','FormasPago','id');
	}
}

