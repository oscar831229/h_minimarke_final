<?php

class Planificador extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var integer
	 */
	protected $tipo_servicio_id;

	/**
	 * @var integer
	 */
	protected $salon_id;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_servicio_id
	 * @param integer $tipo_servicio_id
	 */
	public function setTipoServicioId($tipo_servicio_id){
		$this->tipo_servicio_id = $tipo_servicio_id;
	}

	/**
	 * Metodo para establecer el valor del campo salon_id
	 * @param integer $salon_id
	 */
	public function setSalonId($salon_id){
		$this->salon_id = $salon_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo tipo_servicio_id
	 * @return integer
	 */
	public function getTipoServicioId(){
		return $this->tipo_servicio_id;
	}

	/**
	 * Devuelve el valor del campo salon_id
	 * @return integer
	 */
	public function getSalonId(){
		return $this->salon_id;
	}

}

