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
 * FisicoController
 *
 * Controlador del movimiento
 *
 */
class FisicoController extends ApplicationController
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
		$this->setParamToView('message', 'Ingrese un criterio de búsqueda para consultar referencias');

		$this->setParamToView('lineas', $this->Lineas->count('group: linea,nombre'));
		$this->setParamToView('almacenes', $this->Almacenes->find('estado="A"','columns: codigo,nom_almacen'));

		Tag::displayTo('lineaFinal', $this->Lineas->maximum('linea'));
	}

	/**
	 *
	 * Consulta los items segun seleccion de formulario de busqueda
	 */
	public function consultarAction()
	{

		$codigoAlmacen = $this->getPostParam('almacen', 'alpha');
		$almacen = $this->Almacenes->findFirst("codigo='$codigoAlmacen' AND estado='A'");
		if ($almacen == false) {
			Flash::error('El almacén no existe ó está inactivo');
			return $this->routeToAction('index');
		}

		$codigoLineaInicial = $this->getPostParam('lineaInicial', 'alpha');
		$codigoLineaFinal = $this->getPostParam('lineaFinal', 'alpha');

		$lineaInicial = $this->Lineas->findFirst("linea='$codigoLineaInicial' AND almacen='{$almacen->getCodigo()}'");
		$lineaFinal = $this->Lineas->findFirst("linea='$codigoLineaFinal' AND almacen='{$almacen->getCodigo()}'");

		if($lineaInicial==false){
			Flash::error('La línea inicial no existe en almacén '.$almacen->getCodigo().'/'.$almacen->getNomAlmacen());
			return $this->routeToAction('index');
		}

		if($lineaFinal==false){
			Flash::error('La línea final no existe en almacén '.$almacen->getCodigo().'/'.$almacen->getNomAlmacen());
			return $this->routeToAction('index');
		}

		$fields = array(
			'{#Lineas}.linea', '{#Lineas}.nombre', '{#Inve}.item', '{#Inve}.descripcion', '{#Inve}.saldo_actual',
			'{#Saldos}.saldo', '{#Saldos}.costo', '{#Saldos}.f_u_mov'
		);

		$saldosReferencias = new ActiveRecordJoin(array(
			'entities' 	  => array('Inve', 'Saldos', 'Lineas'),
			'fields' 	  => $fields,
			'conditions'  => "{#Inve}.linea BETWEEN '$codigoLineaInicial' AND '$codigoLineaFinal' AND {#Saldos}.almacen='$codigoAlmacen'".
				" AND ano_mes='0' AND {#Inve}.item = {#Saldos}.item AND {#Lineas}.linea = {#Inve}.linea AND {#Lineas}.almacen = 1",
			'order' 	  => array('{#Inve}.linea', '{#Inve}.item'),
			'noRelations' => true
		));
		$saldos = $saldosReferencias->getResultSet();

		if (count($saldos) == 0) {
			Flash::notice('No se encontraron referencias');
			return $this->routeToAction('index');
		}

		$this->setParamToView('almacen', $almacen);
		$this->setParamToView('saldos', $saldos);

		$this->setParamToView('message', 'Ingrese la cantidad física para cada referencia y haga click en "Guardar"');
	}

	/**
	 * Genera array de contenido enviado
	 *
	 * @param  int   $codigoAlmacen
	 * @param  array $item
	 * @param  array $cantidades
	 * @return array
	 */
	private function getDetails($codigoAlmacen, $item, $cantidades)
	{
		$almacen = BackCacher::getAlmacen($codigoAlmacen);
		if ($almacen == false) {
			throw new TaticoException('No existe el almacén ' . $codigoAlmacen);
		}

		$addDetail = array();

		$numeroAjustes = 0;
		$numberItems = count($item);
		//throw new TaticoException("item: ".count($item).", cantidades: ".count($cantidades)."", 1);

		for ($i = 0; $i < $numberItems; $i++) {

			if (!isset($item[$i])) {
				throw new TaticoException("Offset $i no existe en variable item");
			}

			if (!isset($cantidades[$i])) {
				throw new TaticoException("Offset $i no existe en variable cantidades");
			}

			$inve = BackCacher::getInve($item[$i]);
			if($inve==false){
				throw new TaticoException('No existe la referencia "'.$item[$i].'" en la línea '.($i+1));
			}

			$saldo = $this->Saldos->findFirst("item='{$item[$i]}' AND almacen='$codigoAlmacen' AND ano_mes='0'");
			if($saldo==false){
				$saldo = new Saldos();
			}

			$cantidad = $saldo->getSaldo() - $cantidades[$i];
			if ($cantidad == 0.00) {
				continue;
			} else {

				if ($cantidad < 0.00) {
					$tipo = 'SUMAR';
				} else {
					$tipo = 'RESTAR';
				}

				$costo = Tatico::getCosto($item[$i], 'I', $codigoAlmacen);
				if ($costo == 0.00) {
					throw new TaticoException("No se pudo valorizar la referencia {$item[$i]}/{$inve->getDescripcion()} en el almacén $codigoAlmacen/".$almacen->getNomAlmacen());
				}
			}

			$addDetail[$item[$i]] = array(
				'Item' 	   => $item[$i],
				'Offset'   => $i,
				'Nombre'   => $inve->getDescripcion(),
				'Cantidad' => abs($cantidad),
				'Valor'    => abs($costo*$cantidad),
				'Tipo' 	   => $tipo,
				'costo'    => $costo
			);
			$numeroAjustes++;
		}

		if ($numeroAjustes == 0) {
			throw new TaticoException('No se indicaron ajustes en el inventario a realizar');
		}

		return $addDetail;
	}

	/**
	 *
	 * Guarda el nuevo saldo o conteo fisico
	 */
	public function guardarAction()
	{
		$this->setResponse('json');

		$item = $this->getPostParam('i', 'item');
		$cantidades = $this->getPostParam('c', 'double');
		$codigoAlmacen = $this->getPostParam('a', 'alpha');

		$addDetail = array();

		try {

			$count = count($item);
			//$inputs = ini_get('max_input_nesting_level');
			$inputs = 600;

			if ($count > $inputs) {
				throw new TaticoException(
					"El limite de variables adjuntas a enviar al servidor son '$inputs',
					usted esta enviando '$count'. Debe pedir soporte tecnico algo no esta correctamente configurado.",
					1
				);
			}

			//get detail from POST
			$addDetail = $this->getDetails($codigoAlmacen, $item, $cantidades);

			//Cogemos la fecha de cierre actual
			$empresa = $this->Empresa->findFirst();

			$fechaCierre = $empresa->getFCierrei();
			$fechaCierreInicial = clone $fechaCierre;
			$fechaCierreFinal = clone $fechaCierre;
			$fechaCierreInicial->addDays(1);
			$fechaCierreFinal->addMonths(1);

			//Si la fecha esta entre las fechas posibles del cierre
			$fechaMovimiento = new Date();
			if(!$fechaMovimiento->isBetween($fechaCierreInicial, $fechaCierreFinal)){
				$fechaMovimientoS = $fechaCierreFinal->getDate();
			} else {
				$fechaMovimientoS = $fechaMovimiento->getDate();
			}

			$almacen = BackCacher::getAlmacen($codigoAlmacen);
			if($almacen==false){
				return array(
					'status'  => 'FAILED',
					'message' => 'No existe el almacén '.$codigoAlmacen
				);
			} else {
				$centro = BackCacher::getCentro($almacen->getCentroCosto());
				if($centro==false){
					return array(
						'status'  => 'FAILED',
						'message' => 'No existe el centro de costo asociado al almacén '.$codigoAlmacen
					);
				}
			}

			$comprob = sprintf('A%02s', $codigoAlmacen);
			$tatico = new Tatico($comprob, 0, $fechaMovimientoS);
			$movement = array(
				'Comprobante' 	=> $comprob,
				'Fecha' 	  	=> $fechaMovimientoS,
				'Almacen' 	  	=> $codigoAlmacen,
				'CentroCosto' 	=> $almacen->getCentroCosto(),
				'Observaciones' => 'INVENTARIO FÍSICO DE ' . Date::getCurrentDate(),
				'Estado' 	    => 'C',
			);
			$movement['Detail'] = $addDetail;
			$tatico->addMovement($movement);

			$numeros = $tatico->getLastConsecutivos();

			return array(
				'status' => 'OK',
				'numero' => $numeros['inve'],
				'numeroComprobContab' => $numeros['contab'],
				'message' => 'Se realizó el ajuste al conteo físico con número:' . $numeros['inve'] . " y comprobante contable: " . $numeros['contab']
			);
		}
		catch(Exception $te){
			return array(
				'status'  => 'FAILED',
				'message' => $te->getMessage()
			);
		}
	}

	/**
	 * Genera un reporte de diferencias entre el conteo fisico actual y el cambiado
	 * @return JSON
	 */
	public function printAction()
	{
		$this->setResponse('json');

		$item = $this->getPostParam('i', 'item');
		$cantidades = $this->getPostParam('c', 'double');
		$codigoAlmacen = $this->getPostParam('a', 'alpha');

		try {
			$addDetail = $this->getDetails($codigoAlmacen, $item, $cantidades);

			$reportType = $this->getPostParam('reportType', 'alpha');
			$report = ReportBase::factory('html');

			$titulo = new ReportText('DIFERENCIAS DE CONTEO FISICO', array(
				'fontSize' => 16,
	   			'fontWeight' => 'bold',
	   			'textAlign' => 'center'
	  		));

			$nombre = $this->Almacenes->findFirst("codigo='{$codigoAlmacen}'")->getNomAlmacen();
	 		$titulo2 = new ReportText($codigoAlmacen.' - '.$nombre, array(
				'fontSize' => 13,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
	 		));

	 		$report->setHeader(array($titulo, $titulo2));
	  		$report->setDocumentTitle('Diferencias de conteo fisico');
	  		$report->setColumnHeaders(array(
	  			'REFERENCIA',
	  			'DESCRIPCIÓN',
	  			'CANTIDAD ACTUAL',
				'CANTIDAD NUEVA',
				'DIFF'
	  		));

			$leftColumnBold = new ReportStyle(array(
	  			'textAlign' => 'left',
	  			'fontSize' => 11,
				'fontWeight' => 'bold'
	  		));

			$centerColumnBold = new ReportStyle(array(
	  			'textAlign' => 'center',
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

			$report->setColumnStyle(array(0, 1), new ReportStyle(array(
				'textAlign' => 'left',
				'fontSize' => 11
			)));

			$report->setColumnStyle(array(4), new ReportStyle(array(
				'textAlign'  => 'center',
				'fontWeight' => 'bold',
				'fontSize'   => 11
			)));

	  		$report->setColumnStyle(range(2, 3), new ReportStyle(array(
	  			'textAlign' => 'right',
	  			'fontSize' => 11,
	  		)));

			$report->setColumnFormat(range(2, 3), $numberFormat);

			$report->start(true);

			$saldos = $this->Saldos->find("almacen='$codigoAlmacen' AND ano_mes='0'");

			foreach($saldos as $saldo) {

				$codigoItem = $saldo->getItem();
				$inve = BackCacher::getInve($codigoItem);

				if (!$inve) {
					continue;
				}

				$diff = '';
				$cantidadActual = $saldo->getSaldo();
				$cantidadNueva = $saldo->getSaldo();
				if (isset($addDetail[$codigoItem])) {
					$diff = '<--';
					$offset = $addDetail[$codigoItem]['Offset'];
					$cantidadNueva = $cantidades[$offset];
				}

				$report->addRow(array(
					$codigoItem,
					$inve->getDescripcion(),
					$cantidadActual,
					$cantidadNueva,
					$diff
				));
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/fisicoReporte');

			return array(
				'status'  => 'OK',
				'file' 	  => 'temp/'.$fileName,
				'message' => 'Se genero el reporte correctamente '
			);

		}
		catch(Exception $te){
			return array(
				'status'  => 'FAILED',
				'message' => $te->getMessage()
			);
		}
	}
}
