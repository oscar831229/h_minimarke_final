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

class Empresa1 extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var Date
	 */
	protected $f_cierref;

	/**
	 * @var Date
	 */
	protected $f_cierrep;

	/**
	 * @var string
	 */
	protected $ano_c;

	/**
	 * @var string
	 */
	protected $base_ret;

	/**
	 * @var string
	 */
	protected $otros;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo f_cierref
	 * @param Date $f_cierref
	 */
	public function setFCierref($f_cierref){
		$this->f_cierref = $f_cierref;
	}

	/**
	 * Metodo para establecer el valor del campo f_cierrep
	 * @param Date $f_cierrep
	 */
	public function setFCierrep($f_cierrep){
		$this->f_cierrep = $f_cierrep;
	}

	/**
	 * Metodo para establecer el valor del campo ano_c
	 * @param string $ano_c
	 */
	public function setAnoC($ano_c){
		$this->ano_c = $ano_c;
	}

	/**
	 * Metodo para establecer el valor del campo base_ret
	 * @param string $base_ret
	 */
	public function setBaseRet($base_ret){
		$this->base_ret = $base_ret;
	}

	/**
	 * Metodo para establecer el valor del campo otros
	 * @param string $otros
	 */
	public function setOtros($otros){
		$this->otros = $otros;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo f_cierref
	 * @return Date
	 */
	public function getFCierref(){
		return new Date($this->f_cierref);
	}

	/**
	 * Devuelve el valor del campo f_cierrep
	 * @return Date
	 */
	public function getFCierrep(){
		return new Date($this->f_cierrep);
	}

	/**
	 * Devuelve el valor del campo ano_c
	 * @return string
	 */
	public function getAnoC(){
		return $this->ano_c;
	}

	/**
	 * Devuelve el valor del campo base_ret
	 * @return string
	 */
	public function getBaseRet(){
		return $this->base_ret;
	}

	/**
	 * Devuelve el valor del campo otros
	 * @return string
	 */
	public function getOtros(){
		return $this->otros;
	}

	public function initialize(){
		$config = CoreConfig::readFromActiveApplication('app.ini', 'ini');
		if(isset($config->pos->ramocol)){
			$this->setSchema($config->pos->ramocol);
		} else {
			$this->setSchema('ramocol');
		}
	}

}

