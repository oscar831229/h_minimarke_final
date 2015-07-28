<?php

class AjustePagos extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $periodo;

	/**
	 * @var string
	 */
	protected $fecha_hora;

	/**
	 * @var integer
	 */
	protected $usuarios_id;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo comprob
	 * @param string $comprob
	 */
	public function setComprob($comprob){
		$this->comprob = $comprob;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo periodo
	 * @param integer $periodo
	 */
	public function setPeriodo($periodo){
		$this->periodo = $periodo;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_hora
	 * @param string $fecha_hora
	 */
	public function setFechaHora($fecha_hora){
		$this->fecha_hora = $fecha_hora;
	}

	/**
	 * Metodo para establecer el valor del campo usuarios_id
	 * @param integer $usuarios_id
	 */
	public function setUsuariosId($usuarios_id){
		$this->usuarios_id = $usuarios_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo comprob
	 * @return string
	 */
	public function getComprob(){
		return $this->comprob;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo periodo
	 * @return integer
	 */
	public function getPeriodo(){
		return $this->periodo;
	}

	/**
	 * Devuelve el valor del campo fecha_hora
	 * @return string
	 */
	public function getFechaHora(){
		return $this->fecha_hora;
	}

	/**
	 * Devuelve el valor del campo usuarios_id
	 * @return integer
	 */
	public function getUsuariosId(){
		return $this->usuarios_id;
	}

}

