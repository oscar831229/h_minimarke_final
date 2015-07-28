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

require_once 'SociosException.php';
require_once 'SociosCore.php';
Core::importFromLibrary('Hfos/Socios','SociosFactura.php');
/**
 * SociosMigration
 *
 * Clase central que controla procesos internos de migración de Socios
 *
 */
class SociosMigration extends UserComponent 
{
	
	/**
	* @var TransactionManager
	*/
	private $_transaction;

	/**
	* @var array
	*/
	private $_titulares;

	/**
	* Metodo que asigna transaccion
	*/
	public function setTransaction($transaction)
	{
		$this->_transaction = $transaction;
	}

	public function main()
	{
		set_time_limit(0);

		$this->_transaction = TransactionManager::getUserTransaction();

		//Limpiamos la BD de los socios
		SociosCore::limpiarBD($this->_transaction);

		//Copia de la bd payande a hfos_socios
		$this->_copySocios();

		//Verificamos titulares
		$this->_checkTitulares();

		//Agrega a todos los socios una amortización
		$config = array(
			'valorFinanciacion' => 11700000,
			'fechaPrestamo'		=> '2007-06-30',
			'fechaInicio'		=> '2007-06-30',
			'numeroCuotas'		=> '78'
		);
		$this->_addPrestamo($config);

		$this->_transaction->commit();
	}

	
	/**
	* Copia la información de los socios de payande anterior
	*/
	private function _copySocios()
	{

		//Buscamos socios y creamos BD de ellos con totda su información antes de crearlo
		$configAll = array();
		$sociosPayaObj = EntityManager::get('SociosPaya')->setTransaction($this->_transaction)->find();//array('conditions'=>'numero_accion in("0460-00","0001-02")'));
		foreach ($sociosPayaObj as $socioPaya) 
		{

			if (!$socioPaya->getIdentificacion()) {
				echo "<br>El socios no tiene identitficación, numeroAccion: ".$socioPaya->getNumeroAccion();
				continue;
			}

			$numeroAccion = $socioPaya->getNumeroAccion();

			//Sacamos ciudades
			$ciudadExpedido = $this->_getLocationByCiudadesId($socioPaya->getCiudadExpedido());

			if ($socioPaya->getCiudadExpedido()==$socioPaya->getCiudadNacimiento()) {
				$ciudadNacimiento = $ciudadExpedido;
			}else{
				$ciudadNacimiento = $this->_getLocationByCiudadesId($socioPaya->getCiudadNacimiento());
			}

			$ciudadCasa = $ciudadExpedido;

			//Activo
			$estadosSociosId = 1;

			//Inactivo
			if ($socioPaya->getEstado()=='X' || $socioPaya->getEstado()=='I') {
				$estadosSociosId = 2;
			}

			//Socio Ausente
			if ($socioPaya->getEstado()=='T') {
				$estadosSociosId = 4;
			}

			//Suspendido
			if ($socioPaya->getEstado()=='S') {
				$estadosSociosId = 5;
			}

			//Miembro Visitante Temporal
			if ($socioPaya->getEstado()=='V') {
				$estadosSociosId = 6;
			}

			if (!$socioPaya->getTelefonoCasa()) {
				$socioPaya->setTelefonoCasa(1);
			}

			if (!$socioPaya->getCelular()) {
				$socioPaya->setCelular(1);
			}

			if (!$socioPaya->getCorreo1()) {
				$socioPaya->setCorreo1('cartera@clubpayande.com');
			}

			if (!$socioPaya->getTipoDocumentosId()) {
				$socioPaya->setTipoDocumentosId(1);
			}

			if (!$socioPaya->getTipoSociosId()) {
				$socioPaya->setTipoSociosId(1);
			}

			if (!$socioPaya->getNombres()) {
				$socioPaya->setNombres('?????');
			}

			if (!$socioPaya->getApellidos()) {
				$socioPaya->setApellidos('??????');
			}

			if (!$socioPaya->getDireccionCasa()) {
				$socioPaya->setDireccionCasa('??????');
			}

			if (!$socioPaya->getSexo()) {
				$socioPaya->setSexo('M');
			}

			if (!$socioPaya->getParentescosId()) {
				$socioPaya->setParentescosId(1);
			}

			if (!$socioPaya->getFechaNacimiento()) {
				$socioPaya->setFechaNacimiento('1900-01-01');
			}

			//ESTUDIOS
			$estudiosSocio = EntityManager::get('EstudiosPaya')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'socios_id='.$socioPaya->getId()));

			$ciudadColegio 			= 0;	
			$ciudadUniversidad 		= 0;
			$estudiosInstitucion 	= array();
			$estudiosFechaGrado 	= array();
			$estudiosTitulo 		= array();
			$estudiosCiudad 		= array();

			if ($estudiosSocio!=false) {
			
				$ciudadColegio = 0;	
				if ($socioPaya->getCiudadExpedido()==$estudiosSocio->getCiudadColegio()) {
					$ciudadColegio = $ciudadExpedido;
				}else{
					$ciudadColegio = $this->_getLocationByCiudadesId($estudiosSocio->getCiudadColegio());
				}
			
				$ciudadUniversidad = 0;	
				if ($socioPaya->getCiudadExpedido()==$estudiosSocio->getCiudadUniversidad()) {
					$ciudadUniversidad = $ciudadExpedido;
				}else{
					$ciudadUniversidad = $this->_getLocationByCiudadesId($estudiosSocio->getCiudadUniversidad());
				}

				if (!$estudiosSocio->getFechaGrado1() || $estudiosSocio->getFechaGrado1()=='0000-00-00') {
					$estudiosSocio->setFechaGrado1(date('Y-m-d'));
				}
				if ($estudiosSocio->getColegio()) {
					$estudiosInstitucion[]=$estudiosSocio->getColegio();
				}else{
					$estudiosInstitucion[]='??????';
				}
				if ($estudiosSocio->getUniversidad()) {
					$estudiosInstitucion[]=$estudiosSocio->getUniversidad();
				}else{
					$estudiosInstitucion[]='??????';
				}

				if ($estudiosSocio->getFechaGrado1() && $estudiosSocio->getFechaGrado1()!='0000-00-00') {
					$estudiosFechaGrado[]=$estudiosSocio->getFechaGrado1();
				}else{
					$estudiosFechaGrado[]='1900-01-01';
				}
				if ($estudiosSocio->getFechaGrado2() && $estudiosSocio->getFechaGrado2()!='0000-00-00') {
					$estudiosFechaGrado[]=$estudiosSocio->getFechaGrado2();
				}else{
					$estudiosFechaGrado[]='1900-01-01';
				}

				if ($estudiosSocio->getTitulo1()) {
					$estudiosTitulo[]=$estudiosSocio->getTitulo1();
				}else{
					$estudiosTitulo[]='??????';
				}
				if ($estudiosSocio->getTitulo2()) {
					$estudiosTitulo[]=$estudiosSocio->getTitulo2();
				}else{
					$estudiosTitulo[]='??????';
				}

				$estudiosCiudad = array($ciudadColegio, $ciudadUniversidad);

			}


