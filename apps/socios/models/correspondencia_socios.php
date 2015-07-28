<?php

class CorrespondenciaSocios extends ActiveRecord {

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
	protected $tipo_correspondencia_id;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $size;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var string
	 */
	protected $estado;


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
	 * Metodo para establecer el valor del campo tipo_correspondencia_id
	 * @param integer $tipo_correspondencia_id
	 */
	public function setTipoCorrespondenciaId($tipo_correspondencia_id){
		$this->tipo_correspondencia_id = $tipo_correspondencia_id;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param string $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo name
	 * @param string $name
	 */
	public function setName($name){
		$this->name = $name;
	}

	/**
	 * Metodo para establecer el valor del campo type
	 * @param string $type
	 */
	public function setType($type){
		$this->type = $type;
	}

	/**
	 * Metodo para establecer el valor del campo size
	 * @param string $size
	 */
	public function setSize($size){
		$this->size = $size;
	}

	/**
	 * Metodo para establecer el valor del campo content
	 * @param string $content
	 */
	public function setContent($content){
		$this->content = $content;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
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
	 * Devuelve el valor del campo tipo_correspondencia_id
	 * @return integer
	 */
	public function getTipoCorrespondenciaId(){
		return $this->tipo_correspondencia_id;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return string
	 */
	public function getFecha(){
		return $this->fecha;
	}

	/**
	 * Devuelve el valor del campo name
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Devuelve el valor del campo type
	 * @return string
	 */
	public function getType(){
		return $this->type;
	}

	/**
	 * Devuelve el valor del campo size
	 * @return string
	 */
	public function getSize(){
		return $this->size;
	}

	/**
	 * Devuelve el valor del campo content
	 * @return string
	 */
	public function getContent(){
		return $this->content;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	public function initialize(){
		$this->belongsTo('tipo_correspondencia_id', 'TipoCorrespondencia', 'id');
	}

}

