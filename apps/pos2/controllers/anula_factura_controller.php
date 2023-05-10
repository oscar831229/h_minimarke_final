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
 * @copyright 	BH-TECK Inc. 2009-2013
 * @version		$Id$
 */

/**
 * Anula_FacturaController
 *
 */
class Anula_FacturaController extends ApplicationController
{

	public function indexAction()
	{
		$this->loadModel('Salon');
		$datos = $this->Datos->findFirst();
		$this->setParamToView('facturas', $this->Factura->find("fecha='{$datos->getFecha()}' AND estado='A' AND tipo = 'O'"));
	}

	private function _anulaFactura($factura)
	{
		$transaction = TransactionManager::getUserTransaction();
		$factura->setTransaction($transaction);

		$datos = $this->Datos->findFirst();
		if (Date::isEquals($factura->fecha, $datos->getFecha())) {
			$factura->estado = 'N';
			$accountCuenta = $factura->getAccountCuentas();
			if ($accountCuenta) {
				$cuenta = $accountCuenta->cuenta;
				$accountMasterId = $accountCuenta->account_master_id;
				$conditions = "account_master_id = '$accountMasterId' AND cuenta = '$cuenta' AND estado <> 'C'";
				$this->Account->setTransaction($transaction);
				foreach ($this->Account->findForUpdate($conditions) as $account) {
					if ($account->estado == 'L') {
						$account->estado = 'C';
					}
					if ($account->save() == false) {
						foreach ($account->getMessages() as $message) {
							Flash::addMessage($message->getMessage(), Flash::ERROR);
						}
						$transaction->rollback();
					}
				}

				$accountMaster = $this->AccountMaster->findFirst($accountMasterId);
				if ($accountMaster) {
					$accountMaster->setTransaction($transaction);
					if ($accountMaster->estado=='L'){
						$numberMaster = $this->AccountCuentas->count("account_master_id='$accountMasterId'");
						if ($numberMaster==1) {
							$accountMaster->estado = 'C';
							$salonMesa = $this->SalonMesas->findFirst($accountMaster->salon_mesas_id);
							if ($salonMesa) {
								if ($salonMesa->estado == 'A') {
									$salonMesa->estado = 'N';
									if ($salonMesa->save() == false) {
										foreach ($salonMesa->getMessage() as $message) {
											Flash::addMessage($message->getMessage(), Flash::ERROR);
										}
										$transaction->rollback();
									}
								}
							}
							if ($accountMaster->save() == false) {
								foreach ($accountMaster->getMessages() as $message) {
									Flash::addMessage($message->getMessage(), Flash::ERROR);
								}
								$transaction->rollback();
							}
						}
					}
				} else {
					Flash::addMessage('No se pudo anular la orden/factura', Flash::ERROR);
					$transaction->rollback();
				}
				if ($accountCuenta->estado == 'L') {
					$accountCuenta->estado = 'C';
					if ($accountCuenta->save() == false) {
						foreach ($accountCuenta->getMessages() as $message) {
							Flash::addMessage($message->getMessage(), Flash::ERROR);
						}
						$transaction->rollback();
					}
				}
			} else {
				Flash::addMessage('No se pudo anular la orden/factura', Flash::ERROR);
				$transaction->rollback();
			}

			if ($factura->save() == false) {
				foreach ($factura->getMessages() as $message) {
					Flash::addMessage($message->getMessage(), Flash::ERROR);
				}
				$transaction->rollback();
			}

			if($accountCuenta->tipo_venta=='H'){

				$habitacion = $accountCuenta->getHabitacion();
				if($habitacion==false){
					Flash::addMessage('No se pudo anular la orden porque ya se le hizo check-out al folio/habitación asociado', Flash::ERROR);
					$transaction->rollback();
				}

				$cargosFront = $this->CargosFront->find("prefijo_facturacion='{$factura->prefijo_facturacion}' AND numero='{$factura->consecutivo_facturacion}'");
				foreach ($cargosFront as $cargoFront) {
					$valcar = $cargoFront->getValcar();
					if ($valcar!=false) {
						$valcar->setEstado('B');
						if ($valcar->save()==false) {
							foreach ($valcar->getMessages() as $message) {
								Flash::addMessage($message->getMessage(), Flash::ERROR);
							}
							$transaction->rollback();
						}
					}
				}
			}

			$salonMesas = $accountMaster->getSalonMesas();
			if ($salonMesas) {
				if ($salonMesas->estado == 'A') {
					$salonMesas = $this->SalonMesas->findFirst("estado='N'");
					if($salonMesas==false){
						Flash::addMessage('No hay mesas libres para restaurar el pedido', Flash::ERROR);
						$transaction->rollback();
					}
				}
			} else {
				$salonMesas = $this->SalonMesas->findFirst("estado='N'");
				if($salonMesas==false){
					Flash::addMessage('No hay mesas libres para restaurar el pedido', Flash::ERROR);
					$transaction->rollback();
				}
			}

			$baseAccountMaster = new AccountMaster();
			$baseAccountMaster->setTransaction($transaction);
			$baseAccountMaster->setDebug(true);
			$baseAccountMaster->usuarios_id = Session::get('usuarios_id');
			$baseAccountMaster->nombre = Session::get('usuarios_nombre');
			$baseAccountMaster->salon_id = $salonMesas->salon_id;
			$baseAccountMaster->salon_mesas_id = $salonMesas->id;
			$baseAccountMaster->hora = $datos->getFecha().' '.Date::getCurrentTime();
			$baseAccountMaster->hora_atencion = null;
			$baseAccountMaster->numero_asientos = $accountMaster->numero_asientos;
			$baseAccountMaster->estado = 'N';
			if($baseAccountMaster->save()==false){
				foreach($baseAccountMaster->getMessages() as $message){
					Flash::addMessage($message->getMessage(), Flash::ERROR);
				}
				$transaction->rollback();
			}

			$salon = $salonMesas->getSalon();
			if($salon==false){
				Flash::addMessage('No existe el ambiente donde se realizó el pedido', Flash::ERROR);
				$transaction->rollback();
			}

			$baseAccountCuentas = new AccountCuentas();
			$baseAccountCuentas->setTransaction($transaction);
			$baseAccountCuentas->account_master_id = $baseAccountMaster->id;
			$baseAccountCuentas->cuenta = 1;
			$baseAccountCuentas->clientes_cedula = $accountCuenta->clientes_cedula;
			$baseAccountCuentas->clientes_nombre = $accountCuenta->clientes_nombre;
			$baseAccountCuentas->habitacion_id = $accountCuenta->habitacion_id;
			$baseAccountCuentas->tipo_venta = $accountCuenta->tipo_venta;
			$baseAccountCuentas->nota = $accountCuenta->nota;
			$baseAccountCuentas->prefijo = $salon->prefijo_facturacion;
			$baseAccountCuentas->propina_fija = $accountCuenta->propina_fija;
			$baseAccountCuentas->propina = $accountCuenta->propina;
			$baseAccountCuentas->estado = 'A';
			if($baseAccountCuentas->save()==false){
				foreach($baseAccountCuentas->getMessages() as $message){
					Flash::addMessage($message->getMessage(), Flash::ERROR);
				}
				$transaction->rollback();
			}

			foreach ($accountCuenta->getAccount() as $account) {
				$baseAccount = new Account();
				$baseAccount->setTransaction($transaction);
				$baseAccount->account_master_id = $baseAccountMaster->id;
				$baseAccount->salon_mesas_id = $salonMesas->id;
				$baseAccount->comanda = $account->comanda;
				$baseAccount->cuenta = 1;
				$baseAccount->asiento = $account->asiento;
				$baseAccount->menus_items_id = $account->menus_items_id;
				$baseAccount->cantidad = $account->cantidad;
				$baseAccount->cantidad_atendida = 0;
				$baseAccount->valor = $account->valor;
				$baseAccount->servicio = $account->servicio;
				$baseAccount->iva = $account->iva;
				$baseAccount->impo = $account->impo;
				$baseAccount->total = $account->total;
				$baseAccount->descuento = $account->descuento;
				$baseAccount->tiempo = Date::getCurrentTime();
				$baseAccount->note = $account->note;
				$baseAccount->send_kitchen = 'S';
				if ($baseAccount->estado != 'C') {
					$baseAccount->estado = 'S';
				}
				if ($baseAccount->save() == false) {
					foreach ($baseAccount->getMessages() as $message) {
						Flash::addMessage($message->getMessage(), Flash::ERROR);
					}
					$transaction->rollback();
				}
			}

			$salonMesas->estado = 'A';
			if ($salonMesas->save() == false) {
				foreach ($salonMesas->getMessage() as $message) {
					Flash::addMessage($message->getMessage(), Flash::ERROR);
				}
				$transaction->rollback();
			}

			if ($factura->tipo_venta == "F") {
				new POSAudit("ANULO FACTURA {$factura->prefijo_facturacion}-{$factura->consecutivo_facturacion}");
			} else {
				new POSAudit("ANULO LA ORDEN DE SERVICIO {$factura->prefijo_facturacion}-{$factura->consecutivo_facturacion}");
			}
			if($factura->tipo_venta=="F"){
				Flash::addMessage('Se anuló correctamente la factura '.$factura->prefijo_facturacion.':'.$factura->consecutivo_facturacion, Flash::SUCCESS);
			} else {
				Flash::addMessage('Se anuló correctamente la orden de servicio '.$factura->prefijo_facturacion.':'.$factura->consecutivo_facturacion, Flash::SUCCESS);
			}
			Flash::addMessage('Se restauró el pedido original', Flash::NOTICE);

			$transaction->commit();

			return $salonMesas->id;

		} else {
			Flash::addMessage('Solo es posible anular facturas ó ordenes del día activo del sistema', Flash::ERROR);
			$transaction->rollback();
		}
	}

