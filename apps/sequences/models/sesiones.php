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

class Sesiones extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $sid;

	/**
	 * @var integer
	 */
	protected $usuarios_id;

	/**
	 * @var string
	 */
	protected $ipaddress;

	/**
	 * @var integer
	 */
	protected $creado;

	/**
	 * @var integer
	 */
	protected $modificado;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo sid
	 * @param string $sid
	 */
	public function setSid($sid){
		$this->sid = $sid;
	}

	/**
	 * Metodo para establecer el valor del campo usuarios_id
	 * @param integer $usuarios_id
	 */
	public function setUsuariosId($usuarios_id){
		$this->usuarios_id = $usuarios_id;
	}

	/**
	 * Metodo para establecer el valor del campo ipaddress
	 * @param string $ipaddress
	 */
	public function setIpaddress($ipaddress){
		$this->ipaddress = $ipaddress;
	}

	/**
	 * Metodo para establecer el valor del campo creado
	 * @param integer $creado
	 */
	public function setCreado($creado){
		$this->creado = $creado;
	}

	/**
	 * Metodo para establecer el valor del campo modificado
	 * @param integer $modificado
	 */
	public function setModificado($modificado){
		$this->modificado = $modificado;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo sid
	 * @return string
	 */
	public function getSid(){
		return $this->sid;
	}

	/**
	 * Devuelve el valor del campo usuarios_id
	 * @return integer
	 */
	public function getUsuariosId(){
		return $this->usuarios_id;
	}

	/**
	 * Devuelve el valor del campo ipaddress
	 * @return string
	 */
	public function getIpaddress(){
		return $this->ipaddress;
	}

	/**
	 * Devuelve el valor del campo creado
	 * @return integer
	 */
	public function getCreado(){
		return $this->creado;
	}

	/**
	 * Devuelve el valor del campo modificado
	 * @return integer
	 */
	public function getModificado(){
		return $this->modificado;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('usuarios');
	}

}

