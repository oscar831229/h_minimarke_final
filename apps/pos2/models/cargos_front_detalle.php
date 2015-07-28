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

class CargosFrontDetalle extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $cargos_front_id;

	/**
	 * @var integer
	 */
	protected $account_id;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo cargos_front_id
	 * @param integer $cargos_front_id
	 */
	public function setCargosFrontId($cargos_front_id){
		$this->cargos_front_id = $cargos_front_id;
	}

	/**
	 * Metodo para establecer el valor del campo account_id
	 * @param integer $account_id
	 */
	public function setAccountId($account_id){
		$this->account_id = $account_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo cargos_front_id
	 * @return integer
	 */
	public function getCargosFrontId(){
		return $this->cargos_front_id;
	}

	/**
	 * Devuelve el valor del campo account_id
	 * @return integer
	 */
	public function getAccountId(){
		return $this->account_id;
	}

}

