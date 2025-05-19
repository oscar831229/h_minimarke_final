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

class CloseController extends ApplicationController
{

	public function indexAction()
	{
		try {

			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();

			$fechaHotel = $this->DatosHotel->findFirst()->getFecha();
			$fechaPOS = $datos->getFecha();

			if(Date::isLater($fechaPOS, $fechaHotel)){
				Flash::addMessage('El cierre del front debe realizarse antes de cerrar nuevamente en el POS', Flash::ERROR);
				$transaction->rollback();
			}

			$this->_calculateDiaryReport($transaction, $fechaPOS);

			$fecha_cierre = $fechaPOS;

			$note = "REALIZÓ EL CIERRE DEL DÍA DE {$fechaPOS}";
			$fecha = new Date($datos->getFecha());
			$fecha->addDays(1);
			$datos->setFecha($fecha->getDate());
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$this->routeTo(array('controller' => 'appmenu'));
			} else {
				new POSAudit($note);
				Flash::addMessage('Se realizó correctamente el cierre del día', Flash::SUCCESS);
				$this->redirect('appmenu');
			}

			$this->Account->setTransaction($transaction);
			foreach($this->Account->find("send_kitchen='N'") as $account){
				$account->send_kitchen = "S";
				if($account->save()==false){
					foreach($account->getMessages() as $message){
						Flash::addMessage($message->getMessage(), Flash::ERROR);
					}
					$transaction->rollback();
				}
			}
			$transaction->commit();

			try {

				// $config = CoreConfig::readFromActiveApplication('app.ini');
			
				// $envio_emails = isset($config->pos->envio_emails) ? $config->pos->envio_emails: '';
			
				// Ejecutar el comando en segundo plano
				$urlservicio = Utils::getExternalUrl("pos2/sincronizar_terceros/sincronizar/{$fecha_cierre}");
				if (substr(php_uname(), 0, 7) == "Windows"){ 
					$comando = 'start /B curl --request GET "'.$urlservicio.'" > NUL 2>&1';
					pclose(popen($comando, "r"));  
				} 
				else { 
					$comando = "curl --request GET '{$urlservicio}' > /dev/null 2>&1 &";
					exec($comando);   
				}
			
			} catch (Exception $e) {
				
			}

		}
		catch(TransactionFailed $e){
			$this->redirect('appmenu');
		}

	}

	private function _calculateDiaryReport($transaction, $fechaPOS){

		$fechaPOS = (string) $fechaPOS;

		$this->Estacon->setTransaction($transaction);
		$this->Estacon->deleteAll("front='N' AND fecha='$fechaPOS'");

		$estadistica = array();
		$this->Factura->setTransaction($transaction);
		foreach($this->Factura->find("fecha='$fechaPOS' AND tipo='F' AND estado='A'") as $factura){
			foreach($factura->getDetalleFactura() as $detalleFactura){

				$menuItem = $detalleFactura->getMenusItems();
				if($menuItem==false){
					Flash::addMessage('El item '.$detalleFactura->menus_items_id.' fue facturado pero ya no existe', Flash::ERROR);
					$transaction->rollback();
				}

				$salonMenusItems = $this->SalonMenusItems->findFirst("salon_id='{$factura->salon_id}' AND menus_items_id='{$detalleFactura->menus_items_id}'");
				if($salonMenusItems==false){
					Flash::addMessage('El item '.$detalleFactura->menus_items_id.' no está activo en el ambiente '.$factura->salon_id, Flash::ERROR);
					$transaction->rollback();
				}

				$cargo = $this->Cargos->findFirst("codcar='{$salonMenusItems->conceptos_id}'");
				if($cargo==false){
					Flash::addMessage('El concepto de recepción asociado a el item '.$menuItem->nombre.' no es válido en el ambiente '.$factura->salon_id, Flash::ERROR);
					$transaction->rollback();
				}

				if(!isset($estadistica[$factura->salon_id][$salonMenusItems->conceptos_id]['valor'])){
					$estadistica[$factura->salon_id][$salonMenusItems->conceptos_id]['valor'] = $detalleFactura->valor;
				} else {
					$estadistica[$factura->salon_id][$salonMenusItems->conceptos_id]['valor'] += $detalleFactura->valor;
				}

				if(!isset($estadistica[$factura->salon_id][$salonMenusItems->conceptos_id]['iva'])){
					$estadistica[$factura->salon_id][$salonMenusItems->conceptos_id]['iva'] = $detalleFactura->iva;
				} else {
					$estadistica[$factura->salon_id][$salonMenusItems->conceptos_id]['iva'] += $detalleFactura->iva;
				}

				if(!isset($estadistica[$factura->salon_id][$salonMenusItems->conceptos_id]['servicio'])){
					$estadistica[$factura->salon_id][$salonMenusItems->conceptos_id]['servicio'] = $detalleFactura->servicio;
				} else {
					$estadistica[$factura->salon_id][$salonMenusItems->conceptos_id]['servicio'] += $detalleFactura->servicio;
				}

			}

			if ($factura->propina > 0) {
				$salon = $this->Salon->findFirst($factura->salon_id);
				if($salon==false){
					Flash::addMessage('El ambiente '.$factura->salon_id.' generó facturas pero ya no existe', Flash::ERROR);
					$transaction->rollback();
				}
				if(!isset($estadistica[$factura->salon_id][$salon->conceptos_id]['servicio'])){
					$estadistica[$factura->salon_id][$salon->conceptos_id]['servicio'] = $factura->propina;
				} else {
					$estadistica[$factura->salon_id][$salon->conceptos_id]['servicio'] += $factura->propina;
				}
			}

		}

		if (count($estadistica)) {
			foreach ($estadistica as $salonId => $concepto) {
				foreach ($concepto as $codigoCargo => $cargo) {
					$estacon = new Estacon();
					$estacon->setTransaction($transaction);
					$estacon->setFront('N');
					$estacon->setFecha((string)$fechaPOS);
					$estacon->setCodcar($codigoCargo);
					$estacon->setCodsal($salonId);
					if (isset($cargo['valor'])) {
						$estacon->setValor($cargo['valor']);
					} else {
						$estacon->setValor(0);
					}
					if (isset($cargo['iva'])) {
						$estacon->setIva($cargo['iva']);
					} else {
						$estacon->setIva(0);
					}
					if (isset($cargo['servicio'])) {
						$estacon->setServicio($cargo['servicio']);
					} else {
						$estacon->setServicio(0);
					}
					$estacon->setAloja('N');
					if ($estacon->save() == false) {
						foreach ($account->getMessages() as $message) {
							Flash::addMessage('Estadisticas: ' . $message->getMessage(), Flash::ERROR);
						}
						$transaction->rollback();
					}
				}
			}
		}

	}

	public function recalculaDiaAction($fecha)
	{
		$this->setResponse('view');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$transaction->getConnection()->setDebug(true);
			$fecha = $this->filter($fecha, 'date');
			if($fecha){
				$this->_calculateDiaryReport($transaction, $fecha);
				$transaction->commit();
			}
		}
		catch(TransactionFailed $e){
			foreach(Flash::getMessages() as $message){
				Flash::show($message);
			}
			Flash::error($e->getMessage());
		}
	}


}
