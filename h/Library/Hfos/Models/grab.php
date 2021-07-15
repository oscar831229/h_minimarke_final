<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */


class Grab extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $accion;

	/**
	 * @var Date
	 */
	protected $fecha_grab;

	/**
	 * @var string
	 */
	protected $hora_grab;

	/**
	 * @var string
	 */
	protected $codigo_grab;

	/**
	 * @var integer
	 */
	protected $usuarios_id;


	/**
	 * Metodo para establecer el valor del campo comprob
	 * @param string $comprob
	 */
	public function setComprob($comprob){
		$this->comprob = $comprob;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo accion
	 * @param string $accion
	 */
	public function setAccion($accion){
		$this->accion = $accion;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_grab
	 * @param Date $fecha_grab
	 */
	public function setFechaGrab($fecha_grab){
		$this->fecha_grab = $fecha_grab;
	}

	/**
	 * Metodo para establecer el valor del campo hora_grab
	 * @param string $hora_grab
	 */
	public function setHoraGrab($hora_grab){
		$this->hora_grab = $hora_grab;
	}

	/**
	 * Metodo para establecer el valor del campo codigo_grab
	 * @param string $codigo_grab
	 */
	public function setCodigoGrab($codigo_grab){
		$this->codigo_grab = $codigo_grab;
	}

	/**
	 * Metodo para establecer el valor del campo usuarios_id
	 * @param integer $usuarios_id
	 */
	public function setUsuariosId($usuarios_id){
		$this->usuarios_id = $usuarios_id;
	}


	/**
	 * Devuelve el valor del campo comprob
	 * @return string
	 */
	public function getComprob(){
		return $this->comprob;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo accion
	 * @return string
	 */
	public function getAccion(){
		return $this->accion;
	}

	/**
	 * Devuelve el valor del campo fecha_grab
	 * @return Date
	 */
	public function getFechaGrab(){
		return new Date($this->fecha_grab);
	}

	/**
	 * Devuelve el valor del campo hora_grab
	 * @return string
	 */
	public function getHoraGrab(){
		return $this->hora_grab;
	}

	/**
	 * Devuelve el valor del campo codigo_grab
	 * @return string
	 */
	public function getCodigoGrab(){
		return $this->codigo_grab;
	}

	/**
	 * Devuelve el valor del campo usuarios_id
	 * @return integer
	 */
	public function getUsuariosId(){
		return $this->usuarios_id;
	}

}

