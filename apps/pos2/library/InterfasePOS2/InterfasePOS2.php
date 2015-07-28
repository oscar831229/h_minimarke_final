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

class InterfasePOS2 extends UserComponent {

	private $_descargue = array();
	private $_items = array();
	private $_debug = false;
	private $_numberErrors = 0;

	private function itemsDeReceta($numeroReceta, $cantidad, $valorVenta, $almacen, $descargo){
		$recetap = $this->Recetap->findFirst("numero_rec='$numeroReceta'");
		if($recetap){
			$recetals = $this->Recetal->find("numero_rec='$numeroReceta'");
			if(count($recetals)){
				foreach($recetals as $recetal){
					if($recetal->tipol=='I'){
						$acantidad = 0;
						if($recetal->divisor==0){
							Flash::error('El divisor en el item "'.$recetal->item.'" de la receta "'.$recetap->nombre.'" es cero');
						} else {
							if($recetap->num_personas==0){
								Flash::error('El número de personas de la receta "'.$recetap->nombre.'" es cero');
							} else {
								$acantidad = ($recetal->cantidad/$recetal->divisor/$recetap->num_personas)*$cantidad;
							}
						}
						if(!isset($this->_descargue[$almacen])){
							$this->_descargue[$almacen] = array();
						}
						if(!isset($this->_descargue[$almacen][$recetal->item])){
							$this->_descargue[$almacen][$recetal->item] = array(
								'cantidad' => 0,
								'cantidadu' => 0,
								'valorv' => $valorVenta,
								'descargo' => $descargo
							);
						}
						$this->_descargue[$almacen][$recetal->item]['cantidad']+=$acantidad;
					} else {
						$this->itemsDeReceta($recetal->item, $cantidad, $valorVenta, $almacen, $descargo);
					}
				}
			} else {
				Flash::error("La receta '{$recetap->nombre}' no tiene ingredientes");
			}
		} else {
			Flash::error("La receta '$numeroReceta' no existe");
		}
	}

