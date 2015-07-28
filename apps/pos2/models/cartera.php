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

class Cartera extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $tipo_doc;

	/**
	 * @var integer
	 */
	protected $numero_doc;

	/**
	 * @var string
	 */
	protected $vendedor;

	/**
	 * @var string
	 */
	protected $centro_costo;

	/**
	 * @var Date
	 */
	protected $f_emision;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $saldo;

	/**
	 * @var Date
	 */
	protected $f_vence;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_doc
	 * @param string $tipo_doc
	 */
	public function setTipoDoc($tipo_doc){
		$this->tipo_doc = $tipo_doc;
	}

	/**
	 * Metodo para establecer el valor del campo numero_doc
	 * @param integer $numero_doc
	 */
	public function setNumeroDoc($numero_doc){
		$this->numero_doc = $numero_doc;
	}

	/**
	 * Metodo para establecer el valor del campo vendedor
	 * @param string $vendedor
	 */
	public function setVendedor($vendedor){
		$this->vendedor = $vendedor;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param string $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo f_emision
	 * @param Date $f_emision
	 */
	public function setFEmision($f_emision){
		$this->f_emision = $f_emision;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo saldo
	 * @param string $saldo
	 */
	public function setSaldo($saldo){
		$this->saldo = $saldo;
	}

	/**
	 * Metodo para establecer el valor del campo f_vence
	 * @param Date $f_vence
	 */
	public function setFVence($f_vence){
		$this->f_vence = $f_vence;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo tipo_doc
	 * @return string
	 */
	public function getTipoDoc(){
		return $this->tipo_doc;
	}

	/**
	 * Devuelve el valor del campo numero_doc
	 * @return integer
	 */
	public function getNumeroDoc(){
		return $this->numero_doc;
	}

	/**
	 * Devuelve el valor del campo vendedor
	 * @return string
	 */
	public function getVendedor(){
		return $this->vendedor;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return string
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo f_emision
	 * @return Date
	 */
	public function getFEmision(){
		return new Date($this->f_emision);
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo saldo
	 * @return string
	 */
	public function getSaldo(){
		return $this->saldo;
	}

	/**
	 * Devuelve el valor del campo f_vence
	 * @return Date
	 */
	public function getFVence(){
		return new Date($this->f_vence);
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function initialize(){
		$config = CoreConfig::readFromActiveApplication('app.ini', 'ini');
		if(isset($config->pos->ramocol)){
			$this->setSchema($config->pos->ramocol);
		} else {
			$this->setSchema('ramocol');
		}
	}

}

