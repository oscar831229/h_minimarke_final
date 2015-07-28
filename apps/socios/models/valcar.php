<?php

class Valcar extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numfol;

	/**
	 * @var integer
	 */
	protected $numcue;

	/**
	 * @var integer
	 */
	protected $item;

	/**
	 * @var integer
	 */
	protected $codusu;

	/**
	 * @var integer
	 */
	protected $codcaj;

	/**
	 * @var string
	 */
	protected $fecha;

	/**
	 * @var integer
	 */
	protected $cantidad;

	/**
	 * @var integer
	 */
	protected $codcar;

	/**
	 * @var string
	 */
	protected $cladoc;

	/**
	 * @var string
	 */
	protected $numdoc;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $iva;

	/**
	 * @var string
	 */
	protected $impo;

	/**
	 * @var string
	 */
	protected $valser;

	/**
	 * @var string
	 */
	protected $valter;

	/**
	 * @var string
	 */
	protected $total;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var integer
	 */
	protected $oldfol;

	/**
	 * @var string
	 */
	protected $movcor;


	/**
	 * Metodo para establecer el valor del campo numfol
	 * @param integer $numfol
	 */
	public function setNumfol($numfol){
		$this->numfol = $numfol;
	}

	/**
	 * Metodo para establecer el valor del campo numcue
	 * @param integer $numcue
	 */
	public function setNumcue($numcue){
		$this->numcue = $numcue;
	}

	/**
	 * Metodo para establecer el valor del campo item
	 * @param integer $item
	 */
	public function setItem($item){
		$this->item = $item;
	}

	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
	}

	/**
	 * Metodo para establecer el valor del campo codcaj
	 * @param integer $codcaj
	 */
	public function setCodcaj($codcaj){
		$this->codcaj = $codcaj;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param string $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo cantidad
	 * @param integer $cantidad
	 */
	public function setCantidad($cantidad){
		$this->cantidad = $cantidad;
	}

	/**
	 * Metodo para establecer el valor del campo codcar
	 * @param integer $codcar
	 */
	public function setCodcar($codcar){
		$this->codcar = $codcar;
	}

	/**
	 * Metodo para establecer el valor del campo cladoc
	 * @param string $cladoc
	 */
	public function setCladoc($cladoc){
		$this->cladoc = $cladoc;
	}

	/**
	 * Metodo para establecer el valor del campo numdoc
	 * @param string $numdoc
	 */
	public function setNumdoc($numdoc){
		$this->numdoc = $numdoc;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo iva
	 * @param string $iva
	 */
	public function setIva($iva){
		$this->iva = $iva;
	}

	/**
	 * Metodo para establecer el valor del campo impo
	 * @param string $impo
	 */
	public function setImpo($impo){
		$this->impo = $impo;
	}

	/**
	 * Metodo para establecer el valor del campo valser
	 * @param string $valser
	 */
	public function setValser($valser){
		$this->valser = $valser;
	}

	/**
	 * Metodo para establecer el valor del campo valter
	 * @param string $valter
	 */
	public function setValter($valter){
		$this->valter = $valter;
	}

	/**
	 * Metodo para establecer el valor del campo total
	 * @param string $total
	 */
	public function setTotal($total){
		$this->total = $total;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo oldfol
	 * @param integer $oldfol
	 */
	public function setOldfol($oldfol){
		$this->oldfol = $oldfol;
	}

	/**
	 * Metodo para establecer el valor del campo movcor
	 * @param string $movcor
	 */
	public function setMovcor($movcor){
		$this->movcor = $movcor;
	}


	/**
	 * Devuelve el valor del campo numfol
	 * @return integer
	 */
	public function getNumfol(){
		return $this->numfol;
	}

	/**
	 * Devuelve el valor del campo numcue
	 * @return integer
	 */
	public function getNumcue(){
		return $this->numcue;
	}

	/**
	 * Devuelve el valor del campo item
	 * @return integer
	 */
	public function getItem(){
		return $this->item;
	}

	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
	}

	/**
	 * Devuelve el valor del campo codcaj
	 * @return integer
	 */
	public function getCodcaj(){
		return $this->codcaj;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return string
	 */
	public function getFecha(){
		return $this->fecha;
	}

	/**
	 * Devuelve el valor del campo cantidad
	 * @return integer
	 */
	public function getCantidad(){
		return $this->cantidad;
	}

	/**
	 * Devuelve el valor del campo codcar
	 * @return integer
	 */
	public function getCodcar(){
		return $this->codcar;
	}

	/**
	 * Devuelve el valor del campo cladoc
	 * @return string
	 */
	public function getCladoc(){
		return $this->cladoc;
	}

	/**
	 * Devuelve el valor del campo numdoc
	 * @return string
	 */
	public function getNumdoc(){
		return $this->numdoc;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo iva
	 * @return string
	 */
	public function getIva(){
		return $this->iva;
	}

	/**
	 * Devuelve el valor del campo impo
	 * @return string
	 */
	public function getImpo(){
		return $this->impo;
	}

	/**
	 * Devuelve el valor del campo valser
	 * @return string
	 */
	public function getValser(){
		return $this->valser;
	}

	/**
	 * Devuelve el valor del campo valter
	 * @return string
	 */
	public function getValter(){
		return $this->valter;
	}

	/**
	 * Devuelve el valor del campo total
	 * @return string
	 */
	public function getTotal(){
		return $this->total;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo oldfol
	 * @return integer
	 */
	public function getOldfol(){
		return $this->oldfol;
	}

	/**
	 * Devuelve el valor del campo movcor
	 * @return string
	 */
	public function getMovcor(){
		return $this->movcor;
	}

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize()
	{
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->front_db)){
			$this->setSchema($config->hfos->front_db);
		} else {
			$this->setSchema('hotel2');
		}
		$this->setSource('valcar');
	}

}

