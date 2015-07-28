<?php

class PagosAutomaticos extends ActiveRecord {

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
	protected $numero_tarjeta;

	/**
	 * @var integer
	 */
	protected $formas_pago_id;

	/**
	 * @var Date
	 */
	protected $fecha_exp;

	/**
	 * @var Date
	 */
	protected $fecha_ven;

	/**
	 * @var integer
	 */
	protected $bancos_id;

	/**
	 * @var integer
	 */
	protected $digito_verificacion;

	/**
	 * @var string
	 */
	protected $estado;


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
	 * Metodo para establecer el valor del campo numero_tarjeta
	 * @param string $numero_tarjeta
	 */
	public function setNumeroTarjeta($numero_tarjeta){
		$this->numero_tarjeta = $numero_tarjeta;
	}

	/**
	 * Metodo para establecer el valor del campo formas_pago_id
	 * @param integer $formas_pago_id
	 */
	public function setFormasPagoId($formas_pago_id){
		$this->formas_pago_id = $formas_pago_id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_exp
	 * @param Date $fecha_exp
	 */
	public function setFechaExp($fecha_exp){
		$this->fecha_exp = $fecha_exp;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_ven
	 * @param Date $fecha_ven
	 */
	public function setFechaVen($fecha_ven){
		$this->fecha_ven = $fecha_ven;
	}

	/**
	 * Metodo para establecer el valor del campo bancos_id
	 * @param integer $bancos_id
	 */
	public function setBancosId($bancos_id){
		$this->bancos_id = $bancos_id;
	}

	/**
	 * Metodo para establecer el valor del campo digito_verificacion
	 * @param integer $digito_verificacion
	 */
	public function setDigitoVerificacion($digito_verificacion){
		$this->digito_verificacion = $digito_verificacion;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
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
	 * Devuelve el valor del campo numero_tarjeta
	 * @return string
	 */
	public function getNumeroTarjeta(){
		return $this->numero_tarjeta;
	}

	/**
	 * Devuelve el valor del campo formas_pago_id
	 * @return integer
	 */
	public function getFormasPagoId(){
		return $this->formas_pago_id;
	}

	/**
	 * Devuelve el valor del campo fecha_exp
	 * @return Date
	 */
	public function getFechaExp(){
		if($this->fecha_exp){
			return new Date($this->fecha_exp);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecha_ven
	 * @return Date
	 */
	public function getFechaVen(){
		if($this->fecha_ven){
			return new Date($this->fecha_ven);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo bancos_id
	 * @return integer
	 */
	public function getBancosId(){
		return $this->bancos_id;
	}

	/**
	 * Devuelve el valor del campo digito_verificacion
	 * @return integer
	 */
	public function getDigitoVerificacion(){
		return $this->digito_verificacion;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

