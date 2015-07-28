<?php

class Plares extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numres;

	/**
	 * @var integer
	 */
	protected $numpla;

	/**
	 * @var integer
	 */
	protected $codpla;

	/**
	 * @var Date
	 */
	protected $fecini;

	/**
	 * @var Date
	 */
	protected $fecfin;


	/**
	 * Metodo para establecer el valor del campo numres
	 * @param integer $numres
	 */
	public function setNumres($numres){
		$this->numres = $numres;
	}

	/**
	 * Metodo para establecer el valor del campo numpla
	 * @param integer $numpla
	 */
	public function setNumpla($numpla){
		$this->numpla = $numpla;
	}

	/**
	 * Metodo para establecer el valor del campo codpla
	 * @param integer $codpla
	 */
	public function setCodpla($codpla){
		$this->codpla = $codpla;
	}

	/**
	 * Metodo para establecer el valor del campo fecini
	 * @param Date $fecini
	 */
	public function setFecini($fecini){
		$this->fecini = $fecini;
	}

	/**
	 * Metodo para establecer el valor del campo fecfin
	 * @param Date $fecfin
	 */
	public function setFecfin($fecfin){
		$this->fecfin = $fecfin;
	}


	/**
	 * Devuelve el valor del campo numres
	 * @return integer
	 */
	public function getNumres(){
		return $this->numres;
	}

	/**
	 * Devuelve el valor del campo numpla
	 * @return integer
	 */
	public function getNumpla(){
		return $this->numpla;
	}

	/**
	 * Devuelve el valor del campo codpla
	 * @return integer
	 */
	public function getCodpla(){
		return $this->codpla;
	}

	/**
	 * Devuelve el valor del campo fecini
	 * @return Date
	 */
	public function getFecini(){
		if($this->fecini){
			return new Date($this->fecini);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecfin
	 * @return Date
	 */
	public function getFecfin(){
		if($this->fecfin){
			return new Date($this->fecfin);
		} else {
			return null;
		}
	}

}

