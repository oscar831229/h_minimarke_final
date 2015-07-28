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
 * ApplicationController
 *
 * Controlador del Kardex
 *
 */
class ReabrirController extends ApplicationController
{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
	}

	public function indexAction()
	{

		$fechaCierre = $this->Empresa->findFirst()->getFCierrei();

		$anteriorCierre = clone $fechaCierre;
		$anteriorCierre->diffMonths(1);
		$anteriorCierre->toLastDayOfMonth();

		$this->setParamToView('anteriorCierre', $anteriorCierre);
		$this->setParamToView('fechaCierre', $fechaCierre);

		$this->setParamToView('message', 'Haga click en "Reabrir Mes" para volver al periodo de inventarios anterior');
	}

	public function processAction()
	{
		$this->setResponse('json');

		try {

			$transaction = TransactionManager::getUserTransaction();

			$this->Saldos->setTransaction($transaction);

			$empresa = BackCacher::getEmpresa();
			$empresa->setTransaction($transaction);
			$fechaCierre = $empresa->getFCierrei();
			$fechaCierre->toLastDayOfMonth();

			if ($this->Movihead->count("fecha>'$fechaCierre' AND comprob NOT LIKE 'O%' AND comprob NOT LIKE 'P%'") > 0) {
				$transaction->rollback('No se puede reabrir el mes porque ya hay movimiento en el periodo actual');
			}

			$periodoActual = $fechaCierre->getPeriod();
			$anteriorCierre = clone $fechaCierre;
			$anteriorCierre->diffMonths(1);
			$anteriorCierre->toLastDayOfMonth();

			$empresa->setFCierrei((string) $anteriorCierre);
			if ($empresa->save() == false) {
				foreach ($empresa->getMessages() as $message) {
					$transaction->rollback('Empresa: ' . $message->getMessage());
				}
			}

			Rcs::disable();
			TaticoKardex::setFastRecalculate(true);
			foreach (BackCacher::getTodosAlmacenes() as $almacen) {
				foreach ($this->Inve->find() as $inve) {
					BackCacher::setInve($inve);
					TaticoKardex::show($inve->getItem(), $almacen->getCodigo(), '1999-01-01');
				}
			}

			$this->Saldos->setTransaction($transaction);
			$this->Saldos->deleteAll("ano_mes='$periodoActual'");

			$periodoAnterior = $anteriorCierre->getPeriod();
			new EventLogger('REABRIÓ EL PERIODO "' . $periodoAnterior . '" EN INVENTARIO', 'A', $transaction);

			$transaction->commit();

		} catch (TransactionFailed $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

		return array(
			'status' => 'OK',
			'message' => 'Se reabrió el mes correctamente',
			'anteriorCierre' => $fechaCierre->getDate(),
			'cierreActual' => $anteriorCierre->getDate(),
		);
	}

}
