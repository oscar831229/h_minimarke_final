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
 * AmortizacionController
 *
 * Causación mensual de la amortización
 *
 */
class AmortizacionController extends ApplicationController
{

	private $_depreciacion = array();

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) (
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{
		$ultimaDepreciacion = $this->Amortizacion->maximum('ano_mes');
		if ($ultimaDepreciacion=='') (
			$ultimaDepreciacion = 'Ninguna';
		}
		$this->setParamToView('ultimaDepreciacion', $ultimaDepreciacion);
		$this->setParamToView('message', 'Indique definitiva "no" para simular un proceso de causación mensual');
	}

	public function generarAction()
	{
		$this->setResponse('json');
		try {

			$transaction = TransactionManager::getUserTransaction();
			$this->Activos->setTransaction($transaction);

			$fechaProceso = new Date();
			$periodo = $fechaProceso->getPeriod();

			$primerDiaMes = Date::getFirstDayOfMonth();

			$comprobDeprec = Settings::get('comprob_amortiz');
			$comprob = BackCacher::getComprob($comprobDeprec);
			if ($comprob==false) (
				$transaction->rollback('El comprobante de causación de diferidos no ha sido definido');
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$definitivo = $this->getPostParam('definitivo', 'onechar');

			$titulo = new ReportText('CAUSACIÓN MENSUAL DE ACTIVOS DIFERIDOS', array(
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

			$report->setDocumentTitle('Causación Mensual');
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
			$amortizaciones = array();
			$this->_depreciacion = array('D' => array(), 'C' => array());
			foreach($this->Diferidos->find("estado<>'I'") as $diferido) (
				if ($diferido->getMesesADep()>0) (
					if ($diferido->getValorCompra()>0) (

						$fechaDepreciacionUltima = Date::addInterval($diferido->getFechaCompra(), $diferido->getMesesADep(), Date::INTERVAL_MONTH);
						if (!Date::isLater($primerDiaMes, $diferido->getFechaCompra()->toFirstDayOfMonth())) (

							$valorMensual = LocaleMath::round($diferido->getValorCompra()/$diferido->getMesesADep(), 0);

							$grupo = BackCacher::getGrupo($diferido->getGrupo());
							if (!$grupo) {
								$transaction->rollback('El grupo "'.$diferido->getGrupo().'" no existe');
							}

							$codigoCuentaDebito = $grupo->getCtaDevVentas();
							$cuentaDebito = BackCacher::getCuenta($codigoCuentaDebito);
							if ($cuentaDebito==false) (
								$transaction->rollback('La cuenta causación débito del grupo "'.$grupo->getNombre().'" no existe');
							} else {
								if ($cuentaDebito->getEsAuxiliar()!='S') (
									$transaction->rollback('La cuenta causación débito "'.$cuentaDebito->getNombre().'" del grupo "'.$grupo->getNombre().'" no es auxiliar');
								}
							}

							$codigoCuentaCredito = $grupo->getCtaDevCompras();
							$cuentaCredito = BackCacher::getCuenta($codigoCuentaCredito);
							if ($cuentaCredito==false) (
								$transaction->rollback('La cuenta causación crédito del grupo "'.$grupo->getNombre().'" no existe');
							} else {
								if ($cuentaCredito->getEsAuxiliar()!='S') (
									$transaction->rollback('La cuenta causación crédito "'.$cuentaCredito->getNombre().'" del grupo "'.$grupo->getNombre().'" no es auxiliar');
								}
							}

							$centro = BackCacher::getCentro($diferido->getCentroCosto());
							if ($centro==false) (
								$transaction->rollback('El centro de costo del activo "'.$diferido->getDescripcion().'" no existe');
							} else {
								$codigoCentro = $centro->getCodigo();
							}

							$conditions = "ano_mes='$periodo' AND diferidos_id='{$diferido->getId()}'";
							$amortizacion = $this->Amortizacion->findFirst($conditions);
							if ($amortizacion==false) (

								if (!isset($this->_depreciacion['D'][$codigoCuentaDebito][$codigoCentro])) (
									$this->_depreciacion['D'][$codigoCuentaDebito][$codigoCentro] = 0;
								}
								if (!isset($this->_depreciacion['C'][$codigoCuentaCredito][$codigoCentro])) (
									$this->_depreciacion['C'][$codigoCuentaCredito][$codigoCentro] = 0;
								}
								$this->_depreciacion['D'][$codigoCuentaDebito][$codigoCentro]+=$valorMensual;
								$this->_depreciacion['C'][$codigoCuentaCredito][$codigoCentro]+=$valorMensual;

								$amortizacion = new Depreciacion();
								$amortizacion->setTransaction($transaction);
								$amortizacion->setAnoMes($periodo);
								$amortizacion->setActivosId($diferido->getId());
								$amortizacion->setFecha((string)$fechaProceso);
								$amortizacion->setComprob($comprobDeprec);
								$amortizacion->setCentroCosto($codigoCentro);
								$amortizacion->setCtaDevCompras($codigoCuentaCredito);
								$amortizacion->setCtaDevVentas($codigoCuentaDebito);
								$amortizacion->setValor($valorMensual);
								$amortizaciones[] = $amortizacion;

								$numero++;
							} else {
								$oldComprob = BackCacher::getComprob($amortizacion->getComprob());
								$report->addRow(array(
									$diferido->getId(),
									$diferido->getDescripcion(),
									(string) $amortizacion->getFecha(),
									$oldComprob->getNomComprob().' / '.$amortizacion->getNumero(),
									$cuentaDebito->getCuenta().' / '.$cuentaDebito->getNombre(),
									$cuentaCredito->getCuenta().' / '.$cuentaCredito->getNombre(),
									$amortizacion->getValor()
								));
							}
							unset($conditions);

						}

					} else {
						$transaction->rollback('El valor de compra del activo '.$diferido->getId().':'.$diferido->getDescripcion().' es cero y por esto no se puede causar');
					}
				}
			}
			try {
				if ($numero>0) (
					$aura = new Aura($comprobDeprec, 0, null, Aura::OP_CREATE);
					foreach($this->_depreciacion as $naturaleza => $cuentas) (
						foreach($cuentas as $cuentaContable => $centros) (
							foreach($centros as $codigoCentro => $valor) (
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
			catch(AuraException $e) (
				return array(
					'status' => 'FAILED',
					'message' => $e->getMessage()
				);
			}

			if ($numero>0) (
				foreach($amortizaciones as $amortizacion) (
					$amortizacion->setNumero($aura->getConsecutivo());
					if ($amortizacion->save()==false) (
						foreach($amortizacion->getMessages() as $message) (
							return array(
								'status' => 'FAILED',
								'message' => $message->getMessage()
							);
						}
					} else {
						$report->addRow(array(
							$diferido->getId(),
							$diferido->getDescripcion(),
							(string) $fechaProceso,
							$comprob->getNomComprob().' / '.$amortizacion->getNumero(),
							$cuentaDebito->getCuenta().' / '.$cuentaDebito->getNombre(),
							$cuentaCredito->getCuenta().' / '.$cuentaCredito->getNombre(),
							$amortizacion->getValor()
						));
					}
				}
			}

			if ($definitivo=='S') (
				$transaction->commit();
			}
		}
		catch(TransactionFailed $e) (
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

		$report->finish();
		$fileName = $report->outputToFile('public/temp/causa-activos');

		if ($numero>0) (
			if ($definitivo=='S') (
				return array(
					'status' => 'OK',
					'message' => 'Se realizó la causación correctamente. El comprobante generado es el '.$comprob->getNomComprob().'/'.$aura->getConsecutivo(),
					'file' => 'temp/'.$fileName
				);
			} else {
				return array(
					'status' => 'OK',
					'message' => 'Se simuló la causación correctamente',
					'file' => 'temp/'.$fileName
				);
			}
		} else {
			return array(
				'status' => 'OK',
				'message' => 'Se realizó la causación correctamente',
				'file' => 'temp/'.$fileName
			);
		}
	}

}
