<?php

class Reserva extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numres;

	/**
	 * @var string
	 */
	protected $referencia;

	/**
	 * @var integer
	 */
	protected $tipdoc;

	/**
	 * @var string
	 */
	protected $cedula;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var integer
	 */
	protected $locpro;

	/**
	 * @var integer
	 */
	protected $codpai;

	/**
	 * @var integer
	 */
	protected $codciu;

	/**
	 * @var string
	 */
	protected $pordes;

	/**
	 * @var string
	 */
	protected $numhab;

	/**
	 * @var integer
	 */
	protected $codtra;

	/**
	 * @var integer
	 */
	protected $trasal;

	/**
	 * @var integer
	 */
	protected $tipres;

	/**
	 * @var integer
	 */
	protected $codusu;

	/**
	 * @var Date
	 */
	protected $fecres;

	/**
	 * @var Date
	 */
	protected $feclle;

	/**
	 * @var Date
	 */
	protected $fecsal;

	/**
	 * @var string
	 */
	protected $hora;

	/**
	 * @var integer
	 */
	protected $numadu;

	/**
	 * @var integer
	 */
	protected $numnin;

	/**
	 * @var integer
	 */
	protected $numinf;

	/**
	 * @var string
	 */
	protected $observacion;

	/**
	 * @var integer
	 */
	protected $numgru;

	/**
	 * @var integer
	 */
	protected $numpre;

	/**
	 * @var string
	 */
	protected $carta;

	/**
	 * @var string
	 */
	protected $habfij;

	/**
	 * @var string
	 */
	protected $solicitada;

	/**
	 * @var integer
	 */
	protected $forpag;

	/**
	 * @var Date
	 */
	protected $fecest;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo numres
	 * @param integer $numres
	 */
	public function setNumres($numres){
		$this->numres = $numres;
	}

	/**
	 * Metodo para establecer el valor del campo referencia
	 * @param string $referencia
	 */
	public function setReferencia($referencia){
		$this->referencia = $referencia;
	}

	/**
	 * Metodo para establecer el valor del campo tipdoc
	 * @param integer $tipdoc
	 */
	public function setTipdoc($tipdoc){
		$this->tipdoc = $tipdoc;
	}

	/**
	 * Metodo para establecer el valor del campo cedula
	 * @param string $cedula
	 */
	public function setCedula($cedula){
		$this->cedula = $cedula;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo locpro
	 * @param integer $locpro
	 */
	public function setLocpro($locpro){
		$this->locpro = $locpro;
	}

	/**
	 * Metodo para establecer el valor del campo codpai
	 * @param integer $codpai
	 */
	public function setCodpai($codpai){
		$this->codpai = $codpai;
	}

	/**
	 * Metodo para establecer el valor del campo codciu
	 * @param integer $codciu
	 */
	public function setCodciu($codciu){
		$this->codciu = $codciu;
	}

	/**
	 * Metodo para establecer el valor del campo pordes
	 * @param string $pordes
	 */
	public function setPordes($pordes){
		$this->pordes = $pordes;
	}

	/**
	 * Metodo para establecer el valor del campo numhab
	 * @param string $numhab
	 */
	public function setNumhab($numhab){
		$this->numhab = $numhab;
	}

	/**
	 * Metodo para establecer el valor del campo codtra
	 * @param integer $codtra
	 */
	public function setCodtra($codtra){
		$this->codtra = $codtra;
	}

	/**
	 * Metodo para establecer el valor del campo trasal
	 * @param integer $trasal
	 */
	public function setTrasal($trasal){
		$this->trasal = $trasal;
	}

	/**
	 * Metodo para establecer el valor del campo tipres
	 * @param integer $tipres
	 */
	public function setTipres($tipres){
		$this->tipres = $tipres;
	}

	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
	}

	/**
	 * Metodo para establecer el valor del campo fecres
	 * @param Date $fecres
	 */
	public function setFecres($fecres){
		$this->fecres = $fecres;
	}

	/**
	 * Metodo para establecer el valor del campo feclle
	 * @param Date $feclle
	 */
	public function setFeclle($feclle){
		$this->feclle = $feclle;
	}

	/**
	 * Metodo para establecer el valor del campo fecsal
	 * @param Date $fecsal
	 */
	public function setFecsal($fecsal){
		$this->fecsal = $fecsal;
	}

	/**
	 * Metodo para establecer el valor del campo hora
	 * @param string $hora
	 */
	public function setHora($hora){
		$this->hora = $hora;
	}

	/**
	 * Metodo para establecer el valor del campo numadu
	 * @param integer $numadu
	 */
	public function setNumadu($numadu){
		$this->numadu = $numadu;
	}

	/**
	 * Metodo para establecer el valor del campo numnin
	 * @param integer $numnin
	 */
	public function setNumnin($numnin){
		$this->numnin = $numnin;
	}

	/**
	 * Metodo para establecer el valor del campo numinf
	 * @param integer $numinf
	 */
	public function setNuminf($numinf){
		$this->numinf = $numinf;
	}

	/**
	 * Metodo para establecer el valor del campo observacion
	 * @param string $observacion
	 */
	public function setObservacion($observacion){
		$this->observacion = $observacion;
	}

	/**
	 * Metodo para establecer el valor del campo numgru
	 * @param integer $numgru
	 */
	public function setNumgru($numgru){
		$this->numgru = $numgru;
	}

	/**
	 * Metodo para establecer el valor del campo numpre
	 * @param integer $numpre
	 */
	public function setNumpre($numpre){
		$this->numpre = $numpre;
	}

	/**
	 * Metodo para establecer el valor del campo carta
	 * @param string $carta
	 */
	public function setCarta($carta){
		$this->carta = $carta;
	}

	/**
	 * Metodo para establecer el valor del campo habfij
	 * @param string $habfij
	 */
	public function setHabfij($habfij){
		$this->habfij = $habfij;
	}

	/**
	 * Metodo para establecer el valor del campo solicitada
	 * @param string $solicitada
	 */
	public function setSolicitada($solicitada){
		$this->solicitada = $solicitada;
	}

	/**
	 * Metodo para establecer el valor del campo forpag
	 * @param integer $forpag
	 */
	public function setForpag($forpag){
		$this->forpag = $forpag;
	}

	/**
	 * Metodo para establecer el valor del campo fecest
	 * @param Date $fecest
	 */
	public function setFecest($fecest){
		$this->fecest = $fecest;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo numres
	 * @return integer
	 */
	public function getNumres(){
		return $this->numres;
	}

	/**
	 * Devuelve el valor del campo referencia
	 * @return string
	 */
	public function getReferencia(){
		return $this->referencia;
	}

	/**
	 * Devuelve el valor del campo tipdoc
	 * @return integer
	 */
	public function getTipdoc(){
		return $this->tipdoc;
	}

	/**
	 * Devuelve el valor del campo cedula
	 * @return string
	 */
	public function getCedula(){
		return $this->cedula;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo locpro
	 * @return integer
	 */
	public function getLocpro(){
		return $this->locpro;
	}

	/**
	 * Devuelve el valor del campo codpai
	 * @return integer
	 */
	public function getCodpai(){
		return $this->codpai;
	}

	/**
	 * Devuelve el valor del campo codciu
	 * @return integer
	 */
	public function getCodciu(){
		return $this->codciu;
	}

	/**
	 * Devuelve el valor del campo pordes
	 * @return string
	 */
	public function getPordes(){
		return $this->pordes;
	}

	/**
	 * Devuelve el valor del campo numhab
	 * @return string
	 */
	public function getNumhab(){
		return $this->numhab;
	}

	/**
	 * Devuelve el valor del campo codtra
	 * @return integer
	 */
	public function getCodtra(){
		return $this->codtra;
	}

	/**
	 * Devuelve el valor del campo trasal
	 * @return integer
	 */
	public function getTrasal(){
		return $this->trasal;
	}

	/**
	 * Devuelve el valor del campo tipres
	 * @return integer
	 */
	public function getTipres(){
		return $this->tipres;
	}

	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
	}

	/**
	 * Devuelve el valor del campo fecres
	 * @return Date
	 */
	public function getFecres(){
		return new Date($this->fecres);
	}

	/**
	 * Devuelve el valor del campo feclle
	 * @return Date
	 */
	public function getFeclle(){
		return new Date($this->feclle);
	}

	/**
	 * Devuelve el valor del campo fecsal
	 * @return Date
	 */
	public function getFecsal(){
		return new Date($this->fecsal);
	}

	/**
	 * Devuelve el valor del campo hora
	 * @return string
	 */
	public function getHora(){
		return $this->hora;
	}

	/**
	 * Devuelve el valor del campo numadu
	 * @return integer
	 */
	public function getNumadu(){
		return $this->numadu;
	}

	/**
	 * Devuelve el valor del campo numnin
	 * @return integer
	 */
	public function getNumnin(){
		return $this->numnin;
	}

	/**
	 * Devuelve el valor del campo numinf
	 * @return integer
	 */
	public function getNuminf(){
		return $this->numinf;
	}

	/**
	 * Devuelve el valor del campo observacion
	 * @return string
	 */
	public function getObservacion(){
		return $this->observacion;
	}

	/**
	 * Devuelve el valor del campo numgru
	 * @return integer
	 */
	public function getNumgru(){
		return $this->numgru;
	}

	/**
	 * Devuelve el valor del campo numpre
	 * @return integer
	 */
	public function getNumpre(){
		return $this->numpre;
	}

	/**
	 * Devuelve el valor del campo carta
	 * @return string
	 */
	public function getCarta(){
		return $this->carta;
	}

	/**
	 * Devuelve el valor del campo habfij
	 * @return string
	 */
	public function getHabfij(){
		return $this->habfij;
	}

	/**
	 * Devuelve el valor del campo solicitada
	 * @return string
	 */
	public function getSolicitada(){
		return $this->solicitada;
	}

	/**
	 * Devuelve el valor del campo forpag
	 * @return integer
	 */
	public function getForpag(){
		return $this->forpag;
	}

	/**
	 * Devuelve el valor del campo fecest
	 * @return Date
	 */
	public function getFecest(){
		return new Date($this->fecest);
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

}

