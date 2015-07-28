<?php

class Location extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var integer
	 */
	protected $zone_id;

	/**
	 * @var integer
	 */
	protected $territory_id;

	/**
	 * @var integer
	 */
	protected $rank;

	/**
	 * @var integer
	 */
	protected $relevance;

	/**
	 * @var integer
	 */
	protected $codigo_dane;

	/**
	 * @var integer
	 */
	protected $location_id;


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
	 * Metodo para establecer el valor del campo zone_id
	 * @param integer $zone_id
	 */
	public function setZoneId($zone_id){
		$this->zone_id = $zone_id;
	}

	/**
	 * Metodo para establecer el valor del campo territory_id
	 * @param integer $territory_id
	 */
	public function setTerritoryId($territory_id){
		$this->territory_id = $territory_id;
	}

	/**
	 * Metodo para establecer el valor del campo rank
	 * @param integer $rank
	 */
	public function setRank($rank){
		$this->rank = $rank;
	}

	/**
	 * Metodo para establecer el valor del campo relevance
	 * @param integer $relevance
	 */
	public function setRelevance($relevance){
		$this->relevance = $relevance;
	}

	/**
	 * Metodo para establecer el valor del campo codigo_dane
	 * @param integer $codigo_dane
	 */
	public function setCodigoDane($codigo_dane){
		$this->codigo_dane = $codigo_dane;
	}

	/**
	 * Metodo para establecer el valor del campo location_id
	 * @param integer $location_id
	 */
	public function setLocationId($location_id){
		$this->location_id = $location_id;
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
	 * Devuelve el valor del campo zone_id
	 * @return integer
	 */
	public function getZoneId(){
		return $this->zone_id;
	}

	/**
	 * Devuelve el valor del campo territory_id
	 * @return integer
	 */
	public function getTerritoryId(){
		return $this->territory_id;
	}

	/**
	 * Devuelve el valor del campo rank
	 * @return integer
	 */
	public function getRank(){
		return $this->rank;
	}

	/**
	 * Devuelve el valor del campo relevance
	 * @return integer
	 */
	public function getRelevance(){
		return $this->relevance;
	}

	/**
	 * Devuelve el valor del campo codigo_dane
	 * @return integer
	 */
	public function getCodigoDane(){
		return $this->codigo_dane;
	}

	/**
	 * Devuelve el valor del campo location_id
	 * @return integer
	 */
	public function getLocationId(){
		return $this->location_id;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSchema('hfos_geoinfo');
		$this->belongsTo('Zone');
		$this->belongsTo('Territory');
	}

}

