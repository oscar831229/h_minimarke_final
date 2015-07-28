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

class PerfilesUsuarios extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $usuarios_id;

	/**
	 * @var integer
	 */
	protected $perfiles_id;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo usuarios_id
	 * @param integer $usuarios_id
	 */
	public function setUsuariosId($usuarios_id){
		$this->usuarios_id = $usuarios_id;
	}

	/**
	 * Metodo para establecer el valor del campo perfiles_id
	 * @param integer $perfiles_id
	 */
	public function setPerfilesId($perfiles_id){
		$this->perfiles_id = $perfiles_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo usuarios_id
	 * @return integer
	 */
	public function getUsuariosId(){
		return $this->usuarios_id;
	}

	/**
	 * Devuelve el valor del campo perfiles_id
	 * @return integer
	 */
	public function getPerfilesId(){
		return $this->perfiles_id;
	}

	public function initialize(){
		$this->belongsTo('Perfiles');
	}

}

