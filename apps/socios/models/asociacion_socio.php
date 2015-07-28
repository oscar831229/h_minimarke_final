<?php

class AsociacionSocio extends ActiveRecord {

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
	protected $tipo_asociacion_socio_id;

	/**
	 * @var integer
	 */
	protected $otro_socio_id;


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
	 * Metodo para establecer el valor del campo tipo_asociacion_socio_id
	 * @param integer $tipo_asociacion_socio_id
	 */
	public function setTipoAsociacionSocioId($tipo_asociacion_socio_id){
		$this->tipo_asociacion_socio_id = $tipo_asociacion_socio_id;
	}

	/**
	 * Metodo para establecer el valor del campo otro_socio_id
	 * @param integer $otro_socio_id
	 */
	public function setOtroSocioId($otro_socio_id){
		$this->otro_socio_id = $otro_socio_id;
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
	 * Devuelve el valor del campo tipo_asociacion_socio_id
	 * @return integer
	 */
	public function getTipoAsociacionSocioId(){
		return $this->tipo_asociacion_socio_id;
	}

	/**
	 * Devuelve el valor del campo otro_socio_id
	 * @return integer
	 */
	public function getOtroSocioId(){
		return $this->otro_socio_id;
	}

	public function initialize(){
		$this->addForeignKey('socios_id', 'Socios', 'socios_id', array(
			'message' => 'El socio no es valido'
		));
		$this->addForeignKey('otro_socio_id', 'Socios', 'socios_id', array(
			'message' => 'El socio asociado no es valido'
		));
		$this->addForeignKey('tipo_asociacion_socio_id', 'TipoAsociacionSocio', 'id', array(
			'message' => 'El tipo de asociación con otro socio no es valido'
		));
		$this->hasOne('socios_id','Socios','socios_id');
		$this->hasOne('tipo_asociacion_socio_id','TipoAsociacionSocio','id');
	}

}

