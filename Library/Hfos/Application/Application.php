<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * Hfos_Application
 *
 * Clase principal para bootear las aplicaciones y asociar sus recursos externos
 *
 */
class Hfos_Application
{

	/**
	 * Nombre de Código
	 *
	 */
	const APP_CODE_NAME = 'Amitaruci';

	/**
	 * Revisión
	 *
	 */
	const APP_REVISION = '$Revision$ $Date$';

	/**
	 * Último commit realizado
	 *
	 */
	const APP_LAST_COMMIT = '$Last-Commit: Tue, 07 Feb 2012 15:03:04 -0500$';

	/**
	 * Versiones de las aplicaciones
	 *
	 * @var array
	 */
	private static $_versions = array(
		'IN' => '6.1.12',
		'CO' => '6.1.12',
		'NO' => '6.1.12',
		'IM' => '6.1.12',
		'FC' => '6.1.12',
		'CN' => '6.1.12',
		'PV' => '6.1.12',
		'SO' => '1.0',
		'TC' => '1.0',
	);

	private static $_productNames = array(
		'IN' => 'Hotel Front-Office Solution : Inventarios',
		'CO' => 'Hotel Front-Office Solution : Contabilidad',
		'IM' => 'Hotel Front-Office Solution : Administración',
		'FC' => 'Hotel Front-Office Solution : Facturador',
		'CN' => 'Hotel Front-Office Solution : Consecutivos',
		'PV' => 'Hotel Front-Office Solution : Punto de Venta',
		'NO' => 'Hotel Front-Office Solution : Nómina',
		'SO' => 'Gestión de Socios de Clubes',
		'TC' => 'Gestión de Tiempo Compartido',
	);

	private static $_frameworks = array(
		'IN' => 'kumbia',
		'CO' => 'kumbia',
		'NO' => 'phalcon',
		'IM' => 'kumbia',
		'FC' => 'kumbia',
		'CN' => 'kumbia',
		'PV' => 'kumbia',
		'SO' => 'kumbia',
		'TC' => 'kumbia',
	);

	/**
	 * Javascripts que deben ser cargados por cada aplicación
	 *
	 * @var array
	 */
	private static $_javascriptSources = array(
		'IN' => array(
			'restore', 'ordenes', 'pedidos', 'tatico', 'entradas', 'kardex', 'conversion',
			'traslados', 'salidas', 'salidas-buffet', 'saldos-almacen', 'stocks', 'reabrir', 'cerrar', 'ajustes',
			'saldos-almacen-consolidado', 'consecutivos', 'movimientos-inve', 'trasunto',
			'listado-referencias','listado-proveedores', 'transformaciones', 'fisico',
			'transaccion-inve', 'matriz-proveedores', 'comportamiento', 'horti','movinventario', 'consumos',
			'impresion'
		),
		'CO' => array(
			'movimiento', 'movimiento_niif', 'cuentas', 'incluir', 'excluir', 'terceros', 'balance', 'amortizacion',
			'consecutivos', 'libro-auxiliar', 'movimiento-terceros',
			'movimiento-documentos', 'movimiento-centros', 'balance-centros', 'listado-movimiento',
			'listado-retencion', 'retencion', 'cheque', 'formato-cheque',
			'comprobante-diario', 'consultas', 'cambio-nit',
			'depreciacion', 'consulta-causacion', 'consulta-depreciacion', 'anular-depreciacion',
			'traslado-activos', 'cartera-edades', 'cierre-contable',
			'cuentas-selector', 'campos-medios', 'medios', 'libro-terceros', 'ejecucion', 'numeracion',
			'novedad-activos', 'libro-diario', 'libro-mayor', 'reabrir-mes',
			'comprobante-cierre', 'ordenes-servicio', 'pyg', 'ejecucion-pres',
			'cierre-cuentas', 'reabrir-cuentas', 'caratulas', 'balance-general', 'certificado-retencion',
			'certificado-iva', 'certificado-ica', 'cierre-anual', 'reabrir-ano', 'recibo-caja', 'listado-comprob',
			'interfase-siigo', 'cuentas-selector2', 'informe-balance-consolidado', 'corregir-cartera'
		),
		'NO' => array(
			'liquidacion'
		),
		'IM' => array(
			'permisos-perfiles','perfiles-usuarios', 'permisos-comprob', 'permisos-centros'
		),
		'FC' => array(
			'facturas', 'reimprimir'
		),
		'SO' => array(
            'socios', 'cambio_accion', 'asignacion_cargos_grupo', 'cargos_socios', 'movimiento_cargos', 'facturar',
            'facturar_personal', 'proyeccion', 'consulta-socios', 'cierre-periodo', 'reabrir',
            'prestamos-socios', 'suspendidos-mora', 'facturas-generadas', 'conceptos-causados', 'informe-cartera',
            'informe-convenios', 'estado_cuenta', 'estado_cuenta_consolidado', 'pagos-periodo',
            'estado_cuenta_validacion', 'informe_rc', 'importar-pagos'
        ),
		'TC' => array(
			'contratos', 'reservas', 'proyeccion', 'abono_contrato', 'abono_reserva',
			'socios_aldia', 'propietarios', 'cambio_contratos', 'cuenta_cobro'
		),
		'CN' => array(
		),
		'PO' => array(
		),
		'IP' => array(
		)
	);

