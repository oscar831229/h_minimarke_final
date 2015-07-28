<?php

class FacturasHotel extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $prefac;

	/**
	 * @var integer
	 */
	protected $numfac;

	/**
	 * @var integer
	 */
	protected $numfol;

	/**
	 * @var integer
	 */
	protected $numcue;

	/**
	 * @var string
	 */
	protected $nomcad;

	/**
	 * @var string
	 */
	protected $nithot;

	/**
	 * @var string
	 */
	protected $hotel;

	/**
	 * @var string
	 */
	protected $resfac;

	/**
	 * @var Date
	 */
	protected $fecres;

	/**
	 * @var integer
	 */
	protected $numini;

	/**
	 * @var integer
	 */
	protected $numfin;

	/**
	 * @var string
	 */
	protected $notreg;

	/**
	 * @var string
	 */
	protected $notica;

	/**
	 * @var Date
	 */
	protected $fecfac;

	/**
	 * @var string
	 */
	protected $horfac;

	/**
	 * @var string
	 */
	protected $nombre;

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
	protected $numhab;

	/**
	 * @var integer
	 */
	protected $dif;

	/**
	 * @var string
	 */
	protected $direccion;

	/**
	 * @var string
	 */
	protected $ciudad;

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
	protected $caja;

	/**
	 * @var string
	 */
	protected $planes;

	/**
	 * @var string
	 */
	protected $huesped;

	/**
	 * @var string
	 */
	protected $empresa;

	/**
	 * @var integer
	 */
	protected $numadu;

	/**
	 * @var integer
	 */
	protected $numnin;

	/**
	 * @var string
	 */
	protected $tocanogr;

	/**
	 * @var string
	 */
	protected $tocagr;

	/**
	 * @var string
	 */
	protected $tocagr16;

	/**
	 * @var string
	 */
	protected $tocagr10;

	/**
	 * @var string
	 */
	protected $totiva16;

	/**
	 * @var string
	 */
	protected $totiva10;

	/**
	 * @var string
	 */
	protected $totiva;

	/**
	 * @var string
	 */
	protected $totimp;

	/**
	 * @var string
	 */
	protected $totabo;

	/**
	 * @var string
	 */
	protected $total;

	/**
	 * @var string
	 */
	protected $saldo;

	/**
	 * @var string
	 */
	protected $basret;

	/**
	 * @var string
	 */
	protected $propina;

	/**
	 * @var string
	 */
	protected $nota;

	/**
	 * @var string
	 */
	protected $formas;

	/**
	 * @var Date
	 */
	protected $fecven;

	/**
	 * @var Date
	 */
	protected $fecgen;

	/**
	 * @var string
	 */
	protected $tipres;

	/**
	 * @var string
	 */
	protected $facalo;

	/**
	 * @var string
	 */
	protected $quien;

	/**
	 * @var integer
	 */
	protected $genusu;

	/**
	 * @var integer
	 */
	protected $canfac;

	/**
	 * @var integer
	 */
	protected $codusu;

	/**
	 * @var string
	 */
	protected $svf;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo prefac
	 * @param string $prefac
	 */
	public function setPrefac($prefac){
		$this->prefac = $prefac;
	}

	/**
	 * Metodo para establecer el valor del campo numfac
	 * @param integer $numfac
	 */
	public function setNumfac($numfac){
		$this->numfac = $numfac;
	}

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
	 * Metodo para establecer el valor del campo nomcad
	 * @param string $nomcad
	 */
	public function setNomcad($nomcad){
		$this->nomcad = $nomcad;
	}

	/**
	 * Metodo para establecer el valor del campo nithot
	 * @param string $nithot
	 */
	public function setNithot($nithot){
		$this->nithot = $nithot;
	}

	/**
	 * Metodo para establecer el valor del campo hotel
	 * @param string $hotel
	 */
	public function setHotel($hotel){
		$this->hotel = $hotel;
	}

	/**
	 * Metodo para establecer el valor del campo resfac
	 * @param string $resfac
	 */
	public function setResfac($resfac){
		$this->resfac = $resfac;
	}

	/**
	 * Metodo para establecer el valor del campo fecres
	 * @param Date $fecres
	 */
	public function setFecres($fecres){
		$this->fecres = $fecres;
	}

	/**
	 * Metodo para establecer el valor del campo numini
	 * @param integer $numini
	 */
	public function setNumini($numini){
		$this->numini = $numini;
	}

	/**
	 * Metodo para establecer el valor del campo numfin
	 * @param integer $numfin
	 */
	public function setNumfin($numfin){
		$this->numfin = $numfin;
	}

	/**
	 * Metodo para establecer el valor del campo notreg
	 * @param string $notreg
	 */
	public function setNotreg($notreg){
		$this->notreg = $notreg;
	}

	/**
	 * Metodo para establecer el valor del campo notica
	 * @param string $notica
	 */
	public function setNotica($notica){
		$this->notica = $notica;
	}

	/**
	 * Metodo para establecer el valor del campo fecfac
	 * @param Date $fecfac
	 */
	public function setFecfac($fecfac){
		$this->fecfac = $fecfac;
	}

	/**
	 * Metodo para establecer el valor del campo horfac
	 * @param string $horfac
	 */
	public function setHorfac($horfac){
		$this->horfac = $horfac;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
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
	 * Metodo para establecer el valor del campo numhab
	 * @param string $numhab
	 */
	public function setNumhab($numhab){
		$this->numhab = $numhab;
	}

	/**
	 * Metodo para establecer el valor del campo dif
	 * @param integer $dif
	 */
	public function setDif($dif){
		$this->dif = $dif;
	}

	/**
	 * Metodo para establecer el valor del campo direccion
	 * @param string $direccion
	 */
	public function setDireccion($direccion){
		$this->direccion = $direccion;
	}

	/**
	 * Metodo para establecer el valor del campo ciudad
	 * @param string $ciudad
	 */
	public function setCiudad($ciudad){
		$this->ciudad = $ciudad;
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
	 * Metodo para establecer el valor del campo caja
	 * @param string $caja
	 */
	public function setCaja($caja){
		$this->caja = $caja;
	}

	/**
	 * Metodo para establecer el valor del campo planes
	 * @param string $planes
	 */
	public function setPlanes($planes){
		$this->planes = $planes;
	}

	/**
	 * Metodo para establecer el valor del campo huesped
	 * @param string $huesped
	 */
	public function setHuesped($huesped){
		$this->huesped = $huesped;
	}

	/**
	 * Metodo para establecer el valor del campo empresa
	 * @param string $empresa
	 */
	public function setEmpresa($empresa){
		$this->empresa = $empresa;
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
	 * Metodo para establecer el valor del campo tocanogr
	 * @param string $tocanogr
	 */
	public function setTocanogr($tocanogr){
		$this->tocanogr = $tocanogr;
	}

	/**
	 * Metodo para establecer el valor del campo tocagr
	 * @param string $tocagr
	 */
	public function setTocagr($tocagr){
		$this->tocagr = $tocagr;
	}

	/**
	 * Metodo para establecer el valor del campo tocagr16
	 * @param string $tocagr16
	 */
	public function setTocagr16($tocagr16){
		$this->tocagr16 = $tocagr16;
	}

	/**
	 * Metodo para establecer el valor del campo tocagr10
	 * @param string $tocagr10
	 */
	public function setTocagr10($tocagr10){
		$this->tocagr10 = $tocagr10;
	}

	/**
	 * Metodo para establecer el valor del campo totiva16
	 * @param string $totiva16
	 */
	public function setTotiva16($totiva16){
		$this->totiva16 = $totiva16;
	}

	/**
	 * Metodo para establecer el valor del campo totiva10
	 * @param string $totiva10
	 */
	public function setTotiva10($totiva10){
		$this->totiva10 = $totiva10;
	}

	/**
	 * Metodo para establecer el valor del campo totiva
	 * @param string $totiva
	 */
	public function setTotiva($totiva){
		$this->totiva = $totiva;
	}

	/**
	 * Metodo para establecer el valor del campo totimp
	 * @param string $totimp
	 */
	public function setTotimp($totimp){
		$this->totimp = $totimp;
	}

	/**
	 * Metodo para establecer el valor del campo totabo
	 * @param string $totabo
	 */
	public function setTotabo($totabo){
		$this->totabo = $totabo;
	}

	/**
	 * Metodo para establecer el valor del campo total
	 * @param string $total
	 */
	public function setTotal($total){
		$this->total = $total;
	}

	/**
	 * Metodo para establecer el valor del campo saldo
	 * @param string $saldo
	 */
	public function setSaldo($saldo){
		$this->saldo = $saldo;
	}

	/**
	 * Metodo para establecer el valor del campo basret
	 * @param string $basret
	 */
	public function setBasret($basret){
		$this->basret = $basret;
	}

	/**
	 * Metodo para establecer el valor del campo propina
	 * @param string $propina
	 */
	public function setPropina($propina){
		$this->propina = $propina;
	}

	/**
	 * Metodo para establecer el valor del campo nota
	 * @param string $nota
	 */
	public function setNota($nota){
		$this->nota = $nota;
	}

	/**
	 * Metodo para establecer el valor del campo formas
	 * @param string $formas
	 */
	public function setFormas($formas){
		$this->formas = $formas;
	}

	/**
	 * Metodo para establecer el valor del campo fecven
	 * @param Date $fecven
	 */
	public function setFecven($fecven){
		$this->fecven = $fecven;
	}

	/**
	 * Metodo para establecer el valor del campo fecgen
	 * @param Date $fecgen
	 */
	public function setFecgen($fecgen){
		$this->fecgen = $fecgen;
	}

	/**
	 * Metodo para establecer el valor del campo tipres
	 * @param string $tipres
	 */
	public function setTipres($tipres){
		$this->tipres = $tipres;
	}

	/**
	 * Metodo para establecer el valor del campo facalo
	 * @param string $facalo
	 */
	public function setFacalo($facalo){
		$this->facalo = $facalo;
	}

	/**
	 * Metodo para establecer el valor del campo quien
	 * @param string $quien
	 */
	public function setQuien($quien){
		$this->quien = $quien;
	}

	/**
	 * Metodo para establecer el valor del campo genusu
	 * @param integer $genusu
	 */
	public function setGenusu($genusu){
		$this->genusu = $genusu;
	}

	/**
	 * Metodo para establecer el valor del campo canfac
	 * @param integer $canfac
	 */
	public function setCanfac($canfac){
		$this->canfac = $canfac;
	}

	/**
	 * Metodo para establecer el valor del campo codusu
	 * @param integer $codusu
	 */
	public function setCodusu($codusu){
		$this->codusu = $codusu;
	}

	/**
	 * Metodo para establecer el valor del campo svf
	 * @param string $svf
	 */
	public function setSvf($svf){
		$this->svf = $svf;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo prefac
	 * @return string
	 */
	public function getPrefac(){
		return $this->prefac;
	}

	/**
	 * Devuelve el valor del campo numfac
	 * @return integer
	 */
	public function getNumfac(){
		return $this->numfac;
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
	 * Devuelve el valor del campo nomcad
	 * @return string
	 */
	public function getNomcad(){
		return $this->nomcad;
	}

	/**
	 * Devuelve el valor del campo nithot
	 * @return string
	 */
	public function getNithot(){
		return $this->nithot;
	}

	/**
	 * Devuelve el valor del campo hotel
	 * @return string
	 */
	public function getHotel(){
		return $this->hotel;
	}

	/**
	 * Devuelve el valor del campo resfac
	 * @return string
	 */
	public function getResfac(){
		return $this->resfac;
	}

	/**
	 * Devuelve el valor del campo fecres
	 * @return Date
	 */
	public function getFecres(){
		if($this->fecres){
			return new Date($this->fecres);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo numini
	 * @return integer
	 */
	public function getNumini(){
		return $this->numini;
	}

	/**
	 * Devuelve el valor del campo numfin
	 * @return integer
	 */
	public function getNumfin(){
		return $this->numfin;
	}

	/**
	 * Devuelve el valor del campo notreg
	 * @return string
	 */
	public function getNotreg(){
		return $this->notreg;
	}

	/**
	 * Devuelve el valor del campo notica
	 * @return string
	 */
	public function getNotica(){
		return $this->notica;
	}

	/**
	 * Devuelve el valor del campo fecfac
	 * @return Date
	 */
	public function getFecfac(){
		if($this->fecfac){
			return new Date($this->fecfac);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo horfac
	 * @return string
	 */
	public function getHorfac(){
		return $this->horfac;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
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
	 * Devuelve el valor del campo numhab
	 * @return string
	 */
	public function getNumhab(){
		return $this->numhab;
	}

	/**
	 * Devuelve el valor del campo dif
	 * @return integer
	 */
	public function getDif(){
		return $this->dif;
	}

	/**
	 * Devuelve el valor del campo direccion
	 * @return string
	 */
	public function getDireccion(){
		return $this->direccion;
	}

	/**
	 * Devuelve el valor del campo ciudad
	 * @return string
	 */
	public function getCiudad(){
		return $this->ciudad;
	}

	/**
	 * Devuelve el valor del campo feclle
	 * @return Date
	 */
	public function getFeclle(){
		if($this->feclle){
			return new Date($this->feclle);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo fecsal
	 * @return Date
	 */
	public function getFecsal(){
		if($this->fecsal){
			return new Date($this->fecsal);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo caja
	 * @return string
	 */
	public function getCaja(){
		return $this->caja;
	}

	/**
	 * Devuelve el valor del campo planes
	 * @return string
	 */
	public function getPlanes(){
		return $this->planes;
	}

	/**
	 * Devuelve el valor del campo huesped
	 * @return string
	 */
	public function getHuesped(){
		return $this->huesped;
	}

	/**
	 * Devuelve el valor del campo empresa
	 * @return string
	 */
	public function getEmpresa(){
		return $this->empresa;
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
	 * Devuelve el valor del campo tocanogr
	 * @return string
	 */
	public function getTocanogr(){
		return $this->tocanogr;
	}

	/**
	 * Devuelve el valor del campo tocagr
	 * @return string
	 */
	public function getTocagr(){
		return $this->tocagr;
	}

	/**
	 * Devuelve el valor del campo tocagr16
	 * @return string
	 */
	public function getTocagr16(){
		return $this->tocagr16;
	}

	/**
	 * Devuelve el valor del campo tocagr10
	 * @return string
	 */
	public function getTocagr10(){
		return $this->tocagr10;
	}

	/**
	 * Devuelve el valor del campo totiva16
	 * @return string
	 */
	public function getTotiva16(){
		return $this->totiva16;
	}

	/**
	 * Devuelve el valor del campo totiva10
	 * @return string
	 */
	public function getTotiva10(){
		return $this->totiva10;
	}

	/**
	 * Devuelve el valor del campo totiva
	 * @return string
	 */
	public function getTotiva(){
		return $this->totiva;
	}

	/**
	 * Devuelve el valor del campo totimp
	 * @return string
	 */
	public function getTotimp(){
		return $this->totimp;
	}

	/**
	 * Devuelve el valor del campo totabo
	 * @return string
	 */
	public function getTotabo(){
		return $this->totabo;
	}

	/**
	 * Devuelve el valor del campo total
	 * @return string
	 */
	public function getTotal(){
		return $this->total;
	}

	/**
	 * Devuelve el valor del campo saldo
	 * @return string
	 */
	public function getSaldo(){
		return $this->saldo;
	}

	/**
	 * Devuelve el valor del campo basret
	 * @return string
	 */
	public function getBasret(){
		return $this->basret;
	}

	/**
	 * Devuelve el valor del campo propina
	 * @return string
	 */
	public function getPropina(){
		return $this->propina;
	}

	/**
	 * Devuelve el valor del campo nota
	 * @return string
	 */
	public function getNota(){
		return $this->nota;
	}

	/**
	 * Devuelve el valor del campo formas
	 * @return string
	 */
	public function getFormas(){
		return $this->formas;
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
	 * Devuelve el valor del campo fecgen
	 * @return Date
	 */
	public function getFecgen(){
		if($this->fecgen){
			return new Date($this->fecgen);
		} else {
			return null;
		}
	}

	/**
	 * Devuelve el valor del campo tipres
	 * @return string
	 */
	public function getTipres(){
		return $this->tipres;
	}

	/**
	 * Devuelve el valor del campo facalo
	 * @return string
	 */
	public function getFacalo(){
		return $this->facalo;
	}

	/**
	 * Devuelve el valor del campo quien
	 * @return string
	 */
	public function getQuien(){
		return $this->quien;
	}

	/**
	 * Devuelve el valor del campo genusu
	 * @return integer
	 */
	public function getGenusu(){
		return $this->genusu;
	}

	/**
	 * Devuelve el valor del campo canfac
	 * @return integer
	 */
	public function getCanfac(){
		return $this->canfac;
	}

	/**
	 * Devuelve el valor del campo codusu
	 * @return integer
	 */
	public function getCodusu(){
		return $this->codusu;
	}

	/**
	 * Devuelve el valor del campo svf
	 * @return string
	 */
	public function getSvf(){
		return $this->svf;
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
		$this->setSource('factura');
	}

}

