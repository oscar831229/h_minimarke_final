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
 * BackCacher
 *
 * Cachea en memoria registros consultados durante un proceso largo
 * de tal forma que solo se consulten una vez en la base de datos
 *
 */
class BackCacher extends UserComponent {

	/**
	 * Referencia a Empresa
	 *
	 * @var Empresa
	 */
	private static $_empresa = null;

	/**
	 * Cuentas contables cacheadas
	 *
	 * @var array
	 */
	private static $_cuentas = array();

	/**
	 * Terceros cacheados
	 *
	 * @var array
	 */
	private static $_nits = array();

	/**
	 * Centros cacheados
	 *
	 * @var array
	 */
	private static $_centros = array();

	/**
	 * Chequeras cacheadas
	 *
	 * @var array
	 */
	private static $_chequeras = array();

	/**
	 * Cuentas bancos cacheados
	 *
	 * @var array
	 */
	private static $_cuentasBancos = array();

	/**
	 * Comprobantes Cacheados
	 *
	 * @var comprobs
	 */
	private static $_comprobs = array();

	/**
	 * Conceptos Cacheados
	 *
	 * @var array
	 */
	private static $_conceptos = array();

	/**
	 * Cargos de Empleados Cacheados
	 *
	 * @var array
	 */
	private static $_cargos = array();

	/**
	 * Activos Fijos Cacheados
	 *
	 * @var array
	 */
	private static $_activos = array();

	/**
	 * Grupos Cacheados
	 *
	 * @var array
	 */
	private static $_grupos = array();

	/**
	 * Ubicaciones Cacheadas
	 *
	 * @var array
	 */
	private static $_locations = array();

	/**
	 * Usuarios Cacheados
	 *
	 * @var array
	 */
	private static $_usuarios = array();

	/**
	 * Diarios Cacheados
	 *
	 * @var array
	 */
	private static $_diarios = array();

	/**
	 * Formas de Pago Cacheadas
	 */
	private static $_formasPago = array();

	/**
	 * Items de Servicio Cacheados
	 *
	 * @var array
	 */
	private static $_refes = array();

	/**
	 * Referencias Cacheadas
	 *
	 * @var array
	 */
	private static $_inves = array();

	/**
	 * Unidades Cacheadas
	 *
	 * @var array
	 */
	private static $_unidades = array();

	/**
	 * Lineas Cacheadas
	 *
	 * @var array
	 */
	private static $_lineas = array();

	/**
	 * Almacenes Cacheados
	 *
	 * @var array
	 */
	private static $_almacenes = array();

	/**
	 * Empleados Cacheados
	 *
	 * @var array
	 */
	private static $_empleados = array();

	/**
	 * Todos los almacenes
	 *
	 * @var array
	 */
	private static $_todosAlmacenes = null;

	/**
	 * ConversionUnidades Cacheados
	 *
	 * @var array
	 */
	private static $_conversionUnidades = array();

	/**
	 * MatrizProveedores Cacheados
	 *
	 * @var array
	 */
	private static $_matrizProveedores = array();

	/**
	 * Socios Cacheados
	 *
	 * @var array
	 */
	private static $_socios = array();

	/**
	 * Socios de TPC Cacheados
	 *
	 * @var array
	 */
	private static $_sociosTpc = array();

	/**
	 * Consecutivos de Facturación Cacheados
	 *
	 * @var array
	 */
	private static $_consecutivos = array();

	/**
	 * Cargos Fijos de Socios Cacheados
	 *
	 * @var array
	 */
	private static $_cargosFijos = array();

	/**
	 * Obtiene una referencia al modelo Empresa
	 *
	 * @return Empresa
	 */
	public static function getEmpresa(){
		if(self::$_empresa===null){
			self::$_empresa = self::getModel('Empresa')->findFirst();
		}
		return self::$_empresa;
	}

	/**
	 * Obtiene una cuenta contable del BackCacher
	 *
	 * @param	string $codigoCuenta
	 * @return	Cuentas
	 */
	public static function getCuenta($codigoCuenta){
		//$codigoCuenta = preg_replace('/[^0-9]/', '', $codigoCuenta);
		if(!isset(self::$_cuentas[$codigoCuenta])){
			self::$_cuentas[$codigoCuenta] = self::getModel('Cuentas')->findFirst("cuenta='$codigoCuenta'");
		}
		return self::$_cuentas[$codigoCuenta];
	}

