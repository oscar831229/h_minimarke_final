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

class Concue extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numfol;

	/**
	 * @var integer
	 */
	protected $codcar;

	/**
	 * @var integer
	 */
	protected $numcue;


	/**
	 * Metodo para establecer el valor del campo numfol
	 * @param integer $numfol
	 */
	public function setNumfol($numfol){
		$this->numfol = $numfol;
	}

	/**
	 * Metodo para establecer el valor del campo codcar
	 * @param integer $codcar
	 */
	public function setCodcar($codcar){
		$this->codcar = $codcar;
	}

	/**
	 * Metodo para establecer el valor del campo numcue
	 * @param integer $numcue
	 */
	public function setNumcue($numcue){
		$this->numcue = $numcue;
	}


	/**
	 * Devuelve el valor del campo numfol
	 * @return integer
	 */
	public function getNumfol(){
		return $this->numfol;
	}

	/**
	 * Devuelve el valor del campo codcar
	 * @return integer
	 */
	public function getCodcar(){
		return $this->codcar;
	}

	/**
	 * Devuelve el valor del campo numcue
	 * @return integer
	 */
	public function getNumcue(){
		return $this->numcue;
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

