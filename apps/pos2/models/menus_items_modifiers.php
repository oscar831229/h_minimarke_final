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

class MenusItemsModifiers extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $menus_items_id;

	/**
	 * @var integer
	 */
	protected $modifiers_id;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo menus_items_id
	 * @param integer $menus_items_id
	 */
	public function setMenusItemsId($menus_items_id){
		$this->menus_items_id = $menus_items_id;
	}

	/**
	 * Metodo para establecer el valor del campo modifiers_id
	 * @param integer $modifiers_id
	 */
	public function setModifiersId($modifiers_id){
		$this->modifiers_id = $modifiers_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo menus_items_id
	 * @return integer
	 */
	public function getMenusItemsId(){
		return $this->menus_items_id;
	}

	/**
	 * Devuelve el valor del campo modifiers_id
	 * @return integer
	 */
	public function getModifiersId(){
		return $this->modifiers_id;
	}

	public function initialize(){
		$this->belongsTo('Modifiers');
	}

}

