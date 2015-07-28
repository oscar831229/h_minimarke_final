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

class RevertController extends ApplicationController {

	public function indexAction(){

		try {

			$transaction = TransactionManager::getUserTransaction();
			$this->Datos->setTransaction($transaction);

			$datos = $this->Datos->findFirst();

			$fechaHotel = $this->DatosHotel->findFirst()->getFecha();
			$fechaPOS = $datos->getFecha();

			if(Date::isEarlier($fechaPOS, $fechaHotel)){
				Flash::addMessage('Se debe devolver la fecha del front antes de devolver la fecha del POS', Flash::ERROR);
				$transaction->rollback();
			}

			$datos = $this->Datos->findFirst();
			if($this->Factura->count("fecha = '{$datos->getFecha()}'")==0){
				$fecha = new Date($datos->getFecha());
				$note = "DEVOLVIÓ EL CIERRE DEL DÍA DE '{$datos->getFecha()}'";
				$fecha->diffDays(1);
				$datos->setFecha($fecha->getDate());
				if($datos->save()==false){
					foreach($datos->getMessages() as $message){
						Flash::addMessage($message->getMessage(), Flash::ERROR);
					}
					$transaction->rollback();
				} else {
					new POSAudit($note);
					Flash::addMessage('Se devolvió correctamente la fecha del sistema', Flash::SUCCESS);
				}
			} else {
				Flash::addMessage('No se puede devolver el día porque ya se han realizado pedidos', Flash::ERROR);
				$transaction->rollback();
			}

			$transaction->commit();

		}
		catch(TransactionFailed $e){

		}

		$this->redirect('appmenu');

	}

}