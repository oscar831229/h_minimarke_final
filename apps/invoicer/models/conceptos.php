<?php

class Conceptos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $precio_venta;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $cuenta_cruce;

	/**
	 * @var string
	 */
	protected $naturaleza;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo precio_venta
	 * @param string $precio_venta
	 */
	public function setPrecioVenta($precio_venta){
		$this->precio_venta = $precio_venta;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta_cruce
	 * @param string $cuenta_cruce
	 */
	public function setCuentaCruce($cuenta_cruce){
		$this->cuenta_cruce = $cuenta_cruce;
	}

	/**
	 * Metodo para establecer el valor del campo naturaleza
	 * @param string $naturaleza
	 */
	public function setNaturaleza($naturaleza){
		$this->naturaleza = $naturaleza;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo precio_venta
	 * @return string
	 */
	public function getPrecioVenta(){
		return $this->precio_venta;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo cuenta_cruce
	 * @return string
	 */
	public function getCuentaCruce(){
		return $this->cuenta_cruce;
	}

	/**
	 * Devuelve el valor del campo naturaleza
	 * @return string
	 */
	public function getNaturaleza(){
		return $this->naturaleza;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	protected function initialize(){
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->invoicer)){
			$this->setSchema($config->hfos->invoicer);
		} else {
			$this->setSchema('invoicer');
		}
	}
}

