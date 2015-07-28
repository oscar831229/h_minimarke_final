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

class Lineaser extends RcsRecord {

	/**
	 * @var string
	 */
	protected $linea;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $cta_gasto;

	/**
	 * @var string
	 */
	protected $cta_iva;

	/**
	 * @var string
	 */
	protected $porc_iva;

	/**
	 * @var string
	 */
	protected $cta_retiva;

	/**
	 * @var string
	 */
	protected $cta_retencion;

	/**
	 * @var string
	 */
	protected $cta_cartera;

	/**
	 * @var string
	 */
	protected $cta_ex1;

	/**
	 * @var string
	 */
	protected $cta_ex2;


	/**
	 * Metodo para establecer el valor del campo linea
	 * @param string $linea
	 */
	public function setLinea($linea){
		$this->linea = $linea;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo cta_gasto
	 * @param string $cta_gasto
	 */
	public function setCtaGasto($cta_gasto){
		$this->cta_gasto = $cta_gasto;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva
	 * @param string $cta_iva
	 */
	public function setCtaIva($cta_iva){
		$this->cta_iva = $cta_iva;
	}

	/**
	 * Metodo para establecer el valor del campo porc_iva
	 * @param string $porc_iva
	 */
	public function setPorcIva($porc_iva){
		$this->porc_iva = $porc_iva;
	}

	/**
	 * Metodo para establecer el valor del campo cta_retiva
	 * @param string $cta_retiva
	 */
	public function setCtaRetiva($cta_retiva){
		$this->cta_retiva = $cta_retiva;
	}

	/**
	 * Metodo para establecer el valor del campo cta_retencion
	 * @param string $cta_retencion
	 */
	public function setCtaRetencion($cta_retencion){
		$this->cta_retencion = $cta_retencion;
	}

	/**
	 * Metodo para establecer el valor del campo cta_cartera
	 * @param string $cta_cartera
	 */
	public function setCtaCartera($cta_cartera){
		$this->cta_cartera = $cta_cartera;
	}

	/**
	 * Metodo para establecer el valor del campo cta_ex1
	 * @param string $cta_ex1
	 */
	public function setCtaEx1($cta_ex1){
		$this->cta_ex1 = $cta_ex1;
	}

	/**
	 * Metodo para establecer el valor del campo cta_ex2
	 * @param string $cta_ex2
	 */
	public function setCtaEx2($cta_ex2){
		$this->cta_ex2 = $cta_ex2;
	}


	/**
	 * Devuelve el valor del campo linea
	 * @return string
	 */
	public function getLinea(){
		return $this->linea;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo cta_gasto
	 * @return string
	 */
	public function getCtaGasto(){
		return $this->cta_gasto;
	}

	/**
	 * Devuelve el valor del campo cta_iva
	 * @return string
	 */
	public function getCtaIva(){
		return $this->cta_iva;
	}

	/**
	 * Devuelve el valor del campo porc_iva
	 * @return string
	 */
	public function getPorcIva(){
		return $this->porc_iva;
	}

	/**
	 * Devuelve el valor del campo cta_retiva
	 * @return string
	 */
	public function getCtaRetiva(){
		return $this->cta_retiva;
	}

	/**
	 * Devuelve el valor del campo cta_retencion
	 * @return string
	 */
	public function getCtaRetencion(){
		return $this->cta_retencion;
	}

	/**
	 * Devuelve el valor del campo cta_cartera
	 * @return string
	 */
	public function getCtaCartera(){
		return $this->cta_cartera;
	}

	/**
	 * Devuelve el valor del campo cta_ex1
	 * @return string
	 */
	public function getCtaEx1(){
		return $this->cta_ex1;
	}

	/**
	 * Devuelve el valor del campo cta_ex2
	 * @return string
	 */
	public function getCtaEx2(){
		return $this->cta_ex2;
	}

	public function beforeSave(){
		if($this->cta_gasto!=''){
			$cuenta = BackCacher::getCuenta($this->cta_gasto);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de gasto no existe', 'cta_gasto'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta de gasto no es auxiliar', 'cta_gasto'));
					return false;
				}
			}
		}
		if($this->cta_retiva!=''){
			$cuenta = BackCacher::getCuenta($this->cta_retiva);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de retención de IVA no existe', 'cta_retiva'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta de retención de IVA no es auxiliar', 'cta_retiva'));
					return false;
				}
			}
		}
		if($this->cta_retencion!=''){
			$cuenta = BackCacher::getCuenta($this->cta_retencion);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de retención no existe', 'cta_retencion'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta de retención no es auxiliar', 'cta_retencion'));
					return false;
				}
			}
		}
		if($this->cta_cartera!=''){
			$cuenta = BackCacher::getCuenta($this->cta_cartera);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta contable para cuentas por pagar no existe', 'cta_cartera'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta contable para cuentas por pagar no es auxiliar', 'cta_cartera'));
					return false;
				}
			}
		}
		if($this->cta_iva!=''){
			$cuenta = BackCacher::getCuenta($this->cta_iva);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta contable para IVA otros regímenes no existe', 'cta_iva'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta contable para para IVA otros regímenes no es auxiliar', 'cta_iva'));
					return false;
				}
			}
		}
		if($this->cta_ex1!=''){
			$cuenta = BackCacher::getCuenta($this->cta_ex1);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de IVA regímen simplificado no existe', 'cta_ex1'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta cde IVA regímen simplificado no es auxiliar', 'cta_ex1'));
					return false;
				}
			}
		}
	}

	public function beforeDelete(){
		if($this->countRefe()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el línea de servicio porque tiene items asociados', 'linea'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('linea', 'Refe', 'linea');
	}

}

