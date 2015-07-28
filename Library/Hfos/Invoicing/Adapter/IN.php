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
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * Class Invoicing to IN (inve)
 *
 * Add/Print Invoicers
 *
 */
class InvoicingIN extends UserComponent {

	/**
	 * @var string $app
	 */
	private $_apps = 'IN'; //inve

	/**
	 * Transacción para grabar los movimientos
	 *
	 * @var ActiveRecordTransaction
	 */
	private $_transaction;

	public function __construct(){
		require_once 'Library/Mpdf/mpdf.php';
		$this->_transaction = TransactionManager::getUserTransaction();
	}

	/**
	* Validate a Invoicer
	*
	* @param array $options
	*/
	private function _validateInvoicer(&$options){

		//Validaciones globales
		$consecutivoId = Settings::get('consecutivo_factura');
		if(!$consecutivoId){
			$this->_transaction->rollback('No se ha configurado el consecutivo de facturación');
		} else {
			$consecutivo = BackCacher::getConsecutivo($consecutivoId);
			if($consecutivo==false){
				$this->_transaction->rollback('El consecutivo de facturación configurado no existe');
			}
		}
		$options['consecutivoId'] 	= $consecutivoId;
		$options['consecutivo']		= $consecutivo;

		$nitDocumento = $options['nitDocumento'];
		if($nitDocumento==''){
			$this->_transaction->rollback('Indique el tercero al que se le generará la factura');
		} else {
			$tercero = BackCacher::getTercero($nitDocumento);
			if($tercero==false){
				$this->_transaction->rollback('No existe el tercero con número de documento "'.$nitDocumento.'"');
			}
		}
		$options['nitDocumento'] = $nitDocumento;
		$options['tercero'] 	 = $tercero;

		$fechaFactura = $options['fechaFactura'];
		$fechaVencimiento = $options['fechaVencimiento'];

		try {
			if(Date::isLater($fechaFactura, $fechaVencimiento)){
				$this->_transaction->rollback('La fecha de vencimiento no puede ser menor a la fecha de la factura');
			}
		}
		catch(DateException $e){
			$this->_transaction->rollback($e->getMessage());
		}
		$options['fechaFactura'] = $fechaFactura;
		$options['fechaVencimiento'] = $fechaVencimiento;

		$codigoAlmacen = Settings::get('almacen_venta');
		if(!$codigoAlmacen){
			$this->_transaction->rollback('No se ha configurado el almacén donde se descargan las referencias de la factura');
		} else {
			$almacen = BackCacher::getAlmacen($codigoAlmacen);
			if($almacen==false){
				$this->_transaction->rollback('El almacén donde se descargan las referencias de la factura configurado no existe');
			}
		}
		$options['codigoAlmacen'] 	= $codigoAlmacen;
		$options['almacen'] 		= $almacen;

		$codigoComprob = Settings::get('comprob_ventas');
		if($codigoComprob==''){
			$this->_transaction->rollback('No se ha configurado el comprobante de contabilización de la venta');
		}
		$options['codigoComprob'] = $codigoComprob;

		$comprob = BackCacher::getComprob($codigoComprob);
		if($comprob==false){
			$this->_transaction->rollback('El comprobante '.$codigoComprob.' no existe');
		}
		$options['comprob'] = $comprob;

		$nitEntregarDocumento = $options['nitEntregarDocumento'];
		if($nitEntregarDocumento==''){
			$this->_transaction->rollback('Indique el tercero al que se le entregará la mercancía');
		} else {
			$terceroEntregar = BackCacher::getTercero($nitEntregarDocumento);
			if($terceroEntregar==false){
				$this->_transaction->rollback('No existe el tercero con número de documento "'.$nitEntregarDocumento.'"');
			}
		}
		$options['nitEntregarDocumento'] = $nitEntregarDocumento;
		$options['terceroEntregar'] 	 = $terceroEntregar;

	}

