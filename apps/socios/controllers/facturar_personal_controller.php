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

Core::importFromLibrary('Hfos/Socios','SociosCore.php');
Core::importFromLibrary('Hfos/Socios','SociosReports.php');

/**
 * Facturar_PersonalController
 *
 * Controlador de generacion de facturas por socio
 *
 */
class Facturar_PersonalController extends ApplicationController
{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
	}

	
	/**
	 * Vista principal
	 *
	 */
	public function indexAction()
	{
		$periodo = $this->Periodo->findFirst(array('conditions'=> 'cierre="N"'));
		if ($periodo==false) {
			$datosClub = $this->DatosClub->findFirst();
			$fecha = new Date($datosClub->getFCierre());
			$periodo = SociosCore::makePeriodo($fecha->getPeriod());
			$periodoStr = $periodo->getPeriodo();
		} else {
			$periodoStr = $periodo->getPeriodo();
		}
		$this->setParamToView('mes',$periodoStr);
		$this->setParamToView('message', 'De click en Generar Factura');
	}
	
	
	/**
	 * Generar facturas mensuales a socios
	 * 
	 */
	public function generarAction()
	{
		$this->setResponse('json');
		
		try {
			//variables de generación de facturas
			$fechaFactura = $this->getPostParam('dateIni','date');
			$fechaVencimiento = $this->getPostParam('dateFin','date');

			$sostenimiento = $this->getPostParam('sostenimiento');
			$administracion = $this->getPostParam('administracion');
			$novedades = $this->getPostParam('novedades');
			$consumoMinimo = $this->getPostParam('consumoMinimo');
			$interesesMora = $this->getPostParam('interesesMora');
			$ajusteSostenimiento = $this->getPostParam('ajusteSostenimiento');

			$sociosId = $this->getPostParam('sociosId','int');
			if (!$sociosId) {
				throw new Exception('Es necesario dar el socio para generar la factura');
			}
			
			$socios = BackCacher::getSocios($sociosId);
			if ($socios->getEstadosSocios()->getAccion()=='I') {
				throw new Exception('El socio tiene el estado "'.$socios->getEstadosSocios()->getNombre().'" con accion de no generar factura.');
			}
			
			//Buscamos en el periodo
			$debug=true;
			$transaction = TransactionManager::getUserTransaction();
			
			$periodo = SociosCore::getCurrentPeriodo();
			
			//Crea la(s) factura(s)
			$configMovi = array(
				'periodo'	=> $periodo,
				'sociosId'	=> $sociosId,
				'fechaFactura' => $fechaFactura,
				'fechaVencimiento' => $fechaVencimiento,
				'g_sostenimiento' => $sostenimiento,
				'g_administracion' => $administracion,
				'g_novedades' => $novedades,
				'g_consumoMinimo' => $consumoMinimo,
				'g_interesesMora' => $interesesMora,
				'g_ajusteSostenimiento' => $ajusteSostenimiento,
				'showDebug'	=> true
			);
			$sociosFactura = new SociosFactura();

			//Agregamos a configuracion datos estaticos de configuracion
			$sociosFactura->addConfigDefault($configMovi);

			$sociosFactura->generarCargosSocios($configMovi);
			$sociosFactura->generarMovimiento($configMovi);
			$sociosFactura->generarFactura($configMovi);

			$transaction->commit();
			
			return array(
				'status' => 'OK',
				'message' => 'La factura del socio fue re-calculada exitosamente en el periodo '.$periodo.'.'
			);
		}
		catch(SociosException $e) {
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage()
			);
		}
		catch(Exception $e) {
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage()
			);
		}
	}
	
	/**
	 * Metodo que genera la(s) factura(s)
	 *
	 */
	public function reporteFacturaAction()
	{
		$this->setResponse('json');
		
		try 
		{
			$transaction = TransactionManager::getUserTransaction();

			$sociosId = $this->getPostParam('sociosId', 'int');
			$fechaFactura = $this->getPostParam('dateIni','date');
			
			if (!$fechaFactura) {
				return array(
					'status' => 'FAILED',
					'message' => 'Es necesario dar la fecha de la factura'
				);
			}
			
			$config = array(
				'reportType' => 'pdf',
				'fechaFactura' => $fechaFactura,
				'SociosId'		=> $sociosId,
			);

			//Generamos factura
			SociosReports::factura($config, $transaction);
			
			if (isset($config['file']) && $config['file']==false) {
				throw new Exception("No hay datos a mostrar", 1);
			}
			return array(
				'status'	=> 'OK',
				'message' 	=> 'La factura fue generada a PDF correctamente',
				'file'		=> $config['file']
			);

		}catch(Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}
	
	/**
	 * Recorre facturas generadas en el periodo actual y borra su movimiento contable
	 */
	public function borrarAction()
	{
		$this->setResponse('json');
		
		set_time_limit(0);
		
		$controllerRequest = ControllerRequest::getInstance();
		$sociosId = $controllerRequest->getParamPost('sociosId', 'int');
		if ($sociosId<=0) {
			Flash::error('Debe ingresar el id del socio');
		}else{
			try {
				
				$transaction = TransactionManager::getUserTransaction();
			
				//periodo actual
				$periodo = SociosCore::getCurrentPeriodo();		
				
				if ($periodo==false) {
					$transaction->rollback('No existe un periodo sin cerrar actualmente');
				}

				//fecha de factura
				$fechaFactura = $this->getPostParam('dateIni','date');

				$sociosIdArray = array();
				$nitsArray = array();

				$facturaObj = EntityManager::get("Factura")->find(array("conditions"=>"fecha_factura='$fechaFactura' AND socios_id='$sociosId'",'columns'=>'socios_id'));
				foreach ($facturaObj as $factura) 
				{
					$socio = BackCacher::getSocios($factura->getSociosId());
					if ($socio) {
						$sociosIdArray[]=$factura->getSociosId();
						$nitsArray[]=$socio->getIdentificacion();
					}
					unset($socio,$factura);
				}
				unset($facturaObj);
				
				$config = array(
					'nits'		=> $nitsArray,
					'facturas' 	=> $sociosIdArray,
					'fechaFactura' 	=> $fechaFactura,
					'showDebug' => true
				);
				
				$sociosFactura = new SociosFactura();
				$status = $sociosFactura->anularFacturasPeriodo($config);
				
				$transaction->commit();

				return array(
					'status' => 'OK',
					'message' => 'Se borró la factura del socio en el periodo con su movimiento contable.'
				);
			} catch(Exception $e) {
				return array(
					'status' => 'FAILED',
					'message' => $e->getMessage()
				);
			}
		}
	}

	/**
	 * Metodo que verifica si el socio tiene suspension y debe pagar mese acumulados
	 *
	 */
	public function socioSuspendidoAction()
	{
	
		$this->setResponse('json');
		
		try {
			$transaction = TransactionManager::getUserTransaction();
			
			$sociosId = $this->getPostParam('sociosId','int');
			if (!$sociosId) {
				throw new Exception('Es necesario dar el socio para generar cargos mensuales');
			}

			$config = array(
				'sociosId' => $sociosId
			);
						
			$sociosFactura = new SociosFactura();
			$diff = $sociosFactura->facturasNoCausadas($config);			

			return array(
				'status'	=> 'OK',
				'message' 	=> 'OK',
				'diff'		=> $diff
			);

		} catch(Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function getInvoicerAction($numero) 
	{
		$this->setResponse('json');

		try {
			$transaction = TransactionManager::getUserTransaction();
			$reportType = 'pdf';
			$config = array(
				'reportType'	=> $reportType,
				'facturas'		=> array($numero)
			);
			
			$sociosReports = new SociosReports();
			$fileName = $sociosReports->getInvoicerPrint($options);

			$config['file'] = 'public/temp/'.$fileName;
			
			if (isset($config['file']) && $config['file']==false) {
				throw new Exception("No hay datos a mostrar", 1);
			}
			return array(
				'status'	=> 'OK',
				'message' 	=> 'La factura fue generada correctamente',
				'file'		=> $config['file']
			);

		} catch(Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}
}
