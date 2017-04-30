<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * Aura
 *
 * Realiza las contabilizaciones y afecta los saldos
 */
class Aura extends UserComponent
{

	/**
	 * Acción de Crear un comprobante
	 *
	 */
	const OP_CREATE = 1;

	/**
	 * Acción de Modificar un comprobante
	 *
	 */
	const OP_UPDATE = 2;

	/**
	 * Acción de Eliminar un comprobante
	 *
	 */
	const OP_DELETE = 3;

	/**
	 * Campos obligatorios de cada movimiento
	 *
	 * @var array
	 */
	static private $_requiredFields = array(
		'Cuenta', 'Valor', 'DebCre'
	);

	/**
	 * Campos obligatorios que pueden estar predefinidos
	 *
	 * @var array
	 */
	static private $_replaceableFields = array(
		'Comprobante', 'Numero', 'Fecha',
	);

	/**
	 * Todas las Comprobantes
	 *
	 * @var array
	 */
	static private $_comprob = array();

	/**
	 * Todas las Cuentas
	 *
	 * @var array
	 */
	static private $_cuentas = array();

	/**
	 * Todos los centros
	 *
	 * @var array
	 */
	static private $_centros = array();

	/**
	 * Todos los tipos de documentos
	 *
	 * @var array
	 */
	static private $_documentos = array();

	/**
	 * Todos los terceros
	 *
	 * @var array
	 */
	static private $_terceros = array();

	/**
	 * Número de movimientos almacenados. Cada 50 en cualquier comprobante se llama el GC
	 *
	 * @var int
	 */
	static private $_numberStored = 0;

	/**
	 * Número de movimientos del comprobante activo
	 *
	 * @var int
	 */
	private $_activeNumberStored = 0;

	/**
	 * Comprobante de la transacción
	 *
	 * @var array
	 */
	private $_comprobs = array();

	/**
	 * Últimos consecutivos utilizados por los comprobantes
	 *
	 * @var array
	 */
	private $_consecutivos = array();

	/**
	 * Total Debitos de la transacción
	 *
	 * @var double
	 */
	private $_totalDebitos = 0;

	/**
	 * Total Crébitos de la transacción
	 *
	 * @var double
	 */
	private $_totalCreditos = 0;

	/**
	 * Comprobante por defecto
	 *
	 * @var string
	 */
	private $_defaultComprob;

	/**
	 * Número de comprobante por defecto
	 *
	 * @var string
	 */
	private $_defaultNumero = 0;

	/**
	 * Fecha predeterminada
	 *
	 * @var string
	 */
	private $_defaultFecha;

	/**
	 * Periodo en el que se va a grabar el comprobante
	 *
	 * @var int
	 */
	private $_period = 0;

	/**
	 * Linea del comprobante que se está grabando
	 *
	 * @var int
	 */
	private $_linea = 1;

	/**
	 * Datos de la tabla Empresa
	 *
	 * @var Empresa
	 */
	private static $_empresa = null;

	/**
	 * Fecha Limite en el futuro de comprobantes
	 *
	 * @var Date
	 */
	private static $_fechaLimite = null;

	/**
	 * Indica si Aura está en modo debug
	 *
	 * @var boolean
	 */
	private $_debug = false;

	/**
	 * Permite saber si el comprobante ya existe al realizar alguna operación
	 *
	 * @var boolean
	 */
	private $_exists = true;

	/**
	 * Permite saber si se ha borrado el movimiento previo de un comprobante
	 *
	 * @var boolean
	 */
	private $_cleaned = false;

	/**
	 * Indica si el movimiento fue cargado para adicionar los demás movimientos
	 *
	 * @var boolean
	 */
	private $_loaded = false;

	/**
	 * Transacción para grabar los movimientos
	 *
	 * @var ActiveRecordTransaction
	 */
	private $_transaction;

	/**
	 * Indica si la transacción interna ya se habia creado externamente
	 *
	 * @var boolean
	 */
	private $_externalTransaction = false;

	/**
	 * Indica si la fecha supera el limite de presentación de retención
	 *
	 * @var boolean
	 */
	private $_superaLimiteRetencion = false;

	/**
	 * Indica si la fecha supera el limite de presentación de IVA
	 *
	 * @var boolean
	 */
	private $_superaLimiteIVA = false;

	/**
	 * Indica la acción que se va a realizar
	 *
	 * @var string
	 */
	private $_activeAction = null;

	/**
	 * Indica el movimiento actual
	 *
	 * @var array
	 */
	private $_activeMovement = array();

	/**
	 * Todos los movimientos guardados (solo en modo debug)
	 *
	 * @var array
	 */
	private $_movements = array();

	/**
	 * Constructor de Aura
	 *
	 * @param string $codigoComprobante
	 * @param int $numero
	 * @param string $fecha
	 * @param int $activeAction
	 */
	public function __construct($codigoComprobante='', $numero=0, $fecha='', $activeAction=null){
		if(self::$_empresa===null){
			self::$_empresa = $this->Empresa->findFirst();
		}
		$this->_externalTransaction = TransactionManager::hasUserTransaction();
		$this->_transaction = TransactionManager::getUserTransaction();
		$this->Movi->setTransaction($this->_transaction);
		$this->Movitemp->setTransaction($this->_transaction);
		$this->Nits->setTransaction($this->_transaction);
		$this->Saldosc->setTransaction($this->_transaction);
		$this->Saldosn->setTransaction($this->_transaction);
		$this->Saldosp->setTransaction($this->_transaction);
		$this->Cartera->setTransaction($this->_transaction);
		$this->_activeAction = $activeAction;
		if($codigoComprobante!=''){
			$numero = $this->_addComprobante($codigoComprobante, $numero);
			$this->_defaultComprob = $codigoComprobante;
			$this->_defaultFecha = $fecha;
			$this->_defaultNumero = $numero;
			if($fecha!=''){
				$this->_validateFecha($fecha);
			}
		}
		$this->_cleaned = false;
		$this->_activeNumberStored = 0;
	}

	/**
	 * Establece la aplicación en modo debug
	 *
	 * @param boolean $debug
	 */
	public function setDebug($debug)
	{
		$this->_debug = $debug;
	}

	/**
	 * Valida si una posible fecha de comprobante es válida
	 *
	 * @param string $fecha
	 */
	public static function validateFecha($fecha)
	{
		if (self::$_empresa === null) {
			self::$_empresa = self::getModel("Empresa")->findFirst();
		}

		if (self::$_fechaLimite === null) {
			$diasLimite = Settings::get('d_movi_limite','CO');
			if ($diasLimite === null) {
				$diasLimite = 5;
			}
			self::$_fechaLimite = new Date();
			self::$_fechaLimite->addDays($diasLimite);
		}

		if (Date::isLater($fecha, self::$_fechaLimite)) {
			throw new AuraException('La fecha del comprobante es inválida ' . $fecha . ', debe ser menor a la fecha límite ' . self::$_fechaLimite);
		} else {
			if ($fecha <= self::$_empresa->getFCierrec() || Date::isEarlier($fecha, self::$_empresa->getFCierrec())) {
				throw new AuraException('La fecha del comprobante debe ser mayor al último cierre contable');
			}
		}
	}

