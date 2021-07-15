<?php

class Magfor extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codfor;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var integer
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $termen;

	/**
	 * @var string
	 */
	protected $terexti;

	/**
	 * @var string
	 */
	protected $terextf;

	/**
	 * @var string
	 */
	protected $ternom;

	/**
	 * @var string
	 */
	protected $minimo;

	/**
	 * @var string
	 */
	protected $campo;


	/**
	 * Metodo para establecer el valor del campo codfor
	 * @param integer $codfor
	 */
	public function setCodfor($codfor){
		$this->codfor = $codfor;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo version
	 * @param integer $version
	 */
	public function setVersion($version){
		$this->version = $version;
	}

	/**
	 * Metodo para establecer el valor del campo termen
	 * @param string $termen
	 */
	public function setTermen($termen){
		$this->termen = $termen;
	}

	/**
	 * Metodo para establecer el valor del campo terexti
	 * @param string $terexti
	 */
	public function setTerexti($terexti){
		$this->terexti = $terexti;
	}

	/**
	 * Metodo para establecer el valor del campo terextf
	 * @param string $terextf
	 */
	public function setTerextf($terextf){
		$this->terextf = $terextf;
	}

	/**
	 * Metodo para establecer el valor del campo ternom
	 * @param string $ternom
	 */
	public function setTernom($ternom){
		$this->ternom = $ternom;
	}

	/**
	 * Metodo para establecer el valor del campo minimo
	 * @param string $minimo
	 */
	public function setMinimo($minimo){
		$this->minimo = $minimo;
	}

	/**
	 * Metodo para establecer el valor del campo campo
	 * @param string $campo
	 */
	public function setCampo($campo){
		$this->campo = $campo;
	}


	/**
	 * Devuelve el valor del campo codfor
	 * @return integer
	 */
	public function getCodfor(){
		return $this->codfor;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo version
	 * @return integer
	 */
	public function getVersion(){
		return $this->version;
	}

	/**
	 * Devuelve el valor del campo termen
	 * @return string
	 */
	public function getTermen(){
		return $this->termen;
	}

	/**
	 * Devuelve el valor del campo terexti
	 * @return string
	 */
	public function getTerexti(){
		return $this->terexti;
	}

	/**
	 * Devuelve el valor del campo terextf
	 * @return string
	 */
	public function getTerextf(){
		return $this->terextf;
	}

	/**
	 * Devuelve el valor del campo ternom
	 * @return string
	 */
	public function getTernom(){
		return $this->ternom;
	}

	/**
	 * Devuelve el valor del campo minimo
	 * @return string
	 */
	public function getMinimo(){
		return $this->minimo;
	}

	/**
	 * Devuelve el valor del campo campo
	 * @return string
	 */
	public function getCampo(){
		return $this->campo;
	}

}

