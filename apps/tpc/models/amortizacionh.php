<?php

class Amortizacionh extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $numero_cuota;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $capital;

	/**
	 * @var string
	 */
	protected $interes;

	/**
	 * @var string
	 */
	protected $saldo;

	/**
	 * @var string
	 */
	protected $fecha_cuota;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $pagado;

	/**
	 * @var integer
	 */
	protected $nota_historia_id;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo numero_cuota
	 * @param integer $numero_cuota
	 */
	public function setNumeroCuota($numero_cuota){
		$this->numero_cuota = $numero_cuota;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo capital
	 * @param string $capital
	 */
	public function setCapital($capital){
		$this->capital = $capital;
	}

	/**
	 * Metodo para establecer el valor del campo interes
	 * @param string $interes
	 */
	public function setInteres($interes){
		$this->interes = $interes;
	}

	/**
	 * Metodo para establecer el valor del campo saldo
	 * @param string $saldo
	 */
	public function setSaldo($saldo){
		$this->saldo = $saldo;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_cuota
	 * @param string $fecha_cuota
	 */
	public function setFechaCuota($fecha_cuota){
		$this->fecha_cuota = $fecha_cuota;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo pagado
	 * @param string $pagado
	 */
	public function setPagado($pagado){
		$this->pagado = $pagado;
	}

	/**
	 * Metodo para establecer el valor del campo nota_historia_id
	 * @param integer $nota_historia_id
	 */
	public function setNotaHistoriaId($nota_historia_id){
		$this->nota_historia_id = $nota_historia_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo numero_cuota
	 * @return integer
	 */
	public function getNumeroCuota(){
		return $this->numero_cuota;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo capital
	 * @return string
	 */
	public function getCapital(){
		return $this->capital;
	}

	/**
	 * Devuelve el valor del campo interes
	 * @return string
	 */
	public function getInteres(){
		return $this->interes;
	}

	/**
	 * Devuelve el valor del campo saldo
	 * @return string
	 */
	public function getSaldo(){
		return $this->saldo;
	}

	/**
	 * Devuelve el valor del campo fecha_cuota
	 * @return string
	 */
	public function getFechaCuota(){
		return $this->fecha_cuota;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo pagado
	 * @return string
	 */
	public function getPagado(){
		return $this->pagado;
	}

	/**
	 * Devuelve el valor del campo nota_historia_id
	 * @return integer
	 */
	public function getNotaHistoriaId(){
		return $this->nota_historia_id;
	}

}

