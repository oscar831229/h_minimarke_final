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

class Refe extends RcsRecord {

	/**
	 * @var string
	 */
	protected $item;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $linea;


	/**
	 * Metodo para establecer el valor del campo item
	 * @param string $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo linea
	 * @param string $linea
	 */
	public function setLinea($linea){
		$this->linea = $linea;
	}


	/**
	 * Devuelve el valor del campo item
	 * @return string
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo linea
	 * @return string
	 */
	public function getLinea(){
		return $this->linea;
	}

	public function beforeDelete(){
		if($this->countOserv()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el item de servicio porque tiene ordenes asociadas', 'item'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('item', 'Oserv', 'item');
		$this->belongsTo('linea', 'Lineaser', 'linea');
	}

}

