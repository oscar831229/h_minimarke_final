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
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Niif extends RcsRecord {

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $mayor;

	/**
	 * @var string
	 */
	protected $clase;

	/**
	 * @var string
	 */
	protected $subclase;

	/**
	 * @var string
	 */
	protected $auxiliar;

	/**
	 * @var string
	 */
	protected $subaux;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $es_auxiliar;

	/**
	 * @var string
	 */
	protected $pide_nit;

	/**
	 * @var string
	 */
	protected $pide_ban;

	/**
	 * @var string
	 */
	protected $pide_base;

	/**
	 * @var string
	 */
	protected $porc_iva;

	/**
	 * @var string
	 */
	protected $pide_fact;

	/**
	 * @var string
	 */
	protected $pide_centro;

	/**
	 * @var string
	 */
	protected $es_mayor;

	/**
	 * @var string
	 */
	protected $contrapartida;

	/**
	 * @var string
	 */
	protected $cta_retencion;

	/**
	 * @var string
	 */
	protected $porc_retenc;

	/**
	 * @var string
	 */
	protected $cta_iva;

	/**
	 * @var string
	 */
	protected $porcen_iva;

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * Indica si se deben consultar las cuentas padre
	 *
	 * @var boolean
	 */
	private static $_checkParents = true;

	/**
	 * Establece si se deben consultar las cuentas padre
	 *
	 * @param boolean $checkParents
	 */
	public static function setCheckParents($checkParents){
		self::$_checkParents = $checkParents;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo mayor
	 * @param string $mayor
	 */
	public function setMayor($mayor){
		$this->mayor = $mayor;
	}

	/**
	 * Metodo para establecer el valor del campo clase
	 * @param string $clase
	 */
	public function setClase($clase){
		$this->clase = $clase;
	}

	/**
	 * Metodo para establecer el valor del campo subclase
	 * @param string $subclase
	 */
	public function setSubclase($subclase){
		$this->subclase = $subclase;
	}

	/**
	 * Metodo para establecer el valor del campo auxiliar
	 * @param string $auxiliar
	 */
	public function setAuxiliar($auxiliar){
		$this->auxiliar = $auxiliar;
	}

	/**
	 * Metodo para establecer el valor del campo subaux
	 * @param string $subaux
	 */
	public function setSubaux($subaux){
		$this->subaux = $subaux;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo es_auxiliar
	 * @param string $es_auxiliar
	 */
	public function setEsAuxiliar($es_auxiliar){
		$this->es_auxiliar = $es_auxiliar;
	}

	/**
	 * Metodo para establecer el valor del campo pide_nit
	 * @param string $pide_nit
	 */
	public function setPideNit($pide_nit){
		$this->pide_nit = $pide_nit;
	}

	/**
	 * Metodo para establecer el valor del campo pide_ban
	 * @param string $pide_ban
	 */
	public function setPideBan($pide_ban){
		$this->pide_ban = $pide_ban;
	}

	/**
	 * Metodo para establecer el valor del campo pide_base
	 * @param string $pide_base
	 */
	public function setPideBase($pide_base){
		$this->pide_base = $pide_base;
	}

	/**
	 * Metodo para establecer el valor del campo porc_iva
	 * @param string $porc_iva
	 */
	public function setPorcIva($porc_iva){
		$this->porc_iva = $porc_iva;
	}

	/**
	 * Metodo para establecer el valor del campo pide_fact
	 * @param string $pide_fact
	 */
	public function setPideFact($pide_fact){
		$this->pide_fact = $pide_fact;
	}

	/**
	 * Metodo para establecer el valor del campo pide_centro
	 * @param string $pide_centro
	 */
	public function setPideCentro($pide_centro){
		$this->pide_centro = $pide_centro;
	}

	/**
	 * Metodo para establecer el valor del campo es_mayor
	 * @param string $es_mayor
	 */
	public function setEsMayor($es_mayor){
		$this->es_mayor = $es_mayor;
	}

	/**
	 * Metodo para establecer el valor del campo contrapartida
	 * @param string $contrapartida
	 */
	public function setContrapartida($contrapartida){
		$this->contrapartida = $contrapartida;
	}

	/**
	 * Metodo para establecer el valor del campo cta_retencion
	 * @param string $cta_retencion
	 */
	public function setCtaRetencion($cta_retencion){
		$this->cta_retencion = $cta_retencion;
	}

	/**
	 * Metodo para establecer el valor del campo porc_retenc
	 * @param string $porc_retenc
	 */
	public function setPorcRetenc($porc_retenc){
		$this->porc_retenc = $porc_retenc;
	}

	/**
	 * Metodo para establecer el valor del campo cta_iva
	 * @param string $cta_iva
	 */
	public function setCtaIva($cta_iva){
		$this->cta_iva = $cta_iva;
	}

	/**
	 * Metodo para establecer el valor del campo porcen_iva
	 * @param string $porcen_iva
	 */
	public function setPorcenIva($porcen_iva){
		$this->porcen_iva = $porcen_iva;
	}

	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}


	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo mayor
	 * @return string
	 */
	public function getMayor(){
		return $this->mayor;
	}

	/**
	 * Devuelve el valor del campo clase
	 * @return string
	 */
	public function getClase(){
		return $this->clase;
	}

	/**
	 * Devuelve el valor del campo subclase
	 * @return string
	 */
	public function getSubclase(){
		return $this->subclase;
	}

	/**
	 * Devuelve el valor del campo auxiliar
	 * @return string
	 */
	public function getAuxiliar(){
		return $this->auxiliar;
	}

	/**
	 * Devuelve el valor del campo subaux
	 * @return string
	 */
	public function getSubaux(){
		return $this->subaux;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo es_auxiliar
	 * @return string
	 */
	public function getEsAuxiliar(){
		return $this->es_auxiliar;
	}

	/**
	 * Devuelve el valor del campo pide_nit
	 * @return string
	 */
	public function getPideNit(){
		return $this->pide_nit;
	}

	/**
	 * Devuelve el valor del campo pide_ban
	 * @return string
	 */
	public function getPideBan(){
		return $this->pide_ban;
	}

	/**
	 * Devuelve el valor del campo pide_base
	 * @return string
	 */
	public function getPideBase(){
		return $this->pide_base;
	}

	/**
	 * Devuelve el valor del campo porc_iva
	 * @return string
	 */
	public function getPorcIva(){
		return $this->porc_iva;
	}

	/**
	 * Devuelve el valor del campo pide_fact
	 * @return string
	 */
	public function getPideFact(){
		return $this->pide_fact;
	}

	/**
	 * Devuelve el valor del campo pide_centro
	 * @return string
	 */
	public function getPideCentro(){
		return $this->pide_centro;
	}

	/**
	 * Devuelve el valor del campo es_mayor
	 * @return string
	 */
	public function getEsMayor(){
		return $this->es_mayor;
	}

	/**
	 * Devuelve el valor del campo contrapartida
	 * @return string
	 */
	public function getContrapartida(){
		return $this->contrapartida;
	}

	/**
	 * Devuelve el valor del campo cta_retencion
	 * @return string
	 */
	public function getCtaRetencion(){
		return $this->cta_retencion;
	}

	/**
	 * Devuelve el valor del campo porc_retenc
	 * @return string
	 */
	public function getPorcRetenc(){
		return $this->porc_retenc;
	}

	/**
	 * Devuelve el valor del campo cta_iva
	 * @return string
	 */
	public function getCtaIva(){
		return $this->cta_iva;
	}

	/**
	 * Devuelve el valor del campo porcen_iva
	 * @return string
	 */
	public function getPorcenIva(){
		return $this->porcen_iva;
	}

	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	protected function validation(){

		$this->validate('InclusionIn', array(
			'field' => 'es_auxiliar',
			'domain' => array('S', 'N'),
			'message' => 'El campo "Es Auxiliar?" debe ser "SI" ó "NO"',
			'required' => true
		));

		$this->validate('InclusionIn', array(
			'field' => 'pide_nit',
			'domain' => array('S', 'N'),
			'message' => 'El campo "Pide Tercero?" debe ser "SI" ó "NO"',
			'required' => true
		));

		$this->validate('InclusionIn', array(
			'field' => 'pide_ban',
			'domain' => array('S', 'N'),
			'message' => 'El campo "Para Bancos?" debe ser "SI" ó "NO"',
			'required' => true
		));

		$this->validate('InclusionIn', array(
			'field' => 'pide_base',
			'domain' => array('S', 'N'),
			'message' => 'El campo "Pide Base?" debe ser "SI" ó "NO"',
			'required' => true
		));

		$this->validate('InclusionIn', array(
			'field' => 'pide_fact',
			'domain' => array('S', 'N'),
			'message' => 'El campo "Pide Documento?" debe ser "SI" ó "NO"',
			'required' => true
		));

		$this->validate('InclusionIn', array(
			'field' => 'pide_centro',
			'domain' => array('S', 'N'),
			'message' => 'El campo "Pide Centro de Costo?" debe ser "SI" ó "NO"',
			'required' => true
		));

		$this->validate('InclusionIn', array(
			'field' => 'es_mayor',
			'domain' => array('S', 'N'),
			'message' => 'El campo "Es Cuenta Mayor?" debe ser "SI" ó "NO"',
			'required' => true
		));

		if($this->validationHasFailed()==true){
			return false;
		}

	}

	public function beforeValidationOnCreate(){
		$cuenta = '';
		if($this->tipo!==''){
			if($this->tipo>0&&$this->tipo<10){
				$cuenta.=$this->tipo;
			} else {
				$this->appendMessage(new ActiveRecordMessage('El tipo de cuenta es inválido', 'tipo'));
			}
		}
		if(ltrim($this->mayor, "\n\t0 ")!=''){
			if($this->mayor>0&&$this->mayor<10){
				$cuenta.=$this->mayor;
			} else {
				$this->appendMessage(new ActiveRecordMessage('El mayor de la cuenta es inválido ', 'mayor'));
			}
		}
		if(ltrim($this->clase, "\n\t0 ")!=''){
			$this->clase = sprintf('%02s', $this->clase);
			if($this->clase>0&&$this->clase<100){
				$cuenta.=$this->clase;
			} else {
				$this->appendMessage(new ActiveRecordMessage('La clase de cuenta es inválida', 'clase'));
			}
		}
		if(ltrim($this->subclase, "\n\t0 ")!=''){
			$this->subclase = sprintf('%02s', $this->subclase);
			if($this->subclase>0&&$this->subclase<100){
				$cuenta.=$this->subclase;
			} else {
				$this->appendMessage(new ActiveRecordMessage('La subclase de la cuenta es inválida ('.$this->subclase.')', 'subclase'));
			}
		}
		if(ltrim($this->auxiliar, "\n\t0 ")!=''){
			$this->auxiliar = sprintf('%03s', $this->auxiliar);
			if($this->auxiliar>0&&$this->auxiliar<1000){
				$cuenta.=$this->auxiliar;
			} else {
				$this->appendMessage(new ActiveRecordMessage('El auxiliar de la cuenta es inválido', 'auxiliar'));
			}
		}
		if(ltrim($this->subaux, "\n\t0 ")!=''){
			$this->subaux = sprintf('%03s', $this->subaux);
			if($this->subaux>0&&$this->subaux<1000){
				$cuenta.=$this->subaux;
			} else {
				$this->appendMessage(new ActiveRecordMessage('El sub-auxiliar de la cuenta es inválido ('.$this->subaux.')', 'subaux'));
			}
		}
		$this->cuenta = trim($cuenta);
		$Niif = EntityManager::getEntityInstance('Niif');
		$cuenta = $Niif->findFirst("cuenta='{$this->cuenta}'");
		if($cuenta!=false){
			$this->appendMessage(new ActiveRecordMessage('La cuenta con codigo "'.$this->cuenta.'" ya existe, está asignada a "'.$cuenta->getNombre().'"', 'cuenta'));
			return false;
		}
	}

	public function beforeValidationOnUpdate(){
		$this->tipo = substr($this->cuenta, 0, 1);
		$this->mayor = substr($this->cuenta, 1, 1);
		$this->clase = substr($this->cuenta, 2, 2);
		$this->subclase = substr($this->cuenta, 4, 2);
		$this->auxiliar = substr($this->cuenta, 6, 3);
		$this->subaux = substr($this->cuenta, 9, 3);
	}

	public function beforeSave(){
		$Niif = EntityManager::getEntityInstance('Niif');
		if($this->contrapartida!=''){
			if($Niif->count("cuenta='{$this->contrapartida}' AND es_auxiliar='S'")==0){
				$this->appendMessage(new ActiveRecordMessage('La cuenta contrapartida no existe o no es auxiliar', 'cuenta'));
				return false;
			}
		}
		if($this->cta_retencion!=''){
			if($Niif->count("cuenta='{$this->cta_retencion}' AND es_auxiliar='S'")==0){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de retención no existe o no es auxiliar', 'cuenta'));
				return false;
			}
		}
		if($this->cta_iva){
			if($Niif->count("cuenta='{$this->cta_iva}' AND es_auxiliar='S'")==0){
				$this->appendMessage(new ActiveRecordMessage('La cuenta de IVA no existe o no es auxiliar', 'cuenta'));
				return false;
			}
		}
		return true;
	}

	public function beforeDelete(){
		$Niif = EntityManager::getEntityInstance('Niif');
		$length = strlen($this->cuenta);
		if($Niif->count("cuenta LIKE '{$this->cuenta}%' AND LENGTH(cuenta)>$length")){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la cuenta porque tiene subdivisiones', 'cuenta'));
			return false;
		}
		if($this->countMovi()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la cuenta porque tiene movimiento', 'cuenta'));
			return false;
		}
		if($this->countSaldosc()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la cuenta porque tiene saldos acumulados', 'cuenta'));
			return false;
		}
		if($this->countSaldosn()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la cuenta porque tiene saldos de terceros', 'cuenta'));
			return false;
		}
		if($this->countSaldosp()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la cuenta porque tiene presupuesto asociado', 'cuenta'));
			return false;
		}
		if($this->countCuentasBancos()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la cuenta porque se está usando en cuentas bancarias', 'cuenta'));
			return false;
		}
		if($this->countCartera()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la cuenta porque se está usando en cartera', 'cuenta'));
			return false;
		}
		if($this->countComcier()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la cuenta porque se está usando en cuentas de cierre', 'cuenta'));
			return false;
		}
		if($this->countConcepto()){
			$this->appendMessage(new ActiveRecordMessage('No se puede eliminar la cuenta porque se está usando los conceptos de Nomina', 'cuenta'));
			return false;
		}
	}

	public function afterSave(){
		if(self::$_checkParents==true){
			$parteCuenta = '';
			$partes = array('tipo', 'mayor', 'clase', 'subclase', 'auxiliar', 'subaux');
			foreach($partes as $parte){
				$parteCuenta.=$this->readAttribute($parte);
				if($parteCuenta==$this->cuenta){
					break;
				} else {
					$existeCuenta = EntityManager::get('Niif')->count("cuenta='$parteCuenta'");
					if(!$existeCuenta){
						self::$_checkParents = false;
						$cuenta = clone $this;
						$tipo = substr($parteCuenta, 0, 1);
						$mayor = substr($parteCuenta, 1, 1);
						$clase = substr($parteCuenta, 2, 2);
						$subclase = substr($parteCuenta, 4, 2);
						$auxiliar = substr($parteCuenta, 6, 3);
						$subaux = substr($parteCuenta, 9, 3);
						$cuenta->setCuenta($parteCuenta);
						$cuenta->setTipo($tipo);
						$cuenta->setMayor($mayor);
						$cuenta->setClase($clase);
						$cuenta->setSubclase($subclase);
						$cuenta->setAuxiliar($auxiliar);
						$cuenta->setSubaux($subaux);
						$cuenta->setEsAuxiliar('N');
						$cuenta->setPideBan('N');
						$cuenta->setPideBase('N');
						$cuenta->setPideCentro('N');
						$cuenta->setPideNit('N');
						$cuenta->setPideFact('N');
						if($cuenta->save()==false){
							return false;
						}
						self::$_checkParents = true;
					}
				}
			}
		}
		return true;
	}

	public function initialize() {

	}

}

