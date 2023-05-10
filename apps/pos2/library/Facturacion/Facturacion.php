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
 * POSRcs
 *
 */
class Facturacion extends UserComponent
{

    public $preview = false;

    public $reprint = false;

	public $cuenta_liquidada = false;

	public $resolucion_id = '';

	public function setIdResolucion($resolucion_id){
		$this->resolucion_id = $resolucion_id;
	}

	public function genVoice($accountCuenta, $transaction){

		$response = array(
			'success' => true,
			'factura' => array(),
			'detalleFactura' => array(),
			'error' => ''
		);

		try {

			POSRcs::disable();

            $datos = $this->Datos->findFirst();
            $accountCuenta->setTransaction($transaction);
            if ($this->preview==false && $this->reprint == false) {
				if ($accountCuenta->clientes_cedula == '0') {
                    throw new Exception('El cliente no puede ser particular', 1);
                }
			}

			if ($accountCuenta->tipo_venta == 'S') {
				$cliente = $this->SociosActual->findFirst(array("identificacion='{$accountCuenta->clientes_cedula}'"));
				if ($cliente == false) {
					throw new Exception('No se ha definido el socio correctamente ('.$accountCuenta->account_master_id .')');
				}
			} else {
				$cliente = $this->Clientes->findFirst("cedula='{$accountCuenta->clientes_cedula}'");
				if ($cliente == false) {
					$empresa = $this->Empresas->findFirst("nit='{$accountCuenta->clientes_cedula}'");
					if ($empresa == false) {
						throw new Exception('No se ha definido el cliente correctamente ('.$accountCuenta->id.')');
					}
				}
			}

			$accountMaster = $this->AccountMaster->findFirst($accountCuenta->account_master_id);
			if ($accountMaster == false) {
				throw new Exception('No existe la cuenta maestra ('.$accountCuenta->account_master_id.')');
			}

			$accountMaster->setTransaction($transaction);
			$tipoVenta = $accountCuenta->tipo_venta;
			$habitacionId = $accountCuenta->habitacion_id;
			$habitacion = $this->Habitacion->findFirst($habitacionId);
			$this->Salon->setTransaction($transaction);
			$salon = $this->Salon->findFirst($accountMaster->salon_id);
			if ($salon == false) {
				throw new Exception('No existe el ambiente ('.$accountMaster->salon_id.')');
			}

			$this->DetalleFactura->setTransaction($transaction);
			if ($tipoVenta == 'F') {
				$tipo = 'F';
			} else {
				$tipo = 'O';
			}

			$salonId = $salon->id;
			$tipo_factura = 'P';
			$resolucion_factura_id = 0;
			$fecha_fin_autorizacion = null;

			/**
			 *  Estados tabla master_master (CUENTA MAESTRA)
			 *  N : Abierta
			 *  C : Maestra cancelada
			 *  L : Maestra liquidada
			 */


			/**
			*  Estados tabla account_cuentas (CUENTAS MESA)
			*  A : Cuenta abierta sin liquidar 
			*  C : Cuenta cancelada
			*  B : Cuenta facturada (Cuando se genera factura o orden de compra antes de liquidar)
			*  L : Maestra liquidada
			*/


			/***
			*  Estado tabla account (PRODUCTOS MESA)
			*  S : Producto registrado sin liquidar
			*  C : Producto cancelado (Se utiliza cuando cancelan cuenta o borran productos de forma individual)
			*  B : Producto facturado (Proceso que se ejecuta al factura o generar orden de servicio)
			*  L : Producto liquidado
			*/


			if ($accountCuenta->estado != 'L' && $accountCuenta->estado != 'B') {

				if($accountCuenta->numero > 0){
					$consecutivo = $accountCuenta->numero;
				} else {
					if ($accountCuenta->estado == 'A'){
						if ($tipoVenta != "F") {
							// $consecutivo = $accountCuenta->numero = ++$salon->consecutivo_orden;
							$resolucion = $this->Salon->findFirst($accountMaster->salon_id);
							if ($resolucion == false) {
								throw new Exception('No existe el ambiente ('.$accountMaster->salon_id.')');
							}

							$prefijoFacturacion = $resolucion->prefijo_facturacion;
							$consecutivo = $accountCuenta->numero = ++$resolucion->consecutivo_orden;

						} else {

							# ESTADO DE CUENTA SET DEFAUL RESOLUCION
							if($this->preview){
								$this->resolucion_id = 1;
							}

							if(empty($this->resolucion_id)){
								throw new Exception('No esta definida la resolución de facturación.');
							}

							$resolucion_factura_id = $this->resolucion_id;
							$this->ResolucionFactura->setTransaction($transaction);
							$resolucion = $this->ResolucionFactura->findFirst($this->resolucion_id);
							if ($resolucion == false) {
								throw new Exception('No existe la resolución de facturación con id '.$resolucion_factura_id);
							}

							$prefijoFacturacion = $accountCuenta->prefijo =  $resolucion->prefijo_facturacion;
							$consecutivo = $accountCuenta->numero = ++$resolucion->consecutivo_facturacion;
							$tipo_factura = $resolucion->tipo_factura;
							$fecha_fin_autorizacion = $resolucion->fecha_fin_autorizacion;
							
						}
					}
				}

				$conditions = "salon_id='$salonId' AND prefijo_facturacion = '$prefijoFacturacion' and consecutivo_facturacion='$consecutivo' and tipo = '$tipo'";
				$factura = $this->Factura->findFirst($conditions);
				if ($factura == false) {

					$salonMesa = $this->SalonMesas->findFirst($accountMaster->salon_mesas_id);
					if ($salonMesa == false) {
						$salonMesa = $this->SalonMesas->findFirst("salon_id=$salonId");
					}

					$comandas = array();
					$totalCuenta = 0;
					$totalIva = 0;
					$totalImpoconsumo = 0;
					$totalServicio = 0;
					$subtotal = 0;
					$resumen = array(
						'A' => 0,
						'B' => 0
					);
					$this->Account->setTransaction($transaction);
					$accountItems = $this->Account->findForUpdate("account_master_id='$accountCuenta->account_master_id' AND cuenta='$accountCuenta->cuenta' AND estado IN ('S', 'A')");
					if (count($accountItems)) {
						foreach ($accountItems as $account) {

							if(!in_array($account->comanda, $comandas)){
								$comandas[] = $account->comanda;
							}

							if(!$account->tiempo_final){
								$account->tiempo_final = Date::getCurrentTime();
							}

							$account->cantidad_atendida += $account->cantidad;
							$account->estado = 'B';
							if ($account->save() == false) {
								$error_save = array();
								foreach ($account->getMessages() as $message) {
									$error_save[] = $message->getMessage();
								}
								throw new Exception(implode(",",$error_save));
							}

							$menuItem = $this->MenusItems->find($account->menus_items_id);

							if(!isset($resumen[$menuItem->tipo])){
								$resumen[$menuItem->tipo] = 0;
							}
							
							$nombreItem = $menuItem->nombre;
							$modis = 0;
							$modifier_base = 0;
							$modifier_impo = 0;
							$modifier_iva = 0;
							$accountModifiers = $this->AccountModifiers->find("account_id='" . $account->id . "'");
							if (count($accountModifiers)) {
								$nombreItem .= PHP_EOL;
								foreach($accountModifiers as $accountModifier){
									$modifier = $this->Modifiers->findFirst($accountModifier->modifiers_id);
									switch ($modifier->tipo) {
										case 'S':
											$nombreItem.= ' - ' . $modifier->nombre.PHP_EOL;
											break;
										default:
											$nombreItem.= ' + ' . $modifier->nombre.PHP_EOL;
											break;
									}
									if ($modifier != false) {
										$modis += $accountModifier->valor;
									}
								}
								$nombreItem .= PHP_EOL;
							}

							#Calcular la base del modificar y iva o impo
							if ($menuItem->porcentaje_iva > 0) {
								if ($accountCuenta->tipo_venta == 'F') {
									$modifier_base = $modis / (($menuItem->porcentaje_iva + $menuItem->porcentaje_servicio) / 100 + 1);
									$modifier_iva = $modis - ($modis / (1 + ($menuItem->porcentaje_iva / 100)));
								} else {
									if (self::_esExento($accountCuenta, $menuItem)) {
										$modifier_base = $modis / (($menuItem->porcentaje_iva + $menuItem->porcentaje_servicio) / 100 + 1);
										$modifier_iva = 0;
									} else {
										$modifier_base = $modis / (($menuItem->porcentaje_iva + $menuItem->porcentaje_servicio) / 100 + 1);
										$modifier_iva = $modis - ($modis / (1 + ($menuItem->porcentaje_iva / 100)));
									}
								}
								$account->impo = 0;
							} else {
								$modifier_base = $modis / (($menuItem->porcentaje_impoconsumo + $menuItem->porcentaje_servicio) / 100 + 1);
								$modifier_impo = $modis - ($modis / (1 + ($menuItem->porcentaje_impoconsumo / 100)));
								$modifier_iva = 0;
							}

							$valor = ($account->valor + $modifier_base) * $account->cantidad;
							$total = ($account->total + $modis)         * $account->cantidad;
							$account->iva += $modifier_iva;
							$account->impo += $modifier_impo;

							$descuento_aplicado = 0;
							if ($account->descuento > 0) {
								$nombreItem .= " (Descuento {$account->descuento}%)";
								$descuento_aplicado = ($valor * $account->descuento / 100);
								$valor -= $descuento_aplicado;
								$servicio = ($account->servicio - ($account->servicio * $account->descuento / 100)) * $account->cantidad;
								if ($account->iva  > 0) {
									$iva = ($account->iva - ($account->iva * $account->descuento / 100)) * $account->cantidad;
									$impo = 0;
								} else {
									$impo = ($account->impo - ($account->impo * $account->descuento / 100)) * $account->cantidad;
									$iva = 0;
								}
								$total -= ($total * $account->descuento / 100);
							} else {
								$servicio = $account->servicio * $account->cantidad;
								$iva = $account->iva * $account->cantidad;
								$impo = $account->impo * $account->cantidad;
							}

							if (($valor + $iva + $impo + $servicio) < $total) {
								$valor = $total - ($iva + $impo + $servicio);
							} else {
								if (($valor + $iva + $impo + $servicio) > $total) {
									$valor = $total - ($iva + $impo +$servicio);
								}
							}

							$detalle = new DetalleFactura();
							$detalle->setTransaction($transaction);
							$detalle->prefijo_facturacion = $prefijoFacturacion;
							$detalle->consecutivo_facturacion = $consecutivo;
							$detalle->tipo = $tipo;
							$detalle->menus_items_id = $account->menus_items_id;
							$detalle->menus_items_nombre = $nombreItem;
							$detalle->account_id = $account->id;

							if ($iva > 0){
								$detalle->porcentaje_iva = $menuItem->porcentaje_iva;
								$detalle->porcentaje_impoconsumo = 0;
							} else {
								if ($impo > 0) {
									$detalle->porcentaje_impoconsumo = $menuItem->porcentaje_impoconsumo;
									$detalle->porcentaje_iva = 0;
								} else {
									$detalle->porcentaje_impoconsumo = 0;
									$detalle->porcentaje_iva = 0;
								}
							}

							$detalle->cantidad = $account->cantidad;
							$detalle->descuento = $account->descuento;
							$detalle->descuento_aplicado = $descuento_aplicado;
							$detalle->valor = $valor;
							$detalle->iva = $iva;
							$detalle->impo = $impo;
							$detalle->servicio = $servicio;
							$detalle->total = $total;
							if ($detalle->save() == false) {
								$error_save = array();
								foreach ($detalle->getMessages() as $message) {
									$error_save[] = $message->getMessage();
								}
								throw new Exception(implode(",",$error_save));
							}
							$resumen[$menuItem->tipo] += $valor;
							$totalCuenta += $total;
							$subtotal += $valor;
							$totalIva += $iva;
							$totalImpoconsumo += $impo;
							$totalServicio += $servicio;
						}
					} else {
						throw new Exception('Debe agregar items a la cuenta antes de generar la factura/orden');
					}

					if ($accountCuenta->propina_fija == 'N') {
						if ($salon->porcentaje_servicio > 0) {
							$totalAyB = $resumen['A'] + $resumen['B'];
							$propina = LocaleMath::round($totalAyB * ($salon->porcentaje_servicio / 100), 0);
						} else {
							$propina = 0;
						}
					} else {
						$propina = $accountCuenta->propina;
					}

					$totalCuenta += $propina;
					$accountCuenta->estado = 'B';
					if ($accountCuenta->save() == false) {
						$error_save = array();
						foreach ($accountCuenta->getMessages() as $message) {
							$error_save[] = $message->getMessage();
						}
						throw new Exception(implode(",",$error_save));
					}

					if ($resolucion->save() == false) {
						$error_save = array();
						foreach ($resolucion->getMessages() as $message) {
							$error_save[] = $message->getMessage(); 
						}
						throw new Exception(implode(",",$error_save));
						
					}

					$factura = new Factura();
					$factura->setTransaction($transaction);
					$factura->prefijo_facturacion = $prefijoFacturacion;
					$factura->consecutivo_facturacion = $consecutivo;
					$factura->resolucion = $resolucion->autorizacion;
					$factura->fecha_resolucion = $resolucion->fecha_autorizacion;
					$factura->fecha_fin_autorizacion = $fecha_fin_autorizacion;
					$factura->tipo = $tipo;
					$factura->numero_inicial = $resolucion->consecutivo_inicial;
					$factura->numero_final = $resolucion->consecutivo_final;
					$factura->account_master_id = $accountCuenta->account_master_id;
					$factura->cuenta = $accountCuenta->cuenta;
					$factura->comanda = join(',', $comandas);
					$factura->documento = $datos->getDocumento();
					$factura->nit = $datos->getNit();
					$factura->nombre_hotel = i18n::strtoupper($datos->getNombreHotel());
					$factura->nombre_cadena = i18n::strtoupper($datos->getNombreCadena());
					$factura->direccion = $datos->getDireccion();
					$factura->telefonos = $datos->getTelefonos();
					$factura->fax = $datos->getFax();
					$factura->po_box = $datos->getPoBox();
					$factura->ciudad = $datos->getCiudad();
					$factura->pais = $datos->getPais();
					$factura->entidad = $datos->getEntidad();
					$factura->moneda = $datos->getMoneda();
					$factura->centavos = $datos->getCentavos();
					$factura->nota_contribuyentes = $datos->getNotaContribuyentes();
					$factura->tipo_factura = $tipo_factura;
					$factura->resolucion_factura_id = $resolucion_factura_id;

					if ($cliente == false) {
						$factura->cedula = $empresa->nit;
						$factura->nombre = $empresa->nombre;
						$factura->clientes_direccion = $empresa->direccion;
						$factura->clientes_telefono = $empresa->telefono;
					} else {
						//Si es de socios
						if ($accountCuenta->tipo_venta == 'S') {
							$factura->cedula = $cliente->identificacion;
							$factura->nombre = $cliente->nombres.' '.$cliente->apellidos;
							$factura->clientes_direccion = $cliente->direccion_casa;
							$factura->clientes_telefono = $cliente->telefono_casa;
						} else {
							$factura->cedula = $cliente->cedula;
							$factura->nombre = $cliente->nombre;
							$factura->clientes_direccion = $cliente->direccion;
							$factura->clientes_telefono = $cliente->telefono1;
						}

					}

					$factura->habitacion_id = $habitacionId;
					$factura->habitacion_numero = $habitacion ? $habitacion->numhab : '0';
					$factura->salon_id = $salonId;
					$factura->salon_nombre = $salon->nombre ? $salon->nombre : 'ORDEN DIRECTA';
					$factura->salonmesas_numero = $salonMesa->numero ? $salonMesa->numero : '0';
					$factura->usuarios_id = Session::getData('usuarios_id');
					$factura->usuarios_nombre = i18n::strtoupper(Session::getData('usuarios_nombre'));
					$factura->fecha = (string)$datos->getFecha();
					$factura->hora = Date::getCurrentTime('H:i');
					$factura->tipo_venta = $tipoVenta;
					$factura->leyenda_propina = $salon->leyenda_propina;
					$factura->texto_propina = $salon->texto_propina;
					$factura->texto_impresion = $salon->texto_impresion;
					$factura->propina = $propina;
					$factura->total_alimentos = $resumen['A'];
					$factura->total_bebidas = $resumen['B'];
					$factura->subtotal = $subtotal;
					$factura->total_iva = $totalIva;
					$factura->total_impoconsumo = $totalImpoconsumo;
					$factura->total_servicio = $totalServicio;
					$factura->total = $totalCuenta;
					$factura->estado = 'A';
					
					$conditions = "salon_id='$salonId' AND prefijo_facturacion = '$prefijoFacturacion' and consecutivo_facturacion='$consecutivo' and tipo = '$tipo'";
					$validacion = $this->Factura->findFirst($conditions);
					if($validacion){
						throw new Exception('Error al grabar la factura/orden. por favor vuelva a intentarlo');
					}

					if ($factura->save() == false) {
						$error_save = array();
						foreach ($factura->getMessages() as $message) {
							$error_save[] =  $message->getMessage();
						}
						throw new Exception(implode(",",$error_save));
					} else {
						if ($tipo == 'F') {
							new POSAudit("GENERÓ LA FACTURA $prefijoFacturacion-$consecutivo", $transaction);
						}  else {
							new POSAudit("GENERÓ LA ORDEN DE SERVICIO $prefijoFacturacion-$consecutivo", $transaction);
						}
					}
					

				} else {
                    if ($this->preview == false && $this->reprint == false) {
						throw new Exception('El consecutivo a asignar ya fue utilizado');
					}
                }
			} else {

				$prefijoFacturacion = $accountCuenta->prefijo;
				$conditions = "salon_id='$salonId' AND prefijo_facturacion = '$prefijoFacturacion' and consecutivo_facturacion = {$accountCuenta->numero} and tipo = '$tipo'";
				$factura = $this->Factura->findFirst($conditions);
				if ($factura == false) {
					throw new Exception('No existe la factura/orden ' . $prefijoFacturacion . ':' . $accountCuenta->numero);
				} else {
					if ($factura->estado == 'N') {
						throw new Exception('La factura/orden ' . $prefijoFacturacion . ':' . $accountCuenta->numero.' está anulada');
					}
				}

				$this->cuenta_liquidada = true;
				
			}

			$conditions = "prefijo_facturacion = '$prefijoFacturacion' AND consecutivo_facturacion = '{$accountCuenta->numero}' AND tipo = '$tipo'";
			$detalleFactura = $this->DetalleFactura->find($conditions);

			# Consultar pagos factura
			$pagosFactura = $this->PagosFactura->find($conditions);

			$response['Factura'] = $factura;
			$response['detalleFactura'] = $detalleFactura;
			$response['pagosFactura'] = $pagosFactura;

			
		}catch(Exception  $e){
			$response['success'] = false;
			$response['error'] = $e->getMessage();
		}

		return $response;

	}