	/**
	* Validate and add items per apps
	*
	* @param array $options
	*/
	public function _addItem(&$options){


		//$this->_transaction->rollback(print_r($options,true));
		$codigoAlmacen 	= $options['codigoAlmacen'];
		$totalFactura 	= $options['totalFactura'];
		$resumenVenta 	= $options['resumenVenta'];
		$resumenIva 	= $options['resumenIva'];
		$cantidades 	= $options['cantidades'];
		$descuentoGeneral 	= $options['descuentoGeneral'];
		$itemIvaVenta 	= $options['itemIvaVenta'];
		$comprob 		= $options['comprob'];
		$precios 		= $options['precios'];
		$detalles 		= $options['detalles'];
		$item 			= $options['item'];
		$n 				= $options['n'];

		$inve = BackCacher::getInve($item);
		if($inve==false){
			$this->_transaction->rollback('La referencia con código "'.$item.'" no existe, en la línea '.($n+1));
		}
		$options['inve'] = $inve;

		$linea = BackCacher::getLinea($codigoAlmacen, $inve->getLinea());
		if($linea==false){
			$this->_transaction->rollback('La línea de producto "'.$inve->getLinea().'" no existe en el almacén "'.$codigoAlmacen.'", en la línea '.($n+1));
		}
		$options['linea'] = $linea;

		$cuentaVenta = BackCacher::getCuenta($linea->getCtaVenta());
		if($cuentaVenta==false){
			$this->_transaction->rollback('La cuenta de venta no existe, para la línea de producto "'.$linea->getNombre().'" de la referencia "'.$inve->getDescripcion().'", en la línea '.($n+1));
		}
		$options['cuentaVenta'] = $cuentaVenta;

		if($cantidades[$n]<=0){
			$this->_transaction->rollback('La cantidad debe ser mayor o igual a cero en la línea '.($n+1));
		}

		if($precios[$n]<=0){
			$this->_transaction->rollback('El precio debe ser mayor o igual a cero en la línea '.($n+1));
		}

		$codigoCuentaIva = null;

		/*if($inve->getIvaVenta()===null){
			$this->_transaction->rollback('No se ha definido el porcentaje de IVA de venta de la referencia '.$inve->getDescripcion().', en la línea '.($n+1));
		}*/

		$valorTotal = ($precios[$n] * $cantidades[$n]);

		if($inve->getIvaVenta()>0){

			$baseIva = $valorTotal / ( 1 + ($inve->getIvaVenta()/100));
			$iva = $valorTotal - $baseIva;

			//$this->_transaction->rollback("$BaseIva = $valorTotal - ( $valorTotal / ( 1 + ({$inve->getIvaVenta()}/100) ) );");

			if($inve->getIvaVenta()==16||$inve->getIvaVenta()==10){

				$ivaSel = (int) $inve->getIvaVenta();

				$codigoCuentaIva = 0;

				if($ivaSel==16){
					//$codigoCuentaIva = (int) $comprob->getCtaIva16Venta();
					$codigoCuentaIva = (int) $options['ctaIva16v'];
				} else {
					//$codigoCuentaIva = $comprob->getCtaIva10Venta();
					$codigoCuentaIva = (int) $options['ctaIva10v'];
				}

				$cuentaIva = BackCacher::getCuenta($codigoCuentaIva);
				if($cuentaIva==false){
					$this->_transaction->rollback('La cuenta de contabilización del IVA del '.$inve->getIvaVenta().'% configurada en el comprobante de facturación no existe');
				} else {
					if($cuentaIva->getEsAuxiliar()!='S'){
						$this->_transaction->rollback('La cuenta de contabilización del IVA del '.$inve->getIvaVenta().'% configurada en el comprobante de facturación no es auxiliar');
					}
				}

			} else {
				$this->_transaction->rollback('La facturación no está soportada para IVA del '.$inve->getIvaVenta().'%');
			}
		} else {
			$iva = 0;
			$baseIva = $valorTotal;
		}

		$valorTotal -= $iva;

		if(!isset($resumenVenta[(int) $inve->getIvaVenta()])){
			$resumenVenta[(int) $inve->getIvaVenta()] = 0;
		}
		$resumenVenta[(int) $inve->getIvaVenta()]+=$baseIva;

		if(!isset($resumenIva[$inve->getIvaVenta()])){
			$resumenIva[$inve->getIvaVenta()] = 0;
		}
		$resumenIva[$inve->getIvaVenta()]+=$iva;

		$detalles[] = array(
			'Item' 			=> $item,
			'Descripcion' 	=> $inve->getDescripcion(),
			'Cantidad' 		=> $cantidades[$n],
			'Descuento' 	=> $descuentoGeneral,
			'CuentaVenta' 	=> $cuentaVenta->getCuenta(),
			'BaseIva' 		=> $baseIva,
			'PorcentajeIva' => $inve->getIvaVenta(),
			'CuentaIva' 	=> $codigoCuentaIva,
			'Iva' 			=> $iva,
			'Valor'			=> $precios[$n],
			'Total'			=> $valorTotal
		);

		//$valorTotalFactura = LocaleMath::round($valorTotal, 0) + $iva;
		$valorTotalFactura = $valorTotal + $iva;
		$totalFactura += $valorTotalFactura;

		if(!count($detalles)){
			$this->_transaction->rollback('Agregue primero referencias a la factura antes de generarla');
		}

		$options['totalFactura'] = $totalFactura;
		$options['totalFacturaSinIva'] = $totalFactura - $iva;
		$options['resumenVenta'] = $resumenVenta;
		$options['resumenIva'] = $resumenIva;
		$options['detalles'] = $detalles;
		$options['baseIva'] = $baseIva;
		$options['iva'] = $iva;

	}

