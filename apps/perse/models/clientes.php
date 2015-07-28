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

class Clientes extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $cedula;

	/**
	 * @var integer
	 */
	protected $tipdoc;

	/**
	 * @var string
	 */
	protected $lugexp;

	/**
	 * @var string
	 */
	protected $categoria;

	/**
	 * @var string
	 */
	protected $accion;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $sexo;

	/**
	 * @var string
	 */
	protected $telefono1;

	/**
	 * @var string
	 */
	protected $telefono2;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $direccion;

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
	 * @var Date
	 */
	protected $fecnac;

	/**
	 * @var integer
	 */
	protected $locnac;

	/**
	 * @var integer
	 */
	protected $codnac;

	/**
	 * @var integer
	 */
	protected $codpro;

	/**
	 * @var Date
	 */
	protected $ultest;

	/**
	 * @var integer
	 */
	protected $numest;

	/**
	 * @var Date
	 */
	protected $feccre;

	/**
	 * @var integer
	 */
	protected $tipcli;

	/**
	 * @var string
	 */
	protected $credito;

	/**
	 * @var string
	 */
	protected $cupo;

	/**
	 * @var integer
	 */
	protected $diaven;

	/**
	 * @var string
	 */
	protected $cuepla;

	/**
	 * @var string
	 */
	protected $clides;

	/**
	 * @var string
	 */
	protected $actint;

	/**
	 * @var string
	 */
	protected $tipinf;

	/**
	 * @var string
	 */
	protected $observacion;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $estsis;


	/**
	 * Metodo para establecer el valor del campo cedula
	 * @param string $cedula
	 */
	public function setCedula($cedula){
		$this->cedula = $cedula;
	}

	/**
	 * Metodo para establecer el valor del campo tipdoc
	 * @param integer $tipdoc
	 */
	public function setTipdoc($tipdoc){
		$this->tipdoc = $tipdoc;
	}

	/**
	 * Metodo para establecer el valor del campo lugexp
	 * @param string $lugexp
	 */
	public function setLugexp($lugexp){
		$this->lugexp = $lugexp;
	}

	/**
	 * Metodo para establecer el valor del campo categoria
	 * @param string $categoria
	 */
	public function setCategoria($categoria){
		$this->categoria = $categoria;
	}

	/**
	 * Metodo para establecer el valor del campo accion
	 * @param string $accion
	 */
	public function setAccion($accion){
		$this->accion = $accion;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo sexo
	 * @param string $sexo
	 */
	public function setSexo($sexo){
		$this->sexo = $sexo;
	}

	/**
	 * Metodo para establecer el valor del campo telefono1
	 * @param string $telefono1
	 */
	public function setTelefono1($telefono1){
		$this->telefono1 = $telefono1;
	}

	/**
	 * Metodo para establecer el valor del campo telefono2
	 * @param string $telefono2
	 */
	public function setTelefono2($telefono2){
		$this->telefono2 = $telefono2;
	}

	/**
	 * Metodo para establecer el valor del campo email
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

	/**
	 * Metodo para establecer el valor del campo direccion
	 * @param string $direccion
	 */
	public function setDireccion($direccion){
		$this->direccion = $direccion;
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
	 * Metodo para establecer el valor del campo fecnac
	 * @param Date $fecnac
	 */
	public function setFecnac($fecnac){
		$this->fecnac = $fecnac;
	}

	/**
	 * Metodo para establecer el valor del campo locnac
	 * @param integer $locnac
	 */
	public function setLocnac($locnac){
		$this->locnac = $locnac;
	}

	/**
	 * Metodo para establecer el valor del campo codnac
	 * @param integer $codnac
	 */
	public function setCodnac($codnac){
		$this->codnac = $codnac;
	}

	/**
	 * Metodo para establecer el valor del campo codpro
	 * @param integer $codpro
	 */
	public function setCodpro($codpro){
		$this->codpro = $codpro;
	}

	/**
	 * Metodo para establecer el valor del campo ultest
	 * @param Date $ultest
	 */
	public function setUltest($ultest){
		$this->ultest = $ultest;
	}

	/**
	 * Metodo para establecer el valor del campo numest
	 * @param integer $numest
	 */
	public function setNumest($numest){
		$this->numest = $numest;
	}

	/**
	 * Metodo para establecer el valor del campo feccre
	 * @param Date $feccre
	 */
	public function setFeccre($feccre){
		$this->feccre = $feccre;
	}

	/**
	 * Metodo para establecer el valor del campo tipcli
	 * @param integer $tipcli
	 */
	public function setTipcli($tipcli){
		$this->tipcli = $tipcli;
	}

	/**
	 * Metodo para establecer el valor del campo credito
	 * @param string $credito
	 */
	public function setCredito($credito){
		$this->credito = $credito;
	}

	/**
	 * Metodo para establecer el valor del campo cupo
	 * @param string $cupo
	 */
	public function setCupo($cupo){
		$this->cupo = $cupo;
	}

	/**
	 * Metodo para establecer el valor del campo diaven
	 * @param integer $diaven
	 */
	public function setDiaven($diaven){
		$this->diaven = $diaven;
	}

	/**
	 * Metodo para establecer el valor del campo cuepla
	 * @param string $cuepla
	 */
	public function setCuepla($cuepla){
		$this->cuepla = $cuepla;
	}

	/**
	 * Metodo para establecer el valor del campo clides
	 * @param string $clides
	 */
	public function setClides($clides){
		$this->clides = $clides;
	}

	/**
	 * Metodo para establecer el valor del campo actint
	 * @param string $actint
	 */
	public function setActint($actint){
		$this->actint = $actint;
	}

	/**
	 * Metodo para establecer el valor del campo tipinf
	 * @param string $tipinf
	 */
	public function setTipinf($tipinf){
		$this->tipinf = $tipinf;
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
	 * Metodo para establecer el valor del campo estsis
	 * @param string $estsis
	 */
	public function setEstsis($estsis){
		$this->estsis = $estsis;
	}


	/**
	 * Devuelve el valor del campo cedula
	 * @return string
	 */
	public function getCedula(){
		return $this->cedula;
	}

	/**
	 * Devuelve el valor del campo tipdoc
	 * @return integer
	 */
	public function getTipdoc(){
		return $this->tipdoc;
	}

	/**
	 * Devuelve el valor del campo lugexp
	 * @return string
	 */
	public function getLugexp(){
		return $this->lugexp;
	}

	/**
	 * Devuelve el valor del campo categoria
	 * @return string
	 */
	public function getCategoria(){
		return $this->categoria;
	}

	/**
	 * Devuelve el valor del campo accion
	 * @return string
	 */
	public function getAccion(){
		return $this->accion;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo sexo
	 * @return string
	 */
	public function getSexo(){
		return $this->sexo;
	}

	/**
	 * Devuelve el valor del campo telefono1
	 * @return string
	 */
	public function getTelefono1(){
		return $this->telefono1;
	}

	/**
	 * Devuelve el valor del campo telefono2
	 * @return string
	 */
	public function getTelefono2(){
		return $this->telefono2;
	}

	/**
	 * Devuelve el valor del campo email
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Devuelve el valor del campo direccion
	 * @return string
	 */
	public function getDireccion(){
		return $this->direccion;
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
	 * Devuelve el valor del campo fecnac
	 * @return Date
	 */
	public function getFecnac(){
		return new Date($this->fecnac);
	}

	/**
	 * Devuelve el valor del campo locnac
	 * @return integer
	 */
	public function getLocnac(){
		return $this->locnac;
	}

	/**
	 * Devuelve el valor del campo codnac
	 * @return integer
	 */
	public function getCodnac(){
		return $this->codnac;
	}

	/**
	 * Devuelve el valor del campo codpro
	 * @return integer
	 */
	public function getCodpro(){
		return $this->codpro;
	}

	/**
	 * Devuelve el valor del campo ultest
	 * @return Date
	 */
	public function getUltest(){
		return new Date($this->ultest);
	}

	/**
	 * Devuelve el valor del campo numest
	 * @return integer
	 */
	public function getNumest(){
		return $this->numest;
	}

	/**
	 * Devuelve el valor del campo feccre
	 * @return Date
	 */
	public function getFeccre(){
		return new Date($this->feccre);
	}

	/**
	 * Devuelve el valor del campo tipcli
	 * @return integer
	 */
	public function getTipcli(){
		return $this->tipcli;
	}

	/**
	 * Devuelve el valor del campo credito
	 * @return string
	 */
	public function getCredito(){
		return $this->credito;
	}

	/**
	 * Devuelve el valor del campo cupo
	 * @return string
	 */
	public function getCupo(){
		return $this->cupo;
	}

	/**
	 * Devuelve el valor del campo diaven
	 * @return integer
	 */
	public function getDiaven(){
		return $this->diaven;
	}

	/**
	 * Devuelve el valor del campo cuepla
	 * @return string
	 */
	public function getCuepla(){
		return $this->cuepla;
	}

	/**
	 * Devuelve el valor del campo clides
	 * @return string
	 */
	public function getClides(){
		return $this->clides;
	}

	/**
	 * Devuelve el valor del campo actint
	 * @return string
	 */
	public function getActint(){
		return $this->actint;
	}

	/**
	 * Devuelve el valor del campo tipinf
	 * @return string
	 */
	public function getTipinf(){
		return $this->tipinf;
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

	/**
	 * Devuelve el valor del campo estsis
	 * @return string
	 */
	public function getEstsis(){
		return $this->estsis;
	}

}

