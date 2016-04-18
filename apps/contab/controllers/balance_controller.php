<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package     Back-Office
 * @copyright   BH-TECK Inc. 2009-2014
 * @version     $Id$
 */

/**
 * BalanceController
 *
 * Balance de Comprobación
 *
 */
class BalanceController extends ApplicationController
{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{

		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		//$fechaCierre->addDays(1);
		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('nivel', 6);
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	/**
	 * @return array
	 */
	public function generarAction()
	{

		$this->setResponse('json');

		try
		{

			$tipo = $this->getPostParam('tipo');
			if ($tipo == 'N') {
				return $this->niifBalance();
			} else {
				if ($tipo == 'M') {
					return $this->normalBalance();
				}
			}


		} catch(Exception $e) {
			return array(
				'status'  => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

	/**
	 * Normal Balance
	 *
	 * @return array
	 */
	private function normalBalance()
	{
		try
		{
			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			if ($fechaInicial == '' || $fechaFinal == '') {
				return array(
					'status' => 'FAILED',
					'message' => 'Indique las fechas inicial y final del balance'
				);
			}

			list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);


			$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

			$nivel = $this->getPostParam('nivel', 'int');

			$detallado = $this->getPostParam('detallado', 'onechar');

			$noIncluirCierre = $this->getPostParam('noIncluirCierre', 'onechar');
			$soloRangoCuentas = $this->getPostParam('soloRangoCuentas', 'onechar');
			$datosTercero = $this->getPostParam('datosTercero', 'onechar');

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('BALANCE DE COMPROBACIÓN', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2), false, true);

			$report->setDocumentTitle('Balance de Comprobación');

			$headers = array(
				'CÓDIGO',
				'DESCRIPCIÓN',
				'SALDO ANTERIOR',
				'DEBITOS',
				'CRÉDITOS',
				'NETO MES',
				'NUEVO SALDO'
			);

			//Datos de Tercero
			if ($datosTercero) {
				$headers[]= 'DIRECCION';
				$headers[]= 'CIUDAD';
				$headers[]= 'TELÉFONO';
			}

			$report->setColumnHeaders($headers);

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

			$report->setColumnStyle(array(2, 3, 4, 5, 6), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));
			$report->setColumnFormat(array(2, 3, 4, 5, 6), $numberFormat);

			$leftColumn = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			));

			$rightColumn = new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			));

			$report->setTotalizeColumns(array(2, 5, 6));

			$report->start(true);

			$comprobCierre = Settings::get('comprob_cierre');

			$fechaInicio = $fechaInicial;

			$empresa1 = $this->Empresa1->findFirst();
			$empresa = $this->Empresa->findFirst();
			$fechaCierre = $empresa->getFCierrec();
			if (Date::isLater($fechaInicio, $fechaCierre)) {
				$fechaInicio = Date::addInterval($fechaCierre, 1, Date::INTERVAL_DAY);
			}

			$fechaCierre = $empresa->getFCierrec();
			$fechaCierre->addDays(1);

			$fechaPeriodoAnt = new Date($fechaInicio);
			$fechaPeriodoAnt->diffMonths(1);
			$periodoAnterior = $fechaPeriodoAnt->getPeriod();

			$balance = array();
			$totalSaldoAnterior = 0;
			$totalDebitos = 0;
			$totalCreditos = 0;
			$totalDiferencia = 0;
			$totalNuevoSaldo = 0;
			if ($cuentaInicial=='' && $cuentaFinal=='') {
				$cuentas = $this->Cuentas->find("es_auxiliar='S'");
			} else {
				list($cuentaInicial, $cuentaFinal) = Utils::sortRange($cuentaInicial, $cuentaFinal);
				if ($soloRangoCuentas) {
					$cuentas = $this->Cuentas->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND es_auxiliar='S'");
				} else {
					#sin limite de cuentas
					$cuentas = $this->Cuentas->find("es_auxiliar='S'");
				}
			}

			foreach ($cuentas as $cuenta) {
				$codigoCuenta = trim($cuenta->getCuenta());

				$balance[$codigoCuenta] = array(
					'debitos' => 0,
					'creditos' => 0,
					'saldoAnterior' => 0
				);

				$saldosc = $this->Saldosc->findFirst("cuenta='{$codigoCuenta}' AND ano_mes='{$periodoAnterior}'");
				if ($saldosc==false) {
					$balance[$codigoCuenta]['saldoAnterior'] = 0;
				} else {
					$balance[$codigoCuenta]['saldoAnterior'] = $saldosc->getSaldo();
					$totalSaldoAnterior+=$saldosc->getSaldo();
				}

				//verificamos que siempre sea el primer dia del mes apra el saldo
				$fechaInicioSaldos = new Date($fechaInicio);
				$fechaInicioSaldos->toFirstDayOfMonth();
				$fechaInicioSaldosStr = $fechaInicioSaldos->getDate();

				//if ($noIncluirCierre=='S') {
				$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicio' AND fecha<='$fechaFinal'";
				if ($noIncluirCierre) {
					$conditions .= " AND comprob!='$comprobCierre'";
				}

				$moviObj = EntityManager::get('Movi')->find(array($conditions, 'columns' => 'fecha,valor,deb_cre'));
				foreach ($moviObj as $movi) {

					if (!isset($balance[$codigoCuenta])) {
						$balance[$codigoCuenta] = array();
					}

					if (!Date::isEarlier($movi->getFecha(), $fechaInicial)) {

						if ($movi->getDebCre()=='D') {
							$balance[$codigoCuenta]['debitos'] += $movi->getValor();
							$totalDebitos+=$movi->getValor();
							$totalDiferencia+=$movi->getValor();
						} else {
							$balance[$codigoCuenta]['creditos'] += $movi->getValor();
							$totalCreditos += $movi->getValor();
							$totalDiferencia -= $movi->getValor();
						}
					} else {

						if ($movi->getDebCre()=='D') {
							$balance[$codigoCuenta]['saldoAnterior'] += $movi->getValor();
							$totalSaldoAnterior += $movi->getValor();
						} else {
							$balance[$codigoCuenta]['saldoAnterior'] -= $movi->getValor();
							$totalSaldoAnterior -= $movi->getValor();
						}
					}
					unset($movi);
				}
				unset($moviObj);

				if ($balance[$codigoCuenta]['saldoAnterior']==0 && $balance[$codigoCuenta]['debitos']==0 && $balance[$codigoCuenta]['creditos']==0) {
					unset($balance[$codigoCuenta]);
				}

				//BackCacher::setCuenta($codigoCuenta, $cuenta);

				unset($codigoCuenta);
				unset($cuenta);
				unset($conditions);
				unset($saldosc);
			}

			if (count($balance)) {

				$partes = array(
					'tipo' => 1,
					'mayor' => 2,
					'clase' => 4,
					'subclase' => 6,
					'auxiliar' => 9
				);

				foreach ($balance as $codigoCuenta => $balanceCuenta) {
					foreach ($partes as $tipoParte => $valorNivel) {
						$length = strlen($codigoCuenta);
						if ($length>$valorNivel) {
							$parte = substr($codigoCuenta, 0, $valorNivel);
							if ($parte!='') {
								if (!isset($balance[$parte])) {
									$balance[$parte] = array(
										'saldoAnterior' => $balanceCuenta['saldoAnterior'],
										'debitos' => $balanceCuenta['debitos'],
										'creditos' => $balanceCuenta['creditos']
									);
								} else {
									$balance[$parte]['saldoAnterior']+=$balanceCuenta['saldoAnterior'];
									$balance[$parte]['debitos']+=$balanceCuenta['debitos'];
									$balance[$parte]['creditos']+=$balanceCuenta['creditos'];
								}
							}
							unset($parte);
						}
						unset($valorNivel,$tipoParte,$length);
					}
					/*$totalDebitos+=$balanceCuenta['debitos'];
					$totalCreditos+=$balanceCuenta['creditos'];
					$totalSaldoAnterior+=$balanceCuenta['saldoAnterior'];

					$totalDiferencia += $balanceCuenta['debitos'];
					$totalDiferencia -= $balanceCuenta['creditos'];*/

					unset($codigoCuenta,$balanceCuenta);
				}

				ksort($balance, SORT_STRING);
				foreach ($balance as $codigoCuenta => $balanceCuenta) {
					$codigoCuenta = trim($codigoCuenta);
					$length = strlen($codigoCuenta);
					if (($nivel==6)||($nivel==5&&$length<10)||($nivel==4&&$length<7)||
						($nivel==3&&$length<5)||($nivel==2&&$length<3)||($nivel==1&&$length==1)) {
						$cuenta = BackCacher::getCuenta($codigoCuenta);
						if ($cuenta==false) {
							$nombreCuenta = 'NO EXISTE CUENTA';
						} else {
							$nombreCuenta = $cuenta->getNombre();
						}

						///VALIDACION DE CUENTAS
						$valorRow = array(
							$codigoCuenta,
							$nombreCuenta,
							$balanceCuenta['saldoAnterior'],
							$balanceCuenta['debitos'],
							$balanceCuenta['creditos'],
							$balanceCuenta['debitos']-$balanceCuenta['creditos'],
							$balanceCuenta['saldoAnterior']+$balanceCuenta['debitos']-$balanceCuenta['creditos']
						);

						if ($cuentaInicial=='' && $cuentaFinal=='') {
							$report->addRow($valorRow);
						} else {
							if ($this->_inRangeOfAccounts($codigoCuenta, $cuentaInicial, $cuentaFinal)) {
								$report->addRow($valorRow);
							}
						}

						unset($valorRow);

						if ($detallado == 'S') {
							if ($cuenta != false) {
								if ($cuenta->getPideNit() == 'S') {
									$nitsTerceros = array();

									foreach ($this->Saldosn->distinct(array('nit', 'conditions' => "cuenta='$codigoCuenta'", 'order' => 'nit')) as $numeroNit) {
										$numeroNit = trim($numeroNit);
										$nitsTerceros[$numeroNit] = true;
									}
									$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicio' AND fecha<='$fechaFinal'";
									foreach ($this->Movi->distinct(array('nit', 'conditions' => $conditions, 'order'=>'nit')) as $numeroNit) {
										$numeroNit = trim($numeroNit);
										$nitsTerceros[$numeroNit] = true;
									}
									unset($conditions);

									$nitsTerceros = array_keys($nitsTerceros);
									sort($nitsTerceros, SORT_NUMERIC);

									foreach ($nitsTerceros as $numeroNit) {
										$conditions = '';
										$saldoAnterior = 0;
										//Verificamos si es cuenta de cierre anual y saca saldo ano_mes 0
										$conditions = "cuentaf='$codigoCuenta' AND nit='$numeroNit'";
										$comcier = $this->Comcier->findFirst($conditions);
										$anno = substr($periodoAnterior, 0, 4);
										$mes = substr($periodoAnterior, 4, 2);
										//throw new Exception($mes);

										if ($comcier && $mes==12) {

											/**
											 * Aqui se corrigo problema de arraste de saldos de cierre anual
											 * a cuentas con nit que al cerrar el ano paso a ese nit
											 * debe tomar el saldo de saldosc del anopasado en diciembre siempre no el del mes anterior
											 * este caso se presento por el nit 17 en la cuenta 135517*** no debia aprecer porque en saldosc en
											 * 201302 estaba con saldo 0. Por favor no cambiar
											 */
											$saldoscTemp = $this->Saldosc->findFirst("ano_mes='{$empresa1->getAnoc()}12' AND cuenta='$codigoCuenta'");
											if ($saldoscTemp) {
												$saldoAnterior = $saldoscTemp->getSaldo();
											}

										} else {
											$conditions = "ano_mes = '$periodoAnterior' AND nit='$numeroNit' AND cuenta='$codigoCuenta'";
											$saldon = $this->Saldosn->findFirst($conditions);
											if ($saldon==false) {
												$saldoAnterior = 0;
											} else {
												$saldoAnterior = $saldon->getSaldo();
											}
										}
										unset($conditions);

										$debitos = 0;
										$creditos = 0;
										$conditions = "cuenta='$codigoCuenta' AND nit='$numeroNit' AND fecha>='$fechaInicio' AND fecha<='$fechaFinal'";
										foreach ($this->Movi->find(array($conditions, 'columns' => 'deb_cre,comprob,fecha,valor')) as $movi) {
											if ($movi->getComprob() != $comprobCierre) {
												if (!Date::isEarlier($movi->getFecha(), $fechaInicial)) {
													if ($movi->getDebCre()=='D') {
														$debitos+=$movi->getValor();
													} else {
														$creditos+=$movi->getValor();
													}
												} else {
													if ($movi->getDebCre() == 'D') {
														$saldoAnterior += $movi->getValor();
													} else {
														$saldoAnterior -= $movi->getValor();
													}
												}
											}
											unset($movi);
										}

										if ($saldoAnterior!=0 || $debitos!=0 || $creditos!=0 || ($debitos-$creditos) != 0) {

											$tercero = BackCacher::getTercero($numeroNit);
											if ($tercero==false) {
												$nombreTercero = 'NO EXISTE EL TERCERO';
											} else {
												$nombreTercero = $tercero->getNombre();
											}

											$columnaTerceroNit = new ReportRawColumn(array(
												'value' => $numeroNit,
												'style' => $rightColumn
											));

											$columnaTerceroNombre = new ReportRawColumn(array(
												'value' => $nombreTercero,
												'style' => $leftColumn
											));

											$columnaTerceroSaldoAnterior = new ReportRawColumn(array(
												'value' => $saldoAnterior,
												'style' => $rightColumn,
												'format' => $numberFormat
											));

											$columnaTerceroDebitos = new ReportRawColumn(array(
												'value' => $debitos,
												'style' => $rightColumn,
												'format' => $numberFormat
											));

											$columnaTerceroCreditos = new ReportRawColumn(array(
												'value' => $creditos,
												'style' => $rightColumn,
												'format' => $numberFormat
											));

											$columnaTerceroDebCre = new ReportRawColumn(array(
												'value' => ($debitos-$creditos),
												'style' => $rightColumn,
												'format' => $numberFormat
											));

											$columnaTerceroNuevoSaldo = new ReportRawColumn(array(
												'value' => ($saldoAnterior+$debitos-$creditos),
												'style' => $rightColumn,
												'format' => $numberFormat
											));

											$rowX = array();

											if ($cuentaInicial==''&&$cuentaFinal=='') {
												$rowX = array(
													$columnaTerceroNit,
													$columnaTerceroNombre,
													$columnaTerceroSaldoAnterior,
													$columnaTerceroDebitos,
													$columnaTerceroCreditos,
													$columnaTerceroDebCre,
													$columnaTerceroNuevoSaldo
												);
											} else {
												if ($this->_inRangeOfAccounts($codigoCuenta, $cuentaInicial, $cuentaFinal)) {
													$rowX = array(
														$columnaTerceroNit,
														$columnaTerceroNombre,
														$columnaTerceroSaldoAnterior,
														$columnaTerceroDebitos,
														$columnaTerceroCreditos,
														$columnaTerceroDebCre,
														$columnaTerceroNuevoSaldo
													);
												}
											}

											//Datos de Tercero
											if ($datosTercero) {

												if ($tercero) {
													$direccion = $tercero->getDireccion();
													$ciudad = i18n::strtoupper($tercero->getLocation()->getName());
													$telefono = $tercero->getTelefono();
												} else {
													$direccion = '-';
													$ciudad = '-';
													$telefono = '-';
												}

												$columnaTerceroDireccion = new ReportRawColumn(array(
													'value' => $direccion,
													'style' => $leftColumn
												));
												$columnaTerceroCiudad = new ReportRawColumn(array(
													'value' => $ciudad,
													'style' => $leftColumn
												));
												$columnaTerceroTelefono = new ReportRawColumn(array(
													'value' => $telefono,
													'style' => $leftColumn
												));

												$rowX[] = $columnaTerceroDireccion;
												$rowX[] = $columnaTerceroCiudad;
												$rowX[] = $columnaTerceroTelefono;
											}

											//Add to report
											if (count($rowX)) {
												$report->addRawRow($rowX);
											}

											unset($nombreTercero, $tercero, $rowX);
										}
										unset($saldoAnterior, $debitos, $creditos, $conditions, $saldosn, $saldon);
									}
									unset($nitsTerceros);
								}
							}
						}
						unset($cuenta);
					}
					unset($balanceCuenta);
				}

				$report->setTotalizeValues(array(
					2 => $totalSaldoAnterior,
					3 => $totalDebitos,
					4 => $totalCreditos,
					5 => $totalDiferencia,
					6 => $totalSaldoAnterior + $totalDebitos - $totalCreditos
				));
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/balance');

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		} catch(Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

	/**
	 * Niif Balance
	 *
	 * @return array
	 */
	private function niifBalance()
	{
		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');

		if (empty($fechaInicial) || empty($fechaFinal)) {
			return array(
				'status'  => 'FAILED',
				'message' => 'Indique las fechas inicial y final del balance'
			);
		}

		list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);


		$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
		$cuentaFinal   = $this->getPostParam('cuentaFinal', 'cuentas');

		$nivel = $this->getPostParam('nivel', 'int');

		$detallado = $this->getPostParam('detallado', 'onechar');

		$noIncluirCierre = $this->getPostParam('noIncluirCierre', 'onechar');
		$soloRangoCuentas = $this->getPostParam('soloRangoCuentas', 'onechar');
		$datosTercero = $this->getPostParam('datosTercero', 'onechar');

		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);

		$titulo = new ReportText('BALANCE DE COMPROBACIÓN NIIF', array(
			'fontSize'   => 16,
			'fontWeight' => 'bold',
			'textAlign'  => 'center'
		));

		$titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
			'fontSize'   => 11,
			'fontWeight' => 'bold',
			'textAlign'  => 'center'
		));

		$report->setHeader(array($titulo, $titulo2), false, true);

		$report->setDocumentTitle('Balance de Comprobación');

		$headers = array(
			'CÓDIGO',
			'DESCRIPCIÓN',
			'SALDO ANTERIOR',
			'DEBITOS',
			'CRÉDITOS',
			'NETO MES',
			'NUEVO SALDO'
		);

		//Datos de Tercero
		if ($datosTercero) {
			$headers[]= 'DIRECCION';
			$headers[]= 'CIUDAD';
			$headers[]= 'TELÉFONO';
		}

		$report->setColumnHeaders($headers);

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

		$report->setColumnStyle(array(2, 3, 4, 5, 6), new ReportStyle(array(
			'textAlign' => 'right',
			'fontSize' => 11,
		)));

		$numberFormat = new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 2
		));
		$report->setColumnFormat(array(2, 3, 4, 5, 6), $numberFormat);

		$leftColumn = new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		));

		$rightColumn = new ReportStyle(array(
			'textAlign' => 'right',
			'fontSize' => 11,
		));

		$report->setTotalizeColumns(array(2, 5, 6));

		$report->start(true);

		$comprobCierre = Settings::get('comprob_cierre');

		$fechaInicio = $fechaInicial;

		$empresa1 = $this->Empresa1->findFirst();
		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		if (Date::isLater($fechaInicio, $fechaCierre)) {
			$fechaInicio = Date::addInterval($fechaCierre, 1, Date::INTERVAL_DAY);
		}

		$fechaCierre = $empresa->getFCierrec();
		$fechaCierre->addDays(1);

		$fechaPeriodoAnt = new Date($fechaInicio);
		$fechaPeriodoAnt->diffMonths(1);
		$periodoAnterior = $fechaPeriodoAnt->getPeriod();

		$balance = array();
		$totalSaldoAnterior = 0;
		$totalDebitos = 0;
		$totalCreditos = 0;
		$totalDiferencia = 0;
		$totalNuevoSaldo = 0;



		if ($cuentaInicial=='' && $cuentaFinal=='') {
			$cuentas = $this->Niif->find("es_auxiliar='S'");
		} else {
			list($cuentaInicial, $cuentaFinal) = Utils::sortRange($cuentaInicial, $cuentaFinal);
			if ($soloRangoCuentas) {
				$cuentas = $this->Niif->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND es_auxiliar='S'");
			} else {
				#sin limite de cuentas
				$cuentas = $this->Niif->find("es_auxiliar='S'");
			}
		}

		if (!count($cuentas)) {
			throw new Exception("No se encontraron cuentas Niif", 1);
		}

		foreach ($cuentas as $cuenta) {
			$codigoCuenta = trim($cuenta->getCuenta());

			$balance[$codigoCuenta] = array(
				'debitos' => 0,
				'creditos' => 0,
				'saldoAnterior' => 0
			);

			$saldosNiif = $this->SaldosNiif->findFirst("cuenta='{$codigoCuenta}' AND ano_mes='{$periodoAnterior}'");
			if ($saldosNiif==false) {
				$balance[$codigoCuenta]['saldoAnterior'] = 0;
			} else {
				$balance[$codigoCuenta]['saldoAnterior'] = $saldosNiif->getSaldo();
				$totalSaldoAnterior+=$saldosNiif->getSaldo();
			}

			//verificamos que siempre sea el primer dia del mes apra el saldo
			$fechaInicioSaldos = new Date($fechaInicio);
			$fechaInicioSaldos->toFirstDayOfMonth();
			$fechaInicioSaldosStr = $fechaInicioSaldos->getDate();

			//if ($noIncluirCierre=='S') {
			$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicio' AND fecha<='$fechaFinal'";

			if ($noIncluirCierre) {
				$conditions .= " AND comprob!='$comprobCierre'";
			}

			$moviObj = EntityManager::get('MoviNiif')->find(array($conditions, 'columns' => 'fecha,valor,deb_cre'));
			foreach ($moviObj as $moviNiif) {

				if (!isset($balance[$codigoCuenta])) {
					$balance[$codigoCuenta] = array();
				}

				if (!Date::isEarlier($moviNiif->getFecha(), $fechaInicial)) {

					if ($moviNiif->getDebCre()=='D') {
						$balance[$codigoCuenta]['debitos'] += $moviNiif->getValor();
						$totalDebitos+=$moviNiif->getValor();
						$totalDiferencia+=$moviNiif->getValor();
					} else {
						$balance[$codigoCuenta]['creditos'] += $moviNiif->getValor();
						$totalCreditos += $moviNiif->getValor();
						$totalDiferencia -= $moviNiif->getValor();
					}
				} else {

					if ($moviNiif->getDebCre()=='D') {
						$balance[$codigoCuenta]['saldoAnterior'] += $moviNiif->getValor();
						$totalSaldoAnterior += $moviNiif->getValor();
					} else {
						$balance[$codigoCuenta]['saldoAnterior'] -= $moviNiif->getValor();
						$totalSaldoAnterior -= $moviNiif->getValor();
					}
				}
				unset($moviNiif);
			}
			unset($moviObj);

			if ($balance[$codigoCuenta]['saldoAnterior']==0 && $balance[$codigoCuenta]['debitos']==0 && $balance[$codigoCuenta]['creditos']==0) {
				unset($balance[$codigoCuenta]);
			}

			//BackCacher::setCuenta($codigoCuenta, $cuenta);

			unset($codigoCuenta);
			unset($cuenta);
			unset($conditions);
			unset($saldosc);
		}

		if (count($balance)) {

			$partes = array(
				'tipo' => 1,
				'mayor' => 2,
				'clase' => 4,
				'subclase' => 6,
				'auxiliar' => 9
			);

			foreach ($balance as $codigoCuenta => $balanceCuenta) {
				foreach ($partes as $tipoParte => $valorNivel) {
					$length = strlen($codigoCuenta);
					if ($length>$valorNivel) {
						$parte = substr($codigoCuenta, 0, $valorNivel);
						if ($parte!='') {
							if (!isset($balance[$parte])) {
								$balance[$parte] = array(
									'saldoAnterior' => $balanceCuenta['saldoAnterior'],
									'debitos' => $balanceCuenta['debitos'],
									'creditos' => $balanceCuenta['creditos']
								);
							} else {
								$balance[$parte]['saldoAnterior']+=$balanceCuenta['saldoAnterior'];
								$balance[$parte]['debitos']+=$balanceCuenta['debitos'];
								$balance[$parte]['creditos']+=$balanceCuenta['creditos'];
							}
						}
						unset($parte);
					}
					unset($valorNivel,$tipoParte,$length);
				}
				/*$totalDebitos+=$balanceCuenta['debitos'];
				$totalCreditos+=$balanceCuenta['creditos'];
				$totalSaldoAnterior+=$balanceCuenta['saldoAnterior'];

				$totalDiferencia += $balanceCuenta['debitos'];
				$totalDiferencia -= $balanceCuenta['creditos'];*/

				unset($codigoCuenta,$balanceCuenta);
			}

			ksort($balance, SORT_STRING);
			foreach ($balance as $codigoCuenta => $balanceCuenta) {
				$codigoCuenta = trim($codigoCuenta);
				$length = strlen($codigoCuenta);
				if (($nivel==6)||($nivel==5&&$length<10)||($nivel==4&&$length<7)||
					($nivel==3&&$length<5)||($nivel==2&&$length<3)||($nivel==1&&$length==1)) {
					$cuenta = BackCacher::getCuentaNiif($codigoCuenta);
					if ($cuenta==false) {
						$nombreCuenta = 'NO EXISTE CUENTA NIIF';
					} else {
						$nombreCuenta = $cuenta->getNombre();
					}

					///VALIDACION DE CUENTAS
					$valorRow = array(
						$codigoCuenta,
						$nombreCuenta,
						$balanceCuenta['saldoAnterior'],
						$balanceCuenta['debitos'],
						$balanceCuenta['creditos'],
						$balanceCuenta['debitos']-$balanceCuenta['creditos'],
						$balanceCuenta['saldoAnterior']+$balanceCuenta['debitos']-$balanceCuenta['creditos']
					);

					if ($cuentaInicial=='' && $cuentaFinal=='') {
						$report->addRow($valorRow);
					} else {
						if ($this->_inRangeOfAccounts($codigoCuenta, $cuentaInicial, $cuentaFinal)) {
							$report->addRow($valorRow);
						}
					}

					unset($valorRow);

					if ($detallado == 'S') {
						if ($cuenta != false) {
							if ($cuenta->getPideNit() == 'S') {
								$nitsTerceros = array();

								foreach ($this->SaldosNiif->distinct(array('nit', 'conditions' => "cuenta='$codigoCuenta'", 'order' => 'nit')) as $numeroNit) {
									$numeroNit = trim($numeroNit);
									$nitsTerceros[$numeroNit] = true;
								}
								$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicio' AND fecha<='$fechaFinal'";
								foreach ($this->MoviNiif->distinct(array('nit', 'conditions' => $conditions, 'order'=>'nit')) as $numeroNit) {
									$numeroNit = trim($numeroNit);
									$nitsTerceros[$numeroNit] = true;
								}
								unset($conditions);

								$nitsTerceros = array_keys($nitsTerceros);
								sort($nitsTerceros, SORT_NUMERIC);

								foreach ($nitsTerceros as $numeroNit) {
									$conditions = '';
									$saldoAnterior = 0;
									//Verificamos si es cuenta de cierre anual y saca saldo ano_mes 0
									$conditions = "cuentaf='$codigoCuenta' AND nit='$numeroNit'";
									$comcier = $this->Comcier->findFirst($conditions);
									$anno = substr($periodoAnterior, 0, 4);
									$mes = substr($periodoAnterior, 4, 2);
									//throw new Exception($mes);

									if ($comcier && $mes==12) {

										/**
										 * Aqui se corrigo problema de arraste de saldos de cierre anual
										 * a cuentas con nit que al cerrar el ano paso a ese nit
										 * debe tomar el saldo de saldosc del anopasado en diciembre siempre no el del mes anterior
										 * este caso se presento por el nit 17 en la cuenta 135517*** no debia aprecer porque en saldosc en
										 * 201302 estaba con saldo 0. Por favor no cambiar
										 */
										$saldoscTemp = $this->SaldosNiif->findFirst("ano_mes='{$empresa1->getAnoc()}12' AND cuenta='$codigoCuenta'");
										if ($saldoscTemp) {
											$saldoAnterior = $saldoscTemp->getSaldo();
										}

									} else {
										$conditions = "ano_mes = '$periodoAnterior' AND nit='$numeroNit' AND cuenta='$codigoCuenta'";
										$saldon = $this->SaldosNiif->findFirst($conditions);
										if ($saldon==false) {
											$saldoAnterior = 0;
										} else {
											$saldoAnterior = $saldon->getSaldo();
										}
									}
									unset($conditions);

									$debitos = 0;
									$creditos = 0;
									$conditions = "cuenta='$codigoCuenta' AND nit='$numeroNit' AND fecha>='$fechaInicio' AND fecha<='$fechaFinal'";
									foreach ($this->MoviNiif->find(array($conditions, 'columns' => 'deb_cre,comprob,fecha,valor')) as $movi) {
										if ($movi->getComprob() != $comprobCierre) {
											if (!Date::isEarlier($movi->getFecha(), $fechaInicial)) {
												if ($movi->getDebCre()=='D') {
													$debitos+=$movi->getValor();
												} else {
													$creditos+=$movi->getValor();
												}
											} else {
												if ($movi->getDebCre() == 'D') {
													$saldoAnterior += $movi->getValor();
												} else {
													$saldoAnterior -= $movi->getValor();
												}
											}
										}
										unset($movi);
									}

									if ($saldoAnterior!=0 || $debitos!=0 || $creditos!=0 || ($debitos-$creditos) != 0) {

										$tercero = BackCacher::getTercero($numeroNit);
										if ($tercero==false) {
											$nombreTercero = 'NO EXISTE EL TERCERO';
										} else {
											$nombreTercero = $tercero->getNombre();
										}

										$columnaTerceroNit = new ReportRawColumn(array(
											'value' => $numeroNit,
											'style' => $rightColumn
										));

										$columnaTerceroNombre = new ReportRawColumn(array(
											'value' => $nombreTercero,
											'style' => $leftColumn
										));

										$columnaTerceroSaldoAnterior = new ReportRawColumn(array(
											'value' => $saldoAnterior,
											'style' => $rightColumn,
											'format' => $numberFormat
										));

										$columnaTerceroDebitos = new ReportRawColumn(array(
											'value' => $debitos,
											'style' => $rightColumn,
											'format' => $numberFormat
										));

										$columnaTerceroCreditos = new ReportRawColumn(array(
											'value' => $creditos,
											'style' => $rightColumn,
											'format' => $numberFormat
										));

										$columnaTerceroDebCre = new ReportRawColumn(array(
											'value' => ($debitos-$creditos),
											'style' => $rightColumn,
											'format' => $numberFormat
										));

										$columnaTerceroNuevoSaldo = new ReportRawColumn(array(
											'value' => ($saldoAnterior+$debitos-$creditos),
											'style' => $rightColumn,
											'format' => $numberFormat
										));

										$rowX = array();

										if ($cuentaInicial==''&&$cuentaFinal=='') {
											$rowX = array(
												$columnaTerceroNit,
												$columnaTerceroNombre,
												$columnaTerceroSaldoAnterior,
												$columnaTerceroDebitos,
												$columnaTerceroCreditos,
												$columnaTerceroDebCre,
												$columnaTerceroNuevoSaldo
											);
										} else {
											if ($this->_inRangeOfAccounts($codigoCuenta, $cuentaInicial, $cuentaFinal)) {
												$rowX = array(
													$columnaTerceroNit,
													$columnaTerceroNombre,
													$columnaTerceroSaldoAnterior,
													$columnaTerceroDebitos,
													$columnaTerceroCreditos,
													$columnaTerceroDebCre,
													$columnaTerceroNuevoSaldo
												);
											}
										}

										//Datos de Tercero
										if ($datosTercero) {

											if ($tercero) {
												$direccion = $tercero->getDireccion();
												$ciudad = i18n::strtooupper($tercero->getLocation()->getName());
												$telefono = $tercero->getTelefono();
											} else {
												$direccion = '-';
												$ciudad = '-';
												$telefono = '-';
											}

											$columnaTerceroDireccion = new ReportRawColumn(array(
												'value' => $direccion,
												'style' => $leftColumn
											));
											$columnaTerceroCiudad = new ReportRawColumn(array(
												'value' => $ciudad,
												'style' => $leftColumn
											));
											$columnaTerceroTelefono = new ReportRawColumn(array(
												'value' => $telefono,
												'style' => $leftColumn
											));

											$rowX[] = $columnaTerceroDireccion;
											$rowX[] = $columnaTerceroCiudad;
											$rowX[] = $columnaTerceroTelefono;
										}

										//Add to report
										if (count($rowX)) {
											$report->addRawRow($rowX);
										}

										unset($nombreTercero, $tercero, $rowX);
									}
									unset($saldoAnterior, $debitos, $creditos, $conditions, $saldosn, $saldon);
								}
								unset($nitsTerceros);
							}
						}
					}
					unset($cuenta);
				}
				unset($balanceCuenta);
			}

			$report->setTotalizeValues(array(
				2 => $totalSaldoAnterior,
				3 => $totalDebitos,
				4 => $totalCreditos,
				5 => $totalDiferencia,
				6 => $totalSaldoAnterior + $totalDebitos - $totalCreditos
			));
		}

		$report->finish();
		$fileName = $report->outputToFile('public/temp/balance');

		return array(
			'status' => 'OK',
			'file' => 'temp/'.$fileName
		);
	}


	/**
	 * Valida si una cuenta esta en un rango de cuentas
	 */
	private function _inRangeOfAccounts($codigoCuenta, $cuentaInicial, $cuentaFinal)
	{
		$l1 = strlen($codigoCuenta);
		$l2 = strlen($cuentaInicial);

		if ($l1>$l2) {
			$l = $l2;
		} else {
			$l = $l1;
		}

		$v1 = substr($codigoCuenta, 0, $l);
		$v2 = substr($cuentaInicial, 0, $l);
		$v3 = substr($cuentaFinal, 0, $l);


		$flag = false;

		if ($v1==$v2 && $v1==$v3) {
			$flag = true;
		} else {
			if (strcmp($codigoCuenta, $cuentaInicial) >= 0 && strcmp($codigoCuenta, $cuentaFinal) <= 0) {
				$flag = true;
			}
		}


		return $flag;
	}
}
