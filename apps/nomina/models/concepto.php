<?php

class Concepto extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_concepto;

	/**
	 * @var string
	 */
	protected $vacaciones;

	/**
	 * @var string
	 */
	protected $aportes;

	/**
	 * @var string
	 */
	protected $prestacion;

	/**
	 * @var string
	 */
	protected $base_iss;

	/**
	 * @var string
	 */
	protected $retencion;

	/**
	 * @var string
	 */
	protected $porc_ret;

	/**
	 * @var string
	 */
	protected $salario;

	/**
	 * @var string
	 */
	protected $porc_salario;

	/**
	 * @var string
	 */
	protected $recargo;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $contra;

	/**
	 * @var string
	 */
	protected $netea;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_concepto
	 * @param string $nom_concepto
	 */
	public function setNomConcepto($nom_concepto){
		$this->nom_concepto = $nom_concepto;
	}

	/**
	 * Metodo para establecer el valor del campo vacaciones
	 * @param string $vacaciones
	 */
	public function setVacaciones($vacaciones){
		$this->vacaciones = $vacaciones;
	}

	/**
	 * Metodo para establecer el valor del campo aportes
	 * @param string $aportes
	 */
	public function setAportes($aportes){
		$this->aportes = $aportes;
	}

	/**
	 * Metodo para establecer el valor del campo prestacion
	 * @param string $prestacion
	 */
	public function setPrestacion($prestacion){
		$this->prestacion = $prestacion;
	}

	/**
	 * Metodo para establecer el valor del campo base_iss
	 * @param string $base_iss
	 */
	public function setBaseIss($base_iss){
		$this->base_iss = $base_iss;
	}

	/**
	 * Metodo para establecer el valor del campo retencion
	 * @param string $retencion
	 */
	public function setRetencion($retencion){
		$this->retencion = $retencion;
	}

	/**
	 * Metodo para establecer el valor del campo porc_ret
	 * @param string $porc_ret
	 */
	public function setPorcRet($porc_ret){
		$this->porc_ret = $porc_ret;
	}

	/**
	 * Metodo para establecer el valor del campo salario
	 * @param string $salario
	 */
	public function setSalario($salario){
		$this->salario = $salario;
	}

	/**
	 * Metodo para establecer el valor del campo porc_salario
	 * @param string $porc_salario
	 */
	public function setPorcSalario($porc_salario){
		$this->porc_salario = $porc_salario;
	}

	/**
	 * Metodo para establecer el valor del campo recargo
	 * @param string $recargo
	 */
	public function setRecargo($recargo){
		$this->recargo = $recargo;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo contra
	 * @param string $contra
	 */
	public function setContra($contra){
		$this->contra = $contra;
	}

	/**
	 * Metodo para establecer el valor del campo netea
	 * @param string $netea
	 */
	public function setNetea($netea){
		$this->netea = $netea;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_concepto
	 * @return string
	 */
	public function getNomConcepto(){
		return $this->nom_concepto;
	}

	/**
	 * Devuelve el valor del campo vacaciones
	 * @return string
	 */
	public function getVacaciones(){
		return $this->vacaciones;
	}

	/**
	 * Devuelve el valor del campo aportes
	 * @return string
	 */
	public function getAportes(){
		return $this->aportes;
	}

	/**
	 * Devuelve el valor del campo prestacion
	 * @return string
	 */
	public function getPrestacion(){
		return $this->prestacion;
	}

	/**
	 * Devuelve el valor del campo base_iss
	 * @return string
	 */
	public function getBaseIss(){
		return $this->base_iss;
	}

	/**
	 * Devuelve el valor del campo retencion
	 * @return string
	 */
	public function getRetencion(){
		return $this->retencion;
	}

	/**
	 * Devuelve el valor del campo porc_ret
	 * @return string
	 */
	public function getPorcRet(){
		return $this->porc_ret;
	}

	/**
	 * Devuelve el valor del campo salario
	 * @return string
	 */
	public function getSalario(){
		return $this->salario;
	}

	/**
	 * Devuelve el valor del campo porc_salario
	 * @return string
	 */
	public function getPorcSalario(){
		return $this->porc_salario;
	}

	/**
	 * Devuelve el valor del campo recargo
	 * @return string
	 */
	public function getRecargo(){
		return $this->recargo;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo contra
	 * @return string
	 */
	public function getContra(){
		return $this->contra;
	}

	/**
	 * Devuelve el valor del campo netea
	 * @return string
	 */
	public function getNetea(){
		return $this->netea;
	}

}

