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
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class WebcheckinController extends ApplicationController {

	public function initialize(){

		parent::initialize();

		$frontDir = CoreConfig::getAppSetting('front_dir', 'hfos');
		if($frontDir==null){
			$frontDir = null;
		}

		require_once '../'.$frontDir.'/hfos/config/config.php';
		require_once '../'.$frontDir.'/hfos/config/config.custom.php';

		Tag::setDocumentTitle(JASMIN_SYSTEM_CAPTION.' / Pre Check-In');

		if(!Session::get('shortName')){
			$shortName = preg_replace('/[^0-9a-z]/', '', trim(i18n::strtolower(JASMIN_SYSTEM_CAPTION)));
			if(!file_exists('public/img/plasticine/'.$shortName.'-header.png')){
				require_once 'Library/Gradient/Gradient.php';
				new Gradient(10, 80, 'vertical', JASMIN_NORMAL_COLOR, JASMIN_DARK_COLOR, 0, 'public/img/plasticine/'.$shortName.'-header.png');
			}
			if(!file_exists('public/img/plasticine/'.$shortName.'-footer.png')){
				require_once 'Library/Gradient/Gradient.php';
				$imgSrc = imagecreatetruecolor(100, 100);
				list($r, $g, $b) = Gradient::hex2rgb(JASMIN_TEXT_COLOR);
				$darkColor = ImageColorAllocate($imgSrc, $r, $g, $b);
				imagefilledrectangle($imgSrc, 0, 0, 100, 100, $darkColor);
				Gradient::setOpacity($imgSrc, 90);
				if(class_exists('Imagick')){
					ImagePNG($imgSrc, 'public/img/plasticine/test.png');
					$image = new Imagick('public/img/plasticine/test.png');
					$image->setImageOpacity(0.8);
					$image->writeImage('public/img/plasticine/'.$shortName.'-footer.png');
				} else {
					ImagePNG($imgSrc, 'public/img/plasticine/'.$shortName.'-footer.png');
				}
			}
			if(!file_exists('public/img/plasticine/'.$shortName.'-logo.jpg')){
				copy('../'.$frontDir.'/img/logo.jpg', 'public/img/plasticine/'.$shortName.'-logo.jpg');
			}

			Session::set('shortName', $shortName);
		}
	}

	public function indexAction($numeroReserva=0){
		$numeroReserva = $this->filter($numeroReserva, 'alpha');
		$this->setId($numeroReserva);

		$numeroReserva = Session::get('numeroReserva');
		if($numeroReserva>0){
			return $this->routeToAction('enterInformation');
		}
	}

	public function validateCodeAction($numeroReserva=0){
		$traslate = $this->_loadTraslation();
		$numeroReserva = $this->filter($numeroReserva, 'alpha');
		$clave = $this->getPostParam('clave', 'alpha');
		$numero = $this->getPostParam('numeroReserva', 'int');
		if($numero==0){
			Flash::error($traslate['NoNumeroReserva']);
			return $this->routeToAction('index');
		};
		if(base_convert($numeroReserva, 32, 10)!=$numero){
			Flash::error($traslate['NoCode']);
			return $this->routeToAction('index');
		} else {
			$plasticine = $this->Plasticine->findFirst("clave='$clave' AND numres='$numero'");
			if($plasticine==false){
				Flash::error($traslate['NoClave']);
				return $this->routeToAction('index');
			} else {
				$reserva = $this->Reserva->findFirst("numres='$numero' AND estado IN ('P', 'G')");
				if($reserva==false){
					Flash::error($traslate['NoClave']);
					return $this->routeToAction('index');
				}
			}
		}
		Session::set('numeroReserva', $numero);
		$this->redirect('webcheckin/enterInformation/'.$numeroReserva);
	}

	private function _completePlasticine($reserva){

		if($reserva->getCedula()!='0'){

			Tag::displayTo('numeroDocumento', $reserva->getCedula());
			$cliente = $this->Clientes->findFirst("cedula='{$reserva->getCedula()}'");
			if($cliente!=false){
				$tipdoc = $this->Tipdoc->findFirst("tipdoc='{$cliente->getTipdoc()}'");
				if($tipdoc!=false){
					switch($tipdoc->getTipcoa()){
						case 'C':
							Tag::displayTo('tipoDocumento', 'C');
							break;
						case 'E':
							Tag::displayTo('tipoDocumento', 'E');
							break;
						case 'P':
							Tag::displayTo('tipoDocumento', 'P');
							break;
						default:
							Tag::displayTo('tipoDocumento', 'O');
							break;
					}
				}
				Tag::displayTo('lugarExpedicion', $cliente->getLugexp());

				$parts = Names::getParts($cliente->getNombre());
				if(isset($parts['primerApellido'])){
					Tag::displayTo('primerApellido', $parts['primerApellido']);
				}
				if(isset($parts['segundoApellido'])){
					Tag::displayTo('segundoApellido', $parts['segundoApellido']);
				}
				if(isset($parts['primerNombre'])){
					Tag::displayTo('nombre', $parts['primerNombre'].' '.$parts['segundoNombre']);
				}

				Tag::displayTo('nacionalidad', $cliente->getLocnac());
				Tag::displayTo('fechaNacimiento', (string)$cliente->getFecnac());

				Tag::displayTo('direccion', $cliente->getDireccion());
				Tag::displayTo('telefono', $cliente->getTelefono1());
				if($cliente->getEMail()!='@'){
					Tag::displayTo('email', $cliente->getEMail());
				}

				$locdir = $cliente->getLocdir();
				if($locdir>0){
					$location = $this->Location->findFirst($locdir);
					Tag::displayTo('locDireccionCodigo', $locdir);
					Tag::displayTo('locDireccion', $location->getName().' / '.$location->getZone()->getName().' / '.$location->getTerritory()->getName());
				}

				$this->setParamToView('nombreReserva', $cliente->getNombre());

			}
		} else {
			Tag::displayTo('nacionalidad', 135);
		}

		$locpro = $reserva->getLocpro();
		if($locpro>0){
			$location = $this->Location->findFirst($locpro);
			Tag::displayTo('locProcedenciaCodigo', $locpro);
			Tag::displayTo('locProcedencia', $location->getName().' / '.$location->getZone()->getName().' / '.$location->getTerritory()->getName());
		}

		if($reserva->getNit()!='0'){
			$this->setParamToView('conEmpresa', true);

			$empresa = $this->Empresas->findFirst("nit='{$reserva->getNit()}'");
			if($empresa==false){
				$this->setParamToView('conEmpresa', false);
			} else {
				Tag::displayTo('nitEmpresa', $empresa->getNit());
				Tag::displayTo('nombreEmpresa', $empresa->getNombre());
				Tag::displayTo('direccionEmpresa', $empresa->getDireccion());
				Tag::displayTo('telefonoEmpresa', $empresa->getTelefono());
				Tag::displayTo('emailEmpresa', $empresa->getEmail());

				$locdir = $empresa->getLocdir();
				if($locdir>0){
					$location = $this->Location->findFirst($locdir);
					Tag::displayTo('locDireccionEmpresaCodigo', $locdir);
					Tag::displayTo('locDireccionEmpresa', $location->getName().' / '.$location->getZone()->getName().' / '.$location->getTerritory()->getName());
				}
			}

		} else {
			$this->setParamToView('conEmpresa', false);
		}

		if(!$reserva->getHora()){
			Tag::displayTo('hora', '15:00');
		} else {
			Tag::displayTo('hora', $reserva->getHora());
		}
		Tag::displayTo('transporteLlegada', $reserva->getCodtra());

		$motivoViaje = $this->Motvia->findFirst("predeterminado='S'");
		if($motivoViaje!=false){
			Tag::displayTo('motivoViaje', $motivoViaje->getCodmot());
		}
	}

	public function enterInformationAction(){

		$traslate = $this->_loadTraslation();
		$numeroReserva = Session::get('numeroReserva');
		if($numeroReserva>0){

			$controllerRequest = $this->getRequestInstance();
			$reserva = $this->Reserva->findFirst("numres='$numeroReserva' AND estado IN ('P', 'G')");
			if($reserva==false){
				Flash::error($traslate['NoCode']);
				return $this->routeToAction('index');
			} else {

				$this->setParamToView('reserva', $reserva);

				$plasticine = $this->Plasticine->findFirst("numres='$numeroReserva'");
				if($plasticine==false){
					Flash::error($traslate['NoCode']);
					return $this->routeToAction('index');
				}

				$this->setParamToView('nombreReserva', $reserva->getReferencia());
				if(!$controllerRequest->isPost()){

					if(!$plasticine->getModifiedIn()){
						$this->_completePlasticine($reserva);
					} else {

						Tag::displayTo('tipoDocumento', $plasticine->getTipdoc());
						Tag::displayTo('numeroDocumento', $plasticine->getCedula());
						Tag::displayTo('lugarExpedicion', $plasticine->getLugexp());
						Tag::displayTo('primerApellido', $plasticine->getPriape());
						Tag::displayTo('segundoApellido', $plasticine->getSegape());
						Tag::displayTo('nombre', $plasticine->getNombre());
						Tag::displayTo('nacionalidad', $plasticine->getLocnac());
						Tag::displayTo('fechaNacimiento', (string)$plasticine->getFecnac());

						Tag::displayTo('direccion', $plasticine->getDireccion());

						$locdir = $plasticine->getLocdir();
						if($locdir>0){
							$location = $this->Location->findFirst($locdir);
							Tag::displayTo('locDireccionCodigo', $locdir);
							Tag::displayTo('locDireccion', $location->getName().' / '.$location->getZone()->getName().' / '.$location->getTerritory()->getName());
						}

						Tag::displayTo('telefono', $plasticine->getTelefono());
						Tag::displayTo('email', $plasticine->getEmail());

						if($plasticine->getNit()){
							$this->setParamToView('conEmpresa', true);
						} else {
							$this->setParamToView('conEmpresa', false);
						}

						Tag::displayTo('nitEmpresa', $plasticine->getNit());
						Tag::displayTo('nombreEmpresa', $plasticine->getNomemp());
						Tag::displayTo('direccionEmpresa', $plasticine->getDiremp());
						Tag::displayTo('telefonoEmpresa', $plasticine->getTelemp());
						Tag::displayTo('emailEmpresa', $plasticine->getEmaemp());

						$locdir = $plasticine->getLocemp();
						if($locdir>0){
							$location = $this->Location->findFirst($locdir);
							Tag::displayTo('locDireccionEmpresaCodigo', $locdir);
							Tag::displayTo('locDireccionEmpresa', $location->getName().' / '.$location->getZone()->getName().' / '.$location->getTerritory()->getName());
						}

						$locpro = $plasticine->getLocpro();
						if($locpro>0){
							$location = $this->Location->findFirst($locpro);
							Tag::displayTo('locProcedenciaCodigo', $locpro);
							Tag::displayTo('locProcedencia', $location->getName().' / '.$location->getZone()->getName().' / '.$location->getTerritory()->getName());
						}
						Tag::displayTo('transporteLlegada', $plasticine->getCodtra());
						Tag::displayTo('motivoViaje', $plasticine->getCodmot());

						if(!$plasticine->getHora()){
							Tag::displayTo('hora', '15:00');
						} else {
							Tag::displayTo('hora', $plasticine->getHora());
						}
						Tag::displayTo('nota', $plasticine->getNota());

					}

				} else {

					if(Router::wasRouted()){
						if(!$plasticine->getModifiedIn()){
							$this->_completePlasticine($reserva);
						}
					}

					$conEmpresa = $this->getPostParam('conEmpresa', 'onechar');
					if($conEmpresa=='S'){
						$this->setParamToView('conEmpresa', true);
					} else {
						$this->setParamToView('conEmpresa', false);
					}
				}

			}

			$transportes = array();
			foreach($this->Transporte->find(array('order' => 'nombre')) as $transporte){
				$transportes[$transporte->getCodtra()] = $traslate[$transporte->getNombre()];
			}
			asort($transportes);
			$this->setParamToView('transportes', $transportes);

			$motivosViaje = array();
			foreach($this->Motvia->find(array('order' => 'detalle')) as $motivoViaje){
				$motivosViaje[$motivoViaje->getCodmot()] = $traslate[$motivoViaje->getDetalle()];
			}
			asort($motivosViaje);
			$this->setParamToView('motivosViaje', $motivosViaje);

			$this->setParamToView('nacionalidades', $this->Territory->find(array('order' => 'name')));

			$tarifas = $this->_getTarifas($reserva);
			$this->setParamToView('tarifas', $tarifas);

		} else {
			Flash::error($traslate['NoNumeroReserva']);
			return $this->routeToAction('index');
		}
	}

	public function saveInformationAction($numeroReserva=0){

		$controllerRequest = $this->getRequestInstance();
		if($controllerRequest->isPost()){

			$traslate = $this->_loadTraslation();
			$numeroReserva = Session::get('numeroReserva');
			if($numeroReserva>0){

				$numeroDocumento = $this->getPostParam('numeroDocumento', 'alpha', 'upper');
				if(!$numeroDocumento){
					Flash::error($traslate['NumeroDocumentoRequerido']);
					Tag::displayTo('errorField', 'numeroDocumento');
					return $this->routeToAction('enterInformation');
				}

				if(strlen($numeroDocumento)<5){
					Flash::error($traslate['NumeroDocumentoInvalido']);
					Tag::displayTo('errorField', 'numeroDocumento');
					return $this->routeToAction('enterInformation');
				}

				$lugarExpedicion = $this->getPostParam('lugarExpedicion', 'striptags', 'extraspaces', 'upper');
				if(!$lugarExpedicion){
					Flash::error($traslate['LugarExpedicionRequerido']);
					Tag::displayTo('errorField', 'lugarExpedicion');
					return $this->routeToAction('enterInformation');
				}

				$primerApellido = $this->getPostParam('primerApellido', 'striptags', 'extraspaces', 'upper');
				if(!$primerApellido){
					Flash::error($traslate['PrimerApellidoRequerido']);
					Tag::displayTo('errorField', 'primerApellido');
					return $this->routeToAction('enterInformation');
				}

				$nombre = $this->getPostParam('nombre', 'striptags', 'extraspaces', 'upper');
				if(!$nombre){
					Flash::error($traslate['NombreRequerido']);
					Tag::displayTo('errorField', 'nombre');
					return $this->routeToAction('enterInformation');
				}

				$direccion = $this->getPostParam('direccion', 'striptags', 'extraspaces', 'upper');
				if(!$direccion){
					Flash::error($traslate['DireccionRequerida']);
					Tag::displayTo('errorField', 'direccion');
					return $this->routeToAction('enterInformation');
				}

				$locdirCodigo = $this->getPostParam('locDireccionCodigo', 'int');
				if(!$locdirCodigo){
					Flash::error($traslate['LocdirRequerida']);
					Tag::displayTo('errorField', 'locDireccionCodigo');
					return $this->routeToAction('enterInformation');
				}

				$telefono = $this->getPostParam('telefono', 'striptags', 'extraspaces');
				if(!$telefono){
					Flash::error($traslate['TelefonoRequerido']);
					Tag::displayTo('errorField', 'telefono');
					return $this->routeToAction('enterInformation');
				}

				$email = $this->getPostParam('email', 'email', 'extraspaces', 'lower');
				if(!$email){
					Flash::error($traslate['EmailRequerido']);
					Tag::displayTo('errorField', 'email');
					return $this->routeToAction('enterInformation');
				}

				$locproCodigo = $this->getPostParam('locProcedenciaCodigo', 'int');
				if(!$locproCodigo){
					Flash::error($traslate['LocproRequerido']);
					return $this->routeToAction('enterInformation');
				}

				$tipoDocumento = $this->getPostParam('tipoDocumento', 'onechar');
				$segundoApellido = $this->getPostParam('segundoApellido', 'striptags', 'extraspaces', 'upper');
				$nacionalidad = $this->getPostParam('nacionalidad', 'int');
				$fechaNacimiento = $this->getPostParam('fechaNacimiento', 'date');

				$transporteLLegada = $this->getPostParam('transporteLLegada', 'int');
				$motivoViaje = $this->getPostParam('motivoViaje', 'int');

				$hora = $this->getPostParam('hora', 'time');
				$nota = $this->getPostParam('nota', 'striptags', 'extraspaces');

				$nitEmpresa = $this->getPostParam('nitEmpresa', 'extraspaces');
				$nitEmpresa = preg_replace('/\-[0-9]$/', '', $nitEmpresa);
				$nitEmpresa = $this->filter($nitEmpresa, 'alpha');
				$nombreEmpresa = $this->getPostParam('nombreEmpresa', 'striptags', 'extraspaces', 'upper');
				$direccionEmpresa = $this->getPostParam('direccionEmpresa', 'striptags', 'extraspaces', 'upper');
				$telefonoEmpresa = $this->getPostParam('telefonoEmpresa', 'striptags', 'extraspaces');
				$locempCodigo = $this->getPostParam('locDireccionEmpresaCodigo', 'int');
				$emailEmpresa = $this->getPostParam('emailEmpresa', 'email', 'extraspaces', 'lower');

				$plasticine = $this->Plasticine->findFirst("numres='$numeroReserva'");
				if($plasticine==false){
					$plasticine = new Plasticine();
				}
				$plasticine->setTipdoc($tipoDocumento);
				$plasticine->setCedula($numeroDocumento);
				$plasticine->setLugexp($lugarExpedicion);
				$plasticine->setPriape($primerApellido);
				$plasticine->setSegape($segundoApellido);
				$plasticine->setNombre($nombre);
				$plasticine->setLocnac($nacionalidad);
				$plasticine->setFecnac($fechaNacimiento);
				$plasticine->setDireccion($direccion);
				$plasticine->setLocdir($locdirCodigo);
				$plasticine->setTelefono($telefono);
				$plasticine->setEmail($email);
				$plasticine->setNit($nitEmpresa);
				$plasticine->setNomemp($nombreEmpresa);
				$plasticine->setDiremp($direccionEmpresa);
				$plasticine->setTelemp($telefonoEmpresa);
				$plasticine->setLocemp($locempCodigo);
				$plasticine->setEmaemp($emailEmpresa);

				$plasticine->setLocpro($locproCodigo);
				$plasticine->setCodtra($transporteLLegada);
				$plasticine->setCodmot($motivoViaje);
				$plasticine->setHora($hora);
				$plasticine->setNota($nota);
				if($plasticine->save()==false){
					foreach($plasticine->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}

				Flash::success($traslate['SaveOK']);
				return $this->routeToAction('doPay');

			} else {
				Flash::error($traslate['NoNumeroReserva']);
				return $this->routeToAction('index');
			}
		} else {
			$this->filter($numeroReserva, 'alpha');
			$this->redirect('webcheckin/index/'.$numeroReserva);
		}

	}

	private function _getTarifas($reserva){
		$tarifas = array();
		$plareses = $this->Plares->find("numres='{$reserva->getNumres()}'");
		foreach($plareses as $plares){
			$plan = $this->Planes->findFirst("codpla='{$plares->getCodpla()}' AND estado='A'");
			if($plan==false){
				Flash::error('The rate '.$tarifa->getCodpla().' doesn\'t exists');
			} else {
				$tarifas[] = array(
					'nombre' => $plan->getDescripcion(),
					'fechaInicial' => $plares->getFecini(),
					'fechaFinal' => $plares->getFecfin(),
					'tarifa' => $this->_calculateRate($plan, $reserva, $plares)
				);
			}
		}
		return $tarifas;
	}

	private function _getGarantias($numeroReserva){
		$garantias = array();
		$garreses = $this->Garres->find("numres='$numeroReserva' AND estado='N'");
		foreach($garreses as $garres){
			$garantia = array('fecha' => $garres->getFecha());
			$reccaj = $this->Reccaj->findFirst("numrec='{$garres->getNumrec()}'");
			if($reccaj==false){
				$recegr = $this->Recegr->findFirst("numrec='{$garres->getNumegr()}'");
				if($recegr==false){
					$garantia['recibo'] = '???';
					$garantia['nota'] = 'El recibo de caja no existe, consulte con el hotel más información';
				} else {
					$garantia['recibo'] = 'RE-'.$garres->getNumegr();
					$garantia['nota'] = $recegr->getNota();
					$garantia['valor'] = -$garres->getTotal();
				}
			} else {
				$garantia['recibo'] = 'RC-'.$garres->getNumrec();
				$garantia['nota'] = $reccaj->getNota();
				$garantia['valor'] = $garres->getTotal();
			}
			$garantias[] = $garantia;
		}
		return $garantias;
	}

	/**
	 * Calcula el valor por noche de una tarifa
	 *
	 * @param Planes $plan
	 * @param Reservas $reserva
	 * @param Plares $plares
	 *
	 * @return array
	 */
	private function _calculateRate($plan, $reserva, $plares){
		$valorIva = 0;
		$valorNoche = 0;
		foreach($plan->getDetpla() as $detpla){
			if($detpla->getCodcar()>0){
				$cargo = $this->Cargos->findFirst("codcar='{$detpla->getCodcar()}' AND estado='A'");
				if($cargo==false){
					continue;
				}
				$valor = $detpla->getValor();
				if($cargo->getPoriva()>0||$cargo->getPorser()>0){
					if($cargo->getIvainc()=='S'){
						$valor = $valor/(($cargo->getPoriva()+$cargo->getPorser())/100+1);
	   					$iva = $valor*($cargo->getPoriva()/100);
	   					$valorServicio = $valor*($cargo->getPorser()/100);
	   					$valorTerceros = 0;
					} else {
						$valorServicio = $valor*($cargo->getPorser()/100+1) - $valor;
	   					$iva = $valor*($cargo->getPoriva()/100+1) - $valor;
	   					$valorTerceros = 0;
					}
				} else {
					$iva = 0;
					$valorServicio = 0;
					$valorTerceros = 0;
				}
				if($cargo->getDescaj()=='S'){
					$valorNoche-=$valor;
					$valorNoche-=$iva;
					$valorNoche-=$valorServicio;
					$valorNoche-=$valorTerceros;
					$valorIva-=$iva;
				} else {
					$valorNoche+=$valor;
					$valorNoche+=$iva;
					$valorNoche+=$valorServicio;
					$valorNoche+=$valorTerceros;
					$valorIva+=$iva;
				}
			}
		}
		if($plan->getTipper()=='A'){
			$valorNoche = $valorNoche*$reserva->getNumadu();
		} else {
			if($plan->getTipper()=='N'){
				$valorNoche = $valorNoche*$reserva->getNumnin();
			} else {
				if($plan->getTipper()=='I'){
					$valorNoche = $valorNoche*$reserva->getNuminf();
				}
			}
		}
		$noches = Date::difference($plares->getFecfin(), $plares->getFecini());
		if($noches>0){
			return array(
				'valorNoche' => LocaleMath::round($valorNoche, 0),
				'impuestos' => LocaleMath::round($valorIva, 0),
				'noches' => $noches,
				'totalNoches' => LocaleMath::round($noches*$valorNoche, 0)
			);
		}
	}

	public function doPayAction(){

		$traslate = $this->_loadTraslation();

		$numeroReserva = Session::get('numeroReserva');
		if($numeroReserva>0){
			$reserva = $this->Reserva->findFirst("numres='$numeroReserva'");
			if($reserva==false){
				Flash::error($traslate['NoReserva']);
				return $this->routeToAction('enterInformation');
			}
		} else {
			Flash::error($traslate['NoReserva']);
			return $this->routeToAction('enterInformation');
		}

		if(!Wepax::isEnabled()){
			Flash::error($traslate['NoPaysEnabled']);
			return $this->routeToAction('enterInformation');
		}

		if($reserva->getEstado()=='G'){
			if($reserva->getCarta()=='S'){
				Flash::notice($traslate['LetterGuaranted']);
			}
		}
		$this->setParamToView('reserva', $reserva);

		$depositosPendientes = $this->Plasabo->count("numres='$numeroReserva' AND estado='P'");
		if($depositosPendientes>0){
			DepositTrack::updatePendent();
			$reserva = $this->Reserva->findFirst("numres='$numeroReserva'");
			if($reserva==false){
				Flash::error($traslate['NoReserva']);
				return $this->routeToAction('enterInformation');
			}
			$depositosPendientes = $this->Plasabo->count("numres='$numeroReserva' AND estado='P'");
		}
		$this->setParamToView('depositosPendientes', $depositosPendientes);



		$tarifas = $this->_getTarifas($reserva);
		$this->setParamToView('tarifas', $tarifas);

		$garantias = $this->_getGarantias($numeroReserva);
		$this->setParamToView('garantias', $garantias);

		$controllerRequest = ControllerRequest::getInstance();
		if(!$controllerRequest->isSetPostParam('pagoAdicional')){
			Tag::displayTo('pagoAdicional', 0);

			$email = null;
			if($reserva->getNit()!='0'){
				$empresa = $this->Empresas->findFirst("nit='{$reserva->getNit()}'");
				if($empresa!=false){
					$empcon = $this->Empcon->findFirst("nit='{$reserva->getNit()}' AND tipo='R'");
					if($empcon!=false){
						$email = $empcon->getEMail();
					}
					if($email==null){
						if($empresa->getEMail()!='@'){
							$email = $empresa->getEMail();
						}
					}
				}
			}

			if($email==null){
				if($reserva->getCedula()!='0'){
					$cliente = $this->Clientes->findFirst("cedula='{$reserva->getCedula()}'");
					if($cliente!=false){
						if($cliente->getEMail()!='@'){
							$email = $cliente->getEMail();
						}
					}
				}
			}

			if($email==null){
				$plasticine = $this->Plasticine->findFirst("numres='$numeroReserva'");
				if($plasticine!=false){
					$email = $plasticine->getEmail();
					if($plasticine->save()==false){
						foreach($plasticine->getMessages() as $message){
							Flash::error($message->getMessage());
						}
					}
				}
			}

			Tag::displayTo('email', $email);


		}

	}

	public function savePayAction(){

		$traslate = $this->_loadTraslation();

		$numeroReserva = Session::get('numeroReserva');
		if($numeroReserva>0){
			$reserva = $this->Reserva->findFirst("numres='$numeroReserva'");
			if($reserva==false){
				Flash::error($traslate['NoReserva']);
				return;
			}
		} else {
			Flash::error($traslate['NoReserva']);
			return;
		}

		if(!Wepax::isEnabled()){
			Flash::error($traslate['NoPaysEnabled']);
			return;
		}

		$depositosPendientes = $this->Plasabo->count("numres='$numeroReserva' AND estado='P'");
		if($depositosPendientes>0){
			DepositTrack::updatePendent();
			$depositosPendientes = $this->Plasabo->count("numres='$numeroReserva' AND estado='P'");
			if($depositosPendientes>0){
				Flash::notice($traslate['ValidandoPagos']);
				return $this->routeToAction('doPay');
			}
		}

		$totalIva = 0;
		$totalEstadia = 0;
		$tarifas = $this->_getTarifas($reserva);
		foreach($tarifas as $tarifa){
			$totalEstadia+=($tarifa['tarifa']['totalNoches']);
			$totalIva+=$tarifa['tarifa']['impuestos'];
		}

		$totalGarantia = 0;
		$garantias = $this->_getGarantias($numeroReserva);
		foreach($garantias as $garantia){
			$totalGarantia+=$garantia['valor'];
		}

		$totalPago = 0;
		if(($totalEstadia-$totalGarantia)>0){
			$totalPago+=($totalEstadia-$totalGarantia);
		}

		$pagoAdicional = $this->getPostParam('pagoAdicional', 'double');
		$totalPago+=$pagoAdicional;

		if($totalPago<150000){
			Flash::error($traslate['Minimo150']);
			return $this->routeToAction('doPay');
		}

		if($totalPago>3000000){
			Flash::error($traslate['Maximo3000']);
			return $this->routeToAction('doPay');
		}

		if($reserva->getEstado()=='H'){
			Flash::error($traslate['ReservaEfectiva']);
			return;
		}

		$email = $this->getPostParam('email', 'email');
		if(!$email){
			Flash::error($traslate['NoEmail']);
			return $this->routeToAction('doPay');
		}

		$plasticine = $this->Plasticine->findFirst("numres='$numeroReserva'");
		if($plasticine==false){
			Flash::error($traslate['NoReserva']);
			return;
		}

		$publicHost = CoreConfig::getAppSetting('public_host', 'plasticine');
		$ticketToken = Wepax::generateLocalTicket('DP');

		$ticketData = array(
			'Token' => $ticketToken,
			'Type' => 'DP',
			'DocumentKind' => null,
			'DocumentNumber' => null,
			'Name' => null,
			'Telephone' => null,
			'Email' => $email,
			'TotalValue' => $totalPago,
			'TotalTaxes' => $totalIva,
			'Description' => 'ABONO/DEPOSITO DE RESERVACION EN EL HOTEL '.JASMIN_SYSTEM_CAPTION.' Reserva='.$reserva->getNumres(),
			'Silent' => 'S',
			'UrlRedirect' => $publicHost.'/transaction/close',
			'ExpireTime' => 900
		);

		if($reserva->getCedula()!='0'||$reserva->getNit()!='0'){

			if($reserva->getNit()!='0'){
				$empresa = $this->Empresas->findFirst("nit='{$reserva->getNit()}'");
				if($empresa!=false){
					$ticketData['DocumentNumber'] = $empresa->getNit();
					$ticketData['Name'] = $empresa->getNombre();
					$ticketData['Email'] = $email;
					$ticketData['Telephone'] = $empresa->getTelefono();
				}
			} else {
				if($reserva->getNit()!='0'){
					$cliente = $this->Clientes->findFirst("cedula='{$reserva->getCedula()}'");
					if($cliente!=false){
						$ticketData['DocumentType'] = 'CC';
						$ticketData['DocumentNumber'] = $cliente->getNit();
						$ticketData['Name'] = $cliente->getNombre();
						$ticketData['Email'] = $email;
						$ticketData['Telephone'] = $cliente->getTelefono1();
					}
				}
			}

		}

		if(!$ticketData['DocumentKind']){
			if(!$plasticine->getTipdoc()){
				Flash::error($traslate['WebcheckinRequerido']);
				return $this->routeToAction('enterInformation');
			}
			switch($plasticine->getTipdoc()){
				case 'C':
					$ticketData['DocumentKind'] = 'CC';
					break;
				case 'P':
					$ticketData['DocumentKind'] = 'CC';
					break;
				case 'E':
					$ticketData['DocumentKind'] = 'CE';
					break;
				default:
					$ticketData['DocumentKind'] = 'CC';
			}
		}

		if(!$ticketData['DocumentNumber']){
			if(!$plasticine->getCedula()){
				Flash::error($traslate['WebcheckinRequerido']);
				return $this->routeToAction('enterInformation');
			}
			$ticketData['DocumentNumber'] = $plasticine->getCedula();
		}

		if(!$ticketData['Name']){
			if(!$plasticine->getPriape()){
				Flash::error($traslate['WebcheckinRequerido']);
				return $this->routeToAction('enterInformation');
			}
			$ticketData['Name'] = $plasticine->getPriape().' '.$plasticine->getNombre();
		}

		if(!$ticketData['Telephone']){
			if(!$plasticine->getTelefono()){
				Flash::error($traslate['WebcheckinRequerido']);
				return $this->routeToAction('enterInformation');
			}
			$ticketData['Telephone'] = $plasticine->getTelefono();
		}

		try {

			$transaction = TransactionManager::getUserTransaction();

			try {

				$ticket = Wepax::invokeMethod('createTransactionTicket', $ticketData);
				if($ticket['status']=='OK'){

					$controllerRequest = $this->getRequestInstance();
					$ipAddress = $controllerRequest->getClientAddress();

					$plasabo = new Plasabo();
					$plasabo->setTransaction($transaction);
					$plasabo->setNumres($numeroReserva);
					$plasabo->setPlasticineId($plasticine->getId());
					$plasabo->setToken($ticketToken);
					$plasabo->setValor($totalPago);
					$plasabo->setEstado('P');
					if($plasabo->save()==false){
						foreach($plasabo->getMessages() as $message){
							$transaction->rollback($message->getMessage());
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


		}
		catch(TransactionFailed $e){
			Flash::error($e->getMessage());
			return $this->routeToAction('doPay');
		}
	}

	public function setLocaleAction($locale=''){
		$language = $this->filter($locale, 'locale');
		if($language!=''){
			$locale = new Locale($language);
			Session::set('locale', $language);
		}
		if(isset($_SERVER['HTTP_REFERER'])&&strpos($_SERVER['HTTP_REFERER'], 'setLocale')===false){
			$this->setResponse('view');
			header('Location: '.$_SERVER['HTTP_REFERER']);
		} else {
			$this->routeTo(array('controller' => 'index'));
		}
	}

	public function logoutAction($numeroReserva=0){
		Session::unsetData('numeroReserva');
		$this->filter($numeroReserva, 'alpha');
		$this->redirect('webcheckin/index/'.$numeroReserva);
	}

	public function notFoundAction(){
		Flash::error('Página no encontrada/Page not found');
	}

	public function pendentAction(){

	}

}