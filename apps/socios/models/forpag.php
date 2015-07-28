<?php

class Forpag extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $forpag;

	/**
	 * @var string
	 */
	protected $detalle;

	/**
	 * @var integer
	 */
	protected $moneda;

	/**
	 * @var string
	 */
	protected $cuecon;

	/**
	 * @var string
	 */
	protected $tipfor;

	/**
	 * @var string
	 */
	protected $gencom;

	/**
	 * @var string
	 */
	protected $nitcom;

	/**
	 * @var string
	 */
	protected $comtar;

	/**
	 * @var string
	 */
	protected $cuecom;

	/**
	 * @var string
	 */
	protected $cencom;

	/**
	 * @var string
	 */
	protected $retfue;

	/**
	 * @var string
	 */
	protected $cuerfue;

	/**
	 * @var string
	 */
	protected $retiva;

	/**
	 * @var string
	 */
	protected $cueriva;

	/**
	 * @var string
	 */
	protected $retica;

	/**
	 * @var string
	 */
	protected $cuerica;

	/**
	 * @var string
	 */
	protected $muecaj;

	/**
	 * @var string
	 */
	protected $mueres;

	/**
	 * @var string
	 */
	protected $predeterminado;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo forpag
	 * @param integer $forpag
	 */
	public function setForpag($forpag){
		$this->forpag = $forpag;
	}

	/**
	 * Metodo para establecer el valor del campo detalle
	 * @param string $detalle
	 */
	public function setDetalle($detalle){
		$this->detalle = $detalle;
	}

	/**
	 * Metodo para establecer el valor del campo moneda
	 * @param integer $moneda
	 */
	public function setMoneda($moneda){
		$this->moneda = $moneda;
	}

	/**
	 * Metodo para establecer el valor del campo cuecon
	 * @param string $cuecon
	 */
	public function setCuecon($cuecon){
		$this->cuecon = $cuecon;
	}

	/**
	 * Metodo para establecer el valor del campo tipfor
	 * @param string $tipfor
	 */
	public function setTipfor($tipfor){
		$this->tipfor = $tipfor;
	}

	/**
	 * Metodo para establecer el valor del campo gencom
	 * @param string $gencom
	 */
	public function setGencom($gencom){
		$this->gencom = $gencom;
	}

	/**
	 * Metodo para establecer el valor del campo nitcom
	 * @param string $nitcom
	 */
	public function setNitcom($nitcom){
		$this->nitcom = $nitcom;
	}

	/**
	 * Metodo para establecer el valor del campo comtar
	 * @param string $comtar
	 */
	public function setComtar($comtar){
		$this->comtar = $comtar;
	}

	/**
	 * Metodo para establecer el valor del campo cuecom
	 * @param string $cuecom
	 */
	public function setCuecom($cuecom){
		$this->cuecom = $cuecom;
	}

	/**
	 * Metodo para establecer el valor del campo cencom
	 * @param string $cencom
	 */
	public function setCencom($cencom){
		$this->cencom = $cencom;
	}

	/**
	 * Metodo para establecer el valor del campo retfue
	 * @param string $retfue
	 */
	public function setRetfue($retfue){
		$this->retfue = $retfue;
	}

	/**
	 * Metodo para establecer el valor del campo cuerfue
	 * @param string $cuerfue
	 */
	public function setCuerfue($cuerfue){
		$this->cuerfue = $cuerfue;
	}

	/**
	 * Metodo para establecer el valor del campo retiva
	 * @param string $retiva
	 */
	public function setRetiva($retiva){
		$this->retiva = $retiva;
	}

	/**
	 * Metodo para establecer el valor del campo cueriva
	 * @param string $cueriva
	 */
	public function setCueriva($cueriva){
		$this->cueriva = $cueriva;
	}

	/**
	 * Metodo para establecer el valor del campo retica
	 * @param string $retica
	 */
	public function setRetica($retica){
		$this->retica = $retica;
	}

	/**
	 * Metodo para establecer el valor del campo cuerica
	 * @param string $cuerica
	 */
	public function setCuerica($cuerica){
		$this->cuerica = $cuerica;
	}

	/**
	 * Metodo para establecer el valor del campo muecaj
	 * @param string $muecaj
	 */
	public function setMuecaj($muecaj){
		$this->muecaj = $muecaj;
	}

	/**
	 * Metodo para establecer el valor del campo mueres
	 * @param string $mueres
	 */
	public function setMueres($mueres){
		$this->mueres = $mueres;
	}

	/**
	 * Metodo para establecer el valor del campo predeterminado
	 * @param string $predeterminado
	 */
	public function setPredeterminado($predeterminado){
		$this->predeterminado = $predeterminado;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo forpag
	 * @return integer
	 */
	public function getForpag(){
		return $this->forpag;
	}

	/**
	 * Devuelve el valor del campo detalle
	 * @return string
	 */
	public function getDetalle(){
		return $this->detalle;
	}

	/**
	 * Devuelve el valor del campo moneda
	 * @return integer
	 */
	public function getMoneda(){
		return $this->moneda;
	}

	/**
	 * Devuelve el valor del campo cuecon
	 * @return string
	 */
	public function getCuecon(){
		return $this->cuecon;
	}

	/**
	 * Devuelve el valor del campo tipfor
	 * @return string
	 */
	public function getTipfor(){
		return $this->tipfor;
	}

	/**
	 * Devuelve el valor del campo gencom
	 * @return string
	 */
	public function getGencom(){
		return $this->gencom;
	}

	/**
	 * Devuelve el valor del campo nitcom
	 * @return string
	 */
	public function getNitcom(){
		return $this->nitcom;
	}

	/**
	 * Devuelve el valor del campo comtar
	 * @return string
	 */
	public function getComtar(){
		return $this->comtar;
	}

	/**
	 * Devuelve el valor del campo cuecom
	 * @return string
	 */
	public function getCuecom(){
		return $this->cuecom;
	}

	/**
	 * Devuelve el valor del campo cencom
	 * @return string
	 */
	public function getCencom(){
		return $this->cencom;
	}

	/**
	 * Devuelve el valor del campo retfue
	 * @return string
	 */
	public function getRetfue(){
		return $this->retfue;
	}

	/**
	 * Devuelve el valor del campo cuerfue
	 * @return string
	 */
	public function getCuerfue(){
		return $this->cuerfue;
	}

	/**
	 * Devuelve el valor del campo retiva
	 * @return string
	 */
	public function getRetiva(){
		return $this->retiva;
	}

	/**
	 * Devuelve el valor del campo cueriva
	 * @return string
	 */
	public function getCueriva(){
		return $this->cueriva;
	}

	/**
	 * Devuelve el valor del campo retica
	 * @return string
	 */
	public function getRetica(){
		return $this->retica;
	}

	/**
	 * Devuelve el valor del campo cuerica
	 * @return string
	 */
	public function getCuerica(){
		return $this->cuerica;
	}

	/**
	 * Devuelve el valor del campo muecaj
	 * @return string
	 */
	public function getMuecaj(){
		return $this->muecaj;
	}

	/**
	 * Devuelve el valor del campo mueres
	 * @return string
	 */
	public function getMueres(){
		return $this->mueres;
	}

	/**
	 * Devuelve el valor del campo predeterminado
	 * @return string
	 */
	public function getPredeterminado(){
		return $this->predeterminado;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->front_db)){
			$this->setSchema($config->hfos->front_db);
		} else {
			$this->setSchema('hotel2');
		}
	}

}

