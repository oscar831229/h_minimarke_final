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

Core::importFromLibrary('Hfos/Tpc','TpcContratos.php');
/**
 * TPC
 *
 * Clase componente que controla procesos internos de tpc con respecto a los contratos
 *
 */
class TpcTests extends UserComponent {

	/**
	 * Metodo principal de Test
	 */
	static function main(){
		$transaction = TransactionManager::getUserTransaction();
		
		self::limpiarBD($transaction);
		
		//Retorne los ActiveRecords de contratos creados
		$SociosObj = self::crearContratoMain($transaction);
		
		//Aplicamos pagos a esos contratos
		self::abonoContratoMain($transaction, $SociosObj);
		
		//Aplicamos notas contables
		self::notasContablesMain($transaction, $SociosObj);

		//Aplicamos cambios de contrato de pruebas
		self::cambioContratosMain($transaction);

		
		$transaction->commit();
		return true;
	}

	/**
	 * Metodo que valida los datos generados en el contrato
	 */
	static function validarContrato($transaction, &$config){
		$Socios = $config['Socios'];
		if(!isset($config['validarContrato']) && count($config['validarContrato'])<=0){
			$transaction->rollback('Es necesario crear la estructura de pruebas del contrato a crear');
		}
		$validarContrato = $config['validarContrato'];
		//Validamos la amortización
		if(isset($validarContrato['amortizacion'])){
			foreach($validarContrato['amortizacion'] as $numCuota => $validar){
				$validarFields = array(
					array('fieldTable'=>'valor', 'fieldArray'=>'valorCuotaFija', 'message'=>'La cuota fija mensual en la cuota '.$numCuota.' no es correcta'),
					array('fieldTable'=>'capital', 'fieldArray'=>'abonoCapital', 'message'=>'El abono a capital en la cuota '.$numCuota.' no es correcta'),
					array('fieldTable'=>'interes', 'fieldArray'=>'intereses', 'message'=>'El interes corriente en la cuota '.$numCuota.' no es correcta'),
					array('fieldTable'=>'saldo', 'fieldArray'=>'saldo', 'message'=>'El saldo en la cuota '.$numCuota.' no es correcta'),
				);
				//Campos a validar en amortización
				$cuota = self::getModel('Amortizacion')->setTransaction($transaction)->findFirst(array('conditions'=>'numero_cuota='.$numCuota.' AND socios_id='.$Socios->getId()));
				if($cuota==false){
					$transaction->rollback('La cuota de amortización '.$numCuota.' no existe');
				}
				//Validamos campos
				foreach($validarFields as $field){
					$valor1	= LocaleMath::round($cuota->readAttribute($field['fieldTable']),2); 
					$valor2	= LocaleMath::round($validar[$field['fieldArray']],2);
					$valorDiff = $valor1 - $valor2;
					if($valorDiff != 0){
						$transaction->rollback($field['message'].': '.$valor1.' - '.$valor2.'='.$valorDiff);
					}
				}
			}
		}
		//Validamos fecha de primer pago recalculada de 15 o 30 de mes
		if(isset($validarContrato['fechaPrimerPagoFinanciacion'])==true && empty($validarContrato['fechaPrimerPagoFinanciacion'])==false){
			$pagoSaldo = self::getModel('PagoSaldo')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$Socios->getId()));
			if($pagoSaldo==false){
				$transaction->rollback('El socios no tiene datos en modelo PagoSaldo');
			}
			if($pagoSaldo->getFechaPrimeraCuota()!=$validarContrato['fechaPrimerPagoFinanciacion']){
				$transaction->rollback('La fecha de primera cuota '.$pagoSaldo->getFechaPrimeraCuota().' debe ser calculada a '.$validarContrato['fechaPrimerPagoFinanciacion']);
			}
		}
	}

	/**
	 * Metodo que valida el estado del pago si esta bien o no
	 */
	static function validarPago(&$configAbono, $transaction){
		if(!isset($configAbono['validarPago'])){
			$transaction->rollback('Se debe ingresar un espacio de validación de pagos en la configuración');
		}
		$validarPago = $configAbono['validarPago'];
		//Validamos los campos
		$validarFields = array(
			array('fieldValidar'=>'derechosAfiliacion','fieldAddAbonoContrato'=>'valorCuoafi', 'message'=>'El derecho de afiliación pagado no es correcto. '),
			array('fieldValidar'=>'valorInicial','fieldAddAbonoContrato'=>'valorInicial', 'message'=>'El valor de financiación pagado no es correcto. '),
			array('fieldValidar'=>'totalDias','fieldAddAbonoContrato'=>'totalDias', 'message'=>'El valor de total días no es correcto. '),
			array('fieldValidar'=>'diasMora','fieldAddAbonoContrato'=>'totalDiasMora', 'message'=>'El valor de días de  mora no es correcto. '),
			array('fieldValidar'=>'interes','fieldAddAbonoContrato'=>'interecesCorrientesAplicados', 'message'=>'El valor de interes corriente aplicado no es correcto. '),
			array('fieldValidar'=>'valorMora','fieldAddAbonoContrato'=>'interesesMora', 'message'=>'El valor de interes mora aplicado no es correcto. '),
			array('fieldValidar'=>'saldo','fieldAddAbonoContrato'=>'saldo', 'message'=>'El valor de saldo no es correcto. '),
			array('fieldValidar'=>'capital','fieldAddAbonoContrato'=>'capital', 'message'=>'El valor de capital no es correcto. '),
		);
		//Recorremos los campos a validar en el pago
		foreach($validarFields as $field){
			//Validamos el derecho de afiliación
			if(isset($validarPago[$field['fieldValidar']])){
				if(isset($configAbono[$field['fieldAddAbonoContrato']])){
					if(is_numeric($configAbono[$field['fieldAddAbonoContrato']])==true){
						$valor1 = LocaleMath::round($validarPago[$field['fieldValidar']],2);
						$valor2 = LocaleMath::round($configAbono[$field['fieldAddAbonoContrato']],2);
					} else {
						$valor1 = $validarPago[$field['fieldValidar']];
						$valor2 = $configAbono[$field['fieldAddAbonoContrato']];
					}
					if($valor1!=$valor2){
						$transaction->rollback($field['message']."(".$configAbono['fechaPago']."/".$configAbono['identificacion'].")".$valor1.'(DeberiaSer)!='.$valor2.'(Calculado): '.print_r($configAbono,true));
					}
				}
			}
		}
	}

	/**
	 * Metodo principal de creacion de contratos
	 */
	static function abonoContratoMain($transaction, &$SociosObj){
		$today = date('Y-m-d');
		$configAbonoAll = array(
			////////////////////////////
			// CONTRATO1 DE PRUEBAS
			////////////////////////////
			//Pago derechos de afiliacion y cuotas iniciales
			array(
				'identificacion'=> 1070585456, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-01-15', 
				'formasPago' => array( 
					'count' => 3, 
					'total' => 5643953,
					'totalFPago' => array( 
						'E' => 5643953
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 5578953,
								'tipo' => 'E' 
							),
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 65000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 65000,
					'valorInicial'			=> 5578953,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,//Bogota
				'debug' => true
			),
			//Pagando cuota 1 financiacion
			array(
				'identificacion'=> 1070585456, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-01-15', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 50756, 
					'totalFPago' => array( 
						'E' => 50756
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 50756,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 30,
					'capital'				=> 17402.95,
					'interes'				=> 33353.05,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 1835544.05
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota 2 financiacion
			array(
				'identificacion'=> 1070585456, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-03-15', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 50756, 
					'totalFPago' => array( 
						'E' => 50756
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 50756,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 60,
					'capital'				=> 0,
					'interes'				=> 47084.91,
					'diasMora'				=> 30,
					'valorMora'				=> 3671.09,
					'saldo'					=> 1854538.72
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota 3 financiacion
			array(
				'identificacion'=> 1070585456, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-04-15', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 186999, 
					'totalFPago' => array( 
						'E' => 186999
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 186999,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 30,
					'capital'				=> 149908.23,
					'interes'				=> 33381.70,
					'diasMora'				=> 30,
					'valorMora'				=> 3709.08,
					'saldo'					=> 1704630.49
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota 4 financiacion
			array(
				'identificacion'=> 1070585456, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-09-15', 
				'formasPago' => array(
					'count' => 1,
					'total' => 173489,
					'totalFPago' => array(
						'E' => 173489
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 173489,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 150,
					'capital'				=> 20072.26,
					'interes'				=> 153416.74,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 1684558.24
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota 5 financiacion
			array(
				'identificacion'=> 1070585456, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-10-15', 
				'formasPago' => array(
					'count' => 1,
					'total' => 50756,
					'totalFPago' => array(
						'E' => 50756
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 50756,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 30,
					'capital'				=> 20433.95,
					'interes'				=> 30322.05,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 1664124.29
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			////////////////////////////
			// CONTRATO2 DE PRUEBAS
			////////////////////////////
			//Pago derechos de afiliacion y cuota inicial 1
			array(
				'identificacion'=> 12126834, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-01-17', 
				'formasPago' => array( 
					'count' => 2, 
					'total' => 3057500,
					'totalFPago' => array(
						'E' => 3057500
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 2992500,
								'tipo' => 'E' 
							),
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 65000,
								'tipo' => 'E'
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 65000,
					'valorInicial'			=> 2992500,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando seguna cuota inicial
			array(
				'identificacion'=> 12126834, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-02-24', 
				'formasPago' => array(
					'count' => 1,
					'total' => 2992500,
					'totalFPago' => array(
						'E' => 2992500
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 2992500,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 2992500,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cupta 1 de financiacion
			array(
				'identificacion'=> 12126834, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-04-25', 
				'formasPago' => array(
					'count' => 1,
					'total' => 834000,
					'totalFPago' => array(
						'E' => 834000
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 834000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 55,
					'capital'				=> 448680.00,
					'interes'				=> 366795.00,
					'diasMora'				=> 25,
					'valorMora'				=> 18525.00,
					'saldo'					=> 10666320.00
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota de financiacion
			array(
				'identificacion'=> 12126834, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-05-26', 
				'formasPago' => array(
					'count' => 1,
					'total' => 692000,
					'totalFPago' => array(
						'E' => 692000
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 692000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 31,
					'capital'				=> 493606.45,
					'interes'				=> 198393.55,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 10172713.55
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota de financiacion
			array(
				'identificacion'=> 12126834, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-06-29',
				'formasPago' => array(
					'count' => 1,
					'total' => 410500,
					'totalFPago' => array(
						'E' => 410500
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 410500,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 33,
					'capital'				=> 209080.27,
					'interes'				=> 201419.73,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 9963633.28
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			////////////////////////////
			// CONTRATO 3 DE PRUEBAS
			////////////////////////////
			//Pago derechos de afiliacion y cuota inicial 1
			array(
				'identificacion'=> 10277192, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-02-16', 
				'formasPago' => array( 
					'count' => 2, 
					'total' => 2375000,
					'totalFPago' => array(
						'E' => 2375000
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 2310000,
								'tipo' => 'E' 
							),
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 65000,
								'tipo' => 'E'
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 65000,
					'valorInicial'			=> 2310000,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago de financiacion 1
			array(
				'identificacion'=> 10277192, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-03-31', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 1500000,
					'totalFPago' => array(
						'E' => 1500000
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 1500000,
								'tipo' => 'E' 
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 30,
					'capital'				=> 1415580.00,
					'interes'				=> 84420.00,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 3274420.00
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago de financiacion 2
			array(
				'identificacion'=> 10277192, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-05-04', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 178150,
					'totalFPago' => array(
						'E' => 178150
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 178150,
								'tipo' => 'E' 
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 34,
					'capital'				=> 111351.83,
					'interes'				=> 66798.17,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 3163068.17
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago de financiacion 3
			array(
				'identificacion'=> 10277192, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-06-07', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 178150,
					'totalFPago' => array(
						'E' => 178150
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 178150,
								'tipo' => 'E' 
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 33,
					'capital'				=> 115521.25,
					'interes'				=> 62628.75,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 3047546.92
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago de financiacion 4
			array(
				'identificacion'=> 10277192, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-07-06', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 178150,
					'totalFPago' => array(
						'E' => 178150
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 178150,
								'tipo' => 'E' 
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 29,
					'capital'				=> 125122.68,
					'interes'				=> 53027.32,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 2922424.23
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			////////////////////////////
			// CONTRATO4 DE PRUEBAS
			////////////////////////////
			//Pago derechos de afiliacion y cuotas iniciales
			array(
				'identificacion'=> 10705854561, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-01-15', 
				'formasPago' => array( 
					'count' => 2, 
					'total' => 5643953,
					'totalFPago' => array(
						'E' => 5643953
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 5578953,
								'tipo' => 'E' 
							),
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 65000,
								'tipo' => 'E' 
							)  
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 65000,
					'valorInicial'			=> 5578953,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			////////////////////////////
			// CONTRATO5 DE PRUEBAS
			////////////////////////////
			//Pago derechos de afiliacion y cuotas iniciales
			array(
				'identificacion'=> 32858008, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-06-15', 
				'formasPago' => array( 
					'count' => 2, 
					'total' => 3065000,
					'totalFPago' => array(
						'E' => 3065000,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 3000000,
								'tipo' => 'E' 
							),
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 65000,
								'tipo' => 'E' 
							)  
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 65000,
					'valorInicial'			=> 3000000,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago de financiacion 1
			array(
				'identificacion'=> 32858008, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-09-15', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 280206.00,
					'totalFPago' => array(
						'E' => 280206.00
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 280206.00,
								'tipo' => 'E' 
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 30,
					'capital'				=> 226206.00,
					'interes'				=> 54000.00,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 2773794.00
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago de financiacion 2
			array(
				'identificacion'=> 32858008, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-10-15', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 280205.00,
					'totalFPago' => array(
						'E' => 280205.00
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 280205.00,
								'tipo' => 'E' 
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 30,
					'capital'				=> 230276.71,
					'interes'				=> 49928.29,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 2543517.29
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago de financiacion 3
			array(
				'identificacion'=> 32858008, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-11-15', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 285294.00,
					'totalFPago' => array(
						'E' => 285294.00,
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 285294.00,
								'tipo' => 'E' 
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 30,
					'capital'				=> 234423.65,
					'interes'				=> 45783.31,
					'diasMora'				=> 30,
					'valorMora'				=> 5087.03,
					'saldo'					=> 2309093.64
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago de financiacion 4
			array(
				'identificacion'=> 32858008, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2012-01-17', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 280206,
					'totalFPago' => array(
						'E' => 280206,
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 280206,
								'tipo' => 'E' 
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 62,
					'capital'				=> 189381.65,
					'interes'				=> 85898.28,
					'diasMora'				=> 32,
					'valorMora'				=> 4926.07,
					'saldo'					=> 2119711.99
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago de financiacion 5
			array(
				'identificacion'=> 32858008, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2012-02-15', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 579077,
					'totalFPago' => array(
						'E' => 579077,
					),
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 579077,
								'tipo' => 'E' 
							)
						)
					)
				),
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 28,
					'capital'				=> 539509.04,
					'interes'				=> 35611.16,
					'diasMora'				=> 28,
					'valorMora'				=> 3956.80,
					'saldo'					=> 1580202.94
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			////////////////////////////
			// CONTRATO6 DE PRUEBAS
			////////////////////////////
			//Pago derechos de afiliacion y cuotas iniciales
			array(
				'identificacion'=> 328580081, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-02-15', 
				'formasPago' => array( 
					'count' => 2, 
					'total' => 825000+65000+156448.00,
					'totalFPago' => array(
						'E' => 825000+65000+156448.00,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 825000,
								'tipo' => 'E' 
							),
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 65000,
								'tipo' => 'E' 
							),
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 156448.00,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 65000,
					'valorInicial'			=> 825000,
					'totalDias'				=> 0,
					'capital'				=> 156448.00,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 1518552.00
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota 2 financiacion
			array(
				'identificacion'=> 328580081, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-06-15', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 626000.00, 
					'totalFPago' => array( 
						'E' => 626000.00
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 626000.00,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 120,
					'capital'				=> 510590.05,
					'interes'				=> 109335.74,
					'diasMora'				=> 60,
					'valorMora'				=> 6074.21,
					'saldo'					=> 1007961.95
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota 3 financiacion
			array(
				'identificacion'=> 328580081, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-06-30', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 35445.00,
					'totalFPago' => array( 
						'E' => 35445.00
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 35445.00,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 15,
					'capital'				=> 26373.34,
					'interes'				=> 9071.66,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 981588.61
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota 4 financiacion
			array(
				'identificacion'=> 328580081, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-07-15', 
				'formasPago' => array(
					'count' => 1,
					'total' => 156448.00,
					'totalFPago' => array(
						'E' => 156448.00
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 156448.00,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 15,
					'capital'				=> 147613.70,
					'interes'				=> 8834.30,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 833974.91
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota 5 financiacion
			array(
				'identificacion'=> 328580081, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-10-15', 
				'formasPago' => array(
					'count' => 1,
					'total' => 469920.00,
					'totalFPago' => array(
						'E' => 469920.00
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 469920.00,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 90,
					'capital'				=> 423217.41,
					'interes'				=> 45034.64,
					'diasMora'				=> 30,
					'valorMora'				=> 1667.95,
					'saldo'					=> 410757.50
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			////////////////////////////
			// CONTRATO7 DE PRUEBAS
			////////////////////////////
			//Pago derechos de afiliacion y cuotas iniciales
			array(
				'identificacion'=> 328580082,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-02-01', 
				'formasPago' => array( 
					'count' => 2, 
					'total' => 525000+135000,
					'totalFPago' => array(
						'E' => 525000+135000,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 525000,
								'tipo' => 'E' 
							),
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 135000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 135000,
					'valorInicial'			=> 525000,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pagando cuota 2 financiacion
			array(
				'identificacion'=> 328580082,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-02-10', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 1620000+800000,
					'totalFPago' => array(
						'E' => 1620000+800000
					), 
					'data' => array(
						'E' => array(
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 1620000+800000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 1620000,
					'totalDias'				=> 0,
					'capital'				=> 800000,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 3555000
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			////////////////////////////
			// CONTRATO8 DE PRUEBAS
			////////////////////////////
			//Pago derechos de afiliacion y cuotas iniciales
			array(
				'identificacion'=> 328580083,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-01-05', 
				'formasPago' => array( 
					'count' => 2, 
					'total' => 2265000+235000,
					'totalFPago' => array(
						'E' => 2265000+235000,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 2265000,
								'tipo' => 'E' 
							),
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 235000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 235000,
					'valorInicial'			=> 2265000,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago resto de cuota iniciale y 1ra financiacion
			array(
				'identificacion'=> 328580083,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-02-18',
				'formasPago' => array( 
					'count' => 1, 
					'total' => 1155309,
					'totalFPago' => array(
						'E' => 1155309,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 1155309,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 735000,
					'totalDias'				=> 18,
					'capital'				=> 371709.00,
					'interes'				=> 48600.00,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 4128291.00
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago 2 financiacion
			array(
				'identificacion'=> 328580083,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-03-30',
				'formasPago' => array( 
					'count' => 1, 
					'total' => 313016.00,
					'totalFPago' => array(
						'E' => 313016.00,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 313016.00,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 60,
					'capital'				=> 164397.52,
					'interes'				=> 148618.48,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 3963893.48
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago 3 financiacion
			array(
				'identificacion'=> 328580083,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-04-30',
				'formasPago' => array( 
					'count' => 1, 
					'total' => 420309.00,
					'totalFPago' => array(
						'E' => 420309.00,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 420309.00,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 30,
					'capital'				=> 341031.13,
					'interes'				=> 71350.08,
					'diasMora'				=> 30,
					'valorMora'				=> 7927.79,
					'saldo'					=> 3622862.35
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago 4 financiacion
			array(
				'identificacion'=> 328580083,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-05-30',
				'formasPago' => array( 
					'count' => 1, 
					'total' => 543256.00,
					'totalFPago' => array(
						'E' => 543256.00,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 543256.00,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 30,
					'capital'				=> 470798.75,
					'interes'				=> 65211.52,
					'diasMora'				=> 30,
					'valorMora'				=> 7245.72,
					'saldo'					=> 3152063.59
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			////////////////////////////
			// CONTRATO9 DE PRUEBAS
			////////////////////////////
			//Pago derechos de afiliacion y cuotas iniciales
			array(
				'identificacion'=> 328580084,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-01-05', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 1175000,
					'totalFPago' => array(
						'E' => 1175000,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 1175000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 235000,
					'valorInicial'			=> 940000,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago resto de cuota iniciale y 1ra financiacion
			array(
				'identificacion'=> 328580084,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-02-05',
				'formasPago' => array( 
					'count' => 1, 
					'total' => 2330000,
					'totalFPago' => array(
						'E' => 2330000,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 2330000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 2180000,
					'totalDias'				=> 5,
					'capital'				=> 135960.00,
					'interes'				=> 14040.00,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 4544040.00
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago 2 financiacion
			array(
				'identificacion'=> 328580084,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2011-03-30',
				'formasPago' => array( 
					'count' => 1, 
					'total' => 437121.00,
					'totalFPago' => array(
						'E' => 437121.00,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 437121.00,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 60,
					'capital'				=> 264447.48,
					'interes'				=> 163585.44,
					'diasMora'				=> 30,
					'valorMora'				=> 9088.08,
					'saldo'					=> 4279592.52
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			////////////////////////////
			// CONTRATO10 DE PRUEBAS
			////////////////////////////
			//Pago derechos de afiliacion y cuota iniciales 1
			array(
				'identificacion'=> 328580085,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2009-08-01', 
				'formasPago' => array( 
					'count' => 1, 
					'total' => 823140+135000,
					'totalFPago' => array(
						'E' => 823140+135000,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 823140+135000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 135000,
					'valorInicial'			=> 823140,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago resto de cuota iniciale y 1ra financiacion
			array(
				'identificacion'=> 328580085,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2009-08-28',
				'formasPago' => array( 
					'count' => 1, 
					'total' => 777500,
					'totalFPago' => array(
						'E' => 777500,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 777500,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 777410,
					'totalDias'				=> 0,
					'capital'				=> 90.00,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 2972360.00
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'debug' => true
			),
			//Pago resto de cuota iniciale y 1ra financiacion
			array(
				'identificacion'=> 328580085,
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2009-12-23',
				'formasPago' => array( 
					'count' => 1, 
					'total' => 338715,
					'totalFPago' => array(
						'E' => 338715,
					), 
					'data' => array(
						'E' => array(
							array(
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 338715,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 98,
					'capital'				=> 150465.53,
					'interes'				=> 174774.77,
					'diasMora'				=> 68,
					'valorMora'				=> 13474.70,
					'saldo'					=> 2680799.74
				),
				'cuentasId' => 4,
				'reciboProvisional' => '',
				'ciudadPago' => 127591,
				'porcentCondonacion' => 5,//5%
				'debug' => true
			),
			
		);
		$abonosContratoObj = array();
		//Recorremos lso pago a hacer y aplicamos segun contrato
		//ActiveRecord::disableEvents(true);
		Rcs::disable();
		foreach($SociosObj as $Socio){
			foreach($configAbonoAll as $configAbono){
				//Si el pago es para el contrato con la cedula tal, aplique el abono a ese contrato
				if($Socio->getIdentificacion()==$configAbono['identificacion']){
					//Asignamos id del registro en modelo Socios
					$configAbono['sociosId'] = $Socio->getId();
					//Aplicamos el pago
					TPC::addAbonoContrato($configAbono, $transaction);
					self::validarPago($configAbono, $transaction);
					$abonosContratoObj[]=$configAbono;
				}
				unset($configAbono);
			}
			unset($Socio);
		}
	}

	/**
	 * Metodo principal de creacion de contratos
	 */
	static function crearContratoMain($transaction){
		$configAll = array(
			////////////////////////////
			// CONTRATO1 DE PRUEBAS
			////////////////////////////
			array(
				//Socios
				'tipoContratoId'		=> 1,//Cualquier tipo de contrato
				'fechaCompra'			=> '2010-11-07',
				'tipoDocumentosId'		=> 1,//Cualquiera
				'identificacion'		=> 1070585456,
				'apellidos'				=> 'Carvajal',
				'nombres'				=> 'Eduar',
				'direccionResidencia'	=> 'Cra 127D Bis #139A-46',
				'ciudad_residencia'		=> 127591,
				'telefonoResidencia'	=> 6936466,
				'celular'				=> 3014114428,
				'envioCorrespondencia'	=> 'S',
				'tipoSociosId'			=> 16,//
				'estadoContrato'		=> 'A',//Activo
				'estadoMovimiento'		=> 'R',//reserva
				'estadosCivilesId'		=> 2,
				'cambioContrato'		=> 'N', //Nooo
				//Si es activar reserva
				'reservas_contrato'		=> false,
				//Si es cambio de contrato
				'nuevo_contrato'		=> false,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 7431900,
				'cuota_inicial'			=> 5578953,
				'derecho_afiliacion_id'	=> 1,
				//Detalle cuota
				'hoy'					=> 5578953,
				'fecha1'				=> '2011-01-15',
				'cuota2'				=> 0,
				'fecha2'				=> '',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 60,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2011-01-10',
				'premios_id'			=> 0,
				'observaciones'			=> '',
				//Conyuges
				'conyuge_tipo_documentos_id'=> 0,
				'conyuge_identificacion'=> 0,
				'conyuge_nombres'		=> '',
				'conyuge_apellidos'		=> '',
				'conyuge_fecha_nacimiento'=> '',
				'conyuge_direccion'		=> '',
				'conyuge_telefono'		=> '',
				'conyuge_celular'		=> '',
				'conyuge_profesiones_id'=> '',
				'conyuge_estados_civiles_id'=> '',
				//Validacion contrato
				'validarContrato'		=> array(
					'amortizacion' => array(
						'1' =>  array(
							'valorCuotaFija'	=> 50755.86,
							'abonoCapital'		=> 17402.82,
							'intereses'			=> 33353.05,
							'saldo'				=> 1835544.18
						),
						'30' =>  array(
							'valorCuotaFija'	=> 50755.86,
							'abonoCapital'		=> 29194.77,
							'intereses'			=> 21561.09,
							'saldo'				=> 1168643.55
						),
						'60' =>  array(
							'valorCuotaFija'	=> 50755.86,
							'abonoCapital'		=> 49858.41,
							'intereses'			=> 897.45,
							'saldo'				=> 0.00
						),
					),
					//Valida la fecha generada en financiacion
					'fechaPrimerPagoFinanciacion' => '2011-01-15'
				),
			),
			////////////////////////////
			// CONTRATO2 DE PRUEBAS
			////////////////////////////
			array(
				//Socios
				'tipoContratoId'		=> 1,//Cualquier tipo de contrato
				'fechaCompra'			=> '2010-01-14',
				'tipoDocumentosId'		=> 1,//Cualquiera
				'identificacion'		=> 12126834,
				'apellidos'				=> 'TOVAR',
				'nombres'				=> 'HEBER',
				'direccionResidencia'	=> 'Cra 127D Bis #139A-46',
				'ciudad_residencia'		=> 127591,
				'telefonoResidencia'	=> 6936466,
				'celular'				=> 3014114428,
				'envioCorrespondencia'	=> 'S',
				'tipoSociosId'			=> 16,//
				'estadoContrato'		=> 'A',//Activo
				'estadoMovimiento'		=> 'R',//reserva
				'estadosCivilesId'		=> 2,
				'cambioContrato'		=> 'N', //Nooo
				//Si es activar reserva
				'reservas_contrato'		=> false,
				//Si es cambio de contrato
				'nuevo_contrato'		=> false,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 17100000,
				'cuota_inicial'			=> 5985000,
				'derecho_afiliacion_id'	=> 1,//65000
				//Detalle cuota
				'hoy'					=> 2992500,
				'fecha1'				=> '2011-01-17',
				'cuota2'				=> 2992500,
				'fecha2'				=> '2011-02-24',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 36,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2011-03-30',
				'premios_id'			=> 0,
				'observaciones'			=> '',
				//Conyuges
				'conyuge_tipo_documentos_id'=> 0,
				'conyuge_identificacion'=> 0,
				'conyuge_nombres'		=> '',
				'conyuge_apellidos'		=> '',
				'conyuge_fecha_nacimiento'=> '',
				'conyuge_direccion'		=> '',
				'conyuge_telefono'		=> '',
				'conyuge_celular'		=> '',
				'conyuge_profesiones_id'=> '',
				'conyuge_estados_civiles_id'=> '',
				//Validacion contrato
				'validarContrato'		=> array(
					'amortizacion' => array(
						'1' =>  array(
							'valorCuotaFija'	=> 422190.29,
							'abonoCapital'		=> 222120.29,
							'intereses'			=> 200070.00,
							'saldo'				=> 10892879.71
						),
						'20' =>  array(
							'valorCuotaFija'	=> 422190.29,
							'abonoCapital'		=> 311742.50,
							'intereses'			=> 110447.79,
							'saldo'				=> 5824245.81
						),
						'36' =>  array(
							'valorCuotaFija'	=> 422190.29,
							'abonoCapital'		=> 414725.24,
							'intereses'			=> 7465.05,
							'saldo'				=> 0.00
						),
					),
					//Valida la fecha generada en financiacion
					'fechaPrimerPagoFinanciacion' => '2011-03-30'
				),
			),
			////////////////////////////
			// CONTRATO3 DE PRUEBAS
			////////////////////////////
			array(
				//Socios
				'tipoContratoId'		=> 1,//Cualquier tipo de contrato
				'fechaCompra'			=> '2010-02-16',
				'tipoDocumentosId'		=> 1,//Cualquiera
				'identificacion'		=> 10277192,
				'apellidos'				=> 'IDARRAGA',
				'nombres'				=> 'ALVARO',
				'direccionResidencia'	=> 'Cra 127D Bis #139A-46',
				'ciudad_residencia'		=> 127591,
				'telefonoResidencia'	=> 6936466,
				'celular'				=> 3014114428,
				'envioCorrespondencia'	=> 'S',
				'tipoSociosId'			=> 16,//
				'estadoContrato'		=> 'A',//Activo
				'estadoMovimiento'		=> 'R',//reserva
				'estadosCivilesId'		=> 2,
				'cambioContrato'		=> 'N', //Nooo
				//Si es activar reserva
				'reservas_contrato'		=> false,
				//Si es cambio de contrato
				'nuevo_contrato'		=> false,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 7000000,
				'cuota_inicial'			=> 2310000,
				'derecho_afiliacion_id'	=> 1,//65000
				//Detalle cuota
				'hoy'					=> 2310000,
				'fecha1'				=> '2011-02-16',
				'cuota2'				=> 0,
				'fecha2'				=> '',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 36,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2011-03-30',
				'premios_id'			=> 0,
				'observaciones'			=> '',
				//Conyuges
				'conyuge_tipo_documentos_id'=> 0,
				'conyuge_identificacion'=> 0,
				'conyuge_nombres'		=> '',
				'conyuge_apellidos'		=> '',
				'conyuge_fecha_nacimiento'=> '',
				'conyuge_direccion'		=> '',
				'conyuge_telefono'		=> '',
				'conyuge_celular'		=> '',
				'conyuge_profesiones_id'=> '',
				'conyuge_estados_civiles_id'=> '',
				//Validacion contrato
				'validarContrato'		=> array(
					'amortizacion' => array(
						'1' =>  array(
							'valorCuotaFija'	=> 178144.17,
							'abonoCapital'		=> 93724.17,
							'intereses'			=> 84420.00,
							'saldo'				=> 4596275.83
						),
						'20' =>  array(
							'valorCuotaFija'	=> 178144.17,
							'abonoCapital'		=> 131540.47,
							'intereses'			=> 46603.70,
							'saldo'				=> 2457554.01
						),
						'36' =>  array(
							'valorCuotaFija'	=> 178144.17,
							'abonoCapital'		=> 174994.27,
							'intereses'			=> 3149.90,
							'saldo'				=> 0.00
						),
					),
					//Valida la fecha generada en financiacion
					'fechaPrimerPagoFinanciacion' => '2011-03-30'
				),
			),
			////////////////////////////
			// CONTRATO4 DE PRUEBAS
			////////////////////////////
			array(
				//Socios
				'tipoContratoId'		=> 1,//Cualquier tipo de contrato
				'fechaCompra'			=> '2010-11-07',
				'tipoDocumentosId'		=> 1,//Cualquiera
				'identificacion'		=> 10705854561,
				'apellidos'				=> 'Carvajal2',
				'nombres'				=> 'Eduar2',
				'direccionResidencia'	=> 'Cra 127D Bis #139A-46',
				'ciudad_residencia'		=> 127591,
				'telefonoResidencia'	=> 6936466,
				'celular'				=> 3014114428,
				'envioCorrespondencia'	=> 'S',
				'tipoSociosId'			=> 16,//
				'estadoContrato'		=> 'A',//Activo
				'estadoMovimiento'		=> 'R',//reserva
				'estadosCivilesId'		=> 2,
				'cambioContrato'		=> 'N', //Nooo
				//Si es activar reserva
				'reservas_contrato'		=> false,
				//Si es cambio de contrato
				'nuevo_contrato'		=> false,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 7431900,
				'cuota_inicial'			=> 5578953,
				'derecho_afiliacion_id'	=> 1,
				//Detalle cuota
				'hoy'					=> 5578953,
				'fecha1'				=> '2011-01-15',
				'cuota2'				=> 0,
				'fecha2'				=> '',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 60,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2011-01-10',
				'premios_id'			=> 0,
				'observaciones'			=> '',
				//Conyuges
				'conyuge_tipo_documentos_id'=> 0,
				'conyuge_identificacion'=> 0,
				'conyuge_nombres'		=> '',
				'conyuge_apellidos'		=> '',
				'conyuge_fecha_nacimiento'=> '',
				'conyuge_direccion'		=> '',
				'conyuge_telefono'		=> '',
				'conyuge_celular'		=> '',
				'conyuge_profesiones_id'=> '',
				'conyuge_estados_civiles_id'=> '',
				//Validacion contrato
				'validarContrato'		=> array(
					'amortizacion' => array(
						'1' =>  array(
							'valorCuotaFija'	=> 50755.86,
							'abonoCapital'		=> 17402.82,
							'intereses'			=> 33353.05,
							'saldo'				=> 1835544.18
						),
						'30' =>  array(
							'valorCuotaFija'	=> 50755.86,
							'abonoCapital'		=> 29194.77,
							'intereses'			=> 21561.09,
							'saldo'				=> 1168643.55
						),
						'60' =>  array(
							'valorCuotaFija'	=> 50755.86,
							'abonoCapital'		=> 49858.41,
							'intereses'			=> 897.45,
							'saldo'				=> 0.00
						),
					),
					//Valida la fecha generada en financiacion
					'fechaPrimerPagoFinanciacion' => '2011-01-15'
				),
			),
			////////////////////////////
			// CONTRATO5 DE PRUEBAS
			////////////////////////////
			array(
				//Socios
				'tipoContratoId'		=> 1,//Cualquier tipo de contrato
				'fechaCompra'			=> '2011-06-15',
				'tipoDocumentosId'		=> 1,//Cualquiera
				'identificacion'		=> 32858008,
				'apellidos'				=> 'ARIAS',
				'nombres'				=> 'ANTONIA',
				'direccionResidencia'	=> 'Cra 127D Bis #139A-46',
				'ciudad_residencia'		=> 127591,
				'telefonoResidencia'	=> 6936466,
				'celular'				=> 3014114428,
				'envioCorrespondencia'	=> 'S',
				'tipoSociosId'			=> 16,//
				'estadoContrato'		=> 'A',//Activo
				'estadoMovimiento'		=> 'R',//reserva
				'estadosCivilesId'		=> 2,
				'cambioContrato'		=> 'N', //Nooo
				//Si es activar reserva
				'reservas_contrato'		=> false,
				//Si es cambio de contrato
				'nuevo_contrato'		=> false,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 6000000,
				'cuota_inicial'			=> 3000000,
				'derecho_afiliacion_id'	=> 1,
				//Detalle cuota
				'hoy'					=> 1000000,
				'fecha1'				=> '2011-06-15',
				'cuota2'				=> 1000000,
				'fecha2'				=> '2011-06-15',
				'cuota3'				=> 1000000,
				'fecha3'				=> '2011-06-15',
				//PagoSaldo
				'numero_cuotas'			=> 12,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2011-09-15',
				'premios_id'			=> 0,
				'observaciones'			=> '',
				//Conyuges
				'conyuge_tipo_documentos_id'=> 0,
				'conyuge_identificacion'=> 0,
				'conyuge_nombres'		=> '',
				'conyuge_apellidos'		=> '',
				'conyuge_fecha_nacimiento'=> '',
				'conyuge_direccion'		=> '',
				'conyuge_telefono'		=> '',
				'conyuge_celular'		=> '',
				'conyuge_profesiones_id'=> '',
				'conyuge_estados_civiles_id'=> '',
				//Validacion contrato
				'validarContrato'		=> array(
					'amortizacion' => array(
						'1' =>  array(
							'valorCuotaFija'	=> 280205.93,
							'abonoCapital'		=> 226205.93,
							'intereses'			=> 54000.00,
							'saldo'				=> 2773794.07
						),
						'6' =>  array(
							'valorCuotaFija'	=> 280205.93,
							'abonoCapital'		=> 247310.68,
							'intereses'			=> 32895.25,
							'saldo'				=> 1580203.07
						),
						'12' =>  array(
							'valorCuotaFija'	=> 280205.93,
							'abonoCapital'		=> 275251.41,
							'intereses'			=> 4954.53,
							'saldo'				=> 0.00
						),
					),
					//Valida la fecha generada en financiacion
					'fechaPrimerPagoFinanciacion' => '2011-09-15'
				),
			),
			////////////////////////////
			// CONTRATO6 DE PRUEBAS
			////////////////////////////
			array(
				//Socios
				'tipoContratoId'		=> 1,//Cualquier tipo de contrato
				'fechaCompra'			=> '2011-02-01',
				'tipoDocumentosId'		=> 1,//Cualquiera
				'identificacion'		=> 328580081,
				'apellidos'				=> 'ANDREA',
				'nombres'				=> 'PLAZAS',
				'direccionResidencia'	=> 'Cra 127D Bis #139A-46',
				'ciudad_residencia'		=> 127591,
				'telefonoResidencia'	=> 6936466,
				'celular'				=> 3014114428,
				'envioCorrespondencia'	=> 'S',
				'tipoSociosId'			=> 16,//
				'estadoContrato'		=> 'A',//Activo
				'estadoMovimiento'		=> 'R',//reserva
				'estadosCivilesId'		=> 2,
				'cambioContrato'		=> 'N', //Nooo
				//Si es activar reserva
				'reservas_contrato'		=> false,
				//Si es cambio de contrato
				'nuevo_contrato'		=> false,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 2500000,
				'cuota_inicial'			=> 825000,
				'derecho_afiliacion_id'	=> 1,
				//Detalle cuota
				'hoy'					=> 825000,
				'fecha1'				=> '2011-02-15',
				'cuota2'				=> 0,
				'fecha2'				=> '',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 12,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2011-03-15',
				'premios_id'			=> 0,
				'observaciones'			=> '',
				//Conyuges
				'conyuge_tipo_documentos_id'=> 0,
				'conyuge_identificacion'=> 0,
				'conyuge_nombres'		=> '',
				'conyuge_apellidos'		=> '',
				'conyuge_fecha_nacimiento'=> '',
				'conyuge_direccion'		=> '',
				'conyuge_telefono'		=> '',
				'conyuge_celular'		=> '',
				'conyuge_profesiones_id'=> '',
				'conyuge_estados_civiles_id'=> '',
				//Validacion contrato
				'validarContrato'		=> array(
					'amortizacion' => array(
						'1' =>  array(
							'valorCuotaFija'	=> 156448.31,
							'abonoCapital'		=> 126298.31,
							'intereses'			=> 30150.00,
							'saldo'				=> 1548701.69
						),
						'6' =>  array(
							'valorCuotaFija'	=> 156448.31,
							'abonoCapital'		=> 138081.80,
							'intereses'			=> 18366.51,
							'saldo'				=> 882280.05
						),
						'12' =>  array(
							'valorCuotaFija'	=> 156448.31,
							'abonoCapital'		=> 153682.03,
							'intereses'			=> 2766.28,
							'saldo'				=> 0.00
						),
					),
					//Valida la fecha generada en financiacion
					'fechaPrimerPagoFinanciacion' => '2011-03-15'
				),
			),
			////////////////////////////
			// CONTRATO7 DE PRUEBAS
			////////////////////////////
			array(
				//Socios
				'tipoContratoId'		=> 1,//Cualquier tipo de contrato
				'fechaCompra'			=> '2011-01-10',
				'tipoDocumentosId'		=> 1,//Cualquiera
				'identificacion'		=> 328580082,
				'apellidos'				=> 'ANDREA',
				'nombres'				=> 'PLAZAS',
				'direccionResidencia'	=> 'Cra 127D Bis #139A-46',
				'ciudad_residencia'		=> 127591,
				'telefonoResidencia'	=> 6936466,
				'celular'				=> 3014114428,
				'envioCorrespondencia'	=> 'S',
				'tipoSociosId'			=> 16,//
				'estadoContrato'		=> 'A',//Activo
				'estadoMovimiento'		=> 'R',//reserva
				'estadosCivilesId'		=> 2,
				'cambioContrato'		=> 'N', //Nooo
				//Si es activar reserva
				'reservas_contrato'		=> false,
				//Si es cambio de contrato
				'nuevo_contrato'		=> false,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 6500000,
				'cuota_inicial'			=> 2145000,
				'derecho_afiliacion_id'	=> 4,//135000
				//Detalle cuota
				'hoy'					=> 2145000,
				'fecha1'				=> '2011-02-10',
				'cuota2'				=> 0,
				'fecha2'				=> '',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 24,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2011-03-15',
				'premios_id'			=> 0,
				'observaciones'			=> '',
				//Conyuges
				'conyuge_tipo_documentos_id'=> 0,
				'conyuge_identificacion'=> 0,
				'conyuge_nombres'		=> '',
				'conyuge_apellidos'		=> '',
				'conyuge_fecha_nacimiento'=> '',
				'conyuge_direccion'		=> '',
				'conyuge_telefono'		=> '',
				'conyuge_celular'		=> '',
				'conyuge_profesiones_id'=> '',
				'conyuge_estados_civiles_id'=> '',
				//Validacion contrato
				'validarContrato'		=> array(
					'amortizacion' => array(
						'1' =>  array(
							'valorCuotaFija'	=> 225070.04,
							'abonoCapital'		=> 146680.04,
							'intereses'			=> 78390.00,
							'saldo'				=> 4208319.96
						),
						'6' =>  array(
							'valorCuotaFija'	=> 225070.04,
							'abonoCapital'		=> 160365.11,
							'intereses'			=> 64704.92,
							'saldo'				=> 3434352.76
						),
						'24' =>  array(
							'valorCuotaFija'	=> 225070.04,
							'abonoCapital'		=> 221090.41,
							'intereses'			=> 3979.63,
							'saldo'				=> 0.00
						),
					),
					//Valida la fecha generada en financiacion
					'fechaPrimerPagoFinanciacion' => '2011-03-15'
				),
			),
			////////////////////////////
			// CONTRATO8 DE PRUEBAS
			////////////////////////////
			array(
				//Socios
				'tipoContratoId'		=> 1,//Cualquier tipo de contrato
				'fechaCompra'			=> '2011-01-05',
				'tipoDocumentosId'		=> 1,//Cualquiera
				'identificacion'		=> 328580083,
				'apellidos'				=> 'ANDREA',
				'nombres'				=> 'PLAZAS',
				'direccionResidencia'	=> 'Cra 127D Bis #139A-46',
				'ciudad_residencia'		=> 127591,
				'telefonoResidencia'	=> 6936466,
				'celular'				=> 3014114428,
				'envioCorrespondencia'	=> 'S',
				'tipoSociosId'			=> 16,//
				'estadoContrato'		=> 'A',//Activo
				'estadoMovimiento'		=> 'R',//reserva
				'estadosCivilesId'		=> 2,
				'cambioContrato'		=> 'N', //Nooo
				//Si es activar reserva
				'reservas_contrato'		=> false,
				//Si es cambio de contrato
				'nuevo_contrato'		=> false,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 7500000,
				'cuota_inicial'			=> 3000000,
				'derecho_afiliacion_id'	=> 3,//235000
				//Detalle cuota
				'hoy'					=> 2000000,
				'fecha1'				=> '2011-01-05',
				'cuota2'				=> 1000000,
				'fecha2'				=> '2011-01-16',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 12,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2011-02-16',
				'premios_id'			=> 0,
				'observaciones'			=> '',
				//Conyuges
				'conyuge_tipo_documentos_id'=> 0,
				'conyuge_identificacion'=> 0,
				'conyuge_nombres'		=> '',
				'conyuge_apellidos'		=> '',
				'conyuge_fecha_nacimiento'=> '',
				'conyuge_direccion'		=> '',
				'conyuge_telefono'		=> '',
				'conyuge_celular'		=> '',
				'conyuge_profesiones_id'=> '',
				'conyuge_estados_civiles_id'=> '',
				//Validacion contrato
				'validarContrato'		=> array(
					'amortizacion' => array(
						'1' =>  array(
							'valorCuotaFija'	=> 420308.90,
							'abonoCapital'		=> 339308.90,
							'intereses'			=> 81000.00,
							'saldo'				=> 4160691.10
						),
						'6' =>  array(
							'valorCuotaFija'	=> 420308.90,
							'abonoCapital'		=> 370966.02,
							'intereses'			=> 49342.87,
							'saldo'				=> 2370304.60
						),
						'12' =>  array(
							'valorCuotaFija'	=> 420308.90,
							'abonoCapital'		=> 412877.11,
							'intereses'			=> 7431.79,
							'saldo'				=> 0.00
						),
					),
					//Valida la fecha generada en financiacion
					'fechaPrimerPagoFinanciacion' => '2011-02-30'
				),
			),
			////////////////////////////
			// CONTRATO9 DE PRUEBAS
			////////////////////////////
			array(
				//Socios
				'tipoContratoId'		=> 1,//Cualquier tipo de contrato
				'fechaCompra'			=> '2011-01-05',
				'tipoDocumentosId'		=> 1,//Cualquiera
				'identificacion'		=> 328580084,
				'apellidos'				=> 'ANDREA',
				'nombres'				=> 'PLAZAS',
				'direccionResidencia'	=> 'Cra 127D Bis #139A-46',
				'ciudad_residencia'		=> 127591,
				'telefonoResidencia'	=> 6936466,
				'celular'				=> 3014114428,
				'envioCorrespondencia'	=> 'S',
				'tipoSociosId'			=> 16,//
				'estadoContrato'		=> 'A',//Activo
				'estadoMovimiento'		=> 'R',//reserva
				'estadosCivilesId'		=> 2,
				'cambioContrato'		=> 'N', //Nooo
				//Si es activar reserva
				'reservas_contrato'		=> false,
				//Si es cambio de contrato
				'nuevo_contrato'		=> false,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 7800000,
				'cuota_inicial'			=> 3120000,
				'derecho_afiliacion_id'	=> 3,//235000
				//Detalle cuota
				'hoy'					=> 1040000,
				'fecha1'				=> '2011-01-05',
				'cuota2'				=> 2080000,
				'fecha2'				=> '2011-02-05',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 12,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2011-02-16',
				'premios_id'			=> 0,
				'observaciones'			=> '',
				//Conyuges
				'conyuge_tipo_documentos_id'=> 0,
				'conyuge_identificacion'=> 0,
				'conyuge_nombres'		=> '',
				'conyuge_apellidos'		=> '',
				'conyuge_fecha_nacimiento'=> '',
				'conyuge_direccion'		=> '',
				'conyuge_telefono'		=> '',
				'conyuge_celular'		=> '',
				'conyuge_profesiones_id'=> '',
				'conyuge_estados_civiles_id'=> '',
				//Validacion contrato
				'validarContrato'		=> array(
					'amortizacion' => array(
						'1' =>  array(
							'valorCuotaFija'	=> 437121.25,
							'abonoCapital'		=> 352881.25,
							'intereses'			=> 84240.00,
							'saldo'				=> 4327118.75
						),
						'6' =>  array(
							'valorCuotaFija'	=> 437121.25,
							'abonoCapital'		=> 385804.67,
							'intereses'			=> 51316.59,
							'saldo'				=> 2465116.79
						),
						'12' =>  array(
							'valorCuotaFija'	=> 437121.25,
							'abonoCapital'		=> 429392.19,
							'intereses'			=> 7729.06,
							'saldo'				=> 0.00
						),
					),
					//Valida la fecha generada en financiacion
					'fechaPrimerPagoFinanciacion' => '2011-02-30'
				),
			),
			////////////////////////////
			// CONTRATO10 DE PRUEBAS
			////////////////////////////
			array(
				//Socios
				'tipoContratoId'		=> 1,//Cualquier tipo de contrato
				'fechaCompra'			=> '2009-08-01',
				'tipoDocumentosId'		=> 1,//Cualquiera
				'identificacion'		=> 328580085,
				'apellidos'				=> 'ANGELA',
				'nombres'				=> 'RODRIGUEZ',
				'direccionResidencia'	=> 'Cra 127 Bis #139A-46',
				'ciudad_residencia'		=> 127591,
				'telefonoResidencia'	=> 6936466,
				'celular'				=> 3014114428,
				'envioCorrespondencia'	=> 'S',
				'tipoSociosId'			=> 16,//
				'estadoContrato'		=> 'A',//Activo
				'estadoMovimiento'		=> 'R',//reserva
				'estadosCivilesId'		=> 2,
				'cambioContrato'		=> 'N', //Nooo
				//Si es activar reserva
				'reservas_contrato'		=> false,
				//Si es cambio de contrato
				'nuevo_contrato'		=> false,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 4573000,
				'cuota_inicial'			=> 1600550,
				'derecho_afiliacion_id'	=> 4,//135000
				//Detalle cuota
				'hoy'					=> 823140,
				'fecha1'				=> '2009-08-01',
				'cuota2'				=> 777410,
				'fecha2'				=> '2009-08-28',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 36,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2009-10-15',
				'premios_id'			=> 0,
				'observaciones'			=> '',
				//Conyuges
				'conyuge_tipo_documentos_id'=> 0,
				'conyuge_identificacion'=> 0,
				'conyuge_nombres'		=> '',
				'conyuge_apellidos'		=> '',
				'conyuge_fecha_nacimiento'=> '',
				'conyuge_direccion'		=> '',
				'conyuge_telefono'		=> '',
				'conyuge_celular'		=> '',
				'conyuge_profesiones_id'=> '',
				'conyuge_estados_civiles_id'=> '',
				//Validacion contrato
				'validarContrato'		=> array(
					'amortizacion' => array(
						'1' =>  array(
							'valorCuotaFija'	=> 112905.04,
							'abonoCapital'		=> 59400.94,
							'intereses'			=> 53504.10,
							'saldo'				=> 2913049.06
						),
						'6' =>  array(
							'valorCuotaFija'	=> 112905.04,
							'abonoCapital'		=> 64942.98,
							'intereses'			=> 47962.06,
							'saldo'				=> 2599615.95
						),
						'12' =>  array(
							'valorCuotaFija'	=> 112905.04,
							'abonoCapital'		=> 72280.12,
							'intereses'			=> 40624.92,
							'saldo'				=> 2184659.77
						),
					),
					//Valida la fecha generada en financiacion
					'fechaPrimerPagoFinanciacion' => '2009-10-15'
				),
			),
		);
		//Recorre los contratos creados y almacena su ActiveRecord
		$SociosObj = array();
		foreach($configAll as $config){
			//crear en contrato con base a $config
			$Socios = self::crearContrato($transaction, $config);
			//valida los datos generados segun index 'validarContrato'
			self::validarContrato($transaction, $config);
			//aumentamos consecutivo de contrato
			TPC::aumentarConsecutivoContrato($config['tipoContratoId'], $transaction);
			$SociosObj[]= $Socios;
		}
		return $SociosObj;
	}

	/**
	 * Metodo que limpia la BD de datos
	 */
	static function limpiarBD($transaction){
			//Delete all models
			$models = array('Socios', 'Reservas', 'MembresiasSocios', 'DetalleCuota', 'PagoSaldo', 'Amortizacion', 'Sociosh', 'ReservasDesistimientos', 'MembresiasSociosh', 'DetalleCuotah', 'PagoSaldoh', 'Amortizacionh', 'RecibosPagos', 'DetalleRecibosPagos', 'RecibosPagosh', 'DetalleRecibosPagosh', 'ControlPagos', 'NotaContable', 'NotaHistoria', 'CambioContrato', 'AbonoReservas', 'DetalleAbonoReservas', 'RefinanciarAmortizacion');
			ActiveRecord::disableEvents(true);
			foreach($models as $model){
				$tempModel = self::getModel($model)->setTransaction($transaction);
				if($tempModel->delete(array('conditions'=>'1=1'))==false){
					foreach($tempModel->getMessages() as $message){
						$transaction->rollback($message->getMessage());
					}
				}
			}
			
			//Initialize the autonumerics of contratos
			$tipoContratoObj = self::getModel('TipoContrato')->find();
			foreach($tipoContratoObj as $tipoContrato){
				$tipoContrato->setTransaction($transaction);
				$tipoContrato->setValidar(false);
				$tipoContrato->setNumero(0);
				if($tipoContrato->save()==false){
					foreach($tipoContrato->getMessages() as $message){
						$transaction->rollback($message->getMessage());
					}
				}
			}
			ActiveRecord::disableEvents(false);
			//Initialize autoincremnt of RC y reservas in empresa
			$empresa = self::getModel('Empresa')->setTransaction($transaction)->findFirst();
			$empresa->setCreservas(0);
			$empresa->setCrc(0);
			if($empresa->save()==false){
				foreach($empresa->getMessages() as $message){
					$transaction->rollback($message->getMessage());
				}
			}
		
	}

	/**
	 * Metodo que crea un contrato de prueba
	 */
	static function crearContrato($transaction, &$config){
		ActiveRecord::disableEvents(true);
		$Socios = new Socios();
		$Socios->setTransaction($transaction);
		$tipoContrato = self::getModel('TipoContrato')->findFirst();
		if($tipoContrato==false){
			$transaction->rollback('Debe crear al menos un tipo de contrato');
		}
		$formatoContrato = TPC::getFormatoContrato($transaction, $tipoContrato->getId());
		print "<br>".$formatoContrato;
		$Socios->setValidar(true);
		$Socios->setNumeroContrato($formatoContrato);
		$Socios->setTipoContratoId($config['tipoContratoId']);
		$Socios->setFechaCompra($config['fechaCompra']);
		$Socios->setTipoDocumentosId($config['tipoDocumentosId']);
		$Socios->setIdentificacion($config['identificacion']);
		$Socios->setApellidos($config['apellidos']);
		$Socios->setNombres($config['nombres']);
		$Socios->setDireccionResidencia($config['direccionResidencia']);
		$Socios->setCiudadResidencia($config['ciudad_residencia']);
		$Socios->setTelefonoResidencia($config['telefonoResidencia']);
		$Socios->setCelular($config['celular']);
		$Socios->setEnvioCorrespondencia($config['envioCorrespondencia']);
		$Socios->setTipoSociosId($config['tipoSociosId']);
		$Socios->setEstadoContrato($config['estadoContrato']);
		$Socios->setEstadoMovimiento($config['estadoMovimiento']);
		//$Socios->setEstadosCivilesId($config['estadosCivilesId']);
		$Socios->setCambioContrato($config['cambioContrato']);
		if($Socios->save()==false){
			foreach($Socios->getMessages() as $message){
				$transaction->rollback($message->getMessage());
			}
		}
		$config['Socios'] = $Socios;
		TpcContratos::actualizarSocio($transaction, $config);
		return $Socios;
	}

	/**
	 * Metodo donde se ingresan los cambios de contratoa realizar y sus pruebas
	 */
	static function cambioContratosMain($transaction){
		$configAll = array(
			////////////////////////////
			// CAMBIOCONTRATO1 DE PRUEBAS
			////////////////////////////
			array(
				//Numero Contrato a cambiar
				'numeroContrato'		=> 'TPC-GX-12-1',//Con este buscamos el Activerecord de Socios
				//Si es cambio de contrato (TipodeContratoId Nuevo)
				'nuevo_contrato'		=> 2, 
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 5786770,
				'cuota_inicial'			=> 5786770,
				'derecho_afiliacion_id'	=> 4,//135000
				//Detalle cuota
				'hoy'					=> 5786770,
				'fecha1'				=> '2011-01-01',
				'cuota2'				=> 0,
				'fecha2'				=> '',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 0,
				'interes'				=> 0,
				'fecha_primera_cuota'	=> '2011-09-01',
				'premios_id'			=> 0,
				'observaciones'			=> 'Cambio de Contrato de prueba',
				//Validaciones
				'validationCambioContrato' => array(
					'valorCambioContrato'	=> 0,
					'estadoContrato'		=> 'A',//Activo
					'estadoMovimiento'		=> 'P',//100% pagado
				),
				
			),
			////////////////////////////
			// CAMBIOCONTRATO2 DE PRUEBAS
			////////////////////////////
			array(
				//Numero Contrato a cambiar
				'numeroContrato'		=> 'TPC-GX-12-2',//Con este buscamos el Activerecord de Socios
				//Si es cambio de contrato (TipodeContratoId Nuevo)
				'nuevo_contrato'		=> 2,
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 10100000,
				'cuota_inicial'			=> 7136367,
				'derecho_afiliacion_id'	=> 4,//135000
				//Detalle cuota
				'hoy'					=> 7136367,
				'fecha1'				=> '2011-01-01',
				'cuota2'				=> 0,
				'fecha2'				=> '',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 12,
				'interes'				=> 1.8,
				'fecha_primera_cuota'	=> '2011-02-01',
				'premios_id'			=> 0,
				'observaciones'			=> 'Cambio de Contrato de prueba',
				//Validaciones
				'validationCambioContrato' => array(
					'valorCambioContrato'	=> 0,
					'estadoContrato'		=> 'A',//Activo
					'estadoMovimiento'		=> 'N',//nuevo cambio de contrato
				),
				
			),
			////////////////////////////
			// CAMBIOCONTRATO3 DE PRUEBAS de CAMBIOCONTRATO1
			////////////////////////////
			array(
				//Numero Contrato a cambiar
				'numeroContrato'		=> 'TPC-G-12-1',//Con este buscamos el Activerecord de Socios
				//Si es cambio de contrato (TipodeContratoId Nuevo)
				'nuevo_contrato'		=> 2,//PUNTOS 
				//Membresias
				'membresias_id'			=> 3,
				'temporadas_id'			=> 1,
				'capacidad'				=> 2,
				'puntos_ano'			=> 700,
				'numero_anos'			=> 7,
				'valor_total'			=> 5786770,
				'cuota_inicial'			=> 5786770,
				'derecho_afiliacion_id'	=> 4,//135000
				//Detalle cuota
				'hoy'					=> 5786770,
				'fecha1'				=> '2011-01-01',
				'cuota2'				=> 0,
				'fecha2'				=> '',
				'cuota3'				=> 0,
				'fecha3'				=> '',
				//PagoSaldo
				'numero_cuotas'			=> 0,
				'interes'				=> 0,
				'fecha_primera_cuota'	=> '2011-09-01',
				'premios_id'			=> 0,
				'observaciones'			=> 'Cambio de Contrato de prueba',
				//Validaciones
				'validationCambioContrato' => array(
					'valorCambioContrato'	=> 0,
					'estadoContrato'		=> 'A',//Activo
					'estadoMovimiento'		=> 'P',//100% pagado
				),
				
			),
			
		);

		//Ingresamos los cambios de contrato
		foreach($configAll as $config){
			TpcTests::crearCambioContrato($transaction, $config);
		}
	}

	/**
	 * Metodo que crea un cambio de contrato de prueba
	 * 
	 * @param	ActiveRecordTransaction $transaction
	 * @config	array $config(
	 * 	...
	 * )
	 */
	static function crearCambioContrato($transaction, &$config){
		
		//Buscamos el contrato por su numero de contrato
		$socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst(array('conditions'=>'numero_contrato="'.$config['numeroContrato'].'"'));
		if($socios==false){
			$transaction->rollback('El numero de contrato no existe. '.$config['numeroContrato']);
		}
		$config['Socios']			= $socios;
		$config['fecha_primera_cuota'] = '2011-01-01';
		
		//Aplicamos actualizaciarSocios que se encarga de hacer el cambio de contrato
		TpcContratos::actualizarSocio($transaction, $config);
		
		//$transaction->rollback(print_r($config, true));
	}


	static function notasContablesMain($transaction, $SociosObj){
		print "<br>notasContablesMain:";
		$today = date('Y-m-d');
		$configNotas = array(
			////////////////////////////
			// NOTA1 DE PRUEBAS
			////////////////////////////
			array(
				'identificacion'=> 1070585456, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2012-01-15',
				'debCre'		=> 'D',//Credito
				'onlyCapital'	=> true,
				'notaContable'	=> true,
				'formasPago' => array( 
					'count' => 1, 
					'total' => 100000,
					'totalFPago' => array( 
						'E' => 100000
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 100000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,//Bogota
				'debug' => true
			),
			////////////////////////////
			// NOTA2 DE PRUEBAS
			////////////////////////////
			array(
				'identificacion'=> 1070585456, 
				'fechaRecibo'	=> $today, 
				'fechaPago'		=> '2012-01-15',
				'debCre'		=> 'C',//Credito
				'onlyCapital'	=> true,
				'notaContable'	=> true,
				'formasPago' => array( 
					'count' => 1, 
					'total' => 100000,
					'totalFPago' => array( 
						'E' => 100000
					), 
					'data' => array( 
						'E' => array( 
							array( 
								'formaPago' => 1,
								'numeroForma' => '',
								'valor' => 100000,
								'tipo' => 'E' 
							)
						) 
					) 
				), 
				'validarPago' => array(
					'derechosAfiliacion'	=> 0,
					'valorInicial'			=> 0,
					'totalDias'				=> 0,
					'capital'				=> 0,
					'interes'				=> 0,
					'diasMora'				=> 0,
					'valorMora'				=> 0,
					'saldo'					=> 0
				),
				'cuentasId' => 4, 
				'reciboProvisional' => '',
				'ciudadPago' => 127591,//Bogota
				'debug' => true
			),
		);

		foreach($SociosObj as $Socio){
			print '<br>'.$Socio->getIdentificacion();
			foreach ($configNotas as $config) {

				//Si esta activo
				if($Socio->getEstadoContrato()=='A'){
					//Si el pago es para el contrato con la cedula tal, aplique el abono a ese contrato
					if($Socio->getIdentificacion()==$config['identificacion']){
						//Asignamos id del registro en modelo Socios
						$config['sociosId'] = $Socio->getId();
						print '<br>sociosId: '.$config['sociosId'].', estado: '.$Socio->getEstadoContrato().', debCre: '.$config['debCre'];
						//Aplicamos el pago		
						TPC::addNotaContableContrato($config, $transaction);

						echo ', rcId: ', $config['rcReciboPago'];
					}
				}

			}
		}
	}
}
