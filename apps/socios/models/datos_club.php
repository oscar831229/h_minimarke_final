<?php

class DatosClub extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

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
	protected $nomcad;

	/**
	 * @var string
	 */
	protected $nomger;

	/**
	 * @var integer
	 */
	protected $ciudad_id;

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
	protected $sitweb;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var integer
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
	 * @var integer
	 */
	protected $numrec;

	/**
	 * @var string
	 */
	protected $imagen;

	/**
	 * @var integer
	 */
	protected $numsoc;

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var Date
	 */
	protected $f_cierre;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

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
	 * Metodo para establecer el valor del campo ciudad_id
	 * @param integer $ciudad_id
	 */
	public function setCiudadId($ciudad_id){
		$this->ciudad_id = $ciudad_id;
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
	 * @param integer $resfac
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
	 * Metodo para establecer el valor del campo numrec
	 * @param integer $numrec
	 */
	public function setNumrec($numrec){
		$this->numrec = $numrec;
	}

	/**
	 * Metodo para establecer el valor del campo imagen
	 * @param string $imagen
	 */
	public function setImagen($imagen){
		$this->imagen = $imagen;
	}

	/**
	 * Metodo para establecer el valor del campo numsoc
	 * @param integer $numsoc
	 */
	public function setNumsoc($numsoc){
		$this->numsoc = $numsoc;
	}

	/**
	 * Metodo para establecer el valor del campo version
	 * @param string $version
	 */
	public function setVersion($version){
		$this->version = $version;
	}

	/**
	 * Metodo para establecer el valor del campo f_cierre
	 * @param Date $f_cierre
	 */
	public function setFCierre($f_cierre){
		$this->f_cierre = $f_cierre;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
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
	 * Devuelve el valor del campo ciudad_id
	 * @return integer
	 */
	public function getCiudadId(){
		return $this->ciudad_id;
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
	 * @return integer
	 */
	public function getResfac(){
		return $this->resfac;
	}

	/**
	 * Devuelve el valor del campo fecfac
	 * @return Date
	 */
	public function getFecfac(){
		if($this->fecfac){
			return new Date($this->fecfac);
		} else {
			return null;
		}
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
	 * Devuelve el valor del campo numrec
	 * @return integer
	 */
	public function getNumrec(){
		return $this->numrec;
	}

	/**
	 * Devuelve el valor del campo imagen
	 * @return string
	 */
	public function getImagen(){
		return $this->imagen;
	}

	/**
	 * Devuelve el valor del campo numsoc
	 * @return integer
	 */
	public function getNumsoc(){
		return $this->numsoc;
	}

	/**
	 * Devuelve el valor del campo version
	 * @return string
	 */
	public function getVersion(){
		return $this->version;
	}

	/**
	 * Devuelve el valor del campo f_cierre
	 * @return Date
	 */
	public function getFCierre(){
		if($this->f_cierre){
			return new Date($this->f_cierre);
		} else {
			return null;
		}
	}

	public function beforeDelete(){
		$this->appendMessage(new ActiveRecordMessage('No se puede borrar los datos del club, solo editar'));
		return false;
	}

	public function beforeNew(){
		$this->appendMessage(new ActiveRecordMessage('No se puede crear un nuevo registro de los datos del club, solo editar'));
		return false;
	}

}

