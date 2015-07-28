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
 * Abono_ReservaController
 *
 * Generación de abonos a reservas
 */
class Abono_ReservaController extends ApplicationController{

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	/**
	 * Carga cosas en la primera pantalla
	 */
	public function indexAction(){
		$this->setParamToView('message', 'Ingrese un criterio de búsqueda para consultar la reserva');
	}

	/**
	 * Consulta las reservas por aprametros de busqueda
	 */
	public function buscarAction(){
		$this->setResponse('json');
		$response 		= array();
		$conditions 	= array();
		$numeroContrato	= $this->getPostParam('numeroContrato');
		$identificacion	= $this->getPostParam('identificacion', 'int');
		$nombres		= $this->getPostParam('nombres', 'alpha', 'striptags', 'extraspaces');
		$apellidos		= $this->getPostParam('apellidos', 'alpha', 'striptags', 'extraspaces');
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
			$reservas = $this->Reservas->find(array(implode(' AND ', $conditions), 'order' => 'numero_contrato DESC'));
		}else{
			$reservas = $this->Reservas->find(array('order' => 'numero_contrato DESC'));
		}
		if(count($reservas)==0){
			$response['number'] = '0';
		}else{
			if(count($reservas)==1){
				$reserva = $reservas->getFirst();
				$response['number'] = '1';
				$response['key'] = 'id='.$reserva->getId();
			}else{
				$responseResults = array(
					'headers' => array(
						array('name' => 'Número de Reserva', 'ordered' => 'S'),
						array('name' => 'Cédula', 'ordered' => 'N'),
						array('name' => 'Nombres', 'ordered' => 'N'),
						array('name' => 'Apellidos', 'ordered' => 'N'),
						array('name' => 'Estado Contrato', 'ordered' => 'N'),
						array('name' => 'Estado Movimiento', 'ordered' => 'N')
					)
				);
				$data = array();
				$estadoContratoObj		= EntityManager::get('EstadoContrato')->find();
				$estadoReservasObj	= EntityManager::get('EstadoReservas')->find();
				$estadoContratoArray	= array();
				$estadoReservasArray	= array();
				foreach($estadoContratoObj as $estadoContrato){
					$estadoContratoArray[$estadoContrato->getCodigo()] = $estadoContrato->getNombre();
				}
				foreach($estadoReservasObj as $estadoReservas){
					$estadoReservasArray[$estadoReservas->getCodigo()] = $estadoReservas->getNombre();
				}
				foreach($reservas as $reserva){
					$estadoContratoLabel = '???';
					if(isset($estadoContratoArray[$reserva->getEstadoContrato()])){
						$estadoContratoLabel = $estadoContratoArray[$reserva->getEstadoContrato()];
					}
					$estadoReservasLabel = '???';
					if(isset($estadoReservasArray[$reserva->getEstadoMovimiento()])){
						$estadoReservasLabel = $estadoReservasArray[$reserva->getEstadoMovimiento()];
					}
					$data[] = array(
						'primary' => array('id='.$reserva->getId()),
						'data' => array(
							array('key' => 'numeroContrato', 'value' => $reserva->getNumeroContrato()),
							array('key' => 'identificacion', 'value' => $reserva->getIdentificacion()),
							array('key' => 'nombres', 'value' => $reserva->getNombres()),
							array('key' => 'apellidos', 'value' => $reserva->getApellidos()),
							array('key' => 'estado_contrato', 'value' => $estadoContratoLabel),
							array('key' => 'estado_movimiento', 'value' => $estadoReservasLabel),
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
	 */
	public function verAction(){
		$reservasId = $this->getPostParam('id', 'int');
		if($reservasId>0){
			$reserva = $this->Reservas->findFirst($reservasId);
			if($reserva==false){
				Flash::error('No existe la reserva');
				$this->routeToAction('errores');
			}
			if($reserva->getEstadoContrato()=='AA'){
				$this->setParamToView('message', 'La reserva está anulada');
			}
			$new = $this->getPostParam('new', 'int');
			if($new==true){
				Flash::success('Se creó el abono correctamente');
			}
			$this->setParamToView('reserva', $reserva);
		}else{
			Flash::error('No existe la reserva');
			$this->routeToAction('errores');
		}
	}

	public function erroresAction(){}

	/**
	 * Metodo que abre la vista de abono de contrato
	 *
	 */
	public function nuevoAction(){
		$reservasId = $this->getPostParam('id', 'int');
		if($reservasId>0){
			$this->setParamToView('message', 'Ingrese los datos del abono y haga click en "Grabar"');
			$cuentasArray = array();
			$this->setParamToView('reservasId', $reservasId);
			$this->setParamToView('cuentas', $this->Cuentas->find(array('conditions'=>'estado="A"', 'order'=>'banco ASC')));
			$this->setParamToView('formasPago', $this->FormasPago->find(array("estado='A'")));
			Tag::displayTo('fechaPago', (string) Date::getCurrentDate());
			Tag::displayTo('fechaRecibo', (string) Date::getCurrentDate());
		}else{
			Flash::error('nuevoAction: Ingrese el id de reserva');
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
		try{
			$transaction = TransactionManager::getUserTransaction();
			$reservasId = $this->getPostParam('reservasId', 'int');
			if($reservasId<0){
				$transaction->rollback('Abono: La reserva no es valida');
			}
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
			//$transaction->rollback(print_r($formasPagos,true).'<br>'.print_r($dataF,true));
			//Agregamos el pago
			$data = array(
				'reservasId'		=>  $reservasId,
				'fechaRecibo'		=>  $fechaRecibo,
				'fechaPago'			=>  $fechaPago,
				'formasPago'		=>  $formasPagos,
				'cuentasId'			=>  $cuentasId,
				'reciboProvisional'	=>  $reciboProvisional,
				'ciudadPago'		=>  $ciudadPago,
				'detallePago'		=>  'Abono a Reserva',
				'debug'				=>  true
			);
			TPC::addAbonoReserva($data, $transaction);
			if(isset($data['abonoReservaId'])){
				$transaction->commit();
				return array(
					'status'	=> 'OK',
					'message'	=> 'Se generó el abono a la reserva correctamente',
					'id'		=> $data['reciboPagoId'],
					'reservasId'=> $reservasId
				);
			}else{
				return array(
					'status'	=> 'FAILED',
					'message'	=> 'Abono Reserva Not Id: '.print_r($data,true)
				);
			}
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => 'Abono Reservas: '.$e->getMessage()
			);
		}
	}
	
	public function getFormatoAction(){
		$reservasId = $this->getPostParam('id','int');
		$rcId = $this->getPostParam('rcId','int');
		$urlAction = $this->getPostParam('urlAction');
		$this->setParamToView('reservasId',$reservasId);
		$this->setParamToView('rcId',$rcId);
		$this->setParamToView('urlAction',$urlAction);
	}

	/**
	 * Metodo que genera recibo de caja para reservas
	 */
	public function getReciboPagoAction(){
		$this->setResponse('json');
		try{
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
			$abonoReservas = EntityManager::get('AbonoReservas')->findFirst($reciboPago->getAbonoReservasId());
			$reservasId = $abonoReservas->getReservasId();
			if(!$reservasId){
				$transaction->rollback('getReciboPago: La reserva id es requerida');
			}
			$reservas = EntityManager::get('Reservas')->findFirst($reservasId);
			if($reservas == false){
				$transaction->rollback('getReciboPago: La reserva no existe');
			}
			$detalleReciboPagoObj = EntityManager::get('DetalleRecibosPagos')->find(array('conditions'=>'recibos_pagos_id='.$reciboPago->getId()));
			if(count($detalleReciboPagoObj)<=0){
				$transaction->rollback('El detalle del recibo de caja no existe');
			}
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		$empresa = $this->Empresa->findFirst();
		$provisional = '-------';
		if($reciboPago->getReciboProvisional()>0){
			$provisional = $reciboPago->getReciboProvisional();
		}
		$reportType = $this->getPostParam('reportType', 'alpha');
		
		$html = '
		<html>
			<head>
				<title>Recibo de Caja #'.sprintf('%1$06d',$reciboPago->getRc()).'</title>
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
										'.$reservas->getNombres().' '.$reservas->getApellidos().'<br>
										Documento: '.$reservas->getIdentificacion().'<br/>
										Contrato: '.$reservas->getNumeroContrato().'<br/>
										Código: '.$reservas->getId().'<br/>
										Provisional: '.$provisional.'<br/>
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
							</tr>
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
						foreach($detalleReciboPagoObj as $detalle){
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
													'.$reservas->getNumeroContrato().'
												</div>
											</td>
											<td valign="top">
												<div class="datos-c">
													<b>Documento de Identidad</b>
													<br/>'.$reservas->getIdentificacion().'
												</div>
											</td>
											<td valign="top">
												<div class="datos-c">
													<b>Nombres y Apellidos</b>
													<br/>
													'.$reservas->getNombres().' '.$reservas->getApellidos().'
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
		  'status' => 'OK',
		  'file' => $urlFile
		);

		
	}
	
	public function getListaRecibosPagosAction(){
		$this->setResponse('json');
		$transaction = TransactionManager::getUserTransaction();
		try{
			$rules = array(
				'reservasId' 	=> array(
					'message' 	=> 'Debe indicar el id de al reserva',
					'filter' 	=> 'int'
				)
			);
			if($this->validateRequired($rules)==false){
				foreach($this->getValidationMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
			$reservasId = $this->getPostParam('reservasId', 'int');
			if(!$reservasId){
				$this->addValidationMessage($rules['reservasId']['message'],'reservasId');
				$transaction->rollback($rules['reservasId']['message']);
			}
			$reservas = EntityManager::get('Reservas')->findFirst($reservasId);
			if(!$reservas){
				$transaction->rollback('El id de la reserva no existe');
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

		$titulo2 = new ReportText('CÉDULA: '.$reservas->getIdentificacion().' CONTRATO: '.$reservas->getNumeroContrato(),
		array(
			'fontSize' => 13,
			'fontWeight' => 'bold',
			'textAlign' => 'center'
		));
		$titulo3 = new ReportText('NOMBRES: '.$reservas->getNombres().' APELLIDOS: '.$reservas->getApellidos(),
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
		//Recolectamos los id de los abonos a nombre de esa reserva
		$abonoReservasObj = EntityManager::get('AbonoReservas')->find(array('conditions' => 'reservas_id='.$reservasId));
		$idAbonosReservasArray = array();
		foreach($abonoReservasObj as $abonoReserva){
			$idAbonosReservasArray[]=$abonoReserva->getId();
		}
		if(count($idAbonosReservasArray)>0){
			$conditions = ' abono_reservas_id IN('.implode(', ', $idAbonosReservasArray).')';
		}else{
			$conditions = '1=0';//NO debe salir nada
		}
		$recibosPagosObj = EntityManager::get('RecibosPagos')->find(array('conditions'=>$conditions));
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
		$transaction = TransactionManager::getUserTransaction();
		try {
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
			if($reciboPago->getFechaPago()!=date('Y-m-d')){
				$transaction->rollback('El recibo de caja solo se puede anular si es de hoy');
			}
			//Anular Recibo de caja y recalcula todos los pagos sin el
			$anularConfig = array('reciboPagoId'=>$rcId);
			TPC::anularReciboCaja($anularConfig, $transaction);
			//commit transaction
			$transaction->commit();
			//Reservas 
			$reservasId = 0;
			if($reciboPago->getAbonoReservasId()>0){
				$abonoReservasId = $reciboPago->getAbonoReservasId();
				$reservasId = $this->AbonoReservas->findFirst($abonoReservasId)->getReservasId();
			}
			//Retornamos success
			return array(
				'status'		=> 'OK',
				'message'		=> 'Se anulo correctamente el recibo de caja '.$rcId.', recalculando de nuevo los pagos del contrato y creando historia de movimiento número '.$anularConfig['notaHistoriaId'],
				'notaHistoriaId'=> $anularConfig['notaHistoriaId'],
				'reservasId'	=> $reservasId,
				'id'			=> $rcId
			);
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}
}