	/**
	 * Establece una cuenta contable en el BackCacher
	 *
	 * @param string $codigoCuenta
	 * @param Cuentas $cuenta
	 */
	public static function setCuenta($codigoCuenta, $cuenta){
		self::$_cuentas[$codigoCuenta] = $cuenta;
	}

	/**
	 * Obtiene un tercero del BackCacher
	 *
	 * @param	string $nitTercero
	 * @return	Nits
	 */
	public static function getTercero($nitTercero){
		if(!isset(self::$_nits[$nitTercero])){
			self::$_nits[$nitTercero] = self::getModel('Nits')->findFirst("nit='$nitTercero'");
		}
		return self::$_nits[$nitTercero];
	}

	/**
	 * Obtiene un centro de costo del BackCacher
	 *
	 * @param	string $codigoCentro
	 * @return	Centros
	 */
	public static function getCentro($codigoCentro){
		$codigoCentro = (int) $codigoCentro;
		if(!isset(self::$_centros[$codigoCentro])){
			self::$_centros[$codigoCentro] = self::getModel('Centros')->findFirst("codigo='$codigoCentro'");
		}
		return self::$_centros[$codigoCentro];
	}

	/**
	 * Obtiene un comprobante del BackCacher
	 *
	 * @param	string $codigoComprob
	 * @return	Comprob
	 */
	public static function getComprob($codigoComprob){
		if(!isset(self::$_comprobs[$codigoComprob])){
			self::$_comprobs[$codigoComprob] = self::getModel('Comprob')->findFirst(array('conditions'=>"codigo='$codigoComprob'"));
		}
		return self::$_comprobs[$codigoComprob];
	}

	/**
	 * Obtiene una chequera del BackCacher
	 *
	 * @param	int $chequeraId
	 * @return  Chequeras
	 */
	public static function getChequera($chequeraId){
		$chequeraId = (int) $chequeraId;
		if(!isset(self::$_chequeras[$chequeraId])){
			self::$_chequeras[$chequeraId] = self::getModel('Chequeras')->findFirst($chequeraId);
		}
		return self::$_chequeras[$chequeraId];
	}

	/**
	 * Obtiene una cuenta bancaria del BackCacher
	 *
	 * @param	int $chequeraId
	 * @return  CuentasBancos
	 */
	public static function getCuentaBanco($cuentaBancoId){
		$cuentaBancoId = (int) $cuentaBancoId;
		if(!isset(self::$_cuentasBancos[$cuentaBancoId])){
			self::$_cuentasBancos[$cuentaBancoId] = self::getModel('CuentasBancos')->findFirst($cuentaBancoId);
		}
		return self::$_cuentasBancos[$cuentaBancoId];
	}

	/**
	 * Obtiene una forma de pago
	 *
	 * @param	int $formaPago
	 * @return  FormaPago
	 */
	public static function getFormaPago($formaPago){
		$formaPago = (int) $formaPago;
		if(!isset(self::$_formasPago[$formaPago])){
			self::$_formasPago[$formaPago] = self::getModel('FormaPago')->findFirst($formaPago);
		}
		return self::$_formasPago[$formaPago];
	}

	/**
	 * Obtiene un concepto del BackCacher
	 *
	 * @param	int $codigoConcepto
	 * @return  Concepto
	 */
	public static function getConcepto($codigoConcepto){
		$codigoConcepto = (int) $codigoConcepto;
		if(!isset(self::$_conceptos[$codigoConcepto])){
			self::$_conceptos[$codigoConcepto] = self::getModel('Concepto')->findFirst($codigoConcepto);
		}
		return self::$_conceptos[$codigoConcepto];
	}

	/**
	 * Obtiene un cargo del BackCacher
	 *
	 * @param	int $codigoCargo
	 * @return  Cargos
	 */
	public static function getCargo($codigoCargo){
		$codigoCargo = (int) $codigoCargo;
		if(!isset(self::$_cargos[$codigoCargo])){
			self::$_cargos[$codigoCargo] = self::getModel('Cargos')->findFirst($codigoCargo);
		}
		return self::$_cargos[$codigoCargo];
	}