	/**
	* Validate and add pagos per apps
	*
	* @param array $options
	*/
	private function _addPagos(&$options){

		$totalFormas 	= 0;
		$pagos 			= array();
		$detalleFormas 	= array();
		$formasPago 	= $options['formasPago'];
		$valoresFormas 	= $options['valoresFormas'];
		$totalFactura 	= $options['totalFactura'];
		$n 				= $options['n'];

		if(count($formasPago)){
			foreach($formasPago as $n => $codigoFormaPago){
				if($codigoFormaPago){
					$formaPago = BackCacher::getFormaPago($codigoFormaPago);
					if($formaPago==false){
						$this->_transaction->rollback('La forma de pago no existe en la línea '.($n+1));
					} else {
						$cuentaForma = BackCacher::getCuenta($formaPago->getCtaContable());
						if($cuentaForma==false){
							$this->_transaction->rollback('La cuenta de contabilización no existe en la forma de pago '.($n+1));
						} else {
							if($cuentaForma->getEsAuxiliar()!='S'){
								$this->_transaction->rollback('La cuenta de contabilización no es auxiliar en la forma de pago '.($n+1));
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

		if(intval($totalFormas)!=intval($totalFactura)){
			$this->_transaction->rollback('Las formas de pago no son igual al total de la factura TotalFactura='.$totalFactura.' TotalFormas='.$totalFormas);
		}
		$options['totalFormas'] = $totalFormas;
		$options['detalleFormas'] = $detalleFormas;

	}

	/**
	 * Add a Tatico
	 *
	 * @param array $options
	 */
	private function _addTatico(&$options){

		$codigoAlmacen	= $options['codigoAlmacen'];
		$fechaFactura	= $options['fechaFactura'];
		$consecutivo 	= $options['consecutivo'];
		$detalles 		= $options['detalles'];
		$almacen		= $options['almacen'];


		try {
			$comprobInve = sprintf('C%02s', $codigoAlmacen);
			$tatico = new Tatico($comprobInve, 0, '', $this->_transaction);
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

			foreach($detalles as $detalle){
				$details[] = array(
					'Item' => $detalle['Item'],
					'Cantidad' => $detalle['Cantidad'],
					'Valor' => 0
				);
			}

			$movement['Detail'] = $details;
			$tatico->addMovement($movement);

		}
		catch(TaticoException $e){
			$this->_transaction->rollback('Descargando de Inventarios: '.$e->getMessage());
		}


		$taticoConsecutivos = $tatico->getLastConsecutivos();

		$options['taticoConsecutivos'] = $taticoConsecutivos;
		$options['comprobInve'] = $comprobInve;
		$options['tatico'] = $tatico;


	}


	/**
	 * Add a Aura
	 *
	 * @param array $options
	 */
	private function _addAura(&$options){

		try {

			$fechaVencimiento 	= $options['fechaVencimiento'];
			$codigoComprob 		= $options['codigoComprob'];
			$detalleFormas 		= $options['detalleFormas'];
			$fechaFactura 		= $options['fechaFactura'];
			$nitDocumento 		= $options['nitDocumento'];
			$consecutivo 		= $options['consecutivo'];
			$detalles 			= $options['detalles'];
			$almacen 			= $options['almacen'];

			$aura = new Aura($codigoComprob, 0, $fechaFactura, Aura::OP_CREATE);

			$dataIva = array();

			foreach($detalles as $detalle){

				$aura->addMovement(array(
					'Descripcion' 		=> $detalle['Descripcion'],
					'Nit'				=> $nitDocumento,
					'CentroCosto' 		=> $almacen->getCentroCosto(),
					'Cuenta' 			=> $detalle['CuentaVenta'],
					'Valor' 			=> $detalle['Total'],
					'BaseGrab' 			=> $detalle['BaseIva'],
					'TipoDocumento' 	=> 'FAC',
					'NumeroDocumento' 	=> $consecutivo->getNumeroActual(),
					'FechaVence' 		=> $fechaVencimiento,
					'DebCre' 			=> 'C',
					'debug' 			=> true
				));

				//Adding data of iva
				if(isset($detalle['CuentaIva'])){
					if(!isset($dataIva[$detalle['CuentaIva']]) && $detalle['Iva']>0){
						$dataIva[$detalle['CuentaIva']] = array(
							'Descripcion' 	=> 'IVA '.$detalle['Descripcion'],
							'Nit' 			=> $nitDocumento,
							'CentroCosto' 	=> $almacen->getCentroCosto(),
							'Cuenta' 		=> $detalle['CuentaIva'],
							'Valor' 		=> $detalle['Iva'],
							'BaseGrab' 		=> $detalle['BaseIva'],
							'TipoDocumento' => 'FAC',
							'NumeroDocumento' => $consecutivo->getNumeroActual(),
							'FechaVence' 	=> $fechaVencimiento,
							'DebCre' 		=> 'C',
							'debug' 		=> true
						);
					} else {
						$dataIva[$detalle['CuentaIva']]['Valor'] += $detalle['Iva'];
						$dataIva[$detalle['CuentaIva']]['BaseGrab'] += $detalle['BaseIva'];
						$dataIva[$detalle['CuentaIva']]['Descripcion'] .= ', '.$detalle['Descripcion'];
					}
				}


			}

			//IVA
			//$this->_transaction->rollback(print_r($dataIva,true));
			foreach ($dataIva as $data) {
				$aura->addMovement($data);
			}

			//Formas de Pago
			foreach($detalleFormas as $detalleForma){
				$aura->addMovement(array(
					'Descripcion' 		=> $detalleForma['Descripcion'],
					'Nit' 				=> $nitDocumento,
					'CentroCosto' 		=> $almacen->getCentroCosto(),
					'Cuenta' 			=> $detalleForma['Cuenta'],
					'Valor' 			=> $detalleForma['Valor'],
					'BaseGrab' 			=> 0,
					'TipoDocumento' 	=> 'FAC',
					'NumeroDocumento' 	=> $consecutivo->getNumeroActual(),
					'FechaVence' 		=> $fechaVencimiento,
					'DebCre' 			=> 'D',
					'debug' 			=> true
				));
			}

			$aura->save();
		}
		catch(AuraException $e){
			$this->_transaction->rollback('Contabilidad: '.$e->getMessage());
		}

		$options['aura'] = $aura;
	}

	/**
	* Add a Factura
	*
	* @param array $options
	*/
	private function _addFactura(&$options){

		try{
			$nitEntregarDocumento 	= $options['nitEntregarDocumento'];
			$taticoConsecutivos 	= $options['taticoConsecutivos'];
			$fechaVencimiento 		= $options['fechaVencimiento'];
			$terceroEntregar 		= $options['terceroEntregar'];
			$detalleFormas 			= $options['detalleFormas'];
			$fechaFactura 			= $options['fechaFactura'];
			$totalFactura 			= $options['totalFactura'];
			$resumenVenta 			= $options['resumenVenta'];
			$nitDocumento 			= $options['nitDocumento'];
			$codigoComprob 			= $options['codigoComprob'];
			$comprobInve 			= $options['comprobInve'];
			$totalFormas 			= $options['totalFormas'];
			$consecutivo 			= $options['consecutivo'];
			$resumenIva 			= $options['resumenIva'];
			$detalles 				= $options['detalles'];
			$tercero 				= $options['tercero'];
			$aura 					= $options['aura'];

			//$this->_transaction->rollback(print_r($options,true));

			$factura = new Facturas();
			$factura->setTransaction($this->_transaction);
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
			if($factura->save()==false){
				foreach($factura->getMessages() as $message){
					$this->_transaction->rollback('Factura: '.$message->getMessage());
				}
			}

			foreach($detalles as $detalle){
				$facturaDetalle = new FacturasDetalle();
				$facturaDetalle->setTransaction($this->_transaction);
				$facturaDetalle->setFacturasId($factura->getId());
				$facturaDetalle->setItem($detalle['Item']);
				$facturaDetalle->setDescripcion($detalle['Descripcion']);
				$facturaDetalle->setCantidad($detalle['Cantidad']);
				$facturaDetalle->setDescuento($detalle['Descuento']);
				$facturaDetalle->setValor($detalle['Valor']);
				$facturaDetalle->setIva($detalle['Iva']);
				$facturaDetalle->setTotal($detalle['Total'] + $detalle['Iva']);
				if($facturaDetalle->save()==false){
					foreach($facturaDetalle->getMessages() as $message){
						$this->_transaction->rollback('Factura-Detalle: '.$message->getMessage().print_r($detalle, true));
					}
				}
			}

			foreach($detalleFormas as $detalleForma){
				$pagosDetalle = new FacturasPagos();
				$pagosDetalle->setTransaction($this->_transaction);
				$pagosDetalle->setFacturasId($factura->getId());
				$pagosDetalle->setFormaPago($detalleForma['Codigo']);
				$pagosDetalle->setDescripcion($detalleForma['Descripcion']);
				$pagosDetalle->setValor($detalleForma['Valor']);
				if($pagosDetalle->save()==false){
					foreach($pagosDetalle->getMessages() as $message){
						$this->_transaction->rollback('Factura-Pagos: '.$message->getMessage().print_r($detalleForma, true));
					}
				}
			}

			$consecutivo->setNumeroActual($consecutivo->getNumeroActual()+1);
			if($consecutivo->save()==false){
				foreach($consecutivo->getMessages() as $message){
					$this->_transaction->rollback('Consecutivo: '.$message->getMessage());
				}
			}

			$options['factura'] = $factura;
		}
		catch(Exception $e){
			$this->_transaction->rollback($e->getMessage());
		}
	}

	/**
	* Add a Invoicer
	*
	* @param array $options(
	*	'items' 				=> array
	*	'precios' 				=> array
	*	'cantidades' 			=> array
	*	'descuentos' 			=> array
	*	'formasPago' 			=> array
	*	'valoresFormas' 		=> array
	*	'nitDocumento' 			=> string
	*	'nitEntregarDocumento' 	=> string
	*	'fechaVencimiento' 		=> date
	*	'fechaFactura'			=> date
	*)
	*/
	public function addInvoicer(&$options){

		//validar invoicer
		$this->_validateInvoicer($options);

		$TotalFactura 	= 0;
		$detalles 		= array();
		$movimiento 	= array();
		$resumenIva 	= array('0' => 0, '10' => 0, '16' => 0);
		$resumenVenta 	= array('0' => 0, '10' => 0, '16' => 0);
		$items 			= $options['items'];

		$options['detalles']		= $detalles;
		$options['resumenIva']		= $resumenIva;
		$options['resumenVenta'] 	= $resumenVenta;
		$options['totalFactura'] 	= $totalFactura;

		if(count($items)){

			foreach($items as $n => $item){
				if($item){

					$options['n'] = $n;
					$options['item'] = $item;

					//add item
					$this->_addItem($options);

				}
			}

			//$this->_transaction->rollback(print_r($options['detalles'],true));
			//print_r($options);

			//add pagos
			$this->_addPagos($options);

			//agregamos a tatico lo que se necesite
			$this->_addTatico($options);

			//agregamos a aura
			$this->_addAura($options);

			//creamos factura
			$this->_addFactura($options);

		} else {
			$this->_transaction->rollback('Agregue primero referencias a la factura antes de generarla');
		}

		//$this->_transaction->commit();

	}


	/**
	* Print a Invoicer
	*
	* @param int $facturaId
	*/
	public function getPrint($facturaId){

		$factura = $this->Facturas->findFirst($facturaId);
		if($factura==false){
			throw new InvoicingException("La factura con ID $facturaId no existe");
		}

		$pdf = new mPDF('win-1252', 'letter');
		$pdf->SetDisplayMode('fullpage');
		$pdf->tMargin = 10;
		$pdf->lMargin = 10;
		$pdf->ignore_invalid_utf8 = true;

		$empresa = BackCacher::getEmpresa();
		if($empresa==false){
			throw new InvoicingException("No existe la empresa que generó la factura");
		}

		$terceroEmpresa = BackCacher::getTercero($empresa->getNit());
		if($terceroEmpresa==false){
			throw new InvoicingException("La empresa no existe como un tercero");
		}

		$terceroFactura = BackCacher::getTercero($factura->getNit());
		if($terceroFactura==false){
			throw new InvoicingException("El tercero al que se generó la factura no existe");
		}

		$numeroFactura = $factura->getPrefijo().sprintf('%07s', $factura->getNumero());

		$html = '<html>
		<head>
			<title>Factura '.$numeroFactura.'</title>
			<style type="text/css">
			body {
				font-family: Helvetica;
			}
			.datos-wepax {
				color: #AFAEAD;
			}
			.numero-factura {
				font-size: 20px;
			}
			.datos-consec {
				font-size: 10px;
				padding: 15px;
			}
			.resumen-factura {
				border-bottom: 1px solid #ababab;
				border-right: 1px solid #ababab;
			}
			.resumen-factura th {
				border-top: 1px solid #ababab;
				border-left: 1px solid #ababab;
				padding: 3px;
				background: #fafafa;
				font-size: 12px;
			}
			.resumen-factura td {
				border-top: 1px solid #ababab;
				border-left: 1px solid #ababab;
				padding: 3px;
				font-size: 12px;
			}
			.paragraph {
				padding: 10px;
			}
			.resumen-factura td.total-label {
				font-size: 18px;
			}
			.resumen-factura td.total-factura {
				font-size: 18px;
			}
			.firma {
				text-align: center;
				font-size: 11px;
				color: #AFAEAD;
			}
			.resumen-mail th {
				font-size: 11px;
			}
			.resumen-mail td {
				font-size: 11px;
			}
			.traspaso {
				font-size: 11px;
			}
			</style>
		</head>
		<body>
		<div class="paragraph">
			<table width="100%">
				<tr>
					<td>
						<img src="http://'.$_SERVER['HTTP_HOST'].''.Core::getInstancePath().'/img/backoffice/logo.jpg" alt="BackOffice Logo" height="70" />
					</td>
					<td align="right" class="datos-empresa">
						<b>'.$empresa->getNit().'</b><br/>
						<b>'.$empresa->getNombre().'</b><br/>
						'.$terceroEmpresa->getDireccion().'<br/>
						Teléfono:'.$terceroEmpresa->getTelefono().'<br/>
						Cota, Colombia

						<br>
						'.$factura->getNotaFactura().'<br/>
						'.$factura->getNotaIca().'<br/>
					</td>
				</tr>
			</table>
		</div>
		<div class="paragraph">
			<table width="100%">
				<tr>
					<td valign="top" width="45%" align="left">
						<b>DATOS DEL COMPRADOR</b><br/>
						<table>
							<tr>
								<td align="right"><b>NIT/Cedula</b></td>
								<td>'.$factura->getNit().'</td>
							</tr>
							<tr>
								<td align="right"><b>Nombre</b></td>
								<td>'.$factura->getNombre().'</td>
							</tr>
							<tr>
								<td align="right"><b>Dirección</b></td>
								<td>'.$factura->getDireccion().', '.$terceroFactura->getCiudadNombre().'</td>
							</tr>
							<tr>
								<td align="right"><b>Teléfono</b></td>
								<td>'.$terceroFactura->getTelefono().'</td>
							</tr>
						</table>
					</td>
					<td align="right">
						<b>Factura de Venta</b><br/>
						<span class="numero-factura">'.$numeroFactura.'</span><br/>
						<br/>

						<div class="datos-consec">
						CONSECUTIVO AUTORIZADO FACTURACIÓN POR COMPUTADOR SEGUN RESOLUCIÓN No. '.$factura->getResolucion().'<br/>
						DEL '.$factura->getFechaResolucion()->getLocaleDate('medium').' NUMERACIÓN AUTORIZADA<br/>
						'.$factura->getPrefijo().sprintf('%07s', $factura->getNumeroInicial()).' AL '.$factura->getPrefijo().sprintf('%07s', $factura->getNumeroFinal()).'</b>
						</div>

						<br/>
						<b>Fecha:</b> '.$factura->getFechaEmision()->getLocaleDate('short').'<br/>
						<b>Fecha Vencimiento:</b> '.$factura->getFechaVencimiento()->getLocaleDate('short').'

					</td>
				</tr>
			</table>
		</div>

		<div class="paragraph">
			<table cellspacing="0" cellpadding="0" width="100%" align="center">
				<tr>
					<td style="background:#ababab;height: 10px;" width="40%"></td>
					<td style="background:#dadada;height: 10px;" width="30%"></td>
					<td style="background:#eaeaea;height: 10px;" width="30%"></td>
				</tr>
			</table>
		</div>

		<div class="paragraph">
			<table class="resumen-factura" cellspacing="0" cellpadding="0" width="95%" align="center">
				<tr>
					<th>Código Ref.</th>
					<th>Descripción</th>
					<th>Cantidad</th>
					<th>Valor Uni.</th>
					<th>Total Sin Iva</th>
					<th>IVA</th>
					<th>Valor Total</th>
					<!--<th width="10%">% Desc.</th>-->
				</tr>';

			$totalBase = 0;
			$totalIva = 0;
			$totalValor = 0;
			$totalSinIva = 0;
			foreach($this->FacturasDetalle->find("facturas_id='$facturaId'") as $detalle){
				$html.='<tr>
					<td>'.$detalle->getItem().'</td>
					<td>'.$detalle->getDescripcion().'</td>
					<td align="right">'.Currency::number($detalle->getCantidad(), 0).'</td>
					<td align="right">'.Currency::money($detalle->getValor()).'</td>
					<td align="right">'.Currency::money($detalle->getTotal()-$detalle->getIva()).'</td>
					<td align="right">'.Currency::money($detalle->getIva()).'</td>
					<td align="right">'.Currency::money($detalle->getTotal()).'</td>
					<!--<td align="right">'.Currency::number($detalle->getDescuento()).'</td>-->
				</tr>';
				$totalBase+=$detalle->getValor();
				$totalIva+=$detalle->getIva();
				$totalValor+=$detalle->getTotal();
				$totalSinIva+= ($detalle->getTotal() - $detalle->getIva());
			}
			$html.='<tr>
				<td align="right" colspan="3"><b>TOTALES</b></td>
				<!--<td align="right"><b>'.Currency::money($totalBase).'</b></td>-->
				<td align="right">&nbsp;</td>
				<td align="right"><b>'.Currency::money($totalSinIva).'</b></td>
				<td align="right"><b>'.Currency::money($totalIva).'</b></td>
				<td align="right"><b>'.Currency::money($totalValor).'</b></td>
				<!--<td align="right">&nbsp;</td>-->
			</tr>';
		$html.='</table>
		</div>';

		$formasPago = array();
		foreach($this->FacturasPagos->find("facturas_id='$facturaId'") as $facturaPago){
			$formasPago[] = $facturaPago->getDescripcion();
		}
		if(count($formasPago)>0){
			$locale = Locale::getApplication();
			$formasPago = 'Formas de Pago: '.$locale->getConjunction($formasPago);
		} else {
			$formasPago = 'Forma de Pago: '.$formasPago[0];
		}

		$html.='<div class="paragraph">
			<table cellspacing="0" cellpadding="0" width="100%" align="center">
				<tr>
					<td style="background:#ababab;height: 10px;" width="40%"></td>
					<td style="background:#dadada;height: 10px;" width="30%"></td>
					<td style="background:#eaeaea;height: 10px;" width="30%"></td>
				</tr>
			</table>
		</div>

		<div class="paragraph">
			<table width="100%">
				<tr>
					<td width="50%" align="left" valign="top" class="traspaso">
						<b>TRASPASO FACTURAS AL COBRO</b><br/>
						Nombre ______________________________________________________________<br/>
						<br/>
						<br/>

						<b>ACEPTADA</b><br/>
						Nombre ______________________________________________________________<br/>
						<br/>
						<br/>

						Entiendo que mi responsabilidad por esta cuenta sigue vigente y
						me hago personalmente responsable en caso que la persona, compa√±√≠a ó asociación
						indicada dejase de pagar parcial ó totalmente la suma a cancelar aqui especificada.
					</td>
					<td width="50%" align="right">
						<table class="resumen-factura" cellspacing="0" cellpadding="0" align="right">
							<tr>
								<td colspan="2"><b>'.$formasPago.'</b></td>
							</tr>
							<tr>
								<td colspan="2"></td>
							</tr>
							<tr>
								<td align="right"><b>Venta Gravada 16%</b></td>
								<td align="right">'.Currency::money($factura->getVenta16()).'</td>
							</tr>
							<tr>
								<td align="right"><b>Venta Gravada 10%</b></td>
								<td align="right">'.Currency::money($factura->getVenta10()).'</td>
							</tr>
							<tr>
								<td align="right"><b>Venta No Gravada</b></td>
								<td align="right">'.Currency::money($factura->getVenta0()).'</td>
							</tr>
							<tr>
								<td colspan="2"></td>
							</tr>
							<tr>
								<td align="right"><b>IVA 16%</b></td>
								<td align="right">'.Currency::money($factura->getIva16()).'</td>
							</tr>
							<tr>
								<td align="right"><b>IVA 10%</b></td>
								<td align="right">'.Currency::money($factura->getIva10()).'</td>
							</tr>
							<tr>
								<td align="right"><b>Total IVA</b></td>
								<td align="right">'.Currency::money($factura->getIva16()+$factura->getIva10()).'</td>
							</tr>
							<tr>
								<td colspan="2"></td>
							</tr>
							<tr>
								<td align="right" class="total-label"><b>Total Facturado</b></td>
								<td align="right" class="total-factura">'.Currency::money($factura->getTotal()).'</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>

		<div class="firma">
			<br/>
			IMPRESO POR '.$empresa->getNombre().' (NIT. '.$empresa->getNit().')
		</div>

		';

		$html.='</body></html>';

		$pdf->writeHTML($html);
		$fileName = 'factura-'.$numeroFactura.'-'.mt_rand(100,1000).'.pdf';
		$pdf->Output('public/temp/'.$fileName);

		return $fileName;

		//readfile('public/temp/'.$fileName);

	}

	/**
	 * Retorna un arreglo asociativo con los datos del pedido con lista de precios
	 *
	 * @param	int $nAlmacen
	 * @param	int $nPedido
	 * @param	mixed $contrato
	 * @param	mixed $nit
	 * @return	mixed
	 */
	public static function getCheckPedidoInvoicer($taticoPedido, $contrato, $nit) 
	{

		/* Content TaticoPedido:
		 * 
		$taticoPedido = array(
			'status' => 'OK',
			'data' => array(
				'nit' => $pedido->getNit(),
				'nit_det' => $nombre,
				'almacen_destino' => $pedido->getAlmacenDestino(),
				'centro_costo' => $pedido->getCentroCosto(),
				'f_vence' => $pedido->getFVence()->getDate(),
				'observaciones' => $pedido->getObservaciones(),
				'movilin' => array(
					'id' => $movilin->getId(),
					'item' => $movilin->getItem(),
					'descripcion' => $inve->getDescripcion(),
					'unidad' => $inve->getUnidad(),
					'costo' => LocaleMath::round($movilin->getValor(), 2),
					'cantidad' => LocaleMath::round($movilin->getCantidad(), 3),
					'valor' => LocaleMath::round($movilin->getValor(), 2),
				)
			)ps
			* 
		);*/
		
		if ($taticoPedido['status']=='FAILED') {
			return $taticoPedido; 
		}
		
		foreach ($taticoPedido['data']['movilin'] as $index => $movilin)
		{
			$numeroItem = $movilin['item'];
			
			//Verificamos si esta en lista de precios esa referencia con ese tercero y ese contrato
			$listaPrecios = EntityManager::get('ListaPrecios')->findFirst("referencia='$numeroItem' AND nit='$nit' AND contrato='$contrato'");
			
			$precio = $movilin['precio'];
			if ($listaPrecios!=false) {
				$precio = $listaPrecios->getPrecioVenta();
			}
						
			$taticoPedido['data']['movilin'][$index]['valor'] = $precio;
			$taticoPedido['data']['movilin'][$index]['precio'] = $precio;
			
			unset($numeroItem, $movilin, $precio, $listaPrecios, $index);
		}
		
		return $taticoPedido;
	}
}
