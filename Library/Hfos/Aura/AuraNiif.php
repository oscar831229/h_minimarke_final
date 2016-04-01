<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author      BH-TECK Inc. 2009-2015
 * @version     $Id$
 */
 error_reporting(E_ALL);

/**
 * Aura para Niif
 *
 * Realiza las contabilizaciones niif
 */
class AuraNiif extends UserComponent
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
	 * Constructor de AuraNiif
	 *
	 * @param string $codigoComprobante
	 * @param int $numero
	 * @param string $fecha
	 * @param int $activeAction
	 */
	public function __construct($codigoComprobante='', $numero=0, $fecha='', $activeAction=null)
	{

		if (self::$_empresa === null) {
			self::$_empresa = $this->Empresa->findFirst();
		}

		$this->_externalTransaction = TransactionManager::hasUserTransaction();
		$this->_transaction = TransactionManager::getUserTransaction();
		$this->MoviNiif->setTransaction($this->_transaction);
		$this->Movitempniif->setTransaction($this->_transaction);
		$this->Nits->setTransaction($this->_transaction);
		$this->SaldosNiif->setTransaction($this->_transaction);
		$this->_activeAction = $activeAction;
		if ($codigoComprobante!='') {
			$numero = $this->_addComprobante($codigoComprobante, $numero);
			$this->_defaultComprob = $codigoComprobante;
			$this->_defaultFecha = $fecha;
			$this->_defaultNumero = $numero;
			if ($fecha!='') {
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
			self::$_empresa = $this->Empresa->findFirst();
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
			throw new AuraNiifException('La fecha del comprobante es inválida ' . $fecha . ', debe ser menor a la fecha límite ' . self::$_fechaLimite);
		} else {
			if ($fecha <= self::$_empresa->getFCierrec() || Date::isEarlier($fecha, self::$_empresa->getFCierrec())) {
				throw new AuraNiifException('La fecha del comprobante debe ser mayor al último cierre contable');
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
			if ($comprob  ==  false) {
				throw new AuraNiifException("No existe el comprobante '$codigoComprobante'");
			} else {
				$this->_comprobs[$codigoComprobante] = $comprob;
				if ($numero > 0) {
					$this->_exists = $this->MoviNiif->count("comprob='{$codigoComprobante}' AND numero='{$numero}'");
				} else {
					$this->_exists = false;
					if ($numero <= 0) {
						$numero = $this->MoviNiif->maximum(array('numero', 'conditions' => "comprob='$codigoComprobante'"))+1;
					}
				}

				$identity = IdentityManager::getActive();
				if ($identity['id'] <= 0) {
					throw new AuraNiifException('El perfil público no está habilitado para realizar operaciones sobre comprobantes, inicie sesión con un usuario que tenga privilegios');
				}

				if ($this->_activeAction == null) {
					if ($this->_exists == false) {
						if (!self::checkPermission($identity['id'], $codigoComprobante, 'A')) {
							throw new AuraNiifException('No tiene permiso para adicionar comprobantes de "'.$comprob->getNomComprob().'"');
						}
					} else {
						if (!self::checkPermission($identity['id'], $codigoComprobante, 'M')) {
							throw new AuraNiifException('No tiene permiso para actualizar comprobantes de "'.$comprob->getNomComprob().'"');
						}
					}
				} else {
					switch($this->_activeAction) {
						case self::OP_CREATE:
							if (!self::checkPermission($identity['id'], $codigoComprobante, 'A')) {
								throw new AuraNiifException('No tiene permiso para adicionar comprobantes de "'.$comprob->getNomComprob().'"');
							}
							break;
						case self::OP_UPDATE:
							if (!self::checkPermission($identity['id'], $codigoComprobante, 'M')) {
								throw new AuraNiifException('No tiene permiso para actualizar comprobantes de "'.$comprob->getNomComprob().'"');
							}
							break;
						case self::OP_DELETE:
							if (!self::checkPermission($identity['id'], $codigoComprobante, 'D')) {
								throw new AuraNiifException('No tiene permiso para eliminar comprobantes de "'.$comprob->getNomComprob().'"');
							}
							break;
						default:
							throw new AuraNiifException('Acción inválida en realizar: '.$this->_activeAction);
					}
				}

				//Autoincremente recursivo de consecutivo de comprobante
				$consecutivoNuevo = ($numero+1);
				while($this->MoviNiif->count(array('conditions'=>"comprob='{$codigoComprobante}' AND numero='{$consecutivoNuevo}'"))>0) {
					$consecutivoNuevo += 1;
				}

				$this->_comprobs[$codigoComprobante]->setConsecutivo($consecutivoNuevo);
				$this->_consecutivos[$codigoComprobante] = $numero;
			}
		} else {
			if ($numero>0) {
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
	public function setActiveLine($line) {
		$this->_linea = $line;
	}

	/**
	 * Establece el periodo en el que se va a grabar el comprobante
	 *
	 * @param int $period
	 */
	public function setPeriod($period) {
		$this->_period = $period;
	}

    /**
	 * Aplica filtros a los valores del movimiento
	 *
	 * @param array $movement
	 */
	public function sanizite($movement) {
		if (isset($movement['Comprob'])) {
			$movement['Comprob'] = $this->filter($movement['Comprob'], 'comprob');
		}
		if (isset($movement['Numero'])) {
			$movement['Numero'] = $this->filter($movement['Numero'], 'int');
		}
		if (isset($movement['Fecha'])) {
			$movement['Fecha'] = $this->filter($movement['Fecha'], 'date');
		}
		if (isset($movement['Cuenta'])) {
			$movement['Cuenta'] = $this->filter($movement['Cuenta'], 'cuentas');
		}
		if (isset($movement['Nit'])) {
			$movement['Nit'] = $this->filter($movement['Nit'], 'terceros');
		}
		if (isset($movement['Descripcion'])) {
			$movement['Descripcion'] = $this->filter($movement['Descripcion'], 'striptags', 'extraspaces');
		}
		if (isset($movement['CentroCosto'])) {
			$movement['CentroCosto'] = $this->filter($movement['CentroCosto'], 'int');
		}
		if (isset($movement['DebCre'])) {
			$movement['DebCre'] = $this->filter($movement['DebCre'], 'onechar');
		}
		if (isset($movement['Valor'])) {
			$movement['Valor'] = $this->filter($movement['Valor'], 'numeric');
		}
		if (isset($movement['TipoDocumento'])) {
			$movement['TipoDocumento'] = $this->filter($movement['TipoDocumento'], 'documento');
		}
		if (isset($movement['NumeroDocumento'])) {
			$movement['NumeroDocumento'] = $this->filter($movement['NumeroDocumento'], 'int');
		}
		if (isset($movement['BaseGrab'])) {
			$movement['BaseGrab'] = $this->filter($movement['BaseGrab'], 'numeric');
		}
		if (isset($movement['FechaVence'])) {
			$movement['FechaVence'] = $this->filter($movement['FechaVence'], 'date');
		}
		return $movement;
	}

	/**
	 * Agrega un movimiento
	 *
	 * @param array $movement
	 */
	public function addMovement($movement) {
		if ($this->_exists) {
			if ($this->_cleaned == false) {
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
	public function appendMovement($movement) {
		if ($this->_loaded == false) {
			if ($this->_exists) {
				$consecutivo = 0;
				$tokenId = IdentityManager::getTokenId();
				$conditions = "sid='$tokenId' AND comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero'";
				$this->Movitempniif->setTransaction($this->_transaction);
				$this->Movitempniif->deleteAll($conditions);
				$movis = $this->MoviNiif->find("comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero'");
				foreach ($movis as $movi) {
					$movitemp = new Movitempniif();
					$movitemp->setTransaction($this->_transaction);
					$movitemp->setSid($tokenId);
					$movitemp->setConsecutivo($consecutivo);
					foreach ($movi->getAttributes() as $attribute) {
						$movitemp->writeAttribute($attribute, $movi->readAttribute($attribute));
					}
					if ($movitemp->save() == false) {
						foreach ($movitemp->getMessages() as $message) {
							throw new AuraNiifException($message->getMessage());
						}
					}
					$consecutivo++;
					unset($movi);
				}
				if ($this->_cleaned == false) {
					$this->_delete();
					$this->_cleaned = true;
				}
				foreach ($this->Movitempniif->find($conditions) as $movitemp) {
					$this->addMovement(array(
						'Fecha' => $movitemp->getFecha(),
						'Cuenta' => $movitemp->getCuenta(),
						'Nit' => $movitemp->getNit(),
						'CentroCosto' => $movitemp->getCentroCosto(),
						'Valor' => $movitemp->getValor(),
						'DebCre' => $movitemp->getDebCre(),
						'Descripcion' => $movitemp->getDescripcion(),
						'TipoDocumento' => $movitemp->getTipoDoc(),
						'NumeroDocumento' => $movitemp->getNumeroDoc(),
						'BaseGrab' => $movitemp->getBaseGrab(),
						'Conciliado' => $movitemp->getConciliado(),
						'FechaVence' => $movitemp->getFVence(),
						'Numfol' => $movitemp->getNumfol()
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
	public function validate($movement) {

		$this->_activeMovement = $movement;

		$this->_checkRequiredFields($movement);

		if (!isset($movement['Comprobante'])) {
			$movement['Comprobante'] = $this->_defaultComprob;
		} else {
			$this->_addComprobante($movement['Comprobante']);
		}
		if (!isset($movement['Numero'])) {
			if (isset($movement['Comprobante'])&&$movement['Comprobante']) {
				$movement['Numero'] = $this->_consecutivos[$movement['Comprobante']];
			} else {
				throw new AuraNiifException('No se ha definido el tipo de comprobante a grabar');
			}
		}
		if (!isset($movement['Fecha'])) {
			if ($this->_defaultFecha == '') {
				$this->_defaultFecha = Date::getCurrentDate();
				$this->_validateFecha($this->_defaultFecha);
			}
			$movement['Fecha'] = $this->_defaultFecha;
		} else {
			if ($this->_defaultFecha!='') {
				if ($movement['Fecha'] != $this->_defaultFecha) {
					throw new AuraNiifException('Se definieron distintas fechas en movimientos diferentes del comprobante');
				}
			} else {
				$this->_defaultFecha = $movement['Fecha'];
				//if ($this->_debug != false) {
					$this->_validateFecha($this->_defaultFecha);
				//}
			}
		}
		if ($movement['Fecha']  ==  '') {
			throw new AuraNiifException('No se indicó la fecha del comprobante');
		}

		if ($movement['DebCre']   === '1') {
			$movement['DebCre'] = 'C';
		} else {
			if ($movement['DebCre']   === '0') {
				$movement['DebCre'] = 'D';
			}
		}

		if ($movement['DebCre'] !== 'D' && $movement['DebCre'] !==  'C') {
			throw new AuraNiifException('El campo naturaleza debe ser "C" ó "D" en la línea '.$this->_linea);
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
		if ($cuenta  ==  false) {
			throw new AuraNiifException('No existe la cuenta niif "'.$movement['Cuenta'].'" ó no es auxiliar, en la línea '.$this->_linea);
		}

		if ($cuenta->getPideNit() == 'S') {
			$nitErrado = false;
			if (!isset($movement['Nit'])) {
				$nitErrado = true;
				$movement['Nit'] = '';
			} else {
				if ($movement['Nit'] ===''||$movement['Nit'] ==='0'||is_null($movement['Nit'])) {
					$nitErrado = true;
				} else {
					if (is_array($movement['Nit'])||is_object($movement['Nit'])) {
						$nitErrado = true;
					} else {
						$nitErrado = !self::_existeTercero($movement['Nit']);
					}
				}
			}
			if ($nitErrado == true) {
				throw new AuraNiifException('Tercero requerido, la cuenta "'.$movement['Cuenta'].'"('.$cuenta->getNombre().') solicita tercero, en la línea '.$this->_linea.' ('.$movement['Nit'].')');
			}
			unset($nitErrado);
		} else {
			$movement['Nit'] = '0';
		}

		if ($movement['Valor']<0) {
			throw new AuraNiifException('El valor "'.$movement['Valor'].'" es inválido  en la línea '.$this->_linea);
		} else {
			$movement['Valor'] = LocaleMath::round($movement['Valor'], 2);
			if (strlen($movement['Valor'])>=17) {
				throw new AuraNiifException('La base de datos no podrá almacenar el valor "'.$movement['Valor'].'" en la línea '.$this->_linea);
			}
		}

		if ($movement['DebCre'] == 'D') {
			$this->_totalDebitos+=$movement['Valor'];
		} else {
			$this->_totalCreditos+=$movement['Valor'];
		}
		unset($tipoCuenta);
		unset($cuenta);

		self::$_numberStored++;
		if (self::$_numberStored>50) {
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

		if ($this->_debug == true) {
			$this->_movements[] = $movement;
		}

		try {
			$movi = new MoviNiif();
			$movi->setTransaction($this->_transaction);
			$movi->setComprob($movement['Comprobante']);
			$movi->setNumero($movement['Numero']);
			$movi->setFecha((string)$movement['Fecha']);
			$movi->setCuenta($movement['Cuenta']);
			$movi->setNit($movement['Nit']);
			$movi->setCentroCosto($movement['CentroCosto']);
			$movi->setValor($movement['Valor']);
			$movi->setDebCre($movement['DebCre']);
			if (isset($movement['Folio'])) {
				if ($movement['Folio']!=0) {
					if (isset($movement['Descripcion'])) {
						$movement['Descripcion'].=' F'.$movement['Folio'];
					} else {
						$movement['Descripcion'] = ' F'.$movement['Folio'];
					}
					$movi->setNumfol($movement['Folio']);
				}
			}
			if (isset($movement['Descripcion'])) {
				$movi->setDescripcion($movement['Descripcion']);
			}
			if (isset($movement['TipoDocumento'])) {
				$movi->setTipoDoc($movement['TipoDocumento']);
				$movi->setNumeroDoc($movement['NumeroDocumento']);
			}
			if (isset($movement['BaseGrab'])) {
				$movi->setBaseGrab($movement['BaseGrab']);
			}
			if (isset($movement['Conciliado'])) {
				$movi->setConciliado($movement['Conciliado']);
			}
			if (isset($movement['FechaVence'])) {
				$movi->setFVence((string)$movement['FechaVence']);
			}
			if (isset($movement['Numfol'])) {
				$movi->setNumfol($movement['Numfol']);
			}
			if ($movi->save() == false) {
				if ($this->_externalTransaction == true) {
					foreach ($movi->getMessages() as $message) {
						$this->_transaction->rollback('Movi: ' . $message->getMessage().'. '.$movi->inspect().'. '.print_r($movement, true), $message->getCode());
					}
				} else {
					foreach ($movi->getMessages() as $message) {
						throw new AuraNiifException('Movi: ' . $message->getMessage().'. '.$movi->inspect().'. '.print_r($movement, true), $message->getCode());
					}
				}
			}
			unset($movi);

			$cuenta = $this->_getCuenta($movement['Cuenta']);
			if ($cuenta->getPideNit() == 'S') {

				//SALDOS NIIF
				if ($cuenta->getCuentaNiif()) {

					$saldosNiif = $this->SaldosNiif->findFirst("cuenta='{$cuenta->getCuentaNiif ()}' AND nit='{$movement['Nit']}' AND ano_mes=".$this->_period);

					if ($saldosNiif == false) {
						$saldosNiif = new SaldosNiif();
                        $saldosNiif->setTransaction($this->_transaction);
						$saldosNiif->setCuenta($cuenta->getCuentaNiif ());
						$saldosNiif->setNit($movement['Nit']);
						$saldosNiif->setAnoMes($this->_period);

						if ($movement['DebCre'] == 'C') {
							$saldosNiif->setDebe(0);
							$saldosNiif->setHaber($movement['Valor']);
							$saldosNiif->setSaldo(-$movement['Valor']);
						} else {
							$saldosNiif->setDebe($movement['Valor']);
							$saldosNiif->setHaber(0);
							$saldosNiif->setSaldo($movement['Valor']);
						}
					} else {
						$saldosNiif->setTransaction($this->_transaction);
						if ($movement['DebCre'] == 'C') {
							$haber = $saldosNiif->getHaber() + $movement['Valor'];
							$saldosNiif->setHaber($haber);
							$saldosNiif->setSaldo($saldosNiif->getDebe() - $haber);
							unset($haber);
						} else {
							$debe = $saldosNiif->getDebe() + $movement['Valor'];
							$saldosNiif->setDebe($debe);
							$saldosNiif->setSaldo($debe - $saldosNiif->getHaber());
							unset($debe);
						}
					}
					if ($saldosNiif->save() == false) {
						if ($this->_externalTransaction == true) {
							foreach ($saldosNiif->getMessages() as $message) {
								$this->_transaction->rollback('SaldosNiif: '.$message->getMessage().'. '.$saldosn->inspect().'. '.print_r($movement, true), $message->getCode());
							}
						} else {
							foreach ($saldosNiif->getMessages() as $message) {
								throw new AuraNiifException('SaldosNiif: '.$message->getMessage().'. '.$saldosn->inspect().'. '.print_r($movement, true), $message->getCode());
							}
						}
					}
					unset($saldosNiif);
				}

			}
		}
		catch(DbLockAdquisitionException $e) {
			$message = 'La base de datos está bloqueada mientras otro usuario termina un proceso de grabación. Intente grabar nuevamente en un momento';
			if ($this->_externalTransaction == true) {
				$this->_transaction->rollback($message);
			} else {
				throw new AuraNiifException($message);
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
	public function delete() {
		$this->_activeAction = self::OP_DELETE;
		$this->_delete();
		if ($this->_externalTransaction == true) {
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
	private function _delete() {

		if ($this->_activeAction == self::OP_DELETE) {
			$comprob = $this->Comprob->findFirst("codigo='{$this->_defaultComprob}'");
			$identity = IdentityManager::getActive();
			if (!self::checkPermission($identity['id'], $this->_defaultComprob, 'D')) {
				if ($comprob == false) {
					throw new AuraNiifException('No existe el comprobante "'.$this->_defaultComprob.'"');
				} else {
					throw new AuraNiifException('No tiene permiso para eliminar comprobantes de "'.$comprob->getNomComprob().'"');
				}
			}
		}

		if (!$this->_exists) {
			throw new AuraNiifException("No existe el comprobante a eliminar {$this->_defaultComprob}-{$this->_defaultNumero}");
		}

		$conditions = "comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero'";

		$this->backupMovi($this->_defaultComprob, $this->_defaultNumero);

		$movis = $this->MoviNiif->find(array($conditions, 'columns' => 'comprob,numero,cuenta,valor,centro_costo,nit,tipo_doc,deb_cre,numero_doc'));
		foreach ($movis as $movi) {

			if ($movi->getValor()!=0) {

				$cuenta = $this->_getCuenta($movi->getCuenta());
				if ($cuenta == false) {
					throw new AuraNiifException('La cuenta "'.$movi->getCuenta().'" antes existía pero ya no existe. No se puede borrar el comprobante');
				}

				if ($cuenta->getPideNit() == 'S') {
					$saldosNiif = $this->SaldosNiif->findFirst("cuenta='{$movi->getCuenta()}' AND nit='{$movi->getNit()}' AND ano_mes=0");
					if ($saldosNiif!=false) {
						$saldosNiif->setTransaction($this->_transaction);
						if ($movi->getDebCre() == 'C') {
							$haber = $saldosNiif->getHaber() - $movi->getValor();
							$saldosNiif->setHaber($haber);
							$saldosNiif->setSaldo($saldosNiif->getDebe() - $haber);
						} else {
							$debe = $saldosNiif->getDebe() - $movi->getValor();
							$saldosNiif->setDebe($debe);
							$saldosNiif->setSaldo($debe - $saldosNiif->getHaber());
						}
						if ($saldosNiif->save() == false) {
							if ($this->_externalTransaction == true) {
								foreach ($movi->getMessages() as $message) {
									$this->_transaction->rollback('SaldosNiif: '.$message->getMessage().'. '.$saldosNiif->inspect().'.', $message->getCode());
								}
							} else {
								foreach ($saldosNiif->getMessages() as $message) {
									throw new AuraNiifException('SaldosNiif: '.$message->getMessage().'. '.$saldosNiif->inspect().'.', $message->getCode());
								}
							}
						}
					}
					unset($saldosNiif);
				}
			}
			unset($cuenta);
			unset($movi);
		}
		unset($movis);
		$this->MoviNiif->deleteAll("comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero'");

		if ($this->_activeAction == self::OP_DELETE) {
			$this->Movitempniif->deleteAll("comprob='{$this->_defaultComprob}' AND numero='$this->_defaultNumero'");
			if ($comprob) {
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
	private function _checkRequiredFields($movement) {
		foreach (self::$_requiredFields as $requiredField) {
			if (!isset($movement[$requiredField])) {
				throw new AuraNiifException("No existe el campo requerido '$requiredField' en el movimiento");
			}
		}
	}

	/**
	 * Consulta un comprobante
	 *
	 * @param  string $comprobante
	 * @return boolean
	 */
	private function _existeComprobante($comprobante) {
		$comprobante = $this->filter($comprobante, 'comprob');
		return (bool) $this->Comprob->count("codigo='$comprobante'");
	}

	/**
	 * Consulta una cuenta y cachea el resultado
	 *
	 * @param  string $codigoCuenta
	 * @return Cuentas
	 */
	private function _getCuenta($codigoCuenta) {
		if (!isset(self::$_cuentas[$codigoCuenta])) {
			self::$_cuentas[$codigoCuenta] = $this->Niif->findFirst("cuenta='{$codigoCuenta}' AND es_auxiliar='S'", 'columns: cuenta,nombre,tipo,pide_nit,pide_centro,pide_fact');
		}
		return self::$_cuentas[$codigoCuenta];
	}

	/**
	 * Consulta un comprobante y cachea el resultado
	 *
	 * @param  string $codigoComprob
	 * @return Comprob
	 */
	private function _getComprob($codigoComprob) {
		if (!isset(self::$_comprob[$codigoComprob])) {
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
	private function _existeCentro($codigoCentro) {
		if (!isset(self::$_centros[$codigoCentro])) {
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
	private function _existeDocumento($tipoDocumento) {
		if (!isset(self::$_documentos[$tipoDocumento])) {
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
	private function _existeTercero($nit) {
		if (!isset(self::$_terceros[$nit])) {
			self::$_terceros[$nit] = $this->Nits->count("nit='{$nit}'")>0;
		}
		return self::$_terceros[$nit];
	}

	/**
	 * Verifica si el comprobante tiene sumas iguales de débitos y créditos
	 *
	 */
	private function _checkEquals() {
		$this->_totalCreditos = LocaleMath::round($this->_totalCreditos, 3);
		$this->_totalDebitos = LocaleMath::round($this->_totalDebitos, 3);
		if ($this->_totalCreditos!=$this->_totalDebitos) {
			throw new AuraNiifException("El comprobante está descuadrado Créditos=".Currency::number($this->_totalCreditos)." y Débitos=".Currency::number($this->_totalDebitos));
		}
		if ($this->_activeNumberStored<2) {
			throw new AuraNiifException('El comprobante debe tener al menos 2 movimientos');
		}
	}

	/**
	 * Almacena el comprobante
	 *
	 * @return boolean
	 */
	public function save() {
		$this->_checkEquals();
		if ($this->_exists == false) {
			//Rcs::disable();
			foreach ($this->_comprobs as $tipoComprob => $comprob) {
				$comprob->setTransaction($this->_transaction);
				if ($comprob->save() == false) {
					if ($this->_externalTransaction == true) {
						foreach ($comprob->getMessages() as $message) {
							$this->_transaction->rollback('Tipo Comprobante: '.$message->getMessage(), $message->getCode());
						}
					} else {
						foreach ($comprob->getMessages() as $message) {
							throw new AuraNiifException('Tipo Comprobante: '.$message->getMessage(), $message->getCode());
						}
					}
				}
				$this->_createGrab($comprob, 'A');
				unset($this->_comprobs[$tipoComprob]);
			}
		} else {
			foreach ($this->_comprobs as $tipoComprob => $comprob) {
				$comprob->setTransaction($this->_transaction);
				$this->_createGrab($comprob, 'M');
				unset($this->_comprobs[$tipoComprob]);
			}
		}
		//Rcs::enable();
		if ($this->_externalTransaction == false) {
			$this->_transaction->commit();
		}
		return true;
	}

	/**
	 * Crea el registro en grab indicando la operación realizada sobre el comprobante
	 *
	 * @param Comprob $comprob
	 */
	private function _createGrab(Comprob $comprob, $accion, $consecutivoOff=false) {
		$identity = IdentityManager::getActive();
		$grab = new Grabniif();
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

		if ($grab->save() == false) {
			if ($this->_externalTransaction == true) {
				foreach ($grab->getMessages() as $message) {
					$this->_transaction->rollback('GrabNiif: '.$message->getMessage(), $message->getCode());
				}
			} else {
				foreach ($grab->getMessages() as $message) {
					throw new AuraNiifException('GrabNiif: '.$message->getMessage(), $message->getCode());
				}
			}
		}
		//Create Movi Niif
		new AuraNiif ($grab->getComprob(), $grab->getNumero());
	}

	/**
	 * Obtiene el consecutivo actual de un comprobante
	 *
	 * @param int $codigoComprobante
	 */
	public function getConsecutivo($codigoComprobante='') {
		if ($codigoComprobante == '') {
			if (count($this->_consecutivos) == 1) {
				return current($this->_consecutivos);
			} else {
				throw new AuraNiifException('Indique el comprobante donde se consultará el consecutivo');
			}
		}
		if (isset($this->_consecutivos[$codigoComprobante])) {
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
	public function getDefaultFecha() {
		return $this->_defaultFecha;
	}

	/**
	 * Obtiene el movimiento que se estaba guardando al generar una excepcion
	 *
	 * @return array
	 */
	public function getActiveMovement() {
		return $this->_activeMovement;
	}

	/**
	 * Obtiene todos los movimientos grabados (solo en modo debug)
	 *
	 */
	public function getMovements() {
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
	public static function validateComprob($comprob, $numero) {
		$aura = new self($comprob, $numero, null, self::OP_CREATE);
		$line = 1;
		$messages = array();
		$movis = self::getModel('MoviNiif')->find("comprob='$comprob' AND numero='$numero'", "columns: comprob,numero,fecha,cuenta,nit,centro_costo,valor,deb_cre,tipo_doc,numero_doc,base_grab");
		foreach ($movis as $movi) {
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
			catch(AuraNiifException $e) {
				$messages[] = $e->getMessage();
			}
			$line++;
			unset($movi);
		}
		return $messages;
	}

    /**
	 * Comprueba si el usuario activo tiene permiso para realizar determinada acción sobre un comprobante
	 *
	 * @param	string $comprob
	 * @param	string $type
	 * @return	boolean
	 */
	public static function checkPermission($identityId, $comprob, $type) {
		return self::getModel('PermisosComprob')->count("usuarios_id='$identityId' AND comprob='$comprob' AND popcion='$type'");
	}

    private function backupMovi($comprob, $numero)
	{
		$movis = $this->MoviNiif->find("comprob='$comprob' AND numero='$numero'");
		if (count($movis)) {

			$identity = IdentityManager::getActive();

			foreach ($movis as $movi) {

				$moviBackup = new Movibackupniif();

				foreach ($movi->getAttributes() as $field) {
					$moviBackup->writeAttribute($field, $movi->readAttribute($field));
				}

				$moviBackup->setDeletedTime(date("Y-m-d H:i:s"));
				$moviBackup->setUsuariosId($identity["id"]);

				$moviBackup->save();

				unset($moviBackup);
			}
		}
		unset($movis);
	}

    /**
     * Create movi niff by Movi
     *
     * @param  string   $comprob
     * @param  int      $numero
     */
    public function createMoviNiifByMovi($comprob, $numero)
    {
        $condition = "comprob = '$comprob' AND numero= '$numero'";

        $movis = $this->Movi->setTransaction($this->_transaction)->find(array(
            "conditions" => $condition
        ));

        if ($movis && count($movis)) {

            $this->MoviNiif->setTransaction($this->_transaction)->deleteAll($condition);

            foreach ($movis as $movi) {

                $cuentaMovi = $movi->readAttribute('cuenta');
                $cuenta = EntityManager::get("Cuentas")->findFirst($cuentaMovi);

                $cuentaNiif = $cuenta->getCuentaNiif();
                if (!$cuentaNiif && $this->debug == true) {
                    throw new AuraNiifException("La cuenta '$cuentaMovi' no tiene parametrizada la cuenta NIIF");
                }

                if ($cuenta) {

                    $moviNiif = new MoviNiif();
                    $moviNiif->setTransaction($this->_transaction);

                    foreach ($movi->getAttributes() as $field) {
                        $moviNiif->writeAttribute($field, $movi->readAttribute($field));
                    }

                    //Copiamos cuenta de movi a columna cuenta_movi
                    $moviNiif->writeAttribute('cuenta_movi', $cuentaMovi);

                    //Cambiamos a cuenta niif en column cuenta
                    $moviNiif->writeAttribute('cuenta', $cuentaNiif);

                    if (!$moviNiif->save()) {

                        foreach ($moviNiif->getMessages() as $message) {
                            throw new AuraNiifException($message->getMessage());
                        }

                    }

                }

                unset($movi);
            }

            unset($movis);
        } else {
            //throw new AuraNiifException("Movimiento '$comprob-$numero' no existe" . print_r(debug_backtrace(), true));
            $this->removeNiif($comprob, $numero);
        }

    }

	public function setTransaction($transaction)
	{
		$this->_transaction = $transaction;
	}


    /**
     * Remove comprob in movi_niif if Movi does not exists
     *
     * @param  string $comprob
     * @param  integer $numero
     * @return boolean
     */
    public function removeNiif($comprob, $numero)
    {
        return $this->MoviNiif->setTransaction($this->_transaction)->deleteAll("comprob='$comprob' AND numero='$numero'");
    }
}
