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
 * @copyright 	BH-TECK Inc. 2009-2013
 * @version		$Id$
 */

/**
 * CerrarController
 *
 * Controlador del cierre de inventarios
 *
 */
class CerrarController extends ApplicationController
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

		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrei();

		$this->setParamToView('fechaCierre', $fechaCierre);

		$proximoCierre = clone $fechaCierre;
		$proximoCierre->addMonths(1);
		$proximoCierre->toLastDayOfMonth();

		$this->setParamToView('proximoCierre', $proximoCierre);

		$this->setParamToView('message', 'Haga click en "Hacer Cierre" para cerrar el periodo actual de inventarios');
	}

	public function processAction()
	{
	    // Se da tiempo necesario para completar transacciones
	    set_time_limit(0);

		$this->setResponse('json');

		$empresa = BackCacher::getEmpresa();
		$fechaCierre = $empresa->getFCierrei();
		//$periodo = $fechaCierre->getPeriod();

		$fechaProximo = clone $fechaCierre;
		$fechaProximo->addMonths(1);
		$fechaProximo->toLastDayOfMonth();
		$periodo = $fechaProximo->getPeriod();

		if(Date::isEarlier($fechaProximo, $fechaCierre)){
			return array(
				'status' => 'FAILED',
				'message' => 'Periodo "'.$periodo.'" ya cerrado'
			);
		} else {
			if($fechaProximo->isFuture()){
				return array(
					'status' => 'FAILED',
					'message' => 'Todavia no se puede hacer el cierre de inventarios de '.$fechaProximo->getMonthName()
				);
			}
		}

		try
		{

			$transaction = TransactionManager::getUserTransaction();

			Rcs::disable();
			TaticoKardex::setFastRecalculate(true);

			foreach (BackCacher::getTodosAlmacenes() as $almacen) {
				foreach ($this->Inve->find("estado = 'A'") as $inve) {
					BackCacher::setInve($inve);
					TaticoKardex::show($inve->getItem(), $almacen->getCodigo(), '1999-01-01');
					unset($inve);
				}
				unset($almacen);
			}

			$empresa->setTransaction($transaction);
			$this->Inve->setTransaction($transaction);
			$this->Saldos->setTransaction($transaction);
			$this->Movilin->setTransaction($transaction);

			$empresa->setFCierrei((string) $fechaProximo);
			if ($empresa->save() == false) {
				foreach ($empresa->getMessages() as $message) {
					$transaction->rollback('Empresa: '.$message->getMessage());
					unset($message);
				}
			}

			$saldos = EntityManager::get('Saldos')->setTransaction($transaction)->find('ano_mes=0 AND saldo<0 AND almacen!=0');
			foreach ($saldos as $saldo) {
				$inve = BackCacher::getInve($saldo->getItem());
				$almacen = BackCacher::getAlmacen($saldo->getAlmacen());
				return array(
					'status' => 'FAILED',
					'message' => 'No se puede cerrar el periodo porque la referencia '.$inve->getItem().'/'.$inve->getDescripcion().' tiene saldo negativo en el almacén '.$saldo->getAlmacen().'/'.$almacen->getNomAlmacen()
				);
				unset($saldo);
			}

			/*return array(
				'status' => 'FAILED',
				'message' => "periodo: $periodo, fechaChierre: $fechaCierre, fechaProximo: $fechaProximo"
			);*/

			$saldos = EntityManager::get('Saldos')->setTransaction($transaction)->find('ano_mes=0');
			foreach ($saldos as $saldo)	{
				$saldoPeriodoAnterior = clone $saldo;
				$saldoPeriodoAnterior->setAnoMes($periodo);
				if ($saldoPeriodoAnterior->save() == false) {
					foreach ($saldoPeriodoAnterior->getMessages() as $message) {
						$transaction->rollback('Saldos: '.$message->getMessage());
						unset($message);
					}
				}
				unset($saldo,$saldoPeriodoAnterior);
			}

			new EventLogger('CERRÓ EL PERIODO "'.$periodo.'" EN INVENTARIO', 'A', $transaction);

			$transaction->commit();

		}
		catch (TransactionFailed $e) {
			return array(
			    'status' => 'FAILED',
			    'message' => $e->getMessage()
			);
		}

		$fechaCierre = clone $fechaProximo;
		$fechaProximo->addDays(1);
		$fechaProximo->toLastDayOfMonth();

		return array(
			'status' => 'OK',
			'message' => 'Se cerró el periodo exitosamente',
			'proximoCierre' => $fechaProximo->getLocaleDate('long'),
			'cierreActual' => $fechaCierre->getLocaleDate('medium')
		);
	}

}
