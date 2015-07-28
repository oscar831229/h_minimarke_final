<?php

class Apofol extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numfol;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $tipdoc;

	/**
	 * @var string
	 */
	protected $cedula;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $lugexp;

	/**
	 * @var integer
	 */
	protected $codnac;

	/**
	 * @var integer
	 */
	protected $locnac;

	/**
	 * @var integer
	 */
	protected $codpro;

	/**
	 * @var string
	 */
	protected $sexo;

	/**
	 * @var Date
	 */
	protected $fecnac;

	/**
	 * @var Date
	 */
	protected $feclle;

	/**
	 * @var Date
	 */
	protected $fecsal;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo numfol
	 * @param integer $numfol
	 */
	public function setNumfol($numfol){
		$this->numfol = $numfol;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo tipdoc
	 * @param integer $tipdoc
	 */
	public function setTipdoc($tipdoc){
		$this->tipdoc = $tipdoc;
	}

	/**
	 * Metodo para establecer el valor del campo cedula
	 * @param string $cedula
	 */
	public function setCedula($cedula){
		$this->cedula = $cedula;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo lugexp
	 * @param string $lugexp
	 */
	public function setLugexp($lugexp){
		$this->lugexp = $lugexp;
	}

	/**
	 * Metodo para establecer el valor del campo codnac
	 * @param integer $codnac
	 */
	public function setCodnac($codnac){
		$this->codnac = $codnac;
	}

	/**
	 * Metodo para establecer el valor del campo locnac
	 * @param integer $locnac
	 */
	public function setLocnac($locnac){
		$this->locnac = $locnac;
	}

	/**
	 * Metodo para establecer el valor del campo codpro
	 * @param integer $codpro
	 */
	public function setCodpro($codpro){
		$this->codpro = $codpro;
	}

	/**
	 * Metodo para establecer el valor del campo sexo
	 * @param string $sexo
	 */
	public function setSexo($sexo){
		$this->sexo = $sexo;
	}

	/**
	 * Metodo para establecer el valor del campo fecnac
	 * @param Date $fecnac
	 */
	public function setFecnac($fecnac){
		$this->fecnac = $fecnac;
	}

	/**
	 * Metodo para establecer el valor del campo feclle
	 * @param Date $feclle
	 */
	public function setFeclle($feclle){
		$this->feclle = $feclle;
	}

	/**
	 * Metodo para establecer el valor del campo fecsal
	 * @param Date $fecsal
	 */
	public function setFecsal($fecsal){
		$this->fecsal = $fecsal;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo numfol
	 * @return integer
	 */
	public function getNumfol(){
		return $this->numfol;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo tipdoc
	 * @return integer
	 */
	public function getTipdoc(){
		return $this->tipdoc;
	}

	/**
	 * Devuelve el valor del campo cedula
	 * @return string
	 */
	public function getCedula(){
		return $this->cedula;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo lugexp
	 * @return string
	 */
	public function getLugexp(){
		return $this->lugexp;
	}

	/**
	 * Devuelve el valor del campo codnac
	 * @return integer
	 */
	public function getCodnac(){
		return $this->codnac;
	}

	/**
	 * Devuelve el valor del campo locnac
	 * @return integer
	 */
	public function getLocnac(){
		return $this->locnac;
	}

	/**
	 * Devuelve el valor del campo codpro
	 * @return integer
	 */
	public function getCodpro(){
		return $this->codpro;
	}

	/**
	 * Devuelve el valor del campo sexo
	 * @return string
	 */
	public function getSexo(){
		return $this->sexo;
	}

	/**
	 * Devuelve el valor del campo fecnac
	 * @return Date
	 */
	public function getFecnac(){
		if($this->fecnac){
			return new Date($this->fecnac);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo feclle
	 * @return Date
	 */
	public function getFeclle(){
		if($this->feclle){
			return new Date($this->feclle);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecsal
	 * @return Date
	 */
	public function getFecsal(){
		if($this->fecsal){
			return new Date($this->fecsal);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