	/**
	 * Valida si una posible fecha de comprobante es válida
	 *
	 * @param string $fecha
	 */
	private function _validateFecha($fecha)
	{
		self::validateFecha($fecha);
		$empresa1 = $this->Empresa1->findFirst();
		//$fLimiteR = Date::fromFormat(substr($empresa1->getOtros(), 4, 10), 'MM/DD/YYYY');
		//if(Date::isEarlier($fecha, $fLimiteR)){
		//$this->_superaLimiteRetencion = true;
		//}
		//$fLimiteI = Date::fromFormat(substr($empresa1->getOtros(), 14, 10), 'MM/DD/YYYY');
		//if(Date::isEarlier($fecha, $fLimiteI)){
		//$this->_superaLimiteIVA = true;
		//}
	}

	/**
	 * Consulta un comprobante y genera el consecutivo si es necesario
	 *
	 * @param string $codigoComprobante
	 * @param int $numero
	 */
	private function _addComprobante($codigoComprobante, $numero=0)
	{
		if (!isset($this->_comprobs[$codigoComprobante])) {
			$comprob = $this->_getComprob($codigoComprobante);
			if ($comprob == false) {
				throw new AuraException("No existe el comprobante '$codigoComprobante'");
			} else {
				$this->_comprobs[$codigoComprobante] = $comprob;
				if ($numero > 0) {
					$this->_exists = $this->Movi->count("comprob='{$codigoComprobante}' AND numero='{$numero}'");
				} else {
					$this->_exists = false;
					if ($numero <= 0) {
						$numero = $this->Movi->maximum(array('numero', 'conditions' => "comprob='$codigoComprobante'"))+1;
					}
				}

				$identity = IdentityManager::getActive();
				if ($identity['id'] <= 0) {
					throw new AuraException('El perfil público no está habilitado para realizar operaciones sobre comprobantes, inicie sesión con un usuario que tenga privilegios');
				}

				if($this->_activeAction==null){
					if($this->_exists==false){
						if(!self::checkPermission($identity['id'], $codigoComprobante, 'A')){
							throw new AuraException('No tiene permiso para adicionar comprobantes de "'.$comprob->getNomComprob().'"');
						}
					} else {
						if(!self::checkPermission($identity['id'], $codigoComprobante, 'M')){
							throw new AuraException('No tiene permiso para actualizar comprobantes de "'.$comprob->getNomComprob().'"');
						}
					}
				} else {
					switch($this->_activeAction){
						case self::OP_CREATE:
							if(!self::checkPermission($identity['id'], $codigoComprobante, 'A')){
								throw new AuraException('No tiene permiso para adicionar comprobantes de "'.$comprob->getNomComprob().'"');
							}
							break;
						case self::OP_UPDATE:
							if(!self::checkPermission($identity['id'], $codigoComprobante, 'M')){
								throw new AuraException('No tiene permiso para actualizar comprobantes de "'.$comprob->getNomComprob().'"');
							}
							break;
						case self::OP_DELETE:
							if(!self::checkPermission($identity['id'], $codigoComprobante, 'D')){
								throw new AuraException('No tiene permiso para eliminar comprobantes de "'.$comprob->getNomComprob().'"');
							}
							break;
						default:
							throw new AuraException('Acción inválida en realizar: '.$this->_activeAction);
					}
				}

				//Autoincremente recursivo de consecutivo de comprobante
				$consecutivoNuevo = ($numero+1);
				while($this->Movi->count(array('conditions'=>"comprob='{$codigoComprobante}' AND numero='{$consecutivoNuevo}'"))>0){
					$consecutivoNuevo += 1;
				}

				$this->_comprobs[$codigoComprobante]->setConsecutivo($consecutivoNuevo);
				$this->_consecutivos[$codigoComprobante] = $numero;
			}
		} else {
			if($numero>0){
				$this->_consecutivos[$codigoComprobante] = $numero;
			}
		}
		return $numero;
	}

	/**
	 * Establece la linea activa en el validador
	 *
	 * @param number $line
	 */
	public function setActiveLine($line){
		$this->_linea = $line;
	}

	/**
	 * Establece el periodo en el que se va a grabar el comprobante
	 *
	 * @param int $period
	 */
	public function setPeriod($period){
		$this->_period = $period;
	}

	/**
	 * Aplica filtros a los valores del movimiento
	 *
	 * @param array $movement
	 */
	public function sanizite($movement){
		if(isset($movement['Comprob'])){
			$movement['Comprob'] = $this->filter($movement['Comprob'], 'comprob');
		}
		if(isset($movement['Numero'])){
			$movement['Numero'] = $this->filter($movement['Numero'], 'int');
		}
		if(isset($movement['Fecha'])){
			$movement['Fecha'] = $this->filter($movement['Fecha'], 'date');
		}
		if(isset($movement['Cuenta'])){
			$movement['Cuenta'] = $this->filter($movement['Cuenta'], 'cuentas');
		}
		if(isset($movement['Nit'])){
			$movement['Nit'] = $this->filter($movement['Nit'], 'terceros');
		}
		if(isset($movement['Descripcion'])){
			$movement['Descripcion'] = $this->filter($movement['Descripcion'], 'striptags', 'extraspaces');
		}
		if(isset($movement['CentroCosto'])){
			$movement['CentroCosto'] = $this->filter($movement['CentroCosto'], 'int');
		}
		if(isset($movement['DebCre'])){
			$movement['DebCre'] = $this->filter($movement['DebCre'], 'onechar');
		}
		if(isset($movement['Valor'])){
			$movement['Valor'] = $this->filter($movement['Valor'], 'numeric');
		}
		if(isset($movement['TipoDocumento'])){
			$movement['TipoDocumento'] = $this->filter($movement['TipoDocumento'], 'documento');
		}
		if(isset($movement['NumeroDocumento'])){
			$movement['NumeroDocumento'] = $this->filter($movement['NumeroDocumento'], 'int');
		}
		if(isset($movement['BaseGrab'])){
			$movement['BaseGrab'] = $this->filter($movement['BaseGrab'], 'numeric');
		}
		if(isset($movement['FechaVence'])){
			$movement['FechaVence'] = $this->filter($movement['FechaVence'], 'date');
		}
		return $movement;
	}

	/**
	 * Agrega un movimiento
	 *
	 * @param array $movement
	 */
	public function addMovement($movement){
		if($this->_exists){
			if($this->_cleaned==false){
				$this->_delete();
				$this->_cleaned = true;
			}
		}
		$movement = $this->validate($movement);
		$this->_storeMovement($movement);
		++$this->_linea;
		++$this->_activeNumberStored;
		unset($movement);
	}

