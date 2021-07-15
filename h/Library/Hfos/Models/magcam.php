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

class Magcam extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codfor;

	/**
	 * @var string
	 */
	protected $campo;

	/**
	 * @var integer
	 */
	protected $posicion;


	/**
	 * Metodo para establecer el valor del campo codfor
	 * @param integer $codfor
	 */
	public function setCodfor($codfor){
		$this->codfor = $codfor;
	}

	/**
	 * Metodo para establecer el valor del campo campo
	 * @param string $campo
	 */
	public function setCampo($campo){
		$this->campo = $campo;
	}

	/**
	 * Metodo para establecer el valor del campo posicion
	 * @param integer $posicion
	 */
	public function setPosicion($posicion){
		$this->posicion = $posicion;
	}


	/**
	 * Devuelve el valor del campo codfor
	 * @return integer
	 */
	public function getCodfor(){
		return $this->codfor;
	}

	/**
	 * Devuelve el valor del campo campo
	 * @return string
	 */
	public function getCampo(){
		return $this->campo;
	}

	/**
	 * Devuelve el valor del campo posicion
	 * @return integer
	 */
	public function getPosicion(){
		return $this->posicion;
	}

}

