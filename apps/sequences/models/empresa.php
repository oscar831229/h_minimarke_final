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
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Empresa extends RcsRecord {

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var Date
	 */
	protected $f_cierrec;

	/**
	 * @var Date
	 */
	protected $f_cierrei;

	/**
	 * @var Date
	 */
	protected $f_cierren;

	/**
	 * @var string
	 */
	protected $seis;

	/**
	 * @var string
	 */
	protected $sop;

	/**
	 * @var string
	 */
	protected $cinco;

	/**
	 * @var string
	 */
	protected $contabiliza;

	/**
	 * @var string
	 */
	protected $presupuesto;

	/**
	 * @var string
	 */
	protected $centro_costo;

	/**
	 * @var string
	 */
	protected $version;


	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo f_cierrec
	 * @param Date $f_cierrec
	 */
	public function setFCierrec($f_cierrec){
		$this->f_cierrec = $f_cierrec;
	}

	/**
	 * Metodo para establecer el valor del campo f_cierrei
	 * @param Date $f_cierrei
	 */
	public function setFCierrei($f_cierrei){
		$this->f_cierrei = $f_cierrei;
	}

	/**
	 * Metodo para establecer el valor del campo f_cierren
	 * @param Date $f_cierren
	 */
	public function setFCierren($f_cierren){
		$this->f_cierren = $f_cierren;
	}

	/**
	 * Metodo para establecer el valor del campo seis
	 * @param string $seis
	 */
	public function setSeis($seis){
		$this->seis = $seis;
	}

	/**
	 * Metodo para establecer el valor del campo sop
	 * @param string $sop
	 */
	public function setSop($sop){
		$this->sop = $sop;
	}

	/**
	 * Metodo para establecer el valor del campo cinco
	 * @param string $cinco
	 */
	public function setCinco($cinco){
		$this->cinco = $cinco;
	}

	/**
	 * Metodo para establecer el valor del campo contabiliza
	 * @param string $contabiliza
	 */
	public function setContabiliza($contabiliza){
		$this->contabiliza = $contabiliza;
	}

	/**
	 * Metodo para establecer el valor del campo presupuesto
	 * @param string $presupuesto
	 */
	public function setPresupuesto($presupuesto){
		$this->presupuesto = $presupuesto;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param string $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo version
	 * @param string $version
	 */
	public function setVersion($version){
		$this->version = $version;
	}


	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo f_cierrec
	 * @return Date
	 */
	public function getFCierrec(){
		return new Date($this->f_cierrec);
	}

	/**
	 * Devuelve el valor del campo f_cierrei
	 * @return Date
	 */
	public function getFCierrei(){
		return new Date($this->f_cierrei);
	}

	/**
	 * Devuelve el valor del campo f_cierren
	 * @return Date
	 */
	public function getFCierren(){
		return new Date($this->f_cierren);
	}

	/**
	 * Devuelve el valor del campo seis
	 * @return string
	 */
	public function getSeis(){
		return $this->seis;
	}

	/**
	 * Devuelve el valor del campo sop
	 * @return string
	 */
	public function getSop(){
		return $this->sop;
	}

	/**
	 * Devuelve el valor del campo cinco
	 * @return string
	 */
	public function getCinco(){
		return $this->cinco;
	}

	/**
	 * Devuelve el valor del campo contabiliza
	 * @return string
	 */
	public function getContabiliza(){
		return $this->contabiliza;
	}

	/**
	 * Devuelve el valor del campo presupuesto
	 * @return string
	 */
	public function getPresupuesto(){
		return $this->presupuesto;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return string
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo version
	 * @return string
	 */
	public function getVersion(){
		return $this->version;
	}

	public function initialize(){
		$backDbName = CoreConfig::getAppSetting('back_db', 'hfos');
		if($backDbName){
			$this->setSchema($backDbName);
		} else {
			$this->setSchema('ramocol');
		}
	}

}

