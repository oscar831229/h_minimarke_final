<?php

class SociosPresentado extends ActiveRecord {

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
	protected $presentado1_id;

	/**
	 * @var integer
	 */
	protected $presentado2_id;

	/**
	 * @var integer
	 */
	protected $presentado3_id;


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
	 * Metodo para establecer el valor del campo presentado1_id
	 * @param integer $presentado1_id
	 */
	public function setPresentado1Id($presentado1_id){
		$this->presentado1_id = $presentado1_id;
	}

	/**
	 * Metodo para establecer el valor del campo presentado2_id
	 * @param integer $presentado2_id
	 */
	public function setPresentado2Id($presentado2_id){
		$this->presentado2_id = $presentado2_id;
	}

	/**
	 * Metodo para establecer el valor del campo presentado3_id
	 * @param integer $presentado3_id
	 */
	public function setPresentado3Id($presentado3_id){
		$this->presentado3_id = $presentado3_id;
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
	 * Devuelve el valor del campo presentado1_id
	 * @return integer
	 */
	public function getPresentado1Id(){
		return $this->presentado1_id;
	}

	/**
	 * Devuelve el valor del campo presentado2_id
	 * @return integer
	 */
	public function getPresentado2Id(){
		return $this->presentado2_id;
	}

	/**
	 * Devuelve el valor del campo presentado3_id
	 * @return integer
	 */
	public function getPresentado3Id(){
		return $this->presentado3_id;
	}

}

