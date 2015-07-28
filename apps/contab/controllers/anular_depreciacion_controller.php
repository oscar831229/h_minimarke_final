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
 * Anular_DepreciacionController
 *
 * Anula el proceso de depreciación mensual activo
 */
class Anular_DepreciacionController extends ApplicationController
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
		$fecha = new Date();
		$ultimaDepreciacion = $this->Depreciacion->maximum('ano_mes');
		if ($ultimaDepreciacion == $fecha->getPeriod()) {
			$this->setParamToView('periodoActivo', $fecha->getPeriod());
			$this->setParamToView('message', 'Utilice esta opción para anular la depreciación mensual y eliminar los comprobantes asociados');
		} else {
			return $this->routeToAction('noProceso');
		}
	}

	public function noProcesoAction()
	{

	}

	public function anularAction()
	{
		$this->setResponse('json');
		try {
			$transaction = TransactionManager::getUserTransaction();

			$fecha = new Date();
			$periodoActivo = $fecha->getPeriod();

			$comprobantes = array();
			$this->Depreciacion->setTransaction($transaction);
			foreach ($this->Depreciacion->find("ano_mes='$periodoActivo'") as $depreciacion) {
				$comprobantes[$depreciacion->getComprob()][$depreciacion->getNumero()] = 0;
				if ($depreciacion->delete()==false) {
					foreach ($depreciacion->getMessages() as $message) {
						$transaction->rollback($message->getMessage());
					}
				}
			}

			try {
				foreach ($comprobantes as $comprob => $numeroComprobs) {
					foreach ($numeroComprobs as $numero => $zero) {
						$aura = new Aura($comprob, $numero, null, Aura::OP_DELETE);
						$aura->delete();
					}
				}
			}
			catch(AuraException $e){
				return array(
					'status' => 'FAILED',
					'message' => $e->getMessage()
				);
			}

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
			'message' => 'Se anuló la depreciación correctamente'
		);
	}

}