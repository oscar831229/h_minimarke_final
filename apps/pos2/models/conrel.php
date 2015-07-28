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

class Conrel extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codrel;

	/**
	 * @var integer
	 */
	protected $codcar;

	/**
	 * @var integer
	 */
	protected $condes;

	/**
	 * @var integer
	 */
	protected $conexe;


	/**
	 * Metodo para establecer el valor del campo codrel
	 * @param integer $codrel
	 */
	public function setCodrel($codrel){
		$this->codrel = $codrel;
	}

	/**
	 * Metodo para establecer el valor del campo codcar
	 * @param integer $codcar
	 */
	public function setCodcar($codcar){
		$this->codcar = $codcar;
	}

	/**
	 * Metodo para establecer el valor del campo condes
	 * @param integer $condes
	 */
	public function setCondes($condes){
		$this->condes = $condes;
	}

	/**
	 * Metodo para establecer el valor del campo conexe
	 * @param integer $conexe
	 */
	public function setConexe($conexe){
		$this->conexe = $conexe;
	}


	/**
	 * Devuelve el valor del campo codrel
	 * @return integer
	 */
	public function getCodrel(){
		return $this->codrel;
	}

	/**
	 * Devuelve el valor del campo codcar
	 * @return integer
	 */
	public function getCodcar(){
		return $this->codcar;
	}

	/**
	 * Devuelve el valor del campo condes
	 * @return integer
	 */
	public function getCondes(){
		return $this->condes;
	}

	/**
	 * Devuelve el valor del campo conexe
	 * @return integer
	 */
	public function getConexe(){
		return $this->conexe;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$config = CoreConfig::readFromActiveApplication('app.ini', 'ini');
		if(isset($config->pos->hotel)){
			$this->setSchema($config->pos->hotel);
		} else {
			$this->setSchema('hotel2');
		}
	}

}

