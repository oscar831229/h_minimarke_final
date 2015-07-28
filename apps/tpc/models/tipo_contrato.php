<?php

class TipoContrato extends RcsRecord {

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
	protected $sigla;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $usa_formato;

	/**
	 * @var string
	 */
	protected $formato;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var boolean
	 */
	protected $validar;


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
	 * Metodo para establecer el valor del campo sigla
	 * @param string $sigla
	 */
	public function setSigla($sigla){
		$this->sigla = $sigla;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo usa_formato
	 * @param string $usa_formato
	 */
	public function setUsaFormato($usa_formato){
		$this->usa_formato = $usa_formato;
	}

	/**
	 * Metodo para establecer el valor del campo formato
	 * @param string $formato
	 */
	public function setFormato($formato){
		$this->formato = $formato;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer si se valida o no
	 * @param string $validar
	 */
	public function setValidar($validar){
		$this->validar = $validar;
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
	 * Devuelve el valor del campo sigla
	 * @return string
	 */
	public function getSigla(){
		return $this->sigla;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo usa_formato
	 * @return string
	 */
	public function getUsaFormato(){
		return $this->usa_formato;
	}

	/**
	 * Devuelve el valor del campo formato
	 * @return string
	 */
	public function getFormato(){
		return $this->formato;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Metodo para obtiene si se valida o no
	 * @param string $validar
	 */
	public function getValidar(){
		return $this->validar;
	}

	protected function beforeValidation(){
		$this->sigla = i18n::strtoupper($this->sigla);
		if($this->usa_formato=='S'){
			if(trim($this->formato)==''){
				$this->appendMessage(new ActiveRecordMessage('El contrato usa formato pero no indicó cual es el formato'));
				return false;
			}
		}
		return true;
	}
	
	public function beforeValidationOnUpdate(){
		if($this->validar!=false){
		    $Socios = EntityManager::get('Socios');
		    $Socios->setConnection($this->getConnection());
		    $maxNumeroContrato = $Socios->maximum(array('numero_contrato', 'conditions' => "tipo_contrato_id='{$this->id}'"));
		    if($maxNumeroContrato){
	    	    $maxConsecutivoArray = explode('-', $maxNumeroContrato);
	    	    $maxConsecutivo = $maxConsecutivoArray[count($maxConsecutivoArray)-1];
	    	    //$this->appendMessage(new ActiveRecordMessage($maxNumeroContrato.', '.print_r($maxConsecutivoArray,true).', '.$maxConsecutivo));return false;
	    	    if ($this->numero < $maxConsecutivo){
	    	        $this->appendMessage(new ActiveRecordMessage('El numero de consecutivo debe ser mayor o igual a '.$maxConsecutivo));
	    	        return false;
	    	    }
		    }
		}
	}

	protected function validation(){
		$this->validate('Uniqueness', array(
			'field' => 'sigla',
			'message' => 'La sigla debe ser única, otro contrato ya tiene esta sigla'
		));
		if($this->validationHasFailed()==true){
		    return false;
		}
		$this->validate('Uniqueness', array(
			'field' => 'nombre',
			'message' => 'El nombre debe ser único, otro tipo de contrato ya tiene esta nombre'
		));
		if($this->validationHasFailed()==true){
		    return false;
		}
	}

	public function beforeDelete(){
	    if($this->id>0){
		    $socios = EntityManager::get('Socios')->count('tipo_contrato_id='.$this->id);
		    if($socios>0){
		        $this->appendMessage(new ActiveRecordMessage('El tipo de contrato esta siendo usado en contratos'));
		        return false;
		    }
	    }
	    return true;
	}

}

