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

class Documentos extends RcsRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_documen;

	/**
	 * @var string
	 */
	protected $cartera;



	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_documen
	 * @param string $nom_documen
	 */
	public function setNomDocumen($nom_documen){
		$this->nom_documen = $nom_documen;
	}

	/**
	 * Metodo para establecer el valor del campo cartera
	 * @param string $cartera
	 */
	public function setCartera($cartera){
		$this->cartera = $cartera;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_documen
	 * @return string
	 */
	public function getNomDocumen(){
		return $this->nom_documen;
	}

	/**
	 * Devuelve el valor del campo cartera
	 * @return string
	 */
	public function getCartera(){
		return $this->cartera;
	}

	public function beforeDelete(){
		if($this->countMovi()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el documento porque tiene movimiento asociado', 'codigo'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('codigo', 'Movi', 'tipo_doc');
	}

}

