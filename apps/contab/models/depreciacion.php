<?php

class Depreciacion extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $activos_id;

	/**
	 * @var string
	 */
	protected $ano_mes;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $centro_costo;

	/**
	 * @var string
	 */
	protected $cta_dev_compras;

	/**
	 * @var string
	 */
	protected $cta_dev_ventas;

	/**
	 * @var string
	 */
	protected $valor;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo activos_id
	 * @param integer $activos_id
	 */
	public function setActivosId($activos_id){
		$this->activos_id = $activos_id;
	}

	/**
	 * Metodo para establecer el valor del campo ano_mes
	 * @param string $ano_mes
	 */
	public function setAnoMes($ano_mes){
		$this->ano_mes = $ano_mes;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo comprob
	 * @param string $comprob
	 */
	public function setComprob($comprob){
		$this->comprob = $comprob;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param integer $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo cta_dev_compras
	 * @param string $cta_dev_compras
	 */
	public function setCtaDevCompras($cta_dev_compras){
		$this->cta_dev_compras = $cta_dev_compras;
	}

	/**
	 * Metodo para establecer el valor del campo cta_dev_ventas
	 * @param string $cta_dev_ventas
	 */
	public function setCtaDevVentas($cta_dev_ventas){
		$this->cta_dev_ventas = $cta_dev_ventas;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo activos_id
	 * @return integer
	 */
	public function getActivosId(){
		return $this->activos_id;
	}

	/**
	 * Devuelve el valor del campo ano_mes
	 * @return string
	 */
	public function getAnoMes(){
		return $this->ano_mes;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo comprob
	 * @return string
	 */
	public function getComprob(){
		return $this->comprob;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return integer
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo cta_dev_compras
	 * @return string
	 */
	public function getCtaDevCompras(){
		return $this->cta_dev_compras;
	}

	/**
	 * Devuelve el valor del campo cta_dev_ventas
	 * @return string
	 */
	public function getCtaDevVentas(){
		return $this->cta_dev_ventas;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

}