	/**
	 * Obtiene un activo del BackCacher
	 *
	 * @param	int $activoId
	 * @return  Activos
	 */
	public static function getActivo($activoId){
		$activoId = (int) $activoId;
		if(!isset(self::$_activos[$activoId])){
			self::$_activos[$activoId] = self::getModel('Activos')->findFirst($activoId);
		}
		return self::$_activos[$activoId];
	}

	/**
	 * Obtiene un grupo del BackCacher
	 *
	 * @param	int $codigoGrupo
	 * @return  Grupos
	 */
	public static function getGrupo($codigoGrupo){
		$codigoGrupo = (int) $codigoGrupo;
		if(!isset(self::$_grupos[$codigoGrupo])){
			self::$_grupos[$codigoGrupo] = self::getModel('Grupos')->findFirst($codigoGrupo);
		}
		return self::$_grupos[$codigoGrupo];
	}

	/**
	 * Obtiene un diario del BackCacher
	 *
	 * @param	int $codigoDiario
	 * @return  Diarios
	 */
	public static function getDiario($codigoDiario){
		$codigoDiario = (int) $codigoDiario;
		if(!isset(self::$_diarios[$codigoDiario])){
			self::$_diarios[$codigoDiario] = self::getModel('Diarios')->findFirst($codigoDiario);
		}
		return self::$_diarios[$codigoDiario];
	}

	/**
	 * Obtiene un item de servicio del BackCacher
	 *
	 * @param	int $codigoRefe
	 * @return  Refe
	 */
	public static function getRefe($codigoRefe){
		//$codigoRefe = (int) $codigoRefe;
		if(!isset(self::$_refes[$codigoRefe])){
			self::$_refes[$codigoRefe] = self::getModel('Refe')->findFirst($codigoRefe);
		}
		return self::$_refes[$codigoRefe];
	}

	/**
	 * Obtiene un grupo del BackCacher
	 *
	 * @param	int $codigoGrupo
	 * @return  Location
	 */
	public static function getLocation($locationId){
		$locationId = (int) $locationId;
		if(!isset(self::$_locations[$locationId])){
			self::$_locations[$locationId] = self::getModel('Location')->findFirst($locationId);
		}
		return self::$_locations[$locationId];
	}

	/**
	 * Obtiene un usuario del BackCacher
	 *
	 * @param	int $usuarioId
	 * @return  Usuarios
	 */
	public static function getUsuario($usuarioId){
		$usuarioId = (int) $usuarioId;
		if(!isset(self::$_usuarios[$usuarioId])){
			self::$_usuarios[$usuarioId] = self::getModel('Usuarios')->findFirst($usuarioId);
		}
		return self::$_usuarios[$usuarioId];
	}

	/**
	 * Establece una referencia en el BackCacher
	 *
	 * @param	Inve $inve
	 */
	public static function setInve($inve){
		self::$_inves[$inve->getItem()] = $inve;
	}

	/**
	 * Obtiene una referencia del BackCacher
	 *
	 * @param	int $item
	 * @return  Inve
	 */
	public static function getInve($item){
		if(!isset(self::$_inves[$item])){
			self::$_inves[$item] = self::getModel('Inve')->findFirst("item='$item'");
		}
		return self::$_inves[$item];
	}

	/**
	 * Obtiene una unidad del BackCacher
	 *
	 * @param	int $codigo
	 * @return  Unidad
	 */
	public static function getUnidad($codigo){
		if(!isset(self::$_unidades[$codigo])){
			self::$_unidades[$codigo] = self::getModel('Unidad')->findFirst("codigo='$codigo'");
		}
		return self::$_unidades[$codigo];
	}

	/**
	 * Obtiene una línea del BackCacher
	 *
	 * @param	int $codigo
	 * @return  Lineas
	 */
	public static function getLinea($almacen, $linea){
		if(!isset(self::$_lineas[$linea][$almacen])){
			self::$_lineas[$linea][$almacen] = self::getModel('Lineas')->findFirst("almacen='$almacen' AND linea='$linea'");
		}
		return self::$_lineas[$linea][$almacen];
	}

