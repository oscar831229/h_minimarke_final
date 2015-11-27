<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

$menuDisposition = array(
	array(
		'title' => 'Movimiento Contable',
		'description' => 'Opciones para mantener movimiento contable',
		'options' => array(
			'movimiento',
			'movimiento_niif',
			'consultas',
			'cambio_nit',
			'incluir',
			'excluir',
			'aura',
		)
	),
	array(
		'title' => 'Informes de Comprobación',
		'description' => 'Informes de comprobación y balances',
		'options' => array(
			'balance',
			'informe_balance_consolidado',
			'libro_auxiliar',
			'consecutivos',
			'movimiento_terceros',
			'movimiento_centros',
			'balance_centros',
			'comprobante_diario',
			'listado_movimiento',
			'listado_retencion',
			'listado_comprob',
			'retencion'
		)
	),
	array(
		'title' => 'Informes Oficiales y Tributarios',
		'description' => 'Informes oficiales y tributarios',
		'options' => array(
			'balance_general',
			'libro_diario',
			'libro_mayor',
			'libro_terceros',
			'numeracion'
		)
	),
	array(
		'title' => 'Certificados',
		'description' => 'Certificados de retención e IVA/ICA',
		'options' => array(
			'certificado_retencion',
			'certificado_ica'
		)
	),
	array(
		'title' => 'Informes Financieros',
		'description' => 'Informes de estados de resultados y PYG',
		'options' => array(
			'pyg',
			'caratulas'
		)
	),
	array(
		'title' => 'Informes de Cartera',
		'description' => 'Informes de cuentas por pagar y cobrar',
		'options' => array(
			'movimiento_documentos',
			'cartera_edades',
			'corregir_cartera'
		)
	),
	array(
		'title' => 'Activos Fijos',
		'description' => 'Administrar y depreciar activos fijos',
		'options' => array(
			'activos',
			'grupos',
			'tipos_activos',
			'ubicacion',
			'depreciacion',
			'anular_depreciacion',
			'traslado_activos',
			'novedad_activos',
			'consulta_depreciacion'
		)
	),
	array(
		'title' => 'Acreedores Varios',
		'description' => 'Generar ordenes de servicio a acreedores varios',
		'options' => array(
			'ordenes',
			'refe',
			'lineaser'
		)
	),
	array(
		'title' => 'Presupuesto',
		'description' => 'Administrar y comparar presupuesto contable',
		'options' => array(
			'presupuesto',
			'ejecucion_pres'
		)
	),
	array(
		'title' => 'Tesoreria',
		'description' => 'Generar y administrar el stock de cheques',
		'options' => array(
			'recibo_caja',
			'cheque',
			'chequeras',
			'cuentas_bancos',
			'banco',
			'formato_cheque'
		)
	),
	array(
		'title' => 'Medios Magnéticos',
		'description' => 'Generación de medios magnéticos según reglamentación',
		'options' => array(
			'medios',
			'campos_medios',
			'codigos_medios',
			'formatos_medios'
		)
	),
	array(
		'title' => 'Activos Diferidos',
		'description' => 'Administrar y causar activos diferidos',
		'options' => array(
			'diferidos',
			'grupos_diferidos',
			'amortizacion',
			'consulta_causacion'
		)
	),
	array(
		'title' => 'Interfaces',
		'description' => 'Interfaces contables',
		'options' => array(
			'interface_siigo'
		)
	),
	array(
		'title' => 'Cierres',
		'description' => 'Procesos de cierre contable',
		'options' => array(
			'cierre_contable',
			'reabrir_mes',
			'cierre_cuentas',
			'reabrir_cuentas',
			'comprobante_cierre',
			'cierre_anual',
			'reabrir_ano'
		)
	),
	array(
		'title' => 'Básicas',
		'description' => 'Administrar parametrización de la aplicación',
		'options' => array(
			'niif',
			'nic',
			'cuentas',
			'terceros',
			'tipodoc',
			'comprobantes',
			'diarios',
			'documentos',
			'formapago',
			'regimen_cuentas',
			'ica',
			'comcier',
			'cuentas_cree',
			'centros',
			'consolidados',
			'settings'
		)
	),
);

