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
 * TPC
 *
 * Clase componente que controla procesos internos de tpc con respecto a los contratos
 *
 */
class TpcContratos extends UserComponent {
	
	
	/**
	 * Metodo que cambia el contrato
	 *
	 * @param	TransactionManager $transaction
	 * @param	ActiveRecordBase $record
	 * @param	integer $nuevoContratoId
	 */
	static function _antesDeCambiarContrato($transaction, $record, $nuevoTipoContratoId){
		if(!$nuevoTipoContratoId){
			$transaction->rollback('El id del nuevo contrato es necesario');
		}
		$tipoContrato = self::getModel('TipoContrato')->findFirst($nuevoTipoContratoId);
		if($tipoContrato==false){
			$transaction->rollback('El id del nuevo contrato no existe');
		}
		//Copiamos contrato a uno nuevo
		$sociosNew = TPC::copiarANuevoContrato(array(
			'sociosId'			=> $record->getId(),
			'tipoContratoId'	=> $nuevoTipoContratoId
		), $transaction);
		$sociosNew->setCambioContrato('S');
		//Cuota incial cambio contrato
		$config = array(
			'SociosId' => $record->getId()
		);
		TPC::getCuotaInicialCambioContrato($config, $transaction);
		$nuevaCuotaInicial = $config['nuevaCuotaInicial'];
		$derechoAfiliacion = $config['valorDerechoAfiliacion'];
		$sociosNew->setValorCambioContrato($nuevaCuotaInicial+$derechoAfiliacion);
		
		//Copiamos a historia los datos
		$configHistoria = array(
			'sociosId'			=> $record->getId(),
			'notaHistoria'  	=> array(
				'estado'		=> 'C', //Cambio de contrato
				'fecha'			=> date('Y-m-d'),
				'observaciones' => 'Se realizó cambio de contrato de '.$record->getTipoContrato()->getNombre().'('.$record->getNumeroContrato().') a '.$tipoContrato->getNombre().'('.$sociosNew->getNumeroContrato().').',
				'copiarContrato'=> true //copia el contenido del contrato en sus repectivas tabla h(historia)
			),
			'debug'				=> true
		);
		TPC::copiarAHistoria($configHistoria, $transaction);
		//$transaction->rollback(print_r($configHistoria,true));
		$record = $sociosNew;
		return $sociosNew;
	}

