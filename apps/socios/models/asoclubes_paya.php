<?php

class AsoclubesPaya extends ActiveRecord {

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
	protected $club1;

	/**
	 * @var string
	 */
	protected $desde1;

	/**
	 * @var integer
	 */
	protected $club2;

	/**
	 * @var string
	 */
	protected $desde2;

	/**
	 * @var integer
	 */
	protected $club3;

	/**
	 * @var string
	 */
	protected $desde3;

	/**
	 * @var string
	 */
	protected $asociaciones;


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
	 * Metodo para establecer el valor del campo club1
	 * @param integer $club1
	 */
	public function setClub1($club1){
		$this->club1 = $club1;
	}

	/**
	 * Metodo para establecer el valor del campo desde1
	 * @param string $desde1
	 */
	public function setDesde1($desde1){
		$this->desde1 = $desde1;
	}

	/**
	 * Metodo para establecer el valor del campo club2
	 * @param integer $club2
	 */
	public function setClub2($club2){
		$this->club2 = $club2;
	}

	/**
	 * Metodo para establecer el valor del campo desde2
	 * @param string $desde2
	 */
	public function setDesde2($desde2){
		$this->desde2 = $desde2;
	}

	/**
	 * Metodo para establecer el valor del campo club3
	 * @param integer $club3
	 */
	public function setClub3($club3){
		$this->club3 = $club3;
	}

	/**
	 * Metodo para establecer el valor del campo desde3
	 * @param string $desde3
	 */
	public function setDesde3($desde3){
		$this->desde3 = $desde3;
	}

	/**
	 * Metodo para establecer el valor del campo asociaciones
	 * @param string $asociaciones
	 */
	public function setAsociaciones($asociaciones){
		$this->asociaciones = $asociaciones;
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
	 * Devuelve el valor del campo club1
	 * @return integer
	 */
	public function getClub1(){
		return $this->club1;
	}

	/**
	 * Devuelve el valor del campo desde1
	 * @return string
	 */
	public function getDesde1(){
		return $this->desde1;
	}

	/**
	 * Devuelve el valor del campo club2
	 * @return integer
	 */
	public function getClub2(){
		return $this->club2;
	}

	/**
	 * Devuelve el valor del campo desde2
	 * @return string
	 */
	public function getDesde2(){
		return $this->desde2;
	}

	/**
	 * Devuelve el valor del campo club3
	 * @return integer
	 */
	public function getClub3(){
		return $this->club3;
	}

	/**
	 * Devuelve el valor del campo desde3
	 * @return string
	 */
	public function getDesde3(){
		return $this->desde3;
	}

	/**
	 * Devuelve el valor del campo asociaciones
	 * @return string
	 */
	public function getAsociaciones(){
		return $this->asociaciones;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSource('asoclubes');		
		$this->setSchema('payande');
	}

}

