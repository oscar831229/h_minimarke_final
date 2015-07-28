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

class SalonTipoVenta extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $salon_id;

	/**
	 * @var string
	 */
	protected $tipo_venta_id;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo salon_id
	 * @param integer $salon_id
	 */
	public function setSalonId($salon_id){
		$this->salon_id = $salon_id;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_venta_id
	 * @param string $tipo_venta_id
	 */
	public function setTipoVentaId($tipo_venta_id){
		$this->tipo_venta_id = $tipo_venta_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo salon_id
	 * @return integer
	 */
	public function getSalonId(){
		return $this->salon_id;
	}

	/**
	 * Devuelve el valor del campo tipo_venta_id
	 * @return string
	 */
	public function getTipoVentaId(){
		return $this->tipo_venta_id;
	}

	public function initialize(){
		$this->belongsTo('TipoVenta');
	}

}

