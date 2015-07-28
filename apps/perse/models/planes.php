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

class Planes extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codpla;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $tipper;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var integer
	 */
	protected $tippla;

	/**
	 * @var integer
	 */
	protected $tippro;

	/**
	 * @var string
	 */
	protected $muepre;

	/**
	 * @var string
	 */
	protected $muefac;

	/**
	 * @var string
	 */
	protected $observacion;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo codpla
	 * @param integer $codpla
	 */
	public function setCodpla($codpla){
		$this->codpla = $codpla;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo tipper
	 * @param string $tipper
	 */
	public function setTipper($tipper){
		$this->tipper = $tipper;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo tippla
	 * @param integer $tippla
	 */
	public function setTippla($tippla){
		$this->tippla = $tippla;
	}

	/**
	 * Metodo para establecer el valor del campo tippro
	 * @param integer $tippro
	 */
	public function setTippro($tippro){
		$this->tippro = $tippro;
	}

	/**
	 * Metodo para establecer el valor del campo muepre
	 * @param string $muepre
	 */
	public function setMuepre($muepre){
		$this->muepre = $muepre;
	}

	/**
	 * Metodo para establecer el valor del campo muefac
	 * @param string $muefac
	 */
	public function setMuefac($muefac){
		$this->muefac = $muefac;
	}

	/**
	 * Metodo para establecer el valor del campo observacion
	 * @param string $observacion
	 */
	public function setObservacion($observacion){
		$this->observacion = $observacion;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo codpla
	 * @return integer
	 */
	public function getCodpla(){
		return $this->codpla;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo tipper
	 * @return string
	 */
	public function getTipper(){
		return $this->tipper;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo tippla
	 * @return integer
	 */
	public function getTippla(){
		return $this->tippla;
	}

	/**
	 * Devuelve el valor del campo tippro
	 * @return integer
	 */
	public function getTippro(){
		return $this->tippro;
	}

	/**
	 * Devuelve el valor del campo muepre
	 * @return string
	 */
	public function getMuepre(){
		return $this->muepre;
	}

	/**
	 * Devuelve el valor del campo muefac
	 * @return string
	 */
	public function getMuefac(){
		return $this->muefac;
	}

	/**
	 * Devuelve el valor del campo observacion
	 * @return string
	 */
	public function getObservacion(){
		return $this->observacion;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