	/**
	 * Agrega un movimiento a comprobante manteniendo el movimiento previo
	 *
	 * @param array $movement
	 */
	public function appendMovement($movement){
		if($this->_loaded==false){
			if($this->_exists){
				$consecutivo = 0;
				$tokenId = IdentityManager::getTokenId();
				$conditions = "sid='$tokenId' AND comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero'";
				$this->Movitemp->setTransaction($this->_transaction);
				$this->Movitemp->deleteAll($conditions);
				$movis = $this->Movi->find("comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero'");

				$movitemps = array();
				foreach($movis as $movi){
					$movitemp = new Movitemp();
					$movitemp->setTransaction($this->_transaction);
					$movitemp->setSid($tokenId);
					$movitemp->setConsecutivo($consecutivo);
					foreach($movi->getAttributes() as $attribute){
						$movitemp->writeAttribute($attribute, $movi->readAttribute($attribute));
					}
					if($movitemp->save()==false){
						foreach($movitemp->getMessages() as $message){
							throw new AuraException($message->getMessage());
						}
					}
					$consecutivo++;
					unset($movi);

					$movitemps[]= $movitemp;
				}
				
				if($this->_cleaned==false){
					$this->_delete();
					$this->_cleaned = true;
				}

				foreach($movitemps as $movitemp){
					$this->addMovement(array(
						'Fecha'  	  => $movitemp->getFecha(),
						'Cuenta' 	  => $movitemp->getCuenta(),
						'Nit' 	 	  => $movitemp->getNit(),
						'CentroCosto' => $movitemp->getCentroCosto(),
						'Valor'  	  => $movitemp->getValor(),
						'DebCre' 	  => $movitemp->getDebCre(),
						'Descripcion' => $movitemp->getDescripcion(),
						'TipoDocumento' => $movitemp->getTipoDoc(),
						'NumeroDocumento' => $movitemp->getNumeroDoc(),
						'BaseGrab' 	  => $movitemp->getBaseGrab(),
						'Conciliado'  => $movitemp->getConciliado(),
						'FechaVence'  => $movitemp->getFVence(),
						'Numfol' 	  => $movitemp->getNumfol()
					));
				}
				$this->_loaded = true;
			}
		}
		$this->addMovement($movement);
	}

	/**
	 * Valida que un movimiento tenga todo lo requerido para grabarse
	 *
	 * @param	array $movement
	 * @return	array
	 */
	public function validate($movement){

		$this->_activeMovement = $movement;

		$this->_checkRequiredFields($movement);

		if(!isset($movement['Comprobante'])){
			$movement['Comprobante'] = $this->_defaultComprob;
		} else {
			$this->_addComprobante($movement['Comprobante']);
		}
		if(!isset($movement['Numero'])){
			if(isset($movement['Comprobante'])&&$movement['Comprobante']){
				$movement['Numero'] = $this->_consecutivos[$movement['Comprobante']];
			} else {
				throw new AuraException('No se ha definido el tipo de comprobante a grabar');
			}
		}
		if (!isset($movement['Fecha'])) {
			if ($this->_defaultFecha=='') {
				$this->_defaultFecha = Date::getCurrentDate();
				$this->_validateFecha($this->_defaultFecha);
			}
			$movement['Fecha'] = $this->_defaultFecha;
		} else {
			if ($this->_defaultFecha!='') {
				if ($movement['Fecha'] != $this->_defaultFecha) {
					throw new AuraException('Se definieron distintas fechas en movimientos diferentes del comprobante');
				}
			} else {
				$this->_defaultFecha = $movement['Fecha'];
				//if ($this->_debug != false) {
				$this->_validateFecha($this->_defaultFecha);
				//}
			}
		}
		if ($movement['Fecha'] == '') {
			throw new AuraException('No se indicó la fecha del comprobante');
		}

		if ($movement['DebCre'] === '1') {
			$movement['DebCre'] = 'C';
		} else {
			if ($movement['DebCre'] === '0') {
				$movement['DebCre'] = 'D';
			}
		}

		if ($movement['DebCre'] !== 'D' && $movement['DebCre'] !== 'C') {
			throw new AuraException('El campo naturaleza debe ser "C" ó "D" en la línea '.$this->_linea);
		}

		if (substr($movement['Cuenta'], 9, 3) === '000') {
			$movement['Cuenta'] = substr($movement['Cuenta'], 0, 9);
			if (substr($movement['Cuenta'], 6, 3) === '000') {
				$movement['Cuenta'] = substr($movement['Cuenta'], 0, 6);
				if (substr($movement['Cuenta'], 4, 2) === '00') {
					$movement['Cuenta'] = substr($movement['Cuenta'], 0, 4);
				}
			}
		}

		$cuenta = $this->_getCuenta($movement['Cuenta']);
		if ($cuenta == false) {
			throw new AuraException('No existe la cuenta "'.$movement['Cuenta'].'" ó no es auxiliar, en la línea '.$this->_linea);
		}

		if ($this->_superaLimiteRetencion) {
			if (substr($movement['Cuenta'], 0, 4)=='2365'||substr($movement['Cuenta'], 0, 4) == '2367') {
				throw new AuraException('Las cuentas de retención ya están cerradas ' . $this->_linea);
			}
		} else {
			if ($this->_superaLimiteIVA) {
				if (substr($movement['Cuenta'], 0, 1)=='4'||substr($movement['Cuenta'], 0, 4) == '2408') {
					throw new AuraException('Las cuentas de retención ya están cerradas ' . $this->_linea);
				}
			}
		}

		if($cuenta->getPideNit()=='S'){
			$nitErrado = false;
			if(!isset($movement['Nit'])){
				$nitErrado = true;
				$movement['Nit'] = '';
			} else {
				if($movement['Nit']===''||$movement['Nit']==='0'||is_null($movement['Nit'])){
					$nitErrado = true;
				} else {
					if(is_array($movement['Nit'])||is_object($movement['Nit'])){
						$nitErrado = true;
					} else {
						$nitErrado = !self::_existeTercero($movement['Nit']);
					}
				}
			}
			if($nitErrado==true){
				throw new AuraException('Tercero requerido, la cuenta "'.$movement['Cuenta'].'"('.$cuenta->getNombre().') solicita tercero, en la línea '.$this->_linea.' ('.$movement['Nit'].')');
			}
			unset($nitErrado);
		} else {
			$movement['Nit'] = '0';
		}

		if($cuenta->getPideFact()=='S'){
			$existeTipoDocumento = false;
			if(!isset($movement['TipoDocumento'])){
				$movement['TipoDocumento'] = '';
				$existeTipoDocumento = false;
			} else {
				$movement['TipoDocumento'] = $this->filter($movement['TipoDocumento'], 'documento');
				if($movement['TipoDocumento']===''){
					$existeTipoDocumento = false;
				} else {
					$existeTipoDocumento = self::_existeDocumento($movement['TipoDocumento']);
				}
			}
			if(!$existeTipoDocumento){
				throw new AuraException('El tipo de documento "'.$movement['TipoDocumento'].'" es inválido en la línea '.$this->_linea.', con la cuenta "'.$movement['Cuenta'].'", en el comprobante "'.$movement['Comprobante'].'-'.$movement['Numero'].'"');
			}
			unset($existeTipoDocumento);
			if(isset($movement['NumeroDocumento'])){
				if($movement['NumeroDocumento']<=0){
					throw new AuraException('El número de documento "'.$movement['NumeroDocumento'].'" para la cuenta "'.$movement['Cuenta'].'" es inválido en la línea '.$this->_linea);
				}
			} else {
				throw new AuraException('Documento requerido para la cuenta "'.$movement['Cuenta'].'" en la línea '.$this->_linea);
			}
		} else {
			$movement['TipoDocumento'] = '';
			$movement['NumeroDocumento'] = 0;
		}

		$tipoCuenta = $cuenta->getTipo();
		if($tipoCuenta<'4'||$tipoCuenta>'7'){
			$movement['CentroCosto'] = self::$_empresa->getCentroCosto();
		} else {
			if($cuenta->getPideCentro()=='S'){
				$centroCostoErrado = false;
				if(!isset($movement['CentroCosto'])){
					$movement['CentroCosto'] = '';
					$centroCostoErrado = true;
				} else {
					if($movement['CentroCosto']===''||$movement['CentroCosto']=='0'){
						$centroCostoErrado = true;
					} else {
						if($tipoCuenta>'3'&&$tipoCuenta<'8'&&$movement['CentroCosto']==self::$_empresa->getCentroCosto()){
							throw new AuraException('La cuenta "'.$movement['Cuenta'].'" no puede utilizar el centro de costo de balance en la línea '.$this->_linea.' ('.$movement['CentroCosto'].')');
						} else {
							$centroCostoErrado = !self::_existeCentro($movement['CentroCosto']);
						}
					}
				}
				if($centroCostoErrado){
					throw new AuraException('La cuenta "'.$movement['Cuenta'].'" solicita centro de costo en la línea '.$this->_linea.' ('.$movement['CentroCosto'].')');
				}
				unset($centroCostoErrado);
			} else {
				$movement['CentroCosto'] = 0;
			}
		}

		if($movement['Valor']<0){
			throw new AuraException('El valor "'.$movement['Valor'].'" es inválido  en la línea '.$this->_linea);
		} else {
			$movement['Valor'] = LocaleMath::round($movement['Valor'], 2);
			if(strlen($movement['Valor'])>=17){
				throw new AuraException('La base de datos no podrá almacenar el valor "'.$movement['Valor'].'" en la línea '.$this->_linea);
			}
		}

		if($cuenta->getPideBase()=='S'){
			if($cuenta->getPorcIva()>0){
				if(!isset($movement['BaseGrab'])){
					$movement['BaseGrab'] = $movement['Valor']*$cuenta->getPorcIva();
				} else {
					if($movement['BaseGrab']<$movement['Valor']){
						throw new AuraException('La base '.$movement['Base'].' no es válida en la línea '.$this->_linea);
					}
				}
			}
		}

		if($movement['DebCre']=='D'){
			$this->_totalDebitos+=$movement['Valor'];
		} else {
			$this->_totalCreditos+=$movement['Valor'];
		}
		unset($tipoCuenta);
		unset($cuenta);

		self::$_numberStored++;
		if(self::$_numberStored>50){
			self::$_numberStored = 0;
			GarbageCollector::collectCycles();
		}

		return $movement;

	}

