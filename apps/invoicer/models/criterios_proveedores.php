<?php

class CriteriosProveedores extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $almacen;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var integer
	 */
	protected $criterios_id;

	/**
	 * @var integer
	 */
	protected $puntaje;


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
	 * Metodo para establecer el valor del campo almacen
	 * @param integer $almacen
	 */
	public function setAlmacen($almacen){
		$this->almacen = $almacen;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo criterios_id
	 * @param integer $criterios_id
	 */
	public function setCriteriosId($criterios_id){
		$this->criterios_id = $criterios_id;
	}

	/**
	 * Metodo para establecer el valor del campo puntaje
	 * @param integer $puntaje
	 */
	public function setPuntaje($puntaje){
		$this->puntaje = $puntaje;
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
	 * Devuelve el valor del campo almacen
	 * @return integer
	 */
	public function getAlmacen(){
		return $this->almacen;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo criterios_id
	 * @return integer
	 */
	public function getCriteriosId(){
		return $this->criterios_id;
	}

	/**
	 * Devuelve el valor del campo puntaje
	 * @return integer
	 */
	public function getPuntaje(){
		return $this->puntaje;
	}

}

