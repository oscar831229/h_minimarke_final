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


/**
 * Cambio_contratosController
 *
 * Clase controller que cambia el numero de contrato de un contrato
 *
 */
class Cambio_contratosController extends ApplicationController {

    public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	/**
	 * Metodo que carga cosas al ingresar al formulario de inicio
	 *
	 */
	public function indexAction(){
		$this->setParamToView('message', 'Indique los datos para generar el cambio de número de contrato');
		//Tag::displayTo('cuotaInicial', 0);
		//Tag::displayTo('saldoPagar', 0);
		//Tag::displayTo('fechaPrimeraCuota', Date::getCurrentDate());
	}

	/**
	 * Metodo que genera tabla de amortizacion
	 *
	 * @return json
	 */
	public function generarAction(){
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();
		try {
			$rules = array(
				'numeroContrato' => array(
					'message' => 'Debe indicar el nuevo número de contrato',
					'filter' => 'alpha'
				),
				'sociosId' => array(
					'message' => 'Debe indicar la el socio a cambiar',
					'filter' => 'int'
				)
			);
			if($this->validateRequired($rules)==false){
				foreach($this->getValidationMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

		    $sociosId = $this->getPostParam('sociosId', 'int');
		    if(!$sociosId){
		        $this->addValidationMessage($rules['sociosId']['message'],'sociosId');
		    }
		    $numeroContrato = $this->getPostParam('numeroContrato');
		    if(!$numeroContrato){
		        $this->addValidationMessage($rules['numeroContrato']['message'],'numeroContrato');
		    }
		    foreach($this->getValidationMessages() as $message){
				$transaction->rollback($message->getMessage());
			}

			$socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($sociosId);
		    if($socios==false){
		    	$transaction->rollback('El socio con id '.$sociosId.' no existe!');
		    }
		    
		    //Verificamos si exite numero de contrato en un contrato activo
		    $sociosExists = EntityManager::get('Socios')->setTransaction($transaction)->findFirst(array('conditions'=>"numero_contrato='$numeroContrato' AND estado_contrato='A'"));
		    if($sociosExists!=false){
		    	$transaction->rollback("El número de contrato '$numeroContrato' ya existe en un contrato activo!");
		    }
		    
		    $socios->setNumeroContrato($numeroContrato);
		    if($socios->save()==false){
		    	foreach ($socios->getMessages() as $message) {
		    		$transaction->rollback($message->getMessage());
		    	}
		    }

		    $transaction->commit();

			return array(
				'status' => 'OK',
				'message' => 'Se realizó el cambio de número de contrato correctamente'
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

}
