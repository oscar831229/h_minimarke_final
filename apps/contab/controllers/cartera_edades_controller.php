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
 * Cartera_EdadesController
 *
 * Listado de Cartera por Edades
 *
 */
class Cartera_EdadesController extends ApplicationController
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

		Tag::displayTo('fechaLimite', Date::getCurrentDate());
		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');
		try
		{
			$fechaLimite = $this->getPostParam('fechaLimite', 'date');

			$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

			$nitInicial = $this->getPostParam('nitInicial', 'terceros');
			$nitFinal = $this->getPostParam('nitFinal', 'terceros');

			$orden = $this->getPostParam('orden', 'onechar');

			$conditions = array();
			$conditions[] = "f_emision<='$fechaLimite'";
			if($cuentaInicial!=''&&$cuentaFinal!=''){
				$conditions[] = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
			}
			if($nitInicial!=''&&$nitFinal!=''){
				$conditions[] = "nit>='$nitInicial' AND nit<='$nitFinal'";
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('CARTERA POR EDADES', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$titulo2 = new ReportText('Documentos por Vencimiento Hasta: '.$fechaLimite, array(
				'fontSize' => 11,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo, $titulo2));
			$report->setDocumentTitle('Cartera Por Edades');
			$report->setColumnHeaders(array(
				'COMPROB.',
				'NÚMERO',
				'TIPO DOC.',
				'NÚMERO DOC.',
				'F. EMISIÓN',
				'VALOR DOC.',
				'SALDO',
				'1 A 30 DÍAS',
				'31 A 60 DÍAS',
				'61 A 90 DÍAS',
				'91 A 120 DÍAS',
				'> 4 MESES',
				'DESDE',
				'DÍAS',
				'INTERESES'
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

			$report->setColumnStyle(array(0, 2, 4), $leftColumn);
			$report->setColumnStyle(array(1, 3, 5, 6, 7, 8, 9, 10, 11, 13, 14), $rightColumn);
			$report->setColumnFormat(array(5, 6, 7, 8, 9, 10, 11, 14), $numberFormat);

			$columnaTotalCuenta = new ReportRawColumn(array(
				'value' => 'TOTAL CUENTA',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$columnaTotalTercero = new ReportRawColumn(array(
				'value' => 'TOTAL TERCERO',
				'style' => $rightColumnBold,
				'span' => 5
			));

			$report->start(true);

			$cuentaAnterior = '';
			$terceroAnterior = '';
			$totalCuenta = array();
			$totalTercero = array();
			if($orden=='N'){
				$carteras = $this->Cartera->find(array(join(' AND ', $conditions), 'order' => 'nit,cuenta'));
			} else {
				$carteras = $this->Cartera->find(array(join(' AND ', $conditions), 'order' => 'cuenta,nit'));
			}
			foreach($carteras as $cartera){

				if(!$cartera->getFVence()){
					$cartera->setFVence($cartera->setFEmision());
				}
				$codigoCuenta = $cartera->getCuenta();

				$codigoComprob = '';
				$numeroComprob = 0;
				$saldoCartera = 0;
				$conditions = "cuenta='$codigoCuenta' AND
				nit='{$cartera->getNit()}' AND
				tipo_doc='{$cartera->getTipoDoc()}' AND
				numero_doc='{$cartera->getNumeroDoc()}' AND
				fecha<='$fechaLimite'";
				$movis = $this->Movi->find($conditions);
				foreach($movis as $movi){
					if(substr($codigoCuenta, 0, 1)=='1'){
						if($movi->getDebCre()=='D'){
							$codigoComprob = $movi->getComprob();
							$numeroComprob = $movi->getNumero();
							$saldoCartera+=$movi->getValor();
						} else {
							$saldoCartera -= abs($movi->getValor());
						}
					} else {
						if($movi->getDebCre()=='D'){
							$codigoComprob = $movi->getComprob();
							$numeroComprob = $movi->getNumero();
							$saldoCartera+=$movi->getValor();
						} else {
							$saldoCartera -= abs($movi->getValor());
						}
					}
				}
				if($saldoCartera!=0){

					if($cuentaAnterior!=$codigoCuenta){

						if($terceroAnterior!=''){
							$totales = array($columnaTotalTercero);
							for($i=5;$i<12;$i++){
								$totales[$i-4] = new ReportRawColumn(array(
									'value' => $totalTercero[$i],
									'style' => $rightColumn,
									'format' => $numberFormat
								));
							}
							$report->addRawRow($totales);
						}

						if($cuentaAnterior!=''){
							$totales = array($columnaTotalCuenta);
							for($i=5;$i<12;$i++){
								$totales[$i-4] = new ReportRawColumn(array(
									'value' => $totalCuenta[$i],
									'style' => $rightColumn,
									'format' => $numberFormat
								));
							}
							$report->addRawRow($totales);
						}

						$cuenta = BackCacher::getCuenta($codigoCuenta);
						if ($cuenta==false) {
							return array(
								'status' => 'FAILED',
								'message' => "No existe la cuenta '$codigoCuenta' en el plan contable"
							);
						}
						$columnaCuenta = new ReportRawColumn(array(
							'value' => 'CUENTA No. '.$cuenta->getCuenta().' : '.$cuenta->getNombre(),
							'style' => $leftColumnBold,
							'span' => 15
						));
						$report->addRawRow(array($columnaCuenta));
						$cuentaAnterior = $codigoCuenta;
						$terceroAnterior = '';

						for($i=5;$i<12;$i++){
							$totalCuenta[$i] = 0;
							$totalTercero[$i] = 0;
						}

					}

					if($terceroAnterior!=$cartera->getNit()){

						if($terceroAnterior!=''){
							$totales = array($columnaTotalTercero);
							for($i=5;$i<12;$i++){
								$totales[$i-4] = new ReportRawColumn(array(
									'value' => $totalTercero[$i],
									'style' => $rightColumn,
									'format' => $numberFormat
								));
							}
							$report->addRawRow($totales);
						}

						$tercero = BackCacher::getTercero($cartera->getNit());
						if($tercero){
							$columnaCuenta = new ReportRawColumn(array(
								'value' => $tercero->getNit().' : '.$tercero->getNombre(),
								'style' => $leftColumnBold,
								'span' => 15
							));
						} else {
							$columnaCuenta = new ReportRawColumn(array(
								'value' => $cartera->getNit().' : NO EXISTE EL TERCERO',
								'style' => $leftColumnBold,
								'span' => 15
							));
						}
						$report->addRawRow(array($columnaCuenta));
						$terceroAnterior = $cartera->getNit();

						for($i=5;$i<12;$i++){
							$totalTercero[$i] = 0;
						}
					}

					$row = array(
						$codigoComprob, //0
						$numeroComprob, //1
						$cartera->getTipoDoc(), //2
						$cartera->getNumeroDoc(), //3
						$cartera->getFEmision(), //4
						$cartera->getValor(), //5
						$saldoCartera, //6
						0, //7
						0, //8
						0, //9
						0, //10
						0, //11
						$cartera->getFEmision(), //12
					);
					if(Date::isLater($cartera->getFEmision(), $fechaLimite)){
						$jt = (Date::difference($cartera->getFEmision(), $fechaLimite)/30)+1;
						$row[13] = 0;
					} else {
						$jt = (Date::difference($fechaLimite, $cartera->getFEmision())/30)+1;
						$row[13] = Date::difference($fechaLimite, $cartera->getFEmision());
					}
					if($jt<6){
						$row[$jt+6] = $saldoCartera;
					} else {
						$row[11] = $saldoCartera;
					}
					$totalIntereses = 0; //$saldoCartera*$porcen;
					$row[6]+=$totalIntereses;
					$row[14] = $totalIntereses;
					$report->addRow($row);

					//Acumular totales por cuenta y tercero
					for($i=5;$i<12;$i++){
						$totalCuenta[$i]+=$row[$i];
						$totalTercero[$i]+=$row[$i];
					}
				}
			}

			$totales = array($columnaTotalTercero);
			for($i=5;$i<12;$i++){
				if(isset($totalTercero[$i])){
					$totales[$i-4] = new ReportRawColumn(array(
						'value' => $totalTercero[$i],
						'style' => $rightColumn,
						'format' => $numberFormat
					));
				}
			}
			$report->addRawRow($totales);

			$totales = array($columnaTotalCuenta);
			for($i=5;$i<12;$i++){
				if(isset($totalCuenta[$i])){
					$totales[$i-4] = new ReportRawColumn(array(
						'value' => $totalCuenta[$i],
						'style' => $rightColumn,
						'format' => $numberFormat
					));
				}
			}
			$report->addRawRow($totales);

			$report->finish();
			$fileName = $report->outputToFile('public/temp/cartera-edades');

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
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
