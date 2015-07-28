<?php

class Tipodoc extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nombre;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param integer $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return integer
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	protected function beforeDelete(){
		if($this->countMovi()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el tipo de documento porque tiene movimiento', 'cuenta'));
			return false;
		}
	}

	public function initialize(){
	     $config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->back_db)){
			$this->setSchema($config->hfos->back_db);
		} else {
			$this->setSchema('ramocol');
		}
		$this->hasMany('tipodoc', 'Nits', 'codigo');
	}

}

