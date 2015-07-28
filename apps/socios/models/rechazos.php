<?php

class Rechazos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $tipo_documentos_id;

	/**
	 * @var string
	 */
	protected $identificacion;

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
	protected $fecha_solicitud;

	/**
	 * @var string
	 */
	protected $observaciones;

	/**
	 * @var Date
	 */
	protected $primera_fecha;

	/**
	 * @var string
	 */
	protected $primera_acta;

	/**
	 * @var string
	 */
	protected $primera_observacion;

	/**
	 * @var string
	 */
	protected $primera_estado;

	/**
	 * @var Date
	 */
	protected $segunda_fecha;

	/**
	 * @var string
	 */
	protected $segunda_acta;

	/**
	 * @var string
	 */
	protected $segunda_observacion;

	/**
	 * @var string
	 */
	protected $segunda_estado;

	/**
	 * @var Date
	 */
	protected $tercera_fecha;

	/**
	 * @var string
	 */
	protected $tercera_acta;

	/**
	 * @var string
	 */
	protected $tercera_observacion;

	/**
	 * @var string
	 */
	protected $tercera_estado;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
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
	 * Metodo para establecer el valor del campo fecha_solicitud
	 * @param Date $fecha_solicitud
	 */
	public function setFechaSolicitud($fecha_solicitud){
		$this->fecha_solicitud = $fecha_solicitud;
	}

	/**
	 * Metodo para establecer el valor del campo observaciones
	 * @param string $observaciones
	 */
	public function setObservaciones($observaciones){
		$this->observaciones = $observaciones;
	}

	/**
	 * Metodo para establecer el valor del campo primera_fecha
	 * @param Date $primera_fecha
	 */
	public function setPrimeraFecha($primera_fecha){
		$this->primera_fecha = $primera_fecha;
	}

	/**
	 * Metodo para establecer el valor del campo primera_acta
	 * @param string $primera_acta
	 */
	public function setPrimeraActa($primera_acta){
		$this->primera_acta = $primera_acta;
	}

	/**
	 * Metodo para establecer el valor del campo primera_observacion
	 * @param string $primera_observacion
	 */
	public function setPrimeraObservacion($primera_observacion){
		$this->primera_observacion = $primera_observacion;
	}

	/**
	 * Metodo para establecer el valor del campo primera_estado
	 * @param string $primera_estado
	 */
	public function setPrimeraEstado($primera_estado){
		$this->primera_estado = $primera_estado;
	}

	/**
	 * Metodo para establecer el valor del campo segunda_fecha
	 * @param Date $segunda_fecha
	 */
	public function setSegundaFecha($segunda_fecha){
		$this->segunda_fecha = $segunda_fecha;
	}

	/**
	 * Metodo para establecer el valor del campo segunda_acta
	 * @param string $segunda_acta
	 */
	public function setSegundaActa($segunda_acta){
		$this->segunda_acta = $segunda_acta;
	}

	/**
	 * Metodo para establecer el valor del campo segunda_observacion
	 * @param string $segunda_observacion
	 */
	public function setSegundaObservacion($segunda_observacion){
		$this->segunda_observacion = $segunda_observacion;
	}

	/**
	 * Metodo para establecer el valor del campo segunda_estado
	 * @param string $segunda_estado
	 */
	public function setSegundaEstado($segunda_estado){
		$this->segunda_estado = $segunda_estado;
	}

	/**
	 * Metodo para establecer el valor del campo tercera_fecha
	 * @param Date $tercera_fecha
	 */
	public function setTerceraFecha($tercera_fecha){
		$this->tercera_fecha = $tercera_fecha;
	}

	/**
	 * Metodo para establecer el valor del campo tercera_acta
	 * @param string $tercera_acta
	 */
	public function setTerceraActa($tercera_acta){
		$this->tercera_acta = $tercera_acta;
	}

	/**
	 * Metodo para establecer el valor del campo tercera_observacion
	 * @param string $tercera_observacion
	 */
	public function setTerceraObservacion($tercera_observacion){
		$this->tercera_observacion = $tercera_observacion;
	}

	/**
	 * Metodo para establecer el valor del campo tercera_estado
	 * @param string $tercera_estado
	 */
	public function setTerceraEstado($tercera_estado){
		$this->tercera_estado = $tercera_estado;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
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
	 * Devuelve el valor del campo fecha_solicitud
	 * @return Date
	 */
	public function getFechaSolicitud(){
		if($this->fecha_solicitud){
			return new Date($this->fecha_solicitud);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo observaciones
	 * @return string
	 */
	public function getObservaciones(){
		return $this->observaciones;
	}

	/**
	 * Devuelve el valor del campo primera_fecha
	 * @return Date
	 */
	public function getPrimeraFecha(){
		if($this->primera_fecha){
			return new Date($this->primera_fecha);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo primera_acta
	 * @return string
	 */
	public function getPrimeraActa(){
		return $this->primera_acta;
	}

	/**
	 * Devuelve el valor del campo primera_observacion
	 * @return string
	 */
	public function getPrimeraObservacion(){
		return $this->primera_observacion;
	}

	/**
	 * Devuelve el valor del campo primera_estado
	 * @return string
	 */
	public function getPrimeraEstado(){
		return $this->primera_estado;
	}

	/**
	 * Devuelve el valor del campo segunda_fecha
	 * @return Date
	 */
	public function getSegundaFecha(){
		if($this->segunda_fecha){
			return new Date($this->segunda_fecha);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo segunda_acta
	 * @return string
	 */
	public function getSegundaActa(){
		return $this->segunda_acta;
	}

	/**
	 * Devuelve el valor del campo segunda_observacion
	 * @return string
	 */
	public function getSegundaObservacion(){
		return $this->segunda_observacion;
	}

	/**
	 * Devuelve el valor del campo segunda_estado
	 * @return string
	 */
	public function getSegundaEstado(){
		return $this->segunda_estado;
	}

	/**
	 * Devuelve el valor del campo tercera_fecha
	 * @return Date
	 */
	public function getTerceraFecha(){
		if($this->tercera_fecha){
			return new Date($this->tercera_fecha);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo tercera_acta
	 * @return string
	 */
	public function getTerceraActa(){
		return $this->tercera_acta;
	}

	/**
	 * Devuelve el valor del campo tercera_observacion
	 * @return string
	 */
	public function getTerceraObservacion(){
		return $this->tercera_observacion;
	}

	/**
	 * Devuelve el valor del campo tercera_estado
	 * @return string
	 */
	public function getTerceraEstado(){
		return $this->tercera_estado;
	}

}