$accessList = array(

	//base
	'workspace' => array(
		'elevation' => true,
		'base' => true,
		'description' => 'Espacio de Trabajo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			),
			'storeElement' => array(
				'description' => 'Modificar el'
			),
			'getApplicationState' => array(
				'description' => 'Consultar el'
			)
		)
	),

	//app-specific
	'aura' => array(
		'elevation' => true,
		'description' => 'Interfaz de Contabilización',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			),
			'save' => array(
				'description' => 'Crear ó Actualizar en'
			),
			'getComprobByDate' => array(
				'description' => 'Consultar comprobantes por fecha en'
			)
		)
	),

	'movimiento' => array(
		'elevation' => true,
		'description' => 'Movimiento Contable',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			),
			'buscar' => array(
				'description' => 'Buscar en'
			),
			'guardar' => array(
				'description' => 'Crear ó Actualizar en'
			),
			'eliminar' => array(
				'description' => 'Eliminar en'
			),
			'cambiarFecha' => array(
				'description' => 'Cambiar Fecha de'
			),
			'copiar' => array(
				'description' => 'Copiar en'
			),
			'nuevo' => array(
				'sameAs' => 'guardar'
			),
			'editar' => array(
				'sameAs' => 'guardar'
			),
			'guardarLinea' => array(
				'sameAs' => 'guardar'
			),
			'validarFecha' => array(
				'sameAs' => 'guardar'
			),
			'borrarLineas' => array(
				'sameAs' => 'guardar'
			),
			'getDetalles' => array(
				'sameAs' => 'buscar'
			)
		)
	),

	'movimiento_niif' => array(
		'elevation' => true,
		'description' => 'Movimiento Niif',
		'actions' => array(
			'index' => array(
				'description' => 'Ingresar a'
			),
			'buscar' => array(
				'description' => 'Buscar en'
			),
			'guardar' => array(
				'description' => 'Crear ó Actualizar en'
			),
			'eliminar' => array(
				'description' => 'Eliminar en'
			),
			'cambiarFecha' => array(
				'description' => 'Cambiar Fecha de'
			),
			'copiar' => array(
				'description' => 'Copiar en'
			),
			'nuevo' => array(
				'sameAs' => 'guardar'
			),
			'editar' => array(
				'sameAs' => 'guardar'
			),
			'guardarLinea' => array(
				'sameAs' => 'guardar'
			),
			'validarFecha' => array(
				'sameAs' => 'guardar'
			),
			'borrarLineas' => array(
				'sameAs' => 'guardar'
			),
			'getDetalles' => array(
				'sameAs' => 'buscar'
			)
		)
	),

	'consultas' => array(
		'elevation' => true,
		'description' => 'Consultas de Movimiento Contable',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	'incluir' => array(
		'elevation' => true,
		'description' => 'Importar Movimiento Contable',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	'excluir' => array(
		'elevation' => true,
		'description' => 'Exportar Movimiento Contable',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	'cambio_nit' => array(
		'elevation' => true,
		'description' => 'Cambiar un Tercero por Otro',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	'balance' => array(
		'elevation' => true,
		'description' => 'Balance de Comprobación',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	'informe_balance_consolidado' => array(
		'elevation' => true,
		'description' => 'Balance Consolidado Anual',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	'libro_auxiliar' => array(
		'elevation' => true,
		'description' => 'Libro Auxiliar',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'consecutivos' => array(
		'elevation' => true,
		'description' => 'Validar Integridad',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'movimiento_terceros' => array(
		'elevation' => true,
		'description' => 'Movimiento de Cuentas por Terceros',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'movimiento_centros' => array(
		'elevation' => true,
		'description' => 'Movimiento de Cuentas por Centros de Costo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'balance_centros' => array(
		'elevation' => true,
		'description' => 'Balance por Centro de Costo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'comprobante_diario' => array(
		'elevation' => true,
		'description' => 'Comprobante Diario',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'listado_movimiento' => array(
		'elevation' => true,
		'description' => 'Listado de Movimiento',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'retencion' => array(
		'elevation' => true,
		'description' => 'Retención Acumulada',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'listado_retencion' => array(
		'elevation' => true,
		'description' => 'Listado de Retención',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'listado_comprob' => array(
		'elevation' => true,
		'description' => 'Listado de Comprobantes',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	'balance_general' => array(
		'elevation' => true,
		'description' => 'Balance General Comparativo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'libro_diario' => array(
		'elevation' => true,
		'description' => 'Libro Diario',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'libro_mayor' => array(
		'elevation' => true,
		'description' => 'Libro Mayor y Balance',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'libro_terceros' => array(
		'elevation' => true,
		'description' => 'Libro Inventario y Balance por Tercero',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'numeracion' => array(
		'elevation' => true,
		'description' => 'Numeración de Libros Oficiales',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//Certificados
	'certificado_retencion' => array(
		'elevation' => true,
		'description' => 'Certificado Retención',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'certificado_ica' => array(
		'elevation' => true,
		'description' => 'Certificado ICA',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//Informes Financieros
	'pyg' => array(
		'elevation' => true,
		'description' => 'Estado de Perdidas y Ganancias',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'caratulas' => array(
		'elevation' => true,
		'description' => 'Caratulas del Balance',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//Presupuesto
	'presupuesto' => array(
		'elevation' => true,
		'description' => 'Administración del Presupuesto',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'ejecucion' => array(
		'elevation' => true,
		'description' => 'Consultar Ejecución del Presupuesto',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'ejecucion_pres' => array(
		'elevation' => true,
		'description' => 'Reporte de Ejecución del Presupuesto',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//Activos Fijos
	'activos' => array(
		'elevation' => true,
		'description' => 'Activos Fijos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'grupos' => array(
		'elevation' => true,
		'description' => 'Grupos de Activos Fijos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'tipos_activos' => array(
		'elevation' => true,
		'description' => 'Tipos de Activos Fijos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'ubicacion' => array(
		'elevation' => true,
		'description' => 'Ubicación de Activos Fijos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'depreciacion' => array(
		'elevation' => true,
		'description' => 'Depreciación Mensual de Activos Fijos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	'consulta_depreciacion' => array(
		'elevation' => true,
		'description' => 'Consulta de Depreciación Mensual de Activos Fijos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

  	'anular_depreciacion' => array(
		'elevation' => true,
		'description' => 'Anular Depreciación de Activos Fijos Mensual',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'traslado_activos' => array(
		'elevation' => true,
		'description' => 'Traslado de Activos Fijos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'novedad_activos' => array(
		'elevation' => true,
		'description' => 'Novedades de Activos Fijos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//Acreedores Varios
	'ordenes' => array(
		'elevation' => true,
		'description' => 'Ordenes de Servicio',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'refe' => array(
		'elevation' => true,
		'description' => 'Items de Servicio',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'lineaser' => array(
		'elevation' => true,
		'description' => 'Líneas de Servicio',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),

	//Cartera
	'movimiento_documentos' => array(
		'elevation' => true,
		'description' => 'Movimiento de Documentos en Cartera',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'cartera_edades' => array(
		'elevation' => true,
		'description' => 'Cartera por Edades',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	'corregir_cartera' => array(
		'elevation' => true,
		'description' => 'Corregir Cartera',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//Tesoreria
	'cheque' => array(
		'elevation' => true,
		'description' => 'Cheques',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'chequeras' => array(
		'elevation' => true,
		'description' => 'Líneas de Servicio',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'cuentas_bancos' => array(
		'elevation' => true,
		'description' => 'Cuentas Bancos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'banco' => array(
		'elevation' => true,
		'description' => 'Bancos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'formato_cheque' => array(
		'elevation' => true,
		'description' => 'Formatos de Cheques',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//Recibos
	'recibo_caja' => array(
		'elevation' => true,
		'description' => 'Recibo de Caja',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'buscar'  => array(
				'description' => 'Consulta a'
			),
			'ver'  => array(
				'description' => 'Ver a'
			),
			'nuevo'  => array(
				'description' => 'Adición a'
			),
			'generar'  => array(
				'description' => 'Causar a'
			),
			'anular'  => array(
				'description' => 'Anular a'
			),
			'imprimir'  => array(
				'description' => 'Imprimir a'
			),
		)
	),


	//Medios Magnéticos
	'medios' => array(
		'elevation' => true,
		'description' => 'Generación de Medios Magnéticos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'campos_medios' => array(
		'elevation' => true,
		'description' => 'Campos y Rangos de Cuentas de Formatos Medios Magnéticos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'codigos_medios' => array(
		'elevation' => true,
		'description' => 'Códigos de Conceptos de Medios Magnéticos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'formatos_medios' => array(
		'elevation' => true,
		'description' => 'Formatos de Medios Magnéticos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),

	//Activos Diferidos
	'diferidos' => array(
		'elevation' => true,
		'description' => 'Activos Diferidos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'grupos_diferidos' => array(
		'elevation' => true,
		'description' => 'Grupos de Activos Direfidos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'amortizacion' => array(
		'elevation' => true,
		'description' => 'Amortización Mensual de Activos Diferidos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	'consulta_causacion' => array(
    'elevation' => true,
    'description' => 'Consulta de Causación Mensual de Diferidos',
    'actions' => array(
      'index' => array(
        'description' => 'Ingreso a'
      )
    )
  ),


	//Cierres Contables
	'interface_siigo' => array(
		'elevation' => true,
		'description' => 'Interface SIIGO',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//Cierres Contables
	'cierre_contable' => array(
		'elevation' => true,
		'description' => 'Realizar Cierre Contable',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'reabrir_mes' => array(
		'elevation' => true,
		'description' => 'Reabrir Mes',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'comprobante_cierre' => array(
		'elevation' => true,
		'description' => 'Generar Comprobante de Cierre Anual',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'cierre_cuentas' => array(
		'elevation' => true,
		'description' => 'Realizar Cierre de Cuentas de Retención e IVA',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'reabrir_cuentas' => array(
		'elevation' => true,
		'description' => 'Reabrir Cuentas de Retención e IVA',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'cierre_anual' => array(
		'elevation' => true,
		'description' => 'Cierre Anual',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'reabrir_ano' => array(
		'elevation' => true,
		'description' => 'Reabrir Año',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//Básicas
	'niif' => array(
		'elevation' => true,
		'description' => 'Cuentas NIIF',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'nic' => array(
		'elevation' => true,
		'description' => 'NIC',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'cuentas' => array(
		'elevation' => true,
		'description' => 'Plan de Cuentas',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'queryCuenta' => array(
				'description' => 'Consultar en',
			)
		)
	),
	'terceros' => array(
		'elevation' => true,
		'description' => 'Terceros',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'queryByNit' => array(
				'sameAs' => 'search'
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'comprobantes' => array(
		'elevation' => true,
		'description' => 'Comprobantes',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'tipodoc' => array(
		'elevation' => true,
		'description' => 'Tipos de Documentos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'diarios' => array(
		'elevation' => true,
		'description' => 'Mantenimiento de Diarios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'documentos' => array(
		'elevation' => true,
		'description' => 'Tipos de Documentos Contables',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),

	'formapago' => array(
		'elevation' => true,
		'description' => 'Formas de Pago',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),

	'regimen_cuentas' => array(
		'elevation' => true,
		'description' => 'Contabilización por Regímen',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),

	'ica' => array(
		'elevation' => true,
		'description' => 'Cuentas de ICA',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),

	'comcier' => array(
		'elevation' => true,
		'description' => 'Cuentas de Cierre Anual',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'cuentas_cree' => array(
		'elevation' => true,
		'description' => 'Cuentas CREE',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),
	'centros' => array(
		'elevation' => true,
		'description' => 'Centros de Costo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),

	'consolidados' => array(
		'elevation' => true,
		'description' => 'Servidores de Consolidado',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'sameAs' => 'save'
			),
			'edit' => array(
				'sameAs' => 'save'
			),
			'save' => array(
				'description' => 'Adicionar ó Modificar en'
			),
			'delete' => array(
				'description' => 'Eliminar en'
			),
			'search' => array(
				'description' => 'Consultar ó Reporte en',
			),
			'rcs' => array(
				'description' => 'Consultar revisiones en'
			)
		)
	),

	'settings' => array(
		'elevation' => true,
		'description' => 'Configuración de Contabilidad',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'save' => array(
				'description' => 'Actualizar la'
			),
		)
	)

);