	/**
	 * Almacena un movimiento en movi y en las tablas de saldos
	 *
	 * @param array $movement
	 */
	private function _storeMovement($movement)
	{

		$comprob = $this->Comprob->findFirst("codigo='{$this->_defaultComprob}'");

		if ($this->_debug == true) {
			$this->_movements[] = $movement;
		}

		try {
			$movi = new Movi();
			$movi->setTransaction($this->_transaction);
			$movi->setComprob($movement['Comprobante']);
			$movi->setNumero($movement['Numero']);
			$movi->setFecha((string)$movement['Fecha']);
			$movi->setCuenta($movement['Cuenta']);
			$movi->setNit($movement['Nit']);
			$movi->setCentroCosto($movement['CentroCosto']);
			$movi->setValor($movement['Valor']);
			$movi->setDebCre($movement['DebCre']);
			if(isset($movement['Folio'])){
				if($movement['Folio']!=0){
					if(isset($movement['Descripcion'])){
						$movement['Descripcion'].=' F'.$movement['Folio'];
					} else {
						$movement['Descripcion'] = ' F'.$movement['Folio'];
					}
					$movi->setNumfol($movement['Folio']);
				}
			}
			if(isset($movement['Descripcion'])){
				$movi->setDescripcion($movement['Descripcion']);
			}
			if(isset($movement['TipoDocumento'])){
				$movi->setTipoDoc($movement['TipoDocumento']);
				$movi->setNumeroDoc($movement['NumeroDocumento']);
			}
			if(isset($movement['BaseGrab'])){
				$movi->setBaseGrab($movement['BaseGrab']);
			}
			if(isset($movement['Conciliado'])){
				$movi->setConciliado($movement['Conciliado']);
			}
			if(isset($movement['FechaVence'])){
				$movi->setFVence((string)$movement['FechaVence']);
			}
			if(isset($movement['Numfol'])){
				$movi->setNumfol($movement['Numfol']);
			}

			if($movi->save()==false){
				if($this->_externalTransaction==true){
					foreach ($movi->getMessages() as $message) {
						$this->_transaction->rollback('Movi: ' . $message->getMessage().'. '.$movi->inspect().'. '.print_r($movement, true), $message->getCode());
					}
				} else {
					foreach ($movi->getMessages() as $message) {
						throw new AuraException('Movi: ' . $message->getMessage().'. '.$movi->inspect().'. '.print_r($movement, true), $message->getCode());
					}
				}
			}
			unset($movi);

			$saldosc = $this->Saldosc->findFirst("cuenta='{$movement['Cuenta']}' AND ano_mes='".$this->_period."'");
			if($saldosc==false){
				$saldosc = new Saldosc();
				$saldosc->setTransaction($this->_transaction);
				$saldosc->setCuenta($movement['Cuenta']);
				$saldosc->setAnoMes($this->_period);
				if($movement['DebCre']=='D'){
					$saldosc->setDebe($movement['Valor']);
					$saldosc->setHaber(0);
					$saldosc->setSaldo($movement['Valor']);
				} else {
					$saldosc->setDebe(0);
					$saldosc->setHaber($movement['Valor']);
					$saldosc->setSaldo(-$movement['Valor']);
				}
			} else {
				if($movement['DebCre']=='C'){
					$haber = $saldosc->getHaber() + $movement['Valor'];
					$saldosc->setHaber($haber);
					$saldosc->setSaldo($saldosc->getDebe() - $haber);
				} else {
					$debe = $saldosc->getDebe() + $movement['Valor'];
					$saldosc->setDebe($debe);
					$saldosc->setSaldo($debe - $saldosc->getHaber());
				}
			}

			if($saldosc->save()==false){
				if($this->_externalTransaction==true){
					foreach($saldosc->getMessages() as $message){
						$this->_transaction->rollback('Saldosc: '.$message->getMessage().'. '.$saldosc->inspect().'. '.print_r($movement, true), $message->getCode());
					}
				} else {
					foreach($saldosc->getMessages() as $message){
						throw new AuraException('Saldosc: '.$message->getMessage().'. '.$saldosc->inspect().'. '.print_r($movement, true), $message->getCode());
					}
				}
			}
			unset($saldosc);

			$cuenta = $this->_getCuenta($movement['Cuenta']);
			if($cuenta->getPideNit()=='S'){
				$saldosn = $this->Saldosn->findFirst("cuenta='{$movement['Cuenta']}' AND nit='{$movement['Nit']}' AND ano_mes=".$this->_period);
				if($saldosn==false){
					$saldosn = new Saldosn();
					$saldosn->setTransaction($this->_transaction);
					$saldosn->setCuenta($movement['Cuenta']);
					$saldosn->setNit($movement['Nit']);
					$saldosn->setAnoMes($this->_period);
					if($movement['DebCre']=='C'){
						$saldosn->setDebe(0);
						$saldosn->setHaber($movement['Valor']);
						$saldosn->setSaldo(-$movement['Valor']);
					} else {
						$saldosn->setDebe($movement['Valor']);
						$saldosn->setHaber(0);
						$saldosn->setSaldo($movement['Valor']);
					}
				} else {
					$saldosn->setTransaction($this->_transaction);
					if($movement['DebCre']=='C'){
						$haber = $saldosn->getHaber() + $movement['Valor'];
						$saldosn->setHaber($haber);
						$saldosn->setSaldo($saldosn->getDebe() - $haber);
						unset($haber);
					} else {
						$debe = $saldosn->getDebe() + $movement['Valor'];
						$saldosn->setDebe($debe);
						$saldosn->setSaldo($debe - $saldosn->getHaber());
						unset($debe);
					}
				}
				if($saldosn->save()==false){
					if($this->_externalTransaction==true){
						foreach($saldosn->getMessages() as $message){
							$this->_transaction->rollback('Saldosn: '.$message->getMessage().'. '.$saldosn->inspect().'. '.print_r($movement, true), $message->getCode());
						}
					} else {
						foreach($saldosn->getMessages() as $message){
							throw new AuraException('Saldosn: '.$message->getMessage().'. '.$saldosn->inspect().'. '.print_r($movement, true), $message->getCode());
						}
					}
				}
				unset($saldosn);

			}

			if($cuenta->getPideCentro()=='S'){
				$saldosp = $this->Saldosp->findFirst("cuenta='{$movement['Cuenta']}' AND centro_costo='{$movement['CentroCosto']}' AND ano_mes=".$this->_period);
				if($saldosp==false){
					$saldosp = new Saldosp();
					$saldosp->setTransaction($this->_transaction);
					$saldosp->setCuenta($movement['Cuenta']);
					$saldosp->setCentroCosto($movement['CentroCosto']);
					$saldosp->setAnoMes($this->_period);
					if($movement['DebCre']=='C'){
						$saldosp->setDebe(0);
						$saldosp->setHaber($movement['Valor']);
						$saldosp->setSaldo(-$movement['Valor']);
					} else {
						$saldosp->setDebe($movement['Valor']);
						$saldosp->setHaber(0);
						$saldosp->setSaldo($movement['Valor']);
					}
					$saldosp->setPres(0);
				} else {
					$saldosp->setTransaction($this->_transaction);
					if($movement['DebCre']=='C'){
						$haber = $saldosp->getHaber() + $movement['Valor'];
						$saldosp->setHaber($haber);
						$saldosp->setSaldo($saldosp->getDebe() - $haber);
					} else {
						$debe = $saldosp->getDebe() + $movement['Valor'];
						$saldosp->setDebe($debe);
						$saldosp->setSaldo($debe - $saldosp->getHaber());
					}
				}
				if($saldosp->save()==false){
					if($this->_externalTransaction==true){
						foreach($saldosp->getMessages() as $message){
							$this->_transaction->rollback('Saldosp: '.$message->getMessage().'. '.print_r($movement, true), $message->getCode());
						}
					} else {
						foreach($saldosp->getMessages() as $message){
							throw new AuraException('Saldosp: '.$message->getMessage().'. '.print_r($movement, true), $message->getCode());
						}
					}
				}
				unset($saldosp);
			}

			if($cuenta->getPideFact()=='S'){
				$tipo = substr($movement['Cuenta'], 0, 1);
				$conditions = "cuenta='{$movement['Cuenta']}' AND nit='{$movement['Nit']}' AND tipo_doc='{$movement['TipoDocumento']}' AND numero_doc='{$movement['NumeroDocumento']}'";
				$cartera = $this->Cartera->findFirst(array($conditions, 'for_update' => true));
				if($cartera==false){
					if($movement['TipoDocumento']!==''&&$movement['NumeroDocumento']!==''){
						$cartera = new Cartera();
						$cartera->setTransaction($this->_transaction);
						$cartera->setCuenta($movement['Cuenta']);
						$cartera->setNit($movement['Nit']);
						$cartera->setTipoDoc($movement['TipoDocumento']);
						$cartera->setNumeroDoc($movement['NumeroDocumento']);
						$cartera->setVendedor(0);
						$cartera->setCentroCosto($movement['CentroCosto']);
						$cartera->setFEmision((string)$movement['Fecha']);
						if($movement['DebCre']=='D'){
							if($tipo=='1'){
								$cartera->setValor($movement['Valor']);
							} else {
								$cartera->setValor(0);
							}
							$cartera->setSaldo($movement['Valor']);
						} else {
							if($tipo=='2'){
								$cartera->setValor($movement['Valor']);
							} else {
								$cartera->setValor(0);
							}
							$cartera->setSaldo(-$movement['Valor']);
						}
						if(isset($movement['FechaVence'])){
							$cartera->setFVence((string)$movement['FechaVence']);
						} else {
							$cartera->setFVence((string)$movement['Fecha']);
						}
					}
				} else {
					if($movement['DebCre']=='D'){
						if($tipo=='1'){
							$cartera->setValor($cartera->getValor()+$movement['Valor']);
							if(Date::isLater($cartera->getFEmision(), $movement['Fecha'])){
								$cartera->setFEmision((string)$movement['Fecha']);
							}
						}
						$cartera->setSaldo($cartera->getSaldo()+$movement['Valor']);
					} else {
						if($tipo=='2'){
							$cartera->setValor($cartera->getValor()+$movement['Valor']);
							if(Date::isLater($cartera->getFEmision(), $movement['Fecha'])){
								$cartera->setFEmision((string)$movement['Fecha']);
							}
						}
						$cartera->setSaldo($cartera->getSaldo()-$movement['Valor']);
					}
				}
				if($cartera->save()==false){
					if($this->_externalTransaction==true){
						foreach($cartera->getMessages() as $message){
							$this->_transaction->rollback('Cartera: '.$message->getMessage(), $message->getCode());
						}
					} else {
						foreach($cartera->getMessages() as $message){
							throw new AuraException('Cartera: '.$message->getMessage(), $message->getCode());
						}
					}
				}

				unset($cartera);
				unset($tipo);
			}
		}
		catch(DbLockAdquisitionException $e){
			$message = 'La base de datos está bloqueada mientras otro usuario termina un proceso de grabación. Intente grabar nuevamente en un momento';
			if($this->_externalTransaction==true){
				$this->_transaction->rollback($message);
			} else {
				throw new AuraException($message);
			}
		}
		unset($cuenta);
		unset($movement);

	}

