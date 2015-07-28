<?php

class PagoSaldoTpc extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_tpc_id;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $porcentaje;

	/**
	 * @var integer
	 */
	protected $numero_cuotas;

	/**
	 * @var string
	 */
	protected $interes;

	/**
	 * @var Date
	 */
	protected $fecha_primera_cuota;

	/**
	 * @var string
	 */
	protected $valor_cuota_aproximada;

	/**
	 * @var string
	 */
	protected $solicitud_inicial;

	/**
	 * @var integer
	 */
	protected $premios_id;

	/**
	 * @var string
	 */
	protected $observaciones;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo socios_tpc_id
	 * @param integer $socios_tpc_id
	 */
	public function setSociosTpcId($socios_tpc_id){
		$this->socios_tpc_id = $socios_tpc_id;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo porcentaje
	 * @param string $porcentaje
	 */
	public function setPorcentaje($porcentaje){
		$this->porcentaje = $porcentaje;
	}

	/**
	 * Metodo para establecer el valor del campo numero_cuotas
	 * @param integer $numero_cuotas
	 */
	public function setNumeroCuotas($numero_cuotas){
		$this->numero_cuotas = $numero_cuotas;
	}

	/**
	 * Metodo para establecer el valor del campo interes
	 * @param string $interes
	 */
	public function setInteres($interes){
		$this->interes = $interes;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_primera_cuota
	 * @param Date $fecha_primera_cuota
	 */
	public function setFechaPrimeraCuota($fecha_primera_cuota){
		$this->fecha_primera_cuota = $fecha_primera_cuota;
	}

	/**
	 * Metodo para establecer el valor del campo valor_cuota_aproximada
	 * @param string $valor_cuota_aproximada
	 */
	public function setValorCuotaAproximada($valor_cuota_aproximada){
		$this->valor_cuota_aproximada = $valor_cuota_aproximada;
	}

	/**
	 * Metodo para establecer el valor del campo solicitud_inicial
	 * @param string $solicitud_inicial
	 */
	public function setSolicitudInicial($solicitud_inicial){
		$this->solicitud_inicial = $solicitud_inicial;
	}

	/**
	 * Metodo para establecer el valor del campo premios_id
	 * @param integer $premios_id
	 */
	public function setPremiosId($premios_id){
		$this->premios_id = $premios_id;
	}

	/**
	 * Metodo para establecer el valor del campo observaciones
	 * @param string $observaciones
	 */
	public function setObservaciones($observaciones){
		$this->observaciones = $observaciones;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo socios_tpc_id
	 * @return integer
	 */
	public function getSociosTpcId(){
		return $this->socios_tpc_id;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo porcentaje
	 * @return string
	 */
	public function getPorcentaje(){
		return $this->porcentaje;
	}

	/**
	 * Devuelve el valor del campo numero_cuotas
	 * @return integer
	 */
	public function getNumeroCuotas(){
		return $this->numero_cuotas;
	}

	/**
	 * Devuelve el valor del campo interes
	 * @return string
	 */
	public function getInteres(){
		return $this->interes;
	}

	/**
	 * Devuelve el valor del campo fecha_primera_cuota
	 * @return Date
	 */
	public function getFechaPrimeraCuota(){
		return new Date($this->fecha_primera_cuota);
	}

	/**
	 * Devuelve el valor del campo valor_cuota_aproximada
	 * @return string
	 */
	public function getValorCuotaAproximada(){
		return $this->valor_cuota_aproximada;
	}

	/**
	 * Devuelve el valor del campo solicitud_inicial
	 * @return string
	 */
	public function getSolicitudInicial(){
		return $this->solicitud_inicial;
	}

	/**
	 * Devuelve el valor del campo premios_id
	 * @return integer
	 */
	public function getPremiosId(){
		return $this->premios_id;
	}

	/**
	 * Devuelve el valor del campo observaciones
	 * @return string
	 */
	public function getObservaciones(){
		return $this->observaciones;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSchema('sociostpc');
		$this->setSource('pago_saldo');

		$this->belongsTo('socios_tpc');
		$this->belongsTo('premios');
	}

}

