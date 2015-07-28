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

/**
 * Consulta_SociosController
 *
 * Controlador de la consulta de socios
 *
 */
class Consulta_SociosController extends ApplicationController {

	public function initialize() {
		$controllerRequest = ControllerRequest::getInstance();
		if ($controllerRequest->isAjax()) {
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
	}
	
	/**
	 * Vista principal
	 *
	 */
	public function indexAction() {
		$this->setParamToView('message', 'Seleccione los criterios de busqueda');
		$actividades = EntityManager::get('Hobbies')->find(array('order'=>'nombre ASC')); 
		$this->setParamToView('actividades', $actividades);
	}

	/**
	 * Cambia el numero de accion de un socio
	 *
	 * 
	 */
	public function generarAction()
	{
	    $this->setResponse('json');

	    try 
	    {

	    	$transaction = TransactionManager::getUserTransaction();

	    	//Parametros de busqueda
		    $socioId = $this->getPostParam('socios_id', 'int');
		    $actividadId = $this->getPostParam('actividad', 'int');

		    $titulo = $this->getPostParam('titulo', array('alpha','striptags'));
		    $cargo = $this->getPostParam('cargo', array('alpha','striptags'));

			//$reportType	= $this->getPostParam('reportType', 'alpha');
			$reportType	= 'html';
			
			$entities = array('Socios', 'EstadosSocios');
			$report	= ReportBase::factory($reportType);
			$wheres	= array('1=1');
			$headers = array();
			$i = 1;
			
			//TITULO PRINCIPAL
			$headers[]= new ReportText('INFORME DE SOCIOS', array(
				'fontSize' => 16,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			$headers[]= new ReportText('Fecha de emisión: '.date('Y-m-d'), array(
				'fontSize' => 13,
				'fontWeight' => 'bold',
				'textAlign' => 'center'
			));

			//Condiciones de búsqueda

			//$columns = array('{#Socios}.numero_accion','{#Socios}.fecha_ingreso','{#Socios}.nombres','{#Socios}.apellidos','{#Socios}.identificacion','{#Socios}.fecha_nacimiento','{#Socios}.direccion_casa','{#Socios}.telefono_casa','{#Socios}.correo_1','{#EstadosSocios}.nombre as estado');

			$sociosArray = array();

			if ($socioId>0) {

			    //Validamos que exista
			    $socio = BackCacher::getSocios($socioId);
			    if ($socio==false || !$socio->getSociosId()) {
			        return array(
						'status' => 'FAILED',
						'message' => 'El socio ('.$socioId.') no existe'
					);
			    }
			    $sociosArray[]=$socio->getSociosId();

			}

			//$columns['actividad'] 	= '( SELECT GROUP_CONCAT(nombre) FROM hobbies WHERE id IN (SELECT hobbies_id FROM actividades WHERE socios_id={#Socios}.socios_id) ) as actividad';
			if ($actividadId>0) {

				$actividadesObj = EntityManager::get('Actividades')->find(array('conditions'=>'hobbies_id='.$actividadId, "group"=>"socios_id"));

				foreach ($actividadesObj as $actividades)
				{
					$sociosArray[]= $actividades->getSociosId();
					unset($actividades);
				}
			}

			//$columns['titulo'] 	= '( SELECT GROUP_CONCAT(titulo) FROM estudios WHERE socios_id={#Socios}.socios_id ) as titulo';
			if (empty($titulo)==false) {

				$estudiosObj = EntityManager::get('Estudios')->find(array('conditions'=>"titulo LIKE '%".$titulo."%'", "group"=>"socios_id"));

				foreach($estudiosObj as $estudios) {
					$sociosArray[]= $estudios->getSociosId();
				}
			}

			//$columns['cargo'] 	= '{#Explaboral}.cargo';
			//$columns['cargo'] 	= '( SELECT GROUP_CONCAT(cargo) FROM explaboral WHERE socios_id={#Socios}.socios_id ) as cargo';
			if (empty($cargo)==false) {

				$explaboralObj = EntityManager::get('Explaboral')->find(array('conditions'=>"cargo LIKE '%".$cargo."%'", "group"=>"socios_id"));

				foreach($explaboralObj as $explaboral) {
					$sociosArray[]= $explaboral->getSociosId();
				}
			}

			if (!count($sociosArray)) {

				$sociosObj = EntityManager::get('Socios')->find(array('columns'=>'socios_id'));
				foreach ($sociosObj as $socios) 
				{

					//cache socio to more fast 
					$socio = BackCacher::getSocios($socios->getSociosId());
					
					$sociosArray[]= $socios->getSociosId();
					unset($socio,$socios);
				}

			}

			$report->setHeader($headers);
			$report->setDocumentTitle('Informe de Propietarios');
			$report->setColumnHeaders(array(
				'NUM.',//0
				'NÚMERO DE ACCIÓN',//1
				'NOMBRE',//2
				'CÉDULA',//3
				'FECHA DE INGRESO',//4
				'FECHA DE NACIMIENTO',//5
				'DIRECCIÓN DE CASA',//6
				'TELÉFONO DE CASA',//7
				'E-MAIL',//8
				'CATEGORIA',//9
				'SOSTENIMIENTOS SIN IVA',//10
				'CONSUMO MINIMO SIN IVA',//11
				'ESTADO',//12
				'ACTIVIDAD',//13
				'TITULO',//14
				'CARGO'//15
			));
			$report->setCellHeaderStyle(new ReportStyle(array(
				'textAlign' => 'center',
				'backgroundColor' => '#eaeaea'
			)));
			$report->setColumnStyle(array(0,1,2,3,4,5,6,7,8,9,12,13,14,15), new ReportStyle(array(
				'textAlign' => 'center',
				'fontSize' => 11
			)));
			$report->setColumnStyle(array(10,11), new ReportStyle(array(
				'textAlign' => 'right',
				'fontSize' => 11
			)));
			$numberFormat = new ReportFormat(array(
				'type' => 'Number',
				'decimals' => 2
			));
			$report->setColumnFormat(array(10,11), $numberFormat);
			$report->start(true);


			//Buscamos segun array de ids de socios
			foreach($sociosArray as $sociosId) 
			{

				//Get info de socio
				$socio = BackCacher::getSocios($sociosId);

				if ($socio==false) {
					$transaction->rollback('El socio con id '.$sociosId.' no existe');
				}

				//Get information of Actividades
				$actividadesName 	= array();
				$actividadesObj 	= EntityManager::get('Actividades')->find(array('conditions'=>'socios_id='.$sociosId));
				foreach ($actividadesObj as $actividades) 
				{
					$actividadesName[]= $actividades->getHobbies()->getNombre();
					unset($actividades);
				}
				unset($actividadesObj);

				//Get information of Estudios
				$estudiosName 	= array();
				$estudiosObj 	= EntityManager::get('Estudios')->find(array('conditions'=>'socios_id='.$sociosId));
				foreach ($estudiosObj as $estudios) 
				{
					$estudiosName[]= $estudios->getTitulo();
					unset($estudios);
				}
				unset($estudiosObj);

				//Get information of Explaboral
				$cargosName 	= array();
				$explaboralObj 	= EntityManager::get('Explaboral')->find(array('conditions'=>'socios_id='.$sociosId));
				foreach ($explaboralObj as $explaboral) 
				{
					$cargosName[]= $explaboral->getCargo();
					unset($explaboral);
				}
				unset($explaboralObj);

				//Cargos fijos
				$totalSoste = 0;
				$asignacionCargosObj = EntityManager::get('AsignacionCargos')->find("socios_id='$sociosId' AND estado='A'");
				foreach ($asignacionCargosObj as $asignacionCargos) 
				{
					$cargoFijoId = $asignacionCargos->getCargosFijosId();
					$cargoFijo = BackCacher::getCargosFijos($cargoFijoId);
					if (!$cargoFijo) {
						throw new SociosException("No existe cargo fijo con Id '$cargoFijoId'");
					}

					//Sostenimiento
					if ($cargoFijo->getClaseCargo()=='S') {
						$totalSoste += $cargoFijo->getValor();
					}
					unset($asignacionCargos);
				}
				unset($asignacionCargosObj);

				$porce = "0";
				if ($socio->getPorcMoraDesfecha()) {
					$porce = $socio->getPorcMoraDesfecha();
				}

				//Add new row
				$report->addRow(array(
					$i,
					$socio->getNumeroAccion(),
					$socio->getNombres().' '.$socio->getApellidos(),
					$socio->getIdentificacion(),
					$socio->getFechaIngreso(),
					$socio->getFechaNacimiento(),
					$socio->getDireccionCasa(),
					$socio->getTelefonoCasa(),
					$socio->getCorreo1(),
					$socio->getTipoSocios()->getNombre()." (".$porce."%)",
					$totalSoste,
					$socio->getTipoSocios()->getCuotaMinima(),
					$socio->getEstadosSocios()->getNombre(),
					implode(', ', $actividadesName),
					implode(', ', $estudiosName),
					implode(', ', $cargosName),
				));
				$i++;
			}
			$report->finish();
			$config['file']= $report->outputToFile('public/temp/consulta-socios');

			return array(
				'status' 	=> 'OK',
				'message' 	=> 'Se genero el informe exitosamente',
				'file'		=> 'temp/'.$config['file']
			);				

		}
		catch(Exception $e) {
			return array(
			    'status' 	=> 'FAILED',
			    'message' 	=> $e->getMessage()
			);
		}
		
	}

}
