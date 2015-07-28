<?php

class Auditoria extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var Date
	 */
	protected $fecha_at;

	/**
	 * @var string
	 */
	protected $hora;

	/**
	 * @var string
	 */
	protected $ip;

	/**
	 * @var integer
	 */
	protected $usuario_id;

	/**
	 * @var string
	 */
	protected $tabla;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $valor_anterior;

	/**
	 * @var string
	 */
	protected $valor_nuevo;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_at
	 * @param Date $fecha_at
	 */
	public function setFechaAt($fecha_at){
		$this->fecha_at = $fecha_at;
	}

	/**
	 * Metodo para establecer el valor del campo hora
	 * @param string $hora
	 */
	public function setHora($hora){
		$this->hora = $hora;
	}

	/**
	 * Metodo para establecer el valor del campo ip
	 * @param string $ip
	 */
	public function setIp($ip){
		$this->ip = $ip;
	}

	/**
	 * Metodo para establecer el valor del campo usuario_id
	 * @param integer $usuario_id
	 */
	public function setUsuarioId($usuario_id){
		$this->usuario_id = $usuario_id;
	}

	/**
	 * Metodo para establecer el valor del campo tabla
	 * @param string $tabla
	 */
	public function setTabla($tabla){
		$this->tabla = $tabla;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo valor_anterior
	 * @param string $valor_anterior
	 */
	public function setValorAnterior($valor_anterior){
		$this->valor_anterior = $valor_anterior;
	}

	/**
	 * Metodo para establecer el valor del campo valor_nuevo
	 * @param string $valor_nuevo
	 */
	public function setValorNuevo($valor_nuevo){
		$this->valor_nuevo = $valor_nuevo;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo fecha_at
	 * @return Date
	 */
	public function getFechaAt(){
		return new Date($this->fecha_at);
	}

	/**
	 * Devuelve el valor del campo hora
	 * @return string
	 */
	public function getHora(){
		return $this->hora;
	}

	/**
	 * Devuelve el valor del campo ip
	 * @return string
	 */
	public function getIp(){
		return $this->ip;
	}

	/**
	 * Devuelve el valor del campo usuario_id
	 * @return integer
	 */
	public function getUsuarioId(){
		return $this->usuario_id;
	}

	/**
	 * Devuelve el valor del campo tabla
	 * @return string
	 */
	public function getTabla(){
		return $this->tabla;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo valor_anterior
	 * @return string
	 */
	public function getValorAnterior(){
		return $this->valor_anterior;
	}

	/**
	 * Devuelve el valor del campo valor_nuevo
	 * @return string
	 */
	public function getValorNuevo(){
		return $this->valor_nuevo;
	}

}

