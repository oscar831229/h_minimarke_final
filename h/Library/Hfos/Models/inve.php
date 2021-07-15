<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Inve extends RcsRecord {

	/**
	 * @var string
	 */
	protected $item;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $linea;

	/**
	 * @var string
	 */
	protected $unidad;

	/**
	 * @var string
	 */
	protected $unidad_procion;


	/**
	 * @var string
	 */
	protected $minimo;

	/**
	 * @var string
	 */
	protected $maximo;

	/**
	 * @var string
	 */
	protected $peso;

	/**
	 * @var string
	 */
	protected $volumen;

	/**
	 * @var string
	 */
	protected $plazo_reposicion;

	/**
	 * @var string
	 */
	protected $producto;

	/**
	 * @var string
	 */
	protected $saldo_actual;

	/**
	 * @var string
	 */
	protected $fisico;

	/**
	 * @var string
	 */
	protected $costo_actual;

	/**
	 * @var string
	 */
	protected $precio_compra;

	/**
	 * @var Date
	 */
	protected $f_u_compra;

	/**
	 * @var string
	 */
	protected $precio_venta_m;

	/**
	 * @var Date
	 */
	protected $f_u_venta;

	/**
	 * @var integer
	 */
	protected $iva;

	/**
	 * @var string
	 */
	protected $por_recibir;

	/**
	 * @var string
	 */
	protected $por_entregar;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo item
	 * @param string $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo linea
	 * @param string $linea
	 */
	public function setLinea($linea){
		$this->linea = $linea;
	}

	/**
	 * Metodo para establecer el valor del campo unidad
	 * @param string $unidad
	 */
	public function setUnidad($unidad){
		$this->unidad = $unidad;
	}

	/**
	 * Metodo para establecer el valor del campo unidad de porción
	 * @param string $unidad
	 */
	public function setUnidadPorcion($unidad){
		$this->unidad_procion = $unidad;
	}

	/**
	 * Metodo para establecer el valor del campo minimo
	 * @param string $minimo
	 */
	public function setMinimo($minimo){
		$this->minimo = $minimo;
	}

	/**
	 * Metodo para establecer el valor del campo maximo
	 * @param string $maximo
	 */
	public function setMaximo($maximo){
		$this->maximo = $maximo;
	}

	/**
	 * Metodo para establecer el valor del campo peso
	 * @param string $peso
	 */
	public function setPeso($peso){
		$this->peso = $peso;
	}

	/**
	 * Metodo para establecer el valor del campo volumen
	 * @param string $volumen
	 */
	public function setVolumen($volumen){
		$this->volumen = $volumen;
	}

	/**
	 * Metodo para establecer el valor del campo plazo_reposicion
	 * @param string $plazo_reposicion
	 */
	public function setPlazoReposicion($plazo_reposicion){
		$this->plazo_reposicion = $plazo_reposicion;
	}

	/**
	 * Metodo para establecer el valor del campo producto
	 * @param string $producto
	 */
	public function setProducto($producto){
		$this->producto = $producto;
	}

	/**
	 * Metodo para establecer el valor del campo saldo_actual
	 * @param string $saldo_actual
	 */
	public function setSaldoActual($saldo_actual){
		$this->saldo_actual = $saldo_actual;
	}

	/**
	 * Metodo para establecer el valor del campo fisico
	 * @param string $fisico
	 */
	public function setFisico($fisico){
		$this->fisico = $fisico;
	}

	/**
	 * Metodo para establecer el valor del campo costo_actual
	 * @param string $costo_actual
	 */
	public function setCostoActual($costo_actual){
		$this->costo_actual = $costo_actual;
	}

	/**
	 * Metodo para establecer el valor del campo precio_compra
	 * @param string $precio_compra
	 */
	public function setPrecioCompra($precio_compra){
		$this->precio_compra = $precio_compra;
	}

	/**
	 * Metodo para establecer el valor del campo f_u_compra
	 * @param Date $f_u_compra
	 */
	public function setFUCompra($f_u_compra){
		$this->f_u_compra = $f_u_compra;
	}

	/**
	 * Metodo para establecer el valor del campo precio_venta_m
	 * @param string $precio_venta_m
	 */
	public function setPrecioVentaM($precio_venta_m){
		$this->precio_venta_m = $precio_venta_m;
	}

	/**
	 * Metodo para establecer el valor del campo f_u_venta
	 * @param Date $f_u_venta
	 */
	public function setFUVenta($f_u_venta){
		$this->f_u_venta = $f_u_venta;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param integer $iva
	 */
	public function setIva($iva){
		$this->iva = $iva;
	}

	/**
	 * Metodo para establecer el valor del campo por_recibir
	 * @param string $por_recibir
	 */
	public function setPorRecibir($por_recibir){
		$this->por_recibir = $por_recibir;
	}

	/**
	 * Metodo para establecer el valor del campo por_entregar
	 * @param string $por_entregar
	 */
	public function setPorEntregar($por_entregar){
		$this->por_entregar = $por_entregar;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo item
	 * @return string
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo linea
	 * @return string
	 */
	public function getLinea(){
		return $this->linea;
	}

	/**
	 * Devuelve el valor del campo unidad
	 * @return string
	 */
	public function getUnidad(){
		return $this->unidad;
	}

	/**
	 * Devuelve el valor del campo unidad de porción
	 * @return string
	 */
	public function getUnidadPorcion(){
		return $this->unidad_porcion;
	}

	/**
	 * Devuelve el valor del campo minimo
	 * @return string
	 */
	public function getMinimo(){
		return $this->minimo;
	}

	/**
	 * Devuelve el valor del campo maximo
	 * @return string
	 */
	public function getMaximo(){
		return $this->maximo;
	}

	/**
	 * Devuelve el valor del campo peso
	 * @return string
	 */
	public function getPeso(){
		return $this->peso;
	}

	/**
	 * Devuelve el valor del campo volumen
	 * @return string
	 */
	public function getVolumen(){
		return $this->volumen;
	}

	/**
	 * Devuelve el valor del campo plazo_reposicion
	 * @return string
	 */
	public function getPlazoReposicion(){
		return $this->plazo_reposicion;
	}

	/**
	 * Devuelve el valor del campo producto
	 * @return string
	 */
	public function getProducto(){
		return $this->producto;
	}

	/**
	 * Devuelve el valor del campo saldo_actual
	 * @return string
	 */
	public function getSaldoActual(){
		return $this->saldo_actual;
	}

	/**
	 * Devuelve el valor del campo fisico
	 * @return string
	 */
	public function getFisico(){
		return $this->fisico;
	}

	/**
	 * Devuelve el valor del campo costo_actual
	 * @return string
	 */
	public function getCostoActual(){
		return $this->costo_actual;
	}

	/**
	 * Devuelve el valor del campo precio_compra
	 * @return string
	 */
	public function getPrecioCompra(){
		return $this->precio_compra;
	}

	/**
	 * Devuelve el valor del campo f_u_compra
	 * @return Date
	 */
	public function getFUCompra(){
		return new Date($this->f_u_compra);
	}

	/**
	 * Devuelve el valor del campo precio_venta_m
	 * @return string
	 */
	public function getPrecioVentaM(){
		return $this->precio_venta_m;
	}

	/**
	 * Devuelve el valor del campo f_u_venta
	 * @return Date
	 */
	public function getFUVenta(){
		return new Date($this->f_u_venta);
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return integer
	 */
	public function getIva(){
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo por_recibir
	 * @return string
	 */
	public function getPorRecibir(){
		return $this->por_recibir;
	}

	/**
	 * Devuelve el valor del campo por_entregar
	 * @return string
	 */
	public function getPorEntregar(){
		return $this->por_entregar;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	protected function beforeSave(){
		$this->descripcion = i18n::strtoupper(trim($this->descripcion));
		$linea = BackCacher::getLinea(1, $this->linea);
		if($linea==false){
			$this->appendMessage(new ActiveRecordMessage('La línea de productos "'.$this->linea.'" no es válida', 'linea'));
			return false;
		} else {
			if($linea->getEsAuxiliar()!='S'){
				$this->appendMessage(new ActiveRecordMessage('La línea de productos "'.$this->linea.'" asignada a la referencia no recibe referencias', 'linea'));
				return false;
			}
		}
	}

	/**
	 * Validador del modelo Inve
	 *
	 * @return boolean
	 */
	protected function validation(){
		$this->validate('InclusionIn', array(
			'field' => 'estado',
			'domain' => array('A', 'I'),
			'message' => 'El estado debe ser "ACTIVO" ó "INACTIVO"',
			'required' => true
		));
		if($this->validationHasFailed()==true){
			return false;
		}
	}

	protected function beforeDelete(){
		if($this->countMovilin()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la referencia porque tiene movimiento en Inventarios', 'item'));
			return false;
		}
	}

	public function initialize(){

		$this->hasMany('item', 'Movilin', 'item');

		$this->addForeignKey('unidad', 'Unidad', 'codigo', array(
			'message' => 'La unidad de medida no es válida'
		));
		$this->addForeignKey('unidad_porcion', 'Unidad', 'codigo', array(
			'message' => 'La unidad de medida no es válida'
		));
		$this->addForeignKey('producto', 'Producto', 'codigo', array(
			'message' => 'El tipo de producto no es válido'
		));

	}

}

