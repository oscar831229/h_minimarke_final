<?php

class SociosPorteria extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $numero_accion;

	/**
	 * @var Date
	 */
	protected $fecha_ingreso;

	/**
	 * @var Date
	 */
	protected $fecha_inscripcion;

	/**
	 * @var integer
	 */
	protected $tiempo;

	/**
	 * @var integer
	 */
	protected $parentescos_id;

	/**
	 * @var string
	 */
	protected $nombres;

	/**
	 * @var string
	 */
	protected $apellidos;

	/**
	 * @var string
	 */
	protected $identificacion;

	/**
	 * @var integer
	 */
	protected $tipo_documentos_id;

	/**
	 * @var integer
	 */
	protected $pais_expedido;

	/**
	 * @var integer
	 */
	protected $ciudad_expedido;

	/**
	 * @var integer
	 */
	protected $pais_nacimiento;

	/**
	 * @var integer
	 */
	protected $ciudad_nacimiento;

	/**
	 * @var Date
	 */
	protected $fecha_nacimiento;

	/**
	 * @var integer
	 */
	protected $edad;

	/**
	 * @var integer
	 */
	protected $nacionalidad;

	/**
	 * @var integer
	 */
	protected $estados_civiles_id;

	/**
	 * @var string
	 */
	protected $sexo;

	/**
	 * @var string
	 */
	protected $direccion_casa;

	/**
	 * @var string
	 */
	protected $telefono_casa;

	/**
	 * @var string
	 */
	protected $celular;

	/**
	 * @var string
	 */
	protected $direccion_trabajo;

	/**
	 * @var string
	 */
	protected $telefono_trabajo;

	/**
	 * @var string
	 */
	protected $fax;

	/**
	 * @var string
	 */
	protected $apartado_aereo;

	/**
	 * @var string
	 */
	protected $direccion_correspondencia;

	/**
	 * @var string
	 */
	protected $correo_1;

	/**
	 * @var string
	 */
	protected $correo_2;

	/**
	 * @var string
	 */
	protected $correo_3;

	/**
	 * @var integer
	 */
	protected $tipo_socios_id;

	/**
	 * @var integer
	 */
	protected $formas_pago_id;

	/**
	 * @var string
	 */
	protected $envia_correo;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var Date
	 */
	protected $fecha_retiro;

	/**
	 * @var string
	 */
	protected $imagen_socio;

	/**
	 * @var int
	 */
	protected $socios_id;



	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo numero_accion
	 * @param string $numero_accion
	 */
	public function setNumeroAccion($numero_accion){
		$this->numero_accion = $numero_accion;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_ingreso
	 * @param Date $fecha_ingreso
	 */
	public function setFechaIngreso($fecha_ingreso){
		$this->fecha_ingreso = $fecha_ingreso;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_inscripcion
	 * @param Date $fecha_inscripcion
	 */
	public function setFechaInscripcion($fecha_inscripcion){
		$this->fecha_inscripcion = $fecha_inscripcion;
	}

	/**
	 * Metodo para establecer el valor del campo tiempo
	 * @param integer $tiempo
	 */
	public function setTiempo($tiempo){
		$this->tiempo = $tiempo;
	}

	/**
	 * Metodo para establecer el valor del campo parentescos_id
	 * @param integer $parentescos_id
	 */
	public function setParentescosId($parentescos_id){
		$this->parentescos_id = $parentescos_id;
	}

	/**
	 * Metodo para establecer el valor del campo nombres
	 * @param string $nombres
	 */
	public function setNombres($nombres){
		$this->nombres = $nombres;
	}

	/**
	 * Metodo para establecer el valor del campo apellidos
	 * @param string $apellidos
	 */
	public function setApellidos($apellidos){
		$this->apellidos = $apellidos;
	}

	/**
	 * Metodo para establecer el valor del campo identificacion
	 * @param string $identificacion
	 */
	public function setIdentificacion($identificacion){
		$this->identificacion = $identificacion;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_documentos_id
	 * @param integer $tipo_documentos_id
	 */
	public function setTipoDocumentosId($tipo_documentos_id){
		$this->tipo_documentos_id = $tipo_documentos_id;
	}

	/**
	 * Metodo para establecer el valor del campo pais_expedido
	 * @param integer $pais_expedido
	 */
	public function setPaisExpedido($pais_expedido){
		$this->pais_expedido = $pais_expedido;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad_expedido
	 * @param integer $ciudad_expedido
	 */
	public function setCiudadExpedido($ciudad_expedido){
		$this->ciudad_expedido = $ciudad_expedido;
	}

	/**
	 * Metodo para establecer el valor del campo pais_nacimiento
	 * @param integer $pais_nacimiento
	 */
	public function setPaisNacimiento($pais_nacimiento){
		$this->pais_nacimiento = $pais_nacimiento;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad_nacimiento
	 * @param integer $ciudad_nacimiento
	 */
	public function setCiudadNacimiento($ciudad_nacimiento){
		$this->ciudad_nacimiento = $ciudad_nacimiento;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_nacimiento
	 * @param Date $fecha_nacimiento
	 */
	public function setFechaNacimiento($fecha_nacimiento){
		$this->fecha_nacimiento = $fecha_nacimiento;
	}

	/**
	 * Metodo para establecer el valor del campo edad
	 * @param integer $edad
	 */
	public function setEdad($edad){
		$this->edad = $edad;
	}

	/**
	 * Metodo para establecer el valor del campo nacionalidad
	 * @param integer $nacionalidad
	 */
	public function setNacionalidad($nacionalidad){
		$this->nacionalidad = $nacionalidad;
	}

	/**
	 * Metodo para establecer el valor del campo estados_civiles_id
	 * @param integer $estados_civiles_id
	 */
	public function setEstadosCivilesId($estados_civiles_id){
		$this->estados_civiles_id = $estados_civiles_id;
	}

	/**
	 * Metodo para establecer el valor del campo sexo
	 * @param string $sexo
	 */
	public function setSexo($sexo){
		$this->sexo = $sexo;
	}

	/**
	 * Metodo para establecer el valor del campo direccion_casa
	 * @param string $direccion_casa
	 */
	public function setDireccionCasa($direccion_casa){
		$this->direccion_casa = $direccion_casa;
	}

	/**
	 * Metodo para establecer el valor del campo telefono_casa
	 * @param string $telefono_casa
	 */
	public function setTelefonoCasa($telefono_casa){
		$this->telefono_casa = $telefono_casa;
	}

	/**
	 * Metodo para establecer el valor del campo celular
	 * @param string $celular
	 */
	public function setCelular($celular){
		$this->celular = $celular;
	}

	/**
	 * Metodo para establecer el valor del campo direccion_trabajo
	 * @param string $direccion_trabajo
	 */
	public function setDireccionTrabajo($direccion_trabajo){
		$this->direccion_trabajo = $direccion_trabajo;
	}

	/**
	 * Metodo para establecer el valor del campo telefono_trabajo
	 * @param string $telefono_trabajo
	 */
	public function setTelefonoTrabajo($telefono_trabajo){
		$this->telefono_trabajo = $telefono_trabajo;
	}

	/**
	 * Metodo para establecer el valor del campo fax
	 * @param string $fax
	 */
	public function setFax($fax){
		$this->fax = $fax;
	}

	/**
	 * Metodo para establecer el valor del campo apartado_aereo
	 * @param string $apartado_aereo
	 */
	public function setApartadoAereo($apartado_aereo){
		$this->apartado_aereo = $apartado_aereo;
	}

	/**
	 * Metodo para establecer el valor del campo direccion_correspondencia
	 * @param string $direccion_correspondencia
	 */
	public function setDireccionCorrespondencia($direccion_correspondencia){
		$this->direccion_correspondencia = $direccion_correspondencia;
	}

	/**
	 * Metodo para establecer el valor del campo correo_1
	 * @param string $correo_1
	 */
	public function setCorreo1($correo_1){
		$this->correo_1 = $correo_1;
	}

	/**
	 * Metodo para establecer el valor del campo correo_2
	 * @param string $correo_2
	 */
	public function setCorreo2($correo_2){
		$this->correo_2 = $correo_2;
	}

	/**
	 * Metodo para establecer el valor del campo correo_3
	 * @param string $correo_3
	 */
	public function setCorreo3($correo_3){
		$this->correo_3 = $correo_3;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_socios_id
	 * @param integer $tipo_socios_id
	 */
	public function setTipoSociosId($tipo_socios_id){
		$this->tipo_socios_id = $tipo_socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo formas_pago_id
	 * @param integer $formas_pago_id
	 */
	public function setFormasPagoId($formas_pago_id){
		$this->formas_pago_id = $formas_pago_id;
	}

	/**
	 * Metodo para establecer el valor del campo envia_correo
	 * @param string $envia_correo
	 */
	public function setEnviaCorreo($envia_correo){
		$this->envia_correo = $envia_correo;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_retiro
	 * @param Date $fecha_retiro
	 */
	public function setFechaRetiro($fecha_retiro){
		$this->fecha_retiro = $fecha_retiro;
	}

	/**
	 * Metodo para establecer el valor del campo imagen_socio
	 * @param string $imagen_socio
	 */
	public function setImagenSocio($imagen_socio){
		$this->imagen_socio = $imagen_socio;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param int $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo numero_accion
	 * @return string
	 */
	public function getNumeroAccion(){
		return $this->numero_accion;
	}

	/**
	 * Devuelve el valor del campo fecha_ingreso
	 * @return Date
	 */
	public function getFechaIngreso(){
		if($this->fecha_ingreso){
			return new Date($this->fecha_ingreso);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecha_inscripcion
	 * @return Date
	 */
	public function getFechaInscripcion(){
		if($this->fecha_inscripcion){
			return $this->fecha_inscripcion;
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo tiempo
	 * @return integer
	 */
	public function getTiempo(){
		return $this->tiempo;
	}

	/**
	 * Devuelve el valor del campo parentescos_id
	 * @return integer
	 */
	public function getParentescosId(){
		return $this->parentescos_id;
	}

	/**
	 * Devuelve el valor del campo nombres
	 * @return string
	 */
	public function getNombres(){
		return $this->nombres;
	}

	/**
	 * Devuelve el valor del campo apellidos
	 * @return string
	 */
	public function getApellidos(){
		return $this->apellidos;
	}

	/**
	 * Devuelve el valor del campo identificacion
	 * @return string
	 */
	public function getIdentificacion(){
		return $this->identificacion;
	}

	/**
	 * Devuelve el valor del campo tipo_documentos_id
	 * @return integer
	 */
	public function getTipoDocumentosId(){
		return $this->tipo_documentos_id;
	}

	/**
	 * Devuelve el valor del campo pais_expedido
	 * @return integer
	 */
	public function getPaisExpedido(){
		return $this->pais_expedido;
	}

	/**
	 * Devuelve el valor del campo ciudad_expedido
	 * @return integer
	 */
	public function getCiudadExpedido(){
		return $this->ciudad_expedido;
	}

	/**
	 * Devuelve el valor del campo pais_nacimiento
	 * @return integer
	 */
	public function getPaisNacimiento(){
		return $this->pais_nacimiento;
	}

	/**
	 * Devuelve el valor del campo ciudad_nacimiento
	 * @return integer
	 */
	public function getCiudadNacimiento(){
		return $this->ciudad_nacimiento;
	}

	/**
	 * Devuelve el valor del campo fecha_nacimiento
	 * @return Date
	 */
	public function getFechaNacimiento(){
		if($this->fecha_nacimiento){
			return $this->fecha_nacimiento;
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo edad
	 * @return integer
	 */
	public function getEdad(){
		return $this->edad;
	}

	/**
	 * Devuelve el valor del campo nacionalidad
	 * @return integer
	 */
	public function getNacionalidad(){
		return $this->nacionalidad;
	}

	/**
	 * Devuelve el valor del campo estados_civiles_id
	 * @return integer
	 */
	public function getEstadosCivilesId(){
		return $this->estados_civiles_id;
	}

	/**
	 * Devuelve el valor del campo sexo
	 * @return string
	 */
	public function getSexo(){
		return $this->sexo;
	}

	/**
	 * Devuelve el valor del campo direccion_casa
	 * @return string
	 */
	public function getDireccionCasa(){
		return $this->direccion_casa;
	}

	/**
	 * Devuelve el valor del campo telefono_casa
	 * @return string
	 */
	public function getTelefonoCasa(){
		return $this->telefono_casa;
	}

	/**
	 * Devuelve el valor del campo celular
	 * @return string
	 */
	public function getCelular(){
		return $this->celular;
	}

	/**
	 * Devuelve el valor del campo direccion_trabajo
	 * @return string
	 */
	public function getDireccionTrabajo(){
		return $this->direccion_trabajo;
	}

	/**
	 * Devuelve el valor del campo telefono_trabajo
	 * @return string
	 */
	public function getTelefonoTrabajo(){
		return $this->telefono_trabajo;
	}

	/**
	 * Devuelve el valor del campo fax
	 * @return string
	 */
	public function getFax(){
		return $this->fax;
	}

	/**
	 * Devuelve el valor del campo apartado_aereo
	 * @return string
	 */
	public function getApartadoAereo(){
		return $this->apartado_aereo;
	}

	/**
	 * Devuelve el valor del campo direccion_correspondencia
	 * @return string
	 */
	public function getDireccionCorrespondencia(){
		return $this->direccion_correspondencia;
	}

	/**
	 * Devuelve el valor del campo correo_1
	 * @return string
	 */
	public function getCorreo1(){
		return $this->correo_1;
	}

	/**
	 * Devuelve el valor del campo correo_2
	 * @return string
	 */
	public function getCorreo2(){
		return $this->correo_2;
	}

	/**
	 * Devuelve el valor del campo correo_3
	 * @return string
	 */
	public function getCorreo3(){
		return $this->correo_3;
	}

	/**
	 * Devuelve el valor del campo tipo_socios_id
	 * @return integer
	 */
	public function getTipoSociosId(){
		return $this->tipo_socios_id;
	}

	/**
	 * Devuelve el valor del campo formas_pago_id
	 * @return integer
	 */
	public function getFormasPagoId(){
		return $this->formas_pago_id;
	}

	/**
	 * Devuelve el valor del campo envia_correo
	 * @return string
	 */
	public function getEnviaCorreo(){
		return $this->envia_correo;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo fecha_retiro
	 * @return Date
	 */
	public function getFechaRetiro(){
		if($this->fecha_retiro){
			return new Date($this->fecha_retiro);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo imagen_socio
	 * @return string
	 */
	public function getImagenSocio(){
		return $this->imagen_socio;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return int
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSource('socios');
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->porteria)){
			$this->setSchema($config->hfos->porteria);
		} else {
			$this->setSchema('porteria');
		}
	}

}

