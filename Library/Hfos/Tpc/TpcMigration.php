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
error_reporting(E_ALL);
set_time_limit(0);

Core::importFromLibrary('Hfos/Tpc','TpcTests.php');
Core::importFromLibrary('Hfos/Tpc','TpcContratos.php');
/**
 * TpcMigration
 *
 * Clase componente que controla procesos de migración de datos desde archivmos migrados desde excel a json
 *
 */
class TpcMigration extends UserComponent {

	/**
	* @var Transaction
	*/
	private $_transaction;

	private $_limit;

	/**
	* Archivo JSON con datos de cuadros de excel de cartera
	* @var string $_carteraJson
	*/
	//private $_carteraJson = '/home/eduar/proyectos/MigracionTPC/a.json';
	private $_carteraJson = '/tmp/a.json';

	/**
	* Archivo JSON con datos de contabilidad
	* @var string $_contabJson
	*/
	//private $_contabTxt = '/home/eduar/proyectos/MigracionTPC/contab';
	private $_contabTxt = '/tmp/contab';

	/**
	*/
	public $blackList = Array ( 'TPC-G-07-025','TPC-G-07-045','TPC-G-07-117','TPC-G-07-205','TPC-G-07-215','TPC-G-07-217','TPC-G-07-242','TPC-G-07-243','TPC-G-07-251','TPC-G-07-261','TPC-G-07-268','TPC-G-07-277','TPC-G-07-293','TPC-G-07-301','TPC-G-07-314','TPC-G-07-331','TPC-G-07-336','TPC-G-07-359','TPC-G-07-378','TPC-G-07-403','TPC-G-07-423','TPC-G-08-1007','TPC-G-08-1090','TPC-G-08-1124','TPC-G-08-1139','TPC-G-08-1144','TPC-G-08-1149','TPC-G-08-428','TPC-G-08-431','TPC-G-08-925','TPC-G-08-982','TPC-G-09-1167','TPC-G-09-1190','TPC-G-09-1216','TPC-G-09-1244','TPC-G-09-1274','TPC-G-09-1277','TPC-G-09-1285','TPC-G-09-1286','TPC-G-09-1287','TPC-G-09-1296','TPC-G-09-1303','TPC-G-09-1315','TPC-G-09-1326','TPC-G-09-1340','TPC-G-09-1453','TPC-G-10-1759','TPC-G-10-1829','TPC-G-10-1833','TPC-G-10-1835','TPC-G-10-1850','TPC-G-10-1894','TPC-G-10-1922','TPC-G-10-1928','TPC-G-10-1947','TPC-G-10-1948','TPC-G-10-1969','TPC-G-10-1974','TPC-G-10-2162','TPC-G-11-2414','TPC-GX-09-0032','TPC-GX-09-0205','TPC-GX-09-0269','TPC-GX-10-0340','TPC-GX-10-0366','TPC-GX-10-0472','TPC-GX-11-0616','TPC-GX-11-0618' ) ;


	/**
	* Cuenta contable que contiene pago de derecho de afiliación
	* @var int
	*/
	private $_cuentaDerechoAfiliacion = 425050050001;
	
	/**
	* Cuenta contable que contiene el saldo de cuota inicial
	* @var int
	*/
	private $_cuentaCuotasIniciales = 130505010;

	/**
	* Cuenta contable que contiene el saldo de financiacion
	* @var int
	*/
	private $_cuentaFinanciacion = 130505011;

	/**
	* Lista de logs abiertos en proceso
	* @var array
	*/
	private $_loggers = array();

	/**
	* Lista de contadores de loggers
	* @var array
	*/
	private $_logCount = array();

	/**
	* Lista de contratos de prueba co casos especiales
	* @var array
	*/
	public $contratosTest = array('TPC-G-09-1397','TPC-G-11-2379','TPC-G-09-1383','TPC-G-09-1374','TPC-G-09-1477','TPC-G-11-2281','TPC-G-10-2080','TPC-GX-10-0351','TPC-G-09-1405','TPC-GX-11-0606','TPC-G-10-2049','TPC-G-09-1476','TPC-G-09-1456');

	public $notContratosTest = array('TPC-G-09-1476', 'TPC-GX-10-0448', 'TPC-GX-10-0445', 'TPC-GX-10-0447', 'TPC-G-10-1914','TPC-G-10-1916','TPC-G-10-1913','TPC-GX-09-0142');

	public function __construct() {
		$this->_transaction = TransactionManager::getUserTransaction();
	}

	/**
	 * Metodo principal de Test
	 */
	public function main() {

		try{		
			//creamos loggers
			$this->_getLogger('migradoReservas',array());
			$this->_getLogger('migradoReservasError',array());
			$this->_getLogger('migradoReservasIgnore',array());
			$this->_getLogger('migradoContratos',array());
			$this->_getLogger('migradoContratosIgnore',array());
			$this->_getLogger('migradoContratosFaltantes',array());
			$this->_getLogger('migradoContratosError',array());
			$this->_getLogger('migradoContratosSinAmortizacion');
			$this->_getLogger('migradoPagosSinRC',array());


			$this->_transaction = TransactionManager::getUserTransaction();
			
			//Limpiamos la BD
			//TpcTests::limpiarBD($this->_transaction);
			
			//Copiar de Socios_TPC a tiempo compartido v1.0
			//$this->_copyDataToTC();

			//Verifica información de nits.txt con contratos creados
			//$this->_checkDataFromContab($transaction);

			//Agregamos pagos de cartera
			$this->_addPagosCartera();
		}
		catch(Exception $e) {
			if ($this->_transaction) {
				$this->_transaction->rollback($e->getMessage());	
			}
			
		}
	}

	/**
	* Metodo que agrega la informacion de BD sociostpc a hfos_tc
	* 
	* @param ActiveRecordTransaction $transaction
	*/
	private function _copyDataToTC() {

		//add reservas
		$this->_copyReservas();

		//Copia contratos TPC
		$this->_copyContratos();
	}

	/**
	* Copia la informacion de contratos unicamente sin reservas a el nuevo
	*/
	private function _copyReservas() {
		//trae todos los registros de TPC-%
		$sociosTpcObj = EntityManager::get('SociosTpc')->setTransaction($this->_transaction)->find(array('conditions'=>'numero_contrato LIKE "RESERVA%"'/*.' AND numero_contrato>="TPC-G-11-2239"'*/,'order'=>'numero_contrato ASC'/*, 'limit'=>10*/));

		$this->_logCount['R']['Ignore'] = 0;
		$this->_logCount['R']['Error'] = 0;
		$this->_logCount['R']['Ok'] = 0;

		//Recorremos los contrato y pasamos a hfos_tc
		foreach ($sociosTpcObj as $sociosTpc) {
			$numeroContrato = $sociosTpc->getNumeroContrato();
			if ($numeroContrato && !in_array($numeroContrato, $this->blackList)) {
				echo print_r($numeroContrato,true), '<br>';

				//Validamos si va alista negra
				$blackName = array('ANULADO','N.A.', 'RESERVA');
				if (in_array(trim($sociosTpc->getNombres()), $blackName) || $sociosTpc->getIdentificacion()<=0 
				|| $sociosTpc->getFechaCompra()==NULL) {
					$this->blackList[] = $numeroContrato;
					
					//grabando en log
					$logger = $this->_getLogger('migradoReservasIgnore');
					$logger->log("La reserva '$numeroContrato' fue ignorada por tener como nombre ".$sociosTpc->getNombres());
					$this->_logCount['R']['Ignore']++;

					continue;
				}
	
				//agregamos los datos a tabla hfos_tc.socios
				$reservaNew = $this->_addToReserva($sociosTpc);

			}

		}
		//ignorados
		$logger = $this->_getLogger('migradoReservasIgnore');
		$logger->log("Total Ignorados: ".$this->_logCount['R']['Ignore']);

		//ok
		$logger = $this->_getLogger('migradoReservas');
		$logger->log("Total Insertados: ".$this->_logCount['R']['Ok']);

		//error
		$logger = $this->_getLogger('migradoReservasError');
		$logger->log("Total con error: ".$this->_logCount['R']['Error']);
			
		unset($sociosTpcObj);			
	}

