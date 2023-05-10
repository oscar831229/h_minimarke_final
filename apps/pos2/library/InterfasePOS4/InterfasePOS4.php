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

class InterfasePOS4 extends UserComponent {

	private $_descargue = array();

	private $_items = array();

	private $_debug = false;

	private $_verbose = false;

	private $_numberErrors = 0;

	private $_errors = [];

	private $_recetap = array();

	private $_recetal = array();

	/**
	 * Variable para almacenar temporalmente las facturas
	 *
	 * @var array
	 */
	private $_facturas = array();

	private $_consolidateCentro = true;

	/**
	 * Constructor de InterfasePOS3
	 *
	 * @param boolean $consolidateCentro
	 */
	public function __construct($consolidateCentro=true){
		$this->_consolidateCentro = $consolidateCentro;
		
	}

	/**
	 * Indica el nivel de verbosidad
	 *
	 * @param boolean $verbose
	 */
	public function setVerbose($verbose){
		$this->_verbose = $verbose;
	}

	/**
	 * Agrega o acumula una cantidad para ser descargada
	 *
	 * @param int $usuarioId
	 * @param int $codigoAlmacen
	 * @param int $centroCosto
	 * @param int $codigoItem
	 * @param double $cantidad
	 * @param double $cantidadu
	 */
	private function _addCantidad($usuarioId, $codigoAlmacen, $centroCosto, $codigoItem, $cantidad, $cantidadu){
		if(!isset($this->_descargue[$usuarioId])){
			$this->_descargue[$usuarioId] = array();
		}
		if(!isset($this->_descargue[$usuarioId][$codigoAlmacen])){
			$this->_descargue[$usuarioId][$codigoAlmacen] = array();
		}
		if($this->_consolidateCentro==true){
			if(!isset($this->_descargue[$usuarioId][$codigoAlmacen][$centroCosto])){
				$this->_descargue[$usuarioId][$codigoAlmacen][$centroCosto] = array();
			}
			if(!isset($this->_descargue[$usuarioId][$codigoAlmacen][$centroCosto][$codigoItem])){
				$this->_descargue[$usuarioId][$codigoAlmacen][$centroCosto][$codigoItem] = array(
					'cantidad' => 0,
					'cantidadu' => 0
				);
			}
			$this->_items[$codigoAlmacen][$centroCosto][$codigoAlmacen] = 1;
			$this->_descargue[$usuarioId][$codigoAlmacen][$centroCosto][$codigoItem]['cantidad']+=$cantidad;
			$this->_descargue[$usuarioId][$codigoAlmacen][$centroCosto][$codigoItem]['cantidadu']+=$cantidadu;
		} else {
			if(!isset($this->_descargue[$usuarioId][$codigoAlmacen][$codigoItem])){
				$this->_descargue[$usuarioId][$codigoAlmacen][$codigoItem] = array(
					'cantidad' => 0,
					'cantidadu' => 0
				);
			}
			$this->_descargue[$usuarioId][$codigoAlmacen][$codigoItem]['cantidad']+=$cantidad;
			$this->_descargue[$usuarioId][$codigoAlmacen][$codigoItem]['cantidadu']+=$cantidadu;
		}
	}

	/**
	 * Obtiene una factura y la cachea para sus futuras consultas
	 *
	 * @param	string $prefac
	 * @param	int $numfac
	 * @return 	Factura
	 */
	private function _getFactura($prefac, $numfac, $fecha = ''){
		if(!isset($this->_facturas[$prefac][$numfac])){
			$conditions = "prefijo_facturacion='{$prefac}' AND consecutivo_facturacion='{$numfac}'";

			if(!empty($fecha))
				$conditions .= " AND fecha='{$fecha}'";

			
			$factura = $this->Factura->findFirst(array($conditions, 'columns' => 'prefijo_facturacion,consecutivo_facturacion,tipo,estado,usuarios_id'));
			$this->_facturas[$prefac][$numfac] = $factura;
		} else {
			$factura = $this->_facturas[$prefac][$numfac];
		}
		return $factura;
	}

	/**
	 * Verifica una factura/orden anulada y la quita de Invepos
	 *
	 * @param Factura $factura
	 * @param Invepos $invepos
	 */
	private function _anulaDescargaFactura($factura, $invepos){
		if($factura->estado=='N'){
			if($this->_verbose==true){
				if($factura->tipo=='O'){
					Flash::notice("La orden de servicio '{$invepos->getPrefac()}':'{$invepos->getNumfac()}' está anulada, no se descarga");
				} else {
					Flash::notice("La factura '{$invepos->getPrefac()}':'{$invepos->getNumfac()}' está anulada, no se descarga");
				}
				$invepos->delete();
			}
		}
	}

