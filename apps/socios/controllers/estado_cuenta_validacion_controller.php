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
Core::importFromLibrary('Hfos/Socios','SociosEstadoCuenta.php');

/**
 * Estado_Cuenta_ValidacionController
 *
 * Controlador de vaidación de generación de estados de cuenta por socios vs contabilidad
 *
 */
class Estado_Cuenta_ValidacionController extends ApplicationController
{

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
	}

	
	/**
	 * Vista principal
	 *
	 */
	public function indexAction()
	{
		$periodoStr = SociosCore::getCurrentPeriodo();
		
		$fechas = EntityManager::get('EstadoCuenta')->find(array('columns'=>'fecha','group'=>'fecha','order'=>'fecha DESC')); 
		$this->setParamToView('fechas', $fechas);

		$this->setParamToView('mes',$periodoStr);
		$this->setParamToView('message', 'De click en Validar para revisar estados de cuenta  que esten iguales en contabilidad');
	}
	
	/**
	 * Metodo que genera la validación
	 *
	 */
	public function reporteAction($fecha=false,$reportType=false)
	{
		$this->setResponse('json');
		
		try 
		{

			$transaction = TransactionManager::getUserTransaction();
			
			if (!$fecha) {
				$fecha = $this->getPostParam('fecha', 'date');
				$reportType = $this->getPostParam('reportType', 'alpha');
			}
			$dateFecha = new Date($fecha);
			
			$config = array(
				'reportType' => $reportType,
				'fecha' => $fecha
			);
			
			//Generamos factura
			$sociosReports = new SociosEstadoCuenta();
			$config['file'] = $sociosReports->estadoCuentaValidacion($config);

			if (isset($config['file']) && $config['file']==false) {
				throw new Exception("No hay datos a mostrar, debe generar el estado de cuenta primero con la fecha '$fecha'");
			}

			return array(
				'status'	=> 'OK',
				'message' 	=> 'La validación de estados de cuenta fue generado correctamente',
				'file'		=> 'public/temp/'.$config['file']
			);

		}
		catch (Exception $e) {
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}
}
