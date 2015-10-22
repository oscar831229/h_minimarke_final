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
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version	$Id$
 */

/**
 * Tatico
 *
 * Clase para generar interfaz de Inventarios
 *
 */
class Tatico extends UserComponent
{

	/**
	 * Comprobante de la transacción
	 *
	 * @var array
	 */
	private $_comprobs = array();

	/**
	 * Consecutivos de la última transaccion
	 *
	 * @var array
	 */
	private $_consecutivos = array();

	/**
	 * Comprobante por defecto
	 *
	 * @var string
	 */
	private $_defaultComprob;

	/**
	 * ActiveRecord de empresa
	 *
	 * @var ActiveRecord
	 */
	private $_empresa;

	/**
	 * Fecha predeterminada
	 *
	 * @var string
	 */
	private $_defaultFecha;

	/**
	 * Transacción para grabar los movimientos
	 *
	 * @var ActiveRecordTransaction
	 */
	private $_transaction;

	/**
	 * Indica si ya existe una transacción externa
	 *
	 * @var boolean
	 */
	private $_hasTransaction = false;

	/**
	 * Indica si se hace debug o no y mostrar mas cosas para depuración
	 *
	 * @var boolean
	 */
	public $debug = false;

	/**
	 * Indica si se debe controlar que no queden existencias negativas
	 *
	 * @var boolean
	 */
	private static $_controlNegatives = true;

	/**
	 * Indica si se debe controlar los stocks por almacén
	 *
	 * @var boolean
	 */
	private static $_controlStocks = true;

	/**
	 * Indica si se debe mostrar la explicación del calculo del IVA
	 *
	 * @var boolean
	 */
	private static $_taxesDebug = false;

	/**
	 * Advertencia de stock bajo o alto generadas
	 *
	 * @var array
	 */
	private static $_stockWarnings = array();

	/**
	 * Advertencia al tomar costos del almacén principal
	 *
	 * @var array
	 */
	private static $_costosWarnings = array();

	/**
	 * Tipos de Comprobantes de Inventarios
	 *
	 * @var array
	 */
	static private $_types = array(
		'O' => 'Orden de Compra',
		'E' => 'Entrada',
		'P' => 'Pedido',
		'C' => 'Consumo',
		'T' => 'Traslado',
		'A' => 'Ajuste',
		'R' => 'Transformación'
	);

	/**
	 * Constructor de Tatico
	 *
	 * @param	string $codigoComprobante
	 * @param	int $numero
	 * @param	Date $fecha
	 */
	public function __construct($codigoComprobante='', $numero=0, $fecha='')
	{

		$this->debug = false;

		$this->_hasTransaction = TransactionManager::hasUserTransaction();
		$this->_transaction = TransactionManager::getUserTransaction();

		$this->Comprob->setTransaction($this->_transaction);

		$this->_addComprobante($codigoComprobante, $numero);
		$this->_defaultComprob = $codigoComprobante;
		$this->_empresa = BackCacher::getEmpresa();
		$this->_defaultFecha = $fecha;
	}

	/**
	 * Consulta un comprobante y genera el consecutivo si es necesario
	 *
	 * @param	string $codigoComprobante
	 * @param	int $numero
	 */
	private function _addComprobante($codigoComprobante, $numero=0)
	{
		$tipoComprobante = $codigoComprobante[0];
		if (!isset(self::$_types[$tipoComprobante])) {
			throw new TaticoException('El tipo de comprobante "'.$tipoComprobante.'" de inventarios no existe', 10001);
		}
		if (!isset($this->_comprobs[$codigoComprobante])) {
			$comprob = $this->Comprob->findFirst(array("codigo='$codigoComprobante'", 'for_update' => true));
			if ($comprob==false) {
				throw new TaticoException("No existe el comprobante '$codigoComprobante'", 10002);
			} else {
				if ($numero == 0) {
					$numero = $this->_getMaxNumeroComprob($codigoComprobante);
				}
				$this->_comprobs[$codigoComprobante] = $comprob;
				$this->_comprobs[$codigoComprobante]->setConsecutivo($numero);
			}
		} else {
			$this->_comprobs[$codigoComprobante]->setConsecutivo($numero);
		}
	}

	/**
	 * Valida que un movimiento tenga todo lo requerido para grabarse
	 *
	 * @param	array $movement
	 * @return	array
	 */
	public function validate($movement)
	{

		if (!isset($movement['Comprobante'])) {
			$movement['Comprobante'] = $this->_defaultComprob;
		}
		if (!isset($movement['Numero'])) {
			$movement['Numero'] = $this->_comprobs[$movement['Comprobante']]->getConsecutivo();
		}
		if (!isset($movement['Fecha'])) {
			$movement['Fecha'] = Date::getCurrentDate();
		}

		//Validaciones especificas del tipo de comprobante
		$tipoComprob = substr($movement['Comprobante'], 0, 1);

		//Obtiene el movihead
		$movihead = self::getMovement($movement['Comprobante'], $movement['Almacen'], $movement['Numero']);
		if ($movihead != false) {
			if ($movihead->getEstado() == 'C') {
				switch ($tipoComprob) {
					case 'O':
						$this->_throwException('Ya se hizo la entrada a la orden de compra y no puede ser modificada', 10003);
						break;
					case 'E':
						$this->_throwException('Las entradas al almacén no pueden ser modificadas, por favor cree una nueva');
						break;
					case 'C':
						$this->_throwException('Las salidas del almacén no pueden ser modificadas, por favor cree una nueva');
						break;
					case 'T':
						$this->_throwException('Los traslados entre almacenes no pueden ser modificados, por favor cree uno nuevo');
						break;
					case 'P':
						$this->_throwException('El pedido ya se hizo efectivo y no puede ser modificado, por favor cree uno nuevo', 10003);
						break;
					case 'A':
						$this->_throwException('Los ajustes a inventarios no pueden ser modificados, por favor cree uno nuevo');
						break;
					default:
						$this->_throwException('El movimiento de inventarios ya fue cerrado y no puede ser editado');
				}
			}
		}

		//Validación de fecha en el periodo activo
		try {
			$fechaMovimiento = new Date($movement['Fecha']);
		} catch (DateException $e) {
			$this->_throwException($e->getMessage());
		}

		$fechaCierre = $this->_empresa->getFCierrei();
		if (Date::isEarlier($fechaMovimiento, $fechaCierre)) {
			$this->_throwException('La fecha del movimiento no puede estar en un periodo ya cerrado', 10005);
		} else {
			if (Date::isLater($fechaMovimiento, Date::getCurrentDate())) {
				$this->_throwException('La fecha ingresada debe ser menor o igual a la fecha actual', 10006);
			}
		}

		//Validar que el movimiento esté en el cierre
		if ($tipoComprob != 'O' && $tipoComprob != 'P') {
			$fechaCierreInicial = clone $fechaCierre;
			$fechaCierreFinal = clone $fechaCierre;
			$fechaCierreInicial->addDays(1);
			$fechaCierreFinal->addMonths(1);
			$fechaCierreFinal->toLastDayOfMonth();
			if (!$fechaMovimiento->isBetween($fechaCierreInicial, $fechaCierreFinal)) {
				$this->_throwException('La fecha debe estar en el rango de fechas del periodo activo. Del ' .
					$fechaCierreInicial->getLocaleDate('medium').' hasta el ' . $fechaCierreFinal->getLocaleDate('medium') .
					'. Fecha de la transacción: ' . $fechaMovimiento->getLocaleDate('medium'), 10007);
			}
		}

		//La fecha de vencimiento tiene que ser menor o igual a la fecha actual
		if (isset($movement['FechaVencimiento'])) {
			if (Date::isEarlier($movement['FechaVencimiento'], $movement['Fecha'])) {
				$this->_throwException('La fecha de vencimiento debe ser mayor que la fecha del movimiento', 10008);
			}
		}

		//Transformacion
		if ($tipoComprob == 'R') {

			//Tipo de transformacion
			if (!isset($movement['Tipo']) || !in_array($movement['Tipo'], array('1N', 'N1'))) {
				$this->_throwException('El tipo de trasformación no fue digitado o no es válido');
			}

			//Item a transformar sea valido
			$itemTarget = BackCacher::getInve($movement['ItemTarget']);
			if ($itemTarget==false) {
				if ($movement['Tipo']=='1N') {
					$this->_throwException('Indique la referencia base de la trasformación');
				} else {
					$this->_throwException('Indique la referencia destino de la trasformación');
				}
			} else {
				if ($itemTarget->getEstado() <> 'A') {
					if ($movement['Tipo'] == '1N') {
						$this->_throwException('La referencia base de la trasformación está inactiva');
					} else {
						$this->_throwException('La referencia destino de la trasformación está inactiva');
					}
				}
			}

			//Que la cantidad sea valida
			if (!isset($movement['CantidadTarget'])) {
				$this->_throwException('La cantidad de la referencia a trasformar no fue digitada ó no es válida');
			}
		}

		//Traslados
		if ($tipoComprob=='T') {

			//Que el almacen destino exista y sea valido
			$almacenDestino = self::getModel('Almacenes')->findFirst("codigo='{$movement['AlmacenDestino']}'");
			if ($almacenDestino==false) {
				$this->_throwException('El traslado debe tener un almacén de destino asociado', 10009);
			}
			if ($movement['AlmacenDestino']==$movement['Almacen']) {
				$this->_throwException('El almacén de origen debe ser diferente al almacen de destino', 10009);
			}

		}

		//Ordenes de Compra
		if ($tipoComprob=='O') {
			//Forma de Pago Exista
			$formapago = self::getModel('FormaPago')->findFirst("codigo='{$movement['FormaPago']}'");
			if ($formapago==false) {
				$this->_throwException('La orden de compra debe tener una forma de pago asociada');
			}
		}

		//Orden o Entrada
		if ($tipoComprob=='O'||$tipoComprob=='E') {
			//Proveedor exista
			$tercero = BackCacher::getTercero($movement['Nit']);
			if ($tercero==false) {
				if ($tipoComprob=='O') {
					$this->_throwException('La orden de compra debe tener un proveedor asociado');
				} else {
					$this->_throwException('La entrada debe tener un proveedor asociado');
				}
			}
		}

		//Entrada
		if ($tipoComprob == 'E') {
			if (!isset($movement['FacturaC'])||!$movement['FacturaC']) {
				$this->_throwException('La entrada debe tener una factura de compra asociada');
			}
			$conditions = "comprob LIKE 'E%' AND nit='{$movement['Nit']}' AND factura_c='{$movement['FacturaC']}'";
			$movihead = self::getModel('Movihead')->findFirst($conditions);
			if ($movihead != false) {
				$this->_throwException('Ya se realizó la entrada al almacén a la factura '.$movement['FacturaC'].' del proveedor '.$tercero->getNit().'/'.$tercero->getNombre().'. La entrada es la '.$movihead->getComprob().'-'.$movihead->getNumero(), 10010);
			}
		}

		//Salidas
		if ($tipoComprob=='C' || $tipoComprob == 'P' || $tipoComprob=='A') {
			if (!isset($movement['CentroCosto']) || !$movement['CentroCosto']) {
				if ($tipoComprob == 'C') {
					$this->_throwException('Indique el centro de costo asociado a la salida del almacén');
				} else {
					if ($tipoComprob == 'P') {
						$this->_throwException('Indique el centro de costo asociado al pedido');
					} else {
						$this->_throwException('Indique el centro de costo asociado al ajuste');
					}
				}
			}
			if ($tipoComprob == 'C') {
				if (!isset($movement['Tipo']) || !$movement['Tipo']) {
					$this->_throwException('Indique si la salida a realizar es de tipo interno ó externo');
				}
			}
		}

		//Estado??
		if (!isset($movement['Estado'])) {
			$movement['Estado'] = 'A';
		}

		//Si no hay centro de costo él le coloca el centro de costo del hotel que está en empresa
		if (!isset($movement['CentroCosto'])) {
			$movement['CentroCosto'] = $this->_empresa->getCentroCosto();
		}

		//Validación del Detalle
		if (!is_array($movement['Detail']) || count($movement['Detail'])==0) {
			switch($tipoComprob) {
				case 'O':
					$this->_throwException('Debe ingresar las referencias de la orden de compra');
					break;
				case 'E':
					$this->_throwException('Debe ingresar las referencias a las que se le hará la entrada del almacén');
					break;
				case 'C':
					$this->_throwException('Debe ingresar las referencias a las que se le hará la salida del almacén');
					break;
				case 'T':
					$this->_throwException('Debe ingresar las referencias que se trasladarán entre almacenes');
					break;
				case 'A':
					$this->_throwException('Debe ingresar las referencias a ajustar');
					break;
				case 'P':
					$this->_throwException('Debe ingresar las referencias del pedido');
					break;
				case 'R':
					$this->_throwException('Debe ingresar las referencias a transformar');
					break;
				default:
					$this->_throwException('El movimiento debe tener referencias asociadas');
					break;
			}
		} else {
			if ($tipoComprob!='A'&&$tipoComprob!='C'&&$tipoComprob!='T'&&$tipoComprob!='P') {
				$linea = 1;
				foreach ($movement['Detail'] as $detail) {
					if ($detail['Valor'] == 0) {
						$this->_throwException('La referencia '.$detail['Item'].' en la línea '.$linea.' debe estar valorizada');
					}
					$linea++;
					unset($detail);
				}
			}
			//Detalle de Transformaciones
			if ($tipoComprob=='R') {
				$valorTotal = 0;
				foreach ($movement['Detail'] as $detail) {
					if ($movement['ItemTarget'] == $detail['Item']) {
						if ($movement['Tipo'] == '1N') {
							$this->_throwException('La referencia base no puede estar presente en las referencias destino');
						} else {
							$this->_throwException('La referencia destino no puede estar presente en las referencias base');
						}
					}
					$valorTotal += $detail['Valor'];
					unset($detail);
				}
				if (LocaleMath::round($movement['VTotal'], 2) != LocaleMath::round($valorTotal,2)) {
					$this->_throwException('La suma de las valorizaciones de cada referencia no corresponde con el valor total base '.$movement['VTotal']."!=".$valorTotal);
				}
			}
		}

		return $movement;
	}

	/**
	 * Agrega un movimiento
	 *
	 * @param	array $movement
	 * @throws	TaticoException
	 */
	public function addMovement($movement)
	{
		try {
			set_time_limit(0);
			$movement = $this->validate($movement);
			return $this->_storeMovement($movement);
		}
		catch (DbLockAdquisitionException $e) {
			throw new TaticoException('La base de datos está bloqueada por otro usuario, por favor espere un momento y vuelva a intentar');
		}
		catch (AuraException $e) {
			throw new TaticoException('Contabilidad: '.$e->getMessage());
		}
		catch (AuraNiifException $e) {
			throw new TaticoException('Contabilidad: '.$e->getMessage());
		}
		catch (GardienException $e) {
			throw new TaticoException('Seguridad: '.$e->getMessage());
		}
		catch (IdentityManagerException $e) {
			throw new TaticoException('Autenticación: '.$e->getMessage());
		}
		catch (TransactionFailed $e) {
			throw new TaticoException($e->getMessage(), $e->getCode());
		}
		catch (Exception $e) {
			throw new TaticoException('Contabilidad: '.$e->getMessage());
		}
	}