	/**
	 * Explota una receta estandar y descarga las referencias que vaya encontrando
	 *
	 * @param int $usuarioId
	 * @param int $codigoAlmacen
	 * @param int $centroCosto
	 * @param int $numeroReceta
	 * @param double $cantidad
	 */
	private function itemsDeReceta($usuarioId, $codigoAlmacen, $centroCosto, $numeroReceta, $cantidad){
		if(!isset($this->_recetap[$numeroReceta])){
			$this->_recetap[$numeroReceta] = $this->Recetap->findFirst("numero_rec='$numeroReceta'");
		}
		$recetap = $this->_recetap[$numeroReceta];
		if($recetap){
			if(!isset($this->_recetal[$numeroReceta])){
				$this->_recetal[$numeroReceta] = $this->Recetal->find("numero_rec='$numeroReceta'");
			}
			$recetals = $this->_recetal[$numeroReceta];
			if(count($recetals)){
				foreach($recetals as $recetal){
					if($recetal->tipol=='I'){
						$acantidad = 0;
						if($recetal->divisor==0){
							Flash::error('El divisor en el item "'.$recetal->item.'" de la receta "'.$recetap->nombre.'" es cero');
							$this->_numberErrors++;
						} else {
							if($recetap->num_personas==0){
								Flash::error('El número de personas de la receta "'.$recetap->nombre.'" es cero');
								$this->_numberErrors++;
							} else {
								$acantidad = ($recetal->cantidad/$recetal->divisor/$recetap->num_personas)*$cantidad;
							}
						}
						$this->_addCantidad($usuarioId, $codigoAlmacen, $centroCosto, $recetal->item, $acantidad, 0);
					} else {
						if($recetal->item!=$numeroReceta){
							$this->itemsDeReceta($usuarioId, $codigoAlmacen, $centroCosto, $recetal->item, ($recetal->cantidad * $cantidad));
						} else {
							Flash::error("La receta '{$recetap->nombre}' es sub-receta de sí misma");
							$this->_numberErrors++;
						}
					}
				}
			} else {
				Flash::error("La receta '{$recetap->nombre}' no tiene ingredientes");
				$this->_numberErrors++;
			}
		} else {
			Flash::error("La receta '$numeroReceta' no existe");
			$this->_numberErrors++;
		}
	}

	/**
	 * Obtiene el array de items que se van a descargar en una determinada fecha
	 *
	 * @param	Date $fechaProceso
	 * @return	array
	 */
	public function getItemsToDownload($fechaProceso){
		$numero = 0;
		foreach($this->Invepos->find("fecha='$fechaProceso' AND estado='N'") as $invepos){
			$factura = $this->_getFactura($invepos->getPrefac(), $invepos->getNumfac(), $fechaProceso);
			if($factura!=false){
				if($factura->estado=='N'){
					$this->_anulaDescargaFactura($factura, $invepos);
					continue;
				}
			} else {
				if($this->_verbose==true){
					Flash::error("La factura '{$invepos->getPrefac()}':'{$invepos->getNumfac()}' no existe");
				}
				$invepos->delete();
				continue;
			}
			$usuarioId = $factura->usuarios_id;
			$codigoAlmacen = $invepos->getAlmacen();
			if($invepos->getTipo()=='I'){
				$this->_addCantidad($usuarioId, $codigoAlmacen, 0, $invepos->getCodigo(), $invepos->getCantidad(), $invepos->getCantidadu());
			} else {
				if($invepos->getTipo()=='R'){
					$this->itemsDeReceta($usuarioId, $codigoAlmacen, 0, $invepos->getCodigo(), $invepos->getCantidad());
				} else {
					if($invepos->getTipo()!='N'){
						$menuItem = $invepos->getMenusItems();
						if($this->_verbose==true){
							if($menuItem){
								Flash::error("El tipo de costo de '{$menuItem->nombre}' es desconocido");
							} else {
								Flash::error("El tipo de costo del item código '{$invepos->menus_items_id}' es desconocido");
							}
						}
						$this->_numberErrors++;
					}
				}
			}
			$numero++;
			if($numero%50==0){
				GarbageCollector::collectCycles();
			}
		}
		return $this->_descargue;
	}

