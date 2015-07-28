
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

Hfos.getApplication().getMenu().setOptions([
	{
		'title': 'Registro',
		'icon': 'users.png',
		'options': [
			{
				'title': 'Reservas',
				'icon': 'hire-me.png',
				'click': function(){
					Hfos.getApplication().run({
					id: 'win-reservas-tpc',
					icon: 'hire-me.png',
					title: "Reservas",
					width: '900px',
					height: '570px',
					action: 'reservas'
					});
				},
			},
			{
				'title': 'Contratos',
				'icon': 'hire-me.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-contratos-tpc',
						icon: 'hire-me.png',
						title: "Contratos",
						width: '1000px',
						height: '600px',
						action: 'contratos'
					});
				}
			},
			{
				'title': 'Proyección',
				'icon': 'attibutes.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-proyeccion-tpc',
						icon: 'attibutes.png',
						title: "Proyección",
						width: '700px',
						height: '450px',
						action: 'proyeccion'
					});
				}
			},
			{
				'title': 'Cambio de Contratos',
				'icon': 'blue-document-convert.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-cambio-contratos-tpc',
						icon: 'blue-document-convert.png',
						title: "Cambio de Contratos",
						width: '700px',
						height: '330px',
						action: 'cambio_contratos'
					});
				}
			}
		]
	},
	{
		'title': 'Pagos',
		'icon': 'cartera.png',
		'options': [
			{
				'title': 'Abono a Reserva',
				'icon': 'cheque.png',
				'click': function(){
					icon: 'cheque.png',
					Hfos.getApplication().run({
						id: 'win-abono-reserva-tpc',
						icon: 'cheque.png',
						title: "Abono a Reserva",
						width: '1000px',
						height: '550px',
						action: 'abono_reserva'
					});
				}
			},
			{
				'title': 'Abono a Contrato',
				'icon': 'cheque.png',
				'click': function(){
					icon: 'cheque.png',
					Hfos.getApplication().run({
						id: 'win-abono-contrato-tpc',
						icon: 'cheque.png',
						title: "Abono a Contrato",
						width: '1000px',
						height: '550px',
						action: 'abono_contrato'
					});
				}
			},
			{
				'title': 'Ajuste de Recibos',
				'icon': 'cheque.png',
				'click': function(){
					icon: 'cheque.png',
					Hfos.getApplication().run({
						id: 'win-recibos-pagos-tpc',
						icon: 'cheque.png',
						title: "Ajuste de Recibos",
						width: '1000px',
						height: '550px',
						action: 'recibos_pagos'
					});
				}
			}
		]
	},
	{
		'title': 'Informes',
		'icon': 'document-list.png',
		'options': [
			{
				'title'	: 'Cuentas de Cobro',
				'icon'	: 'document-list.png',
				'click'	: function(){
					icon: 'document-list.png',
					Hfos.getApplication().run({
						id		: 'win-cuenta-cobro-tpc',
						icon	: 'document-list.png',
						title	: "Cuentas de Cobro",
						width	: '600px',
						height	: '450px',
						action	: 'cuenta_cobro'
					});
				}
			},
			{
				'title'	: 'Pago de socios al día',
				'icon'	: 'document-list.png',
				'click'	: function(){
					icon: 'document-list.png',
					Hfos.getApplication().run({
						id		: 'win-socios-aldia-tpc',
						icon	: 'document-list.png',
						title	: "Pago de socios al día",
						width	: '500px',
						height	: '350px',
						action	: 'socios_aldia'
					});
				}
			},
			{
				'title'	: 'Cartera por edades',
				'icon'	: 'document-list.png',
				'click'	: function(){
					icon: 'document-list.png',
					Hfos.getApplication().run({
						id		: 'win-cartera-edades-tpc',
						icon	: 'document-list.png',
						title	: "Cartera por edades",
						width	: '1000px',
						height	: '550px',
						action	: 'cartera_edades'
					});
				}
			},
			{
				'title'	: 'Proyeción de cartera',
				'icon'	: 'document-list.png',
				'click'	: function(){
					icon: 'document-list.png',
					Hfos.getApplication().run({
						id		: 'win-proyeccion-cartera-tpc',
						icon	: 'document-list.png',
						title	: "Proyeción de cartera",
						width	: '1000px',
						height	: '550px',
						action	: 'proyeccion_cartera'
					});
				}
			},
			{
				'title'	: 'Cartera consolidada',
				'icon'	: 'document-list.png',
				'click'	: function(){
					icon: 'document-list.png',
					Hfos.getApplication().run({
						id		: 'win-cartera-consolidada-tpc',
						icon	: 'document-list.png',
						title	: "Cartera consolidada",
						width	: '1000px',
						height	: '550px',
						action	: 'cartera_consolidada'
					});
				}
			},
			{
				'title'	: 'Propietarios Avanzado',
				'icon'	: 'document-list.png',
				'click'	: function(){
					icon: 'document-list.png',
					Hfos.getApplication().run({
						id		: 'win-propietarios-tpc',
						icon	: 'document-list.png',
						title	: "Propietarios Avanzado",
						width	: '900px',
						height	: '550px',
						action	: 'propietarios'
					});
				}
			}
		]
	},
	{
		'title': 'Básicas',
		'icon': 'wrench-screwdriver.png',
		'options': [
			{
				'title': 'Contratos',
				'icon': 'cheque.png',
				'options': [
					{
						'title': 'Tipo de Contrato',
						'icon': 'type-user.png',
						'click': function(){
							icon: 'type-user.png',
							Hfos.getApplication().run({
								id: 'win-tipo-contrato-tpc',
								icon: 'type-user.png',
								title: "Tipo de Contrato",
								width: '700px',
								height: '350px',
								action: 'tipo_contrato'
							});
						}
					},
					{
						'title': 'Estados Civiles',
						'icon': 'formatos.png',
						'click': function(){
							icon: 'formatos.png',
							Hfos.getApplication().run({
								id: 'win-estado-civil-tpc',
								icon: 'formatos.png',
								title: "Estados Civiles",
								width: '700px',
								height: '350px',
								action: 'estado_civil'
							});
						}
					},
					{
						'title': 'Tipo de Documento',
						'icon': 'my-account.png',
						'click': function(){
							icon: 'my-account.png',
							Hfos.getApplication().run({
								id: 'win-tipo-documento-tpc',
								icon: 'my-account.png',
								title: "Tipo de Documento",
								width: '700px',
								height: '350px',
								action: 'tipo_documento'
							});
						}
					},
					{
						'title': 'Profesiones',
						'icon': 'formatos.png',
						'click': function(){
							icon: 'formatos.png',
							Hfos.getApplication().run({
								id: 'win-profesion-tpc',
								icon: 'formatos.png',
								title: "Profesiones",
								width: '700px',
								height: '350px',
								action: 'profesion'
							});
						}
					},
					{
						'title': 'Membresias',
						'icon': 'formatos.png',
						'click': function(){
							icon: 'formatos.png',
							Hfos.getApplication().run({
								id: 'win-membresia-tpc',
								icon: 'formatos.png',
								title: "Membresias",
								width: '700px',
								height: '350px',
								action: 'membresia'
							});
						}
					},
					{
						'title': 'Tipo de Socio',
						'icon': 'my-account.png',
						'click': function(){
							icon: 'my-account.png',
							Hfos.getApplication().run({
								id: 'win-tipo-socios-tpc',
								icon: 'my-account.png',
								title: "Tipo de Socio",
								width: '700px',
								height: '350px',
								action: 'tipo_socios'
							});
						}
					},
					{
						'title': 'Temporadas',
						'icon': 'formatos.png',
						'click': function(){
							icon: 'formatos.png',
							Hfos.getApplication().run({
								id: 'win-temporada-tpc',
								icon: 'formatos.png',
								title: "Temporadas",
								width: '700px',
								height: '350px',
								action: 'temporada'
							});
						}
					},
					{
						'title': 'Premios',
						'icon': 'cake.png',
						'click': function(){
							icon: 'cake.png',
							Hfos.getApplication().run({
								id: 'win-premio-tpc',
								icon: 'cake.png',
								title: "Premios",
								width: '700px',
								height: '350px',
								action: 'premio'
							});
						}
					}
				]
			},
			{
				'title': 'Cuentas Bancarias',
				'icon': 'formatos.png',
				'click': function(){
					icon: 'formatos.png',
					Hfos.getApplication().run({
						id: 'win-cuenta-banco-tpc',
						icon: 'formatos.png',
						title: "Cuentas Bancarias",
						width: '700px',
						height: '350px',
						action: 'cuentas'
					});
				}
			},
			{
				'title': 'Derechos de Afiliación',
				'icon': 'formatos.png',
				'click': function(){
					icon: 'formatos.png',
					Hfos.getApplication().run({
						id: 'win-derecho-afilaicion-tpc',
						icon: 'formatos.png',
						title: "Derechos de Afiliación",
						width: '700px',
						height: '350px',
						action: 'derecho_afiliacion'
					});
				}
			},
			{
				'title': 'Interes de Mora',
				'icon': 'table_money.png',
				'click': function(){
					icon: 'table_money.png',
					Hfos.getApplication().run({
						id: 'win-interes-mora-tpc',
						icon: 'table_money.png',
						title: "Interés de Mora",
						width: '700px',
						height: '420px',
						action: 'interes_usura'
					});
				}
			},
			{
				'title': 'Periodos',
				'icon': 'table_money.png',
				'click': function(){
					icon: 'table_money.png',
					Hfos.getApplication().run({
						id: 'win-periodo-tpc',
						icon: 'table_money.png',
						title: "Periodos",
						width: '700px',
						height: '420px',
						action: 'periodo'
					});
				}
			},
			{
				'title': 'Motivos de Desistimiento',
				'icon': 'attibutes.png',
				'click': function(){
					icon: 'cake.png',
					Hfos.getApplication().run({
						id: 'win-motivo-desistimiento-tpc',
						icon: 'attibutes.png',
						title: "Motivos de Desistimiento",
						width: '700px',
						height: '350px',
						action: 'motivo_desistimiento'
					});
				}
			},
			{
				'title': 'Formas de Pago',
				'icon': 'formatos.png',
				'click': function(){
					icon: 'formatos.png',
					Hfos.getApplication().run({
						id: 'win-formas-pago-tpc',
						icon: 'formatos.png',
						title: "Formas de Pago",
						width: '700px',
						height: '350px',
						action: 'formas_pago'
					});
				}
			},
			{
				'title': 'Empresa',
				'icon': 'bank.png',
				'click': function(){
					icon: 'bank.png',
					Hfos.getApplication().run({
						id: 'win-empresa-tpc',
						icon: 'bank.png',
						title: "Empresa",
						width: '700px',
						height: '450px',
						action: 'empresa'
					});
				}
			},
			{
				'title': 'Configuración',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-settings-tpc',
						icon: 'gear.png',
						title: "Configuración",
						width: '700px',
						height: '350px',
						action: 'settings'
					});
				}
			}
		]
	}
]
);
