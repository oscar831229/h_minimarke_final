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
 * TpcFactura
 *
 * Clase componente que controla procesos que genera las facturas de TPC
 *
 */
class TpcFactura extends UserComponent {


	/**
	* Contiene la transaccion actual del proceso
	*
	* @var ActiveRecordTransaction $_transaction
	*/
	protected $_transaction;


	public function __contruct()
	{

	}

	/**
	* Genera las facturas de TPC
	*
	* @param array $config
	*/
	public function makeFactura(&$config)
	{

		#Validacion
		$listaValidar = array(
			array('name' => 'socios', 'message' => 'Es necesario indicar un array con los socios a generar factura'),
		);
		TPC::validateInArray($listaValidar, $config, null);

		$transaccion = TransactionManager::getUserTransaction();

		#Recorremos socios a generar su factura
		$sociosArray = $config['socios'];
		foreach ($sociosArray as $sociosId) 
		{

			if ($sociosId) {

				#Verificamos si socio existe
				$socios = $this->Socios->findFirst($sociosId);

				if ($socios!=false) {

					try 
					{
						#Obtenemos el estado de cuenta
						$estadoCuenta = array(
							'sociosId' => $sociosId,
							'periodo' => $config['periodoFin']
						);
						$estadoCuenta = $this->getEstadoDeCuenta($estadoCuenta);

						#creamos registro de cuenta de cobro
						$cuentaCobro = new CuentaCobro();
						$cuentaCobro->setTransaction($transaccion);
						$cuentaCobro->setSociosId($sociosId);
						$cuentaCobro->setNumeroContrato($socios->getNumeroContrato());
						$cuentaCobro->setPeriodo($estadoCuenta['periodo']);
						$cuentaCobro->setFechaCorte($estadoCuenta['fechaCorte']);
						$cuentaCobro->setFechaLimitePago($estadoCuenta['fechaLimitePago']);
						$cuentaCobro->setValorDerechoAfiliacion($estadoCuenta['valorDerechoAfiliacion']);
						$cuentaCobro->setValorCuotaInicial($estadoCuenta['valorCuotaInicial']);
						$cuentaCobro->setValorCuotaFinanciacion($estadoCuenta['valorCuotaFinanciacion']);
						$cuentaCobro->setSaldoDerechoAfiliacion($estadoCuenta['saldoDerechoAfiliacion']);
						$cuentaCobro->setSaldoCuotaInicial($estadoCuenta['saldoCuotaInicial']);
						$cuentaCobro->setSaldoCuotaFinanciacion($estadoCuenta['saldoCuotaFinanciacion']);
						$cuentaCobro->setBaseCorriente($estadoCuenta['baseCorriente']);
						$cuentaCobro->setBaseMora($estadoCuenta['baseMora']);
						$cuentaCobro->setDiasCorriente($estadoCuenta['diasCorriente']);
						$cuentaCobro->setValorCorriente($estadoCuenta['valorCorriente']);
						$cuentaCobro->setDiasMora($estadoCuenta['diasMora']);
						$cuentaCobro->setValorMora($estadoCuenta['valorMora']);
						$cuentaCobro->setValorCapital($estadoCuenta['valorCapital']);
						$cuentaCobro->setPagoMinimo($estadoCuenta['pagoMinimo']);
						$cuentaCobro->setPagoTotal($estadoCuenta['pagoTotal']);
						$cuentaCobro->setConsecutivo($estadoCuenta['consecutivo']);

						if ($cuentaCobro->save()==false) {
							foreach ($cuentaCobro->getMessages() as $message) 
							{
								throw new Exception($message->getMessage());
							}
						}

						#Aumentamos consecutivo de cuenta de cobro
						$this->_aumentarConsecutivoCuentaCobro($transaccion);

					}
					catch(Exception $e) {
						#???
					}

				} else {
					throw new Exception("No existe el socios con codigo ".$sociosId, 1);
				}
			} else {
				throw new Exception("No hay un sociosId", 1);
			}

		}

		$transaccion->commit();

	}

