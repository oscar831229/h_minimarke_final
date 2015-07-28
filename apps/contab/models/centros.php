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

class Centros extends RcsRecord
{

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_centro;

	/**
	 * @var string
	 */
	protected $responsable;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_centro
	 * @param string $nom_centro
	 */
	public function setNomCentro($nom_centro){
		$this->nom_centro = $nom_centro;
	}

	/**
	 * Metodo para establecer el valor del campo responsable
	 * @param string $responsable
	 */
	public function setResponsable($responsable){
		$this->responsable = $responsable;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_centro
	 * @return string
	 */
	public function getNomCentro(){
		return $this->nom_centro;
	}

	/**
	 * Devuelve el valor del campo responsable
	 * @return string
	 */
	public function getResponsable(){
		return $this->responsable;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Validador del modelo Cheque
	 *
	 * @return boolean
	 */
	protected function validation(){
		$this->validate('Uniqueness', array(
			'field' => array('nom_centro'),
			'message' => 'El nombre del centro de costo ya está siendo usado por otro centro de costo'
		));
		$this->validate('InclusionIn', array(
			'field' => 'estado',
			'domain' => array('A', 'I'),
			'message' => 'El estado debe ser "ACTIVO" ó "INACTIVO"',
			'required' => true
		));
		if($this->validationHasFailed()==true){
			return false;
		}
	}

	public function beforeDelete(){
		if($this->countMovi()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el centro de costo porque tiene movimiento', 'codigo'));
			return false;
		}
		if($this->countActivos()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el centro de costo porque tiene activos fijos asociados', 'codigo'));
			return false;
		}
		if($this->countPres()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el centro de costo porque tiene valores de presupuesto asociados', 'codigo'));
			return false;
		}
		if($this->countMovihead()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el centro de costo porque tiene movimiento en Inventarios', 'codigo'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('codigo', 'Movi', 'centro_costo');
		$this->hasMany('codigo', 'Activos', 'centro_costo');
		$this->hasMany('codigo', 'Pres', 'centro_costo');
		$this->hasMany('codigo', 'Movihead', 'centro_costo');
	}

}