	/**
	 * Metodo privado que actualiza los datos de membresias socios de un contrato
	 *
	 * @param	$config array(
	 * 	'transaction'	=> $transaction,
	 * 	'record'		=> $record,
	 * 	'configCambio'	=> $configCambio
	 * );
	 */
	static function _saveMembresiasSocios($transaction, &$config){
		$record 			= $config['Socios'];
		if($record==false){
			$transaction->rollback('No esta presente el Active record de socios');
		}
		//Solo si es cambio de contrato calcula lo pagado
		if(isset($config['configCambio'])){
			$configCambio	= $config['configCambio'];
		}
		$membresiasSocios	= self::getModel('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions' => 'socios_id='.$record->getId()));
		if($membresiasSocios==false || (isset($config['existeNuevoContrato'])==true && $config['existeNuevoContrato']==true)){
			$membresiasSocios = new MembresiasSocios();
			//Agregamos tranasacción actual
			$membresiasSocios->setTransaction($transaction);
		}
		//Adicionamos datos
		if(isset($config['existeNuevoContrato'])==true && $config['existeNuevoContrato']==true){
			$membresiasSocios->setSociosId($config['sociosIdNew']);
		}else{
			$membresiasSocios->setSociosId($record->getId());
		}
		$membresiasSocios->setMembresiasId($config['membresias_id']);
		$membresiasSocios->setTemporadasId($config['temporadas_id']);
		$membresiasSocios->setCapacidad($config['capacidad']);
		$membresiasSocios->setPuntosAno($config['puntos_ano']);
		$membresiasSocios->setNumeroAnos($config['numero_anos']);
		$membresiasSocios->setTotalPuntos($membresiasSocios->getPuntosAno()*$membresiasSocios->getNumeroAnos());
		$valorTotal = LocaleMath::round($config['valor_total'],0);
		$membresiasSocios->setValorTotal($valorTotal);
		$valorCuotaInicial = LocaleMath::round($config['cuota_inicial'],0);
		$membresiasSocios->setCuotaInicial($valorCuotaInicial);
		$membresiasSocios->setSaldoPagar($membresiasSocios->getValorTotal() - $membresiasSocios->getCuotaInicial());
		$membresiasSocios->setDerechoAfiliacionId($config['derecho_afiliacion_id']);
		$porce33 = ($membresiasSocios->getValorTotal() * 0.33);
		$cuotaInicial = $membresiasSocios->getCuotaInicial();
		if(abs($cuotaInicial - $porce33)<0){
			$transaction->rollback('La cuota inicial debe ser mayor o igual a '.$porce33.'---'." (abs($cuotaInicial - $porce33)<0) : ".(abs($cuotaInicial - $porce33)<0));
		}
		//Si es cambio de contrato
		if(isset($config['configCambio'])==true && isset($configCambio['totalContrato'])==true && $configCambio['totalContrato']>0){
			//$transaction->rollback(1);
			$valorDerechoAfiliacion = $membresiasSocios->getDerechoAfiliacion()->getValor();
			if($configCambio['totalContrato'] > $valorDerechoAfiliacion){
				$configCambio['totalContrato'] -= $valorDerechoAfiliacion;
				$membresiasSocios->setAfiliacionPagado($valorDerechoAfiliacion);
				$membresiasSocios->setEstadoCuoafi('P');
				//$transaction->rollback(2);
			}else{
				$membresiasSocios->setAfiliacionPagado($configCambio['totalContrato']);
				$membresiasSocios->setEstadoCuoafi('D');
				//$transaction->rollback(3);
			}
		}else{
			$membresiasSocios->setAfiliacionPagado(0);
			$membresiasSocios->setEstadoCuoafi('D');
			//$transaction->rollback(4);
		}
		if($membresiasSocios->save()==false){
			foreach($membresiasSocios->getMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
		}
		$config['configCambio'] = $configCambio;
		//$transaction->rollback($configCambio['totalContrato'].', '.$valorDerechoAfiliacion.', estadoAFI: '.$membresiasSocios->getEstadoCuoafi().', pagadoAFI: '.$membresiasSocios->getAfiliacionPagado());
	}

