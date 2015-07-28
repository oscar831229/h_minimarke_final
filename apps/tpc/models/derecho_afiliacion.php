<?php

class DerechoAfiliacion extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $tipo_contrato_id;

	/**
	 * @var string
	 */
	protected $valor;

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
	 * Metodo para establecer el valor del campo tipo_contrato_id
	 * @param integer $tipo_contrato_id
	 */
	public function setTipoContratoId($tipo_contrato_id){
		$this->tipo_contrato_id = $tipo_contrato_id;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
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
	 * Devuelve el valor del campo tipo_contrato_id
	 * @return integer
	 */
	public function getTipoContratoId(){
		return $this->tipo_contrato_id;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}
	
	protected function validation(){
		if($this->id>0){
		    $exists = $this->exists('tipo_contrato_id='.$this->tipo_contrato_id.' AND valor='.$this->valor.' AND id<>'.$this->id);
		    /*$this->validate('Uniqueness', array(
				'field' => 'tipo_contrato_id,valor',
				'message' => 'El nombre debe ser único, otro estado civil ya tiene este nombre'
			));
			if($this->validationHasFailed()==true){
			    return false;
			}*/
		    if($exists==true){
		        $this->appendMessage(new ActiveRecordMessage('El tipo de contrato y valor debe ser único, otro derecho de afilaición ya tiene estos datos'));
		        return false;
		    }
	    }
	}
	
	public function beforeDelete(){
	    $status = true;
	    
	    $listModels = array(
	       'MembresiasSocios'  => 'una membresia de un contrato'
	    );
	    foreach ($listModels as $model  => $label){
	        $modeloObj = EntityManager::get($model);
    	    $modeloObj->setConnection($this->getConnection());
    	    $exists = $modeloObj->exists('derecho_afiliacion_id='.$this->id);
    	    if($exists == true){
    	        $this->appendMessage(new ActiveRecordMessage('El derecho de afiliación esta siendo usado en '.$label));
    	        $status = false;
    	    }    
	    }
	    
	    return $status;
	}
	
	public function initialize(){
	    $this->addForeignKey('tipo_contrato_id', 'TipoContrato', 'id', array(
	    	'message' => 'El tipo de contrato no es valido'
	    ));
	    $this->hasOne('tipo_contrato_id','TipoContrato','id');
	}

}

