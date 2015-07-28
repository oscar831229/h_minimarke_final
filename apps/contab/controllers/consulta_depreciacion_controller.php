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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Consulta_DepreciacionController
 *
 * Controlador de las consultas a cuentas de depreciacion de activos
 *
 */
class Consulta_DepreciacionController extends ApplicationController {

	private $_depreciacion = array();

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$ultimaDepreciacion = $this->Depreciacion->maximum('ano_mes');
		if($ultimaDepreciacion==''){
			$ultimaDepreciacion = 'Ninguna';
		}
		$this->setParamToView('ultimaDepreciacion', $ultimaDepreciacion);

		$activos = $this->Activos->find();
		$this->setParamToView('activos', $activos);
	}

	/**
	 *
	 * Metodo que verifica la existencia de una depreciacion por activoId y ano_mes
	 * @param int $activosId
	 * @param int $anoMes
	 * @return boolean
	 */
	protected function checkExistsDepreciacion($activosId,$anoMes){
		$activo = $this->Depreciacion->findFirst("activos_id=".$activosId." AND ano_mes='".$anoMes."'");
		if($activo){
			return true;
		}else{
			return false;
		}
	}

	/**
	 *
	 * Metodo que calcula las depreciaciones de un activo fijo
	 */
	public function calcularDepreciacionaction(){
		$controllerRequest = ControllerRequest::getInstance();
		$activosId = $this->getPostParam('activosId', 'int');

		if(!$activosId){
			return array(
				'status' => 'FAILED',
				'message' => 'Debe ingresar un activo fijo'
			);
		}

		//Se busca el activo a caluclar su depreciación
		$activos = $this->Activos->findFirst("codigo=".$activosId);

		if(!$activos){
			return array(
				'status' => 'FAILED',
				'message' => 'El activo fijo no existe'
			);
		}

		//Se calcula la depreciacion
		$currency = new Currency();

		$depreciacionArray = array();
		$valorCompra = $activos->getValorCompra();
		$valorIvaCompra = $activos->getValorIva();
		$fechaCompra = $activos->getFechaCompra();
		$mesesDepreciar = $activos->getMesesADep();

		if(!$valorIvaCompra){
			$valorIvaCompra = 0;
		}

		$fechaCompraDate = new Date($fechaCompra);
		$nMes = $fechaCompraDate->getMonth();

		$valorCompraMasIva = $valorCompra+$valorIvaCompra;
		$valorDepreciacion = $valorCompra;

		$anoRayaMes = $fechaCompraDate->getYear()."-".$fechaCompraDate->getMonth();
		$anoMes = $fechaCompraDate->getYear().$fechaCompraDate->getMonth();

		$depreciacionArray["depreciacion"] = array();
		$depreciacionArray["depreciacion"][] = array(
			"valorDep" => Currency::number($valorDepreciacion),
			"fecha" => $anoRayaMes,
			"valorARestar" => Currency::number($valorCompraMasIva),
			"ciclo" => "-",
			"saved" => $this->checkExistsDepreciacion($activos->getId(),$anoMes)
		);
		for($i=$mesesDepreciar;$i>0;$i--){
			$fechaCompraDate = new Date($fechaCompra);
			$fechaCompraDate->addMonths($i);


			//Se resta el valor de compra mas iva cada mez al valor del valor de la compra
			$valorARestar = $valorCompraMasIva/$i;
			$valorDepreciacion = abs($valorCompraMasIva - $valorARestar);


			$anoRayaMes = $fechaCompraDate->getYear()."-".$fechaCompraDate->getMonth();
			$anoMes = $fechaCompraDate->getYear().$fechaCompraDate->getMonth();

			//almaceno en un vector
			$depreciacionArray["depreciacion"][] = array(
				"valorDep" => Currency::number($valorDepreciacion),
				"fecha" => $anoRayaMes,
				"valorARestar" => $valorARestar,
				"ciclo" => $i,
				"saved" => $this->checkExistsDepreciacion($activos->getId(),$anoMes)
			);
		}

		$this->setParamToView("valorCompra", Currency::number($valorCompra));
		$this->setParamToView("valorIvaCompra", Currency::number($valorIvaCompra));
		$this->setParamToView("fechaCompra", $fechaCompra->getDate());
		$this->setParamToView("mesesDepreciar", $mesesDepreciar);
		$this->setParamToView("depreciacion", $depreciacionArray["depreciacion"]);


		$this->renderPartial("listaDepreciacion");

	}

}