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

class Motaju extends RcsRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_centro
	 * @param string $nom_centro
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	public function getNombre(){
		return $this->nombre;
	}

	public function beforeDelete(){
		if($this->countMovihead()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el motivo de ajuste porque tiene movimiento en Inventarios', 'codigo'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('codigo', 'Movihead', 'motaju');
	}

}