	/**
	 * Javascripts que deben ser cualquier por cada aplicación
	 *
	 * @var array
	 */
	private static $_coreSources = array(

		//HyperForm
		'hfos/hyperform/hyperform',
		'hfos/hyperform/browse-data',
		'hfos/hyperform/messages',
		'hfos/hyperform/manager',
		'hfos/hyperform/tabs',
		'hfos/hyperform/grid',
		'hfos/hyperform/grid-row',
		'hfos/hyperform/grid-group',
		'hfos/hyperform/record-data',

		//General
		'hfos/login',
		'hfos/common',

		//Network
		'hfos/kernel/network/ajax',

		//Communications
		'hfos/kernel/communications/communications',

		//UI
		'hfos/ui/video',
		//'hfos/ui/tips',
		'hfos/ui/report-type',
		'hfos/ui/browse-data',
		'hfos/ui/draggable',
		'hfos/ui/autocompleter',
		'hfos/ui/messages',
		'hfos/ui/tabs',
		'hfos/ui/modal-form',
		'hfos/ui/form',
		'hfos/ui/table-sort',
		'hfos/ui/workspace',
		'hfos/ui/shortcuts',
		'hfos/ui/screen-saver',
		'hfos/ui/notifier',
		'hfos/ui/system-tray-widgets',
		'hfos/ui/system-tray',
		'hfos/ui/taskbar',
		'hfos/ui/submenu',
		'hfos/ui/menu',
		'hfos/ui/start-menu',
		'hfos/ui/toolbar',
		'hfos/ui/manager',
		'hfos/ui/window',
		'hfos/ui/windows',
		'hfos/ui/modal',
		'hfos/ui/ui',

		//Productivity
		'hfos/productivity/productivity',
		'hfos/productivity/mail',

		//Shell
		'hfos/shell/console',

		//Security
		'hfos/security/acl',
		'hfos/security/uac',
		'hfos/security/wallet',
		'hfos/security/user-account',
		'hfos/security/virtual-user',

		//SMP Workers
		'hfos/kernel/smp/background-process',

		//Storage
		'hfos/kernel/storage/db',

		//Kernel
		'hfos/kernel/settings',
		'hfos/kernel/base64',
		'hfos/kernel/md5',
		'hfos/kernel/serializer',
		'hfos/kernel/welcome-box',
		'hfos/kernel/upgrade-box',
		'hfos/kernel/panic',
		'hfos/kernel/core-dump',
		'hfos/kernel/exception',
		'hfos/kernel/process-container',
		'hfos/kernel/process',
		'hfos/kernel/bindings',
		'hfos/kernel/application',
		'hfos/kernel/time',
		'hfos/kernel/main',

		//Core Animation-Audio-Video
		'hfos/core/animation',

		//Swf-Flash
		'hfos/swf/swfobject',

		//JSON
		'hfos/json/json',

		//Frameworks
		'hfos/base'

	);

