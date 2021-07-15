<?php

class Unidad extends RcsRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_unidad;

	/**
	 * @var integer
	 */
	protected $magnitud;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_unidad
	 * @param string $nom_unidad
	 */
	public function setNomUnidad($nom_unidad){
		$this->nom_unidad = $nom_unidad;
	}

	/**
	 * Metodo para establecer el valor del campo magnitud
	 * @param integer $magnitud
	 */
	public function setMagnitud($magnitud){
		$this->magnitud = $magnitud;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_unidad
	 * @return string
	 */
	public function getNomUnidad(){
		return $this->nom_unidad;
	}

	/**
	 * Devuelve el valor del campo magnitud
	 * @return integer
	 */
	public function getMagnitud(){
		return $this->magnitud;
	}

	protected function beforeDelete(){
		if($this->countInve()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la unidad porque tiene referencias asociadas', 'codigo'));
			return false;
		}
	}

	public function initialize(){

		$this->hasMany('unidad', 'Inve', 'codigo');

		$this->addForeignKey('magnitud', 'Magnitudes', 'id', array(
			'message' => 'La magnitud no es válida'
		));

	}

}

