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

$accessList = array(
	'admin' => array(
		'description' => 'Administración',
		'actions' => array(
			'index' => 'Ingresar al area admininistrativa'
		),
		'require' => 'appmenu'
	),
	'ambientes_items' => array(
		'description' => 'Actuailizar Items en Ambientes',
		'actions' => array(
			'index' => 'Activar/Desactivar Items en Ambientes',
		),
		'require' => 'admin'
	),
	'analisis_consumos' => array(
		'description' => 'Análisis de Consumos',
		'actions' => array(
			'index' => 'Generar análisis de consumos'
		),
		'require' => 'admin'
	),
	'analisis_detallado' => array(
		'description' => 'Análisis de Consumos Detallado',
		'actions' => array(
			'index' => 'Generar análisis de consumos detallado por referencia'
		),
		'require' => 'admin'
	),
	'exportar_consumos' => array(
		'description' => 'Exportar Consumos',
		'actions' => array(
			'index' => 'Exportar consumos'
		),
		'require' => 'admin'
	),
	'anula_factura' => array(
		'description' => 'Anular Facturas y Ordenes',
		'actions' => array(
			'index' => 'Anular Facturas y Ordenes de Servicio',
		),
		'require' => 'appmenu'
	),
	'appmenu' => array(
		'description' => 'Punto de Venta',
		'actions' => array(
			'index' => 'Ingresar a la aplicación'
		)
	),
	'audit' => array(
		'description' => 'Auditoria de Operación',
		'actions' => array(
			'index' => 'Ingresar y consultar en la Auditoría de la Operación'
		),
		'require' => 'admin'
	),
	'cancel' => array(
		'description' => 'Cancelación de Pedidos',
		'actions' => array(
			'index' => 'Ingresar y cancelar pedidos activos'
		),
		'require' => 'appmenu'
	),
	'cash_tray' => array(
		'description' => 'Cajas del Sistema',
		'actions' => array(
			'index' => 'Ingresar a cajas del sistema',
			'insert' => 'Adicionar a cajas del sistema',
			'update' => 'Modificar a cajas del sistema',
			'delete' => 'Borrar a cajas del sistema',
			'query' => 'Consultar a cajas del sistema',
			'browse' => 'Visualizar a cajas del sistema',
			'query' => 'Generar reporte de cajas del sistema',
		),
		'require' => 'admin'
	),
	'cashouttro' => array(
		'description' => 'Entrada/Salida Cajeros',
		'actions' => array(
			'index' => 'Ingresar a entrada/salida cajeros',
			'open' => 'Abrir cajas',
			'close' => 'Cerrar cajas'
		),
		'require' => 'appmenu'
	),
	'check' => array(
		'description' => 'Revisar Pedido',
		'actions' => array(
			'index' => 'Ingresar a revisar pedidos',
			'status' => 'Marcar un item como atendido',
		),
		'require' => 'appmenu'
	),
	'clave' => array(
		'description' => 'Contraseña',
		'actions' => array(
			'index' => 'Autenticar contraseña en la aplicación',
			'change' => 'Cambiar contraseña',
		),
		'require' => 'appmenu'
	),
	'close' => array(
		'description' => 'Cerrar el día del sistema',
		'actions' => array(
			'index' => 'Ingresar y cerrar el día del sistema',
		),
		'require' => 'appmenu'
	),
	'conceptos_cancelacion' => array(
		'description' => 'Conceptos de Cancelación de Pedidos',
		'actions' => array(
			'index' => 'Ingresar a conceptos de cancelación',
			'insert' => 'Adicionar conceptos de cancelación',
			'update' => 'Modificar conceptos de cancelación',
			'delete' => 'Borrar conceptos de cancelación',
			'query' => 'Consultar conceptos de cancelación',
			'browse' => 'Visualizar conceptos de cancelación',
			'query' => 'Generar reporte de conceptos de cancelación',
		),
		'require' => 'admin'
	),
	'datos' => array(
		'description' => 'Datos del Sistema',
		'actions' => array(
			'index' => 'Ingresar a datos del sistema',
			'update' => 'Modificar datos del sistema',
			'query' => 'Consultar datos del sistema',
			'browse' => 'Visualizar datos del sistema',
			'query' => 'Generar reporte de datos del sistema',
		),
		'require' => 'admin'
	),
	'descarga' => array(
		'description' => 'Descarga de Inventarios',
		'actions' => array(
			'index' => 'Ingresar, simular y descargar de inventarios',
		),
		'require' => 'admin'
	),
	'planificador' => array(
		'description' => 'Planificador de Producción',
		'actions' => array(
			'index' => 'Ingresar y grabar planificaciones de producción',
		),
		'require' => 'admin'
	),
	/*'descarga_ajustes' => array(
		'description' => 'Reporte de descarga y costo de ajustes de Inventarios',
		'actions' => array(
			'index' => 'Ingresar y generar reporte de ajustes de Inventarios',
		),
		'require' => 'admin'
	),*/
	'discount' => array(
		'description' => 'Descuentos de Pedidos',
		'actions' => array(
			'index' => 'Ingresar a descuentos de pedidos',
			'insert' => 'Adicionar descuentos de pedidos',
			'update' => 'Modificar descuentos de pedidos',
			'delete' => 'Borrar descuentos de pedidos',
			'query' => 'Consultar descuentos de pedidos',
			'browse' => 'Visualizar descuentos de pedidos',
			'query' => 'Generar repore de descuentos de pedidos',
		),
		'require' => 'admin'
	),
	'gardien' => array(
		'description' => 'Permisos de Roles',
		'actions' => array(
			'index' => 'Ingresar y modificar permisos de roles',
		),
		'require' => 'admin'
	),

	'menus' => array(
		'description' => 'Menús, Cartas del Punto de Venta',
		'actions' => array(
			'index' => 'Ingresar a menús',
			'insert' => 'Adicionar menús',
			'update' => 'Modificar menús',
			'delete' => 'Borrar menús',
			'query' => 'Consultar menús',
			'browse' => 'Visualizar menús',
			'query' => 'Generar reporte de menús',
		),
		'require' => 'admin'
	),
	'menus_items' => array(
		'description' => 'Items de Menús',
		'actions' => array(
			'index' => 'Ingresar a items de menús',
			'insert' => 'Adicionar a items de menús',
			'update' => 'Modificar a items de menús',
			'delete' => 'Borrar a items de menús',
			'query' => 'Consultar a items de menús',
			'browse' => 'Visualizar a items de menús',
			'query' => 'Generar reporte de a items de menús',
		),
		'require' => 'admin'
	),

	'tables' => array(
		'description' => 'Mesas y Habitaciones de Ambientes',
		'actions' => array(
			'index' => 'Ingresar a selección de mesas y habitaciones en ambientes',
			'edit' => 'Adicionar, Modificar número y Eliminar mesas en ambientes'
		),
		'require' => 'appmenu'
	),
	'order' => array(
		'description' => 'Tomar pedidos',
		'actions' => array(
			'index' => 'Ingresar a toma pedidos',
			'add' => 'Realizar pedidos en los ambientes en los diferentes tipos de servicio',
			'modifiers' => 'Adicionar modificadores a los items del pedido',
			'notes' => 'Adicionar notas a los items del pedido',
			'sendKitchen' => 'Enviar impresión de producción a cocina',
		),
		'require' => 'appmenu'
	),
	'factura' => array(
		'description' => 'Facturas y Ordenes de Servicio',
		'actions' => array(
			'index' => 'Generar facturas y Ordenes de Servicio'
		),
		'require' => 'appmenu'
	),
	'mobile' => array(
		'description' => 'Tomar pedidos usando dispositivos móviles',
		'actions' => array(
			'index' => 'Ingresar a toma pedidos',
			'add' => 'Realizar pedidos en los ambientes en los diferentes tipos de servicio',
			'modifiers' => 'Adicionar modificadores a los items del pedido',
			'notes' => 'Adicionar notas a los items del pedido',
			'sendKitchen' => 'Enviar impresión de producción a cocina'
		),
		'require' => 'appmenu'
	),
	'modifiers' => array(
		'description' => 'Administrar Modificadores',
		'actions' => array(
			'index' => 'Ingresar a modificadores de items',
			'insert' => 'Adicionar a modificadores de items',
			'update' => 'Modificar a modificadores de items',
			'delete' => 'Borrar a modificadores de items',
			'query' => 'Consultar a modificadores de items',
			'browse' => 'Visualizar a modificadores de items',
			'query' => 'Generar reporte de modificadores de items',
		),
		'require' => 'admin'
	),
	'modifiers_items' => array(
		'description' => 'Modificadores de Items de Menús',
		'actions' => array(
			'index' => 'Ingresar y asignar modificadores de items de menús'
		),
		'require' => 'admin'
	),
	'pay' => array(
		'description' => 'Liquidar ordenes de servicio y facturas',
		'actions' => array(
			'index' => 'Ingresar y liquidar ordenes de servicio y facturas'
		),
		'require' => 'appmenu',
		'unauthorized' => 'tables'
	),
	'permisos' => array(
		'description' => 'Permisos de Usuarios en Ambientes',
		'actions' => array(
			'index' => 'Configurar Permisos de Usuarios en Ambientes'
		),
		'require' => 'admin'
	),
	'prefactura' => array(
		'description' => 'Pre-Facturas de Pedidos',
		'actions' => array(
			'index' => 'Generar Pre-Facturas de Pedidos'
		),
		'require' => 'appmenu'
	),
	'printers' => array(
		'description' => 'Impresoras de Producción',
		'actions' => array(
			'index' => 'Ingresar a impresoras de producción',
			'insert' => 'Adicionar a impresoras de producción',
			'update' => 'Modificar a impresoras de producción',
			'delete' => 'Borrar en impresoras de producción',
			'query' => 'Consultar a impresoras de producción',
			'browse' => 'Visualizar en impresoras de producción',
			'query' => 'Generar reporte de impresoras de producción',
		),
		'require' => 'admin'
	),
	'screens' => array(
		'description' => 'Pantallas de Estado de Pedidos',
		'actions' => array(
			'index' => 'Ingresar a pantallas de estado',
			'insert' => 'Adicionar a pantallas de estado',
			'update' => 'Modificar a pantallas de estado',
			'delete' => 'Borrar en pantallas de estado',
			'query' => 'Consultar a impresoras de producción',
			'browse' => 'Visualizar en pantallas de estado',
			'query' => 'Generar reporte de  pantallas de estado',
		),
		'require' => 'admin'
	),
	'recalcula_saldos' => array(
		'description' => 'Kardex de Inventarios',
		'actions' => array(
			'index' => 'Ingresar y consultar kardex de inventarios',
		),
		'require' => 'admin'
	),
	'reporte_bioseguridad' => array(
		'description' => 'Reporte de Bioseguridad',
		'actions' => array(
			'index' => 'Generar reporte bioseguridad',
		),
		'require' => 'admin'
	),
	'reporte_recetaconsolidado' => array(
		'description' => 'Reporte Estandar/Consolidado',
		'actions' => array(
			'index' => 'Generar reporte Estandar/Consolidado',
		),
		'require' => 'admin'
	),
	'receta' => array(
		'description' => 'Receta Estándar',
		'actions' => array(
			'index' => 'Ingresar, consultar y modificar kardex de inventarios',
		),
		'require' => 'admin'
	),
	'regenerar_clave' => array(
		'description' => 'Regenerar Clave a Usuarios',
		'actions' => array(
			'index' => 'Ingresar, consultar y modificar kardex de inventarios',
		),
		'require' => 'admin'
	),
	'reimprimir' => array(
		'description' => 'Reimpresión de Facturas y Ordenes',
		'actions' => array(
			'index' => 'Reimprimir Facturas y Ordenes de Servicio',
		),
		'require' => 'appmenu'
	),
	'reports' => array(
		'description' => 'Reportes del Sistema',
		'actions' => array(
			'index' => 'Ingreso a Reportes del Sistema',
			'cortesias' => 'Reporte de Cortesias/Funcionarios',
			'huesped' => 'Reporte de Huéspedes Actual',
			'cuadre_caja' => 'Cuadre de Caja (Cajero Actual)',
			'cuadre_caja_todos' => 'Cuadre de Caja (Todos)',
			'facturasHtml' => 'Reporte de Facturas',
			'mas_vendidos' => 'Reporte de Items más Vendidos',
			'menos_vendidos' => 'Reporte de Items menos Vendidos',
			'mayor_utilidad' => 'Reporte de Items de Mayor Utilidad',
			'menor_utilidad' => 'Reporte de Items de Menor Utilidad',
			'recordedMovement' => 'Reporte de Movimiento Digitado',
			'venta_plato_cajero' => 'Reporte de Venta por Plato (Cajero Actual)',
			'ventaPlato' => 'Reporte de Venta por Plato (Todos)'
		),
		'require' => 'appmenu'
	),
	'revert' => array(
		'description' => 'Devolver la fecha del sistema',
		'actions' => array(
			'index' => 'Ingresar y devolver la fecha del sistema',
		),
		'require' => 'appmenu'
	),
	'saldos' => array(
		'description' => 'Reporte de Saldos de Inventarios',
		'actions' => array(
			'index' => 'Ingresar y generar reporte de Saldos de Inventarios',
		),
		'require' => 'admin'
	),
	'salon' => array(
		'description' => 'Ambientes del Sistema',
		'actions' => array(
			'index' => 'Ingresar a ambientes del sistema',
			'insert' => 'Adicionar a ambientes del sistema',
			'update' => 'Modificar a ambientes del sistema',
			'delete' => 'Borrar a ambientes del sistema',
			'query' => 'Consultar a ambientes del sistema',
			'browse' => 'Visualizar a ambientes del sistema',
			'query' => 'Generar reporte de ambientes del sistema',
		),
		'require' => 'admin'
	),
	'salon_menus_items' => array(
		'description' => 'Items en Ambientes',
		'actions' => array(
			'index' => 'Ingresar a items en ambientes',
			'insert' => 'Adicionar a items en ambientes',
			'update' => 'Modificar a items en ambientes',
			'delete' => 'Borrar a items en ambientes',
			'query' => 'Consultar a items en ambientes',
			'browse' => 'Visualizar a items en ambientes',
			'query' => 'Generar reporte de items en ambientes',
		),
		'require' => 'admin'
	),
	'carta' => array(
		'description' => 'Subir Carta',
		'actions' => array(
			'index' => 'Ingresar a subir carta',
		),
		'require' => 'admin'
	),
	'status' => array(
		'description' => 'Estado de Pedidos',
		'actions' => array(
			'index' => 'Ingresar a estado de pedidos'
		),
		'require' => 'appmenu'
	),
	'tipo_venta' => array(
		'description' => 'Tipos de Servicio en Ambientes',
		'actions' => array(
			'index' => 'Ingresar a tipos de servicio en Ambientes'
		),
		'require' => 'admin'
	),
	'ventas_default' => array(
		'description' => 'Opciones de Servicios Menores',
		'actions' => array(
			'index' => 'Ingresar a opciones de servicios menores'
		),
		'require' => 'admin'
	),
	'tipo_servicio' => array(
		'description' => 'Tipos de Servicio',
		'actions' => array(
			'index' => 'Ingresar a tipos de servicio'
		),
		'require' => 'admin'
	),
	'rcs' => array(
		'description' => 'Revisiones de la información del sistema',
		'actions' => array(
			'index' => 'Ingresar y consultar revisiones de la información del sistema',
		),
		'require' => 'admin'
	),
	'usuarios_pos' => array(
		'description' => 'Usuarios del Sistema',
		'actions' => array(
			'index' => 'Ingresar a usuarios del sistema',
			'insert' => 'Adicionar en usuarios del sistema',
			'update' => 'Modificar en usuarios del sistema',
			'delete' => 'Borrar en usuarios del sistema',
			'query' => 'Consultar en usuarios del sistema',
			'browse' => 'Visualizar en usuarios del sistema',
			'query' => 'Generar reporte en usuarios del sistema'
		),
		'require' => 'admin'
	),
);