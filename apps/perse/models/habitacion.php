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

class Habitacion extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $numhab;

	/**
	 * @var integer
	 */
	protected $codcla;

	/**
	 * @var string
	 */
	protected $area;

	/**
	 * @var integer
	 */
	protected $piso;

	/**
	 * @var integer
	 */
	protected $numcam;

	/**
	 * @var string
	 */
	protected $fumador;

	/**
	 * @var string
	 */
	protected $observacion;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $extension;

	/**
	 * @var integer
	 */
	protected $codest;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo numhab
	 * @param string $numhab
	 */
	public function setNumhab($numhab){
		$this->numhab = $numhab;
	}

	/**
	 * Metodo para establecer el valor del campo codcla
	 * @param integer $codcla
	 */
	public function setCodcla($codcla){
		$this->codcla = $codcla;
	}

	/**
	 * Metodo para establecer el valor del campo area
	 * @param string $area
	 */
	public function setArea($area){
		$this->area = $area;
	}

	/**
	 * Metodo para establecer el valor del campo piso
	 * @param integer $piso
	 */
	public function setPiso($piso){
		$this->piso = $piso;
	}

	/**
	 * Metodo para establecer el valor del campo numcam
	 * @param integer $numcam
	 */
	public function setNumcam($numcam){
		$this->numcam = $numcam;
	}

	/**
	 * Metodo para establecer el valor del campo fumador
	 * @param string $fumador
	 */
	public function setFumador($fumador){
		$this->fumador = $fumador;
	}

	/**
	 * Metodo para establecer el valor del campo observacion
	 * @param string $observacion
	 */
	public function setObservacion($observacion){
		$this->observacion = $observacion;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo extension
	 * @param string $extension
	 */
	public function setExtension($extension){
		$this->extension = $extension;
	}

	/**
	 * Metodo para establecer el valor del campo codest
	 * @param integer $codest
	 */
	public function setCodest($codest){
		$this->codest = $codest;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo numhab
	 * @return string
	 */
	public function getNumhab(){
		return $this->numhab;
	}

	/**
	 * Devuelve el valor del campo codcla
	 * @return integer
	 */
	public function getCodcla(){
		return $this->codcla;
	}

	/**
	 * Devuelve el valor del campo area
	 * @return string
	 */
	public function getArea(){
		return $this->area;
	}

	/**
	 * Devuelve el valor del campo piso
	 * @return integer
	 */
	public function getPiso(){
		return $this->piso;
	}

	/**
	 * Devuelve el valor del campo numcam
	 * @return integer
	 */
	public function getNumcam(){
		return $this->numcam;
	}

	/**
	 * Devuelve el valor del campo fumador
	 * @return string
	 */
	public function getFumador(){
		return $this->fumador;
	}

	/**
	 * Devuelve el valor del campo observacion
	 * @return string
	 */
	public function getObservacion(){
		return $this->observacion;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo extension
	 * @return string
	 */
	public function getExtension(){
		return $this->extension;
	}

	/**
	 * Devuelve el valor del campo codest
	 * @return integer
	 */
	public function getCodest(){
		return $this->codest;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function initialize(){
		$this->belongsTo('codcla', 'clahab');
	}

}

