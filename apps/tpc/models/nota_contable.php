<?php

class NotaContable extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

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
	 * @var integer
	 */
	protected $rc;


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
	 * Metodo para establecer el valor del campo rc
	 * @param integer $rc
	 */
	public function setRc($rc){
		$this->rc = $rc;
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
	 * Devuelve el valor del campo rc
	 * @return integer
	 */
	public function getRc(){
		return $this->rc;
	}

}

