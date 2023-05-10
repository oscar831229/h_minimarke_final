<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

class FacturaController extends ApplicationController
{

	public function indexAction($currentCuenta=0, $currentMaster=0)
	{

		$preview = $this->getRequestInstance()->isSetQueryParam('preview');
		$reprint = $this->getRequestInstance()->isSetQueryParam('reprint');

		$currentCuenta = $this->filter($currentCuenta, 'int');
		$currentMaster = $this->filter($currentMaster, 'int');
		if($currentMaster<=0){
			$currentMaster = Session::get('current_master', 'int');
		}
		if($currentCuenta<=0){
			$currentCuenta = Session::get('numero_cuenta', 'int');
		}

		$accountCuenta = $this->AccountCuentas->findFirst("account_master_id=$currentMaster AND cuenta=$currentCuenta");
		if ($accountCuenta == false) {
			return $this->routeTo(array('controller' => 'pay'));
		} else {
			if ($accountCuenta->estado == 'C') {
				if ($accountCuenta->numero == 0) {
					return $this->routeTo(array('controller' => 'pay'));
				} else {
					if ($reprint == true) {
						if ($accountCuenta->tipo_venta == 'F') {
							Flash::error('La factura ' . $accountCuenta->prefijo . ':' . $accountCuenta->numero . ' está anulada');
						} else {
							if ($accountCuenta->tipo_venta == 'S') {
								Flash::error('La factura  ' . $accountCuenta->prefijo . ':' . $accountCuenta->numero . ' no se pudo asignar a socios');
							} else {
								Flash::error('La orden ' . $accountCuenta->prefijo . ':' . $accountCuenta->numero . ' está anulada');
							}
						}
					}
				}
			}
		}

		$transaction = TransactionManager::getUserTransaction();
		$Facturacion = new Facturacion();
		$Facturacion->preview = $preview;
		$Facturacion->reprint = $reprint;
		$response = $Facturacion->genVoice($accountCuenta, $transaction);

		if(!$response['success']){
			try{
				$transaction->rollback();
			}catch(TransactionFailed $e){
				Flash::error($response['error']);
			}
			return $this->routeToAction('showErrores');
		}else{

			# Confirmar cambios facturación
			if($preview==false && $reprint==false){
				$transaction->commit();
			}

		    $redirec_pay = $accountCuenta->estado == 'B' ? true:false;
			$this->setParamToView('redirec_pay',$redirec_pay);
			$this->setParamToView('factura',$response['Factura']);
			$this->setParamToView('detalleFactura', $response['detalleFactura']);
			$this->setParamToView('pagosFactura', $response['pagosFactura']);
			$this->setParamToView('preview', $preview);
			$this->setParamToView('reprint', $reprint);
			$this->setParamToView('accountCuenta', $accountCuenta);
			
		}
	}

	public function showErroresAction()
	{
		$this->setResponse('view');
	}

	public function processAction()
	{
		$this->setResponse('view');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->Factura->setTransaction($transaction);
			foreach ($this->Factura->findForUpdate("resolucion = ''") as $factura) {
				$cuenta = $this->AccountCuentas->findFirst("prefijo = '{$factura->prefijo_facturacion}' AND numero='{$factura->consecutivo_facturacion}' AND estado='L'");
				if ($cuenta != false) {
					foreach ($this->Account->find("account_master_id={$factura->account_master_id}") as $account) {

					}
					$salon = $this->Salon->findFirst($factura->salon_id);
					$factura->resolucion = $salon->autorizacion;
					$factura->fecha_resolucion = $salon->fecha_autorizacion;
					if ($factura->save() == false) {
						foreach ($factura->getMessages() as $message) {
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				} else {
					Flash::error('No existe la cuenta para la factura '."prefijo = '{$factura->prefijo_facturacion}' AND numero='{$factura->consecutivo_facturacion}'");
				}
			}
		}
		catch(TransactionFailed $e){

		}
	}
}
