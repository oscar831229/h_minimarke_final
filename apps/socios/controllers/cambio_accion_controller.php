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
 * Cambio_AccionController
 *
 * Controlador del cambio de una accion del socio
 *
 */
class Cambio_AccionController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
	}

	
	/**
	 * Vista principal
	 *
	 */
	public function indexAction(){
		
		$this->setParamToView('message', 'Seleccione el socio a cambiar y asignele un nuevo numero de acción');
	}

	/**
	 * Cambia el numero de accion de un socio
	 *
	 * 
	 */
	public function cambiarAction(){
	    $this->setResponse('json');

	    try {

	    	$transaction = TransactionManager::getUserTransaction();

			Rcs::disable();
			
			//verificamos si el socio viejo es correcto por su numero de accion
		    $viejoSocioId = $this->getPostParam('socios_id');
		    //Validamos que exista
		    $viejoSocio = BackCacher::getSocios($viejoSocioId);
		    if(!$viejoSocio->getSociosId()){
		        return array(
					'status' => 'FAILED',
					'message' => 'El numero de acción ('.$viejoSocioId.') no existe'
				);
		    }
		    //Validamos que no exista el nuevo socio
		    $nuevoNumero = $this->getPostParam('nuevoNumeroAccion');
		    $nuevoSocio = new Socios();
		    $nuevoSocio->findFirst('numero_accion="'.$nuevoNumero.'"');
		    if($nuevoSocio->getSociosId()){
		        return array(
					'status' => 'FAILED',
					'message' => 'El numero de acción ('.$nuevoNumero.') ya existe intente con otro'
				);
		    }
		    
			$viejoSocio->setTransaction($transaction);
			$viejoSocio->setNumeroAccion((string)$nuevoNumero);
			if($viejoSocio->save()==false){
				foreach($viejoSocio->getMessages() as $message){
					$transaction->rollback('Cambio Accion: '.$message->getMessage());
				}
			}

			new EventLogger('Se cambio la acción '.$viejoSocio->getNumeroAccion().' por el contrato '.$nuevoNumero, 'A', $transaction);

			$transaction->commit();

		}
		catch(TransactionFailed $e){
			return array(
			    'status' => 'FAILED',
			    'message' => $e->getMessage()
			);
		}

		return array(
			'status' => 'OK',
			'message' => 'Se cambio la acción exitosamente'
		);
	}

}
