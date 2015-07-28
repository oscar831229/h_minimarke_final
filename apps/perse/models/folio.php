<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Persé
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Folio extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $numfol;

	/**
	 * @var integer
	 */
	protected $numres;

	/**
	 * @var integer
	 */
	protected $codeve;

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
	 * @var integer
	 */
	protected $paides;

	/**
	 * @var integer
	 */
	protected $locdes;

	/**
	 * @var integer
	 */
	protected $ciudes;

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
	protected $codmot;

	/**
	 * @var string
	 */
	protected $numhab;

	/**
	 * @var integer
	 */
	protected $usuout;

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
	 * @var string
	 */
	protected $horsal;

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
	protected $nota;

	/**
	 * @var string
	 */
	protected $notaayb;

	/**
	 * @var string
	 */
	protected $equipaje;

	/**
	 * @var string
	 */
	protected $placa;

	/**
	 * @var integer
	 */
	protected $estpai;

	/**
	 * @var string
	 */
	protected $corregir;

	/**
	 * @var integer
	 */
	protected $forpag;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $walkin;


	/**
	 * Metodo para establecer el valor del campo numfol
	 * @param integer $numfol
	 */
	public function setNumfol($numfol){
		$this->numfol = $numfol;
	}

	/**
	 * Metodo para establecer el valor del campo numres
	 * @param integer $numres
	 */
	public function setNumres($numres){
		$this->numres = $numres;
	}

	/**
	 * Metodo para establecer el valor del campo codeve
	 * @param integer $codeve
	 */
	public function setCodeve($codeve){
		$this->codeve = $codeve;
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
	 * Metodo para establecer el valor del campo paides
	 * @param integer $paides
	 */
	public function setPaides($paides){
		$this->paides = $paides;
	}

	/**
	 * Metodo para establecer el valor del campo locdes
	 * @param integer $locdes
	 */
	public function setLocdes($locdes){
		$this->locdes = $locdes;
	}

	/**
	 * Metodo para establecer el valor del campo ciudes
	 * @param integer $ciudes
	 */
	public function setCiudes($ciudes){
		$this->ciudes = $ciudes;
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
	 * Metodo para establecer el valor del campo codmot
	 * @param integer $codmot
	 */
	public function setCodmot($codmot){
		$this->codmot = $codmot;
	}

	/**
	 * Metodo para establecer el valor del campo numhab
	 * @param string $numhab
	 */
	public function setNumhab($numhab){
		$this->numhab = $numhab;
	}

	/**
	 * Metodo para establecer el valor del campo usuout
	 * @param integer $usuout
	 */
	public function setUsuout($usuout){
		$this->usuout = $usuout;
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
	 * Metodo para establecer el valor del campo horsal
	 * @param string $horsal
	 */
	public function setHorsal($horsal){
		$this->horsal = $horsal;
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
	 * Metodo para establecer el valor del campo nota
	 * @param string $nota
	 */
	public function setNota($nota){
		$this->nota = $nota;
	}

	/**
	 * Metodo para establecer el valor del campo notaayb
	 * @param string $notaayb
	 */
	public function setNotaayb($notaayb){
		$this->notaayb = $notaayb;
	}

	/**
	 * Metodo para establecer el valor del campo equipaje
	 * @param string $equipaje
	 */
	public function setEquipaje($equipaje){
		$this->equipaje = $equipaje;
	}

	/**
	 * Metodo para establecer el valor del campo placa
	 * @param string $placa
	 */
	public function setPlaca($placa){
		$this->placa = $placa;
	}

	/**
	 * Metodo para establecer el valor del campo estpai
	 * @param integer $estpai
	 */
	public function setEstpai($estpai){
		$this->estpai = $estpai;
	}

	/**
	 * Metodo para establecer el valor del campo corregir
	 * @param string $corregir
	 */
	public function setCorregir($corregir){
		$this->corregir = $corregir;
	}

	/**
	 * Metodo para establecer el valor del campo forpag
	 * @param integer $forpag
	 */
	public function setForpag($forpag){
		$this->forpag = $forpag;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo walkin
	 * @param string $walkin
	 */
	public function setWalkin($walkin){
		$this->walkin = $walkin;
	}


	/**
	 * Devuelve el valor del campo numfol
	 * @return integer
	 */
	public function getNumfol(){
		return $this->numfol;
	}

	/**
	 * Devuelve el valor del campo numres
	 * @return integer
	 */
	public function getNumres(){
		return $this->numres;
	}

	/**
	 * Devuelve el valor del campo codeve
	 * @return integer
	 */
	public function getCodeve(){
		return $this->codeve;
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
	 * Devuelve el valor del campo paides
	 * @return integer
	 */
	public function getPaides(){
		return $this->paides;
	}

	/**
	 * Devuelve el valor del campo locdes
	 * @return integer
	 */
	public function getLocdes(){
		return $this->locdes;
	}

	/**
	 * Devuelve el valor del campo ciudes
	 * @return integer
	 */
	public function getCiudes(){
		return $this->ciudes;
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
	 * Devuelve el valor del campo codmot
	 * @return integer
	 */
	public function getCodmot(){
		return $this->codmot;
	}

	/**
	 * Devuelve el valor del campo numhab
	 * @return string
	 */
	public function getNumhab(){
		return $this->numhab;
	}

	/**
	 * Devuelve el valor del campo usuout
	 * @return integer
	 */
	public function getUsuout(){
		return $this->usuout;
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
	 * Devuelve el valor del campo horsal
	 * @return string
	 */
	public function getHorsal(){
		return $this->horsal;
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
	 * Devuelve el valor del campo nota
	 * @return string
	 */
	public function getNota(){
		return $this->nota;
	}

	/**
	 * Devuelve el valor del campo notaayb
	 * @return string
	 */
	public function getNotaayb(){
		return $this->notaayb;
	}

	/**
	 * Devuelve el valor del campo equipaje
	 * @return string
	 */
	public function getEquipaje(){
		return $this->equipaje;
	}

	/**
	 * Devuelve el valor del campo placa
	 * @return string
	 */
	public function getPlaca(){
		return $this->placa;
	}

	/**
	 * Devuelve el valor del campo estpai
	 * @return integer
	 */
	public function getEstpai(){
		return $this->estpai;
	}

	/**
	 * Devuelve el valor del campo corregir
	 * @return string
	 */
	public function getCorregir(){
		return $this->corregir;
	}

	/**
	 * Devuelve el valor del campo forpag
	 * @return integer
	 */
	public function getForpag(){
		return $this->forpag;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo walkin
	 * @return string
	 */
	public function getWalkin(){
		return $this->walkin;
	}

	public function initialize(){
		$this->belongsTo('numhab', 'habitacion');
		$this->belongsTo('cedula', 'clientes');
	}

}