	/**
	 * Graba el movimiento
	 *
	 * @param array $movement
	 */
	private function _storeMovement($movement)
	{

		//Tercero del Hotel
		$terceroHotel = BackCacher::getTercero($this->_empresa->getNit());
		if ($terceroHotel==false) {
			$this->_throwException('El hotel debe ser creado como un tercero', 10011);
		}

		//Validar Almacén
		$tipoComprob = substr($movement['Comprobante'], 0, 1);
		$almacen = BackCacher::getAlmacen($movement['Almacen']);
		if ($almacen==false) {
			$this->_throwException('No existe el almacén '.$movement['Almacen']);
		}
		if ($tipoComprob != 'C' && $tipoComprob != 'A') {
			if (!$almacen->getCentroCosto()) {
				$this->_throwException('El centro de costo del almacén '.$movement['Almacen'].' no existe');
			}
			$centroCosto = BackCacher::getCentro($almacen->getCentroCosto());
			if ($centroCosto==false) {
				$this->_throwException('El centro de costo del almacén '.$movement['Almacen'].' no es válido');
			}
		}

		$identity = IdentityManager::getActive();

		//Crear Movihead
		$movihead = new Movihead();
		$movihead->setTransaction($this->_transaction);
		$movihead->setComprob($movement['Comprobante']);
		$movihead->setAlmacen($movement['Almacen']);
		$movihead->setNumero($movement['Numero']);
		$movihead->setFecha($movement['Fecha']);
		$movihead->setCentroCosto($movement['CentroCosto']);
		$movihead->setUsuariosId($identity['id']);
		$movihead->setIva(0);
		$movihead->setIvad(0);
		$movihead->setIvam(0);
		$movihead->setIca(0);
		$movihead->setDescuento(0);
		$movihead->setRetencion(0);
		$movihead->setCree(0);
		$movihead->setImpo(0);
		$movihead->setTotalNeto(0);
		$movihead->setVTotal(0);
		$movihead->setEstado($movement['Estado']);
		if (isset($movement['Nit'])) {
			$movihead->setNit($movement['Nit']);
			$tercero = self::getModel('Nits')->findFirst("nit='{$movement['Nit']}'");
			if ($tercero == false) {
				if ($tipoComprob == 'E' || $tipoComprob == 'O') {
					$this->_throwException('No existe el tercero "'.$movement['Nit'].'" asociado a la transacción de inventarios');
				} else {
					$movihead->setNit(null);
					unset($movement['Nit']);
				}
			}
		} else {
			$tercero = false;
		}
		if (isset($movement['NPedido'])) {
			$movihead->setNPedido($movement['NPedido']);
		}
		if (isset($movement['FechaVencimiento'])) {
			$movihead->setFVence($movement['FechaVencimiento']);
		}
		if (isset($movement['AlmacenDestino'])) {
			$movihead->setAlmacenDestino($movement['AlmacenDestino']);
		}
		if (isset($movement['FechaEntrega'])) {
			$movihead->setFEntrega($movement['FechaEntrega']);
		}
		if (isset($movement['FormaPago'])) {
			$movihead->setFormaPago($movement['FormaPago']);
		}
		if (isset($movement['FacturaC'])) {
			$movihead->setFacturaC($movement['FacturaC']);
		}
		if (isset($movement['Tipo'])) {
			$movihead->setNota($movement['Tipo']);
		}
		if (isset($movement['Observaciones'])) {
			$movihead->setObservaciones($movement['Observaciones']);
		}

		//Ordenes o entradas
		if ($tipoComprob=='O'||$tipoComprob=='E') {

			//Almacenar los Ivas
			$movih1 = new Movih1();
			foreach ($movih1->getAttributes() as $attribute) {
				$movih1->writeAttribute($attribute, 0);
				unset($attribute);
			}
			$movih1->setComprob($movement['Comprobante']);
			$movih1->setNumero($movement['Numero']);
			if ($movih1->save()==false) {
				foreach ($movih1->getMessages() as $message)
				{
					$this->_throwException('Movih1: '.$message->getMessage());
					unset($message);
				}
			}
		}

		//Salidas
		if ($tipoComprob=='C') {
			//En la nota guarda el tipo de consumo
			$movihead->setNota($movement['Tipo']);
		}

		//Eliminar movilin previo
		$this->Movilin->setTransaction($this->_transaction);
		$this->Movilin->deleteAll('comprob="'.$movement['Comprobante'].'" AND almacen="'.$movement['Almacen'].'" AND numero="'.$movement['Numero'].'"');

		//Transformaciones
		$i = 1;
		if ($tipoComprob=='R') {

			$movement['CantidadTarget'] = LocaleMath::round($movement['CantidadTarget'], 3);

			$trasformacion = array(
				'Almacen' => $movement['Almacen'],
				'Item' => $movement['ItemTarget'],
				'Fecha' => $movement['Fecha'],
				'Cantidad' => $movement['CantidadTarget'],
				'Valor' => $movement['VTotal']
			);
			if ($movement['Tipo'] == '1N') {
				$trasformacion['Tipo'] = 'S';
			} else {
				$trasformacion['Tipo'] = 'E';
			}
			$inve = $this->moverSaldos($tipoComprob, $trasformacion);
			$movilin = $this->Movilin->findFirst('comprob="'.$movement['Comprobante'].'" AND almacen="'.$movement['Almacen'].'" AND numero="'.$movement['Numero'].'" AND item="'.$movement['ItemTarget'].'"');
			if ($movilin == false) {
				$movilin = new Movilin();
				$movilin->setComprob($movement['Comprobante']);
				$movilin->setAlmacen($movement['Almacen']);
				$movilin->setNumero($movement['Numero']);
				$movilin->setItem($movement['ItemTarget']);
			}
			$movilin->setTransaction($this->_transaction);
			$movilin->setFecha($movement['Fecha']);
			if ($movement['Tipo']=='1N') {
				$movilin->setCantidad($movement['CantidadTarget'] * -1);
				$movilin->setValor($movement['VTotal'] * -1);
				$movilin->setCosto($movement['VTotal'] / $movement['CantidadTarget'] * -1);
			} else {
				$movilin->setCantidad($movement['CantidadTarget']);
				$movilin->setValor($movement['VTotal']);
				$movilin->setCosto(0);
			}
			$movilin->setNumLinea($i++);
			$movilin->setDescuento(0);
			$movilin->setAlmacenDestino($movement['Almacen']);

			if (!$movement['CantidadTarget']) {
				$movement['CantidadTarget'] = 1;
			}

			$movilin->setNota($movement['Tipo']);
			$movilin->setPrioridad(4);
			$movilin->setIva(0);

			if ($movilin->save()==false) {
				foreach ($movilin->getMessages() as $message) {
					$this->_throwException('Movilin: '.$message->getMessage());
					unset($message);
				}
			}
		}

		//Almacenar el detalle
		$lineas = array();
		$lineasCuentas = array();
		$lineasRetencion = array();
		foreach ($movement['Detail'] as $detail) {

			$inve = BackCacher::getInve($detail['Item']);
			if ($inve==false) {
				$this->_throwException('No existe la referencia "'.$detail['Item'].'"');
			} else {
				if ($inve->getEstado()<>'A') {
					$this->_throwException('La referencia '.$detail['Item'].'/'.$inve->getDescripcion().' está inactiva y no recibe movimientos');
				}
			}

			if (isset($detail['CantidadTragos'])) {
				if ($detail['CantidadTragos'] > 0) {
					if ($inve->getVolumen() <= 0) {
						$this->_throwException('No se ha definido el número de tragos de la referencia '.$detail['Item'].'/'.$inve->getDescripcion(), 10012);
					}
					$detail['Cantidad'] = LocaleMath::round($detail['CantidadTragos']/$inve->getVolumen(), 3);
				} else {
					$detail['Cantidad'] = 0;
				}
			} else {
				$detail['Cantidad'] = LocaleMath::round($detail['Cantidad'], 3);
			}

			$linea = BackCacher::getLinea($movement['Almacen'], $inve->getLinea());
			if ($linea==false) {
				$this->_throwException('La línea de producto asignada a la referencia '.$inve->getItem().'/'.$inve->getDescripcion().' no existe en el almacén '.$movement['Almacen'].'/'.$almacen->getNomAlmacen(), 10013);
			}

			//Busca el movilin
			$movilin = $this->Movilin->findFirst('comprob="'.$movement['Comprobante'].'" AND almacen="'.$movement['Almacen'].'" AND numero="'.$movement['Numero'].'" AND item="'.$detail['Item'].'"');
			if ($movilin==false) {
				$movilin = new Movilin();
				$movilin->setComprob($movement['Comprobante']);
				$movilin->setAlmacen($movement['Almacen']);
				$movilin->setNumero($movement['Numero']);
				$movilin->setItem($detail['Item']);
				$movilin->setFecha($movement['Fecha']);
				$movilin->setNumLinea($i++);
			}
			$movilin->setTransaction($this->_transaction);
			if ($tipoComprob == 'A') {
				if ($detail['Tipo'] == 'SUMAR') {
					$movilin->setCantidad($detail['Cantidad'] + $movilin->getCantidad());
				} else {
					$movilin->setCantidad(-$detail['Cantidad'] + $movilin->getCantidad());
				}
			} else {
				$movilin->setCantidad($detail['Cantidad']+$movilin->getCantidad());
			}

			$movilin->setDescuento(0);

			//La entrada
			//La cantidad recibida en la entrada no puede ser mayor a lo solicitado en la orden
			if (isset($detail['CantidadRecibida'])) {
				if ((double) $detail['CantidadRecibida'] > (double) $detail['Cantidad']) {
					$this->_throwException('La cantidad a entregar no puede superar la cantidad pedida');
				}
			} else {
				//Las entradas deben solicitar CantidadRecibida
				if ($tipoComprob == 'E') {
					$this->_throwException('Debe ingresar la cantidad recibida para la referencia "'.$detail['Item'].'".');
				}
			}

			//Almacen destino solamente es diferente en los traslados
			if (isset($detail['AlmacenDestino']) || $tipoComprob=='T') {
				$movilin->setAlmacenDestino($detail['AlmacenDestino']);
			} else {
				//Para lo que no es traslado es igual al almacén
				$movilin->setAlmacenDestino($movement['Almacen']);
			}

			if (isset($detail['Valor'])) {

				//Solo ajustes permiten valor en 0
				if ($detail['Valor'] == 0) {
					if ($tipoComprob!='A' && $tipoComprob!='C'&&$tipoComprob!='T'&&$tipoComprob!='P') {
						$this->_throwException('No se indicó el valor asociado a la referencia '.$detail['Item']);
					}
				}

				//Traslados y Salidas siempre toman el costo actual
				$costo = 0;
				if ($tipoComprob=='T' || $tipoComprob=='C' || $tipoComprob=='P') {
					$costoItem = self::getCosto($detail['Item'], 'I', $movement['Almacen'], $this);
					$costoItem = LocaleMath::round($costoItem, 2);
					if ($tipoComprob=='T' || $tipoComprob=='C') {
						if ($costoItem<=0) {
							$this->_throwException('No se pudo valorizar la referencia '.$detail['Item'].'/'.$inve->getDescripcion().' porque el costo es cero en el almacén '.$movement['Almacen'].'/'.$almacen->getNomAlmacen(), 10014);
						}
					}
					$detail['Valor'] = $costoItem * $detail['Cantidad'];
					$costo = $costoItem;
				} else {
					if ($tipoComprob=='A') {
						if ($detail['Tipo']=='SUMAR') {
							if ($detail['Cantidad']>0) {
								$costo = $detail['Valor'] / $detail['Cantidad'];
							} else {
								$costo = 0;
							}
						} else {
							$detail['Valor'] = -$detail['Valor'];
							$costo = 0;
						}
					} else {
						if ($tipoComprob=='R') {
							if ($movement['Tipo']=='1N') {
								$detail['Valor'] = $detail['Valor'];
							} else {
								$detail['Valor'] = -$detail['Valor'];
							}
						}
						if (isset($detail['Cantidad']) ) {
							if ($detail['Cantidad']>0) {
								$costo = $detail['Valor'] / $detail['Cantidad'];
							} else {
								$costo = 0;
							}
						}
					}
				}

				//Asignar valor de la línea
				$movilin->setValor($movilin->getValor()+$detail['Valor']);
				$movilin->setCosto($movilin->getCosto()+$costo);
			} else {
				$this->_throwException('No se indicó el valor asociado a la referencia '.$detail['Item']);
			}

			//Al realizar el cierre es necesario que las entradas y traslados tengan más prioridad
			if (isset($detail['Prioridad'])) {
				$movilin->setPrioridad($detail['Prioridad']);
			} else {
				switch ($tipoComprob) {
					case 'E':
						$movilin->setPrioridad(1);
						break;
					case 'T':
						$movilin->setPrioridad(2);
						break;
					case 'C':
						$movilin->setPrioridad(3);
						break;
					case 'R':
					case 'A':
						$movilin->setPrioridad(4);
						break;
					default:
						$movilin->setPrioridad(5);
						break;
				}
			}

			//Iva
			if (isset($detail['Iva'])) {
				if ($movilin->getIva() == 0) {
					$movilin->setIva($detail['Iva']);
				} else {
					if ($movilin->getIva() != $detail['Iva']) {
						$this->_throwException('Se indicó IVAs diferentes en distintas líneas para la referencia '.$detail['Item'], 10015);
					}
				}
			} else {
				$movilin->setIva(0);
			}

			if ($tipoComprob == 'R') {
				$movilin->setNota($movement['Tipo']);
			}

			//Impoconsumo de Costo/Gasto
			if ($inve->getProdTrib() != 'D') {
				if (isset($detail['Iva'])) {
					$movilin->setImpo(LocaleMath::round($movilin->getValor() - ($movilin->getValor() / (1 + $detail['Iva']/100)), 2));
				} else {
					$movilin->setImpo(LocaleMath::round($movilin->getValor() - ($movilin->getValor() / (1 + $movilin->getIva()/100)), 2));
				}
			}

			//$this->_throwException('p' . $movilin->getImpo());

			//Grabar Movilin
			if ($movilin->save() == false) {
				foreach ($movilin->getMessages() as $message) {
					$this->_throwException('Movilin: ' . $message->getMessage());
				}
			}
		}
		//Fin foreach detail

		$ivaIncluido = Settings::get('iva_incluido');

		//Mover saldos y actualizar total
		$total = 0;
		$conditions = 'comprob="' . $movement['Comprobante'] . '" AND almacen="' . $movement['Almacen'] . '" AND numero="' . $movement['Numero'].'"';
		foreach ($this->Movilin->find($conditions) as $movilin) {

			//Entradas a Inventarios
			switch ($tipoComprob) {

				case 'E':
					//Hacer una entrada al inventario
					$entrada = array(
						'Almacen'  => $movement['Almacen'],
						'Fecha'    => $movement['Fecha'],
						'Item'     => $movilin->getItem(),
						'Cantidad' => $movilin->getCantidad(),
						'Valor'    => $movilin->getValor(),
						'Tipo'     => 'E'
					);
					$inve = $this->moverSaldos($tipoComprob, $entrada);

					//Actualizar valor en orden compra
					$ordenc = sprintf('O%02s', $movement['Almacen']);

					$movimiento = array(
						'Almacen' => $movement['Almacen'],
						'Numero' => $movement['Numero'],
						'Comprobante' => $ordenc,
						'Numero' => $movement['NPedido'],
						'Item' => $movilin->getItem(),
						'Cantidad' => $detail['CantidadRecibida'],
						'Impo' => $movilin->getImpo()
					);
					$this->actualizaLineaOrdenPedido($movimiento);

					//Acumular en la línea el valor
					$valor = LocaleMath::round($movilin->getValor(), 2);
					if (!isset($lineas[$inve->getLinea()])) {
						$lineas[$inve->getLinea()] = $valor;
					} else {
						$lineas[$inve->getLinea()] += $valor;
					}

					if ($ivaIncluido == 'S' || !$ivaIncluido) {
						if (isset($detail['Iva'])) {
							$iva = $detail['Iva']/100;
						} else {
							$iva = $movilin->getIva()/100;
						}
						if ($inve->getProdTrib() != 'D') {
							$valorBase = ($valor / (1 + $iva));
						} else {
							$valorBase = $valor;
						}
					} else {
						$valorBase = $valor;
					}

					if (!isset($lineasRetencion[$inve->getLinea()])) {
						$lineasRetencion[$inve->getLinea()] = $valorBase;
					} else {
						$lineasRetencion[$inve->getLinea()] += $valorBase;
					}

					//Impocomsumo Costo/Gasto
					if ($inve->getProdTrib() != 'D') {

						if (!isset($lineasCuentas[$inve->getLinea()])) {
							$lineasCuentas[$inve->getLinea()] = array('cuenta' => 0, 'total' => 0, 'cuentaCompra' => 0);
						}

						$lineaObj = EntityManager::get('Lineas')->findFirst("linea='{$inve->getLinea()}'");
						if ($lineaObj == false) {
							$this->_throwException('No existe la línea de la referencia ' . $inve->getItem());
						}

						if ($inve->getProdTrib() == 'C') {
							$cuentaImpo = $lineaObj->getImpoCosto();
						} else {
							$cuentaImpo = $lineaObj->getImpoGasto();
						}

						$cuentaImpoCruce = $lineaObj->getCtaCompra();
						if (!$cuentaImpoCruce) {
							$this->_throwException('Es necesario asignar la cuenta de Entradas a la línea ' . $inve->getLinea().'/'.$lineaObj->getNombre());
						}
						if (!$cuentaImpo) {
							$this->_throwException('Es necesario asignar las cuentas de impoconsumo del costo y gasto a la línea ' . $inve->getLinea() . '/' . $lineaObj->getNombre());
						}

						$lineasCuentas[$inve->getLinea()]['cuenta'] = $cuentaImpo;
						$lineasCuentas[$inve->getLinea()]['cuentaCompra'] = $cuentaImpoCruce;
						$lineasCuentas[$inve->getLinea()]['total'] += $movilin->getImpo();
						//$lineasCuentas[$inve->getLinea()]['total'] += LocaleMath::round(($valor*$inve->getIva()/100), 2);
						//$lineasCuentas[$inve->getLinea()]['total'] += LocaleMath::round(($valor*$detail['Iva']/100), 2);
					}
					break;

				case 'C':
					//Salida
					$consumo = array(
						'Almacen' => $movement['Almacen'],
						'Numero' => $movement['Numero'],
						'Fecha' => $movement['Fecha'],
						'Item' => $movilin->getItem(),
						'Cantidad' => $movilin->getCantidad(),
						'Valor' => $movilin->getValor(),
						'Tipo' => 'S'
					);
					$inve = $this->moverSaldos($tipoComprob, $consumo);

					//Actualizar cantidades en pedido
					$pedido = sprintf('P%02s', $movement['Almacen']);
					$movimiento = array(
						'Almacen' => $movement['Almacen'],
						'Numero' => $movement['NPedido'],
						'Comprobante' => $pedido,
						'Item' => $movilin->getItem(),
						'Cantidad' => $movilin->getCantidad()
					);
					$this->actualizaLineaOrdenPedido($movimiento);

					$valor = LocaleMath::round($movilin->getValor(), 2);
					if (!isset($lineas[$inve->getLinea()])) {
						$lineas[$inve->getLinea()] = $valor;
					} else {
						$lineas[$inve->getLinea()]+= $valor;
					}
					break;

				case 'A':
					$ajuste = array(
						'Almacen' => $movement['Almacen'],
						'Item' => $movilin->getItem(),
						'Fecha' => $movement['Fecha'],
						'Cantidad' => $movilin->getCantidad(),
						'Valor' => $movilin->getValor()
					);
					if ($detail['Tipo'] == 'SUMAR') {
						$ajuste['Tipo'] = 'E';
					} else {
						$ajuste['Tipo'] = 'S';
					}

					$inve = $this->moverSaldos($tipoComprob, $ajuste);
					if ($movilin->getValor() != 0) {
						$valor = LocaleMath::round($movilin->getValor(), 2);
						if ($detail['Tipo'] == 'SUMAR') {
							if (!isset($lineas['Entrada'][$inve->getLinea()])) {
								$lineas['Entrada'][$inve->getLinea()] = $movilin->getValor();
							} else {
								$lineas['Entrada'][$inve->getLinea()] += $movilin->getValor();
							}
						} else {
							if (!isset($lineas['Salida'][$inve->getLinea()])) {
								$lineas['Salida'][$inve->getLinea()] = $movilin->getValor();
							} else {
								$lineas['Salida'][$inve->getLinea()] += $movilin->getValor();
							}
						}
					}
					break;

				case 'R':
					//Transformacion
					$trasformacion = array(
						'Almacen' => $movement['Almacen'],
						'Fecha' => $movement['Fecha'],
						'Item' => $movilin->getItem(),
						'Cantidad' => $movilin->getCantidad(),
						'Valor' => $movilin->getValor()
					);
					if ($movement['Tipo']=='1N') {
						$trasformacion['Tipo'] = 'E';
					} else {
						$trasformacion['Tipo'] = 'S';
						$movilin->setCantidad($movilin->getCantidad() * -1);
					}
					$inve = $this->moverSaldos($tipoComprob, $trasformacion);
					break;

				case 'T':
					$traslado = array(
						'Almacen' => $movement['Almacen'],
						'Fecha' => $movement['Fecha'],
						'Item' => $movilin->getItem(),
						'Cantidad' => $movilin->getCantidad(),
						'Valor' => $movilin->getValor(),
						'AfectaConsolidado' => false,
						'Tipo' => 'S'
					);
					$inve = $this->moverSaldos($tipoComprob, $traslado);

					$traslado = array(
						'Almacen' => $movement['AlmacenDestino'],
						'Fecha' => $movement['Fecha'],
						'Item' => $movilin->getItem(),
						'Cantidad' => $movilin->getCantidad(),
						'Valor' => $movilin->getValor(),
						'AfectaConsolidado' => false,
						'Tipo' => 'E'
					);
					$inve = $this->moverSaldos($tipoComprob, $traslado);

					$pedido = sprintf('P%02s', $movement['Almacen']);
					$movimiento = array(
						'Almacen' => $movement['Almacen'],
						'Numero' => $movement['NPedido'],
						'Comprobante' => $pedido,
						'Item' => $movilin->getItem(),
						'Cantidad' => $movilin->getCantidad()
					);
					$this->actualizaLineaOrdenPedido($movimiento);

					$valor = LocaleMath::round($movilin->getValor(), 2);
					if (!isset($lineas[$inve->getLinea()])) {
						$lineas[$inve->getLinea()] = $valor;
					} else {
						$lineas[$inve->getLinea()]+= $valor;
					}

					$movilinOrigen = clone $movilin;
					$movilinOrigen->setAlmacen($movement['Almacen']);
					//$movilinOrigen->setAlmacenDestino($movement['Almacen']);
					$movilinOrigen->setAlmacenDestino($movement['AlmacenDestino']);

					//Grabar Movilin Origen
					if ($movilinOrigen->save()==false) {
						foreach ($movilinOrigen->getMessages() as $message) {
							$this->_throwException('Movilin-Destino: '.$message->getMessage());
						}
					}
					break;
			}

			$total += $movilin->getValor();
			unset($movilin);
		}
		$movihead->setTotalNeto($total);

		unset($detail);

		//throw new TaticoException(print_r($movement['Totales'],true));

		//Calcular Total de Impuestos
		if ($tipoComprob=='O' || $tipoComprob=='E') {

			//Si se definieron los totales se asignan a movihead y movih1
			if (isset($movement['Totales'])) {

				$movihead->setRetencion($movement['Totales']['Retencion']);
				$movihead->setIca($movement['Totales']['Ica']);
				$movihead->setIva($movement['Totales']['Iva16D']);
				$movihead->setDescuento($movement['Totales']['Iva16R']);
				$movihead->setIvad($movement['Totales']['Iva10D']);
				$movihead->setIvam($movement['Totales']['Iva10R']);
				$movihead->setCree($movement['Totales']['Cree']);
				$movihead->setImpo($movement['Totales']['Impo']);
				if (isset($movement['Totales']['Iva5D'])) {
					$movih1->setIva5($movement['Totales']['Iva5D']);
				} else {
					$movih1->setIva5(0);
				}
				if (isset($movement['Totales']['Iva5R'])) {
					$movih1->setRetiva5($movement['Totales']['Iva5R']);
				} else {
					$movih1->setRetiva5(0);
				}
				if ($movement['Totales']['Horti']) {
					$movih1->setReten1($movement['Totales']['Horti']);
				} else {
					$movih1->setReten1(0);
				}
				if (isset($movement['Totales']['Cree'])) {
					$movih1->setCree($movement['Totales']['Cree']);
				} else {
					$movih1->setCree(0);
				}
				if (isset($movement['Totales']['Impo'])) {
					$movih1->setImpo($movement['Totales']['Impo']);
				} else {
					$movih1->setImpo(0);
				}
			} else {
				$fields = array(
					'retencion' => 'retencion',
					'ica' => 'ica',
					'iva' => 'iva16d',
					'descuento' => 'iva16r',
					'ivad' => 'iva10d',
					'ivam' => 'iva10r',
					'cree' => 'cree',
					'impo' => 'impo',
					'total_neto' => 'total_neto',
					'saldo' => 'saldo',
				);
				$response = self::getTaxes($movement['Detail'], $movement['Almacen'], $tipoComprob, $movement['Nit']);
				if ($response['status'] == 'OK') {
					foreach ($fields as $field => $nameField) {
						$movihead->writeAttribute($field, $response[$nameField]);
						unset($nameField);
					}
					$fields = array(
						'iva5' => 'iva5d',
						'retiva5' => 'iva5r',
						'reten1' => 'horti',
						'cree' => 'cree',
						'impo' => 'impo'
					);
					foreach ($fields as $field => $nameField) {
						$movih1->writeAttribute($field, $response[$nameField]);
						unset($nameField);
					}
				} else {
					$this->_throwException($response['message']);
				}
			}
			if ($movih1->save() == false) {
				foreach ($movih1->getMessages() as $message) {
					$this->_throwException('Movih1: '.$message->getMessage());
					unset($message);
				}
			}

			$impuestos = ($movihead->getIva()+$movihead->getIvad()+$movih1->getIva5()) - ($movihead->getDescuento()+$movihead->getIvam()+$movih1->getRetiva5()) - ($movih1->getCree()+$movih1->getReten1()+$movihead->getRetencion()+$movihead->getIca());
			$impuestos = LocaleMath::round($impuestos, 2);
			$movihead->setSaldo(-$impuestos);
			$movihead->setVTotal($movihead->getTotalNeto()+$impuestos);

			//Grabar criterios en la orden y en la entrada
			if (isset($movement['Criterios'])) {

				$criterioPuntos = Settings::get('criterio_puntos');
				$criterioPuntos = Settings::get('criterio_puntos');
				if ($criterioPuntos == 'N') {
					$criterio = new Criterio();
					$criterio->setTransaction($this->_transaction);
					$criterio->setComprob($movement['Comprobante']);
					$criterio->setNumero($movement['Numero']);
					$criterio->setAlmacen($movement['Almacen']);
					foreach ($movement['Criterios'] as $criterioId => $valor) {
						$criterios = $this->Criterios->findFirst($criterioId);
						if ($criterios==false) {
							$this->_throwException('No existe el criterio '.$criterioId);
						}
						$prefijo = strtolower($criterios->getPrefijo());
						if ($criterio->hasField($prefijo)) {
							$criterio->writeAttribute($prefijo, $valor);
						}
						unset($valor);
					}
					if ($criterio->save()==false) {
						foreach ($criterio->getMessages() as $message) {
							$this->_throwException('Criterio: ' . $message->getMessage());
							unset($message);
						}
					}
				} else {
					foreach ($movement['Criterios'] as $criterioId => $puntaje) {
						$criterio = new CriteriosProveedores();
						$criterio->setTransaction($this->_transaction);
						$criterio->setComprob($movement['Comprobante']);
						$criterio->setNumero($movement['Numero']);
						$criterio->setAlmacen($movement['Almacen']);
						$criterio->setNit($movement['Nit']);
						$criterio->setCriteriosId($criterioId);
						$criterio->setPuntaje((double) $puntaje);
						if ($criterio->save()==false) {
							foreach ($criterio->getMessages() as $message)
							{
								$this->_throwException('Criterio-Proveedor: '.$message->getMessage());
								unset($message);
							}
						}
						unset($puntaje, $criterio);
					}
				}
			}
		} else {
			$movihead->setVTotal($movihead->getTotalNeto());
		}

		//Contabilización de la Entrada
		if ($tipoComprob == 'E') {

			//Cerrar la orden de compra y obtener la forma de pago
			$this->_cerrarOrden($movement, $movihead);

			//Contabilización de la entrada
			$options = array();
			$options['Descripcion'] = 'FXP No. ' . $movement['FacturaC'];
			$options['Fecha'] = $movement['Fecha'];
			$options['Nit'] = $movement['Nit'];
			$options['FacturaC'] = $movement['FacturaC'];
			$options['CentroCosto'] = $almacen->getCentroCosto();

			$valorTotal = 0;
			$saldoCartera = 0;
			$totalRetencion = 0;
			$totalHorti = 0;
			$totalCree = 0;
			$totalImpo = 0;
			$cuentasDebitos = array();
			$cuentasCreditos = array();
			$cuentasHorti = array();
			$cuentasCreeArray = array();
			$cuentasRetencion = array();
			$cuentasRetencionLinea = array();
			$hortifruticula = Settings::get('hortifruticula');
			$usarRetecompras = Settings::get('usar_retecompras','IN');
			$tercero = BackCacher::getTercero($options['Nit']);

			foreach ($lineas as $codigoLinea => $valor) {

				$linea = BackCacher::getLinea($movement['Almacen'], $codigoLinea);
				if ($linea==false) {
					$this->_throwException('No existe la línea de producto ' . $linea . '  en el almacén '.$movement['Almacen']);
				}

				$codigoCuentaCompras = $linea->getCtaCompra();
				$cuentaCompras = BackCacher::getCuenta($codigoCuentaCompras);
				if ($cuentaCompras == false) {
					$this->_throwException('La cuenta de entradas asignada a la línea "' . $codigoLinea . '" del almacén '.$movement['Almacen'].' no existe', 10016);
				} else {
					if ($cuentaCompras->getEsAuxiliar()!='S') {
						$this->_throwException('La cuenta de entradas asignada a la línea "'. $codigoLinea . '" del almacén '.$movement['Almacen'].' no es auxiliar', 10016);
					}
				}

				//Cuenta Inventario
				if (!isset($cuentasDebitos[$codigoCuentaCompras])) {
					$cuentasDebitos[$codigoCuentaCompras] = $valor;
				} else {
					$cuentasDebitos[$codigoCuentaCompras] += $valor;
				}
				$saldoCartera += $valor;

				//Retención Hortifrutícula
				if ($hortifruticula == 'S') {
					if ($movih1->getReten1() > 0) {
						if ($tercero->getPlazo() != 'S') {
							if ($linea->getPorcHortic()>0) {
								$codigoCuentaHorti = $linea->getCtaHortic();
								$cuentaRetencion = BackCacher::getCuenta($codigoCuentaHorti);
								if ($cuentaRetencion==false) {
									$this->_throwException('La cuenta de retención hortífruticula asignada a la línea "'.$codigoLinea.'" al almacén '.$movement['Almacen'].' no existe');
								} else {
									if ($cuentaRetencion->getEsAuxiliar()!='S') {
										$this->_throwException('La cuenta de retención hortífruticula asignada a la línea "'.$codigoLinea.'" al almacén '.$movement['Almacen'].' no es auxiliar');
									}
								}

								$valorHorti = LocaleMath::round($valor * $linea->getPorcHortic(), 2);
								if (!isset($cuentasCreditos[$codigoCuentaHorti])) {
									$cuentasCreditos[$codigoCuentaHorti] = $valorHorti;
								} else {
									$cuentasCreditos[$codigoCuentaHorti]+= $valorHorti;
								}
								$totalHorti += $valorHorti;
								$cuentasHorti[$codigoCuentaHorti] = true;
							}
						}
					}
				}

				//Retención CREE
				$porceCree = 0;
				if ($movih1->getCree() > 0) {
					$porceCree = $tercero->getPorceCree();
					if ($porceCree > 0) {
						$cuentasCree = EntityManager::get('CuentasCree')->findFirst("porce='$porceCree'");
						if ($cuentasCree == false) {
							$this->_throwException('No se ha creado el porcentaje de CREE en la opción Cuentas CREE');
						}

						$codigoCuentaCree = $cuentasCree->getCuenta();

						$valorCree = LocaleMath::round($valor * $porceCree, 2);
						if (!isset($cuentasCreditos[$codigoCuentaCree])) {
							$cuentasCreditos[$codigoCuentaCree] = $valorCree;
						} else {
							$cuentasCreditos[$codigoCuentaCree]+= $valorCree;
						}
						$totalCree += $valorCree;
						$cuentasCreeArray[$codigoCuentaCree] = true;
					}
				}

				$valorTotal += $valor;
				unset($valor);
			}

			foreach ($lineasRetencion as $codigoLinea => $valor) {

				$porcCompra = 0;
				$codigoCuentaRetencion = 0;

				//Retención
				if ($movihead->getRetencion() > 0) {
					if ($tercero->getAutoRet() == 'N') {
						if ($usarRetecompras == "S" || $linea->getPorcCompra()>0) {

							//Usa la cuenta de retencion de retecompras
							if ($tercero->getRetecomprasId() > 0) {
								$retecompras = EntityManager::get('Retecompras')->findFirst($tercero->getRetecomprasId());
								if (!$retecompras) {
									$this->_throwException("No se ha definido una retencion de compras en el tercero correctamente");
								}
								$codigoCuentaRetencion = $retecompras->getCuenta();
								$porcCompra = $retecompras->getPorceRetencion() / 100;
							}

							$cuentaRetencion = BackCacher::getCuenta($codigoCuentaRetencion);
							if ($cuentaRetencion == false) {
								$this->_throwException('La cuenta de retención asignada a la línea "'.$codigoLinea.'" al almacen '.$movement['Almacen'].' no existe', 10017);
							} else {
								if ($cuentaRetencion->getEsAuxiliar() != 'S') {
									if ($usarRetecompras=="N") {
										$msgEA = 'La cuenta de retención asignada a la línea "' . $codigoLinea . '" al almacen '.$movement['Almacen'].' no es auxiliar';
									} else {
										$msgEA = 'La cuenta de retención asignada al tercero en el formulario "Retención de Compras" con código"'.$retecompras->getCodigo().'/'.$retecompras->getDescripcion().'" no es auxiliar';
									}
									$this->_throwException($msgEA, 10017);
								}
							}

							$valorRetencion = LocaleMath::round($valor * $porcCompra, 2);

							if (!isset($cuentasCreditos[$codigoCuentaRetencion])) {
								$cuentasCreditos[$codigoCuentaRetencion] = $valorRetencion;
							} else {
								$cuentasCreditos[$codigoCuentaRetencion] += $valorRetencion;
							}

							$totalRetencion += $valorRetencion;
							$cuentasRetencion[$codigoCuentaRetencion] = true;
							$cuentasRetencionLinea[$codigoCuentaRetencion][$linea->getLinea() . ' / ' . $linea->getNombre()] = true;
						}
					}
				}


			}

			//Ajustar retención
			if ($movihead->getRetencion() > 0) {
				$cuentasRetencion = array_keys($cuentasRetencion);
				if (count($cuentasRetencion) == 1) {
					$cuentasCreditos[$cuentasRetencion[0]] -= $totalRetencion;
					$cuentasCreditos[$cuentasRetencion[0]] += $movihead->getRetencion();
					$saldoCartera -= $movihead->getRetencion();
				} else {
					if ($totalRetencion != $movihead->getRetencion()) {
						$message = 'La contabilización afecta cuentas diferentes de retención, el total de retención
						digitado no es igual al calculado por el sistema. No es posible hacer la contabilización.
						Total Digitado=' . $movihead->getRetencion() . ' y Total Calculado=' . $totalRetencion . '.';

						$cuentasLinea = array();
						$locale = Locale::getApplication();
						foreach ($cuentasRetencionLinea as $codigoCuenta => $linea) {
							$cuentasLinea[] = $codigoCuenta . ' en ' . $locale->getConjunction(array_keys($linea));
							unset($linea);
						}
						$this->_throwException($message . ' Cuentas Contables: ' . join(', ', $cuentasLinea));

					}
					$saldoCartera-=$totalRetencion;
				}
			}

			//Ajustar retención hortífrutícula
			if ($hortifruticula=='S') {
				if ($movih1->getReten1()>0) {
					if ($tercero->getPlazo()!='S') {
						$cuentasHorti = array_keys($cuentasHorti);
						if (count($cuentasHorti)==1) {
							$cuentasCreditos[$cuentasHorti[0]]-=$totalHorti;
							$cuentasCreditos[$cuentasHorti[0]]+=$movih1->getReten1();
							$saldoCartera-=$movih1->getReten1();
						} else {
							if ($totalHorti!=$movih1->getReten1()) {
								$this->_throwException('La contabilización afecta cuentas diferentes de retención hortifrutícula, el total de retención digitado no es igual al calculado por el sistema. No es posible hacer la contabilización. Las cuentas son: '.join(', ', $cuentasHorti));
							}
							$saldoCartera-=$totalHorti;
						}
					}
				}
			}

			//Ajustar retención cree
			if ($movih1->getCree()>0) {
				$cuentasCreeA = array_keys($cuentasCreeArray);
				if (count($cuentasCreeA)==1) {
					$cuentasCreditos[$cuentasCreeA[0]] -= $totalCree;
					$cuentasCreditos[$cuentasCreeA[0]] += $movih1->getCree();
					$saldoCartera-=$movih1->getCree();
				} else {
					if ($totalCree!=$movih1->getCree()) {
						$this->_throwException('La contabilización afecta cuentas diferentes de retención cree, el total de retención digitado no es igual al calculado por el sistema. No es posible hacer la contabilización. Las cuentas son: '.join(', ', $cuentasHorti));
					}
					$saldoCartera-=$totalCree;
				}
			}

			//Cuentas x Tipo Regímen
			$regimenCuentas = $this->RegimenCuentas->findFirst("regimen='{$tercero->getEstadoNit()}'");
			if ($regimenCuentas==false) {
				$this->_throwException('No se han definido las cuentas de contabilización para el regímen "'.$tercero->getTipoRegimen().'"');
			}

			//IVA Descontable 16%
			if ($movihead->getIva() > 0) {

				$cuentaIva16D = $regimenCuentas->getCtaIva16d();
				$cuentaIva = BackCacher::getCuenta($cuentaIva16D);
				if ($cuentaIva==false) {
					$this->_throwException('La cuenta de IVA 16% descontable asociada al comprobante "'.$movement['Comprobante'].'" no existe');
				} else {
					if ($cuentaIva->getEsAuxiliar()!='S') {
						$this->_throwException('La cuenta de IVA 16% descontable asociada al comprobante "'.$movement['Comprobante'].'" no es auxiliar');
					}
				}

				if (!isset($cuentasDebitos[$cuentaIva16D])) {
					$cuentasDebitos[$cuentaIva16D] = $movihead->getIva();
				} else {
					$cuentasDebitos[$cuentaIva16D]+= $movihead->getIva();
				}

				$saldoCartera += $movihead->getIva();
			}

			//IVA Descontable 10%
			if ($movihead->getIvad() > 0) {
				$cuentaIva10D = $regimenCuentas->getCtaIva10d();
				$cuentaIva = BackCacher::getCuenta($cuentaIva10D);
				if ($cuentaIva == false) {
					$this->_throwException('La cuenta de IVA 10% descontable asociada al comprobante "'.$movement['Comprobante'].'" no existe');
				} else {
					if ($cuentaIva->getEsAuxiliar() != 'S') {
						$this->_throwException('La cuenta de IVA 10% descontable asociada al comprobante "'.$movement['Comprobante'].'" no es auxiliar');
					}
				}
				if (!isset($cuentasDebitos[$cuentaIva10D])) {
					$cuentasDebitos[$cuentaIva10D] = $movihead->getIvad();
				} else {
					$cuentasDebitos[$cuentaIva10D]+= $movihead->getIvad();
				}
				$saldoCartera += $movihead->getIvad();
			}

			//IVA Descontable 5%
			if ($movih1->getIva5() > 0) {
				$cuentaIva5D = $regimenCuentas->getCtaIva5d();
				$cuentaIva = BackCacher::getCuenta($cuentaIva5D);
				if ($cuentaIva==false) {
					$this->_throwException('La cuenta de IVA 5% descontable asociada al comprobante "'.$movement['Comprobante'].'" no existe');
				} else {
					if ($cuentaIva->getEsAuxiliar()!='S') {
						$this->_throwException('La cuenta de IVA 5% descontable asociada al comprobante "'.$movement['Comprobante'].'" no es auxiliar');
					}
				}
				if (!isset($cuentasDebitos[$cuentaIva5D])) {
					$cuentasDebitos[$cuentaIva5D] = $movih1->getIva5();
				} else {
					$cuentasDebitos[$cuentaIva5D]+= $movih1->getIva5();
				}
				$saldoCartera += $movih1->getIva5();
			}

			//IVA Retenido 16%
			$calcularIVARetenido = false;
			if ($terceroHotel->getEstadoNit()=='C') {
				if ($tercero->getEstadoNit()=='S') {
					$calcularIVARetenido = true;
				}
			} else {
				if ($terceroHotel->getEstadoNit()=='G') {
					if ($tercero->getEstadoNit()=='S'||$tercero->getEstadoNit()=='C') {
						$calcularIVARetenido = true;
					}
				}
			}

			if ($calcularIVARetenido) {
				if ($movihead->getDescuento() > 0) {
					$cuentaIva16R = $regimenCuentas->getCtaIva16r();
					$cuentaIva = BackCacher::getCuenta($cuentaIva16R);
					if ($cuentaIva==false) {
						$this->_throwException('La cuenta de IVA 16% retenido asociada al comprobante "'.$movement['Comprobante'].'" no existe');
					} else {
						if ($cuentaIva->getEsAuxiliar()!='S') {
							$this->_throwException('La cuenta de IVA 16% retenido asociada al comprobante "'.$movement['Comprobante'].'" no es auxiliar');
						}
					}
					if (!isset($cuentasCreditos[$cuentaIva16R])) {
						$cuentasCreditos[$cuentaIva16R] = $movihead->getDescuento();
					} else {
						$cuentasCreditos[$cuentaIva16R]+= $movihead->getDescuento();
					}
					$saldoCartera-=$movihead->getDescuento();
				}
			}

			//IVA Retenido 10%
			if ($calcularIVARetenido) {
				if ($movihead->getIvam()>0) {
					$cuentaIva10R = $regimenCuentas->getCtaIva10r();;
					$cuentaIva = BackCacher::getCuenta($cuentaIva10R);
					if ($cuentaIva==false) {
						$this->_throwException('La cuenta de IVA 10% retenido asociada al comprobante "'.$movement['Comprobante'].'" no existe');
					} else {
						if ($cuentaIva->getEsAuxiliar()!='S') {
							$this->_throwException('La cuenta de IVA 10% retenido asociada al comprobante "'.$movement['Comprobante'].'" no es auxiliar');
						}
					}
					if (!isset($cuentasCreditos[$cuentaIva10R])) {
						$cuentasCreditos[$cuentaIva10R] = $movihead->getIvam();
					} else {
						$cuentasCreditos[$cuentaIva10R]+= $movihead->getIvam();
					}
					$saldoCartera-=$movihead->getIvam();
				}
			}

			//IVA Retenido 7%
			if ($calcularIVARetenido) {
				if ($movih1->getRetiva5()>0) {
					$cuentaIva10R = $regimenCuentas->getCtaIva10r();;
					$cuentaIva = BackCacher::getCuenta($cuentaIva10R);
					if ($cuentaIva==false) {
						$this->_throwException('La cuenta de IVA 10% retenido asociada al comprobante "'.$movement['Comprobante'].'" no existe');
					} else {
						if ($cuentaIva->getEsAuxiliar()!='S') {
							$this->_throwException('La cuenta de IVA 10% retenido asociada al comprobante "'.$movement['Comprobante'].'" no es auxiliar');
						}
					}
					if (!isset($cuentasCreditos[$cuentaIva10R])) {
						$cuentasCreditos[$cuentaIva10R] = $movih1->getRetiva5();
					} else {
						$cuentasCreditos[$cuentaIva10R]+= $movih1->getRetiva5();
					}
					$saldoCartera-=$movih1->getRetiva5();
				}
			}

			//ImpoConsumo Costo/Gasto
			//throw new TaticoException(print_r($lineasCuentas,true));
			/*foreach ($lineasCuentas as $linea => $lineaCuenta) {

				$cuentaImpo = $lineaCuenta['cuenta'];
				$cuentaImpoCruce = $lineaCuenta['cuentaCompra'];
				$valorImpo = $lineaCuenta['total'];

				//$this->_throwException($cuentaImpo);

				if (!isset($cuentasDebitos[$cuentaImpo])) {
					$cuentasDebitos[$cuentaImpo] = $valorImpo;
				} else {
					$cuentasDebitos[$cuentaImpo] += $valorImpo;
				}
				$saldoCartera += $valorImpo;
			}*/

			//ICA
			if ($tercero->getAutoRet()=='N') {
				if ($movihead->getIca() > 0) {
					if ($tercero->getApAereo() > 0) {

						$ica = self::getModel('Ica')->findFirst("codigo='{$tercero->getApAereo()}'");
						if ($ica==false) {
							$this->_throwException('El porcentaje de ICA de '.$tercero->getApAereo().'% no está definido en el sistema');
						}

						$codigoCuentaIca = $ica->getCuenta();
						$cuentaIca = BackCacher::getCuenta($codigoCuentaIca);
						if ($cuentaIca==false) {
							$this->_throwException('La cuenta de ICA asociada al porcentaje '.$tercero->getApAereo().' no existe');
						} else {
							if ($cuentaIca->getEsAuxiliar()!='S') {
								$this->_throwException('La cuenta de ICA asociada al porcentaje '.$tercero->getApAereo().' no es auxiliar');
							}
						}

						if (!isset($cuentasCreditos[$codigoCuentaIca])) {
							$cuentasCreditos[$codigoCuentaIca] = $movihead->getIca();
						} else {
							$cuentasCreditos[$codigoCuentaIca]+= $movihead->getIca();
						}

						$saldoCartera-=$movihead->getIca();
					}
				}
			}

			//Cartera
			$formaPago = self::getModel('FormaPago')->findFirst("codigo='{$movihead->getFormaPago()}'");
			if ($formaPago==false) {
				$this->_throwException('La forma de pago no existe'."codigo='{$movihead->getFormaPago()}'");
			}

			$codigoCuentaCartera = $formaPago->getCtaContable();
			$cuentaCartera = BackCacher::getCuenta($codigoCuentaCartera);
			if ($cuentaCartera==false) {
				$this->_throwException('La cuenta contable asociada a la forma de pago "'.$formaPago->getDescripcion().'" no existe');
			} else {
				if ($cuentaCartera->getEsAuxiliar()!='S') {
					$this->_throwException('La cuenta contable asociada a la forma de pago "'.$formaPago->getDescripcion().'" no es auxiliar');
				}
			}

			$options['CuentaCartera'] = $codigoCuentaCartera;

			if (!isset($cuentasCredito[$codigoCuentaCartera])) {
				$cuentasCreditos[$codigoCuentaCartera] = $saldoCartera;
			} else {
				$cuentasCreditos[$codigoCuentaCartera]+= $saldoCartera;
			}

			$movihead->setSaldo($saldoCartera);
			$movihead->setNumeroComprobContab($this->_contabilizaMovement($options, $cuentasDebitos, $cuentasCreditos));
		}

		//Contabilización del traslado
		if ($tipoComprob == 'T') {

			self::_cerrarPedido($this->_transaction, $movement);

			$options['Descripcion'] = 'TRALADO ALMACEN No. '.$movihead->getComprob().'-'.$movihead->getNumero();
			$options['Fecha'] = $movement['Fecha'];
			if (isset($movement['Nit'])) {
				$options['Nit'] = $movement['Nit'];
			} else {
				$options['Nit'] = $this->_empresa->getNit();
			}
			$options['CentroCosto'] = $almacen->getCentroCosto();

			$cuentasDebitos = array();
			$cuentasCreditos = array();
			foreach ($lineas as $codigoLinea => $valor) {

				$lineaTraslado = BackCacher::getLinea($movement['Almacen'], $codigoLinea);
				if ($lineaTraslado==false) {
					$this->_throwException('No existe la línea de producto '.$codigoLinea.' en el almacén '.$movement['Almacen']);
				}

				$codigoCuentaInventario = $lineaTraslado->getCtaInve();
				$cuentaInventario = BackCacher::getCuenta($codigoCuentaInventario);
				if ($cuentaInventario ==false) {
					$this->_throwException('La cuenta de traslado de la línea '.$codigoLinea.' en el almacén "'.$movement['Almacen'].'" no existe');
				} else {
					if ($cuentaInventario ->getEsAuxiliar()!='S') {
						$this->_throwException('La cuenta de traslado de la línea '.$codigoLinea.' en el almacén "'.$movement['Almacen'].'" no existe');
					}
				}

				if (!isset($cuentasDebitos[$codigoCuentaInventario])) {
					$cuentasDebitos[$codigoCuentaInventario] = $valor;
				} else {
					$cuentasDebitos[$codigoCuentaInventario]+= $valor;
				}

				$codigoCuentaDevCompras = $lineaTraslado->getCtaDevCompras();
				$cuentaDevCompras = BackCacher::getCuenta($codigoCuentaDevCompras);
				if ($cuentaDevCompras==false) {
					$this->_throwException('La cuenta de devolución compras de la línea '.$codigoLinea.' en el almacén "'.$movement['Almacen'].'" no existe');
				} else {
					if ($cuentaDevCompras->getEsAuxiliar()!='S') {
						$this->_throwException('La cuenta de devolución compras de la línea '.$codigoLinea.' en el almacén "'.$movement['Almacen'].'" no es auxiliar');
					}
				}
				if (!isset($cuentasCreditos[$codigoCuentaDevCompras])) {
					$cuentasCreditos[$codigoCuentaDevCompras] = $valor;
				} else {
					$cuentasCreditos[$codigoCuentaDevCompras]+= $valor;
				}
				unset($valor);
			}

			if ($movement['Almacen']==1) {
				$tmp = $cuentasDebitos;
				$cuentasDeb = $cuentasCreditos;
				$cuentasCreditos = $tmp;
			}

			$movihead->setNumeroComprobContab($this->_contabilizaMovement($options, $cuentasDebitos, $cuentasCreditos));
		}

		//Contabilizacion de Salidas
		if ($tipoComprob == 'C') {

			self::_cerrarPedido($this->_transaction, $movement);

			$options['Descripcion'] = 'SALIDA POR CONSUMO NO. '.$movihead->getNumero();
			$options['Fecha'] = $movement['Fecha'];
			if (isset($movement['Nit'])) {
				$options['Nit'] = $movement['Nit'];
			} else {
				$options['Nit'] = $this->_empresa->getNit();
			}
			$options['CentroCosto'] = $movement['CentroCosto'];

			$cuentasDebitos = array();
			$cuentasCreditos = array();
			foreach ($lineas as $linea => $valor) {

				$lineaSalida = BackCacher::getLinea($movement['Almacen'], $linea);
				if ($lineaSalida==false) {
					$this->_throwException('No existe la línea de producto '.$linea.' en el almacén '.$movement['Almacen']);
				}

				//Contabilizar costo ó gasto
				if ($movement['Tipo']=='E') {
					$codigoCuenta = $lineaSalida->getCtaCostoVenta();
				} else {
					$codigoCuenta = $lineaSalida->getCtaConsumo();
				}

				$cuenta = BackCacher::getCuenta($codigoCuenta);
				if ($cuenta==false) {
					if ($movement['Tipo']=='E') {
						$this->_throwException('La cuenta de costo de la línea '.$linea.' en el almacén '.$movement['Almacen'].' no existe');
					} else {
						if ($movement['Tipo']=='I') {
							$this->_throwException('La cuenta de gasto de la línea '.$linea.' en el almacén '.$movement['Almacen'].' no existe');
						}
					}
				} else {
					if ($cuenta->getEsAuxiliar()!='S') {
						if ($movement['Tipo']=='E') {
							$this->_throwException('La cuenta de costo de la línea '.$linea.' en el almacén '.$movement['Almacen'].' no es auxiliar');
						} else {
							if ($movement['Tipo']=='I') {
								$this->_throwException('La cuenta de gasto de la línea '.$linea.' en el almacén '.$movement['Almacen'].' no es auxiliar');
							}
						}
					}
				}

				if (!isset($cuentasDebitos[$codigoCuenta])) {
					$cuentasDebitos[$codigoCuenta] = $valor;
				} else {
					$cuentasDebitos[$codigoCuenta]+= $valor;
				}

				$codigoCuentaInve = $lineaSalida->getCtaInve();
				$cuenta = BackCacher::getCuenta($codigoCuentaInve);
				if ($cuenta==false) {
					$this->_throwException('La cuenta de inventarios de la línea '.$linea.' en el almacén '.$movement['Almacen'].'" no existe');
				} else {
					if ($cuenta->getEsAuxiliar()!='S') {
						$this->_throwException('La cuenta de inventarios de la línea '.$linea.' en el almacén '.$movement['Almacen'].'" no es auxiliar');
					}
				}

				if (!isset($cuentasCreditos[$codigoCuentaInve])) {
					$cuentasCreditos[$codigoCuentaInve] = $valor;
				} else {
					$cuentasCreditos[$codigoCuentaInve]+= $valor;
				}
				unset($valor);
			}

			$movihead->setNumeroComprobContab($this->_contabilizaMovement($options, $cuentasDebitos, $cuentasCreditos));
		}

		if ($movement['Comprobante'][0] == 'R') {
			$movihead->setVTotal($movement['VTotal']);
		}

		//Contabilización del Ajuste
		if ($tipoComprob == 'A') {

			$options = array();
			$options['Descripcion'] = 'AJUSTE '.$movihead->getComprob().'-'.$movihead->getNumero();
			$options['Fecha'] = $movement['Fecha'];
			$options['Nit'] = $this->_empresa->getNit();
			$options['CentroCosto'] = $almacen->getCentroCosto();

			file_put_contents('a.txt', print_r($lineas, true));

			if (isset($lineas['Entrada']) || isset($lineas['Salida'])) {
				$cuentasDebito = array();
				$cuentasCredito = array();
				if (isset($lineas['Entrada'])) {

					foreach ($lineas['Entrada'] as $linea => $valor) {

						if ($valor) {

							$lineaSalida = BackCacher::getLinea($movement['Almacen'], $linea);
							if ($lineaSalida==false) {
								$this->_throwException('No existe la línea ' . $linea . ' en el almacén ' . $movement['Almacen']);
							}

							$codigoCuentaConsumo = $lineaSalida->getCtaConsumo();
							$cuentaConsumo = BackCacher::getCuenta($codigoCuentaConsumo);
							if ($cuentaConsumo == false) {
								$this->_throwException('La cuenta de consumo "' . $codigoCuentaConsumo . '" de la línea ' . $linea . ' en el almacén ' . $movement['Almacen'] . ' no existe');
							} else {
								if ($cuentaConsumo->getEsAuxiliar() != 'S') {
									$this->_throwException('La cuenta de consumo "' . $codigoCuentaConsumo . '" de la línea ' . $linea . ' en el almacén ' . $movement['Almacen'] . ' no es auxiliar');
								}
							}

							if (!isset($cuentasCredito[$codigoCuentaConsumo])) {
								$cuentasCredito[$codigoCuentaConsumo] = abs($valor);
							} else {
								$cuentasCredito[$codigoCuentaConsumo]+= abs($valor);
							}

							$codigoCuentaInventario = $lineaSalida->getCtaInve();
							$cuentaInventario = BackCacher::getCuenta($codigoCuentaInventario);
							if ($cuentaInventario == false) {
								$this->_throwException('La cuenta de inventarios "' . $codigoCuentaInventario . '" de la línea '.$linea.' en el almacén '.$movement['Almacen'].' no existe');
							} else {
								if ($cuentaInventario->getEsAuxiliar()!='S') {
									$this->_throwException('La cuenta de inventarios "' . $codigoCuentaInventario . '" de la línea '.$linea.' en el almacén '.$movement['Almacen'].' no es auxiliar');
								}
							}

							if (!isset($cuentasDebito[$codigoCuentaInventario])) {
								$cuentasDebito[$codigoCuentaInventario] = abs($valor);
							} else {
								$cuentasDebito[$codigoCuentaInventario] += abs($valor);
							}

						}
					}

				}

				if (isset($lineas['Salida'])) {
					foreach ($lineas['Salida'] as $linea => $valor) {

						if ($valor) {

							$lineaSalida = BackCacher::getLinea($movement['Almacen'], $linea);
							if ($lineaSalida == false) {
								$this->_throwException('No existe la línea ' . $linea . ' en el almacén ' . $movement['Almacen']);
							}

							$codigoCuentaConsumo = $lineaSalida->getCtaConsumo();
							$cuentaConsumo = BackCacher::getCuenta($codigoCuentaConsumo);
							if ($cuentaConsumo==false) {
								$this->_throwException('La cuenta de consumo "'.$codigoCuentaConsumo.'" de la línea '.$linea.' en el almacén '.$movement['Almacen'].' no existe');
							} else {
								if ($cuentaConsumo->getEsAuxiliar()!='S') {
									$this->_throwException('La cuenta de consumo "'.$codigoCuentaConsumo.'" de la línea '.$linea.' en el almacén '.$movement['Almacen'].' no es auxiliar');
								}
							}

							if (!isset($cuentasDebito[$codigoCuentaConsumo])) {
								$cuentasDebito[$codigoCuentaConsumo] = abs($valor);
							} else {
								$cuentasDebito[$codigoCuentaConsumo]+= abs($valor);
							}

							$codigoCuentaInventario = $lineaSalida->getCtaInve();
							$cuentaInventario = BackCacher::getCuenta($codigoCuentaInventario);
							if ($cuentaInventario==false) {
								$this->_throwException('La cuenta de inventarios "'.$codigoCuentaInventario.'" de la línea '.$linea.' en el almacén '.$movement['Almacen'].' no existe');
							} else {
								if ($cuentaInventario->getEsAuxiliar()!='S') {
									$this->_throwException('La cuenta de inventarios "'.$codigoCuentaInventario.'" de la línea '.$linea.' en el almacén '.$movement['Almacen'].' no es auxiliar');
								}
							}

							if (!isset($cuentasCredito[$codigoCuentaInventario])) {
								$cuentasCredito[$codigoCuentaInventario] = abs($valor);
							} else {
								$cuentasCredito[$codigoCuentaInventario] += abs($valor);
							}

						}
						unset($valor);
					}
				}

				if (count($cuentasDebito) || count($cuentasCredito)) {
					$movihead->setNumeroComprobContab($this->_contabilizaMovement($options, $cuentasDebito, $cuentasCredito));
				} else {
					$movihead->setNumeroComprobContab(0);
				}

			}
		}

		if ($movihead->save() == false) {
			foreach ($movihead->getMessages() as $message) {
				$this->_throwException('Movihead: ' . $message->getMessage());
				unset($message);
			}
		}

		Rcs::disable();
		$comprob = $this->_comprobs[$movement['Comprobante']];
		if ($comprob->save() == false) {
			foreach ($comprob->getMessages() as $message) {
				$this->_throwException('Comprobante: ' . $message->getMessage());
				unset($message);
			}
		}

		if ($tipoComprob != 'P' && $tipoComprob != 'O') {
			TaticoKardex::setFastRecalculate(true);
			$conditions = 'comprob="' . $movement['Comprobante'] . '" AND almacen="' . $movement['Almacen'] . '" AND numero="' . $movement['Numero'] . '"';
			foreach ($this->Movilin->find(array($conditions, 'columns' => 'item')) as $movilin) {
				TaticoKardex::show($movilin->getItem(), $movement['Almacen'], '1999-01-01');
				unset($movilin);
			}
		}

		Rcs::enable();
		if (!$this->_hasTransaction) {
			$this->_transaction->commit();
		}

		//

		$this->_consecutivos = array(
			'inve' => $movihead->getNumero(),
			'contab' => $movihead->getNumeroComprobContab(),
			'extended' => array(
				'inve' => array('comprob' => $movihead->getComprob(), 'numero' => $movihead->getNumero()),
				'contab' => array('comprob' => $this->_comprobs[$this->_defaultComprob]->getComprobContab(), 'numero' => $movihead->getNumeroComprobContab())
			)
		);

		new EventLogger('SE AGREGÓ MOVIMIENTO "Comprobante: ' . $movihead->getComprob() . ', Almacén: '.$movihead->getAlmacen().', Número: '.$movihead->getNumero().'" EN INVENTARIO', 'A');

		if (self::$_controlStocks == true) {
			$numberWarnings = count(self::$_stockWarnings);
			if ($numberWarnings > 0) {
				$message = '<h3>Advertencia de Stocks</h3><p>';
				if ($numberWarnings == 1) {
					$message .= 'Se ha generado la siguiente advertencia';
				} else {
					$message .= 'Se han generado las siguientes advertencias';
				}
				$message .=' de stocks al realizar la transacción de inventario '.$movihead->getComprob().'-'.$movihead->getNumero().'</p><ul>';
				$stockBajos = 0;
				$stockAltos = 0;
				$almacenistas = array();
				foreach (self::$_stockWarnings as $stockWarning) {
					$inve = $stockWarning['Inve'];
					$saldos = $stockWarning['Saldos'];
					$inveStock = $stockWarning['Stock'];
					$movement = $stockWarning['Movement'];
					$message.= '<li>El saldo ' . Currency::number($saldos->getSaldo(), 2) . ' de la referencia ' . $inve->getItem() . '/' . $inve->getDescripcion();
					if ($inveStock->getMinimo() > $saldos->getSaldo()) {
						$message .= ' es menor al stock mínimo de '.Currency::number($inveStock->getMinimo(), 2);
						$stockBajos++;
					} else {
						if ($inveStock->getMaximo() < $saldos->getSaldo()) {
							$message .= ' es mayor al stock máximo de '.Currency::number($inveStock->getMaximo(), 2);
							$stockAltos++;
						}
					}
					$almacen = BackCacher::getAlmacen($movement['Almacen']);
					$almacenistas[$almacen->getUsuariosId()] = true;
					$message .= ' en el almacén ' . $almacen->getCodigo() . '/' . $almacen->getNomAlmacen() . '</li>';
					unset($stockWarning);
				}
				if ($stockAltos > 0 && $stockBajos > 0) {
					$subject = 'Advertencia de stocks bajos y altos';
				} else {
					if ($stockAltos > 0) {
						$subject = 'Advertencia de stocks altos';
					} else {
						$subject = 'Advertencia de stocks bajos';
					}
				}
				$message .= '</ul>
				<span style="color:#969696">Nota: Este es un mensaje automático, por favor no lo responda</span>';
				$almacenistas = array_keys($almacenistas);
				foreach ($almacenistas as $usuarioId) {
					$usuario = BackCacher::getUsuario($usuarioId);
					if ($usuario != false) {
						HfosMail::send(array(
							'from' => 'Inventarios "<no-responder@localhost.hfos>"',
							'to' => $usuario->getNombreCompleto(),
							'subject' => $subject,
							'body' => $message
						));
					}
					unset($usuarioId);
				}
			}
		}

		//Envia advertencias cuando los costos no son encontrados en el almacén propio
		if ($tipoComprob == 'C' || $tipoComprob == 'A' || $tipoComprob == 'T') {
			$numberWarnings = count(self::$_costosWarnings);
			if ($numberWarnings > 0) {
				$message = '<h3>Advertencia de Costos</h3><p>';
				if ($numberWarnings == 1) {
					$message.= 'Se ha tomado el costo de la siguiente referencia ';
				} else {
					$message.= 'Se han tomado los costos de las siguientes referencias ';
				}
				$message .= ' del almacén principal al no poder valorizarse del almacén donde se realizó la transacción al grabar el movimiento de inventario '.$movihead->getComprob().'-'.$movihead->getNumero().'</p><ul>';
				self::$_costosWarnings = array_keys(self::$_costosWarnings);
				foreach (self::$_costosWarnings as $item) {
					$inve = BackCacher::getInve($item);
					$message .= '<li>' . $inve->getItem() . '/' . $inve->getDescripcion() . '</li>';
				}
				$message .= '</ul>
				<span style="color:#969696">Nota: Este es un mensaje automático, por favor no lo responda</span>';
				self::$_costosWarnings = array();
				$almacen = BackCacher::getAlmacen(1);
				if ($almacen != false) {
					$usuario = BackCacher::getUsuario($almacen->getUsuariosId());
					if ($usuario != false) {
						HfosMail::send(array(
							'from' => 'Inventarios "<no-responder@localhost.hfos>"',
							'to' => $usuario->getNombreCompleto(),
							'subject' => 'Advertencias Costos Otros Almacenes',
							'body' => $message
						));
					}
				}
			}
		}

	}

