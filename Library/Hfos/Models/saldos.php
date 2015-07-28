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

	/**
	 * @var string
	 */
	protected $item;

	/**
	 * @var string
	 */
	protected $almacen;

	/**
	 * @var string
	 */
	protected $ano_mes;

	/**
	 * @var string
	 */
	protected $saldo;

	/**
	 * @var string
	 */
	protected $costo;

	/**
	 * @var string
	 */
	protected $consumo;

	/**
	 * @var string
	 */
	protected $v_ventas;

	/**
	 * @var string
	 */
	protected $v_consumo;

	/**
	 * @var string
	 */
	protected $fisico;

	/**
	 * @var string
	 */
	protected $ubicacion;

	/**
	 * @var Date
	 */
	protected $f_u_mov;


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
	 * Metodo para establecer el valor del campo consumo
	 * @param string $consumo
	 */
	public function setConsumo($consumo){
		$this->consumo = $consumo;
	}

	/**
	 * Metodo para establecer el valor del campo v_ventas
	 * @param string $v_ventas
	 */
	public function setVVentas($v_ventas){
		$this->v_ventas = $v_ventas;
	}

	/**
	 * Metodo para establecer el valor del campo v_consumo
	 * @param string $v_consumo
	 */
	public function setVConsumo($v_consumo){
		$this->v_consumo = $v_consumo;
	}

	/**
	 * Metodo para establecer el valor del campo fisico
	 * @param string $fisico
	 */
	public function setFisico($fisico){
		$this->fisico = $fisico;
	}

	/**
	 * Metodo para establecer el valor del campo ubicacion
	 * @param string $ubicacion
	 */
	public function setUbicacion($ubicacion){
		$this->ubicacion = $ubicacion;
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
	 * Devuelve el valor del campo consumo
	 * @return string
	 */
	public function getConsumo(){
		return $this->consumo;
	}

	/**
	 * Devuelve el valor del campo v_ventas
	 * @return string
	 */
	public function getVVentas(){
		return $this->v_ventas;
	}

	/**
	 * Devuelve el valor del campo v_consumo
	 * @return string
	 */
	public function getVConsumo(){
		return $this->v_consumo;
	}

	/**
	 * Devuelve el valor del campo fisico
	 * @return string
	 */
	public function getFisico(){
		return $this->fisico;
	}

	/**
	 * Devuelve el valor del campo ubicacion
	 * @return string
	 */
	public function getUbicacion(){
		return $this->ubicacion;
	}

	/**
	 * Devuelve el valor del campo f_u_mov
	 * @return Date
	 */
	public function getFUMov(){
		return new Date($this->f_u_mov);
	}

}

