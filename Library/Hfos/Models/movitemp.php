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

class Movitemp extends ActiveRecord {

	/**
	 * @var string
	 */
	protected $sid;

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var integer
	 */
	protected $consecutivo;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $centro_costo;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $deb_cre;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $tipo_doc;

	/**
	 * @var string
	 */
	protected $numero_doc;

	/**
	 * @var string
	 */
	protected $base_grab;

	/**
	 * @var string
	 */
	protected $conciliado;

	/**
	 * @var Date
	 */
	protected $f_vence;

	/**
	 * @var integer
	 */
	protected $numfol;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $checksum;


	/**
	 * Metodo para establecer el valor del campo sid
	 * @param string $sid
	 */
	public function setSid($sid){
		$this->sid = $sid;
	}

	/**
	 * Metodo para establecer el valor del campo comprob
	 * @param string $comprob
	 */
	public function setComprob($comprob){
		$this->comprob = $comprob;
	}

	/**
	 * Metodo para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Metodo para establecer el valor del campo consecutivo
	 * @param integer $consecutivo
	 */
	public function setConsecutivo($consecutivo){
		$this->consecutivo = $consecutivo;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param string $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo deb_cre
	 * @param string $deb_cre
	 */
	public function setDebCre($deb_cre){
		$this->deb_cre = $deb_cre;
	}

	/**
	 * Metodo para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Metodo para establecer el valor del campo tipo_doc
	 * @param string $tipo_doc
	 */
	public function setTipoDoc($tipo_doc){
		$this->tipo_doc = $tipo_doc;
	}

	/**
	 * Metodo para establecer el valor del campo numero_doc
	 * @param string $numero_doc
	 */
	public function setNumeroDoc($numero_doc){
		$this->numero_doc = $numero_doc;
	}

	/**
	 * Metodo para establecer el valor del campo base_grab
	 * @param string $base_grab
	 */
	public function setBaseGrab($base_grab){
		$this->base_grab = $base_grab;
	}

	/**
	 * Metodo para establecer el valor del campo conciliado
	 * @param string $conciliado
	 */
	public function setConciliado($conciliado){
		$this->conciliado = $conciliado;
	}

	/**
	 * Metodo para establecer el valor del campo f_vence
	 * @param Date $f_vence
	 */
	public function setFVence($f_vence){
		$this->f_vence = $f_vence;
	}

	/**
	 * Metodo para establecer el valor del campo numfol
	 * @param integer $numfol
	 */
	public function setNumfol($numfol){
		$this->numfol = $numfol;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo checksum
	 * @param string $checksum
	 */
	public function setChecksum($checksum){
		$this->checksum = $checksum;
	}


	/**
	 * Devuelve el valor del campo sid
	 * @return string
	 */
	public function getSid(){
		return $this->sid;
	}

	/**
	 * Devuelve el valor del campo comprob
	 * @return string
	 */
	public function getComprob(){
		return $this->comprob;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo consecutivo
	 * @return integer
	 */
	public function getConsecutivo(){
		return $this->consecutivo;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return string
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo deb_cre
	 * @return string
	 */
	public function getDebCre(){
		return $this->deb_cre;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo tipo_doc
	 * @return string
	 */
	public function getTipoDoc(){
		return $this->tipo_doc;
	}

	/**
	 * Devuelve el valor del campo numero_doc
	 * @return string
	 */
	public function getNumeroDoc(){
		return $this->numero_doc;
	}

	/**
	 * Devuelve el valor del campo base_grab
	 * @return string
	 */
	public function getBaseGrab(){
		return $this->base_grab;
	}

	/**
	 * Devuelve el valor del campo conciliado
	 * @return string
	 */
	public function getConciliado(){
		return $this->conciliado;
	}

	/**
	 * Devuelve el valor del campo f_vence
	 * @return Date
	 */
	public function getFVence(){
		return new Date($this->f_vence);
	}

	/**
	 * Devuelve el valor del campo numfol
	 * @return integer
	 */
	public function getNumfol(){
		return $this->numfol;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo checksum
	 * @return string
	 */
	public function getChecksum(){
		return $this->checksum;
	}

	/**
	 * Genera el checksum del registro
	 *
	 * @return string
	 */
	public function getUpdatedChecksum(){
		$checksum = '';
		foreach($this->getAttributes() as $attribute){
			if($attribute!='checksum'&&$attribute!='estado'){
				if($attribute!='valor'&&$attribute!='base_grab'){
					$checksum.=trim($this->$attribute);
				} else {
					$checksum.=sprintf('%.2s', $this->$attribute);
				}
			}
		}
		return md5($checksum);
	}

	/**
	 * Evento antes de validar al crear, crea el checksum del registro
	 *
	 */
	public function beforeValidationOnCreate(){
		$this->checksum = $this->getUpdatedChecksum();
	}

}

