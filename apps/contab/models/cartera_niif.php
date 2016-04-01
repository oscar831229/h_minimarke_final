<?php

class CarteraNiif extends ActiveRecord {

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
	 * @var integer
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
	protected $depre;

	/**
	 * @var integer
	 */
	protected $depre_porc;

	/**
	 * @var integer
	 */
	protected $depre_meses;

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
	 * @param integer $centro_costo
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
	 * Metodo para establecer el valor del campo depre_porc
	 * @param integer $depre_porc
	 */
	public function setDeprePorc($depre_porc){
		$this->depre_porc = $depre_porc;
	}

	/**
	 * Metodo para establecer el valor del campo depre_meses
	 * @param integer $depre_meses
	 */
	public function setDepreMeses($depre_meses){
		$this->depre_meses = $depre_meses;
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
	 * @return integer
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo f_emision
	 * @return Date
	 */
	public function getFEmision(){
		if($this->f_emision){
			return new Date($this->f_emision);
		} else {
			return null;
		}
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
		if($this->f_vence){
			return new Date($this->f_vence);
		} else {
			return null;
		}
	}

	/**
	 * Asigna el valor del campo depre
	 * @param string $depre
	 */
	public function setDepre($depre){
		$this->depre = $depre;
	}

	/**
	 * Devuelve el valor del campo depre
	 * @return string
	 */
	public function getDepre(){
		return $this->depre;
	}

	/**
	 * Devuelve el valor del campo depre_porc
	 * @return integer
	 */
	public function getDeprePorc(){
		return $this->depre_porc;
	}

	/**
	 * Devuelve el valor del campo depre_meses
	 * @return integer
	 */
	public function getDepreMeses(){
		return $this->depre_meses;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}
