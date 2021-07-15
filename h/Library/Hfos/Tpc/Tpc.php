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

/**
 * TPC
 *
 * Clase central que controla procesos internos de tpc centralizandolos procesos
 * utiles para la aplicación
 *
 */
class TPC extends UserComponent {

	private $socios_id;

	//Contiene el id del contrato actual
	private $_socios_tpc_id;

	private $_debug;

	public function __construct() {
		//Inicializamos socios_tpc_id
		$this->socios_id = null;
	}

	public function index() {

	}

	/*************************************
	* CALCULOS FINANCIEROS
	**************************************/
	static function validarCampos(&$data=array()) {
		//Campos
		$listaCampos = array(
			'valorTotalCompra',
			//'valorCuotasIniciales',
			//'valorFinanciacion',
			'fechaCompra',
			'fechaPagoFinanciacion',
			'plazoMeses',
			'tasaMesVencido',
			'tasaMora'
		);

		$flag = true;
		foreach ($listaCampos as $campo) {
			if (!isset($data[$campo])) {
				$flag = false;
			}
		}
		return $flag;
	}

	/**
	 *
	 * Metodo que calcula la cuota fija mensual de Valor Total Compra
	 * @param $data (
	 *	P: Monto del préstamo.
	 *	i: (TEM) tasa de interés efectiva mensual.
	 *	n: número de cuotas del crédito.
	 * )
	 *
	 * @return Float
	 */
	static function calcularCuotaFijaMensual($data=array()) {
		$logger = new Logger('File', 'TpcClass.log');
		$flag =false;
		if (!isset($data["P"]) || !is_numeric($data["P"])) {
			$flag = true;
			$logger->log("Falta Monto a calcular cuota fija mensual (P)");
		}
		if (!isset($data["n"]) || !is_numeric($data["n"])) {
			$flag = true;
			$logger->log("Falta numero de cuotas (n)");
		}

		$P = $data["P"];
		$i = $data["i"];
		if (!empty($i) && $i>0) {
			$i = $data["i"]/100; //1.3/100=0.013
		} else {
			$i = 0;
		}
		$n = $data["n"];

		//R: cuota fija mensual a pagar sin gastos
		if ($i>0) {
 			$R = -($i * $P * pow((1 + $i), $n) / (1 - pow((1 + $i), $n)));
		} else {
			//Sin interes es una division de valor por numero de cuotas
			if ($n>0) {
				$R = $P / $n ;
			}else{
				$R = $P;
			}
		}
		return $R;
	}


	/**
	 * Genera una tabla de amortizacion
	 *
	 * $data = array(
	 *	 "valorTotalCompra"	  => 3500000,
	 *	 "fechaCompra"			=> "23-06-2009",
	 *	 "fechaPagoFinanciacion" => "30-07-2009",
	 *	 "plazoMeses"			=> 60,
	 *	 "tasaMesVencido"		=> 1.80,
	 *	 "tasaMora"			  => 2.00
	 * );
	 * @return array
	 */
	static function generarAmortizacion(&$data) {

		$logger = new Logger('File', 'TpcClass.log');
		if (!isset($data['debug'])) {
			$data['debug']=false;
		}

		$amortizacion = array();

		// Se valida que los campos esten completos
		$camposCompletos = TPC::validarCampos($data);

		if ($camposCompletos==true) {

			//Se calcula valores adiciones de cuotas
			if (!isset($data['valorFinanciacion'])) {
				$data['valorCuotasIniciales'] = $data['valorTotalCompra'] * 0.33;
				$data['valorFinanciacion'] = $data['valorTotalCompra'] - $data['valorCuotasIniciales'];
			}
			if ($data['valorFinanciacion']>0) {
			
				$cuotaFijaArray = Array(
					'P' => $data['valorFinanciacion'],
					'i' => $data['tasaMesVencido'],
					'n' => $data['plazoMeses']
				);
				//Calculamos la cuota fija mensual
				$cuotaFija = TPC::calcularCuotaFijaMensual($cuotaFijaArray);
				if ($data['debug']==true) {
					$logger->log("cuotaFija: $cuotaFija");
				}
				//interés
				$interes = $data["tasaMesVencido"] /100;
				if ($data['debug']==true) {
					$logger->log("interés: $interes = ".$data["tasaMesVencido"]." /100;");
				}
				//Saldo actual
				$saldo = $data['valorFinanciacion'];
				if ($data['debug']==true) {
					$logger->log("saldo: $saldo = ".$data['valorFinanciacion']);
				}
				//Validamos febrero apra q no se descuadre
				if (!$data["fechaPagoFinanciacion"]) {
					if (isset($data['fechaCompra'])==true) {
						print_r($data);
						//$data['fechaPagoFinanciacion'] = $data['fechaCompra']->getDate();
					}
				}
				try{
					list($y, $m, $d) = explode('-', $data["fechaPagoFinanciacion"]);
				}catch(Exception $e) {
					throw new Exception("genAmortizacion:".print_r($data,true), 1);
				}
				$fechaPrimerPago = $y.'-'.$m.'-'.$d;
				//validacion de cuotas por 15 o 30 de cada mes segun fecha de primera cuota
				$fechaCorte = 0;
				//15 ó 30
				if ($data['debug']==true) {
					$logger->log("fechaPrimerPago: $fechaPrimerPago");
				}
				if ($d <= 15) {
					$fechaCorte = 15;
				}else{
					$fechaCorte = 30;
				}
				//Recorremos el plazo en meses
				$n = $data['plazoMeses'];
				for($i=1;$i<=$n;$i++) {
					//damos formato a fecha de periodo
					$fechaPeriodo = $fechaPrimerPago;
					list($year,$month,$day) = explode('-',$fechaPeriodo);
					if ($day>30) {
						$day=30;
					}
					if ($day<=15) {
						$day=15;
					}
					if ($day>15) {
						$day=30;
					}
					$fechaPeriodo = $year.'-'.$month.'-'.$fechaCorte;
					if ($data['debug']==true) {
						$logger->log("fechaPeriodo: $fechaPeriodo");
					}
					//interesesPeriodo
					$interesesPeriodo = $saldo * $interes;
					if ($data['debug']==true) {
						$logger->log("interesesPeriodo: $interesesPeriodo = $saldo * $interes;");
					}
					$abonoCapital = $cuotaFija-$interesesPeriodo;
					if ($data['debug']==true) {
						$logger->log("abonoCapital: $abonoCapital = $cuotaFija - $interesesPeriodo;");
					}
					//Descontamos abono capital a saldo actual
					if ($i == $n) {
						$saldo = $abonoCapital;
					}
					$saldo -= $abonoCapital;
					if ($saldo < 0) {
						$saldo = 0.00;
					}
					if ($data['debug']==true) {
						$logger->log("saldo: $saldo -= $abonoCapital;");
					}
					//datos calculados en periodo
					$calculos = Array(
						"cuota"			=> $i,
						"periodo"		=> $fechaPeriodo,
						"cuotaFija"		=> $cuotaFija,
						"abonoCapital"	=> $abonoCapital,
						"intereses"		=> $interesesPeriodo,
						"saldo"			=> $saldo,
						"pagado"		=> 0
					);

					//Agregamos 30 dias a una fecha de corte
					$fechaPrimerPago = TPC::addDaysToDate($fechaPeriodo,30);
					//$fechaPrimerPago = TPC::subDaysToDate($fechaPeriodo,30);

					//Añadimos a fila de amortizacion para periodo $i
					$amortizacion[]=$calculos;

					unset($calculos);
				}

				if (isset($data['debug']) && $data['debug']==true) {
					$logger->log(print_r($amortizacion,true));
				}
			}
		}

		return $amortizacion;
	}



	/**
	 * Metodo que obtiene la amortizacion de un contrato y la convierte en array asociativo
	 * @param int $socios_tpc_id, es el id de el contrato
	 * @param $transaction
	 * @return array
	 */
	static function getAmortizacion($socios_id=false, $transaction) {
		if (!$socios_id) {
			throw new Exception("Para obtener la amortización es necesario dar el id de el contrato");
		}
		$amortizacionA = array();
		//Obtenemos amortizacion
		$amortizacion = EntityManager::get('Amortizacion');
		$amortizacion->setTransaction($transaction);
		$amortizacionFind = $amortizacion->find(array('conditions'=>'socios_id='.$socios_id));
		if (count($amortizacionFind) > 0) {
			$cuotaActual=0;
			//Obtenemos el array de amortizacion de la BD y lo volvemos Array
			//para hacer la busqueda
			foreach ($amortizacionFind as $amortizacionObj) {
				$n = $amortizacionObj->getNumeroCuota();
				$amortizacionA[$n]['cuota'] = $n;
				$amortizacionA[$n]["cuotaFija"] = $amortizacionObj->getValor();
				$amortizacionA[$n]["capital"] = $amortizacionObj->getCapital();
				$amortizacionA[$n]['saldo'] = $amortizacionObj->getSaldo();
				$amortizacionA[$n]["fecha"] = $amortizacionObj->getFechaCuota();
				$amortizacionA[$n]["estado"] = $amortizacionObj->getEstado();
			}
		}
		return $amortizacionA;
	}

	/**
	 * Busca el rango de pago en una amortizacion para saber su estado de pagos
	 *
	 * @param $socios Es el activerecord de Socios
	 * @param float $pago Es el valor del pago actual
	 * @param $transaction
	 *
	 * @return array
	 */
	static function buscarRangoPagoEnAmortizacion($socios, $pago=0, &$data, $transaction) {
		if (isset($data['logger'])) {
			$logger = $data['logger'];
		}else{
			$logger = new Logger('File', 'dummy.log');
		}
		$logger->log('buscarRangoPagoEnAmortizacion >> init');
		$actualCapital = array();
		$amortizacionA = array();
		if (!$socios || $socios->getId() <= 0) {
			throw new Exception('Abono: Es necesario dar el SocioId');
		}
		//Obtenemos amortizacion
		$cuotaActual = 0;
		//Control pagos para saber el saldo actual
		$controlPagos = EntityManager::get('ControlPagos')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado IN ("V","K","N")','order'=>'id DESC'));
		//Ahora buscamos el saldo de el contrato para saber
		//en que capital esta este saldo lo sacamos de amortizacion
		$conditions = '';
		if ($controlPagos==false) {
			//mas que todo cuando es primer pago
			$conditions = 'socios_id='.$socios->getId().' AND estado="D"';
			$order = 'numero_cuota ASC';
		}else{
			//Cuando usa el saldo del control pagos para saber el estado en capital
			$saldo = LocaleMath::round($controlPagos->getSaldo(), 0);
			if ($saldo==0) {
				if (isset($data['setValidar']) && $data['force']==false) {
					///
					return false;
				} else {				
					throw new Exception('Abono: El saldo actual del contrato es '.$saldo);
				}
			}
			$conditions = 'socios_id='.$socios->getId().' AND round(saldo, 0) < '.$saldo;
			$data['calculos']['conditionEstadoCapital']= $conditions = 'socios_id='.$socios->getId().' AND round(saldo, 0) < '.$saldo;
			$order = 'numero_cuota ASC';
		}
		$amortizacion = EntityManager::get('Amortizacion')->setTransaction($transaction)->findFirst(array('conditions'=>$conditions,'order'=>$order));
		if ($amortizacion==false) {
			throw new Exception('Abono: No se encontro pago en amortizacion con esta condicion: '.$conditions);
		}
		$n = $amortizacion->getNumeroCuota();
		$logger->log('cuota in amortizacion: '.$n);
		
		$amortizacionA = array();
		$amortizacionA['id']		= $amortizacion->getId();
		$amortizacionA['cuota']		= $n;
		$amortizacionA['cuotaFija']	= $amortizacion->getValor();
		$amortizacionA['capital']	= $amortizacion->getCapital();
		$amortizacionA['saldo']		= $amortizacion->getSaldo();
		$amortizacionA['fecha']		= $amortizacion->getFechaCuota();
		$amortizacionA['estado']	= $amortizacion->getEstado();
		$amortizacionA['pagado']	= $amortizacion->getPagado();
		//almacenamos saldo de cuota anterior
		if ($n>1) {
			$cuotaAnterior = $n-1;
		}else{
			$cuotaAnterior = 1;
		}
		$amortizacionAnterior = EntityManager::get('Amortizacion')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND numero_cuota='.$cuotaAnterior));
		$data['calculos'][]='amortizacionAnterior: socios_id='.$socios->getId().' AND numero_cuota='.$cuotaAnterior;
		$amortizacionA['saldoAnterior']	= $amortizacionAnterior->getSaldo();
		$amortizacionA['fechaAnterior']	= $amortizacionAnterior->getFechaCuota();
		$actualCapital				= $amortizacionA;
		
		$saldoAmortizacion = $amortizacion->getSaldo();
		if (count($actualCapital)==0) {
			$actualCapital = $amortizacionA[1];
		}
		$logger->log('buscarRangoPagoEnAmortizacion >> Se encontró la cuota actual en que esta la amortización '.$n.', amortizacionA: '.print_r($amortizacionA, true));
		$logger->log('buscarRangoPagoEnAmortizacion >> fin');
		return $actualCapital;
	}


	/**
	 * Metod que busca el valor de amortización por la fecha de pago
	 *
	 * @param ActiveRcord $socios de socios_tpc
	 * @param string $fechaPago es la fecha que se busca
	 * @return Array con datos de pagos actual
	 */
	static function buscarRangoFechaEnAmortizacion($socios=false,$fechaPago='',$data=array()) {
		$logger = new Logger('File', 'TpcClass.log');
		if (!$socios->getId()) {
			$logger->log('para busca amortización por fecha se necesita el numero de contrato');
			return false;
		}
		if (empty($fechaPago)) {
			$logger->log('para busca amortización por fecha se necesita la fecha a buscar');
			return false;
		}
		//Buscamos amortizaciones de esa cuenta
		$amortizacion = new Amortizacion();
		$amortizaciones = $amortizacion->find("socios_id=".$socios->id,"order: id ASC");
		$actualCapital = array();
		$amortizacionA = array();
		//Recorremos y convertimos a array los datos de amortizacion
		foreach ($amortizaciones as $amortizacionObj) {
			if (!$amortizacionObj->id) {
				$logger->log('El contrato no tiene generado la amortización');
			}else{
				$n = $amortizacionObj->numero_cuota;
				$amortizacionA[$n]['cuota'] = $n;
				$amortizacionA[$n]["cuotaFija"] = $amortizacionObj->valor;
				$amortizacionA[$n]["capital"] = $amortizacionObj->capital;
				$amortizacionA[$n]['saldo'] = $amortizacionObj->saldo;
				$amortizacionA[$n]["fecha"] = $amortizacionObj->fecha_cuota;
				$amortizacionA[$n]["estado"] = $amortizacionObj->estado;
			}
		}

		$cuotaActual = Array();
		//Buscamos el rango de fecha en la amortizacion
		$fechaPagoTime = strtotime($fechaPago);

		foreach ($amortizacionA as $cuota => $amortizacion) {
			//Time de fecha de amortizacion
			$fechaAmortizacionActualTime = strtotime($amortizacion["fecha"]);
			$msg = "fechaAmortizacionActualTime: $fechaAmortizacionActualTime, ";
			//existe una mas adelante
			$msg .= "cuota: $cuota, ";
			$msg .= "fechaPago: $fechaPago, ";
			$msg .= "AmortizacionFecha: {$amortizacion["fecha"]}, ";
			if (isset($amortizacionA[$cuota+1])) {
				//Si esta en rango entre una adelante y la actual
				$fechaAmortizacionNextTime = strtotime($amortizacionA[$cuota+1]["fecha"]);
				//Si etsa en rango de de estas dos fechas
				if (isset($data['debug']) && $data['debug']==true) {
					$logger->log("if ($fechaAmortizacionActualTime > $fechaPagoTime && $fechaPagoTime < $fechaAmortizacionNextTime) {");
				}
				if ($fechaAmortizacionActualTime > $fechaPagoTime && $fechaPagoTime < $fechaAmortizacionNextTime) {
					$cuotaActual = $amortizacion;
					break;
				}
				$msg .= "fechaAmortizacionNextTime: $fechaAmortizacionNextTime, ";
			}else{
				//Es la ultima y si no se encontor nada asignamos esta
				if (count($cuotaActual)==0) {
					$cuotaActual = $amortizacion;
					break;
				}
			}
			$msg .= "fechaPagoTime: $fechaPagoTime";
			if (isset($data['debug']) && $data['debug']==true) {
				$logger->log($msg);
			}

		}
		return $cuotaActual;
	}

	/**
	 * Metodo que ordena pagos por fecha. Util para pagos posteriores
	 * @param $pagos Array
	 * @return $pagos Array Sorted
	 */
	static function ordenaPagos(&$formatos=array()) {
		$logger = new Logger('File', 'TpcClass.log');
		$fechas = array();
		$pagosFechas = array();
		//Creamos array con fecha como jey value como index de $pagos
		foreach ($formatos as $n => $pago) {
			$fecha = $pago['fechaPago'];
			$kfecha = strtotime($fecha);
			///echo "<br>($fecha)kfecha=".$kfecha;
			$pagosFechas[$kfecha][] = $pago;
		}
		//Ordenamos por fecha
		$pagosFechasKeys = array_keys($pagosFechas);
		sort($pagosFechasKeys,SORT_ASC);
		//Creamos ordenadamente los pagos
		foreach ($pagosFechasKeys as $kfecha2) {
			foreach ($pagosFechas[$kfecha2] as $nU) {
				$fechas[]=$nU;
			}
		}
		$logger->log(str_replace(PHP_EOL,'<br/>',print_r($fechas,true)));
		//exit;
		$formatos = $fechas;
		return $fechas;
	}

	/**
	 * Calcula el detalle de un pago
	 */
	static function calcularDetallePago($pago=array(), $data=array()) {

		$saldo = $data['valorFinanciacion'];

		//Ordenamos pagos Anteriores
		$pagosAnteriores = self::ordenaPagos($pagosAnteriores);

		//buscamos ultimo pago
		$ultimoPago = $pagosAnteriores[count($pagosAnteriores)-1];

		//buscamos la cuota actual del saldo
		$cuotaActual = self::buscarRangoPagoEnAmortizacion($data["amortizacion"], $pago["valor"], $data);
		if ($cuotaActual===false) {
			return false;
		}

		$tieneInteres = true;
		//Validamos si debe haber interes por capital
		if (strtotime($cuotaActual["periodo"]) < strtotime($pago["fecha"])) {
			if ($data['debug']==true) {
				$logger->log("Validamos si debe haber interés por capital > ".
				"(".$cuotaActual["periodo"].")".strtotime($cuotaActual["periodo"]) .">".
				strtotime($pago["fecha"])."(".$pago["fecha"].")>>>>".
				print_r($cuotaActual,true));
			}

			//Validamos si debe haber interés por mora
			$ultimaPagoFecha = strtotime($ultimoPago["fecha"]);
			$ultimaPagoFechaMas30dias = mktime(0,0,0,date("m",$ultimaPagoFecha),date("d",$ultimaPagoFecha)+30,date("Y",$ultimaPagoFecha));
			if ($ultimaPagoFechaMas30dias >= strtotime($pago["fecha"])) {
				if ($data['debug']==true) {
					$logger->log("Validamos si debe haber interés por mora >".
					"(".date("d-m-Y",$ultimaPagoFechaMas30dias).")".$ultimaPagoFechaMas30dias .">=".
					strtotime($pago["fecha"])."(".$pago["fecha"].")");
				}
				$tieneInteres = false;
			}

		}

	}


	/**
	 * Valida y gestiona calculos de un pago
	 */
	static function generarCalculoPago($pago=array(),$data=array()) {

		$logger = new Logger('File', 'TpcClass.log');
		//Se valida que los campos esten completos
		$camposCompletos = self::validarCampos($data);

		$calculos = Array();

		if (!isset($pago["fecha"])) {
			$logger->log("debe ingresar un pago");
		}else{
			if ($camposCompletos == true) {
				if (isset($data["amortizacion"])) {
					self::calcularDetallePago($pago,$data);
				}else{
					$logger->log("Es necesario adicionar la amortización");
				}
			}
		}

		return $calculos;
	}


	/**
	 * Metodo que busca en Iterese_usura el valor de interés de mora para la fecha actual
	 *
	 * @param string $fecha, es la fecha dondel se busca le periodo si no se digita
	 * coge la de hoy solo formato dd-mm-yyyy
	 *
	 * @return $tasaDeMora
	 */
	static function getTasaDeMora($fecha=false, $data=array(), $transaction) {
		if (!$fecha) {
			$fecha = date('Y-m-d');
		}
		$tasaDeMora=0;
		$conditions = "'$fecha' >= fecha_inicial AND '$fecha' <= fecha_final";
		$interesUsura = EntityManager::get('InteresUsura')->findFirst(array('conditions' => $conditions));
		if ($interesUsura!=false) {
			$tasaDeMora = $interesUsura->getInteresTrimestral();
			if ($tasaDeMora<0) {
				throw new Exception('Hay que darle un valor al interés de mora para ese periodo en el formulario intereses de mora');
			}
		}else{
			throw new Exception('Hay que crear el registro de interés de mora para ese periodo en el formulario intereses de mora');
		}
		return $tasaDeMora;
	}

