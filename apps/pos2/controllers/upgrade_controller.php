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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

class UpgradeController extends ApplicationController
{

	public function indexAction()
	{

	}

	public function dumpDBAction()
	{
		$this->setResponse('view');
		$db = DbBase::rawConnect();
		$tables = $db->listTables();
		$schema = array();
		foreach ($tables as $table){
			$createTableCursor = $db->query("SHOW CREATE TABLE `$table`");
			while ($createTableRow = $db->fetchArray($createTableCursor)) {
				if (strpos($createTableRow[1],'DEFINER VIEW') == false) {
					$createTableRow[1] = preg_replace('/ AUTO_INCREMENT=[0-9]+/', '', $createTableRow[1]);
					$schema[$table] = array(
						'type' => 'TABLE',
						'sql' => $createTableRow[1],
						'fields' => array()
					);
					$position = strpos($createTableRow[1], '(');
					$endPosition = strpos($createTableRow[1], ') ENGINE');
					$fieldsRaw = substr($createTableRow[1], $position+2, $endPosition-$position-1);
					$fieldsArray = explode(",\n", $fieldsRaw);
					foreach ($fieldsArray as $fieldItem) {
						if (substr($fieldItem, 0, 5) != '  KEY' && substr($fieldItem, 0, 5) != '  PRI' && substr($fieldItem, 0, 5) != '  UNI' && substr($fieldItem, 0, 7)!='  CONST'){
							if (preg_match('/`(.+)`/', $fieldItem, $matches)) {
								$schema[$table]['fields'][$matches[1]] = $fieldItem;
							}
						} else {
							if (substr($fieldItem, 0, 5) == '  KEY') {
								if (preg_match('/`([a-z0-9A-Z_]+)`/', $fieldItem, $matches)) {
									$schema[$table]['indexes'][$matches[1]] = str_replace(")\n)", ')', $fieldItem);
								}
							}
						}
					}
				} else {
					$schema[$table] = array(
						'type' => 'VIEW',
						'sql' => $createTableRow[1]
					);
				}
			}
		}
		$version = str_replace('.', '', ControllerBase::APP_VERSION);
		file_put_contents('apps/pos2/schema/'.$version.'.php', serialize($schema));
	}

	private function syncDb($schema)
	{
		$db = DbBase::rawConnect();
		GarbageCollector::freeAllMetaData();
		$tables = $db->listTables();
		$activeTables = array();
		foreach ($tables as $table) {
			$createTableCursor = $db->query("SHOW CREATE TABLE `$table`");
			while ($createTableRow = $db->fetchArray($createTableCursor)) {
				if (strpos($createTableRow[1],'DEFINER VIEW') == false) {
					if (strpos($createTableRow[1],'ENGINE=InnoDB') == false) {
						$db->query("ALTER TABLE $table ENGINE=InnoDB");
					}
					$db->query("ALTER TABLE $table COMMENT=''");
					if (strpos($createTableRow[1],'DEFAULT CHARSET=utf8') == false) {
						$db->query("ALTER TABLE $table DEFAULT CHARSET=utf8;");
					}
				}
			}
			$activeTables[$table] = 1;
		}
		foreach ($schema as $table => $features) {
			if (!isset($activeTables[$table])) {
				$appConfig = CoreConfig::readFromActiveApplication('app.ini');
				$sql = $features['sql'];
				$sql = str_replace('`pos`', "`" . $appConfig->pos->pos . "`", $sql);
				$sql = str_replace('`hotel2`', "`" . $appConfig->pos->hotel . "`", $sql);
				$sql = str_replace('`ramocol`', "`" . $appConfig->pos->ramocol . "`", $sql);
				$db->query($sql);
			} else {
				$createTableCursor = $db->query("SHOW CREATE TABLE `$table`");
				while ($createTableRow = $db->fetchArray($createTableCursor)) {
					if (strpos($createTableRow[1],'DEFINER VIEW') == false) {
						$position = strpos($createTableRow[1], '(');
						$endPosition = strpos($createTableRow[1], ') ENGINE');
						$fieldsRaw = substr($createTableRow[1], $position+2, $endPosition-$position-1);
						$fieldsArray = explode(",\n", $fieldsRaw);
						$fields = array();
						$indexes = array();
						foreach ($fieldsArray as $fieldItem) {
							if (substr($fieldItem, 0, 5)!='  KEY'&&substr($fieldItem, 0, 5)!='  PRI'&&substr($fieldItem, 0, 5)!='  UNI'&&substr($fieldItem, 0, 7)!='  CONST') {
								if(preg_match('/`(.+)`/', $fieldItem, $matches)){
									if(!isset($features['fields'][$matches[1]])){
										$db->query("ALTER TABLE $table DROP COLUMN {$matches[1]}");
									} else {
										if(i18n::strtoupper($features['fields'][$matches[1]])!=i18n::strtoupper($fieldItem)){
											$db->query("ALTER TABLE $table MODIFY {$features['fields'][$matches[1]]}");
										}
									}
									$fields[] = $matches[1];
								}
							} else {
								if(isset($features['indexes'])){
									if(substr($fieldItem, 0, 5)=='  KEY'){
										if(preg_match('/`([a-z0-9A-Z_]+)`/', $fieldItem, $matches)){
											if(!isset($features['indexes'][$matches[1]])){
												$db->query("ALTER TABLE $table DROP INDEX `{$matches[1]}`");
											} else {
												if(i18n::strtoupper($features['indexes'][$matches[1]])!=i18n::strtoupper($fieldItem)){
													$db->query("ALTER TABLE $table DROP INDEX `{$matches[1]}`");
													$db->query("ALTER TABLE $table ADD {$features['indexes'][$matches[1]]}");
												}
											}
											$indexes[] = $matches[1];
										}
									}
								}
							}
						}
						$lastField = "";
						foreach ($features['fields'] as $fieldName => $sql) {
							if (!in_array($fieldName, $fields)) {
								$db->query("ALTER TABLE $table ADD $sql AFTER $lastField");
							}
							$lastField = $fieldName;
						}
						if(isset($features['indexes'])){
							foreach($features['indexes'] as $indexName => $sql){
								if(!in_array($indexName, $indexes)){
									$db->query("ALTER TABLE $table ADD $sql");
								}
							}
						}
					} else {
						$appConfig = CoreConfig::readFromActiveApplication('app.ini');
						$sql = $features['sql'];
						$sql = str_replace('`pos`', "`".$appConfig->pos->pos."`", $sql);
						$sql = str_replace('`hotel2`', "`".$appConfig->pos->hotel."`", $sql);
						$sql = str_replace('`ramocol`', "`".$appConfig->pos->ramocol."`", $sql);
						if($sql!=$createTableRow[1]){
							$db->query("DROP VIEW $table");
							$db->query($sql);
						}
					}
				}
			}
		}
	}

