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

class Cheque extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $chequeras_id;

	/**
	 * @var string
	 */
	protected $comprob;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var integer
	 */
	protected $numero_cheque;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $hora;

	/**
	 * @var Date
	 */
	protected $fecha_cheque;

	/**
	 * @var string
	 */
	protected $valor;

	/**
	 * @var string
	 */
	protected $beneficiario;

	/**
	 * @var string
	 */
	protected $observaciones;

	/**
	 * @var string
	 */
	protected $impreso;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo chequeras_id
	 * @param integer $chequeras_id
	 */
	public function setChequerasId($chequeras_id){
		$this->chequeras_id = $chequeras_id;
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
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo numero_cheque
	 * @param integer $numero_cheque
	 */
	public function setNumeroCheque($numero_cheque){
		$this->numero_cheque = $numero_cheque;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo hora
	 * @param string $hora
	 */
	public function setHora($hora){
		$this->hora = $hora;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_cheque
	 * @param Date $fecha_cheque
	 */
	public function setFechaCheque($fecha_cheque){
		$this->fecha_cheque = $fecha_cheque;
	}

	/**
	 * Metodo para establecer el valor del campo valor
	 * @param string $valor
	 */
	public function setValor($valor){
		$this->valor = $valor;
	}

	/**
	 * Metodo para establecer el valor del campo beneficiario
	 * @param string $beneficiario
	 */
	public function setBeneficiario($beneficiario){
		$this->beneficiario = $beneficiario;
	}

	/**
	 * Metodo para establecer el valor del campo observaciones
	 * @param string $observaciones
	 */
	public function setObservaciones($observaciones){
		$this->observaciones = $observaciones;
	}

	/**
	 * Metodo para establecer el valor del campo impreso
	 * @param string $impreso
	 */
	public function setImpreso($impreso){
		$this->impreso = $impreso;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo chequeras_id
	 * @return integer
	 */
	public function getChequerasId(){
		return $this->chequeras_id;
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
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo numero_cheque
	 * @return integer
	 */
	public function getNumeroCheque(){
		return $this->numero_cheque;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return new Date($this->fecha);
	}

	/**
	 * Devuelve el valor del campo hora
	 * @return string
	 */
	public function getHora(){
		return $this->hora;
	}

	/**
	 * Devuelve el valor del campo fecha_cheque
	 * @return Date
	 */
	public function getFechaCheque(){
		return new Date($this->fecha_cheque);
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return string
	 */
	public function getValor(){
		return $this->valor;
	}

	/**
	 * Devuelve el valor del campo beneficiario
	 * @return string
	 */
	public function getBeneficiario(){
		return $this->beneficiario;
	}

	/**
	 * Devuelve el valor del campo observaciones
	 * @return string
	 */
	public function getObservaciones(){
		return $this->observaciones;
	}

	/**
	 * Devuelve el valor del campo impreso
	 * @return string
	 */
	public function getImpreso(){
		return $this->impreso;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el detalle de un estado del cheque
	 *
	 * @return string
	 */
	public function getDetalleEstado(){
		if($this->estado=='E'){
			return 'EMITIDO';
		} else {
			if($this->estado=='A'){
				return 'ANULADO';
			} else {
				return '';
			}
		}
	}

	/**
	 * Validador del modelo Cheque
	 *
	 * @return boolean
	 */
	protected function validation(){
		$this->validate('InclusionIn', array(
			'field' => 'estado',
			'domain' => array('E', 'A'),
			'message' => 'El estado debe ser "EMITIDO" ó "ANULADO"',
			'required' => true
		));
		if($this->validationHasFailed()==true){
			return false;
		}
	}

	/**
	 * Incializador del modelo
	 *
	 */
	public function initialize(){
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->back_db)){
			$this->setSchema($config->hfos->back_db);
		} else {
			$this->setSchema('ramocol');
		}
	
		$this->belongsTo('Chequeras');
		$this->belongsTo('nit', 'Nits', 'nit');
		$this->hasMany(array('comprob', 'numero'), 'Movi', array('comprob', 'numero'));

		$this->addForeignKey('chequeras_id', 'Chequeras', 'id', array(
			'message' => 'La chequera indicada no es válida'
		));
		$this->addForeignKey('comprob', 'Comprob', 'codigo', array(
			'message' => 'El comprobante indicado no es válido'
		));
		$this->addForeignKey('nit', 'Nits', 'nit', array(
			'message' => 'El tercero indicado no es válido'
		));
	}

}

