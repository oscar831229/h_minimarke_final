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

class Banco extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $oficina;

	/**
	 * @var string
	 */
	protected $ciudad;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo oficina
	 * @param string $oficina
	 */
	public function setOficina($oficina){
		$this->oficina = $oficina;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad
	 * @param string $ciudad
	 */
	public function setCiudad($ciudad){
		$this->ciudad = $ciudad;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo oficina
	 * @return string
	 */
	public function getOficina(){
		return $this->oficina;
	}

	/**
	 * Devuelve el valor del campo ciudad
	 * @return string
	 */
	public function getCiudad(){
		return $this->ciudad;
	}

	protected function beforeDelete(){
		if($this->countCuentasBancos()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el banco porque se está usando en cuentas bancarias', 'cuenta'));
			return false;
		}
	}

	protected function beforeCreate(){
		if($this->count("nombre='{$this->nombre}'")>0){
			$this->appendMessage(new ActiveRecordMessage('Ya existe un banco con ese nombre', 'nombre'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('CuentasBancos');
	}

}

