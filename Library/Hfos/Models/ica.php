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

class Ica extends RcsRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $otros;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo otros
	 * @param string $otros
	 */
	public function setOtros($otros){
		$this->otros = $otros;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo otros
	 * @return string
	 */
	public function getOtros(){
		return $this->otros;
	}

	public function beforeSave(){
		if($this->cuenta!=''){
			$cuenta = BackCacher::getCuenta($this->cuenta);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta asociada no existe', 'cuenta'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta asociada no es auxiliar', 'cuenta'));
					return false;
				}
			}
		} else {
			$this->appendMessage(new ActiveRecordMessage('La cuenta asociada no existe', 'cuenta'));
			return false;
		}
	}

}

