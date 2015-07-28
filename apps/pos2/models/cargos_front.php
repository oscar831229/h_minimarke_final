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
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class CargosFront extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $numero_folio;

	/**
	 * @var integer
	 */
	protected $numero_cuenta;

	/**
	 * @var integer
	 */
	protected $item;

	/**
	 * @var integer
	 */
	protected $codigo_cargo;

	/**
	 * @var string
	 */
	protected $prefijo_facturacion;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $es_propina;

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
	 * Metodo para establecer el valor del campo numero_folio
	 * @param integer $numero_folio
	 */
	public function setNumeroFolio($numero_folio){
		$this->numero_folio = $numero_folio;
	}

	/**
	 * Metodo para establecer el valor del campo numero_cuenta
	 * @param integer $numero_cuenta
	 */
	public function setNumeroCuenta($numero_cuenta){
		$this->numero_cuenta = $numero_cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo item
	 * @param integer $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo codigo_cargo
	 * @param integer $codigo_cargo
	 */
	public function setCodigoCargo($codigo_cargo){
		$this->codigo_cargo = $codigo_cargo;
	}

	/**
	 * Metodo para establecer el valor del campo prefijo_facturacion
	 * @param string $prefijo_facturacion
	 */
	public function setPrefijoFacturacion($prefijo_facturacion){
		$this->prefijo_facturacion = $prefijo_facturacion;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo es_propina
	 * @param string $es_propina
	 */
	public function setEsPropina($es_propina){
		$this->es_propina = $es_propina;
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
	 * Devuelve el valor del campo numero_folio
	 * @return integer
	 */
	public function getNumeroFolio(){
		return $this->numero_folio;
	}

	/**
	 * Devuelve el valor del campo numero_cuenta
	 * @return integer
	 */
	public function getNumeroCuenta(){
		return $this->numero_cuenta;
	}

	/**
	 * Devuelve el valor del campo item
	 * @return integer
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo codigo_cargo
	 * @return integer
	 */
	public function getCodigoCargo(){
		return $this->codigo_cargo;
	}

	/**
	 * Devuelve el valor del campo prefijo_facturacion
	 * @return string
	 */
	public function getPrefijoFacturacion(){
		return $this->prefijo_facturacion;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo es_propina
	 * @return string
	 */
	public function getEsPropina(){
		return $this->es_propina;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function initialize(){
		$this->hasMany('CargosFrontDetalle');
		$this->belongsTo(array('numero_folio', 'numero_cuenta', 'item'), 'Valcar', array('numfol', 'numcue', 'item'));
	}

}

