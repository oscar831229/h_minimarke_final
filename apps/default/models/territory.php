<?php

class Territory extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $iso3166;

	/**
	 * @var integer
	 */
	protected $coddas;

	/**
	 * @var string
	 */
	protected $male_adjective;

	/**
	 * @var string
	 */
	protected $female_adjective;

	/**
	 * @var string
	 */
	protected $name_en;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo name
	 * @param string $name
	 */
	public function setName($name){
		$this->name = $name;
	}

	/**
	 * Metodo para establecer el valor del campo iso3166
	 * @param string $iso3166
	 */
	public function setIso3166($iso3166){
		$this->iso3166 = $iso3166;
	}

	/**
	 * Metodo para establecer el valor del campo coddas
	 * @param integer $coddas
	 */
	public function setCoddas($coddas){
		$this->coddas = $coddas;
	}

	/**
	 * Metodo para establecer el valor del campo male_adjective
	 * @param string $male_adjective
	 */
	public function setMaleAdjective($male_adjective){
		$this->male_adjective = $male_adjective;
	}

	/**
	 * Metodo para establecer el valor del campo female_adjective
	 * @param string $female_adjective
	 */
	public function setFemaleAdjective($female_adjective){
		$this->female_adjective = $female_adjective;
	}

	/**
	 * Metodo para establecer el valor del campo name_en
	 * @param string $name_en
	 */
	public function setNameEn($name_en){
		$this->name_en = $name_en;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo name
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Devuelve el valor del campo iso3166
	 * @return string
	 */
	public function getIso3166(){
		return $this->iso3166;
	}

	/**
	 * Devuelve el valor del campo coddas
	 * @return integer
	 */
	public function getCoddas(){
		return $this->coddas;
	}

	/**
	 * Devuelve el valor del campo male_adjective
	 * @return string
	 */
	public function getMaleAdjective(){
		return $this->male_adjective;
	}

	/**
	 * Devuelve el valor del campo female_adjective
	 * @return string
	 */
	public function getFemaleAdjective(){
		return $this->female_adjective;
	}

	/**
	 * Devuelve el valor del campo name_en
	 * @return string
	 */
	public function getNameEn(){
		return $this->name_en;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){		
		$this->setSchema("hfos_geoinfo");
	}

}

