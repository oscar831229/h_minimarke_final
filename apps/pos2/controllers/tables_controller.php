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

class TablesController extends ApplicationController {

	public $salon;

	public function initialize(){
		$this->setPersistance(true);
	}

	private function _getSalon($salonId){
		$usuarioId = Session::get('usuarios_id');
		if($salonId==0||!$this->Salon->count("id='$salonId' AND estado='A'")){
			$query = new ActiveRecordJoin(array(
				'fields' => array('{#Salon}.id', '{#Salon}.nombre', '{#Salon}.venta_a', '{#Salon}.alto_mesas', '{#Salon}.ancho_mesas'),
				'entities' => array('Salon', 'Permisos'),
				'conditions' => "salon.estado = 'A' AND usuarios_id='$usuarioId'"
			));
			$resulset = $query->getResultSet();
			if(count($resulset)==0){
				Flash::error('No tiene permiso para trabajar en ningún ambiente');
			}
			$salon = $resulset->getFirst();
		} else {
			$salon = $this->Salon->findFirst($salonId);
			$conditions = "salon_id='$salonId' AND usuarios_id='$usuarioId' AND estado='A'";
			if($this->Permisos->count($conditions)==0){
				Flash::error('No tiene permiso para trabajar en el ambiente "'.$salon->nombre.'"');
				return false;
			}
		}
		return $salon;
	}

	public function indexAction($salonId=0, $sendkitchen=""){

		$datosFront = $this->DatosHotel->findFirst();
		$datos = $this->Datos->findFirst();
		if(!Date::isEquals($datosFront->getFecha(), $datos->getFecha())){
			return $this->routeTo(array('controller' => 'appmenu'));
		}

		$salonId = $this->filter($salonId, 'int');
		if($this->Salon->count("estado='A'")){

			$salon = $this->_getSalon($salonId);
			if($salon==false){
				return $this->routeTo(array('controller' => 'appmenu'));
			}

			if($salon->venta_a=='A'){
				$salonMesa = $this->_getSalonMesa($salonId);
				return $this->routeTo(array('controller' => 'order', 'action' => 'add', 'id' => $salonMesa->id));
			}

			$sendkitchen = $this->filter($sendkitchen, 'alpha');
			$this->setParamToView('salon', $salon);
			$this->setParamToView('sendkitchen', $sendkitchen);
			Session::setData('current_master', 0);
			Session::setData("current_salon", $salon->id);

			//Cargar Modelos
			$this->loadModel('SalonMesas', 'Datos', 'AccountMaster', 'AccountCuentas', 'Habitacion');
			$this->salon = $salonId;
		} else {
			$this->routeToAction('crearMesas');
		}
	}

	private function _getSalonMesa($salonId){

		$usuarios_id = $_SESSION["session_data"]["usuarios_id"];
		$salon_mesas_id =  $this->SalonMesas->maximum(array('id', 'conditions' => "salon_id='$salonId' AND usuarios_id='$usuarios_id'"));

		if(empty($salon_mesas_id)){
			$salonMesa = new SalonMesas();
			$salonMesa->salon_id = $salonId;
			$salonMesa->vpos = 0;
			$salonMesa->hpos = 0;
			$salonMesa->numero = '1';
			$salonMesa->usuarios_id = $usuarios_id;
			$salonMesa->estado = 'N';
			if($salonMesa->save()==false){
				foreach($salonMesa->getMessages() as $message){
					Flash::error($message->getMessage());
				}
			}
		}else{
			$salonMesa = $this->SalonMesas->find($salon_mesas_id);
		}

		return $salonMesa;

	}

	public function crearMesasAction(){

	}

	public function addTableAction(){
		$this->setResponse('json');
		$salonMesas = new SalonMesas();
		$salonMesas->salon_id = $this->salon;
		$salonMesas->vpos = $this->getPostParam('y', 'int');
		$salonMesas->hpos = $this->getPostParam('x', 'int');
		$salonMesas->estado = 'N';
		$num = $this->SalonMesas->maximum('concat(space(6-length(numero)), numero)', 'conditions: salon_id='.$this->salon);
		if(!$num){
			$num = 1;
		} else {
			$num++;
		}
		$salonMesas->numero = $num;
		$salonMesas->save();
		return $salonMesas->id;
	}

