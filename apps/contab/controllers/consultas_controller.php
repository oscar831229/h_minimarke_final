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
 * ConsultasController
 *
 * Consultas de movimiento por pantalla
 *
 */
class ConsultasController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

		$this->setParamToView('message', 'Indique la cuenta a consultar y haga click en "Consultar"');

		$empresa = $this->Empresa->findFirst();
		$empresa1 = $this->Empresa1->findFirst();
		$fechaCierre = $empresa->getFCierrec();
		$fechaCierre->addDays(1);

		Tag::displayTo('fechaInicial', (string) Date::getFirstDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));
		Tag::displayTo('fechaFinal', (string) Date::getLastDayOfMonth($fechaCierre->getMonth(), $fechaCierre->getYear()));

		$this->setParamToView('fechaCierre', $fechaCierre);
		$this->setParamToView('anoCierre', $empresa1->getAnoc());

	}

	public function consultarAction(){

		$codigoCuenta = $this->getPostParam('cuenta', 'cuentas');
		if($codigoCuenta!=''){
			$cuenta = $this->Cuentas->findFirst("cuenta='$codigoCuenta' AND es_auxiliar='S'");
			if($cuenta==false){
				Flash::error('La cuenta no existe ó no es auxiliar');
				return $this->routeToAction('index');
			}
		} else {
			Flash::error('Indique la cuenta contable auxiliar a consultar');
			return $this->routeToAction('index');
		}

		try {

			$tipo = $this->getPostParam('tipo', 'onechar');
			switch($tipo){
				case 'F':
					$fechaInicial = $this->getPostParam('fechaInicial', 'date');
					$fechaFinal = $this->getPostParam('fechaFinal', 'date');
					if($fechaInicial==''||$fechaFinal==''){
						Flash::error('El rango de fechas es inválido');
						return $this->routeToAction('index');
					}
					list($fechaInicial, $fechaFinal) = Date::orderDates($fechaInicial, $fechaFinal);
					$conditions = "cuenta='$codigoCuenta' AND fecha>='$fechaInicial' AND fecha<='$fechaFinal'";
					$order = 'fecha';
					break;
				case 'N':
					$nitInicial = $this->getPostParam('nitInicial', 'terceros');
					$nitFinal = $this->getPostParam('nitFinal', 'terceros');
					if($nitInicial==''||$nitFinal==''){
						Flash::error('El rango de terceros es inválido');
						return $this->routeToAction('index');
					}
					$conditions = "cuenta='$codigoCuenta' AND nit>='$nitInicial' AND nit<='$nitFinal'";
					$order = 'nit';
					break;
				case 'D':
					$numeroInicial = $this->getPostParam('numeroInicial', 'int');
					$numeroFinal = $this->getPostParam('numeroFinal', 'int');
					if($numeroInicial==0||$numeroFinal==0){
						Flash::error('El rango de números de documento es inválido');
						return $this->routeToAction('index');
					}
					$conditions = "cuenta='$codigoCuenta' AND numero_doc>='$numeroInicial' AND numero_doc<='$numeroFinal'";
					$order = 'numero_doc';
					break;
				default:
					Flash::error('El tipo de consulta es inválida');
					return $this->routeToAction('index');
			}

		}
		catch(DateException $e){
			Flash::error($e->getMessage());
			return $this->routeToAction('index');
		}

		$movis = $this->Movi->find(array($conditions, 'order' => $order));
		if(count($movis)==0){
			Flash::notice('No se encontraron movimientos');
			return $this->routeToAction('index');
		}

		$this->setParamToView('tipo', $tipo);
		$this->setParamToView('cuenta', $cuenta);
		$this->setParamToView('movis', $movis);

	}

}