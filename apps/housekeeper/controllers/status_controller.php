<?php

class StatusController extends ApplicationController {

	public function initialize(){
		$this->setTemplateAfter('main');
	}

	public function indexAction($order='pendent'){

		$order = $this->filter($order, 'alpha');

		$userInfo = SessionNamespace::get('userInfo');
		if($order=='ready'){
			$asicams = $this->Asicam->find(array("codcam='{$userInfo->getCodcam()}' AND fecha='{$userInfo->getFecha()}' AND estado='A'", "order" => "CONCAT(SPACE(6-LENGTH(numhab)), numhab)"));
		} else {
			$changed = false;
			$asicams = $this->Asicam->find(array("codcam='{$userInfo->getCodcam()}' AND fecha='{$userInfo->getFecha()}' AND estado='N'", "order" => "CONCAT(SPACE(6-LENGTH(numhab)), numhab)"));
			foreach($asicams as $asicam){
				$habitacion = $asicam->getHabitacion();
				if($habitacion){
					if($habitacion->getCodest()==2||$habitacion->getCodest()==4){
						$asicam->setHorfin(Date::getCurrentTime());
						$asicam->setEstado('A');
						$asicam->save();
						$changed = true;
					}
				}
			}
			if($changed){
				$asicams = $this->Asicam->find(array("codcam='{$userInfo->getCodcam()}' AND fecha='{$userInfo->getFecha()}' AND estado='N'", "order" => "CONCAT(SPACE(6-LENGTH(numhab)), numhab)"));
			}
		}

		$this->setParamToView('order', $order);
		$this->setParamToView('asicams', $asicams);
	}

	private function _changeStatus($number){

		try {
			$userInfo = SessionNamespace::get('userInfo');

			$habitacion = $this->Habitacion->findFirst("numhab='$number' AND tipo='V' AND estado='A'");
			if($habitacion==false){
				return;
			}

			$asicam = $this->Asicam->findFirst("numhab='$number' AND codcam='{$userInfo->getCodcam()}' AND fecha='{$userInfo->getFecha()}' AND estado='N'");
			if($asicam==false){
				return;
			}

			$transaction = TransactionManager::getUserTransaction();

			$habitacion->setTransaction($transaction);

			if($habitacion->getCodest()==3){
				$habitacion->setCodest(4);
			} else {
				if($habitacion->getCodest()==1){
					$habitacion->setCodest(2);
				}
			}

			if($habitacion->save()==false){
				foreach($habitacion->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

			$nota = $this->getPost('nota', 'extraspaces', 'striptags');

			$asicam->setTransaction($transaction);
			$asicam->setHorfin(Date::getCurrentTime());
			$asicam->setObservacion($nota);
			$asicam->setEstado('A');
			if($asicam->save()==false){
				foreach($asicam->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}

			$transaction->commit();

		}
		catch(TransactionFailed $e){
			$this->setParamToView('message', $e->getMessage());
		}

	}

	public function changeAction($number=''){

		$number = $this->filter($number, 'alpha');
		if($number){
			$this->_changeStatus($number);
		}
		return $this->routeToAction('index');
	}

	public function preChangeAction($number=''){
		$number = $this->filter($number, 'alpha');
		if(!$number){
			return $this->routeToAction('index');
		}

		$userInfo = SessionNamespace::get('userInfo');

		$habitacion = $this->Habitacion->findFirst("numhab='$number' AND tipo='V' AND estado='A'");
		if($habitacion==false){
			return $this->routeToAction('index');
		}

		$asicam = $this->Asicam->findFirst("numhab='$number' AND codcam='{$userInfo->getCodcam()}' AND fecha='{$userInfo->getFecha()}' AND estado='N'");
		if($asicam==false){
			return $this->routeToAction('index');
		}

		$this->setParamToView('number', $number);
	}

}
