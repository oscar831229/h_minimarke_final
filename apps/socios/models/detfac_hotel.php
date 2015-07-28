<?php

class DetfacHotel extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $prefac;

	/**
	 * @var integer
	 */
	protected $numfac;

	/**
	 * @var integer
	 */
	protected $item;

	/**
	 * @var integer
	 */
	protected $codcar;

	/**
	 * @var string
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $concepto;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $iva;

	/**
	 * @var string
	 */
	protected $impo;

	/**
	 * @var string
	 */
	protected $servicio;

	/**
	 * @var string
	 */
	protected $terceros;

	/**
	 * @var string
	 */
	protected $total;

	/**
	 * @var string
	 */
	protected $abonos;

	/**
	 * @var string
	 */
	protected $saldo;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo prefac
	 * @param string $prefac
	 */
	public function setPrefac($prefac){
		$this->prefac = $prefac;
	}

	/**
	 * Metodo para establecer el valor del campo numfac
	 * @param integer $numfac
	 */
	public function setNumfac($numfac){
		$this->numfac = $numfac;
	}

	/**
	 * Metodo para establecer el valor del campo item
	 * @param integer $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo codcar
	 * @param integer $codcar
	 */
	public function setCodcar($codcar){
		$this->codcar = $codcar;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param string $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo concepto
	 * @param string $concepto
	 */
	public function setConcepto($concepto){
		$this->concepto = $concepto;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param string $iva
	 */
	public function setIva($iva){
		$this->iva = $iva;
	}

	/**
	 * Metodo para establecer el valor del campo impo
	 * @param string $impo
	 */
	public function setImpo($impo){
		$this->impo = $impo;
	}

	/**
	 * Metodo para establecer el valor del campo servicio
	 * @param string $servicio
	 */
	public function setServicio($servicio){
		$this->servicio = $servicio;
	}

	/**
	 * Metodo para establecer el valor del campo terceros
	 * @param string $terceros
	 */
	public function setTerceros($terceros){
		$this->terceros = $terceros;
	}

	/**
	 * Metodo para establecer el valor del campo total
	 * @param string $total
	 */
	public function setTotal($total){
		$this->total = $total;
	}

	/**
	 * Metodo para establecer el valor del campo abonos
	 * @param string $abonos
	 */
	public function setAbonos($abonos){
		$this->abonos = $abonos;
	}

	/**
	 * Metodo para establecer el valor del campo saldo
	 * @param string $saldo
	 */
	public function setSaldo($saldo){
		$this->saldo = $saldo;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo prefac
	 * @return string
	 */
	public function getPrefac(){
		return $this->prefac;
	}

	/**
	 * Devuelve el valor del campo numfac
	 * @return integer
	 */
	public function getNumfac(){
		return $this->numfac;
	}

	/**
	 * Devuelve el valor del campo item
	 * @return integer
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo codcar
	 * @return integer
	 */
	public function getCodcar(){
		return $this->codcar;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return string
	 */
	public function getFecha(){
		return $this->fecha;
	}

	/**
	 * Devuelve el valor del campo concepto
	 * @return string
	 */
	public function getConcepto(){
		return $this->concepto;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return string
	 */
	public function getIva(){
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo impo
	 * @return string
	 */
	public function getImpo(){
		return $this->impo;
	}

	/**
	 * Devuelve el valor del campo servicio
	 * @return string
	 */
	public function getServicio(){
		return $this->servicio;
	}

	/**
	 * Devuelve el valor del campo terceros
	 * @return string
	 */
	public function getTerceros(){
		return $this->terceros;
	}

	/**
	 * Devuelve el valor del campo total
	 * @return string
	 */
	public function getTotal(){
		return $this->total;
	}

	/**
	 * Devuelve el valor del campo abonos
	 * @return string
	 */
	public function getAbonos(){
		return $this->abonos;
	}

	/**
	 * Devuelve el valor del campo saldo
	 * @return string
	 */
	public function getSaldo(){
		return $this->saldo;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){		
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->front_db)){
			$this->setSchema($config->hfos->front_db);
		} else {
			$this->setSchema('hotel2');
		}
		$this->setSource('detfac');	
	}

}