	/**
	* Metodo que agrega la informacion de BD sociostpc.socios_tpc a hfos_tc.reservas
	* 
	* @param ActiveRecord $sociosTpc
	*/
	private function _addToReserva($sociosTpc) {
		$reservas = EntityManager::get('Reservas', true)->setTransaction($this->_transaction);
		$reservas->setvalidar(false);

		foreach ($sociosTpc->getAttributes() as $field) {
			if ($field!='id' && in_array($field, $reservas->getAttributes())==true) {
				$sociosVal = $sociosTpc->readAttribute($field);
				if ($sociosVal && $sociosVal!='0000-00-00') {
					echo  'adding socios field ', $field, '....',$sociosVal,'<br>';
					$reservas->writeAttribute($field, $sociosVal);
				}
			}
		}

		//Corrigiendo reservas por reserva
		$numeroContrato = $reservas->getNumeroContrato();
		$numeroContrato = strtoupper($numeroContrato);
		$numeroContrato = str_replace('RESERVAS', 'RESERVA', $numeroContrato);
		$reservas->setNumeroContrato($numeroContrato);

		//Ciudades_id, Como se maneja nuevo sistema de ciudades se migrara con la primera ciudad que se encuentre
		$config = array(
			'sociosNew' => $reservas,
			'sociosTpc' => $sociosTpc
		);
		$this->_checkCiudadesSocios($config);

		//Envio
		$envioCorreo = 'N';
		if ($reservas->getCorreo()) {
			$envioCorreo = 'S';
		}
		$reservas->setEnvioCorrespondencia($envioCorreo);//Si/NO

		//Celular
		if (!$sociosTpc->getCelular()) {
			$reservas->setCelular(111111111);
		}

		//direccion_residencia
		if (!$sociosTpc->getDireccionResidencia()) {
			$reservas->setDireccionResidencia('?????????');//?? por defecto
		}

		//ciudad_residencia
		if (!$sociosTpc->getCiudadResidencia()) {
			$reservas->setCiudadResidencia(127591);//Bogota por defecto si no tiene
		}

		//telefono resiendencia
		if (!$sociosTpc->getTelefonoResidencia()) {
			$reservas->setTelefonoResidencia(111111111);
		}

		//Tipo Socio
		if (!$reservas->getTipoSociosId()) {
			$reservas->setTipoSociosId(1);
		}

		if (!$reservas->getEstadoContrato()) {
			$reservas->setEstadoContrato('AA'); //anulado
		}

		$reservas->setEstadoMovimiento('R'); //reserva

		//Apellidos nulos asignele el nombre si esta lleno
		if (!$sociosTpc->getApellidos() && $sociosTpc->getNombres()) {
			$reservas->setApellidos($sociosTpc->getNombres());
		}

		if ($sociosTpc->getApellidos()=='RESERVA' && $sociosTpc->getNombres()=='RESERVA') {
			return false;
		}

		if ($reservas->save()==false) {
			print "<br>No se inserto al RESERVA: $numeroContrato ".print_r($reservas->getMessages(),true)."<br>";

			//grabando en log
			$logger = $this->_getLogger('migradoReservasError');
			$logger->log("#############$numeroContrato#############");
			$logger->log("La reserva '$numeroContrato' no se pudo insertar. ".print_r($reservas->getMessages(),true).' '.print_r($reservas,true),Logger::ERROR);
			$this->_logCount['R']['Error']++;

			/*foreach ($reservas->getMessages() as $message) {
				throw new Exception($message->getMessage());
			}*/
			return false;

		} else {
			//grabando en log
			$logger = $this->_getLogger('migradoReservas');
			$logger->log("La reserva '$numeroContrato' se inserto correctamente.");
			$this->_logCount['R']['Ok']++;

		}

		return $reservas;
	}

	/**
	* Copia la informacion de contratos unicamente sin reservas a el nuevo
	*/
	private function _copyContratos() {
		//trae todos los registros de TPC-%
		$sociosTpcObj = EntityManager::get('SociosTpc')->setTransaction($this->_transaction)->find(array('conditions'=>'numero_contrato NOT LIKE "RESERVA%"'/*.' AND numero_contrato>="TPC-G-09-1504"'*/,'order'=>'numero_contrato ASC'/*, 'limit'=>100*/));

		//Recorremos los contrato y pasamos a hfos_tc
		$this->_logCount['C']['Unknow'] = 0;
		$this->_logCount['C']['Ignore'] = 0;
		$this->_logCount['C']['Error'] = 0;
		$this->_logCount['C']['Ok'] = 0;

		foreach ($sociosTpcObj as $sociosTpc) {

			$numeroContrato = $sociosTpc->getNumeroContrato();
			if ($numeroContrato && !in_array($numeroContrato, $this->blackList)) {

				echo print_r($numeroContrato,true), '<br>';

				//Validamos si va alista negra
				$blackName = array('ANULADO','N.A.');
				
				if (in_array(trim($sociosTpc->getNombres()), $blackName) || $sociosTpc->getIdentificacion()<=0 
				|| $sociosTpc->getFechaCompra()==NULL) {
					$this->blackList[] = $numeroContrato;

					//grabando en log
					$logger = $this->_getLogger('migradoContratosIgnore');
					$logger->log("El contrato '$numeroContrato' fue ignorada por tener como nombre ".$sociosTpc->getNombres());
					$this->_logCount['C']['Ignore']++;

					continue;
				}
	
				//agregamos los datos a tabla hfos_tc.socios
				$socioNew = $this->_addToSocios($sociosTpc);

				if ($socioNew==false) {
					print "<br>$numeroContrato <h1>fallo!</h1>";
					continue;
				}

				$config = array(
					'sociosNew' => $socioNew,
					'sociosTpc' => $sociosTpc,
				);

				//agregamos los datos a tabla hfos_tc.conyuges
				$this->_addConyuges($config);

				//agregamos los datos a tabla hfos_tc.membresias_socios
				$status = $this->_addMembresiasSocios($config);

				if ($status==true) {

					//agregamos los datos a tabla hfos_tc.detalle_cuota
					$this->_addDetalleCuota($config);

					//agregamos los datos a tabla hfos_tc.pago_saldo
					$this->_addPagoSaldo($config);
						
					//creamos amortizacion del nuevo socio
					if (isset($config['fechaPrimeraCuota'])==true && '0000-00-00'!=$config['fechaPrimeraCuota']) {

						$status = TpcContratos::_makeAmortizacion($this->_transaction, $socioNew);
						
						if ($status==false) {
							
							//error
							$logger = $this->_getLogger('migradoContratosError');
							$logger->log("###########$numeroContrato###########");
							$logger->log("No se pudo generar la amortización. ");
							$this->_logCount['C']['Error']++;

						}else{
							
							//ok
							$logger = $this->_getLogger('migradoContratos');
							$logger->log("Se creo amortización correctamente");

						}
					}

				}
				unset($config);
			}

		}

		//verificar los consecutivos de los tipos de contratos
		$this->_checkConsecutivosTiposContratos();

		//ignore
		$logger = $this->_getLogger('migradoContratosIgnore');
		$logger->log("Total Ignorados: ".$this->_logCount['C']['Ignore']);

		//error
		$logger = $this->_getLogger('migradoContratosError');
		$logger->log("Total con error: ".$this->_logCount['C']['Error']);

		//ok
		$logger = $this->_getLogger('migradoContratos');
		$logger->log("Total Insertados: ".$this->_logCount['C']['Ok']);

		unset($sociosTpcObj);

	}

	/**
	* Asigna el maximo de consecutivo de un tipo de contrato
	*/
	private function _checkConsecutivosTiposContratos() {
		$tipoContratoObj = $this->tipoContrato->setTransaction($this->_transaction)->find();
		foreach ($tipoContratoObj as $tipoContrato) {
			$maxNumeroContrato = $this->Socios->setTransaction($this->_transaction)->maximum("numero_contrato", "conditions: numero_contrato like 'TPC-{$tipoContrato->getSigla()}-%'");
			$maxNumeroContratoSplit = explode('-', $maxNumeroContrato);
			//TPC[0], G[1], 12[2], 1234[3]
			$maxNumber = 0;
			if (isset($maxNumeroContratoSplit[3]) && $maxNumeroContratoSplit[3]) {
				$maxNumber = $maxNumeroContratoSplit[3];	
			}

			$tipoContrato->setNumero($maxNumber);

			if ($tipoContrato->save()==false) {
				throw new Exception('No se puedo agregar el consecutivo actual del contrato con sigla '.$tipoContrato->getSigla());
			}

			unset($tipoContrato);
		}
		unset($tipoContratoObj);
	}

	/**
	* Metodo que agrega la informacion de BD sociostpc.socios_tpc a hfos_tc.socios 
	* 
	* @param ActiveRecord $sociosTpc
	* @param ActiveRecordTransaction $transaction
	*/
	private function _addToSocios($sociosTpc) {

		$socios = EntityManager::get('Socios', true)->setTransaction($this->_transaction);
		$socios->setvalidar(false);

		foreach ($sociosTpc->getAttributes() as $field) {

			if ($field!='id' && in_array($field, $socios->getAttributes())==true) {

				$sociosVal = $sociosTpc->readAttribute($field);
				
				if ($sociosVal && $sociosVal!='0000-00-00') {
				
					echo  'adding socios field ', $field, '....',$sociosVal,'<br>';
					$socios->writeAttribute($field, $sociosVal);
				
				}
			}
		}

		//Ciudades_id, Como se maneja nuevo sistema de ciudades se migrara con la primera ciudad que se encuentre
		$config = array(
			'sociosNew' => $socios,
			'sociosTpc' => $sociosTpc
		);
		$this->_checkCiudadesSocios($config);

		//Envio
		$envioCorreo = 'N';
		if ($socios->getCorreo()) {
			$envioCorreo = 'S';
		}
		$socios->setEnvioCorrespondencia($envioCorreo);//Si/NO

		//Celular
		if (!$sociosTpc->getCelular()) {
			$socios->setCelular(111111111);
		}

		//ciudad_residencia
		if (!$sociosTpc->getCiudadResidencia()) {
			$socios->setCiudadResidencia(127591);//Bogota por defecto si no tiene
		}

		//telefono resiendencia
		if (!$sociosTpc->getTelefonoResidencia()) {
			$socios->setTelefonoResidencia(111111111);
		}

		//Tipo Socio
		if (!$socios->getTipoSociosId()) {
			$socios->setTipoSociosId(1);
		}

		//Apellidos nulos asignele el nombre si esta lleno
		if (!$sociosTpc->getApellidos() && $sociosTpc->getNombres()) {
			$socios->setApellidos($sociosTpc->getNombres());
		}

		//Tipo de contrato
		$numeroContrato = $sociosTpc->getNumeroContrato();
		$numeroaccionSplit = explode('-',$sociosTpc->getNumeroContrato());
		$tipoContratoId = 0;

		$tipoContratoChar = '';
		if (isset($numeroaccionSplit[1]) && $numeroaccionSplit[1]) {
			$tipoContratoChar = $numeroaccionSplit[1];
		}

		switch($tipoContratoChar) {
			case 'G':
				$tipoContratoId = 2;
				break;
			case 'GX':
				$tipoContratoId = 1;
				break;	
			case 'X':
				$tipoContratoId = 3;
				break;
		}
		$socios->setTipoContratoId($tipoContratoId);

		//Estado contrato
		$estadoContrato = $this->_checkEstadoContrato($socios->getEstadoContrato());
		$socios->setEstadoContrato($estadoContrato);

		//Estado movimiento
		$estadoMovimiento = $this->_checkEstadoMovimiento($socios->getEstadoMovimiento());
		$socios->setEstadoMovimiento($estadoMovimiento);

		if ($socios->save()==false) {
			
			//error
			$logger = $this->_getLogger('migradoContratosError');
			$logger->log("###########$numeroContrato###########");
			$logger->log("El contrato '$numeroContrato' tiene un error: ".print_r($socios->getMessages(),true).' '.print_r($socios,true),Logger::ERROR);
			$this->_logCount['C']['Error']++;

			/*foreach ($socios->getMessages() as $message) {
				throw new Exception($message->getMessage());
			}*/

			return false;

		}else{
			//OK
			$logger = $this->_getLogger('migradoContratos');
			$logger->log("###########$numeroContrato###########");
			$logger->log("El contrato '$numeroContrato' se inserto correctamente.");
			$this->_logCount['C']['Ok']++;
		}

		return $socios;
	}

