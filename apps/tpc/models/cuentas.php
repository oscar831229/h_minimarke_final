<?php

class Cuentas extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $banco;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $cuenta_contable;

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
	 * Metodo para establecer el valor del campo banco
	 * @param string $banco
	 */
	public function setBanco($banco){
		$this->banco = $banco;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_contable
	 * @param string $cuenta_contable
	 */
	public function setCuentaContable($cuenta_contable){
		$this->cuenta_contable = $cuenta_contable;
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
	 * Devuelve el valor del campo banco
	 * @return string
	 */
	public function getBanco(){
		return $this->banco;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo cuenta_contable
	 * @return string
	 */
	public function getCuentaContable(){
		return $this->cuenta_contable;
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
			'field' => 'cuenta',
			'message' => 'La cuenta debe ser única, otra cuenta bancaria ya tiene esta cuenta'
		));
		if($this->validationHasFailed()==true){
		    return false;
		}
	}
	
	public function beforeDelete(){
	    $status = true;
	    
	    $listModels = array(
	       'RecibosPagos'  => 'un recibo de caja'
	    );
	    foreach ($listModels as $model  => $label){
	        $modeloObj = EntityManager::get($model);
    	    $modeloObj->setConnection($this->getConnection());
    	    $exists = $modeloObj->exists('cuentas_id='.$this->id);
    	    if($exists == true){
    	        $this->appendMessage(new ActiveRecordMessage('La cuenta bancaria esta siendo usada en '.$label));
    	        $status = false;
    	    }    
	    }
	    
	    return $status;
	}

}

