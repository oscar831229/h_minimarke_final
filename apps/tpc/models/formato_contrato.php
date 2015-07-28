<?php

class FormatoContrato extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $tipo_contrato_id;

	/**
	 * @var string
	 */
	protected $formato;

	/**
	 * @var string
	 */
	protected $usa_formato;


	/**
	 * Metodo para establecer el valor del campo tipo_contrato_id
	 * @param integer $tipo_contrato_id
	 */
	public function setTipoContratoId($tipo_contrato_id){
		$this->tipo_contrato_id = $tipo_contrato_id;
	}

	/**
	 * Metodo para establecer el valor del campo formato
	 * @param string $formato
	 */
	public function setFormato($formato){
		$this->formato = $formato;
	}

	/**
	 * Metodo para establecer el valor del campo usa_formato
	 * @param string $usa_formato
	 */
	public function setUsaFormato($usa_formato){
		$this->usa_formato = $usa_formato;
	}


	/**
	 * Devuelve el valor del campo tipo_contrato_id
	 * @return integer
	 */
	public function getTipoContratoId(){
		return $this->tipo_contrato_id;
	}

	/**
	 * Devuelve el valor del campo formato
	 * @return string
	 */
	public function getFormato(){
		return $this->formato;
	}

	/**
	 * Devuelve el valor del campo usa_formato
	 * @return string
	 */
	public function getUsaFormato(){
		return $this->usa_formato;
	}

}

