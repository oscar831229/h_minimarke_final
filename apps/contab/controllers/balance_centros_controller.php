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
 * Balance_CentrosController
 *
 * Balance de Comprobación por Centro de Costo
 *
 */
class Balance_CentrosController extends ApplicationController
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

		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		//$fechaCierre->addDays(1);

		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('nivel', 6);

		$this->setParamToView('centros', $this->Centros->find(array('order' => 'nom_centro')));
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');

		try {

			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			if($fechaInicial==''||$fechaFinal==''){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique las fechas inicial y final del balance'
				);
			}

			$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

			$centroInicial = $this->getPostParam('centroInicial', 'int');
			$centroFinal = $this->getPostParam('centroFinal', 'int');

			$nivel = $this->getPostParam('nivel', 'int');

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

	  		$titulo = new ReportText('BALANCE DE COMPROBACIÓN POR CENTRO DE COSTO', array(
				'fontSize' => 16,
	   			'fontWeight' => 'bold',
	   			'textAlign' => 'center'
	  		));

	 		$titulo2 = new ReportText('Desde: '.$fechaInicial.' - '.$fechaFinal, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
	 		));

	  		$report->setHeader(array($titulo, $titulo2));
	  		$report->setDocumentTitle('Balance de Comprobación por Centro de Costo');
	  		$report->setColumnHeaders(array(
	  			'CÓDIGO',
	  			'DESCRIPCIÓN',
	  			'SALDO ANTERIOR',
	  			'DEBITOS',
	  			'CRÉDITOS',
	  			'NETO MES',
	  			'NUEVO SALDO'
	  		));

	  		$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$leftColumn = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			));

			$rightColumn = new ReportStyle(array(
	  			'textAlign' => 'right',
	  			'fontSize' => 11,
	  		));

			$leftColumnBold = new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11,
				'fontWeight' => 'bold'
			));

			$rightColumnBold = new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
				'fontWeight' => 'bold'
			));

			$report->setColumnStyle(array(0,1), $leftColumn);

	  		$report->setColumnStyle(array(2, 3, 4, 5, 6), $rightColumn);

	  		$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$totalDebitosT = 0;
			$totalCreditosT = 0;
			$totalSaldoAnteriorT = 0;
			$totalNetoMesT = 0;
			$totalNuevoSaldoT = 0;

			$report->setColumnFormat(array(2, 3, 4, 5, 6), $numberFormat);

			$report->start(true);

			$empresa = $this->Empresa->findFirst();
			$fechaInicial = new Date($fechaInicial);

			$fechaIn = Date::getFirstDayOfMonth($fechaInicial->getMonth(), $fechaInicial->getYear());
			if (Date::isLater($fechaIn, $empresa->getFCierrec())) {
				$fechaIn = $empresa->getFCierrec();
				$fechaPeriodoAnt = new Date($fechaIn);
			} else {
				$fechaPeriodoAnt = new Date($fechaIn);
				$fechaPeriodoAnt->diffMonths(1);
			}
			$periodoAnterior = $fechaPeriodoAnt->getPeriod();

			$fechaIn = (string) $fechaIn;

			$balance = array();
			$totalDebitos = 0;
			$totalCreditos = 0;

			$conditions = array("(ano_mes=0 OR ano_mes='$periodoAnterior')");
			if ($cuentaInicial!=''&&$cuentaFinal!='') {
				$conditions[] = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
			}
			if ($centroInicial>0 && $centroFinal>0) {
				$conditions[] = "centro_costo>='$centroInicial' AND centro_costo<='$centroFinal'";
			}
			$conditions = join(' AND ', $conditions);

			$saldosps = $this->Saldosp->find(array($conditions, 'order' => 'centro_costo,cuenta', 'columns' => 'centro_costo,cuenta', 'group' => 'centro_costo,cuenta'));
			foreach ($saldosps as $saldosp)
			{

				$codigoCentro = trim($saldosp->getCentroCosto());
				$codigoCuenta = trim($saldosp->getCuenta());

				if (!isset($balance[$codigoCentro][$codigoCuenta])) {
					$balance[$codigoCentro][$codigoCuenta] = array(
						'debitos' => 0,
						'creditos' => 0,
						'saldo' => 0
					);
				}

				$saldospAnterior = $this->Saldosp->findFirst("cuenta='$codigoCuenta' AND centro_costo='$codigoCentro' AND ano_mes='$periodoAnterior'");
				if ($saldospAnterior!=false) {
					$balance[$codigoCentro][$codigoCuenta]['saldo'] = $saldospAnterior->getSaldo();
				}
				unset($saldospAnterior);

				$conditions = "cuenta='$codigoCuenta' AND fecha>'$fechaIn' AND fecha<'$fechaInicial' AND centro_costo='$codigoCentro'";

				$movis = $this->Movi->find(array($conditions, 'columns' => 'deb_cre,valor'));
				foreach ($movis as $movi)
				{
					if ($movi->getDebCre()=='D') {
						$balance[$codigoCentro][$codigoCuenta]['saldo']+=$movi->getValor();
					} else {
						$balance[$codigoCentro][$codigoCuenta]['saldo']-=$movi->getValor();
					}
					unset($movi);
				}
				unset($conditions);
				unset($movis);

				$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal' AND centro_costo='$codigoCentro'";
				//throw new Exception($conditions);
				$movis = $this->Movi->find(array($conditions, 'order' => 'fecha,comprob,numero', 'columns' => 'valor,deb_cre'));
				foreach ($movis as $movi)
				{
					if ($movi->getDebCre()=='D') {
						$balance[$codigoCentro][$codigoCuenta]['debitos']+=$movi->getValor();
					} else {
						$balance[$codigoCentro][$codigoCuenta]['creditos']+=$movi->getValor();
					}
					unset($movi);
				}
				unset($conditions);
				unset($movis);

				unset($codigoCentro);
				unset($codigoCuenta);
				unset($saldosp);
			}
			unset($saldosps);

			if (count($balance)) {

				$partes = array(
					'tipo' => 1,
					'mayor' => 2,
					'clase' => 4,
					'subclase' => 6,
					'auxiliar' => 9
				);

				foreach ($balance as $codigoCentro => $balanceCentro)
				{
					foreach ($balanceCentro as $codigoCuenta => $balanceCuenta)
					{
						foreach ($partes as $tipoParte => $valorNivel)
						{
							if ($balanceCuenta['saldo']!=0||$balanceCuenta['debitos']!=0||$balanceCuenta['creditos']) {
								$length = strlen($codigoCuenta);
								if ($length>$valorNivel) {
									$parte = substr($codigoCuenta, 0, $valorNivel);
									if ($parte!='') {
										if (!isset($balance[$codigoCentro][$parte])) {
											$balance[$codigoCentro][$parte] = array(
												'saldo' => $balanceCuenta['saldo'],
												'debitos' => $balanceCuenta['debitos'],
												'creditos' => $balanceCuenta['creditos']
											);
										} else {
											$balance[$codigoCentro][$parte]['saldo']+=$balanceCuenta['saldo'];
											$balance[$codigoCentro][$parte]['debitos']+=$balanceCuenta['debitos'];
											$balance[$codigoCentro][$parte]['creditos']+=$balanceCuenta['creditos'];
										}
									}
									unset($parte);
								}
								unset($valorNivel);
								unset($tipoParte);
							} else {
								unset($balance[$codigoCentro][$codigoCuenta]);
							}
						}
						unset($codigoCuenta);
						unset($balanceCuenta);
					}
					unset($codigoCentro);
					unset($balanceCentro);
				}

				$columnaTotalCentro = new ReportRawColumn(array(
					'value' => 'TOTAL CENTRO DE COSTO',
					'style' => $rightColumnBold,
					'span' => 2
				));

				ksort($balance, SORT_STRING);
				foreach ($balance as $codigoCentro => $balanceCentro)
				{
					if (count($balanceCentro)) {
						$centroCosto = BackCacher::getCentro($codigoCentro);
						if ($centroCosto==false) {
							$centroNombre = 'NO EXISTE CENTRO COSTO';
						} else {
							$centroNombre = $centroCosto->getNomCentro();
						}

						$columnaNombreCentro = new ReportRawColumn(array(
							'value' => $codigoCentro.' : '.$centroNombre,
							'style' => $leftColumnBold,
							'span' => 7
						));
						$report->addRawRow(array($columnaNombreCentro));

						ksort($balanceCentro, SORT_STRING);
						$totalDebitos = 0;
						$totalCreditos = 0;
						$totalSaldoAnterior = 0;
						$totalNetoMes = 0;
						$totalNuevoSaldo = 0;

						foreach ($balanceCentro as $codigoCuenta => $balanceCuenta)
						{
							$length = strlen($codigoCuenta);
							if (($nivel==6)||($nivel==5&&$length<10)||($nivel==4&&$length<7)||($nivel==3&&$length<5)||($nivel==2&&$length<3)||($nivel==1&&$length==1)) {
								$cuenta = BackCacher::getCuenta($codigoCuenta);
								if ($cuenta==false) {
									$nombreCuenta = 'NO EXISTE CUENTA';
								} else {
									if ($cuenta->getEsAuxiliar()=='S') {
										$totalCreditos += $balanceCuenta['creditos'];
										$totalDebitos += $balanceCuenta['debitos'];
										$totalSaldoAnterior += $balanceCuenta['saldo'];
										$totalNetoMes += ($balanceCuenta['debitos']-$balanceCuenta['creditos']);
										$totalNuevoSaldo += ($balanceCuenta['saldo']+$balanceCuenta['debitos']-$balanceCuenta['creditos']);
									}
									$nombreCuenta = $cuenta->getNombre();
								}
								$report->addRow(array(
									$codigoCuenta,
									$nombreCuenta,
									$balanceCuenta['saldo'],
									$balanceCuenta['debitos'],
									$balanceCuenta['creditos'],
									$balanceCuenta['debitos']-$balanceCuenta['creditos'],
									$balanceCuenta['saldo']+$balanceCuenta['debitos']-$balanceCuenta['creditos']
								));
								unset($cuenta);
							}
							unset($balanceCuenta);
						}
						unset($codigoCentro);
						unset($balanceCentro);

						$columnaTotalSaldoAnterior = new ReportRawColumn(array(
							'value' => $totalSaldoAnterior,
							'style' => $rightColumn,
							'format' => $numberFormat
						));
						$columnaTotalDebitos = new ReportRawColumn(array(
							'value' => $totalDebitos,
							'style' => $rightColumn,
							'format' => $numberFormat
						));
						$columnaTotalCreditos = new ReportRawColumn(array(
							'value' => $totalCreditos,
							'style' => $rightColumn,
							'format' => $numberFormat
						));
						$columnaTotalNetoMes = new ReportRawColumn(array(
							'value' => $totalNetoMes,
							'style' => $rightColumn,
							'format' => $numberFormat
						));
						$columnaTotalNuevoSaldo = new ReportRawColumn(array(
							'value' => $totalNuevoSaldo,
							'style' => $rightColumn,
							'format' => $numberFormat
						));

						$report->addRawRow(array(
							$columnaTotalCentro,
							$columnaTotalSaldoAnterior,
							$columnaTotalDebitos,
							$columnaTotalCreditos,
							$columnaTotalNetoMes,
							$columnaTotalNuevoSaldo
						));

						$totalDebitosT += $totalDebitos;
						$totalCreditosT += $totalCreditos;
						$totalSaldoAnteriorT += $totalSaldoAnterior;
						$totalNetoMesT += $totalNetoMes;
						$totalNuevoSaldoT += $totalNuevoSaldo;
					}
				}

				/*$report->setTotalizeValues(array(
					3 => $totalDebitos,
					4 => $totalCreditos
				));*/

			}

			$columnaSpace = new ReportRawColumn(array(
				'value' => '.',
				'style' => $rightColumnBold,
				'span' => 2
			));

			$columnaTotalCentroT = new ReportRawColumn(array(
				'value' => 'TOTAL CENTROS DE COSTOS',
				'style' => $rightColumnBold,
				'span' => 2
			));

			$columnaTotalSaldoAnteriorT = new ReportRawColumn(array(
				'value' => $totalSaldoAnteriorT,
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$columnaTotalDebitosT = new ReportRawColumn(array(
				'value' => $totalDebitosT,
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$columnaTotalCreditosT = new ReportRawColumn(array(
				'value' => $totalCreditosT,
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$columnaTotalNetoMesT = new ReportRawColumn(array(
				'value' => $totalNetoMesT,
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$columnaTotalNuevoSaldoT = new ReportRawColumn(array(
				'value' => $totalNuevoSaldoT,
				'style' => $rightColumn,
				'format' => $numberFormat
			));

			$report->addRawRow(array(
				$columnaSpace
			));

			$report->addRawRow(array(
				$columnaTotalCentroT,
				$columnaTotalSaldoAnteriorT,
				$columnaTotalDebitosT,
				$columnaTotalCreditosT,
				$columnaTotalNetoMesT,
				$columnaTotalNuevoSaldoT
			));

			$report->finish();
			$fileName = $report->outputToFile('public/temp/balance-centros');

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		}
		catch(Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

}