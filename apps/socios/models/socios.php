<?php

class Socios extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $titular_id;

	/**
	 * @var string
	 */
	protected $numero_accion;

	/**
	 * @var string
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
	protected $ciudad_expedido;

	/**
	 * @var integer
	 */
	protected $ciudad_nacimiento;

	/**
	 * @var string
	 */
	protected $fecha_nacimiento;

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
	protected $tipos_pago_id;

	/**
	 * @var integer
	 */
	protected $formas_pago_id;

	/**
	 * @var string
	 */
	protected $numero_tarjeta;

	/**
	 * @var string
	 */
	protected $envia_correo;

	/**
	 * @var integer
	 */
	protected $estados_socios_id;

	/**
	 * @var string
	 */
	protected $cobra;

	/**
	 * @var Date
	 */
	protected $fecha_retiro;

	/**
	 * @var string
	 */
	protected $imagen_socio;

	/**
	 * @var integer
	 */
	protected $ciudad_casa;

	/**
	 * @var integer
	 */
	protected $ciudad_trabajo;

	/**
	 * @var string
	 */
	protected $celular_trabajo;

	/**
	 * @var string
	 */
	protected $nombre_padre;

	/**
	 * @var string
	 */
	protected $nombre_madre;

	/**
	 * @var string
	 */
	protected $imprime;

	/**
	 * @var string
	 */
	protected $porc_mora_desfecha;

	/**
	 * @var string
	 */
	protected $consumo_minimo;

	/**
	 * @var string
	 */
	protected $genera_mora;

	/**
	 * @var string
	 */
	protected $ajuste_sostenimiento;


	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo titular_id
	 * @param integer $titular_id
	 */
	public function setTitularId($titular_id){
		$this->titular_id = $titular_id;
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
	 * @param string $fecha_ingreso
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
	 * Metodo para establecer el valor del campo ciudad_expedido
	 * @param integer $ciudad_expedido
	 */
	public function setCiudadExpedido($ciudad_expedido){
		$this->ciudad_expedido = $ciudad_expedido;
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
	 * @param string $fecha_nacimiento
	 */
	public function setFechaNacimiento($fecha_nacimiento){
		$this->fecha_nacimiento = $fecha_nacimiento;
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
	 * Metodo para establecer el valor del campo tipos_pago_id
	 * @param integer $tipos_pago_id
	 */
	public function setTiposPagoId($tipos_pago_id){
		$this->tipos_pago_id = $tipos_pago_id;
	}

	/**
	 * Metodo para establecer el valor del campo formas_pago_id
	 * @param integer $formas_pago_id
	 */
	public function setFormasPagoId($formas_pago_id){
		$this->formas_pago_id = $formas_pago_id;
	}

	/**
	 * Metodo para establecer el valor del campo numero_tarjeta
	 * @param string $numero_tarjeta
	 */
	public function setNumeroTarjeta($numero_tarjeta){
		$this->numero_tarjeta = $numero_tarjeta;
	}

	/**
	 * Metodo para establecer el valor del campo envia_correo
	 * @param string $envia_correo
	 */
	public function setEnviaCorreo($envia_correo){
		$this->envia_correo = $envia_correo;
	}

	/**
	 * Metodo para establecer el valor del campo estados_socios_id
	 * @param integer $estados_socios_id
	 */
	public function setEstadosSociosId($estados_socios_id){
		$this->estados_socios_id = $estados_socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo cobra
	 * @param string $cobra
	 */
	public function setCobra($cobra){
		$this->cobra = $cobra;
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
	 * Metodo para establecer el valor del campo ciudad_casa
	 * @param integer $ciudad_casa
	 */
	public function setCiudadCasa($ciudad_casa){
		$this->ciudad_casa = $ciudad_casa;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad_trabajo
	 * @param integer $ciudad_trabajo
	 */
	public function setCiudadTrabajo($ciudad_trabajo){
		$this->ciudad_trabajo = $ciudad_trabajo;
	}

	/**
	 * Metodo para establecer el valor del campo celular_trabajo
	 * @param string $celular_trabajo
	 */
	public function setCelularTrabajo($celular_trabajo){
		$this->celular_trabajo = $celular_trabajo;
	}

	/**
	 * Metodo para establecer el valor del campo nombre_padre
	 * @param string $nombre_padre
	 */
	public function setNombrePadre($nombre_padre){
		$this->nombre_padre = $nombre_padre;
	}

	/**
	 * Metodo para establecer el valor del campo nombre_madre
	 * @param string $nombre_madre
	 */
	public function setNombreMadre($nombre_madre){
		$this->nombre_madre = $nombre_madre;
	}

	/**
	 * Metodo para establecer el valor del campo imprime
	 * @param string $imprime
	 */
	public function setImprime($imprime){
		$this->imprime = $imprime;
	}

	/**
	 * Metodo para establecer el valor del campo porc_mora_desfecha
	 * @param string $porc_mora_desfecha
	 */
	public function setPorcMoraDesfecha($porc_mora_desfecha){
		$this->porc_mora_desfecha = $porc_mora_desfecha;
	}

	/**
	 * Metodo para establecer el valor del campo consumo_minimo
	 * @param string $consumo_minimo
	 */
	public function setConsumoMinimo($consumo_minimo){
		$this->consumo_minimo = $consumo_minimo;
	}

	/**
	 * Metodo para establecer el valor del campo genera_mora
	 * @param string $genera_mora
	 */
	public function setGeneraMora($genera_mora){
		$this->genera_mora = $genera_mora;
	}

	/**
	 * Metodo para establecer el valor del campo ajuste_sostenimiento
	 * @param string $ajuste_sostenimiento
	 */
	public function setAjusteSostenimiento($ajuste_sostenimiento){
		$this->ajuste_sostenimiento = $ajuste_sostenimiento;
	}


	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo titular_id
	 * @return integer
	 */
	public function getTitularId(){
		return $this->titular_id;
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
	 * @return string
	 */
	public function getFechaIngreso(){
		return $this->fecha_ingreso;
	}

	/**
	 * Devuelve el valor del campo fecha_inscripcion
	 * @return Date
	 */
	public function getFechaInscripcion(){
		if($this->fecha_inscripcion){
			return new Date($this->fecha_inscripcion);
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
	 * Devuelve el valor del campo ciudad_expedido
	 * @return integer
	 */
	public function getCiudadExpedido(){
		return $this->ciudad_expedido;
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
	 * @return string
	 */
	public function getFechaNacimiento(){
		return $this->fecha_nacimiento;
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
	 * Devuelve el valor del campo tipos_pago_id
	 * @return integer
	 */
	public function getTiposPagoId(){
		return $this->tipos_pago_id;
	}

	/**
	 * Devuelve el valor del campo formas_pago_id
	 * @return integer
	 */
	public function getFormasPagoId(){
		return $this->formas_pago_id;
	}

	/**
	 * Devuelve el valor del campo numero_tarjeta
	 * @return string
	 */
	public function getNumeroTarjeta(){
		return $this->numero_tarjeta;
	}

	/**
	 * Devuelve el valor del campo envia_correo
	 * @return string
	 */
	public function getEnviaCorreo(){
		return $this->envia_correo;
	}

	/**
	 * Devuelve el valor del campo estados_socios_id
	 * @return integer
	 */
	public function getEstadosSociosId(){
		return $this->estados_socios_id;
	}

	/**
	 * Devuelve el valor del campo cobra
	 * @return string
	 */
	public function getCobra(){
		return $this->cobra;
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
	 * Devuelve el valor del campo ciudad_casa
	 * @return integer
	 */
	public function getCiudadCasa(){
		return $this->ciudad_casa;
	}

	/**
	 * Devuelve el valor del campo ciudad_trabajo
	 * @return integer
	 */
	public function getCiudadTrabajo(){
		return $this->ciudad_trabajo;
	}

	/**
	 * Devuelve el valor del campo celular_trabajo
	 * @return string
	 */
	public function getCelularTrabajo(){
		return $this->celular_trabajo;
	}

	/**
	 * Devuelve el valor del campo nombre_padre
	 * @return string
	 */
	public function getNombrePadre(){
		return $this->nombre_padre;
	}

	/**
	 * Devuelve el valor del campo nombre_madre
	 * @return string
	 */
	public function getNombreMadre(){
		return $this->nombre_madre;
	}

	/**
	 * Devuelve el valor del campo imprime
	 * @return string
	 */
	public function getImprime(){
		return $this->imprime;
	}

	/**
	 * Devuelve el valor del campo porc_mora_desfecha
	 * @return string
	 */
	public function getPorcMoraDesfecha(){
		return $this->porc_mora_desfecha;
	}

	/**
	 * Devuelve el valor del campo consumo_minimo
	 * @return string
	 */
	public function getConsumoMinimo(){
		return $this->consumo_minimo;
	}

	/**
	 * Devuelve el valor del campo genera_mora
	 * @return string
	 */
	public function getGeneraMora(){
		return $this->genera_mora;
	}

	/**
	 * Devuelve el valor del campo ajuste_sostenimiento
	 * @return string
	 */
	public function getAjusteSostenimiento(){
		return $this->ajuste_sostenimiento;
	}

	public function beforeDelete(){
		//$this->appendMessage(new ActiveRecordMessage('No se puede borrar un socio'));
		//return false;
		EntityManager::get('Estudios')->delete(array('conditions'=>"socios_id='{$this->getSociosId()}'"));
		EntityManager::get('Explaboral')->delete(array('conditions'=>"socios_id='{$this->getSociosId()}'"));
		EntityManager::get('Actividades')->delete(array('conditions'=>"socios_id='{$this->getSociosId()}'"));
		EntityManager::get('Asoclubes')->delete(array('conditions'=>"socios_id='{$this->getSociosId()}'"));
		EntityManager::get('AsignacionCargos')->delete(array('conditions'=>"socios_id='{$this->getSociosId()}'"));
		EntityManager::get('AsociacionSocio')->delete(array('conditions'=>"socios_id='{$this->getSociosId()}'"));
		EntityManager::get('CargosSocios')->delete(array('conditions'=>"socios_id='{$this->getSociosId()}'"));
		EntityManager::get('PrestamosSocios')->delete(array('conditions'=>"socios_id='{$this->getSociosId()}'"));
		EntityManager::get('Movimiento')->delete(array('conditions'=>"socios_id='{$this->getSociosId()}'"));
		//EntityManager::get('SociosPorteria')->delete(array('conditions'=>"socios_id='{$this->getSociosId()}'"));
		//EntityManager::get('SociosPorteria')->delete(array('conditions'=>"numero_accion='{$this->getNumeroAccion()}' AND identificacion='{$this->getIdentificacion()}'"));
	}

	public function initialize(){
		/*$this->addForeignKey('estados_socios_id', 'EstadosSocios', 'id', array(
			'message' => 'El estado del socio no es valido'
		));
		$this->addForeignKey('tipo_documentos_id', 'TipoDocumentos', 'id', array(
			'message' => 'El tipo de documento no es valido'
		));
		$this->addForeignKey('tipo_socios_id', 'TipoSocios', 'id', array(
			'message' => 'El tipo de socio no es valido'
		));
		$this->addForeignKey('estados_civiles_id', 'EstadosCiviles', 'id', array(
			'message' => 'El estado civil no es valido'
		));
		*/
		$this->hasOne('tipo_socios_id','TipoSocios','id');
		$this->belongsTo('estados_socios_id', 'EstadosSocios', 'id');
		
		$this->belongsTo('socios_id', 'Movimiento', 'socios_id');
		$this->belongsTo('socios_id', 'Factura', 'socios_id');
	}

}