	/**
	 * Busca si la cuenta donde se agregará un item es exenta ó no
	 *
	 * @param	AccountCuentas $accountCuenta
	 * @param	MenusItems $menuItem
	 * @param	boolean $singleItem
	 */
	public static function _esExento($accountCuenta, $menuItem, $singleItem=true)
	{
		if ($accountCuenta->tipo_venta == 'H') {
			if ($accountCuenta->habitacion_id != -1) {

				# CUENTA MAESTRA
				$AccountMaster = $accountCuenta->getAccountMaster();

				# NUMERO DE FOLIO
				$numeroFolio = $accountCuenta->habitacion_id;

				# CARGAR MODELOS
				$SalonMenusItems = EntityManager::getEntityInstance('SalonMenusItems');
				$Concue = EntityManager::getEntityInstance('Concue');
				$Carghab = EntityManager::getEntityInstance('Carghab');
				$Conrel = EntityManager::getEntityInstance('Conrel');
				$Conceptos = EntityManager::getEntityInstance('Conceptos');
				$Clientes = EntityManager::getEntityInstance('Clientes');


				$salonMenuItem = $SalonMenusItems->findFirst("salon_id='{$AccountMaster->salon_id}' AND menus_items_id='{$menuItem->id}' AND estado='A'");
				if ($salonMenuItem == false) {
					if ($singleItem == true) {
						Flash::error('El item no está activo en el ambiente');
					}
					return false;
				}

				$numeroCuenta = 0;
				$concue = $Concue->findFirst("numfol='$numeroFolio' AND codcar='{$salonMenuItem->conceptos_id}'");

				if ($concue != false) {
					$numeroCuenta = $concue->getNumcue();
				} else {
					$numeroCuenta = $Carghab->minimum("numcue", "conditions: numfol='{$numeroFolio}' AND estado='N'");
				}

				if ($numeroCuenta > 0) {
					$cuenta = $Carghab->findFirst("numfol='{$numeroFolio}' AND numcue='{$numeroCuenta}' AND estado='N'");
					if ($cuenta->getExento() == 'S') {
						$conrel = $Conrel->findFirst("codcar='{$salonMenuItem->conceptos_id}'");
						if ($conrel == false) {
							$concepto = $Conceptos->findFirst($salonMenuItem->conceptos_id);
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
					$cliente = $Clientes->findFirst("cedula='{$accountCuenta->clientes_cedula}'");
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

}