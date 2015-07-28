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
 * @copyright 	BH-TECK Inc. 2009-2011
 * @version		$Id$
 */

class Consecutivos extends RcsRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $prefijo;

	/**
	 * @var string
	 */
	protected $resolucion;

	/**
	 * @var Date
	 */
	protected $fecha_resolucion;

	/**
	 * @var integer
	 */
	protected $numero_inicial;

	/**
	 * @var integer
	 */
	protected $numero_final;

	/**
	 * @var integer
	 */
	protected $numero_actual;

	/**
	 * @var string
	 */
	protected $nota_factura;

	/**
	 * @var string
	 */
	protected $nota_ica;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo prefijo
	 * @param string $prefijo
	 */
	public function setPrefijo($prefijo){
		$this->prefijo = $prefijo;
	}

	/**
	 * Metodo para establecer el valor del campo resolucion
	 * @param string $resolucion
	 */
	public function setResolucion($resolucion){
		$this->resolucion = $resolucion;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_resolucion
	 * @param Date $fecha_resolucion
	 */
	public function setFechaResolucion($fecha_resolucion){
		$this->fecha_resolucion = $fecha_resolucion;
	}

	/**
	 * Metodo para establecer el valor del campo numero_inicial
	 * @param integer $numero_inicial
	 */
	public function setNumeroInicial($numero_inicial){
		$this->numero_inicial = $numero_inicial;
	}

	/**
	 * Metodo para establecer el valor del campo numero_final
	 * @param integer $numero_final
	 */
	public function setNumeroFinal($numero_final){
		$this->numero_final = $numero_final;
	}

	/**
	 * Metodo para establecer el valor del campo numero_actual
	 * @param integer $numero_actual
	 */
	public function setNumeroActual($numero_actual){
		$this->numero_actual = $numero_actual;
	}

	/**
	 * Metodo para establecer el valor del campo nota_factura
	 * @param string $nota_factura
	 */
	public function setNotaFactura($nota_factura){
		$this->nota_factura = $nota_factura;
	}

	/**
	 * Metodo para establecer el valor del campo nota_ica
	 * @param string $nota_ica
	 */
	public function setNotaIca($nota_ica){
		$this->nota_ica = $nota_ica;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo prefijo
	 * @return string
	 */
	public function getPrefijo(){
		return $this->prefijo;
	}

	/**
	 * Devuelve el valor del campo resolucion
	 * @return string
	 */
	public function getResolucion(){
		return $this->resolucion;
	}

	/**
	 * Devuelve el valor del campo fecha_resolucion
	 * @return Date
	 */
	public function getFechaResolucion(){
		if($this->fecha_resolucion){
			return new Date($this->fecha_resolucion);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo numero_inicial
	 * @return integer
	 */
	public function getNumeroInicial(){
		return $this->numero_inicial;
	}

	/**
	 * Devuelve el valor del campo numero_final
	 * @return integer
	 */
	public function getNumeroFinal(){
		return $this->numero_final;
	}

	/**
	 * Devuelve el valor del campo numero_actual
	 * @return integer
	 */
	public function getNumeroActual(){
		return $this->numero_actual;
	}

	/**
	 * Devuelve el valor del campo nota_factura
	 * @return string
	 */
	public function getNotaFactura(){
		return $this->nota_factura;
	}

	/**
	 * Devuelve el valor del campo nota_ica
	 * @return string
	 */
	public function getNotaIca(){
		return $this->nota_ica;
	}

	public function beforeValidation(){
		$this->detalle = $this->prefijo.'  DEL '.$this->numero_inicial.' AL '.$this->numero_final;
	}

	public function beforeValidationOnCreate(){
		$this->numero_actual = 1;
	}

	/*public function beforeSave()
	{
		if ($this->numero_actual>0) {
			$exists = EntityManager::get('Factura')->count("numero='{$this->numero_actual}'");
			if ($exists) {
				$message = 'No se puede asignar el consecutivo "'.$this->numero_actual.'" porque ya existe una factura asociado a este. ';
				$siguiente = EntityManager::get('Factura')->maximum(array('numero'))+1;
				if($siguiente>0){
					$message.='El siguiente consecutivo libre es: '.$siguiente;
				}
				$this->appendMessage(new ActiveRecordMessage($message, 'consecutivo'));
				return false;
			}
		}
	}*/
}

