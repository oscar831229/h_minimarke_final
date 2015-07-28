
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
		'title': 'Maestro de Socios',
		'icon': 'users.png',
		'options': [
			{
				'title': 'Socios',
				'icon': 'hire-me.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-socios-socios',
						icon: 'hire-me.png',
						title: "Socios",
						width: '1200px',
						height: '600px',
						action: 'socios'
					});
				}
			},
			{
				'title': 'Solicitudes Rechazadas',
				'icon': 'switch.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-rechazos-socios',
						icon: 'switch.png',
						title: "Solicitudes Rechazadas",
						width: '900px',
						height: '520px',
						action: 'rechazos'
					});
				}
			},
			/*{
				'title': 'Cambio de Acción',
				'icon': 'transfer.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-cambio-accion-socios',
						icon: 'transfer.png',
						title: "Cambio de Acción",
						width: '700px',
						height: '320px',
						action: 'cambio_accion'
					});
				}
			},*/
			{
				'title': 'Asignación de Cargos',
				'icon': 'cartera.png',
				'click': function(){
					icon: 'cartera.png',
					Hfos.getApplication().run({
						id: 'win-asignacion-cargos-socios',
						icon: 'cartera.png',
						title: "Asignación de Cargos",
						width: '800px',
						height: '450px',
						action: 'asignacion_cargos'
					});
				}
			},
			{
				'title': 'Asignación de Cargos Por Tipo de Socio',
				'icon': 'cartera.png',
				'click': function(){
					icon: 'cartera.png',
					Hfos.getApplication().run({
						id: 'win-asignacion-cargos-grupo-socios',
						icon: 'cartera.png',
						title: "Asignación de Cargos Por Tipo de Socio",
						width: '700px',
						height: '450px',
						action: 'asignacion_cargos_grupo'
					});
				}
			},
			{
				'title': 'Asignación de Estados',
				'icon': 'cheque.png',
				'click': function(){
					icon: 'cheque.png',
					Hfos.getApplication().run({
						id: 'win-asignacion-estados-socios',
						icon: 'cheque.png',
						title: "Asignación de Estados",
						width: '700px',
						height: '450px',
						action: 'asignacion_estados'
					});
				}
			},
			{
				'title': 'Cambio de Categoría',
				'icon': 'type-user.png',
				'click': function(){
					icon: 'type-user.png',
					Hfos.getApplication().run({
						id: 'win-cambio-categoria-socios',
						icon: 'type-user.png',
						title: "Cambio de Categoría",
						width: '700px',
						height: '450px',
						action: 'cambio_categoria'
					});
				}
			},
			{
				'title'	: 'Pagos Automáticos',
				'icon'	: 'cheque.png',
				'click'	: function(){
					icon: 'cheque.png',
					Hfos.getApplication().run({
						id		: 'win-pagos-automaticos-socios',
						icon	: 'cheque.png',
						title	: 'Pagos Automáticos',
						width	: '700px',
						height	: '450px',
						action	: 'pagos_automaticos'
					});
				}
			}
		]
	},
	{
		'title': 'Facturación',
		'icon': 'finance-2.png',
		'options': [
			{
				'title': 'Generar cargos mensuales',
				'icon': 'publish.png',
				'click': function(){
					icon: 'cheque.png',
					Hfos.getApplication().run({
						id: 'win-cargos-socios-socios',
						icon: 'publish.png',
						title: "Generar cargos mensuales",
						width: '700px',
						height: '350px',
						action: 'cargos_socios'
					});
				}
			},
			{
				'title': 'Facturas Periodicas',
				'icon': 'cheque.png',
				'click': function(){
					icon: 'cheque.png',
					Hfos.getApplication().run({
						id: 'win-facturar-socios',
						icon: 'cheque.png',
						title: "Facturas Periodicas",
						width: '900px',
						height: '700px',
						action: 'facturar'
					});
				}
			},
			{
				'title': 'Facturas por Socio',
				'icon': 'cheque.png',
				'click': function(){
					icon: 'cheque.png',
					Hfos.getApplication().run({
						id: 'win-facturar-personal-socios',
						icon: 'cheque.png',
						title: "Facturas por Socio",
						width: '700px',
						height: '350px',
						action: 'facturar_personal'
					});
				}
			},
			{
				'title': 'Novedades de Factura',
				'icon': 'document-list.png',
				'click': function(){
					icon: 'cheque.png',
					Hfos.getApplication().run({
						id: 'win-novedades-factura-socios',
						icon: 'document-list.png',
						title: "Novedades de Factura",
						width: '870px',
						height: '550px',
						action: 'novedades_factura'
					});
				}
			},
			{
				'title': 'Convenios',
				'icon': 'cartera.png',
				'options': [
					{
						'title': 'Proyección simulada',
						'icon': 'cheque.png',
						'click': function(){
							icon: 'cheque.png',
							Hfos.getApplication().run({
								id: 'win-proyeccion-socios',
								icon: 'cheque.png',
								title: "Proyección",
								width: '700px',
								height: '350px',
								action: 'proyeccion'
							});
						}
					},
					{
						'title': 'Convenios',
						'icon': 'cartera.png',
						'click': function(){
							icon: 'cartera.png',
							Hfos.getApplication().run({
								id: 'win-prestamos-socios',
								icon: 'cartera.png',
								title: "Convenios",
								width: '800px',
								height: '550px',
								action: 'prestamos_socios'
							});
						}
					}
				]
			},
			{
				'title': 'Importar Pagos',
				'icon': 'credit-card.png',
				'click': function(){
					icon: 'credit-card.png',
					Hfos.getApplication().run({
						id: 'win-importar-pagos-socios',
						icon: 'credit-card.png',
						title: "Importar Pagos",
						width: '900px',
						height: '450px',
						action: 'importar_pagos'
					});
				}
			},
		]
			
	},
	{
		'title': 'Informes',
		'icon': 'archives.png',
		'options': [
			{
				'title': 'Socios',
				'icon': 'business-contact.png',
				'options': [
					{
						'title': 'Consulta de socios',
						'icon': 'business-contact.png',
						'click': function(){
							icon: 'business-contact.png',
							Hfos.getApplication().run({
								id: 'win-consulta-socios',
								icon: 'business-contact.png',
								title: "Consulta de socios",
								width: '700px',
								height: '350px',
								action: 'consulta_socios'
							});
						}
					},
					{
						'title': 'Suspendidos por Mora',
						'icon': 'calculator.png',
						'click': function(){
							icon: 'calculator.png',
							Hfos.getApplication().run({
								id: 'win-suspendidos-socios',
								icon: 'calculator.png',
								title: "Suspendidos por Mora",
								width: '700px',
								height: '350px',
								action: 'suspendidos_mora'
							});
						}
					},
					{
						'title': 'Validación de Categorias',
						'icon': 'publish.png',
						'click': function(){
							icon: 'cake.png',
							Hfos.getApplication().run({
								id: 'win-validacion-categorias-socios',
								icon: 'publish.png',
								title: "Validación de Categorias",
								width: '700px',
								height: '350px',
								action: 'validacion_categorias'
							});
						}
					},
					{
						'title': 'Cumpleaños de Socios',
						'icon': 'cake.png',
						'click': function(){
							icon: 'cake.png',
							Hfos.getApplication().run({
								id: 'win-cumpleanos-socios',
								icon: 'cake.png',
								title: "Cumpleaños de Socios",
								width: '700px',
								height: '350px',
								action: 'cumpleanos'
							});
						}
					},
				]
			},
			{
				'title': 'Facturas',
				'icon': 'finance-2.png',
				'options': [
					{
		                'title': 'Facturas Generadas',
		                'icon': 'finance-2.png',
		                'click': function(){
		                    icon: 'finance-2.png',
		                    Hfos.getApplication().run({
		                            id: 'win-facturas-generadas-socios',
		                            icon: 'finance-2.png',
		                            title: "Facturas Generadas",
		                            width: '700px',
		                            height: '350px',
		                            action: 'facturas_generadas'
		                    });
		                }
		        	},
		           	{
		                'title': 'Conceptos Causados',
		                'icon': 'issue.png',
		                'click': function(){
		                    icon: 'issue.png',
		                    Hfos.getApplication().run({
		                            id: 'win-conceptos-causados-socios',
		                            icon: 'issue.png',
		                            title: "Conceptos Causados",
		                            width: '700px',
		                            height: '350px',
		                            action: 'conceptos_causados'
		                    });
		                }
		        	},
		        ]
		    },
		    {
				'title': 'Cartera',
				'icon': 'cartera.png',
				'options': [
		        	{
						'title': 'Cartera por Edades',
						'icon': 'cartera.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-informe-cartera-socios',
								icon: 'cartera.png',
								title: "Cartera por Edades",
								action: "informe_cartera",
								width: '750px',
								height: '400px'
							});
						}
					},
		        	{
						'title': 'Estado de Cuenta Convenios',
						'icon': 'cartera.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-informe-convenios-socios',
								icon: 'cartera.png',
								title: "Estado de Cuenta Convenios",
								action: "informe_convenios",
								width: '750px',
								height: '400px'
							});
						}
					},
				]
			},
			{
				'title': 'Estados de Cuenta',
				'icon': 'invoice.png',
				'options': [
					{
						'title': 'Estado de Cuenta',
						'icon': 'invoice.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-estado-cuenta-socios',
								icon: 'invoice.png',
								title: "Estado de Cuenta",
								action: "estado_cuenta",
								width: '750px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Estado de Cuenta Consolidado',
						'icon': 'invoice.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-estado-cuenta-consolidado-socios',
								icon: 'invoice.png',
								title: "Estado de Cuenta Consolidado",
								action: "estado_cuenta_consolidado",
								width: '750px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Validación de Estados de Cuenta',
						'icon': 'publish.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-estado-cuenta-validacion-socios',
								icon: 'publish.png',
								title: "Validación de Estados de Cuenta",
								action: "estado_cuenta_validacion",
								width: '750px',
								height: '400px'
							});
						}
					},
				]
			},
			{
				'title': 'Informe de Recibos de Caja',
				'icon': 'contables.png',
				'click': function(){
					icon: 'contables.png',
					Hfos.getApplication().run({
						id: 'win-informe-rc-socios',
						icon: 'contables.png',
						title: "Informe de Recibos de Caja",
						width: '700px',
						height: '350px',
						action: 'informe_rc'
					});
				}
			},
			{
				'title': 'Informe de Pagos en Periodo',
				'icon': 'cheque.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-informe-pagos-periodo-socios',
						icon: 'cheque.png',
						title: "Informe de Pagos en Periodo",
						action: "pagos_periodo",
						width: '750px',
						height: '400px'
					});
				}
			},
		]
	},
	{
		'title': 'Cierres',
		'icon': 'limited-edition.png',
		'options': [
			{
				'title': 'Cierre Periodo',
				'icon': 'limited-edition.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-cierre-socios',
						icon: 'limited-edition.png',
						title: "Cierre de Periodo",
						action: "cierre_periodo",
						width: '550px',
						height: '350px'
					});
				},
			},
			{
				'title': 'Reabrir Periodo',
				'icon': 'refresh.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-reabrir-socios',
						icon: 'refresh.png',
						title: "Reabrir Cierre de Periodo",
						action: "reabrir_periodo",
						width: '550px',
						height: '350px'
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
				'title': 'Socios',
				'icon': 'users.png',
				'options': [
					{
						'title': 'Tipo de Correspondencia',
						'icon': 'switch.png',
						'click': function(){
							icon: 'switch.png',
							Hfos.getApplication().run({
								id: 'win-tipo_correspondencia-socios',
								icon: 'switch.png',
								title: "Tipo de Correspondencia",
								width: '700px',
								height: '350px',
								action: 'tipo_correspondencia'
							});
						}
					},
					{
						'title': 'Tipo de Documentos',
						'icon': 'my-account.png',
						'click': function(){
							icon: 'my-account.png',
							Hfos.getApplication().run({
								id: 'win-tipo_documentos-socios',
								icon: 'my-account.png',
								title: "Tipo de Documentos",
								width: '700px',
								height: '350px',
								action: 'tipo_documentos'
							});
						}
					},
					{
						'title': 'Hobbies',
						'icon': 'sitemap.png',
						'click': function(){
							icon: 'sitemap.png',
							Hfos.getApplication().run({
								id: 'win-hobbies-socios',
								icon: 'sitemap.png',
								title: "Hobbies",
								width: '700px',
								height: '350px',
								action: 'hobbies'
							});
						}
					},
					{
						'title': 'Parentescos',
						'icon': 'user.png',
						'click': function(){
							icon: 'user.png',
							Hfos.getApplication().run({
								id: 'win-parentescos-socios',
								icon: 'user.png',
								title: "Parentescos",
								width: '700px',
								height: '350px',
								action: 'parentescos'
							});
						}
					},
					{
						'title': 'Estados Civiles',
						'icon': 'users.png',
						'click': function(){
							icon: 'users.png',
							Hfos.getApplication().run({
								id: 'win-estados-civiles-socios',
								icon: 'users.png',
								title: "Estados Civiles",
								width: '700px',
								height: '350px',
								action: 'estados_civiles'
							});
						}
					},
					{
						'title': 'Tipo de Socios',
						'icon': 'type-user.png',
						'click': function(){
							icon: 'type-user.png',
							Hfos.getApplication().run({
								id: 'win-tipo-socios-socios',
								icon: 'type-user.png',
								title: "Tipo de Socio",
								width: '700px',
								height: '450px',
								action: 'tipo_socios'
							});
						}
					},
					{
						'title': 'Tipo de Titularidad',
						'icon': 'type-user.png',
						'click': function(){
							icon: 'type-user.png',
							Hfos.getApplication().run({
								id: 'win-tipo-titularidad-socios',
								icon: 'type-user.png',
								title: "Tipo de Titularidad",
								width: '700px',
								height: '350px',
								action: 'tipo_titularidad'
							});
						}
					},
					{
						'title': 'Clubes',
						'icon': 'category.png',
						'click': function(){
							icon: 'category.png',
							Hfos.getApplication().run({
								id: 'win-clubes-socios',
								icon: 'category.png',
								title: "Clubes",
								width: '700px',
								height: '350px',
								action: 'clubes'
							});
						}
					},
					{
						'title': 'Estados de Socios',
						'icon': 'users.png',
						'click': function(){
							icon: 'users.png',
							Hfos.getApplication().run({
								id: 'win-estados-socios-socios',
								icon: 'users.png',
								title: "Estados de Socios",
								width: '700px',
								height: '350px',
								action: 'estados_socios'
							});
						}
					},
					{
						'title': 'Acción de Estados',
						'icon': 'users.png',
						'click': function(){
							icon: 'arrow-retweet.png',
							Hfos.getApplication().run({
								id: 'win-accion-estados-socios',
								icon: 'arrow-retweet.png',
								title: 'Acción de Estados',
								width: '700px',
								height: '350px',
								action: 'accion_estados'
							});
						}
					},
					{
						'title': 'Tipo de Asociación con socios',
						'icon': 'my-account.png',
						'click': function(){
							icon: 'my-account.png',
							Hfos.getApplication().run({
								id: 'win-estados-socios-socios',
								icon: 'my-account.png',
								title: "Tipo de Asociación con socios",
								width: '700px',
								height: '350px',
								action: 'tipo_asociacion_socio'
							});
						}
					},
                    /*{
                        'title': 'Categoria X Edad',
                        'icon': 'cake.png',
                        'click': function(){
                            icon: 'cake.png',
                                Hfos.getApplication().run({
                                    id: 'win-categoria-edad-socios',
                                    icon: 'cake.png',
                                    title: "Categoria X Edad",
                                    width: '700px',
                                    height: '470px',
                                    action: 'categoria_edad'
                                });
                        }
                    }*/
				]
			},
			{
				'title': 'Cargos Fijos',
				'icon': 'cartera-2.png',
				'click': function(){
					icon: 'cartera-2.png',
					Hfos.getApplication().run({
						id: 'win-cargos-fijos-socios',
						icon: 'cartera-2.png',
						title: "Cargos Fijos",
						width: '900px',
						height: '550px',
						action: 'cargos_fijos'
					});
				}
			},
			{
				'title': 'Consecutivos',
				'icon': 'contables.png',
				'click': function(){
					icon: 'contables.png',
					Hfos.getApplication().run({
						id: 'win-consecutivos-socios',
						icon: 'cartera-2.png',
						title: "Consecutivos",
						width: '900px',
						height: '550px',
						action: 'consecutivos'
					});
				}
			},
			{
				'title': 'Periodo',
				'icon': 'cartera-2.png',
				'click': function(){
					icon: 'cartera-2.png',
					Hfos.getApplication().run({
						id: 'win-periodo-socios',
						icon: 'cartera-2.png',
						title: "Periodo",
						width: '900px',
						height: '550px',
						action: 'periodo'
					});
				}
			},
			{
				'title': 'Cargos Fijos de Categoría',
				'icon': 'type-user.png',
				'click': function(){
					icon: 'type-user.png',
					Hfos.getApplication().run({
						id: 'win-cargos-fijos-categoria-socios',
						icon: 'type-user.png',
						title: "Cargos Fijos de Categoría",
						width: '700px',
						height: '450px',
						action: 'cargos_fijos_categoria'
					});
				}
			},
			{
				'title': 'Datos del Club',
				'icon': 'home.png',
				'click': function(){
					icon: 'home.png',
					Hfos.getApplication().run({
						id: 'win-datos-club-socios',
						icon: 'home.png',
						title: "Datos del Club",
						width: '800px',
						height: '550px',
						action: 'datos_club'
					});
				}
			},
			{
				'title': 'Configuración',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-settings-socios',
						icon: 'gear.png',
						title: "Configuración",
						width: '700px',
						height: '550px',
						action: 'settings'
					});
				}
			}
		]
	}
]);
