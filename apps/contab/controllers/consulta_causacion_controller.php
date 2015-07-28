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
 * Controlador de las consultas de Diferidos para hacer causaciones
 *
 */
class Consulta_CausacionController extends ApplicationController {

	private $_causacion = array();

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$diferidos = $this->Diferidos->find();
    $this->setParamToView('diferidos', $diferidos);
	}

	/**
	 *
	 * Metodo que verifica la existencia de una causacion por diferidoId y ano_mes en la tavla amortizacion
	 * @param int $diferidosId
	 * @param int $anoMes
	 * @return boolean
	 */
	protected function checkExistsCausacion($diferidosId,$anoMes){
		$amortizacion = $this->Amortizacion->findFirst("diferidos_id=".$diferidosId." AND ano_mes='".$anoMes."'");
		if($amortizacion){
			return true;
		}else{
			return false;
		}
	}

	/**
	 *
	 * Metodo que calcula las causaciones de un diferido
	 */
	public function calcularCausacionAction(){
		$controllerRequest = ControllerRequest::getInstance();
		$diferidosId = $this->getPostParam('diferidosId', 'int');

		if(!$diferidosId){
			return array(
				'status' => 'FAILED',
				'message' => 'Debe ingresar un diferido'
			);
		}

		//Se busca el diferido a calcular su causación
		$diferido = $this->Diferidos->findFirst("id=".$diferidosId);

		if(!$diferido){
			return array(
				'status' => 'FAILED',
				'message' => 'El diferido no existe'
			);
		}

		//Se calcula la causación
		$currency = new Currency();

		$causacionArray = array();
		$valorCompra = $diferido->getValorCompra();
		$valorIvaCompra = $diferido->getValorIva();
		$fechaCompra = $diferido->getFechaCompra();
		$mesesCausacion = $diferido->getMesesADep();

		if(!$valorIvaCompra){
			$valorIvaCompra = 0;
		}

		$fechaCompraDate = new Date($fechaCompra);
		$nMes = $fechaCompraDate->getMonth();

		$valorCompraMasIva = $valorCompra+$valorIvaCompra;
		$valorCausacion = $valorCompra;

		$anoRayaMes = $fechaCompraDate->getYear()."-".$fechaCompraDate->getMonth();
		$anoMes = $fechaCompraDate->getYear().$fechaCompraDate->getMonth();

		$causacionArray["causacion"] = array();
		$causacionArray["causacion"][] = array(
			"valorDep" => Currency::number($valorCausacion),
			"fecha" => $anoRayaMes,
			"valorARestar" => Currency::number($valorCompraMasIva),
			"ciclo" => "-",
			"saved" => $this->checkExistsCausacion($diferido->getId(),$anoMes)
		);
		for($i=$mesesCausacion;$i>0;$i--){
		  try{
  			$fechaCompraDate = new Date($fechaCompra);
  			$fechaCompraDate->addMonths($i);
      }catch(DateException $e){
        Throw new Exception($e->getMessage());
      }


			//Se resta el valor de compra mas iva cada mez al valor del valor de la compra
			$valorARestar = $valorCompraMasIva/$i;
			$valorCausacion = abs($valorCompraMasIva - $valorARestar);


			$anoRayaMes = $fechaCompraDate->getYear()."-".$fechaCompraDate->getMonth();
			$anoMes = $fechaCompraDate->getYear().$fechaCompraDate->getMonth();

			//almaceno en un vector
			$causacionArray["causacion"][] = array(
				"valorDep" => Currency::number($valorCausacion),
				"fecha" => $anoRayaMes,
				"valorARestar" => $valorARestar,
				"ciclo" => $i,
				"saved" => $this->checkExistsCausacion($diferido->getId(),$anoMes)
			);

      unset($fechaCompraDate);
		}

		$this->setParamToView("valorCompra", Currency::number($valorCompra));
		$this->setParamToView("valorIvaCompra", Currency::number($valorIvaCompra));
		$this->setParamToView("fechaCompra", $fechaCompra->getDate());
		$this->setParamToView("mesesCausacion", $mesesCausacion);
		$this->setParamToView("causacion", $causacionArray["causacion"]);


		$this->renderPartial("listaCausacion");

	}

}