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

class Usuarios extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $sucursal_id;

	/**
	 * @var string
	 */
	protected $login;

	/**
	 * @var string
	 */
	protected $clave;

	/**
	 * @var string
	 */
	protected $clave_corta;

	/**
	 * @var string
	 */
	protected $apellidos;

	/**
	 * @var string
	 */
	protected $nombres;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $genero;

	/**
	 * @var Date
	 */
	protected $clave_fecha;

	/**
	 * @var string
	 */
	protected $creado_at;

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
	 * Metodo para establecer el valor del campo sucursal_id
	 * @param integer $sucursal_id
	 */
	public function setSucursalId($sucursal_id){
		$this->sucursal_id = $sucursal_id;
	}

	/**
	 * Metodo para establecer el valor del campo login
	 * @param string $login
	 */
	public function setLogin($login){
		$this->login = $login;
	}

	/**
	 * Metodo para establecer el valor del campo clave
	 * @param string $clave
	 */
	public function setClave($clave){
		$this->clave = $clave;
	}

	/**
	 * Metodo para establecer el valor del campo clave_corta
	 * @param string $clave_corta
	 */
	public function setClaveCorta($clave_corta){
		$this->clave_corta = $clave_corta;
	}

	/**
	 * Metodo para establecer el valor del campo apellidos
	 * @param string $apellidos
	 */
	public function setApellidos($apellidos){
		$this->apellidos = $apellidos;
	}

	/**
	 * Metodo para establecer el valor del campo nombres
	 * @param string $nombres
	 */
	public function setNombres($nombres){
		$this->nombres = $nombres;
	}

	/**
	 * Metodo para establecer el valor del campo email
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

	/**
	 * Metodo para establecer el valor del campo genero
	 * @param string $genero
	 */
	public function setGenero($genero){
		$this->genero = $genero;
	}

	/**
	 * Metodo para establecer el valor del campo clave_fecha
	 * @param Date $clave_fecha
	 */
	public function setClaveFecha($clave_fecha){
		$this->clave_fecha = $clave_fecha;
	}

	/**
	 * Metodo para establecer el valor del campo creado_at
	 * @param string $creado_at
	 */
	public function setCreadoAt($creado_at){
		$this->creado_at = $creado_at;
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
	 * Devuelve el valor del campo sucursal_id
	 * @return integer
	 */
	public function getSucursalId(){
		return $this->sucursal_id;
	}

	/**
	 * Devuelve el valor del campo login
	 * @return string
	 */
	public function getLogin(){
		return $this->login;
	}

	/**
	 * Devuelve el valor del campo clave
	 * @return string
	 */
	public function getClave(){
		return $this->clave;
	}

	/**
	 * Devuelve el valor del campo clave_corta
	 * @return string
	 */
	public function getClaveCorta(){
		return $this->clave_corta;
	}

	/**
	 * Devuelve el valor del campo apellidos
	 * @return string
	 */
	public function getApellidos(){
		return $this->apellidos;
	}

	/**
	 * Devuelve el valor del campo nombres
	 * @return string
	 */
	public function getNombres(){
		return $this->nombres;
	}

	/**
	 * Devuelve el valor del campo email
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Devuelve el valor del campo genero
	 * @return string
	 */
	public function getGenero(){
		return $this->genero;
	}

	/**
	 * Devuelve el valor del campo clave_fecha
	 * @return Date
	 */
	public function getClaveFecha(){
		return new Date($this->clave_fecha);
	}

	/**
	 * Devuelve el valor del campo creado_at
	 * @return string
	 */
	public function getCreadoAt(){
		return $this->creado_at;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSchema("hfos_identity");
	}

}

