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
 * Movimiento_DocumentosController
 *
 * Listado de Movimiento de Documentos
 *
 */
class Movimiento_DocumentosController extends ApplicationController {

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
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));

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

			$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
			$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

			$nitInicial = $this->getPostParam('nitInicial', 'terceros');
			$nitFinal = $this->getPostParam('nitFinal', 'terceros');

			$numeroInicial = $this->getPostParam('numeroInicial', 'int');
			$numeroFinal = $this->getPostParam('numeroFinal', 'int');

			$tipo = $this->getPostParam('tipo', 'onechar');

			$conditions = array();
			if($tipo=='P'){
				$conditions[] = "saldo!=0";
			} else {
				if($tipo=='C'){
					$conditions[] = "saldo=0";
				}
			}
			if($fechaInicial!=''&&$fechaFinal!=''){
				$conditions[] = "f_emision>='$fechaInicial' AND f_emision<='$fechaFinal'";
			}
			if($cuentaInicial!=''&&$cuentaFinal!=''){
				$conditions[] = "cuenta>='$cuentaInicial' AND cuenta<='$cuentaFinal'";
			}
			if($nitInicial!=''&&$nitFinal!=''){
				$conditions[] = "nit>='$nitInicial' AND nit<='$nitFinal'";
			}
			if($numeroInicial!=0&&$numeroFinal!=0){
				$conditions[] = "numero_doc>='$numeroInicial' AND numero_doc<='$numeroFinal'";
			}

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('MOVIMIENTO DE DOCUMENTOS', array(
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
			$report->setDocumentTitle('Movimiento de Documentos');
			$report->setColumnHeaders(array(
				'TERCERO',
				'NOMBRE',
				'NO. DOCUMENTO',
				'F. VENCIMIENTO',
				'VALOR DOC.',
				'SALDO',
				'COMPROBANTE',
				'NÚMERO',
				'F. MOVIMIENTO',
				'VALOR MOV.',
				'NATURALEZA'
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

			$report->setColumnStyle(array(0, 1, 6, 8), $leftColumn);

			$report->setColumnStyle(array(2, 4, 5, 7, 9), $rightColumn);
			$report->setColumnFormat(array(4, 5, 9), $numberFormat);

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

			$report->start(true);

			$cuentaAnterior = '';

			if(count($conditions)>0){
				$carteras = $this->Cartera->find(array(join(' AND ', $conditions), 'order' => 'cuenta,nit,tipo_doc,numero_doc'));
			} else {
				$carteras = $this->Cartera->find(array('order' => 'cuenta,nit,tipo_doc,numero_doc'));
			}
			foreach($carteras as $cartera){

				$codigoCuenta = $cartera->getCuenta();
				if($cuentaAnterior!=$codigoCuenta){
					$cuenta = BackCacher::getCuenta($codigoCuenta);
					$columnaCuenta = new ReportRawColumn(array(
						'value' => $cuenta->getCuenta().' : '.$cuenta->getNombre(),
						'style' => $leftColumnBold,
						'span' => 11
					));
					$report->addRawRow(array($columnaCuenta));
					$cuentaAnterior = $codigoCuenta;
				}

				$tercero = BackCacher::getTercero($cartera->getNit());

				$conditions = "cuenta='$codigoCuenta' AND
				nit='{$cartera->getNit()}' AND
				tipo_doc='{$cartera->getTipoDoc()}' AND
				numero_doc='{$cartera->getNumeroDoc()}' AND
				fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
				$movis = $this->Movi->find($conditions);
				foreach($movis as $movi){
					$report->addRow(array(
						$cartera->getNit(),
						$tercero->getNombre(),
						$cartera->getTipoDoc().'-'.$cartera->getNumeroDoc(),
						$cartera->getFVence(),
						$cartera->getValor(),
						$cartera->getSaldo(),
						$movi->getComprob(),
						$movi->getNumero(),
						$movi->getFecha(),
						$movi->getValor(),
						$movi->getDebCre() == 'D' ? 'DÉBITO' : 'CRÉDITO'
					));
				}
			}

			//414005005001
			//

			/*$columnaTotalDebitos = new ReportRawColumn(array(
				'value' => $totalDebitosCentro,
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$columnaTotalCreditos = new ReportRawColumn(array(
				'value' => $totalCreditosCentro,
				'style' => $rightColumn,
				'format' => $numberFormat
			));
			$columnaTotalSaldo = new ReportRawColumn(array(
				'value' => $totalSaldoCentro,
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
			$fileName = $report->outputToFile('public/temp/movimiento-documentos');

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
