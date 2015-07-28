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
 * DepreciacionController
 *
 * Controlador de las cuentas de depreciacion
 *
 */
class DepreciacionController extends ApplicationController {

	private $_depreciacion = array();

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$ultimaDepreciacion = $this->Depreciacion->maximum('ano_mes');
		if($ultimaDepreciacion==''){
			$ultimaDepreciacion = 'Ninguna';
		}
		$this->setParamToView('ultimaDepreciacion', $ultimaDepreciacion);
		$this->setParamToView('message', 'Indique Definitiva "no" para simular un proceso de depreciación');
	}

	public function generarAction(){
		$this->setResponse('json');
		try {

			$transaction = TransactionManager::getUserTransaction();
			$this->Activos->setTransaction($transaction);

			$fechaProceso = new Date();
			$periodo = $fechaProceso->getPeriod();

			$primerDiaMes = Date::getFirstDayOfMonth();

			$comprobDeprec = Settings::get('comprob_deprec');
			$comprob = BackCacher::getComprob($comprobDeprec);
			if($comprob==false){
				$transaction->rollback('El comprobante de depreciación no ha sido definido');
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$definitivo = $this->getPostParam('definitivo', 'onechar');

			$titulo = new ReportText('DEPRECIACIÓN MENSUAL', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('De: '.$fechaProceso->getMonthName().' '.$fechaProceso->getYear(), array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2));

			$report->setDocumentTitle('Depreciación Mensual');
			$report->setColumnHeaders(array(
				'CÓDIGO',
				'NOMBRE',
				'FECHA PROCESO',
				'COMPROBANTE',
				'CUENTA DEBITO',
				'CUENTA CRÉDITO',
				'VALOR'
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->setColumnStyle(0, new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			)));

			$report->setColumnStyle(1, new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			)));

			$report->setColumnStyle(array(6), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(array(6), new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			)));

			$report->setTotalizeColumns(array(6));

			$report->start(true);

			$numero = 0;
			$depreciaciones = array();
			$this->_depreciacion = array('D' => array(), 'C' => array());
			foreach($this->Activos->find("estado<>'I'") as $activo){
				if($activo->getMesesADep()>0){
					if($activo->getValorCompra()>0){

						$fechaDepreciacionUltima = Date::addInterval($activo->getFechaCompra(), $activo->getMesesADep(), Date::INTERVAL_MONTH);
						if(!Date::isLater($primerDiaMes, $activo->getFechaCompra()->toFirstDayOfMonth())){

							$valorMensual = LocaleMath::round(($activo->getValorCompra()+$activo->getValorIva())/$activo->getMesesADep(), 0);

							$grupo = BackCacher::getGrupo($activo->getGrupo());

							$codigoCuentaDebito = $grupo->getCtaDevVentas();
							$cuentaDebito = BackCacher::getCuenta($codigoCuentaDebito);
							if($cuentaDebito==false){
								$transaction->rollback('La cuenta depreciación débito del grupo "'.$grupo->getNombre().'" no existe');
							} else {
								if($cuentaDebito->getEsAuxiliar()!='S'){
									$transaction->rollback('La cuenta depreciación débito "'.$cuentaDebito->getNombre().'" del grupo "'.$grupo->getNombre().'" no es auxiliar');
								}
							}

							$codigoCuentaCredito = $grupo->getCtaDevCompras();
							$cuentaCredito = BackCacher::getCuenta($codigoCuentaCredito);
							if($cuentaCredito==false){
								$transaction->rollback('La cuenta depreciación crédito del grupo "'.$grupo->getNombre().'" no existe');
							} else {
								if($cuentaCredito->getEsAuxiliar()!='S'){
									$transaction->rollback('La cuenta depreciación crédito "'.$cuentaCredito->getNombre().'" del grupo "'.$grupo->getNombre().'" no es auxiliar');
								}
							}

							$centro = BackCacher::getCentro($activo->getCentroCosto());
							if($centro==false){
								$transaction->rollback('El centro de costo del activo "'.$activo->getDescripcion().'" no existe');
							} else {
								$codigoCentro = $centro->getCodigo();
							}

							$conditions = "ano_mes='$periodo' AND activos_id='{$activo->getId()}'";
							$depreciacion = $this->Depreciacion->findFirst($conditions);
							if($depreciacion==false){

								if(!isset($this->_depreciacion['D'][$codigoCuentaDebito][$codigoCentro])){
									$this->_depreciacion['D'][$codigoCuentaDebito][$codigoCentro] = 0;
								}
								if(!isset($this->_depreciacion['C'][$codigoCuentaCredito][$codigoCentro])){
									$this->_depreciacion['C'][$codigoCuentaCredito][$codigoCentro] = 0;
								}
								$this->_depreciacion['D'][$codigoCuentaDebito][$codigoCentro]+=$valorMensual;
								$this->_depreciacion['C'][$codigoCuentaCredito][$codigoCentro]+=$valorMensual;

								$depreciacion = new Depreciacion();
								$depreciacion->setTransaction($transaction);
								$depreciacion->setAnoMes($periodo);
								$depreciacion->setActivosId($activo->getId());
								$depreciacion->setFecha((string)$fechaProceso);
								$depreciacion->setComprob($comprobDeprec);
								$depreciacion->setCentroCosto($codigoCentro);
								$depreciacion->setCtaDevCompras($codigoCuentaCredito);
								$depreciacion->setCtaDevVentas($codigoCuentaDebito);
								$depreciacion->setValor($valorMensual);
								$depreciaciones[] = $depreciacion;

								$numero++;
							} else {
								$oldComprob = BackCacher::getComprob($depreciacion->getComprob());
								$report->addRow(array(
									$activo->getCodigo(),
									$activo->getDescripcion(),
									(string) $depreciacion->getFecha(),
									$oldComprob->getNomComprob().' / '.$depreciacion->getNumero(),
									$cuentaDebito->getCuenta().' / '.$cuentaDebito->getNombre(),
									$cuentaCredito->getCuenta().' / '.$cuentaCredito->getNombre(),
									$depreciacion->getValor()
								));
							}
							unset($conditions);

						}

					} else {
						$transaction->rollback('El valor de compra del activo '.$activo->getCodigo().':'.$activo->getDescripcion().' es cero y por esto no se puede depreciar');
					}
				}
			}
			try {
				if($numero>0){
					$aura = new Aura($comprobDeprec, 0, null, Aura::OP_CREATE);
					foreach($this->_depreciacion as $naturaleza => $cuentas){
						foreach($cuentas as $cuentaContable => $centros){
							foreach($centros as $codigoCentro => $valor){
								$aura->addMovement(array(
									'Cuenta' => $cuentaContable,
									'CentroCosto' => $codigoCentro,
									'Valor' => $valor,
									'DebCre' => $naturaleza
								));
							}
						}
					}
					$aura->save();
				}
			}
			catch(AuraException $e){
				return array(
					'status' => 'FAILED',
					'message' => $e->getMessage()
				);
			}

			if($numero>0){
				foreach($depreciaciones as $depreciacion){
					$depreciacion->setNumero($aura->getConsecutivo());
					if($depreciacion->save()==false){
						foreach($depreciacion->getMessages() as $message){
							return array(
								'status' => 'FAILED',
								'message' => $message->getMessage()
							);
						}
					} else {
						$report->addRow(array(
							$activo->getCodigo(),
							$activo->getDescripcion(),
							(string) $fechaProceso,
							$comprob->getNomComprob().' / '.$depreciacion->getNumero(),
							$cuentaDebito->getCuenta().' / '.$cuentaDebito->getNombre(),
							$cuentaCredito->getCuenta().' / '.$cuentaCredito->getNombre(),
							$depreciacion->getValor()
						));
					}
				}
			}

			if($definitivo=='S'){
				$transaction->commit();
			}
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

		$report->finish();
		$fileName = $report->outputToFile('public/temp/deprec-activos');

		if($numero>0){
			return array(
				'status' => 'OK',
				'message' => 'Se realizó la depreciación correctamente. El comprobante generado es el '.$comprob->getNomComprob().'/'.$aura->getConsecutivo(),
				'file' => 'temp/'.$fileName
			);
		} else {
			return array(
				'status' => 'OK',
				'message' => 'Se realizó la depreciación correctamente',
				'file' => 'temp/'.$fileName
			);
		}
	}

}