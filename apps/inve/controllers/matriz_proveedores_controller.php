<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Matriz_ProveedoresController extends ApplicationController{

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

	}

	/**
	 * Carga los proveedores en la matriz
	 *
	 */
	public function generarMatrizAction(){

		$this->setResponse('view');

		//Obtenemos los datos enviados por POST
		$controllerRequest = ControllerRequest::getInstance();
		$referenciaId = $controllerRequest->getParamPost('referenciaId', 'alpha');

		if(!$referenciaId){
			Flash::notice('Por favor ingrese una referencia');
			return;
		}

		$referencia = BackCacher::getInve($referenciaId);
		if($referencia==false){
			Flash::error('No existe la referencia '.$referenciaId);
			return;
		}

		$matrizProveedores = array();
		$matrizProveedoresObj = $this->MatrizProveedores->find("item='$referenciaId'");
		if(!count($matrizProveedoresObj)){

			$movilins = $this->Movilin->find("item='$referenciaId'", "order: valor desc", "limit: 5");

			$matrizProveedores = array();
			$matrizProveedoresTabu = array();

			foreach($movilins as $movilin){
				if(!isset($matrizProveedoresTabu[$referenciaId][$movilin->getMovihead()->getNit()])){
					$nit = $movilin->getMovihead()->getNit();
					$item = $movilin->getItem();

					$nits = $this->Nits->findFirst("nit='$nit'");
					if($nits){
						$nitNombre = "{$nits->getNombre()}";
					} else {
						continue;
					}

					$matrizProveedores[] = array(
						'nitId' => $nit,
						'nitNombre' => $nitNombre,
						'prioridad' => 0,
						'checked' => false
					);
					$matrizProveedoresTabu[$referenciaId][$movilin->getMovihead()->getNit()]=true;
				}
			}

		} else {

			foreach($matrizProveedoresObj as $matrizProveedor){

				$nit = BackCacher::getTercero($matrizProveedor->getNit());
				if($nit){
					$nombreNit = $nit->getNombre();
				} else {
					$nombreNit = 'NO EXISTE EL TERCERO';
				}
				$matrizProveedores[] = array(
					'nitId' => $matrizProveedor->getNit(),
					'nitNombre' => $nombreNit,
					'prioridad' => $matrizProveedor->getPreferencia(),
					'checked' => true
				);

			}
		}

		$this->setParamToView('referenciaId', $referenciaId);
		$this->setParamToView('matrizProveedores', $matrizProveedores);

	}

	/**
	 *
	 * Metodo que guarda los proveedores de una referencia
	 */
	public function guardarAction(){

		$this->setResponse("json");

		$controllerRequest = ControllerRequest::getInstance();
		$referenciaId = $controllerRequest->getParamPost('referenciaId', 'alpha');
		if(!$referenciaId){
			return array(
				'status' => 'FAILED',
				'message' => 'Debe ingresar una referencia'
			);
		}

		$nits = $controllerRequest->getParamPost('nit', 'terceros');
		if(is_array($nits)){

			try {

				$transaction = TransactionManager::getUserTransaction();

				$this->MatrizProveedores->setTransaction($transaction);

				//borramos los proveedores de esa referencia
				$this->MatrizProveedores->deleteAll("item=".$referenciaId);

				$preferencia = 1;
				foreach($nits as $nit){
					$matrizProveedores = new MatrizProveedores();
					$matrizProveedores->setTransaction($transaction);
					$matrizProveedores->setNit($nit);
					$matrizProveedores->setItem($referenciaId);
					$matrizProveedores->setPreferencia($preferencia);
					if($matrizProveedores->save()==false){
						foreach($matrizProveedores->getMessages() as $messages){
							$transaction->rollback($messages->getMessage());
						}
					}
					$preferencia++;
				}

				$transaction->commit();
			}
			catch(TransactionFailed $e){
				return array(
					'status' => 'FAILED',
					'message' => $e->getMessage()
				);
			}
		}

		return array(
			'status' => 'OK',
			'message' => 'Se guardó la matriz de proveedores correctamente'
		);

	}
}