	/**
	 * Metodo privado que actualiza los datos de detalle de cuotas iniciales de un contrato
	 *
	 * @param	$config array(
	 * 	'transaction'	=> $transaction,
	 * 	'record'		=> $record,
	 * 	'configCambio'	=> $configCambio
	 * );
	 */
	static function _saveDetalleCuota($transaction, &$config){
		$record 			= $config['Socios'];
		//Solo si es cambio de contrato calcula lo pagado
		if(isset($config['configCambio'])){
			$configCambio	= $config['configCambio'];
		}
		$detalleCuota = self::getModel('DetalleCuota')->setTransaction($transaction)->findFirst(array('conditions' => 'socios_id='.$record->getId()));
		if($detalleCuota==false || (isset($config['existeNuevoContrato'])==true && $config['existeNuevoContrato']==true)){
			$detalleCuota = new DetalleCuota();
		}
		//Agregamos tranasacción actual
		$detalleCuota->setTransaction($transaction);
		//Adicionamos datos
		if(isset($config['existeNuevoContrato'])==true && $config['existeNuevoContrato']==true){
			$detalleCuota->setSociosId($config['sociosIdNew']);
		}else{
			$detalleCuota->setSociosId($record->getId());
		}
		$valorCuotaInicial1 = LocaleMath::round($config['hoy'],0);
		$valorCuotaInicial2 = LocaleMath::round($config['cuota2'],0);
		$valorCuotaInicial3 = LocaleMath::round($config['cuota3'],0);
		$detalleCuota->setHoy($valorCuotaInicial1);
		$detalleCuota->setFecha1($config['fecha1']);
		$detalleCuota->setCuota2($valorCuotaInicial2);
		$detalleCuota->setFecha2($config['fecha2']);
		$detalleCuota->setCuota3($valorCuotaInicial3);
		$detalleCuota->setFecha3($config['fecha3']);
		if($config['hoy']>0){
			$detalleCuota->setEstado1('D');//Debe
		}else{
			$detalleCuota->setEstado1('P');//Pagado
		}
		if($config['cuota2']>0){
			$detalleCuota->setEstado2('D');//Debe
		}else{
			$detalleCuota->setEstado2('P');//Pagado
		}
		if($config['cuota3']>0){
			$detalleCuota->setEstado3('D');//Debe
		}else{
			$detalleCuota->setEstado3('P');//Pagado
		}
		//si es cambio de contrato
		if(isset($config['configCambio'])){
			$configCambio = $config['configCambio'];
			$totalContrato = $configCambio['totalContrato'];
			//Aplicando a cuota 1
			if($totalContrato>=$detalleCuota->getHoy()){
				$detalleCuota->setHoyPagado($detalleCuota->getHoy());
				$detalleCuota->setEstado1('P');//Pagado
				$totalContrato -= $detalleCuota->getHoy();
				//$transaction->rollback($totalContrato.'>='.$detalleCuota->getHoy().'/ '.$detalleCuota->getEstado1());
				//Aplicando a cuota 2
				if($totalContrato>=$detalleCuota->getCuota2()){
					$detalleCuota->setCuota2Pagado($detalleCuota->getCuota2());
					$detalleCuota->setEstado2('P');//Pagado
					$totalContrato -= $detalleCuota->getCuota2();
					//Aplicando a cuota 3
					if($totalContrato>=$detalleCuota->getCuota3()){
						$detalleCuota->setCuota3Pagado($detalleCuota->getCuota3());
						$detalleCuota->setEstado3('P');//Pagado
						$totalContrato -= $detalleCuota->getCuota3();
						//miramos si hay saldo a favor y hay lo metemos a saldo a favor
						if($totalContrato>0){
							$saldoAfavor = EntityManager::get('SaldoAfavor')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$configCambio['sociosIdNew']));
							if($saldoAfavor==false){
								$saldoAfavor = new SaldoAfavor();
								$saldoAfavor->setTransaction($transaction);
								$saldoAfavor->setSociosId($configCambio['sociosIdNew']);
							}
							$saldoAfavor->setValor($saldoAfavor->getValor() + $totalContrato);
							if($saldoAfavor->save()==false){
								foreach($saldoAfavor->getMessages() as $message){
									$transaction->rollback($message->getMessage());
								}
							}
							$totalContrato = 0;
						}
					}else{
						$detalleCuota->setCuota3Pagado($totalContrato);
					}
				}else{
					$detalleCuota->setCuota2Pagado($totalContrato);
				}
			}else{
				//$transaction->rollback($detalleCuota->getEstado1().', '.$detalleCuota->getEstado2().', '.$detalleCuota->getEstado3().'-<<<--'.$detalleCuota->getHoyPagado().', '.$detalleCuota->getCuota2Pagado().', totalContrato: '.$totalContrato);
				if($totalContrato>0){
					$detalleCuota->setHoyPagado($totalContrato);
				}
			}
			//$transaction->rollback($detalleCuota->getEstado1().', '.$detalleCuota->getEstado2().', '.$detalleCuota->getEstado3().'---'.$detalleCuota->getHoyPagado());
		}
		if($detalleCuota->save()==false){
			foreach($detalleCuota->getMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
		}
		//$transaction->rollback($detalleCuota->getEstado1().', '.$detalleCuota->getEstado2().', '.$detalleCuota->getEstado3().'--aa-'.$detalleCuota->getHoyPagado());
		$configCambio['totalContrato'] = $totalContrato;
		$config['configCambio'] = $configCambio;
		//$transaction->rollback(print_r($configCambio,true));
	}

	/**
	 * Metodo privado que crea amortizacion a partir de ActiveRecord de Contrato
	 *
	 * @param	ActiveRecordTransaction $transaction
	 * @param	ActiveRecord $record
	 */
	static function _makeAmortizacion($transaction, $record){
		//generamos amortizacion al contrato
		$status = TPC::remplazarAmortizacion($record, false, $transaction);
		if($status==false){
			$transaction->rollback('No se puedo crear la amortización');
		}
		return $status;
	}

	public function makeAmortizacion($transaction, $record){
		//generamos amortizacion al contrato
		$status = TPC::remplazarAmortizacion($record, false, $transaction);
		if($status==false){
			$transaction->rollback('No se puedo crear la amortización');
		}
		return $status;
	}

	/**
	 * Metodo que convierte una fecha normal a fecha amortizacion
	 */
	static function _fechaFinanciera($fecha){
		$fechaNew = '';
		if(empty($fecha)==false){
			list($year,$month,$day) = explode('-',$fecha);
			if($day<15){
				$day = 15;
			}
			if($day>15 && $day < 32){
				$day = 30;
			}
			$fechaNew = $year.'-'.$month.'-'.$day;
		}
		return $fechaNew;
	}

	/**
	 * Metodo privado que actualiza los datos de pago de saldo de un contrato
	 *
	 * @param	ActiveRecordTransaction $transaction
	 * @param	Array	$config
	 */
	static function _savePagoSaldo($transaction, &$config){
		$record		= $config['Socios'];
		$pagoSaldo	= self::getModel('PagoSaldo')->setTransaction($transaction)->findFirst(array('conditions' => 'socios_id='.$record->getId()));
		if($pagoSaldo==false || (isset($config['existeNuevoContrato'])==true && $config['existeNuevoContrato']==true)){
			$pagoSaldo = new PagoSaldo();
		}
		//Agregamos transacción actual
		$pagoSaldo->setTransaction($transaction);
		//Adicionamos datos
		if(isset($config['existeNuevoContrato'])==true && $config['existeNuevoContrato']==true){
			$pagoSaldo->setSociosId($config['sociosIdNew']);
		}else{
			$pagoSaldo->setSociosId($record->getId());
		}
		$pagoSaldo->setNumeroCuotas($config['numero_cuotas']);
		$pagoSaldo->setInteres($config['interes']);

		//nuevo campo mora
		if($pagoSaldo->getMora()<=0){
			$mora = TPC::getTasaDeMora($record->getFechaCompra(), null, $transaction);
			$pagoSaldo->setMora($mora);	
		}
		
		//areglamos las fecha pra amortizacion; dias solo 15 o 30 de cuota
		$fechaPP = (string) self::_fechaFinanciera($config['fecha_primera_cuota']);
		$pagoSaldo->setFechaPrimeraCuota($fechaPP);
		$pagoSaldo->setPremiosId($config['premios_id']);
		$pagoSaldo->setObservaciones($config['observaciones']);
		if($pagoSaldo->save()==false){
			foreach($pagoSaldo->getMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
			return false;
		}else{
			//Si hay numero de cuotas > 0 se genera amortizacion
			if($pagoSaldo->getNumeroCuotas()>0){
				//Generamos amortizacion de socios
				self::_makeAmortizacion($transaction, $record);
			}
		}
		//$transaction->rollback('le numero='.$controllerRequest->getParamRequest('numero_cuotas', 'double'));
		//$transaction->rollback('le id='.$record->getId());
	}

	/**
	 * Metodo que crea y actualiza el conyugue
	 *
	 * @param	ActiveRecordTransaction $transaction
	 * @param	Array $config
	 * @param	boolean $existeNuevoContrato
	 */
	static function _saveConyuge($transaction, &$config){
		$record					= $config['record'];
		$existeNuevoContrato	= $config['existeNuevoContrato'];
		$conyuge				= self::getModel('Conyuges')->setTransaction($transaction)->findFirst(array('conditions' => 'socios_id='.$record->getId()));
		if($conyuge==false || (isset($config['existeNuevoContrato'])==true && $config['existeNuevoContrato']==true)){
			$conyuge = new Conyuges();
		}
		//Agregamos tranasacción actual
		$conyuge->setTransaction($transaction);
		//Si no usa validacion
		if($existeNuevoContrato==true){
			$conyuge->setValidar(false);
		}
		//Adicionamos datos
		if(isset($config['existeNuevoContrato'])==true && $config['existeNuevoContrato']==true){
			$conyuge->setSociosId($config['sociosIdNew']);
		}else{
			$conyuge->setSociosId($record->getId());
		}
		$conyuge->setTipoDocumentosId($config['conyuge_tipo_documentos_id']);
		$conyuge->setIdentificacion($config['conyuge_identificacion']);
		$conyuge->setNombres($config['conyuge_nombres']);
		$conyuge->setApellidos($config['conyuge_apellidos']);
		$conyuge->setFechaNacimiento($config['conyuge_fecha_nacimiento']);
		$conyuge->setDireccion($config['conyuge_direccion']);
		$conyuge->setTelefono($config['conyuge_telefono']);
		$conyuge->setCelular($config['conyuge_celular']);
		$conyuge->setProfesionesId($config['conyuge_profesiones_id']);
		$conyuge->setEstadosCivilesId($config['conyuge_estados_civiles_id']);
		if($conyuge->save()==false){
			foreach($conyuge->getMessages() as $message){
				$transaction->rollback('Segundo Titular: '.$message->getMessage());
			}
		}
	}

	/**
	 * Metodo que recalcula los abono de reserva si existen a un pago de contrato
	 * para que calcule bien segun la financiacion y cuotas iniciales
	 * @param Activerecord				$socios
	 * @param integer 					$reservasId
	 * @param ActiverecordTransaction	$transaction
	 */
	static function _abonarPagosActivarReserva($socios, $reservasId, $transaction){
		//Generamos formato de datos para recalcular de los recibos de caja existentes a un id de socios
		$formatos = TPC::crearFormatoDePagoDeRcs(array('reservasId'=>$reservasId), $transaction);
		//$transaction->rollback($reservasId.', formatos: '.print_r($formatos,true));
		//Insertamos el pago nuevo al nuevo contrato
		foreach($formatos as $formato){
			$formato['sociosId'] = $socios->getId();//asignamos nuevo id de socio
			$formato['setValidar'] = false;//Decimos que no valide nada en recibos pagos
			//$transaction->rollback($socios->getId().', '.$reservasId.', formatos: '.print_r($formato,true));
			TPC::addAbonoContrato($formato, $transaction);
		}
		$reservas = self::getModel('Reservas')->setTransaction($transaction)->findFirst($reservasId);
		if($reservas==false){
			$transaction->rollback('_abonarPagosActivarReserva: La reserva no existe');
		}
		$reservas->setValidar(false);
		$reservas->setEstadoContrato('AA');//Anulado
		$reservas->setEstadoMovimiento('RC');//Reserva con contrato
		$reservas->setSociosId($socios->getId());
		if($reservas->save()==false){
			foreach($reservas->getMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
		}
		$reservas->setValidar(true);
		//$transaction->rollback($reservasId.', formatos: '.print_r($formatos,true));
	}

	/**
	 * Metodo que actualiza la demas información de un socios
	 *
	 * @param	ActiveRecordTransaction $transaction
	 * @param	Array $config = array(
	 *		'Socios'				=> $record,
	 *		//Si es activar reserva
	 *		'reservas_contrato'		=> $controllerRequest->getParamRequest('reservas_contrato'),
	 *		//Si es cambio de contrato
	 *		'nuevo_contrato'		=> $controllerRequest->getParamRequest('nuevo_contrato', 'int'),
	 *		//Membresias
	 *		'membresias_id'			=> $controllerRequest->getParamRequest('membresias_id', 'int'),
	 *		'temporadas_id'			=> $controllerRequest->getParamRequest('membresias_id', 'int'),
	 *		'capacidad'				=> $controllerRequest->getParamRequest('capacidad', 'int'),
	 *		'puntos_ano'			=> $controllerRequest->getParamRequest('puntos_ano', 'int'),
	 *		'numero_anos'			=> $controllerRequest->getParamRequest('numero_anos', 'int'),
	 *		'valor_total'			=> $controllerRequest->getParamRequest('valor_total', 'double'),
	 *		'cuota_inicial'			=> $controllerRequest->getParamRequest('cuota_inicial', 'double'),
	 *		'derecho_afiliacion_id'	=> $controllerRequest->getParamRequest('derecho_afiliacion_id', 'int'),
	 *		//Detalle cuota
	 *		'hoy'					=> $controllerRequest->getParamRequest('hoy', 'double'),
	 *		'fecha1'				=> $controllerRequest->getParamRequest('fecha1', 'date'),
	 *		'cuota2'				=> $controllerRequest->getParamRequest('cuota2', 'double'),
	 *		'fecha2'				=> $controllerRequest->getParamRequest('fecha2', 'date'),
	 *		'cuota3'				=> $controllerRequest->getParamRequest('cuota3', 'double'),
	 *		'fecha3'				=> $controllerRequest->getParamRequest('fecha3', 'date'),
	 *		//PagoSaldo
	 *		'numero_cuotas'			=> $controllerRequest->getParamRequest('numero_cuotas', 'double'),
	 *		'interes'				=> $controllerRequest->getParamRequest('interes', 'double'),
	 *		'fecha_primera_cuota'	=> $controllerRequest->getParamRequest('fecha_primera_cuota', 'date'),
	 *		'premios_id'			=> $controllerRequest->getParamRequest('premios_id', 'int'),
	 *		'observaciones'			=> $controllerRequest->getParamRequest('observaciones', 'striptags'),
	 *		//Conyuges
	 *		'conyuge_tipo_documentos_id'=> $controllerRequest->getParamRequest('conyuge_tipo_documentos_id', 'int'),
	 *		'conyuge_identificacion'=> $controllerRequest->getParamRequest('conyuge_identificacion', 'int'),
	 *		'conyuge_nombres'		=> $controllerRequest->getParamRequest('conyuge_nombres', 'alpha'),
	 *		'conyuge_apellidos'		=> $controllerRequest->getParamRequest('conyuge_apellidos', 'alpha'),
	 *		'conyuge_fecha_nacimiento'=> $controllerRequest->getParamRequest('conyuge_fecha_nacimiento', 'date'),
	 *		'conyuge_direccion'		=> $controllerRequest->getParamRequest('conyuge_direccion', 'alpha'),
	 *		'conyuge_telefono'		=> $controllerRequest->getParamRequest('conyuge_telefono', 'int'),
	 *		'conyuge_celular'		=> $controllerRequest->getParamRequest('conyuge_celular', 'int'),
	 *		'conyuge_profesiones_id'=> $controllerRequest->getParamRequest('conyuge_profesiones_id', 'int'),
	 *		'conyuge_estados_civiles_id'=> $controllerRequest->getParamRequest('conyuge_estados_civiles_id', 'int'),
	 *	);
	 */
	static function actualizarSocio($transaction, &$config){
		//Validamos campos
		$listRequired = array(
			array('field'=>'Socios', 'message'=>'El ActiveRecord de Socios es necesario'),
		);
		//Socios
		$record = $config['Socios'];
		$sociosId = $record->getId();
		//Si es una activación de reserva
		$existeActivarReserva = false;
		if(isset($config['reservas_contrato'])==true && empty($config['reservas_contrato'])==false){
			$existeActivarReserva = true;
		}
		//Validamos si puede editar
		$canIEdit = true;
		$existeNuevoContrato = false; 
		if($sociosId>0){
			//Si es cambio de contrato
			if(isset($config['nuevo_contrato'])==true && empty($config['nuevo_contrato'])==false){
				$existeNuevoContrato = true;
			}
			//Si existe campo nuevo_contrato es un cambio de contrato
			if($existeNuevoContrato==false){
				//verificamos si hay pagos
				$existenRecibosPagos = self::getModel('RecibosPagos')->exists('socios_id='.$sociosId);
				if($existenRecibosPagos==true){
					$canIEdit = false;
				}
			}
		}
		$config['existeActivarReserva']	= $existeActivarReserva;
		$config['existeNuevoContrato']	= $existeNuevoContrato;
		$config['canIEdit']				= $canIEdit;
		if($canIEdit==true){
			$configCambio = false;
			
			//Si es cambio de contrato copiamos lo necesario
			if($existeNuevoContrato==true){
				$nuevoTipoContratoId = $config['nuevo_contrato'];
				if($nuevoTipoContratoId<=0){
					$transaction->rollback('Es necesario dar el id del nuevo contrato');
				}
				$sociosOldId = $record->getId();
				$sociosNew = self::_antesDeCambiarContrato($transaction, $record, $nuevoTipoContratoId);
				$sociosNewId = $sociosNew->getId();
				//Verificamos lo pagado a capital por el contrato anterior y miramos si se paga en el nuevo
				$config['sociosIdOld'] = $sociosOldId;
				$config['sociosIdNew'] = $sociosNewId;
				$configCambio = array(
					'sociosIdOld' => $sociosOldId,
					'sociosIdNew' => $sociosNewId
				);
				//Obtenemos el capital del contrato antes de cambiar contrato para aplicar a derechos de afiliacion y cuotas iniciale sy si alcanza a financiacion
				TPC::cambioContratoCapital($configCambio, $transaction);
			}
			
			//Actualizamos los datos de membresias (membresias_socios)
			$config['record']				= $record; 
			$config['configCambio']			= $configCambio;
			self::_saveMembresiasSocios($transaction, $config);
			
			//Actualizamos los datos de detalle de cuotas iniciales (detalle_cuota)
			self::_saveDetalleCuota($transaction, $config);
			
			//Actualizamos los datos de saldo de pago (pago_saldo)
			self::_savePagosaldo($transaction, $config);
			
			//Si es cambio de contrato copiamos lo necesario
			if($existeNuevoContrato==true){
				
				//Actualizamos estado de cambio de contrato al nuevo contrato creado
				$sociosNew->setTransaction($transaction);
				$sociosNew->setValidar(false);
				$sociosNew->setEstadoContrato('A');
				$sociosNew->setEstadoMovimiento('CN');
				
				//Asignamos el S en el campo cambio_contrato de tabla Socios
				$sociosNew->setCambioContrato('S');
				if($sociosNew->save()==false){
					foreach($sociosNew->getMessages() as $message){
						$transaction->rollback('Contratos: '.$message->getMessage());
					}
				}
				$sociosNew->setValidar(true);
				
				//Validamos si existe amortización, sino la creamos
				$amortizacion = EntityManager::get('Amortizacion')->setTransaction($transaction)->findFirst(array('conditions' => 'socios_id='.$sociosNewId));
				if($amortizacion==false){
					//Recalculamos amortizacion
					TPC::remplazarAmortizacion($sociosNew, false, $transaction);
				}
				
				//Verificamos estado de contrato
				$data = array();
				$logger = new Logger('File', 'TpcClassSociosId'.$sociosNewId.'.log');
				$data['logger'] = $logger;
				
				//Verificamos si ya pago todo el socio de su contrato
				TPC::verifica100PorcientoPago($sociosNew, $data, $transaction);
			}
		}
		//Actualizamos los datos de saldo de pago (pago_saldo)
		self::_saveConyuge($transaction, $config);
		
		//Si esta activando reserva
		if($existeActivarReserva==true){
			$reservasId = $config['reservas_contrato'];
			if($reservasId){
				self::_abonarPagosActivarReserva($record, $reservasId, $transaction);
			}
		}
		
		return true;
	}
}