			//Trabajos
			$trabajosPaya = EntityManager::get('TrabajosPaya')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'socios_id='.$socioPaya->getId()));
			
			$expLaboralesFax 		= array();
			$expLaboralesTelefono 	= array();
			$expLaboralesCargo 		= array();
			$expLaboralesDireccion 	= array();
			$expLaboralesEmpresa 	= array();
			$expLaboralesFecha		= array();

			if ($trabajosPaya!=false) {
				
				if ($trabajosPaya->getEmpresa1()) {
					$expLaboralesEmpresa[]=$trabajosPaya->getEmpresa1();
				}else{
					$expLaboralesEmpresa[]='??????';
				} 
				if ($trabajosPaya->getEmpresa2()) {
					$expLaboralesEmpresa[]=$trabajosPaya->getEmpresa2();
				}else{
					$expLaboralesEmpresa[]='??????';
				}

				if ($trabajosPaya->getDireccion1()) {
					$expLaboralesDireccion[]=$trabajosPaya->getDireccion1();
				}else{
					$expLaboralesDireccion[]='??????';
				} 
				if ($trabajosPaya->getDireccion2()) {
					$expLaboralesDireccion[]=$trabajosPaya->getDireccion2();
				}else{
					$expLaboralesDireccion[]='??????';
				}

				if ($trabajosPaya->getCargo1()) {
					$expLaboralesCargo[]=$trabajosPaya->getCargo1();
				}else{
					$expLaboralesCargo[]='??????';
				} 
				if ($trabajosPaya->getCargo2()) {
					$expLaboralesCargo[]=$trabajosPaya->getCargo2();
				}else{
					$expLaboralesCargo[]='??????';
				}

				if ($trabajosPaya->getTelefono1()) {
					$expLaboralesTelefono[]=$trabajosPaya->getTelefono1();
				}else{
					$expLaboralesTelefono[]='1';
				} 
				if ($trabajosPaya->getTelefono2()) {
					$expLaboralesTelefono[]=$trabajosPaya->getTelefono2();
				}else{
					$expLaboralesTelefono[]='1';
				}

				if ($trabajosPaya->getFax1()) {
					$expLaboralesFax[]=$trabajosPaya->getFax1();
				}else{
					$expLaboralesFax[]='1';
				} 
				if ($trabajosPaya->getFax2()) {
					$expLaboralesFax[]=$trabajosPaya->getFax2();
				}else{
					$expLaboralesFax[]='1';
				}

				$expLaboralesFecha = array('1900-01-01','1900-01-01');

			}

			//Actividades
			$actividades = array();
			$actividadesPayaObj = EntityManager::get('ActividadesPaya')->setTransaction($this->_transaction)->find(array('conditions'=>'socios_id='.$socioPaya->getId()));
			foreach ($actividadesPayaObj as $actividadPaya) 
			{
				$actividades[]=$actividadPaya->getHobbiesId();
			}

			//Clubes
			$asoclubes = array();
			$asoclubesPaya = EntityManager::get('AsoclubesPaya')->setTransaction($this->_transaction)->findFirst(array('conditions'=>'socios_id='.$socioPaya->getId()));
			if ($asoclubesPaya!=false) {
				$asoclubes[$asoclubesPaya->getClub1()]=$asoclubesPaya->getDesde1();
				$asoclubes[$asoclubesPaya->getClub2()]=$asoclubesPaya->getDesde2();
				$asoclubes[$asoclubesPaya->getClub3()]=$asoclubesPaya->getDesde3();
			}
			//throw new Exception(print_r($asoclubes,true));

			//cargosfijos se aplica derecho de fundacion y tipo 1 si es -00
			$cargosFijos = array();
			if (strstr($numeroAccion, '-00')) {
				$cargosFijos = array(1, 2);
			}

			//Creamos structura para crear socio
			$tmpConfig = array(
				//Numero Acción Consecutivo/Manual
				'numeroAccionManual'		=> 'S',//Manual
				//Socios
				'titularId'					=> null,//Socio titular NULL es el titular
				'numeroAccion'				=> $socioPaya->getNumeroAccion(),
				'fechaIngreso'				=> $socioPaya->getFechaIngreso(),
				'parentescosId'				=> $socioPaya->getParentescosId(),
				'tipoDocumentosId'			=> $socioPaya->getTipoDocumentosId(),
				'identificacion'			=> $socioPaya->getIdentificacion(),
				'apellidos'					=> $socioPaya->getApellidos(),
				'nombres'					=> $socioPaya->getNombres(),
				'ciudadExpedido'			=> $ciudadExpedido,
				'ciudadNacimiento'			=> $ciudadNacimiento,
				'fechaNacimiento'			=> $socioPaya->getFechaNacimiento(),
				'sexo'						=> $socioPaya->getSexo(),
				'direccionCasa'				=> $socioPaya->getDireccionCasa(),
				'ciudadCasa'				=> $ciudadCasa,
				'telefonoCasa'				=> $socioPaya->getTelefonoCasa(),
				'direccionTrabajo'			=> $socioPaya->getDireccionTrabajo(),
				'telefonoTrabajo'			=> $socioPaya->getTelefonoTrabajo(),
				'celular'					=> $socioPaya->getCelular(),
				'direccionCorrespondencia'	=> 'C',//Casa
				'correo1'					=> $socioPaya->getCorreo1(),
				'correo2'					=> $socioPaya->getCorreo2(),
				'fax'						=> $socioPaya->getFax(),
				'tipoSociosId'				=> $socioPaya->getTipoSociosId(),
				'enviaCorreo'				=> 'S',
				'cobra'						=> 'S',
				'estadosSociosId'			=> $estadosSociosId,
				'estadosCivilesId'			=> $socioPaya->getEstadosCivilesId(),
				'imagenSocio'				=> $socioPaya->getImagenSocio(),
				//Estudios
				'estudiosInstitucion'		=> $estudiosInstitucion,
				'estudiosCiudadId'			=> $estudiosCiudad,
				'estudiosFechaGrado'		=> $estudiosFechaGrado,
				'estudiosTitulo'			=> $estudiosTitulo,
				//Experiencia Laboral
				'expLaboralesEmpresa'		=> $expLaboralesEmpresa,
				'expLaboralesDireccion'		=> $expLaboralesDireccion,
				'expLaboralesCargo'			=> $expLaboralesCargo,
				'expLaboralesTelefono'		=> $expLaboralesTelefono,
				'expLaboralesFax'			=> $expLaboralesFax,
				'expLaboralesFecha'			=> $expLaboralesFecha,
				//Actividades
				'actividades'				=> $actividades,
				//Clubes
				'clubId'					=> array_keys($asoclubes),
				'clubDesde'					=> $asoclubes,
				//Cargos Periodicos
				'cargosFijosId'				=> $cargosFijos, //A todos aportes a fundación
				//Otros Socios
				'otrosSociosId'				=> array(),
				'tipoAsociacionSocioId'		=> array(),
				//Tarjeta
				'numeroTarjeta'				=> $socioPaya->getNumeroTarjeta(),
				'formasPagosId'				=> $socioPaya->getFormasPagoId(),
				'fechaExp'					=> '1900-01-01',
				'fechaVen'					=> '1900-01-01',
				'bancosId'					=> '1',
				'digitoVerificacion'		=> substr($socioPaya->getNumeroTarjeta(), strlen($socioPaya->getNumeroTarjeta())-4),
				'estado'					=> 'A',
				//Validacion contrato
				/*'validarSocio'				=> array(
					'numeroAccion'	=> '1-1',//1-titular
				)*/
			);

			if ($numeroAccion=='0460-00' || $numeroAccion=='0001-02') {
				//throw new Exception(print_r($tmpConfig,true));
			} else {
				//continue;
			}

			$configAll[] = $tmpConfig;
		}

		//Recorre los contratos creadolos y almacenando su ActiveRecord
		$SociosObj = array();
		
		foreach($configAll as $config)
		{
			//crear en contrato con base a $config
			$Socios = SociosCore::crearSocio($config, $this->_transaction);

			//almacenamos ActiveRecords creados
			$SociosObj[]= $Socios;
		}
		return $SociosObj;
	}

	/*
	* Verifica si existe un titular segun criterios de numero de accion "-00"
	*/
	private function _checkTitulares()
	{
		//buscamos socios con numero de accion diferente a titular
		$sociosObj = EntityManager::get('Socios')->setTransaction($this->_transaction)->find(array('conditions'=>'numero_accion NOT LIKE "%-00"'));
		
		foreach ($sociosObj as $socio) 
		{
			$numeroAccion = $socio->getNumeroAccion();
			if ($numeroAccion) {
				$perfilSocio = explode('-',$numeroAccion);

				$socioPayaT = EntityManager::get('Socios')->setTransaction($this->_transaction)->findFirst(array('conditions'=>"numero_accion='{$perfilSocio[0]}-00'"));
				//Si encontro alguien 00 con sus XXXX iguales retorne su id es su titular
				if ($socioPayaT!=false) {
					$socio->setTitularId($socioPayaT->getSociosId());
					if ($socio->save() == false) {
						foreach ($socio->getMessages() as $message) 
						{
							throw new Exception($message->getMessage());
						}
					}
				}
			}
		}
		
	}

	/**
	* Mira la ciudades de los socios y saca su location
	*/
	private function _getLocationByCiudadesId($ciudadId=false)
	{
		if ($ciudadId == false) {
			return 0;
		}

		$ciudadesPaya = EntityManager::get('Ciudades')->setTransaction($this->_transaction)->findFirst($ciudadId);
		if ($ciudadesPaya!=false) {
			$ciudadName = $ciudadesPaya->getNombre();
			return $this->_getLocation($ciudadName);
		}
		return 0;
	}

	/**
	* Obtien el location de la tabla ciudades de socios
	*/
	private function _getLocation($nombre) 
	{
		$locationId = 0;

		//buscamos en nuevo sistema de ciudades
		$location = EntityManager::get('Location', true)->setTransaction($this->_transaction)->findFirst(array('conditions'=>'name LIKE "%'.$nombre.'%" AND zone_id IN(SELECT id from hfos_geoinfo.zone where territory_id = 135)'));//Colombia solamente
		
		if ($location!=false) {
			//throw new Exception($ciudades->getNombre().'->'.$location->getName().' 2');
			$locationId = $location->getId();
		} else {
			$location = EntityManager::get('Location', true)->setTransaction($this->_transaction)->findFirst(array('conditions'=>'name LIKE "%'.$nombre.'%"'));//otro menos Colombia 
			if ($location!=false) {
				//throw new Exception($ciudades->getNombre().'->'.$location->getName().' 2');
				$locationId = $location->getId();
			}
		}
		return $locationId;
	}

	/**
	* Agrega un prestamo a todos los titulares basico de payande
	*/
	private function _addPrestamo($config) 
	{
		
		Core::importFromLibrary('Hfos/Socios','SociosFactura.php');

		//buscamos todos los socios titulares
		$sociosObj = EntityManager::get('Socios')->setTransaction($this->_transaction)->find(array('conditions'=>'numero_accion LIKE "%-00"'));

		foreach ($sociosObj as $socio) 
		{
			//creando prestamo al socio
			$prestamosSocios = EntityManager::get('prestamosSocios',true)->setTransaction($this->_transaction);
			$prestamosSocios->setSociosId($socio->getSociosId());
			$prestamosSocios->setValorFinanciacion($config['valorFinanciacion']);
			$prestamosSocios->setFechaPrestamo($config['fechaPrestamo']);
			$prestamosSocios->setFechaInicio($config['fechaInicio']);
			$prestamosSocios->setNumeroCuotas($config['numeroCuotas']);
			$prestamosSocios->setEstado('D');//Debe

			if ($prestamosSocios->save() == false) {
				foreach ($prestamosSocios->getMessages() as $message) 
				{
					throw new Exception('addPrestamo: '.$message->getMessage());	
				}
			}

			//Generando amortizacion
			$configAmortizacion = array(
				'prestamosSociosId'	=> $prestamosSocios->getId(),
				'valorFinanciacion'	=> $config['valorFinanciacion'],
				'fechaCompra'		=> $config['fechaInicio'],
				'plazoMeses'		=> $config['numeroCuotas']
			);
			SociosFactura::crearAmortizacion($configAmortizacion, $this->_transaction);
		}
	}

	/**
	* Proceso de importacion de saldos
	*/
	public function importarSaldos() 
	{

		set_time_limit(0);

		try 
		{

			$this->_transaction = TransactionManager::getUserTransaction();

			$periodo = 201210;
			$fechaFinal = '2012-10-31';
			$fechaVencimiento = '2012-11-10';

			$comprobante 			= 'CXS';
			$tipoDocumento 			= 'CXS';

			$cuentaSeguro 			= 414095005005;
			$cuentaFundacion 		= 414095005005;
			$cuentaSostenimiento 	= 414095005005;

			$cuentaSeguroC 			= 134525001;
			$cuentaFundacionC 		= 134525001;
			$cuentaSostenimientoC 	= 134525001;

			$path = '/tmp/';

			$a = file_get_contents($path.'a.json');
			$dataObj = json_decode($a);
			
			#creamos listado de cedulas que existen
			$nitsExists = array();
			foreach ($dataObj as $data)
			{
				$nitsExists[$data->identificacion]=$data;
			} 

			$movements = array();

			$sociosObj = $this->Socios->find();
			foreach ($sociosObj as $socios) 
			{

				//Borrar Movi
				$this->Movi->setTransaction($this->_transaction)->delete(array('conditions'=>"nit='{$socios->getIdentificacion()}'"));

				//Borrar Cartera
				$this->Cartera->setTransaction($this->_transaction)->delete(array('conditions'=>"nit='{$socios->getIdentificacion()}'"));

				#echo "<br>",
				$numeroAccion = $socios->getNumeroAccion();

				if (isset($nitsExists[$socios->getIdentificacion()])) {

					$data = $nitsExists[$socios->getIdentificacion()];

					//Add Saldo Anterior
					$data->periodo 					= $periodo;
					$data->fechaVencimiento 		= $fechaVencimiento;
					$data->tipoDocumento 			= $tipoDocumento;
					$data->comprobante 				= $comprobante;

					$data->cuentaSeguro 			= $cuentaSeguro;
					$data->cuentaFundacion 			= $cuentaFundacion;
					$data->cuentaSostenimiento 		= $cuentaSostenimiento;
					
					$data->cuentaSeguroC 			= $cuentaSeguroC;
					$data->cuentaFundacionC 		= $cuentaFundacionC;
					$data->cuentaSostenimientoC 	= $cuentaSostenimientoC;

					#echo "<br>",
					$data->numeroAccion = $numeroAccion;

					if ($data->numeroAccion && $data->identificacion) {
						$this->_addSaldoAnterior($data, $movements);
					}

				} else {

					//Borramos movimiento de ese periodo porque ya pago
					$movimiento = $this->Movimiento->setTransaction($this->_transaction)->delete(array('conditions'=>"socios_id='{$socios->getSociosId()}'"));
					
				}

			}

			$this->_transaction->commit();
		}
		catch(Exception $e){
			$this->_transaction->rollback($e->getMessage());
			throw new Exception($e->getMessage(), 1);
			
		}
	}

	/**
	* Add saldo anterior into movimiento
	*
	* @param array $data
	* @return boolean
	*/
	private function _addSaldoAnterior(&$data, &$movements) 
	{
		$flag = true;

		$socio = $this->Socios->setTransaction($this->_transaction)->findFirst(array('conditions'=>"numero_accion='{$data->numeroAccion}' AND identificacion='{$data->identificacion}'"));
		if ($socio == false) {
			echo "<br>",'El socios no existe ',"numero_accion='{$data->numeroAccion}' AND identificacion='{$data->identificacion}'";
			$flag = false;
		} else {
			
			//Limpiamos movi de tercero
			$this->Movi->setTransaction($this->_transaction)->delete(array('conditions'=>"nit='{$data->identificacion}'"));

			//limpiamos cartera
			$this->Cartera->setTransaction($this->_transaction)->delete(array('conditions'=>"nit='{$data->identificacion}'"));

			$movimiento = $this->Movimiento->setTransaction($this->_transaction)->findFirst(array('conditions'=>"socios_id='{$socio->getSociosId()}' AND periodo='{$data->periodo}'"));

			if ($movimiento!=false) {
				echo "<br>",$data->saldoAnterior->saldo;
				if (isset($data->saldoAnterior->saldo)) {
					
					//saldo importado
					$movimiento->setSaldoActual($data->saldoAnterior->saldo);
					
					$periodo = $this->Periodo->findFirst(array('conditions'=>"periodo='{$movimiento->getPeriodo()}'"));
					if ($periodo == false) {
						throw new Exception('El periodo no existe!');
					}

					if ($movimiento->save() == false) {
						foreach ($movimiento->getMessages() as $message) 
						{
							throw new Exception($message->getMessage());
						}
					}

					//Agregando detalles de movimiento de saldo anterior
					$detalleMovimientoMora = $this->DetalleMovimiento->setTransaction($this->_transaction)->findFirst(array('conditions'=>"movimiento_id='{$movimiento->getId()}'"));
					if ($detalleMovimientoMora!=false) {
						
						$detalleMovimiento = new DetalleMovimiento();
						$detalleMovimiento->setTransaction($this->_transaction);
						$detalleMovimiento->setMovimientoId($movimiento->getId());
						$detalleMovimiento->setSociosId($movimiento->getSociosId());
						$detalleMovimiento->setFecha($data->fechaFinal);
						$detalleMovimiento->setFechaVenc($data->fechaVencimiento);
						$detalleMovimiento->setTipo('C');
						$detalleMovimiento->setValor($data->saldoAnterior->saldo);
						$detalleMovimiento->setIva(0);
						$detalleMovimiento->setTotal($data->saldoAnterior->saldo);
						$detalleMovimiento->setDescripcion('SALDO ANTERIOR');
						$detalleMovimiento->setEstado('A');
						$detalleMovimiento->setTipoDocumento('CXS');

						if ($detalleMovimiento->save() == false) {
							foreach ($detalleMovimiento->getMessages() as $message) 
							{
								throw new Exception($message->getMessage());
							}
						}

					}

					if ($data->numeroAccion) {
						$movements = $this->_makeMovementSaldos($data);
					} else {
						echo "<br> No se ingreso el array ",print_r($data,true);
					}

					#Add Aura
					$this->_addPagos($data, $movements, 0);

				}
			} else {
				$flag = false;
			}

		}
		
		return $flag;
	}

	//Crea movment para inicializar cartera
	private function _makeMovementSaldos(&$data) 
	{

		$movements = array();

		if ($data->numeroAccion) {

			echo "<br>numeroAccion: ",$data->numeroAccion;
			echo "<br>identificacion: ",$data->identificacion;

			$numeroAccion = $data->numeroAccion;
			
			$socios = $this->Socios->findFirst(array('conditions'=>"numero_accion='$numeroAccion'"));
			if ($socios == false) {
				return false;
			}

			echo "<br>identificacion2: ",$data->identificacion;

			$sociosFactura = new SociosFactura();
			$sociosFactura->checkTercero($socios);
			
			$total = 0;
			$auraConsecutivo = 0;
			
			$moviSaldoAnterior = $data->saldoAnterior->movi;

			$numeroDocumento = $nit = Filter::bring($numeroAccion, 'int');

			//SEGURO
			if ($moviSaldoAnterior->seguro!=0) {

				$debCre1 = 'C';
				$debCre2 = 'D';
				if ($moviSaldoAnterior->seguro<0) {
					$debCre1 = 'D';
					$debCre2 = 'C';
					$moviSaldoAnterior->seguro = abs($moviSaldoAnterior->seguro);
				}

				$movements[] = array(
					'Descripcion'	=> 'MIGRACION SALDOS OCTUBRE 2012',
					'Nit'			=> $data->identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> $data->cuentaSeguro,
					'Valor'			=> $moviSaldoAnterior->seguro,
					'BaseGrab'		=> $moviSaldoAnterior->seguro,
					'TipoDocumento'	=> $data->tipoDocumento,
					'NumeroDocumento' => $numeroDocumento,
					'FechaVence'	=> $data->fechaVencimiento,
					'DebCre'		=> $debCre1,
					'debug'			=> true
				);
				$movements[] = array(
					'Descripcion'	=> 'MIGRACION SALDOS OCTUBRE 2012',
					'Nit'			=> $data->identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> $data->cuentaSeguroC,
					'Valor'			=> $moviSaldoAnterior->seguro,
					'BaseGrab'		=> $moviSaldoAnterior->seguro,
					'TipoDocumento'	=> $data->tipoDocumento,
					'NumeroDocumento' => $numeroDocumento,
					'FechaVence'	=> $data->fechaVencimiento,
					'DebCre'		=> $debCre2,
					'debug'			=> true
				);
			}

			//FUNDACION
			if ($moviSaldoAnterior->fundacion!=0) {

				$debCre1 = 'C';
				$debCre2 = 'D';
				if ($moviSaldoAnterior->fundacion<0) {
					$debCre1 = 'D';
					$debCre2 = 'C';
					$moviSaldoAnterior->fundacion = abs($moviSaldoAnterior->fundacion);
				}

				$movements[] = array(
					'Descripcion'	=> 'MIGRACION SALDOS OCTUBRE 2012',
					'Nit'			=> $data->identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> $data->cuentaFundacion,
					'Valor'			=> $moviSaldoAnterior->fundacion,
					'BaseGrab'		=> $moviSaldoAnterior->fundacion,
					'TipoDocumento'	=> $data->tipoDocumento,
					'NumeroDocumento' => $numeroDocumento,
					'FechaVence'	=> $data->fechaVencimiento,
					'DebCre'		=> $debCre1,
					'debug'			=> true
				);
				$movements[] = array(
					'Descripcion'	=> 'MIGRACION SALDOS OCTUBRE 2012',
					'Nit'			=> $data->identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> $data->cuentaFundacionC,
					'Valor'			=> $moviSaldoAnterior->fundacion,
					'BaseGrab'		=> $moviSaldoAnterior->fundacion,
					'TipoDocumento'	=> $data->tipoDocumento,
					'NumeroDocumento' => $numeroDocumento,
					'FechaVence'	=> $data->fechaVencimiento,
					'DebCre'		=> $debCre2,
					'debug'			=> true
				);

			}

			//SOSTENIMIENTO
			if ($moviSaldoAnterior->sostenimiento!=0) {

				$debCre1 = 'C';
				$debCre2 = 'D';
				if ($moviSaldoAnterior->sostenimiento<0) {
					$debCre1 = 'D';
					$debCre2 = 'C';
					$moviSaldoAnterior->sostenimiento = abs($moviSaldoAnterior->sostenimiento);
				}
				$movements[] = array(
					'Descripcion'	=> 'MIGRACION SALDOS OCTUBRE 2012',
					'Nit'			=> $data->identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> $data->cuentaSostenimiento,
					'Valor'			=> $moviSaldoAnterior->sostenimiento,
					'BaseGrab'		=> $moviSaldoAnterior->sostenimiento,
					'TipoDocumento'	=> $data->tipoDocumento,
					'NumeroDocumento' => $numeroDocumento,
					'FechaVence'	=> $data->fechaVencimiento,
					'DebCre'		=> $debCre1,
					'debug'			=> true
				);
				$movements[] = array(
					'Descripcion'	=> 'MIGRACION SALDOS OCTUBRE 2012',
					'Nit'			=> $data->identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> $data->cuentaSostenimientoC,
					'Valor'			=> $moviSaldoAnterior->sostenimiento,
					'BaseGrab'		=> $moviSaldoAnterior->sostenimiento,
					'TipoDocumento'	=> $data->tipoDocumento,
					'NumeroDocumento' => $numeroDocumento,
					'FechaVence'	=> $data->fechaVencimiento,
					'DebCre'		=> $debCre2,
					'debug'			=> true
				);

			}

		}

		return $movements;
	}

	////////////////
	///	 PAGOS
	////////////////

	/**
	* Proceso de importacion de pagos de mes
	*/
	public function importarPagos() 
	{

		set_time_limit(0);

		try 
		{

			$this->_transaction = TransactionManager::getUserTransaction();

			//$periodo = 201206;
			//$fechaFinal = '2012-06-30';
			//$fechaVencimiento = '2012-07-10';
			$periodo = 201210;
			$fechaFinal = '2012-10-30';
			$fechaVencimiento = '2012-08-10';
			

			$comprobante 			= 'CXS';
			$tipoDocumento 			= 'CXS';

			$cuentaFormaPago		= 138025;

			$cuentaSeguro 			= 416530015;
			$cuentaFundacion 		= 281505;
			$cuentaSostenimiento 	= 416530001;

			$cuentaSeguroC 			= 130505055;
			$cuentaFundacionC 		= 130505060;
			$cuentaSostenimientoC 	= 130505050;
			
			$path = '/tmp/';

			//$b = file_get_contents($path.'b.json');
			$b = file_get_contents($path.'b.json');
			$dataObj = json_decode($b);
			unset($b);

			$movements = array();
			foreach ($dataObj as $rc) 
			{

				$rcMovi = null;

				foreach ($rc as $data) 
				{

					//add saldo anterior
					$data->periodo 					= $periodo;
					$data->fechaFinal 				= $fechaFinal;
					$data->fechaVencimiento 		= $fechaVencimiento;
					$data->tipoDocumento 			= $tipoDocumento;
					$data->comprobante 				= $comprobante;

					$data->cuentaFormaPago			= $cuentaFormaPago;

					$data->cuentaSeguroC 			= $cuentaSeguroC;
					$data->cuentaFundacionC 		= $cuentaFundacionC;
					$data->cuentaSostenimientoC 	= $cuentaSostenimientoC;

					#if ($data->numeroAccion=='13') {
					if ($data->numeroAccion) {
						$ret = $this->_makeMovementPagos($data, $movements);
						if ($ret!=false) {
							$rcMovi = $ret['rcMovi'];
						} else {
							continue;
						}
						
					} else {
						echo "<br> No se ingreso el array ",print_r($data,true);
					}

				}
				
				#Aura
				if (is_array($movements) && count($movements)>1) {
					if ($data->numeroAccion) {
						$data->comprobante = 'ING';
						$this->_addPagos($data, $movements, 0);
					}
				}
			}

			///PAGOS POR MOVI
			/*$c = file_get_contents($path.'c.json');
			$dataObjC = json_decode($c);
			unset($c);

			$movements = array();
			$filter = new Filter();
			foreach ($dataObjC as $nit) 
			{

				foreach ($nit as $data) 
				{

					$socios = $this->Socios->setTransaction($this->_transaction)->findFirst(array('conditions'=>"identificacion='{$data->identificacion}'"));

					if($socios!=false){

						$numeroDoc = $filter->apply($socios->getNumeroAccion(),array('int'));
						$movements[] = array(
							'Descripcion'	=> $data->descripcion,
							'Nit'			=> $data->identificacion,
							'CentroCosto'	=> $data->centroCosto,
							'Cuenta'		=> $data->cuenta,
							'Valor'			=> $data->valor,
							'BaseGrab'		=> $data->base_grab,
							'TipoDocumento'	=> 'CXS',
							'NumeroDocumento' => $numeroDoc,
							'FechaVence'	=> $data->f_vence,
							'DebCre'		=> $data->debCre,
							'debug'			=> true
						);

					} else {
						echo "<br>","El socio con identificacion '{$data->identificacion}' no existe";
					}

				}				
				
			}
			#Aura Movi
			if (is_array($movements) && count($movements)>1) {
				if ($data->nit) {
					$data->comprobante = 'ING';
					$this->_addPagos($data, $movements, 0);
				}
			}*/


			//COMMIT

			$this->_transaction->commit();
		} 
		catch(Exception $e) {
			throw new Exception($e->getMessage(), 1);
		}
	}

	/**
	* Metodo que obtiene el saldo de cartera por cuenta y cedula
	* 
	* @param int $codigoCuenta
	* @param int $identificacion
	* @return double
	*/
	private function _getSaldoCarteraByCuenta($codigoCuenta, $identificacion) 
	{
		$return = 0;
		$cuenta = $this->Cuentas->findFirst($codigoCuenta);
		if ($cuenta!=false) {
			$cartera = $this->Cartera->setTransaction($this->_transaction)->findFirst(array('conditions' => "cuenta='{$codigoCuenta}' AND nit='{$identificacion}'"));
			if ($cartera!=false) {
				$return = $cartera->getSaldo();
			}
		}
		return $return;
	}

	/**
	* Make exception if not exists index into array
	*/ 
	private function _validarIndexInArray($list, $array) 
	{
		foreach($list as $index) 
		{
			if (!isset($array[$index])) {
				throw new Exception("No existe el index '{$index}'", 1);
			}
		}
	}

	/**
	* Administra el saldo sobrante de los pagos con los saldos de cartera y lo ingresa donde se necesite
	*
	* @return array (
	*	130505050 => 900000,
	*	130505055 => 92000,
	*	130505060 => 28000,
	* )
	*/
	private function _validarSaldosPagos(&$config) 
	{

		$list = array('cuentaSostenimientoC','cuentaSeguroC', 'cuentaFundacionC', 'valorPagoSostenimiento', 'valorPagoSeguro', 'valorPagoFundacion', 'identificacion');
		$this->_validarIndexInArray($list, $config);		

		$valorPagoTotal = $config['valorPagoFundacion'] + $config['valorPagoSeguro'] + $config['valorPagoSostenimiento'];

		$listCuentas = array(
			$config['cuentaFundacionC'] => $config['valorPagoFundacion'],
			$config['cuentaSeguroC'] => $config['valorPagoSeguro'],
			$config['cuentaSostenimientoC'] => $config['valorPagoSostenimiento']
		);

		$total = 0;
		$config['saldosCartera'] = array();
		foreach ($listCuentas as $cuenta => $valorPago) 
		{

			//obtnemos el saldo de cartera de esa cuenta
			$saldoCartera = localeMath::round($this->_getSaldoCarteraByCuenta($cuenta, $config['identificacion']),0);

			$config['saldosCartera'][$cuenta] = $saldoCartera;

			if ($saldoCartera!=0) {

				if ($saldoCartera>0) {
					//SALDO POSITIVO
					
					if ($saldoCartera < $valorPagoTotal) {

						$valorPagoTotal -= 	$saldoCartera;
						$listCuentas[$cuenta] = $saldoCartera;

					} else {
						
						$listCuentas[$cuenta] = $valorPagoTotal;
						$valorPagoTotal = 0;
						
					}
				} else {
					//SALDO NEGATIVO

					$valorPagoTotal += abs($saldoCartera);
					$listCuentas[$cuenta] = 0;

				}

			}

			$total += $listCuentas[$cuenta];
		}

		$listCuentas['valorPagoTotal'] = $valorPagoTotal;
		if ($valorPagoTotal>0) {
			$listCuentas[$config['cuentaSostenimientoC']] += $valorPagoTotal;
			$total += $valorPagoTotal;
			$valorPagoTotal = 0;
		}
		$listCuentas['valorPagoTotal2'] = $valorPagoTotal;
		
		//Total
		$listCuentas['total'] = $total;

		return $listCuentas;
	}


	#Make movmento to pagos de junio
	private function _makeMovementPagos(&$data, &$movements) 
	{

		$movements=array();

		if ($data->numeroAccion) {

			echo "<br>numeroAccion: ",$data->numeroAccion;
			echo "<br>identificacion: ",$data->identificacion;

			$numeroAccion = $data->numeroAccion;

			if (!strstr($numeroAccion,"-")) {
				if ($numeroAccion<10) {
					$numeroAccion = '000'.$numeroAccion;
				} else {
					if ($numeroAccion<100) {
						$numeroAccion = '00'.$numeroAccion;
					} else {
						if ($numeroAccion<1000) {
							$numeroAccion = '0'.$numeroAccion;
						}						
					}
				}
			} else {
				$numeroAccion = substr($numeroAccion, 0, strpos($numeroAccion, "-"));
			}

			echo "<br>numeroAccion2: ",$numeroAccion;

			echo "<br>","numero_accion='$numeroAccion-00'";
			$socios = $this->Socios->findFirst(array('conditions'=>"identificacion='{$data->identificacion}'"));
			if ($socios == false) {
				return 'FAILED';
			} else {
				$data->identificacion = $socios->getIdentificacion();
			}

			echo "<br>identificacion2: ",$data->identificacion;

			$numeroDocumento = $nit = Filter::bring($numeroAccion, 'int');


			$sociosFactura = new SociosFactura();
			$sociosFactura->checkTercero($socios);
			
			//$aura = new Aura($codigoComprob, 0, $fechaFactura, Aura::OP_CREATE);
			$total = 0;
			$auraConsecutivo = 0;
			
			$seguro 		= $data->pagos->seguro;
			$fundacion 		= $data->pagos->fundacion;
			$sostenimiento 	= $data->pagos->sostenimiento;
			
			//Validamos saldos sobrantes
			$configValorPago = array(
				'identificacion' 			=> $data->identificacion,
				
				'cuentaSeguroC' 			=> $data->cuentaSeguroC,
				'cuentaFundacionC' 			=> $data->cuentaFundacionC,
				'cuentaSostenimientoC' 		=> $data->cuentaSostenimientoC,

				'valorPagoSeguro'			=> $seguro->valorPago,
				'valorPagoFundacion'		=> $fundacion->valorPago,
				'valorPagoSostenimiento'	=> $sostenimiento->valorPago
			);
			$valorPagosByCuenta = $this->_validarSaldosPagos($configValorPago);

			//if ($data->identificacion=='19296792') {
			//if ($data->identificacion=='17061109') {
			//if ($data->identificacion=='80417635') {
				//print_r($valorPagosByCuenta);
				//print_r($configValorPago);
				//throw new Exception("Error Processing Request", 1);
			//}

			//SEGURO
			if ($valorPagosByCuenta[$data->cuentaSeguroC]>0) {

				$movements[] = array(
					'Descripcion'	=> $seguro->descripcion,
					'Nit'			=> $data->identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> $data->cuentaSeguroC,
					'Valor'			=> $valorPagosByCuenta[$data->cuentaSeguroC],
					'BaseGrab'		=> 0,
					'TipoDocumento'	=> $data->tipoDocumento,
					'NumeroDocumento' => $numeroDocumento,
					'FechaVence'	=> $data->fechaVencimiento,
					'DebCre'		=> 'C',
					'debug'			=> true
				);
				
			}

			//FUNDACION
			if ($valorPagosByCuenta[$data->cuentaFundacionC]>0) {
	
				$movements[] = array(
					'Descripcion'	=> $fundacion->descripcion,
					'Nit'			=> $data->identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> $data->cuentaFundacionC,
					'Valor'			=> $valorPagosByCuenta[$data->cuentaFundacionC],
					'BaseGrab'		=> 0,
					'TipoDocumento'	=> $data->tipoDocumento,
					'NumeroDocumento' => $numeroDocumento,
					'FechaVence'	=> $data->fechaVencimiento,
					'DebCre'		=> 'C',
					'debug'			=> true
				);
			}

			//SOSTENIMIENTO
			if ($valorPagosByCuenta[$data->cuentaSostenimientoC]>0) {

				$movements[] = array(
					'Descripcion'	=> $sostenimiento->descripcion,
					'Nit'			=> $data->identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> $data->cuentaSostenimientoC,
					'Valor'			=> $valorPagosByCuenta[$data->cuentaSostenimientoC],
					'BaseGrab'		=> 0,
					'TipoDocumento'	=> $data->tipoDocumento,
					'NumeroDocumento' => $numeroDocumento,
					'FechaVence'	=> $data->fechaVencimiento,
					'DebCre'		=> 'C',
					'debug'			=> true
				);

			}


			//CONSOLIDAR
			$movements[] = array(
				'Descripcion'	=> 'PAGO DE FACTURA JUNIO 2012',
				'Nit'			=> $data->identificacion,
				'CentroCosto'	=> 130,
				'Cuenta'		=> $data->cuentaFormaPago,
				'Valor'			=> $valorPagosByCuenta['total'],
				'BaseGrab'		=> $valorPagosByCuenta['total'],
				'FechaVence'	=> $data->fechaVencimiento,
				'DebCre'		=> 'D',
				'debug'			=> true
			);


		}

		print_r($movements);
		return array('rcMovi' => $data->rc, 'movements' => $movements);
	}

	/**
	* Add a Aura
	* 
	* @param array $data
	*/
	private function _addPagos(&$data, $movements, $rcMovi=0) 
	{
		
		try 
		{

			if (!TransactionManager::hasUserTransaction()) {
				$this->_transaction = TransactionManager::getUserTransaction();
			}

			//Aura local
			$aura = new Aura($data->comprobante, $rcMovi,$data->fechaFinal);
			$aura->setDebug(true);

			//add movi
			foreach ($movements as $movement) 
			{
				$aura->addMovement($movement);
			}
			$aura->save();
			$auraConsecutivo = $aura->getConsecutivo($data->comprobante);

			$data->aura = $aura;
			$data->auraConsecutivo = $auraConsecutivo;
		}
		catch(AuraException $e) {
			throw new Exception('Contabilidad: '.$e->getMessage().print_r($e,true));
		}
	}

	/**
	* Add prestamos to cartera by csv
	* 
	*/
	public function makePrestamosByExcel() 
	{

		set_time_limit(0);

		$date = date('Y-m-d');
		$file = '/tmp/a.csv';
		
		if(!$file){
			throw new Exception('El archivo no se pudo cargar al servidor');
		} else {

			if(!preg_match('/\.csv$/', $file)){
				throw new Exception('El archivo cargado parece no ser CSV');
			}

			try {
				$transaction = TransactionManager::getUserTransaction();
				
				Core::importFromLibrary('Hfos/Socios','SociosCore.php');
				$periodo = SociosCore::getCurrentPeriodo();
				
				//Cuenta que cruja los ajustes de pagos
				$cuentaCruce = Settings::get('cuenta_cruce_pagos', 'SO');
				if (!$cuentaCruce) {
					throw new Exception("La cuenta de cruce de ajuste pagos no esta configurado", 1);
				}

				#borramos los prestamos de socios anteriores
				$this->PrestamosSocios->setTransaction($transaction)->deleteAll();

				#borramos prestamos anteriores
				$movisObj = $this->Movi->setTransaction($transaction)->find(array('conditions'=>"descripcion LIKE '%PRESTAMOS SOCIOS No%'", 'group'=>'comprob,numero','columns'=>'comprob,numero'));
				foreach ($movisObj as $movi)
				{
					#echo $movi->getComprob(),' ',$movi->getNumero(),'<br>';
					$aura = new Aura($movi->getComprob(), $movi->getNumero());
					$aura->delete();

					unset($movi);
				}
				unset($movisObj);
					
				$arr_data = file($file);		
				
				$fechaInicioAmorizacion = '2007-06-01';
				$cuentaPrestamo = Settings::get('cuenta_financiacion', 'SO');
				if (!$cuentaPrestamo) {
					throw new Exception("No se a configurado la cuenta de la deuda de financiacion", 1);
				}

				$cuentaPrestamoC = Settings::get('cuenta_cruce_pagos', 'SO');
				if (!$cuentaPrestamoC) {
					throw new Exception("No se a configurado la cuenta cruse de ajustes", 1);
				}

				//throw new Exception(print_r($arr_data,true))	;
				

				//Agrupamos los pagos por comprobantes y numero acción este caso para ingresos y pago con tarjeta
				$data = array();
				foreach ($arr_data as $index => $arr) 
				{
					$line = explode(',',$arr);
					$numeroAccion 	= trim($line[0]);
					$nit 			= trim($line[1]);
					$valorInicial	= trim($line[2]);
					$totalPagado	= trim($line[3]);
					$cuotaMensual	= trim($line[4]);
					$cuotasPagadas	= trim($line[5]);
					$saldoActual	= trim($line[6]);
					$totalCuotas	= trim($line[7]);
										
					//generar saldoe en cartera
					$movements = array();

					$socios = $this->Socios->findFirst(array('conditions'=>"identificacion='$nit'"));
					//$socios = $this->Socios->findFirst(array('conditions'=>"numero_accion='$numeroAccion' AND identificacion='$nit'"));
					if ($socios == false) {
						echo "<br>El socios numero de accion '$numeroAccion' e identificacion '$nit' no existe";
						continue;
						throw new Exception("El socios numero de accion '$numeroAccion' e identificacion '$nit' no existe", 1);
					}

					//creando prestamo al socio
					$prestamosSocios = EntityManager::get('prestamosSocios',true)->setTransaction($transaction);
					$prestamosSocios->setSociosId($socios->getSociosId());
					$prestamosSocios->setValorFinanciacion($valorInicial);
					$prestamosSocios->setFechaPrestamo($fechaInicioAmorizacion);
					$prestamosSocios->setFechaInicio($fechaInicioAmorizacion);
					$prestamosSocios->setInteresCorriente(0);
					$prestamosSocios->setNumeroCuotas($totalCuotas);
					$prestamosSocios->setCuenta($cuentaPrestamo);
					$prestamosSocios->setCuentaCruce($cuentaPrestamoC);
					$prestamosSocios->setEstado('D');//Debe

					if ($prestamosSocios->save() == false) {
						foreach ($prestamosSocios->getMessages() as $message) 
						{
							throw new Exception('addPrestamo: '.$message->getMessage());	
						}
					}

					$sociosFactura = new SociosFactura();
					
					#check terceros
					$sociosFactura->checkTercero($socios);

					//Generando amortizacion
					$configAmortizacion = array(
						'prestamosSociosId'	=> $prestamosSocios->getId(),
						'valorFinanciacion'	=> $valorInicial,
						'fechaCompra'		=> $fechaInicioAmorizacion,
						'plazoMeses'		=> $totalCuotas,
						'tasaMesVencido' 	=> 0
					);
					$sociosFactura->crearAmortizacion($configAmortizacion);
					
					//Las cuotas extraordinarias son patrimonio y no se registran para contabilizar
					//NOOOOOO/////$sociosFactura->makePrestamosAura($transaction, $prestamosSocios);
					//////////////////////////////////////////////////////////////NO

					$movements = array();

					//////////////AJUSTE DE SALDO//////////////////	
					$comprobFinanciacion = Settings::get('comprob_ajustes', 'SO');
					if (!$comprobFinanciacion) {
						throw new Exception('El comprobante de ajustes no se ha definido en configuración');
					}
					$aura = new Aura($comprobFinanciacion, 0);
					
					$aura->addMovement(array(
						'Descripcion'	=> 'PRESTAMOS SOCIOS No.'.$prestamosSocios->getId(),
						'Nit'			=> $socios->getIdentificacion(),
						'CentroCosto' 	=> '',
						'Cuenta' 		=> $prestamosSocios->getCuenta(),
						'Valor' 		=> $totalPagado,
						'BaseGrab' 		=> 0,
						'TipoDocumento' => $comprobFinanciacion,
						'NumeroDocumento' => $prestamosSocios->getId(),
						'FechaVence' 	=> '',
						'DebCre' 		=> 'D',
						'debug' 		=> true
					));
					
					$aura->addMovement(array(
						'Descripcion'	=> 'PRESTAMOS SOCIOS No.'.$prestamosSocios->getId(),
						'Nit'			=> $socios->getIdentificacion(),
						'CentroCosto' 	=> '',
						'Cuenta' 		=> $prestamosSocios->getCuentaCruce(),
						'Valor' 		=> $totalPagado,
						'BaseGrab' 		=> 0,
						'TipoDocumento' => $comprobFinanciacion,
						'NumeroDocumento' => $prestamosSocios->getId(),
						'FechaVence' 	=> '',
						'DebCre' 		=> 'C',
						'debug' 		=> true
					));
					
					$aura->save();

					unset($line,$prestamosSocios,$sociosFactura,$configAmortizacion,$aura);
				}
				unset($arr_data);
				
				$transaction->commit();
			}
			catch(Exception $e){
				throw new Exception("SociosMigracion: ".print_r($e,true));
			}
		}

	}
	
	private function _dateFormatUnl($date) 
	{
		$dateSplit = explode('/',$date);
		return "{$dateSplit[2]}-{$dateSplit[1]}-{$dateSplit[0]}";
	}
	
	/**
	 * Importa los socios desde un archivo plano en unl con los campos:
	 * 
	 * documento identidad socioa.derecho, nombre del socio apellidos, dierecion telefono,  ocupacion , fecha_nacicimiento socio,   fecha de ingreso socio, email, clase socio, activo ,documento id beneficioario ,nombres beneficiario, apellidos benficiario, codigo parentesco, estadocivil, fecha de nacimiento beneificario,fecha ingreso beneficiario,sexo beneficiario 	 
	 * 
	 */
	public function importSociosByFile()
	{
		$filePath = "/tmp/socios_bene.unl";
		if (file_exists($filePath)) {
			
			try 
			{
				$allArray = array();
				$dataArray = file($filePath);
				
				foreach ($dataArray as $line) {
					
					$data = explode("|", $line);
					
					#Socio principal
					$identificacion = $data[0];
					$numeroAccion	= $data[1];
					
					if (!isset($allArray[$identificacion])) {
						
						$nombres		= $data[2];
						$apellidos		= $data[3];
						$direccion		= $data[4];
						$telefono		= $data[5];
						$ocupacion 		= $data[6];
						$fechaNacimiento = $this->_dateFormatUnl($data[7]);
						$fechaIngreso 	= $this->_dateFormatUnl($data[8]);
						$email 			= $data[9];
						$claseSocio 	= $data[10];
						$activo 		= $data[11];
						
						if (!$telefono) {
							$telefono = 111;
						}
						
						if (!$email) {
							$email = 111;
						}
						
						
						$sexoBeneficiario 		= $data[19];
						
						
						/*$allArray[$identificacion] = array(
							'numeroAccionManual' => 'S',
							'titularId' => null,
							'identificacion' => $identificacion,
							'tipoDocumentosId' => 1,
							'numeroAccion'	=> $numeroAccion.'-00',
							'nombres'		=> $nombres,
							'apellidos'		=> $apellidos,
							'ciudadCasa' => 127591,
							'ciudadExpedido' => 127591,
							'ciudadNacimiento' => 127591,
							'direccionCasa'		=> $direccion,
							'telefonoCasa' 		=> $telefono,
							'direccionTrabajo' => '',
							'direccionCorrespondencia' => '',
							'telefonoTrabajo' => '',
							'celular' => '111',
							'fax' => 111,
							'tipoSociosId' => 1,
							'ocupacion'		=> $ocupacion,
							'fechaNacimiento' => $fechaNacimiento,
							'fechaIngreso'	=> $fechaIngreso,
							'correo1'			=> $email,
							'correo2'			=> $email,
							'claseSocio'	=> $claseSocio,
							'activo'		=> $activo,
							'sexo'			=> $sexoBeneficiario,
							'estudiosInstitucion' => array(),
							'enviaCorreo'	=> 'S',
							'cobra'			=> 'S',
							'estadosSociosId' => 1,
							'parentescosId' => 1,	
							'imprime' => 'S',
							'numeroTarjeta' => '',
							'estado'		=> 'A'
						);*/
						
					}
					
					#Beneficiarios
					$documentoIdBeneficiario = $data[12]; 
					if (!isset($allArray[$documentoIdBeneficiario])) {
						
						$nombresBeneficiario 	= $data[13];
						$apellidosBenficiario 	= $data[14];
						$codigoParentesco 		= $data[15];
						$estadoCivil 			= $data[16];
						$fechaNacimientoBeneficario = $this->_dateFormatUnl($data[17]);
						$fechaIngresoBeneficiario = $this->_dateFormatUnl($data[18]);
						
						$allArray[$documentoIdBeneficiario] = array(
							'numeroAccionManual' => 'S',
							'identificacion' => $documentoIdBeneficiario,
							'tipoDocumentosId' => 1,
							'socioTitular'	=> $identificacion,
							'numeroAccion'	=> $numeroAccion.'-'.$codigoParentesco,
							'nombres'		=> $nombres,
							'apellidos'		=> $apellidos,
							'ciudadCasa' => 127591,
							'ciudadExpedido' => 127591,
							'ciudadNacimiento' => 127591,
							'fax' => 111,
							'celular' => '111',
							'direccionCasa'		=> $direccion,
							'direccionTrabajo' => '',
							'telefonoTrabajo' => '',
							'direccionCorrespondencia' => '',
							'tipoSociosId' => 2,
							'telefonoCasa' 		=> $telefono,
							'ocupacion'		=> $ocupacion,
							'fechaNacimiento' => $fechaNacimiento,
							'fechaIngreso'	=> $fechaIngreso,
							'correo1'			=> $email,
							'correo2'			=> $email,
							'claseSocio'	=> $claseSocio,
							'codigoParentesco' => $codigoParentesco,
							'activo'		=> $activo,
							'sexo'			=> $sexoBeneficiario,
							'estudiosInstitucion' => array(),
							'enviaCorreo'	=> 'S',
							'cobra'			=> 'S',
							'estadosSociosId' => 1,	
							'parentescosId' => 2,
							'imprime' => 'S',
							'numeroTarjeta' => '',
							'estado'		=> 'A'
						);
					
					}
					unset($line, $data);
				}
				
				$transaction = TransactionManager::getUserTransaction();
				
				SociosCore::limpiarBD($transaction);

				foreach ($allArray as $data) 
				{
					if (isset($data['socioTitular'])) {
						$socios = $this->Socios->setTransaction($transaction)->findFirst(array('conditions'=>"identificacion='{$data['socioTitular']}'"));
						if ($socios) {
							$data['titularId'] = $socios->getSociosId();
							
						} else {
							$data['titularId'] = null;
						}
						SociosCore::crearSocio($data, $transaction);
					}
				}
				
				#print_r($allArray);
				$transaction->commit();
			}
			catch(Exception $e)
			{
				print_r($e);
				$transaction->rollback();
			}
		}
	}
	//Creamos structura para crear socio
				/*$tmpConfig = array(
					//Numero Acción Consecutivo/Manual
					'numeroAccionManual'		=> 'S',//Manual
					//Socios
					'titularId'					=> null,//Socio titular NULL es el titular
					'numeroAccion'				=> $socioPaya->getNumeroAccion(),
					'fechaIngreso'				=> $socioPaya->getFechaIngreso(),
					'parentescosId'				=> $socioPaya->getParentescosId(),
					'tipoDocumentosId'			=> $socioPaya->getTipoDocumentosId(),
					'identificacion'			=> $socioPaya->getIdentificacion(),
					'apellidos'					=> $socioPaya->getApellidos(),
					'nombres'					=> $socioPaya->getNombres(),
					'ciudadExpedido'			=> $ciudadExpedido,
					'ciudadNacimiento'			=> $ciudadNacimiento,
					'fechaNacimiento'			=> $socioPaya->getFechaNacimiento(),
					'sexo'						=> $socioPaya->getSexo(),
					'direccionCasa'				=> $socioPaya->getDireccionCasa(),
					'ciudadCasa'				=> $ciudadCasa,
					'telefonoCasa'				=> $socioPaya->getTelefonoCasa(),
					'direccionTrabajo'			=> $socioPaya->getDireccionTrabajo(),
					'telefonoTrabajo'			=> $socioPaya->getTelefonoTrabajo(),
					'celular'					=> $socioPaya->getCelular(),
					'direccionCorrespondencia'	=> 'C',//Casa
					'correo1'					=> $socioPaya->getCorreo1(),
					'correo2'					=> $socioPaya->getCorreo2(),
					'fax'						=> $socioPaya->getFax(),
					'tipoSociosId'				=> $socioPaya->getTipoSociosId(),
					'enviaCorreo'				=> 'S',
					'cobra'						=> 'S',
					'estadosSociosId'			=> $estadosSociosId,
					'estadosCivilesId'			=> $socioPaya->getEstadosCivilesId(),
					'imagenSocio'				=> $socioPaya->getImagenSocio(),
					//Estudios
					'estudiosInstitucion'		=> $estudiosInstitucion,
					'estudiosCiudadId'			=> $estudiosCiudad,
					'estudiosFechaGrado'		=> $estudiosFechaGrado,
					'estudiosTitulo'			=> $estudiosTitulo,
					//Experiencia Laboral
					'expLaboralesEmpresa'		=> $expLaboralesEmpresa,
					'expLaboralesDireccion'		=> $expLaboralesDireccion,
					'expLaboralesCargo'			=> $expLaboralesCargo,
					'expLaboralesTelefono'		=> $expLaboralesTelefono,
					'expLaboralesFax'			=> $expLaboralesFax,
					'expLaboralesFecha'			=> $expLaboralesFecha,
					//Actividades
					'actividades'				=> $actividades,
					//Clubes
					'clubId'					=> array_keys($asoclubes),
					'clubDesde'					=> $asoclubes,
					//Cargos Periodicos
					'cargosFijosId'				=> $cargosFijos, //A todos aportes a fundación
					//Otros Socios
					'otrosSociosId'				=> array(),
					'tipoAsociacionSocioId'		=> array(),
					//Tarjeta
					'numeroTarjeta'				=> $socioPaya->getNumeroTarjeta(),
					'formasPagosId'				=> $socioPaya->getFormasPagoId(),
					'fechaExp'					=> '1900-01-01',
					'fechaVen'					=> '1900-01-01',
					'bancosId'					=> '1',
					'digitoVerificacion'		=> substr($socioPaya->getNumeroTarjeta(), strlen($socioPaya->getNumeroTarjeta())-4),
					'estado'					=> 'A'					
				);*/
				
				
		///////////
		// AJUSTES
		///////////
		
	public function ajustarSaldos() 
	{

		$transaction = TransactionManager::getUserTransaction();
		
		$date = date('Y-m-d');
		$cuentasCartera = array('130505050', '130505055' ,'130505060');
			
		$lines = file("/tmp/ajustes.csv");
		
		foreach ($lines as $line) 
		{
			echo "<br>",$line;
			
			$lineSplit = explode(';',$line);
			$numeroAccion = $lineSplit[0];
			$saldoNuevo = $lineSplit[1];
			
			$movements = array();
			
			#Buscamos socio
			$socios = EntityManager::get('Socios')->setTransaction($transaction)->findFirst("numero_accion='$numeroAccion' AND estados_socios_id=1");
			if ($socios == false) {
				echo "<br>socios con accion $numeroAccion no existe";
				continue;
				#throw new Exception("El número de acción no existe ($numeroAccion)", 1);
			}
			
			$identificacion = $socios->getIdentificacion();
			$saldoTotal = array('D'=>0, 'C'=>0);
				
			foreach ($cuentasCartera as $cuenta) 
			{
				
				#CARTERA
				$saldo = 0;
				$carteraObj = EntityManager::get('Cartera')->setTransaction($transaction)->find("nit='$identificacion' and cuenta='$cuenta' and saldo<>0");
				foreach ($carteraObj as $cartera) 
				{
					$saldo += $cartera->getSaldo();
					
				}
				echo "<br>saldo: ",$saldo;
				
				if ($saldo<0) {
					$debCre = 'D';
					$saldoTotal['D'] += abs($saldo);
				} else {
					$debCre = 'C';
					$saldoTotal['C'] += abs($saldo);
				}
				
				$saldo2 = abs($saldo);
				
				$movements[] = array(
					'Descripcion'	=> 'AJUSTE SALDO '.$date,
					'Nit'			=> $identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> $cuenta,
					'Valor'			=> $saldo2,
					'BaseGrab'		=> 0,
					'TipoDocumento'	=> 'CXS',
					'NumeroDocumento' => 1,
					'FechaVence'	=> $date,
					'DebCre'		=> $debCre,
					'debug'			=> true
				);
				
				
				
				unset($carteraObj);
			}

			#AJUSTE NUEVO
			if ($saldoNuevo!=0) {
				
				if ($saldoNuevo<0) {
					$debCre = 'C';
					$saldoTotal['C'] += abs($saldoNuevo);
				} else {
					$debCre = 'D';
					$saldoTotal['D'] += abs($saldoNuevo);
				}
				
				$saldoNuevo2 = abs($saldoNuevo);
				
				$movements[] = array(
					'Descripcion'	=> 'AJUSTE SALDO '.$date,
					'Nit'			=> $identificacion,
					'CentroCosto'	=> 130,
					'Cuenta'		=> 130505050,//SOSTENIMIENTO
					'Valor'			=> $saldoNuevo2,
					'BaseGrab'		=> 0,
					'TipoDocumento'	=> 'CXS',
					'NumeroDocumento' => 1,
					'FechaVence'	=> $date,
					'DebCre'		=> $debCre,
					'debug'			=> true
				);
			
				
				
			}
			echo "<br>saldoTotal2";	print_r($saldoTotal);

			#CRUCE
			$diffSaldo = (abs($saldoTotal['D']) - abs($saldoTotal['C']));
			echo "<br>diff: $diffSaldo = ({$saldoTotal['D']} - {$saldoTotal['C']})";
			
			$debCre = 'C';
			if (abs($saldoTotal['D'])<abs($saldoTotal['C'])) {
				$debCre = 'D';
			}
			
			$diffSaldo2 = abs($diffSaldo);
			
			$movements[] = array(
				'Descripcion'	=> 'AJUSTE SALDO '.$date,
				'Nit'			=> $identificacion,
				'CentroCosto'	=> 130,
				'Cuenta'		=> 110505007,//CAJA
				'Valor'			=> $diffSaldo2,
				'BaseGrab'		=> 0,
				'TipoDocumento'	=> 'CXS',
				'NumeroDocumento' => 1,
				'FechaVence'	=> $date,
				'DebCre'		=> $debCre,
				'debug'			=> true
			);
		
			#print_r($movements);
			$fecha = '2012-10-30';
			$aura = new Aura('AJU',0);
			foreach ($movements as $movement) 
			{
				print_r($movement);
				$aura->addMovement($movement);
			}
			$auraConsecutivo = $aura->save();
			
			echo "<br><h2>OK</h2>";
			
			unset($saldoTotal, $identificacion, $socios, $movements, $aura, $auraConsecutivo, $debCre, $diffSaldo);
			
			
		}

		$transaction->commit();
	}
	
}
