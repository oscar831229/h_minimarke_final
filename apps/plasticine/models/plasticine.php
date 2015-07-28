<?php

class Plasticine extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $numres;

	/**
	 * @var string
	 */
	protected $clave;

	/**
	 * @var integer
	 */
	protected $codusu;

	/**
	 * @var string
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $modified_in;

	/**
	 * @var string
	 */
	protected $tipdoc;

	/**
	 * @var string
	 */
	protected $cedula;

	/**
	 * @var string
	 */
	protected $lugexp;

	/**
	 * @var string
	 */
	protected $priape;

	/**
	 * @var string
	 */
	protected $segape;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var integer
	 */
	protected $locnac;

	/**
	 * @var Date
	 */
	protected $fecnac;

	/**
	 * @var string
	 */
	protected $direccion;

	/**
	 * @var integer
	 */
	protected $locdir;

	/**
	 * @var string
	 */
	protected $telefono;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $nomemp;

	/**
	 * @var string
	 */
	protected $diremp;

	/**
	 * @var integer
	 */
	protected $locemp;

	/**
	 * @var string
	 */
	protected $telemp;

	/**
	 * @var string
	 */
	protected $emaemp;

	/**
	 * @var integer
	 */
	protected $locpro;

	/**
	 * @var integer
	 */
	protected $codtra;

	/**
	 * @var integer
	 */
	protected $codmot;

	/**
	 * @var string
	 */
	protected $hora;

	/**
	 * @var string
	 */
	protected $nota;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo numres
	 * @param integer $numres
	 */
	public function setNumres($numres){
		$this->numres = $numres;
	}

	/**
	 * Metodo para establecer el valor del campo clave
	 * @param string $clave
	 */
	public function setClave($clave){
		$this->clave = $clave;
	}

	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param string $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo modified_in
	 * @param string $modified_in
	 */
	public function setModifiedIn($modified_in){
		$this->modified_in = $modified_in;
	}

	/**
	 * Metodo para establecer el valor del campo tipdoc
	 * @param string $tipdoc
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
	 * Metodo para establecer el valor del campo lugexp
	 * @param string $lugexp
	 */
	public function setLugexp($lugexp){
		$this->lugexp = $lugexp;
	}

	/**
	 * Metodo para establecer el valor del campo priape
	 * @param string $priape
	 */
	public function setPriape($priape){
		$this->priape = $priape;
	}

	/**
	 * Metodo para establecer el valor del campo segape
	 * @param string $segape
	 */
	public function setSegape($segape){
		$this->segape = $segape;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo locnac
	 * @param integer $locnac
	 */
	public function setLocnac($locnac){
		$this->locnac = $locnac;
	}

	/**
	 * Metodo para establecer el valor del campo fecnac
	 * @param Date $fecnac
	 */
	public function setFecnac($fecnac){
		$this->fecnac = $fecnac;
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
	 * Metodo para establecer el valor del campo telefono
	 * @param string $telefono
	 */
	public function setTelefono($telefono){
		$this->telefono = $telefono;
	}

	/**
	 * Metodo para establecer el valor del campo email
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo nomemp
	 * @param string $nomemp
	 */
	public function setNomemp($nomemp){
		$this->nomemp = $nomemp;
	}

	/**
	 * Metodo para establecer el valor del campo diremp
	 * @param string $diremp
	 */
	public function setDiremp($diremp){
		$this->diremp = $diremp;
	}

	/**
	 * Metodo para establecer el valor del campo locemp
	 * @param integer $locemp
	 */
	public function setLocemp($locemp){
		$this->locemp = $locemp;
	}

	/**
	 * Metodo para establecer el valor del campo telemp
	 * @param string $telemp
	 */
	public function setTelemp($telemp){
		$this->telemp = $telemp;
	}

	/**
	 * Metodo para establecer el valor del campo emaemp
	 * @param string $emaemp
	 */
	public function setEmaemp($emaemp){
		$this->emaemp = $emaemp;
	}

	/**
	 * Metodo para establecer el valor del campo locpro
	 * @param integer $locpro
	 */
	public function setLocpro($locpro){
		$this->locpro = $locpro;
	}

	/**
	 * Metodo para establecer el valor del campo codtra
	 * @param integer $codtra
	 */
	public function setCodtra($codtra){
		$this->codtra = $codtra;
	}

	/**
	 * Metodo para establecer el valor del campo codmot
	 * @param integer $codmot
	 */
	public function setCodmot($codmot){
		$this->codmot = $codmot;
	}

	/**
	 * Metodo para establecer el valor del campo hora
	 * @param string $hora
	 */
	public function setHora($hora){
		$this->hora = $hora;
	}

	/**
	 * Metodo para establecer el valor del campo nota
	 * @param string $nota
	 */
	public function setNota($nota){
		$this->nota = $nota;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo numres
	 * @return integer
	 */
	public function getNumres(){
		return $this->numres;
	}

	/**
	 * Devuelve el valor del campo clave
	 * @return string
	 */
	public function getClave(){
		return $this->clave;
	}

	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return string
	 */
	public function getFecha(){
		return $this->fecha;
	}

	/**
	 * Devuelve el valor del campo modified_in
	 * @return string
	 */
	public function getModifiedIn(){
		return $this->modified_in;
	}

	/**
	 * Devuelve el valor del campo tipdoc
	 * @return string
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
	 * Devuelve el valor del campo lugexp
	 * @return string
	 */
	public function getLugexp(){
		return $this->lugexp;
	}

	/**
	 * Devuelve el valor del campo priape
	 * @return string
	 */
	public function getPriape(){
		return $this->priape;
	}

	/**
	 * Devuelve el valor del campo segape
	 * @return string
	 */
	public function getSegape(){
		return $this->segape;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo locnac
	 * @return integer
	 */
	public function getLocnac(){
		return $this->locnac;
	}

	/**
	 * Devuelve el valor del campo fecnac
	 * @return Date
	 */
	public function getFecnac(){
		return new Date($this->fecnac);
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
	 * Devuelve el valor del campo telefono
	 * @return string
	 */
	public function getTelefono(){
		return $this->telefono;
	}

	/**
	 * Devuelve el valor del campo email
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo nomemp
	 * @return string
	 */
	public function getNomemp(){
		return $this->nomemp;
	}

	/**
	 * Devuelve el valor del campo diremp
	 * @return string
	 */
	public function getDiremp(){
		return $this->diremp;
	}

	/**
	 * Devuelve el valor del campo locemp
	 * @return integer
	 */
	public function getLocemp(){
		return $this->locemp;
	}

	/**
	 * Devuelve el valor del campo telemp
	 * @return string
	 */
	public function getTelemp(){
		return $this->telemp;
	}

	/**
	 * Devuelve el valor del campo emaemp
	 * @return string
	 */
	public function getEmaemp(){
		return $this->emaemp;
	}

	/**
	 * Devuelve el valor del campo locpro
	 * @return integer
	 */
	public function getLocpro(){
		return $this->locpro;
	}

	/**
	 * Devuelve el valor del campo codtra
	 * @return integer
	 */
	public function getCodtra(){
		return $this->codtra;
	}

	/**
	 * Devuelve el valor del campo codmot
	 * @return integer
	 */
	public function getCodmot(){
		return $this->codmot;
	}

	/**
	 * Devuelve el valor del campo hora
	 * @return string
	 */
	public function getHora(){
		return $this->hora;
	}

	/**
	 * Devuelve el valor del campo nota
	 * @return string
	 */
	public function getNota(){
		return $this->nota;
	}

}

