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

Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
/**
 * Cierre_PeriodoController
 *
 * Realiza el cierre contable
 *
 */
class Cierre_PeriodoController extends ApplicationController {

	public function initialize() {
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{
		try {

			$periodoActual = SociosCore::getCurrentPeriodo();
			$proximoPeriodo = SociosCore::addPeriodo($periodoActual, 1);

			$this->setParamToView('periodoActual', $periodoActual);
			$this->setParamToView('proximoPeriodo', $proximoPeriodo);
			$this->setParamToView('message', 'Haga click en "Hacer Cierre" para cerrar el periodo actual');
		} catch (Exception $e) {

			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function cierreAction()
	{
		$this->setResponse('json');

		try {
			
			set_time_limit(0);
			
			Core::importFromLibrary('Hfos/Socios','SociosFactura.php');

			$allMessages = array();

			$transaction = TransactionManager::getUserTransaction();
			
			#Suspendemos socios
			$autosupender = Settings::get("autosuspender_usar", 'SO');
			if ($autosupender == 'S') {
				$sociosFactura = new SociosFactura(); 
				$sociosFactura->checkAutoSuspencion();
			}

			$datosClub = $this->DatosClub->findFirst();
			
			//Periodo actual
			$periodoActual = $periodo = SociosCore::getCurrentPeriodo();
			$ano = substr($periodo, 0, 4);
            $mes = substr($periodo, 4, 2);
            $fechaPeriodoAbierto = "$ano-$mes-01";
			$fechaCierre = new Date($fechaPeriodoAbierto);
			$fechaCierre->toLastDayOfMonth();
			$periodoCierre = $fechaCierre->getPeriod();
			$proximoPeriodo = SociosCore::addPeriodo($periodoCierre, 1);
			//throw new Exception("periodoActual: $periodo, nuevoPeriodo: $nuevoPeriodo", 1);
			
			//verificamos el actual periodo
			SociosCore::checkPeriodo($periodoCierre, $transaction);

			//Cerramos periodo actual
			$periodo = SociosCore::getCurrentPeriodoObject();
			if (!$periodo) {
				throw new SociosException("No se encontró un periodo sin cerrado por favor revisar periodos");
			}
			$periodo->setTransaction($transaction);
			$periodo->setCierre('S');
			$periodo->setFacturado('S');

			if ($periodo->save() == false) {
				foreach ($periodo->getMessages() as $message) {
					$transaction->rollback('periodo: ' . $message->getMessage());
				}
			}

			$nuevoCierre = clone $fechaCierre;
			$nuevoCierre->addDays(1);
			$proximoCierre = Date::getLastDayOfMonth($nuevoCierre->getMonth(), $nuevoCierre->getYear());

			//creamos periodos
			$peridoActual = SociosCore::checkPeriodo($periodoActual);
			$periodoProximo = SociosCore::checkPeriodo($proximoPeriodo);

			//Cambiano a no cerrado el proximo cierre
			$peridoNuevo = SociosCore::getCurrentPeriodoObject($nuevoCierre->getPeriod());
			$peridoNuevo->setTransaction($transaction);
			$peridoNuevo->setCierre('N');
			$peridoNuevo->setFacturado('N');

			if ($peridoNuevo->save() == false) {
				foreach ($peridoNuevo->getMessages() as $message) {
					$transaction->rollback('periodoNuevo: ' . $message->getMessage());
				}
			}

			//Guardamos la fecha de cierre
			$datosClub->setFCierre($fechaCierre->getDate());
			if ($datosClub->save() == false) {
				foreach ($datosClub->getMessages() as $message)
				{
					$transaction->rollback('datos_club: ' . $message->getMessage());
				}
			}

			//limpiamos os cargos fijos temporales
			$limpiarCargoTemp = Settings::Get('limpiar_cargos_temporales', 'SO');
			if ($limpiarCargoTemp == 'S') {
				$cargosFijosTemporales = EntityManager::get('CargosFijos')->find(array('conditions'=>"tipo_cargo='T'"));
				$temporales = array();
				foreach ($cargosFijosTemporales as $cargo) {
					$temporales[]= $cargo->getId();
				}
				if (count($temporales) > 0) {
					$cargosSociosDel = EntityManager::get('CargosSocios')->setTransaction($transaction)->delete(array('conditions'=>'cargos_fijos_id IN('.implode(',',$temporales).') AND periodo="'.$periodoCierre.'"'));
				}
			}

			//Reporte de suspendidos
			$url = '';
			if ($autosupender == 'S') {
				$sociosReports = new SociosReports();
				$file = $sociosReports->getReportSuspendidos();
				$url = 'temp/' . $file;
			}

			//Creamos Comprobante de saldos a favor
			$moverSaldosAFavor = Settings::get('mover_saldoafavor','SO');
			if ($moverSaldosAFavor == 'S') {
				$sociosFactura->pasarSaldosAFavor();
			}

			//Creamos siguiente periodo
			SociosCore::checkPeriodo($periodoCierre, $transaction);
			
			$transaction->commit();

			$nuevoProximoPeriodo = SociosCore::addPeriodo($proximoPeriodo, 1);
			return array(
				'status' => 'OK',
				'proximoPeriodo' => $nuevoProximoPeriodo,
				'periodoActual' => $proximoPeriodo,
				'url' => $url
			);
		} catch(SociosException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		} catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage() . ", trace: " . print_r($e->getTrace(), true)
			);
		}
	}

}