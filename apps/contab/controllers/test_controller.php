<?php


Core::importFromLibrary('Hfos', 'Loader/Loader.php');

class TestController extends ProcessController {

	/*public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}*/

	public function indexAction(){
		$this->routeToAction('perspective');
	}

	public function fechaAction(){
		$date = Date::fromFormat('01/31/2010', 'mm/dd/yyyy');
		echo $date->getUsingFormat('mm/dd/yyyy');
	}

	public function xAction(){


		#$this->setResponse('view');

		//Hay que guardarlo en alguna parte
		$ticketToken = md5(uniqid());

		try {

			$ticketData = array(
				'Token' => $ticketToken,
				'Tipo' => 'CA',
				'ClaseDocumento' => 'CC',
				'NumeroDocumento' => '11224080',
				'Nombre' => 'ANDRES FELIPE GUTIERREZ',
				'Email' => 'gutierrezandresfelipe@gmail.com',
				'Telefono' => '3112098172',
				'TotalValor' => '100000.00',
				'TotalIva' => '16000.00',
				'Descripcion' => 'PAGO DE FACTURA EN EL CLUB POR EL MES DE ABRIL/2011',
				'DiasExpiracion' => 15
			);

			//Crear un ticket de Pago
			$ticket = Wepax::invokeMethod('createTransactionTicket', $ticketData);

			if($ticket['status']=='OK'){
				//Guardar la URL en algun lado
				echo $ticket['url'];
			}

			//
			//$ticket = Wepax::invokeMethod('createTransactionTicket', $ticketData);

		}
		catch(WepaxException $e){
			Flash::error($e->getMessage());
		}

		/*

		U = Sin pagar

		P = Esperando el pago
		G = OK, Aprobada
		C = Abandonado, Expiró

		*/

		//Revisar todo
		try {

			$toSync = array();

			$tickets = Wepax::invokeMethod('getStatuses', 'CA');
			foreach($tickets as $ticket){
				if($ticket['Tipo']=='RW'){
					#echo $ticket['Token'];

					$wepsync = $this->getService('front.wepsync')
					if($wepsync->sync($ticket)){
						$toSync[] = $ticket['Token'];
					}

				} else {
					if($ticket['Tipo']=='CA'){
						#echo $ticket['Token'];

						$wepsync = $this->getService('socios.wepsync')
						if($wepsync->sync($ticket)){
							$toSync[] = $ticket['Token'];
						}
					}
				}
			}

			Wepax::invokeMethod('setSynced', $toSync);
		}
		catch(WepaxException $e){

		}

		$ticket = Wepax::invokeMethod('getStatus', $tokenTicket);*/

	}

}