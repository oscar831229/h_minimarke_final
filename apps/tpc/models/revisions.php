<?php

class Revisions extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $source;

	/**
	 * @var integer
	 */
	protected $codusu;

	/**
	 * @var string
	 */
	protected $ipaddress;

	/**
	 * @var integer
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $module;

	/**
	 * @var string
	 */
	protected $server_key;

	/**
	 * @var string
	 */
	protected $checksum;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo db
	 * @param string $db
	 */
	public function setDb($db){
		$this->db = $db;
	}

	/**
	 * Metodo para establecer el valor del campo source
	 * @param string $source
	 */
	public function setSource($source){
		$this->source = $source;
	}

	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
	}

	/**
	 * Metodo para establecer el valor del campo ipaddress
	 * @param string $ipaddress
	 */
	public function setIpaddress($ipaddress){
		$this->ipaddress = $ipaddress;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param integer $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo module
	 * @param string $module
	 */
	public function setModule($module){
		$this->module = $module;
	}

	/**
	 * Metodo para establecer el valor del campo server_key
	 * @param string $server_key
	 */
	public function setServerKey($server_key){
		$this->server_key = $server_key;
	}

	/**
	 * Metodo para establecer el valor del campo checksum
	 * @param string $checksum
	 */
	public function setChecksum($checksum){
		$this->checksum = $checksum;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo db
	 * @return string
	 */
	public function getDb(){
		return $this->db;
	}

	/**
	 * Devuelve el valor del campo source
	 * @return string
	 */
	public function getSource(){
		return $this->source;
	}

	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
	}

	/**
	 * Devuelve el valor del campo ipaddress
	 * @return string
	 */
	public function getIpaddress(){
		return $this->ipaddress;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return integer
	 */
	public function getFecha(){
		return $this->fecha;
	}

	/**
	 * Devuelve el valor del campo module
	 * @return string
	 */
	public function getModule(){
		return $this->module;
	}

	/**
	 * Devuelve el valor del campo server_key
	 * @return string
	 */
	public function getServerKey(){
		return $this->server_key;
	}

	/**
	 * Devuelve el valor del campo checksum
	 * @return string
	 */
	public function getChecksum(){
		return $this->checksum;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSchema('hfos_rcs');
		$this->hasMany('Records');
		$this->belongsTo('codusu', 'Usuarios', 'id');
	}

}

