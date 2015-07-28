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
 * Validacion_CategoriasController
 *
 * Controlador de validación de categorias en socios
 *
 */
class Validacion_CategoriasController extends ApplicationController
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
		$fechaIni = $periodoArray[0] . "-" . $periodoArray[1] . "-01";
		$fechaFin = new Date($fechaIni);
		$fechaFin->toLastDayOfMonth();

		Tag::displayTo('fechaIni', $fechaIni);
		Tag::displayTo('fechaFin', $fechaFin->getDate());
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
			
			$reportType = $this->getPostParam('reportType', 'alpha');
			
			$config = array(
				'reportType' => $reportType
			);
			
			//Generamos factura
			$sociosReports = new SociosReports();
			$config['file'] = $sociosReports->validarCategorias($config);

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
