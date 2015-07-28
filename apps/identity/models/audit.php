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

class Audit extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $hora;

	/**
	 * @var integer
	 */
	protected $codusu;

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
	protected $valant;

	/**
	 * @var string
	 */
	protected $valnue;

	/**
	 * @var string
	 */
	protected $notas;


	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo hora
	 * @param string $hora
	 */
	public function setHora($hora){
		$this->hora = $hora;
	}

	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
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
	 * Metodo para establecer el valor del campo valant
	 * @param string $valant
	 */
	public function setValant($valant){
		$this->valant = $valant;
	}

	/**
	 * Metodo para establecer el valor del campo valnue
	 * @param string $valnue
	 */
	public function setValnue($valnue){
		$this->valnue = $valnue;
	}

	/**
	 * Metodo para establecer el valor del campo notas
	 * @param string $notas
	 */
	public function setNotas($notas){
		$this->notas = $notas;
	}


	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo hora
	 * @return string
	 */
	public function getHora(){
		return $this->hora;
	}

	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
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
	 * Devuelve el valor del campo valant
	 * @return string
	 */
	public function getValant(){
		return $this->valant;
	}

	/**
	 * Devuelve el valor del campo valnue
	 * @return string
	 */
	public function getValnue(){
		return $this->valnue;
	}

	/**
	 * Devuelve el valor del campo notas
	 * @return string
	 */
	public function getNotas(){
		return $this->notas;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$this->setSchema("hfos_audit");
	}

}

