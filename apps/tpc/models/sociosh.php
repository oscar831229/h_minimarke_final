<?php

class Sociosh extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $numero_contrato;

	/**
	 * @var Date
	 */
	protected $fecha_compra;

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
	 * @var string
	 */
	protected $direccion_residencia;

	/**
	 * @var integer
	 */
	protected $ciudad_residencia;

	/**
	 * @var string
	 */
	protected $telefono_residencia;

	/**
	 * @var string
	 */
	protected $correo;

	/**
	 * @var string
	 */
	protected $celular;

	/**
	 * @var string
	 */
	protected $empresa;

	/**
	 * @var string
	 */
	protected $direccion_trabajo;

	/**
	 * @var integer
	 */
	protected $ciudades_id;

	/**
	 * @var string
	 */
	protected $telefono_trabajo;

	/**
	 * @var integer
	 */
	protected $profesiones_id;

	/**
	 * @var string
	 */
	protected $cargo;

	/**
	 * @var string
	 */
	protected $envio_correspondencia;

	/**
	 * @var integer
	 */
	protected $tipo_socios_id;

	/**
	 * @var string
	 */
	protected $estado_movimiento;

	/**
	 * @var string
	 */
	protected $estado_contrato;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $tipo_contrato_id;

	/**
	 * @var integer
	 */
	protected $estados_civiles_id;

	/**
	 * @var integer
	 */
	protected $nota_historia_id;

	/**
	 * @var string
	 */
	protected $cambio_contrato;

	/**
	 * @var integer
	 */
	protected $valor_cambio_contrato;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo numero_contrato
	 * @param string $numero_contrato
	 */
	public function setNumeroContrato($numero_contrato){
		$this->numero_contrato = $numero_contrato;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_compra
	 * @param Date $fecha_compra
	 */
	public function setFechaCompra($fecha_compra){
		$this->fecha_compra = $fecha_compra;
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
	 * Metodo para establecer el valor del campo direccion_residencia
	 * @param string $direccion_residencia
	 */
	public function setDireccionResidencia($direccion_residencia){
		$this->direccion_residencia = $direccion_residencia;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad_residencia
	 * @param integer $ciudad_residencia
	 */
	public function setCiudadResidencia($ciudad_residencia){
		$this->ciudad_residencia = $ciudad_residencia;
	}

	/**
	 * Metodo para establecer el valor del campo telefono_residencia
	 * @param string $telefono_residencia
	 */
	public function setTelefonoResidencia($telefono_residencia){
		$this->telefono_residencia = $telefono_residencia;
	}

	/**
	 * Metodo para establecer el valor del campo correo
	 * @param string $correo
	 */
	public function setCorreo($correo){
		$this->correo = $correo;
	}

	/**
	 * Metodo para establecer el valor del campo celular
	 * @param string $celular
	 */
	public function setCelular($celular){
		$this->celular = $celular;
	}

	/**
	 * Metodo para establecer el valor del campo empresa
	 * @param string $empresa
	 */
	public function setEmpresa($empresa){
		$this->empresa = $empresa;
	}

	/**
	 * Metodo para establecer el valor del campo direccion_trabajo
	 * @param string $direccion_trabajo
	 */
	public function setDireccionTrabajo($direccion_trabajo){
		$this->direccion_trabajo = $direccion_trabajo;
	}

	/**
	 * Metodo para establecer el valor del campo ciudades_id
	 * @param integer $ciudades_id
	 */
	public function setCiudadesId($ciudades_id){
		$this->ciudades_id = $ciudades_id;
	}

	/**
	 * Metodo para establecer el valor del campo telefono_trabajo
	 * @param string $telefono_trabajo
	 */
	public function setTelefonoTrabajo($telefono_trabajo){
		$this->telefono_trabajo = $telefono_trabajo;
	}

	/**
	 * Metodo para establecer el valor del campo profesiones_id
	 * @param integer $profesiones_id
	 */
	public function setProfesionesId($profesiones_id){
		$this->profesiones_id = $profesiones_id;
	}

	/**
	 * Metodo para establecer el valor del campo cargo
	 * @param string $cargo
	 */
	public function setCargo($cargo){
		$this->cargo = $cargo;
	}

	/**
	 * Metodo para establecer el valor del campo envio_correspondencia
	 * @param string $envio_correspondencia
	 */
	public function setEnvioCorrespondencia($envio_correspondencia){
		$this->envio_correspondencia = $envio_correspondencia;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_socios_id
	 * @param integer $tipo_socios_id
	 */
	public function setTipoSociosId($tipo_socios_id){
		$this->tipo_socios_id = $tipo_socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo estado_movimiento
	 * @param string $estado_movimiento
	 */
	public function setEstadoMovimiento($estado_movimiento){
		$this->estado_movimiento = $estado_movimiento;
	}

	/**
	 * Metodo para establecer el valor del campo estado_contrato
	 * @param string $estado_contrato
	 */
	public function setEstadoContrato($estado_contrato){
		$this->estado_contrato = $estado_contrato;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_contrato_id
	 * @param integer $tipo_contrato_id
	 */
	public function setTipoContratoId($tipo_contrato_id){
		$this->tipo_contrato_id = $tipo_contrato_id;
	}

	/**
	 * Metodo para establecer el valor del campo estados_civiles_id
	 * @param integer $estados_civiles_id
	 */
	public function setEstadosCivilesId($estados_civiles_id){
		$this->estados_civiles_id = $estados_civiles_id;
	}

	/**
	 * Metodo para establecer el valor del campo nota_historia_id
	 * @param integer $nota_historia_id
	 */
	public function setNotaHistoriaId($nota_historia_id){
		$this->nota_historia_id = $nota_historia_id;
	}

	/**
	 * Metodo para establecer si es cambio de contrato o no
	 * @param string $cambio_contrato
	 */
	public function setCambioContrato($cambio_contrato){
		$this->cambio_contrato = $cambio_contrato;
	}

	/**
	 * Metodo para establecer el valor del cambio de contrato
	 * @param integer $valor_cambio_contrato
	 */
	public function setValorCambioContrato($valor_cambio_contrato){
		$this->valor_cambio_contrato = $valor_cambio_contrato;
	}

	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo numero_contrato
	 * @return string
	 */
	public function getNumeroContrato(){
		return $this->numero_contrato;
	}

	/**
	 * Devuelve el valor del campo fecha_compra
	 * @return Date
	 */
	public function getFechaCompra(){
		if($this->fecha_compra){
			return new Date($this->fecha_compra);
		} else {
			return null;
		}
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
	 * Devuelve el valor del campo direccion_residencia
	 * @return string
	 */
	public function getDireccionResidencia(){
		return $this->direccion_residencia;
	}

	/**
	 * Devuelve el valor del campo ciudad_residencia
	 * @return integer
	 */
	public function getCiudadResidencia(){
		return $this->ciudad_residencia;
	}

	/**
	 * Devuelve el valor del campo telefono_residencia
	 * @return string
	 */
	public function getTelefonoResidencia(){
		return $this->telefono_residencia;
	}

	/**
	 * Devuelve el valor del campo correo
	 * @return string
	 */
	public function getCorreo(){
		return $this->correo;
	}

	/**
	 * Devuelve el valor del campo celular
	 * @return string
	 */
	public function getCelular(){
		return $this->celular;
	}

	/**
	 * Devuelve el valor del campo empresa
	 * @return string
	 */
	public function getEmpresa(){
		return $this->empresa;
	}

	/**
	 * Devuelve el valor del campo direccion_trabajo
	 * @return string
	 */
	public function getDireccionTrabajo(){
		return $this->direccion_trabajo;
	}

	/**
	 * Devuelve el valor del campo ciudades_id
	 * @return integer
	 */
	public function getCiudadesId(){
		return $this->ciudades_id;
	}

	/**
	 * Devuelve el valor del campo telefono_trabajo
	 * @return string
	 */
	public function getTelefonoTrabajo(){
		return $this->telefono_trabajo;
	}

	/**
	 * Devuelve el valor del campo profesiones_id
	 * @return integer
	 */
	public function getProfesionesId(){
		return $this->profesiones_id;
	}

	/**
	 * Devuelve el valor del campo cargo
	 * @return string
	 */
	public function getCargo(){
		return $this->cargo;
	}

	/**
	 * Devuelve el valor del campo envio_correspondencia
	 * @return string
	 */
	public function getEnvioCorrespondencia(){
		return $this->envio_correspondencia;
	}

	/**
	 * Devuelve el valor del campo tipo_socios_id
	 * @return integer
	 */
	public function getTipoSociosId(){
		return $this->tipo_socios_id;
	}

	/**
	 * Devuelve el valor del campo estado_movimiento
	 * @return string
	 */
	public function getEstadoMovimiento(){
		return $this->estado_movimiento;
	}

	/**
	 * Devuelve el valor del campo estado_contrato
	 * @return string
	 */
	public function getEstadoContrato(){
		return $this->estado_contrato;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo tipo_contrato_id
	 * @return integer
	 */
	public function getTipoContratoId(){
		return $this->tipo_contrato_id;
	}

	/**
	 * Devuelve el valor del campo estados_civiles_id
	 * @return integer
	 */
	public function getEstadosCivilesId(){
		return $this->estados_civiles_id;
	}

	/**
	 * Devuelve el valor del campo nota_historia_id
	 * @return integer
	 */
	public function getNotaHistoriaId(){
		return $this->nota_historia_id;
	}

	/**
	 * Devuelve el valor del campo cambio_contrato
	 * @return string
	 */
	public function getCambioContrato(){
		return $this->cambio_contrato;
	}

	/**
	 * Devuelve el valor del campo valor_cambio_contrato
	 * @return integer
	 */
	public function getValorCambioContrato(){
		return $this->valor_cambio_contrato;
	}

}

