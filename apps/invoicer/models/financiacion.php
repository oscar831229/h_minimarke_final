<?php

class Financiacion extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $factura_id;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $mora;

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
	 * Metodo para establecer el valor del campo factura_id
	 * @param integer $factura_id
	 */
	public function setFacturaId($factura_id){
		$this->factura_id = $factura_id;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo mora
	 * @param string $mora
	 */
	public function setMora($mora){
		$this->mora = $mora;
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
	 * Devuelve el valor del campo factura_id
	 * @return integer
	 */
	public function getFacturaId(){
		return $this->factura_id;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo mora
	 * @return string
	 */
	public function getMora(){
		return $this->mora;
	}

	/**
	 * Devuelve el valor del campo total
	 * @return string
	 */
	public function getTotal(){
		return $this->total;
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

