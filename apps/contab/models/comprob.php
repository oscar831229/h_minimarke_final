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
 * @copyright 	BH-TECK Inc. 2009-2011
 * @version		$Id$
 */

class Comprob extends RcsRecord {

	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nom_comprob;

	/**
	 * @var string
	 */
	protected $diario;

	/**
	 * @var string
	 */
	protected $cta_iva;

	/**
	 * @var string
	 */
	protected $cta_ivad;

	/**
	 * @var string
	 */
	protected $cta_ivam;

	/**
	 * @var string
	 */
	protected $cta_cartera;

	/**
	 * @var string
	 */
	protected $cta_iva16_venta;

	/**
	 * @var string
	 */
	protected $cta_iva10_venta;

	/**
	 * @var string
	 */
	protected $pide_vend;

	/**
	 * @var integer
	 */
	protected $consecutivo;

	/**
	 * @var string
	 */
	protected $comprob_contab;

	/**
	 * @var int
	 */
	protected $consecutivo_niif;

	/**
	 * @var enum('N', 'I')
	 */
	protected $tipo_movi_niif;

	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nom_comprob
	 * @param string $nom_comprob
	 */
	public function setNomComprob($nom_comprob){
		$this->nom_comprob = $nom_comprob;
	}

	/**
	 * Metodo para establecer el valor del campo diario
	 * @param string $diario
	 */
	public function setDiario($diario){
		$this->diario = $diario;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva
	 * @param string $cta_iva
	 */
	public function setCtaIva($cta_iva){
		$this->cta_iva = $cta_iva;
	}

	/**
	 * Metodo para establecer el valor del campo cta_ivad
	 * @param string $cta_ivad
	 */
	public function setCtaIvad($cta_ivad){
		$this->cta_ivad = $cta_ivad;
	}

	/**
	 * Metodo para establecer el valor del campo cta_ivam
	 * @param string $cta_ivam
	 */
	public function setCtaIvam($cta_ivam){
		$this->cta_ivam = $cta_ivam;
	}

	/**
	 * Metodo para establecer el valor del campo cta_cartera
	 * @param string $cta_cartera
	 */
	public function setCtaCartera($cta_cartera){
		$this->cta_cartera = $cta_cartera;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva16_venta
	 * @param string $cta_iva16_venta
	 */
	public function setCtaIva16Venta($cta_iva16_venta){
		$this->cta_iva16_venta = $cta_iva16_venta;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva10_venta
	 * @param string $cta_iva10_venta
	 */
	public function setCtaIva10Venta($cta_iva10_venta){
		$this->cta_iva10_venta = $cta_iva10_venta;
	}

	/**
	 * Metodo para establecer el valor del campo pide_vend
	 * @param string $pide_vend
	 */
	public function setPideVend($pide_vend){
		$this->pide_vend = $pide_vend;
	}

	/**
	 * Metodo para establecer el valor del campo consecutivo
	 * @param integer $consecutivo
	 */
	public function setConsecutivo($consecutivo){
		$this->consecutivo = $consecutivo;
	}

	/**
	 * Metodo para establecer el valor del campo comprob_contab
	 * @param string $comprob_contab
	 */
	public function setComprobContab($comprob_contab){
		$this->comprob_contab = $comprob_contab;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nom_comprob
	 * @return string
	 */
	public function getNomComprob(){
		return $this->nom_comprob;
	}

	/**
	 * Devuelve el valor del campo diario
	 * @return string
	 */
	public function getDiario(){
		return $this->diario;
	}

	/**
	 * Devuelve el valor del campo cta_iva
	 * @return string
	 */
	public function getCtaIva(){
		return $this->cta_iva;
	}

	/**
	 * Devuelve el valor del campo cta_ivad
	 * @return string
	 */
	public function getCtaIvad(){
		return $this->cta_ivad;
	}

	/**
	 * Devuelve el valor del campo cta_ivam
	 * @return string
	 */
	public function getCtaIvam(){
		return $this->cta_ivam;
	}

	/**
	 * Devuelve el valor del campo cta_cartera
	 * @return string
	 */
	public function getCtaCartera(){
		return $this->cta_cartera;
	}

	/**
	 * Devuelve el valor del campo cta_iva16_venta
	 * @return string
	 */
	public function getCtaIva16Venta(){
		return $this->cta_iva16_venta;
	}

	/**
	 * Devuelve el valor del campo cta_iva10_venta
	 * @return string
	 */
	public function getCtaIva10Venta(){
		return $this->cta_iva10_venta;
	}

	/**
	 * Devuelve el valor del campo pide_vend
	 * @return string
	 */
	public function getPideVend(){
		return $this->pide_vend;
	}

	/**
	 * Devuelve el valor del campo consecutivo
	 * @return integer
	 */
	public function getConsecutivo(){
		return $this->consecutivo;
	}

	/**
	 * Devuelve el valor del campo comprob_contab
	 * @return string
	 */
	public function getComprobContab(){
		return $this->comprob_contab;
	}

	/**
	 * Metodo para establecer el valor del campo consecutivo_niif
	 * @param int $consecutivoNiif
	 */
	public function setConsecutivoNiif($consecutivoNiif)
	{
		$this->consecutivo_niif = $consecutivoNiif;
	}

	/**
	 * Devuelve el valor del campo consecutivo_niif
	 * @return int
	 */
	public function getConsecutivoNiif()
	{
		return $this->consecutivo_niif;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_movi_niif
	 * @param string $tipoMoviNiif
	 */
	public function setTipoMoviNiif($tipoMoviNiif)
	{
		$this->tipo_movi_niif = $tipoMoviNiif;
	}

	/**
	 * Devuelve el valor del campo tipo_movi_niif
	 * @return string
	 */
	public function getTipoMoviNiif()
	{
		return $this->tipo_movi_niif;
	}

	public function beforeDelete(){
		if($this->countMovi()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el comprobante porque tiene movimiento asociado', 'codigo'));
			return false;
		}
		if($this->countMovihead()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar el comprobante porque tiene movimiento de inventarios asociado', 'codigo'));
			return false;
		}
	}

	protected function validation(){
	     $this->validate('Uniqueness', array(
	          'field' => array('nom_comprob'),
	          'message' => 'El nombre del comprobante ya está siendo usado por otro comprobante',
	          'code' => 11001
	     ));
	     if($this->validationHasFailed()==true){
	          return false;
	     }
	}

	public function beforeSave(){
		if($this->consecutivo>0){
			$Movi = EntityManager::get('Movi', false, $this);
			$exists = $Movi->count("comprob='{$this->codigo}' AND numero='{$this->consecutivo}'");
			if($exists==true){
				$message = 'No se puede asignar el consecutivo '.$this->consecutivo.' al comprobante "'.$this->nom_comprob.'" porque ya existe movimiento asociado a este. ';
				$siguiente = $Movi->maximum(array('numero', 'conditions' => 'comprob="'.$this->codigo.'"'))+1;
				if($siguiente>0){
					$message.='El siguiente consecutivo libre es: '.$siguiente;
				}
				$this->appendMessage(new ActiveRecordMessage($message, 'consecutivo'));
				return false;
			}
		}
	}

	public function initialize(){

		//Relaciones
		$this->hasMany('codigo', 'Movi', 'comprob');
		$this->hasMany('codigo', 'Movihead', 'comprob');

		//Llaves foráneas
		$this->addForeignKey('diario', 'Diarios', 'codigo', array(
			'message' => 'El diario indicado no es válido'
		));
	}

}