	/**
	 * Cierra las ordenes de compra usadas en entradas
	 *
	 * @param array $movement
	 */
	private function _cerrarOrden($movement, $movihead)
	{
		if ($movement['NPedido'] > 0) {
			$comprob = sprintf('O%02s', $movement['Almacen']);
			$orden = self::getModel('Movihead')->findFirst("comprob='" . $comprob . "' AND numero='" . $movement['NPedido'] . "'");
			if ($orden != false) {
				$orden->setTransaction($this->_transaction);
				$orden->setEstado('C');
				if ($orden->save()==false) {
					foreach ($orden->getMessages() as $message) {
						$this->_throwException('Orden-de-Compra: ' . $message->getMessage());
					}
				} else {
					$movihead->setFormaPago($orden->getFormaPago());
				}
			} else {
				$formaEntrada = Settings::get('forma_entrada');
				if (!$formaEntrada) {
					$movihead->setFormaPago(3);
				} else {
					$movihead->setFormaPago($formaEntrada);
				}
			}
		} else {
			$formaEntrada = Settings::get('forma_entrada');
			if (!$formaEntrada) {
				$movihead->setFormaPago(3);
			} else {
				$movihead->setFormaPago($formaEntrada);
			}
		}
	}

	/**
	 * Cierra los pedidos usados en salidas o traslados
	 *
	 * @param array $movement
	 */
	private static function _cerrarPedido($transaction, $movement)
	{
		if ($movement['NPedido']>0) {
			$comprob = sprintf('P%02s', $movement['Almacen']);
			$pedido = self::getModel('Movihead')->findFirst("comprob='" . $comprob . "' AND numero='" . $movement['NPedido'] . "'");
			if ($pedido != false) {
				$pedido->setTransaction($transaction);
				$pedido->setEstado('C');
				if ($pedido->save() == false) {
					foreach ($pedido->getMessages() as $message) {
                        $tatico = new Tatico();
						$tatico->_throwException('Pedido: '.$message->getMessage());
					}
				}
			}
		}
	}