	/**
	* verifica el estado de contrato y sino existe le coloca uno conocido
	*/
	private function _checkEstadoContrato($estadoContratoCode='') {

		//verificamos si existe
		if ($estadoContratoCode) {
			$estadoContrato = EntityManager::get('EstadoContrato')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'codigo="'.$estadoContratoCode.'"'));

			if ($estadoContrato==false) {
				$estadoContratoCode = 'A';//Activo
			}

		} else {
			$estadoContratoCode = 'AA';//Anulado
		}

		return $estadoContratoCode;
	}

	/**
	* verifica el estado de movimiento y sino existe le coloca uno conocido
	*/
	private function _checkEstadoMovimiento($estadoMovimientoCode='') {

		//verificamos si existe
		if ($estadoMovimientoCode) {
			$estadoContrato = EntityManager::get('EstadoContrato')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'codigo="'.$estadoMovimientoCode.'"'));

			if ($estadoContrato==false) {
				$estadoMovimientoCode = 'R';//Activo
			}

		} else {
			$estadoMovimientoCode = 'R';//Anulado
		}

		return $estadoMovimientoCode;

	}

	/**
	* Obtien el location de la tabla ciudades de sociostpc
	*/
	private function _getLocation($ciudadesId) {
		$locationId = 0;
		$ciudades = EntityManager::get('Ciudades', true)->setTransaction($this->_transaction)->findFirst($ciudadesId);
		//throw new Exception($ciudades->getNombre().' 1');
		if ($ciudades!=false) {
			//buscamos en nuevo sistema de ciudades
			$location = EntityManager::get('Location', true)->setTransaction($this->_transaction)->findFirst(array('conditions'=>'name LIKE "%'.$ciudades->getNombre().'%"'));
			if ($location!=false) {
				//throw new Exception($ciudades->getNombre().'->'.$location->getName().' 2');
				$locationId = $location->getId();
			}	
		}
		return $locationId;
	}

	/**
	* Verifica las ciudades que estan en sociostpc.socios_tpc y los pasa a hfos_tc.socios buscansdo en hfos_geoinfo.location
	*/
	private function _checkCiudadesSocios(&$config) {
		$socios 	= $config['sociosNew'];
		$sociosTpc 	= $config['sociosTpc'];

		//Ciudades_id
		if ($sociosTpc->getCiudadesId()>0) {
			$socios->setCiudadesId($this->_getLocation($sociosTpc->getCiudadesId()));
		}

		//ciudad_residencia
		if ($sociosTpc->getCiudadResidencia()>0) {
			$socios->setCiudadResidencia($this->_getLocation($sociosTpc->getCiudadResidencia()));
		}
	}

	/**
	* Metodo que agrega la informacion de BD sociostpc.conyuges_tpc a hfos_tc.conyuges
	* 
	* @param Object $config(
	*  'sociosNew' => ActiveRecord,
	*  'sociosTpc' => ActiveRecord
	* )
	* @param ActiveRecordTransaction $transaction
	*/
	private function _addConyuges(&$config) {
		
		//variables
		$sociosNew = $config['sociosNew'];
		if ($sociosNew==false) {
			return false;
		}

		$sociosTpc = $config['sociosTpc'];

		if ($sociosTpc==false) {
			return false;
		}


		//obtenemos los conyuges en sociostpc.conyuges_tpc
		$conyugesTpcObj = EntityManager::get('ConyugesTpc')->setTransaction($this->_transaction)->find(array('conditions'=>'socios_tpc_id='.$sociosTpc->getId()));
		
		if (count($conyugesTpcObj)>0) {

			foreach ($conyugesTpcObj as $conyugesTpc) {
				//Creo nuevo conyuge	
				$conyuges = EntityManager::get('Conyuges', true)->setTransaction($this->_transaction);
				$conyuges->setValidar(false);
				
				foreach ($conyugesTpc->getAttributes() as $field) {
					if ($field!='id' && in_array($field, $conyuges->getAttributes())==true) {
						$val = $conyugesTpc->readAttribute($field);
						echo  'adding conyuge field ', $field, '....',$val,'<br>';
						if ($val && $val!='0000-00-00') {
							$conyuges->writeAttribute($field, $val);	
						}
					}
				}
				
				//adding new id of socio
				$conyuges->setSociosId($sociosNew->getId());

				//celular
				$celular = $conyugesTpc->getCelular();
				if (!$celular) {
					$celular = $sociosNew->getCelular();
				}
				$conyuges->setCelular($celular);

				if ($conyuges->save()==false) {
					foreach ($conyuges->getMessages() as $message) {
						throw new Exception($message->getMessage());
					}
				} else {
					$logger = $this->_getLogger('migradoContratos');
					$logger->log("Se inserto el conyuge correctamente. ");
				}

				unset($conyuges);
			}
				
		}

	}

	/**
	* Metodo que agrega la informacion de BD sociostpc.membresias_socios a hfos_tc.membresias_socios
	* 
	* @param Object $config(
	*  'sociosNew' => ActiveRecord,
	*  'sociosTpc' => ActiveRecord
	* )
	* @param ActiveRecordTransaction $transaction
	*/
	private function _addMembresiasSocios($config) {
		
		//variables
		$sociosNew = $config['sociosNew'];
		$sociosTpc = $config['sociosTpc'];

		$numeroContrato = $sociosNew->getNumeroContrato();

		//obtenemos los conyuges en sociostpc.conyuges_tpc
		$membresiasSociosTpcObj = EntityManager::get('MembresiasSociostpc')->setTransaction($this->_transaction)->find(array('conditions'=>'socios_tpc_id='.$sociosTpc->getId()));
		
		if (count($membresiasSociosTpcObj)>0) {

			foreach ($membresiasSociosTpcObj as $membresiasSociosTpc) {
				//Creo nuevo conyuge	
				$membresiasSocios = EntityManager::get('MembresiasSocios', true)->setTransaction($this->_transaction);
				$membresiasSocios->setValidar(false);
				
				foreach ($membresiasSociosTpc->getAttributes() as $field) {
					if ($field!='id' && in_array($field, $membresiasSocios->getAttributes())==true) {
						$val = $membresiasSociosTpc->readAttribute($field);
						if ($val && $val!='0000-00-00') {
							echo  'adding membresiasSocios field ', $field, '....',$val,'<br>';
							$membresiasSocios->writeAttribute($field, $val);
						}
					}
				}
				
				//adding new id of socio
				$membresiasSocios->setSociosId($sociosNew->getId());

				//cuota inicial
				if (!$membresiasSocios->getCuotaInicial()) {
					$membresiasSocios->setCuotaInicial(0);
				}

				//valor_total
				if (!$membresiasSocios->getValorTotal()) {
					$membresiasSocios->setValorTotal(0);
				}

				//agregando derechos de afiliacion nuevos y/o coge el id
				$config['membresiasSociosTpc'] = $membresiasSociosTpc;
				$derechoAfiliacion = $this->_checkDerechoAfiliacion($config);
				$membresiasSocios->setDerechoAfiliacionId($derechoAfiliacion->getId());

				if ($membresiasSocios->save()==false) {

					//error
					$logger = $this->_getLogger('migradoContratosError');
					$logger->log("###########$numeroContrato###########");
					$logger->log("El contrato '$numeroContrato' tiene un error en membresias: ".print_r($membresiasSocios->getMessages(),true).' '.print_r($membresiasSocios,true),Logger::ERROR);
					$this->_logCount['C']['Error']++;

					foreach ($membresiasSocios->getMessages() as $message) {
						throw new Exception($message->getMessage());
					}
				} else {
					//OK
					$logger = $this->_getLogger('migradoContratos');
					$logger->log("Se inserto la membresia correctamente.");
				}

				unset($membresiasSocios);
			}
			return true;
				
		} else {
			//error
			$logger = $this->_getLogger('migradoContratosError');
			$logger->log("###########$numeroContrato###########");
			$logger->log("El contrato '$numeroContrato' no tiene membresia. ",Logger::ERROR);
			$this->_logCount['C']['Error']++;

			print "<br>no se encontro membresia al socio</br>";
			return false;
		}
		
	}

	/**
	* Metodo que verifica el derecho de afiliacion segun su valor y tipo de contrato sino existe lo crea
	* 
	* @param Object $config(
	*  'sociosNew' => ActiveRecord,
	*  'sociosTpc' => ActiveRecord,
	*  'membresiasSociosTpc' => ActiveRecord
	* )
	* @param ActiveRecordTransaction $transaction
	* @return ActiveRecord $derechoAfilaicion
	*/
	private function _checkDerechoAfiliacion(&$config) {

		//variables
		$sociosNew = $config['sociosNew'];
		$sociosTpc = $config['sociosTpc'];
		$membresiasSociosTpc = $config['membresiasSociosTpc'];

		$derechoAfilaicionVal = $membresiasSociosTpc->getDerechoAfiliacion();
		if (!$derechoAfilaicionVal) {
			$derechoAfilaicionVal = 0;
		}
		$derechoAfiliacion = EntityManager::get('DerechoAfiliacion')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'tipo_contrato_id='.$sociosNew->getTipoContratoId().' AND valor='.$derechoAfilaicionVal));
		if ($derechoAfiliacion==false) {
			$derechoAfiliacion = EntityManager::get('DerechoAfiliacion', true)->setTransaction($this->_transaction);
			$derechoAfiliacion->setTipoContratoId($sociosNew->getTipoContratoId());
			$derechoAfiliacion->setValor($derechoAfilaicionVal);
			$derechoAfiliacion->setEstado('A');
			if ($derechoAfiliacion->save()==false) {
				foreach ($derechoAfiliacion->getMessages() as $message) {
					throw new Exception($message->getMessage());
				}
			}	
		}
		return $derechoAfiliacion;
	}

	/**
	* Metodo que agrega la informacion de BD sociostpc.detalle_cuota a hfos_tc.detalle_cuota
	* 
	* @param Object $config(
	*  'sociosNew' => ActiveRecord,
	*  'sociosTpc' => ActiveRecord
	* )
	* @param ActiveRecordTransaction $transaction
	*/
	private function _addDetalleCuota(&$config) {
		
		//variables
		$sociosNew = $config['sociosNew'];
		$sociosTpc = $config['sociosTpc'];

		$numeroContrato = $sociosNew->getNumeroContrato();

		//obtenemos los conyuges en sociostpc.conyuges_tpc
		$detalleCuotaTpcObj = EntityManager::get('DetalleCuotatpc')->setTransaction($this->_transaction)->find(array('conditions'=>'socios_tpc_id='.$sociosTpc->getId()));
		
		if (count($detalleCuotaTpcObj)>0) {

			foreach ($detalleCuotaTpcObj as $detalleCuotaTpc) {
				//Creo nuevo conyuge	
				$detalleCuota = EntityManager::get('DetalleCuota', true)->setTransaction($this->_transaction);
				$detalleCuota->setValidar(false);
				
				foreach ($detalleCuotaTpc->getAttributes() as $field) {
					if ($field!='id' && in_array($field, $detalleCuota->getAttributes())==true) {
						$val = $detalleCuotaTpc->readAttribute($field);
						if ($val && $val!='0000-00-00') {
							echo  'adding detalle_cuota field ', $field, '....',$val,'<br>';
							$detalleCuota->writeAttribute($field, $val);
						}
					}
				}
				
				//adding new id of socio
				$detalleCuota->setSociosId($sociosNew->getId());

				if ($detalleCuota->save()==false) {

					//error
					$logger = $this->_getLogger('migradoContratosError');
					$logger->log("###########$numeroContrato###########");
					$logger->log("El contrato '$numeroContrato' tiene un error en detalleCuota: ".print_r($detalleCuota->getMessages(),true).' '.print_r($detalleCuota,true),Logger::ERROR);
					$this->_logCount['C']['Error']++;


					foreach ($detalleCuota->getMessages() as $message) {
						throw new Exception($message->getMessage());
					}
				} else {
					//ok
					$logger = $this->_getLogger('migradoContratos');
					$logger->log("Se inserto detalleCuota correctamente.");

				}

				unset($detalleCuota);
			}
				
		}

	}

	/**
	* Metodo que agrega la informacion de BD sociostpc.pago_saldo a hfos_tc.pago_saldo
	* 
	* @param Object $config(
	*  'sociosNew' => ActiveRecord,
	*  'sociosTpc' => ActiveRecord
	* )
	* @param ActiveRecordTransaction $transaction
	*/
	private function _addPagoSaldo(&$config) {
		
		//variables
		$sociosNew = $config['sociosNew'];
		$sociosTpc = $config['sociosTpc'];

		$numeroContrato = $sociosNew->getNumeroContrato();

		//obtenemos los conyuges en sociostpc.conyuges_tpc
		$pagoSaldoTpcObj = EntityManager::get('PagoSaldotpc')->setTransaction($this->_transaction)->find(array('conditions'=>'socios_tpc_id='.$sociosTpc->getId()));
		
		if (count($pagoSaldoTpcObj)>0) {

			foreach ($pagoSaldoTpcObj as $pagoSaldoTpc) {
				//Creo nuevo conyuge	
				$pagoSaldo = EntityManager::get('PagoSaldo', true)->setTransaction($this->_transaction);
				$pagoSaldo->setValidar(false);
				
				foreach ($pagoSaldoTpc->getAttributes() as $field) {
					if ($field!='id' && in_array($field, $pagoSaldo->getAttributes())==true) {
						$val = $pagoSaldoTpc->readAttribute($field);
						if ($val && $val!='0000-00-00') {
							echo  'adding pago_saldo field ', $field, '....',$val,'<br>';
							$pagoSaldo->writeAttribute($field, $val);
						}
					}
				}
				
				//adding new id of socio
				$pagoSaldo->setSociosId($sociosNew->getId());

				//fecha primer pago
				if (!$pagoSaldo->getFechaPrimeraCuota()) {
					$fechaCompra = $sociosNew->getFechaCompra()->getDate();
					if ($fechaCompra) {
						$pagoSaldo->setFechaPrimeraCuota($fechaCompra);
					}else{
						$pagoSaldo->setFechaPrimeraCuota(date('Y-m-d'));
					}					
				}

				$config['fechaPrimeraCuota'] = $pagoSaldo->getFechaPrimeraCuota();

				if (!$pagoSaldo->getNumeroCuotas()) {
					$pagoSaldo->setNumeroCuotas(0);
				}

				if (!$pagoSaldo->getMora()) {
					$pagoSaldo->setMora(0);
				}

				if ($pagoSaldo->save()==false) {

					//error
					$logger = $this->_getLogger('migradoContratosError');
					$logger->log("###########$numeroContrato###########");
					$logger->log("El contrato '$numeroContrato' tiene un error en pagoSaldo: ".print_r($pagoSaldo->getMessages(),true).' '.print_r($pagoSaldo,true),Logger::ERROR);
					$this->_logCount['C']['Error']++;

					foreach ($pagoSaldo->getMessages() as $message) {
						throw new Exception($message->getMessage());
					}
				} else {

					//OK
					$logger = $this->_getLogger('migradoContratos');
					$logger->log("Se inserto pagoSaldo correctamente.");

				}

				unset($pagoSaldo);
			}
				
		}

	}

	/**
	* Verifica los nits de contabilidad en busca de actualizar la información de estos confiando en contabilidad
	*
	* @param ActiveRecordTransaction $transaction
	*/
	private function _checkDataFromContab() {
		if (file_exists($this->_contabTxt)==true) {
			$f = fopen($this->_contabTxt,'r');
			if ($f) {
				while(($buffer = fgets($f, 4096)) !== false) {
			        $lineArray = explode('|',$buffer);
			        //echo print_r($lineArray,true), '<br>';

			        $cedula 	= $lineArray[0];
			        $direccion 	= $lineArray[3];
			        $telefono 	= trim($lineArray[4]);


			        //Buscamos cedula si existe
			        $socios = EntityManager::get('Socios')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'identificacion='.$cedula));
			        if ($socios!=false) {
			        	echo $socios->getNumeroContrato(),', ',$cedula,'<br>';
			        	
			        	//Telefono Residencia
			        	$telefono = trim($telefono);
			        	$telefono = str_replace(" ", "", $telefono);
			        	$telefonoTc = trim($socios->getTelefonoResidencia());
			        	$telefonoTc = str_replace(" ", "", $telefonoTc);

			        	if ($telefono!=$telefonoTc) {
			        		if ($telefono) {
			        			$socios->setTelefonoResidencia($telefono);	
			        		}
			        		echo 'TelefonoXContab: '.$telefonoTc,'(TC) != (Contab)',$telefono,'<br>';
			        	}

			        	//direccion Residencia
			        	$direccionContab = $direccion;
			        	$direccionTc = $socios->getDireccionResidencia();
			        	
			        	if ($direccionContab!=$direccionTc) {
			        		if ($direccionContab) {
			        			$socios->setDireccionResidencia($direccionContab);
			        		}
			        		echo 'DireccionXContab: '.$direccionTc,'(TC) != (Contab)',$direccionContab,'<br>';
			        	}
			        	$socios->setValidar(false);
			        	if ($socios->save()==false) {
							foreach ($socios->getMessages() as $message) {
								throw new Exception($message->getMessage());
							}
						}		
			        }
			    }
			    if (!feof($f)) {
			        echo "Error: unexpected fgets() fail\n";
			    }
				fclose($f);
			}
		}else{
			throw new Exception('No existe el archivo '.$this->_contabTxt);
		}
	}

	/**
	* verifica si un contrato tiene pagos
	*/
	private function _checkPagosByContrato($numeroContrato) {

		$flag = false;
		//$socios = EntityManager::get('Socios')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'numero_contrato="'.$numeroContrato.'"'));
		$socios = BackCacher::getSociosTpcContrato($numeroContrato);
		if ($socios!=false) {
			$recibosPagos = EntityManager::get('RecibosPagos')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'socios_id="'.$socios->getId().'"'));
			if ($recibosPagos!=false) {
				$flag=true;
			}
		}

		return $flag;
	}

	/**
	* Agrega todos los pagos de cartera a el contrato
	*/
	private function _addPagosCartera() {

		$pagos = 0;
		$contratosConPagos = 0;
		$empresa = $this->Empresa->findFirst();

		if (file_exists($this->_carteraJson)) {
			$json = file_get_contents($this->_carteraJson);
			$data = json_decode($json);

			unset($json);

			$this->_limit = 0;

			foreach ($data as $index => $content) {

				//Si tiene numero de contrato seguimos
				if ($content->numeroContrato) {
				//if ($content->numeroContrato && !in_array($content->numeroContrato, $this->notContratosTest)) {
				#if ($content->numeroContrato=='TPC-GX-10-0458') {
				#if (in_array($content->numeroContrato, $this->contratosTest)) {

					try{
					
						if (!TransactionManager::hasUserTransaction()) {
							$this->_transaction = TransactionManager::getUserTransaction();
						}				
						
						//Verifica si el contrato ya tiene pagos
						$flag = $this->_checkPagosByContrato($content->numeroContrato);

						echo "<br>numeroContrato: ", $content->numeroContrato;

						if ($flag==true) {
							//$contratosConPagos++;
							//Si tiene pagos next plz
							//continue;	
						}

						$this->_limit++;
						/*if ($this->_limit>15) {
							break;
						}*/
					
						echo "<br><br>startFinanciacion: ", date('r');


						if (!$content->numCuotas) {
							print PHP_EOL."<br/>El contrato {$content->numeroContrato} no presenta numero de cuotas<br/>";
							$logger = $this->_getLogger('migradoContratosSinAmortizacion');
							$logger->log('El contrato {$content->numeroContrato} no tiene financiación activa');
							$this->_limit-=1;
							continue;
						}

						if ($content->numCuotas>0) {
							//verificamos que la amortizacion sea la misma
							$options = array(
								'empresa'				=> $empresa,
								'numeroContrato'		=> $content->numeroContrato,
								'amortizacion'			=> $content->amortizacion,
								'numeroCuotas'			=> $content->numCuotas,
								'valorTotalCompra'		=> $content->valorTotalCompra,
								'fechaIniAmortizacion'	=> $content->fechaIniAmortizacion,
								'valorFinanciacion'		=> $content->valorFinanciacion,
								'valorCuotasIniciales'	=> $content->valorCuotasIniciales,
								'tasaMora'				=> $content->tasaMora,
								'tasaMesVencido'		=> $content->tasaMesVencido,
								'pagos'					=> 0
							);

							//verificamos si la amortizacione s igual al del excel
							$amortizacionOK = $this->_checkAmortizacionXCartera($options);

							//Si la amortizacion es la misma podemos hacer pagos
							if ($amortizacionOK==true) {
								
								//agrega cuotas iniciales segun circuntancias
								$this->_addCuotasByCuenta($content, $options);
														
								$pagos+= (int) $options['pagos'];

								$contratosConPagos++;


							} else {
								print PHP_EOL."<br/>El contrato {$content->numeroContrato} tiene una tabla de amorización diferente<br/>";
								$logger = $this->_getLogger('migradoContratosSinAmortizacion');
								$logger->log('El contrato {$content->numeroContrato} tiene una tabal de amorización diferente');
								$this->_limit-=1;
								continue;
							}

							
							unset($options);
						}

						echo "<br>finishFinanciacion: ", date('r');

						echo "<br>memory Usage: ", (memory_get_usage()/1024);

						echo '<br><b>limit</b>: ',$this->_limit;

						unset($content);

						$this->_transaction->commit();

					}catch(Exception $e) {
						echo "<br><h2>Error Insertando datos:</h2>";
						print_r($e);
						echo "<br>";
					}

				}
			}

			unset($list, $data);

			print "<br><br><h1>Pagos Realizados: '$pagos', Contratos con pagos: '$contratosConPagos' </h1>";

		}else{
			throw new Exception("Error: El archivo ".$this->_carteraJson." no existe");
		}	
	}

	/**
	* Paga los derechos de afilaición segun circuntancias
	*
	* @param array $content
	*/
	private function _addDerechosDeAfiliacion($content, $options) {
			
	}

	/**
	* Paga los las cuotas iniciales
	*
	* @param array $content Excel data
	* @param array $options Array Data
	*/
	private function _addCuotasByCuenta($content, &$options) {
		//$socios	= EntityManager::get('Socios')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"numero_contrato='{$options['numeroContrato']}'"));
		$socios = BackCacher::getSociosTpcContrato($content->numeroContrato);
		if ($socios!=false) {
			
			//sacamos numeroDoc de contrato de tpc
			$numeroContrato = $content->numeroContrato;
			$numeroDocArray = explode('-', $numeroContrato);
			$options['numeroDoc'] = $numeroDocArray[count($numeroDocArray)-1];

			//Ingresamos los pagos segun el cuadro de excel
			$this->_addPagosFromExcel($content, $options);

			unset($numeroDocArray, $numeroContrato);
		}

		unset($socios);
	}

	/**
	* Obtiene la información de cartera por nit y numero de documento
	*
	* @param int $nit
	* @param int $numeroDoc
	*/
	private function _getCartera($nit,$numeroDoc) {
		$ret = array(
			'derechoAfiliacion'	=> array('valor' => 0),
			'cuotaInicial' 		=> array('valor' => 0, 'saldo' => 0),
			'financiacion' 		=> array('valor' => 0, 'saldo' => 0),
		);
		if ($nit && $numeroDoc) {
			//$comprobRc = Settings::get('comprob_rc','CO');

			//Cartera
			$carteraObj = EntityManager::get('Cartera')->setTransaction($this->_transaction)->find(array('conditions'=>"nit='$nit' AND numero_doc='$numeroDoc'"));

			if (count($carteraObj)>0) {
				foreach ($carteraObj as $cartera) {

					switch ($cartera->getCuenta()) {

						//valor/saldo cuota inicial
						case $this->_cuentaCuotasIniciales:
							$ret['cuotaInicial']['cuenta'] = $cartera->getCuenta();
							$ret['cuotaInicial']['valor'] = $cartera->getValor();
							$ret['cuotaInicial']['valorCartera'] = $cartera->getValor();
							$ret['cuotaInicial']['saldo'] = $cartera->getSaldo();
							break;
						//valor/saldo financiacion	
						case $this->_cuentaFinanciacion:
							$ret['financiacion']['cuenta'] = $cartera->getCuenta();
							$ret['financiacion']['valor'] = $cartera->getValor();
							$ret['financiacion']['valorCartera'] = $cartera->getValor();
							$ret['financiacion']['saldo'] = $cartera->getSaldo();
							break;
					}	

				}
			}

			//Movi
			$moviObj = EntityManager::get('Movi')->setTransaction($this->_transaction)->find(array('conditions'=>"nit='$nit' AND numero='$numeroDoc'"));

			if (count($moviObj)>0) {
				foreach ($moviObj as $movi) {

					switch ($movi->getCuenta()) {

						//valor/saldo cuota inicial
						case $this->_cuentaCuotasIniciales:
							if ($ret['cuotaInicial']['valor'] != $movi->getValor()) {
								$ret['cuotaInicial']['valor'] = $movi->getValor();
							}
							break;
						//valor/saldo financiacion	
						case $this->_cuentaFinanciacion:
							if ($ret['financiacion']['valor'] != $movi->getValor()) {
								$ret['financiacion']['valor'] = $movi->getValor();
							}
							break;
					}	

				}
			}

			//Derecho de afilaicion
			$movi = EntityManager::get('Movi')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"nit='$nit' AND cuenta='{$this->_cuentaDerechoAfiliacion}'"));

			if ($movi!=false) {
				$ret['derechoAfiliacion']['valor'] = $movi->getValor();
			}
			

			//throw new Exception('ret: '.print_r($ret,true));
		}

		return $ret;
	}

	/**
	* Suma los movimientos en busca de un total buscado
	*/
	private function _findSumMovi($nit, $numeroDoc, $cuota, $cuenta, $fecha) {
		
		$ret = 0;

		//buscamos pagos en ese año y mes acordado
		$moviObj = EntityManager::get('Movi')->setTransaction($this->_transaction)->find(array('conditions'=>"cuenta='$cuenta' AND nit='$nit' AND numero_doc='$numeroDoc' AND YEAR(fecha)='{$fecha->getYear()}' AND MONTH(fecha)='{$fecha->getMonth()}'"));

		foreach ($moviObj as $movi) {

			//Buscamos movi solo los creditos
			$valorMovi = EntityManager::get('Movi')->setTransaction($this->_transaction)->sum(array('conditions'=>"comprob='{$movi->getComprob()}' AND numero='{$movi->getNumero()}' AND deb_cre='C'",'column'=>'valor'));

			//Si la sumatoria de l movimiento es igual es que se difirio en otras cuentas
			if (LocaleMath::round($cuota->valor,0)==LocaleMath::round($valorMovi,0)) {
				print "<br>".$movi->getNumero().', valor: '.$movi->getValor().', fecha:'.$movi->getFecha().', WHERE: '."cuenta='$cuenta' AND nit='$nit' AND numero_doc='$numeroDoc' AND YEAR(fecha)='{$fecha->getYear()}' AND MONTH(fecha)='{$fecha->getMonth()}'".
			'<br>';
			
				//print "<br>if (LocaleMath::round({$cuota->valor},0)==LocaleMath::round($valorMovi,0)) {return ".$movi->getNumero().'<br>';
			
				$ret = $movi->getNumero();	
				return $ret;
			}

		}

		return $ret;
	}

	/**
	* Trata de obtener el numero de consecutivo de numero de un movimiento
	*/
	private function _getNumeroMovi($nit, $numeroDoc, $cuota, $cuenta) {
		if (!$cuenta || !$nit) {
			return false;
		}
		$ret = 0;
		$fecha = '';
			
		$moviObj = EntityManager::get('Movi')->setTransaction($this->_transaction)->find(array('conditions'=>"cuenta='$cuenta' AND nit='$nit' AND valor='{$cuota->valor}'",'group'=>'numero'));
		//AND numero_doc='$numeroDoc'

		//Si encontro el valor exacto
		if (count($moviObj)>0) {
			//buscamos el que no este registrado en el sistema
			foreach ($moviObj as $movi) {
				$recibosPagos = EntityManager::get('RecibosPagos')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"rc='{$movi->getNumero()}'"));
				if ($recibosPagos==false) {
					$ret = $movi->getNumero();
					return $ret;
				}
			}
		} else {
			//Si no lo encontro tal vez es porque esta en cuentas diferentes como mora o corrientes
			//Si es financiacion
			if (isset($cuota->fechaPago)==true) {
				$fecha = new Date($cuota->fechaPago);
			}else{
				//Cuota Inicial
				$fecha = new Date($cuota->fecha);
			}

			$fecha2 = $fecha; 

			$ret2 = 0;
			//buscamos comprobantes en busca su suma total de creditos para saber el numero del pago hasta encontrar
			for($i=0;$i<7;$i++) {

				if (!is_object($fecha)) {
					$fecha = new Date($fecha);
					$year = $fecha->getYear();
					$month = $fecha->getMonth();
				}

				$ret2 = $this->_findSumMovi($nit, $numeroDoc, $cuota, $cuenta, $fecha);
				
				//Si no encontramos en ese mes supongamos que 
				if (!$ret2) {
					$fecha = $fecha->addMonths(1);
				}else{
					$ret = $ret2;
					break;
				}

			}

			if ($ret>0) {
				$recibosPagos = EntityManager::get('RecibosPagos')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"rc='".$ret."'"));
				if ($recibosPagos==false) {
					print "<br><b>found rc: $ret</b><br>";
					return $ret;
				}
			}
			return false;
						
		}
		//throw new Exception('b');

		if ($ret<=0) {
			$logger = $this->_getLogger('migradoPagosSinRC');
			$logger->log('El contrato {$content->numeroContrato} no tiene rc de pago de cuota inicial por valor de '.$cuota->valor.' en al fecha '.$fecha2->getDate().'. SQL.where:'."nit='{$content->identificacion}' AND numero_doc='{$options['numeroDoc']}' AND cuenta='{$this->_cuentaFinanciacion}' and deb_cre='C'<br>");
		}

		return $ret;
	}

	/**
	* Genera la amortizacion de neuvo a partir de datos de cartera
	*
	* @param array $options(
	*	'numeroContrato'		=> $content->numeroContrato,
	*	'amortizacion'			=> $content->amortizacion,
	*	'numeroCuotas'			=> $content->numCuotas,
	*	'valorTotalCompra'		=> $content->valorTotalCompra,
	*	'fechaIniAmortizacion'	=> $content->fechaIniAmortizacion,
	*	'valorFinanciacion'		=> $content->valorFinanciacion,
	*	'valorCuotasIniciales'	=> $content->valorCuotasIniciales,
	*	'tasaMora'				=> $content->tasaMora,
	*	'tasaMesVencido'		=> $content->tasaMesVencido,
	*  )
	*/
	private function _checkAmortizacionXCartera($options) {

		if (!isset($options['numeroContrato'])) {
			echo "No hay un numero de contrato a buscar<br>";
			return false;
		}

		echo "search: ",'numero_contrato="'.$options['numeroContrato'].'"';
		//$sociosTc = EntityManager::get('Socios')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'numero_contrato="'.$options['numeroContrato'].'"'));
		$sociosTc = BackCacher::getSociosTpcContrato($options['numeroContrato']);

		if ($sociosTc!=false) {
			

			$options['socios'] = $sociosTc;

			//Borramos la amortizacion que se esta usando
			$amortizacionTc = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->delete(array('conditions'=>'socios_id="'.$sociosTc->getId().'"'));

			if ($amortizacionTc==false) {
				throw new Exception("No se pudo borrar la amortizacion en TC del contrato ".$sociosTc->getNumeroContrato()."<br>");
			}

			//asignamos el numero de cuotas
			$pagoSaldo = EntityManager::get('PagoSaldo')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'socios_id="'.$sociosTc->getId().'"'));

			if ($pagoSaldo==false) {
				throw new Exception("No existe datos en pago_saldo en TC del contrato ".$sociosTc->getNumeroContrato()."<br>");
			}
			$options['pagoSaldo'] = $pagoSaldo;

			$pagoSaldo->setNumeroCuotas($options['numeroCuotas']);//numero de cuotas de amortizacion
			$interesCorriente = $options['tasaMesVencido'] * 100;
			//throw new Exception($interesCorriente);
			$pagoSaldo->setInteres($interesCorriente);//interes corriente
			$pagoSaldo->setFechaPrimeraCuota($options['fechaIniAmortizacion']);//fecha primera cuota de amortizacion

			if ($pagoSaldo->save()==false) {
				foreach ($pagoSaldo->getMessages() as $message) {
					throw new Exception('pagoSaldo :'.$message->getMessage());
				}
			}

			LocaleMath::enableBcMath();

			//asignamos los datos de amortizacion para generarla
			$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'socios_id="'.$sociosTc->getId().'"'));

			if ($membresiasSocios==false) {
				throw new Exception("No existe datos en membresias_socios en TC del contrato ".$sociosTc->getNumeroContrato()."<br>");
			}

			$membresiasSocios->setValorTotal($options['valorTotalCompra']);
			$membresiasSocios->setCuotaInicial($options['valorCuotasIniciales']);
			$membresiasSocios->setSaldoPagar($options['valorFinanciacion']);

			if ($membresiasSocios->save()==false) {
				foreach ($membresiasSocios->getMessages() as $message) {
					throw new Exception('membresiasSocios :'.$message->getMessage());
				}
			}
			$options['membresiasSocios'] = $membresiasSocios;

			//Remplazamos la amortizacion actual
			//$status = TpcContratos::_makeAmortizacion($this->_transaction, $sociosTc);

			$tpcContratos = new TpcContratos();
			$status = $tpcContratos->makeAmortizacion($this->_transaction, $sociosTc);

			//print_r($options);

			//asignamos los datos de amortizacion para generarla
			$amortizacionTc = EntityManager::get('Amortizacion')->setTransaction($this->_transaction)->find(array('conditions'=>'socios_id="'.$sociosTc->getId().'"'));

			var_dump($status);
			echo "<br>amortizacionTc: ", count($amortizacionTc);

			
			//Recorremos datos y comparamos con TC
			foreach ($options['amortizacion'] as $cuota) {
				foreach ($amortizacionTc as $cuotaTc) {
					
					//miramos que ambas sean la misma cuota
					if ($cuota->cuota==$cuotaTc->getNumeroCuota()) {
					
						//verificamos cuota fija
						$cuotaValC 	= LocaleMath::round($cuota->cuotaMes,0);
						$cuotaValTc	= LocaleMath::round($cuotaTc->getValor(),0);

						if ($cuotaValC != $cuotaValTc) {
							echo "<br/><b>Cartera</b><br>"."Contrato: ".$options['numeroContrato'].'<br>';
							echo "La cuota ".$cuota->cuota." tiene un valor cuota fija mensual diferente ($cuotaValC(Cartera) != (TC)$cuotaValTc)<br>({$cuota->cuotaMes}!={$cuotaTc->getValor()})";
							return false;
						}

						//verificamos capital
						$capitalValC 	= LocaleMath::round($cuota->abonoCapital,0);
						$capitalValTc	= LocaleMath::round($cuotaTc->getCapital(),0);
						if ($capitalValC != $capitalValTc) {
							echo "<br/><b>Cartera</b><br>"."Contrato: ".$options['numeroContrato'].'<br>';
							echo "La cuota ".$cuota->cuota." tiene un capital diferente ($capitalValC(Cartera) != (TC)$capitalValTc)<br>({$cuota->abonoCapital}!={$cuotaTc->getCapital()})";
							return false;
						}

						unset($cuotaValC, $cuotaValTc, $capitalValC, $capitalValTc);

					}
				}

				//remplazamos amortizacion por la de excel

			}
			$options['amortizacionObj'] = $amortizacionTc;
			//throw new Exception(print_r($options,true));

			unset($amortizacion, $sociosTc, $amortizacionTc, $pagoSaldo, $membresiasSocios);
		} else {
			echo "El contrato ",$options['numeroContrato'],' no existe en el aplicativo';
			//$this->_limit -= 1;
			return false;
		}

		return true;
	}

	
	/**
	* ajusta por medio de una nota debito o credito el saldo de un contrato seguns aldo de archivo de excel
	*
	* @param array $content Excel data
	* @param array $options Array Data
	*/
	private function _addAjusteSaldo($content, &$options) {

		echo "<br>","<h3>_addAjusteSaldo</h3>:","<br>";

		//verificamos si hubo pagos y agregamos nota debito o credito
		if (isset($options['lastSaldo']) && $options['lastSaldo']!=null) {

			$lastSaldo = $options['lastSaldo'];

			echo "<br>","Si hay un index lastSaldo: ",$options['lastSaldo'];
			
			if (!isset($options['socios'])) {
				$transaction->rollback('No existe el index socios de options');
			}
			if ($options['socios']==false) {
				$transaction->rollback('No existe el ActiveRecord de socios en el index socios de options');
			}

			$socios = $options['socios'];
			echo "<br>sociosId: ", $sociosId = $socios->getId();

			$ControlPagos = EntityManager::get('ControlPagos')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'socios_id="'.$sociosId.'"', 'order' => 'fecha_pago DESC'));

			if ($ControlPagos!=false) {
				$saldoDiff = $lastSaldo-$ControlPagos->getSaldo();					
				echo "<br>","if ($lastSaldo!={$ControlPagos->getSaldo()}) {saldoDiff: ",$saldoDiff;
				if ($lastSaldo!=$ControlPagos->getSaldo()) {

					$saldoDiff = 0;

					if ($lastSaldo<0) {
						$lastSaldo = 0;
					}

					if ($lastSaldo>$ControlPagos->getSaldo()) {
						//NOTA DEBITO (+)
						$debCre = 'D';
						$saldoDiff = $lastSaldo - $ControlPagos->getSaldo();

					} else {
						//NOTA CREDITO (-)
						$debCre = 'C';
						$saldoDiff = $ControlPagos->getSaldo() - $lastSaldo;
					}

					echo $today = date('Y-m-d');
					$fechaPago = $ControlPagos->getFechaPago()->getDate();

					echo "<br>saldoDiff: ",$saldoDiff,', debcre: ',$debCre,', fechaPago: ',$fechaPago;

					if ($saldoDiff != 0) {

						$formatoPago = array(
							'sociosId'		=> $sociosId, 
							'fechaRecibo'	=> $fechaPago, 
							'fechaPago'		=> $fechaPago,
							'debCre'		=> $debCre,//Debito/Credito
							'onlyCapital'	=> true,
							'notaContable'	=> true,
							'sinIntereses'	=> true,
							'saldoIdeal'	=> $lastSaldo,
							'setValidar'	=> false, //Force
							'formasPago' => array( 
								'count' => 1,
								'total' => $saldoDiff,
								'totalFPago' => array( 
									'E' => $saldoDiff
								), 
								'data' => array( 
									'E' => array( 
										array( 
											'formaPago' => 1,
											'numeroForma' => '',
											'valor' => $saldoDiff,
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
						);

						if (!TransactionManager::hasUserTransaction()) {
							$this->_transaction = TransactionManager::getUserTransaction();
						}

						//run nota contable
						TPC::addNotaContableContrato($formatoPago, $this->_transaction);
					}
				}
			}
		} else {
			echo "<br>","No hay un index lastSaldo";
		}

	}

	/**
	* Mira si el json del excel contiene valores de financiación para realizar su pago
	*
	* @param array $content Excel data
	* @param array $options Array Data
	*/
	private function _addFinanciacionFromExcel($content, &$options) {

		if (isset($content->financiacion)==true && count($content->financiacion)>0) {

			echo "<br><b>Añadiendo financiación</b><br>";

			//Obtenemos el socio
			$socios = $options['socios'];
			$sociosId = $socios->getId();

			//saldo en cartera
			$cartera = $options['cartera'];

			//Miramos si hay diferencias entre excel y cartera
			$contab = $options['cartera']['financiacion']['valor'];
			if (LocaleMath::round($content->valorFinanciacion,0)!=LocaleMath::round($contab,0)) {
				echo '<br/>El valor de financiación en contabilidad con el de excel '.$content->numeroContrato.' son diferentes. '.
					'Contabilidad: '.$contab.', Excel: '.$content->valorFinanciacion.'<br/>';
			}

			$valorFinanciacion = 0;
			
			//ordenamos pagoSaldo
			//$content->financiacion = array_multisort($content->financiacion);

			$financiacion = array();
			foreach ($content->financiacion as $numCuota => $cuota) {
				$numCuota2 = (int) $numCuota;
				$financiacion[$numCuota2] = $cuota;
			}

			array_multisort($financiacion);

			unset($content->financiacion);

			//guarda ultimo saldo de pagos
			$lastSaldo = null;

			foreach ($financiacion as $numCuota => $cuota) {

				//Si hay valor es que abono
				//print "<br>if (".is_object($cuota)."==true && {$cuota->valorCuota}>0) {";
				if (is_object($cuota)==true && $cuota->valorCuota>0) {

					$cuota->valor = $cuota->valorCuota;

					if (!$cuota->rc) {
						//se trata de obtener el consecutivo de recibo de caja
						//$numRc = $this->_getNumeroMovi($content->identificacion, $options['numeroDoc'], $cuota, $this->_cuentaFinanciacion);
						/*$numRc=0;

						if (!$numRc) {
							$logger = $this->_getLogger('migradoPagosSinRC');
							$logger->log("El contrato {$content->numeroContrato} no tiene rc de pago de financiación por valor de {$cuota->valor} en al fecha {$cuota->fechaPago}. SQL.where:"."nit='{$content->identificacion}' AND numero_doc='{$options['numeroDoc']}' AND cuenta='{$this->_cuentaFinanciacion}' and deb_cre='C'");
							
							print '<br>El contrato '.$content->numeroContrato.' con el pago de financiación con valor de '.$cuota->valorCuota.' en la fecha '.$cuota->fechaPago.' no existe en movimiento contable<br>';
							$numRc=null;
							//throw new Exception('EL contrato '.$content->numeroContrato.' con el pago de financiación con valor de '.$cuota->valorCuota.' en la fecha '.$cuota->fechaPago.' no existe en movimiento contable');
						}*/

						$numRc=null;
					} else {
						//Cogemos el consecutivo que da el excel
						$filter = new Filter();
						$numRc = $filter->apply($cuota->rc, array('int'));
					}

					$ciudadId = $options['empresa']->getCiudadesId();

					//Creamos estructura de formas de pago
					$dataFormas = array(
						'formaPago' 	=> array(4),//Efectivo
						'numeroForma'	=> array(0),
						'valor'			=> array($cuota->valorCuota)	
					);
					$formasPago = TPC::unificaFormasPagos($dataFormas, $this->_transaction);

					$fechaPago = (string) $cuota->fechaPago;
					//Creamos estructura de pago
					$formato = array(
						'sociosId'			=> $sociosId,
						'fechaRecibo'		=> $fechaPago,
						'fechaPago'			=> $fechaPago,
						'formasPago'		=> $formasPago,
						'cuentasId'			=> 1,
						'reciboProvisional'	=> $numRc,
						'ciudadPago'		=> $ciudadId,
						'setValidar'		=> false, //Force
						'debug'				=> true
					);
					if ($numRc) {
						$formato['rcReciboPago'] = $numRc;
						$formato['rc_controlpago'] = $numRc;
					}

					if (!TransactionManager::hasUserTransaction()) {
						$this->_transaction = TransactionManager::getUserTransaction();
					}

					//Inserta pago de tpc
					TPC::addAbonoContrato($formato, $this->_transaction);

					$valorFinanciacion += $cuota->valorCuota;

					print "<br>Se pago {$cuota->valorCuota} en la fecha $fechaPago<br>";

					$options['pagos']++;

					//save saldo
					$lastSaldo = $cuota->saldo;

					//print_r($formato);
					unset($formato, $dataFormas, $formasPago);
								
				}
			}

			unset($financiacion,$cartera);

			//throw new Exception(print_r($options['pagos'],true));
			//$this->_transaction->commit();

			$options['lastSaldo'] = $lastSaldo;

		}
	}

	/**
	* Mira si el json del excel contiene valores de la cuota inicial para realizar su pago
	*
	* @param array $content Excel data
	* @param array $options Array Data
	*/
	private function _addCuotaInicialFromExcel($content, &$options) {

		if (isset($content->cuotasIniciales)==true && is_object($content->cuotasIniciales)) {

			echo "<br><b>Añadiendo Cuotas Iniciales</b><br>";

			//Obtenemos el socio
			$socios = $options['socios'];
			$sociosId = $socios->getId();

			//Miramos si hay diferencias entre excel y cartera
			/*$contab = $options['cartera']['cuotaInicial']['valor'];
			if (LocaleMath::round($content->valorCuotasIniciales,0)!=LocaleMath::round($contab,0)) {
				echo '<br/>El valor de cuota inicial en contabilidad con el de excel '.$content->numeroContrato.' son diferentes. '.
					'Contabilidad: '.$contab.', Excel: '.$content->valorCuotasIniciales.'<br/>';
			}*/

			//espacio donde se define pago segun numero de comprobante que asu vez el el consecutivo de rc
			//$options['pagos'] = array();

			$ciudadId = $options['empresa']->getCiudadesId();
						
			//Contamos el vlaor de las cuotas en excel
			$valorCuotaIniExcel = 0;

			foreach ($content->cuotasIniciales as $label => $cuota) {

				//Si hay valor es que abono
				if ($cuota->valor>0) {

					//se trata de obtener el consecutivo de recibo de caja
					//$numRc = $this->_getNumeroMovi($content->identificacion, $options['numeroDoc'], $cuota, $this->_cuentaCuotasIniciales);
					$numRc=0;

					if ($numRc<=0) {
						$logger = $this->_getLogger('migradoPagosSinRC');
						$logger->log("El contrato {$content->numeroContrato} no tiene rc de pago de cuota inicial por valor de {$cuota->valor} en al fecha {$cuota->fecha}. SQL.where:"."nit='{$content->identificacion}' AND numero_doc='{$options['numeroDoc']}' AND cuenta='{$this->_cuentaCuotasIniciales}' and deb_cre='C'");
						//throw new Exception('El contrato '.$content->numeroContrato.' con el pago de cuota inicial con valor de '.$cuota->valor.' no existe en movimiento contable');
						print '<br>El contrato '.$content->numeroContrato.' con el pago de cuota inicial con valor de '.$cuota->valor.' en la fecha '.$cuota->fecha.' no existe en movimiento contable<br>';
						$numRc = null;
					}

					//Creamos estructura de formas de pago
					$dataFormas = array(
						'formaPago' 	=> array(4),//Efectivo
						'numeroForma'	=> array(0),
						'valor'			=> array($cuota->valor)	
					);
					$formasPago = TPC::unificaFormasPagos($dataFormas, $this->_transaction);

					//Creamos estructura de pago
					$formato = array(
						'sociosId'			=> $sociosId,
						'fechaRecibo'		=> $cuota->fecha,
						'fechaPago'			=> $cuota->fecha,
						'formasPago'		=> $formasPago,
						'cuentasId'			=> 1,
						'reciboProvisional'	=> $numRc,
						'ciudadPago'		=> $ciudadId,
						'setValidar'		=> false, //Force
						'debug'				=> true
					);
					if ($numRc) {
						$formato['rcReciboPago'] = $numRc;
						$formato['rc_controlpago'] = $numRc;
					}
					//$options['pagos'][$sociosId][] = $formato;
					
					//print_r($formato);
					//print "<br><br>";

					if (!TransactionManager::hasUserTransaction()) {
						$this->_transaction = TransactionManager::getUserTransaction();
					}
					
					$retRc = TPC::addAbonoContrato($formato, $this->_transaction);

					$options['pagos']++;

					$valorCuotaIniExcel += $cuota->valor;
					
					unset($formato, $dataFormas, $formasPago);			
				}

			}

			unset($content->cuotasIniciales);

		}
	}

	/**
	* Mira si el movimiento de contabilidad y obtiene el valor que ingreso de derechos de afilaicion
	*
	* @param array $content Excel data
	* @param array $options Array Data
	*/
	private function _addDerechoAfiliacionFromExcel($content, &$options) {

		echo "<br><b>Añadiendo Derecho de afiliación segun info de contabilidad</b><br>";

		//Obtenemos el socio
		$socios = $options['socios'];
		$sociosId = $socios->getId();

		//Miramos si hay diferencias entre excel y cartera
		//$contab = $options['cartera']['derechoAfiliacion']['valor'];

		$ciudadId = $options['empresa']->getCiudadesId();
		$options['ciudadId'] = $ciudadId;

		//throw new Exception("socios_id='$sociosId'");
		$membresiasSocios = EntityManager::get('MembresiasSocios')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"socios_id='$sociosId'"));
		if ($membresiasSocios==false) {
			throw new Exception('La membresia del contrato '.$content->numeroContrato.' no existe');
		}
		$derechoAfiliacionValor = $membresiasSocios->getDerechoAfiliacion()->getValor();

		if ($derechoAfiliacionValor>0) {
			/*if (LocaleMath::round($derechoAfiliacionValor,0)!=LocaleMath::round($contab,0)) {
				echo '<br/>El valor de cuota inicial en contabilidad con el de excel '.$content->numeroContrato.' son diferentes. '.
					'Contabilidad: '.$contab.', ApplicativoTPC: '.$derechoAfiliacionValor.'<br/>';
			}*/

			//se trata de obtener el consecutivo de recibo de caja
			/*$movi = EntityManager::get('Movi')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"nit='{$content->identificacion}' AND numero_doc='{$options['numeroDoc']}' AND cuenta='{$this->_cuentaDerechoAfiliacion}' and deb_cre='C'"));
			if ($movi==false) {
				$numRc = null;
				//throw new Exception('No se encontro rc de derecho de afiliación, where:'."nit='{$content->identificacion}' AND numero_doc='{$options['numeroDoc']}' AND cuenta='{$this->_cuentaDerechoAfiliacion}' and deb_cre='C'");
				print '<br>No se encontro rc de derecho de afiliación por un valor de '.$derechoAfiliacionValor.', where:'."nit='{$content->identificacion}' AND numero_doc='{$options['numeroDoc']}' AND cuenta='{$this->_cuentaDerechoAfiliacion}' and deb_cre='C'<br>";
				$logger = $this->_getLogger('migradoPagosSinRC');
				$logger->log("El contrato {$content->numeroContrato} no tiene rc de pago del derecho de afiliación. Se agrego un consecutivo temporal hasta obtener solución. SQL.where:"."nit='{$content->identificacion}' AND numero_doc='{$options['numeroDoc']}' AND cuenta='{$this->_cuentaDerechoAfiliacion}' and deb_cre='C'");
			}*/
			$numRc = null;
				
			
			//Creamos estructura de formas de pago
			$dataFormas = array(
				'formaPago' 	=> array(4),//Efectivo
				'numeroForma'	=> array(0),
				'valor'			=> array($derechoAfiliacionValor)	
			);
			$formasPago = TPC::unificaFormasPagos($dataFormas, $this->_transaction);

			$fechaCompra = $socios->getFechaCompra()->getDate();

			//Creamos estructura de pago
			$formato = array(
				'sociosId'			=> $sociosId,
				'fechaRecibo'		=> $fechaCompra,
				'fechaPago'			=> $fechaCompra,
				'formasPago'		=> $formasPago,
				'cuentasId'			=> 1,
				'reciboProvisional'	=> $numRc,
				'ciudadPago'		=> $ciudadId,
				'rcReciboPago'		=> $numRc,
				'rc_controlpago'	=> $numRc,
				'setValidar'		=> false, //Force
				'debug'				=> true
			);	
			//$options['pagos'][$sociosId][] = $formato;

			if (!TransactionManager::hasUserTransaction()) {
				$this->_transaction = TransactionManager::getUserTransaction();
			}
			
			//Inserta pago de tpc
			TPC::addAbonoContrato($formato, $this->_transaction);

			$options['pagos']++;

			unset($movi,$dataFormas,$formasPago,$formato);
		}

		unset($membresiasSocios);

		//throw new Exception(print_r($formato,true));
		return true;
	}

	/**
	* administra el Pago las cuotas iniciales
	*
	* @param array $content Excel data
	* @param array $options Array Data
	*/
	private function _addPagosFromExcel($content, &$options) {

		print "<br><h2><b>Pagos a contrato {$content->numeroContrato}</b></h2></br>";

		//Obtenemos el socio
		//$socios = EntityManager::get('Socios')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"numero_contrato='{$content->numeroContrato}'"));
		$socios = BackCacher::getSociosTpcContrato($content->numeroContrato);
		$options['socios'] = $socios;
		$sociosId = $socios->getId();
		$socios->setValidar(false);
		//inicializamos el estado a reserva para que a medida que se paga se actualice
		$socios->setEstadoContrato('A');
		$socios->setEstadoMovimiento('R');
		if ($socios->save()==false) {
			foreach ($socios->getMessages() as $message) {
				throw new Exception($message->getMessage());
			}
		}

		//verificamos si existe igual saldo en cartera
		$options['cartera'] = $this->_getCartera($content->identificacion, $options['numeroDoc']);

		//Comprobante de Recibo de caja
		if (!isset($options['comprobRc'])) {
			
			//$comprobRc = Settings::get('comprob_rc','TC');

			$configuration = EntityManager::get('Configuration')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"application='TC' AND name='comprob_rc'"));
			
			if ($configuration==false) {
				throw new Exception('No se ha asignado un comprobante al recibo de caja en configuración');
			}

			$comprobRc = $configuration->getValue();
			
			//throw new Exception('comprobRc: '.$comprobRc);
			//Se coge el numero de comprobante seleccionado como consecutivo de recibo de caja
			$comprob = EntityManager::get('Comprob')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"codigo='$comprobRc'"));
			if ($comprob==false) {
				throw new Exception('El comprobante seleccionado para recibos de caja no existe');
			}
			$options['comprobRc'] = $comprobRc;

			unset($configuration, $comprob);
		}else{
			$comprobRc = $options['comprobRc'];
		}
		
		//Limpiamos pagos de nuevo contrato
		TPC::limpiarAllPagos($sociosId, $this->_transaction, true);

		//pagamos cuotas iniciales
		$status = $this->_addDerechoAfiliacionFromExcel($content, $options);

		//$status = false;
		if ($status==true) {

			//pagamos cuotas iniciales
			$this->_addCuotaInicialFromExcel($content, $options);

			//pagamos financiacion
			$this->_addFinanciacionFromExcel($content, $options);

			//ajuste de saldos
			$this->_addAjusteSaldo($content, $options);

		}

		unset($socios, $status);
		//print_r($content);exit;
	}

	/**
	* Metodo que crea/obtiene Logs facil
	*/
	private function _getLogger($name='', $options=array(), $force=false) {
		if (isset($this->_loggers[$name])==false || $force==true) {
			$this->_loggers[$name] = new Logger('File', $name.'.log', $options);
		}
		return $this->_loggers[$name];
	}

}