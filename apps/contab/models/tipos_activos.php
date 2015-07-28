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

class TiposActivos extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nombre;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param integer $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return integer
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	public function beforeDelete(){
		if($this->countActivos()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el tipo de activos porque se está usando en activos fijos', 'cuenta'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('codigo', 'Activos', 'tipos_activos_id');
	}

}

