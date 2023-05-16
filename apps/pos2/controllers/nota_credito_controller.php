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
 * @copyright 	BH-TECK Inc. 2009-2013
 * @version		$Id$
 */

class Nota_CreditoController extends ApplicationController
{

	
	public function indexAction()
	{
		$this->loadModel('Salon', 'FormasPago');
		$datos = $this->Datos->findFirst();
		$this->setParamToView('facturas', $this->Factura->find("fecha='{$datos->getFecha()}' AND estado='A' AND tipo_venta='F'"));
	}

	public function findFacturaAction($prefijo_facturacion, $consecutivo_facturacion){

		$this->setResponse('json');

		$response = [
			'success' => true,
			'message' => '',
			'data' => []
		];

		try {

			$prefijo_facturacion = $this->filter($prefijo_facturacion, 'alpha');
			$consecutivo_facturacion = $this->filter($consecutivo_facturacion, 'alpha');

			$conditions = "prefijo_facturacion = '$prefijo_facturacion' AND consecutivo_facturacion='$consecutivo_facturacion' AND estado='A'";
			$factura = $this->Factura->findFirst($conditions);
			$response['data']['factura'] = $factura;
			$response['data']['detalle'] = []; 
			if($factura){

				$sql = "SELECT 
							df.id, 
							df.menus_items_id, 
							mi.nombre, 
							(df.cantidad - SUM(IFNULL(dn.cantidad,0))) AS cantidad,
							(df.valor - SUM(IFNULL(dn.valor,0))) AS valor,
							df.porcentaje_iva,
							(df.iva - SUM(IFNULL(dn.iva,0))) AS iva,
							df.porcentaje_impoconsumo,
							(df.impo - SUM(IFNULL(dn.impo,0))) AS impo, 
							(df.total - SUM(IFNULL(dn.total,0))) AS total,
							df.account_id
						FROM detalle_factura df
						INNER JOIN menus_items mi ON mi.id = df.menus_items_id 
						LEFT JOIN nota_credito_detalle dn ON dn.detalle_factura_id = df.id
						WHERE df.prefijo_facturacion = '{$factura->prefijo_facturacion}' AND df.consecutivo_facturacion = '{$factura->consecutivo_facturacion}' AND df.tipo = '{$factura->tipo}'
						GROUP BY df.id, df.menus_items_id, mi.nombre, df.cantidad, df.valor,  df.porcentaje_iva, df.iva, df.porcentaje_impoconsumo, df.impo, df.total, df.account_id";

				$db = DbBase::rawConnect();
				$response['data']['detalle'] = $db->inQueryAssoc($sql);

				# PAGOS FACTURA - PAGOS NOTAS
				$sql = "SELECT 
							f.id AS factura_id,
							pf.id AS pagos_factura_id,
							pf.formas_pago_id, 
							(pf.pago -SUM(IFNULL(ncp.pago,0))) AS pago,
							'' as numero,
							'' as valor,
							'' as fecha
						FROM factura f
						INNER JOIN pagos_factura pf ON pf.prefijo_facturacion = f.prefijo_facturacion AND f.consecutivo_facturacion = pf.consecutivo_facturacion AND pf.tipo = f.tipo
						LEFT JOIN nota_credito_pago ncp ON ncp.pagos_factura_id = pf.id
						WHERE 
							f.prefijo_facturacion = '{$factura->prefijo_facturacion}' 
							AND f.consecutivo_facturacion = '{$factura->consecutivo_facturacion}' 
							AND f.tipo = '{$factura->tipo}'
						GROUP BY f.id, pf.id, pf.formas_pago_id, pf.pago";

				$response['data']['pago_factura'] = $db->inQueryAssoc($sql);

				# Formas de pago
				foreach($this->FormasPago->find() as $forma){
					$response['data']['formas_pago'][] = $forma;
				}

				# imagen 
				$response['data']['img'] = Tag::image(array('pos2/minus.gif','style'=>'cursor:pointer;', 'onclick'=>'nota.deleteForma(this)')); 
				$response['data']['iconprint'] = Tag::image("pos2/print-p.png", "width: 23");
				
				# Notas credito factura
				$response['data']['notas'] = [];
				foreach($factura->getNotaCredito() as $nota){
					$response['data']['notas'][] = [
						'data'=> $nota,
						'_self' => Utils::getKumbiaURL("nota_credito/imprimir/".base64_encode($nota->id))
					];
				}
			}

		} catch (TransactionFailed $e) {
			$response['success'] = false;
			$response['message'] = $e->getMessage();
		}

		return $response;
		
	}

