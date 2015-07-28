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

class FormaPago extends RcsRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $cta_contable;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo cta_contable
	 * @param string $cta_contable
	 */
	public function setCtaContable($cta_contable){
		$this->cta_contable = $cta_contable;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo cta_contable
	 * @return string
	 */
	public function getCtaContable(){
		return $this->cta_contable;
	}

	protected function beforeSave(){
		$this->descripcion = i18n::strtoupper($this->descripcion);
	}

	protected function beforeDelete(){
		if($this->countMovihead()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la forma de pago porque tiene movimiento en Inventarios', 'codigo'));
			return false;
		}
		if($this->countActivos()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la forma de pago porque tiene activos asociados', 'codigo'));
			return false;
		}
	}

	public function initialize(){
		$this->hasMany('forma_pago', 'Movihead', 'codigo');
		$this->hasMany('forma_pago', 'Activos', 'codigo');
		$this->addForeignKey('cta_contable', 'Cuentas', 'cuenta', array(
			'conditions' => 'es_auxiliar="S"',
			'message' => 'La cuenta contable no existe o no es auxiliar'
		));
	}

}

