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
* Class Invoicing to SO (socios)
*
* Add/Print Invoicers
* 
*/
class InvoicingSO extends UserComponent {

	/**
	* @var string $app
	*/
	private $_apps = 'SO'; //Socios

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

		$this->_transaction = TransactionManager::getUserTransaction();

		

		//Validaciones globales
		$consecutivoId = Settings::get('consecutivo_factura', 'FC');
		if(!$consecutivoId){
			throw new SociosException('No se ha configurado el consecutivo de facturación');
		} else {
			$consecutivo = BackCacher::getConsecutivo($consecutivoId);
			if($consecutivo==false){
				throw new SociosException('El consecutivo de facturación configurado no existe');
			}
		}
		$options['consecutivoId'] 	= $consecutivoId;
		$options['consecutivo']		= $consecutivo;

		$nitDocumento = Filter::bring($options['nitDocumento'], 'terceros');
		if($nitDocumento==''){
			throw new SociosException('Indique el tercero al que se le generará la factura');
		} else {
			$tercero = BackCacher::getTercero($nitDocumento);
			if($tercero==false){
				throw new SociosException('No existe el tercero con número de documento "'.$nitDocumento.'"');
			}
		}
		$options['nitDocumento'] = $nitDocumento;
		$options['tercero'] 	 = $tercero;

		$fechaFactura = $options['fechaFactura'];
		$fechaVencimiento = $options['fechaVencimiento'];

		try {
			if(Date::isLater($fechaFactura, $fechaVencimiento)){
				throw new SociosException('La fecha de vencimiento no puede ser menor a la fecha de la factura');
			}
		}
		catch(DateException $e){
			throw new SociosException($e->getMessage());
		}
		$options['fechaFactura'] = $fechaFactura;
		$options['fechaVencimiento'] = $fechaVencimiento;

		/*$codigoAlmacen = Settings::get('almacen_venta');
		if(!$codigoAlmacen){
			throw new SociosException('No se ha configurado el almacén donde se descargan las referencias de la factura');
		} else {
			$almacen = BackCacher::getAlmacen($codigoAlmacen);
			if($almacen==false){
				throw new SociosException('El almacén donde se descargan las referencias de la factura configurado no existe');
			}
		}
		$options['codigoAlmacen'] 	= $codigoAlmacen;
		$options['almacen'] 		= $almacen;*/
		
		$codigoComprob = Settings::get('comprob_factura', 'SO');
		if($codigoComprob==''){
			throw new SociosException('No se ha configurado el comprobante de socios');
		}
		$options['codigoComprob'] = $codigoComprob;

		//throw new SociosException($codigoComprob);
		$comprob = BackCacher::getComprob($codigoComprob);
		//$comprob = EntityManager::get('Comprob')->findFirst(array('conditions'=>"codigo='$codigoComprob'"));
		if($comprob==false){
			throw new SociosException('El comprobante '.$codigoComprob.' no existe');
		}
		$options['comprob'] = $comprob;

