<?php

class ActividadesPaya extends ActiveRecord {

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
	protected $hobbies_id;


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
	 * Metodo para establecer el valor del campo hobbies_id
	 * @param integer $hobbies_id
	 */
	public function setHobbiesId($hobbies_id){
		$this->hobbies_id = $hobbies_id;
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
	 * Devuelve el valor del campo hobbies_id
	 * @return integer
	 */
	public function getHobbiesId(){
		return $this->hobbies_id;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSource('actividades');		
		$this->setSchema('payande');
	}

}

