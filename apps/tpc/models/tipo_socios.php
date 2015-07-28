<?php

class TipoSocios extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var integer
	 */
	protected $tipo_contrato_id;

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
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_contrato_id
	 * @param integer $tipo_contrato_id
	 */
	public function setTipoContratoId($tipo_contrato_id){
		$this->tipo_contrato_id = $tipo_contrato_id;
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
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo tipo_contrato_id
	 * @return integer
	 */
	public function getTipoContratoId(){
		return $this->tipo_contrato_id;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function initialize(){ 
	    $this->hasOne('tipo_contrato_id','tipo_contrato','id');
	}
	
	protected function validation(){
		$this->validate('Uniqueness', array(
			'field' => 'nombre',
			'message' => 'El nombre debe ser único, otro tipo de socio ya tiene este nombre'
		));
		if($this->validationHasFailed()==true){
		    return false;
		}
	}


	public function beforeDelete(){
	    $status = true;
	    
	    $listModels = array(
	       'Reservas'  => 'una reserva',
	       'Socios'    => 'un contrato'	       
	    );
	    foreach ($listModels as $model  => $label){
	        $modeloObj = EntityManager::get($model);
    	    $modeloObj->setConnection($this->getConnection());
    	    $exists = $modeloObj->exists('tipo_socios_id='.$this->id);
    	    if($exists == true){
    	        $this->appendMessage(new ActiveRecordMessage('El tipo de socio esta siendo usado en '.$label));
    	        $status = false;
    	    }    
	    }
	    
	    return $status;
	}

}

