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

Core::importFromLibrary('Hfos/Tpc','TpcInformes.php');

/**
 * PropietariosController
 *
 * Controlador de informes de propietarios
 */
class PropietariosController extends ApplicationController{

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	/**
	 * Carga cosas en la primera pantalla
	 */
	public function indexAction(){
		$this->setParamToView('message', 'Ingrese un criterio de búsqueda para consultar los propietarios');
		
		//Signos de porcentajes
		$signos = array(
			'>'		=>	'Mayor que',
			'<'		=>	'Menor que',
			'=='	=>	'Igual que',
			'>='	=>	'Mayor Igual que',
			'<='	=>	'Menor Igual que',
			'<>'	=>	'Diferente de'
		);
		$this->setParamToView('signos', $signos);
		//Porcentajes de saldo
		$porc = array(
			'0'	=>	'0%',
			'10'	=>	'10%',
			'20'	=>	'20%',
			'30'	=>	'30%',
			'40'	=>	'40%',
			'50'	=>	'50%',
			'60'	=>	'60%',
			'70'	=>	'70%',
			'80'	=>	'80%',
			'90'	=>	'90%',
			'100'	=>	'100%',
		);
		$this->setParamToView('porc', $porc);
		//campos de ordenamiento
		$ordenField = array(
			'numero_contrato'	=>	'Número de Acción',
			'nombres'			=>	'Nombre de Afiliado1',
			'apellidos'			=>	'Apellidos de Afiliado1',
			'fecha_compra'		=>	'Fecha de Compra',
			'estado_contrato'	=>	'Estado de Contrato',
			'estado_movimiento'	=>	'Estado de Movimiento',
		);
		$this->setParamToView('ordenField', $ordenField);
	}

	/**
	 * Consulta los propietarios y genera el informe
	 */
	public function buscarAction(){
		$this->setResponse('json');
		try{
			$transaction		= TransactionManager::getUserTransaction();
			$fechaCompraIni		= $this->getPostParam('fechaCompraIni', 'date');
			$fechaCompraFin		= $this->getPostParam('fechaCompraFin', 'date');
			$estadoContrato		= $this->getPostParam('estadoContrato', 'alpha');
			$estadoMovimiento	= $this->getPostParam('estadoMovimiento', 'alpha');
			$porcentajeSigno	= $this->getPostParam('porcentajeSigno');
			$porcentajeValor	= $this->getPostParam('porcentajeValor', 'double');
			$ordenField			= $this->getPostParam('ordenField');
			$sortField			= $this->getPostParam('sortField');
			$reportType 		= $this->getPostParam('reportType', 'alpha');
			//Creamos array de configuración de informe
			$config = array(
				'fechaCompraIni'	=> $fechaCompraIni,
				'fechaCompraFin'	=> $fechaCompraFin,
				'estadoContrato'	=> $estadoContrato,
				'estadoMovimiento'	=> $estadoMovimiento,
				'porcentajeSigno'	=> $porcentajeSigno,
				'porcentajeValor'	=> $porcentajeValor,
				'ordenField'		=> $ordenField,
				'sortField'			=> $sortField,
				'reportType'		=> $reportType
			);
			TpcInformes::propietarios($config, $transaction);
			if(!isset($config['file'])){
				$transaction->rollback('Error al generar el informe de propietarios');
			}
			return array(
				'status'	=> 'OK',
				'message'	=> 'Se generó el informe de propietarios correctamente',
				'file'		=> 'temp/'.$config['file']
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status'	=> 'FAILED',
				'message'	=> $e->getMessage(),
				'debug'		=> print_r($e, true)
			);
		}
	}
	
}