	/**
	 * Elimina el comprobante dado en el constructor
	 *
	 * @return boolean
	 */
	public function delete(){
		$this->_activeAction = self::OP_DELETE;
		$this->_delete();
		if($this->_externalTransaction==true){
			return true;
		} else {
			return $this->_transaction->commit();
		}
	}

	/**
	 * Elimina un comprobante sin cerrar la transacción
	 *
	 * @return boolean
	 */
	private function _delete(){

		if($this->_activeAction==self::OP_DELETE){
			$comprob = $this->Comprob->findFirst("codigo='{$this->_defaultComprob}'");
			$identity = IdentityManager::getActive();
			if(!self::checkPermission($identity['id'], $this->_defaultComprob, 'D')){
				if($comprob==false){
					throw new AuraException('No existe el comprobante "'.$this->_defaultComprob.'"');
				} else {
					throw new AuraException('No tiene permiso para eliminar comprobantes de "'.$comprob->getNomComprob().'"');
				}
			}
		}

		if(!$this->_exists){
			throw new AuraException("No existe el comprobante a eliminar {$this->_defaultComprob}-{$this->_defaultNumero}");
		}

		if($this->_activeAction==self::OP_DELETE){
			$conditions = "comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero' AND estado='E'";
			$this->Cheque->setTransaction($this->_transaction);
			if($this->Cheque->count($conditions)>0){
				throw new AuraException('El comprobante está asociado a un cheque, debe anular el cheque primero');
			}
			unset($conditions);
		}

		$conditions = "comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero'";
		$movis = $this->Movi->find(array($conditions, 'columns' => 'comprob,numero,cuenta,valor,centro_costo,nit,tipo_doc,deb_cre,numero_doc'));
		foreach($movis as $movi){

			if($movi->getValor()!=0){

				$saldosc = $this->Saldosc->findFirst("cuenta='{$movi->getCuenta()}' AND ano_mes=0");
				if($saldosc!=false){
					if($movi->getDebCre()=='C'){
						$haber = $saldosc->getHaber() - $movi->getValor();
						$saldosc->setHaber($haber);
						$saldosc->setSaldo($saldosc->getDebe() - $haber);
					} else {
						$debe = $saldosc->getDebe() - $movi->getValor();
						$saldosc->setDebe($debe);
						$saldosc->setSaldo($debe - $saldosc->getHaber());
					}
					if($saldosc->save()==false){
						if($this->_externalTransaction==true){
							foreach($saldosc->getMessages() as $message){
								$this->_transaction->rollback('Saldosc: '.$message->getMessage(), $message->getCode());
							}
						} else {
							foreach($saldosc->getMessages() as $message){
								throw new AuraException('Saldosc: '.$message->getMessage(), $message->getCode());
							}
						}
					}
					unset($saldosc);
				}

				$cuenta = $this->_getCuenta($movi->getCuenta());
				if($cuenta==false){
					throw new AuraException('La cuenta "'.$movi->getCuenta().'" antes existía pero ya no existe. No se puede borrar el comprobante');
				}

				if($cuenta->getPideNit()=='S'){
					$saldosn = $this->Saldosn->findFirst("cuenta='{$movi->getCuenta()}' AND nit='{$movi->getNit()}' AND ano_mes=0");
					if($saldosn!=false){
						$saldosn->setTransaction($this->_transaction);
						if($movi->getDebCre()=='C'){
							$haber = $saldosn->getHaber() - $movi->getValor();
							$saldosn->setHaber($haber);
							$saldosn->setSaldo($saldosn->getDebe() - $haber);
						} else {
							$debe = $saldosn->getDebe() - $movi->getValor();
							$saldosn->setDebe($debe);
							$saldosn->setSaldo($debe - $saldosn->getHaber());
						}
						if($saldosn->save()==false){
							if($this->_externalTransaction==true){
								foreach($movi->getMessages() as $message){
									$this->_transaction->rollback('Saldosn: '.$message->getMessage().'. '.$saldosn->inspect().'.', $message->getCode());
								}
							} else {
								foreach($saldosn->getMessages() as $message){
									throw new AuraException('Saldosn: '.$message->getMessage().'. '.$saldosn->inspect().'.', $message->getCode());
								}
							}
						}
					}
					unset($saldosn);
				}

				if($cuenta->getPideCentro()=='S'){
					$saldosp = $this->Saldosp->findFirst("cuenta='{$movi->getCuenta()}' AND centro_costo='{$movi->getCentroCosto()}' AND ano_mes=0");
					if($saldosp!=false){
						$saldosp->setTransaction($this->_transaction);
						if($movi->getDebCre()=='C'){
							$haber = $saldosp->getHaber() - $movi->getValor();
							$saldosp->setHaber($haber);
							$saldosp->setSaldo($saldosp->getDebe() - $haber);
						} else {
							$debe = $saldosp->getDebe() - $movi->getValor();
							$saldosp->setDebe($debe);
							$saldosp->setSaldo($debe - $saldosp->getHaber());
						}
						if($saldosp->save()==false){
							if($this->_externalTransaction==true){
								foreach($saldosp->getMessages() as $message){
									$this->_transaction->rollback('Saldosp: '.$message->getMessage(), $message->getCode());
								}
							} else {
								foreach($saldosp->getMessages() as $message){
									throw new AuraException('Saldosp: '.$message->getMessage(), $message->getCode());
								}
							}
						}
					}
					unset($saldosp);
				}

				if($cuenta->getPideFact()=='S'){
					$tipo = substr($movi->getCuenta(), 0, 1);
					$conditions = "cuenta='{$movi->getCuenta()}' AND nit='{$movi->getNit()}' AND tipo_doc='{$movi->getTipoDoc()}' AND numero_doc='{$movi->getNumeroDoc()}'";
					$cartera = $this->Cartera->findFirst(array($conditions, 'for_update' => true));
					if($cartera!=false){
						if($movi->getDebCre()=='D'){
							if($tipo=='1'){
								$cartera->setValor($cartera->getValor()-$movi->getValor());
							}
							$cartera->setSaldo($cartera->getSaldo()-$movi->getValor());
						} else {
							if($tipo=='2'){
								$cartera->setValor($cartera->getValor()-$movi->getValor());
							}
							$cartera->setSaldo($cartera->getSaldo()+$movi->getValor());
						}
						if($cartera->save()==false){
							if($this->_externalTransaction==true){
								foreach($cartera->getMessages() as $message){
									$this->_transaction->rollback('Cartera: '.$message->getMessage(), $message->getCode());
								}
							} else {
								foreach($cartera->getMessages() as $message){
									throw new AuraException('Cartera: '.$message->getMessage(), $message->getCode());
								}
							}
						}
					}
					unset($cartera);
				}
			}
			unset($cuenta);
			unset($movi);
		}
		unset($movis);
		$this->Movi->deleteAll("comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero'");

		if($this->_activeAction==self::OP_DELETE){
			if($comprob->getConsecutivo()==($this->_defaultNumero+1)){
				$comprob->setTransaction($this->_transaction);
				$comprob->setConsecutivo($this->_defaultNumero);
				if($comprob->save()==false){
					if($this->_externalTransaction==true){
						foreach($comprob->getMessages() as $message){
							$this->_transaction->rollback('Tipo Comprobante: '.$message->getMessage(), $message->getCode());
						}
					} else {
						foreach($comprob->getMessages() as $message){
							throw new AuraException('Tipo Comprobante: '.$message->getMessage(), $message->getCode());
						}
					}
				}
			}
			$this->Movitemp->deleteAll("comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero'");

			if ($comprob){
				$comprob->setConsecutivo($this->_defaultNumero);
				$this->_createGrab($comprob, 'D', true);
			}

		}

	}

