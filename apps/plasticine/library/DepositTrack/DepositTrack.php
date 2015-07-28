<?php

class DepositTrack extends UserComponent {

	public static function updatePendent(){
		if(Wepax::isEnabled()){
			try {
				$transaction = TransactionManager::getUserTransaction();
				$abonosPlasticine = self::getModel('Plasabo')->setTransaction($transaction);
				foreach($abonosPlasticine->findForUpdate("estado='P'") as $abonoPlasticine){
					try {
						$information = Wepax::invokeMethod('getTransactionInformation', array(
							'Token' => $abonoPlasticine->getToken()
						));
						if($information['status']=='GUARANTED'){
							self::_saveDepositIntoReserva($transaction, $abonoPlasticine, $information);
						} else {
							if($information['status']=='NOT-FOUND'||$information['status']=='CANCELED'){
								$abonoPlasticine->setEstado('C');
								if($abonoPlasticine->save()==false){
									foreach($abonoPlasticine->getMessages() as $message){
										$transaction->rollback('Plasabo: '.$message->getMessage());
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

	private static function _saveDepositIntoReserva($transaction, $abonoPlasticine, $information){

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

		$reserva = self::getModel('Reserva')->findFirst("numres='{$abonoPlasticine->getNumres()}'");
		if($reserva==false){
			$transaction->rollback('No existe la reserva '.$abonoPlasticine->getNumres());
		}

		$plasticine = self::getModel('Plasticine')->findFirst("numres='{$abonoPlasticine->getNumres()}'");
		if($plasticine==false){
			$transaction->rollback('No existe el webcheckin '.$abonoPlasticine->getNumres());
		}

		$folio = self::getModel('Folio')->findFirst("numres='{$abonoPlasticine->getNumres()}' AND walkin='A'");
		if($folio==false){
			$numfol = self::getModel('Folio')->maximum(array("numfol"));

			$transporte = self::getModel('Transporte')->findFirst("predeterminado='S'");
			if($transporte==false){
				$transporte = self::getModel('Transporte')->findFirst();
			}

			$folio = new Folio();
			$folio->setTransaction($transaction);
			$folio->setNumfol($numfol+1);
			$folio->setTipdoc($reserva->getTipdoc());
			$folio->setCedula($reserva->getCedula());
			$folio->setNit($reserva->getNit());
			$folio->setFecres((string)$dathot->getFecha());
			$folio->setFeclle((string)$dathot->getFecha());
			$folio->setFecsal((string)$dathot->getFecha());
			$folio->setNota('ESTE FOLIO HA SIDO CREADO AUTOMATICAMENTE PARA REGISTRAR EL VALOR DEL DEPÓSITO DE RESERVA ELECTRÓNICO PARA ESTE CLIENTE');
			$folio->setNumadu($reserva->getNumadu());
			$folio->setNumnin($reserva->getNumnin());
			$folio->setNuminf($reserva->getNuminf());
			$folio->setCodtra($transporte->getCodtra());
			$folio->setTrasal($transporte->getCodtra());
			$folio->setNumhab(0);
			$folio->setCorregir('N');
			$folio->setEstado('O');
			if($folio->save()==false){
				foreach($folio->getMessages() as $message){
					$transaction->rollback('Folio: '.$message->getMessage());
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
		$detrec->setValor($abonoPlasticine->getValor());
		if($detrec->save()==false){
			foreach($detrec->getMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
		}

		$reccaj = new Reccaj();
		$reccaj->setTransaction($transaction);
		$reccaj->setNumrec($dathot->getNumrec());
		$reccaj->setCedula($plasticine->getCedula());
		$reccaj->setDireccion($plasticine->getDireccion());
		$reccaj->setCiudad($dathot->getNomciu());
		$reccaj->setTelefono($plasticine->getTelefono());
		$reccaj->setFecha((string)$dathot->getFecha());
		$reccaj->setCodusu($parint->getUsuint());
		$reccaj->setCodcaj(1);
		$reccaj->setCodcar($parsis->getDepsin());
		$reccaj->setEstado('A');
		$reccaj->setNota('DEPOSITO DE RESERVA ELECTRONICO, CODIGO TRAZABILIDAD='.$information['trazabilityCode']);
		if($reccaj->save()==false){
			foreach($reccaj->getMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
		}

		$maximum = self::getModel('Valcar')->maximum("item", "conditions: numfol='{$folio->getNumfol()}' AND numcue='1'");

		$valcar = new Valcar();
		$valcar->setTransaction($transaction);
		$valcar->setNumfol($folio->getNumfol());
		$valcar->setNumcue(1);
		$valcar->setItem($maximum+1);
		$valcar->setCodusu($parint->getUsuint());
		$valcar->setCodcaj(1);
		$valcar->setFecha($dathot->getFecha().' '.date('H:i:s'));
		$valcar->setCantidad(1);
		$valcar->setCodcar($parsis->getDepsin());
		$valcar->setCladoc($parsis->getDocrec());
		$valcar->setNumdoc($dathot->getNumrec());
		$valcar->setValor($abonoPlasticine->getValor());
		$valcar->setIva(0);
		$valcar->setValser(0);
		$valcar->setValter(0);
		$valcar->setTotal($abonoPlasticine->getValor());
		$valcar->setEstado('N');
		$valcar->setMovcor('N');
		if($valcar->save()==false){
			foreach($valcar->getMessages() as $message){
				$transaction->rollback('Valcar: '.$message->getMessage());
			}
		}

		$maximum = self::getModel('Garres')->maximum("item", "conditions: numres='{$abonoPlasticine->getNumres()}'");

		$garres = new Garres();
		$garres->setTransaction($transaction);
		$garres->setNumres($abonoPlasticine->getNumres());
		$garres->setItem($maximum+1);
		$garres->setFecha(Date::now());
		$garres->setCodusu($parint->getUsuint());
		$garres->setCodcaj(1);
		$garres->setNumrec($dathot->getNumrec());
		$garres->setNumegr(0);
		$garres->setCodcar($parsis->getDepsin());
		$garres->setTotal($abonoPlasticine->getValor());
		$garres->setEstado('N');
		if($garres->save()==false){
			foreach($garres->getMessages() as $message){
				$transaction->rollback('Garres: '.$message->getMessage());
			}
		}

		if($reserva->getEstado()=='P'){
			$reserva->setTransaction($transaction);
			$reserva->setEstado('G');
			if($reserva->save()==false){
				foreach($reserva->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
		}

		$dathot->setNumrec($dathot->getNumrec()+1);
		if($dathot->save()==false){
			foreach($dathot->getMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
		}

		$abonoPlasticine->setEstado('G');
		if($abonoPlasticine->save()==false){
			foreach($abonoPlasticine->getMessages() as $message){
				$transaction->rollback('Plasabo: '.$message->getMessage());
			}
		}

	}

}