	public function saveAction(){
		
		$this->setResponse('json');

		$response = [
			'success' => true,
			'message' => '',
			'data' => []
		];

		$controllerRequest = ControllerRequest::getInstance();

		# DATOS PASADOS POR POS
		$formas_pago = json_decode($this->getPostParam('formas_pago'));
		$factura_referencia =  json_decode($this->getPostParam('factura'));
		$detalles = json_decode($this->getPostParam('detalle'));

		try {

			$transaction = TransactionManager::getUserTransaction();

			# FACTURA NOTA CREDITO
			$factura = $this->Factura->findFirst($factura_referencia->id);
			if(!$factura)
				throw new Exception("Error no existe la factura de referencia", 1);

			# CONSECUTIVOS NOTA CREDITO - RESOLUCION FACTURAS
			$this->ResolucionFactura->setTransaction($transaction);
			$salon = $this->ResolucionFactura->findForUpdate($factura->resolucion_factura_id);

			# VALIDAMOS QUE EL SALON EXISTA
			if(!$salon)
				throw new Exception("No existe resolución de nota credito vinculada con la factura de referencia", 1);
			
			if(empty($salon->prefi_nota_credi))
				throw new Exception("No esta parametrizado el prefijo nota credito", 1);

			if(empty($salon->consec_inici_nota_credi))
				throw new Exception("No esta parametrizado el consecutivo inicial nota credito", 1);

			if(empty($salon->fecha_ini_nota_credi))
				throw new Exception("No esta parametrizado la fecha inicial nota credito en el ambiente.", 1);

			if(empty($salon->fecha_fin_nota_credi))
				throw new Exception("No esta parametrizado la fecha final nota credito en el ambiente.", 1);

			empty($salon->consec_nota_credi) ? $salon->consec_nota_credi = $salon->consec_inici_nota_credi : $salon->consec_nota_credi++;

			# VALIDAMOS QUE NO EXISTA CONSECUTIVO NOTA CREDITO CREADO
			$nota = $this->NotaCredito->findFirst("prefijo_documento = '$salon->prefi_nota_credi' AND  consecutivo_documento='$salon->consec_nota_credi'");

			if($nota)
				throw new Exception("Existe transacción nota en proceso, por favor volver a intentar.", 1);

			# DATOS SISTEMA
			$this->Datos->findFirst();
			$fechaSistema = (string) $this->Datos->getFecha();

			# CONTROL NOTAS DE AJUSTES AUTOMATICA
			$control_descarga = $factura_referencia->fecha != $fechaSistema ? 'N' : 'S';

			# REGISTRAMOS LA NOTA CREDITO
			$nota_credito = new NotaCredito();
			$nota_credito->setTransaction($transaction);
			$nota_credito->prefijo_documento = $salon->prefi_nota_credi;
			$nota_credito->consecutivo_documento = $salon->consec_nota_credi;
			$nota_credito->tipo = 'NC';
			$nota_credito->factura_id = $factura_referencia->id;
			$nota_credito->prefijo_facturacion = $factura->prefijo_facturacion;
			$nota_credito->consecutivo_facturacion = $factura->consecutivo_facturacion;
			$nota_credito->tipo_facturacion = $factura->tipo;
			$nota_credito->tipo_nota = $factura->tipo_factura;
			$nota_credito->fecha_factura = $factura->fecha;

			$nota_credito->fecha_ini_nota_credi = $salon->fecha_ini_nota_credi;
			$nota_credito->fecha_fin_nota_credi = $salon->fecha_fin_nota_credi;
			$nota_credito->numero_inicial = $salon->consec_inici_nota_credi;
			$nota_credito->numero_final = $salon->consec_final_nota_credi;
			$nota_credito->propina = $factura_referencia->propina_nota_credi;
			$nota_credito->total = $factura_referencia->total_nota_credi;
			$nota_credito->estado = 'A';
			$nota_credito->fecha = $fechaSistema;
			$nota_credito->hora = Date::getCurrentTime('H:i');
			$nota_credito->created_at = Date::getCurrentDate();
			$nota_credito->usuarios_id = Session::getData('usuarios_id');
			$nota_credito->usuarios_nombre = Session::get('usuarios_nombre');

			if($nota_credito->save()==false){
				foreach($nota_credito->getMessages() as $message){
					$transaction->rollback();
					throw new Exception($message->getMessage(), 1);
				}
			}

			# CORREMOS EL CONSECUTIVO
			if($salon->save()==false){
				foreach($nota_credito->getMessages() as $message){
					$transaction->rollback();
					throw new Exception($message->getMessage(), 1);
				}
			}

			$subtotal = 0;
			$total_iva = 0;
			$total_impoconsumo = 0;
			
			# DETALLES FACTURA
			foreach ($detalles as $key => $detalle) {
				
				if(!empty($detalle->cannot)){

					# CALCULAR IMPUESTO PRODUCTO
					$iva = $detalle->iva == 0 ? 0 : ($detalle->iva / $detalle->cantidad) * $detalle->cannot;
					$impo = $detalle->impo == 0 ? 0 : ($detalle->impo / $detalle->cantidad) * $detalle->cannot;
					$valor = $detalle->valor == 0 ? 0 : ($detalle->valor / $detalle->cantidad) * $detalle->cannot;
					$total = $detalle->total == 0 ? 0 : ($detalle->total / $detalle->cantidad) * $detalle->cannot;

					$mdetalle = new NotaCreditoDetalle();
					$mdetalle->setTransaction($transaction);
					$mdetalle->nota_credito_id = $nota_credito->id;
					$mdetalle->detalle_factura_id = $detalle->id;
					$mdetalle->fecha_factura = $factura->fecha;
					$mdetalle->menus_items_id = $detalle->menus_items_id;
					$mdetalle->menus_items_nombre = $detalle->nombre;
					$mdetalle->porcentaje_iva = $detalle->porcentaje_iva;
					$mdetalle->porcentaje_impoconsumo = $detalle->porcentaje_impoconsumo;
					$mdetalle->cantidad = $detalle->cannot;
					$mdetalle->descuento = '0';
					$mdetalle->valor = $valor;
					$mdetalle->iva = $iva;
					$mdetalle->impo = $impo;
					$mdetalle->servicio = '0';
					$mdetalle->total = $total;

					# SUBTOTALES
					$subtotal += $valor;
					$total_iva += $iva;
					$total_impoconsumo += $impo;


					if($mdetalle->save()==false){
						foreach($mdetalle->getMessages() as $message){
							$transaction->rollback();
							throw new Exception($message->getMessage(), 1);
						}
					}

					# VALIDAMOS QUE EL PRODUCTO HAYA TENIDO DESCARGA DE INVENTARIO
					foreach ($this->invepos->find("Account_id = '{$detalle->account_id}'") as $key => $invepos) {

						$cantidad  = $invepos->getCantidad()  > 0 ? $detalle->cannot : 0; 
						$cantidadu = $invepos->getCantidadu() > 0 ? $detalle->cannot : 0;
						
						$inveposnc = new Inveposnc();
						$inveposnc->setTransaction($transaction);
						$inveposnc->setNotaCreditoId($nota_credito->id);
						$inveposnc->setFecha($fechaSistema);
						$inveposnc->setAlmacen($invepos->getAlmacen());
						$inveposnc->setCentroCosto($invepos->getCentroCosto());
						$inveposnc->setTipo($invepos->getTipo());
						$inveposnc->setCodigo($invepos->getCodigo());
						$inveposnc->setMenusItemsId($invepos->getMenusItemsId());
						$inveposnc->setCantidadu($cantidadu);
						$inveposnc->setCantidad($cantidad);
						$inveposnc->setInveposId($invepos->getId());
						$inveposnc->setEstado($control_descarga);

						if ($inveposnc->save() == false) {
							foreach($inveposnc->getMessages() as $message){
								Flash::error('inveposnc: '.$message->getMessage());
							}
							$transaction->rollback();
						}

					}
					
				}
			}

			# ACTUALIZAR DATOS SUBTOTALES
			$nota_credito->subtotal = $subtotal;
			$nota_credito->total_iva = $total_iva;
			$nota_credito->total_impoconsumo = $total_impoconsumo;

			if($nota_credito->update()==false){
				foreach($nota_credito->getMessages() as $message){
					$transaction->rollback();
					throw new Exception($message->getMessage(), 1);
				}
			}

			# PAGOS FACTURA
			foreach ($formas_pago  as $key => $pago) {
				if(!empty($pago->valor)){
					$notapago = new NotaCreditoPago();
					$notapago->setTransaction($transaction);
					$notapago->nota_credito_id = $nota_credito->id;
					$notapago->formas_pago_id = $pago->forpag;
					$notapago->fecha_factura = $factura->fecha;
					$notapago->pago = $pago->valor;
					$notapago->pagos_factura_id = $pago->pagos_factura_id;

					if($notapago->save()==false){
						foreach($notapago->getMessages() as $message){
							$transaction->rollback();
							throw new Exception($message->getMessage(), 1);
						}
					}

				}
			}

			# AJUSTES DE INVENTARIOS
			$config = CoreConfig::readFromActiveApplication('app.ini');
			if (!isset($config->pos->back_version)) {
				$interpos = new InterfasePOS4();
			} else {
				if (version_compare($config->pos->back_version, '6.0', '>=')) {
					$interpos = new InterfasePOS4();
				} else {
					$transaction->rollback();
					throw new TransactionFailed('No esta definida la descarga InterFasePOS4',1,null);
				}
			}

			# REALISER AJUSTE SI ES NC FECHA DIFERENTE A LA FACTURA
			if($control_descarga == 'N'){
				try{
					$interpos->adjustCreditNote($nota_credito->id);
				} catch(Exception $e){
					$transaction->rollback($e->getMessage());
				}
			}

			# GENERAR XML
			if($nota_credito->tipo_nota == 'E'){

				# VALIDAMOS QUE EXISTA LAS LIBRERIAS DE PROCESAMIENTO XML CARVAL
				if(!file_exists(KEF_ABS_PATH.'../fepos/factura_cajasan/notas_credito.class.php'))
					throw new Exception("No existe la libreria de procesamiento xml nota credito carvajal", 1);

				# CARGAR LA LIBRERIA DE PROCESAMIENTO XML
				require_once KEF_ABS_PATH.'../fepos/factura_cajasan/notas_credito.class.php';

				# VALIDAR QUE LA CLASE EXISTA
				if(!class_exists('NotasCredito'))
					throw new Exception("No existe exite la clase de procesamiento de xml de carvajal", 1);

			}
			
			# CONFIRMAMOS TRANSACCION
			$transaction->commit();

			if($nota_credito->tipo_nota == 'E'){
				$facturacion = new NotasCredito();
				$facturacion->generarXMLNota($nota_credito->id);
			}

			$response['data']['urlprint'] = Utils::getKumbiaURL("nota_credito/imprimir/".base64_encode($nota_credito->id));


		} catch(DbLockAdquisitionException $e){
			$response['success'] = false;
			$response['message'] =  $e->getMessage().' linea '.$e->getLine();
		} catch(TransactionFailed $e){
			$response['success'] = false;
			$response['message'] = $e->getMessage().' linea '.$e->getLine();

		} catch(Exception $e){
			$response['success'] = false;
			$response['message'] =  $e->getMessage().' linea '.$e->getLine();
		}

		return $response;

	}


	public function imprimirAction($nota_credito_id){

		# DECODIFICAR ID
		$nota_credito_id = base64_decode($nota_credito_id);

		$nota_credito = $this->NotaCredito->FindFirst($nota_credito_id);
		$factura = $this->Factura->FindFirst($nota_credito->factura_id);
		$nota_credito_detalle = $this->NotaCreditoDetalle->find("nota_credito_id = '$nota_credito_id'");
		$nota_credito_pago = $this->NotaCreditoPago->find("nota_credito_id = '$nota_credito_id'");

		$this->setParamToView('nota_credito', $nota_credito);
		$this->setParamToView('factura', $factura);
		$this->setParamToView('nota_credito_detalle', $nota_credito_detalle);
		$this->setParamToView('nota_credito_pago', $nota_credito_pago);


	}



	


}
