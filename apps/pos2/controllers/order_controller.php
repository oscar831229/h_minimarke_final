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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

class OrderController extends ApplicationController
{

	/**
	 * Numero de menus_items actual
	 *
	 * @var string
	 */
	public $numero = 0;

	/**
	 *
	 * Tipo de Venta
	 *
	 * @var string
	 */
	public $tipo_venta = 'H';

	/**
	 * Id del Account Master actual
	 *
	 * @var integer
	 */
	public $current_master = 0;

	/**
	 * Numero de cuenta actual
	 *
	 * @var integer
	 */
	public $numero_cuenta = 0;

	/**
	 * Numero de asientos actual
	 *
	 * @var integer
	 */
	public $numero_asientos = 0;

	/**
	 * Numero de mesa actual
	 *
	 * @var integer
	 */
	public $numero_mesa = 0;

	/**
	 * Salon actual
	 */
	public $salon_id = 0;

	/**
	 * Porcentaje Servicio
	 *
	 * @var int
	 */
	public $porcentaje_servicio = 0;

	/**
	 * Account Id Actual
	 */
	public $account_id = 0;

	/**
	 * Order
	 */
	public $order;

	/**
	 * Silla
	 *
	 * @var integer
	 */
	public $silla;

	/**
	 * Último item agregado
	 *
	 * @var integer
	 */
	public $last_account_id;

	/**
	 * Último item modificado
	 *
	 * @var integer
	 */
	public $last_changed_account_id;

	/**
	 * Numero comanda
	 *
	 * @var integer
	 */
	public $numero_comanda;

	/**
	 * Variable para almacenar la cuenta temporalmente
	 *
	 * @var AccountCuentas
	 */
	public $AccountCuenta;

	/**
	 * Accion del contexto conversacional
	 *
	 * @var string
	 */
	public $action;

	public function initialize()
	{
		$this->setPersistance(true);
	}

	/**
	 * Accion por defecto
	 *
	 */
	public function indexAction()
	{
		$this->current_master = 0;
		$this->routeTo(array('controller' => 'tables'));
	}

	/**
	 * Verifica si una mesa existe y empieza a tomar el pedido
	 *
	 * @param integer $id
	 */
	public function addAction($salonMesasId=0, $action="")
	{

		$salonMesasId = $this->filter($salonMesasId, 'int');

		if (!$salonMesasId) {
			return $this->routeTo(array(
				'controller' => 'tables',
				'action' => 'index',
				'id' => '1'
			));
		}

		if (Session::getData('salon_type') == 'A' || Browser::isMobile()) {
			$this->numero_asientos = 1;
		}

		if ($this->account_id) {
			if ($this->account_id != $salonMesasId) {
				$this->current_master = 0;
				$this->numero_cuenta = 0;
				$this->numero_comanda = 0;
				$this->silla = 0;
				$this->last_account_id = 0;
				$this->numero_asientos = 0;
				$this->tipo_venta = 'H';
			}
		}

		if ($action) {
			$this->action = $this->filter($action, 'alpha');
		}
		$this->account_id = $salonMesasId;
		$salonMesa = $this->SalonMesas->findFirst($salonMesasId);
		if($salonMesa!=false){

			$accountMaster = $this->AccountMaster->findFirst("salon_mesas_id='$salonMesasId' AND estado = 'N'");
			if($accountMaster){
				$this->current_master = $accountMaster->id;
				$accountCuenta = $this->AccountCuentas->findFirst("account_master_id={$accountMaster->id} AND estado = 'A'");
				if($accountCuenta){
					$this->numero_cuenta = $accountCuenta->cuenta;
					$this->numero_asientos = $accountMaster->numero_asientos;
					Session::set('numero_cuenta', $accountCuenta->cuenta);
					Session::set('current_master', $accountMaster->id);
				}
			} else {
				$this->current_master = 0;
				$this->numero_cuenta = 1;
				Session::set('numero_cuenta', 1);
				Session::set('current_master', 0);
			}


			$this->salon_id = $salonMesa->salon_id;
			$salon = $this->Salon->findFirst($this->salon_id);
			if($salon==false){
				Flash::error('El ambiente no existe');
				return $this->routeTo(array('controller' => 'tables', 'action' => 'index', 'id' => 0));
			}
			$this->setParamToView('salon', $salon);
			$this->porcentaje_servicio = $salon->porcentaje_servicio;
			if($salonMesa->numero){
				$this->numero_mesa = $salonMesa->numero;
			} else {
				$this->numero_mesa = 1;
			}
		} else {
			Flash::error('La mesa no existe');
			return $this->routeTo(array('controller' => 'tables', 'action' => 'index', 'id' => 0));
		}

		if($this->numero_cuenta){
			if($this->current_master){
				$cuenta = $this->AccountCuentas->findFirst("account_master_id='{$this->current_master}' AND cuenta = '{$this->numero_cuenta}' AND estado='A'");
				if($cuenta!=false){
					$this->tipo_venta = $cuenta->tipo_venta;
					Session::set('numero_cuenta', $this->numero_cuenta);
				} else {
					$this->numero_cuenta = 0;
				}
			} else {
				$this->numero_cuenta = 0;
			}
		}

		if(!$this->numero_cuenta){
			Session::set('numero_cuenta', 1);
			$this->numero_cuenta = 1;
			if($this->current_master){
				$maxCuenta = $this->Account->maximum(array("cuenta", "conditions" => "account_master_id='{$this->current_master}' AND estado <> 'C'"));
				if($maxCuenta){
					$this->numero_cuenta = $maxCuenta;
					Session::set('numero_cuenta', $maxCuenta);
					$cuenta = $this->AccountCuentas->findFirst("account_master_id='{$this->current_master}' AND cuenta = '{$this->numero_cuenta}' AND estado='A'");
					if($cuenta!=false){
						$this->tipo_venta = $cuenta->tipo_venta;
					}
				}
			}
		}

		if(!$this->numero_comanda){
			if($this->current_master){
				$comanda = $this->Account->minimum(array("comanda", "conditions" => "account_master_id='{$this->current_master}'"));
				$this->numero_comanda = (int) $comanda;
			} else {
				$this->numero_comanda = 0;
			}
		}

		if(!$this->numero_asientos){
			if($this->current_master){
				$max_asientos = $this->Account->maximum(array("asiento", "conditions" => "account_master_id='{$this->current_master}' AND estado <> 'L'"));
				$this->numero_asientos = (int) $max_asientos;
			} else {
				$this->numero_asientos = 0;
			}
		}

		if(!$this->silla){
			$this->silla = 1;
		}

		if(Browser::isMobile()==true){
			$this->setResponse('json');
			$items = array();
			$accountModifiers = array();
			foreach($this->Account->find("salon_mesas_id='{$salonMesasId}' AND estado IN ('S', 'A')") as $account){
				$menuItem = $this->MenusItems->findFirst(array($account->menus_items_id, 'columns' => 'nombre'));
				$items[] = array(
					'id' => $account->id,
					'cuenta' => $account->cuenta,
					'menus_items_id' => $account->menus_items_id,
					'nombre' => $menuItem->nombre,
					'cantidad' => $account->cantidad,
					'total' => $account->total*$account->cantidad,
				);
				foreach($this->AccountModifiers->find("account_id='{$account->id}'") as $accountModifier){
					$modifier = $this->Modifiers->findFirst($accountModifier->modifiers_id);
					$accountModifiers[] = array(
						'id' => $accountModifier->id,
						'account_id' => $accountModifier->account_id,
						'nombre' => $modifier->nombre,
						'modifiers_id' => $accountModifier->modifiers_id
					);
				}
			}
			return array(
				'cuenta' => $this->numero_cuenta,
				'items' => $items,
				'modifiers' => $accountModifiers
			);
		} else {
			$this->loadModel('SalonMesas', 'Datos', 'AccountMaster', 'Account');
			$this->loadModel('AccountCuentas', 'Salon', 'Habitacion', 'SalonTipoVenta');
			$this->loadModel('MenusItems', 'Menus', 'SalonMenusItems', 'TipoVenta', 'SalonTipoVenta');
			$this->loadModel('AccountModifiers', 'Modifiers', 'AccountDiscount');
		}

	}

