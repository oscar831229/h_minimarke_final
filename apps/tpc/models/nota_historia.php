<?php

class NotaHistoria extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id_errado;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $reservas_id;

	/**
	 * @var Date
	 */
	protected $fecha_nota;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $observaciones;

	/**
	 * @var string
	 */
	protected $rc_errados;

	/**
	 * @var string
	 */
	protected $rc_abonar;

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
	 * Metodo para establecer el valor del campo socios_id_errado
	 * @param integer $socios_id_errado
	 */
	public function setSociosIdErrado($socios_id_errado){
		$this->socios_id_errado = $socios_id_errado;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo reservas_id
	 * @param integer $reservas_id
	 */
	public function setReservasId($reservas_id){
		$this->reservas_id = $reservas_id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_nota
	 * @param Date $fecha_nota
	 */
	public function setFechaNota($fecha_nota){
		$this->fecha_nota = $fecha_nota;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo observaciones
	 * @param string $observaciones
	 */
	public function setObservaciones($observaciones){
		$this->observaciones = $observaciones;
	}

	/**
	 * Metodo para establecer el valor del campo rc_errados
	 * @param string $rc_errados
	 */
	public function setRcErrados($rc_errados){
		$this->rc_errados = $rc_errados;
	}

	/**
	 * Metodo para establecer el valor del campo rc_abonar
	 * @param string $rc_abonar
	 */
	public function setRcAbonar($rc_abonar){
		$this->rc_abonar = $rc_abonar;
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
	 * Devuelve el valor del campo socios_id_errado
	 * @return integer
	 */
	public function getSociosIdErrado(){
		return $this->socios_id_errado;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo reservas_id
	 * @return integer
	 */
	public function getReservasId(){
		return $this->reservas_id;
	}

	/**
	 * Devuelve el valor del campo fecha_nota
	 * @return Date
	 */
	public function getFechaNota(){
		if($this->fecha_nota){
			return new Date($this->fecha_nota);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo observaciones
	 * @return string
	 */
	public function getObservaciones(){
		return $this->observaciones;
	}

	/**
	 * Devuelve el valor del campo rc_errados
	 * @return string
	 */
	public function getRcErrados(){
		return $this->rc_errados;
	}

	/**
	 * Devuelve el valor del campo rc_abonar
	 * @return string
	 */
	public function getRcAbonar(){
		return $this->rc_abonar;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

