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

Core::importFromLibrary('Hfos/Socios', 'SociosCore.php');
Core::importFromLibrary('Hfos/Socios', 'SociosReports.php');

/**
 * CumpleanosController
 *
 * Controlador de cumpleaños de socios
 *
 */
class CumpleanosController extends ApplicationController
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
		$periodoArray = SociosCore::periodoToArray();

		Tag::displayTo("mes", $periodoArray[1]);

		$meses = array(
			"01" => "Enero",
			"02" => "Febrero",
			"03" => "Marzo",
			"04" => "Abril",
			"05" => "Mayo",
			"06" => "Junio",
			"07" => "Julio",
			"08" => "Agosto",
			"09" => "Septiembre",
			"10" => "Octubre",
			"11" => "Noviembre",
			"12" => "Diciembre"
		);
		$this->setParamToView('meses', $meses);
		$this->setParamToView('message', 'De click en Validar para revisar la categoria en cada socios dependiendo de la información dada en tipso de socios');
	}
	
	/**
	 * Metodo que genera la validación
	 *
	 */
	public function reporteAction()
	{
		$this->setResponse('json');
		
		try 
		{

			$transaction = TransactionManager::getUserTransaction();
			
			$mes = $this->getPostParam('mes', 'int');
			$reportType = $this->getPostParam('reportType', 'alpha');
			
			$config = array(
				'reportType' => $reportType,
				'mes' => $mes
			);
			
			//Generamos factura
			$sociosReports = new SociosReports();
			$config['file'] = $sociosReports->cumpleanos($config);

			if (isset($config['file']) && $config['file']==false) {
				throw new Exception("No hay datos a mostrar, intent en un rango de fechas diferente");
			}

			return array(
				'status'	=> 'OK',
				'message' 	=> 'La validación de categorias fue generado correctamente',
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
