<?php

class Recetap extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $almacen;

	/**
	 * @var integer
	 */
	protected $numero_rec;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $num_personas;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $porc_varios;

	/**
	 * @var string
	 */
	protected $precio_venta;

	/**
	 * @var string
	 */
	protected $iva;

	/**
	 * @var string
	 */
	protected $precio_costo;

	/**
	 * @var string
	 */
	protected $porc_costo;

	/**
	 * @var string
	 */
	protected $costoent;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo almacen
	 * @param string $almacen
	 */
	public function setAlmacen($almacen){
		$this->almacen = $almacen;
	}

	/**
	 * Metodo para establecer el valor del campo numero_rec
	 * @param integer $numero_rec
	 */
	public function setNumeroRec($numero_rec){
		$this->numero_rec = $numero_rec;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo num_personas
	 * @param string $num_personas
	 */
	public function setNumPersonas($num_personas){
		$this->num_personas = $num_personas;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo porc_varios
	 * @param string $porc_varios
	 */
	public function setPorcVarios($porc_varios){
		$this->porc_varios = $porc_varios;
	}

	/**
	 * Metodo para establecer el valor del campo precio_venta
	 * @param string $precio_venta
	 */
	public function setPrecioVenta($precio_venta){
		$this->precio_venta = $precio_venta;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param string $iva
	 */
	public function setIva($iva){
		$this->iva = $iva;
	}

	/**
	 * Metodo para establecer el valor del campo precio_costo
	 * @param string $precio_costo
	 */
	public function setPrecioCosto($precio_costo){
		$this->precio_costo = $precio_costo;
	}

	/**
	 * Metodo para establecer el valor del campo porc_costo
	 * @param string $porc_costo
	 */
	public function setPorcCosto($porc_costo){
		$this->porc_costo = $porc_costo;
	}

	/**
	 * Metodo para establecer el valor del campo costoent
	 * @param string $costoent
	 */
	public function setCostoent($costoent){
		$this->costoent = $costoent;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo almacen
	 * @return string
	 */
	public function getAlmacen(){
		return $this->almacen;
	}

	/**
	 * Devuelve el valor del campo numero_rec
	 * @return integer
	 */
	public function getNumeroRec(){
		return $this->numero_rec;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo num_personas
	 * @return string
	 */
	public function getNumPersonas(){
		return $this->num_personas;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo porc_varios
	 * @return string
	 */
	public function getPorcVarios(){
		return $this->porc_varios;
	}

	/**
	 * Devuelve el valor del campo precio_venta
	 * @return string
	 */
	public function getPrecioVenta(){
		return $this->precio_venta;
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return string
	 */
	public function getIva(){
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo precio_costo
	 * @return string
	 */
	public function getPrecioCosto(){
		return $this->precio_costo;
	}

	/**
	 * Devuelve el valor del campo porc_costo
	 * @return string
	 */
	public function getPorcCosto(){
		return $this->porc_costo;
	}

	/**
	 * Devuelve el valor del campo costoent
	 * @return string
	 */
	public function getCostoent(){
		return $this->costoent;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