	/**
	* Obtiene la información de estado de cuenta del periodo abierto
	*
	* @param array $estadoCuenta
	*/
	protected function getEstadoDeCuenta(&$estadoCuenta)
	{
		#Validamos
		$listaValidar = array(	
			array('name' => 'sociosId', 'message' => 'Es necesario indicar un array con los socios a generar factura'),
		);
		TPC::validateInArray($listaValidar, $estadoCuenta, null);

		$consecutivo = Settings::get('consecutivo_cuenta_cobro', 'TC');

		$sociosId = $estadoCuenta['sociosId'];

		$socios = EntityManager::get('Socios')->findFirst($sociosId);
		if ($socios==false) {
			throw new Exception("No existe el socio con id '$sociosId'", 1);			
		}

		#Obtenemos el periodo actual
		$periodoActual = TPC::periodoActual();
		
		#Obtine el último día de periodo
		$ano = substr($periodoActual, 0, 4);
		$mes = substr($periodoActual, 4, 5);
		$fechaLimite = "$ano-$mes-01";
		$fechaLimiteUltimoDiaDate = Date::getLastDayOfMonth($mes, $ano);
		$fechaLimiteUltimoDia = $fechaLimiteUltimoDiaDate->getDate();

		#Estado de cuenta
		$estadoCuenta['fecha'] = TPC::sanitizeFecha360($fechaLimiteUltimoDia);
		TPC::estadoCuenta($estadoCuenta, null);

		#Obtenemos la fecha limite de pago
		$diasLimitePagoFactura = Settings::get('limite_dias_pago', 'TC');
		if (!$diasLimitePagoFactura) {
			throw new Exception("NO se ha configurado el límite de días de plazo para pagar la factura", 1);			
		}
		$fechaLimitePago = $fechaLimiteUltimoDiaDate->addDays($diasLimitePagoFactura);

		#Estructura de estado de cuenta
		$estadoCuentaNew = array(
			'numeroContrato' 			=> $socios->getNumeroContrato(),
			'fechaCorte'				=> $fechaLimiteUltimoDia,
			'periodo'					=> $periodoActual,
			'fechaLimitePago'			=> $fechaLimitePago,
			'valorDerechoAfiliacion'	=> 0,
			'valorCuotaInicial'			=> 0,
			'valorCuotaFinanciacion'	=> 0,
			'saldoDerechoAfiliacion'	=> 0,
			'saldoCuotaInicial'			=> 0,
			'saldoCuotaFinanciacion'	=> 0,
			'diasCorriente' 			=> 0,
			'valorCorriente'			=> 0,
			'diasMora'					=> 0,
			'valorMora'					=> 0,
			'valorCapital'				=> 0,
			'baseMora'					=> 0,
			'baseCorriente'				=> 0,
			'pagoMinimo'				=> 0,
			'pagoTotal'					=> 0,
			'consecutivo'				=> $consecutivo
		);

		#Si debe derecho de Afiliación
		$debeDerechoAfiliacion = TPC::debeDerechoAfiliacion($sociosId);
		$estadoCuentaNew['valorDerechoAfiliacion'] = TPC::getValorDerechoAfiliacion($sociosId);
		if ($debeDerechoAfiliacion==True) {
			$estadoCuentaNew['saldoDerechoAfiliacion'] = $estadoCuenta['valorDerechoAfiliacion'];
		}

		#Sacamos valor y saldo de cuota inicial
		$dataCuotaInicial = TPC::getValorCuotaInicial($sociosId);
		$estadoCuentaNew['valorCuotaInicial'] 	=  $dataCuotaInicial['valor'];
		$estadoCuentaNew['saldoCuotaInicial'] 	=  $dataCuotaInicial['saldo'];

		#Financiación
		$estadoFinanciacion = TPC::getValorFinanciacion($sociosId,null);
		$estadoCuentaNew['valorCuotaFinanciacion'] = $estadoFinanciacion['valor'];
		$estadoCuentaNew['saldoCuotaFinanciacion'] = $estadoFinanciacion['saldo'];

		$estadoCuentaNew['diasCorriente'] = $estadoCuenta['estadoCuenta']['totalDias'];
		$estadoCuentaNew['valorCorriente'] = $estadoCuenta['estadoCuenta']['interecesCorrientesLiquidacion'];

		$estadoCuentaNew['diasMora'] = $estadoCuenta['estadoCuenta']['diasMora'];
		$estadoCuentaNew['valorMora'] = $estadoCuenta['estadoCuenta']['interesesMora'];

		$estadoCuentaNew['pagoMinimo'] = $estadoCuenta['estadoCuenta']['debePagar'];
		$estadoCuentaNew['pagoTotal'] = $estadoCuenta['estadoCuenta']['debePagar'];

		#Obtenemos los intereces base
		$pagoSaldo = EntityManager::get('PagoSaldo')->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		if ($pagoSaldo==false) {
			throw new Exception("No existe información en pago saldo del socios con id '$sociosId'");			
		}
		$estadoCuentaNew['baseCorriente'] = $pagoSaldo->getInteres();

		$estadoCuentaNew['baseMora'] = TPC::getTasaDeMora($fechaLimiteUltimoDia, null, null);

		//throw new Exception(print_r($estadoCuentaNew,true));
		

		return $estadoCuentaNew;
		
	}

	/**
	* Incrmenta +1 el consecutivo de cuenta de cobro en Settings
	*/
	private function _aumentarConsecutivoCuentaCobro($transaccion)
	{
		$consecutivo = $this->Configuration->setTransaction($transaccion)->findFirst(array('conditions'=>"name='consecutivo_cuenta_cobro' AND application='TC'"));
		
		if ($consecutivo==false) {
			throw new Exception("No existe la configuracion de 'consecutivo de cuentas de cobro'");			
		}

		$value = $consecutivo->getValue();
		$value += 1;
		$consecutivo->setValue($value);

		if ($consecutivo->save()==false) {
			foreach ($consecutivo->getMessages() as $message) 
			{
				throw new Exception($message->getMessage());				
			}
		}

	}

}