	public function moveTableAction($id){
		$this->setResponse('view');
		$id = $this->filter($id, 'int');
		if($id>0){
			$salonMesas = $this->SalonMesas->findFirst($id);
			$salonMesas->vpos = $this->getPostParam('y', 'int');
			$salonMesas->hpos = $this->getPostParam('x', 'int');
			$salonMesas->estado = 'N';
			$salonMesas->save();
		}
	}

	public function deleteTableAction($id=0){
		$this->setResponse('view');
		$id = $this->filter($id, 'int');
		if($id>0){
			$this->SalonMesas->delete($id);
		}
	}

	public function changeNumberAction($id=0){
		$this->setResponse('view');
		$id = $this->filter($id, 'int');
		if($id>0){
			$salonMesa = $this->SalonMesas->findFirst($id);
			if($salonMesa!=false){
				$numero = $this->getPostParam('numero', 'alpha');
				$this->SalonMesas->numero = $numero;
				$this->SalonMesas->save();
			}
		}
	}

	public function openTableAction(){
		$numero = $this->getPostParam('numero', 'alpha');
		$salonId = Session::get("current_salon", "int");
		if($numero&&$salonId>0){
			$salonMesa = $this->SalonMesas->findFirst("salon_id='$salonId' AND numero='$numero'");
			if($salonMesa!=false){
				return $this->routeTo(array('controller' => 'order', 'action' => 'add', 'id' => $salonMesa->id));
			}
		}
		return $this->routeToAction('index');
	}

	public function chooseTableAction($salonMesasId=0, $salonId=0, $action=null){

		$salonId = $this->filter($salonId, 'int');
		$salonMesasId = $this->filter($salonMesasId, 'int');
		$action = $this->filter($action, 'alpha');

		if($salonMesasId>0 && $salonId>0){

			$salonMesa = $this->SalonMesas->findFirst($salonMesasId);
			if($salonMesa==false){
				$this->routeTo(array('action'  => 'index'));
			}
			if($salonId==0){
				$salon = $this->_getSalon($salonMesa->salon_id);
			} else {
				$salon = $this->_getSalon($salonId);
			}

			if($salon->venta_a=='A'){
				if($action=='joinOrders'){
					Flash::error('Los ambientes de venta directa no permiten unir pedidos');
				} else {
					Flash::error('Los ambientes de venta directa no permiten cambio de mesa');
				}
				return $this->routeTo(array('controller' => 'order', 'action' => 'add', 'id' => $salonMesasId));
			}

			$this->setParamToView('salon', $salon);
			$this->setParamToView('action', $action);
			$this->setParamToView('salonMesasId', $salonMesasId);
			$this->loadModel('Habitacion', 'Datos', 'AccountMaster', 'AccountCuentas');
		} else {
			$this->routeTo(array('action'  => 'index'));
		}
	}

	public function backAction($id=0)
	{
		if(Session::getData("salon_type")=='A'){
			$this->routeTo(array("controller" => "appmenu"));
		} else {
			$this->routeTo(array("action" => "index"));
		}
	}

