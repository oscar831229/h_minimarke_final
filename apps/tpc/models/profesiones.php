<?php

class Profesiones extends RcsRecord {

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
			'message' => 'El nombre debe ser único, otra profesión ya tiene este nombre'
		));
		if($this->validationHasFailed()==true){
		    return false;
		}
	}

	public function beforeDelete(){
	    $status = true;
	    
	    $listModels = array(
	       'Reservas'  => 'reservas',
	       'Socios'    => 'un contrato',
	       'Conyuges'  => 'un segundo titular'
	    );
	    foreach ($listModels as $model  => $label){
	        $modeloObj = EntityManager::get($model);
    	    $modeloObj->setConnection($this->getConnection());
    	    $exists = $modeloObj->exists('profesiones_id='.$this->id);
    	    if($exists == true){
    	        $this->appendMessage(new ActiveRecordMessage('La profesión esta siendo usada en '.$label));
    	        $status = false;
    	    }    
	    }
	    
	    return $status;
	}

}

