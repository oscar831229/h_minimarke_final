<?php

class InteresUsura extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var Date
	 */
	protected $fecha_inicial;

	/**
	 * @var Date
	 */
	protected $fecha_final;

	/**
	 * @var string
	 */
	protected $interes_trimestral;

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
	 * Metodo para establecer el valor del campo fecha_inicial
	 * @param Date $fecha_inicial
	 */
	public function setFechaInicial($fecha_inicial){
		$this->fecha_inicial = $fecha_inicial;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_final
	 * @param Date $fecha_final
	 */
	public function setFechaFinal($fecha_final){
		$this->fecha_final = $fecha_final;
	}

	/**
	 * Metodo para establecer el valor del campo interes_trimestral
	 * @param string $interes_trimestral
	 */
	public function setInteresTrimestral($interes_trimestral){
		$this->interes_trimestral = $interes_trimestral;
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
	 * Devuelve el valor del campo fecha_inicial
	 * @return Date
	 */
	public function getFechaInicial(){
		if($this->fecha_inicial){
			return new Date($this->fecha_inicial);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecha_final
	 * @return Date
	 */
	public function getFechaFinal(){
		if($this->fecha_final){
			return new Date($this->fecha_final);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo interes_trimestral
	 * @return string
	 */
	public function getInteresTrimestral(){
		return $this->interes_trimestral;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function beforeSave(){
		$InteresUsura = EntityManager::getEntityInstance('InteresUsura');
		$id = $this->getId();
		
		$sqlIni = "'".$this->getFechaInicial()."' BETWEEN fecha_inicial AND fecha_final";
		if($id){
		    $sqlIni .= " AND id<>".$this->getId();
		}
		$flagIni = $InteresUsura->exists($sqlIni);
		
		$sqlFin = "'".$this->getFechaFinal()."' BETWEEN fecha_inicial AND fecha_final";
		if($id){
		    $sqlFin .= " AND id<>".$this->getId();
		}
		$flagFin = $InteresUsura->exists($sqlFin);
		
		if($flagIni == true || $flagFin == true){
			$this->appendMessage(new ActiveRecordMessage('Ya existe un interes de mora en ese rango de fechas', 
			'interes_usura'));
			return false;
		}		
		return true;
	}

}

