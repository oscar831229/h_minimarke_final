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

class Reprocesar_Factura_ElectronicaController extends ApplicationController
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

			$factura = $this->Factura->findFirst("prefijo_facturacion = '$prefijo_facturacion' AND consecutivo_facturacion = '$consecutivo_facturacion' AND estado='A' AND tipo_venta='F'");

			if($factura->tipo_factura != 'E'){
				throw new Exception("La factura a reprocesar no es una factura electrónica", 1);
			}

			$response['data']['factura'] = $factura;
			$response['data']['iconxml'] = Tag::image("pos2/icon_xml.png", "width: 23");
			$response['data']['xmlloading'] = Tag::image("spin.gif", "width: 23", "class: xmlloading", "style: display:none;");

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
		$factura_id = $this->getPostParam('factura_id');

		try {

			$factura = $this->Factura->findFirst($factura_id);

			if($factura->tipo != 'F')
				throw new Exception("Solamente se pueden procesar facturas", 1);

			if($factura->tipo_factura != 'E')
				throw new Exception("La factura a generar XML no es electrónica", 1);

			# VALIDAMOS QUE EXISTA LAS LIBRERIAS DE PROCESAMIENTO XML CARVAL
			if(!file_exists(KEF_ABS_PATH.'../fepos/factura_cajasan/procesar_facturas.class.php'))
				throw new Exception("No existe la libreria de procesamiento xml carvajal", 1);

			# CARGAR LA LIBRERIA DE PROCESAMIENTO XML
			require_once KEF_ABS_PATH.'../fepos/factura_cajasan/procesar_facturas.class.php';

			# VALIDAR QUE LA CLASE EXISTA
			if(!class_exists('procesarFacturas'))
				throw new Exception("No existe exite la clase de procesamiento de xml de carvajal", 1);

			$facturacion = new procesarFacturas();
			$facturacion->generarXML($factura->id);

			$response['data']['path'] = Utils::getKumbiaURL("").'/../../..'.$facturacion->xml_generados[0]['ruta'];


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
		$factura = $this->Factura->FindFirst($nota_credito->id);
		$nota_credito_detalle = $this->NotaCreditoDetalle->find("nota_credito_id = '$nota_credito_id'");
		$nota_credito_pago = $this->NotaCreditoPago->find("nota_credito_id = '$nota_credito_id'");

		$this->setParamToView('nota_credito', $nota_credito);
		$this->setParamToView('factura', $factura);
		$this->setParamToView('nota_credito_detalle', $nota_credito_detalle);
		$this->setParamToView('nota_credito_pago', $nota_credito_pago);

	}



	


}