	public function loadItemsToDownload($transaction, $fechaProceso, $type = 'date'){
		
		$numero = 0;
		$this->Invepos->setTransaction($transaction);

		$condictions = '';

		# TIPO DE DESCARGA A REALIZAR AUTOMATICA
		switch ($type) {
			case 'date':
				$condictions = "fecha='$fechaProceso' AND estado='N'";
				break;
			
			case 'invoice':
				$condictions = "prefac='$fechaProceso->prefijo_facturacion' AND numfac='$fechaProceso->consecutivo_facturacion' AND fecha = '$fechaProceso->fecha_facturacion' AND estado='N'";
				$fechaProceso = $fechaProceso->fecha_facturacion;
				$this->Factura->setTransaction($transaction);
				break;
			
			default:
				throw new Exception("No se ha indicado el tipo de descarga, fecha o factura", 1);
				break;
		}


		foreach($this->Invepos->findForUpdate($condictions) as $invepos){
			$factura = $this->_getFactura($invepos->getPrefac(), $invepos->getNumfac(), $fechaProceso);
			if($factura!=false){
				if($factura->estado=='N'){
					if($this->_verbose==true){
						if($factura->tipo=='O'){
							Flash::notice("La orden de servicio '{$invepos->getPrefac()}':'{$invepos->getNumfac()}' está anulada, no se descarga");
						} else {
							Flash::notice("La factura '{$invepos->getPrefac()}':'{$invepos->getNumfac()}' está anulada, no se descarga");
						}
						$invepos->delete();
						continue;
					}
				}
			} else {
				if($this->_verbose==true){
					Flash::error("La factura '{$invepos->getPrefac()}':'{$invepos->getNumfac()}' no existe");
				}
				$invepos->delete();
				continue;
			}

			$usuarioId = 0;
			$codigoAlmacen = $invepos->getAlmacen();
			$centroCosto = $invepos->getCentroCosto();

			# Validar si existe notas credito del producto a descargar
			$cantidadnc = 0;
			$cantidadunc  = 0;
			foreach ($invepos->getInveposnc() as $inveposnc) {
				if($inveposnc->getFecha() == $invepos->getFecha()){
					$cantidadnc += $inveposnc->getCantidad();
					$cantidadunc += $inveposnc->getCantidadu();
				}
			}

			# Suprimimos productos por nota credito
			$cantidad  = $invepos->getCantidad() - $cantidadnc;
			$cantidadu = $invepos->getCantidadu() - $cantidadunc;

			if($invepos->getTipo()=='I'){
				if($cantidad > 0 || $cantidadu > 0)
					$this->_addCantidad($usuarioId, $codigoAlmacen, $centroCosto, $invepos->getCodigo(), $cantidad, $cantidadu);
			} else {
				if($invepos->getTipo()=='R'){
					if($cantidad > 0 || $cantidadu > 0)
						$this->itemsDeReceta($usuarioId, $codigoAlmacen, $centroCosto, $invepos->getCodigo(), $cantidad);
				} else {
					if($invepos->getTipo()!='N'){
						$menuItem = $invepos->getMenusItems();
						if($this->_verbose==true){
							if($menuItem){
								Flash::error("El tipo de costo de '{$menuItem->nombre}' es desconocido");
							} else {
								Flash::error("El tipo de costo del item código '{$invepos->menus_items_id}' es desconocido");
							}
						}

						# CONTROL DE ERRORES DESCARGA POR FACTURA
						if($type == 'invoice'){
							if($menuItem){
								$this->_errors[] = "El tipo de costo de '{$menuItem->nombre}' es desconocido";
							} else {
								$this->_errors[] = "El tipo de costo del item código '{$invepos->menus_items_id}' es desconocido";
							}
						}
						$this->_numberErrors++;
					}
				}
			}

			$invepos->setEstado('S');
			$invepos->setCantidadnc($cantidadnc);
			$invepos->setCantidadunc($cantidadunc);
			if($invepos->save()==false){
				if($this->_verbose==true){
					foreach($invepos->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}
				$transaction->rollback();
			}
			$numero++;
			if($numero%50==0){
				GarbageCollector::collectCycles();
			}
		}
	}

	public function download($onlyDescarga=false, $transaction=null, $strict=false, $fechaProceso=null){

		$this->_numberErrors = 0;
		$controllerRequest = ControllerRequest::getInstance();
		if($fechaProceso==null){
			$fechaProceso = $controllerRequest->getParamPost('fecha', 'date');
		}

		$procesoDefinitivo = $controllerRequest->getParamPost('definitivo', 'onechar');

		if($this->_verbose){
			$datos = $this->Datos->findFirst();
			echo '<h1>Descarga de Inventarios/Comprobante Contable Costo</h1>';
			echo "<h2>", $datos->getNombreHotel(), "</h2>";
			echo '<h3>Fecha Proceso: ', $fechaProceso, '</h3>';
			echo '<h3>Fecha de Impresión: ', Date::now(), '</h3>';
			echo '<br>';
		}

		try {

			if($transaction==null){
				$transaction = TransactionManager::getUserTransaction();
			}

			IdentityManager::mimicUser('admin');

			$this->loadItemsToDownload($transaction, $fechaProceso);

			if($this->_verbose){
				echo '<h3>Referencias a Descargar</h3>
				<table cellspacing="0" class="tablaLista">
				<tr>
					<th>Almacén</th>
					<th>Centro Costo</th>
					<th>Referencia</th>
					<th>Cantidad</th>
					<th>Cantidad Tragos</th>
					<th>Saldo Actual</th>
					<th>Saldo Final</th>
				</tr>';
				foreach($this->_descargue as $usuarioId => $usuarioItems){
					foreach($usuarioItems as $numeroAlmacen => $centroItems){
						$almacen = BackCacher::getAlmacen($numeroAlmacen);
						if($almacen==false){
							Flash::error('El almacen '.$numeroAlmacen.' no existe');
							$this->_numberErrors++;
							break;
						}
						foreach($centroItems as $centroCosto => $items){
							foreach($items as $codigo => $item){
								$inve = BackCacher::getInve($codigo);
								if($inve==false){
									Flash::error('No existe la referencia '.$codigo);
									$this->_numberErrors++;
								} else {
									$saldo = LocaleMath::round(Tatico::getSaldo($codigo, $numeroAlmacen), 2);
									if($saldo-$item['cantidad']<0){
										echo '<tr bgcolor="pink">
											<td align="right">', $numeroAlmacen, '/', $almacen->getNomAlmacen(), '</td>
											<td align="right">', $centroCosto, '</td>
											<td align="left">', $codigo, ' - ', $inve->getDescripcion(), '</td>
											<td align="right">', Currency::number($item['cantidad'], 2), '</td>
											<td align="right">', Currency::number($item['cantidadu'], 2), '</td>
											<td align="right">', Currency::number($saldo, 2), '</td>
											<td align="right">', Currency::number($saldo-$item['cantidad'], 2), '</td>
										</tr>';
									} else {
										echo '<tr bgcolor="#f7f7f7">
											<td align="right">', $numeroAlmacen, '/', $almacen->getNomAlmacen(), '</td>
											<td align="right">', $centroCosto, '</td>
											<td align="left">', $codigo, ' - ', $inve->getDescripcion(), '</td>
											<td align="right">', Currency::number($item['cantidad'], 2), '</td>
											<td align="right">', Currency::number($item['cantidadu'], 2), '</td>
											<td align="right">', Currency::number($saldo, 2), '</td>
											<td align="right">', Currency::number($saldo-$item['cantidad'], 2), '</td>
										</tr>';
									}
								}
							}
						}
					}
				}
				echo '</table>';
			}

			if($this->_numberErrors==0){
				if($procesoDefinitivo!='S'){
					Tatico::setControlStocks(false);
				}
				$comprobs = array();
				$referencias = array();
				foreach($this->_descargue as $usuarioId => $usuarioItems){
					foreach($usuarioItems as $numeroAlmacen => $centroItems){
						foreach($centroItems as $centroCosto => $items){
							try {
								$comprob = sprintf('C%02s', $numeroAlmacen);
								$tatico = new Tatico($comprob, 0, $fechaProceso);
								$movement = array(
									'Comprobante' => $comprob,
									'Fecha' => $fechaProceso,
									'Almacen' => $numeroAlmacen,
									'Tipo' => 'E',
									'CentroCosto' => $centroCosto,
									'NPedido' => 0,
									'Autoriza' => '',
									'Solicita' => '',
									'FechaVencimiento' => $fechaProceso,
									'Estado' => 'C',
									'Observaciones' => 'DESCARGA AUTOMATICA DEL PUNTO DE VENTA DEL DIA '.$fechaProceso,
									'VTotal' => 0
								);
								$addDetail = array();
								foreach($items as $codigo => $item){
									if($item['cantidad']>0){
										$addDetail[] = array(
											'Item' => $codigo,
											'Cantidad' => $item['cantidad'],
											'Valor' => 0
										);
									}
									if($item['cantidadu']>0){
										$addDetail[] = array(
											'Item' => $codigo,
											'CantidadTragos' => $item['cantidadu'],
											'Valor' => 0
										);
									}
									$referencias[$numeroAlmacen][$codigo] = true;
								}
								$movement['Detail'] = $addDetail;
								$movement['removeDetail'] = array();
								$tatico->addMovement($movement);
								$comprobs[] = $tatico->getLastConsecutivos();
							}
							catch(TaticoException $te){
								Flash::error('Inventarios: '.$te->getMessage());
								return;
							}
						}
					}
				}

				if($this->_verbose){
					echo '<h3>Comprobantes Generados</h3>
					<table cellspacing="0" class="tablaLista">
					<tr>
						<th>Comprobante Inventarios</th>
						<th>Comprobante Contable</th>
					</tr>';
					foreach($comprobs as $comprobDetalle){
						echo '<tr>';

						$comprob = BackCacher::getComprob($comprobDetalle['extended']['inve']['comprob']);
						echo '<td>', $comprobDetalle['extended']['inve']['comprob'], ' - ', $comprob->getNomComprob(), ' / ',
						$comprobDetalle['extended']['inve']['numero'], '</td>';

						$comprob = BackCacher::getComprob($comprobDetalle['extended']['contab']['comprob']);
						echo '<td>', $comprobDetalle['extended']['contab']['comprob'], ' - ', $comprob->getNomComprob(), ' / ',
						$comprobDetalle['extended']['contab']['numero'], '</td>';

						echo '</tr>';
					}
					echo '</table>';
				}

				if($this->_verbose){
					echo '<h3>Saldos Resultantes</h3>
					<table cellspacing="0" class="tablaLista">
					<tr>
						<th>Almacén</th>
						<th>Referencia</th>
						<th>Saldo</th>
						<th>Costo</th>
					</tr>';
					foreach($referencias as $numeroAlmacen => $items){
						foreach($items as $codigo => $one){
							$inve = BackCacher::getInve($codigo);
							if($inve==false){
								Flash::error('No existe la referencia '.$codigo);
							} else {
								echo '<tr bgcolor="#f7f7f7">
									<td align="right">', $numeroAlmacen, '</td>
									<td align="left">', $codigo, ' - ', $inve->getDescripcion(), '</td>
									<td align="right">', Currency::number(Tatico::getSaldo($codigo, $numeroAlmacen), 2), '</td>
									<td align="right">', Currency::number(Tatico::getCosto($codigo, 'I', $numeroAlmacen), 2), '</td>
								</tr>';
							}
						}
					}
					echo '</table>';
				}

				# REGISTRAR HISTORICO DE COSTOS SEGUN INVEPOS DESCARGADO
				if($procesoDefinitivo == 'S'){
					$this->registarHistoricoCostosItemsDescargados($transaction, $fechaProceso);
				}

				$controllerRequest = ControllerRequest::getInstance();
				if($procesoDefinitivo=='S'){
					Flash::success('Se realizó la descarga de inventarios correctamente');
					new POSAudit("REALIZÓ LA DESCARGA DE INVENTARIOS DEL $fechaProceso");
					$transaction->commit();
				} else {
					$transaction->rollback();
				}
			} else {
				$transaction->rollback('Por favor corrija las inconsistencias antes de continuar');
			}

		}
		catch(TransactionFailed $e){
			Flash::error($e->getMessage());
		}

	}

	public function registarHistoricoCostosItemsDescargados($transaction, $fechaProceso){

		# Obtener productos y costo en linea
		$costoitemsdescarga = $this->getCostoProductosDescarga($fechaProceso);

		# Registramos historico de costos menus items
		set_time_limit(0);
		$this->HistoricoCostoMenusItems->setTransaction($transaction);

		foreach ($costoitemsdescarga['items'] as $almacen => $items) {

			foreach ($items as $menus_items_id => $item) {

				$controlhistorico = $this->HistoricoCostoMenusItems->findFirst("almacen='{$almacen}' AND menus_items_id='{$menus_items_id}' AND fecha='{$fechaProceso}'");

				if(!$controlhistorico){

					$historico = new HistoricoCostoMenusItems();
					$historico->setTransaction($transaction);
					$historico->almacen = $almacen;
					$historico->menus_items_id = $menus_items_id;
					$historico->codigo_referencia = $item->codigo_referencia;
					$historico->fecha = $fechaProceso;
					$historico->costo = $item->costo;

					if ($historico->save() == false) {
						Flash::error('No se pudo guardar el historico de costos');
						foreach($historico->getMessages() as $message){
							Flash::error(' > '.$message->getMessage());
						}
					}
				}
			}
		}

		# REGISTRAR COSTOS REFERENCIA DESCARGA
		$this->HistoricoCostoReferencia->setTransaction($transaction);

		foreach ($costoitemsdescarga['referencia'] as $almacen => $items) {

			foreach ($items as $codigo_referencia => $item) {

				$controlhistorico = $this->HistoricoCostoReferencia->findFirst("almacen='{$almacen}' AND codigo_referencia='{$codigo_referencia}' AND fecha='{$fechaProceso}'");

				if(!$controlhistorico){

					$historico = new HistoricoCostoReferencia();
					$historico->setTransaction($transaction);
					$historico->almacen = $almacen;
					$historico->codigo_referencia = $codigo_referencia;
					$historico->fecha = $fechaProceso;
					$historico->costo = $item['valor'];

					if ($historico->save() == false) {
						Flash::error('No se pudo guardar el historico de costos referencia');
						foreach($historico->getMessages() as $message){
							Flash::error(' > '.$message->getMessage());
						}
					}
				}
			}
		}

	}

	public function getCostoProductosDescarga($fecha){

		set_time_limit(0);

		$query = new ActiveRecordJoin(array(
			'entities' => array('Invepos', 'MenusItems'),
			'conditions' => "{#Invepos}.fecha = '$fecha'",
			'fields' => array(
				'menus_items_id' => '{#Invepos}.menus_items_id',
				'tipo_costo' => '{#MenusItems}.tipo_costo',
				'nombre' => '{#MenusItems}.nombre',
				'almacen' => '{#Invepos}.almacen',
				'codigo_referencia' => '{#MenusItems}.codigo_referencia',
				'descontar' => '{#MenusItems}.descontar',
				'valor' => '{#MenusItems}.valor',
				'porcentaje_iva' => '{#MenusItems}.porcentaje_iva',
				'porcentaje_impoconsumo' => '{#MenusItems}.porcentaje_impoconsumo',
				'costo' => '{#MenusItems}.costo',
			),
			'order' => '{#Invepos}.id'
		));

		$costo = new CostoInventario();
		$costo->setVerbose(true);
		# Items descargados y costeados
		$arraycosteoitems  = [];

		foreach ($query->getResultSet() as $menuitem) {

			if(isset($arraycosteoitems['items'][$menuitem->almacen][$menuitem->menus_items_id])){
				continue;
			}
			
			# RESET COSTO
			$valorCosto = 0;

			# COSTEO ITEMS I Y R
			if ($menuitem->tipo_costo != "N") {
				
				if ($menuitem->codigo_referencia){

					# Calcular precio de venta
					if ($menuitem->porcentaje_iva > 0) {
						$precioVenta = $menuitem->valor / (1 + ($menuitem->porcentaje_iva/100));
					} else {
						$precioVenta = $menuitem->valor / (1 + ($menuitem->porcentaje_impoconsumo/100));
					}

					# Obtener costo del producto a descagar
					$valorCosto = $costo->obtenerCosto(
						$menuitem->tipo_costo, 
						$menuitem->nombre, 
						$menuitem->codigo_referencia, 
						$menuitem->descontar, 
						$precioVenta,
						$menuitem->almacen
					);

					$valorCosto = LocaleMath::round($valorCosto, 2);

				}

			}

			# REGISTRAMOS EL COSTO MENUS ITMES ALMACEN.
			$arraycosteoitems['items'][$menuitem->almacen][$menuitem->menus_items_id] = (object) [
				'codigo_referencia' => $menuitem->codigo_referencia,
				'costo' => $valorCosto,
			];

		}

		# Costo referencia
		$arraycosteoitems['referencia'] = $costo->getCostosReferencia();

		return $arraycosteoitems;


	}


	public function explodeRecetasDownload($transaction, $fechaProceso){

		$numero = 0;
		$this->Invepos->setTransaction($transaction);
		$recetasExplode = [];

		foreach($this->Invepos->findForUpdate("fecha='$fechaProceso' AND tipo = 'R' AND codigo = '90170'") as $invepos){

			if(isset($recetasExplode[$invepos->getAlmacen()])){
				continue;
			}

			echo "Referencia ".$invepos->getCodigo().'<br>';

			$usuarioId = 0;
			$this->_descargue[$usuarioId] = array();

			$codigoAlmacen = $invepos->getAlmacen();
			$centroCosto = $invepos->getCentroCosto();
			$this->itemsDeReceta($usuarioId, $codigoAlmacen, $centroCosto, $invepos->getCodigo(), $invepos->getCantidad());

			echo json_encode($this->_descargue);
			exit;


		}
	}

	public function downloadInvoice($prefijo_facturacion, $consecutivo_facturacion, $fecha){

		$this->_numberErrors = 0;
		$this->_errors = [];
		$transaction = TransactionManager::getUserTransaction();
		$procesoDefinitivo = 'S';

		# NO CONTROLAR LOS STOCKS DE INVENTARIO
		Tatico::setControlStocks(false);
		
		try {

			IdentityManager::mimicUser('admin');
			$invoice  = new StdClass;
			$invoice->prefijo_facturacion = $prefijo_facturacion;
			$invoice->consecutivo_facturacion = $consecutivo_facturacion;
			$invoice->fecha_facturacion = $fecha;
			$fechaProceso = $fecha;

			$this->loadItemsToDownload($transaction, $invoice, 'invoice');
		
			if($this->_numberErrors==0){
				$comprobs = array();
				$referencias = array();
				foreach($this->_descargue as $usuarioId => $usuarioItems){
					foreach($usuarioItems as $numeroAlmacen => $centroItems){
						foreach($centroItems as $centroCosto => $items){
							try {

								$comprob = sprintf('C%02s', $numeroAlmacen);
								$tatico = new Tatico($comprob, 0, $fechaProceso);
								$tatico->setControlNegatives(false);
								

								$movement = array(
									'Comprobante' => $comprob,
									'Fecha' => $fechaProceso,
									'Almacen' => $numeroAlmacen,
									'Tipo' => 'E',
									'CentroCosto' => $centroCosto,
									'NPedido' => 0,
									'Autoriza' => '',
									'Solicita' => '',
									'FechaVencimiento' => $fechaProceso,
									'Estado' => 'C',
									'Observaciones' => 'DESCARGA AUTOMATICA DEL PUNTO DE VENTA POR FACTURA '.$prefijo_facturacion.'-'.$consecutivo_facturacion.' FECHA:'.$fechaProceso,
									'VTotal' => 0
								);
								$addDetail = array();
								foreach($items as $codigo => $item){
									if($item['cantidad']>0){
										$addDetail[] = array(
											'Item' => $codigo,
											'Cantidad' => $item['cantidad'],
											'Valor' => 0
										);
									}
									if($item['cantidadu']>0){
										$addDetail[] = array(
											'Item' => $codigo,
											'CantidadTragos' => $item['cantidadu'],
											'Valor' => 0
										);
									}
									$referencias[$numeroAlmacen][$codigo] = true;
								}
								$movement['Detail'] = $addDetail;
								$movement['removeDetail'] = array();
								$tatico->addMovement($movement);
								$comprobs[] = $tatico->getLastConsecutivos();
							}
							catch(TaticoException $te){
								throw new Exception('Inventarios: '.$te->getMessage(), 1);
								return;
							}
						}
					}
				}

				$controllerRequest = ControllerRequest::getInstance();
				new POSAudit("REALIZÓ LA DESCARGA DE INVENTARIOS DE LA FACTURA $prefijo_facturacion - $consecutivo_facturacion");

			} else {
				throw new Exception('Por favor corrija las inconsistencias antes de continuar:'.implode('; ', $this->_errors), 1);
			}

		}
		catch(TransactionFailed $e){
			throw new Exception($e->getMessage(), 1);
		}

	}

	public function adjustCreditNote($nota_credito_id){

		$this->_numberErrors = 0;
		$this->_errors = [];
		$transaction = TransactionManager::getUserTransaction();
		$procesoDefinitivo = 'S';
	
		# NO CONTROLAR LOS STOCKS DE INVENTARIO
		Tatico::setControlStocks(false);
		
		try {
			
			$this->NotaCredito->setTransaction($transaction);
			$nota_credito = $this->NotaCredito->findFirst($nota_credito_id);
			$factura = $this->Factura->findFirst($nota_credito->factura_id);

			if(!$nota_credito){
				throw new Exception('No existe la nota credito que realiza el ajuste.', 1);
			}

			IdentityManager::mimicUser('admin');
			$this->loadItemsToCreditNote($transaction, $nota_credito);

			if($this->_numberErrors==0){
				$comprobs = array();
				$referencias = array();
				foreach($this->_descargue as $usuarioId => $usuarioItems){
					foreach($usuarioItems as $numeroAlmacen => $centroItems){
						foreach($centroItems as $centroCosto => $items){
							try {
								$comprob = sprintf('A%02s', $numeroAlmacen);
								$tatico = new Tatico($comprob, 0, $nota_credito->fecha);
								$tatico->setControlNegatives(false);
								$tatico->ajustCreditNote = true;

								$movement = array(
									'Comprobante' => $comprob,
									'Fecha' => $nota_credito->fecha,
									'Almacen' => $numeroAlmacen,
									'CentroCosto' => $centroCosto,
									'NPedido' => 0,
									'Autoriza' => '',
									'Solicita' => '',
									'FechaVencimiento' => $nota_credito->fecha,
									'Estado' => 'C',
									'Observaciones' => 'AJUSTE AUTOMATICA AMBIENTE '.$factura->salon_nombre.' NOTA CREDITO '.$nota_credito->prefijo_documento.'-'.$nota_credito->consecutivo_documento.' FECHA:'.$nota_credito->fecha.' EN LA FACTURA '.$factura->prefijo_facturacion.'-'.$factura->consecutivo_facturacion,
									'VTotal' => 0
								);
								$addDetail = array();
								foreach($items as $codigo => $item){
									if($item['cantidad']>0){
										$addDetail[] = array(
											'Item' => $codigo,
											'Cantidad' => $item['cantidad'],
											'Valor' => 0,
											'Tipo'=> 'SUMAR'
										);
									}
									if($item['cantidadu']>0){
										$addDetail[] = array(
											'Item' => $codigo,
											'CantidadTragos' => $item['cantidadu'],
											'Valor' => 0,
											'Tipo'=> 'SUMAR'
										);
									}
									$referencias[$numeroAlmacen][$codigo] = true;
								}
								$movement['Detail'] = $addDetail;
								$movement['removeDetail'] = array();

								$tatico->addMovement($movement);
								$comprobs[] = $tatico->getLastConsecutivos();
								
							}
							catch(TaticoException $te){
								throw new Exception('Inventarios: '.$te->getMessage(), 1);
								return;
							}
						}
					}
				}

				$controllerRequest = ControllerRequest::getInstance();
				new POSAudit("REALIZÓ EL AJUSTES DE INVENTARIO A LA NOTA CREDITO $nota_credito->prefijo_documento - $nota_credito->consecutivo_documento");

			} else {
				throw new Exception('Por favor corrija las inconsistencias antes de continuar:'.implode('; ', $this->_errors), 1);
			}

		}
		catch(TransactionFailed $e){
			throw new Exception($e->getMessage(), 1);
		}

	}


	public function loadItemsToCreditNote($transaction, $nota_credito){

		$numero = 0;
		$this->Inveposnc->setTransaction($transaction);

		foreach($this->Inveposnc->findForUpdate("nota_credito_id='$nota_credito->id' AND estado='N'") as $invepos){

			$usuarioId = 0;
			$codigoAlmacen = $invepos->getAlmacen();
			$centroCosto = $invepos->getCentroCosto();
			if($invepos->getTipo()=='I'){
				$this->_addCantidad($usuarioId, $codigoAlmacen, $centroCosto, $invepos->getCodigo(), $invepos->getCantidad(), $invepos->getCantidadu());
			} else {
				if($invepos->getTipo()=='R'){
					$this->itemsDeReceta($usuarioId, $codigoAlmacen, $centroCosto, $invepos->getCodigo(), $invepos->getCantidad());
				} else {
					if($invepos->getTipo()!='N'){
						$menuItem = $invepos->getMenusItems();
						if($menuItem){
							$this->_errors[] = "El tipo de costo de '{$menuItem->nombre}' es desconocido";
						} else {
							$this->_errors[] = "El tipo de costo del item código '{$invepos->menus_items_id}' es desconocido";
						}
						$this->_numberErrors++;
					}
				}
			}
			$invepos->setEstado('S');
			if($invepos->save()==false){
				$error = [];
				if($this->_verbose==true){
					foreach($invepos->getMessages() as $message){
						$error[] = $message->getMessage();
					}
				}
				throw new Exception("Error ".imploe(';', $error), 1);
			}
			$numero++;
			if($numero%50==0){
				GarbageCollector::collectCycles();
			}
		}
	}

}
