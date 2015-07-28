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

class Records extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $revisions_id;

	/**
	 * @var string
	 */
	protected $field_name;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var string
	 */
	protected $is_primary;

	/**
	 * @var string
	 */
	protected $changed;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo revisions_id
	 * @param integer $revisions_id
	 */
	public function setRevisionsId($revisions_id){
		$this->revisions_id = $revisions_id;
	}

	/**
	 * Metodo para establecer el valor del campo field_name
	 * @param string $field_name
	 */
	public function setFieldName($field_name){
		$this->field_name = $field_name;
	}

	/**
	 * Metodo para establecer el valor del campo value
	 * @param string $value
	 */
	public function setValue($value){
		$this->value = $value;
	}

	/**
	 * Metodo para establecer el valor del campo is_primary
	 * @param string $is_primary
	 */
	public function setIsPrimary($is_primary){
		$this->is_primary = $is_primary;
	}

	/**
	 * Metodo para establecer el valor del campo changed
	 * @param string $changed
	 */
	public function setChanged($changed){
		$this->changed = $changed;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo revisions_id
	 * @return integer
	 */
	public function getRevisionsId(){
		return $this->revisions_id;
	}

	/**
	 * Devuelve el valor del campo field_name
	 * @return string
	 */
	public function getFieldName(){
		return $this->field_name;
	}

	/**
	 * Devuelve el valor del campo value
	 * @return string
	 */
	public function getValue(){
		return $this->value;
	}

	/**
	 * Devuelve el valor del campo is_primary
	 * @return string
	 */
	public function getIsPrimary(){
		return $this->is_primary;
	}

	/**
	 * Devuelve el valor del campo changed
	 * @return string
	 */
	public function getChanged(){
		return $this->changed;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSchema('hfos_rcs');
		$this->belongsTo('Revisions');
	}

}

