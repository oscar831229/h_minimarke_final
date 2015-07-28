<?php

class TipoSocios extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $cuota_minima;

	/**
	 * @var string
	 */
	protected $mora_cuota;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var integer
	 */
	protected $edad_ini;

	/**
	 * @var integer
	 */
	protected $edad_fin;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo cuota_minima
	 * @param string $cuota_minima
	 */
	public function setCuotaMinima($cuota_minima){
		$this->cuota_minima = $cuota_minima;
	}

	/**
	 * Metodo para establecer el valor del campo mora_cuota
	 * @param string $mora_cuota
	 */
	public function setMoraCuota($mora_cuota){
		$this->mora_cuota = $mora_cuota;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo edad_ini
	 * @param integer $edad_ini
	 */
	public function setEdadIni($edad_ini){
		$this->edad_ini = $edad_ini;
	}

	/**
	 * Metodo para establecer el valor del campo edad_fin
	 * @param integer $edad_fin
	 */
	public function setEdadFin($edad_fin){
		$this->edad_fin = $edad_fin;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo cuota_minima
	 * @return string
	 */
	public function getCuotaMinima(){
		return $this->cuota_minima;
	}

	/**
	 * Devuelve el valor del campo mora_cuota
	 * @return string
	 */
	public function getMoraCuota(){
		return $this->mora_cuota;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo edad_ini
	 * @return integer
	 */
	public function getEdadIni(){
		return $this->edad_ini;
	}

	/**
	 * Devuelve el valor del campo edad_fin
	 * @return integer
	 */
	public function getEdadFin(){
		return $this->edad_fin;
	}

}

