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

class Ubicacion extends RcsRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_ubica;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_ubica
	 * @param string $nom_ubica
	 */
	public function setNomUbica($nom_ubica){
		$this->nom_ubica = $nom_ubica;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_ubica
	 * @return string
	 */
	public function getNomUbica(){
		return $this->nom_ubica;
	}

	public function beforeDelete(){
		if($this->countActivos()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la ubicación porque se está usando en activos fijos', 'cuenta'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('codigo', 'Activos', 'ubicacion');
	}

}

