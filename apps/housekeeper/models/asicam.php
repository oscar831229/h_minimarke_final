<?php

class Asicam extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codcam;

	/**
	 * @var string
	 */
	protected $numhab;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $horini;

	/**
	 * @var string
	 */
	protected $horfin;

	/**
	 * @var string
	 */
	protected $observacion;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo codcam
	 * @param integer $codcam
	 */
	public function setCodcam($codcam){
		$this->codcam = $codcam;
	}

	/**
	 * Metodo para establecer el valor del campo numhab
	 * @param string $numhab
	 */
	public function setNumhab($numhab){
		$this->numhab = $numhab;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo horini
	 * @param string $horini
	 */
	public function setHorini($horini){
		$this->horini = $horini;
	}

	/**
	 * Metodo para establecer el valor del campo horfin
	 * @param string $horfin
	 */
	public function setHorfin($horfin){
		$this->horfin = $horfin;
	}

	/**
	 * Metodo para establecer el valor del campo observacion
	 * @param string $observacion
	 */
	public function setObservacion($observacion){
		$this->observacion = $observacion;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo codcam
	 * @return integer
	 */
	public function getCodcam(){
		return $this->codcam;
	}

	/**
	 * Devuelve el valor del campo numhab
	 * @return string
	 */
	public function getNumhab(){
		return $this->numhab;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		if($this->fecha){
			return new Date($this->fecha);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo horini
	 * @return string
	 */
	public function getHorini(){
		return $this->horini;
	}

	/**
	 * Devuelve el valor del campo horfin
	 * @return string
	 */
	public function getHorfin(){
		return $this->horfin;
	}

	/**
	 * Devuelve el valor del campo observacion
	 * @return string
	 */
	public function getObservacion(){
		return $this->observacion;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function initialize(){
		$this->belongsTo('numhab', 'Habitacion', 'numhab');
	}

}

