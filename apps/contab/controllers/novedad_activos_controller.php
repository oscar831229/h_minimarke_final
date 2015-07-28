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
 * Novedad_ActivosController
 *
 * Novedades de Activos Fijos
 *
 */
class Novedad_ActivosController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		Tag::displayTo('fechaInicial', Date::getCurrentDate());
		Tag::displayTo('fechaFinal', Date::getCurrentDate());
		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');

		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');

		if($fechaInicial==''||$fechaFinal==''){
			return array(
				'status' => 'FAILED',
				'message' => 'Indique las fechas inicial y final del reporte'
			);
		}

		$cuentaInicial = $this->getPostParam('cuentaInicial', 'cuentas');
		$cuentaFinal = $this->getPostParam('cuentaFinal', 'cuentas');

		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);

  		$titulo = new ReportText('NOVEDADES DE ACTIVOS FIJOS', array(
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
  		$report->setDocumentTitle('Novedades de Activos Fijos');
  		$report->setColumnHeaders(array(
  			'CÓDIGO',
  			'ACTIVO',
  			'USUARIO',
  			'FECHA',
  			'NOVEDAD'
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

		$report->start(true);

		$conditions = "fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
		foreach($this->Novact->find($conditions) as $novact){
			$activo = BackCacher::getActivo($novact->getCodigo());
			$usuario = BackCacher::getUsuario($novact->getUsuariosId());
			$report->addRow(array(
				$novact->getId(),
				$activo->getDescripcion(),
				$usuario->getNombres(),
				$novact->getFecha(),
				$novact->getNovedad()
			));
		}

		$report->finish();
		$fileName = $report->outputToFile('public/temp/novedad-activos');

		return array(
			'status' => 'OK',
			'file' => 'temp/'.$fileName
		);

	}

}