	/**
	 * Carga los javascript, metas y CSS de la aplicación
	 *
	 */
	private static function _loadClientComponents()
	{

		//Cargar custom Javascript y CSS
		$code = CoreConfig::getAppSetting('code');
		$mode = CoreConfig::getAppSetting('mode');
		if ($mode=='production') {
			Tag::addJavascript('hfos/production/'.$code, true);
		} else {
			if (isset(self::$_javascriptSources[$code])) {
				foreach (self::$_javascriptSources[$code] as $source) {
					Tag::addJavascript('hfos/app/'.$code.'/'.$source, false);
				}
			}
			Tag::stylesheetLink('hfos/app/'.$code, false);
		}

		if ($mode == 'production') {
			Tag::stylesheetLink('hfos/production/'.$code, false, 'cache='.self::getVersion().'-'.mt_rand(1, 100000));
			Tag::stylesheetLink('hfos/production/style', false, 'cache='.self::getVersion().'-'.mt_rand(1, 100000));
		} else {
			//Tag::stylesheetLink('hfos/tips');
			Tag::stylesheetLink('hfos/style');
			Tag::stylesheetLink('hfos/general');
			Tag::stylesheetLink('style');
		}
		echo Tag::stylesheetLinkTags();
		echo Tag::getJavascriptLocation();

		if ($mode == 'production') {
			Tag::addJavascript('hfos/production/kernel', true);
		} else {

			//Unit Testing
			if ($mode == 'test') {
				array_unshift(self::$_coreSources, 'hfos/testing/unit');
				array_unshift(self::$_coreSources, 'hfos/testing/app/'.$code.'/suite');
			}

			foreach (self::$_coreSources as $coreSource) {
				Tag::addJavascript($coreSource, false);
			}
			Tag::addJavascript('hfos/protoculous', false);
			Tag::addJavascript('core/base-source', false);

			/*Tag::addJavascript('core/framework/scriptaculous/dragdrop', false);
			Tag::addJavascript('core/framework/scriptaculous/builder', false);
			Tag::addJavascript('core/framework/scriptaculous/controls', false);
			Tag::addJavascript('core/framework/scriptaculous/effects', false);
			Tag::addJavascript('core/framework/scriptaculous/scriptaculous', false);
			Tag::addJavascript('core/framework/scriptaculous/prototype', false);*/
		}

		//Show Script Tags
		echo Tag::javascriptSources();

	}

	/**
	 * Devuelve los recursos Javascript Asociados a la aplicación
	 *
	 * @param 	string $name
	 * @return	array
	 */
	public static function getJavascriptSources($name)
	{
		if (isset(self::$_javascriptSources[$name])) {
			return self::$_javascriptSources[$name];
		} else {
			return array();
		}
	}

	/**
	 * Devuelve los recursos Javascript que deben ser cargados por cualquier aplicación
	 *
	 * @return	array
	 */
	public static function getCoreSources()
	{
		return self::$_coreSources;
	}

	/**
	 * Obtiene el nombre del producto
	 *
	 * @param	string $code
	 * @return	string
	 */
	public static function getProductName($code=null)
	{
		if (!$code) {
			$code = CoreConfig::getAppSetting('code');
		}
		if (isset(self::$_productNames[$code])) {
			return self::$_productNames[$code];
		} else {
			return 'Hotel Front-Office Solution';
		}
	}

	/**
	 * Obtiene la versión del código base del producto
	 *
	 * @param	string $code
	 * @return	string
	 */
	public static function getVersion($code=null)
	{
		if (!$code) {
			$code = CoreConfig::getAppSetting('code');
		}
		if (isset(self::$_versions[$code])) {
			return self::$_versions[$code];
		} else {
			return '6.x.x';
		}
	}

	/**
	 * Obtiene el charset de la aplicación activa
	 *
	 * @return string
	 */
	public static function getAppCharset()
	{
		$charset = CoreConfig::getAppSetting('charset', 'application');
		if (!$charset) {
			$charset = CoreConfig::getAppSetting('charset', 'entities');
			if (!$charset) {
				return 'utf8_unicode_ci';
			}
		}
		return $charset;
	}

	/**
	 * Bootea la aplicación. Debe ser llamado en views/index.phtml
	 *
	 */
	public static function boot()
	{
		$name = CoreConfig::getAppSetting('name');
		$code = CoreConfig::getAppSetting('code');
		$mode = CoreConfig::getAppSetting('mode');
		$icon = CoreConfig::getAppSetting('icon');
		echo '<!DOCTYPE html><html><head>
		<meta charset="utf-8"><title>HFOS - ', $name, '</title>';
		echo self::_loadClientComponents();
		echo '</head><body><div id="mainContent">';
		View::getContent();
		echo '</div>';
		if ($mode == 'test' || $mode == 'development') {
			echo '<div id="app-revision">Build: ', self::APP_REVISION, '<br/>', self::APP_LAST_COMMIT, '</div>';
		}

		$appModel = ControllerBase::getAppModel();
		if (version_compare($appModel->getVersion(), self::getVersion(), '<')) {
			$versionDiferent = 'true';
		} else {
			$versionDiferent = 'false';
		}

		echo '<div id="debug"></div>
<div id="debugException"></div>
<div id="debugFailure"></div>
<script type="text/javascript">
Hfos.setMode("', $mode, '");
Hfos.setConsumer("', $appModel->getNombre(), '");
Hfos.setVersion("', $appModel->getVersion(), '", ', $versionDiferent, ');
Hfos.checkForAuthToken("', $code, '", function(){Hfos.bootApp("', $name, '", "', $code, '", "', Router::getApplication(), '", "', $icon, '", false);});
</script></body></html>';
	}

}
