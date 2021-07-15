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
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Datos extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $documento;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $nombre_hotel;

	/**
	 * @var string
	 */
	protected $nombre_cadena;

	/**
	 * @var string
	 */
	protected $direccion;

	/**
	 * @var string
	 */
	protected $telefonos;

	/**
	 * @var string
	 */
	protected $fax;

	/**
	 * @var string
	 */
	protected $po_box;

	/**
	 * @var string
	 */
	protected $ciudad;

	/**
	 * @var string
	 */
	protected $pais;

	/**
	 * @var string
	 */
	protected $entidad;

	/**
	 * @var string
	 */
	protected $moneda;

	/**
	 * @var string
	 */
	protected $centavos;

	/**
	 * @var string
	 */
	protected $nota_contribuyentes;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $print_server;

	/**
	 * @var string
	 */
	protected $version;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param string $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo documento
	 * @param string $documento
	 */
	public function setDocumento($documento){
		$this->documento = $documento;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo nombre_hotel
	 * @param string $nombre_hotel
	 */
	public function setNombreHotel($nombre_hotel){
		$this->nombre_hotel = $nombre_hotel;
	}

	/**
	 * Metodo para establecer el valor del campo nombre_cadena
	 * @param string $nombre_cadena
	 */
	public function setNombreCadena($nombre_cadena){
		$this->nombre_cadena = $nombre_cadena;
	}

	/**
	 * Metodo para establecer el valor del campo direccion
	 * @param string $direccion
	 */
	public function setDireccion($direccion){
		$this->direccion = $direccion;
	}

	/**
	 * Metodo para establecer el valor del campo telefonos
	 * @param string $telefonos
	 */
	public function setTelefonos($telefonos){
		$this->telefonos = $telefonos;
	}

	/**
	 * Metodo para establecer el valor del campo fax
	 * @param string $fax
	 */
	public function setFax($fax){
		$this->fax = $fax;
	}

	/**
	 * Metodo para establecer el valor del campo po_box
	 * @param string $po_box
	 */
	public function setPoBox($po_box){
		$this->po_box = $po_box;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad
	 * @param string $ciudad
	 */
	public function setCiudad($ciudad){
		$this->ciudad = $ciudad;
	}

	/**
	 * Metodo para establecer el valor del campo pais
	 * @param string $pais
	 */
	public function setPais($pais){
		$this->pais = $pais;
	}

	/**
	 * Metodo para establecer el valor del campo entidad
	 * @param string $entidad
	 */
	public function setEntidad($entidad){
		$this->entidad = $entidad;
	}

	/**
	 * Metodo para establecer el valor del campo moneda
	 * @param string $moneda
	 */
	public function setMoneda($moneda){
		$this->moneda = $moneda;
	}

	/**
	 * Metodo para establecer el valor del campo centavos
	 * @param string $centavos
	 */
	public function setCentavos($centavos){
		$this->centavos = $centavos;
	}

	/**
	 * Metodo para establecer el valor del campo nota_contribuyentes
	 * @param string $nota_contribuyentes
	 */
	public function setNotaContribuyentes($nota_contribuyentes){
		$this->nota_contribuyentes = $nota_contribuyentes;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo print_server
	 * @param string $print_server
	 */
	public function setPrintServer($print_server){
		$this->print_server = $print_server;
	}

	/**
	 * Metodo para establecer el valor del campo version
	 * @param string $version
	 */
	public function setVersion($version){
		$this->version = $version;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return string
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo documento
	 * @return string
	 */
	public function getDocumento(){
		return $this->documento;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo nombre_hotel
	 * @return string
	 */
	public function getNombreHotel(){
		return $this->nombre_hotel;
	}

	/**
	 * Devuelve el valor del campo nombre_cadena
	 * @return string
	 */
	public function getNombreCadena(){
		return $this->nombre_cadena;
	}

	/**
	 * Devuelve el valor del campo direccion
	 * @return string
	 */
	public function getDireccion(){
		return $this->direccion;
	}

	/**
	 * Devuelve el valor del campo telefonos
	 * @return string
	 */
	public function getTelefonos(){
		return $this->telefonos;
	}

	/**
	 * Devuelve el valor del campo fax
	 * @return string
	 */
	public function getFax(){
		return $this->fax;
	}

	/**
	 * Devuelve el valor del campo po_box
	 * @return string
	 */
	public function getPoBox(){
		return $this->po_box;
	}

	/**
	 * Devuelve el valor del campo ciudad
	 * @return string
	 */
	public function getCiudad(){
		return $this->ciudad;
	}

	/**
	 * Devuelve el valor del campo pais
	 * @return string
	 */
	public function getPais(){
		return $this->pais;
	}

	/**
	 * Devuelve el valor del campo entidad
	 * @return string
	 */
	public function getEntidad(){
		return $this->entidad;
	}

	/**
	 * Devuelve el valor del campo moneda
	 * @return string
	 */
	public function getMoneda(){
		return $this->moneda;
	}

	/**
	 * Devuelve el valor del campo centavos
	 * @return string
	 */
	public function getCentavos(){
		return $this->centavos;
	}

	/**
	 * Devuelve el valor del campo nota_contribuyentes
	 * @return string
	 */
	public function getNotaContribuyentes(){
		return $this->nota_contribuyentes;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo print_server
	 * @return string
	 */
	public function getPrintServer(){
		return $this->print_server;
	}

	/**
	 * Devuelve el valor del campo version
	 * @return string
	 */
	public function getVersion(){
		return $this->version;
	}

	protected function beforeCreate(){
		return POSRcs::beforeCreate($this);
	}

	protected function afterCreate(){
		return POSRcs::afterCreate($this);
	}

	protected function beforeUpdate(){
		return POSRcs::beforeUpdate($this);
	}

	protected function afterUpdate(){
		return POSRcs::afterUpdate($this);
	}

	protected function beforeDelete(){
		Flash::error("Los datos del hotel no pueden ser borrados");
		return false;
	}

	public function initialize(){
		$this->setSchema('pos');
	}

}

