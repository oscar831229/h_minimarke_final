<?php

class InveStocks extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $item;

	/**
	 * @var integer
	 */
	protected $almacen;

	/**
	 * @var string
	 */
	protected $minimo;

	/**
	 * @var string
	 */
	protected $maximo;


	/**
	 * Metodo para establecer el valor del campo item
	 * @param string $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo almacen
	 * @param integer $almacen
	 */
	public function setAlmacen($almacen){
		$this->almacen = $almacen;
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
	 * Devuelve el valor del campo item
	 * @return string
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo almacen
	 * @return integer
	 */
	public function getAlmacen(){
		return $this->almacen;
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

	public function initialize(){
		$config = CoreConfig::readFromActiveApplication('app.ini', 'ini');
		if(isset($config->pos->ramocol)){
			$this->setSchema($config->pos->ramocol);
		} else {
			$this->setSchema('ramocol');
		}
	}

}

