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

class Configuration extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $application;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $value;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo application
	 * @param string $application
	 */
	public function setApplication($application){
		$this->application = $application;
	}

	/**
	 * Metodo para establecer el valor del campo name
	 * @param string $name
	 */
	public function setName($name){
		$this->name = $name;
	}

	/**
	 * Metodo para establecer el valor del campo value
	 * @param string $value
	 */
	public function setValue($value){
		$this->value = $value;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo application
	 * @return string
	 */
	public function getApplication(){
		return $this->application;
	}

	/**
	 * Devuelve el valor del campo name
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Devuelve el valor del campo value
	 * @return string
	 */
	public function getValue(){
		return $this->value;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

