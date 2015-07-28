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

class Carghab extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numfol;

	/**
	 * @var integer
	 */
	protected $numcue;

	/**
	 * @var integer
	 */
	protected $numfac;

	/**
	 * @var string
	 */
	protected $tipfac;

	/**
	 * @var string
	 */
	protected $numdoc;

	/**
	 * @var string
	 */
	protected $exento;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo numfol
	 * @param integer $numfol
	 */
	public function setNumfol($numfol){
		$this->numfol = $numfol;
	}

	/**
	 * Metodo para establecer el valor del campo numcue
	 * @param integer $numcue
	 */
	public function setNumcue($numcue){
		$this->numcue = $numcue;
	}

	/**
	 * Metodo para establecer el valor del campo numfac
	 * @param integer $numfac
	 */
	public function setNumfac($numfac){
		$this->numfac = $numfac;
	}

	/**
	 * Metodo para establecer el valor del campo tipfac
	 * @param string $tipfac
	 */
	public function setTipfac($tipfac){
		$this->tipfac = $tipfac;
	}

	/**
	 * Metodo para establecer el valor del campo numdoc
	 * @param string $numdoc
	 */
	public function setNumdoc($numdoc){
		$this->numdoc = $numdoc;
	}

	/**
	 * Metodo para establecer el valor del campo exento
	 * @param string $exento
	 */
	public function setExento($exento){
		$this->exento = $exento;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo numfol
	 * @return integer
	 */
	public function getNumfol(){
		return $this->numfol;
	}

	/**
	 * Devuelve el valor del campo numcue
	 * @return integer
	 */
	public function getNumcue(){
		return $this->numcue;
	}

	/**
	 * Devuelve el valor del campo numfac
	 * @return integer
	 */
	public function getNumfac(){
		return $this->numfac;
	}

	/**
	 * Devuelve el valor del campo tipfac
	 * @return string
	 */
	public function getTipfac(){
		return $this->tipfac;
	}

	/**
	 * Devuelve el valor del campo numdoc
	 * @return string
	 */
	public function getNumdoc(){
		return $this->numdoc;
	}

	/**
	 * Devuelve el valor del campo exento
	 * @return string
	 */
	public function getExento(){
		return $this->exento;
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
		$config = CoreConfig::readFromActiveApplication('app.ini', 'ini');
		if(isset($config->pos->hotel)){
			$this->setSchema($config->pos->hotel);
		} else {
			$this->setSchema('hotel2');
		}
	}

}