	/**
	 * Calucla segun pagos en que valor de capital esta y
	 * valida los intereces que debe tener segun fecha de capital y de pago
	 *
	 * @param $socios ActiveRecord de Socios
	 * @param $data: Son los datos calulados hasta el momento array
	 * @param $transaction
	 */
	static function calcularIntereses($socios, &$data, $transaction) {
		$logger = $data['logger'];
		$logger->log('calcularIntereses >> init');
		if (!$transaction) {
			$transaction = TransactionManager::getUserTransaction();
		}
		//Buscamos si hay un pago abonado en la amortizacion
		$amortizacion = EntityManager::get('Amortizacion')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado="D"', 'order'=>'numero_cuota ASC'));
		if ($amortizacion==false && !isset($data['notaContable'])) {
			//Verificamos si ya pago todo el socio de su contrato
			//$pagado100 = self::verifica100PorcientoPago($socios, $data, $transaction);
			throw new Exception('No se encontro una cuota de amortización por pagar, debe estar al 100%');
		}
		//Primero miramos en que capital esta el contrato
		$estadoCapital = self::buscarRangoPagoEnAmortizacion($socios, $data['valorPago'], $data, $transaction);
		if (!isset($estadoCapital['fecha'])) {
			if (isset($data['setValidar']) && $data['force']==false) {
				return false;
			} else {
				throw new Exception('BuscarRangoPagoEnAmortizacion no encontro el rango de saldo en que esta este pago: '.print_r($estadoCapital,true));
			}
		}
		$data['estadoCapital'] = $estadoCapital;
		//determinamos si debe o aplicarse interés segun fecha de capital
		$fechaCapital	= $estadoCapital['fecha'];
		$fechaPago		= $data['fechaPago'];
		$diasAldia		= TPC::calculoDias($fechaPago, $fechaCapital);
		//Validamos si esta al dia con capital
		$data['alDiaCapital'] 	= false;
		//30-02-2010 > 30-03-2010
		if ($data['aunEstaEnPagosIniciales']==true) {
			$data['alDiaCapital'] = true;
		}
		//Si la fecha de la del estado de capital es mayor a la fecha del pago debe estar al dia
		if (TPC::dateGreaterThan($estadoCapital['fecha'], $fechaPago)==true) {
			$data['alDiaCapital'] = true;
		}
		$logger->log('calcularIntereses >> estadoCapital: '.print_r($estadoCapital, true).', fechaCapital: '.$fechaCapital.', fechaPago: '.$data['fechaPago'].', diasAldia: '.$diasAldia.'. alDiaCapital: '.$data['alDiaCapital']);
		//Buscamos los datos de membresias
		$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId()));
		//Se calcula valores adiciones de cuotas
		$data['valorTotalCompra']		= $membresiasSocios->getValorTotal();
		$data['valorCuotasIniciales']	= $membresiasSocios->getCuotaInicial();
		$data['valorFinanciacion']		= $data['valorTotalCompra'] - $data['valorCuotasIniciales'];
		//Verificamos si hay saldo a favor para agregar a financiación
		if ($estadoCapital['pagado']>0 && $estadoCapital['estado']=='D') {
			$logger->log('calcularIntereses >> Se agrego a favor de lo abonado en el pago anterior a la cuota actual '.$estadoCapital['pagado'].' a valorFinanciacion: '.$data['valorFinanciacion']);
			$data['valorFinanciacion'] += $estadoCapital['pagado'];
			//Ya habiendo usado el saldo a favor limpiamos el pagado
			$amortizacion = self::getModel('Amortizacion')->setTransaction($transaction)->findFirst($estadoCapital['id']);
			if ($amortizacion!=false) {
				$amortizacion->setPagado(0);
				if ($amortizacion->save()==false) {
					foreach ($amortizacion->getMessages() as $message) {
						throw new Exception($message->getMessage());
					}
				}
			}
		}
		//Luego buscamos el ultimo pagos hechos anteriormente de un contrato que tenga estado V(Normal) o N(Nota Contable)
		$reciboPagos = EntityManager::get('RecibosPagos')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado IN ("V","N")','order'=>'fecha_pago DESC'));
		//$controlPagos = EntityManager::get('ControlPagos')->setTransaction($transaction)->findFirst(array('conditions'=>"rc='{$recibosPagos->getRc()}'"));
		//$data['saldoActual'] = $controlPagos->getSaldo();
		
		//Buscamos por control pagos los datos para calcular intereces
		$controlPagosArray = EntityManager::get('ControlPagos')->setTransaction($transaction)->find(array('conditions'=>'socios_id='.$socios->getId(),'order'=>'fecha_pago DESC'));
		$fechaUltimoPago = '';
		//Si la cuota de amortizacion es 1 se debe utilizar no la fecha de ultimo pago sino la fecha de pagoacorrdado
		$fechaPrimerPagoFinanciacionMenos30Time = 0;
		$pagoSaldo = EntityManager::get('PagoSaldo')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId()));
		$fecha1raCuotaAcordada = $pagoSaldo->getFechaPrimeraCuota();
		$data['fecha1raCuotaAcordada'] = $fecha1raCuotaAcordada;
		//throw new Exception($reciboPagos->getFechaPago());
		if ($estadoCapital['cuota']==1) {
			//buscamos si existe un recibo de pago a la cuota 1
			$reciboPagosA1 = EntityManager::get('RecibosPagos')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado IN ("V","N") AND cuota_saldo=1'));
			//Si es el primer pago o si no se ha abonado nada a amortizacion
			if ($reciboPagosA1==false) {
				list($year,$month,$day) = explode('-',$fecha1raCuotaAcordada);
				$fechaUltimoPago = TPC::subDaysToDate($fecha1raCuotaAcordada,30);
				//Si sobro de cuota inicial colocar primera fecha de financiacion
				if ($data['sobroDeCuotaInicial']==true) {
					$fechaUltimoPago = $fecha1raCuotaAcordada;
				}
				//throw new Exception($fechaPago.', '.$fechaUltimoPago);
				$logger->log('calcularIntereses >> Se asigno la fecha del último recibo de caja según la fecha inicial pactada de amortización ('.$estadoCapital['cuota'].'/'.$estadoCapital['fecha'].'), fechaUltimoPago: '.$fechaUltimoPago);
			}else{
				//Obtenemos los dias de diferencia desde el ultimo pago a hoy
				if ($reciboPagos!=false) {
					$fechaUltimoPago = $reciboPagos->getFechaPago();
					//Si la fecha del ultimo pago es menor la fecha de la primera cuota de amortizacion no coja la fecha del ultimo pago sino la de la primera fecha de pago de amortizacion
					if (TPC::dateGreaterThan($fecha1raCuotaAcordada, $fechaUltimoPago)==true) {
						$fechaUltimoPago = $fecha1raCuotaAcordada;
						$logger->log('calcularIntereses >> El contrato actual si tiene pagos anteriores(1); pero la fecha es menor a 1ra cuota de financiación. Por esto se asigno esta fecha  fechaUltimoPago: '.$fechaUltimoPago);
					}else{
						$logger->log('calcularIntereses >> El contrato actual si tiene pagos anteriores(1);  fechaUltimoPago: '.$fechaUltimoPago);
					}
				}else{
					//Si es pago posterior y es primer pago no sabe su ultimo recibo de caja
					if ($fecha1raCuotaAcordada) {
						$fechaUltimoPago = $fecha1raCuotaAcordada;
					}else{
						throw new Exception('calcularIntereses >> no se encontro recibos de caja ultimos');
					}
				}
			}
		}else{
			//throw new Exception('a3');
			if ($reciboPagos!=false) {
				//Obtenemos los dias de diferencia desde el ultimo pago a hoy
				$fechaUltimoPago = $reciboPagos->getFechaPago();
				if ($data['debug']==true) {
					$logger->log('calcularIntereses >> El contrato actual si tiene pagos anteriores(2); fechaUltimoPago: '.$fechaUltimoPago);
				}
			}
		}
		//restamos 30 dias al al primera fecha de amortizacion
		$fechaPrimerPagoFinanciacionMenos30 = TPC::subDaysToDate($fecha1raCuotaAcordada,30);
		$data['fechaUltimoPago'] = $fechaUltimoPago;
		$logger->log('calcularIntereses >> fechaPrimerPagoFinanciacionMenos30: '.$fechaPrimerPagoFinanciacionMenos30);
		//Ya teniendo las fechas debemos calcular los dias transcurridos
		//entre esas dos fecha (hoy - fechaInicio de cuotas o ultimo pago)
		$fechaUltimoRc = $fechaUltimoPago;
		//Calculamos los intereces corrientes
		if ($estadoCapital['cuota']==1) {
			//buscamos si existe un recibo de pago a la cuota 1
			$reciboPagosA1 = EntityManager::get('RecibosPagos')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado IN ("V","N") AND cuota_saldo>=1', 'order'=>'fecha_pago DESC'));
			//Si es el primer pago o si no se ha abonado nada a amortizacion
			if ($reciboPagosA1!=false) {
				$fechaUltimoRc = $reciboPagosA1->getFechaPago();
				$data['fechaUltimoRc'] = $fechaUltimoRc;
			}
		}
		//Calculamos los total dias
		if ($data['sobroDeCuotaInicial']==false) {
			//validamos que la fecha del ultimo recibo de caja es menor a la primera cuota de financiacion coja la de la financiacion
			if ($fecha1raCuotaAcordada!=$fechaUltimoRc) {
				//Si es mayor 1raFinanciacion 
				if (TPC::dateGreaterThan($fecha1raCuotaAcordada,$fechaUltimoRc)==true) {
					$fechaUltimoRc = $fechaPrimerPagoFinanciacionMenos30;
				}
			}
			$dias = TPC::calculoDias($fechaPago,$fechaUltimoRc);
		} else {
			//$dias = 30;//al día
			if (isset($data['identificacion'])==true && $data['identificacion']=='328580083') {
				//throw new Exception('<br>fechaUltimoPago:'.$fechaUltimoPago.', fechaPago: '.$fechaPago.', calucloDias: '.TPC::calculoDias($fechaPago,$fechaUltimoPago).', fechaPrimerPagoFinanciacionMenos30: '.$fechaPrimerPagoFinanciacionMenos30.', calculodias2: '.TPC::calculoDias($fechaPago,$fechaPrimerPagoFinanciacionMenos30));
			}
			//$dias = TPC::calculoDias($fechaPago,$fechaUltimoPago);
			$dias = TPC::calculoDias($fechaPago,$fechaPrimerPagoFinanciacionMenos30);
		}
		//Si los dias son menores 0 se asigna cero a total dias
		if ($dias<0) {
			$dias=0;
		}
		$diasTranscurridos = $dias;
		$data['totalDias'] = $dias;
		if (isset($data['identificacion'])==true && $data['identificacion']=='328580084' && $fechaPago=='2011-03-30') {
			//throw new Exception("$dias = TPC::calculoDias($fechaPago,$fechaUltimoRc);");
		}
		$saldoActual = 0;
		//Validamos la exitencia de saldo de ese contrato
		if ($pagoSaldo != false) {
			$controlPagosSaldo = EntityManager::get('ControlPagos');
			$controlPagosSaldo->setTransaction($transaction);
			//Buscamos solo los pagos normales y abonos a capital o Nota contable
			$controlPagosSaldo = $controlPagosSaldo->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado IN("V", "K", "N")','order'=>'id DESC'));
			//Sino existe el pago coge el valor a finaciar
			if ($controlPagosSaldo == false) {
				$saldoActual = $membresiasSocios->getSaldoPagar();
			}else{
				//Si existe un pago anterior coge el valor dle pago anterior
				$saldoActual = $controlPagosSaldo->getSaldo();

			}
		}else{
			throw new Exception('El contrato actual no tiene saldo a pagar....');
		}
		$logger->log('calcularIntereses >> dias:'.$dias.'(fechaPago: '.$fechaPago.', fechaUltimoPago:'.$fechaUltimoPago.'), diasTranscurridos: '.$diasTranscurridos.', saldoActual: '.$saldoActual);
		//Calculamos el valor de la tasa mes vencido en 30 dis
		$tasaMesVencidoDiaria = ($pagoSaldo->getInteres()/100)/30;
		$logger->log('calcularIntereses >> tasaMesVencidoDiaria:'.$tasaMesVencidoDiaria.'; calculado: '.$tasaMesVencidoDiaria.' = ('.$pagoSaldo->getInteres().'/100)/30)');
		//Obtenemos el total dias de la anterior cuota para acumular al interés corriente
		$totalDiasCuotaPasada = 1;
		//Si existe un pago anterior asignele ese valor total dias para intereses corrientes
		if (isset($controlPagosArray[1])) {
			$controlPagoPenultimo = $controlPagosArray[1];
			$totalDiasCuotaPasada = $controlPagoPenultimo->getDiasCorriente();
		}
		$data['fechaPrimerPagoFinanciacionMenos30Time'] = $fechaPrimerPagoFinanciacionMenos30Time = $fechaUltimoPago;
		$diferenciaFechaPrimerPagoYHoy = $diasTranscurridos;
		$logger->log('calcularIntereses >> fechaPrimerPagoFinanciacionMenos30Time:'.$fechaPrimerPagoFinanciacionMenos30Time.', diferenciaFechaPrimerPagoYHoy: '.$diferenciaFechaPrimerPagoYHoy);
		//Total Dias
		$totalDias = 0;
		$totalDias = $diferenciaFechaPrimerPagoYHoy;
		//Calculamos los intereces corrientes
		if ($estadoCapital['cuota']==1) {
			//buscamos si existe un recibo de pago a la cuota 1
			$reciboPagosA1 = EntityManager::get('RecibosPagos')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado IN ("V","N") AND cuota_saldo=1'));
			//Si es el primer pago o si no se ha abonado nada a amortizacion
			if ($reciboPagosA1==false) {
				$interecesCorrientesLiquidacion = $membresiasSocios->getSaldoPagar() * $tasaMesVencidoDiaria * $totalDias;
				$logger->log("calcularIntereses >> interecesCorrientesLiquidacion: $interecesCorrientesLiquidacion =  ".$membresiasSocios->getSaldoPagar()." * $tasaMesVencidoDiaria * $totalDias;");
			}else{
				$interecesCorrientesLiquidacion = $saldoActual * $tasaMesVencidoDiaria * $totalDias;
				$logger->log("calcularIntereses >> interecesCorrientesLiquidacion: $interecesCorrientesLiquidacion =  $saldoActual * $tasaMesVencidoDiaria * $totalDias;");
			}
			if ($interecesCorrientesLiquidacion<0) {
				$interecesCorrientesLiquidacion=0;
			}
			$logger->log("calcularIntereses >> interecesCorrientesLiquidacion: $interecesCorrientesLiquidacion");
			//Se asigna esal dia el capital por ser primera cuota y esta entre la fecha acordada
			$fechCuotaTime = $estadoCapital['fecha'];
			//Si es primera cuota le deja en estado de capital true solo si:
			//1. la fecha no pasa la acordada en al cuota 1
			//2. El saldo actual es menor al saldo de cuota de amortizacion 1
			//Resultado: Esta al dia en capital
			if (TPC::dateGreaterThan($fechaPago,$fechCuotaTime)==false && $estadoCapital['saldo'] >= $saldoActual) {
				$data['alDiaCapital']=true;
			}
			$logger->log('calcularIntereses >> alDiaCapital: '.$data['alDiaCapital']);
		}else{
			//Con los datos del ultimo pago hecho se usa un nuevo saldo
			$interecesCorrientesLiquidacion =  $saldoActual * $tasaMesVencidoDiaria * $totalDias;
			if ($data['debug']==true) {
				$logger->log("calcularIntereses >> interecesCorrientesLiquidacion: $interecesCorrientesLiquidacion =  $saldoActual * $tasaMesVencidoDiaria * $totalDias;");
			}
		}
		//Obtenemos el valor de Tasa de Mora del pago
		$tasaDeMora = self::getTasaDeMora($data['fechaPago'], $data, $transaction);
		$tasaDeMoraOld = $tasaDeMora;
		$tasaDeMora = $tasaDeMora/100;
		$logger->log("calcularIntereses >> tasaDeMora: $tasaDeMora = $tasaDeMoraOld/100;");
		$logger->log("calcularIntereses >> valorFinanciacion=".$data['valorFinanciacion']);
		//Sacamos la tasa de mora diaria por 30 dias
		$tasaDeMoraDiaria = $tasaDeMora/30;
		$logger->log("calcularIntereses >> tasaDeMoraDiaria: $tasaDeMoraDiaria = $tasaDeMora/30;");
		//Calculamos los dias de mora de 30 dias de un pago a otro pago
		$diasMora = 0;
		//Buscamos en capital los dias de mora, solo cuando esta al dia en capital busca en amortización. Si no lo esta coge al fecha del ultimo recibo de caja
		$estadoCapitalMora = TPC::buscarRangoPagoEnAmortizacion($socios, 0, $data, $transaction);
		//validar estado alDiaCapital si fecha ultimo pago es menor a fecha de capital
		if (TPC::dateGreaterThan($fechaPago, $estadoCapitalMora['fecha'])==false) {
			$data['alDiaCapital'] = true;
		}else{
			$data['alDiaCapital'] = false;
		}
		//Decidimos si la fecha de la cuota en capital actual esta al dia o no
		$fechaCapitalMora = $estadoCapitalMora['fecha'];
		$totalDiasMora = 0;
		//Si esta al dia en capital no debe cobrar mora
		if (isset($data['alDiaCapital']) && $data['alDiaCapital']==true) {
			
		}else{
			//buscamos si existe un recibo de pago a la cuota 1
			$reciboPagosA1 = EntityManager::get('RecibosPagos')->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado IN ("V","N") AND cuota_saldo=1'));
			//Si es el primer pago o si no se ha abonado nada a amortizacion
			if ($reciboPagosA1==false) {
			}else{
				//Solo si la fecha ultimo pago es mayor la fecha de estado capital se aplica para no aplicar doble mora
				$logger->log("if (TPC::dateGreaterThan($fechaUltimoPago, ".$estadoCapitalMora['fecha'].")==true) { \$estadoCapitalMora['fecha'] = $fechaUltimoPago;");
				//throw new Exception("if (TPC::dateGreaterThan($fechaUltimoPago, ".$estadoCapitalMora['fecha'].")==true) { \$estadoCapitalMora['fecha'] = $fechaUltimoPago;");
				$data['calculos'][]="if (TPC::dateGreaterThan($fechaUltimoPago, {$estadoCapitalMora['fecha']})==true) {";
				if (TPC::dateGreaterThan($fechaUltimoPago, $estadoCapitalMora['fecha'])==true) {
					$fechaCapitalMora = $estadoCapitalMora['fecha'] = $fechaUltimoPago;
				}
			}
			$totalDiasMora = TPC::calculoDias($data['fechaPago'],$fechaCapitalMora);
			$data['calculos'][]="totalDiasMora($totalDiasMora) = TPC::calculoDias({$data['fechaPago']},$fechaCapitalMora)";
			
		}
		
		if ($totalDiasMora<0) {
			$totalDiasMora=0;
		}
		$date['diasMora'] = $diasMora = $totalDiasMora;
		$data['totalDiasMora'] = $totalDiasMora;
		$data['fechaCapitalMora'] = $fechaCapitalMora;

		$logger->log('calcularIntereses >> totalDiasMora: '.$totalDiasMora.', fechaCapitalMora: '.$fechaCapitalMora.', fechaPago:'.$data['fechaPago'].', alDiaCapital: '.$data['alDiaCapital']);
		if ($estadoCapital['cuota']==1) {
			//como es primer dia se coge el total dias y le resta 30 siempre :D
			// si es primera cuota
			//buscamos si existe un recibo de pago a la cuota 1
			$reciboPagosA1 = EntityManager::get('RecibosPagos')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado IN ("V","N") AND cuota_saldo=1'));
			//Si es el primer pago o si no se ha abonado nada a amortizacion
			if ($reciboPagosA1==false) {
				//Esta bien la fecha de primera cuota de amortizacion
			}else{
				//Debemos usar la ultima vez que se abono, su fecha
				$fechaCapitalMora = $fechaUltimoPago;
			}
			$diasMora = TPC::calculoDias($data['fechaPago'],$fechaCapitalMora);
			$logger->log('calcularIntereses >> diasMora: '.$diasMora.', fechaCapitalMora: '.$fechaCapitalMora.', amortizacionPagado: '.$amortizacion->getPagado().', cuota 1');
		}else{
			//Si en el segundo pago esta en mora todos los toalDias se asignan a diasMora
			if ($data['alDiaCapital']==false && $data['aunEstaEnPagosIniciales']==false) {
				$diasMora = $totalDiasMora;
				//Si esta en mora y dice que no tiene mora se le aplica el valor de los total días
				if ($diasMora==0) {
					//$diasMora = $diasTranscurridos;
				}
			}else{
				//Si esta no esta en mora se le dan los diasMora iguales a total dias (NEW) 28-02-2011
				if ($data['alDiaCapital']==true && $data['aunEstaEnPagosIniciales']==false) {
					$sePuedeCalcularMora = TPC::dateGreaterThan($fechaPago,$fechaCapitalMora);
					//Si la fecha de amortizacion del capital actual es menor la fecha de pago se aplica validacion
					//de mora aun estando al dia
					if ($sePuedeCalcularMora==true) {
						//Si esta al dia en capital pero se retrazo en la fecha de capital por mas de un mes
						//se resta 30 dias a la resta entre fechas de capital y pago
						if ($totalDiasMora > 30) {
							$diasMora = $totalDiasMora - 30;
							if ($diasMora > $totalDias) {
								$diasMora = $totalDias;
							}
						}else{
							//SI no es mas de 30 dias se asigna cero a la mora
							$diasMora = 0;
						}
					}else{
						$diasMora = 0;
					}
					$logger->log('calcularIntereses >> sePuedeCalcularMora: '.$sePuedeCalcularMora.', diasMora: '.$diasMora);
				}else{
					$diasMora = $totalDiasMora;
					$logger->log('calcularIntereses >> diasMora: '.$diasMora);
				}
			}
		}
		if ($diasMora<0) {
			$diasMora=0;
		}
		//Si los dias de mora son mayores a los dias de totaldias intereces corrientes se asigna el valor de totoalDias
		$logger->log('calcularIntereses >> aunEstaEnPagosIniciales: '.$data['aunEstaEnPagosIniciales'].', valorPago: '.$data['valorPago'].', diasMora: '.$diasMora);
		//Si es solo a capital
		if (isset($data['onlyCapital']) && $data['onlyCapital']==true && !isset($data['notaContable'])&& !isset($data['setValidar'])) {
			if ($diasMora>0) {
				throw new Exception('El contrato actual presenta mora y no puede hacer el abono a capital');
			}
			$diasMora = $dias = $diasAldia = $totalDias = $interecesCorrientesLiquidacion = $totalDiasMora = 0;
		}
		//Se calcula interés de mora (Es pago saldo valor poque es el valor de saldo anterior no el primero)
		$interesesMora = 0;
		if ($diasMora>0) {
			// Si el pago alcanso a finaciacion 1
			if ($data['aunEstaEnPagosIniciales']==false) {
				$interesesMora = ($saldoActual * abs($tasaDeMoraDiaria - $tasaMesVencidoDiaria) * $diasMora);
				$logger->log("calcularIntereses >> interesesMora: $interesesMora = $saldoActual * ($tasaDeMoraDiaria - $tasaMesVencidoDiaria) * $diasMora;");
			}
		}
		
		$data['calculos'][]="interesesMora($interesesMora) = ($saldoActual * abs($tasaDeMoraDiaria - $tasaMesVencidoDiaria) * $diasMora);";
		//Calculamos los intereces corrientes aplicados
		$interecesCorrientesAplicados = 0;
		if ($data['valorPago']>0) { //Si el valorPago es mayor a cero
			//Si el interés corriente liquidado es mayor al pago
			//if ($interecesCorrientesLiquidacion > $data['valorPago']) {
			if (($interecesCorrientesLiquidacion + $interesesMora) > $data['valorPago']) {
				$interecesCorrientesAplicados = $data['valorPago'] - $interesesMora;
				$logger->log("calcularIntereses >> interecesCorrientesAplicados : $interecesCorrientesAplicados = ".$data['valorPago']." - $interesesMora;");
			}else{
				$interecesCorrientesAplicados = $interecesCorrientesLiquidacion;
				$logger->log("calcularIntereses >> interecesCorrientesAplicados : $interecesCorrientesAplicados");
			}
		}
		//Calculamos la diferencia
		if (isset($data['onlyCapital']) && $data['onlyCapital']==true) {
			$diferencia = 0;
		} else {
			$diferencia = $interecesCorrientesLiquidacion - $interecesCorrientesAplicados;	
		}
		
		$logger->log("calcularIntereses >> diferencia : $diferencia = $interecesCorrientesLiquidacion - $interecesCorrientesAplicados");
		//Calculamos el capital
		//se usa valor pago por si se dejo algo de otros lugares como derechos de afiliacion o cuotas iniciales ahi mismo
		$sumaCapital = $data['valorPago'] - $interesesMora - $interecesCorrientesLiquidacion;
		$capital = 0;
		if ($sumaCapital > 0) {
			$capital = $sumaCapital;
		}
		$logger->log("calcularIntereses >> capital : $capital, sumaCapital: $sumaCapital = ".$data['valorPagado']." - $interesesMora - $interecesCorrientesLiquidacion;");
		//Ahora calculamos la mora no cancelada
		$sumaMoraNoCancelada = LocaleMath::round(($capital + $interecesCorrientesAplicados + $interesesMora), 2);
		$moraNoCancelada = 0;
		$data['moraNoCanceladaCalculo'] = "if ($sumaMoraNoCancelada > {$data['valorPago']}(".($sumaMoraNoCancelada>LocaleMath::round($data['valorPago'])).") && {$data['alDiaCapital']} == false) {";
		if ($sumaMoraNoCancelada>LocaleMath::round($data['valorPago'],2) && $data['alDiaCapital'] == false) {
			$moraNoCancelada = $data['valorPago'] - $interecesCorrientesAplicados - $interesesMora;
			$data['moraNoCanceladaCalculo'] .=  "$moraNoCancelada = {$data['valorPago']} - $interecesCorrientesAplicados - $interesesMora;";
		}
		$logger->log("calcularIntereses >> moraNoCancelada : $moraNoCancelada = ".$data['valorPago']." - $interecesCorrientesAplicados - $interesesMora;");
		
		//Agregamos las condonaciones al saldo
		if (isset($data['porcentCondonacion']) && $data['porcentCondonacion']>0) {
			if (!is_numeric($data['porcentCondonacion'])) {
				throw new Exception('calcularIntereses >> El valor de la condonacion debe ser numerico, no se aplico la condonación');
			}else{
				//Si hay mora se decuenta condonación
				if ($interesesMora>0) {
					//se resta el valor a interes de condonacion
					$interesesMora2 = $interesesMora;
					$interesesMora -= (float) (($interesesMora*$data['porcentCondonacion'])/100);
					//Se verifica que no sea negativo
					if ($interesesMora<0) {
						//Si lo es se asigna cero
						$interesesMora = 0;
					}
					if (strlen($data['detallePago'])>0) {
						$data['detallePago'].=', y ';
					}
					$data['detallePago'] .= 'Se realizó condonación del ('.$data['porcentCondonacion'].'%) a la mora, su nuevo saldo de mora es ('.$moraNoCancelada.')';

					if ($interesesMora==0) {
						$capital += $interesesMora2;
					}

				}

			}
		}

		//Calculamos saldo
		$saldo = $saldoActual - $capital + $diferencia - $moraNoCancelada;

		$data['calculoSaldo'] = "$saldo = $saldoActual - $capital + $diferencia - $moraNoCancelada;";
		//Aqui validamos los saldos pequeos para colocarlos en cero este caso para negativos
		// saldo < 0 y saldo > -5000
		if ($saldo < 0) {
			$saldo = 0;
		}
		$logger->log("calcularIntereses >> Saldo: $saldo = $saldoActual  - $capital + $diferencia - $moraNoCancelada");
		//calculamos valor_financiacion
		$valor_financiacion = $capital + $interesesMora + $interecesCorrientesAplicados;
		$logger->log("calcularIntereses >> valor_financiacion: $valor_financiacion = $capital + $interesesMora + $interecesCorrientesAplicados;");
		//Almacenamos la informacion de intereces
		$data['saldoActual']						= $saldoActual;
		$data['diasTranscurridos']					= $diasTranscurridos;
		$data['fechaHoy']							= $fechaPago;
		if (is_object($fechaUltimoPago)) {
			$fechaUltimoPago = $fechaUltimoPago->getDate();
		}
		$data['fechaUltimoPago']					= $fechaUltimoPago;
		if (is_object($fechaPrimerPagoFinanciacionMenos30)) {
			$fechaPrimerPagoFinanciacionMenos30 = $fechaPrimerPagoFinanciacionMenos30->getDate();
		}
		$data['fechaPrimerPagoFinanciacionMenos30']	= $fechaPrimerPagoFinanciacionMenos30;
		$data['totalDias']							= $diferenciaFechaPrimerPagoYHoy;
		$data['tasaMesVencidoDiaria']				= $tasaMesVencidoDiaria;
		$data['valorFinanciacion']					= $membresiasSocios->getSaldoPagar();
		$data['diasMora']							= $diasMora;
		$data['tasaMora']							= $tasaDeMora;
		$data['tasaMoraDiaria']						= $tasaDeMoraDiaria;
		//Almacenamos lo importante aca
		$data['capital']							= $capital;
		//$data['totalDias']							= $diferenciaFechaPrimerPagoYHoy;
		$data['interecesCorrientesLiquidacion']		= $interecesCorrientesLiquidacion;
		$data['interecesCorrientesAplicados']		= $interecesCorrientesAplicados;
		$data['diferencia']							= $diferencia;
		$data['diasMora']							= $diasMora;
		$data['interesesMora']						= $interesesMora;
		$data['moraNoCancelada']					= $moraNoCancelada;
		$data['saldo']								= $saldo;
		$data['valorInicial']						= $data['valorInicial'];
		$data['valorFinanciacion']					= $valor_financiacion;
		$data['detallePago']						= $data['detallePago'];
		//Almacenamos la amortizacion
		$data['amortizacion'] = self::getAmortizacion($socios->getId(), $transaction);
		//guardamos los pagos anteriores
		$data['pagosAnteriores'] = $controlPagosArray;
		$logger->log('calcularIntereses >> fin');
		//throw new Exception(print_r($data['detallePago'].'aaa', true));
		return $data;
	}

	/**
	 * Metodo que paga memebresias de un contrato
	 *
	 * @param $data = array(
	 *	membresiaSocios: ActiveRecordObject
	 *	,valorPago: Valor actual del pago
	 *	,valorCuoafi: Acumulador de valor Cuota de afiliacion
	 *	,detallePago: string de mensajes de pago
	 * )
	 * @param $transaction
	 *
	 * @return $data
	 */
	static function pagarMembresias(&$membresiaSocios, &$data, $transaction) {
		$logger = $data['logger'];
		//Datos
		$valorPago 		= $data['valorPago'];
		$valorCuoafi 	= $data['valorCuoafi'];
		$detallePago 	= $data['detallePago'];
		$data['estadoCuotaAfiliacion']=true;
		//Obtenemos el saldo a pagar de la membresia
		$saldoMembresia = $membresiaSocios->getDerechoAfiliacion()->getValor() - $membresiaSocios->getAfiliacionPagado();
		//Verifica si en membresia se han pagado o se debe
		$logger->log('');
		if ($membresiaSocios->getEstadoCuoafi()=='D') {
			//Si el valor del pago es menor al saldo de membresia todo se va al pago de membresia
			if ($valorPago<$saldoMembresia) {
				//Abonamos a pago de afiliacion del valor de pago
				$membresiaSocios->setAfiliacionPagado($membresiaSocios->getAfiliacionPagado() + $valorPago) ;
				//El valor de la cuota de afiliacion es el valor del pago
				$valorCuoafi 	= $valorPago;
				$detallePago	.= 'Abono a Derechos de Afiliación';
				$valorPago 		= 0;
			}else{
				//Si el valor del pago es mayor o igual a la membresia se paga toda la membresia
				if ($valorPago>=$saldoMembresia) {
					//Aumentamos lo pagado a la afiliación del saldo de la membresia
					$membresiaSocios->setAfiliacionPagado($membresiaSocios->getAfiliacionPagado() + $saldoMembresia);
					//Aumentamos valor de Cuota de afiliación
					$valorCuoafi += $saldoMembresia;
					//Se cambia estado a pagado
					$membresiaSocios->setEstadoCuoafi('P');
					//Descontamos de valor pago el saldo de la membresia
					$valorPago 		-= $saldoMembresia;
					$detallePago 	.= 'Pago de los Derechos de Afiliación ';
					$data['estadoCuotaAfiliacion']=false;
				}
			}
		}
		$data['valorPago'] 		= $valorPago;
		$data['valorCuoafi'] 	= $valorCuoafi;
		$data['detallePago'] 	= $detallePago;
		if ($membresiaSocios->save()==false) {
			foreach ($membresiaSocios->getMessages() as $message) {
				throw new Exception('pagarMembresias: '.$message->getMessage());
			}
		}
		$logger->log('pagarMembresias >> detallePago: '.$data['detallePago'].', valorPago: '.$valorPago);
		//throw new Exception(print_r($data));
		return $data;
	}

	/**
	 * Metodo que paga las cuotas iniciales de un contrato
	 * @param $detalleCuota: Activerecord
	 * @param $data: Array
	 * @param $transaction
	 */
	static function pagarCuotasIniciales($detalleCuota, &$data, $transaction) {
		$logger = $data['logger'];
		//Validamos Campos
		$msg = '';
		$flagCampos = false;
		$campos = array('valorPago', 'valorInicial', 'detallePago');
		foreach ($campos as $campo) {
			if (!isset($data[$campo])) {
				$flagCampos=true;
				$msg .= 'Falta campo '.$campo.' en Tpc::pagarCuotas';
			}
		}
		if ($flagCampos==true) {
			throw new Exception($msg);
		}
		//Lista de campos de cuotas
		$cuotasField = Array(
			//HOY
			array(
				'estado'		=> 'estado1',
				'cuota'		 	=> 'hoy',
				'cuotaPagada'	=> 'hoy_pagado'
			),
			//Cuota2
			array(
				'estado'		=> 'estado2',
				'cuota'		 	=> 'cuota2',
				'cuotaPagada'	=> 'cuota2_pagado'
			),
			//Cuota3
			array(
				'estado'		=> 'estado3',
				'cuota'		 	=> 'cuota3',
				'cuotaPagada'	=> 'cuota3_pagado'
			)
		);
		//throw new Exception(print_r($data, true));
		//Segun calculos de intereces define cuanto tenemos para capital
		$valorPago			= $data['valorPago'];
		$valorInicial		= $data['valorInicial'];
		$detallePago		= $data['detallePago'];
		$detallePagoArray	= array();
		$detalleAbonoArray	= array();
		//Quedo para capital
		if ($data['debug']==true) {
			$logger->log('Se va a pagar a cuotas el valor = '.$valorPago);
		}
		//Conteo de cuotas
		$n=1;
		$sociosId = $detalleCuota->getSociosId();
		$detallePagoS = 'Pago de la(s) Cuota(s) Inicial(es) No(s).';
		$totalCuotasIniciales = 0;
		//Recorremos cuotas y aplicamos sus repsectivo pagos
		foreach ($cuotasField as $cuota) {
			//validamos si el pago es mayor a cero sino no deja abonar a otro lado
			if ($valorPago<=0) {
				break;
			}
			if ($data['debug']==true) {
				$logger->log("($n)valorPago=".$valorPago);
			}
			//Verificamos ahora los pagos de Cuotas a pagar y asigna valores
			//Si debe la cuota 1 ('D' debe)
			if ($data['debug']==true) {
				$logger->log('cuotaEstado='.$detalleCuota->readAttribute($cuota['estado']));
			}
			if ($detalleCuota->readAttribute($cuota['estado'])=='D') {
				//Entro a pagar
				$data['pagoCuotasIniciales'] = true;
				//Obtenemos el saldo de la cuota n
				$saldoDetalleCuota = $detalleCuota->readAttribute($cuota['cuota']) - $detalleCuota->readAttribute($cuota['cuotaPagada']);
				//throw new Exception($saldoDetalleCuota);
				//Si el valor del pago es menor a el saldo de cuota
				if ($valorPago<$saldoDetalleCuota) {
					//Abonamos a el valor pago a la cuota
					$detalleCuota->writeAttribute($cuota['cuotaPagada'],
						($detalleCuota->readAttribute($cuota['cuotaPagada']) + $valorPago)
					);
					//Abonamos a valor inicial el valor del Pago
					$valorInicial += $valorPago;
					//Inicializamos valor Pago ya que se gasto el dinero
					$valorPago=0;
					//Agregamos comentario al detalle del abono
					$detalleAbonoArray[]='Abono a la Cuota Inicial '.$n;
				}else{
					//Ahora si el valor del pago es mayor o igual al saldo de la cuota
					if ($valorPago>=$saldoDetalleCuota) {
						//Abonamos al pago de hoy el saldo de la cuota
						$detalleCuota->writeAttribute($cuota['cuotaPagada'],
							($detalleCuota->readAttribute($cuota['cuotaPagada']) + $saldoDetalleCuota)
						);
						//Se abona al valor inicial el saldo de cuota
						$valorInicial += $saldoDetalleCuota;
						//Cambiamos el estado de la cuenta 1 a pagada
						$detalleCuota->writeAttribute($cuota['estado'], 'P');
						//descontamos del pago el valor de la cuota
						$valorPago -= $saldoDetalleCuota;
						//Si la cuota es menor o igual a cero no necesita detalle
						if ($detalleCuota->readAttribute($cuota['cuota']) && $detalleCuota->readAttribute($cuota['cuota']) > 0) {
							//agregamos detalles de descripcion de abono a cuota
							$detallePagoArray[]=$n;
						}
					}
				}
				//Se guarda las cuotas
				if ($detalleCuota->save()==false) {
					throw new Exception("pagarCuotasIniciales: La cuota inicial $n no fueron modificada");
				}
			}
			//Seguimos con siguiente cuota
			$n++;
		}//END FOREACH

		//Add detalle de pagos
		if (count($detallePagoArray)>0) {
			$detallePago .= 'Pago de la(s) cuota(s) inicial(es) '.implode(', ', $detallePagoArray);
		}
		//Add detalle de abonos
		if (count($detalleAbonoArray)>0) {
			if (count($detallePagoArray)>0) {
				$detallePago .= ' y ';
			}
			$detallePago .=  implode('y ', $detalleAbonoArray);
		}
		//verificamos si todo esta pagado
		$aunEstaEnPagosIniciales = false;
		foreach ($cuotasField as $cuota) {
			if ($detalleCuota->readAttribute($cuota['estado'])!='P') {
				$aunEstaEnPagosIniciales = true;
			}
		}
		
		$data['valorPago'] = $valorPago;
		$data['aunEstaEnPagosIniciales'] = $aunEstaEnPagosIniciales;
		$data['detallePago'] = $detallePago;
		//valor inicial lo qu sobro despues de pagar
		$data['valorInicial'] = $valorInicial;
		$logger->log('pagarCuotasIniciales >> Aun esta debiendo cuotas iniciales? '.$aunEstaEnPagosIniciales.', detallePago: '.$detallePago.', valorPago: '.$valorPago);
		return $aunEstaEnPagosIniciales;
	}

	/**
	 * Metodo que abona cuota de contrato predecesora de pago a membresias
	 *
	 * @param $data = array(
	 *	detalleCuota: ActiveRecordObject
	 *	,valorPago: Valor actual del pago
	 *	,valorInicial: Acumulador de ValorInicial
	 *	,detallePago: string de mensajes de pago
	 * )
	 * @param $transaction
	 *
	 * @return $data
	 */
	static function pagarCuotasAmortizacion($detalleCuota, &$data, $transaction) {
		$logger = $data['logger'];
		$logger->log('pagarCuotasAmortizacion >> init');
		//Validamos Campos
		$msg = "";
		$flagCampos = false;
		$campos = array('valorPago', 'valorInicial', 'detallePago');
		foreach ($campos as $campo) {
			if (!isset($data[$campo])) {
				$flagCampos=true;
				$msg .= PHP_EOL."Falta campo $campo en TPC::pagarCuotas";
			}
		}
		if ($flagCampos==true) {
			throw new Exception($msg);
		}
		//Aqui le agregamos el valor pedonable que son 10000 pesos si el cliente no p0eude pagar una cuota por 10000
		$valorPago		= $data['valorPago'];
		$valorInicial	= $data['valorInicial'];
		$detallePago	= $data['detallePago'];
		//Conteo de cuotas
		$n=1;
		$detallePagoArray		= array();
		$detallePagoAbonoArray	= array();
		try{
			$sociosId = $detalleCuota->getSociosId();
			//estado de si aun esta en pagos iniciales
			$aunEstaEnPagosIniciales = $data['aunEstaEnPagosIniciales'];
			if (!$aunEstaEnPagosIniciales) {
				//Ahora bonamos a capital
				$saldoActual = $data['saldo'];
				//Buscamos la tabla y la ordenamos por cuotas ascendente
				$amortizacionObj = EntityManager::get('Amortizacion')->setTransaction($transaction)->find(array('conditions'=>'socios_id='.$sociosId,'order'=>'numero_cuota ASC'));
				foreach ($amortizacionObj as $amortizacion) {
					if ($valorPago<=0) {
						continue;
					}
					//SI la cuota aun no esta pagada
					if ($amortizacion->getEstado()=='D') {
						//Si esta en una cuota que aun debe de amortizacion y alcanzo a abonar antes debemos sumarle ese valor
						if ($amortizacion->getPagado()>0) {
							$saldoActual -= $amortizacion->getPagado();
						}
						//Se suma lo perdonable si existe por ahora es 0
						//$saldoActual += Globales::getValorPerdonablePago();
						//Saldo > es mayor a saldo de amortizacion se pone en pagada
						if ( LocaleMath::round($saldoActual, 0) <= LocaleMath::round($amortizacion->getSaldo(), 0)) {
							//Detalle de pago
							$detallePagoArray[]= $amortizacion->getNumeroCuota();
							//agregamos a pagado de esa cuota
							$amortizacion->setPagado($amortizacion->getValor());
							//Le descontamos a valor el valor de amortizacion
							$valorPago-=$amortizacion->getValor();
							//Cambiamos el estado de la cuota a pagada
							$amortizacion->setEstado('P');
							//Descontamos del pago lo que se uso para cuota de amortizacion
							if ($amortizacion->save()==false) {
								throw new Exception('No se pudo actualizar la cuota de amortización '.$amortizacion->getNumeroCuota());
							}
						}else{
							//Detalle de pago
							$detallePagoAbonoArray[]='Se abono a cuota de financiación No. '.$amortizacion->getNumeroCuota();
							//agregamos a pagado de esa cuota
							$amortizacion->setPagado($amortizacion->getPagado() + $valorPago);
							$valorPago=0;
							if ($amortizacion->save()==false) {
								throw new Exception('No se pudo actualizar la cuota de amortización '.$amortizacion->getNumeroCuota());
							}
						}
						$logger->log('cuota: '.$amortizacion->getNumeroCuota().", validación: (saldoActual: $saldoActual) <= saldoAmortización:".$amortizacion->getSaldo().": ".((float) ($saldoActual) <= (float) $amortizacion->getSaldo()).", estado: ".$amortizacion->getEstado());
					}
				}
				//detalles
				if (count($detallePagoArray)>0) {
					if (strlen($detallePago)>0) {
						$detallePago .= ', ';
					}
					$detallePago .= 'Pago de cuotas de financiación No. '.implode(', ', $detallePagoArray);
				}
				if (count($detallePagoAbonoArray)>0) {
					if (strlen($detallePago)>0) {
						$detallePago.=', ';
					}
					$detallePago .= implode(', ', $detallePagoAbonoArray);
				}
				$valorPago = 0;
			}
			if (empty($detallePago)) {
				$detallePago .= 'Se abono a cuota '.$amortizacion->getNumeroCuota();
			}
			if ($data['capital']<=0 && $data['aunEstaEnPagosIniciales']==false && $data['totalDiasMora']>0 && $data['interesesMora']>0) {
				if (strlen($detallePago)>0) {
					$detallePago .= ', ';
				}
				$detallePago .= 'Se pago mora y corrientes en cuota ';
			}
			//Actualizamos $data
			$data['valorPago']		= $valorPago;
			$data['valorInicial']	= $valorInicial;
			$data['detallePago']	= $detallePago;
			$logger->log('pagarCuotasAmortizacion >> valorPago: '.$valorPago.', detallePago: '.$detallePago);
			$logger->log('pagarCuotasAmortizacion >> fin');
			return $data;
		}catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Guarda en amortizacion en amortizacionh
	 *
	 * @param $sociosId: id de contrato con amortizacion a copiar
	 * @param $transaction
	 *
	 * @return boolean
	 */
	protected function copiarAmortizacionh($sociosId=false, $transaction) {
		if (!$sociosId) {
			throw new Exception("Para agrega la amortizacion a amortizacionh es necesario la id del contrato");
		}
		//Buscamos la amortizacion de un contrato
		$amortizacion = EntityManager::get('Amortizacion');
		$amortizacion->setTransaction($transaction);
		$amortizacionObj = $amortizacion->find(array('conditions'=>"socios_id='$sociosId'","order"=>"numero_cuota ASC"));
		$flag = true;
		//Recorremos su registros
		foreach ($amortizacionObj as $amortizacionRow) {
			//Copiamos y pegamos en nuevo registro de amortizacionh con insercion segura
			$amortizacionh = new Amortizacionh();
			$amortizacionh->setTransaction($transaction);
			$amortizacionh->socios_id=$amortizacionRow->socios_tpc_id;
			$amortizacionh->numero_cuota=$amortizacionRow->numero_cuota;
			$amortizacionh->valor=$amortizacionRow->valor;
			$amortizacionh->capital=$amortizacionRow->capital;
			$amortizacionh->interes=$amortizacionRow->interes;
			$amortizacionh->saldo=$amortizacionRow->saldo;
			$amortizacionh->fecha_cuota=$amortizacionRow->fecha_cuota;
			$amortizacionh->estado=$amortizacionRow->estado;
			if ($amortizacionh->save() == false) {
				foreach ($amortizacionh->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
				$flag = false;
			}
		}
		//Retorno de flag si funciono o no bien la historia de amortizacion
		return $flag;
	}

	/**
	 * Guarda pago_saldo en pago_saldoh
	 *
	 * @param $sociosId: id de contrato con pagos_saldos a copiar
	 * @param $transaction
	 *
	 * @return boolean
	 */
	protected function copiarPagoSaldoh($sociosId=false, $transaction) {
		if (!$sociosId) {
			throw new Exception("Para agrega la amortizacion a amortizacionh es necesario la id del contrato");
		}
		$flag=true;
		$pagoSaldo = EntityManager::get('PagoSaldo');
		$pagoSaldo->setTransaction($transaction);
		$pagoSaldo->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		if ($pagoSaldo != false) {
			$pagoSaldoh= new PagoSaldoh();
			$pagoSaldoh->setTransaction($transaction);
			$pagoSaldoh->setSociosId($pagoSaldo->getSociosId());
			$pagoSaldoh->setNumeroCuotas($pagoSaldo->getNumeroCuotas());
			$pagoSaldoh->setInteres($pagoSaldo->getInteres());
			$pagoSaldoh->setFechaPrimeraCuota($pagoSaldo->setFechaPrimeraCuota());
			$pagoSaldoh->setPremiosId($pagoSaldo->getPremiosId());
			$pagoSaldoh->setObservaciones($pagoSaldo->getObservaciones());
			if ($pagoSaldoh->save() == false) {
				$flag=false;
				foreach ($pagoSaldoh->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
			}
		}
		//Retorno de flag si funciono o no bn la historia de amortizacion
		return $flag;
	}


	/**
	 * Metodo que actualiza le saldo de un contrato
	 *
	 * @param activeRecord $socios
	 * @param array $data
	 * @param transaction $transaction
	 * @return boolean
	 */
	static function actualizarSaldo($socios, &$data, $transaction) {
		if (!isset($data['valorPago'])) {
			throw new Exception('El valor pago es requerido para actualizar Saldo');
		}
		if (!isset($data['valorPagado'])) {
			throw new Exception('El valor pagado es requerido para actualizar Saldo (Valor de pago total)');
		}
		//Si el valorPago es > a el valorPagadoInicialmente y es mayor a cero
		if ($data['valorPago']>$data['valorPagado'] && $data['valorPago']>0) {
			//Guardamos en historial de amortizacion la amortizacion actual
			$copiadoAmortizacion = $this->copiarAmortizacionh($socios->getId(), $transaction);
			//Guardamos en historial de pagos los pagos actuales
			$copiadoSaldosPagos = $this->copiarPagoSaldoh($socios->getId(), $transaction);
			//Obtenemos Objeto de PagoSaldo de ese contrato
			$pagoSaldo = EntityManager::get('PagoSaldo');
			$pagoSaldo->setTransaction($transaction);
			$pagoSaldo->findFirst(array('conditions'=>"socios_id=".$socios->getId()));
			//membresias de socios
			$membresiasSocios = EntityManager::get('MembresiasSocios');
			$membresiasSocios->setTransaction($transaction);
			$membresiasSocios->findFirst(array('conditions'=>"socios_id=".$socios->getId()));
			//Obtenemos la fecha de Primera cuota
			$fechaPcuota=$pagoSaldo->getFechaPrimeraCuota();
			//Si existe interés
			if ($pagoSaldo->getInteres() > 0) {
				//Calculamos la cuota fija mensual
				$cuotaFija= self::calcularCuotaFijaMensual(Array(
					"P" => $membresiasSocios->getSaldoPagar(),
					"i" => $pagoSaldo->getInteres(),
					"n" => $pagoSaldo->getNumeroCuotas()
				));
				$data["cuotaFija"] = $cuota_fija;
				//Calula el interés
				$data["interes"] = ($membresiasSocios->getSaldoPagar() * ($pagoSaldo->interes/100) );
				//Calcula el capital
				$data["capital"] = $data["cuotaFija"] - $data["interes"];
				//Calculamos el saldo
				$data['saldo'] = $membresiasSocios->getSaldoPagar() - $data["capital"];
			}
			//Si interes es cero
			if ($pagoSaldo->interes==0) {
				//Calculamos la cuota fija mensual
				$cuotaFija= self::calcularCuotaFijaMensual(Array(
					"P" => $membresiasSocios->getSaldoPagar(),
					"i" => $pagoSaldo->getInteres(),
					"n" => $pagoSaldo->getNumeroCuotas()
				));
				$data["cuotaFija"] = $cuota_fija;
				//Calula el interes
				$data["interes"] = $pagoSaldo->getInteres()/100;//0
				//Calcula el capital
				$data["capital"] = $data["cuotaFija"];
				//Calculamos el saldo
				$data['saldo'] = $membresiasSocios->getSaldoPagar() - $data["capital"];
			}
			if ($pagoSaldo->save() == true) {
				if ($data['debug']==true) {
					$logger->log("Se actualizo correctamente pago_saldo");
				}
			}else{
				foreach ($pagoSaldo->getMessages() as $message) {
					throw new Exception("PagoSaldo: ".$message);
				}
			}
		}
	}

	/**
	 * Metodo que crea un registro en la tabla RecibosPagos
	 *
	 * @param array $data(
	 * 		'setValidar' 		: Boolean,	Si usa validacion o no de modelo RecibosPagos
	 * 		'reciboProvisional'	: Alpha,	Si usa registro físico de recibo
	 * 		'sociosId'			: Integer,	Si enlaza el recibo de pago a un contrato
	 * 		'abonoReservasId'	: Integer,	Si enlaza el recibo de pago a un abono de reserva
	 * 		'ciudadPago'		: Integer,	Se da el int del location
	 * 		'fechaPago'			: Date,		Se da la fecha de pago en cartera
	 * 		'fechaRecibo'		: Date,		Se da la fecha de pago para contabilidad
	 * 		'valorPagado'		: float,	Se da el valor total pagado
	 * 		'valorReserva'		: float,	Se da el valor de abono reserva
	 * 		'valorCuoact'		: float,	Se da el valor de la cuota de activación
	 * 		'valorCuoafi'		: float,	Se da el valor de la cuota de afiliación
	 * 		'cuentasId'			: Integer,	Se da el id de la cuenta bancaria
	 * 		'pagoPosterior'		: char,		Se da el estado si es pagos posterior o no (S,N)
	 * 		'detallePago'		: string,	Se da el detalle de concepto de recibo de caja
	 * 		'formasPago'		: Array,	Se da la informacion de detalle de formas de pago post[]
	 * 		'capital'			: float,	Se da el valor a capital en financiación
	 * 		'interecesCorrientesAplicados'	: float,	Se da el valor de los interés corrientes aplicados
	 * 		'interesesMora'		: float,	Se da el valor de la mora palicada a financiación
	 * 		'valorInicial'		: float,	Se da el valor de lo que se pago en cuotas inciales
	 * 		'valorFinanciacion'	: float,	Se da el valor de lo que se pago en financiación
	 * 		'rcReciboPago'		: Integer,	Se da el consecutivo de recibo de pago
	 * )
	 * @param $transaction
	 *
	 * @return ActiveRecord $reciboPago
	 */
	public static function crearRecibosPagos(&$data, $transaction) {
		$logger = $data['logger'];
		$logger->log('crearRecibosPagos >> init');
		//Creamos recibo de pago
		$reciboPago = new RecibosPagos();
		$reciboPago->setTransaction($transaction);
		if (isset($data['reciboProvisional'])) {
			$reciboPago->setReciboProvisional($data['reciboProvisional']);
		}
		if (isset($data['sociosId']) && $data['sociosId'] > 0) {
			$socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($data['sociosId']);
			if ($socios==false) {
				throw new Exception('crearRecibosPagos: El contrato on existe');
			}
			$reciboPago->setSociosId($data['sociosId']);
		}
		if (isset($data['abonoReservasId']) && $data['abonoReservasId'] > 0) {
			$reciboPago->setAbonoReservasId($data['abonoReservasId']);
		}
		if (isset($data['ciudadPago']) && $data['ciudadPago'] > 0) {
			$reciboPago->setCiudadPago($data['ciudadPago']);
		}
		if (isset($data['fechaPago']) && $data['fechaPago']) {
			$reciboPago->setFechaPago($data['fechaPago']);
		}
		if (isset($data['fechaRecibo'])) {
			$reciboPago->setFechaRecibo($data['fechaRecibo']);
		}
		if (isset($data['valorPagado'])) {
			$reciboPago->setValorPagado($data['valorPagado']);
		}
		if (isset($data['valorReserva'])) {
			$reciboPago->setValorReserva($data['valorReserva']);
		}
		if (isset($data['valorCuoact'])) {
			$reciboPago->setValorCuoact($data['valorCuoact']);
		}else{
			$reciboPago->setValorCuoact(0);
		}
		if (isset($data['capital'])) {
			$reciboPago->setValorCapital($data['capital']);
		}
		if (isset($data['interecesCorrientesAplicados'])) {
			$reciboPago->setValorInteresc($data['interecesCorrientesAplicados']);
		}
		if (isset($data['interesesMora'])) {
			$reciboPago->setValorInteresm($data['interesesMora']);
		}
		if (isset($data['valorCuoafi'])) {
			$reciboPago->setValorCuoafi($data['valorCuoafi']);
		}
		if (isset($data['valorInicial'])) {
			$reciboPago->setValorInicial($data['valorInicial']);
		}
		if (isset($data['valorFinanciacion'])) {
			$reciboPago->setValorFinanciacion($data['valorFinanciacion']);
		}
		if (isset($data['valorOtros'])) {
			$reciboPago->setOtros($data['valorOtros']);
		}
		if (isset($data['cuentasId'])) {
			$reciboPago->setCuentasId($data['cuentasId']);
		}
		//Flag si es un pago posterior ('N','S')
		if (isset($data['pagoPosterior'])) {
			$reciboPago->setPagoPosterior($data['pagoPosterior']);
		}else{
			$reciboPago->setPagoPosterior('N');
		}
		if (isset($socios) && $socios->getEstadoMovimiento()!='V') {//si es venta
			$reciboPago->setAplico('N');
		}else {
			$reciboPago->setAplico('S');
		}
		if ($data['debug']==true) {
			$logger->log('detallePago: '.$data['detallePago']);
		}
		$reciboPago->setObservaciones($data['detallePago']);
		//Si es abono a capital
		if (isset($data['onlyCapital']) && $data['onlyCapital']) {

			if (isset($data['notaContable'])) {
				//Nota Contable
				$reciboPago->setEstado('N');//Nota Contable
			} else {
				//Pago a capital
				$reciboPago->setEstado('K');//Capital
			}
		}else{
			//Si hay un estado en especial
			if (isset($data['rcEstado']) && $data['rcEstado']) {
				//Pago normal
				$reciboPago->setEstado($data['rcEstado']);//Estado asignado
			}else{
				//Pago normal
				$reciboPago->setEstado('V');//Normal
			}
		}
		
		//signamos el id generado a rc como el numero de contabilidad del pago
		if (isset($data['rcReciboPago']) && !empty($data['rcReciboPago']) ) {
			//asignamos el rc dado como el numero de contabilidad del pago (comun para pagos posteriores)
			$reciboPago->setRc($data['rcReciboPago']);
		}else{
			//Aumentamos consecutivo de RC
			$nuevoRc = $data['rcReciboPago'] = TPC::aumentarConsecutivoRc($transaction);
			$reciboPago->setRc($nuevoRc);
		}
		//asignamos la cuota que esta pagando segun capital si esta pagando financiación
		if (isset($data['estadoCapital']['cuota'])) {
			$reciboPago->setCuotaSaldo($data['estadoCapital']['cuota']);
		}
		//Definimos si es debito o credito
		if (isset($data['debCre'])) {
			$reciboPago->setDebCre($data['debCre']);
		} else {
			//Debito by default
			$reciboPago->setDebCre('D');
		}

		//Condinacion
		$condonacion = 0.00;
		if (isset($data['porcentCondonacion'])) {
			$condonacion = $data['porcentCondonacion'];
		}
		$reciboPago->setPorcCondonar($condonacion);

		//Almacenamos el proceso de pagos en en un json
		$reciboPago->setCalculos(json_encode($data));

		//throw new Exception(print_r($data,true));

		if (isset($data['setValidar'])) {
			$reciboPago->setValidar((bool) $data['setValidar']);
		}

		//debcre
		$debCre = 'D';
		if (isset($data['debCre']) && $data['debCre']) {
			$debCre = $data['debCre'];
		}
		$reciboPago->setDebCre($debCre);

		//guardamos los datos del recibo de caja
		if ($reciboPago->save()==true) {
			$logger->log('crearRecibosPagos >> reciboPagoRC: '.$reciboPago->getRc());
			//Detalle formas de pago
			if (isset($data['formasPago']['data'])) {
				//recorremos tipos de pagos agrupados
				foreach ($data['formasPago']['data'] as $tipoFP => $formasPago) {
					foreach ($formasPago as $fp) {
						//throw new Exception('<pre>'.print_r($fp,true).'</pre>');
						$detalleRecibosPagos = new DetalleRecibosPagos();
						$detalleRecibosPagos->setTransaction($transaction);
						$detalleRecibosPagos->setRecibosPagosId($reciboPago->getId());
						$detalleRecibosPagos->setFormasPagoId($fp['formaPago']);
						$detalleRecibosPagos->setNumero($fp['numeroForma']);
						$detalleRecibosPagos->setValor($fp['valor']);
						if ($detalleRecibosPagos->save()==false) {
							foreach ($detalleRecibosPagos->getMessages() as $message) {
								throw new Exception($message->getMessage());
							}
						}
					}
				}
			}
			$data['reciboPagoId']=$reciboPago->getId();
		}else{
			foreach ($reciboPago->getMessages() as $message) {
				throw new Exception('No se pudo crear recibo de pago, '.$message->getMessage());
			}
		}
		$logger->log('crearRecibosPagos >> fin');
		$data['reciboPago'] = $reciboPago;
		return $reciboPago;
	}

	/**
	 * Metodo que crea un nuevo registro de control pago
	 * @param $socios, ActiveRecord
	 * @param $data, Array
	 * @param $recibo_id, int
	 * @param $transaction
	 *
	 * @return Array
	 */
	static function nuevoReciboPago($socios, &$data, $recibo_id=false, $transaction) {
		$logger = $data['logger'];
		$logger->log('nuevoReciboPago >> init');
		//Creamos registro del recibo de caja en RecibosPagos
		TPC::crearRecibosPagos($data, $transaction);
		$reciboPago = $data['reciboPago'];
		$recibosPagosId = $data['reciboPagoId'];
		//Creamos el registro en controlpagos
		$controlPago = new ControlPagos();
		$controlPago->setTransaction($transaction);
		$controlPago->setSociosId($socios->getId());
		$controlPago->setPagado($data['valorPagado']);
		if (isset($data["capital"])) {
			$controlPago->setCapital($data["capital"]);
		}
		if (isset($data["diferencia"])) {
			$controlPago->setDiasPagado($data["diferencia"]);
		}
		if (isset($data["interecesCorrientesAplicados"])) {
			$controlPago->setInteres($data["interecesCorrientesAplicados"]);
		}
		if (isset($data["totalDias"])) {
			$controlPago->setDiasCorriente($data["totalDias"]);
		}
		if (isset($data["interesesMora"])) {
			$controlPago->setMora($data["interesesMora"]);
		}
		if (isset($data["diasMora"])) {
			$controlPago->setDiasMora($data["diasMora"]);
		}
		$controlPago->setFechaPago($data['fechaPago']);
		if (isset($data['saldo'])) {
			$controlPago->setSaldo($data['saldo']);
		}else{
			$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst("socios_id=".$socios->getId());
			$controlPago->setSaldo($membresiasSocios->getSaldoPagar());
		}
		//Asignamos nota credito si existe
		if (isset($data["notaCreditoId"])) {
			$controlPago->setNotaCreditoId($data["notaCreditoId"]);
		}
		//Asignamos nota dedito si existe
		if (isset($data["notaDeditoId"])) {
			$controlPago->setNotaDeditoId($data["notaDeditoId"]);
		}
		$controlPago->setEstado($reciboPago->getEstado());//Estado de recibos pagos
		$controlPago->setRecibosPagosId($data['reciboPagoId']);
		$controlPago->setRc($data['rcReciboPago']);

		if ($controlPago->save()==true) {
			//asignamos el id generado a rc como el numero de contabilidad del pago
			$idControlPago = $controlPago->getId();
			$data['controlPagoId'] = $idControlPago;
			if ($data['debug']==true) {
				$logger->log("Se creo control de pago");
			}
		}else{
			foreach ($controlPago->getMessages() as $message) {
				throw new Exception('nuevoReciboPago: '.$message->getMessage());
			}
		}
		if ($data['debug']==true) {
			$logger->log("reciboPagoRc: ".$data["rcReciboPago"]);
			$logger->log("controlPagoRc: ".$data["rcReciboPago"]);
			$logger->log("nuevoRc: ".$data["rcReciboPago"]);
		}
		$logger->log('nuevoReciboPago >> find');
	}

	/**
	 * Metodo que verifica si el pago sumo y ahora capital esta al 11%
	 * si es mayor cambia estado de movimeinto de reserva a venta
	 *
	 * @param $socios, ActiveRecord
	 * @param $data, Array
	 * @param $transaction
	 *
	 * @return
	 */
	public static function verifca11Porciento($socios, &$data, $membresiasSocios, $detalleCuota, $transaction) {
		$logger = $data['logger'];
		$logger->log('verifca11Porciento >> init');
		//Si el contrato tiene un estado de movimiento R (reserva)
		if ($socios->getEstadoMovimiento()=='R') {//Reserva
			//Miramos cuanto es el 11% del contrato
			$valor11Porciento=($membresiasSocios->getValorTotal()*11)/100;
			//Miramos cuanto se ha pagado
			$inicialPagado = $detalleCuota->getHoyPagado()+$detalleCuota->getCuota2Pagado()+$detalleCuota->getCuota3Pagado();
			//Si lo pagado es mayor a valor del 11% del contrato
			if ($inicialPagado>=$valor11Porciento) {
				//Asignamos el valor de venta
				$socios->setTransaction($transaction);
				$socios->setValidar(false);
				$socios->setEstadoContrato('A');//Activo
				$socios->setEstadoMovimiento('V');//Venta
				if ($socios->save() == true) {
					$logger->log('verifca11Porciento >> Se cambio contrato a estado movimiento de Reserva a Venta');
				}else{
					foreach ($socios->getMessages() as $message) {
						throw new Exception($message->getMessage());
					}
				}
				$socios->setValidar(true);
			}
		}
		$logger->log('verifca11Porciento >> fin');
	}

	/**
	 * Metodoq ue verifica si el contrato esta 100 pago, si es asi se cambia el contrato al estado
	 * 100% pagado
	 *
	 * @param $socios, ActiveRecord
	 * @param $data, Array
	 * @param $transaction
	 *
	 * @return $flag
	 */
	public static function verifica100PorcientoPago($socios, &$data, $transaction) {
		$logger = $data['logger'];
		$logger->log('verifica100PorcientoPago >> init');
		$flag = false;
		//Verificamos si ya ha pagado sus cuotas iniciales
		$detalleCuota = EntityManager::get('DetalleCuota')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId()));
		$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId()));
		$logger->log('verifica100PorcientoPago >> estado1: '.$detalleCuota->getEstado1().', estado2: '.$detalleCuota->getEstado2().', estado3: '.$detalleCuota->getEstado3());
		if ($detalleCuota->getEstado1()=='P'&&$detalleCuota->getEstado2()=='P'&&$detalleCuota->getEstado3()=='P') {
			$controlPagos = EntityManager::get('ControlPagos')->setTransaction($transaction)->findFirst(array('conditions'=>"socios_id=".$detalleCuota->getSociosId(),"order"=>"fecha_pago DESC"));
			//Buscamos en amortización también que no existan cuotas sin pagar
			$amortizacion = EntityManager::get('Amortizacion')->setTransaction($transaction)->findFirst(array('conditions' => 'socios_id='.$socios->getId().' AND estado="D"'));
			if ($amortizacion==false) {
				//existe amortizacion del socio?
				$amortizacion2 = EntityManager::get('Amortizacion')->setTransaction($transaction)->findFirst(array('conditions' => 'socios_id='.$socios->getId()));
				if (
				($amortizacion2 != false && $controlPagos->getSaldo()<=0) 
				|| $amortizacion==false
				) {
					//Cambiamos estado a 100% pagado
					$socios->setValidar(false);
					$socios->setEstadoContrato('A');//Activo
					$socios->setEstadoMovimiento('P');//100% pagado
					if ($socios->save()==false) {
						foreach ($socios->getMessages() as $message) {
							throw new Exception($message->getMessage());
						}
					}
					$socios->setValidar(true);
				}
			}else{
				if (isset($data['debug'])==true && $data['debug']==true) {
					$logger->log('Aun no ha pagado amortizacion por completo');
				}
			}
		}else{
			$logger->log('Aun tiene cuotas iniciales pendientes');
		}
		$logger->log('verifica100PorcientoPago >>  estado_contrato: '.$socios->getEstadoContrato().', estado_movimiento: '.$socios->getEstadoMovimiento());
		$logger->log('verifica100PorcientoPago >> fin');
		return $flag;
	}

	/**
	 * Metodo que busca el ultimo pago hecho y velida si la fecha de pago es mayor a la del ultimo pago
	 * sino es un pago posterior
	 *
	 * @param activeRecord $socios
	 * @param string fechaPago
	 * @param $transaction
	 * @return boolean
	 */
	static function esUnPagoPosterior($socios,$fechaPago,$transaction) {
		if (!$socios->getId()) {
			throw new Exception('esUnPagoPosterior: Debe ingresar el id del contrato');
		}
		$reciboPagos = EntityManager::get('RecibosPagos');
		$reciboPagos->setTransaction($transaction);
		$reciboPagos->findFirst(array('conditions'=> 'socios_id='.$socios->getId(), 'order' => 'fecha_pago DESC'));
		$flagPagoPosterior = false;
		//Si existe algun pago
		if ($reciboPagos != false) {
			$fechaPagoAnterior = $reciboPagos->getFechaPago();
			$fechaPagoNuevo = $fechaPago;
			//Si la fecha de pago anterior es mayor a la fecha de pago actual es un pago posterior
			if (TPC::dateGreaterThan($fechaPagoAnterior,$fechaPagoNuevo)==true) {
				$flagPagoPosterior = true;
			}
		}
		return $flagPagoPosterior;
	}

	/**
	 * Main de realizar un pago a capital
	 * @param $data = Array(
	 *		'sociosId'				=>  $sociosId,
	 *		'fechaRecibo'		=>  '',
	 *		'fechaPago'			=>  '',
	 *		'formasPago'		=>  $formasPagos,
	 *		'cuentasId'			=>  0,
	 *		'reciboProvisional'	=>  '',
	 *		'ciudadPago'		=>  '',
	 *		'rcReciboPago'		=>  '',
	 *		'rc_controlpago'	=>  '',
	 *		'debug'				=>  true
	 * )
	 * @param $transaction
	 */
	public static function addAbonoCapitalContrato(&$data, $transaction) {
		
		//Se inicializa las variables de calculo
		$data['valorCuoact']		= 0;
		$data['valorCuoafi']		= 0;
		$data['valorInicial']		= 0;
		$data['valorCapital']		= 0;
		$data['valorFinanciacion']	= 0;
		$data['diasDiferencia']		= 0;
		$data['diasInteresc']		= 0;
		$data['diasMora']			= 0;
		$data['mora']				= 0;
		$data['valorInteresc']		= 0;
		$data['valorMora']			= 0;
		//Asignamos lo datos por defecto
		if (!isset($data['fechaPago'])) {
			$data['fechaPago'] = date('Y-m-d');
		}
		if (!isset($data['fechaRecibo'])) {
			$data['fechaRecibo'] = date('Y-m-d');
		}
		$data['ciudadPago']			= '127591';
		//Si esta vacia $data
		if (count($data)==0) {
			throw new Exception('Para hacer un pago es necesario tener datos');
		}
		//Validamos los campos que deben estar presente
		$msg = '';
		if (!isset($data['sociosId']) || empty($data['sociosId'])) {
			$msg .= PHP_EOL.'Para hacer un pago es necesario tener un contrato al abonar pago';
		}
		//Se valida si existe contrato
		//Obtenemos el id del contrato
		$socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($data['sociosId']);
		if ($socios==false) {
			throw new Exception('Abono capital contrato: El numero de contrato no existe');
		}
		//Agregamos log de procesos en el data
		$logger = new Logger('File', 'TpcClassSociosId'.$data['sociosId'].'.log');
		$data['logger'] = $logger;
		$logger->log('////////////////////////////////////////////////////');
		$logger->log('addAbonoCapitalContrato >> init');
		if (!isset($data['fechaRecibo']) || empty($data['fechaRecibo'])) {
			$msg .= PHP_EOL.'Para hacer un pago es necesario tener una fecha de recibo';
		}
		if (!isset($data['fechaPago']) || empty($data['fechaPago'])) {
			$msg .= PHP_EOL.'Para hacer un pago es necesario tener una fecha de pago';
		}
		if (!isset($data['cuentasId']) || empty($data['cuentasId']) || $data['cuentasId']=='@') {
			$msg .= PHP_EOL.'Para hacer un pago es necesario tener una cuenta de banco asignada';
		}
		if (!isset($data['ciudadPago']) || empty($data['ciudadPago'])) {
			$msg .= PHP_EOL.'Para hacer un pago es necesario tener una ciudad asignada';
		}
		if ($msg) {
			throw new Exception($msg);
		}
		//Asignamos el valor pagado
		$data['valorPago']=$data['formasPago']['total'];
		$msg='';
		//Validamos que las fechas esten dentro de las fechas acordadas
		$detalleCuota = EntityManager::get('DetalleCuota')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId()));
		if ($detalleCuota==false) {
			throw new Exception('El contrato no tiene cuotas inciales '.$socios->getId());
		}
		//Miramos si se hizo un pago
		if (empty($data['valorPago']) ||$data['valorPago'] <= 0) {
			throw new Exception('Abono Capital Contrato: Por favor ingrese detalles de pago');
		}
		$logger->log('valorPago: '.$data['valorPago']);
		//Almacenamos el valor total del pago
		$data['valorPagado'] = $data['valorPago'];
		//Miramos si es un pago posterior
		$data['esUnPagoPosterior'] = self::esUnPagoPosterior($socios,$data['fechaPago'], $transaction);
		if ($data['esUnPagoPosterior']==true && !isset($data['notaContable']) && !isset($data['setValidar'])) {
			throw new Exception('El abono a capital solo es para hacerlo en un fecha mayor');
		}
		$logger->log('esUnPagoPosterior: '.$data['esUnPagoPosterior']);
		//verificamos si el contrato no esta activo no se debe hacer pagos
		if ($socios->getEstadoContrato()=='AA') {
			if (isset($data['setValidar']) && $data['setValidar']==false && $data['force']==true) {
				///
			} else {
				throw new Exception('El contrato esta anulado por tanto no se pueden hacer pagos'." isset({$data['setValidar']}) && {$data['setValidar']}==false && {$data['force']}==true");	
			}
		}
		//verificamos que no este 100% pago el contrato
		if ($socios->getEstadoMovimiento()=='P') {
			if (isset($data['setValidar']) && $data['force']==false) {
				///
			} else {
				throw new Exception('No es necesario pagar más ya que el contrato esta 100% pagado');
			}
		}
		//Obtenemos la Membresia del contrato
		$membresiaSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId()));
		if ($membresiaSocios==false) {
			throw new Exception('El contrato no tiene membresia');
		}
		//detalle de la descripcion del pago
		$data['detallePago'] = '';
		if (isset($cuotaPorCapital) && is_array($cuotaPorCapital)) {
			$data = array_merge_recursive($data,$cuotaPorCapital);
		}
		//Aplicamos pagos a Membresias
		$logger->log('Estado de cuota de afiliación: '.$membresiaSocios->getEstadoCuoafi());
		//throw new Exception($membresiaSocios->getEstadoCuoafi());
		if ($membresiaSocios->getEstadoCuoafi()=='D') { //Si debe
			$pagarMembresias = self::pagarMembresias($membresiaSocios, $data, $transaction);
		}
		//Obtenemos los Detalle de Cuotas del contrato
		$detalleCuota = EntityManager::get('DetalleCuota')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId()));
		if ($detalleCuota==false) {
			throw new Exception('El contrato no tiene detalles de cuotas');
		}
		$controlPagos = EntityManager::get('ControlPagos')->setTransaction($transaction);
		//Paga las cuotas iniciales
		$aunEstaEnPagosIniciales = self::pagarCuotasIniciales($detalleCuota, $data, $transaction);
		//throw new Exception(print_r($data, true));
		//Variable donde dice si aun esta en pagos de cuotas iniciales
		$estaAunPagandoCuotasIniciales = $data['aunEstaEnPagosIniciales'];
		//Validamos que exista amortizacion
		$amortizacionExists = EntityManager::get('Amortizacion')->setTransaction($transaction)->exists('socios_id='.$socios->getId());
		//si aun no esta pagando financiación es decir amortización no haga estas funciones
		//y valida que la amortizacion exista
		if ($estaAunPagandoCuotasIniciales==false && $amortizacionExists==true) {
			//Calculamos los intereces que debe tener el pago
			$data['onlyCapital'] = true; //Con esto solo aplica todo a capital
			$cuotaPorCapital = self::calcularIntereses($socios, $data, $transaction);

			if ($cuotaPorCapital!==false) {
				//Aplicamos pagos segun cuotas pedientes
				$pagarCuotas = self::pagarCuotasAmortizacion($detalleCuota, $data, $transaction);
			} else {
				return false;
			}
			//throw new Exception(print_r($data['detallePago'],true));
		}
		//debug si no hay detallePago
		if (empty($data['detallePago'])) {
			throw new Exception('El detalle del pago no se generó correctamente');
		}
		//Si es abono a capital muestra en el concepto que lo es
		if (isset($data['onlyCapital']) && $data['onlyCapital']==true) {

			if (isset($data['notaContable'])) {

				if (isset($data['debCre'])) {

					if ($data['debCre']=='C') {
						//Saldo menos valorpago
						$data['detallePago'] = ' Nota Contable Crédito.';
						$data['capital'] = 0;
						$data['cuota'] = 0;
						$data['cuotasMora'] = 0;
						$data['debePagar'] = 0;
						$data['totalDias'] = 0;
						$data['interecesCorrientesLiquidacion'] = 0;
						$data['interecesCorrientesAplicados'] = 0;
						$data['diferencia'] = 0;
						$data['diasMora'] = 0;
						$data['interesesMora'] = 0;
						$data['MoraNoCancelada'] = 0;

						$data['saldo'] = $data['saldoActual'] - $data['formasPago']['total']; 
						//echo "<br>xxx_: {$data['saldo']} = {$data['saldoActual']} - {$data['formasPago']['total']}; ";

						$data['calculos']['notaContable'][] = "{$data['saldo']} = {$data['saldoActual']} - {$data['formasPago']['total']};";

					} else {
						//resto a saldo
						$data['detallePago'] = ' Nota Contable Débito.'.$data['detallePago'];
						$data['capital'] = $data['formasPago']['total'];
						$data['cuota'] = 0;
						$data['cuotasMora'] = 0;
						$data['debePagar'] = 0;
						$data['totalDias'] = 0;
						$data['interecesCorrientesLiquidacion'] = 0;
						$data['interecesCorrientesAplicados'] = 0;
						$data['diferencia'] = 0;
						$data['diasMora'] = 0;
						$data['interesesMora'] = 0;
						$data['MoraNoCancelada'] = 0;

						$data['saldo'] = $data['saldoActual'] + $data['formasPago']['total']; 
					}
					
					if (isset($data['sinIntereses']) && $data['sinIntereses']==true) {
						$data['cuota'] = 0;
						$data['cuotasMora'] = 0;
						$data['debePagar'] = 0;
						$data['totalDias'] = 0;
						$data['interecesCorrientesLiquidacion'] = 0;
						$data['interecesCorrientesAplicados'] = 0;
						$data['diferencia'] = 0;
						$data['diasMora'] = 0;
						$data['interesesMora'] = 0;
						$data['MoraNoCancelada'] = 0;

						$diffActualPago = 0;
						if (isset($data['saldoIdeal']) && $data['saldoIdeal']!=$data['saldo']) {
							$diffActualPago = abs($data['saldoIdeal'] - $data['saldo']);
						}

						if ($data['debCre']=='C') {
							
							//$data['saldo'] -= $diffActualPago;	
							//echo "<br>saldo new: ",$data['saldo'],' -= ',$diffActualPago;
							
							
						} else {
							//$data['saldo'] += $data['valorPagado'];
							//$data['saldo'] += $diffActualPago;
						}
					}
				} else {
					$data['detallePago'] = ' Nota Contable. '.$data['detallePago'];
				}
				
			} else {
				$data['detallePago'] = 'Abono a Capital. '.$data['detallePago'];
			}
		}
		//echo "<br>saldo: ",$data['saldo'],'---',$data['valorPagado'],"<br>";
		//print_r($data['estadoCapital']);
		//print_r($data['estadoCapital']);
		
		//insertamos nuevo registro en recibos pagos
		$nuevoReciboPago = self::nuevoReciboPago($socios, $data, false, $transaction);
		//Se verifica si el estado del contrato pasa el 11% cambia su estado
		$verifca11Porciento = self::verifca11Porciento($socios, $data, $membresiaSocios, $detalleCuota, $transaction);
		//Verificamos si ya pago todo el socio de su contrato
		$pagado100 = TPC::verifica100PorcientoPago($socios, $data, $transaction);
		//throw new Exception(print_r($data, true));
		$logger->log('All: <pre>'.print_r($data,true).'</pre>');
		$logger->log('addAbonoCapitalContrato >> fin');
		$logger->log('////////////////////////////////////////////////////');
		return $data;
	}

	/**
	 * Main de realizar una nota contable
	 * @param $data = Array(
	 *		'sociosId'				=>  $sociosId,
	 *		'fechaRecibo'		=>  '',
	 *		'fechaPago'			=>  '',
	 *		'formasPago'		=>  $formasPagos,
	 *		'cuentasId'			=>  0,
	 *		'reciboProvisional'	=>  '',
	 *		'ciudadPago'		=>  '',
	 *		'rcReciboPago'		=>  '',
	 *		'rc_controlpago'	=>  '',
	 *		'debCre'			=>  '',
	 *		'debug'				=>  true
	 * )
	 * @param $transaction
	 */
	public static function addNotaContableContrato(&$data, $transaction) {

		//Se inicializa las variables de calculo
		$data['valorCuoact']		= 0;
		$data['valorCuoafi']		= 0;
		$data['valorInicial']		= 0;
		$data['valorCapital']		= 0;
		$data['valorFinanciacion']	= 0;
		$data['diasDiferencia']		= 0;
		$data['diasInteresc']		= 0;
		$data['diasMora']			= 0;
		$data['mora']				= 0;
		$data['valorInteresc']		= 0;
		$data['valorMora']			= 0;
		$data['diferencia']			= 0;
		//Asignamos lo datos por defecto
		if (!isset($data['fechaRecibo'])) {
			$data['fechaRecibo']		= date('Y-m-d');	
		}
		if (!isset($data['fechaPago'])) {
			$data['fechaPago']			= date('Y-m-d');
		}

		$data['ciudadPago']			= '127591';

		//Si esta vacia $data
		if (count($data)==0) {
			throw new Exception('Para hacer una nota contable es necesario tener datos');
		}
		//Validamos los campos que deben estar presente
		$msg = '';
		if (!isset($data['sociosId']) || empty($data['sociosId'])) {
			$msg .= PHP_EOL.'Para hacer una nota contable es necesario tener un contrato al abonarla';
		}
		//Obtenemos el id del contrato
		$socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($data['sociosId']);
		if ($socios==false) {
			throw new Exception('Nota contable contrato: El numero de contrato no existe');
		}

		//Luego buscamos el ultimo pagos hechos anteriormente de un contrato que tenga estado V(Normal)
		$reciboPagos = EntityManager::get('RecibosPagos')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado IN ("V","N")','order'=>'fecha_pago DESC'));
		if ($reciboPagos!=false) {
			//se asigna fecha de ultimo pago
			//$data['fechaPago'] = TPC::addDaysToDate($reciboPagos->getFechaPago(),1);
		}

		//Asignamos el valor pagado
		$data['valorPago']=$data['formasPago']['total'];
		
		//Agregamos log de procesos en el data
		$logger = new Logger('File', 'TpcClassSociosId'.$data['sociosId'].'.log');
		$data['logger'] = $logger;
		$logger->log('////////////////////////////////////////////////////');
		$logger->log('addNotaCreditoContrato >> init');
		if (!isset($data['cuentasId']) || empty($data['cuentasId']) || $data['cuentasId']=='@') {
			$msg .= PHP_EOL.'Para hacer un pago es necesario tener una cuenta de banco asignada';
		}
		if (!isset($data['ciudadPago']) || empty($data['ciudadPago'])) {
			$msg .= PHP_EOL.'Para hacer un pago es necesario tener una ciudad asignada';
		}
		if ($msg) {
			throw new Exception($msg);
		}
		//Asignamos el valor pagado
		$data['valorPago'] 	 = $data['formasPago']['total'];
		$data['valorPagado'] = $data['valorPago'];

		$msg='';

		if (isset($data['debCre'])==true) {

			if ($data['debCre']=='C') {
				$data['detallePago'] = 'Nota Crédito.';
				//Resta al saldo ajustandolo (C) credito 

			} else {
				$data['detallePago'] = 'Nota Débito.';
			}

		}

		TPC::addAbonoCapitalContrato($data, $transaction);
		
		$logger->log('All: <pre>'.print_r($data,true).'</pre>');
		$logger->log('addNotaCreditoContrato >> fin');
		$logger->log('////////////////////////////////////////////////////');
		return $data;
	}

	/**
	 * Less some value to residue of contract
	 *
	 * @param $data = Array(
	 *	'sociosId'			=> $sociosId,
	 *	'fechaRecibo'		=> '',
	 *	'fechaPago'			=> '',
	 *	'formasPago'		=> $formasPagos,
	 *	'cuentasId'			=> 0,
	 *	'reciboProvisional'	=> '',
	 *	'ciudadPago'		=> '',
	 *	'rcReciboPago'		=> '',
	 *	'rc_controlpago'	=> '',
	 *	'debug'				=> true
	 * )
	 * @param $transaction
	*/
	public static function lessSaldoContrato(&$data, $transaction) {
		
		
		//Luego buscamos el ultimo pagos hechos anteriormente de un contrato que tenga estado V(Normal)
		$controlPagosOld = EntityManager::get('ControlPagos')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId().'','order'=>'fecha_pago DESC'));
		if ($controlPagosOld!=false) {
			$data['saldo'] = $controlPagosOld->getSaldo() - $data['valorPago'];

			$controlPagosNew = clone $controlPagosOld;
			$controlPagosNew->setId(null);
			$controlPagosNew->setSaldo($data['saldo']);
			$controlPagosNew->setRc(TPC::aumentarConsecutivoRc($transaction));
			if (!$controlPagosNew->save()) {
				foreach ($controlPagosNew->getMessages() as $message) {
					throw new Exception('lesssaldoContrato: '.$message->getMessage());
				}
			}

		} else {
			throw new Exception('lesssaldoContrato: No se encontro control_pagos');
		}

		$recibosPagosOld = EntityManager::get('ControlPagos')->setTransaction($transaction)->findFirst(array('conditions'=>'rc='.$controlPagosOld->getRc()));
		if ($recibosPagosOld!=false) {

			$recibosPagosNew = clone $recibosPagosOld;
			$recibosPagosNew->setId(null);
			$controlPagosNew->setValorPago($data['valorPago']);
			$controlPagosNew->setRc($controlPagosNew->getRc());
			if (!$controlPagosNew->save()) {
				foreach ($controlPagosNew->getMessages() as $message) {
					throw new Exception('lesssaldoContrato: '.$message->getMessage());
				}
			}

		} else {
			throw new Exception('lesssaldoContrato: No se encontro recibos_pagos');
		}

		$data['rcReciboPago'] = $controlPagosNew->getRc();

	}

	/**
	 * Main de realizar un pago
	 * @param $data = Array(
	 *	'sociosId'			=> $sociosId,
	 *	'fechaRecibo'		=> '',
	 *	'fechaPago'			=> '',
	 *	'formasPago'		=> $formasPagos,
	 *	'cuentasId'			=> 0,
	 *	'reciboProvisional'	=> '',
	 *	'ciudadPago'		=> '',
	 *	'rcReciboPago'		=> '',
	 *	'rc_controlpago'	=> '',
	 *	'debug'				=> true
	 * )
	 * @param $transaction
	 */
	public static function addAbonoContrato(&$data, $transaction) {

		try{
			//Se inicializa las variables de calculo
			$data['valorCuoact']		= 0;
			$data['valorCuoafi']		= 0;
			$data['valorInicial']		= 0;
			$data['valorCapital']		= 0;
			$data['valorFinanciacion']	= 0;
			$data['diasDiferencia']		= 0;
			$data['diasInteresc']		= 0;
			$data['diasMora']			= 0;
			$data['mora']				= 0;
			$data['valorInteresc']		= 0;
			$data['valorMora']			= 0;
			//Asignamos lo datos por defecto
			$data['fechaRecibo']		= date('Y-m-d');
			$data['ciudadPago']			= '127591';//Bogota
		
			//Si esta vacia $data
			if (count($data)==0) {
				throw new Exception('Para hacer un pago es necesario tener datos');
			}
			//Validamos los campos que deben estar presente
			$msg = '';
			if (!isset($data['sociosId']) || empty($data['sociosId'])) {
				$msg .= PHP_EOL.'Para hacer un pago es necesario tener un contrato al abonar pago';
			}
			//Se valida si existe contrato
			//Obtenemos el id del contrato
			$socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($data['sociosId']);
			if ($socios==false) {
				throw new Exception('Abono contrato: El numero de contrato no existe');
			}

			//Si el pago es hecho con una fecha de pago menor a la fecha de compra del contrato sale error
			if ($socios->getFechaCompra()!=$data['fechaPago'] && TPC::dateGreaterThan($socios->getFechaCompra(), $data['fechaPago'])==true) {
				//throw new Exception('La fecha de pago "'.$data['fechaPago'].'" es menor a la fecha de compra "'.$socios->getFechaCompra().'" del contrato "'.$socios->getNumeroContrato().'"');
			}
			//Agregamos log de procesos en el data
			$logger = new Logger('File', 'TpcClassSociosId'.$data['sociosId'].'-'.$socios->getNumeroContrato().'.log');
			$data['logger'] = $logger;
			$logger->log('////////////////////////////////////////////////////');
			$logger->log('addAbonoContrato >> init');
			if (!isset($data['fechaRecibo']) || empty($data['fechaRecibo'])) {
				$msg .= PHP_EOL.'Para hacer un pago es necesario tener una fecha de recibo';
			}
			if (!isset($data['fechaPago']) || empty($data['fechaPago'])) {
				$msg .= PHP_EOL.'Para hacer un pago es necesario tener una fecha de pago';
			}
			if (!isset($data['cuentasId']) || empty($data['cuentasId']) || $data['cuentasId']=='@') {
				$msg .= PHP_EOL.'Para hacer un pago es necesario tener una cuenta de banco asignada';
			}
			if (!isset($data['ciudadPago']) || empty($data['ciudadPago'])) {
				$msg .= PHP_EOL.'Para hacer un pago es necesario tener una ciudad asignada';
			}
			if ($msg) {
				throw new Exception('Abono Contrato: '.$msg);
			}
			//Asignamos el valor pagado
			$data['valorPago']=$data['formasPago']['total'];
			$msg='';
			//Validamos que las fechas esten dentro de las fechas acordadas
			$detalleCuota = EntityManager::get('DetalleCuota')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$socios->getId()));
			if ($detalleCuota==false) {
				throw new Exception('El contrato no tiene cuotas inciales '.$socios->getId());
			}
			if (TPC::dateGreaterThan($socios->getFechaCompra()->getDate(), $data['fechaPago'])==true && $socios->getFechaCompra()->getDate() != $data['fechaPago'] && !isset($data['reservasId'])) {
				//throw new Exception('Abono Contrato: La fecha de pago debe ser mayor a la fecha de compra del contrato '.$socios->getFechaCompra().'/ fecha pago: '.$data['fechaPago']);
			}
			//Obtenemos el valor de Tasa de Mora del pago
			$tasaDeMora = self::getTasaDeMora($data['fechaPago'], $data, $transaction);
			$logger->log('tasaDeMora: '.$tasaDeMora);
			//Miramos si se hizo un pago
			if (empty($data['valorPago']) || $data['valorPago'] <= 0) {
				if (isset($data['setValidar'])==false || $data['setValidar']!=false) {
					//throw new Exception('Abono Contrato: Por favor ingrese detalles de pago');
				}
			}
			$logger->log('valorPago: '.$data['valorPago']);
			//Almacenamos el valor total del pago
			$data['valorPagado'] = $data['valorPago'];
			//Miramos si es un pago posterior
			$data['esUnPagoPosterior'] = self::esUnPagoPosterior($socios,$data['fechaPago'], $transaction);
			
			if (isset($data['validarPagosPosteriores']) && $data['validarPagosPosteriores']==true
				&& $data['esUnPagoPosterior']==true
			) {
				throw new Exception('Abono Contrato: El contrato ya tiene un pago con una fecha mayor a la que intenta hacer, realize este pago como un pago posterior');
			}
			$logger->log('esUnPagoPosterior: '.$data['esUnPagoPosterior']);
			
			//verificamos si el contrato no esta activo no se debe hacer pagos
			if ($socios->getEstadoContrato()=='AA' && isset($data['setValidar']) && $data['setValidar']) {
				throw new Exception('Abono Contrato: El contrato esta anulado por tanto no se pueden hacer pagos');
			}
			//verificamos que no este 100% pago el contrato
			if ($socios->getEstadoMovimiento()=='P') {
				throw new Exception('Abono Contrato: No es necesario pagar más ya que el contrato esta 100% pagado');
			}
			//Buscamos por control pagos los datos para calcular intereces
			$controlPagosArray = EntityManager::get('ControlPagos')->find(
			array('conditions' => 'socios_id='.$socios->getId(),'order' => 'fecha_pago DESC'));
			//Obtenemos la Membresia del contrato
			$membresiaSocios = EntityManager::get('MembresiasSocios');
			$membresiaSocios->setTransaction($transaction);
			$membresiaSocios = $membresiaSocios->findFirst(array('conditions'=>'socios_id='.$socios->getId()));
			if ($membresiaSocios == false) {
				throw new Exception('El contrato no tiene membresia');
			}
			//detalle de la descripcion del pago
			$data['detallePago'] = '';
			if (isset($cuotaPorCapital) && is_array($cuotaPorCapital)) {
				$data = array_merge_recursive($data,$cuotaPorCapital);
			}
			//Aplicamos pagos a Membresias
			$logger->log('Estado de cuota de afiliación: '.$membresiaSocios->getEstadoCuoafi());
			//throw new Exception($membresiaSocios->getEstadoCuoafi());
			if ($membresiaSocios->getEstadoCuoafi()=='D') { //Si debe
				$pagarMembresias = self::pagarMembresias($membresiaSocios, $data, $transaction);
			}
			//Obtenemos los Detalle de Cuotas del contrato
			$detalleCuota = EntityManager::get('DetalleCuota');
			$detalleCuota->setTransaction($transaction);
			$detalleCuota = $detalleCuota->findFirst(array('conditions'=>'socios_id='.$socios->getId()));
			if ($detalleCuota==false) {
				throw new Exception('El contrato no tiene detalles de cuotas');
			}
			//Detalle inicial
			if (!isset($data['detallePago'])) {
				$data['detallePago'] = '';
			}
			$controlPagos = EntityManager::get('ControlPagos');
			//Paga las cuotas iniciales
			$data['pagoCuotasIniciales'] = false;
			$aunEstaEnPagosIniciales = self::pagarCuotasIniciales($detalleCuota, $data, $transaction);
			//Variable donde dice si aun esta en pagos de cuotas iniciales
			$estaAunPagandoCuotasIniciales = $data['aunEstaEnPagosIniciales'];
			//Validamos que exista amortizacion
			$amortizacionExists = EntityManager::get('Amortizacion')->setTransaction($transaction)->exists('socios_id='.$socios->getId());
			//si aun no esta pagando financiación es decir amortización no haga estas funciones
			//y valida que la amortizacion exista
			if ($estaAunPagandoCuotasIniciales==false && $amortizacionExists==true) {
				$data['sobroDeCuotaInicial'] = false;
				//Pasa cuando paga cuotas iniciales y sobro para 1ra cuota de amortizacion
				if ($data['valorPago']>0) {
					if ($data['pagoCuotasIniciales']==true) {
						$data['sobroDeCuotaInicial'] = true;
					}
					//Calculamos los intereces que debe tener el pago
					$cuotaPorCapital = self::calcularIntereses($socios, $data, $transaction);
					//Aplicamos pagos segun cuotas pedientes
					$pagarCuotas = self::pagarCuotasAmortizacion($detalleCuota, $data, $transaction);
				}
				if (isset($data['identificacion']) && $data['identificacion']==328580082) {
					//throw new Exception(print_r($data,true));
				}
			}

			#Para probar pagos
			if (!isset($data['probarPago'])) {
				//debug si no hay detallePago
				if (empty($data['detallePago'])) {
					if ($amortizacionExists==true) {
						throw new Exception('El detalle del pago no se generó correctamente.'/*.print_r($data,true)*/);
					}else{
						//Verificamos si ya pago todo el socio de su contrato
						$pagado100 = TPC::verifica100PorcientoPago($socios, $data, $transaction);
					}
				}else{
					//throw new Exception(print_r($data, true));
					//insertamos nuevo registro en recibos pagos
					$nuevoReciboPago = self::nuevoReciboPago($socios, $data, false, $transaction);
					//Se verifica si el estado del contrato pasa el 11% cambia su estado
					$verifca11Porciento = self::verifca11Porciento($socios, $data, $membresiaSocios, $detalleCuota, $transaction);
				}
				//Verificamos si ya pago todo el socio de su contrato
				$pagado100 = TPC::verifica100PorcientoPago($socios, $data, $transaction);
			}
			//throw new Exception(print_r($data, true));
			$logger->log('All: <pre>'.print_r($data,true).'</pre>');
			$logger->log('addAbonoContrato >> fin');
			$logger->log('////////////////////////////////////////////////////');
			return $data;
		}
		catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Metodo que limpia los pago hechos solo se necesita numero de contrato
	 * @param integer $sociosId
	 * @param TransactionManager $transaction
	 */
	public static function limpiarAllPagos($sociosId, $transaction, $changeStatus=true) {
		$socio = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($sociosId);
		if ($socio ==false) {
			throw new Exception('limpiarAllPagos: El socio no existe');
		}
		//Borramos control pagos
		$status = EntityManager::get('ControlPagos')->setTransaction($transaction)->delete(array('conditions'=>'socios_id='.$sociosId));
		if ($status==false) {
			foreach ($controlPagos->getMessages() as $message) {
				throw new Exception($message->getMessage());
			}
		}
		//Limpiar Recibos de Caja
		$recibosPagos = EntityManager::get('RecibosPagos')->setTransaction($transaction);
		$detalleRecibosPagos = EntityManager::get('DetalleRecibosPagos')->setTransaction($transaction);
		$recibosPagosObj = $recibosPagos->find(array('conditions'=>'socios_id='.$sociosId));
		if (count($recibosPagosObj)>0) {
			//Borramos detalle de recibos de pago
			foreach ($recibosPagosObj as $reciboPago) {
				$status = $detalleRecibosPagos->setTransaction($transaction)->delete(array('conditions'=>'recibos_pagos_id='.$reciboPago->getId()));
				if ($status == false) {
					foreach ($detalleRecibosPagos->getMessages() as $message) {
						throw new Exception($message->getMessage());
					}
				}
			}
			//Borramos recibos de pago
			$status = $recibosPagos->setTransaction($transaction)->delete(array('conditions'=>'socios_id='.$sociosId));
			if ($status == false) {
				foreach ($recibosPagos->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
			}
		}
		//Limpiar Control pagos
		$deleteControlPagos = EntityManager::get('ControlPagos')->setTransaction($transaction)->delete(array('conditions'=>"socios_id='$sociosId'"));
		
		//Borramos pagos de membresias socios
		$membresiaSocio = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$sociosId));
		if ($membresiaSocio != false) {
			if ($socio->getCambioContrato()!='S') {
				$membresiaSocio->setAfiliacionPagado(0);
				$membresiaSocio->setEstadoCuoafi('D');
			}else{
				//buscamos restar el valor del cambio de contrato a ver cuanto alcanza
				$derechoAfiliacion = EntityManager::get('DerechoAfiliacion')->setTransaction($transaction)->findFirst($membresiaSocio->getDerechoAfiliacionId());
				if ($derechoAfiliacion==false) {
					throw new Exception('El derecho de afiliación no existe '.$membresiaSocio->getDerechoAfiliacionId());
				}
				$valorDerechoAfiliacion1 = LocaleMath::round($derechoAfiliacion->getValor(),0);
				$valorCambioContrato = LocaleMath::round($socio->getValorCambioContrato(),0);
				if ($valorCambioContrato>$valorDerechoAfiliacion1) {
					$valorCambioContrato-=$valorDerechoAfiliacion1;
					$membresiaSocio->setAfiliacionPagado($valorDerechoAfiliacion1);
					$membresiaSocio->setEstadoCuoafi('P');
				}else{
					$valorDerechoAfiliacion1-=$valorCambioContrato;
					$valorCambioContrato=0;
					$membresiaSocio->setAfiliacionPagado($valorDerechoAfiliacion1);
					$membresiaSocio->setEstadoCuoafi('D');
				}
			}
			if ($membresiaSocio->save() == false) {
				foreach ($membresiaSocio->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
			}
		}
		//Ahora borramos las cuotas pagadas
		$detalleCuota = EntityManager::get('DetalleCuota');
		$detalleCuota->setTransaction($transaction);
		$detalleCuota = $detalleCuota->findFirst(array('conditions'=>'socios_id='.$sociosId));
		//El $socio->getCambioContrato() determina si es cambio de contrato si lo es no se debe limpiar afilaicion y cuota inicial
		if ($detalleCuota != false) {
			//Si no es cambio de contrato
			if ($socio->getCambioContrato()!='S') {
				$detalleCuota->setHoyPagado(0);
				$detalleCuota->setEstado1('D');
				$detalleCuota->setCuota2Pagado(0);
				$detalleCuota->setEstado2('D');
				$detalleCuota->setCuota3Pagado(0);
				$detalleCuota->setEstado3('D');
			}else{
				// Si es cambio de contrato
				if ($valorCambioContrato>0) {
					//Cuota1
					$cuota1 = LocaleMath::round($detalleCuota->getHoyPagado(),0);
					if ($valorCambioContrato > $cuota1) {
						$valorCambioContrato -= $cuota1;
						$detalleCuota->setEstado1('P');
					}else{
						$cuota1 -= $valorCambioContrato;
						$valorCambioContrato=0;
						$detalleCuota->setEstado1('D');
					}
					$detalleCuota->setHoyPagado($cuota1);
					
					//cuota2
					$cuota2 = $detalleCuota->getCuota2Pagado();
					if ($valorCambioContrato > $cuota2) {
						$valorCambioContrato -= $cuota2;
						$detalleCuota->setEstado2('P');
					}else{
						$cuota2 -= $valorCambioContrato;
						$valorCambioContrato=0;
						$detalleCuota->setEstado2('D');
					}
					$detalleCuota->setCuota2Pagado($cuota2);
						
					//Cuota3
					$cuota3 = $detalleCuota->getCuota3Pagado();
					if ($valorCambioContrato > $cuota3) {
						$valorCambioContrato -= $cuota3;
						$detalleCuota->setEstado3('P');
					}else{
						$cuota3 -= $valorCambioContrato;
						$valorCambioContrato=0;
						$detalleCuota->setEstado3('D');
					}
					$detalleCuota->setCuota3Pagado($cuota3);
					
				}else{
					$detalleCuota->setHoyPagado(0);
					$detalleCuota->setEstado1('D');
					$detalleCuota->setCuota2Pagado(0);
					$detalleCuota->setEstado2('D');
					$detalleCuota->setCuota3Pagado(0);
					$detalleCuota->setEstado3('D');
				}
			}
			if ($detalleCuota->save() == false) {
				foreach ($detalleCuota->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
			}
		}
		//Cambiamos los pagos de amortizacion
		$amortizacionObj = EntityManager::get('Amortizacion')->setTransaction($transaction)->find(array('conditions'=>'socios_id='.$sociosId));
		foreach ($amortizacionObj as $amortizacionRow) {
			$amortizacionRow->setEstado('D');
			$amortizacionRow->setPagado(0);
			if ($amortizacionRow->save() == false) {
				foreach ($amortizacionRow->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
			}
		}
		//Cambiamos el estado a reserva de un socio
		if ($changeStatus == true) {
			$socio->setValidar(false);
			$socio->setEstadoMovimiento('R');
			if ($socio->save() == false) {
				foreach ($socio->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
			}
		}
		$socio->setValidar(true);
	}

	/**
	 *
	 * Metodo que calcula el estado de cuenta de un contrato desde una fecha por defecto la hoy
	 * @param Array $config(
	 * 		'sociosId'		Es el id del contrato a generar estado
	 * 		'fecha'			Es la fecha hastsa donde edebe calcuar
	 * )
	 * @param ActiveRecordTransaction $transaction
	 */
	static function estadoCuenta(&$config, $transaction) {
		$valor=0;
		$detallePago = '';
		if (!isset($config['sociosId'])) {
			throw new Exception('estadoCuenta: El id del contrato es requerido');
		}
		$sociosId = $config['sociosId'];
		if($transaction){
			$socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($sociosId);
		} else {
			$socios = EntityManager::get('Socios')->findFirst($sociosId);
		}
		if ($socios==false) {
			throw new Exception('estadoCuenta: El id del contrato no existe');
		}
		if (!isset($config['fecha'])) {
			$config['fecha']=date('Y-m-d');
		}
		$fecha = $config['fecha'];
		//validamos que no use 31
		list($year,$month,$day) = explode('-',$fecha);
		if ($day>30) {
			$day=30;
		}
		$fecha = $year.'-'.$month.'-'.$day;
		$cuentas = EntityManager::get('Cuentas')->findFirst();
		//Array de datos a calcular
		$logger = new Logger('File', 'TpcClassEstadoCuenta'.$sociosId.'.log');
		$data = array(
			'sociosId'			=> $sociosId,
			'fechaRecibo'		=> $fecha,
			'fechaPago'			=> $fecha,
			'cuentasId'			=> $cuentas->getId(),
			'valorPago'			=> $valor,
			'detalleCuota'		=> '',
			'logger'			=> $logger,
			'debug'				=> true
		);
		//Miramos si esta aun pagando cuotas iniciales
		if($transaction){
			$alDiaCuotasIni = EntityManager::get('DetalleCuota')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$sociosId.' AND estado1="P" AND estado2="P" AND estado3="P"'));
		} else {
			$alDiaCuotasIni = EntityManager::get('DetalleCuota')->findFirst(array('conditions'=>'socios_id='.$sociosId.' AND estado1="P" AND estado2="P" AND estado3="P"'));
		}
		$data['aunEstaEnPagosIniciales'] = true;
		if ($alDiaCuotasIni==true) {
			$data['aunEstaEnPagosIniciales'] = false;
		}

		$data['capital'] = 0;
		$data['cuota'] = 0;
		$data['cuotasMora'] = 0;
		$data['debePagar'] = 0;
		$data['totalDias'] = 0;
		$data['interecesCorrientesLiquidacion'] = 0;
		$data['interecesCorrientesAplicados'] = 0;
		$data['diferencia'] = 0;
		$data['diasMora'] = 0;
		$data['interesesMora'] = 0;
		$data['MoraNoCancelada'] = 0;
		$data['saldo'] = 0;

		//amortizacion
		$totalCuotasAmortizacion = self::getModel('Amortizacion')->count(array('conditions'=>'socios_id='.$sociosId));
		if ($alDiaCuotasIni==true) {

			//Usamos la funcion para calcular los intereces a esa fecha
			TPC::calcularIntereses($socios, $data, $transaction);
			
			//Buscamos el estado capital de la fecha
			TPC::getEstadoCapitalByFecha($socios, $fecha, $transaction);
			
			//Si esta en una cupota mayor a la numero 1 de amortización
			if ($data['estadoCapital']['cuota']>=1 && $data['aunEstaEnPagosIniciales']==false) {
				//Calculamos cuanto debe pagar para estar al dia
				$debePagar = $data['saldo'] - $data['estadoCapitalHoy']['saldo'];
				$data['debePagar'] = $debePagar;
				$data['cuotasMora'] = $totalCuotasAmortizacion - $data['estadoCapital']['cuota'];
				//Agregamos una observación
				$detallePago = 'El contrato esta en cuota '.$data['estadoCapital']['cuota'];
			} else {
				$detallepago = 'A pagar cuota '.$data['estadoCapital']['cuota'];
				//Calculamos cuanto debe pagar para estar al dia
				$data['debePagar']	= $data['saldo'] - $data['estadoCapital']['saldo'];
				$data['cuotasMora'] = $totalCuotasAmortizacion;
			}
		}else{
			$data['debePagar']	= 0;
			$data['cuotasMora']	= $totalCuotasAmortizacion;
			$detallePago		= 'Aún está pagando las cuotas iniciales';
		}
		//agregamos el detalle creado
		$data['detallePago']	= $detallePago;
		//return todo lo calculado
		$config['estadoCuenta']	= $data;
	}

	/**
	 * Busca el estado de capital por fecha del mes y año
	 *
	 * @param Activerecord $socios
	 * @param string $fecha
	 * @param ActiverecordTransaction $transaction
	 */
	public static function getEstadoCapitalByFecha($socios, $fecha='', $transaction) {
		if (!$fecha) {
			$fecha = date('Y-m-d');
		}
		list($year,$month,$day) = explode('-',$fecha);
		$amortizacion = EntityManager::get('Amortizacion')->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND fecha_cuota like "'.$year.'-'.$month.'-%"'));
		if ($amortizacion==false) {
			$amortizacion = EntityManager::get('Amortizacion')->findFirst(array('conditions'=>'socios_id='.$socios->getId().' AND estado="D"'));
			if ($amortizacion==false) {
				throw new Exception('getEstadoCapitalByFecha: No hay amortizacion con un rango de que contenga la fecha '.$fecha.' y ninguna cup');
			}
		}
		$amortizacionA = array();
		$amortizacionA['cuota'] 	= $amortizacion->getNumeroCuota();
		$amortizacionA['cuotaFija']	= $amortizacion->getValor();
		$amortizacionA['capital']	= $amortizacion->getCapital();
		$amortizacionA['saldo']		= $amortizacion->getSaldo();
		$amortizacionA['fecha']		= $amortizacion->getFechaCuota();
		$amortizacionA['estado']	= $amortizacion->getEstado();
		$data['estadoCapitalHoy'] = $amortizacionA;
	}

	/**
	 *
	 * Metodo que genera un detalle de pago cuando se consuklta el estado de cuenta
	 * @param	array $data
	 * @return	string
	 */
	static function makedetallePagoEstadoCuenta(&$data) {
		$detallePago=array();
		if (isset($data['estadoCapital'])) {
			$estadoCapital = $data['estadoCapital'];
			$detallePago[] = 'El contrato esta en cuota '.$estadoCapital['cuota'];
		}
		return implode(', ', $detallePago);
	}

	/**
	 *
	 * Metodo que borra la amortizacion actual y vuelve a generarla bien
	 * @param Activerecord $socios
	 * @param array $dataA(
	 *  fechaInicial,
	 *  interés,
	 *  cuotas,
	 *  valorTotal,
	 *  valorTotal
	 * )
	 * @param transaction $transaction
	 */
	static function remplazarAmortizacion($socios,$dataA=false,$transaction) {
		//Se mejoró el proceso de calculo de amortización por eso se usa la clase TPC
		$id = $socios->getId();
		if (!$id) {
			return false;
		}
		$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>"socios_id=".$id));
		$pagoSaldo = EntityManager::get('PagoSaldo')->setTransaction($transaction)->findFirst(array('conditions'=>"socios_id=".$id));
		//obtenemos el interés de amortizacion a la fecha de compra
		$fechaPago = $socios->getFechaCompra()->getDate();

		$interesUsura2 = 0;
		if ($pagoSaldo->getMora()<=0) {
			$interesUsura2 = self::getTasaDeMora($fechaPago,array("debug"=>false), $transaction);
		}else{
			$interesUsura2 = $pagoSaldo->getMora();
		}
		//throw new Exception('pagoSaldo->getFechaPrimeraCuota(): '.$pagoSaldo->getFechaPrimeraCuota());
		if ($dataA==false) {
			$fechaInicio	= $pagoSaldo->getFechaPrimeraCuota();
			$interes		= $pagoSaldo->getInteres();
			$cuotas			= $pagoSaldo->getNumeroCuotas();
			$valorTotal		= $membresiasSocios->getValorTotal();
			$valorFinanciacion = $membresiasSocios->getSaldoPagar();
		}else{
			$fechaInicio	= $dataA['fechaInicial'];
			$interes		= $dataA['interes'];
			$cuotas			= $dataA['cuotas'];
			$valorTotal		= $dataA['valorTotal'];
			$valorFinanciacion = $dataA['valorTotal'];
		}
		//Borramos amortizacion actual
		$amortizacion = EntityManager::get('Amortizacion')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$id));
		if ($amortizacion != false) {
			$amortizacion->delete('socios_id='.$id);
		}
		//throw new Exception('fechaInicio: '.$fechaInicio);
		$dataAmortizacion = array(
			'valorFinanciacion'		=> $valorFinanciacion,//3900000
			'valorTotalCompra'		=> $valorTotal,//3900000
			'fechaCompra'			=> $socios->getFechaCompra(),//'29-06-2010'
			'fechaPagoFinanciacion'	=> $fechaInicio,//'30-09-2010'
			'plazoMeses'			=> $cuotas,//24
			'tasaMesVencido'		=> $interes,//1.8
			'tasaMora'				=> $interesUsura2,//pagoSaldo->getMora o de interes_usuara
			'debug'					=> false
		);
		$arrayAmortizacionNew = TPC::generarAmortizacion($dataAmortizacion);
		$status = true;
		//Recorre las cuotas y crea registros
		foreach ($arrayAmortizacionNew as $row) {
			$amortizacion= new Amortizacion();
			$amortizacion->setTransaction($transaction);
			$amortizacion->setSociosId($id);
			$amortizacion->setNumeroCuota($row['cuota']);
			$amortizacion->setValor($row['cuotaFija']);
			$amortizacion->setCapital($row['abonoCapital']);
			$amortizacion->setInteres($row['intereses']);
			$amortizacion->setSaldo($row['saldo']);
			$amortizacion->setFechaCuota($row['periodo']);
			$amortizacion->setEstado('D');
			$amortizacion->setPagado(0);
			if ($amortizacion->save() == false) {
				foreach ($amortizacion->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
				$status = false;
			}
		}
		return $status;
	}

	/**
	 *
	 * Crea una nota credito con parametros de $data
	 * @param array $data(
	 * 	'numero_contrato'
	 *  fecha_nota
	 *  valor_nota
	 *  observaciones
	 * )
	 */
	static function makeNotaCredito($data=array()) {
		$logger = new Logger('File', 'TpcClass.log');
		$msgError=array();
		if (!isset($data['numero_contrato'])) {
			$msgError[]='Es necesario que ingrese el numero de contrato de la nota';
		}
		//Sacamos id de contrato
		$socios = new Socios();
		$socios->find_first("numero_contrato='".$data['numero_contrato']."'");
		if (!$socios->id) {
			$msgError[]='El numero de contrato "'.$data['numero_contrato'].'" no existe';
		}
		if (!isset($data['fecha_nota'])) {
			$msgError[]='Es necesario que ingrese la fecha de la nota';
		}
		if (!isset($data['valor_nota'])) {
			$msgError[]='Es necesario que ingrese la valor de la nota';
		}
		if (!isset($data['observaciones'])) {
			$msgError[]='Es necesario que ingrese la observaciones de la nota';
		}
		if (count($msgError)>0) {
			$msg = implode(", ".PHP_EOL, $msgError);
			$logger->log($msg);
			return false;
		}
		//Creamos un nevo registro de nota credito
		$notaCredito = new NotaCredito();
		$notaCredito->socios_id=$socios->id;
		$notaCredito->fecha_nota = $data['fecha_nota'];
		$notaCredito->valor = $data['valor_nota'];
		$notaCredito->observaciones = $data['observaciones'];
		if (!$notaCredito->save()) {
			$logger->log('No se pudo guardar la nota credito');
			return false;
		}else{
			return $notaCredito->id;
		}
		return true;
	}

	/**
	 *
	 * Crea una nota dedito con parametros de $data
	 * @param array $data(
	 * 	'numero_contrato'
	 *  fecha_nota
	 *  valor_nota
	 *  observaciones
	 *  rc
	 * )
	 */
	static function makeNotaDebito($data=array()) {
		$logger = new Logger('File', 'TpcClass.log');
		$msgError=array();
		if (!isset($data['numero_contrato'])) {
			$msgError[]='Es necesario que ingrese el numero de contrato de la nota';
		}
		//Sacamos id de contrato
		$socios = new Socios();
		$socios->find_first("numero_contrato='".$data['numero_contrato']."'");
		if (!$socios->id) {
			$msgError[]='El numero de contrato "'.$data['numero_contrato'].'" no existe';
		}
		if (!isset($data['fecha_nota'])) {
			$msgError[]='Es necesario que ingrese la fecha de la nota';
		}
		if (!isset($data['valor_nota'])) {
			$msgError[]='Es necesario que ingrese la valor de la nota';
		}
		if (!isset($data['observaciones'])) {
			$msgError[]='Es necesario que ingrese la observaciones de la nota';
		}
		if (!isset($data['rc'])) {
			$msgError[]='Es necesario que ingrese el recibo de caja de la nota';
		}
		if (count($msgError)>0) {
			$msg = implode(", ".PHP_EOL, $msgError);
			$logger->log($msg);
			return false;
		}
		//Creamos un nevo registro de nota credito
		$notaDebito = new NotaDebito();
		$notaDebito->socios_id=$socios->id;
		$notaDebito->fecha_nota = $data['fecha_nota'];
		$notaDebito->valor = $data['valor_nota'];
		$notaDebito->observaciones = $data['observaciones'];
		$notaDebito->rc = $data['rc'];
		if (!$notaDebito->save()) {
			$logger->log('No se pudo guardar la nota debito');
			return false;
		}else{
			return $notaDebito->id;
		}
		return true;
	}


	/**
	 * Crea una nota historia con parametros de $data
	 *
	 * @param array $data
	 * @param TransactionManager $transaction
	 *
	 * @return integer
	 */
	public static function makeNotaHistoria($data=array(),$transaction) {
		$msgError=array();
		if (!isset($data['sociosId'])) {
			if (!isset($data['reservasId'])) {
				$msgError[]='Es necesario que ingrese el id del contrato/reserva de la nota';
			}
		}
		//Sacamos id de contrato
		$sociosId	= $data['sociosId'];
		$reservasId	= $data['reservasId'];
		if ($sociosId>0) {
			$socio = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($sociosId);
			if ($socio==false) {
				$msgError[]='El numero de contrato "'.$data['numero_contrato'].'" no existe';
			}
		}
		if ($reservasId>0) {
			$socio = EntityManager::get('Reservas')->setTransaction($transaction)->findFirst($reservasId);
			if ($socio==false) {
				$msgError[]='El numero de reserva "'.$data['numero_contrato'].'" no existe';
			}
		}
		if (!isset($data['fecha'])) {
			$msgError[]='Es necesario que ingrese la fecha de la nota';
		}
		if (!isset($data['observaciones'])) {
			$msgError[]='Es necesario que ingrese la observaciones de la nota';
		}
		if (!isset($data['estado'])) {
			$msgError[]='Es necesario que ingrese el estado de la nota';
		}
		if (count($msgError)>0) {
			$msg = implode(", ".PHP_EOL, $msgError);
			throw new Exception($msg);
		}
		//Creamos un nevo registro de nota historia
		$notaHistoria = EntityManager::get('NotaHistoria',true);
		$notaHistoria->setTransaction($transaction);
		if (isset($data['sociosIdErrado'])) {
			$sociosErrado = EntityManager::get('Socios')->findFirst($data['sociosIdErrado']);
			if ($sociosErrado != false) {
				$notaHistoria->setSociosIdErrado($sociosErrado->getId());
			}else{
				throw new Exception('makeNotaHistoria: El id del socio errado no existe');
			}
		}
		$notaHistoria->setSociosId($sociosId);
		$notaHistoria->setReservasId($reservasId);
		$notaHistoria->setFechaNota($data['fecha']);
		$notaHistoria->setObservaciones($data['observaciones']);
		$notaHistoria->setEstado($data['estado']);//E: Errado , P: Posterior, C: Cambio de contrato
		if (isset($data['valorNota'])) {
			$notaHistoria->setValor($data['valorNota']);
		}
		if (isset($data['rcErrados'])) {
			$notaHistoria->setRcErrados($data['rcErrados']);
		}
		if (isset($data['rcAbonar'])) {
			$notaHistoria->setRcAbonar($data['rcAbonar']);
		}
		if ($notaHistoria->save() == false) {
			foreach ($notaHistoria->getMessages() as $message) {
				throw new Exception($message->getMessage());
			}
		}
		return $notaHistoria->getId();
	}

	/**
	 * Metodo que suma dias a una fecha
	 *
	 * @param unknown_type $fecha1
	 * @param unknown_type $days
	 * @return unknown
	 */
	public static function addDaysToDate($fecha1=false,$days=0) {
		if ($fecha1==false) {
			$fecha1=date("Y-m-d");
		}
		list($year1,$mon1,$day1) = explode('-',$fecha1);
		//recorremos los dias
		for($i=1;$i<=$days;$i++) {
			//Si supera los 30 dias suma mes
			if ($day1>30) {
				$day1=1;
				$mon1++;
				//Si pasa el mes 12 (Diciembre) suma año
				if ($mon1>12) {
					$mon1=1;
					$year1++;
				}
			}else{
				$day1++;
			}
		}
		if ($mon1<10) {
			$mon1 = "0".$mon1;
		}
		if ($day1<10) {
			$day1 = "0".$day1;
		}
		$newDate = $year1.'-'.$mon1.'-'.$day1;
		return $newDate;
	}

	/**
	 * Resta dias a una fecha
	 *
	 * @param string $fecha1 2011-02-30
	 * @param int $days
	 * @return string date
	 */
	public static function subDaysToDate($fecha1=false,$days=0) {
		if ($fecha1==false) {
			$fecha1=date("Y-m-d");
		}
		list($year1,$mon1,$day1) = explode('-',$fecha1);
		//recorremos los dias
		for($i=1;$i<=$days;$i++) {
			//Si es menor a 1 del mes vuelva a 30 y reste un mes
			if ($day1<=1) {
				$day1=30;
				$mon1--;
				//Si es menor a enero vuelva a diciembre y reste un año
				if ($mon1<1) {
					$mon1=12;
					$year1--;
				}
			}else{
				$day1--;
			}
			//echo "<br>$i: ".$year1.'-'.$mon1.'-'.$day1;
		}
		if ($mon1<10) {
			$mon1 = "0".$mon1;
		}
		if ($day1<10) {
			$day1 = "0".$day1;
		}
		$newDate = $year1.'-'.$mon1.'-'.$day1;
		return $newDate;
	}

	/**
	 * Verifica si la  fecha1 es mayor o igual a la fecha2
	 *
	 * @param string date $fecha1
	 * @param string date $fecha2
	 * @return boolean
	 */
	public static function dateGreaterThan($fecha1=false,$fecha2=false) {
		if (!$fecha1 || !$fecha2) {
			return false;
		}
		list($year1,$mon1,$day1) = explode('-',$fecha1);
		list($year2,$mon2,$day2) = explode('-',$fecha2);
		//Si año es mayor de una es true
		if ($year1 > $year2) {
			return true;
		}else{
			//Si son iguales los años
			if ($year1==$year2) {
				if ($mon1>$mon2) {
					return true;
				}else{
					//Si mes es igual validamos dias
					if ($mon1==$mon2) {
						//Si año,mes son iguales pero dia1 es mayor a dia2 de una es true
						if ($day1>$day2) {
							return true;
						}else{
							//Si los dias son iguales se toma true
							if ($day1==$day2) {
								return true;
							}else{
								//Como no son iguales el dia2 es mayor por tanto de una es false
								return false;
							}
						}
					}else{
						//COmo el mes dos es mayor de una es false
						return false;
					}
				}
			}else{
				//SI año 2 es mayor de una es false
				return false;
			}
		}

	}

	/**
	 * Metodo que obtiene le formato de un numero de contrato
	 * por tipo de contrato si usa_formato esta 'S' sino retorna empty
	 *
	 * @param 	ActiveRecordTransaction $transaction
	 * @param	int $tipoContratoId
	 * @return	string
	 */
	public static function getFormatoContrato($transaction, $tipoContratoId) {
		if (!$tipoContratoId) {
			throw new Exception('El código de tipo de contrato no es válido');
		}
		$tipoContrato = self::getModel('TipoContrato')->setTransaction($transaction)->findFirst($tipoContratoId);
		if ($tipoContrato==false) {
			throw new Exception('El código de tipo de contrato no es válido');
		}
		if ($tipoContrato->getUsaFormato()=='S') {
			$variables = array(
				'ano' => date('y'),
				'sigla' => $tipoContrato->getSigla(),
				'consecutivo' => $tipoContrato->getNumero()+1
			);
			$formato = $tipoContrato->getFormato();
			foreach ($variables as $key => $value) {
				$formato = str_ireplace($key, $value, $formato);
			}
		}else{
			if ($formatoContrato->getUsaFormato()=='N') {
				$formato = $tipoContrato->getNumero()+1;
			}else{
				throw new Exception('No se ha definido si el tipo de contrato "'.$tipoContrato->getNombre().'" usa formato o no');
			}
		}
		return $formato;
	}

	/**
	 * Metodo que aumenta el consecutivo de un tipo de contrato
	 *
	 * @param int $tipoContratoId
	 * @param ActiveRecordTransaction $transaction
	 */
	public static function aumentarConsecutivoContrato($tipoContratoId, $transaction) {
		$tipoContrato = EntityManager::get('TipoContrato')->setTransaction($transaction)->findFirst($tipoContratoId);
		if ($tipoContrato==false) {
			throw new Exception('El tipo de contrato no existe');
		}
		$numero = $tipoContrato->getNumero();
		$numero+=1;
		//Rcs::disable();
		$tipoContrato->setNumero($numero);
		if ($tipoContrato->save() == false) {
			foreach ($tipoContrato->getMessages() as $message) {
				throw new Exception($message->getMessage());
			}
		}
		//Rcs::enable();
	}

	/**
	 * Metodo que aumenta el consecutivo de recibo de caja
	 *
	 * @param ActiveRecordTransaction $transaction
	 */
	public static function aumentarConsecutivoRc($transaction) {
		$empresa = EntityManager::get('Empresa')->setTransaction($transaction)->findFirst();
		if ($empresa==false) {
			throw new Exception('La empresa no existe');
		}
		$numero = $empresa->getCrc();
		$numero+=1;
		ActiveRecord::disableEvents(true);
		$empresa->setCrc($numero);
		if ($empresa->save() == false) {
			foreach ($empresa->getMessages() as $message) {
				throw new Exception($message->getMessage());
			}
		}
		ActiveRecord::disableEvents(false);
		return $numero;
	}

	/**
	 * Metodo que coge los tre campos de las formas de pago y las unifica
	 *
	 * @param array $config (
	 *  'formaPago'
	 *  'numeroForma'
	 *  'valor'
	 * )
	 * @param $transaction
	 *
	 * @return array(
	 *  'total'
	 *  'totalFPago'
	 *  'data'
	 * )
	 */
	public static function unificaFormasPagos($config, $transaction) {
		$formaPagos = array();
		if (!isset($config['formaPago'])) {
			throw new Exception('No hay formade pago '.print_r($config,true));
		}
		$formaPago = $config['formaPago'];
		$numeroForma = $config['numeroForma'];
		$valor = $config['valor'];
		$count = count($valor);
		$formaPagos['count'] = $count;
		//throw new Exception(print_r($config,true));
		//unificamos formasPagos
		$formaPagos['total'] = 0;
		$formaPagos['totalFPago'] = array();
		$formaPagos['data'] = array();
		for($i=0;$i<$count;$i++) {

			if ($valor[$i]<=0) {
				$i++;
				continue;
			}

			$fPago = $formaPago[$i];

			$formaPagoObj = EntityManager::get('FormasPago')->findFirst($fPago);
			if ($formaPagoObj == false) {
				throw new Exception('La forma de pago no es válida');
			}
			$typoPago = $formaPagoObj->getTipo();
			if (!isset($formaPagos['data'][$typoPago])) {
				$formaPagos['data'][$typoPago] = array();
			}
			$val = 0;
			if ($valor[$i]>0) {
				$val += $valor[$i];
			}
			if (!isset($formaPagos['data'][$typoPago])) {
				$formaPagos['data'][$typoPago] = array();
			}
			$formaPagos['data'][$typoPago][] = array(
				'formaPago'	 => $fPago,
				'numeroForma'	=> $numeroForma[$i],
				'valor'		 => $valor[$i],
				'tipo'		  => $typoPago
			);
			//Total por forma de pago
			if (!isset($formaPagos['totalFPago'][$typoPago])) {
				$formaPagos['totalFPago'][$typoPago] = 0;
			}
			$formaPagos['totalFPago'][$typoPago] += $val;
			//Total general
			$formaPagos['total'] += $val;
		}


		return $formaPagos;
	}


	/**
	* Metodo que calcula la diferencia de días entre dos fecha 360 dias de excel
	*/
	public static function calculoDias($fecha1='0000-00-00',$fecha2='0000-00-00') {
		error_reporting(false);
		try{
			$dias_calendario=0;
			$meses=0;
			//print $fecha1;exit;
			list($year1,$mon1,$day1) = explode('-',$fecha1);
			list($year2,$mon2,$day2) = explode('-',$fecha2);
			if ($day1>30) {
				$day1=30;
			}
			if ($day2>30) {
				$day2=30;
			}
			if ($year1>$year2) {
				$meses=$meses+(12-$mon2)+($mon1-1)+1;
				if ($day1>$day2) {
					$dias_diferencia=$meses*30;
					$dias_diferencia=$dias_diferencia+($day1-$day2);
				}else{
					$dias_diferencia=$meses*30;
					$dias_diferencia=$dias_diferencia-($day2-$day1);
				}
				$anios=$year1-$year2;
				if ($anios>1) {
					$dias_calendario=360*($anios-1);
				}
				$dias_diferencia=$dias_diferencia+$dias_calendario;
			}else{
				$meses=$meses+($mon1-$mon2);
				if ($day1>$day2) {
					$dias_diferencia=$meses*30;
					$dias_diferencia=$dias_diferencia+($day1-$day2);
				}else{
					$dias_diferencia=$meses*30;
					$dias_diferencia=$dias_diferencia-($day2-$day1);
				}
			}
			return $dias_diferencia;
		}catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Metodo que ingresa un abono a reserva
	 *
	 * @param array $data(
	 *	'reservasId'		=>  $reservasId,
	 *	'fechaRecibo'		=>  $fechaRecibo,
	 *	'fechaPago'			=>  $fechaPago,
	 *	'formasPago'		=>  $formasPagos,
	 *	'cuentasId'			=>  $cuentasId,
	 *	'reciboProvisional'	=>  $reciboProvisional,
	 *	'ciudadPago'		=>  $ciudadPago,
	 *	'debug'				=>  true
	 * )
	 * @param ActiveRecordTransaction $transaction
	 * @return array $data
	 */
	public static function addAbonoReserva(&$data, $transaction) {
		//Validamos campos necesarios para el proceso
		$listRequired = array('reservasId', 'fechaPago', 'formasPago', 'cuentasId');
		foreach ($listRequired as $param) {
			if (!isset($data[$param])||(isset($data[$param])&&empty($data[$param]))) {
				throw new Exception('addAbonoReserva: El campo "'.$param.'" es requerido');
			}
		}
		//Asignamos lo datos por defecto
		$data['fechaRecibo']		= date('Y-m-d');
		$data['ciudadPago']			= '127591';
		$logger = new Logger('File', 'TpcClassReservaId'.$data['reservasId'].'.log');
		$data['logger'] = $logger;
		$logger->log('////////////////////////////////////////////////////');
		//Ingresamos abono a abono reservas
		$abonoReservas = new AbonoReservas();
		$abonoReservas->setTransaction($transaction);
		$abonoReservas->setReservasId($data['reservasId']);
		$abonoReservas->setValor($data['formasPago']['total']);
		$abonoReservas->setEstado('A');//Activo
		if ($abonoReservas->save()==false) {
			foreach ($abonoReservas->getMessages() as $message) {
				throw new Exception('Abono Reservas: '.$message->getMessage());
			}
		}
		//Agregamos el detalle de abono reservas
		//Detalle formas de pago
		//throw new Exception(print_r($data['formasPago'],true));
		if (isset($data['formasPago']['data'])) {
			//recorremos tipos de pagos agrupados
			foreach ($data['formasPago']['data'] as $tipoFP => $formasPago) {
				foreach ($formasPago as $fp) {
					//throw new Exception('<pre>'.print_r($fp,true).'</pre>');
					$detalleAbonoReservas = new DetalleAbonoReservas();
					$detalleAbonoReservas->setTransaction($transaction);
					$detalleAbonoReservas->setAbonoReservasId($abonoReservas->getId());
					$detalleAbonoReservas->setFormasPagoId($fp['formaPago']);
					$detalleAbonoReservas->setNumero($fp['numeroForma']);
					$detalleAbonoReservas->setValor($fp['valor']);
					if ($detalleAbonoReservas->save()==false) {
						foreach ($detalleAbonoReservas->getMessages() as $message) {
							throw new Exception($message->getMessage());
						}
					}
				}
			}
		}
		$data['abonoReservaId'] = $abonoReservas->getId();
		//cargamos datos
		$data['abonoReservasId'] 	= $abonoReservas->getId();
		$data['valorReserva'] 		= $data['formasPago']['total'];
		$data['valorPagado']		= $data['valorReserva'];
		//Creamos recibo de caja
		TPC::crearRecibosPagos($data, $transaction);
		return $data;
	}

	/**
	 * Metodo que ingresa un abono a otros conceptos de un contrato
	 *
	 * @param array $data(
	 *	'sociosId'			=> $sociosId,
	 *	'fechaPago'			=> $fechaPago,
	 *	'formasPago'		=> $formasPagos,
	 *	'cuentasId'			=> $cuentasId,
	 *	'reciboProvisional'	=> $reciboProvisional,
	 *	'detallePago'		=> $concepto,
	 *	'debug'				=> true
	 * )
	 * @param ActiveRecordTransaction $transaction
	 */
	public static function addAbonoOtros(&$data, $transaction) {
		//Validamos campos necesarios para el proceso
		$listRequired = array('sociosId', 'fechaPago', 'formasPago', 'cuentasId', 'detallePago');
		foreach ($listRequired as $param) {
			if (!isset($data[$param])||(isset($data[$param])&&empty($data[$param]))) {
				throw new Exception('El campo "'.$param.'" es requerido');
			}
		}
		//Asignamos lo datos por defecto
		$data['fechaRecibo']	= date('Y-m-d');
		$data['ciudadPago']		= '127591';
		$logger = new Logger('File', 'TpcClassSociosId'.$data['sociosId'].'.log');
		$data['logger'] = $logger;
		$logger->log('////////////////////////////////////////////////////');
		//Ingresamos abono a abono reservas
		$abonoOtros = new AbonoOtros();
		$abonoOtros->setTransaction($transaction);
		$abonoOtros->setSociosId($data['sociosId']);
		$abonoOtros->setValor($data['formasPago']['total']);
		$abonoOtros->setEstado('A');//Activo
		if ($abonoOtros->save()==false) {
			foreach ($abonoOtros->getMessages() as $message) {
				throw new Exception('Abono a Otros: '.$message->getMessage());
			}
		}
		//Agregamos el detalle de abono reservas
		//Detalle formas de pago
		if (isset($data['formasPago']['data'])) {
			//recorremos tipos de pagos agrupados
			foreach ($data['formasPago']['data'] as $tipoFP => $formasPago) {
				foreach ($formasPago as $fp) {
					$detalleAbonoOtros = new DetalleAbonoOtros();
					$detalleAbonoOtros->setTransaction($transaction);
					$detalleAbonoOtros->setAbonoOtrosId($abonoOtros->getId());
					$detalleAbonoOtros->setFormasPagoId($fp['formaPago']);
					$detalleAbonoOtros->setNumero($fp['numeroForma']);
					$detalleAbonoOtros->setValor($fp['valor']);
					if ($detalleAbonoOtros->save()==false) {
						foreach ($detalleAbonoOtros->getMessages() as $message) {
							throw new Exception($message->getMessage());
						}
					}
				}
			}
		}
		$data['abonoOtrosId'] = $abonoOtros->getId();
		//cargamos datos
		$data['abonoOtrosId']		= $abonoOtros->getId();
		$data['valorOtros']			= $data['formasPago']['total'];
		$data['valorPagado']		= $data['valorOtros'];
		$data['rcEstado']			= 'O';//Otros en recibo de caja
		//Creamos recibo de caja
		TPC::crearRecibosPagos($data, $transaction);
		//Aumentamos el consecutivo de Rc
		TPC::aumentarConsecutivoRc($transaction);
		return $data;
	}


	/**
	 * Metodo que recalcula recibos de caja de un contrato
	 *
	 * @param array $config(
	 *  'sociosId'	  => $record->socios_id,
	 *  'notaHistoria'  => array(
	 *	  'estado'	  => 'C',//Cambio de contrato
	 *	  'fecha'		=> '2011-08-01'
	 *	  'observacion' => 'Se realizó cambio de contrato ......',
	 *	  'copiarContrato' => true //copia el contenido del contrato en sus repectivas tabla h(historia)
	 *  ),
	 *  'debug'		 => true
	 * )
	 * @param $transaction
	 * @return $config
	 */
	public static function copiarAHistoria(&$config, $transaction) {
		if (!isset($config['sociosId'])) {
			if (!isset($config['reservasId'])) {
				throw new Exception('Recalcular Rc: El id del contrato/reserva es requerido');
			}
		}
		//Miramos si el socio es valido
		$sociosId	= 0;
		if (isset($config['sociosId'])) {
			$sociosId	= $config['sociosId'];
		}
		$reservasId	= 0;
		if (isset($config['reservasId'])) {
			$reservasId = $config['reservasId'];
		}
		if ($sociosId>0) {
			$socio = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($sociosId);
			if ($socio==false) {
				throw new Exception('Recalcular Rc: El id del contrato no existe');
			}
		}
		if ($reservasId>0) {
			$socio = EntityManager::get('Reservas')->setTransaction($transaction)->findFirst($reservasId);
			if ($socio==false) {
				throw new Exception('Recalcular Rc: El id de la reserva no existe');
			}
		}
		//Crea la nota de historia
		if (isset($config['notaHistoria']) && is_array($config['notaHistoria'])== true) {
			$dataHistoria = $config['notaHistoria'];
			//Si no damos el id de historia creamos una nueva con copia a el sociosId
			$dataHistoria['sociosId']	= $sociosId;
			$dataHistoria['reservasId']	= $reservasId;
			if (!isset($config['notaHistoria']['notaHistoriaId'])) {
				$notaHistoriaId = TPC::makeNotaHistoria($dataHistoria,$transaction);
				$dataHistoria['notaHistoriaId'] = $config['notaHistoriaId'] = $notaHistoriaId;
			}else{
				$dataHistoria['notaHistoriaId'] = $notaHistoriaId = $config['notaHistoria']['notaHistoriaId'];
			}
			//Si quiere que copiemos la info del contrato
			if (isset($config['notaHistoria']['copiarContrato'])==true && $config['notaHistoria']['copiarContrato']==true) {
				TPC::copiarContrato($dataHistoria, $transaction);
			}
			$config['notaHistoria'] = $dataHistoria;
		}
		return $config;
	}

	/**
	 * Metodo que copia todo el contenido de un contrato en sus respectiva tablas h(historia)
	 *
	 * @param array $dataHistoria
	 * @param TransactionManager $transaction
	 */
	static function copiarContrato(&$dataHistoria, $transaction) {
		//throw new Exception(print_r($dataHistoria,true));
		if (isset($dataHistoria['sociosId'])==false) {
			throw new Exception('EL id del socio a copiarContrato no esta definido');
		}
		if (isset($dataHistoria['notaHistoriaId'])==false) {
			throw new Exception('EL id de la notaHistoria a copiarContrato no esta definido');
		}
		$sociosId		= $dataHistoria['sociosId'];
		$notaHistoriaId	= $dataHistoria['notaHistoriaId'];
		
		//Recorremos tablas que relacionan un contrato y copiamos datos en tabals de historia
		$tableList = array('Socios', 'MembresiasSocios', 'DetalleCuota', 'PagoSaldo', 'Amortizacion',
		'RecibosPagos', 'ControlPagos');
		foreach ($tableList as $model) {
			$modelh = $model.'h';
			$tempModel = EntityManager::get($model)->setTransaction($transaction);
			
			//buscamos datos de el modelo
			$condition = 'id='.$sociosId;
			if ($model != 'Socios') {
				$condition = 'socios_id='.$sociosId;
			}
			$tempModelObj = $tempModel->find(array('conditions'=>$condition));
			
			//Recorremos registros de modelo
			foreach ($tempModelObj as $tempModelRow) {
				
				//Insertamos en modelo de historia
				$tempModelh = new $modelh();
				$tempModelh->setTransaction($transaction);
				foreach ($tempModelRow->getAttributes() as $field) {
					if ($field != 'id') {
						$tempModelh->writeAttribute($field,$tempModelRow->readAttribute($field));
					}
				}
				$tempModelh->setSociosId($sociosId);
				$tempModelh->setNotaHistoriaId($notaHistoriaId);
				if ($tempModelh->save() == false) {
					foreach ($tempModelh->getMessages() as $message) {
						throw new Exception($message->getMessage());
					}
				}
				
				//Copiamos el detalle del recibo de pago
				if ('RecibosPagosh'==$modelh) {
					$detalleRecibosPagos = EntityManager::get('DetalleRecibosPagos');
					$detalleRecibosPagos->setTransaction($transaction);
					$detalleRecibosPagosObj = $detalleRecibosPagos->find(array('conditions'=>'recibos_pagos_id='.$tempModelRow->getId()));
					if (count($detalleRecibosPagosObj) > 0) {
						foreach ($detalleRecibosPagosObj as $detalleReciboPago) {
							$detalleRecibosPagosh = new DetalleRecibosPagosh();
							$detalleRecibosPagosh->setTransaction($transaction);
							foreach ($detalleReciboPago->getAttributes() as $field) {
								if ($field != 'id') {
									$detalleRecibosPagosh->writeAttribute($field,$detalleReciboPago->readAttribute($field));
								}
							}
							$detalleRecibosPagosh->setNotaHistoriaId($notaHistoriaId);
							$detalleRecibosPagosh->setRecibosPagosId($tempModelh->getId());
							if ($detalleRecibosPagosh->save() == false) {
								foreach ($detalleRecibosPagosh->getMessages() as $message) {
									throw new Exception($message->getMessage());
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Metodo que recalcula los recibos de caja de un contrato
	 *
	 * @param ActiveRecord $socios
	 * @param TransactionManager $transaction
	 */
	public static function recalcularRecibosCaja($socios, $transaction) {
		$sociosId = $socios->getId();
		
		//Generamos formato de datos para recalcular de los recibos de caja existentes
		$formatos = TPC::crearFormatoDePagoDeRcs($sociosId, $transaction);
		
		//Limpiamos todos los pagos de un contrato
		TPC::limpiarAllPagos($sociosId,$transaction);
		
		//Recorremos los formatos de los recibos de caja existentes  y se vuelven a meter
		foreach ($formatos as $formato) {
			$formato['force'] = true;
			TPC::addAbonoContrato($formato, $transaction);
		}
	}

	/**
	 * Metodo que genera el formato para ingersar varios pagos por TPC::addAbonoContrato
	 * segun recibos de caja de ese sociosId
	 *
	 * @param array $config(
	 * 		'sociosId' 		Id de contrato que contiene los pagos
	 * 		'reservasId'	Id de reserva que contiene el recibo de pago
	 * 		'reciboPagoId'	Es opcional y cuando existe solo genera formato de ese id de recibo de pago
	 * 		'without'		Es para excluir id de recibos de caja en la busqueda
	 * 		'estado'		Es para buscar por estado de recibos de caja
	 * )
	 * @param Transactionmanager $transaction
	 * @return array $formato
	 */
	public static function crearFormatoDePagoDeRcs($config, $transaction) {
		$formato = array();
		$formatoBase = array(
			'fechaRecibo'		=> '',
			'fechaPago'			=> '',
			'formasPago'		=> array(),
			'cuentasId'			=> 0,
			'reciboProvisional'	=> '',
			'ciudadPago'		=> '',
			'rcReciboPago'		=> '',
			'rc_controlpago'	=> '',
			'debug'				=> true
		);
		//Filtro de estado
		$estado = 'estado IN("V", "K", "N")';
		if (isset($config['estado']) && is_array($config['estado'])) {
			$estado = 'estado IN("'.implode('", "', $config['estado']).'")';
		}
		//Obtenemos los pagos realizados
		$recibosPagos = EntityManager::get('RecibosPagos');
		//$recibosPagos->setTransaction($transaction);
		if (isset($config['sociosId'])) {
			$sociosId = $config['sociosId'];
			$formatoBase['sociosId'] = $sociosId;
			$conditions = 'socios_id='.$sociosId.' AND '.$estado;
		}
		if (isset($config['reservasId'])) {
			$reservasId = $config['reservasId'];
			$formatoBase['reservasId'] = $reservasId;
			$abonoReservasObj = EntityManager::get('AbonoReservas')->setTransaction(
			$transaction)->find(array('conditions'=>'reservas_id='.$reservasId));
			$abonoReservasArray = array();
			foreach ($abonoReservasObj as $abonoReserva) {
				$abonoReservasArray[]=$abonoReserva->getId();
			}
			if (count($abonoReservasArray)>0) {
				$conditions = 'abono_reservas_id IN('.implode(',', $abonoReservasArray).') AND '.$estado;
			}else{
				$conditions = '0=1';
			}
		}
		//Agrega para solo un formato
		if (isset($config['reciboPagoId'])) {
			$conditions .= ' AND id='.$config['reciboPagoId'];
		}
		//Agrega para excluir ids de recibos de caja
		if (isset($config['without']) && is_array($config['without'])) {
			$conditions .= ' AND id NOT IN('.implode(', ',$config['without']).')';
		}
		$recibosPagosObj = $recibosPagos->find(array('conditions'=>$conditions));
		$detalleRecibosPagos = EntityManager::get('DetalleRecibosPagos');
		//$detalleRecibosPagos->setTransaction($transaction);
		foreach ($recibosPagosObj as $reciboPago) {
			//Creamos formato para addAbonoContrato y addAbonoReserva de un recibo de caja existente
			$newFormato = $formatoBase;
			if (isset($config['reservasId'])) {
				$newFormato['reservasId'] = $config['reservasId'];
			}
			$newFormato['fechaRecibo'] = $reciboPago->getFechaRecibo()->getDate();
			$newFormato['fechaPago'] = $reciboPago->getFechaPago()->getDate();
			$newFormato['cuentasId'] = $reciboPago->getCuentasId();
			$newFormato['reciboProvisional'] = $reciboPago->getReciboProvisional();
			$newFormato['ciudadPago'] = $reciboPago->getCiudadPago();
			$newFormato['abonoReservasId'] = $reciboPago->getAbonoReservasId();
			$newFormato['rcReciboPago'] = $reciboPago->getRc();
			$newFormato['estado'] = $reciboPago->getEstado();
			$newFormato['rc_controlpago'] = $newFormato['rcReciboPago'];

			//Cargamos formato de formasPago
			$detalleRecibosPagosObj =$detalleRecibosPagos->find(array('conditions'=>'recibos_pagos_id='.$reciboPago->getId()));
			$dataFormas = array();
			foreach ($detalleRecibosPagosObj as $detalle) {
				$dataFormas['formaPago'][] = $detalle->getFormasPagoId();
				$dataFormas['numeroForma'][] = $detalle->getNumero();
				$dataFormas['valor'][] = $detalle->getValor();
			}
			if (isset($dataFormas['formaPago']) && count($dataFormas['formaPago'])>0) {
				$newFormato['formasPago'] = TPC::unificaFormasPagos($dataFormas, $transaction);
				$formato[]=$newFormato;
			}

			if ($reciboPago->getEstado()=='N') { //NOTA CONTABLE
				
				$dataFormas['debCre']		= $reciboPago->getDebCre();//Debito/Credito
				$dataFormas['onlyCapital']	= true;
				$dataFormas['notaContable']	= true;
				$dataFormas['sinIntereses']	= true;
				$conntrolPagos = EntityManager::get('ControlPagos')->findFirst(array('conditions'=>"rc='{$reciboPago->getRc()}'"));
				$dataFormas['saldoIdeal']	= $conntrolPagos->getSaldo();

				$dataFormas['setValidar']	= false; //Force
							
			}
		}
		//throw new Exception(print_r($formato, true));
		return $formato;
	}

	/**
	 * Metodo que copia un contrato con todos sus datos y los pasa  a uno nuevo segun tipo de contrato
	 *
	 * @param array $config(
	 *  sociosId,
	 *  tipoContratoId
	 * )
	 * @param TransactionManager $transaction
	 *
	 * @return $sociosId New
	 */
	public static function copiarANuevoContrato($config, $transaction) {
		if (!isset($config['sociosId'])) {
			throw new Exception('copiarANuevoContrato: Debe dar el id del contrato');
		}
		$socio = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($config['sociosId']);
		if ($socio == false) {
			throw new Exception('El contrato no existe');
		}
		$sociosIdOld = $socio->getId();
		if (!isset($config['tipoContratoId'])) {
			throw new Exception('Debe dar el id del tipo de contrato');
		}
		$tipoContrato = EntityManager::get('TipoContrato')->setTransaction($transaction)->findFirst($config['tipoContratoId']);
		if ($tipoContrato==false) {
			throw new Exception('El tipo de contrato no existe');
		}
		//copiamos socios en un nuevo activerecord de socios y lo creamos
		$socioNew = clone $socio;
		$socioNew->setTransaction($transaction);
		$socioNew->setValidar(false);
		$socioNew->setId(null);
		//cambiamos numero_contrato
		$numeroContratoNuevo = TPC::getFormatoContrato($transaction, $config['tipoContratoId']);
		//throw new Exception($socio->getNumeroContrato().', '.$numeroContratoNuevo.', '.$config['tipoContratoId']);
		$socioNew->setEstadoContrato('A');//Activo
		$socioNew->setEstadoMovimiento('CN');//'Nuevo contrato por cambio de contrato'
		$socioNew->setCambioContrato('S');//Es cambio de contrato
		$socioNew->setNumeroContrato($numeroContratoNuevo);
		$socioNew->setTipoContratoId($config['tipoContratoId']);
		//guardamos, nota: el aumento de consecutivo de tipo de contrato lo hace el modelo socios
		//por ser un nuevo registro
		if ($socioNew->save() == false) {
			foreach ($socioNew->getMessages() as $message) {
				throw new Exception($message->getMessage());
			}
		}
		$sociosIdNew = $socioNew->getId();
		//Cambiamos el estado del contrato
		$socio->setValidar(false);
		$socio->setEstadoContrato('AA');
		$socio->setEstadoMovimiento('AC');
		$socio->setSociosId($sociosIdNew);
		if ($socio->save()==false) {
			foreach ($socio->getMessages() as $message) {
				throw new Exception($message->getMessage());
			}
		}
		$socio->setValidar(true);
		
		Rcs::disable();
		$socioNew->setValidar(false);
		$socioNew->setEstadoContrato('A');//Activo
		$socioNew->setEstadoMovimiento('CN');//'Nuevo contrato por cambio de contrato'
		$socioNew->setNumeroContrato($numeroContratoNuevo);
		$socioNew->setTipoContratoId($config['tipoContratoId']);
		if ($socioNew->save() == false) {
			foreach ($socioNew->getMessages() as $message) {
				throw new Exception($message->getMessage());
			}
		}
		Rcs::enable();
		$socioNew->setValidar(true);
		return $socioNew;
	}

	/**
	 * Metodo que traslada los recibos de caja de un contrato a otro
	 *
	 * @param TransactionManager $transaction
	 * @param integer $sociosIdOld
	 * @param integer $sociosIdNew
	 */
	public static function trasladarRecibosCaja($transaction, $sociosIdOld, $sociosIdNew) {
		if (!$sociosIdOld) {
			throw new Exception('Es necesario ingresar el id del socio a cambiar contrato');
		}
		$sociosOld = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($sociosIdOld);
		if ($sociosOld==false) {
			throw new Exception('El contrato a cambiar contrato no existe');
		}
		if (!$sociosIdNew) {
			throw new Exception('Es necesario ingresar el id del socio a cambiar contrato');
		}
		$sociosNew = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($sociosIdNew);
		if ($sociosNew==false) {
			throw new Exception('El contrato nuevo a pasar el cambio de contrato no existe');
		}
		Rcs::disable();
		//Cambiamos estados a los recibos de caja de anterior contrato
		$recibosPagosObj = EntityManager::get('RecibosPagos')->setTransaction($transaction)->find(array('conditions'=>'socios_id='.$sociosIdOld));
		foreach ($recibosPagosObj as $reciboPago) {
			$reciboPago->setTransaction($transaction);
			$reciboPago->setValidar(false);
			$reciboPago->setEstado('C');//Cambiado (por cambio de contrato)
			if ($reciboPago->save()==false) {
				foreach ($reciboPago->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
			}
		}
		//throw new Exception($sociosIdOld.', '.$sociosIdNew);
		//Limpiamos pagos de nuevo contrato
		TPC::limpiarAllPagos($sociosIdNew, $transaction, false);
		//Generamos formato de datos para recalcular de los recibos de caja existentes a un id de socios
		$formatos = TPC::crearFormatoDePagoDeRcs(array('sociosId'=>$sociosIdOld), $transaction);
		//throw new Exception($sociosIdOld.', '.$sociosIdNew.', formatos: '.print_r($formatos,true));
		//Insertamos el pago nuevo al nuevo contrato
		foreach ($formatos as $formato) {
			$formato['sociosId'] = $sociosIdNew;//asignamos nuevo id de socio
			$formato['setValidar'] = false;//Decimos que no valide nada en recibos pagos
			TPC::addAbonoContrato($formato, $transaction);
		}
		Rcs::enable();
	}

	/**
	 * Metodo que anula un recibo de caja y recalcula todos los pagos sin ese recibo de caja
	 *
	 * @param Array $config(
	 * 		'reciboPagoId' 		Es el id del recibo de caja que vamos a eliminar
	 * 		'notaHistoriaId'	Es el id de una historia si ya existe una historia que enlazar
	 * )
	 * @param ActiveRecordTransaction $transaction
	 */
	public static function anularReciboCaja(&$config, $transaction) {
		if (!isset($config['reciboPagoId'])) {
			throw new Exception('anularReciboCaja: Para poder anular y recalcular debe ingresar el id del recibo de caja');
		}
		$reciboPagoId = $config['reciboPagoId'];
		$recibosPagos = EntityManager::get('RecibosPagos')->setTransaction($transaction)->findFirst($reciboPagoId);
		if ($recibosPagos==false) {
			throw new Exception('anularReciboCaja: El recibo de caja no existe');
		}
		$sociosId = 0;
		if ($recibosPagos->getSociosId()>0) {
			$sociosId = $recibosPagos->getSociosId();
			$socio = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($sociosId);
			if ($socio==false) {
				throw new Exception('anularReciboCaja: El contrato proporcionado no existe');
			}
		}
		//Cuando se anula un abono a reserva
		$reservasId = 0;
		if ($recibosPagos->getAbonoReservasId()>0) {
			$abonoReservasId = $recibosPagos->getAbonoReservasId();
			$abonoReservas = EntityManager::get('AbonoReservas')->setTransaction($transaction)->findFirst($abonoReservasId);
			if ($abonoReservas==false) {
				throw new Exception('El abono a reserva no existe');
			}
			$socio = EntityManager::get('Reservas')->setTransaction($transaction)->findFirst($abonoReservas->getReservasId());
			if ($socio==false) {
				throw new Exception('La reserva del recibo de caja no existe');
			}
			$reservasId = $socio->getId();
		}
		
		//Si proporciona la historia no la crea
		if (isset($config['notaHistoriaId'])) {
			$notaHistoriaId = $config['notaHistoriaId'];
		}else{
			//Como no ingreso un id de historia crea una con información de la anulacion del recibo de caja
			$detalleHistoria = 'Se anulo el recibo de caja '.$recibosPagos->getRc().' y se recalculo los demás recibos de caja del contrato '.$socio->getNumeroContrato();
			$configHistoria = array(
				'sociosId'			=> $sociosId,
				'reservasId'		=> $reservasId,
				'notaHistoria'		=> array(
					'estado'		=> 'A', //Recibo de caja Anulado
					'fecha'			=> date('Y-m-d'),
					'rcErrados'		=> $reciboPagoId,
					'observaciones' => $detalleHistoria,
					'copiarContrato'=> true //copia el contenido del contrato en sus repectivas tabla h(historia)
				),
				'debug'				=> true
			);
			TPC::copiarAHistoria($configHistoria, $transaction);
			$notaHistoriaId = $configHistoria['notaHistoriaId'];
			$config['notaHistoriaId'] = $notaHistoriaId;
		}
		//Generamos formato de datos para recalcular de los recibos de caja existentes a un id de socios
		$formatosAnuladosObj = EntityManager::get('RecibosPagos')->find(array('conditions'=>'socios_id='.$sociosId.' AND estado NOT IN ("V")'));
		//Si es >0 es contratos
		if ($recibosPagos->getSociosId()>0) {
			$configFormato = array('sociosId'=>$recibosPagos->getSociosId(), 'without'=>array($reciboPagoId));
		}else{
			//Sino son abono a reservas
			$abonoReservasId = $recibosPagos->getAbonoReservasId();
			$reservaId = EntityManager::get('AbonoReservas')->findFirst($recibosPagos->getAbonoReservasId())->getReservasId();
			$configFormato = array('reservasId'=>$reservaId, 'without'=>array($reciboPagoId));
		}
		//Generamos formato de datos para recalcular de los recibos de caja existentes a un id de socios
		$formatos = TPC::crearFormatoDePagoDeRcs($configFormato, $transaction);
		//throw new Exception(print_r($formatos, true));
		//Limpiamos los pagos de contrato
		if ($sociosId>0) {
			TPC::limpiarAllPagos($sociosId, $transaction);
			//Ingresamos de nuevo los pagos sin el anulado
			foreach ($formatos as $formato) {
				$formato['setValidar'] = false;//Decimos que no valide nada en recibos pagos
				if ($recibosPagos->getSociosId()>0) {
					if ($formato['estado']=='V') {//Abono
						TPC::addAbonoContrato($formato, $transaction);
					}
					if ($formato['estado']=='K') {//Capital
						TPC::addAbonoCapitalContrato($formato, $transaction);
					}
					if ($formato['estado']=='N') {//NOta Contable
						TPC::addNotaContableContrato($formato, $transaction);
					}
				}
			}
			$idRc = 0;
			$idDRc = 0;
			//Ingresamos de nuevo los pagos sin el anulado
			foreach ($formatosAnuladosObj as $formatosAnulados) {
				//insertamos
				$newTemp = new RecibosPagos();
				$newTemp->setTransaction($transaction);
				foreach ($formatosAnulados->getAttributes() as $field) {
					$newTemp->writeAttribute($field, $formatosAnulados->readAttribute($field));
				}
				$newTemp->setId(Null);
				if ($newTemp->save()==false) {
					foreach ($newTemp->getMessages() as $message) {
						throw new Exception($message->getMessage());
					}
				}else{
					$idRc = $newTemp->getId();
					//Copiamos el detalle
					$detalleNewTempObj = EntityManager::get('DetalleRecibosPagos')->find(array('conditions'=>'recibos_pagos_id='.$formatosAnulados->getId()));
					foreach ($detalleNewTempObj as $detalleAnulado) {
						//COpiamos de recibos_pagos
						$detalleNewTemp = new DetalleRecibosPagos();
						$detalleNewTemp->setTransaction($transaction);
						foreach ($detalleAnulado->getAttributes() as $field) {
							$detalleNewTemp->writeAttribute($field, $detalleAnulado->readAttribute($field));
						}
						$detalleNewTemp->setId(Null);
						$detalleNewTemp->setRecibosPagosId($newTemp->getId());
						if ($detalleNewTemp->save()==false) {
							foreach ($detalleNewTemp->getMessages() as $message) {
								throw new Exception($message->getMessage());
							}
						}
						$idDRc = $detalleNewTemp->getRecibosPagosId();
					}
				}
			}
			//throw new Exception($idRc.', '.$idDRc);
			//Insertamos de nuevo el RC que vamos a anular con estado anulado
			$recibosPagosAnulado = new RecibosPagos();
			$recibosPagosAnulado->setTransaction($transaction);
			$recibosPagosAnulado->setValidar(false);
			foreach ($recibosPagos->getAttributes() as $field) {
				$recibosPagosAnulado->writeAttribute($field, $recibosPagos->readAttribute($field));
			}
			$recibosPagosAnulado->setId(Null);
			$recibosPagosAnulado->setEstado('A');//Anulado
			if ($recibosPagosAnulado->save()==false) {
				foreach ($recibosPagosAnulado->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
			}else{
				$detalleRecibosPagosAnuladoObj = EntityManager::get('DetalleRecibosPagos')->find(array('conditions'=>'recibos_pagos_id='.$recibosPagos->getId()));
				foreach ($detalleRecibosPagosAnuladoObj as $detalleRecibosPagos) {
					$detalleRecibosPagosAnulado = new DetalleRecibosPagos();
					$detalleRecibosPagosAnulado->setTransaction($transaction);
					foreach ($detalleRecibosPagos->getAttributes() as $field) {
						$detalleRecibosPagosAnulado->writeAttribute($field, $detalleRecibosPagos->readAttribute($field));
					}
					$detalleRecibosPagosAnulado->setId(Null);
					$detalleRecibosPagosAnulado->setRecibosPagosId($recibosPagosAnulado->getId());
					if ($detalleRecibosPagosAnulado->save()==false) {
						foreach ($detalleRecibosPagosAnulado->getMessages() as $message) {
							throw new Exception($message->getMessage());
						}
					}
				}
			}
		}
		//Si es anular un abono a reserva solo cambia estado de ese recibo de caja ya que no afecta nada
		if ($reservasId>0) {
			$recibosPagos->setValidar(false);
			$recibosPagos->setEstado('A');//Anulado
			if ($recibosPagos->save()==false) {
				foreach ($recibosPagos->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
			}
		}
		//throw new Exception('rcAnuladoIdNew: '.$recibosPagosAnulado->getId().'/Rc: '.$recibosPagosAnulado->getRc().'/ Estado: '.$recibosPagosAnulado->getEstado().', reciboPagoId: '.$reciboPagoId.', rcAnulado: '.$recibosPagos->getRc().' notaHistoriaId: '.$notaHistoriaId.'/isset: '.isset($config['notaHistoriaId']).', '.print_r($formatos,true));
	}

	/**
	 * Metodo que valida si existe un campo en el array
	 * 
	 * @param $fields(
	 * 		array(
	 * 			'name'		Es el nombre del campo
	 * 			'message'	Es el mensaje si sale error
	 * 		),
	 * 		....
	 * )
	 * @param $config	Es un array con key el name de fields
	 * @param $transaction
	 */
	static function validateInArray($fields, $config, $transaction) {
		if (is_array($fields)==true && is_array($config)==true) {
			foreach ($fields as $field) {
				if (!isset($config[$field['name']])) {
					throw new Exception($field['message']);
				}
			}
		}else{
			throw new Exception('_validateInArray: Los parametros no estan bien para validar');
		}
	}

	/**
	 * Metodo que valida que un registroe n un modelo exista
	 * 
	 * @params $config(
	 * 		array(
	 * 			'model'		Es el nombre del modelo
	 * 			'conditions'	Es la condicion de la consulta
	 * 			'message'	Es el mensaje si no existe
	 * 		),
	 * 		....
	 * )
	 * @param $transaction
	 */
	static function validarExistenciaDB($config, $transaction) {
		if (is_array($config)==true) {
			foreach ($config as $conf) {
				//throw new Exception(print_r($conf, true));
				$modelTemp = EntityManager::get($conf['model'])->setTransaction($transaction)->findFirst(array('conditions'=>$conf['conditions']));
				if ($modelTemp==false) {
					throw new Exception($conf['message']);
				}
			}
		}else{
			throw new Exception('Por favor ingresa datos vaidos');
		}
	}

	/**
	 * Metodo que coge el capital del contrato viejo y lo aplica al nuevo sin generar recibos de caja
	 * 
	 * @param $config(
	 * 		'sociosIdOld' Es el id del contrato viejo
	 * 		'sociosIdNew' Es el id del contrato nuevo a pasar saldo
	 * )
	 * @param $transaction
	 */
	static function cambioContratoCapital(&$config, $transaction) {
		//Validamos la presencia de campos en $config
		$fields = array(
			array('name'=>'sociosIdOld', 'message'=>'El id del contrato antiguo es requerido'),
			array('name'=>'sociosIdNew', 'message'=>'El id del contrato nuevo es requerido')
		);
		TPC::validateInArray($fields, $config, $transaction);
		//Validamos la existencia de los contratos y membresia
		$confDbExists = array(
			array('model'=>'Socios', 'conditions'=>'id='.$config['sociosIdOld'], 'message'=>'El contrato anterior no existe'),
			array('model'=>'Socios', 'conditions'=>'id='.$config['sociosIdNew'], 'message'=>'El contrato nuevo no existe'),
			array('model'=>'MembresiasSocios', 'conditions'=>'socios_id='.$config['sociosIdOld'], 'message'=>'El contrato viejo no tiene membresia'),
			array('model'=>'DetalleCuota', 'conditions'=>'socios_id='.$config['sociosIdOld'], 'message'=>'El contrato viejo no tiene cuotas iniciales')
		);
		TPC::validarExistenciaDB($confDbExists, $transaction);
		//Inicializamos valores pagados
		$config['afiliacionPagado'] = 0;
		$config['cuotasInicialPagado'] = 0;
		$config['saldoAfavor'] = 0;
		$config['totalContrato'] = 0;
		//Obtenemos lo pagado en derechos de afiliación
		$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$config['sociosIdOld']));
		$afiliacionPagado = $membresiasSocios->getAfiliacionPagado();
		//Obtenemos lo pagado en cuotas iniciales
		$detalleCuota = EntityManager::get('DetalleCuota')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$config['sociosIdOld']));
		$cuotasInicialPagado = $detalleCuota->getHoyPagado() + $detalleCuota->getCuota2Pagado() + $detalleCuota->getCuota3Pagado();
		//Obtenemos el capital pagado si toco financiación
		$capitalTotal = EntityManager::get('RecibosPagos')->setTransaction($transaction)->sum(array('valor_capital', 'conditions'=>'socios_id='.$config['sociosIdOld']));
		//Sacamos si ya antes tenia un cambio de contrato
		$socioOld = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($config['sociosIdOld']);
		if ($socioOld==false) {
			throw new Exception('SociosOld not exists '.$config['sociosIdOld']);
		}
		$valorCambioContratoAnterior = $socioOld->getValorCambioContrato();
		//Si hay valor de cambio de contrato anterior sume a capital ya que estos no tiene recibos de caja
		if ($valorCambioContratoAnterior>0) {
			$capitalTotal += $valorCambioContratoAnterior;
		}
		//TotalContrato
		$totalContrato = $afiliacionPagado + $cuotasInicialPagado + $capitalTotal;
		$totalContrato2 = $totalContrato;
		//return
		$config['afiliacionPagado'] = $afiliacionPagado;
		$config['cuotasInicialPagado'] = $cuotasInicialPagado;
		$config['totalCapital'] = $capitalTotal;
		$config['totalContrato'] = $totalContrato;
		
		//throw new Exception('AFI: '.$afiliacionPagado.', CI: '.$cuotasInicialPagado.', K: '.$capitalTotal.', TT: '.$totalContrato);
		
		//Aplicamos afiliacion en el nuevo contrato
		/*$membresiasSociosNew = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$config['sociosIdNew']));
		if ($membresiasSociosNew==false) {
			throw new Exception('La membresia del contrato nuevo no existe');
		}
		$derechoAfiliacion = $membresiasSociosNew->getDerechoAfiliacion();
		if ($totalContrato>=$derechoAfiliacion->getValor()) {
			//$membresiasSociosNew->setAfiliacionPagado($derechoAfiliacion->getValor());
			$config['afiliacionPagado'] = $derechoAfiliacion->getValor();
			//descontamos el valor de derecho de afiliación al total pagado de contrato anterior
			$totalContrato -= $derechoAfiliacion->getValor();
			//$membresiasSociosNew->setEstadoCuoafi('P');//Pagado
			
			//Aplicamos cuotas iniciales en el nuevo contrato
			$detalleCuotaNew = EntityManager::get('DetalleCuota')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$config['sociosIdNew']));
			if ($detalleCuotaNew==false) {
				throw new Exception('La cuota inicial del contrato nuevo no existe');
			}
			//Aplicando a cuota 1
			if ($totalContrato>=$detalleCuotaNew->getHoy()) {
				//$detalleCuotaNew->setHoyPagado($detalleCuotaNew->getHoy());
				$config['cuotasInicialPagado'][0] = $detalleCuotaNew->getHoy();
				//$detalleCuotaNew->setEstado1('P');//Pagado
				$totalContrato -= $detalleCuotaNew->getHoy();
				
				//Aplicando a cuota 2
				if ($totalContrato>=$detalleCuotaNew->getCuota2()) {
					//$detalleCuotaNew->setCuota2Pagado($detalleCuotaNew->getCuota2());
					$config['cuotasInicialPagado'][1] = $detalleCuotaNew->getCuota2();
					//$detalleCuotaNew->setEstado2('P');//Pagado
					$totalContrato -= $detalleCuotaNew->getCuota2();
					
					//Aplicando a cuota 3
					if ($totalContrato>=$detalleCuotaNew->getCuota3()) {
						//$detalleCuotaNew->setCuota3Pagado($detalleCuotaNew->getCuota3());
						$config['cuotasInicialPagado'][2] = $detalleCuotaNew->getCuota3();
						//$detalleCuotaNew->setEstado3('P');//Pagado
						$totalContrato -= $detalleCuotaNew->getCuota3();
						
						//miramos si hay saldo a favor y hay lo metemos a saldo a favor
						if ($totalContrato>0) {
							$config['saldoAfavor'] = $totalContrato;
							$saldoAfavor = EntityManager::get('SaldoAfavor')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$config['sociosIdNew']));
							if ($saldoAfavor==false) {
								$saldoAfavor = EntityManager::get('SaldoAfavor', true)->setTransaction($transaction);
								$saldoAfavor->setSociosId($config['sociosIdNew']);
							}
							$saldoAfavor->setValor($saldoAfavor->getValor() + $totalContrato);
							if ($saldoAfavor->save()==false) {
								foreach ($saldoAfavor->getMessages() as $message) {
									throw new Exception($message->getMessage());
								}
							}
							$totalContrato = 0;
						}
					}else{
						//$detalleCuotaNew->setCuota3Pagado($totalContrato);
						$config['cuotasInicialPagado'][2] = $totalContrato;
					}
				}else{
					//$detalleCuotaNew->setCuota2Pagado($totalContrato);
					$config['cuotasInicialPagado'][1] = $totalContrato;
				}
			}else{
				//$detalleCuotaNew->setHoyPagado($totalContrato);
				$config['cuotasInicialPagado'][0] = $totalContrato;
			}
		}else{
			//$membresiasSociosNew->setAfiliacionPagado($totalContrato);
			$config['afiliacionPagado'] = $totalContrato;
		}*/
		
		//throw new Exception('AFI: '.$afiliacionPagado.', CI: '.$cuotasInicialPagado.', K: '.$capitalTotal.', TT: '.$totalContrato2.', NewAFI: '.$membresiasSociosNew->getAfiliacionPagado().', CI1: '.$detalleCuotaNew->getHoyPagado().'/'.$detalleCuotaNew->getEstado1().', CI2: '.$detalleCuotaNew->getCuota2Pagado().', CI3: '.$detalleCuotaNew->getCuota3Pagado());
	}

	/**
	 * Obtiene el total en capital para la cuota inicial para el nuevo contrato del contrato antiguo segun reglas de cambio de contrato
	 * 
	 * @param array $config(
	 * 	'SociosId' //Id de socio antiguo
	 * )
	 * @param ActiveRecordTransaction $transaction(
	 */
	public static function getCuotaInicialCambioContrato(&$config, $transaction) {
		if (isset($config['SociosId'])==false || $config['SociosId']<=0) {
			throw new Exception('El id del socio es requerido');
		}
		$sociosId = $config['SociosId'];
		$socio = EntityManager::get('Socios')->setTransaction($transaction)->findFirst($sociosId);
		if ($socio==false) {
			throw new Exception('El contrato no existe');
		}
		$detalleCuota = EntityManager::get('DetalleCuota')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$sociosId));
		if ($detalleCuota==false) {
			throw new Exception('No se encontro cuotas inniciales de contrato');
		}
		$detalleCuotaTotal = $detalleCuota->getHoyPagado() + $detalleCuota->getCuota2() + $detalleCuota->getCuota3();
		//Obtenemos la suma de capitales
		$capitalTotal = EntityManager::get('RecibosPagos')->setTransaction($transaction)->sum(array('valor_capital','conditions'=>'socios_id='.$sociosId.' AND estado IN ("V","N")'));
		//valores de antiguo contrato
		$membresiaSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$sociosId));
		if ($membresiaSocios==false) {
			throw new Exception('NO existe Membresia en el contrato');
		}
		//valor dereco de afiliacion
		$derechoAfiliacion = EntityManager::get('DerechoAfiliacion')->setTransaction($transaction)->findFirst($membresiaSocios->getDerechoAfiliacionId());
		if ($detalleCuota==false) {
			$derechoAfiliacion->rollback('No se encontro derecho de afilaición del contrato');
		}
		
		//nueva cuota inicial
		$nuevaCuotaInicial				= LocaleMath::round(($capitalTotal + $detalleCuotaTotal),0);
		$config['nuevaCuotaInicial']	= $nuevaCuotaInicial;
		$config['capitalTotal']			= $capitalTotal;
		$config['detalleCuotaTotal']	= $detalleCuotaTotal;
		$config['saldoPagar']			= $membresiaSocios->getValorTotal();
		$config['valorDerechoAfiliacion']	= $derechoAfiliacion->getValor();
	}

	/**
	* Busca el ultimo pago realizado a u contrato
	*
	* @param TransactionMananger$transaction
	* @param int $sociosId 
	* @return array
	*/
	public static function getUltimoReciboPago($transaction=false, $sociosId) {
		if (!$sociosId) {
			throw new Exception('getUltimoReciboPago: Ingrese un valor a \$sociosId');
		}
		if ($transaction) {
			$reciboPago = EntityManager::get('RecibosPagos')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$sociosId.' AND estado IN ("V","N")','order'=>'fecha_pago DESC'));	
		} else {
			$reciboPago = EntityManager::get('RecibosPagos')->findFirst(array('conditions'=>'socios_id='.$sociosId.' AND estado IN ("V","N")','order'=>'fecha_pago DESC'));
		}
		
		return $reciboPago;
	}

	/**
	* Verifica si un socio debe el derecho de afiliación
	*
	* @param int $sociosId
	* @return boolean
	*/
	public static function debeDerechoAfiliacion($sociosId, $transaction=false){
		
		if ($transaction) {
			$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		} else {
			$membresiasSocios = EntityManager::get('MembresiasSocios')->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		}
		
		if ($membresiasSocios==false) {
			throw new Exception("El socios con id '$sociosId' no tiene información de membresias", 1);			
		}

		$flag = True;
		if ($membresiasSocios->getEstadoCuoafi()=='P'){
			$flag = false;
		}
		
		return $flag;
	}

	/**
	* Verifica si un socio debe el derecho de afiliación
	*
	* @param int $sociosId
	* @return boolean
	*/
	public static function getValorDerechoAfiliacion($sociosId, $transaction=false){
		
		if ($transaction) {
			$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		} else {
			$membresiasSocios = EntityManager::get('MembresiasSocios')->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		}
		
		if ($membresiasSocios==false) {
			throw new Exception("El socios con id '$sociosId' no tiene información de membresias", 1);			
		}
		$derechoAfiliacionId = $membresiasSocios->getDerechoAfiliacionId();

		if ($transaction) {
			$derechoAfiliacion = EntityManager::get('DerechoAfiliacion')->setTransaction($transaction)->findFirst($derechoAfiliacionId);
		} else {
			$derechoAfiliacion = EntityManager::get('DerechoAfiliacion')->findFirst($derechoAfiliacionId);
		}

		if ($derechoAfiliacion==false) {
			throw new Exception("No existe un registro de derecho de afiliación a la membresia del socio con id '$sociosId'", 1);
		}

		$valor = $derechoAfiliacion->getValor();

		return $valor;
	}

	/**
	* Obtiene el periodo actual abierto
	*
	* @return int
	*/
	public static function periodoActual() 
	{
		$periodo = EntityManager::get('Periodo')->findFirst(array('conditions' => 'cierre="N"', 'order' => 'periodo DESC'));
		if ($periodo==false) {
			throw new Exception("No se ha registrado un periodo abierto", 1);			
		}
		return $periodo->getPeriodo();
	}

	/**
	* Devuelve valor y saldo de Cuota inicila de un socio
	*
	* @param int $sociosId
	* @return array('saldo'=>0, 'valor' => 200000)
	*/
	public static function getValorCuotaInicial($sociosId, $transaction=false){

		#Sacamos valor cuota inicial
		if ($transaction) {
			$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		} else {
			$membresiasSocios = EntityManager::get('MembresiasSocios')->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		}
		
		if ($membresiasSocios==false) {
			throw new Exception("El socios con id '$sociosId' no tiene información de membresias", 1);			
		}

		#Verificamos el saldo de la cuota inicial
		if ($transaction) {
			$detalleCuota = EntityManager::get('DetalleCuota')->setTransaction($transaction)->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		} else {
			$detalleCuota = EntityManager::get('DetalleCuota')->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		}
		
		if ($detalleCuota==false) {
			throw new Exception("El socios con id '$sociosId' no tiene información de detalle de cuotas iniciales", 1);			
		}

		$pagado = 0;

		#cuota1
		if ($detalleCuota->getHoyPagado()>0) {
			$pagado += $detalleCuota->getHoyPagado();
		}
		#cuota2
		if ($detalleCuota->getCuota2Pagado()>0) {
			$pagado += $detalleCuota->getCuota2Pagado();
		}
		#cuota3
		if ($detalleCuota->getCuota3Pagado()>0) {
			$pagado += $detalleCuota->getCuota3Pagado();
		}

		#sacamos la diferencia de lo que se debe con lo pagado
		$valor = $membresiasSocios->getCuotaInicial();

		$saldo = $valor;
		if ($pagado) {
			$saldo = $valor - $pagado;
		}

		$data = array(
			'valor' => $valor,
			'saldo' => $saldo
		);

		return $data;
	}

	/**
	* Devuelve valor y saldo de Financiación de un socio
	*
	* @param int $sociosId
	* @return array(
	*	'saldo'=>0, 'valor' => 200000,
	*	'diasCorriente'=>10, 'valorCorriente' => 1000,
	*	'diasMora'=>10, 'valorMora' => 2000,
	* )
	*/
	public static function getValorFinanciacion($sociosId, $transaction=false){

		#Sacamos valor cuota inicial
		if ($transaction) {
			$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($transaction)->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		} else {
			$membresiasSocios = EntityManager::get('MembresiasSocios')->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		}
		
		if ($membresiasSocios==false) {
			throw new Exception("El socio con id '$sociosId' no tiene información de membresias", 1);			
		}


		#Si no existen pagos es que aun no ha empezado financiación
		$valor = $membresiasSocios->getSaldoPagar();
		$saldo = $valor;

		$ultimoReciboPago = TPC::getUltimoReciboPago(null, $sociosId);

		#Validamos que ya esté en financiación
		if ($ultimoReciboPago!=false && $ultimoReciboPago->getCuotaSaldo()>0) {

			#Buscamos saldo actual del controlPagos
			if ($transaction) {
				$controlPagos  = EntityManager::get('ControlPagos')->setTransaction($transaction)->findFirst(array('conditions'=>"rc='{$ultimoReciboPago->getRc()}'"));
			} else {
				$controlPagos  = EntityManager::get('ControlPagos')->findFirst(array('conditions'=>"rc='{$ultimoReciboPago->getRc()}'"));	
			}
			
			if ($controlPagos==false) {
				throw new Exception("No existe un registro en control pagos con RC '{$ultimoReciboPago->getRc()}'", 1);
			}

			$saldo = $controlPagos->getSaldo();

		}

		$data = array(
			'valor' => $valor,
			'saldo' => $saldo
		);
		
		return $data;
	}

	/**
	* Funcion que arregla la fecha aactual y la convierte a fecha de corte de pago de TPC
	*
	* @param date $fecha
	* @return date
	*/
	public static function sanitizeFecha360($fecha)
	{
		$fechaSplit = explode('-',$fecha);
		$dia = $fechaSplit[2];

		$dia = 15;
		if ($dia > 15) {
			$dia = 30;
		}
		$fechaSplit[2] = $dia;

		$fecha = implode('-', $fechaSplit);

		return $fecha;
	}

}
