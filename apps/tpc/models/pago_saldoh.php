<?php

class PagoSaldoh extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $numero_cuotas;

	/**
	 * @var string
	 */
	protected $interes;

	/**
	 * @var Date
	 */
	protected $fecha_primera_cuota;

	/**
	 * @var integer
	 */
	protected $premios_id;

	/**
	 * @var string
	 */
	protected $observaciones;

	/**
	 * @var decimal
	 */
	protected $mora;

	/**
	 * @var integer
	 */
	protected $nota_historia_id;


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
	 * Metodo para establecer el valor del campo numero_cuotas
	 * @param integer $numero_cuotas
	 */
	public function setNumeroCuotas($numero_cuotas){
		$this->numero_cuotas = $numero_cuotas;
	}

	/**
	 * Metodo para establecer el valor del campo interes
	 * @param string $interes
	 */
	public function setInteres($interes){
		$this->interes = $interes;
	}

	/**
	 * Metodo para establecer el valor del campo mora
	 * @param decimal $mora
	 */
	public function setMora($mora){
		$this->mora = $mora;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_primera_cuota
	 * @param Date $fecha_primera_cuota
	 */
	public function setFechaPrimeraCuota($fecha_primera_cuota){
		$this->fecha_primera_cuota = $fecha_primera_cuota;
	}

	/**
	 * Metodo para establecer el valor del campo premios_id
	 * @param integer $premios_id
	 */
	public function setPremiosId($premios_id){
		$this->premios_id = $premios_id;
	}

	/**
	 * Metodo para establecer el valor del campo observaciones
	 * @param string $observaciones
	 */
	public function setObservaciones($observaciones){
		$this->observaciones = $observaciones;
	}

	/**
	 * Metodo para establecer el valor del campo nota_historia_id
	 * @param integer $nota_historia_id
	 */
	public function setNotaHistoriaId($nota_historia_id){
		$this->nota_historia_id = $nota_historia_id;
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
	 * Devuelve el valor del campo numero_cuotas
	 * @return integer
	 */
	public function getNumeroCuotas(){
		return $this->numero_cuotas;
	}

	/**
	 * Devuelve el valor del campo interes
	 * @return string
	 */
	public function getInteres(){
		return $this->interes;
	}

	/**
	 * Devuelve el valor del campo mora
	 * @param decimal $mora
	 */
	public function getMora(){
		return $this->mora;
	}

	/**
	 * Devuelve el valor del campo fecha_primera_cuota
	 * @return Date
	 */
	public function getFechaPrimeraCuota(){
		return $this->fecha_primera_cuota;
	}

	/**
	 * Devuelve el valor del campo premios_id
	 * @return integer
	 */
	public function getPremiosId(){
		return $this->premios_id;
	}

	/**
	 * Devuelve el valor del campo observaciones
	 * @return string
	 */
	public function getObservaciones(){
		return $this->observaciones;
	}

	/**
	 * Devuelve el valor del campo nota_historia_id
	 * @return integer
	 */
	public function getNotaHistoriaId(){
		return $this->nota_historia_id;
	}

}

