<?php

class Reservas extends RcsRecord {

	/**
	* @var boolean
	*/
	protected $_validar;
	
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
	 * Metodo para establecer el valor del campo _validar
	 * @param integer $id
	 */
	public function setValidar($validar){
		$this->_validar = $validar;
	}

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
	 * Metodo para obtiene el valor del campo _validar
	 * @param boolean $validar
	 */
	public function getValidar(){
		return $this->_validar;
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

	public function beforeValidationOnCreate(){

		$flagStatus = true;
		
		//Actualizamos el consecutivo de reservas
		$empresa = EntityManager::get('Empresa')->findFirst();
		$consecutivoReserva = $empresa->getCreservas();
		$consecutivoReserva += 1;
		$this->numero_contrato = 'RESERVA-'.$consecutivoReserva;
		$exists = EntityManager::get('Reservas')->count("numero_contrato='{$this->numero_contrato}'");
		if($exists>0){
			$this->appendMessage(new ActiveRecordMessage('Ya existe una reserva con ese numero de reserva', 'identificacion'));
			$flagStatus = false;
		}

		$this->estado_contrato = 'A'; //Activo
		$this->estado_movimiento = 'R'; //Reserva
		
		return $flagStatus;

	}

	public function validation(){
		$flagStatus = true;
		
		$this->validate('InclusionIn', array(
			'field' => 'envio_correspondencia',
			'domain' => array('S', 'N'),
			'message' => 'El envio de correspondencia debe ser "Si" ó "No"',
			'required' => true
		));
		if($this->validationHasFailed()==true){
			$this->appendMessage(new ActiveRecordMessage('El envio de correspondencia es obligatoria', 'identificacion'));
			$flagStatus = false;
		}
		
		//valida que la cedula sea mayor a 0
		if($this->getIdentificacion()<=0){
			$this->appendMessage(new ActiveRecordMessage('La cédula es obligatoria', 'identificacion'));
			$flagStatus = false;
		}

		return $flagStatus;
	}
	
	public function beforeValidation(){

		$flagStatus = true;

		//Validamos que el estado de la reserva sea activa para editarla
		if($this->getEstadoContrato()=='AA'){
			if($this->getValidar()==true){
				$this->appendMessage(new ActiveRecordMessage('La reserva no puede modificarse', 'estado_contrato'));
				$flagStatus = false;
			}
		}
		$estadoContrato = substr($this->getEstadoContrato(),0,2);
		//$this->appendMessage(new ActiveRecordMessage($this->getEstadoContrato().'->'.substr($this->getEstadoContrato(),0,2)));
		//return false;
		$this->setEstadoContrato($estadoContrato);
		$conditions = " identificacion='".$this->identificacion."' AND estado_contrato='A'";
		if($this->id>0){
			$conditions .= " AND id<>".$this->id;
		}
		$reserva = EntityManager::get('Reservas')->findFirst(array('conditions'=>$conditions));
		if($reserva != false){
			$this->appendMessage(new ActiveRecordMessage('La cédula ya existe en la reserva '.$reserva->getNumeroContrato(), 'identificacion'));
			$flagStatus = false;
		}
		//valida existencia de cedula en tabla socios_tpc
		$conditions = " identificacion='".$this->identificacion."' AND estado_contrato='A'";
		$socio = EntityManager::get('Socios')->findFirst(array('conditions'=>$conditions));
		if($socio != false){
			if($this->getValidar()==true){
				$this->appendMessage(new ActiveRecordMessage('La cédula ya existe en el contrato '.$socio->getNumeroContrato(), 'identificacion'));
				$flagStatus = false;
			}		
		}
		//validamos id
		if($this->profesiones_id==0){
			$this->profesiones_id = null;
		}			
		//campos en mayusculas
		$this->nombres = i18n::strtoupper($this->nombres);
		$this->apellidos = i18n::strtoupper($this->apellidos);
			

		return $flagStatus;
	}

	public function beforeCreate(){
		//Actualizamos el consecutivo de reservas en empresa
		$empresa = EntityManager::get('Empresa')->findFirst();
		$empresa->setConnection($this->getConnection());
		$empresa->disableEvents(true);
		$empresa->setCreservas($empresa->getCreservas()+1);
		if($empresa->save()==false){
			foreach($empresa->getMessages() as $message){
				$this->appendMessage($message);
			}
			return false;
		}
		return true;
	}

	public function beforeDelete(){
		$this->appendMessage(new ActiveRecordMessage('Las reservas no pueden ser borradas', 'identificacion'));
		return false;
	}

	public function initialize(){
		$this->addForeignKey('tipo_documentos_id', 'TipoDocumentos', 'id', array(
			'message' => 'El tipo de documento no es valido'
		));
		$this->addForeignKey('profesiones_id', 'Profesiones', 'id', array(
			'message' => 'La profesión no es valida'
		));
		$this->addForeignKey('tipo_socios_id', 'TipoSocios', 'id', array(
			'message' => 'El tipo de socio no es valido'
		));
		$this->addForeignKey('estado_contrato', 'EstadoContrato', 'codigo', array(
			'message' => 'El estado de contrato no es valido'
		));
		$this->addForeignKey('estado_movimiento', 'EstadoReservas', 'codigo', array(
			'message' => 'El estado de movimiento no es valido'
		));
		/*$this->addForeignKey('socios_id', 'Socios', 'id', array(
			'message' => 'El nuevo contrato no es valido'
		));*/
	}

}

