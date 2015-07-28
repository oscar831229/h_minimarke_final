<?php

class Planilla extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var Date
	 */
	protected $fecha_planilla;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_planilla
	 * @param Date $fecha_planilla
	 */
	public function setFechaPlanilla($fecha_planilla){
		$this->fecha_planilla = $fecha_planilla;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo fecha_planilla
	 * @return Date
	 */
	public function getFechaPlanilla(){
		return new Date($this->fecha_planilla);
	}

}

