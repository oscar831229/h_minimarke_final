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

class Pres extends RcsRecord {

	/**
	 * @var string
	 */
	protected $cuenta;

	/**
	 * @var integer
	 */
	protected $centro_costo;

	/**
	 * @var integer
	 */
	protected $ano;

	/**
	 * @var integer
	 */
	protected $mes;

	/**
	 * @var string
	 */
	protected $pres;


	/**
	 * Metodo para establecer el valor del campo cuenta
	 * @param string $cuenta
	 */
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}

	/**
	 * Metodo para establecer el valor del campo centro_costo
	 * @param integer $centro_costo
	 */
	public function setCentroCosto($centro_costo){
		$this->centro_costo = $centro_costo;
	}

	/**
	 * Metodo para establecer el valor del campo ano
	 * @param integer $ano
	 */
	public function setAno($ano){
		$this->ano = $ano;
	}

	/**
	 * Metodo para establecer el valor del campo mes
	 * @param integer $mes
	 */
	public function setMes($mes){
		$this->mes = $mes;
	}

	/**
	 * Metodo para establecer el valor del campo pres
	 * @param string $pres
	 */
	public function setPres($pres){
		$this->pres = $pres;
	}


	/**
	 * Devuelve el valor del campo cuenta
	 * @return string
	 */
	public function getCuenta(){
		return $this->cuenta;
	}

	/**
	 * Devuelve el valor del campo centro_costo
	 * @return integer
	 */
	public function getCentroCosto(){
		return $this->centro_costo;
	}

	/**
	 * Devuelve el valor del campo ano
	 * @return integer
	 */
	public function getAno(){
		return $this->ano;
	}

	/**
	 * Devuelve el valor del campo mes
	 * @return integer
	 */
	public function getMes(){
		return $this->mes;
	}

	/**
	 * Devuelve el valor del campo pres
	 * @return string
	 */
	public function getPres(){
		return $this->pres;
	}

	protected function beforeSave(){
		$empresa = EntityManager::get('Empresa')->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		$fechaCierre->addMonths(1);
		$periodo = $this->ano.$this->mes;
		if($fechaCierre->getPeriod()==$periodo){
			$periodo = 0;
		}
		$conditions = "centro_costo={$this->centro_costo} AND cuenta='{$this->cuenta}' AND ano_mes='{$periodo}'";
		$saldosp = EntityManager::get('Saldosp')->findFirst($conditions);
		if($saldosp==false){
			$saldosp = new Saldosp();
			$saldosp->setCentroCosto($this->centro_costo);
			$saldosp->setCuenta($this->cuenta);
			$saldosp->setAnoMes($periodo);
			$saldosp->setConnection($this->getConnection());
			$saldosp->setDebe(0);
			$saldosp->setHaber(0);
			$saldosp->setSaldo(0);
		}
		$saldosp->setPres($this->pres);
		if($saldosp->save()==false){
			foreach($saldosp->getMessages() as $message){
				$this->appendMessage(new ActiveRecordMessage('Saldos: '.$message->getMessage(), $message->getField()));
			}
			return false;
		}
	}

	protected function validation(){
		$this->validate('InclusionIn', array(
			'field' => 'mes',
			'domain' => array('01', '02', '03', '04', '04', '05', '06', '07', '08', '09', '10', '11', '12'),
			'message' => 'El campo "Mes" debe ser "ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE" ó "DICIEMBRE"',
			'required' => true
		));
		if($this->validationHasFailed()==true){
			return false;
		}
	}

	public function initialize(){
		$this->addForeignKey('centro_costo', 'Centros', 'codigo', array(
			'message' => 'El centro de costo no es válido'
		));
		$this->addForeignKey('cuenta', 'Cuentas', 'cuenta', array(
			'message' => 'La cuenta contable no es válida'
		));
	}

}

