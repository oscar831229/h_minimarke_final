<?php

class EstadoCuenta extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var Date
	 */
	protected $fecha_saldo;

	/**
	 * @var string
	 */
	protected $saldo_ant;

	/**
	 * @var string
	 */
	protected $cargos;

	/**
	 * @var string
	 */
	protected $interes;

	/**
	 * @var string
	 */
	protected $pagos;

	/**
	 * @var string
	 */
	protected $d30;

	/**
	 * @var string
	 */
	protected $d60;

	/**
	 * @var string
	 */
	protected $d90;

	/**
	 * @var string
	 */
	protected $d120;

	/**
	 * @var string
	 */
	protected $d120m;

	/**
	 * @var string
	 */
	protected $saldo_nuevo;

	/**
	 * @var string
	 */
	protected $saldo_nuevo_mora;

	/**
	 * @var string
	 */
	protected $mora;


	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_saldo
	 * @param Date $fecha_saldo
	 */
	public function setFechaSaldo($fecha_saldo){
		$this->fecha_saldo = $fecha_saldo;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_ant
	 * @param string $saldo_ant
	 */
	public function setSaldoAnt($saldo_ant){
		$this->saldo_ant = $saldo_ant;
	}

	/**
	 * Metodo para establecer el valor del campo cargos
	 * @param string $cargos
	 */
	public function setCargos($cargos){
		$this->cargos = $cargos;
	}

	/**
	 * Metodo para establecer el valor del campo interes
	 * @param string $interes
	 */
	public function setInteres($interes){
		$this->interes = $interes;
	}

	/**
	 * Metodo para establecer el valor del campo pagos
	 * @param string $pagos
	 */
	public function setPagos($pagos){
		$this->pagos = $pagos;
	}

	/**
	 * Metodo para establecer el valor del campo d30
	 * @param string $d30
	 */
	public function setD30($d30){
		$this->d30 = $d30;
	}

	/**
	 * Metodo para establecer el valor del campo d60
	 * @param string $d60
	 */
	public function setD60($d60){
		$this->d60 = $d60;
	}

	/**
	 * Metodo para establecer el valor del campo d90
	 * @param string $d90
	 */
	public function setD90($d90){
		$this->d90 = $d90;
	}

	/**
	 * Metodo para establecer el valor del campo d120
	 * @param string $d120
	 */
	public function setD120($d120){
		$this->d120 = $d120;
	}

	/**
	 * Metodo para establecer el valor del campo d120m
	 * @param string $d120m
	 */
	public function setD120m($d120m){
		$this->d120m = $d120m;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_nuevo
	 * @param string $saldo_nuevo
	 */
	public function setSaldoNuevo($saldo_nuevo){
		$this->saldo_nuevo = $saldo_nuevo;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_nuevo_mora
	 * @param string $saldo_nuevo_mora
	 */
	public function setSaldoNuevoMora($saldo_nuevo_mora){
		$this->saldo_nuevo_mora = $saldo_nuevo_mora;
	}

	/**
	 * Metodo para establecer el valor del campo mora
	 * @param string $mora
	 */
	public function setMora($mora){
		$this->mora = $mora;
	}


	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		if($this->fecha){
			return new Date($this->fecha);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecha_saldo
	 * @return Date
	 */
	public function getFechaSaldo(){
		if($this->fecha_saldo){
			return new Date($this->fecha_saldo);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo saldo_ant
	 * @return string
	 */
	public function getSaldoAnt(){
		return $this->saldo_ant;
	}

	/**
	 * Devuelve el valor del campo cargos
	 * @return string
	 */
	public function getCargos(){
		return $this->cargos;
	}

	/**
	 * Devuelve el valor del campo interes
	 * @return string
	 */
	public function getInteres(){
		return $this->interes;
	}

	/**
	 * Devuelve el valor del campo pagos
	 * @return string
	 */
	public function getPagos(){
		return $this->pagos;
	}

	/**
	 * Devuelve el valor del campo d30
	 * @return string
	 */
	public function getD30(){
		return $this->d30;
	}

	/**
	 * Devuelve el valor del campo d60
	 * @return string
	 */
	public function getD60(){
		return $this->d60;
	}

	/**
	 * Devuelve el valor del campo d90
	 * @return string
	 */
	public function getD90(){
		return $this->d90;
	}

	/**
	 * Devuelve el valor del campo d120
	 * @return string
	 */
	public function getD120(){
		return $this->d120;
	}

	/**
	 * Devuelve el valor del campo d120m
	 * @return string
	 */
	public function getD120m(){
		return $this->d120m;
	}

	/**
	 * Devuelve el valor del campo saldo_nuevo
	 * @return string
	 */
	public function getSaldoNuevo(){
		return $this->saldo_nuevo;
	}

	/**
	 * Devuelve el valor del campo saldo_nuevo_mora
	 * @return string
	 */
	public function getSaldoNuevoMora(){
		return $this->saldo_nuevo_mora;
	}

	/**
	 * Devuelve el valor del campo mora
	 * @return string
	 */
	public function getMora(){
		return $this->mora;
	}

}

