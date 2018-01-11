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
 * Libro_AuxiliarController
 *
 * Libro Auxiliar
 *
 */
class Libro_AuxiliarController extends ApplicationController
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
		$fechaCierre->addDays(1);
		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');

		try {

			$tipo = $this->getPostParam('tipo');
			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			if($fechaInicial==''||$fechaFinal==''){
				return array(
					'status' => 'FAILED',
					'message' => 'Indique las fechas inicial y final del libro auxiliar'
				);
			}

			list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);

			$empresa = $this->Empresa->findFirst();
			$fecInicial = new Date($fechaInicial);
			$fechaIn = Date::getFirstDayOfMonth($fecInicial->getMonth(), $fecInicial->getYear());
			if(Date::isLater($fechaIn, $empresa->getFCierrec())){
				$fechaIn = $empresa->getFCierrec();
			} else {
				$fechaIn = new Date($fechaIn);
				$fechaIn->diffDays(1);
			}
			$periodoAnterior = $fechaIn->getPeriod();
			$fechaIn = (string) $fechaIn;

			$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

			$listar = $this->getPostParam('listar', 'onechar');

			list($cuentaInicial, $cuentaFinal) = Utils::sortRange($cuentaInicial, $cuentaFinal);

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('LIBRO AUXILIAR', array(
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
			$report->setDocumentTitle('Libro Auxiliar');
			$report->setColumnHeaders(array(
				'FECHA',
				'COMPROBANTE',
				'DESCRIPCIÓN',
				'CENTRO COSTO',
				'FOLIO',
				'TERCERO',
				'NOMBRE',
				'DEBITOS',
				'CRÉDITOS',
				'SALDO CUENTA',
				'S. CONSOLIDADO'
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

			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));

			$report->setColumnStyle(array(0, 1, 2, 3, 5), $leftColumn);

			$report->setColumnStyle(array(4, 7, 8, 9, 10), $rightColumn);

			$report->setColumnFormat(array(7, 8, 9, 10), $numberFormat);

			$columnaTotales = new ReportRawColumn(array(
				'value' => 'TOTALES CUENTA',
				'style' => $rightColumn,
				'span' => 7
			));

			$columnaSaldo = new ReportRawColumn(array(
				'value' => 'SALDO ANTERIOR',
				'style' => $rightColumn,
				'span' => 10
			));

			$report->start(true);

			if($cuentaInicial==''||$cuentaFinal==''){
				$cuentas = $this->Cuentas->find("es_auxiliar='S'", "order: cuenta");
			} else {
				$cuentas = $this->Cuentas->find("cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal' AND es_auxiliar='S'", "order: cuenta");
			}

			if ($tipo == 'M') {
				$moviModel = $this->Movi;
				$saldoscModel = $this->Saldosc; 
			} else {
				$moviModel = $this->MoviNiif;
				$saldoscModel = $this->SaldoscNiif; 					
			}

			foreach($cuentas as $cuenta){

				$saldoCuenta = 0;
				if ($tipo == 'M') {
					$saldoscModel = $this->Saldosc; 
					$codigoCuenta = $cuenta->getCuenta();
				} else {
					$saldoscModel = $this->SaldoscNiif; 					
					$codigoCuenta = $cuenta->getCuentaNiif();
				}

				$saldosc = $saldoscModel->findFirst("cuenta='$codigoCuenta' AND ano_mes='$periodoAnterior'");
				//echo "<br>saldosc: ","cuenta='$codigoCuenta' AND ano_mes='$periodoAnterior'";
				if($saldosc!=false){
					$saldoCuenta = $saldosc->getSaldo();
				}
				$conditions = "cuenta='$codigoCuenta' AND fecha>'$fechaIn' AND fecha<'$fechaInicial'";
				//echo "<br>movi: ",$conditions;
				$movis = $moviModel->find($conditions);
				foreach($movis as $movi){
					if($movi->getDebCre()=='D'||$movi->getDebCre()=='0'){
						$saldoCuenta+=$movi->getValor();
					} else {
						$saldoCuenta-=$movi->getValor();
					}
					unset($movi);
				}

				$saldoCuentaConsulta = 0;
				if($listar=='D'){
					$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal' AND deb_cre='D'";
				} else {
					if($listar=='C'){
						$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal' AND deb_cre='C'";
					} else {
						$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
					}
				}
				//echo "<br>movi2: ",$conditions;
				$movis = $moviModel->find($conditions, 'order: fecha, comprob, numero, numfol');
				if($saldoCuenta!=0||count($movis)>0){

					$columnaCuenta = new ReportRawColumn(array(
						'value' => $cuenta->getCuenta().' : '.$cuenta->getNombre(),
						'style' => $leftColumn,
						'span' => 11
					));
					$report->addRawRow(array($columnaCuenta));

					$columnaSaldoCuenta = new ReportRawColumn(array(
						'value' => $saldoCuenta,
						'style' => $rightColumn,
						'format' => $numberFormat
					));

					$report->addRawRow(array(
						$columnaSaldo,
						$columnaSaldoCuenta
					));

					$totalDebitos = 0;
					$totalCreditos = 0;
					foreach ($movis as $movi) {
						if ($tipo == 'M') {
							$cuenta = BackCacher::getCuenta($movi->getCuenta());
						} else {
							$cuenta = BackCacher::getCuentaNiif($movi->getCuenta());
						}

						if($cuenta==false){
							$nombreCuenta = 'NO EXISTE CUENTA';
						} else {
							$nombreCuenta = $cuenta->getNombre();
						}
						$nitTercero = '';
						$nombreTercero = '';
						if($cuenta->getPideNit()=='S'){
							$nitTercero = $movi->getNit();
							$nit = BackCacher::getTercero($nitTercero);
							if($nit==false){
								$nombreTercero = 'NO EXISTE EL TERCERO';
							} else {
								$nombreTercero = $nit->getNombre();
							}
						}
						$nombreCentro = '';
						if($cuenta->getPideCentro()=='S'){
							$centro = BackCacher::getCentro($movi->getCentroCosto());
							if($centro==false){
								$nombreCentro = 'NO EXISTE CENTRO';
							} else {
								$nombreCentro = $centro->getNomCentro();
							}
						}
						$row = array(
							$movi->getFecha(),
							$movi->getComprob().'-'.$movi->getNumero(),
							$movi->getDescripcion(),
							$nombreCentro,
							$movi->getNumfol(),
							$nitTercero,
							$nombreTercero
						);
						if($movi->getDebCre()=='D'||$movi->getDebCre()=='0'){
							$saldoCuenta+=$movi->getValor();
							$saldoCuentaConsulta+=$movi->getValor();
							$totalDebitos+=$movi->getValor();
							$row[] = $movi->getValor();
							$row[] = 0;
						} else {
							$saldoCuentaConsulta-=$movi->getValor();
							$saldoCuenta-=$movi->getValor();
							$totalCreditos+=$movi->getValor();
							$row[] = 0;
							$row[] = $movi->getValor();
						}
						$row[] = $saldoCuentaConsulta;
						$row[] = $saldoCuenta;
						$report->addRow($row);
						unset($movi);
						unset($row);
					}
					unset($codigoCuenta);
					unset($cuenta);

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

					$columnaSaldoCuenta = new ReportRawColumn(array(
						'value' => $saldoCuentaConsulta,
						'style' => $rightColumn,
						'format' => $numberFormat
					));

					$columnaSaldoConsolidado = new ReportRawColumn(array(
						'value' => $saldoCuenta,
						'style' => $rightColumn,
						'format' => $numberFormat
					));

					$report->addRawRow(array(
						$columnaTotales,
						$columnaTotalDebitos,
						$columnaTotalCreditos,
						$columnaSaldoCuenta,
						$columnaSaldoConsolidado
					));

					unset($saldoCuenta);
					unset($columnaTotalDebitos);
					unset($columnaTotalCreditos);
				}
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/libro-auxiliar');

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
