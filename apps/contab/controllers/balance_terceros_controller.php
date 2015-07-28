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
 * Balance_TercerosController
 *
 * Balance de Terceros con Dirección
 *
 */
class Balance_TercerosController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		$fechaCierre->addDays(1);

		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', (string)  Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('nivel', 6);
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');

		try
		{
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

			$nivel = $this->getPostParam('nivel', 'int');

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

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Balance de Comprobación');
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

			$report->setColumnFormat(array(2, 3, 4, 5, 6), new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			)));

			$report->setTotalizeColumns(array(2, 5, 6));

			$report->start();

			$fechaPeriodoAnt = new Date($fechaFinal);
			$fechaPeriodoAnt->diffMonths(1);
			$periodoAnterior = $fechaPeriodoAnt->getPeriod();

			if($cuentaInicial==''){
				$cuentas = $this->Cuentas->find("es_auxiliar='S'");
			} else {
				$cuentas = $this->Cuentas->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND es_auxiliar='S'");
			}
			$balance = array();
			$totalDebitos = 0;
			$totalCreditos = 0;
			foreach($cuentas as $cuenta){
				$codigoCuenta = $cuenta->getCuenta();
				$balance[$codigoCuenta] = array(
					'debitos' => 0,
					'creditos' => 0
				);
				$saldosc = $this->Saldosc->findFirst("cuenta='{$codigoCuenta}' AND ano_mes='{$periodoAnterior}'");
				if($saldosc==false){
					$balance[$codigoCuenta]['saldoAnterior'] = 0;
				} else {
					$balance[$codigoCuenta]['saldoAnterior'] = $saldosc->getSaldo();
				}
				$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
				foreach($this->Movi->find(array($conditions, 'columns' => 'fecha,deb_cre,valor')) as $movi){
					if(Date::compareDates($movi->getFecha(), $fechaInicial)>=0){
						if($movi->getDebCre()=='D'||$movi->getDebCre()=='0'){
							$balance[$codigoCuenta]['debitos']+=$movi->getValor();
							$totalDebitos+=$movi->getValor();
						} else {
							$balance[$codigoCuenta]['creditos']+=$movi->getValor();
							$totalCreditos+=$movi->getValor();
						}
					} else {
						if($movi->getDebCre()=='D'||$movi->getDebCre()=='0'){
							$balance[$codigoCuenta]['saldoAnterior']+=$movi->getValor();
						} else {
							$balance[$codigoCuenta]['saldoAnterior']-=$movi->getValor();
						}
					}
					unset($movi);
				}
				if($balance[$codigoCuenta]['saldoAnterior']==0&&$balance[$codigoCuenta]['debitos']==0&&$balance[$codigoCuenta]['creditos']==0){
					unset($balance[$codigoCuenta]);
				}
				unset($codigoCuenta);
				unset($cuenta);
			}
			if(count($balance)){

				$partes = array(
					'tipo' => 1,
					'mayor' => 2,
					'clase' => 4,
					'subclase' => 6,
					'auxiliar' => 9
				);
				foreach($balance as $codigoCuenta => $balanceCuenta){
					foreach($partes as $tipoParte => $valorNivel){
						$parte = substr($codigoCuenta, 0, $valorNivel);
						if($parte!=''){
							if(!isset($balance[$parte])){
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
						unset($valorNivel);
						unset($tipoParte);
						unset($parte);
					}

					unset($codigoCuenta);
					unset($balanceCuenta);
				}

				ksort($balance, SORT_STRING);
				foreach($balance as $codigoCuenta => $balanceCuenta){
					$length = strlen($codigoCuenta);
					if(($nivel==6)||($nivel==5&&$length<10)||($nivel==4&&$length<7)||
						($nivel==3&&$length<5)||($nivel==2&&$length<3)||($nivel==1&&$length==1)){
						$cuenta = $this->Cuentas->findFirst("cuenta='$codigoCuenta'");
						if($cuenta==false){
							$nombreCuenta = 'NO EXISTE CUENTA';
						} else {
							$nombreCuenta = $cuenta->getNombre();
						}
						$report->addRow(array(
							$codigoCuenta,
							$nombreCuenta,
							$balanceCuenta['saldoAnterior'],
							$balanceCuenta['debitos'],
							$balanceCuenta['creditos'],
							$balanceCuenta['debitos']-$balanceCuenta['creditos'],
							$balanceCuenta['saldoAnterior']+$balanceCuenta['debitos']-$balanceCuenta['creditos']
						));
						unset($cuenta);
					}
					unset($balanceCuenta);
				}

				$report->setTotalizeValues(array(
					3 => $totalDebitos,
					4 => $totalCreditos
				));

			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/balance');

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