	/**
	 * Recalcula los saldos en inventarios al realizar un movimiento
	 *
	 * @param string $tipoComprob
	 * @param array $movement
	 */
	public function moverSaldos($tipoComprob, $movement)
	{

		$inve = BackCacher::getInve($movement['Item']);
		if ($inve==false) {
			$this->_throwException('No existe la referencia "'.$movement['Item'].'"');
		} else {
			$inve->setTransaction($this->_transaction);
		}

		if (isset($movement['modifyCosto'])) {
			$modifyCosto = $movement['modifyCosto'];
		} else {
			 $modifyCosto = true;
		}
		if (isset($movement['AfectaConsolidado'])) {
			$afectaConsolidado = $movement['AfectaConsolidado'];
		} else {
			$afectaConsolidado = true;
		}

		$Saldos = self::getModel('Saldos');
		$Saldos->setTransaction($this->_transaction);

		$saldos = $Saldos->findFirst('item="'.$movement['Item'].'" AND almacen="'.$movement['Almacen'].'" AND ano_mes=0');
		if ($saldos==false) {
			$saldos = new Saldos();
			$saldos->setTransaction($this->_transaction);
			$saldos->setItem($movement['Item']);
			$saldos->setAlmacen($movement['Almacen']);
			if (!isset($movement['AnoMes'])) {
				$saldos->setAnoMes(0);
			} else {
				$saldos->setAnoMes($movement['AnoMes']);
			}
			$saldos->setSaldo(0);
			$saldos->setCosto(0);
			$saldos->setFUMov((string)$movement['Fecha']);
		} else {
			if (Date::isEarlier($movement['Fecha'], $saldos->getFUMov())) {
				$saldos->setFUMov((string)$movement['Fecha']);
			}
		}
		if (!isset($movement['Tipo'])) {
			if ($movement['Cantidad']>0) {
				$movement['Tipo'] = 'E';
			} else {
				$movement['Tipo'] = 'S';
			}
		}
		$movement['Cantidad'] = abs($movement['Cantidad']);
		$movement['Valor'] = abs($movement['Valor']);

		//Obtenemos el nombre del almacen
		if ($movement['Almacen'] !== 0) {
			$almacen = BackCacher::getAlmacen($movement['Almacen']);
			if ($almacen==false) {
				$this->_throwException('El almacen de la transacción es inválido');
			}
		} else {
			$almacen = new Almacenes();
			$almacen->setNomAlmacen('CONSOLIDADO');
		}

		//Si es una entrada de mercancia
		if ($movement['Tipo'] == 'E') {

			$saldos->setSaldo($saldos->getSaldo() + $movement['Cantidad']);
			if ($saldos->getSaldo() != 0 && $modifyCosto) {
				$saldos->setCosto($saldos->getCosto() + $movement['Valor']);
			}
			if ($afectaConsolidado==true) {
				$inve->setSaldoActual($inve->getSaldoActual() + $movement['Cantidad']);
				if ($inve->getSaldoActual() != 0 && $modifyCosto) {
					$inve->setCostoActual($inve->getCostoActual() + $movement['Valor']);
				}
			}

		} else {

			//Se pierde el almacen en transformaciones
			if (!$almacen) {
				$almacen = BackCacher::getAlmacen($movement['Almacen']);
			}

			//Las salidas, ajustes y traslados validan que no vaya a quedar negativo
			if (self::$_controlNegatives==true) {
				if ($afectaConsolidado==true) {
					if ($saldos->getSaldo()<$movement['Cantidad']) {
						$this->_noExistencias($tipoComprob, $inve, $saldos, $almacen, $movement);
					}
				} else {
					if ($saldos->getSaldo()<$movement['Cantidad']) {
						$this->_noExistencias($tipoComprob, $inve, $saldos, $almacen, $movement);
					}
				}
			}

			$saldos->setSaldo($saldos->getSaldo() - $movement['Cantidad']);
			if ($saldos->getSaldo() != 0 && $modifyCosto) {
				$saldos->setCosto($saldos->getCosto() - $movement['Valor']);
			}
			if ($afectaConsolidado==true) {
				$inve->setSaldoActual($inve->getSaldoActual() - $movement['Cantidad']);
				if ($inve->getSaldoActual() != 0 && $modifyCosto) {
					$inve->setCostoActual($inve->getCostoActual() - $movement['Valor']);
				}
			}
		}

		if ($saldos->getSaldo()==0) {
			$saldos->setCosto(0);
		}

		if ($inve->getSaldoActual()==0) {
			$inve->setCostoActual(0);
		}

		if ($saldos->save()==false) {
			$message = 'Saldos: ';
			foreach ($saldos->getMessages() as $message) {
				$message.= $message->getMessage();
			}
			$this->_throwException($message);
		}

		if (self::$_controlStocks == true) {
			$InveStocks = self::getModel('InveStocks')->setTransaction($this->_transaction);
			$inveStock = $InveStocks->findFirst("item='{$movement['Item']}' AND almacen='{$movement['Almacen']}'");
			if ($inveStock!=false) {
				if ($inveStock->getMinimo() > 0) {
					if ($saldos->getSaldo() < $inveStock->getMinimo()) {
						self::_addStockWarning($movement, $inve, $inveStock, $saldos);
					}
				}
				if ($inveStock->getMaximo() > 0) {
					if ($saldos->getSaldo() > $inveStock->getMaximo()) {
						self::_addStockWarning($movement, $inve, $inveStock, $saldos);
					}
				}
			}
		}

		if ($afectaConsolidado==true) {
			Rcs::disable();
			if ($inve->save()==false) {
				foreach ($inve->getMessages() as $message) {
					$this->_throwException('Referencia: ' . $message->getMessage() . ', en la referencia ' . $inve->getItem() . '/' . $inve->getDescripcion());
				}
			}
			Rcs::enable();
		}
		return $inve;
	}

