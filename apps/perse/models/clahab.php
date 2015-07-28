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

class Clahab extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codcla;

	/**
	 * @var string
	 */
	protected $clase;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var integer
	 */
	protected $numper;

	/**
	 * @var integer
	 */
	protected $tiphab;


	/**
	 * Metodo para establecer el valor del campo codcla
	 * @param integer $codcla
	 */
	public function setCodcla($codcla){
		$this->codcla = $codcla;
	}

	/**
	 * Metodo para establecer el valor del campo clase
	 * @param string $clase
	 */
	public function setClase($clase){
		$this->clase = $clase;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo numper
	 * @param integer $numper
	 */
	public function setNumper($numper){
		$this->numper = $numper;
	}

	/**
	 * Metodo para establecer el valor del campo tiphab
	 * @param integer $tiphab
	 */
	public function setTiphab($tiphab){
		$this->tiphab = $tiphab;
	}


	/**
	 * Devuelve el valor del campo codcla
	 * @return integer
	 */
	public function getCodcla(){
		return $this->codcla;
	}

	/**
	 * Devuelve el valor del campo clase
	 * @return string
	 */
	public function getClase(){
		return $this->clase;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo numper
	 * @return integer
	 */
	public function getNumper(){
		return $this->numper;
	}

	/**
	 * Devuelve el valor del campo tiphab
	 * @return integer
	 */
	public function getTiphab(){
		return $this->tiphab;
	}

}

