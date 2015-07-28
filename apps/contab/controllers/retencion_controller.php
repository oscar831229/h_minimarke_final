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
 * RetencionController
 *
 * Retención Acumulada
 *
 */
class RetencionController extends ApplicationController
{

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		$empresa1 = $this->Empresa1->findFirst();
		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrec();

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		Tag::displayTo('ano', $fechaCierre->getYear());
		Tag::displayTo('mes', $fechaCierre->getMonth());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');

		$ano = $this->getPostParam('ano', 'int');
		$mes = $this->getPostParam('mes', 'int');

		$fechaInicial = (string) Date::getFirstDayOfMonth($mes, $ano);
		$fechaFinal = (string) Date::getLastDayOfMonth($mes, $ano);

		$fecha = new Date($fechaInicial);

		$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
		$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);

  		$titulo = new ReportText('RETENCIÓN ACUMULADA', array(
			'fontSize' => 16,
   			'fontWeight' => 'bold',
   			'textAlign' => 'center'
  		));

 		$titulo2 = new ReportText('De: '.$fecha->getMonthName().' '.$ano, array(
			'fontSize' => 11,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
 		));

  		$report->setHeader(array($titulo, $titulo2));
  		$report->setDocumentTitle('Retención Acumulada');
  		$report->setColumnHeaders(array(
  			'NO. DOCUMENTO',
  			'NOMBRE',
  			'SALDO ANTERIOR',
  			'BASE',
  			'SALDO MES',
  			'BASE',
  			'NUEVO SALDO',
  			'BASE'
  		));

  		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));
		$report->setColumnStyle(0, new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		)));

		$leftColumn = new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		));

		$leftColumnBold = new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11,
			'fontWeight' => 'bold'
		));

		$report->setColumnStyle(1, $leftColumn);

  		$report->setColumnStyle(array(2, 3, 4, 5, 6), new ReportStyle(array(
  			'textAlign' => 'right',
  			'fontSize' => 11,
  		)));

		$report->setColumnFormat(array(2, 3, 4, 5, 6), new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 2
		)));

		$report->setTotalizeColumns(array(2, 5, 6));

		$report->start(true);

		$totalDebitos = 0;
		$totalCreditos = 0;

		$periodoAnterior = $ano.sprintf('%02s', $mes);
		if($mes>1){
			$periodo = $ano.sprintf('%02s', $mes-1);
		} else {
			$periodo = ($ano-1).'12';
		}

		if($cuentaInicial && $cuentaFinal){
			list($cuentaInicial, $cuentaFinal) = Utils::sortRange($cuentaInicial, $cuentaFinal);
			$conditions = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
			$cuentas = $this->Cuentas->find(array($conditions, "columns" => "cuenta", "order" => "cuenta"));
		} else {
			$cuentas = $this->Cuentas->find(array("columns" => "cuenta", "order" => "cuenta"));
		}

		$retenciones = array();

		foreach($cuentas as $cuenta){

			$totalSaldoAnterior = 0;
			$totalBaseAnterior = 0;
			$totalSaldoNuevo = 0;
			$totalBaseNueva = 0;
			$codigoCuenta = $cuenta->getCuenta();
			$conditions = "cuenta='$codigoCuenta' AND (ano_mes=0 OR ano_mes='$periodo' OR ano_mes='$periodoAnterior')";
			$saldosns = $this->Saldosn->find(array($conditions, "order" => "ano_mes,nit"));
			if(count($saldosns)){
				foreach($saldosns as $saldosn){
					$nitTercero = $saldosn->getNit();
					if(!isset($retenciones[$codigoCuenta][$nitTercero])){
						$retenciones[$codigoCuenta][$nitTercero] = array(
							'saldoAnterior' => 0,
							'baseAnterior' => 0,
							'saldoNueva' => 0,
							'baseNueva' => 0
						);
					}
					if($saldosn->getAnoMes()==$periodo){
						$retenciones[$codigoCuenta][$nitTercero]['saldoAnterior'] = $saldosn->getSaldo();
						$retenciones[$codigoCuenta][$nitTercero]['baseAnterior'] = $saldosn->getBaseGrab();
						$totalSaldoAnterior+=$saldosn->getSaldo();
						$totalBaseAnterior+=$saldosn->getBaseGrab();
					} else {
						if($saldosn->getAnoMes()==0){
							$conditions = "cuenta='$codigoCuenta' AND nit='$nitTercero' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
							$movis = $this->Movi->find(array($conditions, "columns" => "valor,deb_cre,base_grab"));
							foreach($movis as $movi){
								if($movi->getDebCre()=='D'||$movi->getDebCre()=='0'){
									$retenciones[$codigoCuenta][$nitTercero]['saldoNueva']+=$movi->getValor();
									$totalSaldoNuevo+=$movi->getValor();
								} else {
									$retenciones[$codigoCuenta][$nitTercero]['saldoNueva']-=$movi->getValor();
									$totalSaldoNuevo-=$movi->getValor();
								}
								$retenciones[$codigoCuenta][$nitTercero]['baseNueva']+=$movi->getBaseGrab();
								$totalBaseNueva+=$movi->getBaseGrab();
								unset($movi);
							}
						}
					}
					unset($saldosn);
				}
			}
		}

		foreach($retenciones as $codigoCuenta => $retencionesCuenta){
			$cuenta = BackCacher::getCuenta($codigoCuenta);
			$columnaCuenta = new ReportRawColumn(array(
				'value' => $cuenta->getCuenta().' : '.$cuenta->getNombre(),
				'style' => $leftColumnBold,
				'span' => 8
			));
			$report->addRawRow(array($columnaCuenta));
			foreach($retencionesCuenta as $nitTercero => $retencionesTercero){
				$nit = BackCacher::getTercero($nitTercero);
				if($nit==false){
					$nombreTercero = 'NO EXISTE EL TERCERO';
				} else {
					$nombreTercero = $nit->getNombre();
				}
				$report->addRow(array(
					$nitTercero,
					$nombreTercero,
					$retencionesTercero['saldoAnterior'],
					$retencionesTercero['baseAnterior'],
					$retencionesTercero['saldoNueva'],
					$retencionesTercero['baseNueva'],
					-$retencionesTercero['saldoAnterior']-$retencionesTercero['saldoNueva'],
					-$retencionesTercero['baseAnterior']-$retencionesTercero['baseNueva']
				));
				unset($nombreTercero);
			}
		}

		$report->finish();
		$fileName = $report->outputToFile('public/temp/retencion-acumulada');

		return array(
			'status' => 'OK',
			'file' => 'temp/'.$fileName
		);

	}

}