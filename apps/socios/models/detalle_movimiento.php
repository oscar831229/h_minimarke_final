<?php

class DetalleMovimiento extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $movimiento_id;

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
	protected $fecha_venc;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var integer
	 */
	protected $cargos_socios_id;

	/**
	 * @var integer
	 */
	protected $recibos_caja_id;

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
	protected $total;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $tipo_documento;

	/**
	 * @var string
	 */
	protected $tipo_movi;

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
	 * Metodo para establecer el valor del campo movimiento_id
	 * @param integer $movimiento_id
	 */
	public function setMovimientoId($movimiento_id){
		$this->movimiento_id = $movimiento_id;
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
	 * Metodo para establecer el valor del campo fecha_venc
	 * @param Date $fecha_venc
	 */
	public function setFechaVenc($fecha_venc){
		$this->fecha_venc = $fecha_venc;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo cargos_socios_id
	 * @param integer $cargos_socios_id
	 */
	public function setCargosSociosId($cargos_socios_id){
		$this->cargos_socios_id = $cargos_socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo recibos_caja_id
	 * @param integer $recibos_caja_id
	 */
	public function setRecibosCajaId($recibos_caja_id){
		$this->recibos_caja_id = $recibos_caja_id;
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
	 * Metodo para establecer el valor del campo total
	 * @param string $total
	 */
	public function setTotal($total){
		$this->total = $total;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_documento
	 * @param string $tipo_documento
	 */
	public function setTipoDocumento($tipo_documento){
		$this->tipo_documento = $tipo_documento;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_movi
	 * @param string $tipo_movi
	 */
	public function setTipoMovi($tipo_movi){
		$this->tipo_movi = $tipo_movi;
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
	 * Devuelve el valor del campo movimiento_id
	 * @return integer
	 */
	public function getMovimientoId(){
		return $this->movimiento_id;
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
	 * Devuelve el valor del campo fecha_venc
	 * @return Date
	 */
	public function getFechaVenc(){
		if($this->fecha_venc){
			return new Date($this->fecha_venc);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo cargos_socios_id
	 * @return integer
	 */
	public function getCargosSociosId(){
		return $this->cargos_socios_id;
	}

	/**
	 * Devuelve el valor del campo recibos_caja_id
	 * @return integer
	 */
	public function getRecibosCajaId(){
		return $this->recibos_caja_id;
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
	 * Devuelve el valor del campo total
	 * @return string
	 */
	public function getTotal(){
		return $this->total;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo tipo_documento
	 * @return string
	 */
	public function getTipoDocumento(){
		return $this->tipo_documento;
	}

	/**
	 * Devuelve el valor del campo tipo_movi
	 * @return string
	 */
	public function getTipoMovi(){
		return $this->tipo_movi;
	}

	/**
	 * Devuelve el valor del campo ico
	 * @return string
	 */
	public function getIco(){
		return $this->ico;
	}

	public function initialize()
    {
		$this->addForeignKey('socios_id', 'Socios', 'socios_id', array(
			'message' => 'El socio no es valido'
		));
		$this->addForeignKey('movimiento_id', 'Movimiento', 'id', array(
			'message' => 'El id de movimiento no es valido'
		));
		
		$this->belongsTo('socios_id', 'Socios', 'socios_id');
		$this->belongsTo('cargos_socios_id', 'CargosSocios', 'id');
	}

}

