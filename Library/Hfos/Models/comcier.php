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

class Comcier extends RcsRecord {

	/**
	 * @var string
	 */
	protected $cuentai;

	/**
	 * @var string
	 */
	protected $cuentaf;

	/**
	 * @var string
	 */
	protected $cuenta3;

	/**
	 * @var string
	 */
	protected $nit;


	/**
	 * Metodo para establecer el valor del campo cuentai
	 * @param string $cuentai
	 */
	public function setCuentai($cuentai){
		$this->cuentai = $cuentai;
	}

	/**
	 * Metodo para establecer el valor del campo cuentaf
	 * @param string $cuentaf
	 */
	public function setCuentaf($cuentaf){
		$this->cuentaf = $cuentaf;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta3
	 * @param string $cuenta3
	 */
	public function setCuenta3($cuenta3){
		$this->cuenta3 = $cuenta3;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}


	/**
	 * Devuelve el valor del campo cuentai
	 * @return string
	 */
	public function getCuentai(){
		return $this->cuentai;
	}

	/**
	 * Devuelve el valor del campo cuentaf
	 * @return string
	 */
	public function getCuentaf(){
		return $this->cuentaf;
	}

	/**
	 * Devuelve el valor del campo cuenta3
	 * @return string
	 */
	public function getCuenta3(){
		return $this->cuenta3;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	public function beforeSave(){
		if($this->cuentai!=''){
			$cuenta = BackCacher::getCuenta($this->cuentai);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta inicial no existe', 'cuentai'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta inicial no existe', 'cuentai'));
					return false;
				}
			}
		}
		if($this->cuentaf!=''){
			$cuenta = BackCacher::getCuenta($this->cuentaf);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta final no existe', 'cuentaf'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta final no existe', 'cuentaf'));
					return false;
				}
			}
		}
	}

	public function initialize(){
		$this->addForeignKey('nit', 'Nits', 'nit', array(
			'message' => 'El tercero indicado no es válido'
		));
	}

}

