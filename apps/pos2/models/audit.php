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

class Audit extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $controller;

	/**
	 * @var string
	 */
	protected $action;

	/**
	 * @var Date
	 */
	protected $fecha_at;

	/**
	 * @var string
	 */
	protected $ipaddress;

	/**
	 * @var integer
	 */
	protected $usuarios_id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $nota;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo controller
	 * @param string $controller
	 */
	public function setController($controller){
		$this->controller = $controller;
	}

	/**
	 * Metodo para establecer el valor del campo action
	 * @param string $action
	 */
	public function setAction($action){
		$this->action = $action;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_at
	 * @param Date $fecha_at
	 */
	public function setFechaAt($fecha_at){
		$this->fecha_at = $fecha_at;
	}

	/**
	 * Metodo para establecer el valor del campo ipaddress
	 * @param string $ipaddress
	 */
	public function setIpaddress($ipaddress){
		$this->ipaddress = $ipaddress;
	}

	/**
	 * Metodo para establecer el valor del campo usuarios_id
	 * @param integer $usuarios_id
	 */
	public function setUsuariosId($usuarios_id){
		$this->usuarios_id = $usuarios_id;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo nota
	 * @param string $nota
	 */
	public function setNota($nota){
		$this->nota = $nota;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo controller
	 * @return string
	 */
	public function getController(){
		return $this->controller;
	}

	/**
	 * Devuelve el valor del campo action
	 * @return string
	 */
	public function getAction(){
		return $this->action;
	}

	/**
	 * Devuelve el valor del campo fecha_at
	 * @return Date
	 */
	public function getFechaAt(){
		return $this->fecha_at;
	}

	/**
	 * Devuelve el valor del campo ipaddress
	 * @return string
	 */
	public function getIpaddress(){
		return $this->ipaddress;
	}

	/**
	 * Devuelve el valor del campo usuarios_id
	 * @return integer
	 */
	public function getUsuariosId(){
		return $this->usuarios_id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo nota
	 * @return string
	 */
	public function getNota(){
		return $this->nota;
	}

}

