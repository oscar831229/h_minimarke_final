<?php

class Wheater extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $db;

	/**
	 * @var Date
	 */
	protected $today;

	/**
	 * @var string
	 */
	protected $hour;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var string
	 */
	protected $original;

	/**
	 * @var string
	 */
	protected $status;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo db
	 * @param string $db
	 */
	public function setDb($db){
		$this->db = $db;
	}

	/**
	 * Metodo para establecer el valor del campo today
	 * @param Date $today
	 */
	public function setToday($today){
		$this->today = $today;
	}

	/**
	 * Metodo para establecer el valor del campo hour
	 * @param string $hour
	 */
	public function setHour($hour){
		$this->hour = $hour;
	}

	/**
	 * Metodo para establecer el valor del campo content
	 * @param string $content
	 */
	public function setContent($content){
		$this->content = $content;
	}

	/**
	 * Metodo para establecer el valor del campo original
	 * @param string $original
	 */
	public function setOriginal($original){
		$this->original = $original;
	}

	/**
	 * Metodo para establecer el valor del campo status
	 * @param string $status
	 */
	public function setStatus($status){
		$this->status = $status;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo db
	 * @return string
	 */
	public function getDb(){
		return $this->db;
	}

	/**
	 * Devuelve el valor del campo today
	 * @return Date
	 */
	public function getToday(){
		return new Date($this->today);
	}

	/**
	 * Devuelve el valor del campo hour
	 * @return string
	 */
	public function getHour(){
		return $this->hour;
	}

	/**
	 * Devuelve el valor del campo content
	 * @return string
	 */
	public function getContent(){
		return $this->content;
	}

	/**
	 * Devuelve el valor del campo original
	 * @return string
	 */
	public function getOriginal(){
		return $this->original;
	}

	/**
	 * Devuelve el valor del campo status
	 * @return string
	 */
	public function getStatus(){
		return $this->status;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){		
		$this->setSchema("hfos_wheater");
	}

}

