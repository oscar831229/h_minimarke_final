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
		'title' => 'Maestro de Socios',
		'description' => 'Opciones de Socios',
		'options' => array(
			'socios',
			'rechazos',
			//NO se usa //'cambio_accion',
			'asignacion_estados',
			'asignacion_cargos',
			'asignacion_cargos_grupo',
            'cambio_categoria',
			'pagos_automaticos'
		)
	),
	array(
		'title' => 'Facturación',
		'description' => 'Opciones de Facturación',
		'options' => array(
			'cargos_socios',
			//No se usa//'movimiento_cargos',
			'facturar',
			'facturar_personal',
			'novedades_factura',
			'proyeccion',
			'prestamos_socios',
			'importar_pagos'
		)
	),
	array(
		'title' => 'Informes',
		'description' => 'Informes de Socios',
		'options' => array(
			'informe_rc',
			'consulta_socios',
			'suspendidos_mora',
			'facturas_generadas',
			'conceptos_causados',
			'informe_cartera',
			'informe_convenios',
			'estado_cuenta',
			'estado_cuenta_consolidado',
			'estado_cuenta_validacion',
			'validacion_categorias',
			'cumpleanos',
			'pagos_periodo'
		)
	),
	array(
		'title' => 'Cierres',
		'description' => 'Cierres',
		'options' => array(
			'cierre_periodo',
			'reabrir_periodo'
		)
	),
	array(
		'title' => 'Básicas',
		'description' => 'Opciones de Básicas en socios',
		'options' => array(
			//Socios
			'tipo_correspondencia',
			'tipo_documentos',
			'hobbies',
			'parentescos',
			'estados_civiles',
			'tipo_socios',
			'tipo_titularidad',
			'clubes',
			'estados_socios',
			'accion_estados',
			'tipo_asociacion_socio',
			'formas_pago',
			'interes_mora',
			'tipos_pago',
			'categoria_edad',
			//Basicas
			'cargos_fijos',
			'consecutivos',
            'periodo',
            'cargos_fijos_categoria',
			'datos_club',
			'settings'
		)
	)
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
	//Básicas
	'socios' => array(
		'elevation' => false,
		'description' => 'Socios',
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
			),
			'queryCuenta' => array(
				'description' => 'Consultar en',
			),
			'queryByName' => array(
				'sameAs' => 'queryCuenta',
			)
		)
	),
	
	'rechazos' => array(
		'elevation' => false,
		'description' => 'Solicitudes Rechazadas',
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
	'cambio_accion' => array(
		'elevation' => false,
		'description' => 'Cambio de Acción',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'generar' => array(
				'description' => 'Actualizar el'
			),
		)
	),
  	
	
	//Menu Cartera
	'parametros_basicos_cartera' => array(
		'elevation' => true,
		'description' => 'Parametros Basicos de Cartera',
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
	'asignacion_cargos' => array(
		'elevation' => true,
		'description' => 'Asignacion de Cargos',
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
			),
		)
	),
	'asignacion_cargos_grupo' => array(
		'elevation' => true,
		'description' => 'Asignacion de Cargos por Tipo de Socio',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'asignar' => array(
				'description' => 'Actualizar el'
			),
			'borrar' => array(
				'description' => 'Actualizar el'
			),
		)
	),
	'pagos_automaticos' => array(
		'elevation' => true,
		'description' => 'Pagos Automáticos',
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
	
	//Menu facturación
	'cargos_socios' => array(
		'elevation' => true,
		'description' => 'Generar Cargos Mensuales',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'save' => array(
				'description' => 'Actualizar el'
			),
		)
	),
	'movimiento_cargos' => array(
		'elevation' => true,
		'description' => 'Generar Movimiento de Cargos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'save' => array(
				'description' => 'Actualizar el'
			),
		)
	),
	'facturar' => array(
		'elevation' => true,
		'description' => 'Generar Facturas Periodicas',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'save' => array(
				'description' => 'Crear las'
			),
			'reporteFactura'  => array(
				'description' => 'Imprimir las'
			),
			'borrar'  => array(
				'description' => 'Borrar las'
			),
		)
	),
	'facturar_personal' => array(
		'elevation' => true,
		'description' => 'Generar Factura por Socio',
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
			),
			'reporteFactura'  => array(
				'description' => 'Imprimir la'
			),
			'borrar'  => array(
				'description' => 'Borrar la'
			),
		)
	),
	'asignacion_estados' => array(
		'elevation' => true,
		'description' => 'Asignación de Estados',
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
	'cambio_categoria' => array(
		'elevation' => true,
		'description' => 'Cambio de Categoría',
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
	'novedades_factura' => array(
		'elevation' => true,
		'description' => 'Novedades de Factura',
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
	'proyeccion' => array(
		'elevation' => true,
		'description' => 'Proyección',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'generar' => array(
				'description' => 'Generar la'
			),
		)
	),
	'prestamos_socios' => array(
		'elevation' => true,
		'description' => 'Prestamos de Socios',
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
	'importar_pagos' => array(
		'elevation' => true,
		'description' => 'Importar Pagos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'generar' => array(
				'description' => 'Realizar '
			),
		)
	),
	//Menú Cierres
	'cierre_periodo' => array(
		'elevation' => true,
		'description' => 'Cerrar Periodo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'cierre' => array(
				'description' => 'generar el'
			),
		)
	),
	'reabrir_periodo' => array(
		'elevation' => true,
		'description' => 'Reabrir Periodo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'reabrir' => array(
				'description' => 'generar el'
			),
		)
	),
	

	//Menu Básicas
	'tipo_correspondencia' => array(
		'elevation' => true,
		'description' => 'Tipo de Correspondencia',
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
	'tipo_documentos' => array(
		'elevation' => true,
		'description' => 'Tipo de Documentos',
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
	'hobbies' => array(
		'elevation' => true,
		'description' => 'Hobbies',
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
	'parentescos' => array(
		'elevation' => true,
		'description' => 'Parentescos',
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
	'estados_civiles' => array(
		'elevation' => true,
		'description' => 'Estados Civiles',
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
	'estados_socios' => array(
		'elevation' => true,
		'description' => 'Estados Socios',
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
	'formas_pago' => array(
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
	'tipo_socios' => array(
		'elevation' => true,
		'description' => 'Tipo de Socios',
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
	'tipo_titularidad' => array(
		'elevation' => true,
		'description' => 'Tipo de Titularidad',
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
	'tipos_pago' => array(
		'elevation' => true,
		'description' => 'Tipos de Pago de Socio',
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
	'clubes' => array(
		'elevation' => true,
		'description' => 'Clubes',
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
	'interes_mora' => array(
		'elevation' => true,
		'description' => 'Intereses de Mora',
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
	'cargos_fijos' => array(
		'elevation' => true,
		'description' => 'Cargos Fijos',
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
	'periodo' => array(
		'elevation' => true,
		'description' => 'Periodos',
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
	'tipo_asociacion_socio' => array(
		'elevation' => true,
		'description' => 'Tipo de Asociación con otro Socio',
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
	'bancos' => array(
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
	'cargos_fijos_categoria' => array(
		'elevation' => true,
		'description' => 'Cargos Fijos x Categoría',
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
	'datos_club' => array(
		'elevation' => true,
		'description' => 'Datos del Club',
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
		'description' => 'Configuración de Socios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'save' => array(
				'description' => 'Actualizar la'
			),
		)
	),
	
	//INFORMES
	'consulta_socios' => array(
		'elevation' => true,
		'description' => 'Consulta de socios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a la',
			),
			'generar' => array(
				'description' => 'Generar la'
			),
		)
	),
	'suspendidos_mora' => array(
		'elevation' => true,
		'description' => 'Suspendidos por Mora',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a la',
			),
			'generar' => array(
				'description' => 'Generar la'
			),
		)
	),
	'facturas_generadas' => array(
		'elevation' => true,
		'description' => 'Facturas Generadas',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a la',
			),
			'generar' => array(
				'description' => 'Generar la'
			),
		)
	),
	'conceptos_causados' => array(
		'elevation' => true,
		'description' => 'Conceptos Causados',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a la',
			),
			'generar' => array(
				'description' => 'Generar la'
			),
		)
	),
	'informe_cartera' => array(
		'elevation' => true,
		'description' => 'Informe de Cartera',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a la',
			),
			'generar' => array(
				'description' => 'Generar la'
			),
		)
	),
	'informe_convenios' => array(
		'elevation' => true,
		'description' => 'Estado de Cuentas Convenios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a la',
			),
			'generar' => array(
				'description' => 'Generar la'
			),
		)
	),
	'informe_rc' => array(
		'elevation' => true,
		'description' => 'Informe de Recibos de Caja',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a la',
			),
			'generar' => array(
				'description' => 'Generar la'
			),
		)
	),
	'accion_estados' => array(
		'elevation' => true,
		'description' => 'Accion de Estados',
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
	'estado_cuenta' => array(
		'elevation' => true,
		'description' => 'Estado de Cuenta',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'generar' => array(
				'description' => 'Generar el'
			),
			'report' => array(
				'description' => 'Imprimir el'
			),
			'send' => array(
				'description' => 'Envia por correo el'
			),
		)
	),
	'estado_cuenta_consolidado' => array(
		'elevation' => true,
		'description' => 'Estado de Cuenta Consolidado',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'report' => array(
				'description' => 'Generar el'
			)
		)
	),
	'estado_cuenta_validacion' => array(
		'elevation' => true,
		'description' => 'Validación de Estados de Cuenta',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a la',
			),
			'report' => array(
				'description' => 'Generar a la'
			)
		)
	),
	'validacion_categorias' => array(
		'elevation' => true,
		'description' => 'Validación de Categorias',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a la',
			),
			'report' => array(
				'description' => 'Generar a la'
			)
		)
	),
	'cumpleanos' => array(
		'elevation' => true,
		'description' => 'Cumpleaños de Socios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a la',
			),
			'report' => array(
				'description' => 'Generar a la'
			)
		)
	),
	'pagos_periodo' => array(
		'elevation' => true,
		'description' => 'Pagos realizados en el periodo',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso al',
			),
			'report' => array(
				'description' => 'Generar el'
			)
		)
	),

	'consecutivos' => array(
		'elevation' => true,
		'description' => 'Consecutivos',
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

    'categoria_edad' => array(
        'elevation' => true,
        'description' => 'Categoria X Edad',
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
);
