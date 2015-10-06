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
 * Informe_Balance_ConsolidadoController
 *
 * Informe Balance Consolidado
 */
class Informe_Balance_ConsolidadoController extends ApplicationController
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

		try {
			return $this->balanceConsolidado();
		} catch(Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

	/**
	 * Normal Balance
	 *
	 * @return array
	 */
	private function balanceConsolidado()
	{
		$year = $this->getPostParam('year', 'int');
		if (empty($year)) {
			return array(
				'status'  => 'FAILED',
				'message' => 'Indique el a&ntilde;o del balance'
			);
		}

		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);

		$titulo = new ReportText('BALANCE CONSOLIDADO ANUAL', array(
			'fontSize' => 16,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));

		$report->setHeader(array($titulo), false, true);

		$report->setDocumentTitle('Balance Consolidado Anual');

		$headers = array(
			'PERIODO',
			'CÓDIGO',
			'DEBITOS',
			'CRÉDITOS',
			'NUEVO SALDO'
		);

		$report->setColumnHeaders($headers);

		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));

		$report->setColumnStyle(0, new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		)));

		$report->setColumnStyle(array(2, 3, 4), new ReportStyle(array(
			'textAlign' => 'right',
			'fontSize' => 11,
		)));

		$numberFormat = new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 2
		));
		$report->setColumnFormat(array(2, 3, 4), $numberFormat);

		$leftColumn = new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		));

		$rightColumn = new ReportStyle(array(
			'textAlign' => 'right',
			'fontSize' => 11,
		));

		$report->setTotalizeColumns(array(2, 3, 4));

		$report->start(true);

	  $totalDebitos = 0;
		$totalCreditos = 0;
		$totalSaldo = 0;

		$saldoscs = $this->Saldosc->find(array(
			"condition" => "ano_mes >= ".$year."01 AND ano_mes <= ".$year."12",
			"order" => "ano_mes, cuenta ASC"
		));
		if (!count($saldoscs)) {
			throw new Exception("No se encontro saldos en el a&ntilde;o '$year'");
		}

		foreach ($saldoscs as $saldosc) {
			$debe  = $saldosc->getDebe();
			$haber = $saldosc->getHaber();
			$saldo = $saldosc->getSaldo();

			$report->addRow(array(
				$saldosc->getAnoMes(),
				$saldosc->getCuenta(),
				$debe,
				$haber,
				$saldo
			));

			$totalDebitos += $debe;
			$totalCreditos += $haber;
			$totalSaldo += $saldo;
		}

		$report->setTotalizeValues(array(
			2 => $totalDebitos,
			3 => $totalCreditos,
			4 => $totalSaldo
		));

		$report->finish();
		$fileName = $report->outputToFile('public/temp/informe_balance_consolidado');

		return array(
			'status' => 'OK',
			'file' => 'temp/' . $fileName
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
