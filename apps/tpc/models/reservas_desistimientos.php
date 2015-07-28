<?php

class ReservasDesistimientos extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $reservas_id;

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
	 * Metodo para establecer el valor del campo reservas_id
	 * @param integer $reservas_id
	 */
	public function setReservasId($reservas_id){
		$this->reservas_id = $reservas_id;
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
	 * Devuelve el valor del campo reservas_id
	 * @return integer
	 */
	public function getReservasId(){
		return $this->reservas_id;
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
	    $reservas = EntityManager::get('Reservas')->findFirst($this->reservas_id);
	    if($reservas == false){
	        $this->appendMessage(new ActiveRecordMessage('La reserva no existe'));
	        return false;
	    } else {
	        //Si la reserva esta anulada no puede hacer desistimiento
	        if($reservas->getEstadoContrato()=='AA'){
	           $this->appendMessage(new ActiveRecordMessage('La reserva esta anulada'));
	           return false;
	        }
	    }	    
	}

	public function initialize(){
	    $this->addForeignKey('reservas_id', 'Reservas', 'id', array(
	    	'message' => 'La reserva no es valida'
	    ));
	    $this->addForeignKey('estado_desistimiento_id', 'EstadoDesistimiento', 'codigo', array(
	    	'message' => 'El estado de desistimiento no es valido'
	    ));
	    $this->addForeignKey('motivo_desistimiento_id', 'MotivoDesistimiento', 'id', array(
	    	'message' => 'El motivo de desistimiento no es valido'
	    ));
	}

}

