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
 * Cierre_PeriodoController
 *
 * Realiza el cierre contable
 *
 */
class Cierre_PeriodoController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{
		try
		{
			$datosClub = $this->DatosClub->findFirst();
			$fechaCierre = $datosClub->getFCierre();

			$nuevoCierre = clone $fechaCierre;
			$nuevoCierre->addDays(1);
			$nuevoCierre->toLastDayOfMonth();
			$this->setParamToView('proximoCierre', $nuevoCierre);

			$this->setParamToView('fechaCierre', $fechaCierre);

			$this->setParamToView('message', 'Haga click en "Hacer Cierre" para cerrar el periodo actual');
		}
		catch(Exception $e){
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
			
			Core::importFromLibrary('Hfos/Socios','SociosCore.php');

			$allMessages = array();

			$transaction = TransactionManager::getUserTransaction();
			
			#Suspendemos socios
			$autosupender = Settings::get("autosuspender_usar", 'SO');
			if ($autosupender=='S') {
				$sociosFactura = new SociosFactura(); 
				$sociosFactura->checkAutoSuspencion();
			}

			$datosClub = $this->DatosClub->findFirst();
			$ultimoCierre = $datosClub->getFCierre();

			$ultimoCierre->toLastDayOfMonth();
			$fechaCierre = clone $ultimoCierre;
			$fechaCierre->addDays(1);
			$fechaCierre->toLastDayOfMonth();

			if(Date::isEarlier($fechaCierre, $ultimoCierre)){
				$transaction->rollback('El periodo ya fue cerrado');
			}

			$periodoCierre = $fechaCierre->getPeriod();
			$periodoUltimoCierre = $ultimoCierre->getPeriod();

			SociosCore::checkPeriodo($periodoCierre, $transaction);
			SociosCore::checkPeriodo($fechaCierre->getPeriod(), $transaction);
			$periodo = SociosCore::getCurrentPeriodoObject();
			if (!$periodo) {
				throw new SociosException("No se encontró un periodo sin cerrado por favor revisar periodos");
			}
			$periodo->setTransaction($transaction);
			$periodo->setCierre('S');
			$periodo->setFacturado('S');

			if ($periodo->save()==false) {
				foreach ($periodo->getMessages() as $message)
				{
					$transaction->rollback('periodo: '.$message->getMessage());
				}
			}

			$nuevoCierre = clone $fechaCierre;
			$nuevoCierre->addDays(1);
			$proximoCierre = Date::getLastDayOfMonth($nuevoCierre->getMonth(), $nuevoCierre->getYear());

			//creamos periodos
			$peridoActual = SociosCore::makePeriodo($fechaCierre->getPeriod());
			$periodoProximo = SociosCore::makePeriodo($proximoCierre->getPeriod());

			//Cambiano a no cerrado el proximo cierre
			$peridoNuevo = SociosCore::getCurrentPeriodoObject($nuevoCierre->getPeriod());
			$peridoNuevo->setTransaction($transaction);
			$peridoNuevo->setCierre('N');
			$peridoNuevo->setFacturado('N');

			if ($peridoNuevo->save()==false) {
				foreach ($peridoNuevo->getMessages() as $message)
				{
					$transaction->rollback('periodoNuevo: '.$message->getMessage());
				}
			}

			//Guardamos la fecha de cierre
			
			$datosClub->setFCierre($fechaCierre->getDate());
			if ($datosClub->save()==false) {
				foreach ($datosClub->getMessages() as $message)
				{
					$transaction->rollback('datos_club: '.$message->getMessage());
				}
			}

			//limpiamos os cargos fijos temporales
			$limpiarCargoTemp = Settings::Get('limpiar_cargos_temporales', 'SO');
			if ($limpiarCargoTemp=='S') {
				$cargosFijosTemporales = EntityManager::get('CargosFijos')->find(array('conditions'=>"tipo_cargo='T'"));
				$temporales = array();
				foreach ($cargosFijosTemporales as $cargo)
				{
					$temporales[]= $cargo->getId();
				}
				if(count($temporales)>0){
					$cargosSociosDel = EntityManager::get('CargosSocios')->setTransaction($transaction)->delete(array('conditions'=>'cargos_fijos_id IN('.implode(',',$temporales).') AND periodo="'.$periodoCierre.'"'));
				}
			}

			//Reporte de suspendidos
			$url = '';
			if ($autosupender=='S') {
				$sociosReports = new SociosReports();
				$file = $sociosReports->getReportSuspendidos();
				$url = 'temp/'.$file;
			}

			//Creamos Comprobante de saldos a favor
			$moverSaldosAFavor = Settings::get('mover_saldoafavor','SO');
			if ($moverSaldosAFavor=='S') {
				$sociosFactura->pasarSaldosAFavor();
			}
			
			$transaction->commit();

			return array(
				'status' => 'OK',
				'proximoCierre' => $proximoCierre->getLocaleDate('long'),
				'cierreActual' => $fechaCierre->getLocaleDate('short'),
				'url' => $url
			);
		}
		catch(SociosException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}