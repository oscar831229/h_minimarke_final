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
			'entities' => array('Inve', 'Saldos', 'Lineas'),
			'fields' => $fields,
			'conditions' => "{#Inve}.linea BETWEEN '$codigoLineaInicial' AND '$codigoLineaFinal' AND {#Saldos}.almacen='$codigoAlmacen'".
				" AND ano_mes='0' AND {#Inve}.item = {#Saldos}.item AND {#Lineas}.linea = {#Inve}.linea AND {#Lineas}.almacen = 1",
			'order' => array('{#Inve}.linea', '{#Inve}.item'),
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
	 *
	 * Guarda el nuevo saldo o conteo fisico
	 */
	public function guardarAction()
	{
		$this->setResponse('json');

		$codigoAlmacen = $this->getPostParam('almacen', 'alpha');
		$almacen = BackCacher::getAlmacen($codigoAlmacen);
		if ($almacen == false) {
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el almacén '.$codigoAlmacen
			);
		}

		$item = $this->getPostParam('item', 'item');
		$cantidades = $this->getPostParam('cantidad', 'double');

		$addDetail = array();
		try {
			$numeroAjustes = 0;
			$numberItems = count($item);
			for ($i = 0; $i < $numberItems; $i++) {

				$inve = BackCacher::getInve($item[$i]);
				if($inve==false){
					return array(
						'status' => 'FAILED',
						'message' => 'No existe la referencia "'.$item[$i].'" en la línea '.($i+1)
					);
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
						return array(
							'status' => 'FAILED',
							'message' => "No se pudo valorizar la referencia {$item[$i]}/{$inve->getDescripcion()} en el almacén $codigoAlmacen/".$almacen->getNomAlmacen()
						);
					}
				}

				$addDetail[] = array(
					'Item' => $item[$i],
					'Cantidad' => abs($cantidad),
					'Valor' => abs($costo*$cantidad),
					'Tipo' => $tipo
				);
				$numeroAjustes++;
			}

			if ($numeroAjustes == 0) {
				return array(
					'status' => 'FAILED',
					'message' => 'No se indicaron ajustes en el inventario a realizar'
				);
			}

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
					'status' => 'FAILED',
					'message' => 'No existe el almacén '.$codigoAlmacen
				);
			} else {
				$centro = BackCacher::getCentro($almacen->getCentroCosto());
				if($centro==false){
					return array(
						'status' => 'FAILED',
						'message' => 'No existe el centro de costo asociado al almacén '.$codigoAlmacen
					);
				}
			}

			$comprob = sprintf('A%02s', $codigoAlmacen);
			$tatico = new Tatico($comprob, 0, $fechaMovimientoS);
			$movement = array(
				'Comprobante' => $comprob,
				'Fecha' => $fechaMovimientoS,
				'Almacen' => $codigoAlmacen,
				'CentroCosto' => $almacen->getCentroCosto(),
				'Observaciones' => 'INVENTARIO FÍSICO DE '.Date::getCurrentDate(),
				'Estado' => 'C',
			);
			$movement['Detail'] = $addDetail;
			$tatico->addMovement($movement);

		}
		catch(TaticoException $te){
			return array(
				'status' => 'FAILED',
				'message' => $te->getMessage()
			);
		}
		$numeros = $tatico->getLastConsecutivos();
		return array(
			'status' => 'OK',
			'numero' => $numeros['inve'],
			'numeroComprobContab' => $numeros['contab'],
			'message' => 'Se realizó el ajuste al conteo físico con número:' .$numeros['inve']." y comprobante contable: ".$numeros['contab']
		);
	}

}
