<?php

class SociosDesistimientos extends RcsRecord {

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
	 * @var integer
	 */
	protected $motivo_desistimiento_id;

	/**
	 * @var string
	 */
	protected $estado_desistimiento_id;


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
	 * Metodo para establecer el valor del campo motivo_desistimiento_id
	 * @param integer $motivo_desistimiento_id
	 */
	public function setMotivoDesistimientoId($motivo_desistimiento_id){
		$this->motivo_desistimiento_id = $motivo_desistimiento_id;
	}

	/**
	 * Metodo para establecer el valor del campo estado_desistimiento_id
	 * @param string $estado_desistimiento_id
	 */
	public function setEstadoDesistimientoId($estado_desistimiento_id){
		$this->estado_desistimiento_id = $estado_desistimiento_id;
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

	/**
	 * Devuelve el valor del campo motivo_desistimiento_id
	 * @return integer
	 */
	public function getMotivoDesistimientoId(){
		return $this->motivo_desistimiento_id;
	}

	/**
	 * Devuelve el valor del campo estado_desistimiento_id
	 * @return string
	 */
	public function getEstadoDesistimientoId(){
		return $this->estado_desistimiento_id;
	}

	public function beforeValidationOnCreate(){
	    $socios = EntityManager::get('Socios')->findFirst($this->socios_id);
	    if($socios == false){
	        $this->appendMessage(new ActiveRecordMessage('El contrato no existe'));
	        return false;
	    } else {
	        //Si El contrato esta anulada no puede hacer desistimiento
	        if($socios->getEstadoContrato()=='AA'){
	           $this->appendMessage(new ActiveRecordMessage('El contrato esta anulado'));
	           return false;
	        }
	    }	    
	}

	public function initialize(){
	    $this->addForeignKey('socios_id', 'Socios', 'id', array(
	    	'message' => 'El contrato no es valido'
	    ));
	    $this->addForeignKey('estado_desistimiento_id', 'EstadoDesistimiento', 'codigo', array(
	    	'message' => 'El estado de desistimiento no es valido'
	    ));
	    $this->addForeignKey('motivo_desistimiento_id', 'MotivoDesistimiento', 'id', array(
	    	'message' => 'El motivo de desistimiento no es valido'
	    ));
	}

}

