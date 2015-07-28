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

class Referencias extends ActiveRecord {

	/*protected $id;
	protected $nombre;
	protected $tipo;*/

	/**
	 * Establece el valor del campo id
	 * @param varchar(15) $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Establece el valor del campo nombre
	 * @param char $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Establece el valor del campo tipo
	 * @param varchar(1) $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Devuelve el valor del campo id
	 * @return varchar(15)
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return char
	 */
	public function getNombre(){
		return trim($this->nombre);
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return varchar(1)
	 */
	public function getTipo(){
		return $this->tipo;
	}

}