		/*$nitEntregarDocumento = $options['nitEntregarDocumento'];
		if($nitEntregarDocumento==''){
			throw new SociosException('Indique el tercero al que se le entregará la mercancía');
		} else {
			$terceroEntregar = BackCacher::getTercero($nitEntregarDocumento);
			if($terceroEntregar==false){
				throw new SociosException('No existe el tercero con número de documento "'.$nitEntregarDocumento.'"');
			}
		}
		$options['nitEntregarDocumento'] = $nitEntregarDocumento;
		$options['terceroEntregar'] 	 = $terceroEntregar;*/

	}

	/**
	* Validate and add items per apps
	*
	* @param array $options
	*/
	public function _addItem(&$options){

		$codigoAlmacen 	= $options['codigoAlmacen'];
		$totalFactura 	= $options['totalFactura'];
		$resumenVenta 	= $options['resumenVenta'];
		$resumenIva 	= $options['resumenIva'];
		$cantidades 	= $options['cantidades'];
		$descuentos 	= $options['descuentos'];
		$comprob 		= $options['comprob'];
		$detalles 		= $options['detalles'];
		$precios 		= $options['precios'];
		$item 			= $options['item'];
		$n 				= $options['n'];

		$empresa = EntityManager::get('Empresa')->findFirst();
		//throw new SociosException($empresa->getNit());
		$nits = EntityManager::get('Nits')->findFirst(array('conditions'=>"nit='{$empresa->getNit()}'"));
		if($nits==false){
			throw new SociosException('El nit de la empresa no esta creado en terceros: '.$empresa->getNit());
		}
		if(!$nits->getEstadoNit()){
			throw new SociosException('No se ha configurado el regimen de la empresa en terceros');
		}
		$regimenCuentas = EntityManager::get('RegimenCuentas')->findFirst(array('conditions'=>"regimen='{$nits->getEstadoNit()}'"));
		if($regimenCuentas==false){
			throw new SociosException('No se ha configurado las cuentas segun regimen de la empresa');
		}

		//$cargosFijos = BackCacher::getCargosFijos($item);
		$cargosFijos = $this->CargosFijos->findFirst($item);
		if($cargosFijos==false){
			throw new SociosException('El cargo fijo con código "'.$item.'" no existe, en la línea '.($n+1));
		}
		$options['cargosFijos'] = $cargosFijos;

		$cuentaVenta = BackCacher::getCuenta($cargosFijos->getCuentaContable());
		if($cuentaVenta==false){
			throw new SociosException('La cuenta de venta no existe, para el cargo fijo "'.$cargosFijos->getNombre().'", en la línea '.($n+1));
		}
		$options['cuentaVenta'] = $cuentaVenta;

		if($cantidades[$n]<=0){
			throw new SociosException('La cantidad debe ser mayor o igual a cero en la línea '.($n+1));
		}

		/*if($precios[$n]<=0){
			throw new SociosException('El precio debe ser mayor o igual a cero en la línea '.($n+1).': '.print_r($options,true));
		}*/

		$codigoCuentaIva = null;

		if($cargosFijos->getPorcentajeIva()===null){
			throw new SociosException('No se ha definido el porcentaje de IVA de venta del cargo fijo '.$cargosFijos->getNombre().', en la línea '.($n+1));
		}

		$codigoCuentaIva = 0;
		
		$valorTotal = ($precios[$n] * $cantidades[$n]);		

		if($cargosFijos->getPorcentajeIva()>0){

			$baseIva = $valorTotal / ( 1 + ($cargosFijos->getPorcentajeIva()/100));
			$iva = $valorTotal - $baseIva;

			if($cargosFijos->getPorcentajeIva()==16||$cargosFijos->getPorcentajeIva()==10){
				$ivaSel = (int) $cargosFijos->getPorcentajeIva();
				if($ivaSel==16){
					//16%
					$codigoCuentaIva = $regimenCuentas->getCtaIva16v();
				} else {
					//10%
					$codigoCuentaIva = $regimenCuentas->getCtaIva10v();
				}
				$cuentaIva = BackCacher::getCuenta($codigoCuentaIva);
				if($cuentaIva==false){
					throw new SociosException('La cuenta de contabilización ('.$codigoCuentaIva.') del IVA del '.$cargosFijos->getPorcentajeIva().'%  de Ventas en Regimen Cuentas configurada en el comprobante de facturación no existe ('.$codigoCuentaIva.')');
				} else {
					if($cuentaIva->getEsAuxiliar()!='S'){
						throw new SociosException('La cuenta de contabilización auxiliar ('.$codigoCuentaIva.') del IVA del '.$cargosFijos->getPorcentajeIva().'%  de Ventas en Regimen Cuentas configurada en el comprobante de facturación no es auxiliar ('.$codigoCuentaIva.')');
					}
				}
			} else {
				throw new SociosException('La facturación no está soportada para IVA del '.$cargosFijos->getPorcentajeIva().'%');
			}
		} else {
			$baseIva = LocaleMath::round($precios[$n]*$cantidades[$n], 0);
			$iva = 0;
		}
		
		
		if($cargosFijos->getIngresoTercero()=='N'){

			if($cargosFijos->getPorcentajeIva()>0){
		
				if(!isset($resumenVenta[16])){
					$resumenVenta[16] = 0;
				}
				$resumenVenta[16]+=$baseIva;
				
			} else {

				if(!isset($resumenVenta[0])){
					$resumenVenta[0]=0;
				}
				$resumenVenta[0]+=$baseIva;

			}

			
		} else {

			if(!isset($resumenVenta[10])){
				$resumenVenta[10]=0;
			}
			$resumenVenta[10]+=$baseIva;

		}
		
		
		
		if(!isset($resumenIva[16])){
			$resumenIva[16] = 0;
		}
		$resumenIva[16]+=$iva;

		$detalles[] = array(
			'Item' 			=> $item,
			'Descripcion' 	=> $cargosFijos->getNombre(),
			'Cantidad' 		=> $cantidades[$n],
			'Descuento' 	=> $descuentos[$n],
			'CuentaVenta' 	=> $cargosFijos->getCuentaContable(),
			'BaseIva' 		=> $baseIva,
			'PorcentajeIva' => $cargosFijos->getPorcentajeIva(),
			'CuentaIva' 	=> $codigoCuentaIva,
			'Iva' 			=> $iva,
			'Valor' 		=> $precios[$n],
			'cargosFijos'	=> $cargosFijos
		);

		$totalFactura += LocaleMath::round($precios[$n]*$cantidades[$n], 0) + $iva;

		if(!count($detalles)){
			throw new SociosException('Agregue primero cargos a la factura antes de generarla.');
		}

		$options['totalFactura'] = $totalFactura;
		$options['resumenVenta'] = $resumenVenta;
		$options['resumenIva'] = $resumenIva;
		$options['detalles'] = $detalles;
		$options['baseIva'] = $baseIva;
		$options['iva'] = $iva;
		
	}

	/**
	* Add a Factura
	*
	* @param array $options
	* @return $options
	*/
	private function _addFactura($options){

		try {
			$socios = $options['socios'];
			$facturaSO = $options['factura'];
			$resumenIva = $options['resumenIva'];
			$consecutivo = $options['consecutivo'];
			$resumenVenta = $options['resumenVenta'];
			$detalleFacturaSO = $options['detalleFacturasObj'];
			$detalleMovimientoSO = $options['detalleMovimiento'];

			$nombreCompleto = $socios['nombres'].' '.$socios['apellidos'].' / '.$socios['numero_accion'];
			
			$factura = EntityManager::get('Facturas', true)->setTransaction($this->_transaction);
			$factura->setConsecutivosId($consecutivo->getId());
			$factura->setPrefijo($consecutivo->getPrefijo());
			$factura->setNumero($consecutivo->getNumeroActual());
			$factura->setResolucion($consecutivo->getResolucion());
			$factura->setFechaResolucion((string)$consecutivo->getFechaResolucion());
			$factura->setNumeroInicial($consecutivo->getNumeroInicial());
			$factura->setNumeroFinal($consecutivo->getNumeroFinal());
			$factura->setNit($options['nitDocumento']);
			$factura->setNombre($nombreCompleto);
			$factura->setDireccion($socios['direccion_casa']);
			$factura->setNitEntregar($options['nitDocumento']);
			$factura->setNombreEntregar($nombreCompleto);
			$factura->setDireccionEntregar($socios['direccion_casa']);
			$factura->setFechaEmision($facturaSO['fecha_factura']);
			$factura->setFechaVencimiento($facturaSO['fecha_vencimiento']);
			$factura->setNotaFactura($consecutivo->getNotaFactura());
			$factura->setNotaIca($consecutivo->getNotaIca());
			$factura->setVenta16($resumenVenta[16]);
			$factura->setVenta10($resumenVenta[10]);
			$factura->setVenta0($resumenVenta[0]);
			$factura->setIva16($resumenIva[16]);
			$factura->setIva10($resumenIva[10]);
			$factura->setIva0($resumenIva[0]);
			$factura->setPagos($facturaSO['total_factura']);
			$factura->setTotal($facturaSO['total_factura']);
			
			//$factura->setComprobInve('');
			//$factura->setNumeroInve('');
			
			$factura->setComprobContab($codigoComprob);
			$factura->setNumeroContab($auraConsecutivo);
			$factura->setEstado('A');
			if ($factura->save()==false) {
				foreach ($factura->getMessages() as $message)
				{
					throw new Exception('Factura: '.$message->getMessage());
				}
			}

			//throw new SociosException(print_r($detalles,true));
			foreach ($detalleMovimientoSO as $detalleMovimiento)
			{
				$facturaDetalle = EntityManager::get('FacturasDetalle', true)->setTransaction($this->_transaction);
				$facturaDetalle->setFacturasId($factura->getId());
				$facturaDetalle->setItem($detalleMovimiento['cargos_fijos_id']);
				$facturaDetalle->setDescripcion($detalleMovimiento['descripcion']);
				$facturaDetalle->setCantidad($detalleMovimiento['cantidad']);
				$facturaDetalle->setDescuento($detalleMovimiento['descuento']);
				$facturaDetalle->setValor($detalleMovimiento['valor']);
				$facturaDetalle->setIva($detalleMovimiento['iva']);
				$total = ($detalleMovimiento['valor']+$detalleMovimiento['iva']);
				$facturaDetalle->setTotal($total);
				if ($facturaDetalle->save()==false) {
					foreach ($facturaDetalle->getMessages() as $message)
					{
						throw new Exception('Factura-Detalle: '.$message->getMessage().print_r($detalle, true));
					}
				}
				unset($detalleMovimiento,$facturaDetalle,$total);
			}
			unset($detalleMovimientoSO);

			$consecutivo->setNumeroActual($consecutivo->getNumeroActual()+1);
			if ($consecutivo->save()==false) {
				foreach ($consecutivo->getMessages() as $message)
				{
					throw new Exception('Consecutivo: '.$message->getMessage());
				}
			}
			
			$facturaData = array();
			foreach ($factura->getAttributes() as $field) 
			{
				$facturaData[$field] = $factura->readAttribute($field);
				unset($field);
			}
			$options['facturas'] = $facturaData;
			$options['facturasId'] = $factura->getId();

			unset($socios,$facturaSO,$resumenIva,$consecutivo,$resumenVenta,$detalleFacturaSO,$nombreCompleto);	
			return $options;
		}
		catch(Exception $e) {
			throw new Exception("_addFactura: ".$e->getMessage());
		}
	}	

	/**
	* Agrega cuota del prestamo a factura
	*
	* @param array $options(
	*	'prestamosSocios',
	*	'amortizacion',
	*	'cargoMes',
	*	'salAntNeto',
	*	'salAntInteres',
	* )
	* @return $financiacionId
	*/
	private function _addPrestamo($options){

		$this->_transaction = TransactionManager::getUserTransaction();

		
		try 
		{
			$prestamoArray = $options['prestamo'];

			#throw new Exception(print_r($prestamoArray,true));
			$nitEntregarDocumento 	= $options['nitDocumento'];
			$facturaId 				= $options['facturasId'];
		
			foreach ($prestamoArray as $prestamo) 
			{

				if (isset($prestamo['total']) && $prestamo['total']) {

					$financiacion = EntityManager::get('Financiacion', true)->setTransaction($this->_transaction);
					$financiacion->setFacturaId($facturaId);
					$financiacion->setDescripcion($prestamo['descripcion']);
					$financiacion->setValor($prestamo['valor']);
					$financiacion->setMora($prestamo['mora']);
					$financiacion->setTotal($prestamo['total']);
					
					if ($financiacion->save()==false) {
						foreach ($financiacion->getMessages() as $message) 
						{
							throw new SociosException('addPrestamo: '.$message->getMessage());
						}
					}

				}
				unset($prestamo,$financiacion);
			}
			unset($prestamoArray,$nitEntregarDocumento,$facturaId);
		}
		catch(Exception $e) {
			throw new Exception('_addPrestamo: '.$e->getMessage());
		}
		
	}

	/**
	* Disable a Invoicer
	*
	* @param array $options(
	*	'facturas'			=> array(int,int,....)
	*)
	*/
	public function disableInvoicer(&$options){
		try 
		{
			$this->_transaction = TransactionManager::getUserTransaction();

			if(isset($options['facturas'])==false){
				throw new SociosException('Agregue primero id de facturas en anular en index facturas');
			}
			$facturasArray = $options['facturas'];
			
			if ($facturasArray && is_array($facturasArray)==true && count($facturasArray)>0) {
				$inWhere = implode(',', $facturasArray);
				if ($inWhere) {
					$andWhere = '';
					//throw new Exception(print_r($options,true));
					
					if (isset($options['nits']) && count($options['nits'])) {
						$year = substr($options['periodo'], 0,4);
						$month = substr($options['periodo'], 4,2);
						$andWhere = ' OR (nit IN("'.implode('","', $options['nits']).'") AND YEAR(fecha_emision)="'.$year.'" AND MONTH(fecha_emision)="'.$month.'")';
					}
					$whereStr = 'id IN('.$inWhere.')'.$andWhere;
					//throw new Exception($whereStr);
					
					$facturasObj = EntityManager::get('Facturas')->setTransaction($this->_transaction)->deleteAll($whereStr);
				}
			}
			$this->_transaction->commit();
		}
		catch(Exception $e) {
			throw new Exception($e->getMessage());
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
		$this->_transaction = TransactionManager::getUserTransaction();

		try {
			$this->_transaction = TransactionManager::getUserTransaction();

			//validar invoicer
			$this->_validateInvoicer($options);

			$TotalFactura 	= 0;
			$detalles 		= array();
			$movimiento 	= array();
			$items 			= $options['items'];

			$options['detalles']		= $detalles;
			$options['totalFactura'] 	= $totalFactura;

			//creamos factura
			$options = $this->_addFactura($options);

			//add prestamo
			$this->_addPrestamo($options);

			$this->_transaction->commit();
		}
		catch(Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	/**
	* Obtiene el consecutivo actual de la tabla consecutivos de facturacion 
	* 
	* @param array $options
	* @return int $consecutivo
	*/
	public function getConsecutivo(&$options){

		try {
			$this->_transaction = TransactionManager::getUserTransaction();
			
			//Si deseo el consecutivo actual
			if (!isset($options['facturaId'])) {
				$consecutivos = EntityManager::get('Consecutivos')->setTransaction($this->_transaction)->findFirst();
				$consecutivo = $consecutivos->getNumeroActual();
			} else {
				//Si deseo el consecutivo de una factura en especial
				$facturas = EntityManager::get('Facturas')->setTransaction($this->_transaction)->findFirst($options['facturaId']);
				if ($facturas==false) {
					throw new Exception("La factura no existe. '{$options['facturaId']}'");
				}
				$consecutivo = $facturas->getNumero();
			}	
			return $consecutivo;				
		}
		catch(Exception $e){
			throw new Exception('getConsecutivo: '.$e->getMessage());
		}
	}

	/**
	* Cambia el consecutivo actual de la tabla consecutivos de facturacion 
	* 
	* @param array $options
	* @return int $consecutivo
	*/
	public function setConsecutivo(&$options){

		try {
			if (!isset($options['consecutivo'])) {
				throw new Exception("Error Processing Request", 1);
			}
			$consecutivos = EntityManager::get('Consecutivos')->setTransaction($this->_transaction)->findFirst();
			$consecutivos->setNumeroActual($options['consecutivo']);
			if (!$consecutivos->save()) {
				foreach ($consecutivos->getMessages() as $message) {
					throw new Exception('save: '.$message->getMessage());
				}
			}
			return $consecutivo;				
		}
		catch(Exception $e){
			throw new Exception('setConsecutivo: '.$e->getMessage());
		}
	}
}
