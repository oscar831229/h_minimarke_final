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
 * Listado_ProveedoresController
 *
 * Listado de los Proveedores
 *
 */
class Listado_ProveedoresController extends ApplicationController
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

		Tag::displayTo('fechaInicial', Date::getFirstDayOfYear());
		Tag::displayTo('fechaFinal', Date::getCurrentDate());

		$this->setParamToView('almacenes', $this->Almacenes->find('estado="A"'));
		$this->setParamToView('lineas', $this->Lineas->count('group: linea,nombre'));
		$this->setParamToView('fechaCierre', $fechaCierre);

		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction()
	{

		$this->setResponse('json');

		$nitInicial = $this->getPostParam('nitInicial', 'alpha');
		$nitFinal = $this->getPostParam('nitFinal', 'alpha');

		$itemInicial = $this->getPostParam('itemInicial', 'alpha');
		$itemFinal = $this->getPostParam('itemFinal', 'alpha');

		$fechaInicial = $this->getPostParam('fechaInicial', 'date');
		$fechaFinal = $this->getPostParam('fechaFinal', 'date');

		$tipo = $this->getPostParam('tipo', 'alpha');
		if (!$tipo) {
			$tipo = "O";
		}

		if($fechaInicial==''||$fechaFinal==''){
			return array(
				'status' => 'FAILED',
				'message' => 'Indique las fechas inicial y final del listado'
			);
		} else {
			list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);
		}

		if($fechaInicial > $fechaFinal){
			return array(
				'status' => 'FAILED',
				'message' => 'La fecha final debe ser posterior a la fecha inicial'
			);
		}

		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);

		$titulo = new ReportText('LISTADO DE PROVEEDORES', array(
			'fontSize' => 16,
   			'fontWeight' => 'bold',
   			'textAlign' => 'center'
  		));

		$titulo2 = "";
		if($nitFinal!="" && $nitInicial!=""){
			$titulo2 = 'Proveedores desde: '.$nitInicial.' al '.$nitFinal.' y ';
		}
		$titulo2.=' Fechas desde '.$fechaInicial.' hasta '.$fechaFinal;

		$titulo2 = new ReportText($titulo2, array(
			'fontSize' => 11,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));

  		$report->setHeader(array($titulo, $titulo2));
  		$report->setDocumentTitle('Listado de Proveedores');
  		$report->setColumnHeaders(array(
			'NIT',
  			'PROVEEDOR',
  			'ORDEN',
  			'FECHA',
  			'CANTIDAD',
  			'VALOR UNITARIO',
  			'VALOR TOTAL',
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

		$leftColumn = new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		));

		$rightColumn = new ReportStyle(array(
  			'textAlign' => 'right',
  			'fontSize' => 11,
  		));

		$leftColumnBold = new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11,
			'fontWeight' => 'bold'
		));

		$rightColumnBold = new ReportStyle(array(
			'textAlign' => 'right',
			'fontSize' => 11,
			'fontWeight' => 'bold'
		));

  		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));

		$report->setColumnStyle(array(0, 1, 2), new ReportStyle(array(
			'textAlign' => 'left',
			'fontSize' => 11
		)));

  		$report->setColumnStyle(array(3, 4, 5, 6), new ReportStyle(array(
  			'textAlign' => 'right',
  			'fontSize' => 11,
  		)));

		$report->setColumnFormat(array(4, 5, 6), $numberFormat);

		$report->start(true);

		$conditions = array("estado='A'");
		if($itemInicial!="" && $itemFinal!=""){
			$conditions[] = "item >= '$itemInicial' AND item <= '$itemFinal'";
		}
		foreach($this->Inve->find(join(" AND ", $conditions)) as $inve){
			$encabezado = false;
			$conditions = "comprob LIKE '" . $tipo . "%' AND item='{$inve->getItem()}' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
			$movilins = $this->Movilin->find($conditions);
			foreach($movilins as $movilin){
				if($movilin->getCantidad()>0){
					$movihead = $movilin->getMovihead();
					if($movihead==false){
						continue;
					}
					if(!$movihead->getNit()){
						continue;
					}
					$proveedor = BackCacher::getTercero($movihead->getNit());
					if($proveedor==false){
						continue;
					}
					if($encabezado==false){
						$columnaReferencia = new ReportRawColumn(array(
							'value' => 'REFERENCIA: '.$inve->getItem().' - '.$inve->getDescripcion(),
							'style' => $leftColumnBold,
							'span'  => 7
						));
						$report->addRawRow(array($columnaReferencia));
						$encabezado = true;
					}
					if($movilin->getCantidad()>0){
						$valorUnitario = $movilin->getValor()/$movilin->getCantidad();
					} else {
						$valorUnitario = 0;
					}
					$report->addRow(array(
						$movihead->getNit(),
						$proveedor->getNombre(),
						$movihead->getComprob().'-'.$movihead->getNumero(),
						$movihead->getFecha(),
						$movilin->getCantidad(),
						$valorUnitario,
						$movilin->getValor()
					));
				}
			}
		}

		/*$datos = array();
		$moviheads = $this->Movihead->find(join(' AND ', $conditions));
		foreach($moviheads as $movihead){

			$movilins = $movihead->getMovilin();
			foreach($movilins as $movilin){

				if(!$movihead->getNit()){
					continue;
				}

				$proveedor = BackCacher::getTercero($movihead->getNit());
				if($proveedor==false){
					continue;
				}

				$inve = $movilin->getInve();
				if($inve==false){
					$key = $movilin->getItem().' - REFERENCIA NO EXISTE';
				} else {
					$key = $movilin->getItem().' - '.$inve->getDescripcion();
				}

				if($movilin->getCantidad()>0){
					$valorUnitario = $movilin->getValor()/$movilin->getCantidad();
				} else {
					$valorUnitario = 0;
				}

				$ultimaCompra = $this->Movilin->maximum("fecha", "conditions: comprob='O01' AND item='".$movilin->getItem()."'");

				if(!isset($datos[$key]) || !is_array($datos[$key])){
					$datos[$key] = array();
				}

				//armamos un array con los datos del reporte
				$datos[$key][$movihead->getNit()] = array(
					$movihead->getNit(),
					$proveedor->getNombre(),
					$ultimaCompra,
					$movilin->getCantidad(),
					$valorUnitario,
					$movilin->getValor()
				);
			}

		}

		//Ordenamos por key
		ksort($datos);

		//Recorremos array ya ordenado para desplegar datos
		foreach($datos as $item => $dato){

			$columnaReferencia = new ReportRawColumn(array(
				'value' => 'REFERENCIA: '.$item,
				'style' => $leftColumnBold,
				'span'  => 6
			));
			$report->addRawRow(array($columnaReferencia));

			//Añadimos datos de producto al reporte
			foreach($dato as $n => $d){
				$report->addRow($d);
			}

		}*/

		$report->finish();
		$fileName = $report->outputToFile('public/temp/proveedores');

		return array(
			'status' => 'OK',
			'file' => 'temp/' . $fileName
		);
	}

}
