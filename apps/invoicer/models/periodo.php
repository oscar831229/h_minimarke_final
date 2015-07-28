<?php

class Periodo extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $usuario_id;

	/**
	 * @var string
	 */
	protected $periodo;

	/**
	 * @var Date
	 */
	protected $fec_final;

	/**
	 * @var integer
	 */
	protected $ini_fact;

	/**
	 * @var integer
	 */
	protected $fin_fact;

	/**
	 * @var string
	 */
	protected $cierre;

	/**
	 * @var string
	 */
	protected $facturado;

	/**
	 * @var string
	 */
	protected $intereses_mora;

	/**
	 * @var string
	 */
	protected $fecha_factura;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo usuario_id
	 * @param integer $usuario_id
	 */
	public function setUsuarioId($usuario_id){
		$this->usuario_id = $usuario_id;
	}

	/**
	 * Metodo para establecer el valor del campo periodo
	 * @param string $periodo
	 */
	public function setPeriodo($periodo){
		$this->periodo = $periodo;
	}

	/**
	 * Metodo para establecer el valor del campo fec_final
	 * @param Date $fec_final
	 */
	public function setFecFinal($fec_final){
		$this->fec_final = $fec_final;
	}

	/**
	 * Metodo para establecer el valor del campo ini_fact
	 * @param integer $ini_fact
	 */
	public function setIniFact($ini_fact){
		$this->ini_fact = $ini_fact;
	}

	/**
	 * Metodo para establecer el valor del campo fin_fact
	 * @param integer $fin_fact
	 */
	public function setFinFact($fin_fact){
		$this->fin_fact = $fin_fact;
	}

	/**
	 * Metodo para establecer el valor del campo cierre
	 * @param string $cierre
	 */
	public function setCierre($cierre){
		$this->cierre = $cierre;
	}

	/**
	 * Metodo para establecer el valor del campo facturado
	 * @param string $facturado
	 */
	public function setFacturado($facturado){
		$this->facturado = $facturado;
	}

	/**
	 * Metodo para establecer el valor del campo intereses_mora
	 * @param string $intereses_mora
	 */
	public function setInteresesMora($intereses_mora){
		$this->intereses_mora = $intereses_mora;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_factura
	 * @param string $fecha_factura
	 */
	public function setFechaFactura($fecha_factura){
		$this->fecha_factura = $fecha_factura;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo usuario_id
	 * @return integer
	 */
	public function getUsuarioId(){
		return $this->usuario_id;
	}

	/**
	 * Devuelve el valor del campo periodo
	 * @return string
	 */
	public function getPeriodo(){
		return $this->periodo;
	}

	/**
	 * Devuelve el valor del campo fec_final
	 * @return Date
	 */
	public function getFecFinal(){
		if($this->fec_final){
			return new Date($this->fec_final);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo ini_fact
	 * @return integer
	 */
	public function getIniFact(){
		return $this->ini_fact;
	}

	/**
	 * Devuelve el valor del campo fin_fact
	 * @return integer
	 */
	public function getFinFact(){
		return $this->fin_fact;
	}

	/**
	 * Devuelve el valor del campo cierre
	 * @return string
	 */
	public function getCierre(){
		return $this->cierre;
	}

	/**
	 * Devuelve el valor del campo facturado
	 * @return string
	 */
	public function getFacturado(){
		return $this->facturado;
	}

	/**
	 * Devuelve el valor del campo intereses_mora
	 * @return string
	 */
	public function getInteresesMora(){
		return $this->intereses_mora;
	}

	/**
	 * Devuelve el valor del campo fecha_factura
	 * @return string
	 */
	public function getFechaFactura(){
		return $this->fecha_factura;
	}

	public function initialize(){
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->socios)){
			$this->setSchema($config->hfos->socios);
		} else {
			$this->setSchema('hfos_socios');
		}
	}
}

