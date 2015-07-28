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
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class CuentasBancos extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $es_sucursal;

	/**
	 * @var string
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $banco_id;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $transferencia;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var integer
	 */
	protected $centro_costo;

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
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo es_sucursal
	 * @param string $es_sucursal
	 */
	public function setEsSucursal($es_sucursal){
		$this->es_sucursal = $es_sucursal;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param string $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo banco_id
	 * @param integer $banco_id
	 */
	public function setBancoId($banco_id){
		$this->banco_id = $banco_id;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo transferencia
	 * @param string $transferencia
	 */
	public function setTransferencia($transferencia){
		$this->transferencia = $transferencia;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param integer $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
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
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo es_sucursal
	 * @return string
	 */
	public function getEsSucursal(){
		return $this->es_sucursal;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return string
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo banco_id
	 * @return integer
	 */
	public function getBancoId(){
		return $this->banco_id;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo transferencia
	 * @return string
	 */
	public function getTransferencia(){
		return $this->transferencia;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return integer
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function beforeSave(){
		if($this->cuenta!=''){
			$cuenta = BackCacher::getCuenta($this->cuenta);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta contable no existe', 'cuenta'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta contable no es auxiliar', 'cuenta'));
					return false;
				}
			}
		}
		if($this->centro_costo>0){
			$empresa = EntityManager::get('Empresa')->findFirst();
			if($empresa->getCentroCosto()==$this->centro_costo){
				$this->appendMessage(new ActiveRecordMessage('La cuenta bancaria no puede tener el centro de costo del hotel', 'centro_costo'));
				return false;
			}
		}
	}

	public function beforeDelete(){
		if($this->countChequeras()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la cuenta bancaria porque tiene chequeras asociadas', 'descripcion'));
			return false;
		}
	}

	public function initialize(){
		$this->addForeignKey('banco_id', 'Banco', 'id', array(
			'message' => 'El banco indicado no es válido'
		));
		$this->addForeignKey('centro_costo', 'Centros', 'codigo', array(
			'message' => 'El centro de costo indicado no es válido'
		));
		$this->addForeignKey('cuenta', 'Cuentas', 'cuenta', array(
			'message' => 'La cuenta indicada no es válida'
		));
		$this->hasMany('Chequeras');
		$this->belongsTo('Banco');
	}

}

