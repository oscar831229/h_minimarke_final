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

Core::importFromLibrary('Hfos/Tpc','Tpc.php');

/**
 * Abono_ContratoController
 *
 * Generación de abonos a contratos
 */
class Abono_ContratoController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$this->setParamToView('message', 'Ingrese un criterio de búsqueda para consultar el contrato');
	}

	/**
	 * Metodo que busca contratos y prepara por si hay paginación
	 */
	public function buscarAction(){
		$this->setResponse('json');
		$numeroContrato = $this->getPostParam('numeroContrato');
		$identificacion = $this->getPostParam('identificacion', 'int');
		$nombres = $this->getPostParam('nombres', 'alpha', 'striptags', 'extraspaces');
		$apellidos = $this->getPostParam('apellidos', 'alpha', 'striptags', 'extraspaces');
		$response = array();
		$conditions = array();
		if($identificacion>0){
			$conditions[] = 'identificacion = \''.$identificacion.'\'';
		}
		if($numeroContrato){
			$conditions[] = 'numero_contrato LIKE \'%'.$numeroContrato.'%\'';
		}
		if($nombres){
			$nombres = i18n::strtoupper($nombres);
			$conditions[] = 'nombres LIKE \'%'.$nombres.'%\'';
		}
		if($apellidos){
			$apellidos = i18n::strtoupper($apellidos);
			$conditions[] = 'apellidos LIKE \'%'.$apellidos.'%\'';
		}
		if(count($conditions)>0){
			$socios = $this->Socios->find(array(implode(' AND ', $conditions), 'order' => 'numero_contrato DESC'));
		}else{
			$socios = $this->Socios->find(array('order' => 'numero_contrato DESC'));
		}
		if(count($socios)==0){
			$response['number'] = '0';
		}else{
			if(count($socios)==1){
				$socio = $socios->getFirst();
				$response['number'] = '1';
				$response['key'] = 'id='.$socio->getId();
			}else{
				$responseResults = array(
					'headers' => array(
						array('name' => 'Número de Contrato', 'ordered' => 'S'),
						array('name' => 'Cédula', 'ordered' => 'N'),
						array('name' => 'Nombres', 'ordered' => 'N'),
						array('name' => 'Apellidos', 'ordered' => 'N'),
						array('name' => 'Estado Contrato', 'ordered' => 'N'),
						array('name' => 'Estado Movimiento', 'ordered' => 'N')
					)
				);
				$data = array();
				$estadoContratoObj		= EntityManager::get('EstadoContrato')->find();
				$estadoMovimientoObj	= EntityManager::get('EstadoMovimiento')->find();
				$estadoContratoArray	= array();
				$estadoMovimientoArray	= array();
				foreach($estadoContratoObj as $estadoContrato){
					$estadoContratoArray[$estadoContrato->getCodigo()] = $estadoContrato->getNombre();
				}
				foreach($estadoMovimientoObj as $estadoMovimiento){
					$estadoMovimientoArray[$estadoMovimiento->getCodigo()] = $estadoMovimiento->getNombre();
				}
				foreach($socios as $socio){
					$estadoContratoLabel = '???';
					if(isset($estadoContratoArray[$socio->getEstadoContrato()])){
						$estadoContratoLabel = $estadoContratoArray[$socio->getEstadoContrato()];
					}
					$estadoMovimientoLabel = '???';
					if(isset($estadoMovimientoArray[$socio->getEstadoMovimiento()])){
						$estadoMovimientoLabel = $estadoMovimientoArray[$socio->getEstadoMovimiento()];
					}
					$data[] = array(
						'primary' => array('id='.$socio->getId()),
						'data' => array(
							array('key' => 'numeroContrato', 'value' => $socio->getNumeroContrato()),
							array('key' => 'identificacion', 'value' => $socio->getIdentificacion()),
							array('key' => 'nombres', 'value' => $socio->getNombres()),
							array('key' => 'apellidos', 'value' => $socio->getApellidos()),
							array('key' => 'estado_contrato', 'value' => $estadoContratoLabel),
							array('key' => 'estado_movimiento', 'value' => $estadoMovimientoLabel),
						)
					);
				}
				$responseResults['data'] = $data;
				$response['numberResults'] = count($responseResults['data']);
				$response['results'] = $responseResults;
				$response['number'] = 'n';
			}
		}
		return $response;
	}

	/**
	 * Metodo que visualiza el estado de cuenta de un contrato
	 *
	 */
	public function verAction(){
		$this->setParamToView('message', 'aquí podra ver información detallada del cliente y sus movimientos');
		$sociosId = $this->getPostParam('id', 'int');
		if($sociosId>0){
			$socio = $this->Socios->findFirst($sociosId);
			if($socio==false){
				Flash::error('No existe el cheque');
				$this->routeToAction('errores');
			}
			if($socio->getEstadoContrato()=='AA'){
				$this->setParamToView('message', 'El contrato está anulado');
			}
			$new = $this->getPostParam('new', 'int');
			if($new==true){
				Flash::success('Se creó el abono correctamente');
			}
			$this->setParamToView('socio', $socio);
			$this->setParamToView('EstadoContrato', $this->EstadoContrato);
			$this->setParamToView('EstadoMovimiento', $this->EstadoMovimiento);
		} else {
			Flash::error('No existe el socio');
			$this->routeToAction('errores');
		}
	}

	public function erroresAction(){}

	/**
	 * Metodo que abre la vista de abono de contrato
	 *
	 */
	public function nuevoAction(){
		$sociosId = $this->getPostParam('id', 'int');
		if($sociosId>0){
			$this->setParamToView('message', 'Ingrese los datos del abono y haga click en "Grabar"');
			
			$cuentasArray = array();

			$this->setParamToView('sociosId', $sociosId);
			$this->setParamToView('cuentas', $this->Cuentas->find(array('conditions'=>'estado="A"', 'order'=>'banco ASC')));
			$this->setParamToView('formasPago', $this->FormasPago->find(array("estado='A'")));

			Tag::displayTo('fechaPago', (string) Date::getCurrentDate());
			Tag::displayTo('fechaRecibo', (string) Date::getCurrentDate());

			if($this->getPostParam('tipo')){
				$this->setParamToView('tipoAbono',$this->getPostParam('tipo'));
			}
		}else{
			Flash::error('No existe el socio');
			$this->routeToAction('errores');
		}
	}


	/**
	 * Metodo que realizá una abono a un contrato
	 *
	 * @return array json
	 */
	public function abonoAction(){
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();

		try {
			$transaction = TransactionManager::getUserTransaction();

			$sociosId = $this->getPostParam('sociosId', 'int');
			if($sociosId<0){
				$transaction->rollback('Abono: El socio no es valido');
			}

			$reciboProvisional	= $this->getPostParam('reciboProvisional', 'int');
			$ciudadPago			= $this->getPostParam('ciudadPago','int');
			$fechaPago			= $this->getPostParam('fechaPago', 'date');
			$fechaRecibo		= $this->getPostParam('fechaRecibo', 'date');
			$cuentasId			= $this->getPostParam('cuentasId', 'int');
			$porcentCondonacion	= $this->getPostParam('condonacion', 'int');

			//Agrupamos los pago hechos
			$dataF = array(
				'formaPago'		=> $this->getPostParam('formaPago'),
				'numeroForma'	=> $this->getPostParam('numeroForma'),
				'valor'			=> $this->getPostParam('valor')
			);

			$formasPagos = TPC::unificaFormasPagos($dataF, $transaction);
			//$transaction->rollback(print_r($dataF,true).'<br>'.print_r($formasPagos,true));
			
			//Agregamos el pago
			$data = array(
				'sociosId'			=>  $sociosId,
				'fechaRecibo'		=>  $fechaRecibo,
				'fechaPago'			=>  $fechaPago,
				'formasPago'		=>  $formasPagos,
				'cuentasId'			=>  $cuentasId,
				'reciboProvisional'	=>  $reciboProvisional,
				'ciudadPago'		=>  $ciudadPago,
				'porcentCondonacion'=>  $porcentCondonacion,
				'debug'				=>  true
			);

			//$transaction->rollback(print_r($data, true));
			TPC::addAbonoContrato($data, $transaction);

			if(isset($data['reciboPagoId'])){
				$transaction->commit();
				return array(
					'status'	=> 'OK',
					'message'	=> 'Se generó el abono al contrato correctamente',
					'id'		=> $data['reciboPagoId'],
					'sociosId'	=> $sociosId
				);
			}else{
				return array(
					'status' => 'FAILED',
					'message' => 'Error: '.print_r($data,true)
				);
			}
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function errorAction(){
	}

	/**
	 * Metodo que realizá una abono posterior a un contrato, este recalcula los pagos
	 * si es necesario creando también historia
	 *
	 * @return array json
	 */
	public function abonoPosteriorAction(){
		$this->setResponse('json');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$sociosId = $this->getPostParam('sociosId', 'int');
			if($sociosId<0){
				$transaction->rollback('Abono: El socio no es valido');
			}
			$socio = $this->Socios->findFirst($sociosId);
			if($socio==false){
				$transaction->rollback('Abono: El socio no existe');
			}
			//Obtenemos datos de pago posterior
			$reciboProvisional	= $this->getPostParam('reciboProvisional', 'int');
			$ciudadPago			= $this->getPostParam('ciudadPago','int');
			$fechaPago			= $this->getPostParam('fechaPago', 'date');
			$fechaRecibo		= $this->getPostParam('fechaRecibo', 'date');
			$cuentasId			= $this->getPostParam('cuentasId', 'int');
			//Agrupamos los pago hechos
			$dataF = array(
				'formaPago'		=> $this->getPostParam('formaPago'),
				'numeroForma'	=> $this->getPostParam('numeroForma'),
				'valor'			=> $this->getPostParam('valor')
			);
			$formasPagos = TPC::unificaFormasPagos($dataF, $transaction);
			//$transaction->rollback(print_r($dataF,true).'<br>'.print_r($formasPagos,true));
			//creamos estructura de pago posterior
			$dataPosterior = array(
				'sociosId'			=>  $sociosId,
				'fechaRecibo'		=>  $fechaRecibo,
				'fechaPago'			=>  $fechaPago,
				'formasPago'		=>  $formasPagos,
				'cuentasId'			=>  $cuentasId,
				'reciboProvisional'	=>  $reciboProvisional,
				'ciudadPago'		=>  $ciudadPago,
				'posterior'			=>	true,
				'debug'				=>  true
			);
			//obtenemos el ultimo recibo de pago hecho
			$recibosPagos = $this->RecibosPagos->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$sociosId, 'order'=>'fecha_pago DESC'));
			if($recibosPagos==false){
				$transaction->rollback('PagoPosterior: No existen pagos aun en el contrato para ser un pago posterior');
			}else{
				//Si la fecha pago es mayor al ultimo pago hecho no es un pago posterior
				if(TPC::dateGreaterThan($fechaPago, $recibosPagos->getFechaPago())==true){
					$transaction->rollback('Nos es valida la fecha de pago posterior debe ser menor a '.$recibosPagos->getFechaPago());
				}
			}
			//Generamos formato de datos para recalcular de los recibos de caja existentes a un id de socios
			$formatos = TPC::crearFormatoDePagoDeRcs(array('sociosId'=>$sociosId), $transaction);
			//agregamos nuevo abono a formatos
			$formatos[]=$dataPosterior;
			//Ordenamos los pagos segun su fecha
			TPC::ordenaPagos($formatos);
			//Copiamos a historia los datos
			$configHistoria = array(
				'sociosId'		=> $sociosId,
				'notaHistoria'  => array(
					'estado'		=> 'P', //Pago Posterior
					'fecha'			=> date('Y-m-d'),
					'observaciones' => 'Se realizó un pago posterior al contrato '.$socio->getNumeroContrato().' con fecha '.$dataPosterior['fechaPago'].' recalculando los recibos de caja con fecha mayores a esta.',
					'copiarContrato'=> true //copia el contenido del contrato en sus repectivas tabla h(historia)
				),
				'debug'			=> true
			);
			TPC::copiarAHistoria($configHistoria, $transaction);
			//Limpiamos pagos de contrato para recalcular
			TPC::limpiarAllPagos($sociosId, $transaction, false);
			//Insertamos el pago nuevo al nuevo contrato
			Rcs::disable();
			foreach($formatos as $formato){
				$formato['sociosId'] = $sociosId;//asignamos nuevo id de socio
				$formato['setValidar'] = false;//Decimos que no valide nada en recibos pagos
				TPC::addAbonoContrato($formato, $transaction);
				if(isset($formato['posterior']) && $formato['posterior']==true){
					$rcDePosterior = $formato['rcReciboPago'];
					$reciboPagoIdPosterior = $formato['reciboPagoId'];
				}
			}
			Rcs::enable();
			//???
			//$transaction->rollback('Aaaaa,rcPosterio: '.$rcDePosterior.',rcActual: '.$this->Empresa->findFirst()->getcRc().', historia: '.print_r($configHistoria,true).', '.print_r($formatos, true));
			$transaction->commit();
			return array(
				'status'	=> 'OK',
				'message'	=> 'Se generó el abono posterior al contrato correctamente',
				'id'		=> $reciboPagoIdPosterior,
				'sociosId'	=> $sociosId
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	public function getDetalleAbonoAction(){
		$this->setResponse('view');
		$recibosPagosId = $this->getPostParam('id', 'int');
		if($recibosPagosId>0){
			$RecibosPagos = $this->RecibosPagos->findFirst($recibosPagosId);
			if($RecibosPagos!=false){
				$this->setParamToView('RecibosPagos', $RecibosPagos);
			}else{
				$this->routeToAction('error');
			}
			$this->setParamToView('status', 'OK');
		}else{
			$this->routeToAction('error');
		}
	}

	/**
	 * Metodo que realizá un traslado de un recibo de caja errado a un contrato diferente
	 * con histira que respalde la transacción
	 *
	 * @return array json
	 */
	public function abonoErradoAction(){
		$this->setResponse('json');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$sociosIdNew = $this->getPostParam('sociosIdNew', 'int');
			if($sociosIdNew<=0){
				$transaction->rollback('Abono: El socio a trasladar no es valido');
			}
			$socioNew = $this->Socios->findFirst($sociosIdNew);
			if($socioNew==false){
				$transaction->rollback('Abono: El socio a trasladar no existe');
			}
			$sociosIdOld = $this->getPostParam('sociosIdOld', 'int');
			if($sociosIdOld<=0){
				$transaction->rollback('Abono Errado: El socio actual no es valido');
			}
			$socioOld = $this->Socios->findFirst($sociosIdOld);
			if($socioOld==false){
				$transaction->rollback('Abono Errado: El socio actual no existe');
			}
			if($sociosIdOld==$sociosIdNew){
				$transaction->rollback('Abono Errado: El contrato a trasladar debe ser diferente al actual');
			}
			$reciboPagoId = $this->getPostParam('reciboPagoId', 'int');
			if($reciboPagoId<=0){
				$transaction->rollback('Abono: El recibo de caja actual no es valido');
			}
			$recibosPagos = $this->RecibosPagos->findFirst($reciboPagoId);
			if($recibosPagos==false){
				$transaction->rollback('Abono: El recibo de caja actual no existe');
			}
			//Generamos el formato del errado
			$formatosOld = TPC::crearFormatoDePagoDeRcs(array('sociosId'=>$sociosIdOld, 'reciboPagoId'=>$reciboPagoId), $transaction);
			$formatosNew = TPC::crearFormatoDePagoDeRcs(array('sociosId'=>$sociosIdNew), $transaction);
			//Copiamos a historia los datos del contrato errado
			$detalleHistoria = 'Se realizó un abono errado, quitando el recibo de caja '.$recibosPagos->getRc().' del contrato '.$socioOld->getNumeroContrato().'  y se traslada a el contrato '.$socioNew->getNumeroContrato().'. Se recalculo de nuevo los pago sin el abono errado y en el otro contrato se reclaculo sumandolo.';
			$configHistoriaOld = array(
				'sociosId'			=> $sociosIdOld,
				'notaHistoria'		=> array(
					'sociosIdErrado'=> $sociosIdOld,
					'estado'		=> 'E', //Pago Errado
					'fecha'			=> date('Y-m-d'),
					'rcErrados'		=> $reciboPagoId,
					'observaciones' => $detalleHistoria,
					'copiarContrato'=> true //copia el contenido del contrato en sus repectivas tabla h(historia)
				),
				'debug'				=> true
			);
			TPC::copiarAHistoria($configHistoriaOld, $transaction);
			//Copiamos a tablas de historia el contrato a trasladar el rc y usamos la misma historia del contrato errado
			$configHistoriaNew = array(
				'sociosId'			=> $sociosIdNew,
				'notaHistoria'		=> array(
					'notaHistoriaId'=> $configHistoriaOld['notaHistoriaId'],
					'copiarContrato'=> true //copia el contenido del contrato en sus repectivas tabla h(historia)
				),
				'debug'				=> true
			);
			TPC::copiarAHistoria($configHistoriaNew, $transaction);
			//Anular Recibo de caja y recalcula todos los pagos sin el
			$anularConfig = array('reciboPagoId'=>$reciboPagoId, 'notaHistoriaId'=>$configHistoriaOld['notaHistoriaId']);
			TPC::anularReciboCaja($anularConfig, $transaction);
			//Agregamos recibo de caja anulado a contrato a formatos de contrato a trasladar
			foreach($formatosOld as $formato){
				//asignamos el nuevo propietario de el recibo de caja anulado
				$formato['sociosId'] = $sociosIdNew;
				$formatosNew[]=$formato;
			}
			//Ordenamos formatos por fecha
			TPC::ordenaPagos($formatosNew);
			//Limpiamos pagos de contrato para recalcular el contrato a trasladar
			TPC::limpiarAllPagos($sociosIdNew, $transaction, false);
			//Insertamos los pagos mas elnuevo al contrato a trasladar
			Rcs::disable();
			foreach($formatosNew as $formato){
				$formato['setValidar'] = false;//Decimos que no valide nada en recibos pagos
				TPC::addAbonoContrato($formato, $transaction);
				//Obtenemos el nuevo id
				if($formato['rcReciboPago']==$recibosPagos->getRc()){
					$reciboPagoIdNew = $formato['reciboPagoId'];
				}
			}
			Rcs::enable();
			//$transaction->rollback('Aaaa, '.print_r($formatosNew, true));
			$transaction->commit();
			return array(
				'status'	=> 'OK',
				'message'	=> 'Se generó el traslado del recibo de caja al contrato correctamente recalculando ambos contratos',
				'id'		=> $reciboPagoIdNew,
				'sociosId'	=> $sociosIdNew
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	/**
	 * Metodo que realizá una abono posterior a un contrato, este recalcula los pagos
	 * si es necesario creando también historia
	 *
	 * @return array json
	 */
	public function abonoCapitalAction(){
		$this->setResponse('json');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$sociosId = $this->getPostParam('sociosId', 'int');
			if($sociosId<0){
				$transaction->rollback('Abono: El socio no es valido');
			}
			$socio = $this->Socios->findFirst($sociosId);
			if($socio==false){
				$transaction->rollback('Abono: El socio no existe');
			}
			//Obtenemos datos de pago posterior
			$reciboProvisional	= $this->getPostParam('reciboProvisional', 'int');
			$ciudadPago			= $this->getPostParam('ciudadPago','int');
			$fechaPago			= $this->getPostParam('fechaPago', 'date');
			$fechaRecibo		= $this->getPostParam('fechaRecibo', 'date');
			$cuentasId			= $this->getPostParam('cuentasId', 'int');
			//Agrupamos los pago hechos
			$dataF = array(
				'formaPago'		=> $this->getPostParam('formaPago'),
				'numeroForma'	=> $this->getPostParam('numeroForma'),
				'valor'			=> $this->getPostParam('valor')
			);
			$formasPagos = TPC::unificaFormasPagos($dataF, $transaction);
			//$transaction->rollback(print_r($dataF,true).'<br>'.print_r($formasPagos,true));
			//creamos estructura de pago posterior
			$dataCapital = array(
				'sociosId'			=>  $sociosId,
				'fechaRecibo'		=>  $fechaRecibo,
				'fechaPago'			=>  $fechaPago,
				'formasPago'		=>  $formasPagos,
				'cuentasId'			=>  $cuentasId,
				'reciboProvisional'	=>  $reciboProvisional,
				'ciudadPago'		=>  $ciudadPago,
				'posterior'			=>	true,
				'debug'				=>  true
			);
			//obtenemos el ultimo recibo de pago hecho
			$recibosPagos = $this->RecibosPagos->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$sociosId, 'order'=>'fecha_pago DESC'));
			if($recibosPagos==false){
				//Puede agregar la fecha que desee
			}else{
				//Si la fecha pago es mayor al ultimo pago hecho no es un pago posterior
				if(TPC::dateGreaterThan($fechaPago, $recibosPagos->getFechaPago())==false){
					$transaction->rollback('Nos es valida la fecha de pago a capital debe ser mayor a '.$recibosPagos->getFechaPago());
				}
			}
			//Generamos formato de datos para recalcular de los recibos de caja existentes a un id de socios
			$formatos = array();
			//agregamos nuevo abono a formatos
			$formatos[]=$dataCapital;
			//Copiamos a historia los datos
			$configHistoria = array(
				'sociosId'		=> $sociosId,
				'notaHistoria'  => array(
					'estado'		=> 'K', //Abono a Capital
					'fecha'			=> date('Y-m-d'),
					'observaciones' => 'Se realizó un abono a capital de '.$formasPagos['total'].'.',
					'copiarContrato'=> true //copia el contenido del contrato en sus repectivas tabla h(historia)
				),
				'debug'			=> true
			);
			TPC::copiarAHistoria($configHistoria, $transaction);
			//Insertamos el pago nuevo al nuevo contrato
			Rcs::disable();
			foreach($formatos as $formato){
				$formato['sociosId'] = $sociosId;//asignamos nuevo id de socio
				$formato['setValidar'] = false;//Decimos que no valide nada en recibos pagos
				TPC::addAbonoCapitalContrato($formato, $transaction);
				$rcDeCapital = $formato['rcReciboPago'];
				$reciboPagoIdCapital = $formato['reciboPagoId'];
			}
			Rcs::enable();
			//???
			//$transaction->rollback('Aaaaa,rcPosterio: '.$rcDeCapital.',rcActual: '.$this->Empresa->findFirst()->getcRc().', historia: '.print_r($configHistoria,true).', '.print_r($formatos, true));
			$transaction->commit();
			return array(
				'status'	=> 'OK',
				'message'	=> 'Se generó el abono a capital al contrato correctamente',
				'id'		=> $reciboPagoIdCapital,
				'sociosId'	=> $sociosId
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	/**
	 * Metodo que realizá una abono otros a un contrato
	 *
	 * @return array json
	 */
	public function abonoOtrosAction(){
		$this->setResponse('json');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$sociosId = $this->getPostParam('sociosId', 'int');
			if($sociosId<0){
				$transaction->rollback('El id del contrato no es valido');
			}
			$socio = $this->Socios->findFirst($sociosId);
			if($socio==false){
				$transaction->rollback('El contrato no existe');
			}
			//Obtenemos datos de pago posterior
			$reciboProvisional	= $this->getPostParam('reciboProvisional', 'int');
			$fechaPago			= $this->getPostParam('fechaPago', 'date');
			$cuentasId			= $this->getPostParam('cuentasId', 'int');
			$detallePago		= $this->getPostParam('concepto', 'striptags');
			//Agrupamos los pago hechos
			$dataF = array(
				'formaPago'		=> $this->getPostParam('formaPago'),
				'numeroForma'	=> $this->getPostParam('numeroForma'),
				'valor'			=> $this->getPostParam('valor')
			);
			$formasPagos = TPC::unificaFormasPagos($dataF, $transaction);
			//creamos estructura de pago posterior
			$dataOtros = array(
				'sociosId'			=> $sociosId,
				'fechaPago'			=> $fechaPago,
				'formasPago'		=> $formasPagos,
				'cuentasId'			=> $cuentasId,
				'reciboProvisional'	=> $reciboProvisional,
				'detallePago'		=> $detallePago,
				'debug'				=> true
			);
			//Generamos formato de datos para recalcular de los recibos de caja existentes a un id de socios
			$formatos = array();
			//agregamos nuevo abono a formatos
			$formatos[]=$dataOtros;
			//Insertamos el pago nuevo al nuevo contrato
			Rcs::disable();
			foreach($formatos as $formato){
				$formato['sociosId'] = $sociosId;//asignamos nuevo id de socio
				$formato['setValidar'] = false;//Decimos que no valide nada en recibos pagos
				TPC::addAbonoOtros($formato, $transaction);
				$rcDeOtros			= $formato['rcReciboPago'];
				$reciboPagoIdOtros	= $formato['reciboPagoId'];
			}
			Rcs::enable();
			$transaction->commit();
			return array(
				'status'	=> 'OK',
				'message'	=> 'Se generó el abono a otros al contrato correctamente',
				'id'		=> $reciboPagoIdOtros,
				'sociosId'	=> $sociosId
			);
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

	/**
	 * Metodo que visualiza el formato a imprimir
	 */
	public function getFormatoAction(){
		$sociosId	= $this->getPostParam('id','int');
		$rcId		= $this->getPostParam('rcId','int');
		$urlAction	= $this->getPostParam('urlAction');
		$this->setParamToView('sociosId',$sociosId);
		$this->setParamToView('rcId',$rcId);
		$this->setParamToView('urlAction',$urlAction);
	}

	/**
	 * Metodo que visualiza en el popup el formulario de pago errado
	 */
	public function getFormErradoAction(){
		$sociosId = $this->getPostParam('id', 'int');
		$this->setParamToView('sociosId', $sociosId);
		$this->setParamToView('recibosPagos', $this->RecibosPagos->find(array('conditions'=>'socios_id='.$sociosId.' AND estado="V"')));
	}

	/**
	 * Metodo que visualiza el recibo de pago
	 */
	public function getReciboPagoAction(){
		$this->setResponse('json');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$rules = array(
				'rcId' => array(
					'message' => 'Debe indicar el id del recibo de caja',
					'filter' => 'int'
				)
			);
			if($this->validateRequired($rules)==false){
				foreach($this->getValidationMessages()  as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			$rcId = $this->getPostParam('rcId','int');
			if(!$rcId){
				$this->addValidationMessage($rules['rcId']['message'],'rcId');
				$transaction->rollback($rules['rcId']['message']);
			}
			$reciboPago = EntityManager::get('RecibosPagos')->findFirst($rcId);
			if($reciboPago == false){
				$transaction->rollback('El id de recibo de caja no existe');
			}
			$controlPago = EntityManager::get('ControlPagos')->findFirst(array('conditions'=>"rc='{$reciboPago->getRc()}'"));

			$sociosId = $reciboPago->getSociosId();
			if(!$sociosId){
				$transaction->rollback('Abono Contrato: El id del contrato no existe');
			}
			$socios = EntityManager::get('Socios')->findFirst($sociosId);
			if($socios == false){
				$transaction->rollback('Abono Contrato: El id de contrato no existe');
			}
			$detalleReciboPagoObj = EntityManager::get('DetalleRecibosPagos')->find(array('conditions'=>'recibos_pagos_id='.$reciboPago->getId()));
			if(count($detalleReciboPagoObj)<=0){
				$transaction->rollback('El detalle del recibo de caja no existe');
			}
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		$empresa = $this->Empresa->findFirst();
		$reportType = $this->getPostParam('reportType', 'alpha');
		$provisional = '-------';
		if($reciboPago->getReciboProvisional()>0){
			$provisional = $reciboPago->getReciboProvisional();
		}
		$html = '
		<html>
			<head>
				<title>Recibo de Cajas #'.sprintf('%1$06d',$reciboPago->getRc()).'</title>
				'.Tag::stylesheetLink('hfos/recibo').'
			</head>
			<body>
				<div align="center">
					<div class="page">
						<!--<table width="95%">
							<tr>
								<td>
									<div class="logo">
										'.Tag::image(array('tpc/logo.png','style'=>'width:225px; height:82px;')).'
									</div>
								</td>
								<td align="right">
									<div class="datos" align="right">
										Proyecto e Inversiones TPC S.A<br/>
										NIT: 900.141.821-1
									</div>
								</td>
							</tr>
						</table>
						<table width="95%">
							<tr>
								<td>
									<div align="left" class="datos-c">
										<b>Datos del Contrato</b><br/>
										'.$socios->getNombres().' '.$socios->getApellidos().'<br>
										Documento: '.$socios->getIdentificacion().'<br/>
										Contrato: '.$socios->getNumeroContrato().'<br/>
										Código: '.$socios->getId().'<br/>
										Número físico provisional: '.$provisional.'<br/>
									</div>
								</td>
								<td  align="right">
									<div align="right" class="datos-r">
										<div class="title">RECIBO DE CAJA</div>
										<table>
											<tr>
												<td align="right">Número</td>
												<td align="right">'.sprintf('%1$06d',$reciboPago->getRc()).'</td>
											</tr>
											<tr>
												<td align="right">Ciudad</td>
												<td align="right">Bogotá</td>
											</tr>
											<tr>
												<td align="right">Fecha pago</td>
												<td align="right">'.$reciboPago->getFechaPago().'</td>
											</tr>
											<tr>
												<td align="right">Fecha Recibo</td>
												<td align="right">'.$reciboPago->getFechaRecibo().'</td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>

						<table width="95%" cellpadding="0" cellspacing="0" class="spacer">
							<tr>
								<td width="20%" bgcolor="#007634"></td>
								<td width="30%" bgcolor="#5BB041"></td>
								<td width="40%" bgcolor="#9BD54D"></td>
							</tr>
						</table>

						<table width="95%">
							<tr>
								<td align="left">
									<b>Activación</b><br/>
									<div align="right">'.Currency::number($reciboPago->getValorCuoact()).'</div>
								</td>
								<td width="25"></td>
								<td align="left">
									<b>Derecho de Afiliación</b><br/>
									<div align="right">'.Currency::number($reciboPago->getValorCuoafi()).'</div>
								</td>
								<td width="25"></td>
								<td align="left">
									<b>Interés por mora</b><br/>
									<div align="right">'.Currency::number($reciboPago->getValorInteresm()).'</div>
								</td>
								<td width="25"></td>
								<td align="left">
									<b>Intereses Corrientes</b><br/>
									<div align="right">'.Currency::number($reciboPago->getValorInteresc()).'</div>
								</td>
							</tr>
							<tr>
								<td style="height:10px"></td>
							</tr>
							<tr>
								<td align="left">
									<b>Capital</b><br/>
									<div align="right">'.Currency::number($reciboPago->getValorCapital()).'</div>
								</td>
								<td width="25"></td>
								<td align="left">
									<b>Valor Cuota Inicial</b><br/>
									<div align="right">'.Currency::number($reciboPago->getValorInicial()).'</div>
								</td>
								<td width="25"></td>
								<td align="left">
									<b>Valor Cuota Financiación</b><br/>
									<div align="right">'.Currency::number($reciboPago->getValorFinanciacion()).'</div>
								</td>
								<td width="25"></td>
								<td align="left">
									<b>Otros</b><br/>
									<div align="right">'.Currency::number($reciboPago->getOtros()).'</div>
								</td>
							</tr>';

						if($controlPago!=false){

							$html .= '
							<tr>
								<td align="left">
									<b>Nuevo Saldo</b><br/>
									<div align="right">'.Currency::number($controlPago->getSaldo()).'</div>
								</td>
							</tr>';
						}
							
						$html .= '		
						</table>

						<table width="95%" cellpadding="0" cellspacing="0" class="spacer-small">
							<tr>
								<td width="20%" bgcolor="#007634"></td>
								<td width="30%" bgcolor="#5BB041"></td>
								<td width="40%" bgcolor="#9BD54D"></td>
							</tr>
						</table>

						<table width="80%">
							<tr>
								<th>Forma de Pago</th>
								<th>Cuenta</th>
								<th>Valor</th>
							</tr>';
						$cuentas = $reciboPago->getCuentas();
						$totalPagos = 0;
						$tiposPagos['E'] = $tiposPagos['C'] = $tiposPagos['TD'] = $tiposPagos['TC'] = 0;
						foreach ($detalleReciboPagoObj as $detalle) {
							$formasPago = $detalle->getFormasPago();
							$html .= '
								<tr>
									<td>'.$formasPago->getNombre().'</td>
									<td>'.$cuentas->getBanco().' '.$cuentas->getCuenta().'</td>
									<td align="right">'.Currency::number($detalle->getValor()).'</td>
								</tr>
							';
							$totalPagos += $detalle->getValor();
							//Sumamos por tipo
							$tiposPagos[$formasPago->getTipo()] += $detalle->getValor();
						}
						$Currency = new Currency();
						$html .='
							<tr>
								<td colspan="2" align="right"><b>TOTAL RECIBO</b></td>
								<td align="right" class="total"><b>'.Currency::number($totalPagos).'</b></td>
							</tr>
						</table>

						<br/>
						<div align="center">
						 	<div class="concepto" align="left">
								<b>Concepto:</b> '.$reciboPago->getObservaciones().'
							</div>
						</div>

						<br/>
						<br/>
						<table width="90%" class="firmas">
							<tr>
								<td>
									<b>Elaborado</b><br/><br/>
									<div align="center">______________________</div>
								</td>
								<td>
									<b>Revisado</b><br/><br/>
									<div align="center">______________________</div>
								</td>
								<td>
									<b>Firma y Sello de quien Recibe</b><br/><br/>
									<div align="center">______________________</div>
								</td>
							</tr>
						</table>-->
						
						<table width="100%" align="left" cellspacing="0" style="border: 1px solid #C0C0C0" class="datos-c">
							<tr>
								<td width="100%" align="center" valign="middle" >
									<table width="100%" cellspacing="0">
										<tr>
											<td align="center" valign="middle" width="200" class="small">
												'.Tag::image(array('tpc/logo.png','style'=>'width:125px; height:52px;')).'
											</td>
											<td align="center" valign="middle" colspan="2" class="small">
												<b>
													<div class="datos" align="center">
														<div class="title">
														RECIBO DE CAJA No. 
														<span style="color:red;">'.sprintf('%1$06d',$reciboPago->getRc()).'</span>
														</div>
														Proyecto e Inversiones TPC S.A<br/>
														NIT: 900.141.821-1
													</div>
												</b>
											</td>
										</tr>
										<tr>
											<td width="20%" bgcolor="#007634"></td>
											<td width="30%" bgcolor="#5BB041"></td>
											<td width="40%" bgcolor="#9BD54D"></td>
										</tr>
									</table>
									<br/>
								</td>
							</tr>
							<tr>
								<td width="100%" align="center" valign="middle" >
									<table width="100%" align="center" cellspacing="0">
										<tr>
											<td valign="top">
												<div class="datos-c">
													<b>Ciudad y Fecha de Pago</b><br>
													BOGOTA , '.$reciboPago->getFechaPago().'
												</div>
											</td>
											<td valign="top">
												<div class="datos-c">
													<b>Ciudad y Fecha de Recibo</b><br>
													BOGOTA , '.$reciboPago->getFechaRecibo().'
												</div>
											</td>
											<td valign="top">
												<b>Recibo Provisional No.</b><br>
												<span class="nofac">'.$provisional.'</span>
											</td>
											<td valign="top" >
												<div class="datos-c">
													<b>Valor Recibo</b><br>
													$&nbsp;<span align="right" class="total">'.Currency::number($totalPagos).'</span>
												</div>
											</td>
										</tr>
									</table>
									<br/>
									<table width="100%" align="center" cellspacing="0">
										<tr>
											<td colspan="4" valign="top" width="100%">
												<div class="concepto">
													<b>La suma de (en letras)</b><br>
													<span align="right" class="total">'.$Currency->getMoneyAsText($totalPagos).'</span>
												</div>
											</td>
										</tr>
									</table>
									<br/>
									<table width="100%" align="center" cellspacing="0" cellpadding="5">
										<tr>
											<td valign="top">
												<div class="datos-c">
													<b>Contrato No.</b><br>
													'.$socios->getNumeroContrato().'
												</div>
											</td>
											<td valign="top">
												<div class="datos-c">
													<b>Documento de Identidad</b>
													<br/>'.$socios->getIdentificacion().'
												</div>
											</td>
											<td valign="top">
												<div class="datos-c">
													<b>Nombres y Apellidos</b>
													<br/>
													'.$socios->getNombres().' '.$socios->getApellidos().'
												</div>
											</td>
											<td valign="top">
												<b>Reserva</b>
												<br/>
												$&nbsp;'.Currency::number($reciboPago->getValorReserva()).'
											</td>
										</tr>
										<tr>
											<td align="left">
												<b>Activación</b><br/>
												$&nbsp;'.Currency::number($reciboPago->getValorCuoact()).'
											</td>
											<td align="left">
												<b>Derecho de Afiliación</b><br/>
												$&nbsp;'.Currency::number($reciboPago->getValorCuoafi()).'
											</td>
											<td align="left">
												<b>Interés por mora</b><br/>
												$&nbsp;'.Currency::number($reciboPago->getValorInteresm()).'
											</td>
											<td align="left">
												<b>Intereses Corrientes</b><br/>
												$&nbsp;'.Currency::number($reciboPago->getValorInteresc()).'
											</td>
										</tr>
										<tr>
											<td valign="top">
												<b>Valor Cuota Inicial</b><br>
												$&nbsp;'.Currency::number($reciboPago->getValorInicial()).'
											</td>
											<td valign="top">
												<b>Valor Financiación</b><br>
												$&nbsp;'.Currency::number($reciboPago->getValorFinanciacion()).'
											</td>
											<td valign="top">
												<b>Capital</b><br>
												$&nbsp;'.Currency::number($reciboPago->getValorCapital()).'
											</td>
											<td valign="top">
												<b>Otros</b><br>
												$&nbsp;'.Currency::number($reciboPago->getOtros()).'
											</td>
										</tr>
										<tr>
											<td valign="top">
												<b>Banco</b><br>
												'.$cuentas->getBanco().' '.$cuentas->getCuenta().'
											</td>
											<td valign="top">
												<b>Efectivo :</b><br>
												$&nbsp;'.Currency::number($tiposPagos['E']).'
											</td>
											<td valign="top" class="small">
												<strong>Cheque</strong>
												<br/>
												$&nbsp;'.Currency::number($tiposPagos['C']).'
											</td>
											<td valign="top" class="small">
												<b>Tarjeta</b><br/>
												$&nbsp;'.Currency::number(($tiposPagos['TC']+$tiposPagos['TD'])).'
											</td>
										</tr>
										';

										if($controlPago!=false){

											$html .= '
											<tr>
												<td align="left" colspan="4">
													<b>Nuevo Saldo</b><br/>
													<div align="left">'.Currency::number($controlPago->getSaldo()).'</div>
												</td>
											</tr>';
										}
											
										$html .= '
										<tr>
											<td valign="top" colspan="4" width="100%">
												<div class="concepto">
													<b>Por Concepto de</b><br/>
													'.$reciboPago->getObservaciones().'
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td width="100%" align="center" valign="middle" >
									<table width="100%" cellspacing="0">
										<tr>
											<td valign="top">
												<p><b>Elaborado</b><br>
												<br>________________________________<br><br>

											</td>
											<td valign="top">
												<p><b>Revisado</b><br>
												<br>________________________________<br><br>
											</td>
											<td valign="top">
												<p><b>Firma y Sello de quien Recibe</b><br>
												<br>________________________________<br><br>
												<b>C.C. o NIT.</b>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>

						<br/>
					</div>
				</div>
			</body>
		</html>
		';

		$dir = 'public/temp/';

		$fileName = 'listaRecibosPagos.html';
		if($reportType=='html'){
			$fileName = 'listaRecibosPagos.html';
			file_put_contents($dir.$fileName,$html);
			$urlFile = 'temp/'.$fileName;
		}else{
			$fileName = 'listaRecibosPagos.pdf';
			Core::importFromLibrary('Mpdf','mpdf.php');
			$mpdf = new mPDF();
			$mpdf->showImageErrors = true;
			$mpdf->WriteHTML($html);
			$mpdf->Output($dir.$fileName);
			$urlFile = 'temp/'.$fileName;
		}
		return array(
			'status'	=> 'OK',
			'file'		=> $urlFile
		);
	}

	/**
	 * Metodo que visualiza el listado de recibos de pagos hechos
	 */
	public function getListaRecibosPagosAction(){
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();
		try {
			$rules = array(
				'sociosId' => array(
					'message'	=> 'Debe indicar el id del contrato',
					'filter'	=> 'int'
				)
			);
			if($this->validateRequired($rules)==false){
				foreach($this->getValidationMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			$sociosId = $this->getPostParam('sociosId', 'int');
			if(!$sociosId){
				$this->addValidationMessage($rules['sociosId']['message'],'sociosId');
				$transaction->rollback($rules['sociosId']['message']);
			}
			$socios = EntityManager::get('Socios')->findFirst($sociosId);
			if($socios==false){
				$transaction->rollback('El id de contrato no existe');
			}
			foreach($this->getValidationMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
		}
		catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		$reportType = $this->getPostParam('reportType', 'alpha');
		$report = ReportBase::factory($reportType);
		$titulo = new ReportText('LISTA DE RECIBOS DE PAGO', array(
			'fontSize' => 16,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));
		$titulo2 = new ReportText('CÉDULA: '.$socios->getIdentificacion().' CONTRATO: '.$socios->getNumeroContrato(),
		array(
			'fontSize' => 13,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));
		$titulo3 = new ReportText('NOMBRES: '.$socios->getNombres().' APELLIDOS: '.$socios->getApellidos(),
		array(
			'fontSize' => 13,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));
		$report->setHeader(array($titulo,$titulo2,$titulo3));
		$report->setDocumentTitle('Tabla de Amortización');
		$report->setColumnHeaders(array(
			'RECIBO DE CAJA',
			'FECHA DE PAGO',
			'CONCEPTO',
			'VALOR PAGADO',
			'ES PAGO POSTERIOR?'
		));
		$report->setCellHeaderStyle(new ReportStyle(array(
			'textAlign' => 'center',
			'backgroundColor' => '#eaeaea'
		)));
		$report->setColumnStyle(array(0,1,2,4), new ReportStyle(array(
			'textAlign' => 'center',
			'fontSize' => 11
		)));
		$report->setColumnStyle(array(3), new ReportStyle(array(
			'textAlign' => 'right',
			'fontSize' => 11,
		)));
		$report->setColumnFormat(array(3), new ReportFormat(array(
			'type' => 'Number',
			'decimals' => 0
		)));
		$report->setTotalizeColumns(array(0, 3));
		$report->start(true);
		$empresa = $this->Empresa->findFirst();
		$totalRc = 0;
		$totalValorPago = 0;
		$recibosPagosObj = EntityManager::get('RecibosPagos')->find(array('conditions'=>'socios_id='.$sociosId));
		foreach($recibosPagosObj as $reciboPago){
			$report->addRow(array(
				$reciboPago->getRc(),
				$reciboPago->getFechapago(),
				$reciboPago->getObservaciones(),
				$reciboPago->getValorPagado(),
				$reciboPago->getPagoPosterior()
			));
			$totalRc += 1;
			$totalValorPago += $reciboPago->getValorPagado();
		}
		$report->setTotalizeValues(array(
			0 => $totalRc,
			3 => $totalValorPago
		));
		$report->finish();
		$fileName = $report->outputToFile('public/temp/listaRecibosPagos');
		return array(
			'status' => 'OK',
			'file' => 'temp/'.$fileName
		);
	}

	/**
	 * Metodo que anula un recibo de caja solo si la fecha de pago es de hoy, recalculando el saldo y dejandolo con un estado A(nulado)
	 */
	public function anularAction(){
		$this->setResponse('json');
		try {
			$transaction = TransactionManager::getUserTransaction();
			$rules = array(
				'rcId' => array(
					'message'	=> 'Debe indicar el id del recibo de caja',
					'filter'	=> 'int'
				)
			);
			if($this->validateRequired($rules)==false){
				foreach($this->getValidationMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			$rcId = $this->getPostParam('rcId', 'int');
			if($rcId<=0){
				$transaction->rollback($rules['rcId']['message']);
			}
			$reciboPago = EntityManager::get('RecibosPagos')->findFirst($rcId);
			if($reciboPago==false){
				$transaction->rollback('El id de recibo de caja no existe');
			}
			if($reciboPago->getFechaRecibo()!=date('Y-m-d')){
				$transaction->rollback('El recibo de caja solo se puede anular si es de hoy');
			}
			//Anular Recibo de caja y recalcula todos los pagos sin el
			$anularConfig = array('reciboPagoId'=>$rcId);
			TPC::anularReciboCaja($anularConfig, $transaction);
			//commit transaction
			$transaction->commit();
			//Retornamos succes
			return array(
				'status'		=> 'OK',
				'message'		=> 'Se anulo correctamente el recibo de caja '.$rcId.', recalculando de nuevo los pagos del contrato y creando historia de movimiento número '.$anularConfig['notaHistoriaId'],
				'notaHistoriaId'=> $anularConfig['notaHistoriaId'],
				'sociosId'		=> $reciboPago->getSociosId(),
				'id'			=> $rcId
			);
		}
		catch(Exception $e){
			$transaction->rollback($e->getMessage());
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}
	
	public function cleanAllPagosAction(){
		$transaction = TransactionManager::getUserTransaction();
		try {
			//Limpiamos todos los pagos de un contrato
			TPC::limpiarAllPagos(39,$transaction);
		}catch(Exception $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		$transaction->commit();
	}

	public function getEstadoCuentaAction(){
		$this->setResponse('view');
		Core::importFromLibrary('Hfos/Tpc','TpcHelper.php');
		$transaction = TransactionManager::getUserTransaction();
		try {
			$rules = array(
				'id' => array(
					'message'	=> 'Debe indicar el id del socio',
					'filter'	=> 'int'
				),
				'fechaPago' => array(
					'message'	=> 'Debe indicar la fecha de corte a calcular',
					'filter'	=> 'date'
				)
			);
			if($this->validateRequired($rules)==false){
				foreach($this->getValidationMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			$sociosId = $this->getPostParam('id', 'int');
			if($sociosId<=0){
				$transaction->rollback($rules['sociosId']['message']);
			}
			$socios = EntityManager::get('Socios')->findFirst($sociosId);
			if($socios==false){
				$transaction->rollback('El id de socio no existe');
			}

			$fechaPago = $this->getPostParam('fechaPago', 'date');
			if(!$fechaPago){
				$transaction->rollback('La fecha a calcular es necesaria');	
			}

			$config = array(
				'sociosId' 	=> $sociosId,
				'fecha' 	=> $fechaPago
			);
			echo TpcHelper::estadoCuenta($config);

			//$transaction->rollback(print_r($config,true));

			//commit transaction
			$transaction->commit();
		}
		catch(Exception $e){
			Flash::error($e->getMessage());
		}
	}
	
}
