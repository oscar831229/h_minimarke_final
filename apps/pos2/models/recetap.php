<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

class Recetap extends RcsRecord {

	/**
	 * Establece el valor del campo almacen
	 * @param decimal $almacen
	 */
	public function setAlmacen($almacen){
		$this->almacen = $almacen;
	}

	/**
	 * Establece el valor del campo numero_rec
	 * @param int $numero_rec
	 */
	public function setNumeroRec($numero_rec){
		$this->numero_rec = $numero_rec;
	}

	/**
	 * Establece el valor del campo nombre
	 * @param char $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Establece el valor del campo num_personas
	 * @param decimal $num_personas
	 */
	public function setNumPersonas($num_personas){
		$this->num_personas = $num_personas;
	}

	/**
	 * Establece el valor del campo tipo
	 * @param char $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Establece el valor del campo porc_varios
	 * @param decimal $porc_varios
	 */
	public function setPorcVarios($porc_varios){
		$this->porc_varios = $porc_varios;
	}

	/**
	 * Establece el valor del campo precio_venta
	 * @param decimal $precio_venta
	 */
	public function setPrecioVenta($precio_venta){
		$this->precio_venta = $precio_venta;
	}

	/**
	 * Establece el valor del campo iva
	 * @param decimal $iva
	 */
	public function setIva($iva){
		$this->iva = $iva;
	}

	/**
	 * Establece el valor del campo precio_costo
	 * @param decimal $precio_costo
	 */
	public function setPrecioCosto($precio_costo){
		$this->precio_costo = $precio_costo;
	}

	/**
	 * Establece el valor del campo porc_costo
	 * @param decimal $porc_costo
	 */
	public function setPorcCosto($porc_costo){
		$this->porc_costo = $porc_costo;
	}

	/**
	 * Establece el valor del campo costoent
	 * @param decimal $costoent
	 */
	public function setCostoent($costoent){
		$this->costoent = $costoent;
	}

	/**
	 * Establece el valor del campo costoent
	 * @param decimal $costoent
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Devuelve el valor del campo almacen
	 * @return decimal
	 */
	public function getAlmacen(){
		return $this->almacen;
	}

	/**
	 * Devuelve el valor del campo numero_rec
	 * @return int
	 */
	public function getNumeroRec(){
		return $this->numero_rec;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return char
	 */
	public function getNombre(){
		return trim($this->nombre);
	}

	/**
	 * Devuelve el valor del campo num_personas
	 * @return decimal
	 */
	public function getNumPersonas(){
		return $this->num_personas;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return char
	 */
	public function getTipo(){
		return trim($this->tipo);
	}

	/**
	 * Devuelve el valor del campo porc_varios
	 * @return decimal
	 */
	public function getPorcVarios(){
		return $this->porc_varios;
	}

	/**
	 * Devuelve el valor del campo precio_venta
	 * @return decimal
	 */
	public function getPrecioVenta(){
		return $this->precio_venta;
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return decimal
	 */
	public function getIva(){
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo precio_costo
	 * @return decimal
	 */
	public function getPrecioCosto(){
		return $this->precio_costo;
	}

	/**
	 * Devuelve el valor del campo porc_costo
	 * @return decimal
	 */
	public function getPorcCosto(){
		return $this->porc_costo;
	}

	/**
	 * Devuelve el valor del campo costoent
	 * @return decimal
	 */
	public function getCostoent(){
		return $this->costoent;
	}

	/**
	 * Establece el valor del campo costoent
	 * @param decimal $costoent
	 */
	public function getEstado(){
		return $this->estado;
	}


	/**
	 * Metodo Inicializador
	 */
	public function initialize(){
		$config = CoreConfig::readFromActiveApplication("app.ini", 'ini');
		if(isset($config->pos->ramocol)){
			$this->setSchema($config->pos->ramocol);
		} else {
			$this->setSchema("ramocol");
		}
	}
}
