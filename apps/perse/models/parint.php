<?php

class Parint extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $pagint;

	/**
	 * @var string
	 */
	protected $paspag;

	/**
	 * @var integer
	 */
	protected $usuint;

	/**
	 * @var integer
	 */
	protected $forint;

	/**
	 * @var integer
	 */
	protected $conint;

	/**
	 * @var integer
	 */
	protected $resint;

	/**
	 * @var integer
	 */
	protected $venint;

	/**
	 * @var integer
	 */
	protected $grucor;

	/**
	 * @var string
	 */
	protected $email1;

	/**
	 * @var string
	 */
	protected $email2;

	/**
	 * @var string
	 */
	protected $cobseg;

	/**
	 * @var integer
	 */
	protected $usupag;

	/**
	 * @var string
	 */
	protected $llaenc;

	/**
	 * @var string
	 */
	protected $enviro;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $nitint;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo pagint
	 * @param string $pagint
	 */
	public function setPagint($pagint){
		$this->pagint = $pagint;
	}

	/**
	 * Metodo para establecer el valor del campo paspag
	 * @param string $paspag
	 */
	public function setPaspag($paspag){
		$this->paspag = $paspag;
	}

	/**
	 * Metodo para establecer el valor del campo usuint
	 * @param integer $usuint
	 */
	public function setUsuint($usuint){
		$this->usuint = $usuint;
	}

	/**
	 * Metodo para establecer el valor del campo forint
	 * @param integer $forint
	 */
	public function setForint($forint){
		$this->forint = $forint;
	}

	/**
	 * Metodo para establecer el valor del campo conint
	 * @param integer $conint
	 */
	public function setConint($conint){
		$this->conint = $conint;
	}

	/**
	 * Metodo para establecer el valor del campo resint
	 * @param integer $resint
	 */
	public function setResint($resint){
		$this->resint = $resint;
	}

	/**
	 * Metodo para establecer el valor del campo venint
	 * @param integer $venint
	 */
	public function setVenint($venint){
		$this->venint = $venint;
	}

	/**
	 * Metodo para establecer el valor del campo grucor
	 * @param integer $grucor
	 */
	public function setGrucor($grucor){
		$this->grucor = $grucor;
	}

	/**
	 * Metodo para establecer el valor del campo email1
	 * @param string $email1
	 */
	public function setEmail1($email1){
		$this->email1 = $email1;
	}

	/**
	 * Metodo para establecer el valor del campo email2
	 * @param string $email2
	 */
	public function setEmail2($email2){
		$this->email2 = $email2;
	}

	/**
	 * Metodo para establecer el valor del campo cobseg
	 * @param string $cobseg
	 */
	public function setCobseg($cobseg){
		$this->cobseg = $cobseg;
	}

	/**
	 * Metodo para establecer el valor del campo usupag
	 * @param integer $usupag
	 */
	public function setUsupag($usupag){
		$this->usupag = $usupag;
	}

	/**
	 * Metodo para establecer el valor del campo llaenc
	 * @param string $llaenc
	 */
	public function setLlaenc($llaenc){
		$this->llaenc = $llaenc;
	}

	/**
	 * Metodo para establecer el valor del campo enviro
	 * @param string $enviro
	 */
	public function setEnviro($enviro){
		$this->enviro = $enviro;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo nitint
	 * @param string $nitint
	 */
	public function setNitint($nitint){
		$this->nitint = $nitint;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo pagint
	 * @return string
	 */
	public function getPagint(){
		return $this->pagint;
	}

	/**
	 * Devuelve el valor del campo paspag
	 * @return string
	 */
	public function getPaspag(){
		return $this->paspag;
	}

	/**
	 * Devuelve el valor del campo usuint
	 * @return integer
	 */
	public function getUsuint(){
		return $this->usuint;
	}

	/**
	 * Devuelve el valor del campo forint
	 * @return integer
	 */
	public function getForint(){
		return $this->forint;
	}

	/**
	 * Devuelve el valor del campo conint
	 * @return integer
	 */
	public function getConint(){
		return $this->conint;
	}

	/**
	 * Devuelve el valor del campo resint
	 * @return integer
	 */
	public function getResint(){
		return $this->resint;
	}

	/**
	 * Devuelve el valor del campo venint
	 * @return integer
	 */
	public function getVenint(){
		return $this->venint;
	}

	/**
	 * Devuelve el valor del campo grucor
	 * @return integer
	 */
	public function getGrucor(){
		return $this->grucor;
	}

	/**
	 * Devuelve el valor del campo email1
	 * @return string
	 */
	public function getEmail1(){
		return $this->email1;
	}

	/**
	 * Devuelve el valor del campo email2
	 * @return string
	 */
	public function getEmail2(){
		return $this->email2;
	}

	/**
	 * Devuelve el valor del campo cobseg
	 * @return string
	 */
	public function getCobseg(){
		return $this->cobseg;
	}

	/**
	 * Devuelve el valor del campo usupag
	 * @return integer
	 */
	public function getUsupag(){
		return $this->usupag;
	}

	/**
	 * Devuelve el valor del campo llaenc
	 * @return string
	 */
	public function getLlaenc(){
		return $this->llaenc;
	}

	/**
	 * Devuelve el valor del campo enviro
	 * @return string
	 */
	public function getEnviro(){
		return $this->enviro;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo nitint
	 * @return string
	 */
	public function getNitint(){
		return $this->nitint;
	}

}

