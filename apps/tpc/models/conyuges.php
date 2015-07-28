<?php

class Conyuges extends RcsRecord {
    
    /**
     * Variable que activa o desactiva validaciones
     *
     * @var boolean
     */
    protected $validar = true;    

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

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
	 * Metodo que cambia el estado de validar o no
	 *
	 * @param unknown_type $validar
	 */
	public function setValidar($validar){
		$this->validar = $validar;
	}
	
	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
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
	 * Metodo que obtiene el estado de validar 
	 *
	 */
	public function getValidar(){
		return $this->validar;
	}	

	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
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
		if($this->fecha_nacimiento){
			return new Date($this->fecha_nacimiento);
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

	public function beforeCreate(){
	    $this->estado = 'A';//Activo
	}

	public function beforeValidation(){
	    $flagStatus = true;
	    
	    if(!$this->estado){
	        $this->estado = 'A';//Activo
	    }
	    
	    $haveData = false;
	    foreach ($this->getAttributes() as $field){
	        if(!empty($this->$field) && $this->$field!='@' && !in_array($field,array('fecha_nacimiento','socios_id','id','estado'))){
	           //print "if(!empty(".$this->$field.") && ".$this->$field."!='@' && $field != 'fecha_nacimiento'){";
	           $haveData = true;    
	        }	        
	    }
	   
	    $socios = EntityManager::get('Socios');
	    $socios->setConnection($this->getConnection());
	    $socio = $socios->findFirst($this->socios_id);
	    if($socio == false){
	        $this->appendMessage(new ActiveRecordMessage('El socio no existe', 'identificacion'));
	        $flagStatus = false;
	    } else {
	        if($this->getValidar()==true){
        	    if($socio->getEstadoContrato() == 'AA'){
        	        $this->appendMessage(new ActiveRecordMessage('El socio esta anulado', 'identificacion'));
        	        $flagStatus = false;
        	    }
	        }
	    }
	    
	    //Si tiene datos validamos lo minimo que debe llenar
	    if($haveData == true && $this->validar!=false){	    
    	    if($this->tipo_documentos_id<=0){
    	        $this->appendMessage(new ActiveRecordMessage('El tipo de documento es obligatorio', 'conyuge_tipo_documentos_id'));
    	        $flagStatus = false;
    	    }
    	    if($this->identificacion<=0){
    	        $this->appendMessage(new ActiveRecordMessage('La Cédula es obligatoria', 'conyuge_identificacion'));
    	        $flagStatus = false;
    	    } else {
    	        $reservas = EntityManager::get('Reservas');
    	        $reservas->setConnection($this->getConnection());
    	        $reserva = $reservas->findFirst(array('conditions'=>'identificacion='.$this->identificacion.' AND estado_contrato=\'A\''));
    	        if($reserva != false){
    	           $this->appendMessage(new ActiveRecordMessage('La cedula ya esta siendo usada en la reserva '.$reserva->getNumeroContrato()));
    	           $flagStatus = false; 
    	        }
    	        $socio = $socios->findFirst(array('conditions'=>'identificacion='.$this->identificacion.' AND estado_contrato=\'A\''));
    	        if($socio != false){
    	           $this->appendMessage(new ActiveRecordMessage('La cedula ya esta siendo usada en el contrato '.$socio->getNumeroContrato()));
    	           $flagStatus = false; 
    	        }
    	    }
    	    if(!$this->nombres){
    	        $this->appendMessage(new ActiveRecordMessage('El nombre es obligatorio', 'conyuge_nombres'));
    	        $flagStatus = false;
    	    }
    	    if(!$this->apellidos){
    	        $this->appendMessage(new ActiveRecordMessage('Los apellidos son obligatorio', 'conyuge_apellidos'));
    	        $flagStatus = false;
    	    }
    	    if($this->celular<=0){
    	        $this->appendMessage(new ActiveRecordMessage('El celular es obligatorio', 'conyuge_celular'));
    	        $flagStatus = false;
    	    }
	    }    
	    
	    return $flagStatus;
	}

	public function initialize(){
		$this->addForeignKey('socios_id', 'Socios', 'id', array(
			'message' => 'El socio asociado al conyuge no existe'
		));
		$this->belongsTo('socios');
	}

}

