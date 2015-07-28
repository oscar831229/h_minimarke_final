<?php

class Perdet extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $perabo_id;

	/**
	 * @var integer
	 */
	protected $numcue;

	/**
	 * @var integer
	 */
	protected $numrec;

	/**
	 * @var string
	 */
	protected $valor;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo perabo_id
	 * @param integer $perabo_id
	 */
	public function setPeraboId($perabo_id){
		$this->perabo_id = $perabo_id;
	}

	/**
	 * Metodo para establecer el valor del campo numcue
	 * @param integer $numcue
	 */
	public function setNumcue($numcue){
		$this->numcue = $numcue;
	}

	/**
	 * Metodo para establecer el valor del campo numrec
	 * @param integer $numrec
	 */
	public function setNumrec($numrec){
		$this->numrec = $numrec;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo perabo_id
	 * @return integer
	 */
	public function getPeraboId(){
		return $this->perabo_id;
	}

	/**
	 * Devuelve el valor del campo numcue
	 * @return integer
	 */
	public function getNumcue(){
		return $this->numcue;
	}

	/**
	 * Devuelve el valor del campo numrec
	 * @return integer
	 */
	public function getNumrec(){
		return $this->numrec;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

}

