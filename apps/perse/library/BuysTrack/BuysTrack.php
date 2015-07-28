<?php

class BuysTrack extends UserComponent
{

	public static function updatePendent()
	{
		if (Wepax::isEnabled()) {
			try {
				$transaction = TransactionManager::getUserTransaction();
				$abonosPerse = self::getModel('Perabo')->setTransaction($transaction);
				foreach ($abonosPerse->findForUpdate("estado='P'") as $abonoPerse) {
					try {
						$information = Wepax::invokeMethod('getTransactionInformation', array(
							'Token' => $abonoPerse->getToken()
						));
						if($information['status']=='GUARANTED'){
							self::_savePayIntoFolio($transaction, $abonoPerse, $information);
						} else {
							if($information['status']=='NOT-FOUND'||$information['status']=='CANCELED'){
								$abonoPerse->setEstado('C');
								if($abonoPerse->save()==false){
									foreach($abonoPerse->getMessages() as $message){
										$transaction->rollback('Perabo: '.$message->getMessage());
									}
								}
							}
						}
					}
					catch(WepaxException $e){
						$transaction->rollback($e->getMessage());
					}
				}

				$transaction->commit();

			}
			catch(TransactionFailed $e){
				Flash::error($e->getMessage());
			}
		}
	}

	protected static function _savePayIntoFolio($transaction, $abonoPerse, $information)
	{

		$dathot = self::getModel('Dathot')->findFirst(array('for_update' => 'yes'));
		if($dathot==false){
			$transaction->rollback('No existen los datos del hotel');
		} else {
			$dathot->setTransaction($transaction);
		}

		$maximum = self::getModel('Reccaj')->maximum("numrec");
		if($maximum>=$dathot->getNumrec()){
			$dathot->setNumrec($maximum+1);
			if($dathot->save()==false){
				foreach($dathot->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
		}

		$parint = self::getModel('Parint')->findFirst();
		if($parint==false){
			$transaction->rollback('No existe los parámetros de reserva por internet en recepción');
		}

		$parsis = self::getModel('Parsis')->findFirst();
		if($parsis==false){
			$transaction->rollback('No existe la configuración del sistema');
		}

		$folio = self::getModel('Folio')->findFirst("numfol='{$abonoPerse->getNumfol()}'");
		if($folio==false){
			$transaction->rollback('No existe el folio '.$abonoPerse->getNumfol());
		}

		$cliente = self::getModel('Clientes')->findFirst("cedula='{$abonoPerse->getCedula()}'");
		if($cliente==false){
			$cliente = self::getModel('Clientes')->findFirst("cedula='{$folio->getCedula()}'");
			if($cliente==false){
				$transaction->rollback('No existe el cliente en '.$abonoPerse->getNumfol());
			}
		}

		foreach($abonoPerse->getPerdet() as $abonoDetalle){
			$carghab = self::getModel('Carghab')->findFirst("numfol='{$abonoPerse->getNumfol()}' AND numcue='{$abonoDetalle->getNumcue()}'");
			if($carghab==false){
				$carghab = self::getModel('Carghab')->findFirst("numfol='{$abonoPerse->getNumfol()}' AND estado='N'");
				if($carghab==false){
					$maximum = self::getModel('Carghab')->findFirst("numcue", "conditions: numfol='{$abonoPerse->getNumfol()}'");
					$carghab = new Carghab();
					$carghab->setNumfol();
					$carghab->setNumcue($maximum+1);
					$carghab->setEstado('N');
					$carghab->setExento('N');
					if($carghab->save()==false){
						foreach($carghab->getMessages() as $message){
							$transaction->rollback($message->getMessage());
						}
					}
				}
			}

			$detrec = new Detrec();
			$detrec->setTransaction($transaction);
			$detrec->setNumrec($dathot->getNumrec());
			$detrec->setNumero(1);
			$detrec->setForpag($parint->getForint());
			$detrec->setNumfor($information['trazabilityCode']);
			$detrec->setIvarep(0);
			$detrec->setValorm(0);
			$detrec->setValor($abonoDetalle->getValor());
			if($detrec->save()==false){
				foreach($detrec->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

			$reccaj = new Reccaj();
			$reccaj->setTransaction($transaction);
			$reccaj->setNumrec($dathot->getNumrec());
			$reccaj->setCedula($cliente->getCedula());
			$reccaj->setDireccion($cliente->getDireccion());
			$reccaj->setCiudad($dathot->getNomciu());
			$reccaj->setTelefono($cliente->getTelefono1());
			$reccaj->setFecha((string)$dathot->getFecha());
			$reccaj->setCodusu($parint->getUsuint());
			$reccaj->setCodcaj(1);
			$reccaj->setCodcar($parint->getConint());
			$reccaj->setEstado('A');
			$reccaj->setNota('ABONO POR RECEPCIONISTA ELECTRONICO, CODIGO TRAZABILIDAD='.$information['trazabilityCode']);
			if($reccaj->save()==false){
				foreach($reccaj->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

			$maximum = self::getModel('Valcar')->maximum("item", "conditions: numfol='{$abonoPerse->getNumfol()}' AND numcue='{$carghab->getNumcue()}'");

			$valcar = new Valcar();
			$valcar->setTransaction($transaction);
			$valcar->setNumfol($abonoPerse->getNumfol());
			$valcar->setNumcue($carghab->getNumcue());
			$valcar->setItem($maximum+1);
			$valcar->setCodusu($parint->getUsuint());
			$valcar->setCodcaj(1);
			$valcar->setFecha($dathot->getFecha().' '.date('H:i:s'));
			$valcar->setCantidad(1);
			$valcar->setCodcar($parint->getConint());
			$valcar->setCladoc($parsis->getDocrec());
			$valcar->setNumdoc($dathot->getNumrec());
			$valcar->setValor($abonoDetalle->getValor());
			$valcar->setIva(0);
			$valcar->setValser(0);
			$valcar->setValter(0);
			$valcar->setTotal($abonoDetalle->getValor());
			$valcar->setEstado('N');
			$valcar->setMovcor('N');
			if($valcar->save()==false){
				foreach($valcar->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

			$valnot = new Valnot();
			$valnot->setTransaction($transaction);
			$valnot->setNumfol($abonoPerse->getNumfol());
			$valnot->setNumcue($carghab->getNumcue());
			$valnot->setItem($maximum+1);
			$valnot->setNota('ABONO POR RECEPCIONISTA ELECTRONICO, CODIGO TRAZABILIDAD='.$information['trazabilityCode']);
			if($valnot->save()==false){
				foreach($valnot->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

			$dathot->setNumrec($dathot->getNumrec()+1);
			if($dathot->save()==false){
				foreach($dathot->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

			$abonoDetalle->setNumrec($dathot->getNumrec());
			if($abonoDetalle->save()==false){
				foreach($abonoDetalle->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

		}

		$abonoPerse->setEstado('G');
		if($abonoPerse->save()==false){
			foreach($abonoPerse->getMessages() as $message){
				$transaction->rollback('Perabo: '.$message->getMessage());
			}
		}

	}

}