	/**
	 * Verifica que el registro a agregar contenga todos los campos obligatorios
	 *
	 * @param array $movement
	 */
	private function _checkRequiredFields($movement){
		foreach(self::$_requiredFields as $requiredField){
			if(!isset($movement[$requiredField])){
				throw new AuraException("No existe el campo requerido '$requiredField' en el movimiento");
			}
		}
	}

	/**
	 * Consulta un comprobante
	 *
	 * @param  string $comprobante
	 * @return boolean
	 */
	private function _existeComprobante($comprobante){
		$comprobante = $this->filter($comprobante, 'comprob');
		return (bool) $this->Comprob->count("codigo='$comprobante'");
	}

	/**
	 * Consulta una cuenta y cachea el resultado
	 *
	 * @param  string $codigoCuenta
	 * @return Cuentas
	 */
	private function _getCuenta($codigoCuenta){
		if(!isset(self::$_cuentas[$codigoCuenta])){
			self::$_cuentas[$codigoCuenta] = $this->Cuentas->findFirst("cuenta='{$codigoCuenta}' AND es_auxiliar='S'", 'columns: cuenta,nombre,tipo,pide_nit,pide_centro,pide_fact');
		}
		return self::$_cuentas[$codigoCuenta];
	}

	/**
	 * Consulta un comprobante y cachea el resultado
	 *
	 * @param  string $codigoComprob
	 * @return Comprob
	 */
	private function _getComprob($codigoComprob){
		if(!isset(self::$_comprob[$codigoComprob])){
			self::$_comprob[$codigoComprob] = $this->Comprob->findFirst("codigo='$codigoComprob'");
		}
		return self::$_comprob[$codigoComprob];
	}

