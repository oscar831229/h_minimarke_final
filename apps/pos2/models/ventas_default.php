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

class VentasDefault extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $tipo_venta_id;

	/**
	 * @var string
	 */
	protected $cedula;

	/**
	 * @var string
	 */
	protected $valor_minimo;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_venta_id
	 * @param string $tipo_venta_id
	 */
	public function setTipoVentaId($tipo_venta_id){
		$this->tipo_venta_id = $tipo_venta_id;
	}

	/**
	 * Metodo para establecer el valor del campo cedula
	 * @param string $cedula
	 */
	public function setCedula($cedula){
		$this->cedula = $cedula;
	}

	/**
	 * Metodo para establecer el valor del campo valor_minimo
	 * @param string $valor_minimo
	 */
	public function setValorMinimo($valor_minimo){
		$this->valor_minimo = $valor_minimo;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo tipo_venta_id
	 * @return string
	 */
	public function getTipoVentaId(){
		return $this->tipo_venta_id;
	}

	/**
	 * Devuelve el valor del campo cedula
	 * @return string
	 */
	public function getCedula(){
		return $this->cedula;
	}

	/**
	 * Devuelve el valor del campo valor_minimo
	 * @return string
	 */
	public function getValorMinimo(){
		return $this->valor_minimo;
	}

}

