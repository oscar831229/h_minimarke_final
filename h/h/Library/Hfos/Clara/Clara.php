<?php

class ClaraException extends CoreException {

}

class Clara extends UserComponent {

	/**
	 * Codigo Empleado Inicial
	 *
	 * @var int
	 */
	private $_codigoInicial = 0;

	/**
	 * Codigo Empleado Final
	 *
	 * @var int
	 */
	private $_codigoFinal = 0;

	/**
	 * Transaccion
	 *
	 * @var ActiveRecordTransaction
	 */
	private $_transaction;

	/**
	 * Indica si ya existe una transacción externa
	 *
	 * @var boolean
	 */
	private $_externalTransaction = false;

	/**
	 * Constructor de Clara
	 *
	 */
	public function __construct(){
		$this->_externalTransaction = TransactionManager::hasUserTransaction();
		$this->_transaction = TransactionManager::getUserTransaction();
	}

	/**
	 * Establece el rango de contratos a liquidar
	 *
	 * @param int $codigoInicial
	 * @param int $codigoFinal
	 */
	public function setRangoContratos($codigoInicial, $codigoFinal=0){
		$this->_codigoInicial = $codigoInicial;
		if(!$codigoFinal){
			$this->_codigoFinal = $codigoInicial;
		} else {
			$this->_codigoFinal = $codigoFinal;
		}
	}

	/**
	 * Realiza la liquidación quincenal
	 *
	 */
	public function liquidarQuincenal(){
		$conditions = array();
		if($this->_codigoInicial!=0&&$this->_codigoFinal!=0){
			if($this->_codigoInicial==$this->_codigoFinal){
				$conditions[] = "id='{$this->_codigoInicial}' AND estado='A'";
			} else {
				$conditions[] = "id>='{$this->_codigoInicial}' AND id<='{$this->_codigoFinal}' AND estado='A'";
			}
		}
		foreach($this->Contratos->find(join(' AND ', $conditions)) as $contrato){

			$liquidacion = array();

			$novedades = $this->Novedades->find("contratos_id='{$contrato->getId()}'");
			foreach($novedades as $novedad){

			}

			$conceptosBasicos = $this->ConceptosBasicos->find(array("estado='A'", 'order' => 'codigo'));
			foreach($conceptosBasicos as $conceptoBasico){
				//$liquidacion
			}

		}
	}

}