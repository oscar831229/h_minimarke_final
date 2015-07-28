<?php

class Retoma extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var Date
	 */
	protected $fecha;


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
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
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
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		if($this->fecha){
			return new Date($this->fecha);
		} else {
			return null;
		}
	}

	public function beforeValidationOnCreate(){
	    $socios = EntityManager::get('Socios')->findFirst($this->socios_id);
	    if($socios == false){
	        $this->appendMessage(new ActiveRecordMessage('El contrato no existe'));
	        return false;
	    } else {
	        //Si El contrato esta anulada no puede hacer desistimiento
	        if($socios->getEstadoContrato()=='A'){
	           $this->appendMessage(new ActiveRecordMessage('El contrato esta activo'));
	           return false;
	        }
	    }	    
	}

	public function initialize(){
	    $this->addForeignKey('socios_id', 'Socios', 'id', array(
	    	'message' => 'El contrato no es valido'
	    ));
	}

}

