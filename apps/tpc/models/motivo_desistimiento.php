<?php

class MotivoDesistimiento extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

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
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}
	
	protected function validation(){
		$this->validate('Uniqueness', array(
			'field' => 'nombre',
			'message' => 'El nombre debe ser único, otro motivo de desistimiento ya tiene este nombre'
		));
		if($this->validationHasFailed()==true){
		    return false;
		}
	}
	
	public function beforeDelete(){
	    $status = true;
	    
	    $listModels = array(
	       'ReservasDesistimientos'  => 'un desistimiento de reserva',
	       'SociosDesistimientos'  => 'un desistimiento de contrato'
	    );
	    foreach ($listModels as $model  => $label){
	        $modeloObj = EntityManager::get($model);
    	    $modeloObj->setConnection($this->getConnection());
    	    $exists = $modeloObj->exists('motivo_desistimiento_id='.$this->id);
    	    if($exists == true){
    	        $this->appendMessage(new ActiveRecordMessage('El motivo de desistimiento esta siendo usado en '.$label));
    	        $status = false;
    	    }    
	    }
	    
	    return $status;
	}


}

