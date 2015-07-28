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

class Magcue extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $codfor;

	/**
	 * @var integer
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $campo;

	/**
	 * @var string
	 */
	protected $cueini;

	/**
	 * @var string
	 */
	protected $cuefin;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo codfor
	 * @param integer $codfor
	 */
	public function setCodfor($codfor){
		$this->codfor = $codfor;
	}

	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param integer $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo campo
	 * @param string $campo
	 */
	public function setCampo($campo){
		$this->campo = $campo;
	}

	/**
	 * Metodo para establecer el valor del campo cueini
	 * @param string $cueini
	 */
	public function setCueini($cueini){
		$this->cueini = $cueini;
	}

	/**
	 * Metodo para establecer el valor del campo cuefin
	 * @param string $cuefin
	 */
	public function setCuefin($cuefin){
		$this->cuefin = $cuefin;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo codfor
	 * @return integer
	 */
	public function getCodfor(){
		return $this->codfor;
	}

	/**
	 * Devuelve el valor del campo codigo
	 * @return integer
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo campo
	 * @return string
	 */
	public function getCampo(){
		return $this->campo;
	}

	/**
	 * Devuelve el valor del campo cueini
	 * @return string
	 */
	public function getCueini(){
		return $this->cueini;
	}

	/**
	 * Devuelve el valor del campo cuefin
	 * @return string
	 */
	public function getCuefin(){
		return $this->cuefin;
	}

}