	/**
	 * Agrega una advertencia de stocks
	 *
	 * @param array $movement
	 * @param Inve $inve
	 * @param InveStocks $inveStock
	 * @param Saldos $saldos
	 */
	private static function _addStockWarning($movement, Inve $inve, InveStocks $inveStock, Saldos $saldos)
	{
		self::$_stockWarnings[$inve->getItem()] = array(
			'Movement' => $movement,
			'Inve' => $inve,
			'Stock' => $inveStock,
			'Saldos' => $saldos
		);
	}

	/**
	 * Agrega una advertencia de stocks
	 *
	 * @param	string $item
	 */
	private static function _addCostosWarning($item)
	{
		self::$_costosWarnings[$item] = true;
	}

	/**
	 * Genera un mensaje indicando que no hay existencias
	 *
	 * @param	string $tipoComprob
	 * @param	Inve $inve
	 * @param	Almacenes $almacen
	 * @param	array $movement
	 */
	private function _noExistencias($tipoComprob, $inve, $saldos, $almacen, $movement)
	{
		if (!isset($movement['Numero'])) {
			$movement['Numero'] = 0;
		}
		switch($tipoComprob) {
			case 'C':
				$this->_throwException('La referencia ' . $inve->getItem() . '/' . $inve->getDescripcion() . ' no tiene existencias suficientes en el almacén '.$almacen->getCodigo().'/'.$almacen->getNomAlmacen().' para realizar la salida ('.$tipoComprob.'-'.$movement['Numero'].') Saldo Actual: '.LocaleMath::round($saldos->getSaldo(), 2).'  Cantidad a Descargar: '.$movement['Cantidad']);
			case 'T':
				$this->_throwException('La referencia ' . $inve->getItem() . '/' . $inve->getDescripcion() . ' no tiene existencias suficientes en el almacén '.$almacen->getCodigo().'/'.$almacen->getNomAlmacen().' para realizar el traslado ('.$tipoComprob.'-'.$movement['Numero'].') '.$saldos->getSaldo().'-'.$movement['Cantidad']);
			case 'A':
				$this->_throwException('La referencia ' . $inve->getItem() . '/' . $inve->getDescripcion() . ' no tiene existencias suficientes en el almacén '.$almacen->getCodigo().'/'.$almacen->getNomAlmacen().' para realizar el ajuste ('.$tipoComprob.'-'.$movement['Numero'].'). '.$saldos->getSaldo().'-'.$movement['Cantidad']);
			default:
				$this->_throwException('La referencia ' . $inve->getItem() . '/' . $inve->getDescripcion() . ' no tiene existencias suficientes en el almacén '.$almacen->getCodigo().'/'.$almacen->getNomAlmacen().' para realizar el movimiento ('.$tipoComprob.'-'.$movement['Numero'].')');
		}
	}

	/**
	 * Contabiliza el movimiento
	 *
	 * @param	array $options
	 * @param	array $cuentasDebito
	 * @param	array $cuentasCredito
	 * @return	int
	 */
	private function _contabilizaMovement($options, $cuentasDebito, $cuentasCredito)
	{

		$movementContab = array(
			'Fecha' => $options['Fecha'],
			'Descripcion' => i18n::strtoupper($options['Descripcion']),
			'Nit' => $options['Nit'],
			'CentroCosto' => $options['CentroCosto'],
			'BaseGrab' => 0,
			'TipoDocumento' => null,
			'NumeroDocumento' => 0
		);

		$comprobanteContab = $this->_comprobs[$this->_defaultComprob]->getComprobContab();
		if (!$comprobanteContab) {
			$this->_throwException("Debe asignar el comprobante contable al comprobante ".$this->_defaultComprob);
		} else {
			if ($comprobanteContab == $this->_defaultComprob) {
				$this->_throwException("El comprobante de contabilización del comprobante ".$this->_defaultComprob." no puede ser él mismo");
			}
		}

		$movements = array();
		foreach ($cuentasDebito as $codigoCuenta => $valor) {
			if ($valor == 0) {
				continue;
			}
			$cuenta = BackCacher::getCuenta($codigoCuenta);
			if (!$cuenta) {
				$this->_throwException("Contabilidad: La cuenta contable $codigoCuenta no existe");
			}
			if ($cuenta->getPideFact()=='S') {
				$this->_throwException("Contabilidad: La cuenta contable $codigoCuenta pide documento pero este movimiento no lleva documento alguno");
			}
			$movementContab['Valor'] = $valor;
			$movementContab['Cuenta'] = $codigoCuenta;
			$movementContab['DebCre'] = 'D';
			$movements[] = $movementContab;
		}
		foreach ($cuentasCredito as $codigoCuenta => $valor) {
			if ($valor == 0) {
				continue;
			}
			if (isset($options['CuentaCartera'])&&$codigoCuenta==$options['CuentaCartera']) {
				$movementContab['TipoDocumento'] = 'FXP';
				$movementContab['NumeroDocumento'] = $options['FacturaC'];
			} else {
				$cuenta = BackCacher::getCuenta($codigoCuenta);
				if (!$cuenta) {
					$this->_throwException("Contabilidad: La cuenta contable $codigoCuenta no existe");
				}
				if ($cuenta->getPideFact()=='S') {
					$this->_throwException("Contabilidad: La cuenta contable $codigoCuenta pide documento pero este movimiento no lleva documento alguno");
				}
			}
			$movementContab['Valor'] = $valor;
			$movementContab['Cuenta'] = $codigoCuenta;
			$movementContab['DebCre'] = 'C';
			$movements[] = $movementContab;
			$movementContab['TipoDocumento'] = null;
			$movementContab['NumeroDocumento'] = 0;
		}

		$aura = IdentityManager::getAuthedService('contab.aura');
		$aura->setTransaction($this->_transaction);
		return $aura->save($comprobanteContab, 0, $movements, Aura::OP_CREATE);

	}

	/**
	 * Indica si se deben controlar las existencias negativas
	 *
	 * @param boolean $controlNegatives
	 */
	public static function setControlNegatives($controlNegatives)
	{
		self::$_controlNegatives = $controlNegatives;
	}

	/**
	 * Indica si se deben controlar los stocks minimos y maximos
	 *
	 * @param boolean $controlStocks
	 */
	public static function setControlStocks($controlStocks)
	{
		self::$_controlStocks = $controlStocks;
	}

	/**
	 * Indica si se debe mostrar el detalle del calculo de impuestos
	 *
	 * @param boolean $taxesDebug
	 */
	public static function setTaxesDebug($taxesDebug)
	{
		self::$_taxesDebug = $taxesDebug;
	}

	/**
	 * Elimina un movimiento
	 *
	 * @param array $movement
	 */
	public function delMovement($movement)
	{

		$tipoComprob = $movement['Comprobante'][0];
		$movihead = self::getMovement($tipoComprob, $movement['Almacen'], $movement['Numero']);
		if ($movihead==false) {
			throw new TaticoException('El movimiento "'.$movement['Comprobante'].' - '.$movement['Numero'].'" no existe');
		}
		if (Date::isEarlier($movihead->getFecha(), $this->_empresa->getFCierrei())) {
			throw new TaticoException('El movimiento no puede eliminarse por ser anterior a la hora de cierre');
		}
		if ($movihead->getEstado()=='C') {
			//throw new TaticoException('El movimiento no puede eliminarse por estar cerrado');
		}
		$this->verificarAntesDeBorrar($movihead);

		$fecha = $movihead->getFecha();
		$almacen = $movihead->getAlmacen();
		$comprob = $movihead->getComprob();

		switch ($tipoComprob) {
			case 'E':
				//Hace salida si el comprobnate a borrar es entrada
				$comprob = sprintf('C%02s', $almacen);
				break;
			case 'S':
				//Hace entrada si el comprobnate a borrar es salida
				$comprob = sprintf('E%02s', $almacen);
				break;

			default:
				throw new TaticoException("Comprobante no soportado para borrar '$tipoComprob'", 1);
				break;
		}

		$newMovihead = clone $movihead;
		$newMovihead->setComprob($comprob);
		$max = $this->Movihead->maximum(array('numero', "conditions" => "comprob='$comprob'")) + 1;
		$newMovihead->setNumero($max);
		$newMovihead->setEstado('A');
		$newMovihead->setDescription("Se elimino el movimiento '{$movihead->getComprob()} / {$movihead->getNumero()}'");
		$newMovihead->save();

		$movihead->setEstado('A');
		$movihead->save();


		$movilins = $this->Movilin->find("comprob='{$movihead->getComprob()}' AND numero='{$movihead->getNumero()}' AND almacen='{$movihead->getAlmacen()}'");
		foreach ($movilins as $movilin) {
			$newMovilin= clone $movilin;
			$newMovilin->setId(null);
			$newMovilin->setComprob($comprob);
			$newMovilin->save();
		}
	}

	private function verificarAntesDeBorrar($movihead)
	{
		if ($movihead->getEstado()=='A') {
			throw new TaticoException('El movimiento ya esta anulado');
		}

		$fecha = $movihead->getFecha();
		//throw new TaticoException("fecha > '$fecha' AND comprob NOT LIKE 'O%' AND estado = 'C'");
		$moviFuturo = $this->Movihead->findFirst("fecha > '$fecha' AND comprob NOT LIKE 'O%' AND estado != 'A'");
		if ($moviFuturo) {
			throw new TaticoException("Existen otros movimientos de inventario con fecha mayor a '$fecha'. Antes debe borrar esos movimentos para borrar este.");
		}
	}

	/**
	 * Actualiza el registro de la orden de compra
	 *
	 * @param array $movimiento
	 */
	public function actualizaLineaOrdenPedido($movimiento)
	{
		if (isset($movimiento['Id'])) {
			$conditions = 'id="'.$movimiento['Id'].'"';
		} else {
			$conditions = 'comprob="'.$movimiento['Comprobante'].'" AND almacen="'.$movimiento['Almacen'].'" AND numero="'.$movimiento['Numero'].'" AND item="'.$movimiento['Item'].'"';
		}
		$movilin = self::getModel('Movilin')->findFirst($conditions);
		if ($movilin==false) {
			return;
		}
		$movilin->setTransaction($this->_transaction);
		$movilin->setCantidadRec($movilin->getCantidadRec() + $movimiento['Cantidad']);
		if ($movilin->save() == false) {
			foreach ($movilin->getMessage() as $message) {
				$this->_throwException($message->getMessage());
			}
		}
	}

	/**
	 * Consulta un comprobante y genera el consecutivo si es necesario
	 *
	 * @param	string $codigoComprobante
	 * @return	int
	 */
	private function _getMaxNumeroComprob($codigoComprobante)
	{
		//Obtenemos el maximo de comprobantes de un codigo
		$consecutivo = (int) $this->Comprob->maximum(array('consecutivo', "conditions" => "codigo='$codigoComprobante'")) + 1;
		if ($this->Movilin->count("comprob='$codigoComprobante' AND numero='$consecutivo'")) {
			$consecutivo = $this->Movilin->maximum("numero", "conditions: comprob='$codigoComprobante'")+1;
		}
		return $consecutivo;
	}

	/**
	 * Retorna los últimos consecutivos usados en la transacción
	 *
	 * @return int
	 */
	public function getLastConsecutivos($codigoComprobante='')
	{
		return $this->_consecutivos;
	}

	/**
	 * Lanza una excepción o hace rollback a la transacción dependiendo de si
	 * se ha sido creada dentro o fuera de tatico
	 *
	 * @param string $message
	 */
	private function _throwException($message)
	{
		if ($this->_hasTransaction) {
			throw new TaticoException($message);
		} else {
			$this->_transaction->rollback($message);
		}
	}

	/**
	 * Retorna los últimos consecutivos usados en la transacción
	 *
	 * @param string $codigoComprobante
	 * @param int $almacen
	 * @param int $numero
	 */
	public static function getMovement($type, $almacen, $numero)
	{
		if (strlen($type) == 1 && isset(self::$_types[$type])) {
			$comprob = sprintf($type . '%02s', $almacen);
		} else {
			if (strlen($type) == 3) {
				$comprob = $type;
			} else {
                $tatico = new Tatico();
                $tatico->_throwException('Comprobante o tipo de comprobante no válido');
			}
		}
		return self::getModel('Movihead')->findFirst("comprob='$comprob' AND almacen='$almacen' AND numero='$numero'");
	}

	/**
	 * Retorna los últimos consecutivos usados en la transacción
	 *
	 * @param string $codigoComprobante
	 * @param int $numero
	 */
	public static function getMovementDetail($type, $almacen, $numero)
	{
		if (strlen($type) == 1 && isset(self::$_types[$type])) {
			$comprob = sprintf("$type%02s", $almacen);
		} else {
			if (strlen($type) == 3) {
				$comprob = $type;
			} else {
                $tatico = new Tatico();
				$tatico->_throwException('Comprobante ó tipo de comprobante no válido');
			}
		}
		return self::getModel('Movilin')->find("comprob='$comprob' AND almacen='$almacen' AND numero='$numero'");
	}

	/**
	 * Obtiene las referencias consolidados de una receta
	 *
	 * @param Recetap $recetap
	 * @param array $data
	 */
	public static function getRecetaComponents(Recetap $recetap, $data=array())
	{
		$recetal = self::getModel('Recetal')->find('almacen="1" AND numero_rec = "'.$recetap->getNumeroRec().'"');
		foreach ($recetal as $recetal) {
			if ($recetal->getTipol() == 'I') {
				$cantidad = $recetal->getCantidad()/($recetap->getNumPersonas()*$recetal->getDivisor());
				if (isset($data[$recetal->getItem()])) {
					$data[$recetal->getItem()]['costo'] = LocaleMath::round($recetal->getValor()+$data[$recetal->getItem()]['costo'], 2);
					$data[$recetal->getItem()]['cantidad'] = LocaleMath::round($cantidad+$data[$recetal->getItem()]['cantidad'],4);
					continue;
				}
				$inve = self::getModel('Inve')->findFirst('item="'.$recetal->getItem().'"');
				if ($inve==false) {
					continue;
				}
				$data[$inve->getItem()] = array(
					'descripcion' => $inve->getDescripcion(),
					'unidad' => $inve->getUnidad(),
					'existencias' => $inve->getSaldoActual(),
					'costo' => LocaleMath::round($recetal->getValor(), 2),
					'iva' => (float) $inve->getIva(),
					'cantidad' => LocaleMath::round($cantidad, 4)
				);
			} else {
				$recetap = self::getModel('Recetap')->findFirst('almacen="1" AND numero_rec="'.$recetal->getItem().'"');
				if ($recetap != false) {
					 $data = self::getRecetaComponents($recetap, $data);
				}
			}
		}
		return $data;
	}


	/**
	 * Retorna un arreglo asociativo con los campos de la referencia o de la receta
	 *
	 * @param	int $codigoItem
	 * @param 	int $codigoAlmacen
	 * @return	mixed
	 */
	public static function getSaldoReferencia($codigoItem, $codigoAlmacen=0)
	{
		$inve = BackCacher::getInve($codigoItem);
		if (!$inve) {
			return array(
				'status' => 'FAILED',
				'message' => 'La referencia "'.$codigoItem.'" no existe'
			);
		}
		if ($inve->getEstado()=='I') {
			return array(
				'status' => 'FAILED',
				'message' => 'La referencia "'.$codigoItem.'" está inactiva'
			);
		} else {
			if ($codigoAlmacen > 0) {
				$almacen = BackCacher::getAlmacen($codigoAlmacen);
				if ($almacen==false) {
					$saldo = array(
						'status' => 'FAILED',
						'message' => 'No hay existe el almacén seleccionado'
					);
				} else {
					$saldos = self::getModel('Saldos')->findFirst("item='$codigoItem' AND almacen='$codigoAlmacen' AND ano_mes=0");
					if ($saldos==false) {
						$saldo = array(
							'status' => 'FAILED',
							'message' => 'No hay existencias de ' . $inve->getDescripcion().' en el almacén '.$codigoItem.'/'.$almacen->getNomAlmacen()
						);
					} else {
						$valorSaldo = $saldos->getSaldo();
						if ($valorSaldo <= 0) {
							$saldo = array(
								'status' => 'FAILED',
								'message' => 'No hay existencias de ' . $inve->getDescripcion() . ' en el almacén '.$codigoItem.'/'.$almacen->getNomAlmacen()
							);
						} else {
							$saldo = array(
								'status' => 'OK',
								'message' => 'El saldo actual de ' . $inve->getDescripcion() . ' en el almacén '.$codigoItem.'/'.$almacen->getNomAlmacen().' es: '.LocaleMath::round($valorSaldo, 2)
							);
						}
					}
				}
			} else {
				$saldo = array(
					'status' => 'UNDEFINED'
				);
			}
			return array(
				'status' => 'OK',
				'data' => array(
					'type' => 'I',
					'item' => $inve->getItem(),
					'descripcion' => $inve->getDescripcion(),
					'existencias' => $inve->getSaldoActual(),
					'costo' => LocaleMath::round(self::getCosto($codigoItem, 'I', $codigoAlmacen), 2),
					'saldo' => $saldo
				)
			);
		}
	}

	/**
	 * Retorna un arreglo asociativo con los campos de la referencia o de la receta
	 *
	 * @param	int $codigoItem
	 * @return	mixed
	 */
	public static function getReferenciaOrReceta($codigoItem, $codigoAlmacen=0, $tipoDetalle='I')
	{
        //Buscamos Receta
		if ($tipoDetalle=='R') {
			$recetap = self::getModel('Recetap')->findFirst('almacen="1" AND numero_rec="' . $codigoItem . '"');
			if ($recetap===false) {
				return array(
					'status' => 'FAILED',
					'message' => 'La receta '.$codigoItem.' no existe'
				);
			} else {
				return array(
					'status' => 'OK',
					'data' => array(
						'type' => 'R',
						'item' => $recetap->getNumeroRec(),
						'descripcion' => $recetap->getNombre(),
						'unidad' => '@',
						'num_personas' => $recetap->getNumPersonas(),
						'costo' => LocaleMath::round($recetap->getPrecioCosto(), 2),
						'iva' => 0,
						'estado' => $recetap->getEstado(),
						'components' => self::getRecetaComponents($recetap)
					)
				);
			}
		} else {
            //Buscamos Referencia
            $inve = BackCacher::getInve($codigoItem);
            if ($inve===false) {
                return array(
                    'status' => 'FAILED',
                    'message' => 'La referencia ' . $codigoItem . ' no existe'
                );
            } else {
                if ($inve->getEstado()=='I') {
                    return array(
                        'status' => 'FAILED',
                        'message' => 'La referencia "' . $codigoItem . '" está inactiva'
                    );
                } else {
                    if ($codigoAlmacen>0) {
                        $almacen = BackCacher::getAlmacen($codigoAlmacen);
                        if ($almacen==false) {
                            $saldo = array(
                                'status' => 'FAILED',
                                'message' => 'No hay existe el almacén seleccionado'
                            );
                        } else {
                            $saldos = self::getModel('Saldos')->findFirst("item='$codigoItem' AND almacen='$codigoAlmacen' AND ano_mes=0");
                            if ($saldos==false) {
                                $saldo = array(
                                    'status' => 'FAILED',
                                    'message' => 'No hay existencias de '.$inve->getDescripcion().' en el almacén '.$codigoItem.'/'.$almacen->getNomAlmacen()
                                );
                            } else {
                                $valorSaldo = $saldos->getSaldo();
                                if ($valorSaldo<=0) {
                                    $saldo = array(
                                        'status' => 'FAILED',
                                        'message' => 'No hay existencias de '.$inve->getDescripcion().' en el almacén '.$codigoItem.'/'.$almacen->getNomAlmacen()
                                    );
                                } else {
                                    $saldo = array(
                                        'status' => 'OK',
                                        'message' => 'El saldo actual de '.$inve->getDescripcion().' en el almacén '.$codigoItem.'/'.$almacen->getNomAlmacen().' es: '.LocaleMath::round($valorSaldo, 2)
                                    );
                                }
                            }
                        }
                    } else {
                        $saldo = array(
                            'status' => 'UNDEFINED'
                        );
                    }
                    return array(
                        'status' => 'OK',
                        'data' => array(
                            'type' => 'I',
                            'item' => $inve->getItem(),
                            'descripcion' => $inve->getDescripcion(),
                            'unidad' => $inve->getUnidad(),
                            'existencias' => $inve->getSaldoActual(),
                            'num_personas' => 1,
                            'costo' => LocaleMath::round(self::getCosto($codigoItem, 'I', $codigoAlmacen), 2),
                            'saldo' => $saldo,
                            'iva' => (int) $inve->getIva(),
                            'estado' => $inve->getEstado(),
                        )
                    );
                }
            }
		}
	}

