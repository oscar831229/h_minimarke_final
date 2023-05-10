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

class Reprocesar_Nota_ElectronicaController extends ApplicationController
{

	
	public function indexAction()
	{
		$this->loadModel('Salon', 'FormasPago');
		$datos = $this->Datos->findFirst();
		$this->setParamToView('notas', $this->NotaCredito->find("fecha='{$datos->getFecha()}' AND estado='A'"));
	}

	public function findNotaAction($prefijo_documento, $consecutivo_documento){

		$this->setResponse('json');

		$response = [
			'success' => true,
			'message' => '',
			'data' => []
		];

		try {

			$NotaCredito = $this->NotaCredito->findFirst("prefijo_documento = '$prefijo_documento' AND consecutivo_documento = '$consecutivo_documento'");
			if($NotaCredito ){
				$response['data']['notacredito'] = $NotaCredito;
				$response['data']['factura'] = $NotaCredito->getFactura();
				$response['data']['iconxml'] = Tag::image("pos2/icon_xml.png", "width: 23");
				$response['data']['xmlloading'] = Tag::image("spin.gif", "width: 23", "class: xmlloading", "style: display:none;");
				$response['data']['iconprint'] = Tag::image("pos2/print-p.png", "width: 23");
				$response['data']['_self'] = Utils::getKumbiaURL("nota_credito/imprimir/".base64_encode($NotaCredito->id));
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
		$nota_id = $this->getPostParam('nota_id');

		try {

			# CONSULTAMOS LA NOTA CREDITO A REPROCESAR
			$notaCredito = $this->NotaCredito->findFirst($nota_id);

			# VALIDAMOS QUE LA NOTA CREDITO EXISTA
			if(!$notaCredito)
				throw new Exception("Error no existe la nota credito a reprocesar", 1);

			if($notaCredito->tipo_nota != 'E')
				throw new Exception("Error la nota credito a reprocesar no es electrónica", 1);
			
			# CARGAR LA LIBRERIA DE PROCESAMIENTO XML
			require_once KEF_ABS_PATH.'../fepos/factura_cajasan/notas_credito.class.php';
			$facturacion = new NotasCredito();
			$facturacion->generarXMLNota($notaCredito->id);

			# PATH DE DESCARGA
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
