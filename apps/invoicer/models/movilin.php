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

class Movilin extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var string
	 */
	protected $almacen;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $num_linea;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $almacen_destino;

	/**
	 * @var string
	 */
	protected $item;

	/**
	 * @var string
	 */
	protected $cantidad;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $cantidad_rec;

	/**
	 * @var string
	 */
	protected $cantidad_desp;

	/**
	 * @var string
	 */
	protected $costo;

	/**
	 * @var string
	 */
	protected $nota;

	/**
	 * @var string
	 */
	protected $prioridad;

	/**
	 * @var integer
	 */
	protected $iva;

	/**
	 * @var string
	 */
	protected $descuento;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo comprob
	 * @param string $comprob
	 */
	public function setComprob($comprob){
		$this->comprob = $comprob;
	}

	/**
	 * Metodo para establecer el valor del campo almacen
	 * @param string $almacen
	 */
	public function setAlmacen($almacen){
		$this->almacen = $almacen;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo num_linea
	 * @param integer $num_linea
	 */
	public function setNumLinea($num_linea){
		$this->num_linea = $num_linea;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo almacen_destino
	 * @param string $almacen_destino
	 */
	public function setAlmacenDestino($almacen_destino){
		$this->almacen_destino = $almacen_destino;
	}

	/**
	 * Metodo para establecer el valor del campo item
	 * @param string $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo cantidad
	 * @param string $cantidad
	 */
	public function setCantidad($cantidad){
		$this->cantidad = $cantidad;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo cantidad_rec
	 * @param string $cantidad_rec
	 */
	public function setCantidadRec($cantidad_rec){
		$this->cantidad_rec = $cantidad_rec;
	}

	/**
	 * Metodo para establecer el valor del campo cantidad_desp
	 * @param string $cantidad_desp
	 */
	public function setCantidadDesp($cantidad_desp){
		$this->cantidad_desp = $cantidad_desp;
	}

	/**
	 * Metodo para establecer el valor del campo costo
	 * @param string $costo
	 */
	public function setCosto($costo){
		$this->costo = $costo;
	}

	/**
	 * Metodo para establecer el valor del campo nota
	 * @param string $nota
	 */
	public function setNota($nota){
		$this->nota = $nota;
	}

	/**
	 * Metodo para establecer el valor del campo prioridad
	 * @param string $prioridad
	 */
	public function setPrioridad($prioridad){
		$this->prioridad = $prioridad;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param integer $iva
	 */
	public function setIva($iva){
		$this->iva = $iva;
	}

	/**
	 * Metodo para establecer el valor del campo descuento
	 * @param string $descuento
	 */
	public function setDescuento($descuento){
		$this->descuento = $descuento;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo comprob
	 * @return string
	 */
	public function getComprob(){
		return $this->comprob;
	}

	/**
	 * Devuelve el valor del campo almacen
	 * @return string
	 */
	public function getAlmacen(){
		return $this->almacen;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo num_linea
	 * @return integer
	 */
	public function getNumLinea(){
		return $this->num_linea;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo almacen_destino
	 * @return string
	 */
	public function getAlmacenDestino(){
		return $this->almacen_destino;
	}

	/**
	 * Devuelve el valor del campo item
	 * @return string
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo cantidad
	 * @return string
	 */
	public function getCantidad(){
		return $this->cantidad;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo cantidad_rec
	 * @return string
	 */
	public function getCantidadRec(){
		return $this->cantidad_rec;
	}

	/**
	 * Devuelve el valor del campo cantidad_desp
	 * @return string
	 */
	public function getCantidadDesp(){
		return $this->cantidad_desp;
	}

	/**
	 * Devuelve el valor del campo costo
	 * @return string
	 */
	public function getCosto(){
		return $this->costo;
	}

	/**
	 * Devuelve el valor del campo nota
	 * @return string
	 */
	public function getNota(){
		return $this->nota;
	}

	/**
	 * Devuelve el valor del campo prioridad
	 * @return string
	 */
	public function getPrioridad(){
		return $this->prioridad;
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return integer
	 */
	public function getIva(){
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo descuento
	 * @return string
	 */
	public function getDescuento(){
		return $this->descuento;
	}

	public function initialize(){
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->back_db)){
			$this->setSchema($config->hfos->back_db);
		} else {
			$this->setSchema('ramocol');
		}
		
		$this->belongsTo('item', 'Inve', 'item');
		$this->hasOne(array('comprob','almacen','numero'), 'Movihead', array('comprob','almacen','numero'));
	}

}

