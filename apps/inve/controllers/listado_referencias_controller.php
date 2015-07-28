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
 * Listado_ReferenciasController
 *
 * Listado de las Referencias
 *
 */
class Listado_ReferenciasController extends ApplicationController
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
		$fechaCierre = $empresa->getFCierrei();
		Tag::displayTo('lineaFinal', $this->Lineas->maximum('linea'));

		$this->setParamToView('almacenes', $this->Almacenes->find('estado="A"'));
		$this->setParamToView('lineas', $this->Lineas->count('group: linea,nombre'));
		$this->setParamToView('fechaCierre', $fechaCierre);

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');

		$lineaInicial = $this->getPostParam('lineaInicial', 'alpha');
		$lineaFinal = $this->getPostParam('lineaFinal', 'alpha');
		if($lineaInicial==''||$lineaFinal==''){
			return array(
				'status' => 'FAILED',
				'message' => 'Indique las lineas inicial y final del listado'
			);
		}
		if($lineaInicial > $lineaFinal){
			return array(
				'status' => 'FAILED',
				'message' => 'La linea final debe ser posterior a la linea inicial'
			);
		}

		$almacen = $this->getPostParam('almacen', 'alpha');
		if($almacen==''){
			return array(
				'status' => 'FAILED',
				'message' => 'Indique el almacén del listado'
			);
		}

		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);

		$titulo = new ReportText('LISTADO DE REFERENCIAS', array(
			'fontSize' => 16,
   			'fontWeight' => 'bold',
   			'textAlign' => 'center'
  		));

		$nombre = $this->Almacenes->findFirst("codigo='{$almacen}'")->getNomAlmacen();
 		$titulo2 = new ReportText($almacen.' - '.$nombre, array(
			'fontSize' => 13,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
 		));

 		$titulo3 = new ReportText('Líneas: '.$lineaInicial.' - '.$lineaFinal, array(
			'fontSize' => 11,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
 		));

  		$report->setHeader(array($titulo, $titulo2, $titulo3));
  		$report->setDocumentTitle('Listado de Referencias');
  		$report->setColumnHeaders(array(
  			'REFERENCIA',
  			'DESCRIPCIÓN',
  			'UNIDAD',
  			'SALDO ACTUAL',
  			'COSTO PROMEDIO',
  			'COND.',
  			'ORG',
  			'EMPAQUE',
  			'FECHA VENCE'
  		));

		$leftColumnBold = new ReportStyle(array(
  			'textAlign' => 'left',
  			'fontSize' => 11,
			'fontWeight' => 'bold'
  		));

		$numberFormat = new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 2
		));

  		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));

		$report->setColumnStyle(array(0, 1, 2), new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		)));

  		$report->setColumnStyle(range(3, 4), new ReportStyle(array(
  			'textAlign' => 'right',
  			'fontSize' => 11,
  		)));

		$report->setColumnFormat(range(3, 4), $numberFormat);

		$report->start(true);

		$lineas = $this->Lineas->find("linea BETWEEN '$lineaInicial' AND '$lineaFinal' AND almacen='$almacen' AND linea IN (SELECT distinct linea FROM inve WHERE estado='A')");

		foreach($lineas as $linea){
			$cabeceraDocumento = new ReportRawColumn(array(
				'value' => $linea->getLinea().' - '.$linea->getNombre(),
				'style' => $leftColumnBold,
				'span' => 9
			));
			$report->addRawRow(array($cabeceraDocumento));
			$referencias = $this->Inve->find("linea='{$linea->getLinea()}'", 'order: descripcion');
			foreach($referencias as $inve){
				$unidad = BackCacher::getUnidad($inve->getUnidad());
				if($unidad == false){
					$unidad = 'NO EXISTE LA UNIDAD';
				} else {
					$unidad = $unidad->getNomUnidad();
				}
				if($inve->getSaldoActual() == 0){
					$costoPromedio = 0;
				} else {
					$costoPromedio = $inve->getCostoActual() / $inve->getSaldoActual();
				}
				$report->addRow(array(
					$inve->getItem(),
					$inve->getDescripcion(),
					$unidad,
					$inve->getSaldoActual(),
					$costoPromedio,
					'',
					'',
					'',
					''
				));
			}
		}

		$report->finish();
		$fileName = $report->outputToFile('public/temp/referencias');

		return array(
			'status' => 'OK',
			'file' => 'temp/'.$fileName
		);
	}

}
