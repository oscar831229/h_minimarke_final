<?php

class PrestamosSocios extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var string
	 */
	protected $valor_financiacion;

	/**
	 * @var Date
	 */
	protected $fecha_prestamo;

	/**
	 * @var Date
	 */
	protected $fecha_inicio;

	/**
	 * @var integer
	 */
	protected $numero_cuotas;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $interes_corriente;

	/**
	 * @var integer
	 */
	protected $cuenta;

	/**
	 * @var integer
	 */
	protected $cuenta_cruce;

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;


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
	 * Metodo para establecer el valor del campo valor_financiacion
	 * @param string $valor_financiacion
	 */
	public function setValorFinanciacion($valor_financiacion){
		$this->valor_financiacion = $valor_financiacion;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_prestamo
	 * @param Date $fecha_prestamo
	 */
	public function setFechaPrestamo($fecha_prestamo){
		$this->fecha_prestamo = $fecha_prestamo;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_inicio
	 * @param Date $fecha_inicio
	 */
	public function setFechaInicio($fecha_inicio){
		$this->fecha_inicio = $fecha_inicio;
	}

	/**
	 * Metodo para establecer el valor del campo numero_cuotas
	 * @param integer $numero_cuotas
	 */
	public function setNumeroCuotas($numero_cuotas){
		$this->numero_cuotas = $numero_cuotas;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo interes_corriente
	 * @param string $interes_corriente
	 */
	public function setInteresCorriente($interes_corriente){
		$this->interes_corriente = $interes_corriente;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param integer $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_cruce
	 * @param integer $cuenta_cruce
	 */
	public function setCuentaCruce($cuenta_cruce){
		$this->cuenta_cruce = $cuenta_cruce;
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
	 * Devuelve el valor del campo valor_financiacion
	 * @return string
	 */
	public function getValorFinanciacion(){
		return $this->valor_financiacion;
	}

	/**
	 * Devuelve el valor del campo fecha_prestamo
	 * @return Date
	 */
	public function getFechaPrestamo(){
		if($this->fecha_prestamo){
			return new Date($this->fecha_prestamo);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecha_inicio
	 * @return Date
	 */
	public function getFechaInicio(){
		if($this->fecha_inicio){
			return new Date($this->fecha_inicio);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo numero_cuotas
	 * @return integer
	 */
	public function getNumeroCuotas(){
		return $this->numero_cuotas;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo interes_corriente
	 * @return string
	 */
	public function getInteresCorriente(){
		return $this->interes_corriente;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return integer
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo cuenta_cruce
	 * @return integer
	 */
	public function getCuentaCruce(){
		return $this->cuenta_cruce;
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

	public function initialize(){
		$this->belongsTo('socios_id', 'Socios', 'socios_id');
		$this->addForeignKey('socios_id', 'Socios', 'socios_id', array(
			'message' => 'El socio no es valido'
		));
	}

}