	/**
	 * Obtiene un listado de todos los almacenes
	 *
	 * @return  array
	 */
	public static function getTodosAlmacenes(){
		if(self::$_todosAlmacenes===null){
			self::$_todosAlmacenes = self::getModel('Almacenes')->find();
			foreach(self::$_todosAlmacenes as $almacen){
				self::$_almacenes[$almacen->getCodigo()] = $almacen;
			}
		}
		return self::$_todosAlmacenes;
	}

	/**
	 * Obtiene un empleado del BackCacher
	 *
	 * @param	id $empleadoId
	 * @return	Empleados
	 */
	public static function getEmpleado($empleadoId){
		if(!isset(self::$_empleados[$empleadoId])){
			self::$_empleados[$empleadoId] = self::getModel('Empleados')->findFirst($empleadoId);
		}
		return self::$_empleados[$empleadoId];
	}

	/**
	 * Obtiene un almacen del BackCacher
	 *
	 * @param	int $codigo
	 * @return  Almacenes
	 */
	public static function getAlmacen($almacen){
		if(!isset(self::$_almacenes[$almacen])){
			self::$_almacenes[$almacen] = self::getModel('Almacenes')->findFirst("codigo='$almacen'");
		}
		return self::$_almacenes[$almacen];
	}

	/**
	 * Obtiene un conversionUnidades del BackCacher
	 *
	 * @param int $unidadInicialId
	 * @param int $unidadFinalId
	 * @return  conversionUnidades
	 */
	public static function getConversionUnidades($unidadInicialId,$unidadFinalId){
		if(!isset(self::$_conversionUnidades[$unidadInicialId][$unidadFinalId])){
			self::$_conversionUnidades[$unidadInicialId][$unidadFinalId] = self::getModel('ConversionUnidades')->findFirst("unidad='$unidadFinalId' AND unidad_base='$unidadInicialId'");
		}
		return self::$_conversionUnidades[$unidadInicialId][$unidadFinalId];
	}

	/**
	 * Obtiene un matrizProveedores del BackCacher
	 *
	 * @param int $item
	 * @param int $nit
	 * @return  matrizProveedores OR array
	 */
	public static function getMatrizProveedores($item,$nit=false){
		if($nit){
			if(!isset(self::$_matrizProveedores[$item][$nit])){
				self::$_matrizProveedores[$item][$nit] = self::getModel('MatrizProveedores')->findFirst("item='$item' AND nit='$nit'");
			}
			return self::$_matrizProveedores[$item][$nit];
		} else {
			return false;
		}
	}

	/**
	 * Obtiene un socio del BackCacher
	 *
	 * @param	string $numeroAccion
	 * @return  Socios
	 */
	public static function getSocios($sociosId){
		if(!isset(self::$_socios[$sociosId])){
			self::$_socios[$sociosId] = self::getModel('Socios')->findFirst("socios_id='$sociosId'");
		}
		return self::$_socios[$sociosId];
	}

	/**
	 * Obtiene un socio de tpc del BackCacher
	 *
	 * @param	string $numeroAccion
	 * @return  Socios
	 */
	public static function getSociosTpcContrato($numeroContrato){
		if(!isset(self::$_sociosTpc[$numeroContrato])){
			self::$_sociosTpc[$numeroContrato] = self::getModel('Socios')->findFirst("numero_contrato='$numeroContrato'");
		}
		return self::$_sociosTpc[$numeroContrato];
	}

	/**
	 * Obtiene un consecutivo del BackCacher
	 *
	 * @param	int $consecutivoId
	 * @return  Consecutivos
	 */
	public static function getConsecutivo($consecutivoId){
		if(!isset(self::$_consecutivos[$consecutivoId])){
			self::$_consecutivos[$consecutivoId] = self::getModel('Consecutivos')->findFirst($consecutivoId);
		}
		return self::$_consecutivos[$consecutivoId];
	}

	/**
	 * Obtiene un cargos fijos del BackCacher
	 *
	 * @param	int $cargosFijosId
	 * @return  Consecutivos
	 */
	public static function getCargosFijos($cargosFijosId){
		if(!isset(self::$_cargosFijos[$cargosFijosId])){
			self::$_cargosFijos[$cargosFijosId] = self::getModel('CargosFijos')->findFirst($cargosFijosId);
		}
		return self::$_cargosFijos[$cargosFijosId];
	}

}
