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

			if ($accountCuenta->clientes_cedula == '0') {
				throw new Exception('El cliente no puede ser particular', 1);
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
			$prefijoFacturacion = $salon->prefijo_facturacion;
			if ($accountCuenta->estado != 'L' && $accountCuenta->estado != 'B') {

				if($accountCuenta->numero > 0){
					$consecutivo = $accountCuenta->numero;
				} else {
					if ($accountCuenta->estado == 'A'){
						if ($tipoVenta != "F") {
							$consecutivo = $accountCuenta->numero = ++$salon->consecutivo_orden;
						} else {
							$consecutivo = $accountCuenta->numero = ++$salon->consecutivo_facturacion;
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

							$valor = $account->valor * $account->cantidad;
							$total = $account->total * $account->cantidad;
							$nombreItem = $menuItem->nombre;

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
										$valor += $modifier->valor;
										$total += $modifier->valor;
									}
								}
								$nombreItem .= PHP_EOL;
							}

							if ($account->descuento > 0) {
								$nombreItem .= " (Descuento {$account->descuento}%)";
								$valor -= ($valor * $account->descuento / 100);
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

					if ($salon->save() == false) {
						$error_save = array();
						foreach ($salon->getMessages() as $message) {
							$error_save[] = $message->getMessage(); 
						}
						throw new Exception(implode(",",$error_save));
						
					}

					$factura = new Factura();
					$factura->setTransaction($transaction);
					$factura->prefijo_facturacion = $prefijoFacturacion;
					$factura->consecutivo_facturacion = $consecutivo;
					$factura->resolucion = $salon->autorizacion;
					$factura->fecha_resolucion = $salon->fecha_autorizacion;
					$factura->tipo = $tipo;
					$factura->numero_inicial = $salon->consecutivo_inicial;
					$factura->numero_final = $salon->consecutivo_final;
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
					throw new Exception('El consecutivo a asignar ya fue utilizado');					
				}
			} else {

				$conditions = "salon_id='$salonId' AND prefijo_facturacion = '$prefijoFacturacion' and consecutivo_facturacion = {$accountCuenta->numero} and tipo = '$tipo'";
				$factura = $this->Factura->findFirst($conditions);
				if ($factura == false) {
					throw new Exception('No existe la factura/orden ' . $prefijoFacturacion . ':' . $accountCuenta->numero);
				} else {
					if ($factura->estado == 'N') {
						throw new Exception('La factura/orden ' . $prefijoFacturacion . ':' . $accountCuenta->numero.' está anulada');
					}
				}
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

}