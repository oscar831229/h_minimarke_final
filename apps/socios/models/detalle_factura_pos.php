<?php

class DetalleFacturaPos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $prefijo_facturacion;

	/**
	 * @var integer
	 */
	protected $consecutivo_facturacion;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var integer
	 */
	protected $account_id;

	/**
	 * @var integer
	 */
	protected $menus_items_id;

	/**
	 * @var string
	 */
	protected $menus_items_nombre;

	/**
	 * @var integer
	 */
	protected $porcentaje_iva;

	/**
	 * @var integer
	 */
	protected $porcentaje_impoconsumo;

	/**
	 * @var integer
	 */
	protected $cantidad;

	/**
	 * @var integer
	 */
	protected $descuento;

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
	protected $total;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo prefijo_facturacion
	 * @param string $prefijo_facturacion
	 */
	public function setPrefijoFacturacion($prefijo_facturacion){
		$this->prefijo_facturacion = $prefijo_facturacion;
	}

	/**
	 * Metodo para establecer el valor del campo consecutivo_facturacion
	 * @param integer $consecutivo_facturacion
	 */
	public function setConsecutivoFacturacion($consecutivo_facturacion){
		$this->consecutivo_facturacion = $consecutivo_facturacion;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo account_id
	 * @param integer $account_id
	 */
	public function setAccountId($account_id){
		$this->account_id = $account_id;
	}

	/**
	 * Metodo para establecer el valor del campo menus_items_id
	 * @param integer $menus_items_id
	 */
	public function setMenusItemsId($menus_items_id){
		$this->menus_items_id = $menus_items_id;
	}

	/**
	 * Metodo para establecer el valor del campo menus_items_nombre
	 * @param string $menus_items_nombre
	 */
	public function setMenusItemsNombre($menus_items_nombre){
		$this->menus_items_nombre = $menus_items_nombre;
	}

	/**
	 * Metodo para establecer el valor del campo porcentaje_iva
	 * @param integer $porcentaje_iva
	 */
	public function setPorcentajeIva($porcentaje_iva){
		$this->porcentaje_iva = $porcentaje_iva;
	}

	/**
	 * Metodo para establecer el valor del campo porcentaje_impoconsumo
	 * @param integer $porcentaje_impoconsumo
	 */
	public function setPorcentajeImpoconsumo($porcentaje_impoconsumo){
		$this->porcentaje_impoconsumo = $porcentaje_impoconsumo;
	}

	/**
	 * Metodo para establecer el valor del campo cantidad
	 * @param integer $cantidad
	 */
	public function setCantidad($cantidad){
		$this->cantidad = $cantidad;
	}

	/**
	 * Metodo para establecer el valor del campo descuento
	 * @param integer $descuento
	 */
	public function setDescuento($descuento){
		$this->descuento = $descuento;
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
	 * Metodo para establecer el valor del campo total
	 * @param string $total
	 */
	public function setTotal($total){
		$this->total = $total;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo prefijo_facturacion
	 * @return string
	 */
	public function getPrefijoFacturacion(){
		return $this->prefijo_facturacion;
	}

	/**
	 * Devuelve el valor del campo consecutivo_facturacion
	 * @return integer
	 */
	public function getConsecutivoFacturacion(){
		return $this->consecutivo_facturacion;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo account_id
	 * @return integer
	 */
	public function getAccountId(){
		return $this->account_id;
	}

	/**
	 * Devuelve el valor del campo menus_items_id
	 * @return integer
	 */
	public function getMenusItemsId(){
		return $this->menus_items_id;
	}

	/**
	 * Devuelve el valor del campo menus_items_nombre
	 * @return string
	 */
	public function getMenusItemsNombre(){
		return $this->menus_items_nombre;
	}

	/**
	 * Devuelve el valor del campo porcentaje_iva
	 * @return integer
	 */
	public function getPorcentajeIva(){
		return $this->porcentaje_iva;
	}

	/**
	 * Devuelve el valor del campo porcentaje_impoconsumo
	 * @return integer
	 */
	public function getPorcentajeImpoconsumo(){
		return $this->porcentaje_impoconsumo;
	}

	/**
	 * Devuelve el valor del campo cantidad
	 * @return integer
	 */
	public function getCantidad(){
		return $this->cantidad;
	}

	/**
	 * Devuelve el valor del campo descuento
	 * @return integer
	 */
	public function getDescuento(){
		return $this->descuento;
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
	 * Devuelve el valor del campo total
	 * @return string
	 */
	public function getTotal(){
		return $this->total;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	public function initialize()
	{
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->pos_db)){
			$this->setSchema($config->hfos->pos_db);
		} else {
			$this->setSchema('pos');
		}
		$this->setSource('detalle_factura');
	}

}

