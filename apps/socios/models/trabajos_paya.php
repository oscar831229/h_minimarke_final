<?php

class TrabajosPaya extends ActiveRecord {

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
	protected $profesiones_id;

	/**
	 * @var integer
	 */
	protected $especializaciones_id;

	/**
	 * @var string
	 */
	protected $empresa1;

	/**
	 * @var string
	 */
	protected $cargo1;

	/**
	 * @var string
	 */
	protected $direccion1;

	/**
	 * @var string
	 */
	protected $telefono1;

	/**
	 * @var string
	 */
	protected $fax1;

	/**
	 * @var string
	 */
	protected $empresa2;

	/**
	 * @var string
	 */
	protected $cargo2;

	/**
	 * @var string
	 */
	protected $direccion2;

	/**
	 * @var string
	 */
	protected $telefono2;

	/**
	 * @var string
	 */
	protected $fax2;

	/**
	 * @var string
	 */
	protected $comentario;


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
	 * Metodo para establecer el valor del campo profesiones_id
	 * @param integer $profesiones_id
	 */
	public function setProfesionesId($profesiones_id){
		$this->profesiones_id = $profesiones_id;
	}

	/**
	 * Metodo para establecer el valor del campo especializaciones_id
	 * @param integer $especializaciones_id
	 */
	public function setEspecializacionesId($especializaciones_id){
		$this->especializaciones_id = $especializaciones_id;
	}

	/**
	 * Metodo para establecer el valor del campo empresa1
	 * @param string $empresa1
	 */
	public function setEmpresa1($empresa1){
		$this->empresa1 = $empresa1;
	}

	/**
	 * Metodo para establecer el valor del campo cargo1
	 * @param string $cargo1
	 */
	public function setCargo1($cargo1){
		$this->cargo1 = $cargo1;
	}

	/**
	 * Metodo para establecer el valor del campo direccion1
	 * @param string $direccion1
	 */
	public function setDireccion1($direccion1){
		$this->direccion1 = $direccion1;
	}

	/**
	 * Metodo para establecer el valor del campo telefono1
	 * @param string $telefono1
	 */
	public function setTelefono1($telefono1){
		$this->telefono1 = $telefono1;
	}

	/**
	 * Metodo para establecer el valor del campo fax1
	 * @param string $fax1
	 */
	public function setFax1($fax1){
		$this->fax1 = $fax1;
	}

	/**
	 * Metodo para establecer el valor del campo empresa2
	 * @param string $empresa2
	 */
	public function setEmpresa2($empresa2){
		$this->empresa2 = $empresa2;
	}

	/**
	 * Metodo para establecer el valor del campo cargo2
	 * @param string $cargo2
	 */
	public function setCargo2($cargo2){
		$this->cargo2 = $cargo2;
	}

	/**
	 * Metodo para establecer el valor del campo direccion2
	 * @param string $direccion2
	 */
	public function setDireccion2($direccion2){
		$this->direccion2 = $direccion2;
	}

	/**
	 * Metodo para establecer el valor del campo telefono2
	 * @param string $telefono2
	 */
	public function setTelefono2($telefono2){
		$this->telefono2 = $telefono2;
	}

	/**
	 * Metodo para establecer el valor del campo fax2
	 * @param string $fax2
	 */
	public function setFax2($fax2){
		$this->fax2 = $fax2;
	}

	/**
	 * Metodo para establecer el valor del campo comentario
	 * @param string $comentario
	 */
	public function setComentario($comentario){
		$this->comentario = $comentario;
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
	 * Devuelve el valor del campo profesiones_id
	 * @return integer
	 */
	public function getProfesionesId(){
		return $this->profesiones_id;
	}

	/**
	 * Devuelve el valor del campo especializaciones_id
	 * @return integer
	 */
	public function getEspecializacionesId(){
		return $this->especializaciones_id;
	}

	/**
	 * Devuelve el valor del campo empresa1
	 * @return string
	 */
	public function getEmpresa1(){
		return $this->empresa1;
	}

	/**
	 * Devuelve el valor del campo cargo1
	 * @return string
	 */
	public function getCargo1(){
		return $this->cargo1;
	}

	/**
	 * Devuelve el valor del campo direccion1
	 * @return string
	 */
	public function getDireccion1(){
		return $this->direccion1;
	}

	/**
	 * Devuelve el valor del campo telefono1
	 * @return string
	 */
	public function getTelefono1(){
		return $this->telefono1;
	}

	/**
	 * Devuelve el valor del campo fax1
	 * @return string
	 */
	public function getFax1(){
		return $this->fax1;
	}

	/**
	 * Devuelve el valor del campo empresa2
	 * @return string
	 */
	public function getEmpresa2(){
		return $this->empresa2;
	}

	/**
	 * Devuelve el valor del campo cargo2
	 * @return string
	 */
	public function getCargo2(){
		return $this->cargo2;
	}

	/**
	 * Devuelve el valor del campo direccion2
	 * @return string
	 */
	public function getDireccion2(){
		return $this->direccion2;
	}

	/**
	 * Devuelve el valor del campo telefono2
	 * @return string
	 */
	public function getTelefono2(){
		return $this->telefono2;
	}

	/**
	 * Devuelve el valor del campo fax2
	 * @return string
	 */
	public function getFax2(){
		return $this->fax2;
	}

	/**
	 * Devuelve el valor del campo comentario
	 * @return string
	 */
	public function getComentario(){
		return $this->comentario;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSource('trabajos');
		$this->setSchema('payande');
	}

}

