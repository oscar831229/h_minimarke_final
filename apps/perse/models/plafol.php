<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Persé
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Plafol extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numfol;

	/**
	 * @var integer
	 */
	protected $numpla;

	/**
	 * @var integer
	 */
	protected $codpla;

	/**
	 * @var Date
	 */
	protected $fecini;

	/**
	 * @var Date
	 */
	protected $fecfin;


	/**
	 * Metodo para establecer el valor del campo numfol
	 * @param integer $numfol
	 */
	public function setNumfol($numfol){
		$this->numfol = $numfol;
	}

	/**
	 * Metodo para establecer el valor del campo numpla
	 * @param integer $numpla
	 */
	public function setNumpla($numpla){
		$this->numpla = $numpla;
	}

	/**
	 * Metodo para establecer el valor del campo codpla
	 * @param integer $codpla
	 */
	public function setCodpla($codpla){
		$this->codpla = $codpla;
	}

	/**
	 * Metodo para establecer el valor del campo fecini
	 * @param Date $fecini
	 */
	public function setFecini($fecini){
		$this->fecini = $fecini;
	}

	/**
	 * Metodo para establecer el valor del campo fecfin
	 * @param Date $fecfin
	 */
	public function setFecfin($fecfin){
		$this->fecfin = $fecfin;
	}


	/**
	 * Devuelve el valor del campo numfol
	 * @return integer
	 */
	public function getNumfol(){
		return $this->numfol;
	}

	/**
	 * Devuelve el valor del campo numpla
	 * @return integer
	 */
	public function getNumpla(){
		return $this->numpla;
	}

	/**
	 * Devuelve el valor del campo codpla
	 * @return integer
	 */
	public function getCodpla(){
		return $this->codpla;
	}

	/**
	 * Devuelve el valor del campo fecini
	 * @return Date
	 */
	public function getFecini(){
		return new Date($this->fecini);
	}

	/**
	 * Devuelve el valor del campo fecfin
	 * @return Date
	 */
	public function getFecfin(){
		return new Date($this->fecfin);
	}

	public function initialize(){
		$this->belongsTo('codpla', 'Planes');
	}

}