	/**
	 * Obtiene el menu seleccionado
	 *
	 * @param	int $id
	 * @return	array
	 */
	public function getMenuAction($id)
	{
		$this->setResponse('json');
		$id = $this->filter($id, 'int');
		if($id>0){
			$query = new ActiveRecordJoin(array(
				'entities' => array('MenusItems', 'SalonMenusItems', 'Menus'),
				'fields' => array(
					'id' => '{#MenusItems}.id',
					'nombre_pedido' => '{#MenusItems}.nombre_pedido',
					'valor' => '{#MenusItems}.valor',
					'valor_salon' => '{#SalonMenusItems}.valor'
				),
				'conditions' =>
					"{#MenusItems}.menus_id=$id AND
					 {#SalonMenusItems}.salon_id = '" . $this->salon_id . "' AND
					 {#SalonMenusItems}.estado = 'A' AND
					 {#MenusItems}.estado = 'A'",
				'order' => array('{#MenusItems}.nombre')
			));
			$menuItems = array();
			foreach($query->getResultSet() as $menuItem){
				if(!$menuItem->valor_salon){
					$valor = Currency::number($menuItem->valor, 2);
				} else {
					$valor = Currency::number($menuItem->valor_salon, 2);
				}
				$menuItems[] = array(
					'id' => $menuItem->id,
					'nombre' => utf8_encode(utf8_decode($menuItem->nombre_pedido)),
					'valor' => $valor,
					'modifiers' => $this->MenusItemsModifiers->count("menus_items_id='{$menuItem->id}'")
				);
			}
			return $menuItems;
		} else {
			return array();
		}

	}

	/**
	 * Busca un item por su nombre
	 *
	 * @param string $id
	 */
	public function searchItemAction(){
		$this->setResponse('json');

		$text = $this->getPostParam('text', 'striptags', 'extraspaces');
		if($text){
			$menuItems = array();
			$sql = "SELECT menus_items.id,
			menus_items.nombre_pedido, if(salon_menus_items.valor is not null and salon_menus_items.valor != '',
			salon_menus_items.valor, menus_items.valor) as valor
			FROM menus_items, salon_menus_items
			WHERE
			(
				menus_items.codigo_barras = '$text' 
				OR
				menus_items.nombre LIKE '%$text%'
			)
			AND
			salon_menus_items.menus_items_id = menus_items.id AND
			salon_menus_items.salon_id = '{$this->salon_id}' AND
			salon_menus_items.estado = 'A' AND
			menus_items.estado = 'A'
			ORDER BY menus_items.nombre";
			
			foreach($this->MenusItems->findAllBySql($sql) as $menuItem){
				$nombre = str_ireplace($text, '<span class="highlight">'.$text.'</span>', $menuItem->nombre_pedido);
				$menuItems[] = array(
					'id' => $menuItem->id,
					'nombre' => $nombre,
					'valor' => Currency::number($menuItem->valor, 2),
					'modifiers' => $this->MenusItemsModifiers->count("menus_items_id='{$menuItem->id}'")
				);
			}
			return $menuItems;
		} else {
			return array();
		}
	}

	public function getModifiersAction($menuId){
		$this->setResponse('json');
		$menuId = $this->filter($menuId, 'int');
		if($menuId>0){
			$menuItem = $this->MenusItems->findFirst($menuId);
			if($menuItem==false){
				return array();
			}
			$modifiers = array(array(
				'id' => $menuItem->id,
				'nombre' => $menuItem->nombre_pedido,
				'valor' => Currency::number($menuItem->valor, 2)
			));
			foreach($this->MenusItemsModifiers->find("menus_items_id='$menuId'") as $menuItemModifier){
				$modifier = $menuItemModifier->getModifiers();
				if($modifier==false){
					continue;
				}
				$modifiers[] = array(
					'id' => $modifier->id,
					'nombre' => $modifier->nombre_pedido,
					'valor' => Currency::number($modifier->valor, 2),
				);
			}
			return $modifiers;
		} else {
			return array();
		}
	}

	/**
	 * Devuelve el precio de venta dependiendo del Ambiente
	 *
	 * @param integer $id
	 * @return numeric
	 */
	private function _getPrecioItem($id){
		$id = $this->filter($id, 'int');
		$conditions = "menus_items_id='$id' AND salon_id='{$this->salon_id}' AND estado='A'";
		$salonm = $this->SalonMenusItems->findFirst($conditions);
		if($salonm==false||$salonm->valor==0){
			$menuItem = $this->MenusItems->findFirst($id);
			if($menuItem==false){
				return 0;
			} else {
				return (double) $menuItem->valor;
			}
		} else {
			return (double) $salonm->valor;
		}
	}

	/**
	 * Busca si la cuenta donde se agregará un item es exenta ó no
	 *
	 * @param	AccountCuentas $accountCuenta
	 * @param	MenusItems $menuItem
	 * @param	boolean $singleItem
	 */
	private function _esExento($accountCuenta, $menuItem, $singleItem=true)
	{
		if ($accountCuenta->tipo_venta == 'H') {
			if ($accountCuenta->habitacion_id != -1) {

				$numeroFolio = $accountCuenta->habitacion_id;
				$salonMenuItem = $this->SalonMenusItems->findFirst("salon_id='{$this->salon_id}' AND menus_items_id='{$menuItem->id}' AND estado='A'");
				if ($salonMenuItem == false) {
					if ($singleItem == true) {
						Flash::error('El item no está activo en el ambiente');
					}
					return false;
				}

				$numeroCuenta = 0;
				$concue = $this->Concue->findFirst("numfol='$numeroFolio' AND codcar='{$salonMenuItem->conceptos_id}'");
				if ($concue != false) {
					$numeroCuenta = $concue->getNumcue();
				} else {
					$numeroCuenta = $this->Carghab->minimum("numcue", "conditions: numfol='{$numeroFolio}' AND estado='N'");
				}

				if ($numeroCuenta > 0) {
					$cuenta = $this->Carghab->findFirst("numfol='{$numeroFolio}' AND numcue='{$numeroCuenta}' AND estado='N'");
					if ($cuenta->getExento() == 'S') {
						$conrel = $this->Conrel->findFirst("codcar='{$salonMenuItem->conceptos_id}'");
						if ($conrel == false) {
							$concepto = $this->Conceptos->findFirst($salonMenuItem->conceptos_id);
							if ($concepto == false) {
								Flash::notice('La cuenta de recepción recibe cargos exentos pero el concepto asignado no existe');
							} else {
								Flash::notice('La cuenta de recepción recibe cargos exentos pero no existe el concepto "'.$concepto->descripcion.'" que sea exento');
							}
						} else {
							if ($singleItem == true) {
								Flash::notice('La cuenta de la habitación recibe solo cargos exentos');
							}
							return true;
						}
					}
				} else {
					$cliente = $this->Clientes->findFirst("cedula='{$accountCuenta->clientes_cedula}'");
					if ($cliente != false) {
						if ($cliente->exento == 'S') {
							if ($singleItem == true) {
								Flash::notice('Al cliente se le factura exento de impuestos');
							}
							return true;
						}
					}
				}
			}
		}
		return false;
	}

	/**
	 * Actualiza los items del pedido cuando se cambia el cliente
	 *
	 * @param Transaction $transaction
	 * @param AccountCuentas $accountCuenta
	 * @param boolean $sendToKitchen
	 */
	private function _updateItems(Transaction $transaction, AccountCuentas $accountCuenta, $sendToKitchen=false)
	{
		if ($accountCuenta->estado == 'A') {

			$cargosExentos = false;
			$cargosNoExentos = false;
			$this->Account->setTransaction($transaction);

			$tipoVenta = $this->TipoVenta->findById($accountCuenta->tipo_venta);
			if ($tipoVenta != false) {

				$conditions = "account_master_id='" . $accountCuenta->account_master_id . "' and cuenta=" . $accountCuenta->cuenta." AND estado IN ('S', 'A')";
				$accounts = $this->Account->findForUpdate($conditions);
				foreach ($accounts as $account) {

					$menuItem = $this->MenusItems->findFirst($account->menus_items_id);
					if ($menuItem == false) {
						Flash::error('No existe el item de menu');
						$transaction->rollback();
					}

					if ($tipoVenta->costo == 'N') {
						$valor = $this->_getPrecioItem($menuItem->id);
						if ($accountCuenta->tipo_venta == 'F') {
							if ($menuItem->porcentaje_iva > 0) {
								$account->valor = $valor / (($menuItem->porcentaje_iva + $menuItem->porcentaje_servicio) / 100 + 1);
								$account->iva = $account->valor * ($menuItem->porcentaje_iva / 100);
								$account->servicio = $account->valor * ($menuItem->porcentaje_servicio / 100);
								$account->impo = 0;
							} else {
								$account->valor = $valor / (($menuItem->porcentaje_impoconsumo + $menuItem->porcentaje_servicio) / 100 + 1);
								$account->impo = $account->valor * ($menuItem->porcentaje_impoconsumo / 100);
								$account->servicio = $account->valor * ($menuItem->porcentaje_servicio / 100);
								$account->iva = 0;
							}
						} else {
							if ($menuItem->porcentaje_iva > 0) {
								if ($this->_esExento($accountCuenta, $menuItem)) {
									$valor = $valor / (($menuItem->porcentaje_iva + $menuItem->porcentaje_servicio) / 100 + 1);
									$account->servicio = $valor - ($valor / (1 + ($menuItem->porcentaje_servicio / 100)));
									$account->valor = $valor;
									$account->iva = 0;
									$account->impo = 0;
									$cargosExentos = true;
								} else {
									$account->valor = $valor / (($menuItem->porcentaje_iva + $menuItem->porcentaje_servicio) / 100 + 1);
									$account->iva = $account->valor * ($menuItem->porcentaje_iva / 100);
									$account->servicio = $account->valor * ($menuItem->porcentaje_servicio / 100);
									$account->impo = 0;
									$cargosNoExentos = true;
								}
							} else {
								$account->valor = $valor / (($menuItem->porcentaje_impoconsumo + $menuItem->porcentaje_servicio) / 100 + 1);
								$account->impo = $account->valor * ($menuItem->porcentaje_impoconsumo / 100);
								$account->servicio = $account->valor * ($menuItem->porcentaje_servicio / 100);
								$account->iva = 0;
								$cargosNoExentos = true;
							}
						}
					} else {
						$valor = (double) $menuItem->costo;
						$account->valor = $valor;
						$account->iva = 0;
						$account->impo = 0;
						$account->servicio = 0;
					}

					$account->total = $account->valor + $account->servicio + $account->iva + $account->impo;
					if (($account->valor + $account->iva + $account->impo + $account->servicio) < $account->total) {
						$account->valor = $account->total - ($account->iva + $account->impo + $account->servicio);
					} else {
						if(($account->valor + $account->iva + $account->impo + $account->servicio) > $account->total){
							$account->valor = $account->total - ($account->iva + $account->impo + $account->servicio);
						}
					}

					if ($sendToKitchen == true) {
						$account->send_kitchen = 'N';
					}

					if ($account->save() == false) {
						foreach ($account->getMessages() as $message) {
							Flash::error('Account: '.$message->getMessage());
						}
						$transaction->rollback();
					}
				}
			}

			if ($accountCuenta->tipo_venta == 'H') {
				if ($cargosExentos == true && $cargosNoExentos == true) {
					Flash::notice('Algunos items serán cargados al folio como exentos');
				} else {
					if ($cargosExentos == true) {
						Flash::notice('Todos los items serán cargados al folio como exentos');
					}
				}
			}
		}
	}

	/**
	 * Agrega el item $id a la lista de la Cuenta
	 *
	 * @param integer $id
	 */
	public function addToListAction($id = null)
	{
		$this->setResponse('view');
		try {

			$id = $this->filter($id, 'int');
			$transaction = TransactionManager::getUserTransaction();

			$menuItem = $this->MenusItems->findFirst(array(
				"id='$id' AND estado='A'",
				'columns' => 'id,nombre,porcentaje_iva,porcentaje_impoconsumo,porcentaje_servicio,costo,tipo_costo,descontar,codigo_referencia'
			));
			if ($menuItem == false) {
				$transaction->rollback('No existe el item ó no está activo en el ambiente');
			}

			$this->Account->setTransaction($transaction);
			$this->AccountMaster->setTransaction($transaction);
			$this->AccountCuentas->setTransaction($transaction);
			$this->SalonMesas->setTransaction($transaction);

			$salonMesa = $this->SalonMesas->findFirst($this->account_id);
			if ($salonMesa == false) {
				$transaction->rollback('No existe la mesa del pedido');
			}
			$salon = $salonMesa->getSalon();

			$salonMenuItem = $this->SalonMenusItems->findFirst("salon_id='{$salon->id}' AND menus_items_id='$id' AND estado='A'");
			if ($salonMenuItem == false) {
				$transaction->rollback('El item no está activo en el ambiente');
			}

			$conditions = "salon_mesas_id='{$this->account_id}' AND estado = 'N'";
			$accountMaster = $this->AccountMaster->findFirst($conditions);
			if ($accountMaster == false) {

				$datos = $this->Datos->findFirst('columns: fecha');
				$accountMaster = new AccountMaster();
				$accountMaster->salon_mesas_id = $salonMesa->id;
				$accountMaster->salon_id = $salonMesa->salon_id;
				$accountMaster->hora = $datos->getFecha() . ' ' . Date::getCurrentTime();

				if (!Session::getData('usuarios_nombre')) {
					$this->UsuariosPos->findFirst("estado='A'");
					Session::setData('usuarios_nombre', $this->UsuariosPos->nombre);
				}

				$accountMaster->usuarios_id = Session::getData('usuarios_id');
				$accountMaster->nombre = Session::getData('usuarios_nombre');
				$accountMaster->numero_asientos = $this->numero_asientos;
				$accountMaster->estado = 'N';
				if ($accountMaster->create() == false) {
					foreach ($accountMaster->getMessages() as $message) {
						$transaction->rollback('accountMaster: '.$message->getMessage());
					}
				}

				$salonMesa->estado = 'A';
				if ($salonMesa->save() == false) {
					foreach ($salonMesa->getMessages() as $message) {
						$transaction->rollback('salonMesa: '.$message->getMessage());
					}
				}
			} else {
				if ($salonMesa->estado != 'A') {
					$salonMesa->estado = 'A';
					if ($salonMesa->save() == false) {
						foreach ($salonMesa->getMessages() as $message) {
							$transaction->rollback('salonMesa: '.$message->getMessage());
						}
					}
				}
			}

			if(!$this->numero_cuenta){
				$maxCuenta = $this->Account->maximum(array("cuenta", "conditions" => "account_master_id='{$accountMaster->id}'"));
				if ($maxCuenta) {
					$this->numero_cuenta = $maxCuenta;
					Session::set('numero_cuenta', $maxCuenta);
				} else {
					Session::set('numero_cuenta', 1);
					$this->numero_cuenta = 1;
				}
			}

			$this->current_master = $accountMaster->id;
			Session::set('current_master', $this->current_master);

			if(!$this->numero_comanda){
				$account = $this->Account->findFirst("salon_mesas_id='" . $this->account_id . "' AND estado='S'");
				if ($account) {
					if ($account->comanda > 0) {
						$this->numero_comanda = $account->comanda;
					} else {
						if (Browser::isMobile() == false) {
							$transaction->rollback('Por favor cree una comanda primero');
						} else {
							$this->numero_comanda = $this->Account->maximum(array('comanda', "conditions" => "estado<>'C'"))+1;
						}
					}
				} else {
					if (Browser::isMobile() == false) {
						$transaction->rollback('Por favor cree una comanda primero');
					} else {
						$this->numero_comanda = $this->Account->maximum(array('comanda', "conditions" => "estado<>'C'"))+1;
					}
				}
			}

			if ($this->numero_comanda) {

				$conditions = "account_master_id='{$accountMaster->id}' AND cuenta='" . $this->numero_cuenta . "'";
				$accountCuenta = $this->AccountCuentas->findFirst($conditions);
				if ($accountCuenta == false) {
					$accountCuenta = new AccountCuentas();
					$accountCuenta->setTransaction($transaction);
					$accountCuenta->account_master_id = $accountMaster->id;
					$accountCuenta->cuenta = $this->numero_cuenta;

					if ($salon->venta_a == 'H') {
						$habitacion = $this->Habitacion->findFirst("numhab='{$salonMesa->numero}'");
						if($habitacion){
							$accountCuenta->clientes_cedula = $habitacion->cedula;
							$accountCuenta->clientes_nombre = $habitacion->nombre;
							$accountCuenta->habitacion_id = $habitacion->id;
						} else {
							$accountCuenta->clientes_cedula = 0;
							$accountCuenta->clientes_nombre = 'PARTICULAR';
							$accountCuenta->habitacion_id = -1;
						}
					} else {
						$accountCuenta->clientes_cedula = 0;
						$accountCuenta->clientes_nombre = 'PARTICULAR';
						$accountCuenta->habitacion_id = -1;
					}

					$accountCuenta->prefijo = $salon->prefijo_facturacion;
					$accountCuenta->propina_fija = 'N';
					$accountCuenta->numero = 0;
					$accountCuenta->tipo_venta = $this->tipo_venta;
					$accountCuenta->estado = 'A';
					if ($accountCuenta->save() == false) {
						foreach ($accountCuenta->getMessages() as $message) {
							$transaction->rollback('AccountCuenta: '.$message->getMessage());
						}
					}
					Session::set('numero_cuenta', $this->numero_cuenta);
				} else {
					if ($accountCuenta->estado != 'A') {
						$transaction->rollback('No se puede agregar más items a esta cuenta. Debe crear otra cuenta');
						return;
					} else {
						if ($accountCuenta->tipo_venta=='P' || $accountCuenta->tipo_venta=='H') {
							if ($accountCuenta->habitacion_id != -1) {
								$habitacion = $accountCuenta->getHabitacion();
								if ($habitacion == false) {
									$transaction->rollback('Ya se le hizo check-out al folio del pedido');
								}
							}
						}
					}
				}

				if ($salon->tipo_comanda == 'A') {
					if ($this->numero_comanda > $salon->consecutivo_comanda) {
						POSRcs::disable();
						$salon->consecutivo_comanda = $this->numero_comanda;
						if ($salon->save() == false) {
							foreach ($salon->getMessages() as $message) {
								$transaction->rollback('Ambiente: '.$message->getMessage());
							}
						}
					}
				}

				$conditions = "
				salon_mesas_id=".$this->account_id." AND
				menus_items_id='$id' AND
				comanda = '".$this->numero_comanda."' AND
				cuenta = '".$this->numero_cuenta."' AND
				asiento = '".$this->silla."' AND
				estado IN ('S', 'A')";
				$account = $this->Account->findFirst($conditions);
				if ($account == false) {
					$account = new Account();
					$account->setTransaction($transaction);
					$account->salon_mesas_id = $this->account_id;
					$account->account_master_id = $accountMaster->id;
					$account->menus_items_id = $id;
					$account->cantidad_atendida = 0;
					$account->descuento = 0;
					$account->asiento = $this->silla;
					$account->comanda = $this->numero_comanda;
					$account->cuenta = $this->numero_cuenta;
					$account->tiempo = Date::getCurrentTime();
					$account->tiempo_final = '';
					$account->cantidad = 1;
				} else {
					$account->cantidad++;
				}
				$account->estado = 'S';

				$this->tipo_venta = $accountCuenta->tipo_venta;
				$tipoVenta = $this->TipoVenta->findById($this->tipo_venta);
				if ($tipoVenta->costo == 'N') {
					$precioVenta = $this->_getPrecioItem($menuItem->id);
					if ($menuItem->porcentaje_iva > 0) {
						if ($accountCuenta->tipo_venta == 'F') {
							$account->valor = $precioVenta / (($menuItem->porcentaje_iva + $menuItem->porcentaje_servicio) / 100 + 1);
							$account->iva = $precioVenta - ($precioVenta / (1 + ($menuItem->porcentaje_iva / 100)));
							$account->servicio = $precioVenta - ($precioVenta / (1 + ($menuItem->porcentaje_servicio / 100)));
						} else {
							if ($this->_esExento($accountCuenta, $menuItem)) {
								$precioVenta = $precioVenta / (($menuItem->porcentaje_iva + $menuItem->porcentaje_servicio) / 100 + 1);
								$account->valor = $precioVenta;
								$account->iva = 0;
								$account->servicio = $precioVenta - ($precioVenta / (1 + ($menuItem->porcentaje_servicio / 100)));
							} else {
								$account->valor = $precioVenta / (($menuItem->porcentaje_iva + $menuItem->porcentaje_servicio) / 100 + 1);
								$account->iva = $precioVenta - ($precioVenta / (1 + ($menuItem->porcentaje_iva / 100)));
								$account->servicio = $precioVenta - ($precioVenta / (1 + ($menuItem->porcentaje_servicio / 100)));
							}
						}
						$account->impo = 0;
					} else {
						$account->valor = $precioVenta / (($menuItem->porcentaje_impoconsumo + $menuItem->porcentaje_servicio) / 100 + 1);
						$account->impo = $precioVenta - ($precioVenta / (1 + ($menuItem->porcentaje_impoconsumo / 100)));
						$account->servicio = $precioVenta - ($precioVenta / (1 + ($menuItem->porcentaje_servicio / 100)));
						$account->iva = 0;
					}
				} else {
					$account->valor = (double) $menuItem->costo;
					$account->iva = 0;
					$account->impo = 0;
					$account->servicio = 0;
					$precioVenta = (double) $menuItem->costo;
				}

				$account->total = $precioVenta;
				if(($account->valor + $account->iva +  $account->impo + $account->servicio) < $account->total){
					$account->valor = $account->total - ($account->iva + $account->impo + $account->servicio);
				} else {
					if(($account->valor + $account->iva + $account->impo + $account->servicio) > $account->total){
						$account->valor = $account->total - ($account->iva + $account->impo + $account->servicio);
					}
				}

				$account->send_kitchen = 'N';
				if ($account->descuento == 0) {
					$conditions = "account_cuentas_id='{$accountCuenta->id}'";
					if ($this->AccountDiscount->count($conditions)) {
						$totalDiscount = 0;
						$accountDiscounts = $this->AccountDiscount->find($conditions);
						foreach ($accountDiscounts as $accountDiscount) {
							$totalDiscount += $accountDiscount->valor;
						}
						$account->descuento = $totalDiscount;
					}
				}

				if ($account->save() == false) {
					foreach($account->getMessages() as $message){
						$transaction->rollback('Account: ' . $message->getMessage());
					}
				}

				$this->last_account_id = $account->id;
				$this->last_changed_account_id = null;

				$this->_checkVentaMenor($transaction, $accountCuenta);
				$this->_explodeReceta($transaction, $account, $menuItem, $salonMenuItem);

				$transaction->commit();
			} else {
				if (Browser::isMobile() == true) {
					$transaction->rollback('Indique el número de comanda');
				}
			}
		}
		catch (TransactionFailed $e) {
			if (Browser::isMobile() == false) {
				Flash::error($e->getMessage());
			} else {
				$this->setResponse('json');
				return array(
					'status' => 'FAILED',
					'message' => $e->getMessage()
				);
			}
		}

		if (Browser::isMobile() == false) {
			$this->loadModel('SalonMesas', 'Datos', 'AccountMaster', 'Account');
			$this->loadModel('AccountCuentas', 'Salon', 'Habitacion');
			$this->loadModel('MenusItems', 'Menus', 'SalonMenusItems', 'TipoVenta', 'SalonTipoVenta');
			$this->loadModel('AccountModifiers', 'Modifiers', 'AccountDiscount');
			if (isset($account) && is_object($account)) {
				$this->renderPartial('preOrder', $account->id);
			} else {
				$this->renderPartial('preOrder');
			}
		} else {
			$this->setResponse('json');
			$itemModifiers = array();
			if ($account != false) {
				if ($account->menus_items_id > 0) {
					foreach ($this->MenusItemsModifiers->find("menus_items_id='{$account->menus_items_id}'") as $modifierItem) {
						$modifier = $this->Modifiers->findFirst($modifierItem->getModifiersId(), 'columns: nombre');
						$itemModifiers[] = array(
							'id' => $modifier->id,
							'nombre' => $modifier->nombre
						);
					}
				}
				return array(
					'status' => 'OK',
					'id' => (int) $account->id,
					'cuenta' => $account->cuenta,
					'menus_items_id' => $account->menus_items_id,
					'nombre' => $this->MenusItems->nombre,
					'cantidad' => $account->cantidad,
					'total' => $account->valor,
					'modifiers' => $itemModifiers
				);
			}
		}
	}

	/**
	 * Explota una receta y obtiene los items asociados a ella
	 *
	 * @param	Transaction $transaction
	 * @param	int $codigoReceta
	 * @param	MenusItems $menuItem
	 * @param	array $items
	 * @param	boolean $subReceta
	 * @return	boolean
	 */
	private function _getInveReceta(Transaction $transaction, $codigoReceta, $menuItem, &$items, $subReceta)
	{
		if ($codigoReceta > 0) {
			$recetap = $this->Recetap->findFirst("almacen='1' AND numero_rec='$codigoReceta'");
			if ($recetap == false) {
				$transaction->rollback('No se puede agregar el item "' . $menuItem->nombre . '" porque la receta asociada "' . $codigoReceta . '" no existe');
			} else {

				if ($recetap->estado != 'A') {
					if ($subReceta == false) {
						$transaction->rollback('No se puede agregar el item "' . $menuItem->nombre . '" porque la receta asociada "' . $recetap->nombre . '" está inactiva');
					} else {
						return false;
					}
				}

				foreach ($this->Recetal->find("almacen='1' AND numero_rec='$codigoReceta'") as $recetal) {
					if ($recetal->tipol == 'I') {
						if ($recetal->divisor > 0) {
							if (!isset($items[$recetal->item])) {
								$items[$recetal->item] = ($recetal->cantidad / $recetal->divisor);
							} else {
								$items[$recetal->item] += ($recetal->cantidad / $recetal->divisor);
							}
						} else {
							$transaction->rollback('No se puede agregar el item "' . $menuItem->nombre . '" porque el divisor en el item "' . $recetal->item . '" de la receta asociada "' . $recetap->nombre . '" es cero');
						}
					} else {
						$this->_getInveReceta($transaction, $recetal->item, $menuItem, $items, true);
					}
				}
			}
		} else {
			$transaction->rollback('No se puede agregar el item "' . $menuItem->nombre . '" porque la receta asociada "' . $codigoReceta . '" no existe');
		}
	}

	/**
	 * Graba las referencias asociadas a un item para su posterior descarga
	 *
	 * @param Transaction $transaction
	 * @param Account $account
	 * @param MenusItems $menuItem
	 * @param SalonMenusItems $salonMenuItem
	 */
	private function _explodeReceta(Transaction $transaction, Account $account, MenusItems $menuItem, SalonMenusItems $salonMenuItem)
	{
		if ($menuItem->tipo_costo != 'N') {
			$this->AccountInve->setTransaction($transaction);
			$this->AccountInve->deleteAll("account_id='{$account->id}'");
			if ($salonMenuItem->descarga == 'S') {
				if ($menuItem->tipo_costo == 'I') {

					$inve = $this->Inve->findFirst(array("item='{$menuItem->codigo_referencia}'", 'columns' => 'item,descripcion,estado'));
					if ($inve == false) {
						$transaction->rollback('No se puede agregar el item "'.$menuItem->nombre.'" porque la referencia asociada no existe');
					} else {
						if ($inve->estado != 'A') {
							$transaction->rollback('No se puede agregar el item "'.$menuItem->nombre.'" porque la referencia asociada "'.$inve->descripcion.'" está inactiva ');
						}
					}

					if (!Browser::isMobile()) {
						$saldo = $this->Saldos->findFirst(array("ano_mes=0 AND almacen='{$salonMenuItem->almacen}' AND item='{$menuItem->codigo_referencia}'", 'columns' => 'saldo,costo'));
						if ($saldo == false) {
							Flash::notice('Advertencia: No hay existencias de '.$inve->descripcion.' en el almacén '.$salonMenuItem->almacen);
						} else {
							if ($saldo->saldo <= 0) {
								Flash::notice('Advertencia: No hay existencias de '.$inve->descripcion.' en el almacén '.$salonMenuItem->almacen);
							}
						}
					}

					$accountInve = new AccountInve();
					$accountInve->setTransaction($transaction);
					$accountInve->account_id = $account->id;
					$accountInve->codigo = $menuItem->codigo_referencia;
					$accountInve->cantidad = $account->cantidad;
					$accountInve->cantidad_usuario = $account->cantidad;
					$accountInve->tipo = 'S';
					$accountInve->estado = 'P';
					if ($accountInve->save() == false) {
						foreach ($accountInve->getMessages() as $message) {
							$transaction->rollback('Account-Inve: ' . $message->getMessage());
						}
					}
				} else {
					$items = array();
					$this->_getInveReceta($transaction, $menuItem->codigo_referencia, $menuItem, $items, false);
					foreach ($items as $item => $cantidad) {
						$accountInve = new AccountInve();
						$accountInve->setTransaction($transaction);
						$accountInve->account_id = $account->id;
						$accountInve->codigo = $item;
						$accountInve->cantidad = $account->cantidad*$cantidad;
						$accountInve->cantidad_usuario = $account->cantidad*$cantidad;
						$accountInve->tipo = 'S';
						$accountInve->estado = 'P';
						if ($accountInve->save() == false) {
							foreach ($accountInve->getMessages() as $message) {
								$transaction->rollback('Account-Inve: ' . $message->getMessage());
							}
						}
					}
				}
			} else {
				if (!Browser::isMobile()) {
					Flash::notice('El ítem no es descargado de inventarios');
				}
			}
		}
	}

	/**
	 * Verifica si al pedido se le debe aplicar un tercero automático
	 *
	 * @param Transaction $transaction
	 * @param AccountCuentas $accountCuenta
	 */
	private function _checkVentaMenor(Transaction $transaction, AccountCuentas $accountCuenta)
	{
		if ($this->tipo_venta == 'F' || $this->tipo_venta == 'C' || $this->tipo_venta == 'U') {
			if ($accountCuenta->estado == 'A') {
				$ventasDefault = $this->VentasDefault->findFirst("tipo_venta_id='{$this->tipo_venta}'");
				if ($ventasDefault != false) {

					$totalPedido = 0;
					$this->Account->setTransaction($transaction);
					$conditions = "salon_mesas_id=".$this->account_id." AND cuenta='" . $this->numero_cuenta . "' AND estado IN ('S', 'A')";
					foreach($this->Account->find(array($conditions, 'columns' => 'total, descuento, cantidad')) as $account){
						if ($account->descuento > 0) {
							$totalPedido += (($account->total - $account->total * $account->descuento / 100) * $account->cantidad);
						} else {
							$totalPedido += ($account->total * $account->cantidad);
						}
					}

					$accountCuenta->setTransaction($transaction);
					if ($accountCuenta->clientes_cedula == '0') {
						if ($totalPedido <= $ventasDefault->getValorMinimo()) {
							$cliente = $this->Clientes->findFirst($ventasDefault->getCedula());
							if ($cliente == false) {
								Flash::error('Se configuró un cliente predeterminado para este tipo de pedido, pero este no existe en la base de clientes');
							} else {
								$accountCuenta->clientes_cedula = $ventasDefault->getCedula();
								$accountCuenta->clientes_nombre = $cliente->nombre;
								if ($accountCuenta->save() == false) {
									foreach ($accountCuenta->getMessages() as $message) {
										$transaction->rollback('AccountCuenta: '.$message->getMessage());
									}
								}
							}
						}
					} else {
						if ($accountCuenta->clientes_cedula == $ventasDefault->getCedula()) {
							if ($totalPedido > $ventasDefault->getValorMinimo()) {
								$accountCuenta->clientes_cedula = '0';
								$accountCuenta->clientes_nombre = 'PARTICULAR';
								if ($accountCuenta->save() == false) {
									foreach ($accountCuenta->getMessages() as $message) {
										$transaction->rollback('AccountCuenta: '.$message->getMessage());
									}
								}
							}
						}
					}
				}
			}
		}
	}

	public function cancelItemsAction()
	{
		$this->setResponse('view');
		$items = explode(',', $this->getPostParam('items'));
		$this->log($items);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Account->setTransaction($transaction);
			foreach ($items as $item) {
				$item = $this->filter($item, 'int');
				$account = $this->Account->findFirst($item);
				if (!in_array($account->estado, array('L', 'B'))) {
					$account->send_kitchen = 'N';
					$account->estado = 'C';
					if ($account->save() == false) {
						foreach ($account->getMessages() as $message) {
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				}
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){

		}
	}

	/**
	 * Refresca la Pre-Orden
	 *
	 */
	public function refreshAction($orden = null)
	{
		$this->setResponse('view');
		if ($orden) {
			$this->order = $orden;
		}
		$this->loadModel('AccountMaster', 'Account');
		$this->loadModel('AccountCuentas', 'Habitacion');
		$this->loadModel('MenusItems', 'Menus', 'SalonMenusItems', 'TipoVenta', 'SalonTipoVenta');
		$this->loadModel('AccountModifiers', 'Modifiers', 'AccountDiscount');
		$this->renderPartial("preOrder");
	}

	public function discountAction()
	{
		$this->setResponse('view');
		$this->setParamToView('discounts', $this->Discount->find(array("estado='A'", "order" => "nombre")));
	}

	/**
	 * Aplica un descuento a una cuenta
	 *
	 */
	public function applyDiscountAction($id = null)
	{
		$this->setResponse('view');

		$id = $this->filter($id, "int");
		$accountCuenta = $this->AccountCuentas->findFirst("account_master_id='{$this->current_master}' AND cuenta='{$this->numero_cuenta}'");
		if ($accountCuenta != false) {
			$accountDiscount = $this->AccountDiscount->findFirst("account_cuentas_id='{$accountCuenta->id}' AND discount_id='$id'");
			if ($accountDiscount == false) {
				$discount = $this->Discount->findFirst($id);
				if ($discount != false) {
					try {
						$transaction = TransactionManager::getUserTransaction();
						$accountDiscount = new AccountDiscount();
						$accountDiscount->setTransaction($transaction);
						$accountDiscount->account_cuentas_id = $accountCuenta->id;
						$accountDiscount->discount_id = $id;
						$accountDiscount->valor = $discount->valor;
						$accountDiscount->tipo = $discount->tipo;
						if ($accountDiscount->save() == false) {
							foreach ($accountDiscount->getMessages() as $message) {
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}

						$this->Account->setTransaction($transaction);
						$conditions = "account_master_id='{$this->current_master}' AND cuenta = '{$this->numero_cuenta}'";
						foreach ($this->Account->findForUpdate($conditions) as $account) {
							if (($account->descuento+$discount->valor) <= 100) {
								$account->descuento += $discount->valor;
							} else {
								$account->descuento = 100;
							}
							if ($account->save() == false) {
								foreach($account->getMessages() as $message){
									Flash::error($message->getMessage());
								}
								$transaction->rollback();
							}
						}
						$transaction->commit();
					}
					catch(TransactionFailed $e){

					}
				} else {
					Flash::error("No existe el descuento");
				}
			}
		} else {
			Flash::error("No existe la cuenta");
		}
	}

	public function deleteDiscountAction($id)
	{
		$this->setResponse('view');

		$id = $this->filter($id, "int");
		$accountCuenta = $this->AccountCuentas->findFirst("account_master_id='{$this->current_master}' AND cuenta='{$this->numero_cuenta}'");
		if($accountCuenta!=false){
			$accountDiscount = $this->AccountDiscount->findFirst("account_cuentas_id='{$accountCuenta->id}' AND discount_id='$id'");
			if($accountDiscount!=false){
				$discount = $this->Discount->findFirst($id);
				if($discount!=false){
					try {
						$transaction = TransactionManager::getUserTransaction();
						$accountDiscount->setTransaction($transaction);
						if($accountDiscount->delete()==false){
							$transaction->rollback();
						}
						$this->Account->setTransaction($transaction);
						$conditions = "account_master_id='{$this->current_master}' AND cuenta = {$this->numero_cuenta}";
						foreach($this->Account->findForUpdate($conditions) as $account){
							$account->descuento-= $discount->valor;
							if($account->descuento<0){
								$account->descuento = 0;
							}
							if($account->save()==false){
								foreach($account->getMessages() as $message){
									Flash::error($message->getMessage());
								}
								$transaction->rollback();
							}
						}
						$transaction->commit();
					}
					catch(TransactionFailed $e){

					}
				} else {
					Flash::error('No existe el descuento');
				}
			}
		} else {
			Flash::error('No existe la cuenta');
		}
	}

	/**
	 * Muestra/Refresca la lista de items Pendientes
	 *
	 */
	public function showItemListAction()
	{
		$this->setResponse('view');
		$this->renderPartial('list_items');
	}

	/**
	 * Reduce la cantidades pendientes solicitada
	 *
	 */
	public function downItemListAction($id=null)
	{
		$this->setResponse('view');
		$this->Account->findFirst($id);
		if ($this->Account->cantidad > $this->Account->cantidad_atendida) {
			$this->Account->cantidad--;
			if ($this->Account->cantidad <= 0 && $this->Account->cantidad_atendida == 0) {
				$this->Account->send_kitchen = 'N';
				$this->Account->estado = 'C';
				$this->AccountModifiers->delete('menus_items_id='.$this->Account->menus_items_id.
				' AND salon_mesas_id='.$this->Account->salon_mesas_id.
				' AND comanda = '.$this->Account->comanda.
				' AND cuenta = '.$this->Account->cuenta);
				$cuenta = $this->Account->cuenta;
			}
			$this->Account->save();
			$conditions = "account_master_id = '".$this->current_master."' AND cuenta = '$cuenta' and estado in ('S', 'N')";
			if (!$this->Account->count($conditions)) {
				$conditions = "account_master_id = '".$this->current_master."' AND cuenta = '$cuenta' and estado = 'A'";
				if ($this->AccountCuentas->findFirst($conditions)) {
					$this->AccountCuentas->estado = 'C';
					$this->AccountCuentas->save();
				}
			}
		}
	}

	public function manualAction(){

	}

	/**
	 * Cambia el Modificador Seleccionado en la Sesión
	 *
	 * @param integer $id
	 */
	public function changeSelectedItemAction($id = null)
	{
		$this->setResponse('view');
		$this->last_account_id = $this->filter($id, 'int');
		$this->last_changed_account_id = null;
	}

	/**
	 * Modifiers Action
	 *
	 */
	public function modifiersAction()
	{
		$this->loadModel('Account', 'MenusItems', 'Modifiers');
	}

	/**
	 * Ingredients Action
	 *
	 */
	public function ingredientsAction()
	{
		$this->loadModel('Account', 'AccountModifiers', 'MenusItems', 'AccountInve', 'Modifiers');
	}

	/**
	 * Guarda las modificaciones en los ingredientes
	 *
	 */
	public function saveIngredientsAction()
	{
		$this->redirect('order/add/'.$this->account_id);
	}

	/**
	 * Agrega un modificador a un Item de la Cuenta
	 *
	 * @param integer $id
	 */
	public function addModifierAction($id = null, $accountId=0)
	{
		$this->setResponse('view');
		$id = $this->filter($id, 'int');
		$accountId = $this->filter($accountId, 'int');
		if ($accountId>0) {
			$this->last_account_id = $accountId;
		}
		if ($this->last_account_id > 0) {
			$account = $this->Account->findFirst($this->last_account_id);
			if ($account!=false) {
				if ($this->AccountModifiers->count("account_id='{$account->id}' AND modifiers_id='$id'") == 0) {
					$modifier = $this->Modifiers->findFirst($id);
					if ($modifier != false) {

						$accountModifier = new AccountModifiers();
						$accountModifier->account_id = $account->id;
						$accountModifier->modifiers_id = $id;
						if (!$modifier->valor) {
							$accountModifier->valor = 0;
						} else {
							$accountModifier->valor = $modifier->valor;
						}

						if ($accountModifier->save() == false) {
							foreach ($accountModifier->getMessages() as $message) {
								Flash::error($message->getMessage());
							}
						} else {
							if(Browser::isMobile()==true){
								$this->setResponse('json');
								return $accountModifier->id;
							}
						}
					}
				}
			}
		}
	}


	/**
	 * Almacena una Modifier Manual
	 *
	 */
	public function saveModifierAction()
	{
		if ($this->getPostParam('texto')) {
			$modifier = new Modifiers();
			$modifier->tipo = 'U';
			$modifier->nombre = ucwords(strtolower($this->getPostParam('texto')));
			$modifier->valor = 0.0;
			$modifier->save();
		}
		$this->routeTo(array('action' => 'modifiers'));
	}

	/**
	 * Elimina un modificador de un Item
	 *
	 * @param integer $id
	 */
	public function deleteModifierAction($id=null)
	{
		$this->setResponse('view');
		$id = $this->filter($id, 'int');
		$this->AccountModifiers->delete($id);
	}

	/**
	 * Coloca la Comanda Actual a $id
	 *
	 * @param integer $id
	 */
	public function setComandaAction($id = null)
	{
		$this->setResponse('view');
		$this->numero_comanda = $this->filter($id, 'int');
	}

	/**
	 * Verifica si la cuenta $id tiene items atendidos
	 *
	 * @param	integer $id
	 * @return	integer
	 */
	public function queryCuentaAction($id = null)
	{
		$this->setResponse('json');
		$id = $this->filter($id, "int");
		if ($id > 0) {
			return $this->Account->count("salon_mesas_id='".$this->account_id."' and cuenta = '$id' AND estado = 'A'");
		}
		return 0;
	}

	/**
	 * Verifica si la comanda $id tiene items atendidos
	 *
	 * @param	integer $id
	 * @return	integer
	 */
	public function queryComandaAction($id = null)
	{
		$this->setResponse('json');
		$id = $this->filter($id, "int");
		if ($id > 0) {
			return $this->Account->count("salon_mesas_id='".$this->account_id."' and comanda = '$id' AND estado = 'A'");
		}
		return 0;
	}

	/**
	 * Coloca la Cuenta Actual a $id
	 *
	 * @param integer $id
	 */
	public function setCuentaAction($numeroCuenta=0)
	{
		$this->setResponse('json');
		$numeroCuenta = $this->filter($numeroCuenta, 'int');
		if ($numeroCuenta > 0) {
			$conditions = "account_master_id='".$this->current_master."' AND cuenta = '$numeroCuenta' AND estado IN ('A', 'B')";
			$cuenta = $this->AccountCuentas->findFirst($conditions);
			if ($cuenta != false) {
				$this->tipo_venta = $this->AccountCuentas->tipo_venta;
				$this->numero_cuenta = $numeroCuenta;
				Session::set('numero_cuenta', $numeroCuenta);
				return array(
					'status' => 'OK',
					'documento' => $cuenta->clientes_cedula,
					'tipo_venta' => $cuenta->tipo_venta,
					'cliente' => $cuenta->clientes_nombre,
					'habitacion' => $cuenta->habitacion_id,
					'nota' => $cuenta->nota,
					'estado' => $cuenta->estado
				);
			} else {
				return array(
					'status' => 'OK',
					'documento' => '',
					'tipo_venta' => '',
					'cliente' => '',
					'habitacion' => '',
					'nota' => ''
				);
			}
		}
	}

	/**
	 * Coloca la Cuenta Actual a $id
	 *
	 * @param integer $id
	 */
	public function changeOrSetCuentaAction($id = null){
		$this->setResponse('json');
		$id = $this->filter($id, 'int');
		if($id>0){
			$conditions = "account_master_id='".$this->current_master."' AND cuenta = '$id' AND estado = 'A'";
			$accountCuenta = $this->AccountCuentas->findFirst($conditions);
			if($accountCuenta){
				$this->tipo_venta = $accountCuenta->tipo_venta;
				$this->numero_cuenta = $this->filter($id, "int");
				$nombreCliente = $accountCuenta->clientes_nombre;
			} else {
				$salon = $this->Salon->findFirst($this->salon_id);
				if($salon==false){
					return 'EL AMBIENTE NO ESTÁ ACTIVO';
				} else {
					$nuevaCuenta = $this->AccountCuentas->maximum('cuenta', "conditions: account_master_id='{$this->_current_master}'");
					if(!$nuevaCuenta){
						$nuevaCuenta = 1;
					} else {
						$nuevaCuenta++;
					}
					$accountCuenta = new AccountCuentas();
					$accountCuenta->account_master_id = $this->current_master;
					$accountCuenta->cuenta = $nuevaCuenta;
					$accountCuenta->clientes_cedula = 0;
					$accountCuenta->clientes_nombre = "PARTICULAR";
					$accountCuenta->habitacion_id = -1;
					$accountCuenta->prefijo = $salon->prefijo_facturacion;
					$accountCuenta->numero = 0;
					$accountCuenta->propina_fija = 'N';
					$accountCuenta->tipo_venta = $this->tipo_venta;
					$accountCuenta->estado = 'A';
					$accountCuenta->save();
					$nombreCliente = $accountCuenta->clientes_nombre;
				}
			}
			Session::set('numero_cuenta', $id);
			$this->numero_cuenta = $id;
			return $nombreCliente;
		}
	}

	/**
	 * Devuelve el tipo de venta actual
	 *
	 */
	public function getTipoVentaAction(){
		$this->setResponse('xml');
		return $this->tipo_venta;
	}

	/**
	 * Obtiene el siguiente consecutivo de cuenta del pedido actual
	 *
	 * @return integer
	 */
	public function addCuentaAction(){
		$this->setResponse('json');
		$nuevaCuenta = $this->AccountCuentas->maximum(array("cuenta", "conditions" => "account_master_id='".$this->current_master."'"));
		if(!$nuevaCuenta){
			$nuevaCuenta = 1;
		} else {
			$nuevaCuenta++;
		}
		$this->numero_cuenta = $nuevaCuenta;
		return $nuevaCuenta;
	}

	/**
	 * Cuantas cuentas hay en la Cuenta Actual
	 */
	public function getNumeroCuentasAction(){
		$this->setResponse('json');
		return $this->Account->count(array("distinct" => "cuenta", "conditions" => "salon_mesas_id=".$this->account_id." and estado in ('S', 'A')"));
	}

	/**
	 * Cuantas comandas hay en el pedido actual
	 */
	public function getNumeroComandasAction(){
		$this->setResponse('json');
		return $this->Account->count(array("distinct" => "comanda", "conditions" => "salon_mesas_id=".$this->account_id." and estado in ('S', 'A')"));
	}

	/**
	 * Elimina una cuenta en la Cuenta Actual
	 * $id es el numero de cuenta a borrar
	 *
	 * @param integer $numeroCuenta
	 */
	public function deleteCuentaAction($numeroCuenta=0){
		$this->setResponse('view');
		$numeroCuenta = $this->filter($numeroCuenta, 'int');
		if($numeroCuenta>0){
			try {

				$transaction = TransactionManager::getUserTransaction();
				$this->Account->setTransaction($transaction);
				$this->AccountCuentas->setTransaction($transaction);

				$conditions = "salon_mesas_id=".$this->account_id." AND cuenta = '$numeroCuenta' and estado = 'A'";
				if(!$this->Account->count($conditions)){
					$accounts = $this->Account->find("salon_mesas_id=".$this->account_id." AND cuenta = '$numeroCuenta' AND estado = 'S'");
					foreach($accounts as $account){
						$account->send_kitchen = 'N';
						$account->estado = 'C';
						if($account->save()==false){
							foreach($account->getMessages() as $message){
								$transaction->rollback($message->getMessage());
							}
						}
						foreach($account->getAccountModifiers() as $accountModifier){
							if($accountModifier->save()==false){
								foreach($accountModifier->getMessages() as $message){
									$transaction->rollback($message->getMessage());
								}
							}
						}
					}
					$accountCuenta = $this->AccountCuentas->findFirst("account_master_id = '".$this->current_master."' AND cuenta = '$numeroCuenta' AND estado = 'A'");
					if($accountCuenta){
						$accountCuenta->estado = 'C';
						if($accountCuenta->save()==false){
							foreach($accountCuenta->getMessages() as $message){
								$transaction->rollback($message->getMessage());
							}
						}
					}
				}
				$transaction->commit();

				$accountCuenta = $this->AccountCuentas->findFirst("account_master_id='".$this->current_master."' AND estado='A'");
				if($accountCuenta){
					$this->numero_cuenta = $accountCuenta->cuenta;
				}

			}
			catch(TransactionFailed $e){
				Flash::error($e->getMessage());
			}
		}
	}

	/**
	 * Elimina la comanda $id de la cuenta Actual
	 *
	 * @param integer $id
	 */
	public function deleteComandaAction($id=null)
	{
		$this->setResponse('view');
		$id = $this->filter($id, 'int');
		if ($id > 0) {
			$conditions = "salon_mesas_id=".$this->account_id." and comanda = '$id' AND estado = 'A'";
			if (!$this->Account->count($conditions)) {
				try {
					$transaction = TransactionManager::getUserTransaction();
					$this->Account->setTransaction($transaction);
					$accounts = $this->Account->findForUpdate("salon_mesas_id=".$this->account_id." AND comanda = '$id' AND estado = 'S'");
					foreach ($accounts as $account) {
						$account->send_kitchen = 'N';
						$account->estado = 'C';
						if ($account->save() == false) {
							foreach($account->getMessages() as $message){
								$transaction->rollback($message->getMessage());
							}
						}
						foreach ($account->getAccountModifiers() as $accountModifier) {
							if ($accountModifier->save() == false) {
								foreach($accountModifier->getMessages() as $message){
									$transaction->rollback($message->getMessage());
								}
							}
						}
					}
					$transaction->commit();
				}
				catch(TransactionFailed $e){
					Flash::error($message->getMessage());
				}
			}
		}
	}

	/**
	 * Cancela las cantidades pendientes de un Item
	 *
	 * @param integer $id
	 */
	public function deleteItemAction($id=null)
	{
		$this->setResponse('view');
		$id = $this->filter($id, "int");
		$account = $this->Account->find($id);
		if($account!=false){
			$account->send_kitchen = 'N';
			if($account->cantidad_atendida==0){
				$account->estado = 'C';
			} else {
				$account->cantidad = $account->cantidad_atendida;
			}
			$account->save();
			if($account->estado=='C'){
				$this->AccountModifiers->delete("account_id='{$account->id}'");
			}
		}
	}

	public function setPropinaAction(){
		$this->setResponse('view');
		$propina = $this->getPostParam('valor', 'double');
		$accountCuenta = $this->AccountCuentas->findFirst("account_master_id='{$this->current_master}' AND cuenta='{$this->numero_cuenta}'");
		if($accountCuenta!=false){
			if($accountCuenta->estado=='A'||$accountCuenta->estado=='S'){
				$propina = LocaleMath::round($propina, 0);
				$accountCuenta->propina_fija = 'S';
				$accountCuenta->propina = $propina;
				$accountCuenta->save();
			}
		}
	}

	/**
	 * Cambia el Precio predeterminado para un item de la cuenta
	 *
	 * @param integer $id
	 */
	public function changePriceAction($id = null, $price)
	{
		$this->setResponse('view');
		$id = $this->filter($id, "int");
		$price = $this->filter($price, "double");
		if ($price >= 0) {
			$account = $this->Account->findFirst($id);
			if ($account) {
				$menuItem = $this->MenusItems->findFirst($account->menus_items_id);
				if ($menuItem->cambio_precio == 'S' && ($account->estado == "A" || $account->estado == "S")) {
					$account->valor = $price;

					if ($account->descuento > 0) {
						$account->valor *= $account->descuento;
					}

					if ($menuItem->porcentaje_iva > 0) {
						$account->valor = $account->valor / (($menuItem->porcentaje_iva + $menuItem->porcentaje_servicio) / 100 + 1);
						$account->iva = $price - ($price / (1 + ($menuItem->porcentaje_iva / 100)));
						$account->servicio = $price - ($price/(1 + ($menuItem->porcentaje_servicio / 100)));
						$account->impo = 0;
					} else {
						$account->valor = $account->valor / (($menuItem->porcentaje_impoconsumo + $menuItem->porcentaje_servicio) / 100 + 1);
						$account->impo = $price - ($price / (1 + ($menuItem->porcentaje_impoconsumo / 100)));
						$account->servicio = $price - ($price/(1 + ($menuItem->porcentaje_servicio / 100)));
						$account->iva = 0;
					}

					$account->total = $account->valor + $account->servicio + $account->iva + $account->impo;
					if ($account->save() == false) {
						foreach ($account->getMessages as $message) {
							Flash::error($message->getMessage());
						}
					}
				}
			}
		}
	}

	/**
	 * Cambia la Comanda para un item de la cuenta
	 *
	 * @param integer $id
	 */
	public function changeItemComandaAction($accountId=0, $newComanda=0)
	{
		$this->setResponse('view');
		$accountId = $this->filter($accountId, 'int');
		$newComanda = $this->filter($newComanda, 'int');
		if ($accountId > 0 && $newComanda > 0) {
			$account = $this->Account->findFirst($accountId);
			if ($account != false) {
				if ($account->estado == 'S' || $account->estado == 'A') {

					$conditions = "account_master_id='{$account->account_master_id}' AND
					menus_items_id='{$account->menus_items_id}' AND
					comanda='$newComanda' AND
					cuenta='{$account->cuenta}' AND
					asiento='{$account->asiento}' AND
					estado IN ('S', 'A')";
					$otherAccount = $this->Account->findFirst($conditions);
					if ($otherAccount != false) {
						$otherAccount->cantidad += $this->Account->cantidad;
						$otherAccount->cantidad_atendida += $this->Account->cantidad_atendida;
						$otherAccount->save();
						$account->estado = 'C';
					} else {
						$account->comanda = $newComanda;
					}
					if ($account->save() != false) {
						$this->last_changed_account_id = $account->id;
					}
				}
			}
		}
	}

	/**
	 * Cambia la Cuenta para un item de la cuenta
	 *
	 * @param integer $id
	 */
	public function changeItemCuentaAction($accountId=0, $newCuenta=0)
	{
		try {
			$this->setResponse('view');
			$accountId = $this->filter($accountId, 'int');
			$newCuenta = $this->filter($newCuenta, 'int');
			if ($accountId > 0 && $newCuenta > 0) {
				$transaction = TransactionManager::getUserTransaction();
				$account = $this->Account->findFirst($accountId);
				$account->setTransaction($transaction);
				if ($account != false) {
					$conditions = "account_master_id='{$this->current_master}' AND cuenta='$newCuenta'";
					$accountCuenta = $this->AccountCuentas->findFirst($conditions);
					if ($accountCuenta == false) {
						$salon = $this->Salon->findFirst($this->salon_id);
						if ($salon == false) {
							Flash::error('El ambiente no está activo');
							$transaction->rollback();
						} else {
							$accountCuenta = new AccountCuentas();
							$accountCuenta->setTransaction($transaction);
							$accountCuenta->account_master_id = $this->current_master;
							$accountCuenta->cuenta = $newCuenta;
							$accountCuenta->clientes_cedula = 0;
							$accountCuenta->clientes_nombre = "PARTICULAR";
							$accountCuenta->habitacion_id = -1;
							$accountCuenta->prefijo = $salon->prefijo_facturacion;
							$accountCuenta->numero = 0;
							$accountCuenta->tipo_venta = $this->tipo_venta;
							$accountCuenta->propina_fija = 'N';
							$accountCuenta->estado = 'A';
							if ($accountCuenta->save() == false) {
								foreach ($accountCuenta->getMessages() as $message) {
									Flash::error($message->getMessage());
								}
								$transaction->rollback();
							}
						}
					}
					if ($account->estado == 'S' || $account->estado == 'A') {
						$conditions = "account_master_id='{$account->account_master_id}' AND
						menus_items_id='{$account->menus_items_id}' AND
						cuenta='$newCuenta' AND
						comanda='{$account->comanda}' AND
						asiento='{$account->asiento}' AND
						estado IN ('S', 'A')";
						$otherAccount = $this->Account->findFirst($conditions);
						if ($otherAccount != false) {
							$otherAccount->setTransaction($transaction);
							$otherAccount->cantidad += $account->cantidad;
							$otherAccount->cantidad_atendida += $account->cantidad_atendida;
							if ($otherAccount->save() == false) {
								foreach ($account->getMessages() as $message) {
									Flash::error($message->getMessage());
								}
								$transaction->rollback();
							}
							$account->estado = 'C';
						} else {
							$account->cuenta = $newCuenta;
						}
						if($account->save()==false){
							foreach($account->getMessages() as $message){
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
						$this->last_changed_account_id = $account->id;
						$this->_updateItems($transaction, $accountCuenta);
					}
					$transaction->commit();
				}
			}
		}
		catch(TransactionFailed $e){

		}
	}

	public function cancelOrderAction(){

	}

	public function customerNameAction(){
		$this->setResponse('view');
		if($this->current_master>0){
			$conditions = "account_master_id='{$this->current_master}' and cuenta = '".$this->numero_cuenta."'";
			$accountCuenta = $this->AccountCuentas->findFirst($conditions);
			$this->setParamToView('accountCuenta', $accountCuenta);
		}
		$this->loadModel('Habitacion');
	}

	/**
	 * Busca un cliente en la base de datos de hotel por su nombre
	 *
	 */
	public function queryCustomersAction(){
		$this->setResponse('view');
		echo '<ul>';
		$controllerRequest = ControllerRequest::getInstance();
		$nombre = $controllerRequest->getParamPost('nombre', 'extraspaces');
		if($nombre){
			$clientes = $this->Clientes->find("nombre like '%".preg_replace("/[ ]+/", "%", $nombre)."%'", "limit: 10", "order: nombre");
			foreach($clientes as $cliente){
				echo "<li id='", $cliente->cedula, "'>", $cliente->nombre, "</li>\n";
			}
		}
		echo '</ul>';
	}

	/**
	 * Hace una consulta de los socios actualmente hospedados
	 *
	 */
	public function querySociosAction()
	{

		$this->setResponse('view');
		$controllerRequest = ControllerRequest::getInstance();
		$numeroAccion = $controllerRequest->getParamPost('numeroAccion', 'extraspaces');
		$tipoVenta = $controllerRequest->getParamPost('tipoVenta', 'onechar');

		$socios = array();
		if ($numeroAccion) {
			echo '<table width="70%" align="center" class="sociosTab" cellspacing="0">
			<thead>
				<tr>
					<th>Número Acción</th>
					<th>Habitación/Folio</th>
					<th>Identificación</th>
					<th>Nombre</th>
					<th>Estado</th>
					<th></th>
				</tr>';
			if ($tipoVenta == 'H' || $tipoVenta == 'P' || $tipoVenta == 'U') {
				if (strlen($numeroAccion)==3) {
					if (is_numeric($numeroAccion)) {
						$socios = $this->SociosActual->find("numhab='$numeroAccion'", "limit: 5");
					} else {
						$socios = $this->SociosActual->find("nombre LIKE '%$numeroAccion%'", "limit: 5", "order: nombre");
					}
				} else {
					$socios = $this->SociosActual->find("accion LIKE '$numeroAccion%' OR nombre LIKE '%$numeroAccion%'", "limit: 5", "order: nombre");
				}
			} else {
				if ($tipoVenta == 'S') {
				   $socios = $this->SociosActual->find("accion LIKE '%$numeroAccion%'", "limit: 5");
				}
			}

			foreach ($socios as  $socio) {
				if ($socio->numhab == 0) {
					$socio->numhab = 'F. DIRECTA';
				}
				echo '<tr>
					<td>', $socio->accion, '</td>
					<td>', $socio->numhab, '</td>
					<td>', $socio->nombre, '</td>
					<td>', Tag::button(array("Escoger", "class" => "okButton aplSubmit", "title" => $socio->numfol)), '
				</tr>';
			}

			echo '</table>';

		}
	}

	public function saveSocioAction()
	{
		$config = CoreConfig::readFromActiveApplication('app.ini', 'ini');
		$this->setResponse('view');
		$folio = $this->getPostParam('folio', 'int');
		$conditions = "account_master_id='" . $this->current_master . "' AND cuenta = '" . $this->numero_cuenta . "' AND estado = 'A'";

		$accountCuenta = $this->AccountCuentas->findFirst($conditions);
		if ($accountCuenta) {

			$habitacion = $this->Habitacion->findFirst($folio);
			if ($habitacion != false) {

				$accountCuenta->habitacion_id = $folio;

				$accountCuenta->clientes_cedula = $habitacion->cedula;

				$accountCuenta->clientes_nombre = $habitacion->nombre;

				if ($habitacion->numhab != 0) {

					$nota = $habitacion->nota ? $habitacion->nota."<br>" : "";

					$accountCuenta->nota = $nota.

					"<strong>Adultos:</strong> " . $habitacion->numero_adultos . " " .

					"<strong>Niños:</strong> " . $habitacion->numero_ninos;

				} else {
					$accountCuenta->nota = $habitacion->nota;
				}

				if ($accountCuenta->save() == true) {
					Flash::success("Se definió el cliente correctamente. Presione cerrar");
					$this->renderJavascript("pedido.closeRefresh();");
				} else {
					foreach ($accountCuenta->getMessages() as $message) {
						Flash::error($message->getMessage());
					}
				}
			} else {
				Flash::error("No existe el socios con id ($sociosId)");
			}
		} else {
			Flash::error("No existe la cuenta");
		}
	}

	private function _filterId($id)
	{
		$id = trim($id);
		if ($id !== '0') {
			$id = strtoupper($id);
			$filteredId = preg_replace('/[^a-zA-Z0-9]/', '', $id);
			$length = strlen($filteredId);
			if ($length <= 3) {
				Flash::error('El documento "' . $id . '" no es válido, por favor revise (0)');
				return '';
			}
			if (preg_match('/^[A-Z]+$/', $filteredId)) {
				Flash::error('El documento "' . $id . '" no es válido, por favor revise (1)');
				return '';
			}
			if ($filteredId != $id) {
				Flash::notice('Algunos carácteres fueron eliminados del documento "'.$id."' por no ser válidos. El nuevo documento es: $filteredId");
			}
			if($filteredId!='222222222'&&$filteredId!='444444444'){
				$count = 1;
				$diff = false;
				$chr = array();
				$last_counts = 0;
				$last = substr($filteredId, 0, 1);
				for($i=0;$i<$length;$i++){
					$ch = substr($filteredId, $i, 1);
					if($ch==$last){
						$count++;
						if($count==6){
							Flash::error('El documento "'.$filteredId.'" no es válido, por favor revise (2)');
							return '';
						}
					} else {
						if($count>2){
							$last_counts++;
						}
						$diff = true;
						$count = 0;
						$last = $ch;
					}
					$chr[$i] = ord($ch);
				}

				$numberInc = 0;
				$numberDec = 0;
				for($i=0;$i<$length;$i++){
					for($j=$i;$j<$length;$j++){
						if($j==$i){
							$numberInc = 1;
							$numberDec = 0;
							$ord = $chr[$j];
							$oldOrd = $chr[$j];
						} else {
							if($oldOrd+1==$chr[$j]){
								$numberInc++;
							} else {
								if($oldOrd-1==$chr[$j]){
									$numberDec++;
								}
							}
							if($numberInc==5||$numberDec==5){
								Flash::error('El documento "'.$filteredId.'" no es válido, por favor revise (7)');
								return '';
							}
							$oldOrd = $chr[$j];
						}
					}
				}


				if($count>=2){
					$last_counts++;
				}
				if($last_counts>1){
					Flash::error('El documento "'.$filteredId.'" no es válido, por favor revise (3)');
					return '';
				}
				if($diff==false){
					Flash::error('El documento "'.$filteredId.'" no es válido, por favor revise (4)');
					return '';
				}

				if($length<10){
					$two_count = array();
					for($i=1;$i<$length-1;$i++){
						if($i==1){
							$ch = substr($filteredId, $i-1, 2);
							if(isset($two_count[$ch])){
								$two_count[$ch]++;
							} else {
								$two_count[$ch] = 1;
							}
						} else {
							if($i==($length-2)){
								$ch = substr($filteredId, $i, 2);
								if(isset($two_count[$ch])){
									$two_count[$ch]++;
								} else {
									$two_count[$ch] = 1;
								}
								$ch = substr($filteredId, $i-1, 2);
								if(isset($two_count[$ch])){
									$two_count[$ch]++;
								} else {
									$two_count[$ch] = 1;
								}
							} else {
								$ch = substr($filteredId, $i-1, 2);
								if(isset($two_count[$ch])){
									$two_count[$ch]++;
								} else {
									$two_count[$ch] = 1;
								}
							}
						}
					}
					foreach($two_count as $count){
						if($count>5){
							Flash::error('El documento "'.$filteredId.'" no es válido, por favor revise (5) ');
							return '';
						}
					}
				}

				$first = substr($filteredId, 0, 1);
				if(preg_match('/^'.$first.'{6,}/', $filteredId)){
					Flash::error('El documento "'.$filteredId.'" no es válido, por favor revise (5)');
					return '';
				}

				$last = substr($filteredId, 0, 1);
				if(preg_match('/'.$last.'{5,}$/', $filteredId)){
					Flash::error('El documento "'.$filteredId.'" no es válido, por favor revise (5)');
					return '';
				}



				return $filteredId;
			} else {
				return $filteredId;
			}
		} else {
			return '0';
		}

	}

	public function saveClientAction(){

		$this->setResponse('view');
		try {

			$transaction = TransactionManager::getUserTransaction();
			$this->AccountCuentas->setTransaction($transaction);

			$tipo = $this->getPostParam('tipo');
			if($tipo=='P'){

				$cedula = $this->getPostParam('documento_cliente', 'alpha');
				if(!$cedula){
					Flash::error('Debe digitar el documento del Cliente');
					return;
				}

				$cedula = $this->_filterId($cedula);
				if(!$cedula){
					return;
				}

				$nombre = $this->getPostParam('nombre', 'extraspaces', 'striptags');
				if($nombre=='NO EXISTE EL CLIENTE EN LA BASE DE DATOS'){
					Flash::error('Nombre de cliente invalido');
					return;
				}
				$existeCliente = $this->Clientes->count("cedula='$cedula'");
				if($existeCliente==0){
					$cliente = new Clientes();
					$cliente->setTransaction($transaction);
					$cliente->tipdoc = 1;
					$cliente->cedula = $cedula;
					$cliente->sexo = 'M';
					$cliente->nombre = $nombre;
					$cliente->locnac = 135;
					$cliente->feccre = Date::getCurrentDate();
					$cliente->credito = 'N';
					$cliente->cuepla = 'N';
					$cliente->exento = 'N';
					$cliente->clides = 'N';
					$cliente->tipinf = 'N';
					$cliente->estado = 'N';
					$cliente->estsis = 'A';
					if($cliente->save()==true){
						Flash::success('Se creó el nuevo cliente correctamente');
					} else {
						foreach($cliente->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						Flash::error('Ocurrió un error al crear el cliente');
					}
				}

				$conditions = "account_master_id='".$this->current_master."' AND cuenta = '".$this->numero_cuenta."' AND estado = 'A'";
				$accountCuenta = $this->AccountCuentas->findFirst($conditions);
				if($accountCuenta){
					if($accountCuenta->tipo_venta=='U'||$accountCuenta->tipo_venta=='C'||$accountCuenta->tipo_venta=='F'){
						$accountCuenta->clientes_cedula = $cedula;
						$accountCuenta->clientes_nombre = $nombre;
						$accountCuenta->habitacion_id = -1;
						$accountCuenta->nota = '';
						if($accountCuenta->save()==true){
							$this->_updateItems($transaction, $accountCuenta);
							Flash::success('Se definió el cliente correctamente. Presione cerrar');
							$this->renderJavascript("pedido.closeRefresh();");
						} else {
							foreach($accountCuenta->getMessages() as $message){
								Flash::error($message->getMessage());
							}
						}
					} else {
						Flash::error('Este tipo de pedido no acepta este cliente');
					}
				}
			} else {

				$numeroHabitacion = $this->getPostParam('numHabitacion', 'alpha');
				if($numeroHabitacion==0){
					$habitacionId = $this->getPostParam('habitacion', 'alpha');
					$habitacion = $this->Habitacion->findFirst($habitacionId);
				} else {
					$habitacion = $this->Habitacion->findByNumhab($numeroHabitacion);
				}

				if($habitacion==false){
					Flash::error("No existe la habitación '$numeroHabitacion' ó no está ocupada");
					return;
				} else {
					$habitacionId = $habitacion->id;
				}

				$conditions = "account_master_id='".$this->current_master."' AND cuenta='".$this->numero_cuenta."'";
				$accountCuenta = $this->AccountCuentas->findFirst($conditions);
				if($accountCuenta){
					if($accountCuenta->estado=='A'){
						if($accountCuenta->tipo_venta=='H'||$accountCuenta->tipo_venta=='P'){
							$accountCuenta->habitacion_id = $habitacion->id;
							$accountCuenta->clientes_cedula = $habitacion->cedula;
							$accountCuenta->clientes_nombre = $habitacion->nombre;
							if($habitacion->numhab){
								$nota = $habitacion->nota ? $habitacion->nota.'<br>' : '';
								$accountCuenta->nota = $nota.
								'<strong>Adultos:</strong> '.$habitacion->numero_adultos.' '.
								'<strong>Niños:</strong> '.$habitacion->numero_ninos;
							} else {
								$accountCuenta->nota = $habitacion->nota;
							}
							if($accountCuenta->save()==true){
								$this->_updateItems($transaction, $accountCuenta);
								Flash::success("Se definió el cliente correctamente. Presione cerrar");
								$this->renderJavascript("pedido.closeRefresh();");
							} else {
								foreach($accountCuenta->getMessages() as $message){
									Flash::error($message->getMessage());
								}
							}
						} else {
							Flash::error('Este tipo de pedido no acepta este cliente');
						}
					} else {
						Flash::error("No se puede definir el cliente en la cuenta");
					}
				} else {
					Flash::error("No existe la cuenta");
				}
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){

		}
	}

	public function setClienteAction($id=null){
		$this->setResponse('json');
		$id = $this->filter($id, 'alpha');
		if($id!=''){
			$conditions = "account_master_id='".$this->current_master."' AND cuenta = '".$this->numero_cuenta."' AND estado = 'A'";
			$accountCuenta = $this->AccountCuentas->findFirst($conditions);
			if($accountCuenta!=false){
				$habitacion = $this->Habitacion->findFirst($id);
				if($habitacion!=false){
					$accountCuenta->habitacion_id = $id;
					$accountCuenta->clientes_cedula = $habitacion->cedula;
					$accountCuenta->clientes_nombre = $habitacion->nombre;
					if($habitacion->numhab){
						$nota = $habitacion->nota ? $habitacion->nota."<br>" : "";
						$accountCuenta->nota = $nota.
						"<strong>Adultos:</strong> ".$habitacion->numero_adultos." ".
						"<strong>Niños:</strong> ".$habitacion->numero_ninos;
					} else {
						$accountCuenta->nota = $habitacion->nota;
					}
					if($accountCuenta->save()==true){
						return "";
					} else {
						foreach($accountCuenta->getMessages() as $message){
							return $message->getMessage();
						}
					}
				} else {
					return 'No existe el folio';
				}
			} else {
				return "No existe la cuenta";
			}
		} else {
			return 'Debe seleccionar un huesped ó una facturación directa '.$numeroHabitacion;
		}
	}

	/**
	 * Verifica si ya existe una comanda
	 *
	 * @param integer $id
	 * @return string
	 */
	public function existeComandaAction($id = null){
		$this->setResponse('json');
		$id = $this->filter($id, "int");
		return $this->Account->count("comanda='$id' AND estado <> 'C'");
	}

	/**
	 * Notes Action
	 *
	 */
	public function notesAction($id = null){
		$id = $this->filter($id, 'int');
		if($id>0){
			$this->last_account_id = $id;
		} else {
			$id = $this->last_account_id;
		}
		$account = $this->Account->findFirst($id);
		if($account==false){
			Flash::notice('Selecione un item para agregarle una nota');
			return $this->routeTo(array(
				'action' => 'add',
				'id' => $this->account_id
			));
		} else {
			$conditions = "account_master_id='".$this->current_master."' AND cuenta = '$this->numero_cuenta'";
			$cuenta = $this->AccountCuentas->findFirst($conditions);
			if($cuenta!=false){
				if($cuenta->estado=='L'||$cuenta->estado=='B'){
					Flash::notice('La cuenta no se puede modificar porque ya se generó la orden/factura');
					return $this->routeTo(array(
						'action' => 'add',
						'id' => $this->account_id
					));
				}
			}
			$menuItem = $this->MenusItems->findFirst($account->menus_items_id);
			$this->setParamToView('menuItem', $menuItem);
			$this->setParamToView('account', $account);
		}
		$this->loadModel('Account');
	}

	/**
	 * Save a Note
	 *
	 */
	public function saveNotesAction()
	{
		$texto = $this->getRequestParam('texto');
		$account = $this->Account->find($this->last_account_id);
		if ($account) {
			$texto = strip_tags($texto);
			$texto = htmlentities($texto);
			$account->note = i18n::strtoupper($texto);
			$account->save();
		}
		return $this->redirect('order/add/'.$this->account_id);
	}

	/**
	 * Obtiene el nombre del cliente en la cuenta Activa
	 *
	 */
	public function getCustomerNameAction()
	{
		$this->setResponse('xml');
		$conditions = "account_master_id='".$this->current_master."' and cuenta='".$this->numero_cuenta."'";
		$this->AccountCuentas->findFirst($conditions);
		return $this->AccountCuentas->clientes_nombre ? $this->AccountCuentas->clientes_nombre : "PARTICULAR";
	}

	/**
	 * Obtiene el nombre del cliente en la cuenta Activa
	 *
	 */
	public function getCustomerNoteAction(){
		$this->setResponse('xml');
		$conditions = "account_master_id='".$this->current_master."' and cuenta='".$this->numero_cuenta."'";
		$this->AccountCuentas->findFirst($conditions);
		return $this->AccountCuentas->nota;
	}

	/**
	 * Obtiene el documento del cliente en la cuenta Activa
	 *
	 */
	public function getCustomerDocumentAction(){
		$this->setResponse('xml');
		$conditions = "account_master_id=".$this->current_master." AND cuenta=".$this->numero_cuenta;
		$this->AccountCuentas->findFirst($conditions);
		return $this->AccountCuentas->clientes_cedula ? $this->AccountCuentas->clientes_cedula : "0";
	}

	/**
	 * Obtiene el numero de habitacion del cliente en la cuenta Activa
	 *
	 */
	public function getCustomerHabitacionAction(){
		$this->setResponse('xml');
		$this->AccountCuentas->findFirst("account_master_id=".$this->current_master." AND cuenta=".$this->numero_cuenta);
		return $this->AccountCuentas->habitacion_id ? $this->AccountCuentas->habitacion_id : "0";
	}

	/**
	 * Establece el Numero de Asientos
	 *
	 * @param integer $number
	 */
	public function setNumberAsientosAction($number){
		$this->setResponse('view');
		$number = $this->filter($number, 'int');
		if($number>0){
			$this->numero_asientos = $number;
			$accountMaster = $this->AccountMaster->findFirst($this->current_master);
			if($accountMaster!==false){
				$accountMaster->numero_asientos = $number;
				if($accountMaster->save()==false){
					foreach($accountMaster->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}
			}
			$this->silla = 1;
		}
	}

	/**
	 * Establece el numero de Asiento Activo
	 *
	 * @param integer $number
	 */
	public function setActiveAsientoAction($number){
		$this->setResponse('view');
		$number = $this->filter($number, "int");
		if($this->numero_asientos<$number){
			$this->numero_asientos = $number;
		}
		$this->silla = $number;
	}

	public function changeTipoVentaAction($tipo){
		$this->setResponse('view');
		$tipoVenta = $this->filter($tipo, 'onechar');
		$currentMaster = $this->current_master;
		$numeroCuenta = $this->numero_cuenta;
		try {
			$accountCuenta = $this->AccountCuentas->findFirst("account_master_id='".$currentMaster."' and cuenta=".$numeroCuenta);
			if($accountCuenta){
				if($accountCuenta->estado=='A'){
					$transaction = TransactionManager::getUserTransaction();
					$accountCuenta->setTransaction($transaction);
					if(($tipo=='F'||$tipo=='U'||$tipo=='C')&&($accountCuenta->tipo_venta=='H'||$accountCuenta->tipo_venta=='P')){
						$accountCuenta->habitacion_id = '-1';
						$accountCuenta->clientes_cedula = '0';
						$accountCuenta->clientes_nombre = 'PARTICULAR';
					}
					if(($tipo=='H'||$tipo=='P')&&($accountCuenta->tipo_venta=='F'||$accountCuenta->tipo_venta=='U'||$accountCuenta->tipo_venta=='C')){
						$accountCuenta->habitacion_id = '-1';
						$accountCuenta->clientes_cedula = '0';
						$accountCuenta->clientes_nombre = 'PARTICULAR';
					}
					$accountCuenta->tipo_venta = $tipoVenta;
					if($accountCuenta->save()==false){
						foreach($accountCuenta->getMessages() as $message){
							Flash::error($message->getMessage());
							$transaction->rollback();
						}
					}
					$this->tipo_venta = $tipoVenta;
					$this->_updateItems($transaction, $accountCuenta);
					$this->_checkVentaMenor($transaction, $accountCuenta);
					$transaction->commit();
				} else {
					Flash::error('No se puede cambiar el tipo de pedido');
				}
			}
		}
		catch(TransactionFailed $e){

		}
	}

	public function changeQuantityAction($accountId, $cantidad){
		try {
			$this->setResponse('view');
			$cantidad = $this->filter($cantidad, 'int');
			$accountId = $this->filter($accountId, 'int');
			if($cantidad>=0&&$accountId>0){
				$transaction = TransactionManager::getUserTransaction();
				$account = $this->Account->findFirst($accountId);
				if($account){

					$menuItem = $this->MenusItems->findFirst($account->menus_items_id);
					if($menuItem==false){
						$transaction->rollback('No existe el item ó no está activo en el ambiente');
					}

					$salonMenuItem = $this->SalonMenusItems->findFirst("salon_id='{$this->salon_id}' AND menus_items_id='{$account->menus_items_id}' AND estado='A'");
					if($salonMenuItem==false){
						$transaction->rollback('El item no está activo en el ambiente');
					}

					$account->setTransaction($transaction);
					$account->cantidad = $cantidad;
					$account->send_kitchen = 'N';
					if($account->save()==false){
						foreach($account->getMessages() as $message){
							$transaction->rollback($message->getMessage());
						}
					}
					if($this->tipo_venta=='F'||$this->tipo_venta=='C'||$this->tipo_venta=='U'){
						$accountCuenta = $this->AccountCuentas->findFirst("account_master_id='{$account->account_master_id}' AND cuenta='{$account->cuenta}'");
						if($accountCuenta!=false){
							$accountCuenta->setTransaction($transaction);
							$this->_checkVentaMenor($transaction, $accountCuenta);
						}
					}

					$this->_explodeReceta($transaction, $account, $menuItem, $salonMenuItem);

					$this->last_account_id = $account->id;

					$transaction->commit();
				}
			}
		}
		catch(TransactionFailed $e){
			Flash::error($e->getMessage());
		}
	}

	public function changeDiscountAction($accountId, $descuento){
		try {
			$this->setResponse('view');
			$descuento = $this->filter($descuento, 'double');
			$accountId = $this->filter($accountId, 'int');
			if($descuento>=0&&$accountId>0){
				$transaction = TransactionManager::getUserTransaction();
				$account = $this->Account->findFirst($accountId);
				if($account){
					$account->setTransaction($transaction);
					$account->descuento = $descuento;
					if($account->save()==false){
						foreach($account->getMessages() as $message){
							$transaction->rollback($message->getMessage());
						}
					}
					if($this->tipo_venta=='F'||$this->tipo_venta=='C'||$this->tipo_venta=='U'){
						$accountCuenta = $this->AccountCuentas->findFirst("account_master_id='{$account->account_master_id}' AND cuenta='{$account->cuenta}'");
						if($accountCuenta!=false){
							$accountCuenta->setTransaction($transaction);
							$this->_checkVentaMenor($transaction, $accountCuenta);
						}
					}
					$this->last_account_id = $accountId;
					$transaction->commit();
				}
			}
		}
		catch(TransactionFailed $e){
			Flash::error($e->getMessage());
		}
	}

	public function getHuespedInfoAction($id){
		$this->setResponse("view");
		$this->setParamToView('numfol', $this->filter($id, 'int'));
		$this->loadModel('Acompanantes', 'Planes');
	}

	/**
	 * Genera la tira de orden de producción del pedido actual en pantalla
	 *
	 */
	public function sendToKitchenAction()
	{
		$this->setResponse("view");
		try {

			$transaction = TransactionManager::getUserTransaction();

			$this->Account->setTransaction($transaction);
			if ($this->Account->count("send_kitchen='N'")) {

				$datos = $this->Datos->findFirst();
				if ($datos->print_type) {
					$printingType = $datos->print_type;
				} else {
					$printingType = 'C';
				}

				$imp = array();
				$replaceWords = array(' DE ', ' CON ', ' EL ', ' LOS ', ' A ', ' LA ');
				$accountMaster = $this->AccountMaster->findFirst($this->current_master);
				if ($accountMaster == false) {
					$transaction->rollback('No existe el pedido');
				}

				$usuario = $this->UsuariosPos->findFirst($accountMaster->usuarios_id);
				if ($usuario == false) {
					$transaction->rollback('No existe el usuario que tomó el pedido');
				}

				$comandas = array();
				$accounts = $this->Account->findForUpdate("account_master_id='{$this->current_master}' AND send_kitchen='N'");
				foreach ($accounts as $account) {
					$comandas[$account->comanda] = true;
				}

				$comandas = array_keys($comandas);
				foreach ($accounts as $account) {

					$clienteNombre = '';
					$accountCuenta = $this->AccountCuentas->findFirst("account_master_id={$account->account_master_id} AND estado = 'A'");
					if($accountCuenta){
						$clienteNombre = "Cliente: " . $accountCuenta->clientes_nombre . "\n";
					}

					$salonMesa = $account->getSalonMesas();
					if ($salonMesa==false) {
						continue;
					}

					$salon = $accountMaster->getSalon();
					if ($salon==false) {
						continue;
					}

					$menuItem = $account->getMenusItems();
					if ($menuItem==false) {
						continue;
					}

					$salonMenusItems = $this->SalonMenusItems->findFirst("salon_id='{$salon->id}' AND menus_items_id='{$account->menus_items_id}'");
					if ($salonMenusItems==false) {
						continue;
					}

					if ($account->estado == 'S') {
						$estado = 'SIN ATENDER';
					} else {
						if ($account->estado == 'A') {
							$estado = 'ATENDIDA';
						} else {
							$estado = 'CANCELADA';
						}
					}

					$cantidadSinEnviar = $account->cantidad-$account->cantidad_cocina;
					if ($printingType == 'C') {
						$nombre = str_ireplace($replaceWords, ' ', $menuItem->nombre);
						$impresion = $clienteNombre."{$salon->nombre}  M:{$salonMesa->numero} A:{$account->asiento}<br>$nombre C: {$cantidadSinEnviar}\n$estado\n";
						foreach ($account->getAccountModifiers() as $accountModifier) {
							$modifier = $accountModifier->getModifiers();
							if ($modifier != false) {
								$impresion .= ' > ' . $modifier->nombre . '<br/>';
							}
						}
						if ($account->note != '') {
							$impresion .= ' * ' . $account->note . '<br/>';
						}
						$imp[$salonMenusItems->printers_id][] = $impresion;
					} else {

						if ($printingType == 'S') {
							$salonMenusItems = $this->SalonMenusItems->findFirst("salon_id='$this->salon_id' AND menus_items_id='{$account->menus_items_id}'");
							if ($salonMenusItems == false) {
								continue;
							}

							$nombre = str_ireplace($replaceWords, ' ', $menuItem->nombre);
							$salonNombre = '';
							$salonNombreP = explode(' ', $salon->nombre);
							foreach($salonNombreP as $part){
								$salonNombre.= substr($part, 0, 7).' ';
							}

							$mesero = substr($usuario->nombre, 0, 10);
							if(!isset($imp[$salonMenusItems->printers_id])){
								$imp[$salonMenusItems->printers_id] = array();
							}
							if(!isset($imp[$salonMenusItems->printers_id]['P'])){
								$imp[$salonMenusItems->printers_id]['P'] = array();
							}
							if(!isset($imp[$salonMenusItems->printers_id]['C'])){
								$imp[$salonMenusItems->printers_id]['C'] = array();
							}

							$printer = $this->Printers->findFirst($salonMenusItems->printers_id);
							if($printer!=false){
								$impresora = $printer->nombre;
							} else {
								$impresora = 'NO EXISTE LA IMPRESORA '.$salonMenusItems->printers_id;
							}

							$modifierData = '';
							foreach($account->getAccountModifiers() as $accountModifier){
								$modifier = $this->Modifiers->find($accountModifier->modifiers_id);
								$modifierData.= " > ".$modifier->nombre."\n";
							}
							if($modifierData!=""){
								$modifierData.= "\n";
							}
							$imp[$salonMenusItems->printers_id]['P'][$account->estado][$this->SalonMesas->numero][] = $clienteNombre."[P] A: $salonNombre  M: {$salonMesa->numero} A:{$account->asiento}\n{$account->comanda}:{$nombre} C: $cantidadSinEnviar\n$modifierData\nNota: {$account->note}";
							$imp[$salonMenusItems->printers_id2]['C'][$account->estado][$this->SalonMesas->numero][] = $clienteNombre."[C] A: $salonNombre  M: {$salonMesa->numero} A:{$account->asiento}\n{$account->comanda}:{$nombre} C: $cantidadSinEnviar P: $impresora\n$modifierData\nNota: {$account->note}";
						}
					}

					if ($printingType == 'C' || $printingType == 'S') {
						$account->send_kitchen = 'S';
						$account->cantidad_cocina = $account->cantidad;
						if ($account->save() == false) {
							foreach ($account->getMessages() as $message) {
								$transaction->rollback($message->getMessage());
							}
						}
					}
				}

				if ($printingType == 'S') {
					if ($datos->getPrintServer() != '') {
						foreach ($imp as $printer => $imt) {
							$Printer = $this->Printers->findFirst($printer);
							$data = "======================================\n";
							$data.= $datos->getNombreHotel()."\n";
							$data.= "F: ".Date::now()." I: ";
							$data.= $Printer->nombre."\nU: {$accountMaster->nombre}\n";
							$tm = 0;
							$jj = count($imt);
							$kk = 0;
							foreach ($imt as $type => $ime) {
								if ($kk != $jj) {
									if ($type == 'P') {
										$data.= "--------- P R O D U C C I O N --------\n";
									} else {
										$data.= "------- C O N F I R M A C I O N ------\n";
									}
								}
								$j = count($ime);
								$k = 0;
								if($j>0){
									foreach($ime as $estado => $imm){
										if($estado=='A'){
											$data.= "+++++++++++ A T E N D I D O ++++++++++\n";
										}
										if($estado=='S'){
											$data.= "+++++++++ S I N  A T E N D E R +++++++\n";
										}
										if($estado=='C'){
											$data.= "+++++++++ C A N C E L A D O S ++++++++\n";
										}
										foreach($imm as $nmesa => $im){
											foreach($im as $line){
												$data.=$line."\n";
												$tm++;
											}
											$k++;
											if($k!=$j){
												$data.= "- - - - - - - - - - - - - - - - - - - \n";
											}
										}
									}
								}
								$kk++;
							}
							if ($tm < 15) {
								for ($i = $tm; $i <= 9; $i++) {
									$data .= "\n";
								}
							}
							$data .= "======================================\n";
							$data .= "\n";
							try {
								$http = new HttpRequest('http://'.$datos->getPrintServer().'/printServer.php' , HTTP_METH_POST);
								$http->addPostFields(array(
									'printer' => $Printer->ubicacion,
									'data' => base64_encode($data)
								));
								$http->send();
							}
							catch(HttpInvalidParamException $e){
								echo $e->getMessage();
							}
						}
					}
					if(!Browser::isMobile()){
						$this->redirect('tables/index/'.$this->salon_id);
					}
				} else {
					if ($printingType == 'C') {
						$file = '';
						foreach($imp as $printer => $im){
							$file = substr(md5(time()), 0, 10);
							$fp = fopen('public/temp/' . $file . '.html', 'w');
							fputs($fp, "\n<html><head><style>body{margin:0px}\npre{font-family:'Courier New';font-size: 14px;}</style>
							<body><pre><br>-----------------------------------\n".$datos->getNombreHotel().
							"\nORDEN DE PRODUCCIÓN\nFECHA: ".Date::now()."\nCOMANDAS: ".join(', ', $comandas).
							"\nMESERO: ".$usuario->nombre."\n\n");
							foreach($im as $i){
								fputs($fp, $i."<br>");
							}
							fputs($fp, "\n<br><br><br>-----------------------------------
							<script type='text/javascript'>window.print()</script></pre></body></html>");
							fclose($fp);
							readfile('public/temp/'.$file.'.html');
						}
						if (!Browser::isMobile()) {
							if ($file != '') {
								$this->redirect('tables/index/' . $this->salon_id . '/' . $file);
							} else {
								$this->redirect('tables/index/' . $this->salon_id);
							}
						}
					} else {
						$this->redirect('tables/index/' . $this->salon_id);
					}
				}
			}
			$transaction->commit();
		} catch (TransactionFailed $e) {
			Flash::error($e->getMessage());
		}
	}

	public function getNextComandaAction()
	{
		$this->setResponse('xml');
		if ($this->salon_id>0) {
			$salon = $this->Salon->findFirst($this->salon_id);
			return $salon->consecutivo_comanda+1;
		}
		return 0;
	}

	public function addNextComandaAction()
	{
		$this->setResponse('json');
		if($this->salon_id>0){
			$salon = $this->Salon->findFirst($this->salon_id);
			$this->numero_comanda = $salon->consecutivo_comanda+1;
			return $this->numero_comanda;
		}
		return 0;
	}

	public function redirectToMesasAction()
	{
		$this->redirect("tables/index/{$this->salon_id}");
	}

	/**
	 * Obtiene el tipo de comanda actual
	 *
	 * @return string
	 */
	public function getTipoComandaAction(){
		$this->setResponse('xml');
		$tipoComanda = Session::getData("tipo_comanda");
		return $tipoComanda ? $tipoComanda : "?";
	}

	/**
	 * Devuelve el numero de comanda actual
	 *
	 * @return int
	 */
	public function getNumeroComandaAction(){
		$this->setResponse('xml');
		return $this->numero_comanda;
	}

	/**
	 * Consulta si requiere numero de personas
	 *
	 */
	public function getPidePersonasAction(){
		$this->setResponse('xml');
		return in_array(Session::get("salon_type"), array('C', 'P')) ? 1 : 0;
	}

}