	public function anulaAction($id, $salonId, $tipo_venta)
	{
		$this->setResponse('view');
		if ($tipo_venta == 'O') {
			$tipo_venta = "tipo_venta IN ('H', 'P', 'U', 'C')";
		} else {
			$tipo_venta = "tipo_venta = 'F'";
			throw new Exception("Error no es posible anular facturas", 1);
		}

		try {
			$pedidoId = 0;
			$id = $this->filter($id, 'alpha');
			$salonId = $this->filter($salonId, 'int');
			$conditions = "sha1(consecutivo_facturacion)='$id' AND salon_id='$salonId' AND estado='A' AND $tipo_venta";
			$factura = $this->Factura->findFirst($conditions);
			if($factura){
				$pedidoId = $this->_anulaFactura($factura);
			}
		} catch (TransactionFailed $e) {
			Flash::addMessage($e->getMessage(), Flash::ERROR);
		}

		if ($pedidoId > 0) {
			$this->redirect('order/add/' . $pedidoId);
		} else {
			$this->redirect('appmenu');
		}
	}

	public function anulaByIdAction($facturaId=0)
	{
		$facturaId = $this->filter($facturaId, 'int');
		try {
			$pedidoId = 0;
			if($facturaId>0){
				$factura = $this->Factura->findFirst($facturaId);
				if($factura){
					$pedidoId = $this->_anulaFactura($factura);
				}
			}
		} catch (TransactionFailed $e) {
			Flash::addMessage($e->getMessage(), Flash::ERROR);
		}
		if ($pedidoId > 0) {
			$this->redirect('order/add/' . $pedidoId);
		} else {
			$this->redirect('appmenu');
		}
	}

	public function existsAction($id, $salon, $tipo_venta)
	{
		$this->setResponse('json');

		$salon = $this->filter($salon, 'int');
		$id = $this->filter($id, 'int');
		$tipo_venta = $this->filter($tipo_venta, 'onechar');

		if ($tipo_venta == 'O') {
			$tipo_venta = "tipo_venta IN ('H', 'P', 'U', 'C')";
		} else {
			$tipo_venta = "tipo_venta = 'F'";
		}
		$conditions = "consecutivo_facturacion = '$id' AND salon_id = '$salon' AND $tipo_venta";
		$exists = $this->Factura->count($conditions);
		if ($exists) {
			return "yes";
		} else {
			return "no";
		}
	}

}
