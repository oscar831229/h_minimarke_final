<?php

class Asoclubes extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $socios_id;

	/**
	 * @var integer
	 */
	protected $club;

	/**
	 * @var string
	 */
	protected $desde;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo socios_id
	 * @param integer $socios_id
	 */
	public function setSociosId($socios_id){
		$this->socios_id = $socios_id;
	}

	/**
	 * Metodo para establecer el valor del campo club
	 * @param integer $club
	 */
	public function setClub($club){
		$this->club = $club;
	}

	/**
	 * Metodo para establecer el valor del campo desde
	 * @param string $desde
	 */
	public function setDesde($desde){
		$this->desde = $desde;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo socios_id
	 * @return integer
	 */
	public function getSociosId(){
		return $this->socios_id;
	}

	/**
	 * Devuelve el valor del campo club
	 * @return integer
	 */
	public function getClub(){
		return $this->club;
	}

	/**
	 * Devuelve el valor del campo desde
	 * @return string
	 */
	public function getDesde(){
		return $this->desde;
	}

}

