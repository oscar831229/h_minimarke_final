<?php

class CargosSocios extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $periodo;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $cargos_fijos_id;

	/**
	 * @var string
	 */
	protected $descripcion;

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
	protected $cuota_aplicar;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $ico;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo periodo
	 * @param string $periodo
	 */
	public function setPeriodo($periodo){
		$this->periodo = $periodo;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo cargos_fijos_id
	 * @param integer $cargos_fijos_id
	 */
	public function setCargosFijosId($cargos_fijos_id){
		$this->cargos_fijos_id = $cargos_fijos_id;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
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
	 * Metodo para establecer el valor del campo cuota_aplicar
	 * @param string $cuota_aplicar
	 */
	public function setCuotaAplicar($cuota_aplicar){
		$this->cuota_aplicar = $cuota_aplicar;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo ico
	 * @param string $ico
	 */
	public function setIco($ico){
		$this->ico = $ico;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
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
	 * Devuelve el valor del campo periodo
	 * @return string
	 */
	public function getPeriodo(){
		return $this->periodo;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo cargos_fijos_id
	 * @return integer
	 */
	public function getCargosFijosId(){
		return $this->cargos_fijos_id;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
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
	 * Devuelve el valor del campo cuota_aplicar
	 * @return string
	 */
	public function getCuotaAplicar(){
		return $this->cuota_aplicar;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo ico
	 * @return string
	 */
	public function getIco(){
		return $this->ico;
	}

	public function initialize(){
		$this->hasMany('cargos_fijos_id','CargosFijos','id');
		$this->hasOne('socios_id','Socios','socios_id');
		$this->belongsTo('id','DetalleMovimiento','cargos_socios_id');
	}

}