	/**
	 * Retorna un arreglo asociativo con los campos de la referencia
	 *
	 * @param int $codigoItem
	 * @return mixed
	 */
	public static function getReferencia($codigoItem, $almacen=1)
    {
		$inve = BackCacher::getInve($codigoItem);
		if ($inve===false) {
			return array(
				'status' => 'FAILED',
				'message' => 'La referencia "'.$codigoItem.'" no existe'
			);
		} else {
			return array(
				'status' => 'OK',
				'data' => array(
					'type' => 'I',
					'item' => $inve->getItem(),
					'descripcion' => $inve->getDescripcion(),
					'unidad' => $inve->getUnidad(),
					'unidad_porcion' => $inve->getUnidadPorcion(),
					'peso' => $inve->getPeso(),
					'existencias' => $inve->getSaldoActual(),
					'num_personas' => 1,
					'costo' => LocaleMath::round(self::getCosto($codigoItem, 'I', $almacen), 2),
					'iva' => $inve->getIva(),
					'estado' => $inve->getEstado()
				)
			);
		}
	}

    /**
     * Retorna un arreglo asociativo con los campos de la receta
     *
     * @param int $codigoItem
     * @return mixed
     */
    public static function getReceta($codigoItem, $almacen=1)
    {
        $receta = EntityManager::get('Recetap')->findFirst("almacen='$almacen' AND numero_rec='$codigoItem'");
        if ($receta===false) {
            return array(
                'status' => 'FAILED',
                'message' => 'La receta "'.$codigoItem.'" no existe'
            );
        } else {
            return array(
                'status' => 'OK',
                'data' => array(
                    'type' => 'R',
                    'item' => $receta->getNumeroRec(),
                    'descripcion' => $receta->getNombre(),
                    'num_personas' => 1,
                    'costo' => LocaleMath::round(self::getCosto($codigoItem, 'R', $almacen), 2),
                    'estado' => $receta->getEstado()
                )
            );
        }
    }

	/**
	 * Retorna el costo de una referencia o una receta en un determinado almacén
	 *
	 * @param	int $codigoItem
	 * @param	string $tipo
	 * @param 	int $almacen
	 * @param 	Tatico $tatico
	 * @return	mixed
	 */
	public static function getCosto($codigoItem, $tipo='I', $almacen=1, $tatico=null)
    {
		$costo = 0;
		$hasTransaction = TransactionManager::hasUserTransaction();
		if ($hasTransaction) {
			$transaction = TransactionManager::getUserTransaction();
			self::getModel('Saldos')->setTransaction($transaction);
			self::getModel('Movilin')->setTransaction($transaction);
			self::getModel('Recetap')->setTransaction($transaction);
		}
		if ($tipo=='I') {
			$saldo = self::getModel('Saldos')->findFirst("almacen='$almacen' AND ano_mes='0' AND item='$codigoItem'");
			if ($saldo==false||$saldo->getSaldo()<=0||$saldo->getCosto()<=0) {
				$comprobEntrada = 'E'.sprintf('%02s', $almacen);
				$movilin = self::getModel('Movilin')->findFirst("comprob='$comprobEntrada' AND item='$codigoItem' AND cantidad>0", 'order: fecha DESC');
				if ($movilin==false) {
					$comprobTraslado = 'T'.sprintf('%02s', $almacen);
					$movilin = self::getModel('Movilin')->findFirst("comprob='$comprobTraslado' AND item='$codigoItem' AND cantidad>0", 'order: fecha DESC');
					if ($movilin==false) {
						$comprobTransformacion = 'R'.sprintf('%02s', $almacen);
						$movilin = self::getModel('Movilin')->findFirst("comprob='$comprobTransformacion' AND item='$codigoItem' AND cantidad>0", 'order: fecha DESC');
						if ($movilin==false) {
							if ($almacen<>1) {
								$saldo = self::getModel('Saldos')->findFirst("almacen='1' AND ano_mes='0' AND item='$codigoItem'");
								if ($saldo==false||$saldo->getSaldo()<=0||$saldo->getCosto()<=0) {
									$movilin = self::getModel('Movilin')->findFirst("comprob='E01' AND item='$codigoItem' AND cantidad>0", 'order: fecha DESC');
									if ($movilin==false) {
										$costo = 0;
									} else {
										$costo = $movilin->getValor()/$movilin->getCantidad();
										self::_addCostosWarning($codigoItem);
									}
								} else {
									$costo = $saldo->getCosto()/$saldo->getSaldo();
									self::_addCostosWarning($codigoItem);
								}
							}
						} else {
							$costo = $movilin->getValor()/$movilin->getCantidad();
						}
					} else {
						$costo = $movilin->getValor()/$movilin->getCantidad();
					}
				} else {
					$costo = $movilin->getValor()/$movilin->getCantidad();
				}
			} else {
				$costo = $saldo->getCosto()/$saldo->getSaldo();
			}
		} else {
			$costo = self::getModel('Recetap')->maximum('precio_costo', 'conditions: almacen="1" AND numero_rec="' . $codigoItem . '"');
		}
		if ($costo<=0) {
			return false;
		}
		return $costo;
	}

	/**
	 * Retorna el saldo de una referencia
	 *
	 * @param	int $codigoItem
	 * @param	int $almacen
	 * @return	double
	 */
	public static function getSaldo($codigoItem, $almacen=1)
    {
		$hasTransaction = TransactionManager::hasUserTransaction();
		if ($hasTransaction) {
			$transaction = TransactionManager::getUserTransaction();
			self::getModel('Saldos')->setTransaction($transaction);
		}
		$saldo = self::getModel('Saldos')->findFirst('almacen="'.$almacen.'" AND ano_mes="0" AND item="'.$codigoItem.'"');
		if ($saldo==false||$saldo->getSaldo()<=0) {
			return 0;
		} else {
			return $saldo->getSaldo();
		}
	}

	/**
	 * Retorna el saldo promedio de una referencia
	 *
	 * @param	int $codigoItem
	 * @param	int $almacen
	 * @return	double
	 */
	public static function getSaldoPromedio($codigoItem, $almacen=1)
    {
		$hasTransaction = TransactionManager::hasUserTransaction();
		if ($hasTransaction) {
			$transaction = TransactionManager::getUserTransaction();
			self::getModel('Saldos')->setTransaction($transaction);
		}
		return self::getModel('Saldos')->average(array('saldo', "conditions" => "almacen='".$almacen."' AND item='".$codigoItem."'"));
	}

