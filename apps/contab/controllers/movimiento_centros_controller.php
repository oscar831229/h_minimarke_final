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
 * Movimiento_CentrosController
 *
 * Listado de Movimiento de Centros
 *
 */
class Movimiento_CentrosController extends ApplicationController
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
		$fechaCierre->addDays(1);

		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));

		$this->setParamToView('centros', $this->Centros->find(array('order' => 'nom_centro')));
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		//ini_set("memory_limit",-1); ASI NO SE CAMBIA EL CONSUMO DE MEMORIA!!!

		$this->setResponse('json');

		try {

			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			if ($fechaInicial == '' || $fechaFinal == '') {
				return array(
					'status' => 'FAILED',
					'message' => 'Indique las fechas inicial y final del movimiento por centro de costo'
				);
			}

			$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

			$centroInicial = $this->getPostParam('centroInicial', 'int');
			$centroFinal = $this->getPostParam('centroFinal', 'int');

			$orden = $this->getPostParam('orden', 'onechar');

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('MOVIMIENTO DE CUENTAS POR CENTRO DE COSTO', array(
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
			$report->setDocumentTitle('Movimiento de Cuentas Por Centro de Costo');
			$report->setColumnHeaders(array(
				'NO. DOCUMENTO',
				'NOMBRE',
				'FECHA',
				'COMPROBANTE',
				'DESCRIPCIÓN',
				'DEBITOS',
				'CRÉDITOS',
				'SALDO'
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
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

			$rightColumn = new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			));

			$rightColumnBold = new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
				'fontWeight' => 'bold'
			));

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$report->setColumnStyle(array(0, 1, 2, 3, 4), $leftColumn);

			$report->setColumnStyle(array(5, 6, 7), $rightColumn);
			$report->setColumnFormat(array(5, 6, 7), $numberFormat);

			$columnaSaldo = new ReportRawColumn(array(
				'value' => 'SALDO ANTERIOR',
				'style' => $rightColumn
			));

			$columnaTotalCuenta = new ReportRawColumn(array(
				'value' => 'TOTAL CUENTA',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$columnaTotalCentro = new ReportRawColumn(array(
				'value' => 'TOTAL CENTRO DE COSTO',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$report->setRowsPerPage(35);
			$report->start(true);

			$empresa = $this->Empresa->findFirst();
			$fecInicial = new Date($fechaInicial);
			$fechaIn = Date::getFirstDayOfMonth($fecInicial->getMonth(), $fecInicial->getYear());
			if(Date::isLater($fechaIn, $empresa->getFCierrec())){
				$fechaIn = $empresa->getFCierrec();
			}
			$fechaIn = new Date($fechaIn);
			$periodoAnterior = $fechaIn->getPeriod();
			$fechaIn = (string) $fechaIn;

			$fechaAnterior2 = Date::diffInterval($fechaIn, 1, Date::INTERVAL_MONTH);
			$periodoAnterior2 = $fechaAnterior2->getPeriod();


			$cuentaAnterior = '';
			$centroAnterior = '';

			$conditions = array();
			$conditions[] = "(ano_mes=0 OR ano_mes='$periodoAnterior') AND (debe>0 OR haber>0 OR saldo<>0)";
			if($cuentaInicial!=''&&$cuentaFinal!=''){
				$conditions[] = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
			}
			if($centroInicial>0 && $centroFinal>0){
				$conditions[] = "centro_costo>='$centroInicial' AND centro_costo<='$centroFinal'";
				if($centroInicial==$centroFinal){
					$centroAnterior = $centroInicial;
				}
			}

			/*$totalDebitosCuenta = 0;
			$totalCreditosCuenta = 0;
			$totalSaldoCuenta = 0;

			$totalDebitosCentro = 0;
			$totalCreditosCentro = 0;
			$totalSaldoCentro = 0;*/

			$totalDebitosCuenta = array();
			$totalCreditosCuenta = array();
			$totalSaldoCuenta = array();

			$totalDebitosCentro = array();
			$totalCreditosCentro = array();
			$totalSaldoCentro = array();

			$conditions = join(' AND ', $conditions);
			if($orden=='C'){
				$saldosps = $this->Saldosp->find(array($conditions, 'column' => 'cuenta,centro_costo', 'group' => 'cuenta,centro_costo', 'order' => 'cuenta,centro_costo'));
			} else {
				$saldosps = $this->Saldosp->find(array($conditions, 'column' => 'cuenta,centro_costo', 'group' => 'centro_costo,cuenta', 'order' => 'centro_costo,cuenta'));
			}

			$moviSaldosP = array();
			$saldoAnterior = array();

			foreach($saldosps as $saldosp){
				$codigoCuenta = $saldosp->getCuenta();
				$codigoCentro = $saldosp->getCentroCosto();

				if(!isset($moviSaldosP[$codigoCuenta])){
					$moviSaldosP[$codigoCuenta] = array();
				}

				if(!isset($moviSaldosP[$codigoCuenta][$codigoCentro])){
					$moviSaldosP[$codigoCuenta][$codigoCentro] = array();
				}

				//Cuenta
				$cuenta = BackCacher::getCuenta($codigoCuenta);

				/*$columnaCuenta = new ReportRawColumn(array(
					'value' => $cuenta->getCuenta().' : '.$cuenta->getNombre(),
					'style' => $leftColumnBold,
					'span' => 9
				));
				$report->addRawRow(array($columnaCuenta));*/

				$saldoCuenta = 0;
				//echo "<br>saldosp: ","cuenta='$codigoCuenta' AND centro_costo='$codigoCentro' AND ano_mes='$periodoAnterior'";
				//echo "<br>saldosp: ","cuenta='$codigoCuenta' AND centro_costo='$codigoCentro' AND ano_mes='$periodoAnterior2'";
				$saldospAnterior = $this->Saldosp->findFirst("cuenta='$codigoCuenta' AND centro_costo='$codigoCentro' AND ano_mes='$periodoAnterior2'");
				//$saldospAnterior = $this->Saldosp->findFirst("cuenta='$codigoCuenta' AND centro_costo='$codigoCentro' AND ano_mes='$periodoAnterior'");
				if($saldospAnterior!=false){
					$saldoCuenta = $saldospAnterior->getSaldo();
				}
				unset($saldospAnterior);

				//echo "<br>saldo anterios en saldosp: ($codigoCuenta)-($codigoCentro) ",$saldoCuenta;

				$conditions = "cuenta='$codigoCuenta' AND centro_costo='$codigoCentro' AND fecha>'$fechaIn' AND fecha<'$fechaInicial'";
				//echo "<br>movi: ", $conditions;
				$movis = $this->Movi->find(array($conditions, 'columns' => 'deb_cre,valor'));
				foreach($movis as $movi){
					if($movi->getDebCre()=='D'){
						$saldoCuenta+=$movi->getValor();
					} else {
						$saldoCuenta-=$movi->getValor();
					}
					unset($movi);
				}

				//echo "<br>saldo anterios en saldosp2: ($codigoCuenta)-($codigoCentro) ",$saldoCuenta;

				unset($conditions);
				unset($movis);

				$centro = BackCacher::getCentro($codigoCentro);
				if($centro==false){
					$centroNombre = 'NO EXISTE CENTRO DE COSTO';
				} else {
					$centroNombre = $centro->getNomCentro();
				}

				/*$reprt->addRow(array(
					$codigoCentro,
					$centroNombre,
					'',
					'',
					'',
					'',
					'',
					$saldoCuenta
				));*/


				if(!isset($totalSaldoCuenta[$codigoCuenta])){
					$totalSaldoCuenta[$codigoCuenta]=array();
				}
				if(!isset($totalSaldoCuenta[$codigoCuenta][$codigoCentro])){
					$totalSaldoCuenta[$codigoCuenta][$codigoCentro] = 0;
				}

				if(!isset($totalSaldoCentro[$codigoCuenta])){
					$totalSaldoCentro[$codigoCuenta] = array();
				}
				if(!isset($totalSaldoCentro[$codigoCuenta][$codigoCentro])){
					$totalSaldoCentro[$codigoCuenta][$codigoCentro] = 0;
				}

				$totalSaldoCentro[$codigoCuenta][$codigoCentro]+=$saldoCuenta;
				$totalSaldoCuenta[$codigoCuenta][$codigoCentro]+=$saldoCuenta;

				if(!isset($totalDebitosCuenta[$codigoCuenta])){
					$totalDebitosCuenta[$codigoCuenta] = array();
				}
				if(!isset($totalDebitosCuenta[$codigoCuenta][$codigoCentro])){
					$totalDebitosCuenta[$codigoCuenta][$codigoCentro] = 0;
				}

				if(!isset($totalCreditosCuenta[$codigoCuenta])){
					$totalCreditosCuenta[$codigoCuenta] = array();
				}
				if(!isset($totalCreditosCuenta[$codigoCuenta][$codigoCentro])){
					$totalCreditosCuenta[$codigoCuenta][$codigoCentro] = 0;
				}

				if(!isset($totalDebitosCentro[$codigoCuenta])){
					$totalDebitosCentro[$codigoCuenta] = array();
				}
				if(!isset($totalCreditosCentro[$codigoCuenta])){
					$totalCreditosCentro[$codigoCuenta] = array();
				}

				if(!isset($totalDebitosCentro[$codigoCuenta][$codigoCentro])){
					$totalDebitosCentro[$codigoCuenta][$codigoCentro] = 0;
				}
				if(!isset($totalCreditosCentro[$codigoCuenta][$codigoCentro])){
					$totalCreditosCentro[$codigoCuenta][$codigoCentro] = 0;
				}

				//echo "<br>saldoCuenta: ",$saldoCuenta;print_r($totalSaldoCuenta);
				//echo "<br>saldoCuenta: ",$saldoCuenta,", centro: ";print_r($totalSaldoCentro);

				if(!isset($saldoAnterior[$codigoCuenta])){
					$saldoAnterior[$codigoCuenta] = array();
				}
				$saldoAnterior[$codigoCuenta][$codigoCentro] = $saldoCuenta;

				$conditions = "cuenta='$codigoCuenta' AND centro_costo='$codigoCentro' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
				$movis = $this->Movi->find(array($conditions, 'order' => 'fecha, comprob, numero', 'columns' => 'fecha,comprob,numero,descripcion,valor,deb_cre'));
				foreach ($movis as $movi) {
					$row = array(
						'',
						'',
						$movi->getFecha(),
						$movi->getComprob().'-'.$movi->getNumero(),
						$movi->getDescripcion()
					);
					if ($movi->getDebCre() == 'D') {
						$saldoCuenta += $movi->getValor();
						$totalDebitosCuenta[$codigoCuenta][$codigoCentro] += $movi->getValor();
						$totalDebitosCentro[$codigoCuenta][$codigoCentro] += $movi->getValor();
						$row[] = $movi->getValor();
						$row[] = 0;
					} else {
						$saldoCuenta -= $movi->getValor();
						$totalCreditosCuenta[$codigoCuenta][$codigoCentro] += $movi->getValor();
						$totalCreditosCentro[$codigoCuenta][$codigoCentro] += $movi->getValor();
						$row[] = 0;
						$row[] = $movi->getValor();
					}
					$totalSaldoCuenta[$codigoCuenta][$codigoCentro]=$saldoCuenta;
					$totalSaldoCentro[$codigoCuenta][$codigoCentro]=$saldoCuenta;
					$row[] = $saldoCuenta;


					/*if($codigoCuenta=='414095001015'|| $codigoCuenta=='421055001'){
						print_r($row);
						echo "<br>:totales:".PHP_EOL;
						echo "<br>debitos:".PHP_EOL;
						print_r($totalDebitosCuenta);
						echo "<br>creditos:".PHP_EOL;
						print_r($totalCreditosCuenta);
						echo "<br>saldos:".PHP_EOL;
						print_r($totalSaldoCuenta);
					}*/

					$moviSaldosP[$codigoCuenta][$codigoCentro][] = $row;
					//$report->addRow($row);
					unset($movi);
				}

				//unset($saldoCuenta);
			}

			//echo "<br>",$saldoCuenta;print_r($totalSaldoCuenta);
			//echo "<br>saldoCuenta: ",$saldoCuenta,", centro: ";print_r($totalSaldoCentro);


			//Cuentas
			$cuentaAnterior = 0;
			foreach ($moviSaldosP as $codigoCuenta => $centrosObj) {

				$cuenta = BackCacher::getCuenta($codigoCuenta);

				$columnaCuenta = new ReportRawColumn(array(
					'value' => $cuenta->getCuenta().' : '.$cuenta->getNombre(),
					'style' => $leftColumnBold,
					'span' => 9
				));
				$report->addRawRow(array($columnaCuenta));

				//Centros ->Row Movi
				$centroAnterior = 0;

				foreach($centrosObj as $codigoCentro => $rowObj){

					//Centro nuevo
					//echo "<br>","codigoCuenta($codigoCuenta) -> if(codigoCentro($codigoCentro) != centroAnterior($centroAnterior)){";
					if($codigoCentro != $centroAnterior){
						$centro = BackCacher::getCentro($codigoCentro);
						if($centro==false){
							$centroNombre = 'NO EXISTE CENTRO DE COSTO';
						} else {
							$centroNombre = $centro->getNomCentro();
						}

						$report->addRow(array(
							$codigoCentro,
							$centroNombre,
							'',
							'',
							'SALDO ANTERIOR',
							'',
							'',
							$saldoAnterior[$codigoCuenta][$codigoCentro]
							//$totalSaldoCentro[$codigoCuenta][$codigoCentro]
						));

						//echo "<br>saldo anterio: ",$saldoAnterior;
					}

					//Movi
					foreach ($rowObj as $row) {
						$report->addRow($row);
					}

					//Totales centros
					if($centroAnterior!=$codigoCentro){

						//print_r($totalDebitosCentro[$codigoCuenta]);
						//print_r($totalCreditosCentro[$codigoCuenta]);

						$totalSaldoCentro2 = $totalSaldoCentro[$codigoCuenta][$codigoCentro];

						$columnaTotalDebitos = new ReportRawColumn(array(
							'value' => $totalDebitosCentro[$codigoCuenta][$codigoCentro],
							'style' => $rightColumn,
							'format' => $numberFormat
						));
						$columnaTotalCreditos = new ReportRawColumn(array(
							'value' => $totalCreditosCentro[$codigoCuenta][$codigoCentro],
							'style' => $rightColumn,
							'format' => $numberFormat
						));
						$columnaTotalSaldo = new ReportRawColumn(array(
							'value' => $totalSaldoCentro2,
							'style' => $rightColumn,
							'format' => $numberFormat
						));

						/*if($codigoCuenta=='531515'){
							print_r($row);
							echo "<br>totalDebitosCuenta: ",$totalDebitosCuenta;
						}*/

						$report->addRawRow(array(
							$columnaTotalCentro,
							$columnaTotalDebitos,
							$columnaTotalCreditos,
							$columnaTotalSaldo
						));
						/*$totalDebitosCentro = 0;
						$totalCreditosCentro = 0;
						$totalSaldoCentro = 0;*/

					}

					$centroAnterior = $codigoCentro;

					unset($movis);
					unset($conditions);

					unset($centroNombre);
					unset($saldosp);
				}

				//Totales cuenta
				if($cuentaAnterior!=$codigoCuenta){

					//$totalSaldoCuenta2 = $totalDebitosCuenta[$codigoCuenta] - $totalCreditosCuenta[$codigoCuenta];
					$totalSaldoCuenta2 = 0;
					foreach ($totalSaldoCuenta[$codigoCuenta] as $codigoCentro => $totalCentro) {
						$totalSaldoCuenta2 += $totalCentro;
					}

					$totalDebitosCuenta2 = 0;
					foreach ($totalDebitosCuenta[$codigoCuenta] as $codigoCentro => $totalCentro) {
						$totalDebitosCuenta2 += $totalCentro;
					}

					$totalCreditosCuenta2 = 0;
					foreach ($totalCreditosCuenta[$codigoCuenta] as $codigoCentro => $totalCentro) {
						$totalCreditosCuenta2 += $totalCentro;
					}

					$columnaTotalDebitos = new ReportRawColumn(array(
						'value' => $totalDebitosCuenta2,
						'style' => $rightColumn,
						'format' => $numberFormat
					));
					$columnaTotalCreditos = new ReportRawColumn(array(
						'value' => $totalCreditosCuenta2,
						'style' => $rightColumn,
						'format' => $numberFormat
					));
					$columnaTotalSaldo = new ReportRawColumn(array(
						'value' => $totalSaldoCuenta2,
						'style' => $rightColumn,
						'format' => $numberFormat
					));
					$report->addRawRow(array(
						$columnaTotalCuenta,
						$columnaTotalDebitos,
						$columnaTotalCreditos,
						$columnaTotalSaldo
					));
					/*$totalDebitosCuenta = 0;
					$totalCreditosCuenta = 0;
					$totalSaldoCuenta = 0;*/
				}

				$cuentaAnterior = $codigoCuenta;

			}

			/*$totalSaldoCentro2 = $totalDebitosCentro[$codigoCuenta][$codigoCentro] - $totalCreditosCentro[$codigoCuenta][$codigoCentro];

			$columnaTotalDebitos = new ReportRawColumn(array(
				'value' => $totalDebitosCentro[$codigoCuenta][$codigoCentro],
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$columnaTotalCreditos = new ReportRawColumn(array(
				'value' => $totalCreditosCentro[$codigoCuenta][$codigoCentro],
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$columnaTotalSaldo = new ReportRawColumn(array(
				'value' => $totalSaldoCentro2,
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$report->addRawRow(array(
				$columnaTotalCentro,
				$columnaTotalDebitos,
				$columnaTotalCreditos,
				$columnaTotalSaldo
			));*/

			$report->finish();
			$fileName = $report->outputToFile('public/temp/movimiento-centros');

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		} catch (Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}
