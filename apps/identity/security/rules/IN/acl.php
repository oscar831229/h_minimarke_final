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

/**
 *
 * Variable que controla las opciones del menu que maneja la aplicación
 *
 * @var $menuDisposition tipo Array
 */
$menuDisposition = array(
	array(
		'title' => 'Entradas',
		'description' => 'Opciones de entrada en inventario',
		'options' => array(
			'ordenes',
			'entradas',
			'salidas',
			'ajustes',
			'traslados',
			'fisico'
		)
	),
	array(
		'title' => 'Salidas',
		'description' => 'Opciones de salida en inventario',
		'options' => array(
			'salidas',
			'salidas_buffet',
			'pedidos',
			'transformaciones'
		)
	),
	array(
		'title' => 'Informes',
		'description' => 'Opciones de informes en inventario',
		'options' => array(
			'kardex',
			'saldos_almacen',
			'saldos_almacen_consolidado',
			'stocks',
			'comportamiento',
			'movimientos',
			'consecutivos',
			'trasunto',
			'consumos',
			'listado_referencias',
			'listado_proveedores',
			'horti',
			'impresion'
		)
	),
	array(
		'title' => 'Cierres',
		'description' => 'Opciones de cierre en inventario',
		'options' => array(
			'cerrar',
			'reabrir'
		)
	),
	array(
		'title' => 'Básicas',
		'description' => 'Opciones de Báscias en inventario',
		'options' => array(
			'referencias',
			'lineas',
			'producto',
			'almacenes',
			'centros',
			'formapago',
			'terceros',
			'matriz_proveedores',
			'criterios',
			'unidades',
			'magnitudes',
			'conversion',
			'cuentas',
			'regimen_cuentas',
			'consumos_internos',
			'retecompras',
			'settings'
		)
	),
);


/**
 *
 * Variable que maneja los permisos de un controlador en una aplicacion
 * @var $accessList tipo Array
 */
