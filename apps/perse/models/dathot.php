<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Persé
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Dathot extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $tipest;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $nomcad;

	/**
	 * @var string
	 */
	protected $nomger;

	/**
	 * @var integer
	 */
	protected $codpai;

	/**
	 * @var string
	 */
	protected $nomdep;

	/**
	 * @var string
	 */
	protected $nomciu;

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
	protected $apapos;

	/**
	 * @var string
	 */
	protected $sitweb;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $resfac;

	/**
	 * @var Date
	 */
	protected $fecfac;

	/**
	 * @var string
	 */
	protected $prefac;

	/**
	 * @var integer
	 */
	protected $numfac;

	/**
	 * @var integer
	 */
	protected $numfai;

	/**
	 * @var integer
	 */
	protected $numfaf;

	/**
	 * @var string
	 */
	protected $notfac;

	/**
	 * @var string
	 */
	protected $notrec;

	/**
	 * @var string
	 */
	protected $notreg;

	/**
	 * @var string
	 */
	protected $notica;

	/**
	 * @var integer
	 */
	protected $numpre;

	/**
	 * @var integer
	 */
	protected $numrec;

	/**
	 * @var integer
	 */
	protected $numegr;

	/**
	 * @var integer
	 */
	protected $numcam;

	/**
	 * @var integer
	 */
	protected $condas;

	/**
	 * @var string
	 */
	protected $coddas;

	/**
	 * @var integer
	 */
	protected $ciudas;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $location;

	/**
	 * @var string
	 */
	protected $longitude;

	/**
	 * @var string
	 */
	protected $latitude;

	/**
	 * @var string
	 */
	protected $apikey;

	/**
	 * @var string
	 */
	protected $wheater;

	/**
	 * @var string
	 */
	protected $serial;


	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo tipest
	 * @param string $tipest
	 */
	public function setTipest($tipest){
		$this->tipest = $tipest;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo nomcad
	 * @param string $nomcad
	 */
	public function setNomcad($nomcad){
		$this->nomcad = $nomcad;
	}

	/**
	 * Metodo para establecer el valor del campo nomger
	 * @param string $nomger
	 */
	public function setNomger($nomger){
		$this->nomger = $nomger;
	}

	/**
	 * Metodo para establecer el valor del campo codpai
	 * @param integer $codpai
	 */
	public function setCodpai($codpai){
		$this->codpai = $codpai;
	}

	/**
	 * Metodo para establecer el valor del campo nomdep
	 * @param string $nomdep
	 */
	public function setNomdep($nomdep){
		$this->nomdep = $nomdep;
	}

	/**
	 * Metodo para establecer el valor del campo nomciu
	 * @param string $nomciu
	 */
	public function setNomciu($nomciu){
		$this->nomciu = $nomciu;
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
	 * Metodo para establecer el valor del campo apapos
	 * @param string $apapos
	 */
	public function setApapos($apapos){
		$this->apapos = $apapos;
	}

	/**
	 * Metodo para establecer el valor del campo sitweb
	 * @param string $sitweb
	 */
	public function setSitweb($sitweb){
		$this->sitweb = $sitweb;
	}

	/**
	 * Metodo para establecer el valor del campo email
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

	/**
	 * Metodo para establecer el valor del campo resfac
	 * @param string $resfac
	 */
	public function setResfac($resfac){
		$this->resfac = $resfac;
	}

	/**
	 * Metodo para establecer el valor del campo fecfac
	 * @param Date $fecfac
	 */
	public function setFecfac($fecfac){
		$this->fecfac = $fecfac;
	}

	/**
	 * Metodo para establecer el valor del campo prefac
	 * @param string $prefac
	 */
	public function setPrefac($prefac){
		$this->prefac = $prefac;
	}

	/**
	 * Metodo para establecer el valor del campo numfac
	 * @param integer $numfac
	 */
	public function setNumfac($numfac){
		$this->numfac = $numfac;
	}

	/**
	 * Metodo para establecer el valor del campo numfai
	 * @param integer $numfai
	 */
	public function setNumfai($numfai){
		$this->numfai = $numfai;
	}

	/**
	 * Metodo para establecer el valor del campo numfaf
	 * @param integer $numfaf
	 */
	public function setNumfaf($numfaf){
		$this->numfaf = $numfaf;
	}

	/**
	 * Metodo para establecer el valor del campo notfac
	 * @param string $notfac
	 */
	public function setNotfac($notfac){
		$this->notfac = $notfac;
	}

	/**
	 * Metodo para establecer el valor del campo notrec
	 * @param string $notrec
	 */
	public function setNotrec($notrec){
		$this->notrec = $notrec;
	}

	/**
	 * Metodo para establecer el valor del campo notreg
	 * @param string $notreg
	 */
	public function setNotreg($notreg){
		$this->notreg = $notreg;
	}

	/**
	 * Metodo para establecer el valor del campo notica
	 * @param string $notica
	 */
	public function setNotica($notica){
		$this->notica = $notica;
	}

	/**
	 * Metodo para establecer el valor del campo numpre
	 * @param integer $numpre
	 */
	public function setNumpre($numpre){
		$this->numpre = $numpre;
	}

	/**
	 * Metodo para establecer el valor del campo numrec
	 * @param integer $numrec
	 */
	public function setNumrec($numrec){
		$this->numrec = $numrec;
	}

	/**
	 * Metodo para establecer el valor del campo numegr
	 * @param integer $numegr
	 */
	public function setNumegr($numegr){
		$this->numegr = $numegr;
	}

	/**
	 * Metodo para establecer el valor del campo numcam
	 * @param integer $numcam
	 */
	public function setNumcam($numcam){
		$this->numcam = $numcam;
	}

	/**
	 * Metodo para establecer el valor del campo condas
	 * @param integer $condas
	 */
	public function setCondas($condas){
		$this->condas = $condas;
	}

	/**
	 * Metodo para establecer el valor del campo coddas
	 * @param string $coddas
	 */
	public function setCoddas($coddas){
		$this->coddas = $coddas;
	}

	/**
	 * Metodo para establecer el valor del campo ciudas
	 * @param integer $ciudas
	 */
	public function setCiudas($ciudas){
		$this->ciudas = $ciudas;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo location
	 * @param string $location
	 */
	public function setLocation($location){
		$this->location = $location;
	}

	/**
	 * Metodo para establecer el valor del campo longitude
	 * @param string $longitude
	 */
	public function setLongitude($longitude){
		$this->longitude = $longitude;
	}

	/**
	 * Metodo para establecer el valor del campo latitude
	 * @param string $latitude
	 */
	public function setLatitude($latitude){
		$this->latitude = $latitude;
	}

	/**
	 * Metodo para establecer el valor del campo apikey
	 * @param string $apikey
	 */
	public function setApikey($apikey){
		$this->apikey = $apikey;
	}

	/**
	 * Metodo para establecer el valor del campo wheater
	 * @param string $wheater
	 */
	public function setWheater($wheater){
		$this->wheater = $wheater;
	}

	/**
	 * Metodo para establecer el valor del campo serial
	 * @param string $serial
	 */
	public function setSerial($serial){
		$this->serial = $serial;
	}


	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo tipest
	 * @return string
	 */
	public function getTipest(){
		return $this->tipest;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo nomcad
	 * @return string
	 */
	public function getNomcad(){
		return $this->nomcad;
	}

	/**
	 * Devuelve el valor del campo nomger
	 * @return string
	 */
	public function getNomger(){
		return $this->nomger;
	}

	/**
	 * Devuelve el valor del campo codpai
	 * @return integer
	 */
	public function getCodpai(){
		return $this->codpai;
	}

	/**
	 * Devuelve el valor del campo nomdep
	 * @return string
	 */
	public function getNomdep(){
		return $this->nomdep;
	}

	/**
	 * Devuelve el valor del campo nomciu
	 * @return string
	 */
	public function getNomciu(){
		return $this->nomciu;
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
	 * Devuelve el valor del campo apapos
	 * @return string
	 */
	public function getApapos(){
		return $this->apapos;
	}

	/**
	 * Devuelve el valor del campo sitweb
	 * @return string
	 */
	public function getSitweb(){
		return $this->sitweb;
	}

	/**
	 * Devuelve el valor del campo email
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Devuelve el valor del campo resfac
	 * @return string
	 */
	public function getResfac(){
		return $this->resfac;
	}

	/**
	 * Devuelve el valor del campo fecfac
	 * @return Date
	 */
	public function getFecfac(){
		return new Date($this->fecfac);
	}

	/**
	 * Devuelve el valor del campo prefac
	 * @return string
	 */
	public function getPrefac(){
		return $this->prefac;
	}

	/**
	 * Devuelve el valor del campo numfac
	 * @return integer
	 */
	public function getNumfac(){
		return $this->numfac;
	}

	/**
	 * Devuelve el valor del campo numfai
	 * @return integer
	 */
	public function getNumfai(){
		return $this->numfai;
	}

	/**
	 * Devuelve el valor del campo numfaf
	 * @return integer
	 */
	public function getNumfaf(){
		return $this->numfaf;
	}

	/**
	 * Devuelve el valor del campo notfac
	 * @return string
	 */
	public function getNotfac(){
		return $this->notfac;
	}

	/**
	 * Devuelve el valor del campo notrec
	 * @return string
	 */
	public function getNotrec(){
		return $this->notrec;
	}

	/**
	 * Devuelve el valor del campo notreg
	 * @return string
	 */
	public function getNotreg(){
		return $this->notreg;
	}

	/**
	 * Devuelve el valor del campo notica
	 * @return string
	 */
	public function getNotica(){
		return $this->notica;
	}

	/**
	 * Devuelve el valor del campo numpre
	 * @return integer
	 */
	public function getNumpre(){
		return $this->numpre;
	}

	/**
	 * Devuelve el valor del campo numrec
	 * @return integer
	 */
	public function getNumrec(){
		return $this->numrec;
	}

	/**
	 * Devuelve el valor del campo numegr
	 * @return integer
	 */
	public function getNumegr(){
		return $this->numegr;
	}

	/**
	 * Devuelve el valor del campo numcam
	 * @return integer
	 */
	public function getNumcam(){
		return $this->numcam;
	}

	/**
	 * Devuelve el valor del campo condas
	 * @return integer
	 */
	public function getCondas(){
		return $this->condas;
	}

	/**
	 * Devuelve el valor del campo coddas
	 * @return string
	 */
	public function getCoddas(){
		return $this->coddas;
	}

	/**
	 * Devuelve el valor del campo ciudas
	 * @return integer
	 */
	public function getCiudas(){
		return $this->ciudas;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo location
	 * @return string
	 */
	public function getLocation(){
		return $this->location;
	}

	/**
	 * Devuelve el valor del campo longitude
	 * @return string
	 */
	public function getLongitude(){
		return $this->longitude;
	}

	/**
	 * Devuelve el valor del campo latitude
	 * @return string
	 */
	public function getLatitude(){
		return $this->latitude;
	}

	/**
	 * Devuelve el valor del campo apikey
	 * @return string
	 */
	public function getApikey(){
		return $this->apikey;
	}

	/**
	 * Devuelve el valor del campo wheater
	 * @return string
	 */
	public function getWheater(){
		return $this->wheater;
	}

	/**
	 * Devuelve el valor del campo serial
	 * @return string
	 */
	public function getSerial(){
		return $this->serial;
	}

}

