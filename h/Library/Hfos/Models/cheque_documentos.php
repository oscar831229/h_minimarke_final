<?php

class ChequeDocumentos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $cheque_id;

	/**
	 * @var string
	 */
	protected $tipo_doc;

	/**
	 * @var integer
	 */
	protected $numero;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo cheque_id
	 * @param integer $cheque_id
	 */
	public function setChequeId($cheque_id){
		$this->cheque_id = $cheque_id;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_doc
	 * @param string $tipo_doc
	 */
	public function setTipoDoc($tipo_doc){
		$this->tipo_doc = $tipo_doc;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo cheque_id
	 * @return integer
	 */
	public function getChequeId(){
		return $this->cheque_id;
	}

	/**
	 * Devuelve el valor del campo tipo_doc
	 * @return string
	 */
	public function getTipoDoc(){
		return $this->tipo_doc;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

}

