<?php

class Empresa extends RcsRecord {

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
	 * @var integer
	 */
	protected $ciudades_id;

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
	 * @var string
	 */
	protected $serial;

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var integer
	 */
	protected $creservas;

	/**
	 * @var integer
	 */
	protected $crc;


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
	 * Metodo para establecer el valor del campo ciudades_id
	 * @param integer $ciudades_id
	 */
	public function setCiudadesId($ciudades_id){
		$this->ciudades_id = $ciudades_id;
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
	 * Metodo para establecer el valor del campo serial
	 * @param string $serial
	 */
	public function setSerial($serial){
		$this->serial = $serial;
	}

	/**
	 * Metodo para establecer el valor del campo version
	 * @param string $version
	 */
	public function setVersion($version){
		$this->version = $version;
	}

	/**
	 * Metodo para establecer el valor del campo creservas
	 * @param integer $creservas
	 */
	public function setCreservas($creservas){
		$this->creservas = $creservas;
	}

	/**
	 * Metodo para establecer el valor del campo crc
	 * @param integer $crc
	 */
	public function setCrc($crc){
		$this->crc = $crc;
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
	 * Devuelve el valor del campo ciudades_id
	 * @return integer
	 */
	public function getCiudadesId(){
		return $this->ciudades_id;
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
	 * Devuelve el valor del campo serial
	 * @return string
	 */
	public function getSerial(){
		return $this->serial;
	}

	/**
	 * Devuelve el valor del campo version
	 * @return string
	 */
	public function getVersion(){
		return $this->version;
	}

	/**
	 * Devuelve el valor del campo creservas
	 * @return integer
	 */
	public function getCreservas(){
		return $this->creservas;
	}

	/**
	 * Devuelve el valor del campo crc
	 * @return integer
	 */
	public function getCrc(){
		return $this->crc;
	}
	
	public function beforeValidationOnCreate(){
	    $countRecords = $this->count();
	    if($countRecords > 0){
	        $this->appendMessage(new ActiveRecordMessage('La empresa no puede tener más de un registro'));
	        return false;
	    }
	}

	public function beforeDelete(){
		$this->appendMessage(new ActiveRecordMessage('La empresa no puede eliminarse'));
	    return false;
	}

}

