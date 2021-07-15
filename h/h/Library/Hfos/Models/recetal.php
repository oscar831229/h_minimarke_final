<?php

class Recetal extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $tipol;

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
	protected $item;

	/**
	 * @var string
	 */
	protected $divisor;

	/**
	 * @var string
	 */
	protected $cantidad;

	/**
	 * @var string
	 */
	protected $valore;

	/**
	 * @var string
	 */
	protected $valor;


	/**
	 * Metodo para establecer el valor del campo tipol
	 * @param string $tipol
	 */
	public function setTipol($tipol){
		$this->tipol = $tipol;
	}

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
	 * Metodo para establecer el valor del campo item
	 * @param string $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo divisor
	 * @param string $divisor
	 */
	public function setDivisor($divisor){
		$this->divisor = $divisor;
	}

	/**
	 * Metodo para establecer el valor del campo cantidad
	 * @param string $cantidad
	 */
	public function setCantidad($cantidad){
		$this->cantidad = $cantidad;
	}

	/**
	 * Metodo para establecer el valor del campo valore
	 * @param string $valore
	 */
	public function setValore($valore){
		$this->valore = $valore;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}


	/**
	 * Devuelve el valor del campo tipol
	 * @return string
	 */
	public function getTipol(){
		return $this->tipol;
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
	 * Devuelve el valor del campo item
	 * @return string
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo divisor
	 * @return string
	 */
	public function getDivisor(){
		return $this->divisor;
	}

	/**
	 * Devuelve el valor del campo cantidad
	 * @return string
	 */
	public function getCantidad(){
		return $this->cantidad;
	}

	/**
	 * Devuelve el valor del campo valore
	 * @return string
	 */
	public function getValore(){
		return $this->valore;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

}

