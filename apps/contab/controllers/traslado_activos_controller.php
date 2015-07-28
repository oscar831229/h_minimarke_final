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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Traslado_ActivosController
 *
 * Controlador de los traslado activos
 *
 */
class Traslado_ActivosController extends ApplicationController
{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{
		$this->setParamToView('message', 'Seleccione el dato origen y destino y haga click en "Trasladar"');
		$this->setParamToView('centros', $this->Centros->find('order: nom_centro'));
		$this->setParamToView('ubicaciones', $this->Ubicacion->find('order: nom_ubica'));
	}

	public function trasladarAction()
	{
		$this->setResponse('json');

		try {

			$response = array();

			$transaction = TransactionManager::getUserTransaction();
			$this->Activos->setTransaction($transaction);

			$hizoTraslado = false;

			$centroInicial = $this->getPostParam('centroInicial', 'int');
			$centroFinal = $this->getPostParam('centroFinal', 'int');
			if($centroInicial>0&&$centroFinal>0){
				foreach($this->Activos->find("centro_costo='$centroInicial'") as $activo){
					$activo->setCentroCosto($centroFinal);
					if($activo->save()==false){
						foreach($activo->getMessages() as $message){
							$response['message'] = $activo->getDescripcion().': '.$message->getMessage();
							$transaction->rollback();
						}
					}
				}
				$hizoTraslado = true;
			}

			$ubicacionInicial = $this->getPostParam('ubicacionInicial', 'int');
			$ubicacionFinal = $this->getPostParam('ubicacionFinal', 'int');
			if($ubicacionInicial>0&&$ubicacionFinal>0){
				foreach($this->Activos->find("ubicacion='$ubicacionInicial'") as $activo){
					$activo->setUbicacion($ubicacionFinal);
					if($activo->save()==false){
						foreach($activo->getMessages() as $message){
							$response['message'] = $activo->getDescripcion().': '.$message->getMessage();
							$transaction->rollback();
						}
					}
				}
				$hizoTraslado = true;
			}

			$responsableInicial = $this->getPostParam('responsableInicial', 'alpha');
			$responsableFinal = $this->getPostParam('responsableFinal', 'alpha');
			if($responsableInicial&&$responsableFinal){
				foreach($this->Activos->find("responsable='$responsableInicial'") as $activo){
					$activo->setResponsable($responsableFinal);
					if($activo->save()==false){
						foreach($activo->getMessages() as $message){
							$response['message'] = $activo->getDescripcion().': '.$message->getMessage();
							$transaction->rollback();
						}
					}
				}
				$hizoTraslado = true;
			}

			$transaction->commit();

		}
		catch(TransactionFailed $e){
			$response['status'] = 'FAILED';
			return $response;
		}
		if($hizoTraslado==false){
			return array(
				'status' => 'FAILED',
				'message' => 'Indique los parámetros de traslado'
			);
		} else {
			return array(
				'status' => 'OK'
			);
		}
	}

}

