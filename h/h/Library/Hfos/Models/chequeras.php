<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Chequeras extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $cuentas_bancos_id;

	/**
	 * @var integer
	 */
	protected $numero_inicial;

	/**
	 * @var integer
	 */
	protected $numero_final;

	/**
	 * @var integer
	 */
	protected $numero_actual;

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
	 * Metodo para establecer el valor del campo cuentas_bancos_id
	 * @param integer $cuentas_bancos_id
	 */
	public function setCuentasBancosId($cuentas_bancos_id){
		$this->cuentas_bancos_id = $cuentas_bancos_id;
	}

	/**
	 * Metodo para establecer el valor del campo numero_inicial
	 * @param integer $numero_inicial
	 */
	public function setNumeroInicial($numero_inicial){
		$this->numero_inicial = $numero_inicial;
	}

	/**
	 * Metodo para establecer el valor del campo numero_final
	 * @param integer $numero_final
	 */
	public function setNumeroFinal($numero_final){
		$this->numero_final = $numero_final;
	}

	/**
	 * Metodo para establecer el valor del campo numero_actual
	 * @param integer $numero_actual
	 */
	public function setNumeroActual($numero_actual){
		$this->numero_actual = $numero_actual;
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
	 * Devuelve el valor del campo cuentas_bancos_id
	 * @return integer
	 */
	public function getCuentasBancosId(){
		return $this->cuentas_bancos_id;
	}

	/**
	 * Devuelve el valor del campo numero_inicial
	 * @return integer
	 */
	public function getNumeroInicial(){
		return $this->numero_inicial;
	}

	/**
	 * Devuelve el valor del campo numero_final
	 * @return integer
	 */
	public function getNumeroFinal(){
		return $this->numero_final;
	}

	/**
	 * Devuelve el valor del campo numero_actual
	 * @return integer
	 */
	public function getNumeroActual(){
		return $this->numero_actual;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	protected function beforeValidation(){
		if($this->numero_actual==0){
			if($this->_operationMade==self::OP_CREATE){
				$this->numero_actual = $this->numero_inicial;
			} else {
				$maxCheque = EntityManager::get('Cheque')->maximum(array('numero', 'conditions' => "chequeras_id='{$this->id}' AND estado='E'"));
				$this->numero_actual = $maxCheque;
			}
		}
	}

	public function beforeDelete(){
		if($this->countCheque()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la chequera porque tiene cheques asociados', 'id'));
			return false;
		}
	}

	protected function validation(){
		if($this->numero_final<$this->numero_inicial){
			$this->appendMessage(new ActiveRecordMessage('El consecutivo final debe ser mayor al inicial', 'numero_inicial'));
			return false;
		}
		$minCheque = EntityManager::get('Cheque')->minimum(array('numero', 'conditions' => "chequeras_id='{$this->id}' AND estado='E'"));
		if($minCheque>0){
			if($this->numero_inicial>$minCheque){
				$this->appendMessage(new ActiveRecordMessage('No se puede asignar el numero inicial del cheque porque hay cheques emitidos por fuera de la numeración ('.$minCheque.')', 'numero_inicial'));
				return false;
			}
		}
		$maxCheque = EntityManager::get('Cheque')->maximum(array('numero', 'conditions' => "chequeras_id='{$this->id}' AND estado='E'"));
		if($maxCheque>0){
			if($this->numero_final<$maxCheque){
				$this->appendMessage(new ActiveRecordMessage('No se puede asignar el numero final del cheque porque hay cheques emitidos por fuera de la numeración ('.$maxCheque.')', 'numero_final'));
				return false;
			}
		}
		$this->validate('InclusionIn', array(
			'field' => 'estado',
			'domain' => array('A', 'I'),
			'message' => 'El estado debe ser "ACTIVO" ó "INACTIVO"',
			'required' => true
		));
		if($this->validationHasFailed()==true){
			return false;
		}
	}

	public function initialize(){

		$this->belongsTo('CuentasBancos');
		$this->hasMany('Cheque');

		$this->addForeignKey('cuentas_bancos_id', 'cuentas_bancos', 'id', array(
			'message' => 'La cuenta bancaria indicada no es válida'
		));
	}

}