	/**
	 * Consulta si un centro existe y cachea la respuesta
	 *
	 * @param  int $codigoCentro
	 * @return boolean
	 */
	private function _existeCentro($codigoCentro){
		if(!isset(self::$_centros[$codigoCentro])){
			self::$_centros[$codigoCentro] = $this->Centros->count("codigo='{$codigoCentro}'")>0;
		}
		return self::$_centros[$codigoCentro];
	}

	/**
	 * Consulta si un tipo de documento existe y cachea la respuesta
	 *
	 * @param  int $tipoDocumento
	 * @return boolean
	 */
	private function _existeDocumento($tipoDocumento){
		if(!isset(self::$_documentos[$tipoDocumento])){
			self::$_documentos[$tipoDocumento] = $this->Documentos->count("codigo='{$tipoDocumento}'")>0;
		}
		return self::$_documentos[$tipoDocumento];
	}

	/**
	 * Consulta si un tercero existe y cachea la respuesta
	 *
	 * @param  int $nit
	 * @return boolean
	 */
	private function _existeTercero($nit){
		if(!isset(self::$_terceros[$nit])){
			self::$_terceros[$nit] = $this->Nits->count("nit='{$nit}'")>0;
		}
		return self::$_terceros[$nit];
	}

	/**
	 * Verifica si el comprobante tiene sumas iguales de débitos y créditos
	 *
	 */
	private function _checkEquals(){
		$this->_totalCreditos = LocaleMath::round($this->_totalCreditos, 3);
		$this->_totalDebitos = LocaleMath::round($this->_totalDebitos, 3);
		if($this->_totalCreditos!=$this->_totalDebitos){
			throw new AuraException("El comprobante está descuadrado Créditos=".Currency::number($this->_totalCreditos)." y Débitos=".Currency::number($this->_totalDebitos));
		}
		if($this->_activeNumberStored<2){
			throw new AuraException('El comprobante debe tener al menos 2 movimientos');
		}
	}

	/**
	 * Almacena el comprobante
	 *
	 * @return boolean
	 */
	public function save(){
		$this->_checkEquals();
		if($this->_exists==false){
			Rcs::disable();
			foreach($this->_comprobs as $tipoComprob => $comprob){
				$comprob->setTransaction($this->_transaction);
				if($comprob->save()==false){
					if($this->_externalTransaction==true){
						foreach($comprob->getMessages() as $message){
							$this->_transaction->rollback('Tipo Comprobante: '.$message->getMessage(), $message->getCode());
						}
					} else {
						foreach($comprob->getMessages() as $message){
							throw new AuraException('Tipo Comprobante: '.$message->getMessage(), $message->getCode());
						}
					}
				}
				$this->_createGrab($comprob, 'A');
				unset($this->_comprobs[$tipoComprob]);
			}
		} else {
			foreach($this->_comprobs as $tipoComprob => $comprob){
				$comprob->setTransaction($this->_transaction);
				$this->_createGrab($comprob, 'M');
				unset($this->_comprobs[$tipoComprob]);
			}
		}
		Rcs::enable();
		if($this->_externalTransaction==false){
			$this->_transaction->commit();
		}
		return true;
	}

	/**
	 * Crea el registro en grab indicando la operación realizada sobre el comprobante
	 *
	 * @param Comprob $comprob
	 */
	private function _createGrab(Comprob $comprob, $accion, $consecutivoOff=false){
		$identity = IdentityManager::getActive();
		$grab = new Grab();
		$grab->setTransaction($this->_transaction);
		if (!$consecutivoOff) {
			$grab->setNumero($comprob->getConsecutivo()-1);
		} else {
			$grab->setNumero($comprob->getConsecutivo());
		}
		$grab->setComprob($comprob->getCodigo());
		$grab->setAccion($accion);
		$grab->setFechaGrab(Date::getCurrentDate());
		$grab->setHoraGrab(Date::getCurrentTime());
		$grab->setCodigoGrab($identity['login']);
		$grab->setUsuariosId($identity['id']);

		if($grab->save()==false){
			if($this->_externalTransaction==true){
				foreach($grab->getMessages() as $message){
					$this->_transaction->rollback('Grab: '.$message->getMessage(), $message->getCode());
				}
			} else {
				foreach($grab->getMessages() as $message){
					throw new AuraException('Grab: '.$message->getMessage(), $message->getCode());
				}
			}
		}
	}

