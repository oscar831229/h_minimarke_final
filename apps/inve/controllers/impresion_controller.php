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
 * ImpresionController
 *
 * Retención de Impresion continua de movimientos
 *
 */
class ImpresionController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierrei();

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('tipoMovimiento', array(
			'E' => 'ENTRADAS',
			'C' => 'SALIDAS',
			'A' => 'AJUSTES',
			'T' => 'TRASLADOS',
			'R' => 'TRANSFORMACIONES',
			'O' => 'ORDENES DE COMPRA',
			'P' => 'PEDIDOS'
		));
		Tag::displayTo('fechaInicial', Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', $fechaCierre->getDate());

		$this->setParamToView('almacenes', $this->Almacenes->find("estado='A'"));
		$this->setParamToView('message', 'Indique los parámetros y haga click en "Generar"');
	}

	public function generarAction(){

		$this->setResponse('json');

		try {
			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);
		

			$reportType = $this->getPostParam('reportType', 'alpha');
			$tipoMovimiento = $this->getPostParam('tipoMovimiento', 'onechar');
			$codigoAlmacen = $this->getPostParam('almacen', 'int');
			$comprob = sprintf($tipoMovimiento.'%02s', $codigoAlmacen);
			$numeroInicial = $this->getPostParam('numeroInicial', 'int');
			$numeroFinal = $this->getPostParam('numeroFinal', 'int');

			$conditions = array("comprob='$comprob' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'");
			if($numeroInicial>0&&$numeroFinal>0){
				list($numeroInicial, $numeroFinal) = Utils::sortRange($numeroInicial, $numeroFinal);
				$conditions[] = "numero>='$numeroInicial' AND numero<='$numeroFinal'";
			}

			$isFirst = true;
			$isLast = false;
			$contents = '';
			$number = 1;
			$moviheads = $this->Movihead->find(array(join(' AND ', $conditions), 'order' => 'comprob,numero,fecha'));
			$totalMoviheads = count($moviheads);
			foreach($moviheads as $movihead){
				if($totalMoviheads==$number){
					$isLast = true;
				}
				$contents.=Tatico::getPrint($reportType, $comprob, $codigoAlmacen, $movihead->getNumero(), $isFirst, $isLast);
				$isFirst = false;
			}

			$url = Tatico::getPrintOutput($reportType, 'multiple-'.mt_rand(0, 10000), $contents);

			return array(
				'status' => 'OK',
				'file' => $url
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	/**
	 * Solicita el formato de impresión del reporte
	 *
	 */
	public function getFormatoAction(){
		$this->setResponse('view');
		$controller = $this->getPostParam('controller', 'alpha');
		$codigoComprobante = $this->getPostParam('comprob', 'comprob');
		$codigoAlmacen = $this->getPostParam('almacen', 'int');
		$numero = $this->getPostParam('numero', 'int');
		$this->setParamToView('controller', $controller);
		$this->setParamToView('codigoComprobante', $codigoComprobante);
		$this->setParamToView('codigoAlmacen', $codigoAlmacen);
		$this->setParamToView('numero', $numero);
	}

}
