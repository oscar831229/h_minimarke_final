<?php

class Usuarios extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codusu;

	/**
	 * @var integer
	 */
	protected $codsuc;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $login;

	/**
	 * @var string
	 */
	protected $pass;

	/**
	 * @var string
	 */
	protected $foto;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $telefono;

	/**
	 * @var string
	 */
	protected $genero;

	/**
	 * @var integer
	 */
	protected $codprf;

	/**
	 * @var integer
	 */
	protected $ultlog;

	/**
	 * @var string
	 */
	protected $ipaddress;

	/**
	 * @var string
	 */
	protected $status;

	/**
	 * @var integer
	 */
	protected $writing;

	/**
	 * @var Date
	 */
	protected $camcla;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
	}

	/**
	 * Metodo para establecer el valor del campo codsuc
	 * @param integer $codsuc
	 */
	public function setCodsuc($codsuc){
		$this->codsuc = $codsuc;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo login
	 * @param string $login
	 */
	public function setLogin($login){
		$this->login = $login;
	}

	/**
	 * Metodo para establecer el valor del campo pass
	 * @param string $pass
	 */
	public function setPass($pass){
		$this->pass = $pass;
	}

	/**
	 * Metodo para establecer el valor del campo foto
	 * @param string $foto
	 */
	public function setFoto($foto){
		$this->foto = $foto;
	}

	/**
	 * Metodo para establecer el valor del campo email
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

	/**
	 * Metodo para establecer el valor del campo telefono
	 * @param string $telefono
	 */
	public function setTelefono($telefono){
		$this->telefono = $telefono;
	}

	/**
	 * Metodo para establecer el valor del campo genero
	 * @param string $genero
	 */
	public function setGenero($genero){
		$this->genero = $genero;
	}

	/**
	 * Metodo para establecer el valor del campo codprf
	 * @param integer $codprf
	 */
	public function setCodprf($codprf){
		$this->codprf = $codprf;
	}

	/**
	 * Metodo para establecer el valor del campo ultlog
	 * @param integer $ultlog
	 */
	public function setUltlog($ultlog){
		$this->ultlog = $ultlog;
	}

	/**
	 * Metodo para establecer el valor del campo ipaddress
	 * @param string $ipaddress
	 */
	public function setIpaddress($ipaddress){
		$this->ipaddress = $ipaddress;
	}

	/**
	 * Metodo para establecer el valor del campo status
	 * @param string $status
	 */
	public function setStatus($status){
		$this->status = $status;
	}

	/**
	 * Metodo para establecer el valor del campo writing
	 * @param integer $writing
	 */
	public function setWriting($writing){
		$this->writing = $writing;
	}

	/**
	 * Metodo para establecer el valor del campo camcla
	 * @param Date $camcla
	 */
	public function setCamcla($camcla){
		$this->camcla = $camcla;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
	}

	/**
	 * Devuelve el valor del campo codsuc
	 * @return integer
	 */
	public function getCodsuc(){
		return $this->codsuc;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo login
	 * @return string
	 */
	public function getLogin(){
		return $this->login;
	}

	/**
	 * Devuelve el valor del campo pass
	 * @return string
	 */
	public function getPass(){
		return $this->pass;
	}

	/**
	 * Devuelve el valor del campo foto
	 * @return string
	 */
	public function getFoto(){
		return $this->foto;
	}

	/**
	 * Devuelve el valor del campo email
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Devuelve el valor del campo telefono
	 * @return string
	 */
	public function getTelefono(){
		return $this->telefono;
	}

	/**
	 * Devuelve el valor del campo genero
	 * @return string
	 */
	public function getGenero(){
		return $this->genero;
	}

	/**
	 * Devuelve el valor del campo codprf
	 * @return integer
	 */
	public function getCodprf(){
		return $this->codprf;
	}

	/**
	 * Devuelve el valor del campo ultlog
	 * @return integer
	 */
	public function getUltlog(){
		return $this->ultlog;
	}

	/**
	 * Devuelve el valor del campo ipaddress
	 * @return string
	 */
	public function getIpaddress(){
		return $this->ipaddress;
	}

	/**
	 * Devuelve el valor del campo status
	 * @return string
	 */
	public function getStatus(){
		return $this->status;
	}

	/**
	 * Devuelve el valor del campo writing
	 * @return integer
	 */
	public function getWriting(){
		return $this->writing;
	}

	/**
	 * Devuelve el valor del campo camcla
	 * @return Date
	 */
	public function getCamcla(){
		if($this->camcla){
			return new Date($this->camcla);
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

	public function initialize(){
		$this->hasMany('codusu', 'Camarera', 'codusu');
	}

}

