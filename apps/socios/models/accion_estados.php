<?php

class AccionEstados extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $estados_socios_id;

	/**
	 * @var integer
	 */
	protected $cargos_fijos_id_ini;

	/**
	 * @var integer
	 */
	protected $cargos_fijos_id_fin;

	/**
	 * @var string
	 */
	protected $borrar_cargo_fijo;


	/**
	 * Metodo para establecer el valor del campo estados_socios_id
	 * @param integer $estados_socios_id
	 */
	public function setEstadosSociosId($estados_socios_id){
		$this->estados_socios_id = $estados_socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo cargos_fijos_id_ini
	 * @param integer $cargos_fijos_id_ini
	 */
	public function setCargosFijosIdIni($cargos_fijos_id_ini){
		$this->cargos_fijos_id_ini = $cargos_fijos_id_ini;
	}

	/**
	 * Metodo para establecer el valor del campo cargos_fijos_id_fin
	 * @param integer $cargos_fijos_id_fin
	 */
	public function setCargosFijosIdFin($cargos_fijos_id_fin){
		$this->cargos_fijos_id_fin = $cargos_fijos_id_fin;
	}

	/**
	 * Metodo para establecer el valor del campo borrar_cargo_fijo
	 * @param string $borrar_cargo_fijo
	 */
	public function setBorrarCargoFijo($borrar_cargo_fijo){
		$this->borrar_cargo_fijo = $borrar_cargo_fijo;
	}


	/**
	 * Devuelve el valor del campo estados_socios_id
	 * @return integer
	 */
	public function getEstadosSociosId(){
		return $this->estados_socios_id;
	}

	/**
	 * Devuelve el valor del campo cargos_fijos_id_ini
	 * @return integer
	 */
	public function getCargosFijosIdIni(){
		return $this->cargos_fijos_id_ini;
	}

	/**
	 * Devuelve el valor del campo cargos_fijos_id_fin
	 * @return integer
	 */
	public function getCargosFijosIdFin(){
		return $this->cargos_fijos_id_fin;
	}

	/**
	 * Devuelve el valor del campo borrar_cargo_fijo
	 * @return string
	 */
	public function getBorrarCargoFijo(){
		return $this->borrar_cargo_fijo;
	}

}

