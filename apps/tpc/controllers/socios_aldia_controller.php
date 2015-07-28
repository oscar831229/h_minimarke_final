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
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */


/**
 * Socios_AldiaController
 *
 * Clase controller que visualiza el informe de socios al día
 *
 */
class Socios_AldiaController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	/**
	 * Metodo que carga cosas al ingresar al formulario de inicio
	 *
	 */
	public function indexAction(){
		$this->setParamToView('message', 'Indique los datos para generar el informe');
		Tag::displayTo('month',date('m'));
		Tag::displayTo('year',date('Y'));
	}

	/**
	 * Metodo que genera tabla de amortizacion
	 *
	 * @return json
	 */
	public function generarAction(){
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();
		try {
			$rules = array(
				'fecha_ini' => array(
					'message' => 'Debe indicar la fecha inicial',
					'filter' => 'date'
				),
				'fecha_fin' => array(
					'message' => 'Debe indicar la fecha final',
					'filter' => 'date'
				)
			);
			if($this->validateRequired($rules)==false){
				foreach($this->getValidationMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

			$fechaIni = $this->getPostParam('fecha_ini', 'date');
			$fechaFin = $this->getPostParam('fecha_fin', 'date');
			
			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory($reportType);

			$titulo = new ReportText('INFORME DE SOCIOS AL DÍA', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$report->setHeader(array($titulo));

			$report->setDocumentTitle('Informe de socios al día');
			$report->setColumnHeaders(array(
				'NÚMERO CONTRATO',
				'CÉDULA',
				'NOMBRES',
				'APELLIDOS',
				'FECHA',
				'CUOTA FIJA',
				'CAPITAL',
				'INTERES',
				'SALDO'
			));

			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));

			$report->setColumnStyle(array(0,1,2,3,4), new ReportStyle(array(
				'textAlign' => 'center',
				'fontSize' => 11
			)));

			$report->setColumnStyle(array(5,6,7,8), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11,
			)));

			$report->setColumnFormat(array(5,6,7,8), new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 0
			)));

			$report->setTotalizeColumns(array(5,6,7));

			$report->start(true);

			$empresa = $this->Empresa->findFirst();

			$totalValor = 0;
			$totalCapital = 0;
			$totalInteres = 0;
			
			/**
			 * 'NÚMERO CONTRATO',
				'CÉDULA',
				'NOMBRES',
				'APELLIDOS',
				'FECHA',
				'CUOTA FIJA',
				'CAPITAL',
				'INTERES',
				'SALDO'
			 */
			$amortizacionObj = $this->Amortizacion->find(array('conditions'=>'fecha_cuota >= "'.$fechaIni.'" AND fecha_cuota <= "'.$fechaFin.'"'));
			
			if(count($amortizacionObj)<=0){
				$transaction->rollback('No se encontro registros');
			}
			
			foreach($amortizacionObj as $amortizacion){
				//obtenemos info de socio de amortización
				$socio = $amortizacion->getSocios();
				if($socio==false){
					$transaction->rollback('La amortización tiene un socios invalido ('.$amortizacion->getSociosId().')');
				}
				//Agregamos linea de datos de amortización
				$report->addRow(array(
					$socio->getNumeroContrato(),
					$socio->getIdentificacion(),
					$socio->getNombres(),
					$socio->getApellidos(),
					$amortizacion->getFechaCuota(),
					$amortizacion->getValor(),
					$amortizacion->getCapital(),
					$amortizacion->getInteres(),
					$amortizacion->getSaldo()
				));
				$totalValor		+= $amortizacion->getValor();
				$totalCapital	+= $amortizacion->getCapital();
				$totalInteres	+= $amortizacion->getInteres();
			}
			
			$report->setTotalizeValues(array(
				5 => $totalValor,
				6 => $totalCapital,
				7 => $totalInteres
			));

			$report->finish();
			$fileName = $report->outputToFile('public/temp/sociosAldia');

			return array(
				'status' => 'OK',
				'file' => 'temp/'.$fileName
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}
}
