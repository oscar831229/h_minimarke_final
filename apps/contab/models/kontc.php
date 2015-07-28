<?php

class Kontc extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $informe;

	/**
	 * @var string
	 */
	protected $linea;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $salto;

	/**
	 * @var string
	 */
	protected $pagina;

	/**
	 * @var string
	 */
	protected $columna;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $saldo_a;

	/**
	 * @var string
	 */
	protected $debe;

	/**
	 * @var string
	 */
	protected $haber;

	/**
	 * @var string
	 */
	protected $saldo;

	/**
	 * @var string
	 */
	protected $cuenta_i;

	/**
	 * @var string
	 */
	protected $cuenta_f;

	/**
	 * @var string
	 */
	protected $operador;


	/**
	 * Metodo para establecer el valor del campo informe
	 * @param string $informe
	 */
	public function setInforme($informe){
		$this->informe = $informe;
	}

	/**
	 * Metodo para establecer el valor del campo linea
	 * @param string $linea
	 */
	public function setLinea($linea){
		$this->linea = $linea;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo salto
	 * @param string $salto
	 */
	public function setSalto($salto){
		$this->salto = $salto;
	}

	/**
	 * Metodo para establecer el valor del campo pagina
	 * @param string $pagina
	 */
	public function setPagina($pagina){
		$this->pagina = $pagina;
	}

	/**
	 * Metodo para establecer el valor del campo columna
	 * @param string $columna
	 */
	public function setColumna($columna){
		$this->columna = $columna;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_a
	 * @param string $saldo_a
	 */
	public function setSaldoA($saldo_a){
		$this->saldo_a = $saldo_a;
	}

	/**
	 * Metodo para establecer el valor del campo debe
	 * @param string $debe
	 */
	public function setDebe($debe){
		$this->debe = $debe;
	}

	/**
	 * Metodo para establecer el valor del campo haber
	 * @param string $haber
	 */
	public function setHaber($haber){
		$this->haber = $haber;
	}

	/**
	 * Metodo para establecer el valor del campo saldo
	 * @param string $saldo
	 */
	public function setSaldo($saldo){
		$this->saldo = $saldo;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_i
	 * @param string $cuenta_i
	 */
	public function setCuentaI($cuenta_i){
		$this->cuenta_i = $cuenta_i;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_f
	 * @param string $cuenta_f
	 */
	public function setCuentaF($cuenta_f){
		$this->cuenta_f = $cuenta_f;
	}

	/**
	 * Metodo para establecer el valor del campo operador
	 * @param string $operador
	 */
	public function setOperador($operador){
		$this->operador = $operador;
	}


	/**
	 * Devuelve el valor del campo informe
	 * @return string
	 */
	public function getInforme(){
		return $this->informe;
	}

	/**
	 * Devuelve el valor del campo linea
	 * @return string
	 */
	public function getLinea(){
		return $this->linea;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo salto
	 * @return string
	 */
	public function getSalto(){
		return $this->salto;
	}

	/**
	 * Devuelve el valor del campo pagina
	 * @return string
	 */
	public function getPagina(){
		return $this->pagina;
	}

	/**
	 * Devuelve el valor del campo columna
	 * @return string
	 */
	public function getColumna(){
		return $this->columna;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo saldo_a
	 * @return string
	 */
	public function getSaldoA(){
		return $this->saldo_a;
	}

	/**
	 * Devuelve el valor del campo debe
	 * @return string
	 */
	public function getDebe(){
		return $this->debe;
	}

	/**
	 * Devuelve el valor del campo haber
	 * @return string
	 */
	public function getHaber(){
		return $this->haber;
	}

	/**
	 * Devuelve el valor del campo saldo
	 * @return string
	 */
	public function getSaldo(){
		return $this->saldo;
	}

	/**
	 * Devuelve el valor del campo cuenta_i
	 * @return string
	 */
	public function getCuentaI(){
		return $this->cuenta_i;
	}

	/**
	 * Devuelve el valor del campo cuenta_f
	 * @return string
	 */
	public function getCuentaF(){
		return $this->cuenta_f;
	}

	/**
	 * Devuelve el valor del campo operador
	 * @return string
	 */
	public function getOperador(){
		return $this->operador;
	}

}

