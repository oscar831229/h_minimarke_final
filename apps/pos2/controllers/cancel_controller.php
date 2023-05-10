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

class CancelController extends ApplicationController
{

	public function indexAction()
	{
		$this->loadModel('ConceptosCancelacion', 'SalonMesas', 'AccountMaster', 'Salon');
	}

	public function docancelAction()
	{

		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isSetPostParam('accountMasterId')){
			$accountMasterId = $controllerRequest->getParamPost('accountMasterId', 'int');
		} else {
			$accountMasterId = Session::get('current_master', 'int');
		}

		try {

			$transaction = TransactionManager::getUserTransaction();
			$this->AccountMaster->setTransaction($transaction);
			$this->SalonMesas->setTransaction($transaction);
			$this->AccountMaster->setTransaction($transaction);
			$this->Factura->setTransaction($transaction);
			$this->Account->setTransaction($transaction);

			$accountMaster = $this->AccountMaster->findFirst($accountMasterId);
			if($accountMaster){
				if($accountMaster->estado=='N'){
					$accountMaster->estado = 'C';
					if($accountMaster->save()==false){
						foreach($accountMaster->getMessages() as $message){
							Flash::error('Account-Master: '.$message->getMessage());
						}
						$transaction->rollback();
					}
					$salonMesa = $this->SalonMesas->findFirst($accountMaster->salon_mesas_id);
					if($salonMesa){
						$salonMesa->estado = 'N';
						if($salonMesa->save()==false){
							foreach($salonMesa->getMessages() as $message){
								Flash::error('Salon-Mesas: '.$message->getMessage());
							}
							$transaction->rollback();
						}
					}
					if($this->Account->count("account_master_id='$accountMasterId' AND estado IN ('A', 'B')")){
						Flash::notice('El pedido tenia items atendidos');
					}

					foreach($accountMaster->getAccountCuentas() as $accountCuenta){

						# Verificar que la cuenta no este liquidada
						if($accountCuenta->estado == 'L'){
							continue;
						}

						if($accountCuenta->numero>0){
							$factura = $accountCuenta->getFactura();
							if($factura!=false){
								if($factura->estado!='N'){
									$factura->estado = 'N';
									if($factura->nombre==''){
										$factura->nombre = 'SIN NOMBRE';
									}
									if($factura->save()==false){
										foreach($factura->getMessages() as $message){
											Flash::error('Factura: '.$message->getMessage());
										}
										$transaction->rollback();
										return;
									} else {
										Flash::notice('Se anuló el comprobante/factura '.$factura->prefijo_facturacion."-".$accountCuenta->numero);
										if($factura->tipo_venta=='F'){
											new POSAudit('ANULO LA FACTURA '.$factura->prefijo_facturacion.'-'.$accountCuenta->numero.' CANCELANDO UN PEDIDO', $transaction);
										} else {
											new POSAudit('ANULO EL COMPROBANTE '.$factura->prefijo_facturacion.'-'.$accountCuenta->numero.' CANCELANDO UN PEDIDO', $transaction);
										}
									}
								}
							}
						}
						$accountCuenta->estado = 'C';
						if ($accountCuenta->save() == false) {
							foreach ($accountCuenta->getMessages() as $message) {
								Flash::error('Account-Cuenta'.$message->getMessage());
							}
							$transaction->rollback();
						}
					}

					foreach($accountMaster->getAccount() as $account){

						if($account->estado == 'L'){
							continue;
						}

						$account->estado = 'C';
						if($account->save()==false){
							foreach($account->getMessages() as $message){
								Flash::error($message->getMessage());
							}
							$transaction->rollback();
						}
					}
					GarbageCollector::freeControllerData('order');
					$transaction->commit();
					Flash::success('Se canceló correctamente el pedido');
				}
			}

		}
		catch(TransactionFailed $e){
			Flash::error("No se pudo cancelar el pedido");
		}
		$this->routeTo('action: index');
	}

}
