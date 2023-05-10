<?php

class Invepos extends ActiveRecord {

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
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var integer
	 */
	protected $almacen;

	/**
	 * @var integer
	 */
	protected $centro_costo;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var integer
	 */
	protected $codigo;

	/**
	 * @var integer
	 */
	protected $menus_items_id;

	/**
	 * @var integer
	 */
	protected $cantidad;

	/**
	 * @var integer
	 */
	protected $cantidadu;

	/**
	 * @var string
	 */
	protected $estado;
	
	/**
	 * @var integer
	 */
	protected $account_id;
	
	/**
	 * @var integer
	 */
	protected $account_modifiers_id;

	/**
	 * @var integer
	 */
	protected $cantidadnc;

	/**
	 * @var integer
	 */
	protected $cantidadunc;

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
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo almacen
	 * @param integer $almacen
	 */
	public function setAlmacen($almacen){
		$this->almacen = $almacen;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param integer $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param integer $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo menus_items_id
	 * @param integer $menus_items_id
	 */
	public function setMenusItemsId($menus_items_id){
		$this->menus_items_id = $menus_items_id;
	}

	/**
	 * Metodo para establecer el valor del campo cantidad
	 * @param integer $cantidad
	 */
	public function setCantidad($cantidad){
		$this->cantidad = $cantidad;
	}

	/**
	 * Metodo para establecer el valor del campo cantidadu
	 * @param integer $cantidadu
	 */
	public function setCantidadu($cantidadu){
		$this->cantidadu = $cantidadu;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}
	
	
	/**
	 * Metodo para establecer el valor del campo account_id
	 * @param integer $account_id
	 */
	public function setAccountId($account_id){
		$this->account_id = $account_id;
	}
	
	/**
	 * Metodo para establecer el valor del campo account_modifiers_id
	 * @param integer $account_id
	 */
	public function setAccountModifiersId($account_modifiers_id){
		$this->account_modifiers_id = $account_modifiers_id;
	}

	public function setCantidadnc($cantidadnc){
		$this->cantidadnc = $cantidadnc;
	}

	public function setCantidadunc($cantidadunc){
		$this->cantidadunc = $cantidadunc;
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
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo almacen
	 * @return integer
	 */
	public function getAlmacen(){
		return $this->almacen;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return integer
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo codigo
	 * @return integer
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo menus_items_id
	 * @return integer
	 */
	public function getMenusItemsId(){
		return $this->menus_items_id;
	}

	/**
	 * Devuelve el valor del campo cantidad
	 * @return integer
	 */
	public function getCantidad(){
		return $this->cantidad;
	}

	/**
	 * Devuelve el valor del campo cantidadu
	 * @return integer
	 */
	public function getCantidadu(){
		return $this->cantidadu;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}
	
	/**
	 * Devuelve valor del campo account_id
	 * @param integer $account_id
	 */
	public function getAccountId($account_id){
		return $this->account_id;
	}
	
	
	/**
	 * Devuelve el valor del campo account_modifiers_id
	 * @param integer $account_id
	 */
	public function getAccountModifiersId($account_modifiers_id){
		return $this->account_modifiers_id;
	}

	public function getCantidadnc(){
		return $this->cantidadnc;
	}

	public function getCantidadunc(){
		return $this->cantidadunc;
	}

	protected function initialize()
	{
		$this->belongsTo('MenusItems');
		$this->hasMany('Inveposnc');
	}

}

