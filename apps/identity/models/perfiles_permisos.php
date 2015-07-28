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

class PerfilesPermisos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $perfiles_id;

	/**
	 * @var integer
	 */
	protected $permisos_id;

	/**
	 * @var integer
	 */
	protected $aplicaciones_id;

	/**
	 * @var string
	 */
	protected $controller;

	/**
	 * @var string
	 */
	protected $action;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo perfiles_id
	 * @param integer $perfiles_id
	 */
	public function setPerfilesId($perfiles_id){
		$this->perfiles_id = $perfiles_id;
	}

	/**
	 * Metodo para establecer el valor del campo permisos_id
	 * @param integer $permisos_id
	 */
	public function setPermisosId($permisos_id){
		$this->permisos_id = $permisos_id;
	}

	/**
	 * Metodo para establecer el valor del campo aplicaciones_id
	 * @param integer $aplicaciones_id
	 */
	public function setAplicacionesId($aplicaciones_id){
		$this->aplicaciones_id = $aplicaciones_id;
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
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo perfiles_id
	 * @return integer
	 */
	public function getPerfilesId(){
		return $this->perfiles_id;
	}

	/**
	 * Devuelve el valor del campo permisos_id
	 * @return integer
	 */
	public function getPermisosId(){
		return $this->permisos_id;
	}

	/**
	 * Devuelve el valor del campo aplicaciones_id
	 * @return integer
	 */
	public function getAplicacionesId(){
		return $this->aplicaciones_id;
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

}

