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

/**
 * Módulo de liquidación de cuentas e interfase a inventarios
 *
 * @package POS
 * @subpackage Pay
 *
 */
class PayController extends ApplicationController
{

	public $pay;
	public $cuentas;
	public $salon_mesas_id;

	public function initialize()
	{
		$this->setPersistance(true);
	}

	public function indexAction($id=null)
	{
		if ($id) {
			$this->pay = explode(':', $id);
			if (count($this->pay) == 2) {
				$this->pay[0] = $this->filter($this->pay[0], 'int');
				$this->pay[1] = $this->filter($this->pay[1], 'int');
				if (!$this->pay[0]) {
					$this->pay[0] = Session::get('current_master');
				}
				$conditions = "account_master_id='{$this->pay[0]}' AND cuenta='{$this->pay[1]}'";
				$accountCuenta = $this->AccountCuentas->findFirst($conditions);
				if ($accountCuenta) {
					if ($accountCuenta->estado == 'B'){
						if ($accountCuenta->tipo_venta != 'F') {
							if(Router::wasRouted() == false){
								return $this->routeTo(array('action' => 'savePay', 'id' => $this->pay[0].':'.$this->pay[1]));
							}
						} else {
							$usuariosId = Session::getData('usuarios_id');
							$activeCash = $this->CashTray->count("usuarios_id='$usuariosId' and estado='A'");
							if (!$activeCash) {
								return $this->routeToAction('abrirCaja');
							}
						}
					}
				}
			} else {
				$this->pay = array();
			}
		} else {
			$this->pay = array();
		}
		$this->loadModel('AccountMaster', 'SalonMesas', 'Salon', 'AccountCuentas');
	}

	public function loadAccountAction($id = null)
	{
		$cuentas = array();
		$this->setResponse('view');
		if ($id) {
			$this->cuentas = explode('-', $id);
			foreach ($this->cuentas as $key => $value) {
				$cuentas[] = explode(':', $value);
			}
			$this->cuentas = $cuentas;
			foreach ($this->cuentas as $cuenta) {
				$conditions = "cuenta='{$cuenta[1]}' AND account_master_id='{$cuenta[0]}'";
				$accountCuenta = $this->AccountCuentas->findFirst($conditions);
				if ($accountCuenta->estado <> 'B') {
					$accountMaster = $this->AccountMaster->findFirst($accountCuenta->account_master_id);
					if ($accountMaster) {
						$this->salon_mesas_id = $accountMaster->salon_mesas_id;
						if ($accountCuenta->tipo_venta == 'F') {
							Flash::error('Debe generar primero la factura (Redireccionado...)');
						} else {
							Flash::error('Debe generar primero la orden de servicio (Redireccionado...)');
						}
						//$this->render('pay/errorLiquida');
					}
					return;
				}
			}
			$this->loadModel('AccountMaster', 'AccountCuentas', 'Habitacion', 'Account', 'MenusItems');
			$this->loadModel('Modifiers', 'SalonMesas', 'Salon', 'FormasPago', 'AccountModifiers');
			$this->render('pay/view_account');
		} else{
			Flash::notice('Debe selecionar una cuenta');
		}
	}

	public function getAccountsAction($id=null)
	{

	}

	/**
	 * Devuelve el nombre del cliente por su numero de habitacion
	 *
	 * @param int $id
	 * @return string
	 */
	public function getClientNameByNumhabAction($id = null)
	{
		$id = $this->filter($id, 'alpha');
		$this->setResponse('xml');
		if (!$id) {
			return '';
		}
		if($this->Habitacion->count($id)==1){
			$this->Habitacion->findFirst($id);
			if($this->Habitacion->id){
				return $this->Habitacion->nombre;
			} else{
				return 'NO SE ENCONTRO DATOS';
			}
		} else {
			return 'NO SE ENCONTRO DATOS';
		}
	}

	public function getClientNoteByNumhabAction($id = null)
	{
		$this->setResponse('xml');
		$id = $this->filter($id, 'int');
		if(!$id){
			return "";
		}
		if ($this->Habitacion->count($id) == 1) {
			$this->Habitacion->findFirst($id);
			if ($this->Habitacion->id) {
				return $this->Habitacion->nota ? $this->Habitacion->nota : 'NINGUNA';
			} else{
				return 'NINGUNA';
			}
		} else {
			return 'NINGUNA';
		}
	}

	public function getClientDocumentByNumhabAction($id = null){
		$this->setResponse('xml');
		$id = $this->filter($id, 'int');
		if(!$id){
			return '';
		}
		if ($this->Habitacion->count($id) == 1) {
			$this->Habitacion->findFirst($id);
			if ($this->Habitacion->id) {
				return $this->Habitacion->cedula;
			} else{
				return '0';
			}
		} else {
			return '0';
		}
	}

	public function getClientNameAction($cedula=null)
	{
		$controllerRequest = $this->getRequestInstance();
		if($controllerRequest->isAjax()){
			$this->setResponse('xml');
			$cedula = $this->filter($cedula, 'alpha', 'extraspaces');
			if (!$cedula) {
				return '';
			}
			if ($this->Clientes->count("cedula='$cedula'") > 0) {
				$cliente = $this->Clientes->findFirst("cedula='$cedula'");
				if ($cliente->cedula != '0') {
					return $cliente->nombre;
				}
			}
			return 'NO EXISTE EL CLIENTE EN LA BASE DE DATOS';
		}
	}

