<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Persé
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class MobileController extends ApplicationController {

	public function initialize(){
		parent::initialize();
		View::setRenderLevel(View::LEVEL_LAYOUT);
	}

	public function accountsAction(){
		$accounts = new Accounts();
		$this->setParamToView('cuentas', $accounts->getResume());
		$this->setParamToView('total', $accounts->getTotal());
	}

	public function detailsAction($numeroCuenta){
		$numeroCuenta = $this->filter($numeroCuenta, 'int');
		if($numeroCuenta>0){

			$traslate = $this->_loadTraslation();
			$guestInfo = SessionNamespace::get('guestInfo');
			$conditions = "numfol='{$guestInfo->getFolio()}' AND numcue='$numeroCuenta' AND estado IN ('N', 'A')";
			$carghab = $this->Carghab->findFirst($conditions);
			if($carghab==false){
				Flash::error($traslate['noExCuenta']);
				return $this->routeToAction('index');
			}

			$accounts = new Accounts();
			$cuentas = $accounts->getResume($numeroCuenta);

			$movimientos = $accounts->getMovement($carghab);

			$this->setParamToView('cuentas', $cuentas);
			$this->setParamToView('abonos', $accounts->getAbonos());
			$this->setParamToView('consumos', $accounts->getConsumos());
			$this->setParamToView('movimientos', $movimientos);

		} else {
			$this->routeToAction('accounts');
		}
	}

	public function indexAction(){

	}

}