$accessList = array(

	//base
	'workspace' => array(
		'elevation' => true,
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
			),
			'onRollback' => array(
				'description' => 'Deshacer transacción en'
			),
		)
	),


	//app-specific
	//Básicas
	'cuentas' => array(
		'elevation' => false,
		'description' => 'Plan de Cuentas',
		'actions' => array(
			'queryCuenta' => array(
				'description' => 'Consultar en',
			),
			'queryByName' => array(
				'sameAs' => 'queryCuenta',
			)
		)
	),
	'almacenes' => array(
		'elevation' => true,
		'description' => 'Almacenes',
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


	//MENU Entradas

	//Ordenes de compras
	'ordenes' => array(
		'elevation' => true,
		'description' => 'Órdenes de Compra',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
			'puedeReAbrir' => array(
				'description' => 'Re-Abrir en',
			),
		)
	),

	//ENTRADAS A ALMACEN
	'entradas' => array(
		'elevation' => true,
		'description' => 'Entradas al Almacén',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
		)
	),

	//Ajustes al Almacén
	'ajustes' => array(
		'elevation' => true,
		'description' => 'Ajustes al almacén',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
		)
	),

	//Traslados
	'traslados' => array(
		'elevation' => true,
		'description' => 'Traslados entre Almacenes',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
		)
	),

	//CONTEO FÍSICO
	'fisico' => array(
		'elevation' => true,
		'description' => 'Conteo Físico',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'consultar' => array(
				'description' => 'Buscar en',
			),
		)
	),

	//Menu Salidas
	'salidas' => array(
		'elevation' => true,
		'description' => 'Salidas de Almacén',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
		)
	),

	//Menu Salidas
	'salidas_buffet' => array(
		'elevation' => true,
		'description' => 'Salidas Buffet de Almacén',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
		)
	),

	'pedidos' => array(
		'elevation' => true,
		'description' => 'Requisiciones de Almacén',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
		)
	),
	'transformaciones' => array(
		'elevation' => true,
		'description' => 'Transformaciones',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
		)
	),

	//Menu INFORMES
	'kardex' => array(
		'elevation' => true,
		'description' => 'kardex de inventarios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),
	'saldos_almacen' => array(
		'elevation' => true,
		'description' => 'Listado de Saldos por Almacén',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),
	'stocks' => array(
		'elevation' => true,
		'description' => 'Stocks Altos y Bajos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),
	'comportamiento' => array(
		'elevation' => true,
		'description' => 'Comportamiento del Costo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),
	'saldos_almacen_consolidado' => array(
		'elevation' => true,
		'description' => 'Listado de Saldos por Almacén Consolidado',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),
	'movimientos' => array(
		'elevation' => true,
		'description' => 'Movimientos de Inventarios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
		)
	),
	'consecutivos' => array(
		'elevation' => true,
		'description' => 'Consecutivos de Inventarios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
		)
	),
	'trasunto' => array(
		'elevation' => true,
		'description' => 'Movimiento de Almacenes por Grupo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
		)
	),
	'consumos' => array(
		'elevation' => true,
		'description' => 'Consumos/Pedidos por Centro de Costo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),
	'listado_referencias' => array(
		'elevation' => true,
		'description' => 'Listado de Referencias',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
		)
	),
	'listado_proveedores' => array(
		'elevation' => true,
		'description' => 'Listado de Proveedores',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'horti' => array(
		'elevation' => true,
		'description' => 'Retefuentes Productos Hortifrutículas',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
		)
	),
	'impresion' => array(
		'elevation' => true,
		'description' => 'Impresión continua de Movimientos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
		)
	),

	//Menu Cierres
	'cerrar' => array(
		'elevation' => true,
		'description' => 'Cierre de Inventarios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),
	'reabrir' => array(
		'elevation' => true,
		'description' => 'Reabrir un Mes Cerrado',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//Menu Básicas
	'referencias' => array(
		'elevation' => true,
		'description' => 'Referencias',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'queryByItemAction' => array(
				'description' => 'Consulta en'
			),
			'queryByNameAction' => array(
				'description' => 'Consulta en'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			)
		)
	),
	'lineas' => array(
		'elevation' => true,
		'description' => 'Lineas',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			)
		)
	),
	'producto' => array(
		'elevation' => true,
		'description' => 'Tipos de Productos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			)
		)
	),
	'almacenes' => array(
		'elevation' => true,
		'description' => 'Almacenes',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
			'import' => array(
				'description' => 'Importar en'
			)
		)
	),
	'centros' => array(
		'elevation' => true,
		'description' => 'Centros de costo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
			'import' => array(
				'description' => 'Importar en'
			)
		)
	),
	'formapago' => array(
		'elevation' => true,
		'description' => 'Formas de pago',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
			'import' => array(
				'description' => 'Importar en'
			)
		)
	),
	'terceros' => array(
		'elevation' => true,
		'description' => 'Terceros',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
			'import' => array(
				'description' => 'Importar en'
			)
		)
	),
	'matriz_proveedores' => array(
		'elevation' => true,
		'description' => 'Matriz de Proveedores',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			)
		)
	),
	'criterios' => array(
		'elevation' => true,
		'description' => 'Criterios de Calificación de Proveedores',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
			'import' => array(
				'description' => 'Importar en'
			)
		)
	),
	'unidades' => array(
		'elevation' => true,
		'description' => 'Unidades de medida',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
			'import' => array(
				'description' => 'Importar en'
			)
		)
	),
	'magnitudes' => array(
		'elevation' => true,
		'description' => 'Magnitudes',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
			'import' => array(
				'description' => 'Importar en'
			)
		)
	),
	'conversion' => array(
		'elevation' => true,
		'description' => 'Conversión de Unidades',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
			'import' => array(
				'description' => 'Importar en'
			)
		)
	),
	'regimen_cuentas' => array(
		'elevation' => true,
		'description' => 'Cuentas Regimen Tributario',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
			'import' => array(
				'description' => 'Importar en'
			)
		)
	),
	'settings' => array(
		'elevation' => true,
		'description' => 'Configuración de Inventarios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'save' => array(
				'description' => 'Actualizar la'
			),
		)
	),

	//Tatico
	'tatico' => array(
		'elevation' => false,
		'description' => 'Movimientos y Consultas de Inventario',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a'
			)
		)
	),

	//6.1.8
	'consumos_internos' => array(
		'elevation' => true,
		'description' => 'Consumos Internos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'new' => array(
				'description' => 'Crear en ',
			),
			'edit' => array(
				'description' => 'Editar en',
			),
			'delete' => array(
				'description' => 'Borrar en',
			),
			'search' => array(
				'description' => 'Consultar en',
			),
			'report' => array(
				'description' => 'Reporte en',
			),
		)
	),
	'retecompras' => array(
		'elevation' => true,
		'description' => 'Retención de Compras',
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

);