	public function saveClientAction($id=null)
	{

		$this->setResponse('view');

		$cedula = $this->filter($id, 'alpha', 'extraspaces');
		$nombre = $this->filter($nombre, 'extraspaces', 'striptags');

		if($this->getRequestParam('nombre') == 'NO EXISTE EL CLIENTE EN LA BASE DE DATOS'){
			Flash::error('Nombre de cliente invalido');
			return;
		}
		if (!$cedula) {
			Flash::error('Debe digitar el documento del Cliente');
			return;
		}
		if ($this->Clientes->count("cedula='$cedula'") == 0) {
			$cliente = new Clientes();
			$cliente->cedula = $cedula;
			$cliente->nombre = $nombre;
			if ($cliente->save() == true) {
				Flash::success('Se creó el nuevo cliente correctamente');
			} else {
				foreach ($cliente->getMessages() as $message) {
					Flash::error($cliente->getMessage());
				}
				Flash::error('Ocurrió un error al crear el cliente');
			}
		}
	}

	public function savePayAction($id=null)
	{

		$controllerRequest = ControllerRequest::getInstance();

		try {

			$cuentas = array();
			foreach (explode('-', $id) as $key => $value) {
				$cuentas[] = explode(':', $value);
			}
			if (count($cuentas) > 1) {
				Flash::error('Solo se puede liquidar una cuenta al tiempo');
				$transaction->rollback();
			}

			$transaction = TransactionManager::getUserTransaction();
			//$transaction->getConnection()->setDebug(true);

			$this->AccountMaster->setTransaction($transaction);
			$this->AccountCuentas->setTransaction($transaction);
			$this->SalonMesas->setTransaction($transaction);
			$this->Account->setTransaction($transaction);
			$this->Datos->findFirst();

			foreach($cuentas as $cuenta){
				$cuenta[0] = $this->filter($cuenta[0], 'int');
				$cuenta[1] = $this->filter($cuenta[1], 'int');
				$conditions = "cuenta='{$cuenta[1]}' AND account_master_id='{$cuenta[0]}'";
				$accountCuenta = $this->AccountCuentas->findFirst($conditions);
				if($accountCuenta==false){
					Flash::error('No existe la cuenta '.$cuenta[0].':'.$cuenta[1]);
					$transaction->rollback();
				} else {
					if($accountCuenta->estado!='B'){
						Flash::error('La cuenta no tiene un estado apto para ser liquidada '.$cuenta[0].':'.$cuenta[1]);
						$transaction->rollback();
					}
				}
			}

			$factura = $accountCuenta->getFactura();
			if ($factura == false) {
				if ($factura == false) {
					if ($accountCuenta->tipo_venta == 'F') {
						Flash::error('El pedido no tiene una factura generada asociada');
					} else {
						Flash::error('El pedido no tiene una orden de servicio generada asociada');
					}
					$transaction->rollback();
				} else {
					if ($factura->estado == 'N') {
						if($accountCuenta->tipo_venta=='F'){
							Flash::error('La factura asociada al pedido está anulada');
						} else {
							Flash::error('La orden de servicio asociada al pedido está anulada');
						}
						$transaction->rollback();
					}
				}
			}

			$accountMaster = $this->AccountMaster->findFirst($cuenta[0]);
			if($accountMaster==false){
				Flash::error('No existe el maestro del pedido: '.$cuenta[0].':'.$cuenta[1]);
				$transaction->rollback();
			}

			$salon = $this->Salon->findFirst($accountMaster->salon_id);
			if ($salon == false) {
				Flash::error('Ambiente inválido en: '.$cuenta[0].':'.$cuenta[1]);
				$transaction->rollback();
			}

			$numeroHabitacion = 0;
			$numeroCuenta = 0;
			$numeroFolio = $accountCuenta->habitacion_id;
			if($accountCuenta->tipo_venta=='H'||$accountCuenta->tipo_venta=='P'){
				$habitacion = $this->Habitacion->findFirst($numeroFolio);
				if($habitacion==false){
					Flash::error('El folio a cargar no existe ó ya se le hizo check-out');
					$transaction->rollback();
				}
				$numeroHabitacion = $habitacion->numhab;
			}

			if ($accountCuenta->tipo_venta == 'H') {

				$this->Carghab->setTransaction($transaction);
				$numeroCuenta = $this->Carghab->minimum('numcue', "conditions: numfol='$numeroFolio' AND estado='N'");
				if ($numeroCuenta == 0) {

					$cliente = $this->Clientes->findFirst("cedula='{$accountCuenta->clientes_cedula}'");
					if($cliente==false){
						Flash::error('El cliente del pedido ya no existe');
						$transaction->rollback();
					}

					$numeroCuenta = $this->Carghab->maximum(array('numcue', "conditions" => "numfol='$numeroFolio'"));
					$numeroCuenta++;
					$cuentaFolio = new Carghab();
					$cuentaFolio->setTransaction($transaction);
					$cuentaFolio->setNumfol($numeroFolio);
					$cuentaFolio->setNumcue($numeroCuenta);
					$cuentaFolio->setNumfac(0);
					$cuentaFolio->setTipfac('C');
					$cuentaFolio->setExento($cliente->exento);
					$cuentaFolio->setEstado('N');
					if ($cuentaFolio->save() == false) {
						foreach ($cuentaFolio->getMessages() as $message) {
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				} else {
					$cuentaFolio = $this->Carghab->findFirst("numfol='$numeroFolio' AND numcue='$numeroCuenta'");
				}
			}

			$totalVenta = 0;
			$comandas = array();
			$cargos = array();
			$conditions = "account_master_id='{$cuenta[0]}' AND cuenta='{$cuenta[1]}' AND estado = 'B'";
			$accounts = $this->Account->findForUpdate($conditions);
			foreach ($accounts as $account) {

				$valor = 0;
				$iva = 0;
				$impo = 0;
				$servicio = 0;
				$total = 0;

				$menuItem = $this->MenusItems->findFirst($account->menus_items_id);
				if($menuItem==false){
					Flash::error('El item "'.$account->menus_items_id.'" no existe');
					$transaction->rollback();
				}
				if ($account->descuento > 0) {
					$valor = ($account->valor - ($account->valor * $account->descuento / 100)) * $account->cantidad;
					$iva = ($account->iva - ($account->iva * $account->descuento / 100)) * $account->cantidad;
					$impo = ($account->impo - ($account->impo * $account->descuento / 100)) * $account->cantidad;
					$servicio = ($account->servicio - ($account->servicio * $account->descuento / 100)) * $account->cantidad;
					$total = ($account->total - ($account->total * $account->descuento / 100)) * $account->cantidad;
				} else {
					$valor = $account->valor * $account->cantidad;
					$iva = $account->iva * $account->cantidad;
					$impo = $account->impo * $account->cantidad;
					$servicio = $account->servicio * $account->cantidad;
					$total = $account->total * $account->cantidad;
				}
				if (($valor + $iva + $impo + $servicio) < $total) {
					$valor = $total - ($iva + $impo + $servicio);
				} else {
					if(($valor + $iva + $impo + $servicio) > $total){
						$valor = $total - ($iva + $impo + $servicio);
					}
				}

				if ($accountCuenta->tipo_venta == 'H') {

					$conditions = "salon_id='{$salon->id}' AND menus_items_id='{$menuItem->id}' AND estado = 'A'";
					$salonm = $this->SalonMenusItems->findFirst($conditions);
					if ($salonm == false) {
						Flash::error('El item "' . $menuItem->nombre . '" no está activo en el ambiente "' . $salon->nombre . '"');
						$transaction->rollback();
					} else {
						$cuentaDestino = $numeroCuenta;
						$concepto = $salonm->conceptos_id;
						$concue = $this->Concue->findFirst("numfol='$numeroFolio' AND codcar='$concepto'");
						if ($concue != false) {
							$carghab = $this->Carghab->findFirst("numfol='$numeroFolio' AND numcue='{$concue->getNumcue()}'");
							if ($carghab->getEstado() == 'N') {
								$cuentaDestino = $concue->getNumcue();
								if ($carghab->getExento() == 'S') {
									$conrel = $this->Conrel->findFirst("codcar='$concepto'");
									if ($conrel != false) {
										$concepto = $conrel->getConexe();
									}
								}
							}
						} else {
							if ($cuentaFolio->getExento() == 'S') {
								$conrel = $this->Conrel->findFirst("codcar='$concepto'");
								if ($conrel != false) {
									$concepto = $conrel->getConexe();
									$concue = $this->Concue->findFirst("numfol='$numeroFolio' AND codcar='$concepto'");
									if ($concue != false) {
										$carghab = $this->Carghab->findFirst("numfol='$numeroFolio' AND numcue='{$concue->getNumcue()}'");
										if ($carghab->getEstado() == 'N') {
											$cuentaDestino = $concue->getNumcue();
										}
									}
								}
							}
						}

						if ($cuentaDestino == 0) {
							Flash::error('La cuenta destino de recepción es inválida');
							$transaction->rollback();
						}

						if (!isset($cargos[$cuentaDestino][$concepto]['items'])) {
							$cargos[$cuentaDestino][$concepto]['items'] = array();
						}

						if (!isset($cargos[$cuentaDestino][$concepto]['valor'])) {
							$cargos[$cuentaDestino][$concepto]['valor'] = 0;
						}

						if (!isset($cargos[$cuentaDestino][$concepto]['iva'])) {
							$cargos[$cuentaDestino][$concepto]['iva'] = 0;
						}

						if (!isset($cargos[$cuentaDestino][$concepto]['impo'])) {
							$cargos[$cuentaDestino][$concepto]['impo'] = 0;
						}

						if (!isset($cargos[$cuentaDestino][$concepto]['servicio'])) {
							$cargos[$cuentaDestino][$concepto]['servicio'] = 0;
						}

						if (!isset($cargos[$cuentaDestino][$concepto]['total'])) {
							$cargos[$cuentaDestino][$concepto]['total'] = 0;
						}

						$cargos[$cuentaDestino][$concepto]['items'][] = $account->id;
						$cargos[$cuentaDestino][$concepto]['valor'] += $valor;
						$cargos[$cuentaDestino][$concepto]['iva'] += $iva;
						$cargos[$cuentaDestino][$concepto]['impo'] += $impo;
						$cargos[$cuentaDestino][$concepto]['servicio'] += $servicio;
						$cargos[$cuentaDestino][$concepto]['total'] += $total;
					}
				}
				$totalVenta += $total;
				if (!isset($comandas[$account->comanda])) {
					$comandas[$account->comanda] = 1;
				}
			}
			if (count($comandas) == 0) {
				Flash::error('No se pudo obtener el número de comanda en el pedido');
				$transaction->rollback();
			} else {
				$comandas = array_keys($comandas);
			}

			$fechaSistema = (string) $this->Datos->getFecha();
			$config = CoreConfig::readFromActiveApplication('app.ini');
			if (isset($config->pos->interpos) && $config->pos->interpos == true) {

				$centroCosto = $this->Centros->findFirst("codigo='{$salon->centro_costo}'");
				if ($centroCosto == false) {
					Flash::error('El centro de costo asignado al ambiente "' . $salon->nombre . '" es inválido, por favor corrija esto antes de liquidar');
					$transaction->rollback();
				}

				$almacenes = array();
				$formaPago = $this->getPostParam('forma0', 'int');
				foreach ($accounts as $account) {
					$menuItem = $this->MenusItems->findFirst($account->menus_items_id);
					if ($menuItem == false) {
						Flash::error('El item "'.$account->menus_items_id.'" no existe');
						$transaction->rollback();
					} else {
						$conditions = "salon_id='{$salon->id}' AND menus_items_id='{$menuItem->id}' AND estado = 'A'";
						$salonm = $this->SalonMenusItems->findFirst($conditions);
						if ($salonm == false) {
							Flash::error('El item "'.$menuItem->nombre.'" no está activo en el ambiente "'.$salon->nombre.'"');
							$transaction->rollback();
						} else {
							if($salonm->descarga=='S'){
								if($menuItem->tipo_costo!="N"){

									if ($menuItem->codigo_referencia=='@' || $menuItem->codigo_referencia=='') {
										Flash::error("Debe definir la receta/referencia del item '{$menuItem->nombre}' para hacer el descargue de inventarios");
										$transaction->rollback();
									}

									if ($menuItem->tipo_costo == 'R') {
										$exists = $this->Recetap->count("almacen=1 AND numero_rec='{$menuItem->codigo_referencia}'");
										if(!$exists){
											Flash::error("La receta {$menuItem->codigo_referencia} del item de menú '{$menuItem->nombre}' no existe para hacer la descarga de inventarios");
											$transaction->rollback();
										}
									} else {
										if ($menuItem->tipo_costo == 'I') {
											$exists = $this->Inve->count("item='{$menuItem->codigo_referencia}'");
											if (!$exists) {
												Flash::error("La referencia {$menuItem->codigo_referencia} del item de menú '{$menuItem->nombre}' no existe para hacer la descarga de inventarios");
												$transaction->rollback();
											}
										}
									}

									if ($salonm->almacen == '' || $salonm->almacen == 0) {
										Flash::error('El almacen del item "'.$menuItem->nombre.'" es inválido, por favor corrija esto antes de liquidar');
										$transaction->rollback();
									}

									if (!isset($almacenes[$salonm->almacen])) {
										$almacen = $this->Almacenes->findFirst("codigo='{$salonm->almacen}'");
										if ($almacen==false) {
											Flash::error('El almacen donde se descarga la referencia "'.$menuItem->nombre.'" es inválido, por favor corrija esto antes de liquidar');
											$transaction->rollback();
										}
										$almacenes[$salonm->almacen] = true;
									}

									if ($menuItem->tipo_costo != 'R' && $menuItem->tipo_costo != 'I') {
										Flash::error('El tipo de costo del item "'.$menuItem->nombre.'" es inválido, por favor corrija esto antes de liquidar');
										$transaction->rollback();
									}

									$invepos = new Invepos();
									$invepos->setTransaction($transaction);
									$invepos->setPrefac($accountCuenta->prefijo);
									$invepos->setNumfac($accountCuenta->numero);
									$invepos->setFecha($fechaSistema);
									$invepos->setAlmacen($salonm->almacen);
									$invepos->setCentroCosto($salon->centro_costo);
									$invepos->setTipo($menuItem->tipo_costo);
									$invepos->setCodigo($menuItem->codigo_referencia);
									$invepos->setMenusItemsId($menuItem->id);
									$invepos->setCantidad($account->cantidad);

									if ($menuItem->descontar != 'T') {
										$invepos->setCantidad($account->cantidad);
										$invepos->setCantidadu(0);
									} else {
										$invepos->setCantidadu($account->cantidad);
										$invepos->setCantidad(0);
									}

									$invepos->setEstado('N');
									if ($invepos->save() == false) {
										foreach($invepos->getMessages() as $message){
											Flash::error('Invepos: '.$message->getMessage());
										}
										$transaction->rollback();
									}
								}
							}

							foreach($account->getAccountModifiers() as $accountModifier){

								$modifier = $accountModifier->getModifiers();
								if ($modifier->tipo_costo != 'N') {

									if ($modifier->codigo_referencia == '@' || $modifier->codigo_referencia == '') {
										Flash::error("Debe definir la receta/referencia del modificador '{$modifier->nombre}' para hacer el descargue de inventarios");
										$transaction->rollback();
									}

									if ($modifier->tipo_costo == 'R') {
										$exists = $this->Recetap->count("almacen=1 AND numero_rec='{$modifier->codigo_referencia}'");
										if (!$exists) {
											Flash::error("La receta {$modifier->codigo_referencia} del modificador '{$modifier->nombre}' no existe para hacer la descarga de inventarios");
											$transaction->rollback();
										}
									} else {
										if ($modifier->tipo_costo == 'I') {
											$exists = $this->Inve->count("item='{$modifier->codigo_referencia}'");
											if (!$exists) {
												Flash::error("La referencia {$modifier->codigo_referencia} del modificador '{$modifier->nombre}' no existe para hacer la descarga de inventarios");
												$transaction->rollback();
											}
										}
									}

									if (!isset($almacenes[$salonm->almacen])) {
										$almacen = $this->Almacenes->findFirst("codigo='{$salonm->almacen}'");
										if ($almacen == false) {
											Flash::error('El almacen asociado a la referencia "'.$menuItem->nombre.'" es inválido, por favor corrija esto para liquidar los modificadores');
											$transaction->rollback();
										}
										$almacenes[$salonm->almacen] = true;
									}

									$invepos = new Invepos();
									$invepos->setTransaction($transaction);
									$invepos->setPrefac($accountCuenta->prefijo);
									$invepos->setNumfac($accountCuenta->numero);
									$invepos->setFecha($fechaSistema);
									$invepos->setAlmacen($salonm->almacen);
									$invepos->setCentroCosto($salon->centro_costo);
									$invepos->setTipo($modifier->tipo_costo);
									$invepos->setCodigo($modifier->codigo_referencia);
									$invepos->setMenusItemsId($menuItem->id);
									$invepos->setCantidad($account->cantidad);
									if($menuItem->descontar!='T'){
										$invepos->setCantidad($account->cantidad);
										$invepos->setCantidadu(0);
									} else {
										$invepos->setCantidadu($account->cantidad);
										$invepos->setCantidad(0);
									}
									$invepos->setEstado('N');
									if($invepos->save()==false){
										foreach($invepos->getMessages() as $message){
											Flash::error('Invepos: '.$message->getMessage());
										}
										$transaction->rollback();
									}
								}
							}
						}
					}
				}
			}

			if($accountCuenta->tipo_venta=='F'){
				$totalPropina = $this->getPostParam('total_propina', 'double');
			} else {
				$totalPropina = $factura->propina;
			}
			$totalPropina = LocaleMath::round($totalPropina, 0);

			if ($accountCuenta->tipo_venta == 'F') {
				foreach ($accounts as $account) {

					$conditions = "menus_items_id='{$account->menus_items_id}' AND salon_id='{$salon->id}' AND estado = 'A'";
					$salonm = $this->SalonMenusItems->findFirst($conditions);
					if ($salonm == false) {
						Flash::error('El item "'.$menuItem->nombre.'" no está activo en el ambiente "'.$salon->nombre.'"');
						$transaction->rollback();
					} else {
						$ventasPos = new Venpos();
						$ventasPos->setTransaction($transaction);
						$ventasPos->setSalonId($salon->id);
						$ventasPos->setPrefac($accountCuenta->prefijo);
						$ventasPos->setNumfac($accountCuenta->numero);
						$ventasPos->setFecha($fechaSistema);
						$ventasPos->setCedula($accountCuenta->clientes_cedula);
						$ventasPos->setCodcar($salonm->conceptos_id);
						$ventasPos->setMenusItemsId($account->menus_items_id);

						if($account->descuento>0){
							$valor = ($account->valor - $account->valor * $account->descuento / 100) * $account->cantidad;
							$iva = ($account->iva - $account->iva * $account->descuento / 100) * $account->cantidad;
							$impo = ($account->impo - $account->impo * $account->descuento / 100) * $account->cantidad;
							$valser = ($account->servicio - $account->servicio * $account->descuento/100) * $account->cantidad;
							$total = ($account->total - $account->total * $account->descuento/100) * $account->cantidad;
						} else {
							$valor = $account->valor * $account->cantidad;
							$iva = $account->iva * $account->cantidad;
							$impo = $account->impo * $account->cantidad;
							$valser = $account->servicio * $account->cantidad;
							$total = $account->total * $account->cantidad;
						}

						if(($valor + $iva + $impo + $valser) != $total){
							$valor = $total - ($iva + $impo + $valser);
						}

						$valor = LocaleMath::round($valor, 0);
						$iva = LocaleMath::round($iva, 0);
						$impo = LocaleMath::round($impo, 0);
						$valser = LocaleMath::round($valser, 0);
						$total = LocaleMath::round($total, 0);

						$ventasPos->setValor($valor);
						$ventasPos->setIva($iva);
						$ventasPos->setImpo($impo);
						$ventasPos->setValser($valser);
						$ventasPos->setTotal($total);

						if ($ventasPos->save() == false) {
							foreach ($ventasPos->getMessages() as $message) {
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
				}

				if ($totalPropina > 0) {
					$ventasPos = new Venpos();
					$ventasPos->setTransaction($transaction);
					$ventasPos->setSalonId($salon->id);
					$ventasPos->setPrefac($accountCuenta->prefijo);
					$ventasPos->setNumfac($accountCuenta->numero);
					$ventasPos->setFecha($fechaSistema);
					$ventasPos->setCedula($accountCuenta->clientes_cedula);
					$ventasPos->setCodcar($salon->conceptos_id);
					$ventasPos->setMenusItemsId(0);
					$ventasPos->setValor(0);
					$ventasPos->setIva(0);
					$ventasPos->setImpo(0);
					$ventasPos->setValser($totalPropina);
					$ventasPos->setTotal($totalPropina);
					if ($ventasPos->save() == false) {
						foreach ($ventasPos->getMessages() as $message) {
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				}

				for ($i = 0; $i <= 8; $i++) {
					$valorPago = $this->getPostParam('pago'.$i, 'double');
					$formaPago = $this->getPostParam('forma'.$i, 'int');
					if ($valorPago > 0) {
						$ingresoPos = new Ingpos();
						$ingresoPos->setTransaction($transaction);
						$ingresoPos->setSalonId($salon->id);
						$ingresoPos->setPrefac($accountCuenta->prefijo);
						$ingresoPos->setNumfac($accountCuenta->numero);
						$ingresoPos->setFecha($fechaSistema);
						$ingresoPos->setCedula($accountCuenta->clientes_cedula);
						$ingresoPos->setForpag($formaPago);
						$ingresoPos->setValor($valorPago);
						if ($ingresoPos->save() == false) {
							foreach ($ingresoPos->getMessages() as $message) {
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
				}
			}

			if ($accountCuenta->tipo_venta == 'H') {

				if (count($cargos) == 0) {
					Flash::error('El pedido no tiene items para transferir al front');
					$transaction->rollback();
				}

				if ($salon->usuarios_hotel_id > 0) {
					$usuarioHotel = $this->UsuariosHotel->findFirst($salon->usuarios_hotel_id);
					if ($usuarioHotel == false) {
						Flash::error('El usuario de interface del hotel asignado al ambiente "'.$salon->nombre.'" no existe');
						$transaction->rollback();
					}
				} else {
					Flash::error('El usuario de interface del hotel asignado al ambiente "'.$salon->nombre.'" no existe');
					$transaction->rollback();
				}

				$this->Valcar->setTransaction($transaction);
				foreach ($cargos as $numeroCuenta => $cargosCuenta) {
					foreach ($cargosCuenta as $codcar => $cargo) {

						$nuevoItem = $this->_getFolioItem($numeroFolio, $numeroCuenta);
						$cargoFront = new CargosFront();
						$cargoFront->setTransaction($transaction);
						$cargoFront->setNumeroFolio($numeroFolio);
						$cargoFront->setNumeroCuenta($numeroCuenta);
						$cargoFront->setItem($nuevoItem);
						$cargoFront->setCodigoCargo($codcar);
						$cargoFront->setPrefijoFacturacion($accountCuenta->prefijo);
						$cargoFront->setNumero($accountCuenta->numero);
						$cargoFront->setEsPropina("N");
						$cargoFront->setEstado("A");
						if ($cargoFront->save() == false) {
							foreach ($cargoFront->getMessages() as $message) {
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}

						foreach ($cargo['items'] as $accountId) {
							$detalleCargo = new CargosFrontDetalle();
							$detalleCargo->setCargosFrontId($cargoFront->getId());
							$detalleCargo->setAccountId($accountId);
							if ($detalleCargo->save() == false) {
								foreach ($detalleCargo->getMessages() as $message) {
									Flash::error($message->getMessage());
								}
								$transaction->rollback();
							}
						}

						$cargo['valor'] = LocaleMath::round($cargo['valor'], 0);
						$cargo['iva'] = LocaleMath::round($cargo['iva'], 0);
						$cargo['impo'] = LocaleMath::round($cargo['impo'], 0);
						$cargo['servicio'] = LocaleMath::round($cargo['servicio'], 0);
						$cargo['total'] = LocaleMath::round($cargo['total'], 0);

						if(($cargo['valor'] + $cargo['iva'] + $cargo['servicio']) != $cargo['total']) {
							$cargo['valor'] = $cargo['total'] - ($cargo['iva'] + $cargo['impo'] + $cargo['servicio']);
						}

						$valcar = new Valcar();
						$valcar->setTransaction($transaction);
						$valcar->setNumfol($numeroFolio);
						$valcar->setNumcue($numeroCuenta);
						$valcar->setItem($nuevoItem);
						$valcar->setCodusu($salon->usuarios_hotel_id);
						$valcar->setCodcaj(1);
						$valcar->setFecha($fechaSistema . ' ' . Date::getCurrentTime());
						$valcar->setCantidad(1);
						$valcar->setCodcar($codcar);

						if ($accountCuenta->tipo_venta == 'F') {
							$valcar->setCladoc('FAC');
						} else {
							$valcar->setCladoc('ORD');
						}

						$valcar->setNumdoc($accountCuenta->numero);
						$valcar->setValor($cargo['valor']);
						$valcar->setIva($cargo['iva']);
						$valcar->setImpo($cargo['impo']);
						$valcar->setValser($cargo['servicio']);
						$valcar->setValter(0);
						$valcar->setTotal($cargo['total']);
						$valcar->setEstado('N');
						if ($valcar->save() == false) {
							foreach ($valcar->getMessages() as $message) {
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}

						$valnot = new Valnot();
						$valnot->setTransaction($transaction);
						$valnot->setNumfol($numeroFolio);
						$valnot->setNumcue($numeroCuenta);
						$valnot->setItem($nuevoItem);
						$valnot->setNota('Comandas=' . join(', ', $comandas) . ' Cajero=' . Session::get('usuarios_nombre') . ' Ambiente=' . $salon->nombre);
						if ($valnot->save() == false) {
							foreach ($valnot->getMessages() as $message) {
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}

					}
				}

				if ($totalPropina > 0) {

					$cuentaDestino = $cuentaFolio->getNumcue();
					$concepto = $salon->conceptos_id;
					$concue = $this->Concue->findFirst("numfol='$numeroFolio' AND codcar='$concepto'");
					if ($concue != false) {
						$carghab = $this->Carghab->findFirst("numfol='$numeroFolio' AND numcue='{$concue->getNumcue()}'");
						if ($carghab->getEstado() == 'N') {
							$cuentaDestino = $concue->getNumcue();
							if ($carghab->getExento() == 'S') {
								$conrel = $this->Conrel->findFirst("codcar='$concepto'");
								if ($conrel != false) {
									$concepto = $conrel->getConexe();
								}
							}
						}
					}

					$nuevoItem = $this->_getFolioItem($numeroFolio, $cuentaDestino);
					$cargoFront = new CargosFront();
					$cargoFront->setTransaction($transaction);
					$cargoFront->setNumeroFolio($numeroFolio);
					$cargoFront->setNumeroCuenta($cuentaDestino);
					$cargoFront->setItem($nuevoItem);
					$cargoFront->setPrefijoFacturacion($accountCuenta->prefijo);
					$cargoFront->setCodigoCargo($concepto);
					$cargoFront->setNumero($accountCuenta->numero);
					$cargoFront->setEsPropina("S");
					$cargoFront->setEstado("A");
					if ($cargoFront->save() == false) {
						foreach ($cargoFront->getMessages() as $message) {
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}

					$valcar = new Valcar();
					$valcar->setTransaction($transaction);
					$valcar->setNumfol($numeroFolio);
					$valcar->setNumcue($cuentaDestino);
					$valcar->setItem($nuevoItem);
					$valcar->setCodusu($salon->usuarios_hotel_id);
					$valcar->setCodcaj(1);
					$valcar->setFecha($fechaSistema.' '.Date::getCurrentTime());
					$valcar->setCantidad(1);
					$valcar->setCodcar($salon->conceptos_id);
					if($accountCuenta->tipo_venta=='F'){
						$valcar->setCladoc('FAC');
					} else {
						$valcar->setCladoc('ORD');
					}
					$valcar->setNumdoc($accountCuenta->numero);
					$valcar->setValor(0);
					$valcar->setIva(0);
					$valcar->setImpo(0);
					$valcar->setValser($totalPropina);
					$valcar->setValter(0);
					$valcar->setTotal($totalPropina);
					$valcar->setEstado('N');
					if($valcar->save()==false){
						foreach($valcar->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}

					$valnot = new Valnot();
					$valnot->setTransaction($transaction);
					$valnot->setNumfol($numeroFolio);
					$valnot->setNumcue($numeroCuenta);
					$valnot->setItem($nuevoItem);
					$valnot->setNota('Comandas='.join(', ', $comandas).' Cajero='.Session::get('usuarios_nombre').' Ambiente='.$salon->nombre);
					if($valnot->save()==false){
						foreach($valnot->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				}
			}

			$sumaPagos = 0;
			for($i=0;$i<=8;$i++){
				$valorPago = $this->getPostParam('pago'.$i, 'double');
				if($valorPago>0){
					$pago = new PagosFactura();
					$pago->setTransaction($transaction);
					$pago->prefijo_facturacion = $accountCuenta->prefijo;
					$pago->consecutivo_facturacion = $accountCuenta->numero;
					if($accountCuenta->tipo_venta=='F'){
						$pago->tipo = 'F';
					} else {
						$pago->tipo = 'O';
					}
					$pago->formas_pago_id = $this->getPostParam('forma'.$i, 'int');
					$pago->pago = $valorPago;
					if($accountCuenta->tipo_venta=='P'){
						$pago->cargo_plan = 'S';
					} else {
						$pago->cargo_plan = 'N';
					}
					$pago->habitacion_id = $numeroFolio;
					$pago->cuenta = $numeroCuenta;
					if($pago->save()==false){
						foreach($pago->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
					$sumaPagos+=$valorPago;
				}
			}

			if($accountCuenta->tipo_venta=='F'){
				$totalPagos = $totalVenta+$totalPropina;
				if($sumaPagos!=$totalPagos){
					if($totalPropina>0){
						Flash::error('El pago no coincide con el valor del servicio + la propina');
					} else {
						Flash::error('El pago no coincide con el valor del servicio');
					}
					$transaction->rollback();
				}
			}

			if($accountCuenta->tipo_venta=='F'){
				new POSAudit('SE LIQUIDO LA FACTURA '.$accountCuenta->prefijo.'-'.$accountCuenta->numero, $transaction);
			} else {
				new POSAudit('SE LIQUIDO LA ORDEN DE SERVICIO '.$accountCuenta->prefijo.'-'.$accountCuenta->numero, $transaction);
			}

			$accountCuenta->propina = $totalPropina;
			$accountCuenta->estado = 'L';
			if($accountCuenta->save()==false){
				foreach($accountCuenta->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}

			$factura->propina = $totalPropina;
			if($factura->save()==false){
				foreach($factura->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}

			$cerrarPedido = false;
			$conditions = "account_master_id='{$accountMaster->id}' AND estado IN ('B', 'A')";
			$numberCuentas = $this->AccountCuentas->count($conditions);
			if($numberCuentas==0){
				$cerrarPedido = true;
			} else {
				$conItems = true;
				$conditions = "account_master_id='{$accountMaster->id}' AND id<>'{$accountCuenta->id}' AND estado NOT IN ('L', 'C')";
				$accountCuentas = $this->AccountCuentas->find($conditions);
				foreach($accountCuentas as $cuentaPedido){
					$numeroItems = $this->Account->count("account_master_id='{$cuentaPedido->account_master_id}' AND cuenta='{$cuentaPedido->cuenta}' AND estado NOT IN ('C', 'L')");
					if($numeroItems!=0){
						$conItems = false;
					} else {
						$cuentaPedido->estado = 'C';
						if($cuentaPedido->save()==false){
							foreach($cuentaPedido->getMessages() as $message){
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
				}
				if($conItems==true){
					$cerrarPedido = true;
				}
			}

			foreach($accounts as $account){
				if($account->estado=='B'){
					if(!$account->tiempo_final){
						$account->tiempo_final = Date::getCurrentTime();
					}
					$account->estado = 'L';
					if($account->save()==false){
						foreach($account->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				}
			}

			if($cerrarPedido==true){
				if(!$accountMaster->hora_atencion){
					$accountMaster->hora_atencion = $fechaSistema.' '.Date::getCurrentTime();
				}
				$accountMaster->estado = 'L';
				if($accountMaster->save()==false){
					foreach($accountMaster->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
				$salonMesa = $accountMaster->getSalonMesas();
				if($salonMesa!=false){
					$salonMesa->estado = 'N';
					if($salonMesa->save()==false){
						foreach($salonMesa->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				}
			}

			if ($salon->descarga_online=='S') {
				if (class_exists('InterfasePOS2')) {
					new InterfasePOS2(false, true, $transaction, false, (string)$this->Datos->getFecha());
				} else {
					throw new Exception("Clase 'InterfasePOS2' no esta cargada ", 1);
				}

			}

			if($transaction->isValid()==false){
				throw new TransactionFailed('Transacción abortada al descargar de inventarios',1,null);
			}

			$transaction->commit();
			if($cerrarPedido==false){
				return $this->redirect('order/add/'.$accountMaster->salon_mesas_id);
			} else {
				GarbageCollector::freeControllerData('order');
				return $this->redirect('tables/index/'.$accountMaster->salon_id);
			}

		}
		catch(DbLockAdquisitionException $e){
			Flash::error("No se pudo efectuar la cancelación del pago (".$e->getLine().")");
			Flash::error("La base de datos está bloqueada en este momento, por favor intente nuevamente en un momento");

		}
		catch(TransactionFailed $e){
			Flash::error("No se pudo efectuar la cancelación del pago (".$e->getLine().")");
			Flash::error($e->getMessage());
		}
		catch(Exception $e){
			Flash::error("No se pudo efectuar la cancelación del pago (".$e->getLine().")");
			Flash::error($e->getMessage());
		}

		return $this->routeTo(array('action' => 'index'));
	}

	public function abrirCajaAction(){

	}

	private function _getFolioItem($numeroFolio, $numeroCuenta){
		$item = $this->Valcar->maximum('item', "conditions: numfol='{$numeroFolio}' AND numcue='$numeroCuenta'");
		if(!$item){
			$item = 1;
		} else {
			$item++;
		}
		return $item;
	}

	private function interpos(){
		$interpos = new InterfasePOS();
	}

	public function loadInterposAction(){
		$interpos = new InterfasePOS();
	}

}
