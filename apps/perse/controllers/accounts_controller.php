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

class AccountsController extends ApplicationController
{

	public function indexAction()
	{
		$guestInfo = SessionNamespace::get('guestInfo');
		if ($guestInfo) {
			$tarifas = array();
			foreach($this->Plafol->find("numfol='{$guestInfo->getFolio()}'") as $plafol){
				$plan = $plafol->getPlanes();
				if($plan!=false){
					if($plan->getMuefac()=='S'){
						$tarifas[] = $plan->getDescripcion();
					}
				}
			}
			$accounts = new Accounts();
			$folio = $this->Folio->findFirst("numfol='{$guestInfo->getFolio()}'");
	   		$this->setParamToView('tarifas', join(', ', $tarifas));
			$this->setParamToView('cuentas', $accounts->getResume());
			$this->setParamToView('total', $accounts->getTotal());
			$this->setParamToView('folio', $folio);
			$this->setParamToView('cliente', $folio->getClientes());
			$this->setParamToView('clahab', $folio->getHabitacion()->getClahab());
		} else {
			return $this->routeTo(array('controller' => 'index'));
		}
	}

	public function detailsAction($numeroCuenta=0)
	{

		$numeroCuenta = $this->filter($numeroCuenta, 'int');
		if($numeroCuenta>0){
			$traslate = $this->_loadTraslation();
			$guestInfo = SessionNamespace::get('guestInfo');
			if($guestInfo){
				$conditions = "numfol='{$guestInfo->getFolio()}' AND numcue='$numeroCuenta' AND estado IN ('N', 'A')";
				$carghab = $this->Carghab->findFirst($conditions);
				if($carghab==false){
					Flash::error($traslate['noExCuenta']);
					return $this->routeToAction('index');
				}
			} else {
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

			$this->loadModel('Currencies');

		} else {
			$this->routeToAction('index');
		}
	}

	public function doPayAction()
	{

		if(!Wepax::isEnabled()){
			return $this->routeToAction('noPay');
		}

		$guestInfo = SessionNamespace::get('guestInfo');
		if($guestInfo){

			$traslate = $this->_loadTraslation();

			$folio = $this->Folio->findFirst("numfol='{$guestInfo->getFolio()}'");
			if($folio==false){
				Flash::error($traslate['FolioCheckout']);
				return $this->routeToAction('index');
			}

			if($folio->getEstado()<>'I'){
				Flash::error($traslate['FolioCheckout']);
				return $this->routeToAction('index');
			}

			$abonosPendientes = $this->Perabo->count("numfol='{$guestInfo->getFolio()}' AND estado='P'");
			if($abonosPendientes>0){
				BuysTrack::updatePendent();
				$abonosPendientes = $this->Perabo->count("numfol='{$guestInfo->getFolio()}' AND estado='P'");
				if($abonosPendientes>0){
					return $this->routeToAction('pendent');
				}
			}

			$perse = $this->Perse->findFirst("numfol='{$guestInfo->getFolio()}'");
			if($perse==false){
				Flash::error($traslate['NoPerse']);
				return $this->routeToAction('index');
			}

			if($perse->getPueabo()!='S'){
				Flash::error($traslate['NoAbonos']);
				return $this->routeToAction('index');
			}

			$cliente = $this->Clientes->findFirst("cedula='{$folio->getCedula()}'");
			if($cliente==false){
				Flash::error($traslate['FolioCheckout']);
				return $this->routeToAction('index');
			}

			$controllerRequest = $this->getRequestInstance();

			$huespedes = array(
				$cliente->getCedula() => $cliente->getNombre()
			);

			$apofols = $this->Apofol->find("numfol='{$guestInfo->getFolio()}'");
			foreach($apofols as $apofol){
				if($apofol->getCedula()){
					$huespedes[$apofol->getCedula()] = $apofol->getNombre();
				}
			}
			$this->setParamToView('huespedes', $huespedes);

			if($controllerRequest->isGet()){
				Tag::displayTo('email', $cliente->getEmail());
				Tag::displayTo('telefono', $cliente->getTelefono1());
			}

			if(Date::difference($folio->getFecsal(), new Date())<=0){
				return $this->routeToAction('noEarlyCheckout');
			}

			$accounts = new Accounts();
			$cuentas = $accounts->getResume();
			$this->setParamToView('resume', $accounts->getResume());
			$this->setParamToView('cuentas', $cuentas);
			$this->setParamToView('total', $accounts->getTotal());

			if($controllerRequest->isGet()){
				$totalPendiente = 0;
				foreach($cuentas as $numero => $cuenta){
					if($cuenta['total']-$cuenta['abonos']>0){
						Tag::displayTo('abono'.$numero, $cuenta['total']-$cuenta['abonos']);
						$totalPendiente+=$cuenta['total']-$cuenta['abonos'];
					} else {
						Tag::displayTo('abono'.$numero, 0);
					}
				}
				$this->setParamToView('abonoTotal', $totalPendiente);
			}

		} else {
			return $this->routeToAction('noPay');
		}


	}

	public function noPayAction()
	{

	}

	public function noEarlyCheckoutAction()
	{

	}

	public function goPayAction()
	{

		$abonoTotal = 0;
		$cuentas = $this->getPostParam('cuenta', 'int');
		if(is_array($cuentas)){
			foreach($cuentas as $numero => $cuenta){
				$abonoTotal+=$this->getPostParam('abono'.$cuenta, 'double');
			}
			$this->setParamToView('abonoTotal', $abonoTotal);
		} else {
			$this->setParamToView('abonoTotal', 0);
		}

		$traslate = $this->_loadTraslation();

		if($abonoTotal<150000){
			Flash::error($traslate['Menos150']);
			return $this->routeToAction('doPay');
		}

		$email = $this->getPostParam('email', 'email');
		if(!$email){
			Flash::error($traslate['NoEmail']);
			return $this->routeToAction('doPay');
		}

		try {

			$transaction = TransactionManager::getUserTransaction();
			if(Wepax::isEnabled()){

				$guestInfo = SessionNamespace::get('guestInfo');
				if($guestInfo){
					$folio = $this->Folio->findFirst("numfol='{$guestInfo->getFolio()}'");
					if($folio==false){
						Flash::error($traslate['FolioCheckout']);
						return $this->routeToAction('doPay');
					}
					if($folio->getEstado()<>'I'){
						Flash::error($traslate['FolioCheckout']);
						return $this->routeToAction('doPay');
					}
				} else {
					Flash::error($traslate['FolioCheckout']);
					return $this->routeToAction('doPay');
				}

				$cedula = $this->getPostParam('cedula', 'alpha');
				$cliente = $this->Clientes->findFirst("cedula='$cedula'");
				if($cliente==false){
					Flash::error($traslate['FolioCheckout']);
					return $this->routeToAction('doPay');
				}

				try {

					$publicHost = CoreConfig::getAppSetting('public_host', 'perse');

					$ticketToken = Wepax::generateLocalTicket('CA');

					$ticketData = array(
						'Token' => $ticketToken,
				        'Type' => 'AB',
				        'DocumentKind' => 'CC',
				        'DocumentNumber' => $cliente->getCedula(),
				        'Name' => $cliente->getNombre(),
				        'Email' => $email,
				        'Telephone' => $cliente->getTelefono1(),
				        'TotalValue' => $abonoTotal,
				        'TotalTaxes' => '16000.00',
				        'Description' => 'ABONO A CUENTA DE HOSPEDAJE EN EL HOTEL '.$guestInfo->getHotel().' Folio='.$folio->getNumfol().' Hab='.$folio->getNumhab(),
				        'Silent' => 'S',
				        'UrlRedirect' => $publicHost.'/transaction/close',
				        'ExpireTime' => 900
					);

					$ticket = Wepax::invokeMethod('createTransactionTicket', $ticketData);
					if($ticket['status']=='OK'){

						$controllerRequest = $this->getRequestInstance();
						$ipAddress = $controllerRequest->getClientAddress();

						$perabo = new Perabo();
						$perabo->setTransaction($transaction);
						$perabo->setCedula($cliente->getCedula());
						$perabo->setNumfol($folio->getNumfol());
						$perabo->setToken($ticketToken);
						$perabo->setIpaddress($ipAddress);
						$perabo->setEstado('P');
						if($perabo->save()==false){
							foreach($perabo->getMessages() as $message){
								$transaction->rollback('Abono: '.$message->getMessage());
							}
						}

						foreach($cuentas as $numero => $cuenta){
							$valorCuenta = $this->getPostParam('abono'.$cuenta, 'double');
							if($valorCuenta>0){
								$perdet = new Perdet();
								$perdet->setTransaction($transaction);
								$perdet->setPeraboId($perabo->getId());
								$perdet->setNumcue($cuenta);
								$perdet->setValor($valorCuenta);
								if($perdet->save()==false){
									foreach($perdet->getMessages() as $message){
										$transaction->rollback('Abono-Detalle: '.$message->getMessage());
									}
								}
							}
						}

						$transaction->commit();

						$this->getResponseInstance()->setHeader('Location: '.$ticket['url']);

					} else {
						$transaction->rollback($ticket['message']);
					}

				}
				catch(WepaxException $e){
					$transaction->rollback($e->getMessage());
				}

			} else {
				$traslate = $this->_loadTraslation();
				Flash::error($traslate['NoPay']);
				return $this->routeToAction('doPay');
			}
		}
		catch(TransactionFailed $e){
			Flash::error($e->getMessage());
			return $this->routeToAction('doPay');
		}

	}

	public function payHistoryAction()
	{

		if(!Wepax::isEnabled()){
			return $this->routeToAction('noPay');
		}

		$guestInfo = SessionNamespace::get('guestInfo');
		if($guestInfo){
			$abonos = array();
			$perabos = $this->Perabo->find("numfol='{$guestInfo->getFolio()}'");
			foreach($perabos as $perabo){
				foreach($perabo->getPerdet() as $perdet){
					switch($perabo->getEstado()){
						case 'G':
							$estado = 'PAGO RECIBIDO Y APLICADO';
							break;
						case 'P':
							$estado = 'VALIDANDO DATOS DE PAGO';
							break;
						case 'C':
							$estado = 'ABANDONADO/CANCELADO';
							break;
						default:
							$estado = 'DESCONOCIDOG';
							break;
					}
					$abonos[] = array(
						'cuenta' => $perdet->getNumcue(),
						'valor' => $perdet->getValor(),
						'fecha' => $perabo->getCreatedAt(),
						'ultimaFecha' => $perabo->getModifiedIn(),
						'recibo' => $perdet->getNumrec(),
						'estado' => $estado
					);
				}
			}
			$this->setParamToView('abonos', $abonos);
		}

	}

	public function pendentAction(){

	}

}