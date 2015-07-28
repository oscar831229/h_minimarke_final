<?php

class Lineas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $almacen;

	/**
	 * @var string
	 */
	protected $linea;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $es_auxiliar;

	/**
	 * @var string
	 */
	protected $cta_compra;

	/**
	 * @var string
	 */
	protected $cta_venta;

	/**
	 * @var string
	 */
	protected $cta_consumo;

	/**
	 * @var string
	 */
	protected $cta_descuento;

	/**
	 * @var string
	 */
	protected $cta_inve;

	/**
	 * @var string
	 */
	protected $cta_costo_venta;

	/**
	 * @var string
	 */
	protected $cta_ret_compra;

	/**
	 * @var string
	 */
	protected $porc_compra;

	/**
	 * @var string
	 */
	protected $minimo_ret;

	/**
	 * @var string
	 */
	protected $cta_dev_ventas;

	/**
	 * @var string
	 */
	protected $cta_dev_compras;

	/**
	 * @var string
	 */
	protected $cta_hortic;

	/**
	 * @var string
	 */
	protected $porc_hortic;

	/**
	 * @var string
	 */
	protected $minimo_v;

	/**
	 * @var string
	 */
	protected $minimo_c;

	/**
	 * @var string
	 */
	protected $impo_gasto;

	/**
	 * @var string
	 */
	protected $impo_costo;


	/**
	 * Metodo para establecer el valor del campo almacen
	 * @param integer $almacen
	 */
	public function setAlmacen($almacen){
		$this->almacen = $almacen;
	}

	/**
	 * Metodo para establecer el valor del campo linea
	 * @param string $linea
	 */
	public function setLinea($linea){
		$this->linea = $linea;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo es_auxiliar
	 * @param string $es_auxiliar
	 */
	public function setEsAuxiliar($es_auxiliar){
		$this->es_auxiliar = $es_auxiliar;
	}

	/**
	 * Metodo para establecer el valor del campo cta_compra
	 * @param string $cta_compra
	 */
	public function setCtaCompra($cta_compra){
		$this->cta_compra = $cta_compra;
	}

	/**
	 * Metodo para establecer el valor del campo cta_venta
	 * @param string $cta_venta
	 */
	public function setCtaVenta($cta_venta){
		$this->cta_venta = $cta_venta;
	}

	/**
	 * Metodo para establecer el valor del campo cta_consumo
	 * @param string $cta_consumo
	 */
	public function setCtaConsumo($cta_consumo){
		$this->cta_consumo = $cta_consumo;
	}

	/**
	 * Metodo para establecer el valor del campo cta_descuento
	 * @param string $cta_descuento
	 */
	public function setCtaDescuento($cta_descuento){
		$this->cta_descuento = $cta_descuento;
	}

	/**
	 * Metodo para establecer el valor del campo cta_inve
	 * @param string $cta_inve
	 */
	public function setCtaInve($cta_inve){
		$this->cta_inve = $cta_inve;
	}

	/**
	 * Metodo para establecer el valor del campo cta_costo_venta
	 * @param string $cta_costo_venta
	 */
	public function setCtaCostoVenta($cta_costo_venta){
		$this->cta_costo_venta = $cta_costo_venta;
	}

	/**
	 * Metodo para establecer el valor del campo cta_ret_compra
	 * @param string $cta_ret_compra
	 */
	public function setCtaRetCompra($cta_ret_compra){
		$this->cta_ret_compra = $cta_ret_compra;
	}

	/**
	 * Metodo para establecer el valor del campo porc_compra
	 * @param string $porc_compra
	 */
	public function setPorcCompra($porc_compra){
		$this->porc_compra = $porc_compra;
	}

	/**
	 * Metodo para establecer el valor del campo minimo_ret
	 * @param string $minimo_ret
	 */
	public function setMinimoRet($minimo_ret){
		$this->minimo_ret = $minimo_ret;
	}

	/**
	 * Metodo para establecer el valor del campo cta_dev_ventas
	 * @param string $cta_dev_ventas
	 */
	public function setCtaDevVentas($cta_dev_ventas){
		$this->cta_dev_ventas = $cta_dev_ventas;
	}

	/**
	 * Metodo para establecer el valor del campo cta_dev_compras
	 * @param string $cta_dev_compras
	 */
	public function setCtaDevCompras($cta_dev_compras){
		$this->cta_dev_compras = $cta_dev_compras;
	}

	/**
	 * Metodo para establecer el valor del campo cta_hortic
	 * @param string $cta_hortic
	 */
	public function setCtaHortic($cta_hortic){
		$this->cta_hortic = $cta_hortic;
	}

	/**
	 * Metodo para establecer el valor del campo porc_hortic
	 * @param string $porc_hortic
	 */
	public function setPorcHortic($porc_hortic){
		$this->porc_hortic = $porc_hortic;
	}

	/**
	 * Metodo para establecer el valor del campo minimo_v
	 * @param string $minimo_v
	 */
	public function setMinimoV($minimo_v){
		$this->minimo_v = $minimo_v;
	}

	/**
	 * Metodo para establecer el valor del campo minimo_c
	 * @param string $minimo_c
	 */
	public function setMinimoC($minimo_c){
		$this->minimo_c = $minimo_c;
	}

	/**
	 * Metodo para establecer el valor del campo impo_gasto
	 * @param string $impo_gasto
	 */
	public function setImpoGasto($impo_gasto){
		$this->impo_gasto = $impo_gasto;
	}

	/**
	 * Metodo para establecer el valor del campo impo_costo
	 * @param string $impo_costo
	 */
	public function setImpoCosto($impo_costo){
		$this->impo_costo = $impo_costo;
	}


	/**
	 * Devuelve el valor del campo almacen
	 * @return integer
	 */
	public function getAlmacen(){
		return $this->almacen;
	}

	/**
	 * Devuelve el valor del campo linea
	 * @return string
	 */
	public function getLinea(){
		return $this->linea;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo es_auxiliar
	 * @return string
	 */
	public function getEsAuxiliar(){
		return $this->es_auxiliar;
	}

	/**
	 * Devuelve el valor del campo cta_compra
	 * @return string
	 */
	public function getCtaCompra(){
		return $this->cta_compra;
	}

	/**
	 * Devuelve el valor del campo cta_venta
	 * @return string
	 */
	public function getCtaVenta(){
		return $this->cta_venta;
	}

	/**
	 * Devuelve el valor del campo cta_consumo
	 * @return string
	 */
	public function getCtaConsumo(){
		return $this->cta_consumo;
	}

	/**
	 * Devuelve el valor del campo cta_descuento
	 * @return string
	 */
	public function getCtaDescuento(){
		return $this->cta_descuento;
	}

	/**
	 * Devuelve el valor del campo cta_inve
	 * @return string
	 */
	public function getCtaInve(){
		return $this->cta_inve;
	}

	/**
	 * Devuelve el valor del campo cta_costo_venta
	 * @return string
	 */
	public function getCtaCostoVenta(){
		return $this->cta_costo_venta;
	}

	/**
	 * Devuelve el valor del campo cta_ret_compra
	 * @return string
	 */
	public function getCtaRetCompra(){
		return $this->cta_ret_compra;
	}

	/**
	 * Devuelve el valor del campo porc_compra
	 * @return string
	 */
	public function getPorcCompra(){
		return $this->porc_compra;
	}

	/**
	 * Devuelve el valor del campo minimo_ret
	 * @return string
	 */
	public function getMinimoRet(){
		return $this->minimo_ret;
	}

	/**
	 * Devuelve el valor del campo cta_dev_ventas
	 * @return string
	 */
	public function getCtaDevVentas(){
		return $this->cta_dev_ventas;
	}

	/**
	 * Devuelve el valor del campo cta_dev_compras
	 * @return string
	 */
	public function getCtaDevCompras(){
		return $this->cta_dev_compras;
	}

	/**
	 * Devuelve el valor del campo cta_hortic
	 * @return string
	 */
	public function getCtaHortic(){
		return $this->cta_hortic;
	}

	/**
	 * Devuelve el valor del campo porc_hortic
	 * @return string
	 */
	public function getPorcHortic(){
		return $this->porc_hortic;
	}

	/**
	 * Devuelve el valor del campo minimo_v
	 * @return string
	 */
	public function getMinimoV(){
		return $this->minimo_v;
	}

	/**
	 * Devuelve el valor del campo minimo_c
	 * @return string
	 */
	public function getMinimoC(){
		return $this->minimo_c;
	}

	/**
	 * Devuelve el valor del campo impo_gasto
	 * @return string
	 */
	public function getImpoGasto(){
		return $this->impo_gasto;
	}

	/**
	 * Devuelve el valor del campo impo_costo
	 * @return string
	 */
	public function getImpoCosto(){
		return $this->impo_costo;
	}

	public function initialize(){
		$this->addForeignKey('almacen', 'Almacenes', 'codigo', array(
			'message' => 'El almacén no es válido'
		));
		$this->addForeignKey('cta_compra', 'Cuentas', 'cuenta', array(
			'conditions' => "es_auxiliar='S'",
			'message' => 'La cuenta de compra no existe o no es auxiliar'
		));
		$this->addForeignKey('cta_venta', 'Cuentas', 'cuenta', array(
			'conditions' => "es_auxiliar='S'",
			'message' => 'La cuenta de venta no existe o no es auxiliar'
		));
		$this->addForeignKey('cta_consumo', 'Cuentas', 'cuenta', array(
			'conditions' => "es_auxiliar='S'",
			'message' => 'La cuenta de consumo no existe o no es auxiliar'
		));
		$this->addForeignKey('cta_descuento', 'Cuentas', 'cuenta', array(
			'conditions' => "es_auxiliar='S'",
			'message' => 'La cuenta de descuento no existe o no es auxiliar'
		));
		$this->addForeignKey('cta_inve', 'Cuentas', 'cuenta', array(
			'conditions' => "es_auxiliar='S'",
			'message' => 'La cuenta de inventarios no existe o no es auxiliar'
		));
		$this->addForeignKey('cta_costo_venta', 'Cuentas', 'cuenta', array(
			'conditions' => "es_auxiliar='S'",
			'message' => 'La cuenta de costo de venta no existe o no es auxiliar'
		));
		$this->addForeignKey('cta_ret_compra', 'Cuentas', 'cuenta', array(
			'conditions' => "es_auxiliar='S'",
			'message' => 'La cuenta de retención de compras no existe o no es auxiliar'
		));
		$this->addForeignKey('cta_dev_ventas', 'Cuentas', 'cuenta', array(
			'conditions' => "es_auxiliar='S'",
			'message' => 'La cuenta de devolución de ventas no existe o no es auxiliar'
		));
		$this->addForeignKey('cta_dev_compras', 'Cuentas', 'cuenta', array(
			'conditions' => "es_auxiliar='S'",
			'message' => 'La cuenta de devolución de compras no existe o no es auxiliar'
		));
		$this->addForeignKey('cta_hortic', 'Cuentas', 'cuenta', array(
			'conditions' => "es_auxiliar='S'",
			'message' => 'La cuenta de hortifrutícula no existe o no es auxiliar'
		));
	}

}

