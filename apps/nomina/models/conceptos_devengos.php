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

class ConceptosDevengos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $vacaciones_disfrutadas;

	/**
	 * @var string
	 */
	protected $vacaciones_retiro;

	/**
	 * @var string
	 */
	protected $parafiscales;

	/**
	 * @var string
	 */
	protected $prima;

	/**
	 * @var string
	 */
	protected $salud;

	/**
	 * @var string
	 */
	protected $retencion;

	/**
	 * @var string
	 */
	protected $porc_retencion;

	/**
	 * @var string
	 */
	protected $cesantias;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var string
	 */
	protected $provision;

	/**
	 * @var string
	 */
	protected $porc_recargo;

	/**
	 * @var string
	 */
	protected $variable;

	/**
	 * @var string
	 */
	protected $formula;

	/**
	 * @var string
	 */
	protected $formula_acl;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param integer $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo vacaciones_disfrutadas
	 * @param string $vacaciones_disfrutadas
	 */
	public function setVacacionesDisfrutadas($vacaciones_disfrutadas){
		$this->vacaciones_disfrutadas = $vacaciones_disfrutadas;
	}

	/**
	 * Metodo para establecer el valor del campo vacaciones_retiro
	 * @param string $vacaciones_retiro
	 */
	public function setVacacionesRetiro($vacaciones_retiro){
		$this->vacaciones_retiro = $vacaciones_retiro;
	}

	/**
	 * Metodo para establecer el valor del campo parafiscales
	 * @param string $parafiscales
	 */
	public function setParafiscales($parafiscales){
		$this->parafiscales = $parafiscales;
	}

	/**
	 * Metodo para establecer el valor del campo prima
	 * @param string $prima
	 */
	public function setPrima($prima){
		$this->prima = $prima;
	}

	/**
	 * Metodo para establecer el valor del campo salud
	 * @param string $salud
	 */
	public function setSalud($salud){
		$this->salud = $salud;
	}

	/**
	 * Metodo para establecer el valor del campo retencion
	 * @param string $retencion
	 */
	public function setRetencion($retencion){
		$this->retencion = $retencion;
	}

	/**
	 * Metodo para establecer el valor del campo porc_retencion
	 * @param string $porc_retencion
	 */
	public function setPorcRetencion($porc_retencion){
		$this->porc_retencion = $porc_retencion;
	}

	/**
	 * Metodo para establecer el valor del campo cesantias
	 * @param string $cesantias
	 */
	public function setCesantias($cesantias){
		$this->cesantias = $cesantias;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo provision
	 * @param string $provision
	 */
	public function setProvision($provision){
		$this->provision = $provision;
	}

	/**
	 * Metodo para establecer el valor del campo porc_recargo
	 * @param string $porc_recargo
	 */
	public function setPorcRecargo($porc_recargo){
		$this->porc_recargo = $porc_recargo;
	}

	/**
	 * Metodo para establecer el valor del campo variable
	 * @param string $variable
	 */
	public function setVariable($variable){
		$this->variable = $variable;
	}

	/**
	 * Metodo para establecer el valor del campo formula
	 * @param string $formula
	 */
	public function setFormula($formula){
		$this->formula = $formula;
	}

	/**
	 * Metodo para establecer el valor del campo formula_acl
	 * @param string $formula_acl
	 */
	public function setFormulaAcl($formula_acl){
		$this->formula_acl = $formula_acl;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return integer
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo vacaciones_disfrutadas
	 * @return string
	 */
	public function getVacacionesDisfrutadas(){
		return $this->vacaciones_disfrutadas;
	}

	/**
	 * Devuelve el valor del campo vacaciones_retiro
	 * @return string
	 */
	public function getVacacionesRetiro(){
		return $this->vacaciones_retiro;
	}

	/**
	 * Devuelve el valor del campo parafiscales
	 * @return string
	 */
	public function getParafiscales(){
		return $this->parafiscales;
	}

	/**
	 * Devuelve el valor del campo prima
	 * @return string
	 */
	public function getPrima(){
		return $this->prima;
	}

	/**
	 * Devuelve el valor del campo salud
	 * @return string
	 */
	public function getSalud(){
		return $this->salud;
	}

	/**
	 * Devuelve el valor del campo retencion
	 * @return string
	 */
	public function getRetencion(){
		return $this->retencion;
	}

	/**
	 * Devuelve el valor del campo porc_retencion
	 * @return string
	 */
	public function getPorcRetencion(){
		return $this->porc_retencion;
	}

	/**
	 * Devuelve el valor del campo cesantias
	 * @return string
	 */
	public function getCesantias(){
		return $this->cesantias;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo provision
	 * @return string
	 */
	public function getProvision(){
		return $this->provision;
	}

	/**
	 * Devuelve el valor del campo porc_recargo
	 * @return string
	 */
	public function getPorcRecargo(){
		return $this->porc_recargo;
	}

	/**
	 * Devuelve el valor del campo variable
	 * @return string
	 */
	public function getVariable(){
		return $this->variable;
	}

	/**
	 * Devuelve el valor del campo formula
	 * @return string
	 */
	public function getFormula(){
		return $this->formula;
	}

	/**
	 * Devuelve el valor del campo formula_acl
	 * @return string
	 */
	public function getFormulaAcl(){
		return $this->formula_acl;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	protected function beforeSave(){
		if($this->estado=='A'){
			try {
				ClaraMacro::parse($this->formula, $this->variable);
				$this->formula_acl = serialize(ClaraMacro::getOpCode());
			}
			catch(ClaraMacroException $e){
				$this->appendMessage(new ActiveRecordMessage($e->getMessage(), 'formula'));
				return false;
			}
			if($this->cuenta!=''){
				$cuenta = BackCacher::getCuenta($this->cuenta);
				if($cuenta==false){
					$this->appendMessage(new ActiveRecordMessage('La cuenta contable no existe ó no es auxiliar', 'cuenta'));
					return false;
				} else {
					if($cuenta->getEsAuxiliar()!='S'){
						$this->appendMessage(new ActiveRecordMessage('La cuenta contable no es auxiliar', 'cuenta'));
						return false;
					}
				}
			}
		}
		return true;
	}

}