	/**
	 * Retorna un arreglo asociativo con los datos de la orden de compra
	 *
	 * @param 	int	$nAlmacen
	 * @param 	int $nOrden
	 * @return	mixed
	 */
	public static function getOrdenDeCompra($nAlmacen, $nOrden)
    {
		$comprob = sprintf('O%02s', $nAlmacen);
		$orden = self::getModel('Movihead')->findFirst('almacen="'.$nAlmacen.'" AND comprob="'.$comprob.'" AND numero="'.$nOrden.'"');
		if ($orden != false) {
			$date = new Date();
			if (Date::isEarlier($orden->getFVence(), $date->getDate()) || $orden->getEstado()=='C') {
				if ($orden->getEstado()!='C') {
					$orden->setEstado('C');
					if ($orden->save()==false) {
						foreach ($orden->getMessages() as $message) {
							return array(
								'status' => 'FAILED',
								'message' => 'Orden de Compra: '.$message->getMessage()
							);
						}
					}
				}
				return array(
					'status' => 'FAILED',
					'message' => 'La orden de compra '.$nOrden.' está cerrada en el almacén '.$nAlmacen
				);
			}

			$totalOrden = 0;
			$detailOC = array();
			$movilins = self::getModel('Movilin')->find('almacen="'.$nAlmacen.'" AND comprob="'.$comprob.'" AND numero="'.$nOrden.'"');
			foreach ($movilins as $movilin)
			{
				$inve = BackCacher::getInve($movilin->getItem());
				if ($inve==false) {
					continue;
				}
				$detailOC[] = array(
					'id' => $movilin->getId(),
					'item' => $movilin->getItem(),
					'descripcion' => $inve->getDescripcion(),
					'unidad' => $inve->getUnidad(),
					'costo' => LocaleMath::round($movilin->getValor(), 2),
					'cantidad' => LocaleMath::round($movilin->getCantidad(), 3),
					'valor' => LocaleMath::round($movilin->getValor(), 2),
					'iva' => LocaleMath::round($movilin->getIva(), 2)
				);
				$totalOrden+=$movilin->getValor();
				unset($movilin);
			}
			unset($movilins);

			$fields = array(
				'retencion' => 'retencion',
				'ica' => 'ica',
				'iva' => 'iva16d',
				'descuento' => 'iva16r',
				'ivad' => 'iva10d',
				'ivam' => 'iva10r',
				'cree' => 'cree',
				'impo' => 'impo',
				'saldo' => 'saldo',
			);
			foreach ($fields as $field => $nameField) {
				$detailTaxes[$nameField] = LocaleMath::round($orden->readAttribute($field), 2);
				unset($nameField);
			}

			$movih1 = self::getModel('Movih1')->findFirst("comprob='{$orden->getComprob()}' AND numero='{$orden->getNumero()}'");
			if ($movih1!=false) {
				$fields = array(
					'iva5' => 'iva5d',
					'retiva5' => 'iva5r',
					'reten1' => 'horti',
					'cree' => 'cree',
					'impo' => 'impo',
				);
				foreach ($fields as $field => $nameField) {
					$detailTaxes[$nameField] = LocaleMath::round($movih1->readAttribute($field),2);
					unset($nameField);
				}
			}

			$detailTaxes['total_neto'] = LocaleMath::round($totalOrden, 2);

			$tercero = self::getRazsocByNit($orden->getNit());
			$nombre = $tercero['status'] == 'NOT FOUND' ? '' : $tercero['nombre'];

			return array(
				'status' => 'OK',
				'data' => array(
					'nit' => $orden->getNit(),
					'nit_det' => $nombre,
					'nombre' => $nombre,
					'f_vence' => $orden->getFVence()->getDate(),
					'n_factura' => $orden->getFacturaC(),
					'observaciones' => $orden->getObservaciones(),
					'movilin' => $detailOC,
					'taxes' => $detailTaxes
				)
			);
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'La orden de compra '.$nOrden.' no existe en el almacén '.$nAlmacen
			);
		}
	}

	/**
	 * Retorna un arreglo asociativo con los datos del pedido
	 *
	 * @param	int $nAlmacen
	 * @param	int $nPedido
	 * @return	mixed
	 */
	public static function getPedido($nAlmacen, $nPedido)
	{

		$comprob = sprintf('P%02s', $nAlmacen);
		$pedido = self::getModel('Movihead')->findFirst('almacen="'.$nAlmacen.'" AND comprob="'.$comprob.'" AND numero="'.$nPedido.'"');
		if ($pedido != false) {
			$date = new Date();
			if (Date::isEarlier($pedido->getFVence(), $date) || $pedido->getEstado()=='C') {
				if ($pedido->getEstado()!='C') {
					$pedido->setEstado('C');
					if ($pedido->save()==false) {
						foreach ($pedido->getMessages() as $message) {
							return array(
								'status' => 'FAILED',
								'message' => 'Pedido: '.$message->getMessage()
							);
						}
					}
				}
				return array(
					'status' => 'FAILED',
					'message' => 'El pedido ya está cerrado'
				);
			}

			$tipoPrecio = Settings::get('tipo_precio');
			if ($tipoPrecio=='P') {
				$almacenVenta = Settings::get('almacen_venta');
				$porcVenta = Settings::get('porc_venta');
			}

			$detailsPedido = array();
			$movilins = self::getModel('Movilin')->find('almacen="'.$nAlmacen.'" AND comprob="'.$comprob.'" AND numero="'.$nPedido.'"');
			foreach ($movilins as $movilin)
			{
				$inve = BackCacher::getInve($movilin->getItem());
				if ($inve==false) {
					continue;
				}
				$detailPedido = array(
					'id' => $movilin->getId(),
					'item' => $movilin->getItem(),
					'descripcion' => $inve->getDescripcion(),
					'unidad' => $inve->getUnidad(),
					'costo' => LocaleMath::round($movilin->getValor(), 2),
					'cantidad' => LocaleMath::round($movilin->getCantidad(), 3),
					'valor' => LocaleMath::round($movilin->getValor(), 2),
				);

				if ($tipoPrecio=='P'||$tipoPrecio=='N') {
					if ($tipoPrecio=='P') {
						$almacenVenta = Settings::get('almacen_venta');
						$porcVenta = Settings::get('porc_venta');
						if ($porcVenta>=0) {
							$costo = Tatico::getCosto($movilin->getItem(), 'I', $almacenVenta);
							$detailPedido['precio'] = LocaleMath::round($costo+($costo*$porcVenta/100), 2);
						}
					} else {
						$detailPedido['precio'] = (double) $inve->getPrecioVentaM();
					}
					$detailPedido['ivaVenta'] =  (double) $inve->getIvaVenta();
				}
				$detailsPedido[] = $detailPedido;
				unset($movilin);
			}

			$tercero = self::getRazsocByNit($pedido->getNit());
			$nombre = $tercero['status'] == 'NOT FOUND' ? '' : $tercero['nombre'];

			return array(
				'status' => 'OK',
				'data' => array(
					'nit' => $pedido->getNit(),
					'nit_det' => $nombre,
					'almacen_destino' => $pedido->getAlmacenDestino(),
					'centro_costo' => $pedido->getCentroCosto(),
					'f_vence' => $pedido->getFVence()->getDate(),
					'observaciones' => $pedido->getObservaciones(),
					'movilin' => $detailsPedido,
				)
			);
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'El pedido "'.$nPedido.'" no existe en el almacén '.$nAlmacen
			);
		}
	}

	/**
	 * Retorna un arreglo asociativo con los datos de los impuestos
	 *
	 * @param	array $items
	 * @param	int $tipo
	 * @param	int $almacen
	 * @param	string $nitTercero
	 * @return	mixed
	 */
	public static function getTaxes($items, $tipo, $codigoAlmacen, $nitTercero)
	{
		$porceCree = 0;
		$valorCree = 0;
		$almacen = BackCacher::getAlmacen($codigoAlmacen);
		if ($almacen==false) {
			return array(
				'status' => 'FAILED',
				'message' => 'No existe el almacén indicado ' . $codigoAlmacen
			);
		}

		$tercero = BackCacher::getTercero($nitTercero);
		if ($tercero == false) {
			if ($tipo == 'E') {
				return array(
					'status' => 'FAILED',
					'message' => 'La entrada al almacén requiere que se indique un proveedor válido'
				);
			} else {
				return array(
					'status' => 'FAILED',
					'message' => 'La orden de compra requiere que se indique un proveedor válido'
				);
			}
		}

		if ($tercero->getEstadoNit()==''||$tercero->getEstadoNit()=='A') {
			return array(
				'status' => 'FAILED',
				'message' => 'No se ha definido el tipo de regímen del proveedor'
			);
		}

		$empresa = self::getModel('Empresa')->findFirst();
		$terceroHotel = BackCacher::getTercero($empresa->getNit());
		if ($terceroHotel==false) {
			return array(
				'status' => 'FAILED',
				'message' => 'El hotel no ha sido creado como un tercero'
			);
		}

		if ($terceroHotel->getEstadoNit()==''||$terceroHotel->getEstadoNit()=='A') {
			return array(
				'status' => 'FAILED',
				'message' => 'No se ha definido el tipo de regímen del hotel'
			);
		}

		$messages = array();
		$hortifruticula = Settings::get('hortifruticula');
		if (self::$_taxesDebug == true) {
			$messages['hotel'] = 'El hotel es regímen '.$terceroHotel->getTipoRegimen();
			$messages['proveedor'] = 'El proveedor '.$tercero->getNit().'/'.$tercero->getNombre().
									 ' es regímen '.$tercero->getTipoRegimen().', '.$tercero->getEsAutoretenedor().
									 ' y '.$tercero->getResumenIca();
			if ($hortifruticula == 'S') {
				if ($tercero->getPlazo()=='S') {
					$messages['horti'] = 'El proveedor presentó certificado hortifrutícula y no se le calcula esta retención';
				} else {
					$messages['horti'] = 'El proveedor no presentó certificado hortifrutícula y se le calcula esta retención';
				}
			}
		}

		$result = array();
		$tablaDetalle = array();
		$ivas = array(0 => 0, 7 => 0, 10 => 0, 16 => 0, 5 => 0);
		$ivasImpo = array(0 => 0, 7 => 0, 10 => 0, 16 => 0, 5 => 0);
		$result['status'] = 'OK';
		$result['total_neto'] = 0;
		$result['total_neto_base'] = 0;
		$result['retencion'] = 0;
		$result['horti'] = 0;
		$result['cree'] = 0;
		$result['impo'] = 0;
		$result['impuestos'] = 0;

		$usarRetecompras = Settings::get('usar_retecompras','IN');

		foreach ($items as $item) {

			$inve = BackCacher::getInve($item['Item']);
			if ($inve==false) {
				continue;
			}

			$linea = BackCacher::getLinea($codigoAlmacen, $inve->getLinea());
			if ($linea == false) {
				return array(
					'status' => 'FAILED',
					'message' => 'No existe la línea de producto '.$inve->getLinea().' en el almacén '.$codigoAlmacen
				);
			}

			if (!isset($result['iva' . $item['Iva'].'d'])) {
				$result['iva' . $item['Iva'] . 'd'] = 0;
			}

			$ivaIncluido = Settings::get('iva_incluido');
			if ($ivaIncluido == 'S' || !$ivaIncluido) {
				if ($inve->getProdTrib() != 'D') {
					$valorIva = LocaleMath::round($item['Valor'] - ($item['Valor'] / (1 + $item['Iva']/100)), 2);
				} else {
					$valorIva = LocaleMath::round($item['Iva'] * $item['Valor']/100, 2);
				}
			} else {
				$valorIva = LocaleMath::round($item['Iva'] * $item['Valor']/100, 2);
			}

			$result['iva' . $item['Iva'] . 'd'] += $valorIva;
			if (!isset($ivas[$item['Iva']])) {
				$ivas[$item['Iva']] = 0;
			}
			$ivas[$item['Iva']] += $valorIva;

			//Validamos si se usa o no la tabla retecompras
			if ($usarRetecompras == 'N') {
				//Usa procentaje de lineas
				$porcCompra = $linea->getPorcCompra();
			} else {
				$porcCompra = 0;
			}

			if ($ivaIncluido == 'S' || !$ivaIncluido) {
				if ($inve->getProdTrib() != 'D') {
					$valorRetencion = ($item['Valor'] / (1 + $item['Iva']/100)) * $porcCompra;
				} else {
					$valorRetencion = $item['Valor'] * $porcCompra;
				}
			} else {
				$valorRetencion = $item['Valor'] * $porcCompra;
			}
			$result['retencion'] += $valorRetencion;

			if ($hortifruticula == 'S') {
				if ($tercero->getPlazo() != 'S') {
					$valorHorti = $item['Valor'] * $linea->getPorcHortic();
					$result['horti'] += $valorHorti;
				} else {
					$valorHorti = 0;
				}
			} else {
				$valorHorti = 0;
			}

			//Impoconsumo
			//Cuando es diferente a (D)escontable es decir al costo o al gasto impoconsume
			$valorImpo = 0;
			if ($inve->getProdTrib() != 'D') {
				$valorImpo = $valorIva;
				$result['impo'] += $valorIva;
				$ivas[$item['Iva']] -= $valorIva;
				$ivasImpo[$item['Iva']] += $valorIva;
			}

			$result['total_neto'] += $item['Valor'];

			if ($ivaIncluido == 'S' || !$ivaIncluido) {
				if ($inve->getProdTrib() != 'D') {
					$valorBase = ($item['Valor'] / (1 + $item['Iva']/100));
				} else {
					$valorBase = $item['Valor'];
				}
			} else {
				$valorBase = $item['Valor'];
			}
			$result['total_neto_base'] += $valorBase;

			if (self::$_taxesDebug == true) {
				$tablaDetalle[] = array(
					'item'           => $item['Item'],
					'nombre'         => $inve->getDescripcion(),
					'valor'          => $item['Valor'],
					'iva'            => $item['Iva'],
					'valorIva'       => $valorIva,
					'linea'          => $inve->getLinea(),
					'lineaNombre'    => $linea->getNombre(),
					'porcRetencion'  => $linea->getPorcCompra(),
					'valorRetencion' => $valorRetencion,
					'porcHorti'      => $linea->getPorcHortic(),
					'porceCree'      => $porceCree,
					'valorHorti'     => $valorHorti,
					'valorCree'      => $valorCree,
					'valorImpo'      => $valorImpo,
					'valorBase'      => $valorBase
				);
			}
			unset($item);
		}

		$porcCompra = 0;
		$baseRetencion = Settings::get('base_retencion');
		if ($usarRetecompras == 'N' || !$usarRetecompras) {
			//Usa procentaje de lineas
			$porcCompra = $linea->getPorcCompra();
		} else {
			//Usa la base de retencion y porcentaje de retencion en base al retecompras del tercero
			if ($tercero->getRetecomprasId() > 0) {
				$retecompras = EntityManager::get('Retecompras')->findFirst($tercero->getRetecomprasId());
				if (!$retecompras) {
					$messages['retencion'] = "No se ha definido una retención de compras en el tercero correctamente";
					$baseRetencion = 0;
					$porcCompra = 0;
				} else {
					$baseRetencion = $retecompras->getBaseRetencion();
					$porcCompra = $retecompras->getPorceRetencion() / 100;
				}
			} else {
				$messages['retencion'] = "No se ha definido una retención de compras en el tercero";
			}
		}

		if (($usarRetecompras == 'N' || !$usarRetecompras)) {

			$result['retencion'] = 0;
			if ($result['total_neto'] < $baseRetencion) {
				if (self::$_taxesDebug == true) {
					if ($baseRetencion) {
						$messages['retencion'] = 'No se calcula retención porque el total de la entrada o orden no supera el valor mínimo de retención de ' . Settings::get('base_retencion');
					} else {
						$messages['retencion'] = 'No se calcula retención porque el valor mínimo de retención no está definido';
					}
				}
			}

		} else {

			if ($usarRetecompras == 'S') {
				//Retencion por tercero
				$result['retencion'] = LocaleMath::round($result['total_neto_base'] * $porcCompra, 2);
			} else {
				//retencion por linea
				$result['retencion'] = LocaleMath::round($result['retencion'], 2);
			}

			if (self::$_taxesDebug == true) {
				if ($result['total_neto'] < $baseRetencion) {
					if ($baseRetencion) {
						$messages['retencion'] = 'Se calcula retención porque el total de la entrada o orden supera el valor mínimo de retención de '.Settings::get('base_retencion');
					} else {
						$messages['retencion'] = 'No se calcula retención porque el valor mínimo de retención no está definido';
					}
				}
			}
		}

		if ($result['horti'] > 0) {
			$result['horti'] = LocaleMath::round($result['horti'], 2);
		}

		if (!isset($result['iva5d'])) {
			$result['iva5d'] = 0;
			$result['iva5r'] = 0;
		}

		if (!isset($result['iva10d'])) {
			$result['iva10d'] = 0;
			$result['iva10r'] = 0;
		}

		if (!isset($result['iva16d'])) {
			$result['iva16d'] = 0;
			$result['iva16r'] = 0;
		}

		$calcularIVARetenido = false;
		if ($terceroHotel->getEstadoNit() == 'C') {
			if ($tercero->getEstadoNit() == 'S') {
				$calcularIVARetenido = true;
				if (self::$_taxesDebug == true) {
					$messages['ivar'] = 'Se calcula IVA retenido porque el regímen del hotel es común y el del proveedor es simplificado';
				}
			}
		} else {
			if ($terceroHotel->getEstadoNit()=='G') {
				if ($tercero->getEstadoNit()=='S' || $tercero->getEstadoNit()=='C') {
					$calcularIVARetenido = true;
					if (self::$_taxesDebug==true) {
						$messages['ivar'] = 'Se calcula IVA retenido porque el regímen del hotel es gran contribuyente y el del proveedor es simplificado ó común';
					}
				}
			} else {
				if (self::$_taxesDebug==true) {
					$messages['ivar'] = 'No se calcula IVA retenido porque el hotel es regimen simplificado';
				}
			}
		}

		//Usa retencion y porcentaje retenido de settings
		$porcIvaRetenido = Settings::get('porc_iva_ret');
		if ($calcularIVARetenido) {
			if ($result['total_neto'] < $baseRetencion) {
				$result['iva5r'] = 0;
				$result['iva10r'] = 0;
				$result['iva16r'] = 0;
				if (self::$_taxesDebug==true) {
					$messages['ivad'] = 'No se calcula IVA retenido porque el total de la orden ó entrada no supera el mínimo de retención de ' . $baseRetencion;
				}
			} else {

				$result['iva5r'] = LocaleMath::round($result['iva5d'] * $porcIvaRetenido/100, 2);
				$result['iva10r'] = LocaleMath::round($result['iva10d'] * $porcIvaRetenido/100, 2);
				$result['iva16r'] = LocaleMath::round($result['iva16d'] * $porcIvaRetenido/100, 2);
				if (self::$_taxesDebug==true) {
					$messages['ivad'] = 'Se calcula el '.$porcIvaRetenido.'% de IVA retenido porque el total de la orden ó entrada supera el mínimo de retención de ' . $baseRetencion;
				}
			}
		} else {
			$result['iva5r'] = 0;
			$result['iva10r'] = 0;
			$result['iva16r'] = 0;
		}

		$porcIvaDescontable = Settings::get('porc_iva_des');

		$result['iva5d'] = LocaleMath::round($result['iva5d'], 2);
		$result['iva10d'] = LocaleMath::round($result['iva10d'], 2);
		$result['iva16d'] = LocaleMath::round($result['iva16d'], 2);

		//Descontamos impo al costo y gasto
		$result['iva5d'] -= LocaleMath::round($ivasImpo[5], 2);
		$result['iva10d'] -= LocaleMath::round($ivasImpo[10], 2);
		$result['iva16d'] -= LocaleMath::round($ivasImpo[16], 2);

		if ($result['iva5d']>0 || $result['iva10d']>0 || $result['iva16d']>0) {
			if (self::$_taxesDebug==true) {
				$messages['ivad'] = 'Se calcula el total de IVA descontable porque el proveedor es regímen común ó simplificado';
			}
		}

		if ($ivasImpo[5]>0 || $ivasImpo[10]>0 || $ivasImpo[16]>0) {
			if (self::$_taxesDebug == true) {
				$messages['ivad'] = 'Se calcula el Impoconsumo porque algunas referencias estan al costo o al gasto';
			}
		}

		if ($tercero->getAutoRet()=='N') {
			if ($tercero->getApAereo() > 0) {
				$result['ica'] = LocaleMath::round($tercero->getApAereo() * $result['total_neto']/1000, 2);
				if (self::$_taxesDebug==true) {
					$messages['ica'] = 'Se calcula ICA porque el proveedor no es autoretenedor sobre un porcentaje de '.$tercero->getApAereo();
				}
			} else {
				$result['ica'] = 0;
				if (self::$_taxesDebug==true) {
					$messages['ica'] = 'No se calcula ICA porque el proveedor no tiene un porcentaje de ICA definido';
				}
			}
		} else {
			if (self::$_taxesDebug==true) {
				$messages['retencion'] = 'No se calcula retención porque el proveedor es autoretenedor';
				$messages['ica'] = 'No se calcula ICA porque el proveedor es autoretenedor';
			}
			$result['ica'] = 0;
			$result['retencion'] = 0;
		}

		if ($tercero->getEstadoNit()!='G' && $tercero->getPorceCree() > 0) {
			$result['cree'] = LocaleMath::round($tercero->getPorceCree() * $result['total_neto']/100, 2);
			if (self::$_taxesDebug == true) {
				$messages['cree'] = 'Se calcula CREE porque el proveedor no es GRAN CONTRIBUYENTE sobre un porcentaje de '.$tercero->getPorceCree();
			}
		} else {
			$result['cree'] = 0;
			if (self::$_taxesDebug == true) {
				$messages['cree'] = 'No se calcula CREE porque el proveedor no tiene un porcentaje de CREE definido';
			}
		}

		$result['impuestos'] = ($result['iva5d'] + $result['iva10d'] + $result['iva16d']) -
						       ($result['iva5r'] + $result['iva10r'] + $result['iva16r']);
		$result['impuestos'] = LocaleMath::round($result['impuestos'], 2);

		$result['saldo'] = ($result['iva5d'] + $result['iva10d'] + $result['iva16d']) -
						   ($result['iva5r'] + $result['iva10r'] + $result['iva16r']) -
						   ($result['cree'] + $result['horti'] + $result['retencion'] + $result['ica']);
		$result['saldo'] = LocaleMath::round($result['saldo'], 2);

		$result['total'] = $result['total_neto'] + $result['saldo'];
		$result['total'] = LocaleMath::round($result['total'], 2);

		if (self::$_taxesDebug == false) {
			$result['criterios'] = array();
			$criterios = self::getModel('Criterios')->find(array("estado='A' AND tipo='O'", 'columns' => 'id'));
			foreach ($criterios as $criterio) {
				$conditions = "nit='$nitTercero' AND criterios_id='{$criterio->getId()}'";
				$promedio = self::getModel('CriteriosProveedores')->average(array('puntaje', 'conditions' => $conditions));
				if ($promedio !== null) {
					$result['criterios'][$criterio->getId()] = floor($promedio);
				}
			}
		} else {
			$html = '<html>';
			$html.= '<head>
				'.Tag::stylesheetLink('hfos/general').'
				'.Tag::stylesheetLink('hfos/app/IN').'
			</head>';
			$html.= '<body>
			<p class="detalleCalculoP">
				<b>Detalle del Cálculo de Impuestos</b><br/>
				<br/>
				El detalle del cálculo explica como se producen los totales de impuestos y valor a pagar en el movimiento
				basado en las referencias, ivas y parametrización actual del sistema. Los valores pueden ser diferentes
				a lo actual si ha modificado manualmente los totales.
			</p>
			<ul class="detalleCalculoUl">';
			foreach ($messages as $message) {
				$html .= '<li>' . $message . '</li>' . PHP_EOL;
			}
			$html.= '</ul>';

			if (count($tablaDetalle) > 0) {

				$htmlTemp = "";
				$porceRetencionTemp = "";
				if ($usarRetecompras=='N') {
					$htmlTemp = "
					<th>% Retención</th>
					<th>Valor Retención</th>";
				} else {
					if (isset($retecompras) && $retecompras) {
						$porceRetencionTemp = "(" . $retecompras->getPorceRetencion() . "%)";
					} else {
						$porceRetencionTemp = "(?%)";
					}
				}

				$totalIva = 0;
				$totalValor = 0;
				$totalHorti = 0;
				$totalRetencion = 0;
				$totalCree = 0;
				$totalImpo = 0;
				$html.='<table cellspacing="0" class="detalleCalculo">
				<thead>
					<tr>
						<th>Código</th>
						<th>Nombre</th>
						<th>Valor</th>
						<th>Valor Base</th>
						<th>% IVA</th>
						<th>Valor IVA</th>
						<th>Línea</th>
						<th>Nombre</th>
						'.$htmlTemp.'
						<!--<th>Valor Cree</th>-->
						<th>Valor Impoconsumo</th>';
				if ($hortifruticula == 'S') {
					$html .= '<th>% Ret. Hortífruticula</th>
					<th>Valor Ret. Hortífruticula</th>';
				}
				$html .= '</tr></thead>';
				foreach ($tablaDetalle as $detalle) {
					$html.='<tr>
						<td align="right">'.$detalle['item'].'</td>
						<td>' . $detalle['nombre'] . '</td>
						<td align="right">' . Currency::number($detalle['valor']) . '</td>
						<td align="right">' . Currency::number($detalle['valorBase']) . '</td>
						<td align="right">' . $detalle['iva'] . ' %</td>
						<td align="right">' . Currency::number($detalle['valorIva']) . '</td>
						<td align="right">' . $detalle['linea'] . '</td>
						<td>' . $detalle['lineaNombre'] . '</td>';
						if ($usarRetecompras == 'N') {
							$html .= '<td align="right">' . $detalle['porcRetencion'] . ' %</td>
							<td align="right">' . Currency::number($detalle['valorRetencion']) . '</td>';
						}
						//$html .= '<td align="right">'.Currency::number($detalle['valorCree']).'</td>
						$html .= '<td align="right">'.Currency::number($detalle['valorImpo']).'</td>';
					if ($hortifruticula=='S') {
						$html.='<td align="right">'.$detalle['porcHorti'].' %</td>
						<td align="right">'.Currency::number($detalle['valorHorti']).'</td>';
					}
					$html.='</tr>';
					$totalValor += $detalle['valor'];
					$totalIva += $detalle['valorIva'];
					$totalRetencion += $detalle['valorRetencion'];
					$totalHorti += $detalle['valorHorti'];
					$totalCree += $detalle['valorCree'];
					$totalImpo += $detalle['valorImpo'];
				}
				$html .= '<tr>
					<td colspan="2">&nbsp;</td>
					<td align="right">'.Currency::number($totalValor).'</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right">'.Currency::number($totalIva).'</td>';
					if ($usarRetecompras=='N') {
						$html .= '<td colspan="3">&nbsp;</td>
						<td align="right">'.Currency::number($totalRetencion).'</td>';
					} else {
						$html .= '<td colspan="2">&nbsp;</td>';
					}
					$html .= '<!--<td align="right">'.Currency::number($totalCree).'</td>-->
					<td align="right">'.Currency::number($totalImpo).'</td>';
				if ($hortifruticula=='S') {
					$html.='<td>&nbsp;</td>
					<td align="right">'.Currency::number($totalHorti).'</td>';
				}
				$html.='</tr>';
				$html.='</table>';

				$html.='<table cellspacing="0" class="detalleCalculo">
					<tr><td align="right" width="50%">Total IVA 16%</td><td align="right">'.Currency::number($ivas[16]).'</td></tr>
					<tr><td align="right">Total IVA 10%</td><td align="right">'.Currency::number($ivas[10]).'</td></tr>
					<tr><td align="right">Total IVA 5%</td><td align="right">'.Currency::number($ivas[7]).'</td></tr>';

				$totalImpuestos = ($result['iva16d'] + $result['iva10d'] + $result['iva5d']) - ($result['iva16r'] + $result['iva10r'] + $result['iva5r']);
				$html.='
					<tr><td align="right" width="50%">IVA 16% Retenido</td><td align="right">'.Currency::number($result['iva16r']).'</td></tr>
					<tr><td align="right">IVA 10% Retenido</td><td align="right">'.Currency::number($result['iva10r']).'</td></tr>
					<tr><td align="right">IVA 5% Retenido</td><td align="right">'.(@Currency::number($result['iva5r'])).'</td></tr>
					<tr><td align="right">IVA 16% Descontable</td><td align="right">'.Currency::number($result['iva16d']).'</td></tr>
					<tr><td align="right">IVA 10% Descontable</td><td align="right">'.Currency::number($result['iva10d']).'</td></tr>
					<tr><td align="right">IVA 5% Descontable</td><td align="right">'.(@Currency::number($result['iva5d'])).'</td></tr>
					<tr><td align="right"><b>Total Impuestos</b></td><td align="right">'.Currency::number($totalImpuestos).'</td></tr>
				</table>

				<table cellspacing="0" class="detalleCalculo">
					<tr><td align="right">IVA Mayor Valor Costo/Gasto 5%</td><td align="right">'.(@Currency::number($ivasImpo[5])).'</td></tr>
					<tr><td align="right">IVA Mayor Valor Costo/Gasto 10%</td><td align="right">'.(@Currency::number($ivasImpo[10])).'</td></tr>
					<tr><td align="right">IVA Mayor Valor Costo/Gasto 16%</td><td align="right">'.(@Currency::number($ivasImpo[16])).'</td></tr>
				</table>';

				$html.='<table cellspacing="0" class="detalleCalculo">
					<tr><td align="right" width="50%"><b>Total ICA</b></td><td align="right">'.Currency::number($result['ica']).'</td></tr>
					<tr><td align="right"><b>Total Retención ' . $porceRetencionTemp . '</b></td><td align="right">'.Currency::number($result['retencion']).'</td></tr>';
				if ($hortifruticula=='S') {
					$html.='<tr><td align="right"><b>Total Ret. Hortifrutícula</b></td><td align="right">'.Currency::number($result['horti']).'</td></tr>';
				}
				$html.='</table>';

				$html.='<table cellspacing="0" class="detalleCalculo">
					<tr><td align="right" width="50%"><b>Total Entrada</b></td><td align="right">'.Currency::number($result['total_neto']).'</td></tr>
					<tr><td align="right"><b>Total a Pagar</b></td><td align="right">'.Currency::number($result['total']).'</td></tr>
				</table>';

			} else {
				$html.='<div class="noticeMessage">Es necesario agregar referencias para hacer el calculo de impuestos</div>'.PHP_EOL;
			}
			$html.='</body></html>';

			$fileName = 'temp/calculo-' . uniqid(null, true) . '.html';
			file_put_contents('public/' . $fileName, $html);

			$result['file'] = $fileName;
		}

		return $result;
	}

	/**
	 * Retorna la razón social de un Tercero
	 *
	 * @param string $nit
	 * @return array
	 */
	public static function getRazsocByNit($nit)
	{
		$nit = self::getModel('Nits')->findFirst("nit='$nit'");
		if ($nit == false) {
			return array(
				'status' => 'NOT FOUND',
				'message' => 'El tercero \''.$nit.'\' no existe'
			);
		}
		return array(
			'status' => 'OK',
			'nombre' => $nit->getNombre(),
		);
	}

	/**
	 * Metodo que calcula los valores unitarios de las transformaciones
	 *
	 * @param $datos [
	 *  items array
	 *  cantidades array
	 *  valorTotal
	 * ]
	 * @return array [
	 *  status,
	 *  message,
	 *  datos
	 * ]
	 */
	public static function getCalcularTransformacion($datos)
	{

		$items = $datos['items'];
		$cantidades = $datos['cantidades'];
		$valorTotal = $datos['valorTotal'];
		$nota = $datos['nota'];
		$cantidad_objetivo = $datos['cantidad_objetivo'];

		if (isset($datos['debug'])) {
			$debug = $datos['debug'];
		} else {
			$debug = false;
		}

		if (!is_array($items)) {
			throw new TaticoException('Debe ingresar las referencias asociadas a la tranformación');
		}

		if (!is_array($cantidades)) {
			throw new TaticoException('Debe ingresar cantidades asociadas a las referencias');
		}

		if (!$cantidad_objetivo) {
			throw new TaticoException('Debe ingresar cantidad asociada a las referencia base');
		}

		if (!$nota) {
			throw new TaticoException('Se debe elegir el Tipo de Trasformación');
		}

		//Solo si es 1N se verifica la referencia base
		if (!$valorTotal && $nota == "1N") {
			throw new TaticoException('La referencia base no tiene existencias suficientes en almacén');
		}

		$valorTotalPeso = 0;
		$itemsCalculos = array();
		foreach ($items as $index => $item) {

			//Siempre que se consulta un dato básico hay que usar BackCacher así
			$inve = BackCacher::getInve($item);
			if ($inve==false) {
				throw new TaticoException('La referencia ('.$item.') no existe');
			} else {
				if ($inve->getEstado()<>'A') {
					throw new TaticoException('La referencia ('.$item.') está inactiva');
				}
			}

			//Obtengo valores de referencia
			$cantidad = $cantidades[$index];
			$peso = $inve->getPeso();

			//Sino hay un peso asignado a la referencia
			if ($peso <= 0) {
				throw new TaticoException('La referencia "'.$inve->getDescripcion().'" debe tener un peso definido mayor a cero');
			}

			$pesoPorCantidad = ($peso * $cantidad);
			$itemsCalculos[$inve->getItem()] = array(
				'item' => $inve->getItem(),
				'peso' => $peso,
				'cantidadPeso' => $cantidad,
				'pesoPorCant' => $pesoPorCantidad
			);

			//Acumulamos los valores calculados de peso
			$valorTotalPeso += $pesoPorCantidad;
			unset($item);
		}

		//Ahora sabiendo el valor total de pesos de todos los items sacamos los valores unitarios según peso
		foreach ($itemsCalculos as $index => $itemCalculos) {
			$divisor = ($valorTotalPeso * $itemCalculos['peso']);
			if ($debug) {
				echo "<br>(" . $valorTotal . "/" . $valorTotalPeso.") *".$itemCalculos['peso']."=".($valorTotal / $valorTotalPeso * $itemCalculos['peso']);
			}
			$valorUnitario = (($valorTotal / $valorTotalPeso) * $itemCalculos['peso']);
			$itemsCalculos[$index]['valorUnitario'] = $valorUnitario;
			$itemsCalculos[$index]['valor'] = $itemsCalculos[$index]['valorTotalPeso'] = round($valorUnitario * $itemsCalculos[$index]['cantidadPeso'],2);
			unset($itemCalculos);
		}

		if ($debug) {
			throw new TaticoException(var_dump($itemsCalculos));
		}

		return $itemsCalculos;
	}

	/**
	 * Metodo que hace la conversión de la cantidad de una unidad inicial a una unidad final
	 *
	 * @param		int $unidadInicialId Es el ID de la unidad de la cantidad
	 * @param		int $unidadFinalId Es el ID de la unidad a convetir
	 * @param		int $cantidad Es la cantidad dela unidad inicial que se desa convertir
	 * @return	int $cantidadConvertida
	 */
	public static function convertirUnidad($unidadInicialId, $unidadFinalId, $cantidad)
	{
		$cantidadConvertida = 0;
		if (!$unidadInicialId) {
			throw new TaticoException('Se debe indicar el Id de la unidad inicial');
		}
		if (!$unidadFinalId) {
			throw new TaticoException('Se debe indicar el Id de la unidad final');
		}
		if (!$cantidad) {
			throw new TaticoException('Se debe indicar la cantidad a convertir');
		}

		$unidadInicial = BackCacher::getUnidad($unidadInicialId);
		if (!$unidadInicial) {
			throw new TaticoException('La unidad inicial no existe');
		}

		$unidadFinal = BackCacher::getUnidad($unidadFinalId);
		if (!$unidadFinal) {
			throw new TaticoException('La unidad final no existe');
		}

		if ($unidadInicial->getMagnitud()!=$unidadFinal->getMagnitud()) {
			throw new TaticoException('Las unidades deben tener la misma magnitud');
		}

		$conversionUnidades = BackCacher::getConversionUnidades($unidadInicialId, $unidadFinalId);
		if (!$conversionUnidades || !$conversionUnidades->getFactorConversion()) {
			throw new TaticoException('No se encontró registro de conversión para las dos unidades');
		}

		$cantidadConvertida = $cantidad * $conversionUnidades->getFactorConversion();
		return $cantidadConvertida;
	}

	/**
	 * Genera la impresión en HTML ó Texto de una impresión
	 *
	 * @param 	string $format
	 * @param	string $comprob
	 * @param	integer $codigoAlmacen
	 * @param	integer $numero
	 * @param 	boolean $firstPrint
	 * @param 	boolean $lastPrint
	 * @return	string
	 */
	public static function getPrint($format, $comprob, $codigoAlmacen, $numero, $firstPrint=true, $lastPrint=true)
	{

		$empresa = BackCacher::getEmpresa();

		$movihead = self::getModel('Movihead')->findFirst("comprob='$comprob' AND almacen='$codigoAlmacen' AND numero='$numero'");
		if ($movihead==false) {
			throw new TaticoException('No existe la transacción '.$comprob.'-'.$codigoAlmacen.'-'.$numero);
		}

		$tipoComprob = substr($comprob, 0, 1);
		switch ($tipoComprob) {
			case 'O':
				$tipoDetalle = 'ORDEN DE COMPRA';
				break;
			case 'E':
				$tipoDetalle = 'ENTRADA AL ALMACEN';
				break;
			case 'C':
				$tipoDetalle = 'SALIDA POR CONSUMO';
				break;
			case 'A':
				$tipoDetalle = 'AJUSTE AL INVENTARIO';
				break;
			case 'T':
				$tipoDetalle = 'TRASLADO ENTRE ALMACENES';
				break;
			case 'P':
				$tipoDetalle = 'PEDIDO AL ALMACÉN';
				break;
			case 'R':
				$tipoDetalle = 'TRANSFORMACIÓN';
				break;
		}

		$condiciones = Settings::get('condiciones');
		$hortifruticula = Settings::get('hortifruticula');

		if ($tipoComprob == 'O' || $tipoComprob == 'E') {

			$movih1 = self::getModel('Movih1')->findFirst("comprob='$comprob' AND numero='$numero'");
			if ($movih1 == false) {
				throw new TaticoException('No existe la transacción '.$comprob.'-'.$codigoAlmacen.'-'.$numero);
			}

			$tercero = BackCacher::getTercero($movihead->getNit());
			if ($tercero == false) {
				throw new TaticoException('No existe el tercero '.$movihead->getNit());
			}
		}

		$content = '';
		if ($format == 'text') {
			$content = "{$empresa->getNombre()} NIT No : {$empresa->getNit()}   Fecha de Impresion:".Date::getCurrentDate()."\r\n";
			$content.= str_repeat(" ", 30).$tipoDetalle." No. $numero\tFECHA {$movihead->getFecha()->getDate()}\r\n";
			if ($tipoComprob=='E') {
				$content.= str_repeat(' ', 30)."FACTURA NUMERO         : {$movihead->getFacturaC()}\r\n";
			}
			if ($tipoComprob == 'O' || $tipoComprob == 'E') {
				$content.= "Senor(es) :\r\n";
				$content.= "{$tercero->getNombre()} ({$tercero->getNit()})\r\n";
				$content.= "{$tercero->getDireccion()}\tTel: {$tercero->getTelefono()}\r\n";
				$content.= "{$tercero->getCiudad()}\r\n";
			}
			if ($tipoComprob=='O') {
				$content.= "   Favor Despachar por nuestra cuenta la siguiente Mercancia :\r\n";
			}
			$content.= "Cons Referencia   D e s c r i p c i o n     Und   Sldo Actual Cant.Solic  Vlr Unitario   Valor Total  Iva \r\n";
		} else {
			if ($firstPrint==true) {
				$content = '<html>
				<head>
					<style type="text/css">
						body, td, p, th {
							font-family: Helvetica,arial,sans-serif;
							font-size: 12px;
						}
						table, td {
							max-width: 700px;
						}
						table.firmas {
							margin-top: 30px;
							width: 670px;
						}
						table.detalle {
							border-right: 1px solid #d1d1d1;
							border-bottom: 1px solid #d1d1d1;
						}
						table.detalle th {
							background: #dadada;
						}
						table.detalle th,
						table.detalle td {
							border-left: 1px solid #d1d1d1;
							border-top: 1px solid #d1d1d1;
							padding: 2px;
						}
						div.pageBreak {
							page-break-after: always;
						}
					</style>
				</head>
				<body>';
			} else {
				$content.='<br/><br/>';
			}

			$content .= '<table width="100%">
					<tr>
						<td><b>'.$empresa->getNombre().' NIT No: '.$empresa->getNit().'</b></td>
						<td align="right">Fecha de Impresión: '.Date::getCurrentDate().'</td>
					</tr>
					<tr>
						<td>'.$tipoDetalle.' No. '.$movihead->getComprob().'-'.$numero.'</td>
						<td align="right">Fecha: '.$movihead->getFecha().'</td>
					</tr>';

			if ($tipoComprob == 'E' || $tipoComprob == 'A' || $tipoComprob == 'A' || $tipoComprob == 'T') {
				$comprobInve = BackCacher::getComprob($movihead->getComprob());
				if ($tipoComprob == 'E') {
					$content.= '<tr>
						<td>Comprobante Contable: ' .$comprobInve->getComprobContab(). '-' . $movihead->getNumeroComprobContab(). '</td>
						<td align="right">Factura Número: '.$movihead->getFacturac().'</td>
					</tr>';
				} else {
					$content.= '<tr>
						<td>Comprobante Contable: ' .$comprobInve->getComprobContab(). '-' . $movihead->getNumeroComprobContab(). '</td>
						<td align="right"></td>
					</tr>';
				}
			}
			if ($tipoComprob=='O' || $tipoComprob=='E') {
				$content.= '<tr>
					<td colspan="2">
						<br/>
						Señor(es)<br/>
						'.$tercero->getNombre().' ('.$tercero->getNit().')<br/>
						'.$tercero->getDireccion().' Tel: '.$tercero->getTelefono().'<br/>
						'.$tercero->getCiudad().'<br/>
					</td>
				</tr>';
			} else {
				$almacen = BackCacher::getAlmacen($codigoAlmacen);
				$content.= '<tr>
					<td>Almacén: '.$almacen->getCodigo().'/'.$almacen->getNomAlmacen().'</td>
				</tr>';
			}
			if ($tipoComprob=='T' || $tipoComprob=='P') {
				$almacen = BackCacher::getAlmacen($movihead->getAlmacenDestino());
				if ($almacen!=false) {
					$content.= '<tr>
						<td>Almacén Destino: '.$almacen->getCodigo().'/'.$almacen->getNomAlmacen().'</td>
					</tr>';
				}
			}
			if ($tipoComprob=='C' || $tipoComprob=='P') {
				$centroCosto = BackCacher::getCentro($movihead->getCentroCosto());
				if ($centroCosto!=false) {
					$content.= '<tr>
						<td>Centro Costo: '.$centroCosto->getCodigo().'/'.$centroCosto->getNomCentro().'</td>
					</tr>';
				}
			}

			$content.='</table><br/><br/>';
			if ($tipoComprob=='O') {
				$content.= '<p>Favor despachar por nuestra cuenta la siguiente mercancia:</p>';
			}
			$content.= '<table class="detalle" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th>Cons</th>
						<th>Referencia</th>
						<th>Descripcion</th>
						<th>Unidad</th>
						<th>Saldo Actual</th>
						<th>Cant. Solic</th>
						<th>Vlr Unitario</th>
						<th>Valor Total</th>
						<th>Iva</th>';
			if ($tipoComprob=='C') {
				if ($condiciones=='S') {
					$content.='<th>Cond.Org.</th>
					<th>Empaque</th>
					<th>Fecha Vcto.</th>';
				}
			}
			$content.= '</tr>
				</thead>
			<tbody>';
		}

		$i = 1;

		if ($tipoComprob == 'T') {
			$movilins = self::getModel('Movilin')->find("almacen='$codigoAlmacen' AND comprob='$comprob' AND numero='$numero' AND almacen<>almacen_destino");
		} else {
			$movilins = self::getModel('Movilin')->find("almacen='$codigoAlmacen' AND comprob='$comprob' AND numero='$numero' AND almacen=almacen_destino");
		}
		foreach ($movilins as $movilin)
		{

			$inve = BackCacher::getInve($movilin->getItem());
			if ($inve==false) {
				continue;
			}

			$unidad = BackCacher::getUnidad($inve->getUnidad());
			if ($unidad==false) {
				$unidad = '???';
			} else {
				$unidad = $unidad->getNomUnidad();
			}

			$conditions = "item='{$movilin->getItem()}' AND almacen='$codigoAlmacen' AND ano_mes = 0";
			$saldos = self::getModel('Saldos')->findFirst($conditions);
			if ($saldos == false) {
				$saldoActual = 0;
			} else {
				$saldoActual = $saldos->getSaldo();
			}
			if ($format=='text') {
				$content.= sprintf('%4s', $i++)."  ".sprintf('%-10s', $movilin->getItem())." ".sprintf('%23.23s', $inve->getDescripcion())."  ".
				sprintf('%3.3s', $unidad).str_repeat(" ", 6).sprintf('%8s', Currency::number($saldoActual, 2))." ".
				sprintf('%10s', Currency::number($movilin->getCantidad(), 3))." ".
				sprintf('%13s', Currency::number($movilin->getValor() / $movilin->getCantidad(), 2)).
				sprintf('%14s', Currency::number($movilin->getValor(), 2))."   ".
				sprintf('%02s', (float)$movilin->getIva())."\r\n";
			} else {
				$content.= '<tr>
					<td align="right">'.($i++).'</td>
					<td width="40" align="right">'.$movilin->getItem().'</td>
					<td>'.$inve->getDescripcion().'</td>
					<td>'.$unidad.'</td>
					<td align="right">'.Currency::number($saldoActual, 2).'</td>
					<td align="right">'.Currency::number($movilin->getCantidad(), 3).'</td>
					<td align="right">'.Currency::number($movilin->getCantidad() > 0 ? $movilin->getValor() / $movilin->getCantidad() : 0, 2).'</td>
					<td align="right">'.Currency::number($movilin->getValor(), 2).'</td>
					<td align="right">'.$movilin->getIva().'</td>';
				if ($tipoComprob=='C') {
					if ($condiciones=='S') {
						$content.='<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>';
					}
				}
				$content.= '</tr>';
			}
			unset($movilin);
		}
		unset($movilins);

		if ($format=='text') {
			for ($i = 0; $i < 8; $i++) {
				$content.= "\r\n";
			}
			if ($tipoComprob=='O' || $tipoComprob=='E') {
				$content.= str_repeat('-', 86)."\r\n";
				$content.= str_repeat(" ", 15)."TOTAL COMPRA :".sprintf('%14s', Currency::number($movihead->getTotalNeto(), 2));
				$content.= str_repeat(" ", 15)."VALOR IVA 16%:".sprintf('%14s', Currency::number($movihead->getIva(), 2));
				$content.= str_repeat(" ", 12)."RETEIVA 16%  :".sprintf('%14s', Currency::number($movihead->getDescuento(), 2))."\r\n";
				$content.= str_repeat(" ", 15)."VALOR IVA 10%:".sprintf('%14s', Currency::number($movihead->getIvad(), 2));
				$content.= str_repeat(" ", 12)."RETEIVA 10%  :".sprintf('%14s', Currency::number($movihead->getIvam(), 2))."\r\n";
				$content.= str_repeat(" ", 15)."VALOR IVA 5%:".sprintf('%14s', Currency::number($movih1->getIva5(), 2));
				$content.= str_repeat(" ", 12)."RETEIVA 5%  :".sprintf('%14s', Currency::number($movih1->getRetIva5(), 2))."\r\n";
				$content.= str_repeat(" ", 12)."VR RETENCION :".sprintf('%14s', Currency::number($movihead->getRetencion(), 2))."\r\n";
				$content.= str_repeat(" ", 55)."VALOR  I C A :".sprintf('%14s', Currency::number($movihead->getIca(), 2))."\r\n";
				$content.= str_repeat(" ", 55)."IVA COSTO/GASTO :".sprintf('%14s', Currency::number($movihead->getImpo(), 2))."\r\n";
				$content.= str_repeat(" ", 55)."T O T A L    :".sprintf('%14s', Currency::number($movihead->getVTotal(), 2))."\r\n";
				$content.= str_repeat('=', 86)."\r\n";
			}

			//$currency = new Currency();
			//$content.= "Son :".$currency->getMoneyAsText($movihead->getSaldo())."\r\n";
			$content.= "Son :\r\n";

			$content.= "\r\n\r\n" . str_repeat('-', 25).str_repeat(" ", 20).str_repeat('-', 25)."\r\n";
			$content.= str_repeat(" ", 5)."Jefe de Compras".str_repeat(" ", 28)."Vo.Bo. Gerencia\r\n";
			$content.= "\r\n\r\n\r\n";

		} else {
			$content.= '</tbody></table>';

			if ($tipoComprob=='O' || $tipoComprob=='E') {
				$criterioPuntos = Settings::get('criterio_puntos');
				if ($criterioPuntos == 'N') {
					$content.= '<br/><table class="detalle" cellspacing="0" cellpadding="0" width="150"><tbody>';
					$criterio = self::getModel('Criterio')->findFirst("almacen='$codigoAlmacen' AND comprob='$comprob' AND numero='$numero'");
					if ($criterio == false) {
						$content.= "<tr><td align='right'>SC</td><td>&nbsp;</td><td align='right'>MAJ</td><td>&nbsp;</td></tr>";
						$content.= "<tr><td align='right'>UP</td><td>&nbsp;</td><td align='right'>PD</td><td>&nbsp;</td></tr>";
						$content.= "<tr><td align='right'>PR</td><td>&nbsp;</td><td align='right'>TRA</td><td>&nbsp;</td></tr>";
						$content.= "<tr><td align='right'>CTE</td><td>&nbsp;</td><td align='right'>FRA</td><td>&nbsp;</td></tr>";
					} else {
						$content.= "<tr><td align='right'>SC</td><td>". ($criterio->getSc()=="S" ? "X" : ""). "&nbsp;</td><td align='right'>MAJ</td><td>". ($criterio->getMaj()=="S" ? "X" : ""). "&nbsp;</td></tr>";
						$content.= "<tr><td align='right'>UP</td><td>". ($criterio->getUp()=="S" ? "X" : ""). "&nbsp;</td><td align='right'>PD</td><td>".($criterio->getPd()=="S" ? "X" : ""). "&nbsp;</td></tr>";
						$content.= "<tr><td align='right'>PR</td><td>". ($criterio->getPr()=="S" ? "X" : ""). "&nbsp;</td><td align='right'>TRA</td><td>".($criterio->getTra()=="S" ? "X" : ""). "&nbsp;</td></tr>";
						$content.= "<tr><td align='right'>CTE</td><td>". ($criterio->getCte()=="S" ? "X" : ""). "&nbsp;</td><td align='right'>FRA</td><td>".($criterio->getFra()=="S" ? "X" : "")."&nbsp;</td></tr>";
					}
					$content.= '</tbody></table>';
				}
			}

			$content.= "<br/><table class='detalle' cellspacing='0' cellpadding='0'>";
			if ($tipoComprob == 'O' || $tipoComprob == 'E') {
				$content.= "<tr><td align='right'>TOTAL COMPRA</td><td align='right'>".Currency::number($movihead->getTotalNeto(), 2)."</td></tr>";
				$content.= "<tr><td align='right'>VALOR IVA 16%</td><td align='right'>".Currency::number($movihead->getIva(), 2)."</td></tr>";
				$content.= "<tr><td align='right'>RETEIVA 16%</td><td align='right'>".Currency::number($movihead->getDescuento(), 2)."</td></tr>";
				$content.= "<tr><td align='right'>VALOR IVA 10%</td><td align='right'>".Currency::number($movihead->getIvad(), 2)."</td></tr>";
				$content.= "<tr><td align='right'>RETEIVA 10%</td><td align='right'>".Currency::number($movihead->getIvam(), 2)."</td></tr>";
				$content.= "<tr><td align='right'>VALOR IVA 5%</td><td align='right'>".Currency::number($movih1->getIva5(), 2)."</td></tr>";
				$content.= "<tr><td align='right'>RETEIVA 5%</td><td align='right'>".Currency::number($movih1->getRetIva5(), 2)."</td></tr>";
				$content.= "<tr><td align='right'>VR RETENCIÓN</td><td align='right'>".Currency::number($movihead->getRetencion(), 2)."</td></tr>";
				$content.= "<tr><td align='right'>VALOR ICA</td><td align='right'>".Currency::number($movihead->getIca(), 2)."</td></tr>";
				$content.= "<tr><td align='right'>IVA COSTO/GASTO</td><td align='right'>".Currency::number($movihead->getImpo(), 2)."</td></tr>";
				if ($hortifruticula == 'S') {
					$content.= "<tr><td align='right'>RETENCIÓN HORTIF.</td><td align='right'>".Currency::number($movih1->getReten1(), 2)."</td></tr>";
				}
				$content.= "<tr><td align='right'>TOTAL</td><td align='right'>".Currency::number($movihead->getVTotal(), 2)."</td></tr>";
			} else {
				$content.= "<tr><td align='right'>TOTAL</td><td align='right'>".Currency::number($movihead->getVTotal(), 2)."</td></tr>";
			}
			$content.= "</table>";

			if ($tipoComprob=='C') {
				if ($condiciones=='S') {
					$content.= '<br/><p>O=olor   C=color  T=textura  A=apariencia</p>';
				}
			}

			$content.= "<br/><br/><table cellspacing='0' cellpadding='0' class='firmas'><tr>";
			if ($tipoComprob=='P') {
				$content.= "<td width='33%' align='center'>_____________________<br/>Pedido por</td>";
				$content.= "<td width='33%' align='center'>_____________________<br/>Vo.Bo. Jefe Departamento</td>";
			} else {
				if ($tipoComprob=='C') {
					$content.= "<td width='33%' align='center'>_____________________<br/>Recibido por</td>";
					$content.= "<td width='50%' align='center'>_____________________<br/>Jefe de Compras</td>";
				} else {
					if ($tipoComprob=='O') {
						$content.= "<td width='50%' align='center'>_____________________<br/>Jefe de Compras</td>";
						$content.= "<td width='50%' align='center'>_____________________<br/>Vo.Bo. Gerencia</td>";
					} else {
						if ($tipoComprob=='E') {
							$content.= "<td width='50%' align='center'>_____________________<br/>Jefe de Compras</td>";
							$content.= "<td width='50%' align='center'>_____________________<br/>Contabilidad</td>";
						}
					}
				}
			}
			$content.= "</tr></table>";
			if ($lastPrint==true) {
				$content.= '</body></html>';
			} else {
				$content.="<div class='pageBreak'></div><pagebreak />";
			}
		}

		return $content;
	}

	/**
	 * Genera y escribe la URL de la impresión de acuerdo al tipo de archivo
	 *
	 * @param	string $reportType
	 * @param	string $comprob
	 * @param	int $almacen
	 * @param	int $numero
	 * @return	string
	 */
	public static function getPrintUrl($reportType, $comprob, $almacen, $numero)
	{
		$fileName = $comprob.'-'.$almacen.'-'.$numero.'-'.mt_rand(0, 1000);
		$content = self::getPrint($reportType, $comprob, $almacen, $numero);
		return self::getPrintOutput($reportType, $fileName, $content);
	}

	/**
	 * Escribe el archivo de impresión y devuelve una ruta
	 *
	 * @param 	string $reportType
	 * @param	string $fileName
	 * @param	string $content
	 * @return	string
	 */
	public static function getPrintOutput($reportType, $fileName, $content)
	{
		if ($reportType=='text') {
			file_put_contents('public/temp/'.$fileName.'.txt', $content);
			return 'temp/'.$fileName.'.txt';
		} else {
			if ($reportType=='html') {
				file_put_contents('public/temp/'.$fileName.'.html', $content);
				return 'temp/'.$fileName.'.html';
			} else {
				if ($reportType=='pdf') {
					//$content = utf8_encode($content);
					require 'Library/Mpdf/mpdf.php';
					$pdf = new mPDF('win-1252', 'A4');
					$pdf->SetDisplayMode('fullpage');
					$pdf->ignore_invalid_utf8 = true;
					$pdf->tMargin = 10;
					$pdf->lMargin = 10;
					$pdf->writeHTML($content);
					$pdf->Output('public/temp/'.$fileName.'.pdf');
					return 'temp/'.$fileName.'.pdf';
				}
			}
		}
		return 'temp/'.$fileName.'.txt';
	}

}
