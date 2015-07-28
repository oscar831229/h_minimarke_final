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

class Centros extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_centro;

	/**
	 * @var string
	 */
	protected $responsable;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_centro
	 * @param string $nom_centro
	 */
	public function setNomCentro($nom_centro){
		$this->nom_centro = $nom_centro;
	}

	/**
	 * Metodo para establecer el valor del campo responsable
	 * @param string $responsable
	 */
	public function setResponsable($responsable){
		$this->responsable = $responsable;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_centro
	 * @return string
	 */
	public function getNomCentro(){
		return $this->nom_centro;
	}

	/**
	 * Devuelve el valor del campo responsable
	 * @return string
	 */
	public function getResponsable(){
		return $this->responsable;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

