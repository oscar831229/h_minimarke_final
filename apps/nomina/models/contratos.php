<?php

class Contratos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $empleados_id;

	/**
	 * @var integer
	 */
	protected $cargo;

	/**
	 * @var integer
	 */
	protected $centro_costo;

	/**
	 * @var string
	 */
	protected $tipo_contrato;

	/**
	 * @var integer
	 */
	protected $forma_pago;

	/**
	 * @var integer
	 */
	protected $fondo_ces;

	/**
	 * @var integer
	 */
	protected $ubica;

	/**
	 * @var integer
	 */
	protected $eps;

	/**
	 * @var integer
	 */
	protected $fondo_pension;

	/**
	 * @var string
	 */
	protected $sueldo;

	/**
	 * @var string
	 */
	protected $transporte;

	/**
	 * @var Date
	 */
	protected $fecha_ingreso;

	/**
	 * @var Date
	 */
	protected $fecha_retiro;

	/**
	 * @var Date
	 */
	protected $ultimo_pago;

	/**
	 * @var Date
	 */
	protected $ultimo_aumento;

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
	 * Metodo para establecer el valor del campo empleados_id
	 * @param integer $empleados_id
	 */
	public function setEmpleadosId($empleados_id){
		$this->empleados_id = $empleados_id;
	}

	/**
	 * Metodo para establecer el valor del campo cargo
	 * @param integer $cargo
	 */
	public function setCargo($cargo){
		$this->cargo = $cargo;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param integer $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_contrato
	 * @param string $tipo_contrato
	 */
	public function setTipoContrato($tipo_contrato){
		$this->tipo_contrato = $tipo_contrato;
	}

	/**
	 * Metodo para establecer el valor del campo forma_pago
	 * @param integer $forma_pago
	 */
	public function setFormaPago($forma_pago){
		$this->forma_pago = $forma_pago;
	}

	/**
	 * Metodo para establecer el valor del campo fondo_ces
	 * @param integer $fondo_ces
	 */
	public function setFondoCes($fondo_ces){
		$this->fondo_ces = $fondo_ces;
	}

	/**
	 * Metodo para establecer el valor del campo ubica
	 * @param integer $ubica
	 */
	public function setUbica($ubica){
		$this->ubica = $ubica;
	}

	/**
	 * Metodo para establecer el valor del campo eps
	 * @param integer $eps
	 */
	public function setEps($eps){
		$this->eps = $eps;
	}

	/**
	 * Metodo para establecer el valor del campo fondo_pension
	 * @param integer $fondo_pension
	 */
	public function setFondoPension($fondo_pension){
		$this->fondo_pension = $fondo_pension;
	}

	/**
	 * Metodo para establecer el valor del campo sueldo
	 * @param string $sueldo
	 */
	public function setSueldo($sueldo){
		$this->sueldo = $sueldo;
	}

	/**
	 * Metodo para establecer el valor del campo transporte
	 * @param string $transporte
	 */
	public function setTransporte($transporte){
		$this->transporte = $transporte;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_ingreso
	 * @param Date $fecha_ingreso
	 */
	public function setFechaIngreso($fecha_ingreso){
		$this->fecha_ingreso = $fecha_ingreso;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_retiro
	 * @param Date $fecha_retiro
	 */
	public function setFechaRetiro($fecha_retiro){
		$this->fecha_retiro = $fecha_retiro;
	}

	/**
	 * Metodo para establecer el valor del campo ultimo_pago
	 * @param Date $ultimo_pago
	 */
	public function setUltimoPago($ultimo_pago){
		$this->ultimo_pago = $ultimo_pago;
	}

	/**
	 * Metodo para establecer el valor del campo ultimo_aumento
	 * @param Date $ultimo_aumento
	 */
	public function setUltimoAumento($ultimo_aumento){
		$this->ultimo_aumento = $ultimo_aumento;
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
	 * Devuelve el valor del campo empleados_id
	 * @return integer
	 */
	public function getEmpleadosId(){
		return $this->empleados_id;
	}

	/**
	 * Devuelve el valor del campo cargo
	 * @return integer
	 */
	public function getCargo(){
		return $this->cargo;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return integer
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo tipo_contrato
	 * @return string
	 */
	public function getTipoContrato(){
		return $this->tipo_contrato;
	}

	/**
	 * Devuelve el valor del campo forma_pago
	 * @return integer
	 */
	public function getFormaPago(){
		return $this->forma_pago;
	}

	/**
	 * Devuelve el valor del campo fondo_ces
	 * @return integer
	 */
	public function getFondoCes(){
		return $this->fondo_ces;
	}

	/**
	 * Devuelve el valor del campo ubica
	 * @return integer
	 */
	public function getUbica(){
		return $this->ubica;
	}

	/**
	 * Devuelve el valor del campo eps
	 * @return integer
	 */
	public function getEps(){
		return $this->eps;
	}

	/**
	 * Devuelve el valor del campo fondo_pension
	 * @return integer
	 */
	public function getFondoPension(){
		return $this->fondo_pension;
	}

	/**
	 * Devuelve el valor del campo sueldo
	 * @return string
	 */
	public function getSueldo(){
		return $this->sueldo;
	}

	/**
	 * Devuelve el valor del campo transporte
	 * @return string
	 */
	public function getTransporte(){
		return $this->transporte;
	}

	/**
	 * Devuelve el valor del campo fecha_ingreso
	 * @return Date
	 */
	public function getFechaIngreso(){
		if($this->fecha_ingreso){
			return new Date($this->fecha_ingreso);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecha_retiro
	 * @return Date
	 */
	public function getFechaRetiro(){
		if($this->fecha_retiro){
			return new Date($this->fecha_retiro);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo ultimo_pago
	 * @return Date
	 */
	public function getUltimoPago(){
		if($this->ultimo_pago){
			return new Date($this->ultimo_pago);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo ultimo_aumento
	 * @return Date
	 */
	public function getUltimoAumento(){
		if($this->ultimo_aumento){
			return new Date($this->ultimo_aumento);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

