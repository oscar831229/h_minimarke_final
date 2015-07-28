<?php

class Auditlog extends ActiveRecord
{

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var Date
	 */
	protected $fecsis;

	/**
	 * @var string
	 */
	protected $hora;

	/**
	 * @var string
	 */
	protected $modulo;

	/**
	 * @var integer
	 */
	protected $codusu;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $ip;

	/**
	 * @var string
	 */
	protected $keywords;

	/**
	 * @var string
	 */
	protected $nota;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo fecsis
	 * @param Date $fecsis
	 */
	public function setFecsis($fecsis){
		$this->fecsis = $fecsis;
	}

	/**
	 * Metodo para establecer el valor del campo hora
	 * @param string $hora
	 */
	public function setHora($hora){
		$this->hora = $hora;
	}

	/**
	 * Metodo para establecer el valor del campo modulo
	 * @param string $modulo
	 */
	public function setModulo($modulo){
		$this->modulo = $modulo;
	}

	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo ip
	 * @param string $ip
	 */
	public function setIp($ip){
		$this->ip = $ip;
	}

	/**
	 * Metodo para establecer el valor del campo keywords
	 * @param string $keywords
	 */
	public function setKeywords($keywords){
		$this->keywords = $keywords;
	}

	/**
	 * Metodo para establecer el valor del campo nota
	 * @param string $nota
	 */
	public function setNota($nota){
		$this->nota = $nota;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo fecsis
	 * @return Date
	 */
	public function getFecsis(){
		return new Date($this->fecsis);
	}

	/**
	 * Devuelve el valor del campo hora
	 * @return string
	 */
	public function getHora(){
		return $this->hora;
	}

	/**
	 * Devuelve el valor del campo modulo
	 * @return string
	 */
	public function getModulo(){
		return $this->modulo;
	}

	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo ip
	 * @return string
	 */
	public function getIp(){
		return $this->ip;
	}

	/**
	 * Devuelve el valor del campo keywords
	 * @return string
	 */
	public function getKeywords(){
		return $this->keywords;
	}

	/**
	 * Devuelve el valor del campo nota
	 * @return string
	 */
	public function getNota(){
		return $this->nota;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSchema("hfos_audit");
	}

}

