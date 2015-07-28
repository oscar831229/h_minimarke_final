<?php

class Empleados extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $cedula;

	/**
	 * @var string
	 */
	protected $primer_apellido;

	/**
	 * @var string
	 */
	protected $segundo_apellido;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $nombre_completo;

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
	protected $sexo;

	/**
	 * @var string
	 */
	protected $estado_civil;

	/**
	 * @var string
	 */
	protected $libreta_militar;

	/**
	 * @var Date
	 */
	protected $fecha_nace;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo cedula
	 * @param string $cedula
	 */
	public function setCedula($cedula){
		$this->cedula = $cedula;
	}

	/**
	 * Metodo para establecer el valor del campo primer_apellido
	 * @param string $primer_apellido
	 */
	public function setPrimerApellido($primer_apellido){
		$this->primer_apellido = $primer_apellido;
	}

	/**
	 * Metodo para establecer el valor del campo segundo_apellido
	 * @param string $segundo_apellido
	 */
	public function setSegundoApellido($segundo_apellido){
		$this->segundo_apellido = $segundo_apellido;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo nombre_completo
	 * @param string $nombre_completo
	 */
	public function setNombreCompleto($nombre_completo){
		$this->nombre_completo = $nombre_completo;
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
	 * Metodo para establecer el valor del campo sexo
	 * @param string $sexo
	 */
	public function setSexo($sexo){
		$this->sexo = $sexo;
	}

	/**
	 * Metodo para establecer el valor del campo estado_civil
	 * @param string $estado_civil
	 */
	public function setEstadoCivil($estado_civil){
		$this->estado_civil = $estado_civil;
	}

	/**
	 * Metodo para establecer el valor del campo libreta_militar
	 * @param string $libreta_militar
	 */
	public function setLibretaMilitar($libreta_militar){
		$this->libreta_militar = $libreta_militar;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_nace
	 * @param Date $fecha_nace
	 */
	public function setFechaNace($fecha_nace){
		$this->fecha_nace = $fecha_nace;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo cedula
	 * @return string
	 */
	public function getCedula(){
		return $this->cedula;
	}

	/**
	 * Devuelve el valor del campo primer_apellido
	 * @return string
	 */
	public function getPrimerApellido(){
		return $this->primer_apellido;
	}

	/**
	 * Devuelve el valor del campo segundo_apellido
	 * @return string
	 */
	public function getSegundoApellido(){
		return $this->segundo_apellido;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo nombre_completo
	 * @return string
	 */
	public function getNombreCompleto(){
		return $this->nombre_completo;
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
	 * Devuelve el valor del campo sexo
	 * @return string
	 */
	public function getSexo(){
		return $this->sexo;
	}

	/**
	 * Devuelve el valor del campo estado_civil
	 * @return string
	 */
	public function getEstadoCivil(){
		return $this->estado_civil;
	}

	/**
	 * Devuelve el valor del campo libreta_militar
	 * @return string
	 */
	public function getLibretaMilitar(){
		return $this->libreta_militar;
	}

	/**
	 * Devuelve el valor del campo fecha_nace
	 * @return Date
	 */
	public function getFechaNace(){
		if($this->fecha_nace){
			return new Date($this->fecha_nace);
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

	protected function beforeSave(){
		if($this->fecha_nace){
			$mayorEdad = Date::diffInterval(new Date(), 18, Date::INTERVAL_YEAR);
			if(Date::isLater($this->fecha_nace, $mayorEdad)){
				$this->appendMessage(new ActiveRecordMessage('El empleado debe ser mayor de edad', 'fecha_nace'));
				return false;
			}
		}
		$this->nombre_completo = $this->primer_apellido.' '.$this->segundo_apellido.' '.$this->nombre;
	}

}

