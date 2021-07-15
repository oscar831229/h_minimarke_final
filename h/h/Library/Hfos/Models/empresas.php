<?php

class Empresas extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $razsoc;

	/**
	 * @var integer
	 */
	protected $locdir;

	/**
	 * @var integer
	 */
	protected $codpai;

	/**
	 * @var integer
	 */
	protected $codciu;

	/**
	 * @var string
	 */
	protected $direccion;

	/**
	 * @var string
	 */
	protected $telefono;

	/**
	 * @var string
	 */
	protected $fax;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $sitweb;

	/**
	 * @var string
	 */
	protected $encargado;

	/**
	 * @var integer
	 */
	protected $codact;

	/**
	 * @var string
	 */
	protected $autoretenedor;

	/**
	 * @var string
	 */
	protected $tipreg;

	/**
	 * @var string
	 */
	protected $credito;

	/**
	 * @var integer
	 */
	protected $diaven;

	/**
	 * @var string
	 */
	protected $cupo;

	/**
	 * @var integer
	 */
	protected $diacor;

	/**
	 * @var string
	 */
	protected $exento;

	/**
	 * @var string
	 */
	protected $cuepla;

	/**
	 * @var string
	 */
	protected $actint;

	/**
	 * @var string
	 */
	protected $observacion;

	/**
	 * @var integer
	 */
	protected $codven;

	/**
	 * @var integer
	 */
	protected $forpag;

	/**
	 * @var string
	 */
	protected $estsis;


	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo razsoc
	 * @param string $razsoc
	 */
	public function setRazsoc($razsoc){
		$this->razsoc = $razsoc;
	}

	/**
	 * Metodo para establecer el valor del campo locdir
	 * @param integer $locdir
	 */
	public function setLocdir($locdir){
		$this->locdir = $locdir;
	}

	/**
	 * Metodo para establecer el valor del campo codpai
	 * @param integer $codpai
	 */
	public function setCodpai($codpai){
		$this->codpai = $codpai;
	}

	/**
	 * Metodo para establecer el valor del campo codciu
	 * @param integer $codciu
	 */
	public function setCodciu($codciu){
		$this->codciu = $codciu;
	}

	/**
	 * Metodo para establecer el valor del campo direccion
	 * @param string $direccion
	 */
	public function setDireccion($direccion){
		$this->direccion = $direccion;
	}

	/**
	 * Metodo para establecer el valor del campo telefono
	 * @param string $telefono
	 */
	public function setTelefono($telefono){
		$this->telefono = $telefono;
	}

	/**
	 * Metodo para establecer el valor del campo fax
	 * @param string $fax
	 */
	public function setFax($fax){
		$this->fax = $fax;
	}

	/**
	 * Metodo para establecer el valor del campo email
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

	/**
	 * Metodo para establecer el valor del campo sitweb
	 * @param string $sitweb
	 */
	public function setSitweb($sitweb){
		$this->sitweb = $sitweb;
	}

	/**
	 * Metodo para establecer el valor del campo encargado
	 * @param string $encargado
	 */
	public function setEncargado($encargado){
		$this->encargado = $encargado;
	}

	/**
	 * Metodo para establecer el valor del campo codact
	 * @param integer $codact
	 */
	public function setCodact($codact){
		$this->codact = $codact;
	}

	/**
	 * Metodo para establecer el valor del campo autoretenedor
	 * @param string $autoretenedor
	 */
	public function setAutoretenedor($autoretenedor){
		$this->autoretenedor = $autoretenedor;
	}

	/**
	 * Metodo para establecer el valor del campo tipreg
	 * @param string $tipreg
	 */
	public function setTipreg($tipreg){
		$this->tipreg = $tipreg;
	}

	/**
	 * Metodo para establecer el valor del campo credito
	 * @param string $credito
	 */
	public function setCredito($credito){
		$this->credito = $credito;
	}

	/**
	 * Metodo para establecer el valor del campo diaven
	 * @param integer $diaven
	 */
	public function setDiaven($diaven){
		$this->diaven = $diaven;
	}

	/**
	 * Metodo para establecer el valor del campo cupo
	 * @param string $cupo
	 */
	public function setCupo($cupo){
		$this->cupo = $cupo;
	}

	/**
	 * Metodo para establecer el valor del campo diacor
	 * @param integer $diacor
	 */
	public function setDiacor($diacor){
		$this->diacor = $diacor;
	}

	/**
	 * Metodo para establecer el valor del campo exento
	 * @param string $exento
	 */
	public function setExento($exento){
		$this->exento = $exento;
	}

	/**
	 * Metodo para establecer el valor del campo cuepla
	 * @param string $cuepla
	 */
	public function setCuepla($cuepla){
		$this->cuepla = $cuepla;
	}

	/**
	 * Metodo para establecer el valor del campo actint
	 * @param string $actint
	 */
	public function setActint($actint){
		$this->actint = $actint;
	}

	/**
	 * Metodo para establecer el valor del campo observacion
	 * @param string $observacion
	 */
	public function setObservacion($observacion){
		$this->observacion = $observacion;
	}

	/**
	 * Metodo para establecer el valor del campo codven
	 * @param integer $codven
	 */
	public function setCodven($codven){
		$this->codven = $codven;
	}

	/**
	 * Metodo para establecer el valor del campo forpag
	 * @param integer $forpag
	 */
	public function setForpag($forpag){
		$this->forpag = $forpag;
	}

	/**
	 * Metodo para establecer el valor del campo estsis
	 * @param string $estsis
	 */
	public function setEstsis($estsis){
		$this->estsis = $estsis;
	}


	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo razsoc
	 * @return string
	 */
	public function getRazsoc(){
		return $this->razsoc;
	}

	/**
	 * Devuelve el valor del campo locdir
	 * @return integer
	 */
	public function getLocdir(){
		return $this->locdir;
	}

	/**
	 * Devuelve el valor del campo codpai
	 * @return integer
	 */
	public function getCodpai(){
		return $this->codpai;
	}

	/**
	 * Devuelve el valor del campo codciu
	 * @return integer
	 */
	public function getCodciu(){
		return $this->codciu;
	}

	/**
	 * Devuelve el valor del campo direccion
	 * @return string
	 */
	public function getDireccion(){
		return $this->direccion;
	}

	/**
	 * Devuelve el valor del campo telefono
	 * @return string
	 */
	public function getTelefono(){
		return $this->telefono;
	}

	/**
	 * Devuelve el valor del campo fax
	 * @return string
	 */
	public function getFax(){
		return $this->fax;
	}

	/**
	 * Devuelve el valor del campo email
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Devuelve el valor del campo sitweb
	 * @return string
	 */
	public function getSitweb(){
		return $this->sitweb;
	}

	/**
	 * Devuelve el valor del campo encargado
	 * @return string
	 */
	public function getEncargado(){
		return $this->encargado;
	}

	/**
	 * Devuelve el valor del campo codact
	 * @return integer
	 */
	public function getCodact(){
		return $this->codact;
	}

	/**
	 * Devuelve el valor del campo autoretenedor
	 * @return string
	 */
	public function getAutoretenedor(){
		return $this->autoretenedor;
	}

	/**
	 * Devuelve el valor del campo tipreg
	 * @return string
	 */
	public function getTipreg(){
		return $this->tipreg;
	}

	/**
	 * Devuelve el valor del campo credito
	 * @return string
	 */
	public function getCredito(){
		return $this->credito;
	}

	/**
	 * Devuelve el valor del campo diaven
	 * @return integer
	 */
	public function getDiaven(){
		return $this->diaven;
	}

	/**
	 * Devuelve el valor del campo cupo
	 * @return string
	 */
	public function getCupo(){
		return $this->cupo;
	}

	/**
	 * Devuelve el valor del campo diacor
	 * @return integer
	 */
	public function getDiacor(){
		return $this->diacor;
	}

	/**
	 * Devuelve el valor del campo exento
	 * @return string
	 */
	public function getExento(){
		return $this->exento;
	}

	/**
	 * Devuelve el valor del campo cuepla
	 * @return string
	 */
	public function getCuepla(){
		return $this->cuepla;
	}

	/**
	 * Devuelve el valor del campo actint
	 * @return string
	 */
	public function getActint(){
		return $this->actint;
	}

	/**
	 * Devuelve el valor del campo observacion
	 * @return string
	 */
	public function getObservacion(){
		return $this->observacion;
	}

	/**
	 * Devuelve el valor del campo codven
	 * @return integer
	 */
	public function getCodven(){
		return $this->codven;
	}

	/**
	 * Devuelve el valor del campo forpag
	 * @return integer
	 */
	public function getForpag(){
		return $this->forpag;
	}

	/**
	 * Devuelve el valor del campo estsis
	 * @return string
	 */
	public function getEstsis(){
		return $this->estsis;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){		
		$this->setSchema("hotel2");
	}

}

