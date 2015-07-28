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

class Perfiles extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var integer
	 */
	protected $aplicaciones_id;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo aplicaciones_id
	 * @param integer $aplicaciones_id
	 */
	public function setAplicacionesId($aplicaciones_id){
		$this->aplicaciones_id = $aplicaciones_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo aplicaciones_id
	 * @return integer
	 */
	public function getAplicacionesId(){
		return $this->aplicaciones_id;
	}

	public function beforeDelete(){
		if($this->countPerfilesUsuarios()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el perfil porque está asignado a usuarios', 'nombre'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('PerfilesUsuarios');
		$this->belongsTo('Aplicaciones');
	}

}

