<?php

class Parsis extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $usecen;

	/**
	 * @var string
	 */
	protected $red;

	/**
	 * @var integer
	 */
	protected $timina;

	/**
	 * @var integer
	 */
	protected $buztam;

	/**
	 * @var string
	 */
	protected $comven;

	/**
	 * @var string
	 */
	protected $coming;

	/**
	 * @var string
	 */
	protected $cueven;

	/**
	 * @var string
	 */
	protected $cenven;

	/**
	 * @var string
	 */
	protected $cuecaj;

	/**
	 * @var string
	 */
	protected $cencaj;

	/**
	 * @var string
	 */
	protected $cuecre;

	/**
	 * @var string
	 */
	protected $cencre;

	/**
	 * @var string
	 */
	protected $tipint;

	/**
	 * @var string
	 */
	protected $doccue;

	/**
	 * @var string
	 */
	protected $doccon;

	/**
	 * @var integer
	 */
	protected $numcar;

	/**
	 * @var string
	 */
	protected $docdep;

	/**
	 * @var string
	 */
	protected $docfac;

	/**
	 * @var string
	 */
	protected $docrec;

	/**
	 * @var string
	 */
	protected $docegr;

	/**
	 * @var string
	 */
	protected $doctel;

	/**
	 * @var integer
	 */
	protected $depsin;

	/**
	 * @var integer
	 */
	protected $depapl;

	/**
	 * @var integer
	 */
	protected $depdev;

	/**
	 * @var integer
	 */
	protected $evesin;

	/**
	 * @var integer
	 */
	protected $eveapl;

	/**
	 * @var integer
	 */
	protected $concar;

	/**
	 * @var integer
	 */
	protected $conaju;

	/**
	 * @var integer
	 */
	protected $pagcon;

	/**
	 * @var integer
	 */
	protected $abopre;

	/**
	 * @var integer
	 */
	protected $motcan;

	/**
	 * @var integer
	 */
	protected $motdes;

	/**
	 * @var string
	 */
	protected $cliext;

	/**
	 * @var string
	 */
	protected $empext;

	/**
	 * @var string
	 */
	protected $habres;

	/**
	 * @var string
	 */
	protected $preres;

	/**
	 * @var string
	 */
	protected $notcre;

	/**
	 * @var string
	 */
	protected $ivatar;

	/**
	 * @var integer
	 */
	protected $usurep;


	/**
	 * Metodo para establecer el valor del campo numero
	 * @param string $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo usecen
	 * @param string $usecen
	 */
	public function setUsecen($usecen){
		$this->usecen = $usecen;
	}

	/**
	 * Metodo para establecer el valor del campo red
	 * @param string $red
	 */
	public function setRed($red){
		$this->red = $red;
	}

	/**
	 * Metodo para establecer el valor del campo timina
	 * @param integer $timina
	 */
	public function setTimina($timina){
		$this->timina = $timina;
	}

	/**
	 * Metodo para establecer el valor del campo buztam
	 * @param integer $buztam
	 */
	public function setBuztam($buztam){
		$this->buztam = $buztam;
	}

	/**
	 * Metodo para establecer el valor del campo comven
	 * @param string $comven
	 */
	public function setComven($comven){
		$this->comven = $comven;
	}

	/**
	 * Metodo para establecer el valor del campo coming
	 * @param string $coming
	 */
	public function setComing($coming){
		$this->coming = $coming;
	}

	/**
	 * Metodo para establecer el valor del campo cueven
	 * @param string $cueven
	 */
	public function setCueven($cueven){
		$this->cueven = $cueven;
	}

	/**
	 * Metodo para establecer el valor del campo cenven
	 * @param string $cenven
	 */
	public function setCenven($cenven){
		$this->cenven = $cenven;
	}

	/**
	 * Metodo para establecer el valor del campo cuecaj
	 * @param string $cuecaj
	 */
	public function setCuecaj($cuecaj){
		$this->cuecaj = $cuecaj;
	}

	/**
	 * Metodo para establecer el valor del campo cencaj
	 * @param string $cencaj
	 */
	public function setCencaj($cencaj){
		$this->cencaj = $cencaj;
	}

	/**
	 * Metodo para establecer el valor del campo cuecre
	 * @param string $cuecre
	 */
	public function setCuecre($cuecre){
		$this->cuecre = $cuecre;
	}

	/**
	 * Metodo para establecer el valor del campo cencre
	 * @param string $cencre
	 */
	public function setCencre($cencre){
		$this->cencre = $cencre;
	}

	/**
	 * Metodo para establecer el valor del campo tipint
	 * @param string $tipint
	 */
	public function setTipint($tipint){
		$this->tipint = $tipint;
	}

	/**
	 * Metodo para establecer el valor del campo doccue
	 * @param string $doccue
	 */
	public function setDoccue($doccue){
		$this->doccue = $doccue;
	}

	/**
	 * Metodo para establecer el valor del campo doccon
	 * @param string $doccon
	 */
	public function setDoccon($doccon){
		$this->doccon = $doccon;
	}

	/**
	 * Metodo para establecer el valor del campo numcar
	 * @param integer $numcar
	 */
	public function setNumcar($numcar){
		$this->numcar = $numcar;
	}

	/**
	 * Metodo para establecer el valor del campo docdep
	 * @param string $docdep
	 */
	public function setDocdep($docdep){
		$this->docdep = $docdep;
	}

	/**
	 * Metodo para establecer el valor del campo docfac
	 * @param string $docfac
	 */
	public function setDocfac($docfac){
		$this->docfac = $docfac;
	}

	/**
	 * Metodo para establecer el valor del campo docrec
	 * @param string $docrec
	 */
	public function setDocrec($docrec){
		$this->docrec = $docrec;
	}

	/**
	 * Metodo para establecer el valor del campo docegr
	 * @param string $docegr
	 */
	public function setDocegr($docegr){
		$this->docegr = $docegr;
	}

	/**
	 * Metodo para establecer el valor del campo doctel
	 * @param string $doctel
	 */
	public function setDoctel($doctel){
		$this->doctel = $doctel;
	}

	/**
	 * Metodo para establecer el valor del campo depsin
	 * @param integer $depsin
	 */
	public function setDepsin($depsin){
		$this->depsin = $depsin;
	}

	/**
	 * Metodo para establecer el valor del campo depapl
	 * @param integer $depapl
	 */
	public function setDepapl($depapl){
		$this->depapl = $depapl;
	}

	/**
	 * Metodo para establecer el valor del campo depdev
	 * @param integer $depdev
	 */
	public function setDepdev($depdev){
		$this->depdev = $depdev;
	}

	/**
	 * Metodo para establecer el valor del campo evesin
	 * @param integer $evesin
	 */
	public function setEvesin($evesin){
		$this->evesin = $evesin;
	}

	/**
	 * Metodo para establecer el valor del campo eveapl
	 * @param integer $eveapl
	 */
	public function setEveapl($eveapl){
		$this->eveapl = $eveapl;
	}

	/**
	 * Metodo para establecer el valor del campo concar
	 * @param integer $concar
	 */
	public function setConcar($concar){
		$this->concar = $concar;
	}

	/**
	 * Metodo para establecer el valor del campo conaju
	 * @param integer $conaju
	 */
	public function setConaju($conaju){
		$this->conaju = $conaju;
	}

	/**
	 * Metodo para establecer el valor del campo pagcon
	 * @param integer $pagcon
	 */
	public function setPagcon($pagcon){
		$this->pagcon = $pagcon;
	}

	/**
	 * Metodo para establecer el valor del campo abopre
	 * @param integer $abopre
	 */
	public function setAbopre($abopre){
		$this->abopre = $abopre;
	}

	/**
	 * Metodo para establecer el valor del campo motcan
	 * @param integer $motcan
	 */
	public function setMotcan($motcan){
		$this->motcan = $motcan;
	}

	/**
	 * Metodo para establecer el valor del campo motdes
	 * @param integer $motdes
	 */
	public function setMotdes($motdes){
		$this->motdes = $motdes;
	}

	/**
	 * Metodo para establecer el valor del campo cliext
	 * @param string $cliext
	 */
	public function setCliext($cliext){
		$this->cliext = $cliext;
	}

	/**
	 * Metodo para establecer el valor del campo empext
	 * @param string $empext
	 */
	public function setEmpext($empext){
		$this->empext = $empext;
	}

	/**
	 * Metodo para establecer el valor del campo habres
	 * @param string $habres
	 */
	public function setHabres($habres){
		$this->habres = $habres;
	}

	/**
	 * Metodo para establecer el valor del campo preres
	 * @param string $preres
	 */
	public function setPreres($preres){
		$this->preres = $preres;
	}

	/**
	 * Metodo para establecer el valor del campo notcre
	 * @param string $notcre
	 */
	public function setNotcre($notcre){
		$this->notcre = $notcre;
	}

	/**
	 * Metodo para establecer el valor del campo ivatar
	 * @param string $ivatar
	 */
	public function setIvatar($ivatar){
		$this->ivatar = $ivatar;
	}

	/**
	 * Metodo para establecer el valor del campo usurep
	 * @param integer $usurep
	 */
	public function setUsurep($usurep){
		$this->usurep = $usurep;
	}


	/**
	 * Devuelve el valor del campo numero
	 * @return string
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo usecen
	 * @return string
	 */
	public function getUsecen(){
		return $this->usecen;
	}

	/**
	 * Devuelve el valor del campo red
	 * @return string
	 */
	public function getRed(){
		return $this->red;
	}

	/**
	 * Devuelve el valor del campo timina
	 * @return integer
	 */
	public function getTimina(){
		return $this->timina;
	}

	/**
	 * Devuelve el valor del campo buztam
	 * @return integer
	 */
	public function getBuztam(){
		return $this->buztam;
	}

	/**
	 * Devuelve el valor del campo comven
	 * @return string
	 */
	public function getComven(){
		return $this->comven;
	}

	/**
	 * Devuelve el valor del campo coming
	 * @return string
	 */
	public function getComing(){
		return $this->coming;
	}

	/**
	 * Devuelve el valor del campo cueven
	 * @return string
	 */
	public function getCueven(){
		return $this->cueven;
	}

	/**
	 * Devuelve el valor del campo cenven
	 * @return string
	 */
	public function getCenven(){
		return $this->cenven;
	}

	/**
	 * Devuelve el valor del campo cuecaj
	 * @return string
	 */
	public function getCuecaj(){
		return $this->cuecaj;
	}

	/**
	 * Devuelve el valor del campo cencaj
	 * @return string
	 */
	public function getCencaj(){
		return $this->cencaj;
	}

	/**
	 * Devuelve el valor del campo cuecre
	 * @return string
	 */
	public function getCuecre(){
		return $this->cuecre;
	}

	/**
	 * Devuelve el valor del campo cencre
	 * @return string
	 */
	public function getCencre(){
		return $this->cencre;
	}

	/**
	 * Devuelve el valor del campo tipint
	 * @return string
	 */
	public function getTipint(){
		return $this->tipint;
	}

	/**
	 * Devuelve el valor del campo doccue
	 * @return string
	 */
	public function getDoccue(){
		return $this->doccue;
	}

	/**
	 * Devuelve el valor del campo doccon
	 * @return string
	 */
	public function getDoccon(){
		return $this->doccon;
	}

	/**
	 * Devuelve el valor del campo numcar
	 * @return integer
	 */
	public function getNumcar(){
		return $this->numcar;
	}

	/**
	 * Devuelve el valor del campo docdep
	 * @return string
	 */
	public function getDocdep(){
		return $this->docdep;
	}

	/**
	 * Devuelve el valor del campo docfac
	 * @return string
	 */
	public function getDocfac(){
		return $this->docfac;
	}

	/**
	 * Devuelve el valor del campo docrec
	 * @return string
	 */
	public function getDocrec(){
		return $this->docrec;
	}

	/**
	 * Devuelve el valor del campo docegr
	 * @return string
	 */
	public function getDocegr(){
		return $this->docegr;
	}

	/**
	 * Devuelve el valor del campo doctel
	 * @return string
	 */
	public function getDoctel(){
		return $this->doctel;
	}

	/**
	 * Devuelve el valor del campo depsin
	 * @return integer
	 */
	public function getDepsin(){
		return $this->depsin;
	}

	/**
	 * Devuelve el valor del campo depapl
	 * @return integer
	 */
	public function getDepapl(){
		return $this->depapl;
	}

	/**
	 * Devuelve el valor del campo depdev
	 * @return integer
	 */
	public function getDepdev(){
		return $this->depdev;
	}

	/**
	 * Devuelve el valor del campo evesin
	 * @return integer
	 */
	public function getEvesin(){
		return $this->evesin;
	}

	/**
	 * Devuelve el valor del campo eveapl
	 * @return integer
	 */
	public function getEveapl(){
		return $this->eveapl;
	}

	/**
	 * Devuelve el valor del campo concar
	 * @return integer
	 */
	public function getConcar(){
		return $this->concar;
	}

	/**
	 * Devuelve el valor del campo conaju
	 * @return integer
	 */
	public function getConaju(){
		return $this->conaju;
	}

	/**
	 * Devuelve el valor del campo pagcon
	 * @return integer
	 */
	public function getPagcon(){
		return $this->pagcon;
	}

	/**
	 * Devuelve el valor del campo abopre
	 * @return integer
	 */
	public function getAbopre(){
		return $this->abopre;
	}

	/**
	 * Devuelve el valor del campo motcan
	 * @return integer
	 */
	public function getMotcan(){
		return $this->motcan;
	}

	/**
	 * Devuelve el valor del campo motdes
	 * @return integer
	 */
	public function getMotdes(){
		return $this->motdes;
	}

	/**
	 * Devuelve el valor del campo cliext
	 * @return string
	 */
	public function getCliext(){
		return $this->cliext;
	}

	/**
	 * Devuelve el valor del campo empext
	 * @return string
	 */
	public function getEmpext(){
		return $this->empext;
	}

	/**
	 * Devuelve el valor del campo habres
	 * @return string
	 */
	public function getHabres(){
		return $this->habres;
	}

	/**
	 * Devuelve el valor del campo preres
	 * @return string
	 */
	public function getPreres(){
		return $this->preres;
	}

	/**
	 * Devuelve el valor del campo notcre
	 * @return string
	 */
	public function getNotcre(){
		return $this->notcre;
	}

	/**
	 * Devuelve el valor del campo ivatar
	 * @return string
	 */
	public function getIvatar(){
		return $this->ivatar;
	}

	/**
	 * Devuelve el valor del campo usurep
	 * @return integer
	 */
	public function getUsurep(){
		return $this->usurep;
	}

}

