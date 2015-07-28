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


Core::importFromLibrary('Hfos/Tpc','TpcFactura.php');
Core::importFromLibrary('Hfos/Tpc','TpcInformes.php');

/**
 * Cuenta_cobroController
 *
 * Controlador de informes de cuentas de cobros
 */
class Cuenta_cobroController extends ApplicationController{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	/**
	 * Carga cosas en la primera pantalla
	 */
	public function indexAction()
	{
		$this->setParamToView('message', 'Ingrese un criterio de búsqueda para generar cuentas de cobro a socios');			
	}

	/**
	 * Consulta los propietarios y genera el informe
	 */
	public function generarAction()
	{
		$this->setResponse('json');
		try
		{
			$sociosId 		= $this->getPostParam('sociosId', 'int');
			$periodoIni		= $this->getPostParam('periodoIni', 'int');
			$periodoFin		= $this->getPostParam('periodoFin', 'int');
			$tipoContrato	= $this->getPostParam('tipoContrato', 'int');
			$reportType 	= $this->getPostParam('reportType', 'alpha');

			//Creamos array de configuración de informe
			$config = array(
				'sociosId'		=> $sociosId,
				'periodoIni'	=> $periodoIni,
				'periodoFin'	=> $periodoFin,
				'tipoContrato'	=> $tipoContrato,
				'reportType'	=> $reportType
			);

			#Definimos si es para un socio o todos
			if ($sociosId>0) {
				$config['socios'] = array($sociosId);
			} else {

				#Todos los socios activos
				$config['socios'] = array();
				$sociosObj = EntityManager::get('Socios')->find(array('conditions'=>"estado_contrato='A'", 'limit'=>5));
				foreach ($sociosObj as $socios) 
				{
					$config['socios'][] = $socios->getId();
				}
				
			}
			//$config['socios'] = array('37629');#Sin pagos
			//$config['socios'] = array('35467');#solo inicial
			//$config['socios'] = array('36188');#financiacion

			
			$tpcFactura = new TpcFactura();
			$tpcFactura->makeFactura($config);
			
			return array(
				'status'	=> 'OK',
				'message'	=> 'Se generó la(s) cuenta(s) de cobro correctamente'
			);
		}
		catch(Exception $e){
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage()
			);
		}
	}

	/**
	 * Consulta los propietarios y genera el informe
	 */
	public function imprimirAction()
	{
		$this->setResponse('json');
		try
		{
			$sociosId 		= $this->getPostParam('sociosId', 'int');
			$periodoIni		= $this->getPostParam('periodoIni', 'int');
			$periodoFin		= $this->getPostParam('periodoFin', 'int');
			$tipoContrato	= $this->getPostParam('tipoContrato', 'int');
			$reportType 	= $this->getPostParam('reportType', 'alpha');

			//Creamos array de configuración de informe
			$config = array(
				'sociosId'		=> $sociosId,
				'periodoIni'	=> $periodoIni,
				'periodoFin'	=> $periodoFin,
				'tipoContrato'	=> $tipoContrato,
				'reportType'	=> $reportType
			);
			
			TpcInformes::cuentaCobro($config);

			if(!isset($config['file'])){
				throw new Exception('Error al generar la impresión de la(s) cuenta(s) de cobro');
			}
			
			return array(
				'status'	=> 'OK',
				'message'	=> 'Se generó el informe de propietarios correctamente',
				'file'		=> 'public/'.$config['file']
			);
		}
		catch(Exception $e){
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage()
			);
		}
	}
	
}
