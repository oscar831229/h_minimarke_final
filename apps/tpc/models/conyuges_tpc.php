<?php

class ConyugesTpc extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_tpc_id;

	/**
	 * @var string
	 */
	protected $apellidos;

	/**
	 * @var string
	 */
	protected $nombres;

	/**
	 * @var Date
	 */
	protected $fecha_nacimiento;

	/**
	 * @var integer
	 */
	protected $tipo_documentos_id;

	/**
	 * @var string
	 */
	protected $identificacion;

	/**
	 * @var integer
	 */
	protected $profesiones_id;

	/**
	 * @var integer
	 */
	protected $estados_civiles_id;

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
	protected $celular;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $imagen_conyuge;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo socios_tpc_id
	 * @param integer $socios_tpc_id
	 */
	public function setSociosTpcId($socios_tpc_id){
		$this->socios_tpc_id = $socios_tpc_id;
	}

	/**
	 * Metodo para establecer el valor del campo apellidos
	 * @param string $apellidos
	 */
	public function setApellidos($apellidos){
		$this->apellidos = $apellidos;
	}

	/**
	 * Metodo para establecer el valor del campo nombres
	 * @param string $nombres
	 */
	public function setNombres($nombres){
		$this->nombres = $nombres;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_nacimiento
	 * @param Date $fecha_nacimiento
	 */
	public function setFechaNacimiento($fecha_nacimiento){
		$this->fecha_nacimiento = $fecha_nacimiento;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_documentos_id
	 * @param integer $tipo_documentos_id
	 */
	public function setTipoDocumentosId($tipo_documentos_id){
		$this->tipo_documentos_id = $tipo_documentos_id;
	}

	/**
	 * Metodo para establecer el valor del campo identificacion
	 * @param string $identificacion
	 */
	public function setIdentificacion($identificacion){
		$this->identificacion = $identificacion;
	}

	/**
	 * Metodo para establecer el valor del campo profesiones_id
	 * @param integer $profesiones_id
	 */
	public function setProfesionesId($profesiones_id){
		$this->profesiones_id = $profesiones_id;
	}

	/**
	 * Metodo para establecer el valor del campo estados_civiles_id
	 * @param integer $estados_civiles_id
	 */
	public function setEstadosCivilesId($estados_civiles_id){
		$this->estados_civiles_id = $estados_civiles_id;
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
	 * Metodo para establecer el valor del campo celular
	 * @param string $celular
	 */
	public function setCelular($celular){
		$this->celular = $celular;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo imagen_conyuge
	 * @param string $imagen_conyuge
	 */
	public function setImagenConyuge($imagen_conyuge){
		$this->imagen_conyuge = $imagen_conyuge;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo socios_tpc_id
	 * @return integer
	 */
	public function getSociosTpcId(){
		return $this->socios_tpc_id;
	}

	/**
	 * Devuelve el valor del campo apellidos
	 * @return string
	 */
	public function getApellidos(){
		return $this->apellidos;
	}

	/**
	 * Devuelve el valor del campo nombres
	 * @return string
	 */
	public function getNombres(){
		return $this->nombres;
	}

	/**
	 * Devuelve el valor del campo fecha_nacimiento
	 * @return Date
	 */
	public function getFechaNacimiento(){
		return new Date($this->fecha_nacimiento);
	}

	/**
	 * Devuelve el valor del campo tipo_documentos_id
	 * @return integer
	 */
	public function getTipoDocumentosId(){
		return $this->tipo_documentos_id;
	}

	/**
	 * Devuelve el valor del campo identificacion
	 * @return string
	 */
	public function getIdentificacion(){
		return $this->identificacion;
	}

	/**
	 * Devuelve el valor del campo profesiones_id
	 * @return integer
	 */
	public function getProfesionesId(){
		return $this->profesiones_id;
	}

	/**
	 * Devuelve el valor del campo estados_civiles_id
	 * @return integer
	 */
	public function getEstadosCivilesId(){
		return $this->estados_civiles_id;
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
	 * Devuelve el valor del campo celular
	 * @return string
	 */
	public function getCelular(){
		return $this->celular;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo imagen_conyuge
	 * @return string
	 */
	public function getImagenConyuge(){
		return $this->imagen_conyuge;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSchema('sociostpc');
				
		$this->belongsTo('socios_tpc');
		$this->belongsTo('tipo_documentos');
		$this->belongsTo('profesiones');
		$this->belongsTo('estados_civiles');
	}

}