	/**
	* Cambia de mesa un pedido
	*
	* @param int $initialId //Pedido inicial
	* @param int $finalId //Pedido destino
	*/
	public function changeTableAction($initialId=0, $finalId=0){

		$finalId = $this->filter($finalId, 'int');
		$initialId = $this->filter($initialId, 'int');

		if($finalId>0 && $initialId>0){

			try {

				$transaction = TransactionManager::getUserTransaction();

				$this->Account->setTransaction($transaction);
				$this->AccountCuentas->setTransaction($transaction);
				$this->AccountMaster->setTransaction($transaction);
				$this->SalonMesas->setTransaction($transaction);

				$accountMaster = $this->AccountMaster->findFirst("salon_mesas_id='$initialId' AND estado='N'");
				if($accountMaster==false){
					Flash::error('No existe el pedido');
					$transaction->rollback();
				}

				$numeroLiquidadas = 0;
				$accountCuentas = $this->AccountCuentas->findForUpdate("account_master_id='{$accountMaster->id}'");
				foreach($accountCuentas as $accountCuenta){
					if($accountCuenta->estado=='L'||$accountCuenta->estado=='B'){
						$numeroLiquidadas++;
					}
				}
				if($numeroLiquidadas>0){
					Flash::error('No se puede cambiar la mesa porque hay cuentas liquidadas en el pedido');
					$transaction->rollback();
				}

				$salonMesas = $this->SalonMesas->findFirst($finalId);
				if($salonMesas!=false){
					$salonMesas->estado = 'A';
					if($salonMesas->save()==false){
						foreach($salonMesas->getMessages() as $message){
							Flash::error('SalonMesas: '.$message->getMessage());
						}
						$transaction->rollback();
					}
				} else {
					Flash::error('No existe la mesa '.$finalId);
					$transaction->rollback();
				}

				$salonDestino = $this->Salon->findFirst($salonMesas->salon_id);
				if($salonDestino==false){
					Flash::error('No existe el ambiente destino');
					$transaction->rollback();
				}

				if($salonDestino->estado!='A'){
					Flash::error('El ambiente destino no está activo');
					$transaction->rollback();
				}

				$accountCuentas = $this->AccountCuentas->findForUpdate("account_master_id='{$accountMaster->id}' AND estado='A'");
				foreach($accountCuentas as $accountCuenta){
					$salonTipoVenta = $this->SalonTipoVenta->findFirst("salon_id='{$salonDestino->id}' AND tipo_venta_id='{$accountCuenta->tipo_venta}'");
					if($salonTipoVenta==false){
						$tipoVenta = $this->TipoVenta->findFirst("id='{$accountCuenta->tipo_venta}'");
						if($tipoVenta){
							Flash::error('El tipo de pedido "'.$tipoVenta->detalle.'" no está activo en el ambiente "'.$salonDestino->nombre.'"');
						} else {
							Flash::error('El tipo de pedido "'.$accountCuenta->tipo_venta.'" no está activo en el ambiente "'.$salonDestino->nombre.'"');
						}
						$transaction->rollback();
					}
				}

				foreach($this->Account->findForUpdate("salon_mesas_id='$initialId' AND estado='S'") as $account){

					$salonMenusItems = $this->SalonMenusItems->findFirst("salon_id='{$salonDestino->id}' AND menus_items_id='{$account->menus_items_id}' AND estado='A'");
					if($salonMenusItems==false){
						$menuItem = $this->MenusItems->findFirst($account->menus_items_id);
						if($menuItem==false){
							Flash::error('El item de menú "'.$account->menus_items_id.'" no está activo en el ambiente "'.$salonDestino->nombre.'"');
						} else {
							Flash::error('El item de menú "'.$menuItem->nombre.'" no está activo en el ambiente "'.$salonDestino->nombre.'"');
						}
						$transaction->rollback();
					}

					$account->salon_mesas_id = $finalId;
					if($account->save()==false){
						foreach($account->getMessages() as $message){
							Flash::error('Account:'. $message->getMessage());
						}
						$transaction->rollback();
					}
				}

				$salonMesas = $this->SalonMesas->findFirst($initialId);
				if($salonMesas!=false){
					$salonMesas->estado = 'N';
					if($salonMesas->save()==false){
						foreach($salonMesas->getMessages() as $message){
							Flash::error('SalonMesas:'. $message->getMessage());
						}
						$transaction->rollback();
					}
				} else {
					Flash::error('No existe la mesa origen '.$initialId);
					$transaction->rollback();
				}

				$accountMaster->salon_mesas_id = $finalId;
				if($accountMaster->save()==false){
					foreach($accountMaster->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}

				$transaction->commit();
				Flash::success('Se realizó el cambio de mesa correctamente');

				return $this->routeTo(array('controller' => 'order', 'action' => 'add', 'id' => $finalId));

			}
			catch(TransactionFailed $e){
				Flash::error('No se pudo hacer el cambio de mesa');

			}
		}
		return $this->routeTo(array('controller' => 'order', 'action' => 'add', 'id' => $initialId));
	}

	public function joinOrderAction($initialId=0, $finalId=0){

		$finalId = $this->filter($finalId, 'int');
		$initialId = $this->filter($initialId, 'int');
		if($finalId!=0&&$initialId!=0){
			try {

				$transaction = TransactionManager::getUserTransaction();

				$this->Account->setTransaction($transaction);
				$this->AccountMaster->setTransaction($transaction);
				$this->AccountCuentas->setTransaction($transaction);
				$this->SalonMesas->setTransaction($transaction);

				$accountMaster = $this->AccountMaster->findFirst("salon_mesas_id='$initialId' AND estado='N'");
				if($accountMaster==false){
					Flash::error('No existe el pedido origen');
					$transaction->rollback();
				}

				$accountMasterDestiny = $this->AccountMaster->findFirst("salon_mesas_id='$finalId' AND estado='N'");
				if($accountMasterDestiny==false){
					Flash::error('No existe el pedido detino');
					$transaction->rollback();
				}

				$salonOrigen = $this->Salon->findFirst($accountMaster->salon_id);
				if($salonOrigen==false){
					Flash::error('No existe el ambiente origen');
					$transaction->rollback();
				}

				$salonDestino = $this->Salon->findFirst($accountMasterDestiny->salon_id);
				if($salonDestino==false){
					Flash::error('No existe el ambiente destino');
					$transaction->rollback();
				}

				if($salonDestino->estado!='A'){
					Flash::error('El ambiente destino no está activo');
					$transaction->rollback();
				}

				$salonMesas = $this->SalonMesas->findFirst($finalId);
				if($salonMesas!=false){
					$salonMesas->estado = 'A';
					if($salonMesas->save()==false){
						foreach($salonMesas->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				} else {
					Flash::error('No existe la mesa '.$finalId);
					$transaction->rollback();
				}

				$cuentas = array();
				$numeroLiquidadas = 0;
				$numeroNoLiquidadas = 0;
				$numeroPendientesLiquidar = 0;
				$accountCuentas = $this->AccountCuentas->findForUpdate("account_master_id='{$accountMaster->id}'");
				foreach($accountCuentas as $accountCuenta){
					$salonTipoVenta = $this->SalonTipoVenta->findFirst("salon_id='{$salonDestino->id}' AND tipo_venta_id='{$accountCuenta->tipo_venta}'");
					if($salonTipoVenta==false){
						$tipoVenta = $this->TipoVenta->findFirst("id='{$accountCuenta->tipo_venta}'");
						if($tipoVenta){
							Flash::error('El tipo de pedido "'.$tipoVenta->detalle.'" no está activo en el ambiente "'.$salonDestino->nombre.'"');
						} else {
							Flash::error('El tipo de pedido "'.$accountCuenta->tipo_venta.'" no está activo en el ambiente "'.$salonDestino->nombre.'"');
						}
						$transaction->rollback();
					}
					if($accountCuenta->estado=='L'){
						$numeroLiquidadas++;
					} else {
						if($accountCuenta->estado=='B'){
							$numeroPendientesLiquidar++;
						} else {
							if($accountCuenta->estado=='A'){
								$numeroNoLiquidadas++;
								$cuentas[$accountCuenta->numero] = $accountCuenta->tipo_venta;
							}
						}
					}
				}
				if($numeroNoLiquidadas==0){
					Flash::error('No hay cuentas sin liquidar en el pedido origen');
					$transaction->rollback();
				}

				$destinos = array();
				$accountCuentas = $this->AccountCuentas->findForUpdate("account_master_id='{$accountMaster->id}' AND estado='A'");
				foreach($accountCuentas as $accountCuenta){

					if(!isset($destinos[$accountCuenta->cuenta])){

						$numeroCuentaDestino = $this->AccountCuentas->minimum(array('cuenta', 'conditions' => "account_master_id='{$accountMasterDestiny->id}' AND estado='A' AND tipo_venta='$accountCuenta->tipo_venta'"));
						if(!$numeroCuentaDestino){

							$numeroCuentaDestino = $this->AccountCuentas->maximum(array('cuenta', 'conditions' => "account_master_id='{$accountMasterDestiny->id}'"));
							$numeroCuentaDestino++;

							$accountCuentaDestiny = new AccountCuentas();
							$accountCuentaDestiny->setTransaction($transaction);
							$accountCuentaDestiny->account_master_id = $accountMasterDestiny->id;
							$accountCuentaDestiny->cuenta = $numeroCuentaDestino;
							if($salonOrigen->venta_a!=$salonDestino->venta_a){
								if($salon->venta_a=='H'){
									$habitacion = $this->Habitacion->findFirst("numhab='{$salonMesas->numero}'");
									if($habitacion){
										$accountCuentaDestiny->clientes_cedula = $habitacion->cedula;
										$accountCuentaDestiny->clientes_nombre = $habitacion->nombre;
										$accountCuentaDestiny->habitacion_id = $habitacion->id;
									} else {
										$accountCuentaDestiny->clientes_cedula = 0;
										$accountCuentaDestiny->clientes_nombre = 'PARTICULAR';
										$accountCuentaDestiny->habitacion_id = -1;
									}
								} else {
									$accountCuentaDestiny->clientes_cedula = 0;
									$accountCuentaDestiny->clientes_nombre = 'PARTICULAR';
									$accountCuentaDestiny->habitacion_id = -1;
								}
							} else {
								$accountCuentaDestiny->clientes_cedula = $accountCuenta->clientes_cedula;
								$accountCuentaDestiny->clientes_nombre = $accountCuenta->clientes_nombre;
								$accountCuentaDestiny->habitacion_id = $accountCuenta->habitacion_id;
							}
							$accountCuentaDestiny->prefijo = $salonDestino->prefijo_facturacion;
							$accountCuentaDestiny->propina_fija = 'N';
							$accountCuentaDestiny->numero = 0;
							$accountCuentaDestiny->tipo_venta = $accountCuenta->tipo_venta;
							$accountCuentaDestiny->estado = 'A';
							if($accountCuentaDestiny->save()==false){
								foreach($accountCuentaDestiny->getMessages() as $message){
									$transaction->rollback('AccountCuentaDestiny: '.$message->getMessage());
								}
							}
						}

						$destinos[$accountCuenta->cuenta] = $numeroCuentaDestino;
					} else {
						$numeroCuentaDestino = $destinos[$accountCuenta->cuenta];
					}

					#$accounts = $this->Account->findForUpdate("salon_mesas_id='$initialId' AND cuenta='{$accountCuenta->cuenta}' AND estado<>'C'");
					$accounts = $this->Account->findForUpdate("salon_mesas_id='$initialId' AND cuenta='{$accountCuenta->cuenta}' AND estado='S'");
					foreach($accounts as $account){
						$salonMenusItems = $this->SalonMenusItems->findFirst("salon_id='{$salonDestino->id}' AND menus_items_id='{$account->menus_items_id}' AND estado='A'");
						if($salonMenusItems==false){
							$menuItem = $this->MenusItems->findFirst($account->menus_items_id);
							if($menuItem==false){
								Flash::error('El item de menú "'.$account->menus_items_id.'" no está activo en el ambiente "'.$salonDestino->nombre.'"');
							} else {
								Flash::error('El item de menú "'.$menuItem->nombre.'" no está activo en el ambiente "'.$salonDestino->nombre.'"');
							}
							$transaction->rollback();
						}
						$account->account_master_id = $accountMasterDestiny->id;
						$account->cuenta = $numeroCuentaDestino;
						$account->salon_mesas_id = $finalId;
						if($account->save()==false){
							foreach($account->getMessages() as $message){
								Flash::error('Account: '.$message->getMessage());
							}
							$transaction->rollback();
						}
					}

					$accountCuenta->estado = 'C';
					if($accountCuenta->save()==false){
						foreach($accountCuenta->getMessages() as $message){
							Flash::error('AccountCuenta: '.$message->getMessage());
						}
						$transaction->rollback();
					}

				}

				$salonMesas = $this->SalonMesas->findFirst($initialId);
				if($salonMesas!=false){
					$salonMesas->estado = 'N';
					if($salonMesas->save()==false){
						foreach($salonMesas->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				} else {
					Flash::error('No existe la mesa '.$initialId);
					$transaction->rollback();
				}

				$accountMaster->salon_mesas_id = $finalId;
				if($numeroLiquidadas>0&&$numeroPendientesLiquidar==0){
					$accountMaster->estado = 'L';
				} else {
					if($numeroLiquidadas==0&&$numeroPendientesLiquidar==0){
						$accountMaster->estado = 'C';
					}
				}
				if($accountMaster->save()==false){
					foreach($accountMaster->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}

				$transaction->commit();
				Flash::success('Se realizó la unión de pedidos correctamente');

				return $this->routeTo(array('controller' => 'order', 'action' => 'add', 'id' => $finalId));

			}
			catch(TransactionFailed $e){
				Flash::error('No se pudo hacer la unión de pedidos');
			}
		}
		return $this->routeTo(array('controller' => 'order', 'action' => 'add', 'id' => $initialId));
	}

}
