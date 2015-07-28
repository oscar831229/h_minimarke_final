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
		'title' => 'Registro',
		'description' => 'Opciones de Registro',
		'options' => array(
			'reservas',
			'contratos',			
			'proyeccion',
			'cambio_contratos'
		)
	),
	array(
		'title' => 'Pagos',
		'description' => 'Opciones de Pagos',
		'options' => array(
			'abono_reserva',
			'abono_contrato',
			'recibos_pagos'
		)
	),
	array(
		'title' => 'Informes',
		'description' => 'Opciones de Informes',
		'options' => array(
			'cuenta_cobro',
			'socios_aldia',
			'cartera_edades',
			'proyeccion_cartera',
			'cartera_consolidada',
			'propietarios'
		)
	),
	array(
		'title' => 'Básicas',
		'description' => 'Opciones de Básicas',
		'options' => array(
			'empresa',
			'cuentas',
			'tipo_contrato',
			'interes_usura',
			'estado_civil',
			'tipo_documento',
			'profesion',
			'membresia',
			'tipo_socios',
			'temporada',
			'premio',
			'periodo',
			'motivo_desistimiento',
			'formas_pago',
			'derecho_afiliacion',
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
	
	//Menu Registro
	'reservas' => array(
		'elevation' => true,
		'description' => 'Reservas de Socios',
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
	
	
	
	//Menu Registro
	'contratos' => array(
		'elevation' => true,
		'description' => 'Contratos de Socios',
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
	'proyeccion' => array(
		'elevation' => true,
		'description' => 'Proyección de Contrato',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),			
			'generar' => array(
				'description' => 'Genera la'
			)
		)
	),
	'cambio_contratos' => array(
		'elevation' => true,
		'description' => 'Cambio de contratos',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),			
			'generar' => array(
				'description' => 'Genera la'
			)
		)
	),
	
	//PAGOS
	'abono_reserva' => array(
		'elevation' => true,
		'description' => 'Abono a Reservas',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'abono' => array(
				'description' => 'Abonar en'
			),
		)
	),
	'abono_contrato' => array(
		'elevation' => true,
		'description' => 'Abono a Contrato',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'abono' => array(
				'description' => 'Abonar en'
			),
		)
	),
	'recibos_pagos' => array(
		'elevation' => true,
		'description' => 'Recibos Pagos',
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
	
	//INFORMES
	'cuenta_cobro' => array(
		'elevation' => true,
		'description' => 'Cuentas de Cobro',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'generar' => array(
				'description' => 'generar informe de'
			),
		)
	),
	'socios_aldia' => array(
		'elevation' => true,
		'description' => 'Informe socios al día',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'generar' => array(
				'description' => 'generar informe en'
			),
		)
	),
	'cartera_edades' => array(
		'elevation' => true,
		'description' => 'Informe cartera por edades',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'generar' => array(
				'description' => 'generar informe en'
			),
		)
	),
	'proyeccion_cartera' => array(
		'elevation' => true,
		'description' => 'Informe proyección de cartera',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'generar' => array(
				'description' => 'generar informe en'
			),
		)
	),
	'cartera_consolidada' => array(
		'elevation' => true,
		'description' => 'Informe cartera consolidada',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'generar' => array(
				'description' => 'generar informe en'
			),
		)
	),
	'propietarios' => array(
		'elevation' => true,
		'description' => 'Informe propietarios',
		'actions' => array(
			'index' => array(
				'description' => 'Ingreso a',
			),
			'generar' => array(
				'description' => 'generar informe en'
			),
		)
	),

	
	
	//BÁSICAS
	'empresa' => array(
		'elevation' => true,
		'description' => 'Datos de la Empresa',
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
	
	'cuentas' => array(
		'elevation' => true,
		'description' => 'Cuentas bancarias',
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
	
	'tipo_contrato' => array(
		'elevation' => true,
		'description' => 'Tipo de Contrato',
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
		
	'interes_usura' => array(
		'elevation' => true,
		'description' => 'Interes de Mora',
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
		
	'estado_civil' => array(
		'elevation' => true,
		'description' => 'Estados civiles',
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
	
	'tipo_documento' => array(
		'elevation' => true,
		'description' => 'Tipo de documentos',
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
	
	'profesion' => array(
		'elevation' => true,
		'description' => 'Profesiones',
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
	
	'membresia' => array(
		'elevation' => true,
		'description' => 'Membresias',
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
	
	'temporada' => array(
		'elevation' => true,
		'description' => 'Temporadas',
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
	
	
	'premio' => array(
		'elevation' => true,
		'description' => 'Premios',
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
	'motivo_desistimiento' => array(
		'elevation' => true,
		'description' => 'Motivos de Desistimiento',
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
		'description' => 'Formas de pago',
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
	'derecho_afiliacion' => array(
		'elevation' => true,
		'description' => 'Derecho de Afilación',
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
		'description' => 'Configuración de Tiempo Compartido',
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
