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

class Saldos extends ActiveRecord {

	/*protected $item;
	protected $almacen;
	protected $ano_mes;
	protected $saldo;
	protected $costo;
	protected $f_u_mov;*/

	/**
	 * Metodo para establecer el valor del campo item
	 * @param string $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo almacen
	 * @param string $almacen
	 */
	public function setAlmacen($almacen){
		$this->almacen = $almacen;
	}

	/**
	 * Metodo para establecer el valor del campo ano_mes
	 * @param string $ano_mes
	 */
	public function setAnoMes($ano_mes){
		$this->ano_mes = $ano_mes;
	}

	/**
	 * Metodo para establecer el valor del campo saldo
	 * @param string $saldo
	 */
	public function setSaldo($saldo){
		$this->saldo = $saldo;
	}

	/**
	 * Metodo para establecer el valor del campo costo
	 * @param string $costo
	 */
	public function setCosto($costo){
		$this->costo = $costo;
	}

	/**
	 * Metodo para establecer el valor del campo f_u_mov
	 * @param Date $f_u_mov
	 */
	public function setFUMov($f_u_mov){
		$this->f_u_mov = $f_u_mov;
	}


	/**
	 * Devuelve el valor del campo item
	 * @return string
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo almacen
	 * @return string
	 */
	public function getAlmacen(){
		return $this->almacen;
	}

	/**
	 * Devuelve el valor del campo ano_mes
	 * @return string
	 */
	public function getAnoMes(){
		return $this->ano_mes;
	}

	/**
	 * Devuelve el valor del campo saldo
	 * @return string
	 */
	public function getSaldo(){
		return $this->saldo;
	}

	/**
	 * Devuelve el valor del campo costo
	 * @return string
	 */
	public function getCosto(){
		return $this->costo;
	}

	/**
	 * Devuelve el valor del campo f_u_mov
	 *
	 * @return date
	 */
	public function getFUMov(){
		if($this->f_u_mov == ''){
			return '';
		}
		return new Date($this->f_u_mov);
	}

	/**
	 * Metodo Inicializador
	 */
	public function initialize(){
		$config = CoreConfig::readFromActiveApplication('app.ini', 'ini');
		if(isset($config->pos->ramocol)){
			$this->setSchema($config->pos->ramocol);
		} else {
			$this->setSchema('ramocol');
		}
	}

}

