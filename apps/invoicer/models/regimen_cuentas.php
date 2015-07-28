<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class RegimenCuentas extends RcsRecord {

	/**
	 * @var string
	 */
	protected $regimen;

	/**
	 * @var string
	 */
	protected $cta_iva10d;

	/**
	 * @var string
	 */
	protected $cta_iva16d;

	/**
	 * @var string
	 */
	protected $cta_iva10r;

	/**
	 * @var string
	 */
	protected $cta_iva16r;

	/**
	 * @var string
	 */
	protected $cta_iva10v;

	/**
	 * @var string
	 */
	protected $cta_iva16v;


	/**
	 * Metodo para establecer el valor del campo regimen
	 * @param string $regimen
	 */
	public function setRegimen($regimen){
		$this->regimen = $regimen;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva10d
	 * @param string $cta_iva10d
	 */
	public function setCtaIva10d($cta_iva10d){
		$this->cta_iva10d = $cta_iva10d;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva16d
	 * @param string $cta_iva16d
	 */
	public function setCtaIva16d($cta_iva16d){
		$this->cta_iva16d = $cta_iva16d;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva10r
	 * @param string $cta_iva10r
	 */
	public function setCtaIva10r($cta_iva10r){
		$this->cta_iva10r = $cta_iva10r;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva16r
	 * @param string $cta_iva16r
	 */
	public function setCtaIva16r($cta_iva16r){
		$this->cta_iva16r = $cta_iva16r;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva10v
	 * @param string $cta_iva10v
	 */
	public function setCtaIva10v($cta_iva10v){
		$this->cta_iva10v = $cta_iva10v;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva16v
	 * @param string $cta_iva16v
	 */
	public function setCtaIva16v($cta_iva16v){
		$this->cta_iva16v = $cta_iva16v;
	}


	/**
	 * Devuelve el valor del campo regimen
	 * @return string
	 */
	public function getRegimen(){
		return $this->regimen;
	}

	/**
	 * Devuelve el valor del campo cta_iva10d
	 * @return string
	 */
	public function getCtaIva10d(){
		return $this->cta_iva10d;
	}

	/**
	 * Devuelve el valor del campo cta_iva16d
	 * @return string
	 */
	public function getCtaIva16d(){
		return $this->cta_iva16d;
	}

	/**
	 * Devuelve el valor del campo cta_iva10r
	 * @return string
	 */
	public function getCtaIva10r(){
		return $this->cta_iva10r;
	}

	/**
	 * Devuelve el valor del campo cta_iva16r
	 * @return string
	 */
	public function getCtaIva16r(){
		return $this->cta_iva16r;
	}

	/**
	 * Devuelve el valor del campo cta_iva10v
	 * @return string
	 */
	public function getCtaIva10v(){
		return $this->cta_iva10v;
	}

	/**
	 * Devuelve el valor del campo cta_iva16v
	 * @return string
	 */
	public function getCtaIva16v(){
		return $this->cta_iva16v;
	}

	public function beforeSave(){
		if($this->cta_iva10d!=''){
			$exists = EntityManager::get('Cuentas')->count("cuenta='{$this->cta_iva10d}' AND es_auxiliar='S'");
			if($exists==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de iva descontable del 10% no existe ó no es auxiliar', 'cta_iva10d'));
				return false;
			}
		}
		if($this->cta_iva16d!=''){
			$exists = EntityManager::get('Cuentas')->count("cuenta='{$this->cta_iva16d}' AND es_auxiliar='S'");
			if($exists==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de iva descontable del 16% no existe ó no es auxiliar', 'cta_iva16d'));
				return false;
			}
		}
		if($this->cta_iva10r!=''){
			$exists = EntityManager::get('Cuentas')->count("cuenta='{$this->cta_iva10r}' AND es_auxiliar='S'");
			if($exists==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de iva retenido del 10% no existe ó no es auxiliar', 'cta_iva10d'));
				return false;
			}
		}
		if($this->cta_iva16r!=''){
			$exists = EntityManager::get('Cuentas')->count("cuenta='{$this->cta_iva16r}' AND es_auxiliar='S'");
			if($exists==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de iva retenido del 16% no existe ó no es auxiliar', 'cta_iva10d'));
				return false;
			}
		}
		if($this->cta_iva10v!=''){
			$exists = EntityManager::get('Cuentas')->count("cuenta='{$this->cta_iva10v}' AND es_auxiliar='S'");
			if($exists==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de iva de ventas del 10% no existe ó no es auxiliar', 'cta_iva10d'));
				return false;
			}
		}
		if($this->cta_iva16v!=''){
			$exists = EntityManager::get('Cuentas')->count("cuenta='{$this->cta_iva16v}' AND es_auxiliar='S'");
			if($exists==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de iva de ventas del 16% no existe ó no es auxiliar', 'cta_iva10d'));
				return false;
			}
		}
	}

	public function initialize(){

		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->back_db)){
			$this->setSchema($config->hfos->back_db);
		} else {
			$this->setSchema('ramocol');
		}

	}

}