	public function descargaReferencia($almacen, $codigo, $cantidad, $cantidadu, $descargo, $fechaProceso){
		$transaction = TransactionManager::getUserTransaction();
		$this->Inve->setTransaction($transaction);
		$this->Saldos->setTransaction($transaction);
		$inve = $this->Inve->findFirst(array("item='$codigo'", "columns" => "item,descripcion,volumen"));
		$this->_items[$almacen][$codigo] = 1;
		if($inve!=false){
			$nuevoCosto = 0;
			$saldo = $this->Saldos->findFirst("almacen='$almacen' AND ano_mes=0 AND item='$codigo'", 'for_update: true');
			if($saldo!=false){
				if($this->_debug){
					Flash::notice('Se van a descargar '.$cantidad);
					Flash::notice("El costo anterior de $codigo-".$inve->getDescripcion()." en el almacen $almacen es $saldo->costo");
				}
				if($saldo->costo<=0){
					$tipoComprob = 'E'.sprintf('%02s', $almacen);
					$movilin = $this->Movilin->findFirst("almacen = '$almacen' AND comprob = '$tipoComprob' AND item = '{$codigo}' AND cantidad > 0", "order: numero DESC");
					if($movilin==false){
						Flash::error("El item '$codigo : ".$inve->getDescripcion()."' ha sido vendido pero no hay entradas en el almacen '$almacen'");
						$this->_numberErrors++;
						$movilin = $this->Movilin->findFirst("almacen = 1 AND comprob = 'E01' AND item = '{$codigo}' AND cantidad > 0", "order: numero DESC");
						if($movilin==false){
							$movilin = $this->Movilin->findFirst("almacen = 1 AND comprob = 'T01' AND item = '{$codigo}' AND cantidad > 0", "order: numero DESC");
							if($movilin==false){
								Flash::error("No se pudo obtener el costo para el item '$codigo' del almacen principal");
								$this->_numberErrors++;
							}
						} else {
							$costo = $movilin->getValor()/$movilin->getCantidad();
							if($this->_debug){
								Flash::notice("El costo se toma de la entrada E01-".$movilin->getNumero()." ".$costo.' para el item '.$inve->getDescripcion());
							}
							$nuevoCosto = (($movilin->getValor()*$cantidad)/$movilin->getCantidad());
							if($cantidadu>0){
								$nuevoCosto = $nuevoCosto/$inve->getVolumen();
							}
							$saldo->costo-=$nuevoCosto;
						}
					} else {
						$nuevoCosto = ($movilin->getValor()*$cantidad/$movilin->getCantidad());
						if($cantidadu>0){
							$nuevoCosto = $nuevoCosto/$inve->getVolumen();
						}
						$saldo->costo-=$nuevoCosto;
					}
				} else {
					if($this->_debug){
						Flash::success('El saldo actual es '.$saldo->saldo);
					}
					if($saldo->saldo>0){
						if($cantidadu>0){
							if($inve->volumen>0){
								$cantidadUnidades = (int)($cantidadu/$inve->getVolumen());
								$cantidadTragos = $cantidadUnidades+(($cantidadu-$cantidadUnidades)/$inve->getVolumen());
								$cantidad += $cantidadTragos;
							} else {
								Flash::error("El item {$inve->getItem()}:'{$inve->getDescripcion()}' se vende por tragos pero no tiene el número de tragos establecido");
								$this->_numberErrors++;
								$nuevoCosto = 0;
							}
						}
						$nuevoCosto = ($saldo->costo*$cantidad)/$saldo->saldo;
						$saldo->costo-=$nuevoCosto;
					} else {
						$nuevoCosto = 0;
					}
				}
				$saldo->saldo-=$cantidad;
				if($saldo->f_u_mov==''){
					$saldo->f_u_mov = $fechaProceso;
				} else {
					if(Date::isLater($fechaProceso, $saldo->f_u_mov)){
						$saldo->f_u_mov = $fechaProceso;
					}
				}
			} else {
				$tipoComprob = 'E'.sprintf('%02s', $almacen);
				$movilin = $this->Movilin->findFirst("almacen = '$almacen' AND comprob = '$tipoComprob' AND item = '{$codigo}' AND cantidad > 0", "order: numero DESC");
				if($movilin==false){
					Flash::error("El item '$codigo' ha sido vendido pero no hay entradas en el almacen '$almacen'");
					$movilin = $this->Movilin->findFirst("almacen = 1 AND comprob = 'E01' AND item = '{$codigo}' AND cantidad > 0", "order: numero DESC");
					if($movilin==false){
						Flash::error("No se pudo obtener el costo para el item '$codigo' del almacen principal");
						$this->_numberErrors++;
					} else {
						$saldo = new Saldos();
						$saldo->setTransaction($transaction);
						$saldo->item = $codigo;
						$saldo->almacen = $almacen;
						$saldo->ano_mes = 0;
						$saldo->consumo = 0;
						$saldo->v_ventas = 0;
						$saldo->v_consumo = 0;
						$saldo->fisico = 0;
						$saldo->ubicacion = '';
						$saldo->f_u_mov = $fechaProceso;
						if($cantidadu>0){
							$cantidadUnidades = (int)($cantidadu/$inve->getVolumen());
							$cantidad += $cantidadUnidades+(($cantidadu-$cantidadUnidades)*$inve->getVolumen());
						}
						$nuevoCosto = -(($movilin->getValor()*$cantidad)/$movilin->getCantidad());
						$saldo->saldo = -$cantidad;
						$saldo->costo = $nuevoCosto;
					}
				} else {
					if($cantidadu>0){
						$cantidadUnidades = (int)($cantidadu/$inve->volumen);
						$cantidad += $cantidadUnidades+(($cantidadu-$cantidadUnidades)*$inve->volumen);
					}
					$saldo = new Saldos();
					$saldo->setTransaction($transaction);
					$saldo->item = $codigo;
					$saldo->almacen = $almacen;
					$saldo->ano_mes = 0;
					$nuevoCosto = -(($movilin->valor*$cantidad)/$movilin->cantidad);
					$saldo->saldo = -$cantidad;
					$saldo->costo = $nuevoCosto;
					$saldo->consumo = 0;
					$saldo->v_ventas = 0;
					$saldo->v_consumo = 0;
					$saldo->fisico = 0;
					$saldo->ubicacion = '';
					$saldo->f_u_mov = $fechaProceso;
				}
			}
			if($descargo=='N'){
				if($saldo!=false){
					if($saldo->save()==false){
						foreach($saldo->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
					if($this->_debug){
						Flash::notice("Descargando $cantidad de $codigo-".$inve->getDescripcion()." del almacen $almacen");
						Flash::notice("El nuevo costo de $codigo-".$inve->getDescripcion()." en el almacen es $saldo->costo");
					}
				}
			}
			return $nuevoCosto;
		} else {
			Flash::error("La referencia '$codigo' no existe");
			return null;
		}
	}

	public function __construct($verbose=true, $onlyDescarga=false, $transaction=null, $strict=false, $fechaProceso=null){

		$this->_numberErrors = 0;
		$controllerRequest = ControllerRequest::getInstance();
		if($fechaProceso==null){
			$fechaProceso = $controllerRequest->getParamPost('fecha', 'date');
		}

		if($verbose){
			$datos = $this->Datos->findFirst();
			echo '<h1>Descarga de Inventarios/Comprobante Contable Costo</h1>';
			echo "<h2>", $datos->getNombreHotel(), "</h2>";
			echo '<h3>Fecha Proceso: ', $fechaProceso, '</h3>';
			echo '<h3>Fecha de Impresión: ', Date::now(), '</h3>';
			echo '<br>';
		}

		$this->_debug = $controllerRequest->getParamQuery('debug');
		try {
			if($transaction==null){
				$transaction = TransactionManager::getUserTransaction();
			}
			$empresa = $this->Empresa->findFirst();
			$fechaCierre = new Date($empresa->getFCierrei());
			$fechaCierre->addMonths(2);
			$fechaCierre = Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear());
			if(Date::compareDates($fechaCierre, $fechaProceso)==-1){
				Flash::error('No es posible hacer la descarga del "'.$fechaProceso.'" porque no se ha cerrado el mes de inventarios');
				$transaction->rollback();
			}

			$connection = $transaction->getConnection();
			$comprobs = array();
			$this->Invepos->setTransaction($transaction);
			if($onlyDescarga==false){
				$conditions = "fecha='$fechaProceso' AND estado='N'";
			} else {
				$conditions = "fecha='$fechaProceso' AND estado='N'";
			}
			foreach($this->Invepos->findForUpdate($conditions) as $invepos){
				$conditions = "prefijo_facturacion='{$invepos->getPrefac()}' AND consecutivo_facturacion='{$invepos->getNumfac()}'";
				$factura = $this->Factura->findFirst($conditions);
				if($factura!=false){
					if($factura->estado=='N'){
						if($factura->tipo=='O'){
							Flash::error("La orden de servicio '{$invepos->getPrefac()}':'{$invepos->getNumfac()}' está anulada, no se descarga");
						} else {
							Flash::error("La factura '{$invepos->getPrefac()}':'{$invepos->getNumfac()}' está anulada, no se descarga");
						}
						$invepos->delete();
						continue;
					}
				}
				if($invepos->getTipo()=='I'){
					if(!isset($this->_descargue[$invepos->getAlmacen()])){
						$this->_descargue[$invepos->getAlmacen()] = array();
					}
					if(!isset($this->_descargue[$invepos->getAlmacen()][$invepos->getCodigo()])){
						$this->_descargue[$invepos->getAlmacen()][$invepos->getCodigo()] = array(
							'cantidad' => 0,
							'cantidadu' => 0,
							'valorv' => 0,
							'descargo' => 'N'
						);
					}
					$this->_items[$invepos->getAlmacen()][$invepos->getCodigo()] = 1;
					$this->_descargue[$invepos->getAlmacen()][$invepos->getCodigo()]['cantidad']+=$invepos->getCantidad();
					$this->_descargue[$invepos->getAlmacen()][$invepos->getCodigo()]['cantidadu']+=$invepos->getCantidadu();
					$this->_descargue[$invepos->getAlmacen()][$invepos->getCodigo()]['descargo'] = 'N';
				} else {
					if($invepos->getTipo()=='R'){
						$this->itemsDeReceta($invepos->getCodigo(), $invepos->getCantidad(), 0, $invepos->getAlmacen(), 'N');
					} else {
						if($invepos->getTipo()!='N'){
							$menuItem = $invepos->getMenusItems();
							if($menuItem){
								Flash::error("El tipo de costo de '{$menuItem->nombre}' es desconocido");
							} else {
								Flash::error("El tipo de costo del item código '{$invepos->menus_items_id}' es desconocido");
							}
							$this->_numberErrors++;
						}
					}
				}
				//$invepos->descargo = 'S';
				if($onlyDescarga==false){	
					$invepos->estado = 'S';
				}
				if($invepos->save()==false){
					foreach($invepos->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
			}

			$consolidaItems = array();
			$interface = array();
			$movilines = array();
			$movihead = array();
			$sumas = array('D' => 0, 'C' => 0);

			if($verbose){
				echo '<h3>Detalle de la Transacción contable</h3>
				<table cellspacing="0" class="tablaLista">
				<tr>
					<th>Comprob</th>
					<th>Centro Costo</th>
					<th>Cuenta</th>
					<th>Tipo Mov.</th>
					<th>Valor</th>
				</tr>';
			}
			foreach($this->_descargue as $numeroAlmacen => $items){
				$tipoComprob = "C".sprintf('%02s', $numeroAlmacen);
				if(!isset($comprobs[$tipoComprob])){
					$comprob = $this->Comprob->findFirst("codigo='$tipoComprob'");
					if($comprob==false){
						Flash::error("No existe el comprobante '$tipoComprob'");
						$transaction->rollback();
					}
					$comprob->consecutivo++;
					$existeMovi = $this->Movilin->count("comprob='$tipoComprob' AND numero='$comprob->consecutivo'");
					if($existeMovi){
						Flash::error("Ya existe el comprobante $tipoComprob-{$comprob->consecutivo}, debe actualizar el consecutivo");
						$transaction->rollback();
					}
					$comprobs[$tipoComprob] = $comprob;
					if($onlyDescarga==false){
						if($comprob->comprob_contab==""){
							Flash::error("Debe definir el comprobante contable en el comprobante $tipoComprob");
							$transaction->rollback();
						}
						if(!isset($comprobs[$comprob->comprob_contab])){
							$comprobContab = $this->Comprob->findFirst("codigo='{$comprob->comprob_contab}'");
							if($comprobContab!=false){
								$comprobContab->consecutivo++;
								$existeMovi = $this->Movi->count("comprob='{$comprobContab->codigo}' AND numero='{$comprobContab->consecutivo}'");
								if($existeMovi){
									Flash::error("Ya existe el comprobante de contabilidad {$comprobContab->codigo}-{$comprobContab->consecutivo}, debe actualizar el consecutivo");
									$transaction->rollback();
								}
								$comprobs[$comprob->comprob_contab] = $comprobContab;
							} else {
								Flash::error("No existe el comprobante contable del comprobante $tipoComprob");
								$transaction->rollback();
							}
						}
					}
					$comprobs[$tipoComprob] = $comprob;
				} else {
					$comprob = $comprobs[$tipoComprob];
				}
				$consecutivo = $comprob->consecutivo;
				$almacen = $this->Almacenes->findFirst("codigo='{$numeroAlmacen}'");
				if($almacen==false){
					Flash::error("El almacen '$numeroAlmacen' no existe");
					$this->_numberErrors++;
				} else {
					$numeroADescargar = 0;
					foreach($items as $item => $datosItem){
						if($datosItem['descargo']=='N'){
							$numeroADescargar++;
						}
					}
					if($numeroADescargar>0){
						$movihead[$tipoComprob][$consecutivo] = array(
							'almacen' => $numeroAlmacen,
							'fecha' => $fechaProceso,
							'centro_costo' => $almacen->getCentroCosto(),
							'v_total' => 0
						);
					}
					foreach($items as $item => $datosItem){
						$nuevoCosto = $this->descargaReferencia($numeroAlmacen, $item, $datosItem['cantidad'], $datosItem['cantidadu'], $datosItem['descargo'], $fechaProceso);
						$cantidad = $datosItem['cantidad'];
						if($datosItem['cantidadu']>0){
							$cantidadu = $datosItem['cantidadu'];
							$inve = $this->Inve->findFirst(array("item='$item'", 'columns' => 'item,descripcion,volumen'));
							if($inve!=false){
								if($inve->volumen>0){
									$cantidadUnidades = (int)($cantidadu/$inve->volumen);
									$cantidad += ($cantidadUnidades+(($cantidadu-$cantidadUnidades)/$inve->volumen));
								} else {
									Flash::error('La referencia "'.$inve->getDescripcion().'" no tiene un número de tragos correcto');
									$this->_numberErrors++;
								}
							} else {
								Flash::error('La referencia "'.$item.'" no existe');
							}
						}
						if($datosItem['descargo']=='N'){
							$movihead[$tipoComprob][$consecutivo]['v_total']+=$nuevoCosto;
							$movilines[] = array(
								'comprob' => $tipoComprob,
								'almacen' => $numeroAlmacen,
								'numero' => $consecutivo,
								'num_linea' => 0,
								'item' => $item,
								'valor' => $datosItem['valorv'],
								'cantidad' => $cantidad,
								'costo' => $nuevoCosto
							);
						}
						$inve = $this->Inve->findFirst("item='{$item}'");
						if($inve==false){
							Flash::error('La referencia "'.$item.'" no existe');
						} else {
							$linea = $this->Lineas->findFirst("almacen='$numeroAlmacen' AND linea='{$inve->linea}'");
							if($linea==false){
								Flash::error('La línea "'.$inve->linea.'" de la referencia "'.$item.'" no existe');
								$this->_numberErrors++;
							} else {
								if($onlyDescarga==false){
									if(!isset($interface[$comprob->comprob_contab])){
										$interface[$comprob->comprob_contab] = array();
									}
									if(!isset($interface[$comprob->comprob_contab][$almacen->getCentroCosto()])){
										$interface[$comprob->comprob_contab][$almacen->getCentroCosto()] = array();
									}
									if(!isset($interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaCostoVenta()])){
										$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaCostoVenta()] = array();
									}
									if(!isset($interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaCostoVenta()]['D'])){
										$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaCostoVenta()]['D'] = array();
										$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaCostoVenta()]['D']['valor'] = 0;
									}
									$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaCostoVenta()]['D']['almacen'] = $numeroAlmacen;
									$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaCostoVenta()]['D']['valor']+=$nuevoCosto;

									if(!isset($interface[$comprob->comprob_contab][$almacen->getCentroCosto()])){
										$interface[$comprob->comprob_contab][$almacen->getCentroCosto()] = array();
									}
									if(!isset($interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaInve()])){
										$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaInve()] = array();
									}
									if(!isset($interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaInve()]['C'])){
										$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaInve()]['C'] = array();
										$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaInve()]['C']['valor'] = 0;
										$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaInve()]['C']['almacen'] = 0;
									}
									$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaInve()]['C']['almacen'] = $numeroAlmacen;
									$interface[$comprob->comprob_contab][$almacen->getCentroCosto()][$linea->getCtaInve()]['C']['valor']+=$nuevoCosto;
								}

								if($verbose){
									echo '<tr>
											<td align="center">', $comprob->comprob_contab, '</td>
											<td align="center">', $almacen->getCentroCosto(), '</td>
											<td>', $linea->getCtaCostoVenta().'</td>
											<td>DÉBITO</td>
											<td align="right">', Currency::number($nuevoCosto, 2), '</td>
										</tr>';

									echo '<tr bgcolor="#f7f7f7">
											<td align="center">', $comprob->comprob_contab, '</td>
											<td align="center">', $almacen->getCentroCosto(), '</td>
											<td>', $linea->getCtaInve(), '</td>
											<td>CRÉDITO</td>
											<td align="right">', Currency::number($nuevoCosto, 2), '</td>
										</tr>';
								}
								$consolidaItems[$inve->item] = true;
							}
						}

					}
				}
			}
			if($verbose){
				echo '</table>';
			}

			foreach($movihead as $comprob => $lineaComprob){
				foreach($lineaComprob as $numero => $linea){
					$this->Comprob->setTransaction($transaction);
					if(!isset($comprobs[$comprob])){
						$comprob = $this->Comprob->findFirst("codigo='$comprob'");
						$comprob->consecutivo++;
						$existeMovi = $this->Movilin->count("comprob='$comprob' AND numero='$comprob->consecutivo'");
						if($existeMovi){
							Flash::error("Ya existe el comprobante $comprob-{$comprob->consecutivo}, debe actualizar el consecutivo");
							$transaction->rollback();
						}
						if($comprob->comprob_contab==""){
							Flash::error("Debe definir el comprobante contable en el comprobante $comprob");
							$transaction->rollback();
						}
						if(!isset($comprobs[$comprob->comprob_contab])){
							$comprobContab = $this->Comprob->findFirst("codigo='{$comprob->comprob_contab}'");
							$comprobContab->consecutivo++;
							$existeMovi = $this->Movi->count("comprob='{$comprobContab->codigo}' AND numero='{$comprobContab->consecutivo}'");
							if($existeMovi){
								Flash::error("Ya existe el comprobante de contabilidad {$comprobContab->codigo}-{$comprobContab->consecutivo}, debe actualizar el consecutivo");
								$transaction->rollback();
							}
							$comprobs[$comprob->comprob_contab] = $comprobContab;
						}
						$comprobs[$comprob] = $comprob;
					} else {
						$comprob = $comprobs[$comprob];
					}
					$movihead = new Movihead();
					$movihead->setTransaction($transaction);
					$movihead->comprob = $comprob->codigo;
					$movihead->almacen = $linea['almacen'];
					$movihead->almacen_destino = $linea['almacen'];
					$movihead->numero = $comprob->consecutivo;
					$movihead->fecha = $fechaProceso;
					$movihead->nit = 0;
					$movihead->centro_costo = $linea['centro_costo'];
					$movihead->n_pedido = 0;
					$movihead->f_vence = '';
					$movihead->f_expira = '';
					$movihead->f_entrega = '';
					$movihead->iva = 0;
					$movihead->ivad = 0;
					$movihead->ivam = 0;
					$movihead->ica = 0;
					$movihead->descuento = 0;
					$movihead->retencion = 0;
					$movihead->saldo = 0;
					$movihead->factura_c = 0;
					$movihead->solicita = 0;
					$movihead->autoriza = 0;
					$movihead->nota = '';
					$movihead->estado = 'E';
					$movihead->v_total = $linea['v_total'];
					if($movihead->save()==false){
						foreach($movihead->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				}
			}

			$i = 1;
			$fecha = Date::getCurrentDate();
			$movilinComprob = array();
			foreach($movilines as $linea){
				/*if($linea['item']=='020'){
					print_r($linea);
				}*/
				$movilin = new Movilin();
				$movilin->setTransaction($transaction);
				$movilinComprob[$linea['comprob']][$linea['numero']] = 1;
				$movilin->setComprob($linea['comprob']);
				$movilin->setAlmacen($linea['almacen']);
				$movilin->setAlmacenDestino($linea['almacen']);
				$movilin->setNumero($linea['numero']);
				$movilin->setNumLinea($i);
				$movilin->setFecha($fechaProceso);
				$movilin->setItem($linea['item']);
				$movilin->setCantidad($linea['cantidad']);
				$movilin->setValor($linea['costo']);
				if($linea['cantidad']!=0){
					$movilin->setCosto($linea['costo']/$linea['cantidad']);
				} else {
					$movilin->setCosto(0);
				}
				$movilin->setCantidadRec(0);
				$movilin->setCantidadDesp(0);
				if($movilin->save()==false){
					foreach($movilin->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
				$i++;
			}

			if($verbose){
				if(count($movilines)){
					echo '<h3>Saldos que fueron/serán Actualizados</h3>';
					$this->Saldos->setTransaction($transaction);
					foreach($this->_items as $almacen => $lineaAlmacen){
						echo '<table cellspacing="0" class="tablaLista"><tr>';
						echo '<th>Referencia</th>';
						echo '<th>Descripción</th>';
						echo '<th>Almacen</th>';
						echo '<th>Saldo</th>';
						echo '<th>Costo</th>';
						foreach($lineaAlmacen as $item => $lineaNumero){
							foreach($this->Saldos->find("almacen='$almacen' AND item='$item' AND ano_mes=0") as $saldo){
								if($saldo->saldo<0){
									echo '<tr bgcolor="pink">';
								} else {
									echo '<tr>';
								}
								$inve = $this->Inve->findFirst("item='{$saldo->item}'", "columns: descripcion,item");
								echo '<td align="right">', $saldo->item, '</td>';
								echo '<td>', utf8_encode($inve->getDescripcion()), '</td>';
								echo '<td align="center">', $saldo->almacen, '</td>';
								echo '<td align="right">', Currency::number($saldo->saldo, 3), '</td>';
								echo '<td align="right">', Currency::number($saldo->costo, 3), '</td>';
								echo '</tr>';
							}
						}
						echo '</table>';
					}
				}

				if(count($movilines)>0){
					echo '<h3>Detalle de la Transacción en Inventarios</h3>';
					$costoTotal = 0;
					$this->Movilin->setTransaction($transaction);
					foreach($movilinComprob as $comprob => $lineaComprob){
						foreach($lineaComprob as $numero => $lineaNumero){
							echo '<table cellspacing="0" class="tablaLista"><tr>';

							echo '<th>Comprob</th>';
							echo '<th>Almacen</th>';
							echo '<th>Número</th>';
							echo '<th>Referencia</th>';
							echo '<th>Descripción</th>';
							echo '<th>Cantidad</th>';
							echo '<th>Costo Unit.</th>';
							echo '<th>Costo Total</th>';

							echo '</tr>';
							$costoTotal = 0;
							$valorTotal = 0;
							foreach($this->Movilin->find("comprob='$comprob' AND numero='$numero'") as $movilin){
								echo '<tr>';
								echo '<td align="center">', $movilin->getComprob(), '</td>';
								echo '<td align="center">', $movilin->getAlmacen(), '</td>';
								echo '<td align="center">', $movilin->getNumero(), '</td>';
								echo '<td align="center">', $movilin->getItem(), '</td>';
								$inve = $this->Inve->findFirst("item='{$movilin->getItem()}'", "colums: item,descripcion");
								if($inve==false){
									Flash::error("La referencia '{$movilin->getItem()}' no existe");
									$this->_numberErrors++;
								} else {
									echo '<td align="left">', utf8_encode($inve->getDescripcion()), '</td>';
									echo '<td align="right">', Currency::number($movilin->getCantidad(), 3), '</td>';
									echo '<td align="right">', Currency::number($movilin->getCosto(), 3), '</td>';
									echo '<td align="right">', Currency::number($movilin->getValor(), 3), '</td>';
									echo '</tr>';
								}
								$costoTotal+=$movilin->getCosto();
								$valorTotal+=$movilin->getValor();
							}
							echo '<tr>';
							echo '<td align="right" colspan="6"><b>TOTAL</b></td>';
							echo '<td align="right">', Currency::number($costoTotal, 3), '</td>';
							echo '<td align="right">', Currency::number($valorTotal, 3), '</td>';
							echo '</table>';
						}
					}
				}
			}

			if($onlyDescarga==false){
				$moviComprob = array();
				foreach($interface as $tipoComprob => $lineaComprob){
					foreach($lineaComprob as $centroCosto => $lineaCentro){
						foreach($lineaCentro as $cuenta => $lineaCuenta){
							foreach($lineaCuenta as $tipoMovimiento => $linea){
								if(isset($comprobs[$tipoComprob])){
									$comprob = $comprobs[$tipoComprob];
								} else {
									Flash::error("No se cargó el tipo de comprobante para la cuenta $cuenta:$tipoComprob");
									$transaction->rollback();
								}
								$moviComprob[$tipoComprob][$comprob->consecutivo] = 1;
								$movi = new Movi();
								$movi->setTransaction($transaction);
								$movi->setComprob($tipoComprob);
								$movi->setNumero($comprob->consecutivo);
								$movi->setFecha($fechaProceso);
								$movi->setCuenta($cuenta);
								$movi->setNit(0);
								$movi->setCentroCosto($centroCosto);
								$movi->setValor(abs($linea['valor']));
								$movi->setDebCre($tipoMovimiento);
								$movi->setDescripcion('SALIDA CONSUMO ALMACEN '.$linea['almacen']);
								$movi->setTipoDoc('');
								$movi->setNumeroDoc(0);
								$movi->setBaseGrab(0);
								$movi->setConciliado(0);
								$movi->setFVence('');
								if($movi->save()==false){
									foreach($movi->getMessages() as $message){
										Flash::error($message->getMessage());
									}
									$transaction->rollback();
								}
							}
						}
					}
				}
				if($verbose){
					echo '<h3>Detalle de la Transacción Contable</h3>';
					$this->Movi->setTransaction($transaction);
					foreach($moviComprob as $comprob => $lineaComprob){
						foreach($lineaComprob as $numero => $lineaNumero){
							echo '<table cellspacing="0" class="tablaLista"><tr>';
							echo '<tr>';
							echo '<th>Comprob</th>';
							echo '<th>Número</th>';
							echo '<th>Fecha</th>';
							echo '<th>Cuenta</th>';
							echo '<th>Centro Costo</th>';
							echo '<th>Valor</th>';
							echo '<th>Descripción</th>';
							echo '<th>Naturaleza</th>';
							echo '</tr>';
							foreach($this->Movi->find("comprob='$comprob' AND numero='$numero'", 'order: deb_cre') as $movi){
								$sumas[$movi->getDebCre()]+=$movi->getValor();
								if($movi->getDebCre()=='D'){
									echo '<tr bgcolor="#f7f7f7">';
								} else {
									echo '<tr>';
								}
								echo '<td align="center">', $movi->getComprob(), '</td>';
								echo '<td align="center">', $movi->getNumero(), '</td>';
								echo '<td align="center">', $movi->getFecha(), '</td>';
								echo '<td align="right">', $movi->getCuenta(), '</td>';
								echo '<td align="center">', $movi->getCentroCosto(), '</td>';
								echo '<td align="right">', Currency::number($movi->getValor(), 3), '</td>';
								echo '<td align="left">', $movi->getDescripcion(), '</td>';
								if($movi->getDebCre()=='D'){
									echo '<td align="left">DEBITO</td>';
								} else {
									echo '<td align="left">CRÉDITO</td>';
								}
								echo '</tr>';
							}
							echo '</table>';
						}
					}
				}
			}

			foreach($comprobs as $comprob){
				$comprob->setTransaction($transaction);
				if($comprob->save()==false){
					foreach($comprob->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
			}

			if($verbose){
				echo '<table class="tablaLista" cellspacing="0">
					<tr>
						<td align="right"><b>DÉBITOS</b></td>
						<td align="right">', Currency::number($sumas['D'], 3).'</td>
						<td align="right"><b>CRÉDITOS</b></td>
						<td align="right">', Currency::number($sumas['C'], 3).'</td>
					</tr>
				</table>';
			}


			if(count($consolidaItems)>0){
				if($verbose){
					echo '<h3>Saldos Consolidados</h3>
					<table cellspacing="0" class="tablaLista">
					<tr>
						<th>Referencia</th>
						<th>Nombre</th>
						<th>Saldo Consolidado</th>
						<th>Costo Consolidado</th>
					</tr>';
				}
				ksort($consolidaItems);
				foreach($consolidaItems as $item => $one){
					$inve = $this->Inve->findFirst("item='$item'");
					if($inve!=false){
						$costoTotal = 0;
						$saldoTotal = 0;
						foreach($this->Saldos->find("ano_mes=0 AND item='$item'") as $saldo){
							$saldoTotal+=$saldo->saldo;
							$costoTotal+=$saldo->costo;
						}
						$inve->saldo_actual = $saldoTotal;
						$inve->costo_actual = $costoTotal;
						if($inve->save()==false){
							foreach($inve->getMessages() as $message){
								Flash::error($message->getMessage());
							}
						}
						if($verbose){
							echo '<tr>
								<td>', $inve->item, '</td>
								<td>', utf8_encode($inve->getDescripcion()), '</td>
								<td align="right">', Currency::number($saldoTotal, 3), '</td>
								<td align="right">', Currency::number($costoTotal, 3), '</td>
							</tr>';
						}
					} else {
						Flash::error('No existe la referencia "'.$item.'"');
					}
				}
				if($verbose){
					echo '</table>';
				}
			}

			if($strict==true&&$this->_numberErrors>0){
				$transaction->rollback();
			}
			if($onlyDescarga==false){
				$controllerRequest = ControllerRequest::getInstance();
				if($controllerRequest->getParamPost('definitivo')=='S'){
					new POSAudit("REALIZÓ LA DESCARGA DE INVENTARIOS DEL $fechaProceso");
					$transaction->commit();
				} else {
					$transaction->rollback();
				}
			}
		}
		catch(TransactionFailed $e){
			Flash::error($e->getMessage());
		}

	}

}
