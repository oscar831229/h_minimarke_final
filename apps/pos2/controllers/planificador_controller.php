<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

class PlanificadorController extends ApplicationController {

	public function initialize(){
		$this->setTemplateAfter('admin_menu');
	}

	public function indexAction(){
		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth());
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth());
	}

	public function buscarAction(){
		$this->loadModel('Salon', 'TipoServicio');
		Tag::displayTo('fecha', Date::getCurrentDate());
	}

	public function editarAction(){

		$fecha = $this->getPostParam('fecha', 'date');
		$this->setParamToView('fecha', $fecha);
		if(!$fecha){
			return $this->routeToAction('index');
		}

		$salonId = $this->getPostParam('salonId', 'int');
		if($salonId>0){
			$salon = $this->Salon->findFirst($salonId);
			if($salon==false){
				Flash::error('Indique el ambiente donde se hará la planificación');
				return $this->routeToAction('index');
			}
			$this->setParamToView('salon', $salon);
		} else {
			Flash::error('Indique el ambiente donde se hará la planificación');
			return $this->routeToAction('index');
		}

		$tipoServicioId = $this->getPostParam('tipoServicioId', 'int');
		if($tipoServicioId>0){
			$tipoServicio = $this->TipoServicio->findFirst($tipoServicioId);
			if($tipoServicio==false){
				Flash::error('Indique el tipo de servicio de la planificación');
				return $this->routeToAction('index');
			}
			$this->setParamToView('tipoServicio', $tipoServicio);
		} else {
			Flash::error('Indique el tipo de servicio de la planificación');
			return $this->routeToAction('index');
		}

		//print_r($_POST);

		$request = $this->getRequestInstance();
		if($request->isSetPostParam('menuItemId0')){

			try {

				$transaction = TransactionManager::getUserTransaction();
				$this->PlanificadorDetalle->setTransaction($transaction);

				$conditions = "fecha='$fecha' AND salon_id='$salonId' AND tipo_servicio_id='$tipoServicioId'";
				$planificador = $this->Planificador->findFirst($conditions);
				if($planificador==false){
					$planificador = new Planificador();
					$planificador->setTransaction($transaction);
					$planificador->setFecha($fecha);
					$planificador->setTipoServicioId($tipoServicioId);
					$planificador->setSalonId($salonId);
					if($planificador->save()==false){
						foreach($planificador->getMessages() as $message){
							$transaction->rollback('Planificador: '.$message->getMessage());
						}
					}
				} else {
					$this->PlanificadorDetalle->deleteAll("planificador_id='{$planificador->getId()}'");
				}

				for($i=0;$i<32;$i++){

					$menuItemId = $this->getPostParam('menuItemId'.$i, 'int');
					if($menuItemId>0){
						$cantidad = $this->getPostParam('cantidad'.$i, 'int');
						$planificadorDetalle = new PlanificadorDetalle();
						$planificadorDetalle->setTransaction($transaction);
						$planificadorDetalle->setPlanificadorId($planificador->getId());
						$planificadorDetalle->setMenusItemsId($menuItemId);
						$planificadorDetalle->setCantidad($cantidad);
						$planificadorDetalle->setCosto(0);
						$planificadorDetalle->setValor(0);
						if($planificadorDetalle->save()==false){
							foreach($planificadorDetalle->getMessages() as $message){
								$transaction->rollback('PlanificadorDetalle: '.$message->getMessage());
							}
						}
					}
				}

				$transaction->commit();

			}
			catch(TransactionFailed $e){
				Flash::error($e->getMessage());
			}

		}

		$conditions = "fecha='$fecha' AND salon_id='$salonId' AND tipo_servicio_id='$tipoServicioId'";
		$planificador = $this->Planificador->findFirst($conditions);
		if($planificador!=false){
			$i = 0;
			foreach($this->PlanificadorDetalle->find("planificador_id='{$planificador->getId()}'") as $planificadorDetalle){
				Tag::displayTo('menuItemId'.$i, $planificadorDetalle->getMenusItemsId());
				Tag::displayTo('cantidad'.$i, $planificadorDetalle->getCantidad());
				$menuItemCosto = $this->_getCostoMenuItem($planificadorDetalle->getMenusItemsId());
				if($menuItemCosto['status']=='OK'){
					Tag::displayTo('valor'.$i, $menuItemCosto['venta']*$planificadorDetalle->getCantidad());
					Tag::displayTo('costo'.$i, $menuItemCosto['costo']*$planificadorDetalle->getCantidad());
					Tag::displayTo('utilidad'.$i, $menuItemCosto['utilidad']*$planificadorDetalle->getCantidad());
				} else {
					Tag::displayTo('valor'.$i, 0);
					Tag::displayTo('costo'.$i, 0);
					Tag::displayTo('utilidad'.$i, 0);
				}
				$i++;
			}
			$this->setParamToView('numero', $i+1);
		} else {
			$this->setParamToView('numero', 1);
		}

	}

	public function getCostoAction(){

		$this->setResponse('json');
		$menuItemId = $this->getPostParam('menuItemId', 'int');
		if($menuItemId>0){
			return $this->_getCostoMenuItem($menuItemId);
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el item de menú'
			);
		}
	}

	private function _getCostoMenuItem($menuItemId){
		$menuItem = $this->MenusItems->findFirst($menuItemId);
		if($menuItem==false){
			return array(
			'status' => 'FAILED',
			'message' => 'No existe el item de menú'
			);
		} else {
			if($menuItem->tipo_costo=="N"){
				return array(
					'status' => 'FAILED',
					'message' => 'El item de menú no descarga de inventarios'
				);
			} else {
				if($menuItem->codigo_referencia=='@'||$menuItem->codigo_referencia==''){
					if($menuItem->tipo_costo=="I"){
						return array(
							'status' => 'FAILED',
							'message' => 'La referencia asociada al item de menú no es valida'
						);
					} else {
						return array(
							'status' => 'FAILED',
							'message' => 'La receta asociada al item de menú no es valida'
						);
					}
				} else {
					$costo = new CostoInventario();
					$costo->setVerbose(true);
					if ($menuItem->porcentaje_iva > 0) {
						$precioVenta = $menuItem->valor/(1+($menuItem->porcentaje_iva/100));
					} else {
						$precioVenta = $menuItem->valor/(1+($menuItem->porcentaje_impoconsumo/100));
					}
					$valorCosto = $costo->obtenerCosto($menuItem->tipo_costo, $menuItem->nombre, $menuItem->codigo_referencia, $menuItem->descontar, $precioVenta);
					if($precioVenta==0){
						return array(
							'status' => 'OK',
							'costo' => LocaleMath::round($valorCosto, 2),
							'venta' => 0,
							'pcosto' => 0,
							'utilidad' => 0
						);
					} else {
						return array(
							'status' => 'OK',
							'costo' => LocaleMath::round($valorCosto, 2),
							'venta' => LocaleMath::round($precioVenta, 2),
							'pcosto' => LocaleMath::round($valorCosto/$precioVenta, 2),
							'utilidad' => LocaleMath::round($precioVenta-$valorCosto, 2)
						);
					}
				}
			}
		}
	}

	public function consultarAction(){

		$this->setResponse('view');

		try {

			$fechaInicial = $this->getPostParam('fechaInicial', 'date');
			$fechaFinal = $this->getPostParam('fechaFinal', 'date');

			$datos = $this->Datos->findFirst();

			echo '<h1>Planificación de Producción</h1>';
			echo "<h2>", $datos->getNombreHotel(), "</h2>";
			echo '<h2>Fechas: '.$fechaInicial.' - '.$fechaFinal.'</h2>';
			echo '<h2>Fecha Reporte: '.Date::now().'</h2>';

			echo '<br/><table cellspacing="0"><thead><tr><th>&nbsp;</th>';

			$row = 1;
			$maxNumero = 0;
			$tabla = array();
			$range = Date::getRange($fechaInicial, $fechaFinal);
			foreach($range as $fecha){
				$numero = 0;
				$fecha = (string) $fecha;
				$planificaciones = $this->Planificador->find("fecha='$fecha'");
				foreach($planificaciones as $planificador){
					if(!isset($tabla[0][0])){
						$tabla[0][0] = 'TIPO SERVICIO';
						$tabla[0][1] = 'AMBIENTE';
					}
					$tipoServicio = $this->TipoServicio->findFirst($planificador->getTipoServicioId());
					if($tipoServicio==false){
						$tabla[$row][] = 'NO EXISTE TIPO SERVICIO';
					} else {
						$tabla[$row][] = $tipoServicio->getNombre();
					}

					$salon = $this->Salon->findFirst($planificador->getSalonId());
					if($salon==false){
						$tabla[$row][] = 'NO EXISTE TIPO SERVICIO';
					} else {
						$tabla[$row][] = $salon->nombre;
					}

					$n = 2;
					$totalCosto = 0;
					$totalVenta = 0;
					$totalUtilidad = 0;
					foreach($this->PlanificadorDetalle->find("planificador_id='{$planificador->getId()}'") as $planificadorDetalle){
						if(!isset($tabla[0][$n])){
							$tabla[0][$n] = 'ITEM';
							$tabla[0][$n+1] = 'CANTIDAD';
							$tabla[0][$n+2] = 'COSTO';
							$tabla[0][$n+3] = 'VALOR VENTA';
							$tabla[0][$n+4] = 'UTILIDAD';
							$n+=5;
						}
						$menuItem = $this->MenusItems->findFirst($planificadorDetalle->getMenusItemsId());
						if($menuItem==false){
							$tabla[$row][] = 'NO EXISTE '.$planificadorDetalle->getMenusItemsId();
							$tabla[$row][] = $planificadorDetalle->getCantidad();
							$tabla[$row][] = 0;
							$tabla[$row][] = 0;
							$tabla[$row][] = 0;
						} else {
							$tabla[$row][] = $menuItem->nombre;
							$tabla[$row][] = $planificadorDetalle->getCantidad();
							$tabla[$row][] = $menuItem->costo;
							$tabla[$row][] = $menuItem->valor;
							$tabla[$row][] = $menuItem->valor-$menuItem->costo;
							$totalCosto+=$menuItem->costo;
							$totalVenta+=$menuItem->valor;
							$totalUtilidad+=($menuItem->valor-$menuItem->costo);
						}
					}

					if(!isset($tabla[0][$n])){
						$tabla[0][$n] = 'TOTAL COSTO SERVICIO';
						$tabla[0][$n+1] = 'TOTAL VENTA';
						$tabla[0][$n+2] = 'TOTAL UTILIDAD';
						$n+=3;
					}

					$tabla[$row][] = $totalCosto;
					$tabla[$row][] = $totalVenta;
					$tabla[$row][] = $totalUtilidad;

					if($maxNumero==0){
						$maxNumero = $n;
					} else {
						if($maxNumero<$n){
							$maxNumero = $n;
						}
					}
				}
				echo '<th>', $fecha, '</th>';
				$row++;
			}
			echo '</tr></thead><tbody>';

			$numberColumns = count($range);
			for($i=0;$i<$maxNumero;$i++){
				echo '<tr>';
				for($j=0;$j<=$numberColumns;$j++){
					if(!isset($tabla[$j][$i])){
						echo '<td>&nbsp;</td>';
					} else {
						if($j==0){
							echo '<td align="right"><b>', $tabla[$j][$i], '</b></td>';
						} else {
							if(is_numeric($tabla[$j][$i])){
								echo '<td align="right">', Currency::number($tabla[$j][$i]), '</td>';
							} else {
								echo '<td>', $tabla[$j][$i], '</td>';
							}
						}
					}
				}
				echo '</tr>';
			}

			echo '</tbody></table>';
		}
		catch(DateException $e){

		}

	}

}