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

class Grupos extends RcsRecord {

	/**
	 * @var string
	 */
	protected $linea;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $es_auxiliar;

	/**
	 * @var string
	 */
	protected $cta_compra;

	/**
	 * @var string
	 */
	protected $cta_inve;

	/**
	 * @var string
	 */
	protected $cta_ret_compra;

	/**
	 * @var string
	 */
	protected $porc_compra;

	/**
	 * @var string
	 */
	protected $minimo_ret;

	/**
	 * @var string
	 */
	protected $cta_dev_ventas;

	/**
	 * @var string
	 */
	protected $cta_dev_compras;


	/**
	 * Metodo para establecer el valor del campo linea
	 * @param string $linea
	 */
	public function setLinea($linea){
		$this->linea = $linea;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo es_auxiliar
	 * @param string $es_auxiliar
	 */
	public function setEsAuxiliar($es_auxiliar){
		$this->es_auxiliar = $es_auxiliar;
	}

	/**
	 * Metodo para establecer el valor del campo cta_compra
	 * @param string $cta_compra
	 */
	public function setCtaCompra($cta_compra){
		$this->cta_compra = $cta_compra;
	}

	/**
	 * Metodo para establecer el valor del campo cta_inve
	 * @param string $cta_inve
	 */
	public function setCtaInve($cta_inve){
		$this->cta_inve = $cta_inve;
	}

	/**
	 * Metodo para establecer el valor del campo cta_ret_compra
	 * @param string $cta_ret_compra
	 */
	public function setCtaRetCompra($cta_ret_compra){
		$this->cta_ret_compra = $cta_ret_compra;
	}

	/**
	 * Metodo para establecer el valor del campo porc_compra
	 * @param string $porc_compra
	 */
	public function setPorcCompra($porc_compra){
		$this->porc_compra = $porc_compra;
	}

	/**
	 * Metodo para establecer el valor del campo minimo_ret
	 * @param string $minimo_ret
	 */
	public function setMinimoRet($minimo_ret){
		$this->minimo_ret = $minimo_ret;
	}

	/**
	 * Metodo para establecer el valor del campo cta_dev_ventas
	 * @param string $cta_dev_ventas
	 */
	public function setCtaDevVentas($cta_dev_ventas){
		$this->cta_dev_ventas = $cta_dev_ventas;
	}

	/**
	 * Metodo para establecer el valor del campo cta_dev_compras
	 * @param string $cta_dev_compras
	 */
	public function setCtaDevCompras($cta_dev_compras){
		$this->cta_dev_compras = $cta_dev_compras;
	}


	/**
	 * Devuelve el valor del campo linea
	 * @return string
	 */
	public function getLinea(){
		return $this->linea;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo es_auxiliar
	 * @return string
	 */
	public function getEsAuxiliar(){
		return $this->es_auxiliar;
	}

	/**
	 * Devuelve el valor del campo cta_compra
	 * @return string
	 */
	public function getCtaCompra(){
		return $this->cta_compra;
	}

	/**
	 * Devuelve el valor del campo cta_inve
	 * @return string
	 */
	public function getCtaInve(){
		return $this->cta_inve;
	}

	/**
	 * Devuelve el valor del campo cta_ret_compra
	 * @return string
	 */
	public function getCtaRetCompra(){
		return $this->cta_ret_compra;
	}

	/**
	 * Devuelve el valor del campo porc_compra
	 * @return string
	 */
	public function getPorcCompra(){
		return $this->porc_compra;
	}

	/**
	 * Devuelve el valor del campo minimo_ret
	 * @return string
	 */
	public function getMinimoRet(){
		return $this->minimo_ret;
	}

	/**
	 * Devuelve el valor del campo cta_dev_ventas
	 * @return string
	 */
	public function getCtaDevVentas(){
		return $this->cta_dev_ventas;
	}

	/**
	 * Devuelve el valor del campo cta_dev_compras
	 * @return string
	 */
	public function getCtaDevCompras(){
		return $this->cta_dev_compras;
	}

	public function beforeSave(){

		if($this->es_auxiliar=='N'){
			if($this->countActivos()>0){
				$this->appendMessage(new ActiveRecordMessage('No se puede establecer el grupo como no-auxiliar porque ya tiene activos asignados', 'es_auxiliar'));
				return false;
			}
		}

		if($this->cta_compra!=''){
			$cuenta = BackCacher::getCuenta($this->cta_compra);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de compra de activos no existe', 'cta_compra'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta de compra de activos no es auxiliar', 'cta_compra'));
					return false;
				}
			}
		}
		if($this->cta_inve!=''){
			$cuenta = BackCacher::getCuenta($this->cta_inve);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de ajustes por inflación no existe', 'cta_inve'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta de ajustes por inflación no es auxiliar', 'cta_inve'));
					return false;
				}
			}
		}
		if($this->cta_dev_ventas!=''){
			$cuenta = BackCacher::getCuenta($this->cta_dev_ventas);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de depreciación de activos (débito) no existe', 'cta_dev_ventas'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta de depreciación de activos (débito) no es auxiliar', 'cta_dev_ventas'));
					return false;
				}
			}
		}
		if($this->cta_dev_compras!=''){
			$cuenta = BackCacher::getCuenta($this->cta_dev_compras);
			if($cuenta==false){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de depreciación de activos (crébito) no existe', 'cta_dev_compras'));
				return false;
			} else {
				if($cuenta->getEsAuxiliar()!='S'){
					$this->appendMessage(new ActiveRecordMessage('La cuenta de depreciación de activos (crébito) no es auxiliar', 'cta_dev_compras'));
					return false;
				}
			}
		}
	}

	public function beforeDelete(){
		if($this->countActivos()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el grupo porque tiene activos fijos asociados', 'codigo'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('grupo', 'Activos', 'linea');
	}

}

