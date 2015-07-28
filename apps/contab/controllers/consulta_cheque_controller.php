<?php

class Consulta_ChequeController extends ApplicationController {

	public function initialize(){
		$this->setTemplateAfter(array('main', 'menu'));
	}

	public function indexAction(){
		GarbageCollector::freeAllMetaData();
	}

	public function buscarAction(){
		$chequeraId = $this->getPostParam('chequeraId', 'int');
		$numeroInicial = $this->getPostParam('numeroInicial', 'int');
		$numeroFinal = $this->getPostParam('numeroFinal', 'int');
		$chequesEmitidos = $this->Cheque->find("chequeras_id='{$chequeraId}' AND numero_cheque >= $numeroInicial AND numero_cheque <= $numeroFinal");
		/*if(count($cheques)==0){
			Flash::notice('No se encontraron cheques en la búsqueda');
			return $this->routeTo('action: index');
		}*/
		$cheques = array();
		foreach($chequesEmitidos as $cheque){
			$cheques[$cheque->getNumeroCheque()] = $cheque;
		}
		$chequera = $this->Chequeras->findFirst($chequeraId);
		if($chequera==false){
			Flash::error('No hay chequeras creadas');
			return $this->routeTo('action: index');
		}
		$this->setParamToView('cheques', $cheques);
		$this->setParamToView('chequera', $chequera);
		$this->setParamToView('numeroInicial', $numeroInicial);
		$this->setParamToView('numeroFinal', $numeroFinal);
	}

}
