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
 * FacturasController
 *
 * Controlador de Facturas
 *
 */
class FacturasController extends ApplicationController
{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{
		$this->setParamToView('message', 'Agregue referencias a la factura y haga click en "Generar"');
		$this->setParamToView('formasPago', $this->FormaPago->find(array("estado='A'")));

		$tipoPrecio = Settings::get('tipo_precio');
		if ($tipoPrecio=='P') {
			$this->setParamToView('tiposPrecio', array('P' => 'PORCENTAJE', 'N' => 'NETO'));
		} else {
			$this->setParamToView('tiposPrecio', array('N' => 'NETO', 'P' => 'PORCENTAJE'));
		}

		$this->setParamToView('refConc', array('R' => 'Referencia', 'C' => 'Concepto'));

		$fecha = new Date();
		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierreI();
		$fechaCierre->addMonths(1);
		if (Date::isLater($fecha, $fechaCierre)) {
			Tag::displayTo('fecha', $fechaCierre->getDate());
		} else {
			Tag::displayTo('fecha', $fecha->getDate());
		}

		$fecha->addDays(31);
		Tag::displayTo('fechaVencimiento', $fecha->getDate());
		Tag::displayTo('descuentoGeneral', 0);

	}


	public function generarAction()
	{
		$this->setResponse('json');

		$transaction = TransactionManager::getUserTransaction();

		try {
			
			$empresa = EntityManager::get('Empresa')->findFirst();
			$nits = EntityManager::get('Nits')->findFirst($empresa->getNit());
			$regimenCuentas = EntityManager::get('RegimenCuentas')->findFirst(array('conditions'=> "regimen='{$nits->getEstadoNit()}'"));
			
			if ($regimenCuentas==false) {
				$transaction->rollback('No se encontro datos en regimen cuentas');
			} else {
				if (!$regimenCuentas->getCtaIva16v()) {
					$transaction->rollback("La cuenta del regimen '{$nits->getEstadoNit()}' de ventas del 16 no esta definida");	
				}
				if (!$regimenCuentas->getCtaIva10v()) {
					$transaction->rollback("La cuenta del regimen '{$nits->getEstadoNit()}' de ventas del 10 no esta definida");	
				}
			}
			
			$nitDocumento = $this->getPostParam('nitFacturar', 'terceros');
			$nitEntregarDocumento = $this->getPostParam('nitEntregar', 'terceros');
			
			$fechaFactura = $this->getPostParam('fecha', 'date');
			$fechaVencimiento = $this->getPostParam('fechaVencimiento', 'date');

			$refConc = $this->getPostParam('refConc', 'alpha');	
			$items = $this->getPostParam('item', 'alpha');

			$precios = $this->getPostParam('precio', 'double');
			$cantidades = $this->getPostParam('cantidad', 'float');
			$descuentoGeneral = $this->getPostParam('descuentoGeneral', 'float');
			$itemIvaVenta = $this->getPostParam('itemIvaVenta', 'float');
			
			$formasPago = $this->getPostParam('formaPago', 'int');
			$valoresFormas = $this->getPostParam('valorForma', 'double');

			$options = array(
				'apps'					=> 'IN',
				'refConc'				=> $refConc,
				'items' 				=> $items,
				'precios' 				=> $precios,
				'cantidades' 			=> $cantidades,
				'descuentoGeneral' 		=> $descuentoGeneral,
				'itemIvaVenta'			=> $itemIvaVenta,
				'formasPago' 			=> $formasPago,
				'valoresFormas' 		=> $valoresFormas,
				'nitDocumento' 			=> $nitDocumento,
				'nitEntregarDocumento' 	=> $nitEntregarDocumento,
				'fechaVencimiento' 		=> $fechaVencimiento,
				'fechaFactura'			=> $fechaFactura,
				'ctaIva16v'				=> $regimenCuentas->getCtaIva16v(),
				'ctaIva10v'				=> $regimenCuentas->getCtaIva10v()
			);

			Core::importFromLibrary('Hfos/Invoicing','Invoicing.php');

			$invoicingIN = Invoicing::factory('IN'); //invoicer to inve
			$invoicingIN->addInvoicer($options);
			$factura = $options['factura'];

			if ($factura==false) {
				return array(
					'status' => 'FAILED',
					'message' => 'No genero factura invoicer'
				);
			}

			$transaction->commit();


			$fileName = $invoicingIN->getPrint($factura->getId());

			return array(
				'status'	=> 'OK',
				'message'	=> 'Se generó la factura correctamente',
				'uri'		=> $fileName
			);

		} catch(TransactionFailed $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

	public function generar2Action()
	{
		$this->setResponse('json');

		try {

			$transaction = TransactionManager::getUserTransaction();

			$codigoAlmacen = Settings::get('almacen_venta');
			if (!$codigoAlmacen) {
				$transaction->rollback('No se ha configurado el almacén donde se descargan las referencias de la factura');
			} else {
				$almacen = BackCacher::getAlmacen($codigoAlmacen);
				if ($almacen==false) {
					$transaction->rollback('El almacén donde se descargan las referencias de la factura configurado no existe');
				}
			}

			$consecutivoId = Settings::get('consecutivo_factura');
			if (!$consecutivoId) {
				$transaction->rollback('No se ha configurado el consecutivo de facturación');
			} else {
				$consecutivo = BackCacher::getConsecutivo($consecutivoId);
				if ($consecutivo==false) {
					$transaction->rollback('El consecutivo de facturación configurado no existe');
				}
			}

			$codigoComprob = Settings::get('comprob_ventas');
			if ($codigoComprob=='') {
				$transaction->rollback('No se ha configurado el comprobante de contabilización de la venta');
			}

			$comprob = BackCacher::getComprob($codigoComprob);
			if ($comprob==false) {
				$transaction->rollback('El comprobante '.$codigoComprob.' no existe');
			}

			$nitDocumento = $this->getPostParam('nitFacturar', 'terceros');
			if ($nitDocumento=='') {
				$transaction->rollback('Indique el tercero al que se le generará la factura');
			} else {
				$tercero = BackCacher::getTercero($nitDocumento);
				if ($tercero==false) {
					$transaction->rollback('No existe el tercero con número de documento "'.$nitDocumento.'"');
				}
			}

			$nitEntregarDocumento = $this->getPostParam('nitEntregar', 'terceros');
			if ($nitEntregarDocumento=='') {
				$transaction->rollback('Indique el tercero al que se le entregará la mercancía');
			} else {
				$terceroEntregar = BackCacher::getTercero($nitEntregarDocumento);
				if ($terceroEntregar==false) {
					$transaction->rollback('No existe el tercero con número de documento "'.$nitEntregarDocumento.'"');
				}
			}

			$fechaFactura = $this->getPostParam('fecha', 'date');
			$fechaVencimiento = $this->getPostParam('fechaVencimiento', 'date');

			try {
				if (Date::isLater($fechaFactura, $fechaVencimiento)) {
					$transaction->rollback('La fecha de vencimiento no puede ser menor a la fecha de la factura');
				}
			} catch(DateException $e) {
				$transaction->rollback($e->getMessage());
			}

			$totalFactura = 0;
			$detalles = array();
			$movimiento = array();
			$resumenIva = array('0' => 0, '10' => 0, '16' => 0);
			$resumenVenta = array('0' => 0, '10' => 0, '16' => 0);
			$items = $this->getPostParam('item', 'alpha');
			if (count($items)) {
				$precios = $this->getPostParam('precio', 'double');
				$cantidades = $this->getPostParam('cantidad', 'float');
				$descuentos = $this->getPostParam('descuento', 'float');
				foreach($items as $n => $item) {
					if ($item!=null) {

						$inve = BackCacher::getInve($item);
						if ($inve==false) {
							$transaction->rollback('La referencia con código "'.$item.'" no existe, en la línea '.($n+1));
						}

						$linea = BackCacher::getLinea($codigoAlmacen, $inve->getLinea());
						if ($linea==false) {
							$transaction->rollback('La línea de producto "'.$inve->getLinea().'" no existe en el almacén "'.$codigoAlmacen.'", en la línea '.($n+1));
						}

						$cuentaVenta = BackCacher::getCuenta($linea->getCtaVenta());
						if ($cuentaVenta==false) {
							$transaction->rollback('La cuenta de venta no existe, para la línea de producto "'.$linea->getNombre().'" de la referencia "'.$inve->getDescripcion().'", en la línea '.($n+1));
						}

						if ($cantidades[$n]<=0) {
							$transaction->rollback('La cantidad debe ser mayor o igual a cero en la línea '.($n+1));
						}

						if ($precios[$n]<=0) {
							$transaction->rollback('El precio debe ser mayor o igual a cero en la línea '.($n+1));
						}

						if ($descuentos[$n]>0) {
							$precios[$n] = LocaleMath::round($precios[$n]-($precios[$n]*$descuentos[$n]/100), 0);
						}

						$codigoCuentaIva = null;

						if ($inve->getIvaVenta()===null) {
							$transaction->rollback('No se ha definido el porcentaje de IVA de venta de la referencia '.$inve->getDescripcion().', en la línea '.($n+1));
						}

						if ($inve->getIvaVenta()>0) {
							$baseIva = LocaleMath::round(($precios[$n]/(1+$inve->getIvaVenta()/100))*$cantidades[$n], 0);
							$iva = $precios[$n]*$cantidades[$n]-$baseIva;
							if ($inve->getIvaVenta()==16||$inve->getIvaVenta()==10) {
								if ($inve->getIvaVenta()==16) {
									$codigoCuentaIva = $comprob->getCtaIva16Venta();
								} else {
									$codigoCuentaIva = $comprob->getCtaIva10Venta();
								}
								$cuentaIva = BackCacher::getCuenta($codigoCuentaIva);
								if ($cuentaIva==false) {
									$transaction->rollback('La cuenta de contabilización del IVA del '.$inve->getIvaVenta().'% configurada en el comprobante de facturación no existe');
								} else {
									if ($cuentaIva->getEsAuxiliar()!='S') {
										$transaction->rollback('La cuenta de contabilización del IVA del '.$inve->getIvaVenta().'% configurada en el comprobante de facturación no es auxiliar');
									}
								}
							} else {
								$transaction->rollback('La facturación no está soportada para IVA del '.$inve->getIvaVenta().'%');
							}
						} else {
							$baseIva = LocaleMath::round($precios[$n]*$cantidades[$n], 0);
							$iva = 0;
						}

						if (!isset($resumenVenta[$inve->getIvaVenta()])) {
							$resumenVenta[$inve->getIvaVenta()] = 0;
						}
						$resumenVenta[$inve->getIvaVenta()]+=$baseIva;

						if (!isset($resumenIva[$inve->getIvaVenta()])) {
							$resumenIva[$inve->getIvaVenta()] = 0;
						}
						$resumenIva[$inve->getIvaVenta()]+=$iva;

						$detalles[] = array(
							'Item' => $item,
							'Descripcion' => $inve->getDescripcion(),
							'Cantidad' => $cantidades[$n],
							'Descuento' => $descuentos[$n],
							'CuentaVenta' => $cuentaVenta->getCuenta(),
							'BaseIva' => $baseIva,
							'PorcentajeIva' => $inve->getIvaVenta(),
							'CuentaIva' => $codigoCuentaIva,
							'Iva' => $iva,
							'Valor' => $precios[$n]
						);

						$totalFactura+=LocaleMath::round($precios[$n]*$cantidades[$n], 0);
					}
				}

				if (!count($detalles)) {
					$transaction->rollback('Agregue primero referencias a la factura antes de generarla');
				}

				$pagos = array();
				$totalFormas = 0;
				$detalleFormas = array();
				$formasPago = $this->getPostParam('formaPago', 'int');
				$valoresFormas = $this->getPostParam('valorForma', 'double');
				if (count($formasPago)) {
					foreach($formasPago as $n => $codigoFormaPago) {
						if ($codigoFormaPago) {
							$formaPago = BackCacher::getFormaPago($codigoFormaPago);
							if ($formaPago==false) {
								$transaction->rollback('La forma de pago no existe en la línea '.($n+1));
							} else {
								$cuentaForma = BackCacher::getCuenta($formaPago->getCtaContable());
								if ($cuentaForma==false) {
									$transaction->rollback('La cuenta de contabilización no existe en la forma de pago '.($n+1));
								} else {
									if ($cuentaForma->getEsAuxiliar()!='S') {
										$transaction->rollback('La cuenta de contabilización no es auxiliar en la forma de pago '.($n+1));
									}
								}
							}
							$detalleFormas[] = array(
								'Codigo' => $formaPago->getCodigo(),
								'Descripcion' => $formaPago->getDescripcion(),
								'Cuenta' => $formaPago->getCtaContable(),
								'Valor' => $valoresFormas[$n]
							);
							$totalFormas+=$valoresFormas[$n];
						}
					}
				}

				if ($totalFormas!=$totalFactura) {
					$transaction->rollback('Las formas de pago no son igual al total de la factura TotalFactura='.$totalFactura.' TotalFormas='.$totalFormas);
				}

				try {
					$comprobInve = sprintf('C%02s', $codigoAlmacen);
					$tatico = new Tatico($comprobInve, 0);
					$movement = array(
						'Comprobante' => $comprobInve,
						'Fecha' => $fechaFactura,
						'Almacen' => $codigoAlmacen,
						'Tipo' => 'E',
						'CentroCosto' => $almacen->getCentroCosto(),
						'NPedido' => 0,
						'Estado' => 'C',
						'Observaciones' => 'SALIDA POR VENTA EN FACTURA '.$consecutivo->getPrefijo().' '.$consecutivo->getNumeroActual()
					);
					$details = array();
					foreach($detalles as $detalle) {
						$details[] = array(
							'Item' => $detalle['Item'],
							'Cantidad' => $detalle['Cantidad'],
							'Valor' => 0
						);
					}
					$movement['Detail'] = $details;
					$tatico->addMovement($movement);
				} catch(TaticoException $e) {
					$transaction->rollback('Descargando de Inventarios: '.$e->getMessage());
				}

			} else {
				$transaction->rollback('Agregue primero referencias a la factura antes de generarla');
			}

			try {
				$aura = new Aura($codigoComprob, 0, $fechaFactura, Aura::OP_CREATE);
				foreach($detalles as $detalle) {
					$aura->addMovement(array(
						'Descripcion' => $detalle['Descripcion'],
						'Nit' => $nitDocumento,
						'CentroCosto' => $almacen->getCentroCosto(),
						'Cuenta' => $detalle['CuentaVenta'],
						'Valor' => $detalle['BaseIva'],
						'BaseGrab' => $detalle['BaseIva'],
						'TipoDocumento' => 'FAC',
						'NumeroDocumento' => $consecutivo->getNumeroActual(),
						'FechaVence' => $fechaVencimiento,
						'DebCre' => 'C'
					));
					$aura->addMovement(array(
						'Descripcion' => 'IVA '.$detalle['Descripcion'],
						'Nit' => $nitDocumento,
						'CentroCosto' => $almacen->getCentroCosto(),
						'Cuenta' => $detalle['CuentaIva'],
						'Valor' => $detalle['Iva'],
						'BaseGrab' => $detalle['BaseIva'],
						'TipoDocumento' => 'FAC',
						'NumeroDocumento' => $consecutivo->getNumeroActual(),
						'FechaVence' => $fechaVencimiento,
						'DebCre' => 'C'
					));
				}
				foreach($detalleFormas as $detalleForma) {
					$aura->addMovement(array(
						'Descripcion' => $detalleForma['Descripcion'],
						'Nit' => $nitDocumento,
						'CentroCosto' => $almacen->getCentroCosto(),
						'Cuenta' => $detalleForma['Cuenta'],
						'Valor' => $detalleForma['Valor'],
						'BaseGrab' => 0,
						'TipoDocumento' => 'FAC',
						'NumeroDocumento' => $consecutivo->getNumeroActual(),
						'FechaVence' => $fechaVencimiento,
						'DebCre' => 'D'
					));
				}
				$aura->save();
			} catch(AuraException $e) {
				$transaction->rollback('Contabilidad: '.$e->getMessage());
			}

			$taticoConsecutivos = $tatico->getLastConsecutivos();

			$factura = new Facturas();
			$factura->setTransaction($transaction);
			$factura->setConsecutivosId($consecutivo->getId());
			$factura->setPrefijo($consecutivo->getPrefijo());
			$factura->setNumero($consecutivo->getNumeroActual());
			$factura->setResolucion($consecutivo->getResolucion());
			$factura->setFechaResolucion((string)$consecutivo->getFechaResolucion());
			$factura->setNumeroInicial($consecutivo->getNumeroInicial());
			$factura->setNumeroFinal($consecutivo->getNumeroFinal());
			$factura->setNit($nitDocumento);
			$factura->setNombre($tercero->getNombre());
			$factura->setDireccion($tercero->getDireccion());
			$factura->setNitEntregar($nitEntregarDocumento);
			$factura->setNombreEntregar($terceroEntregar->getNombre());
			$factura->setDireccionEntregar($terceroEntregar->getDireccion());
			$factura->setFechaEmision($fechaFactura);
			$factura->setFechaVencimiento($fechaVencimiento);
			$factura->setNotaFactura($consecutivo->getNotaFactura());
			$factura->setNotaIca($consecutivo->getNotaIca());
			$factura->setVenta16($resumenVenta[16]);
			$factura->setVenta10($resumenVenta[10]);
			$factura->setVenta0($resumenVenta[0]);
			$factura->setIva16($resumenIva[16]);
			$factura->setIva10($resumenIva[10]);
			$factura->setIva0($resumenIva[0]);
			$factura->setPagos($totalFormas);
			$factura->setTotal($totalFactura);
			$factura->setComprobInve($comprobInve);
			$factura->setNumeroInve($taticoConsecutivos['inve']);
			$factura->setComprobContab($codigoComprob);
			$factura->setNumeroContab($aura->getConsecutivo());
			$factura->setEstado('A');
			if ($factura->save()==false) {
				foreach($factura->getMessages() as $message) {
					$transaction->rollback('Factura: '.$message->getMessage());
				}
			}

			foreach($detalles as $detalle) {
				$facturaDetalle = new FacturasDetalle();
				$facturaDetalle->setTransaction($transaction);
				$facturaDetalle->setFacturasId($factura->getId());
				$facturaDetalle->setItem($detalle['Item']);
				$facturaDetalle->setDescripcion($detalle['Descripcion']);
				$facturaDetalle->setCantidad($detalle['Cantidad']);
				$facturaDetalle->setDescuento($detalle['Descuento']);
				$facturaDetalle->setValor($detalle['BaseIva']);
				$facturaDetalle->setIva($detalle['Iva']);
				$facturaDetalle->setTotal($detalle['Valor']);
				if ($facturaDetalle->save()==false) {
					foreach($facturaDetalle->getMessages() as $message) {
						$transaction->rollback('Factura-Detalle: '.$message->getMessage().print_r($detalle, true));
					}
				}
			}

			foreach($detalleFormas as $detalleForma) {
				$pagosDetalle = new FacturasPagos();
				$pagosDetalle->setTransaction($transaction);
				$pagosDetalle->setFacturasId($factura->getId());
				$pagosDetalle->setFormaPago($detalleForma['Codigo']);
				$pagosDetalle->setDescripcion($detalleForma['Descripcion']);
				$pagosDetalle->setValor($detalleForma['Valor']);
				if ($pagosDetalle->save()==false) {
					foreach($pagosDetalle->getMessages() as $message) {
						$transaction->rollback('Factura-Pagos: '.$message->getMessage().print_r($detalleForma, true));
					}
				}
			}

			$consecutivo->setNumeroActual($consecutivo->getNumeroActual()+1);
			if ($consecutivo->save()==false) {
				foreach($consecutivo->getMessages() as $message) {
					$transaction->rollback('Consecutivo: '.$message->getMessage());
				}
			}

			$transaction->commit();

			$invoicing = new Invoicing();
			$fileName = $invoicing->getPrint($factura->getId());

			return array(
				'status' => 'OK',
				'message' => 'Se generó la factura correctamente',
				'uri' => $fileName
			);

		} catch(TransactionFailed $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}

	}

	public function queryByItemAction()
	{
		$this->setResponse('json');
		$numeroItem = $this->getQueryParam('codigo', 'alpha');
		$inve = $this->Inve->findFirst("item='$numeroItem'");
		if ($inve==false) {
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el item "'.$numeroItem.'"'
			);
		} else {
			return array(
				'status' => 'OK',
				'nombre' => $inve->getDescripcion(),
				'precio' => $inve->getPrecioVentaM(),
				'iva' => $inve->getIvaVenta()
			);
		}
	}

	public function anularAction()
	{
		$this->setResponse('json');
		
		try {

			$transaction = TransactionManager::getUserTransaction();

			$facturaId = $this->getQueryParam('codigo', 'int');

			if (!$facturaId) {
				$transaction->rollback('Por favor envie codigo');
			}

			$facturas = $this->Facturas->setTransaction($transaction)->findFirst($facturaId);

			if ($facturas==false) {
				$transaction->rollback('No existe la factura '.$facturaId);
			}

			$facturasDetalle = $this->FacturasDetalle->setTransaction($transaction)->find(array('conditions'=>"facturas_id='$facturaId'"));

			if (!count($facturasDetalle)) {
				$transaction->rollback('No se encontro detalle de la factura '.$facturaId);
			}


			///Borramos en contab
			try {
			    $aura = new Aura($facturas->getComprobContab(), $facturas->getNumeroContab());
			    $aura->delete();
			}
			catch(AuraException $e) {
			        $transaction->rollback('Aura: '.$e->getMessage());
			}


			//Borramos en Inve
			try {
				
				$comprobInve = 'A01';
				$codigoAlmacen = Settings::get('almacen_venta');
				$fechaFactura = $factura->getFechaEmision();
				$almacen = $this->Almacen->setTransaction($transaction)->findFirst($codigoAlmacen);

				$tatico = new Tatico($comprobInve, 0, '', $transaction);
				$movement = array(
					'Comprobante' => $comprobInve,
					'Fecha' => $fechaFactura,
					'Almacen' => $codigoAlmacen,
					'Tipo' => 'E',
					'CentroCosto' => $almacen->getCentroCosto(),
					'NPedido' => 0,
					'Estado' => 'C',
					'Observaciones' => 'AJUSTE POR ANULACION DE FACTURA '.$facturas->getComprobInve().' '.$facturas->getNumeroInve()
				);
				$details = array();

				foreach($facturasDetalle as $detalle) {
					$details[] = array(
						'Item' => $detalle->getItem(),
						'Cantidad' => $detalle->getCantidad(),
						'Tipo'	=> 'SUMAR',
						'Valor' => $detalle->getValor()
					);
				}

				$movement['Detail'] = $details;
				$tatico->addMovement($movement);

			}
			catch(TaticoException $e) {
				$transaction->rollback('Tatico: '.$e->getMessage());
			}

			return array(
				'status' => 'OK',
				'message' => 'Se anulo la factura correctamente '.$facturaId,
				'uri' => $fileName
			);

		} catch(TransactionFailed $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		
	}

}
