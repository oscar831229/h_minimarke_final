<?php

class Refinanciar extends ActiveRecord {

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
	protected $valor;

	/**
	 * @var integer
	 */
	protected $numero_cuotas;

	/**
	 * @var string
	 */
	protected $interes;

	/**
	 * @var Date
	 */
	protected $fecha_primera_cuota;


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
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo numero_cuotas
	 * @param integer $numero_cuotas
	 */
	public function setNumeroCuotas($numero_cuotas){
		$this->numero_cuotas = $numero_cuotas;
	}

	/**
	 * Metodo para establecer el valor del campo interes
	 * @param string $interes
	 */
	public function setInteres($interes){
		$this->interes = $interes;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_primera_cuota
	 * @param Date $fecha_primera_cuota
	 */
	public function setFechaPrimeraCuota($fecha_primera_cuota){
		$this->fecha_primera_cuota = $fecha_primera_cuota;
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
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo numero_cuotas
	 * @return integer
	 */
	public function getNumeroCuotas(){
		return $this->numero_cuotas;
	}

	/**
	 * Devuelve el valor del campo interes
	 * @return string
	 */
	public function getInteres(){
		return $this->interes;
	}

	/**
	 * Devuelve el valor del campo fecha_primera_cuota
	 * @return Date
	 */
	public function getFechaPrimeraCuota(){
		if($this->fecha_primera_cuota){
			return new Date($this->fecha_primera_cuota);
		} else {
			return null;
		}
	}

	public function beforeValidation(){

	    $status = true;
	    
	    if($this->valor<=0){
	       $this->appendMessage(new ActiveRecordMessage('El valor pagado es obligatorio', 'valorPagado'));
		   $status = false;
	    }else{
	    	//Solo si hay saldo a pagar se aplica interes por eso solo sihat saldo se requeire el interes corriente
	    	$limiteMesesInteres = Settings::get('limite_meses_interes');
		    if($limiteMesesInteres < $this->numero_cuotas && $this->interes <= 0){
		       $this->appendMessage(new ActiveRecordMessage('El interes corriente es obligatorio '.$this->interes.' , '.$this->valor, 'interesCorriente'));
			   $status = false; 
		    }
	    }
	    if($this->numero_cuotas <= 0){
	       $this->appendMessage(new ActiveRecordMessage('El numero de cuotas es oblogatorio', 'numCuotas'));
		   $status = false;
	    }
	    

	    return $status;

	}

	public function initialize(){
	    $this->addForeignKey('socios_id', 'Socios', 'id', array(
	    	'message' => 'El socio asociado a la membresía no existe'
	    ));
	}

}

