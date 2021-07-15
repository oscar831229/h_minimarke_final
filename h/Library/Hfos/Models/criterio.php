<?php

class Criterio extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var string
	 */
	protected $almacen;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $sc;

	/**
	 * @var string
	 */
	protected $pr;

	/**
	 * @var string
	 */
	protected $maj;

	/**
	 * @var string
	 */
	protected $tra;

	/**
	 * @var string
	 */
	protected $up;

	/**
	 * @var string
	 */
	protected $cte;

	/**
	 * @var string
	 */
	protected $fra;

	/**
	 * @var string
	 */
	protected $pd;


	/**
	 * Metodo para establecer el valor del campo comprob
	 * @param string $comprob
	 */
	public function setComprob($comprob){
		$this->comprob = $comprob;
	}

	/**
	 * Metodo para establecer el valor del campo almacen
	 * @param string $almacen
	 */
	public function setAlmacen($almacen){
		$this->almacen = $almacen;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo sc
	 * @param string $sc
	 */
	public function setSc($sc){
		$this->sc = $sc;
	}

	/**
	 * Metodo para establecer el valor del campo pr
	 * @param string $pr
	 */
	public function setPr($pr){
		$this->pr = $pr;
	}

	/**
	 * Metodo para establecer el valor del campo maj
	 * @param string $maj
	 */
	public function setMaj($maj){
		$this->maj = $maj;
	}

	/**
	 * Metodo para establecer el valor del campo tra
	 * @param string $tra
	 */
	public function setTra($tra){
		$this->tra = $tra;
	}

	/**
	 * Metodo para establecer el valor del campo up
	 * @param string $up
	 */
	public function setUp($up){
		$this->up = $up;
	}

	/**
	 * Metodo para establecer el valor del campo cte
	 * @param string $cte
	 */
	public function setCte($cte){
		$this->cte = $cte;
	}

	/**
	 * Metodo para establecer el valor del campo fra
	 * @param string $fra
	 */
	public function setFra($fra){
		$this->fra = $fra;
	}

	/**
	 * Metodo para establecer el valor del campo pd
	 * @param string $pd
	 */
	public function setPd($pd){
		$this->pd = $pd;
	}


	/**
	 * Devuelve el valor del campo comprob
	 * @return string
	 */
	public function getComprob(){
		return $this->comprob;
	}

	/**
	 * Devuelve el valor del campo almacen
	 * @return string
	 */
	public function getAlmacen(){
		return $this->almacen;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo sc
	 * @return string
	 */
	public function getSc(){
		return $this->sc;
	}

	/**
	 * Devuelve el valor del campo pr
	 * @return string
	 */
	public function getPr(){
		return $this->pr;
	}

	/**
	 * Devuelve el valor del campo maj
	 * @return string
	 */
	public function getMaj(){
		return $this->maj;
	}

	/**
	 * Devuelve el valor del campo tra
	 * @return string
	 */
	public function getTra(){
		return $this->tra;
	}

	/**
	 * Devuelve el valor del campo up
	 * @return string
	 */
	public function getUp(){
		return $this->up;
	}

	/**
	 * Devuelve el valor del campo cte
	 * @return string
	 */
	public function getCte(){
		return $this->cte;
	}

	/**
	 * Devuelve el valor del campo fra
	 * @return string
	 */
	public function getFra(){
		return $this->fra;
	}

	/**
	 * Devuelve el valor del campo pd
	 * @return string
	 */
	public function getPd(){
		return $this->pd;
	}

}