	public function to52Action(){
		$this->Datos->setTransaction($transaction);
		$datos = $this->Datos->findFirst();
		$datos->setVersion('5.2');
		if($datos->save()==false){
			foreach($datos->getMessages() as $message){
				Flash::error($message->getMessage());
			}
			$transaction->rollback();
		}
	}

	public function to53Action(){
		$this->Datos->setTransaction($transaction);
		$datos = $this->Datos->findFirst();
		$datos->setVersion('5.3');
		if($datos->save()==false){
			foreach($datos->getMessages() as $message){
				Flash::error($message->getMessage());
			}
			$transaction->rollback();
		}
	}

	public function to54Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		//$this->setResponse('json');
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/54.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();

			$this->Datos->setTransaction($transaction);
			$this->Salon->setTransaction($transaction);
			$this->Account->setTransaction($transaction);
			$this->AccountMaster->setTransaction($transaction);
			$this->AccountCuentas->setTransaction($transaction);
			$this->Factura->setTransaction($transaction);
			$this->DetalleFactura->setTransaction($transaction);
			$this->SalonMenusItems->setTransaction($transaction);
			$this->SalonMesas->setTransaction($transaction);
			$this->UsuariosPos->setTransaction($transaction);

			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			foreach($this->Salon->findForUpdate('venta_a="H"') as $salon){
				$salon->pide_asientos = 'N';
				$salon->pide_personas = 'N';
				if($salon->save()==false){
					foreach($salon->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
			}
			foreach($this->Salon->findForUpdate('autorizacion IS NULL') as $salon){
				$salon->autorizacion = '-';
				$salon->fecha_autorizacion = Date::getCurrentDate();
				if(!$salon->consecutivo_inicial){
					$salon->consecutivo_inicial = 1;
				}
				if(!$salon->consecutivo_final){
					$salon->consecutivo_final = 10000;
				}
				if($salon->save()==false){
					foreach($salon->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
				unset($salon);
			}
			foreach($this->Salon->findForUpdate('alto_mesas = 0 OR ancho_mesas = 0') as $salon){
				if(!$salon->alto_mesas){
					$salon->alto_mesas = 11;
				}
				if(!$salon->ancho_mesas){
					$salon->ancho_mesas = 11;
				}
				if(!$salon->prefijo_facturacion){
					$salon->prefijo_facturacion = $salon->id;
				}
				if($salon->save()==false){
					foreach($salon->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
				unset($salon);
			}
			foreach($this->UsuariosPos->find("LENGTH(perfil)=1") as $usuario){
				if($usuario->perfil=='A'){
					$usuario->perfil = 'Administradores';
				} else {
					if($usuario->perfil=='C'){
						$usuario->perfil = 'Cajeros';
					} else {
						$usuario->perfil = 'Meseros';
					}
				}
				if($usuario->save()==false){
					foreach($usuario->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
				unset($usuario);
			}

			foreach($this->Salon->findForUpdate('autorizacion = "" OR autorizacion IS NULL') as $salon){
				$salon->autorizacion = 'AUT'.$salon->id;
				if($salon->save()==false){
					foreach($salon->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
				unset($salon);
			}

			$num = $this->Factura->count("salon_id is null OR salon_id = '' OR salon_id = 0");
			if($num>0){
				$salones = array();
				foreach($this->Factura->find("salon_id is null OR salon_id = '' OR salon_id = 0") as $factura){
					if(!isset($salones[$factura->salon_nombre])){
						$salon = $this->Salon->findFirst("nombre='$factura->salon_nombre'");
						if($salon!=false){
							$salones[$factura->salon_nombre] = $salon->id;
						} else {
							Flash::error('No existe el ambiente '.$factura->salon_nombre);
						}
					}
					if(isset($salones[$factura->salon_nombre])){
						$factura->salon_id = $salones[$factura->salon_nombre];
						if($factura->save()==false){
							foreach($factura->getMessages() as $message){
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
					unset($factura);
				}
			}
			$cuentas = $this->AccountCuentas->findForUpdate("prefijo <> '' AND prefijo IS NOT NULL AND prefijo <> '0'");
			foreach($cuentas as $cuenta){
				$accountMaster = $cuenta->getAccountMaster();
				$salonMesas = $accountMaster->getSalonMesas('columns: id,salon_id');
				if($salonMesas!=false){
					$salon = $salonMesas->getSalon('columns: id,prefijo_facturacion');
					if($cuenta->prefijo!=$salon->prefijo_facturacion){
						$cuenta->prefijo = $salon->prefijo_facturacion;
						if($cuenta->save()==false){
							foreach($cuenta->getMessages() as $message){
								Flash::error($message->getMessage().' en cuenta (5)');
							}
							$transaction->rollback();
						}
					}
				} else {
					$numero = $this->Factura->count("account_master_id='{$accountMaster->id}'");
					if($numero>0){
						$factura = $this->Factura->findFirst("account_master_id='{$accountMaster->id}'", "columns: id,salon_id");
						$salon = $this->Salon->findFirst($factura->salon_id);
						if($salon!=false){
							$cuenta->prefijo = $salon->prefijo_facturacion;
							if($cuenta->save()==false){
								foreach($cuenta->getMessages() as $message){
									Flash::error($message->getMessage().' en cuenta (3)');
								}
								$transaction->rollback();
							}
						} else {
							Flash::error('La mesa ya no existe en la cuenta (3) '.$accountMaster->id);
							$transaction->rollback();
						}
					} else {
						if($cuenta->estado<>'C'){
							$numberFactura = $this->Factura->count("account_master_id='{$accountMaster->id}'");
							if($numberFactura==0){
								$cuenta->estado = 'C';
								$cuenta->prefijo = ' ';
								if($cuenta->save()==false){
									foreach($cuenta->getMessages() as $message){
										Flash::error($message->getMessage().' en cuenta (4)');
									}
									$transaction->rollback();
								}
								$accountMaster->estado = 'C';
								if($accountMaster->save()==false){
									foreach($accountMaster->getMessages() as $message){
										Flash::error($message->getMessage());
									}
									$transaction->rollback();
								}
							} else {
								Flash::error('La mesa ya no existe en la cuenta (4) '.$accountMaster->id);
								$transaction->rollback();
							}
						}
					}
				}
				unset($accountMaster);
				unset($salonMesas);
				unset($cuenta);
			}

			$db = $transaction->getConnection();
			$cursor = $db->query("SELECT DISTINCT salon_id FROM salon_mesas WHERE salon_id NOT IN (SELECT id FROM salon)");
			while($salon = $db->fetchArray($cursor)){
				$db->insert('salon', array(
					$salon['0'], "'SALON BORRADO'", "'PR".$salon[0]."'", "'S'", "'S'", "10", "10"
				),
				array(
					'id', 'nombre', 'prefijo_facturacion', 'pide_asientos', 'pide_personas', 'ancho_mesas', 'alto_mesas'
				));
			}

			$cuentas = $this->AccountCuentas->findForUpdate("prefijo = '' OR prefijo IS NULL OR prefijo = '0'");
			foreach($cuentas as $cuenta){
				$accountMaster = $cuenta->getAccountMaster();
				if($accountMaster!=false){
					$salonMesas = $accountMaster->getSalonMesas('columns: id,salon_id');
					if($salonMesas!=false){
						$salon = $salonMesas->getSalon('columns: id,prefijo_facturacion');
						if($salon!=false){
							$cuenta->prefijo = $salon->prefijo_facturacion;
							if($cuenta->save()==false){
								foreach($cuenta->getMessages() as $message){
									Flash::error($message->getMessage().' en cuenta (0)');
								}
								$transaction->rollback();
							}
						} else {
							Flash::error('El salon no existe en la cuenta '.$salonMesas->id);
							$transaction->rollback();
						}
					} else {
						$numero = $this->Factura->count("account_master_id='{$accountMaster->id}'");
						if($numero>0){
							$factura = $this->Factura->findFirst("account_master_id='{$accountMaster->id}'", "columns: id,salon_id");
							$salon = $this->Salon->findFirst($factura->salon_id);
							if($salon!=false){
								$cuenta->prefijo = $salon->prefijo_facturacion;
								if($cuenta->save()==false){
									foreach($cuenta->getMessages() as $message){
										Flash::error($message->getMessage().' en cuenta (1)');
									}
									$transaction->rollback();
								}
							} else {
								Flash::error('La mesa ya no existe en la cuenta (1)');
								$transaction->rollback();
							}
						} else {
							if($cuenta->estado<>'C'){
								$numberFactura = $this->Factura->count("account_master_id='{$accountMaster->id}'");
								if($numberFactura==0){
									$cuenta->estado = 'C';
									$cuenta->prefijo = ' ';
									if($cuenta->save()==false){
										foreach($cuenta->getMessages() as $message){
											Flash::error($message->getMessage().' en cuenta (2) '.$cuenta->id);
										}
										$transaction->rollback();
									}
									$accountMaster->estado = 'C';
									if($accountMaster->save()==false){
										foreach($accountMaster->getMessages() as $message){
											Flash::error($message->getMessage());
										}
										$transaction->rollback();
									}
								} else {
									Flash::error('La mesa ya no existe en la cuenta (2) '.$accountMaster->id);
									$transaction->rollback();
								}
							}
						}
					}
				} else {
					$cuenta->estado = 'C';
					$cuenta->prefijo = ' ';
					if($cuenta->save()==false){
						foreach($cuenta->getMessages() as $message){
							Flash::error($message->getMessage().' en cuenta (3) '.$cuenta->id);
						}
						$transaction->rollback();
					}
				}
				unset($accountMaster);
				unset($salonMesas);
				unset($cuenta);
			}
			foreach($this->Factura->findForUpdate('cuenta = 0 AND salon_id IS NOT NULL', 'limit: 1000', 'order: id DESC') as $factura){
				$this->Salon->setTransaction($transaction);
				$salon = $this->Salon->findFirst($factura->salon_id, 'columns: id,autorizacion,fecha_autorizacion,leyenda_propina');
				if(!$factura->prefijo_facturacion){
					$factura->prefijo_facturacion = $salon->id;
				}
				if(!$factura->resolucion){
					$factura->resolucion = $salon->autorizacion;
				}
				if(!$factura->fecha_resolucion){
					$factura->fecha_resolucion = $salon->fecha_autorizacion;
				}
				$this->AccountMaster->setTransaction($transaction);
				$accountMaster = $this->AccountMaster->findFirst($factura->account_master_id, 'columns: id,hora');
				if($accountMaster!=false){
					$this->AccountCuentas->setTransaction($transaction);
					$accountCuenta = $this->AccountCuentas->findFirst("account_master_id='{$factura->account_master_id}' AND prefijo='{$factura->prefijo_facturacion}' AND numero='{$factura->consecutivo_facturacion}'");
					if(!$factura->cuenta){
						if($accountCuenta!=false){
							$factura->cuenta = $accountCuenta->cuenta;
						} else {
							if($factura->estado=='A'){
								$factura->estado = 'N';
								$factura->cuenta = '0';
								//Flash::error('No se encontró el account_cuentas de la factura');
								//$transaction->rollback();
							} else {
								$factura->cuenta = '0';
							}
						}
					}
					if(!$factura->comanda){
						$comandas = array();
						$this->Account->setTransaction($transaction);
						$accounts = $this->Account->find("account_master_id='{$factura->account_master_id}' AND cuenta='{$factura->cuenta}' AND estado <> 'C'");
						if(count($accounts)==0){
							if($factura->tipo=='O'&&$factura->tipo_venta=='H'){
								$valcar = $this->Valcar->findFirst("numdoc='{$factura->consecutivo_facturacion}'");
								if($valcar!=false){
									if($valcar->getEstado()<>'B'){
										$accounts = $this->Account->findForUpdate("account_master_id='{$factura->account_master_id}' AND cuenta='{$factura->cuenta}'");
										foreach($accounts as $account){
											$account->estado = 'L';
											if($account->save()==false){
												foreach($account->getMessages() as $message){
													Flash::error($message->getMessage());
												}
											}
										}
									}
								}
							}
						}
						if(count($accounts)>0){
							$totalCuenta = 0;
							$totalIva = 0;
							$totalServicio = 0;
							$subtotal = 0;
							$allMenuItems = true;
							$detalles = array();
							unset($resumen);
							$resumen = array(
								'A' => 0,
								'B' => 0
							);
							foreach($accounts as $account){
								if(!in_array($account->comanda, $comandas)){
									$comandas[] = $account->comanda;
								}
								$menuItem = $this->MenusItems->find($account->menus_items_id);
								if($menuItem!=false){
									if(!isset($resumen[$menuItem->tipo])){
										$resumen[$menuItem->tipo] = 0;
									}
									$valor = $account->valor*$account->cantidad;
									$total = $account->total*$account->cantidad;
									$nombreItem = $menuItem->nombre;
									foreach($this->AccountModifiers->find("account_id='".$account->id."'") as $accountModifier){
										$modifier = $this->Modifiers->findFirst($accountModifier->modifiers_id);
										$nombreItem.=' + '.$modifier->nombre;
										if($modifier!=false){
											$valor+=$modifier->valor;
											$total+=$modifier->valor;
										}
									}
									if($account->descuento>0){
										$nombreItem.=" (Descuento {$account->descuento}%)";
										$valor-=($valor * $account->descuento / 100);
										$servicio = ($account->servicio - ($account->servicio*$account->descuento/100)) * $account->cantidad;
										$iva = ($account->iva - ($account->iva * $account->descuento/100)) * $account->cantidad;
										$total-=($total * $account->descuento / 100);
									} else {
										$servicio = $account->servicio * $account->cantidad;
										$iva = $account->iva * $account->cantidad;
									}
									$resumen[$menuItem->tipo]+=$valor;
									$totalCuenta+=$total;
									$subtotal+=$valor;
									$totalIva+=$iva;
									$totalServicio+=$servicio;
									$detalles[] = array(
										'nombre' => $nombreItem,
										'account_id' => $account->id,
										'porc_iva' => (int)$menuItem->porcentaje_iva,
										'cantidad' => $account->cantidad,
										'descuento' => (int)$account->descuento,
										'menus_items_id' => (int)$account->menus_items_id,
										'valor' => $valor,
										'iva' => $iva,
										'servicio' => $servicio,
										'total' => $total
									);
								} else {
									$allMenuItems = false;
								}
								unset($account);
							}
							if($allMenuItems==true){
								$conditions = "prefijo_facturacion = '{$factura->prefijo_facturacion}' AND consecutivo_facturacion = {$factura->consecutivo_facturacion} AND tipo = '{$factura->tipo}'";
								$this->DetalleFactura->setTransaction($transaction);
								$this->DetalleFactura->delete($conditions);
								if($factura->total!=$totalCuenta){
									Flash::notice('El total no coincide con el de la cuenta, en la factura: '.$factura->prefijo_facturacion.'-'.$factura->consecutivo_facturacion);
									//$transaction->rollback();
								}
								if($totalCuenta>0){
									foreach($detalles as $d){
										$detalle = new DetalleFactura();
										$detalle->setTransaction($transaction);
										$detalle->prefijo_facturacion = $factura->prefijo_facturacion;
										$detalle->consecutivo_facturacion = $factura->consecutivo_facturacion;
										$detalle->tipo = $factura->tipo;
										$detalle->menus_items_nombre = $d['nombre'];
										$detalle->account_id = $d['account_id'];
										$detalle->porcentaje_iva = $d['porc_iva'];
										$detalle->cantidad = $d['cantidad'];
										$detalle->descuento = $d['descuento'];
										$detalle->menus_items_id = $d['menus_items_id'];
										$detalle->valor = $d['valor'];
										$detalle->iva = $d['iva'];
										$detalle->servicio = $d['servicio'];
										$detalle->total = $d['total'];
										if($detalle->save()==false){
											foreach($detalle->getMessages() as $message){
												Flash::error($message->getMessage());
											}
											$transaction->rollback();
										}
										unset($d);
									}
									unset($detalles);
									$factura->leyenda_propina = $salon->leyenda_propina;
									$factura->propina = $accountCuenta->propina;
									$factura->total_alimentos = $resumen['A'];
									$factura->total_bebidas = $resumen['B'];
									$factura->subtotal = $subtotal;
									$factura->total_iva = $totalIva;
									$factura->total_servicio = $totalServicio;
									$factura->total = $totalCuenta;
								}
							} else {
								Flash::error('No se pudo reconstruir el detalle de la factura '.$factura->prefijo_facturacion.'-'.$factura->consecutivo_facturacion);
							}
							$factura->comanda = join(', ', $comandas);
						} else {
							if($factura->estado=='A'){
								Flash::error("No hay items en account: ".$factura->prefijo_facturacion.'-'.$factura->consecutivo_facturacion);
								$factura->estado = 'N';
								//$transaction->rollback();
							}
							$factura->comanda = '0';
						}
					}
					if($accountCuenta!=false){
						if(!$factura->habitacion_id){
							$factura->habitacion_id = $accountCuenta->habitacion_id;
						}
						if(!$factura->habitacion_numero){
							$habitacion = $this->HabitacionHistorico->findFirst($accountCuenta->habitacion_id, 'columns: numhab');
							if($habitacion!=false){
								$factura->habitacion_numero = $habitacion->numhab;
							} else {
								$factura->habitacion_numero = '0';
							}
						}
					} else {
						$factura->habitacion_id = '0';
						$factura->habitacion_numero = '0';
					}
					$factura->hora = substr($accountMaster->hora, 11, 5);
					if($factura->hora==' '){
						$factura->hora = ' ';
					}
				} else {
					$factura->comanda = ' ';
					$factura->habitacion_id = '0';
					$factura->habitacion_numero = '0';
					$factura->hora = ' ';
					$factura->estado = 'N';
				}
				if($factura->save()==false){
					foreach($factura->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
				unset($salon);
				unset($factura);
				unset($accountCuenta);
				unset($accountMaster);
			}
			foreach($this->SalonMenusItems->findForUpdate("almacen IS NULL") as $salonItem){
				$salonItem->almacen = 0;
				if($salonItem->save()==false){
					foreach($salonItem->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to541Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/541.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.1');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			foreach($this->Factura->find("tipo='F' AND estado='A'") as $factura){
				$rowcount = $this->PagosFactura->count("consecutivo_facturacion='{$factura->consecutivo_facturacion}' AND habitacion_id IS NULL");
				if($rowcount==0){
					$pagoFactura = new PagosFactura();
					$pagoFactura->setTransaction($transaction);
					$pagoFactura->prefijo_facturacion = $factura->prefijo_facturacion;
					$pagoFactura->consecutivo_facturacion = $factura->consecutivo_facturacion;
					$pagoFactura->tipo = $factura->tipo;
					$pagoFactura->formas_pago_id = 1;
					$pagoFactura->pago = $factura->total;
					$pagoFactura->cargo_plan = 'N';
					$pagoFactura->habitacion_id = null;
					$pagoFactura->cuenta = null;
					if($pagoFactura->save()==false){
						foreach($pagoFactura->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				}
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to542Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/542.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.2');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to543Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/543.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.3');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to544Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		#$schema = unserialize(file_get_contents('apps/pos2/schema/544.php'));
		#$this->syncDb($schema);
		#unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);

			$db = $transaction->getConnection();
			$cursor = $db->query("SELECT DISTINCT menus_items_id FROM account WHERE menus_items_id NOT IN (SELECT id FROM menus_items)");
			while($menuItem = $db->fetchArray($cursor)){
				$db->insert('menus_items', array(
					$menuItem['0'], "'ITEM BORRADO'", 1, "'I'"
				),
				array(
					'id', 'nombre', 'menus_id', 'estado'
				));
			}

			$cursor = $db->query("SELECT DISTINCT menus_id FROM menus_items WHERE menus_id NOT IN (SELECT id FROM menus)");
			while($menu = $db->fetchArray($cursor)){
				$db->insert('menus', array(
					$menu[0], "'MENU BORRADO'"
				), array(
					'id', 'nombre'
				));
			}

			foreach($this->AccountCuentas->find("estado='L'") as $accountCuenta){
				if($accountCuenta->propina>0){
					$conditions = "prefijo_facturacion='{$accountCuenta->prefijo}' AND consecutivo_facturacion='{$accountCuenta->numero}' AND tipo_venta='{$accountCuenta->tipo_venta}'";
					$factura = $this->Factura->findFirst($conditions);
					if($factura){
						$factura->setTransaction($transaction);
						if($factura->propina!=$accountCuenta->propina){
							if($factura->resolucion==''){
								$factura->resolucion = '-';
							}
							if($factura->comanda==''){
								$factura->comanda = '-';
							}
							if($factura->habitacion_numero==''){
								$factura->habitacion_numero = '-';
							}
							if($factura->hora==''){
								$factura->hora = '-';
							}
							$factura->propina = $accountCuenta->propina;
							if($factura->save()==false){
								foreach($factura->getMessages() as $message){
									Flash::error($message->getMessage());
								}
								$transaction->rollback();
							}
						}
					}
				}
			}

			$this->Factura->setTransaction($transaction);
			foreach($this->Factura->find("estado='N'") as $factura){
				$accountMaster = $factura->getAccountMaster();
				if($accountMaster){
					if($accountMaster->estado!='C'){
						$accountMaster->setTransaction($transaction);
						$accountMaster->estado = 'C';
						if($accountMaster->save()==false){
							foreach($accountMaster->getMessages() as $message){
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
				} else {
					Flash::error('No existe el account_master de '.$factura->id);
				}
				$accountCuenta = $factura->getAccountCuentas();
				if($accountCuenta){
					if($accountCuenta->estado!='C'){
						$accountCuenta->setTransaction($transaction);
						if($accountCuenta->prefijo==''){
							$accountCuenta->prefijo = '-';
						}
						$accountCuenta->estado = 'C';
						if($accountCuenta->save()==false){
							foreach($accountCuenta->getMessages() as $message){
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
					foreach($accountCuenta->getAccount() as $account){
						$account->setTransaction($transaction);
						if($account->estado!='C'){
							$account->estado = 'C';
							if($account->save()==false){
								foreach($account->getMessages() as $message){
									Flash::error($message->getMessage());
								}
								$transaction->rollback();
							}
						}
					}
				} else {
					Flash::error('No existe el account_master de '.$factura->id);
				}
			}

			foreach($this->Factura->find("estado='A'") as $factura){
				$accountMaster = $factura->getAccountMaster();
				if($accountMaster){
					if($accountMaster->estado!='L'){
						$accountMaster->setTransaction($transaction);
						$accountMaster->estado = 'L';
						if($accountMaster->save()==false){
							foreach($accountMaster->getMessages() as $message){
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
				} else {
					Flash::error('No existe el account_master de '.$factura->id);
				}
				$accountCuenta = $factura->getAccountCuentas();
				if($accountCuenta){
					if($accountCuenta->estado!='L'){
						$accountCuenta->setTransaction($transaction);
						if($accountCuenta->prefijo==''){
							$accountCuenta->prefijo = '-';
						}
						$accountCuenta->estado = 'L';
						if($accountCuenta->save()==false){
							foreach($accountCuenta->getMessages() as $message){
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
					foreach($factura->getDetalleFactura() as $detalleFactura){
						$account = $detalleFactura->getAccount();
						if($account!=false){
							$account->setTransaction($transaction);
							if($account->estado!='L'){
								$account->estado = 'L';
								if($account->save()==false){
									foreach($account->getMessages() as $message){
										Flash::error($message->getMessage());
									}
									$transaction->rollback();
								}
							}
						}
					}
				} else {
					Flash::error('No existe el account_master de '.$factura->id);
				}
			}

			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.4');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to545Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/545.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			foreach($this->Salon->find() as $salon){
				foreach($this->TipoVenta->find() as $tipoVenta){
					$salonTipoVenta = $this->SalonTipoVenta->findFirst("salon_id='{$salon->id}' AND tipo_venta_id='{$tipoVenta->id}'");
					if($salonTipoVenta==false){
						$salonTipoVenta = new SalonTipoVenta();
						$salonTipoVenta->setSalonId($salon->id);
						$salonTipoVenta->setTipoVentaId($tipoVenta->id);
						if($salonTipoVenta->save()==false){
							foreach($salonTipoVenta->getMessages() as $message){
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
				}
			}
			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.5');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to546Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/546.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.6');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to547Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/547.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);

			$db = $transaction->getConnection();
			$cursor = $db->query("SELECT DISTINCT usuarios_id FROM factura WHERE usuarios_id NOT IN (SELECT id FROM usuarios)");
			while($usuario = $db->fetchArray($cursor)){
				if($usuario[0]>0){
					$db->insert('usuarios', array(
						$usuario['0'], "'USUARIO BORRADO'", "'7fe36470143d02abbb49ee25394ef6c932617b2f'", "'Cajeros'", "'I'"
					),
					array(
						'id', 'nombre', 'clave', 'perfil', 'estado'
					));
				}
			}

			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.7');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to548Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/548.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);

			foreach($this->Salon->find() as $salon){
				$salon->texto_propina = "ADVERTENCIA PROPINA\nPor disposición de la Superintendencia\nde Industria y Comercio se informa que, en este\nestablecimiento la propina es sugerida al\nconsumidor y corresponde a un porcentaje del\n10% sobre el valor total de la cuenta, el cual\npodrá ser aceptado, rechazado ó\nmodificado por usted, de acuerdo con su\nvalorización del servicio prestado. Si no\ndesea cancelar dicho valor haga caso omiso del\nmismo. Si desea cancelar un valor diferente\nindiquelo así para hacer el ajuste correspondiente.\n$ _____________\n";
				if($salon->save()==false){
					foreach($salon->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
			}

			foreach($this->Factura->find("leyenda_propina='S'") as $factura){
				$factura->texto_propina = "ADVERTENCIA PROPINA\nPor disposición de la Superintendencia\nde Industria y Comercio se informa que, en este\nestablecimiento la propina es sugerida al\nconsumidor y corresponde a un porcentaje del\n10% sobre el valor total de la cuenta, el cual\npodrá ser aceptado, rechazado ó\nmodificado por usted, de acuerdo con su\nvalorización del servicio prestado. Si no\ndesea cancelar dicho valor haga caso omiso del\nmismo. Si desea cancelar un valor diferente\nindiquelo así para hacer el ajuste correspondiente.\n$ _____________\n";
				if($factura->save()==false){
					foreach($factura->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
			}

			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.8');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to549Action(){
		$this->setResponse('view');
		set_time_limit(0);
		try {
			$db = DbBase::rawConnect();
			$db->query('ALTER TABLE usuarios RENAME TO usuarios_pos');
		}
		catch(DbException $e){

		}
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/549.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);

			$this->_updateInterpos($transaction);

			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.9');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to5410Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/5410.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.10');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to5411Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/5411.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.11');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to5412Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/5412.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.12');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to5413Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/5413.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.13');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}


	public function to5414Action(){
		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/5414.php'));
		$this->syncDb($schema);
		unset($schema);
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('5.4.14');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	private function _cleanTables(){

		$connection = DbPool::getConnection();
		$tables = array('access_inherit', 'cart', 'roles', 'recetal', 'resources', 'qx__$schema', 'resources', 'access_list', 'access_resources', 'cart', 'prueba', 'flights');
		foreach ($tables as $table) {
			try {
				$connection->query('DROP TABLE `'.$table.'`');
			}
			catch(DbException $e){
				Flash::error($e->getMessage());
			}
		}

		$views = array('saldosd', 'almacenes', 'lineas', 'inve', 'centros');
		foreach($views as $view){
			try {
				$connection->query('DROP VIEW `'.$view.'`');
			}
			catch(DbException $e){
				Flash::error($e->getMessage());
			}
		}
	}

	public function to600Action(){

		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/600.php'));
		$this->syncDb($schema);
		unset($schema);

		try {

			$this->_cleanTables();

			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('6.0.0');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}

			ActiveRecord::disableEvents(true);
			foreach($this->MenusItems->find() as $menuItem){
				$menuItem->nombre_pedido = str_ireplace(' DE ', ' ', $menuItem->nombre);
				$menuItem->nombre_pedido = ucwords(i18n::strtolower(trim($menuItem->nombre_pedido)));
				if($menuItem->save()==false){
					foreach($menuItem->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}
			}
			foreach($this->Menus->find() as $menu){
				$menu->nombre_pedido = str_ireplace(' DE ', ' ', $menu->nombre);
				$menu->nombre_pedido = ucwords(i18n::strtolower(trim($menu->nombre_pedido)));
				if($menu->save()==false){
					foreach($menu->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}
			}
			foreach($this->Modifiers->find() as $modifier){
				$modifier->nombre_pedido = ucwords(i18n::strtolower(trim($modifier->nombre)));
				if($modifier->save()==false){
					foreach($modifier->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}
			}

			$transaction->commit();

			$this->_cleanTables();

		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to601Action()
	{

		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/601.php'));
		$this->syncDb($schema);
		unset($schema);

		try {

			$this->_cleanTables();

			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('6.0.1');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}

			$this->_cleanTables();

			$transaction->commit();
		} catch (TransactionFailed $e) {
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to602Action()
	{

		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/602.php'));
		$this->syncDb($schema);
		unset($schema);

		try {

			$this->_cleanTables();

			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('6.0.2');
			if($datos->save()==false){
				foreach($datos->getMessages() as $message){
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}

			$this->_cleanTables();

			$transaction->commit();

		}
		catch(TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function to603Action()
	{

		$this->setResponse('view');
		set_time_limit(0);
		ActiveRecord::disableEvents(true);
		GarbageCollector::freeAllMetaData();
		$schema = unserialize(file_get_contents('apps/pos2/schema/603.php'));
		$this->syncDb($schema);
		unset($schema);

		try {

			$this->_cleanTables();

			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);
			$datos = $this->Datos->findFirst();
			$datos->setVersion('6.0.3');
			if ($datos->save() == false) {
				foreach ($datos->getMessages() as $message) {
					Flash::error($message->getMessage());
				}
				$transaction->rollback();
			}

			$this->_cleanTables();

			$transaction->commit();

		} catch (TransactionFailed $e){
			$controllerResponse = ControllerResponse::getInstance();
			$controllerResponse->setHeader('X-Application-State: Exception', true);
			$controllerResponse->setHeader('HTTP/1.1 500 Application Exception', true);
			Flash::error($e->getMessage());
			print_r($e->getTrace());
		}
	}

	public function addNombrePedidoAction()
	{
	}

	public function arreglaSalonAction()
	{
		try {
			$transaction = TransactionManager::getUserTransaction();
			$salones = array();
			foreach($this->Salon->find() as $salon){
				$salones[$salon->nombre] = array('id' => $salon->id, 'prefijo' => $salon->prefijo_facturacion);
			}
			foreach($this->Factura->find() as $factura){
				if(isset($salones[$factura->salon_nombre])){
					$factura->salon_id = $salones[$factura->salon_nombre]['id'];
					$factura->prefijo_facturacion = $salones[$factura->salon_nombre]['prefijo'];
				}
			}
		} catch (TransactionFailed $e) {

		}
	}

	public function arreglaPrefijosAction()
	{
		$this->setResponse('view');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Factura->setTransaction($transaction);
			foreach($this->Factura->findForUpdate("prefijo_facturacion='---'") as $factura){
				$numero = $this->Factura->count("prefijo_facturacion='---' AND consecutivo_facturacion='{$factura->consecutivo_facturacion}' AND tipo='{$factura->tipo}'");
				if($numero==1){
					$salon = $factura->getSalon();
					if($salon!=false){
						foreach($factura->getDetalleFactura() as $detalleFactura){
							$detalleFactura->prefijo_facturacion = $salon->prefijo_facturacion;
							if($detalleFactura->save()==false){
								foreach($detalleFactura->getMessages() as $message){
									Flash::error($message->getMessage());
								}
								$transaction->rollback();
							}
						}
						foreach($factura->getPagosFactura() as $pagoFactura){
							$pagoFactura->prefijo_facturacion = $salon->prefijo_facturacion;
							if($pagoFactura->save()==false){
								foreach($pagoFactura->getMessages() as $message){
									Flash::error($message->getMessage());
								}
								$transaction->rollback();
							}
						}
						$factura->prefijo_facturacion = $salon->prefijo_facturacion;
						if($factura->save()==false){
							foreach($factura->getMessages() as $message){
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
				}
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){

		}
	}

	public function arreglaAccountCuentasAction(){
		$this->setResponse('view');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Factura->setTransaction($transaction);
			foreach($this->Factura->find() as $factura){
				$accountCuenta = $this->AccountCuentas->findFirst("account_master_id='{$factura->account_master_id}' AND cuenta='{$factura->cuenta}'");
				if($accountCuenta!=false){
					$accountCuenta->setTransaction($transaction);
					$accountCuenta->prefijo = $factura->prefijo_facturacion;
					$accountCuenta->numero = $factura->consecutivo_facturacion;
					if($accountCuenta->save()==false){
						foreach($accountCuenta->getMessages() as $message){
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

	public function arreglaAccountMasterAction(){
		$this->setResponse('view');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Factura->setTransaction($transaction);
			foreach($this->Factura->find() as $factura){
				$accountMaster = $this->AccountMaster->findFirst($factura->account_master_id);
				if($accountMaster!=false){
					$accountMaster->setTransaction($transaction);
					$accountMaster->salon_id = $factura->salon_id;
					if($accountMaster->save()==false){
						foreach($accountMaster->getMessages() as $message){
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

	private function _updateInterpos($transaction){

		$centroCosto = $this->Centros->findFirst("nom_centro LIKE '%RESTAU%'");
		if($centroCosto==false){
			$centroCosto = $this->Centros->findFirst("nom_centro LIKE '%ALIMENT%'");
			if($centroCosto==false){
				$centroCosto = $this->Centros->findFirst("nom_centro LIKE '%COMESTI%'");
				if($centroCosto==false){
					$transaction->rollback('No se encontró el centro de costo de ayb');
				}
			}
		}

		foreach($this->Interpos->find("estado=' '") as $interpos){
			$invepos = new Invepos();
			$invepos->setTransaction($transaction);
			if($interpos->prefac!=''){
				$invepos->setPrefac($interpos->prefac);
			} else {
				$invepos->setPrefac('-');
			}
			$invepos->setNumfac($interpos->numfac);
			$invepos->setFecha($interpos->fecha);
			$invepos->setAlmacen($interpos->almacen);
			$invepos->setCentroCosto($centroCosto->getCodigo());
			$invepos->setTipo($interpos->tipopro);
			$invepos->setCodigo($interpos->codigo);
			if($interpos->menus_items_id==''){
				$invepos->setMenusItemsId(1);
			} else {
				$invepos->setMenusItemsId($interpos->menus_items_id);
			}
			$invepos->setCantidad($interpos->cantidad);
			$invepos->setCantidadu($interpos->cantidadu);
			$invepos->setEstado('N');
			if($invepos->save()==false){
				foreach($invepos->getMessages() as $message){
					Flash::error('Invepos: '.$message->getMessage());
				}
				$transaction->rollback();
			}
		}
	}

	public function updateInterposAction()
	{
		$this->setResponse('view');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->_updateInterpos($transaction);
			$transaction->commit();
		}
		catch(TransactionFailed $e){

		}
	}

}
