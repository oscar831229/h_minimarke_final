<?php

class CargosFijos extends ActiveRecord {

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
	protected $valor;

	/**
	 * @var string
	 */
	protected $cuenta_credito;

	/**
	 * @var string
	 */
	protected $cuenta_iva_deb;

	/**
	 * @var string
	 */
	protected $centro_costos;

	/**
	 * @var string
	 */
	protected $centro_costos_iva;

	/**
	 * @var string
	 */
	protected $iva;

	/**
	 * @var string
	 */
	protected $porcentaje_iva;

	/**
	 * @var string
	 */
	protected $mora;

	/**
	 * @var string
	 */
	protected $tipo_cargo;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $cuenta_debito;

	/**
	 * @var string
	 */
	protected $ingreso_tercero;

	/**
	 * @var string
	 */
	protected $tercero_fijo;

	/**
	 * @var string
	 */
	protected $clase_cargo;

	/**
	 * @var string
	 */
	protected $ico;

	/**
	 * @var string
	 */
	protected $cuenta_ico_deb;

	/**
	 * @var string
	 */
	protected $cuenta_iva_cre;

	/**
	 * @var string
	 */
	protected $cuenta_ico_cre;


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
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_credito
	 * @param string $cuenta_credito
	 */
	public function setCuentaCredito($cuenta_credito){
		$this->cuenta_credito = $cuenta_credito;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_iva_deb
	 * @param string $cuenta_iva_deb
	 */
	public function setCuentaIvaDeb($cuenta_iva_deb){
		$this->cuenta_iva_deb = $cuenta_iva_deb;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costos
	 * @param string $centro_costos
	 */
	public function setCentroCostos($centro_costos){
		$this->centro_costos = $centro_costos;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costos_iva
	 * @param string $centro_costos_iva
	 */
	public function setCentroCostosIva($centro_costos_iva){
		$this->centro_costos_iva = $centro_costos_iva;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param string $iva
	 */
	public function setIva($iva){
		$this->iva = $iva;
	}

	/**
	 * Metodo para establecer el valor del campo porcentaje_iva
	 * @param string $porcentaje_iva
	 */
	public function setPorcentajeIva($porcentaje_iva){
		$this->porcentaje_iva = $porcentaje_iva;
	}

	/**
	 * Metodo para establecer el valor del campo mora
	 * @param string $mora
	 */
	public function setMora($mora){
		$this->mora = $mora;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_cargo
	 * @param string $tipo_cargo
	 */
	public function setTipoCargo($tipo_cargo){
		$this->tipo_cargo = $tipo_cargo;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_debito
	 * @param string $cuenta_debito
	 */
	public function setCuentaDebito($cuenta_debito){
		$this->cuenta_debito = $cuenta_debito;
	}

	/**
	 * Metodo para establecer el valor del campo ingreso_tercero
	 * @param string $ingreso_tercero
	 */
	public function setIngresoTercero($ingreso_tercero){
		$this->ingreso_tercero = $ingreso_tercero;
	}

	/**
	 * Metodo para establecer el valor del campo tercero_fijo
	 * @param string $tercero_fijo
	 */
	public function setTerceroFijo($tercero_fijo){
		$this->tercero_fijo = $tercero_fijo;
	}

	/**
	 * Metodo para establecer el valor del campo clase_cargo
	 * @param string $clase_cargo
	 */
	public function setClaseCargo($clase_cargo){
		$this->clase_cargo = $clase_cargo;
	}

	/**
	 * Metodo para establecer el valor del campo ico
	 * @param string $ico
	 */
	public function setIco($ico){
		$this->ico = $ico;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_ico_deb
	 * @param string $cuenta_ico_deb
	 */
	public function setCuentaIcoDeb($cuenta_ico_deb){
		$this->cuenta_ico_deb = $cuenta_ico_deb;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_iva_cre
	 * @param string $cuenta_iva_cre
	 */
	public function setCuentaIvaCre($cuenta_iva_cre){
		$this->cuenta_iva_cre = $cuenta_iva_cre;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_ico_cre
	 * @param string $cuenta_ico_cre
	 */
	public function setCuentaIcoCre($cuenta_ico_cre){
		$this->cuenta_ico_cre = $cuenta_ico_cre;
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
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo cuenta_credito
	 * @return string
	 */
	public function getCuentaCredito(){
		return $this->cuenta_credito;
	}

	/**
	 * Devuelve el valor del campo cuenta_iva_deb
	 * @return string
	 */
	public function getCuentaIvaDeb(){
		return $this->cuenta_iva_deb;
	}

	/**
	 * Devuelve el valor del campo centro_costos
	 * @return string
	 */
	public function getCentroCostos(){
		return $this->centro_costos;
	}

	/**
	 * Devuelve el valor del campo centro_costos_iva
	 * @return string
	 */
	public function getCentroCostosIva(){
		return $this->centro_costos_iva;
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return string
	 */
	public function getIva(){
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo porcentaje_iva
	 * @return string
	 */
	public function getPorcentajeIva(){
		return $this->porcentaje_iva;
	}

	/**
	 * Devuelve el valor del campo mora
	 * @return string
	 */
	public function getMora(){
		return $this->mora;
	}

	/**
	 * Devuelve el valor del campo tipo_cargo
	 * @return string
	 */
	public function getTipoCargo(){
		return $this->tipo_cargo;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo cuenta_debito
	 * @return string
	 */
	public function getCuentaDebito(){
		return $this->cuenta_debito;
	}

	/**
	 * Devuelve el valor del campo ingreso_tercero
	 * @return string
	 */
	public function getIngresoTercero(){
		return $this->ingreso_tercero;
	}

	/**
	 * Devuelve el valor del campo tercero_fijo
	 * @return string
	 */
	public function getTerceroFijo(){
		return $this->tercero_fijo;
	}

	/**
	 * Devuelve el valor del campo clase_cargo
	 * @return string
	 */
	public function getClaseCargo(){
		return $this->clase_cargo;
	}

	/**
	 * Devuelve el valor del campo ico
	 * @return string
	 */
	public function getIco(){
		return $this->ico;
	}

	/**
	 * Devuelve el valor del campo cuenta_ico_deb
	 * @return string
	 */
	public function getCuentaIcoDeb(){
		return $this->cuenta_ico_deb;
	}

	/**
	 * Devuelve el valor del campo cuenta_iva_cre
	 * @return string
	 */
	public function getCuentaIvaCre(){
		return $this->cuenta_iva_cre;
	}

	/**
	 * Devuelve el valor del campo cuenta_ico_cre
	 * @return string
	 */
	public function getCuentaIcoCre(){
		return $this->cuenta_ico_cre;
	}

}