	/**
	 * Obtiene el consecutivo actual de un comprobante
	 *
	 * @param int $codigoComprobante
	 */
	public function getConsecutivo($codigoComprobante=''){
		if($codigoComprobante==''){
			if(count($this->_consecutivos)==1){
				return current($this->_consecutivos);
			} else {
				throw new AuraException('Indique el comprobante donde se consultará el consecutivo');
			}
		}
		if(isset($this->_consecutivos[$codigoComprobante])){
			return $this->_consecutivos[$codigoComprobante];
		} else {
			return 0;
		}
	}

	/**
	 * Obtiene la fecha del comprobante
	 *
	 * @return Date
	 */
	public function getDefaultFecha(){
		return $this->_defaultFecha;
	}

	/**
	 * Obtiene el movimiento que se estaba guardando al generar una excepcion
	 *
	 * @return array
	 */
	public function getActiveMovement(){
		return $this->_activeMovement;
	}

	/**
	 * Obtiene todos los movimientos grabados (solo en modo debug)
	 *
	 */
	public function getMovements(){
		return $this->_movements;
	}

	/**
	 * Valida todo el movimiento de un comprobante previamente
	 * grabado revisando que todo esté bien
	 *
	 * @param	string $comprob
	 * @param	int $numero
	 * @return	array
	 */
	public static function validateComprob($comprob, $numero){
		$aura = new self($comprob, $numero, null, self::OP_CREATE);
		$line = 1;
		$messages = array();
		$movis = self::getModel('Movi')->find("comprob='$comprob' AND numero='$numero'", "columns: comprob,numero,fecha,cuenta,nit,centro_costo,valor,deb_cre,tipo_doc,numero_doc,base_grab");
		foreach($movis as $movi){
			try {
				$aura->setActiveLine($line);
				$aura->validate(array(
					'Fecha' => $movi->getFecha(),
					'Cuenta' => $movi->getCuenta(),
					'Nit' => $movi->getNit(),
					'CentroCosto' => $movi->getCentroCosto(),
					'Valor' => $movi->getValor(),
					'DebCre' => $movi->getDebCre(),
					'TipoDocumento' => $movi->getTipoDoc(),
					'NumeroDocumento' => $movi->getNumeroDoc()
				));
			}
			catch(AuraException $e){
				$messages[] = $e->getMessage();
			}
			$line++;
			unset($movi);
		}
		return $messages;
	}

	/**
	 * Regraba un comprobante afectando saldos de un periodo dado
	 *
	 * @param	string $comprob
	 * @param	int $numero
	 * @param	int $periodo
	 * @return	array
	 */
	public static function saveOnPeriod($comprob, $numero, $periodo=0){
		$messages = array();
		$aura = new self($comprob, $numero, null, self::OP_UPDATE);
		$aura->setPeriod($periodo);
		$movis = self::copyToTemp($comprob, $numero);
		foreach($movis as $movi){
			try {
				$aura->addMovement(array(
					'Fecha' => $movi->getFecha(),
					'Cuenta' => $movi->getCuenta(),
					'Nit' => $movi->getNit(),
					'CentroCosto' => $movi->getCentroCosto(),
					'Valor' => $movi->getValor(),
					'DebCre' => $movi->getDebCre(),
					'Descripcion' => $movi->getDescripcion(),
					'TipoDocumento' => $movi->getTipoDoc(),
					'NumeroDocumento' => $movi->getNumeroDoc(),
					'BaseGrab' => $movi->getBaseGrab(),
					'Conciliado' => $movi->getConciliado(),
					'FechaVence' => $movi->getFVence()
				));
			}
			catch(AuraException $e){
				$messages[] = $e->getMessage();
			}
			catch(DbException $e){
				$messages[] = $e->getMessage();
			}
			unset($movi);
		}
		try {
			if(count($messages)==0){
				$aura->save();
			}
		}
		catch(AuraException $e){
			$messages[] = $e->getMessage();
		}
		self::dropTemp($comprob, $numero);
		return $messages;
	}

	/**
	 * Copia un comprobante al temporal
	 *
	 * @param string $comprob
	 * @param int $numero
	 */
	public static function copyToTemp($comprob, $numero){
		$consecutivo = 0;
		$tokenId = IdentityManager::getTokenId();
		$hasTransaction = TransactionManager::hasUserTransaction();
		$transaction = TransactionManager::getUserTransaction();

		$MoviTemp = self::getModel('Movitemp');
		if($hasTransaction){
			$MoviTemp->setTransaction($transaction);
		}
		$MoviTemp->deleteAll("sid='$tokenId' AND comprob='$comprob' AND numero='$numero'");

		$Movis = self::getModel('Movi');
		if($hasTransaction){
			$Movis->setTransaction($transaction);
			$movis = $Movis->findForUpdate("comprob='$comprob' AND numero='$numero'");
		} else {
			$movis = $Movis->find("comprob='$comprob' AND numero='$numero'");
		}
		foreach($movis as $movi){
			$moviTemp = new Movitemp();
			$moviTemp->setTransaction($transaction);
			$moviTemp->setSid($tokenId);
			$moviTemp->setConsecutivo($consecutivo);
			foreach($movi->getAttributes() as $attribute){
				$moviTemp->writeAttribute($attribute, $movi->readAttribute($attribute));
			}
			if($moviTemp->save()==false){
				if($hasTransaction==true){
					foreach($moviTemp->getMessages() as $message){
						$transaction->rollback('Error al grabar comprobante en temporal. '.$message->getMessage().'. ('.$moviTemp->getComprob().'-'.$moviTemp->getNumero().')', $message->getCode());
					}
				} else {
					return false;
				}
			}
			$consecutivo++;
			unset($moviTemp);
			unset($movi);
		}
		return $MoviTemp->find("sid='$tokenId' AND comprob='$comprob' AND numero='$numero'");
	}

	/**
	 * Elimina un comprobante al temporal
	 *
	 * @param string $comprob
	 * @param int $numero
	 */
	public static function dropTemp($comprob, $numero){
		$tokenId = IdentityManager::getTokenId();
		$Movitemp = self::getModel('Movitemp');
		$hasTransaction = TransactionManager::hasUserTransaction();
		if($hasTransaction){
			$transaction = TransactionManager::getUserTransaction();
			$Movitemp->setTransaction($transaction);
		}
		return $Movitemp->deleteAll("sid='$tokenId' AND comprob='$comprob' AND numero='$numero'");
	}

	/**
	 * Comprueba si el usuario activo tiene permiso para realizar determinada acción sobre un comprobante
	 *
	 * @param	string $comprob
	 * @param	string $type
	 * @return	boolean
	 */
	public static function checkPermission($identityId, $comprob, $type){
		return self::getModel('PermisosComprob')->count("usuarios_id='$identityId' AND comprob='$comprob' AND popcion='$type'");
	}

}
