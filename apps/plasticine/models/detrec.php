<?php

class Detrec extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numrec;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $forpag;

	/**
	 * @var integer
	 */
	protected $numfor;

	/**
	 * @var Date
	 */
	protected $fecven;

	/**
	 * @var string
	 */
	protected $ivarep;

	/**
	 * @var string
	 */
	protected $valorm;

	/**
	 * @var string
	 */
	protected $valor;


	/**
	 * Metodo para establecer el valor del campo numrec
	 * @param integer $numrec
	 */
	public function setNumrec($numrec){
		$this->numrec = $numrec;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo forpag
	 * @param integer $forpag
	 */
	public function setForpag($forpag){
		$this->forpag = $forpag;
	}

	/**
	 * Metodo para establecer el valor del campo numfor
	 * @param integer $numfor
	 */
	public function setNumfor($numfor){
		$this->numfor = $numfor;
	}

	/**
	 * Metodo para establecer el valor del campo fecven
	 * @param Date $fecven
	 */
	public function setFecven($fecven){
		$this->fecven = $fecven;
	}

	/**
	 * Metodo para establecer el valor del campo ivarep
	 * @param string $ivarep
	 */
	public function setIvarep($ivarep){
		$this->ivarep = $ivarep;
	}

	/**
	 * Metodo para establecer el valor del campo valorm
	 * @param string $valorm
	 */
	public function setValorm($valorm){
		$this->valorm = $valorm;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}


	/**
	 * Devuelve el valor del campo numrec
	 * @return integer
	 */
	public function getNumrec(){
		return $this->numrec;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo forpag
	 * @return integer
	 */
	public function getForpag(){
		return $this->forpag;
	}

	/**
	 * Devuelve el valor del campo numfor
	 * @return integer
	 */
	public function getNumfor(){
		return $this->numfor;
	}

	/**
	 * Devuelve el valor del campo fecven
	 * @return Date
	 */
	public function getFecven(){
		if($this->fecven){
			return new Date($this->fecven);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo ivarep
	 * @return string
	 */
	public function getIvarep(){
		return $this->ivarep;
	}

	/**
	 * Devuelve el valor del campo valorm
	 * @return string
	 */
	public function getValorm(){
		return $this->valorm;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

}

