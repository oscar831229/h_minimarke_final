
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
		'title': 'Movimiento',
		'icon': 'document-library.png',
		'options': [
			{
				'title': 'Movimiento Contable',
				'icon': 'document-library.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-movimiento',
						icon: 'document-library.png',
						title: "Movimiento Contable",
						action: "movimiento",
						height: '570px'
					});
				}
			},
			{
				'title': 'Movimiento Niif',
				'icon': 'document-library.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-movimiento-niif',
						icon: 'document-library.png',
						title: "Movimiento Niif",
						action: "movimiento_niif",
						height: '570px'
					});
				}
			},
			{
				'title': 'Consultas de Movimiento',
				'icon': 'publish.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-consultas',
						icon: 'publish.png',
						title: "Consultas de Movimiento",
						action: "consultas",
						width: '730px',
						height: '400px'
					});
				}
			},
			{
				'title': 'Cambio de un Tercero por Otro',
				'icon': 'hire-me.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-cambio-nit',
						icon: 'hire-me.png',
						title: "Cambio de un Tercero por Otro",
						action: "cambio_nit",
						width: '770px',
						height: '470px'
					});
				}
			},
			{
				'title': 'Importar Movimiento',
				'icon': 'transfer.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-incluir',
						icon: 'transfer.png',
						title: "Importar Movimiento Contable",
						action: "incluir",
						width: '700px',
						height: '370px'
					});
				}
			},
			{
				'title': 'Exportar Movimiento',
				'icon': 'server--arrow.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-excluir',
						icon: 'server--arrow.png',
						title: "Exportar Movimiento",
						action: "excluir",
						width: '650px',
						height: '350px'
					});
				}
			},
			/*{
				'title': 'Test',
				'icon': 'bug-2.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-test',
						icon: 'bug-2.png',
						title: "Test",
						action: "test"
					});
				}
			}*/
		]
	},

	{
		'title': 'Informes',
		'icon': 'archives.png',
		'options': [
			{
				'title': 'Comprobación',
				'icon': 'check.png',
				'options': [
					{
						'title': 'Balance Comprobación',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-balance',
								icon: 'issue.png',
								title: "Balance de Comprobación",
								action: "balance",
								width: '730px',
								height: '470px'
							});
						}
					},
					{
						'title': 'Balance Consolidado Anual',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-balance-consolidado',
								icon: 'issue.png',
								title: "Balance Consolidado Anual",
								action: "informe_balance_consolidado",
								width: '530px',
								height: '270px'
							});
						}
					},
					{
						'title': 'Libro Auxiliar',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-libro-auxiliar',
								icon: 'issue.png',
								title: "Libro Auxiliar",
								action: "libro_auxiliar",
								width: '700px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Validar Integridad',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-consecutivos',
								icon: 'issue.png',
								title: "Validar Integridad",
								action: "consecutivos",
								width: '700px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Movimiento por Terceros',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-movimiento-terceros',
								icon: 'issue.png',
								title: "Movimiento de Cuentas por Terceros",
								action: "movimiento_terceros",
								width: '780px',
								height: '480px'
							});
						}
					},
					{
						'title': 'Movimiento por Centro Costo',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-movimiento-centros',
								icon: 'issue.png',
								title: "Movimiento de Cuentas por Centro de Costo",
								action: "movimiento_centros",
								width: '750px',
								height: '450px'
							});
						}
					},
					{
						'title': 'Balance por Centro Costo',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-balance-centros',
								icon: 'issue.png',
								title: "Balance por Centro de Costo",
								action: "balance_centros",
								width: '750px',
								height: '420px'
							});
						}
					},
					{
						'title': 'Comprobante Diario',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-comprobante-diario',
								icon: 'issue.png',
								title: "Comprobante Diario",
								action: "comprobante_diario",
								width: '700px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Listado de Movimiento',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-listado-movimiento',
								icon: 'issue.png',
								title: "Listado de Movimiento",
								action: "listado_movimiento",
								width: '750px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Retención Acumulada',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-retencion',
								icon: 'issue.png',
								title: "Retención Acumulada",
								action: "retencion",
								width: '700px',
								height: '350px'
							});
						}
					},
					{
						'title': 'Listado Retención',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-listado-retencion',
								icon: 'issue.png',
								title: "Listado de Retención",
								action: "listado_retencion",
								width: '750px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Listado Consecutivos',
						'icon': 'issue.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-listado-comprob',
								icon: 'issue.png',
								title: "Listado de Consecutivos",
								action: "listado_comprob",
								width: '750px',
								height: '400px'
							});
						}
					}
				]
			},
			{
				'title': 'Oficiales y Tributarios',
				'icon': 'finance.png',
				'options': [
					{
						'title': 'Balance General Comparativo',
						'icon': 'finance-2.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-balance-general',
								icon: 'finance-2.png',
								title: "Balance General Comparativo",
								action: "balance_general",
								width: '650px',
								height: '350px'
							});
						}
					},
					{
						'title': 'Libro Diario',
						'icon': 'finance-2.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-libro-diario',
								icon: 'finance-2.png',
								title: "Libro Diario",
								action: "libro_diario",
								width: '700px',
								height: '350px'
							});
						}
					},
					{
						'title': 'Libro Mayor y Balance',
						'icon': 'finance-2.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-libro-mayor',
								icon: 'finance-2.png',
								title: "Libro Mayor y Balance",
								action: "libro_mayor",
								width: '700px',
								height: '350px'
							});
						}
					},
					{
						'title': 'Libro Inventario y Balance',
						'icon': 'finance-2.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-libro-terceros',
								icon: 'finance-2.png',
								title: "Libro Inventario y Balance por Terceros",
								action: "libro_terceros",
								width: '750px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Numeración Libros Oficiales',
						'icon': 'finance-2.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-numeracion',
								icon: 'finance-2.png',
								title: "Numeración Libros Oficiales",
								action: "numeracion",
								width: '600px',
								height: '350px'
							});
						}
					}
				]
			},
			{
				'title': 'Certificados',
				'icon': 'special-offer.png',
				'options': [
					{
						'title': 'Certificado de Retención',
						'icon': 'special-offer.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-certificado-retencion',
								icon: 'special-offer.png',
								title: "Certificado de Retención",
								action: "certificado_retencion",
								width: '750px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Certificado de IVA/ICA',
						'icon': 'special-offer.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-certificado-ica',
								icon: 'special-offer.png',
								title: "Certificado de IVA/ICA",
								action: "certificado_ica",
								width: '750px',
								height: '420px'
							});
						}
					}
				]
			},
			{
				'title': 'Financieros',
				'icon': 'invoice.png',
				'options': [
					{
						'title': 'Estado de Perdidas y Ganancias',
						'icon': 'invoice.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-pyg',
								icon: 'invoice.png',
								title: "Estado de Perdidas y Ganancias",
								action: "pyg",
								width: '550px',
								height: '350px'
							});
						}
					},
					{
						'title': 'Caratulas del Balance',
						'icon': 'invoice.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-caratulas',
								icon: 'invoice.png',
								title: "Caratulas del Balance",
								action: "caratulas",
								width: '550px',
								height: '350px'
							});
						}
					}
				]
			},
			{
				'title': 'Cartera',
				'icon': 'cartera.png',
				'options': [
					{
						'title': 'Movimiento de Documentos',
						'icon': 'cartera-2.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-movimiento-documentos',
								icon: 'cartera-2.png',
								title: "Movimiento Documentos",
								action: "movimiento_documentos",
								width: '750px',
								height: '470px'
							});
						}
					},
					{
						'title': 'Cartera por Edades',
						'icon': 'cartera.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-cartera-edades',
								icon: 'cartera.png',
								title: "Cartera por Edades",
								action: "cartera_edades",
								width: '750px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Corregir Cartera',
						'icon': 'cartera.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-corregir-cartera',
								icon: 'cartera.png',
								title: "Corregir Cartera",
								action: "corregir_cartera",
								width: '750px',
								height: '270px'
							});
						}
					},
				]
			}
		]
	},
	{
		'title': 'Contables',
		'icon': 'contables.png',
		'options': [
			{
				'title': 'Activos Fijos',
				'icon': 'sofa.png',
				'options': [
					{
						'title': 'Activos Fijos',
						'icon': 'sofa.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-activos',
								icon: 'sofa.png',
								title: "Mantenimiento de Activos Fijos",
								action: "activos"
							});
						}
					},
					{
						'title': 'Grupos',
						'icon': 'product-193.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-grupos',
								icon: 'product-193.png',
								title: "Grupos de Activos Fijos",
								action: "grupos"
							});
						}
					},
					{
						'title': 'Tipos',
						'icon': 'chair--arrow.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-tipos-activos',
								icon: 'chair--arrow.png',
								title: "Tipos de Activos Fijos",
								action: "tipos_activos"
							});
						}
					},
					{
						'title': 'Ubicaciones',
						'icon': 'building.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-ubicacion',
								icon: 'building.png',
								title: "Ubicaciones de Activos Fijos",
								action: "ubicacion"
							});
						}
					},
					{
						'title': 'Depreciación Mensual',
						'icon': 'deprec.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-depreciacion',
								icon: 'deprec.png',
								title: "Depreciación de Activos Fijos",
								action: "depreciacion",
								width: '650px',
								height: '300px'
							});
						}
					},
					{
						'title': 'Anular Depreciación Mensual',
						'icon': 'deprec.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-anular-depreciacion',
								icon: 'deprec.png',
								title: "Anular Depreciación Mensual",
								action: "anular_depreciacion",
								width: '650px',
								height: '300px'
							});
						}
					},
					{
						'title': 'Traslados',
						'icon': 'transfer.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-traslado-activos',
								icon: 'transfer.png',
								title: "Traslado de Activos Fijos",
								action: "traslado_activos",
								width: '700px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Reporte de Novedades',
						'icon': 'order-149.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-novedad-activos',
								icon: 'order-149.png',
								title: "Reporte de Novedades",
								action: "novedad_activos",
								width: '700px',
								height: '350px'
							});
						}
					},
					{
						'title': 'Consultar Depreciación',
						'icon': 'order-149.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-consulta-depreciacion',
								icon: 'order-149.png',
								title: "Consultar Depreciación",
								action: "consulta_depreciacion",
								width: '700px',
								height: '350px'
							});
						}
					}

				]
			},
			{
				'title': 'Acreedores Varios',
				'icon': 'order-2.png',
				'options': [
					{
						'title': 'Ordenes de Servicio',
						'icon': 'order-2.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-ordenes-servicio',
								icon: 'order-2.png',
								title: "Ordenes de Servicio",
								action: "ordenes",
								height: '570px'
							});
						}
					},
					{
						'title': 'Items de Servicio',
						'icon': 'document-list.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-refe',
								icon: 'document-list.png',
								title: "Items de Servicio",
								action: "refe"
							});
						}
					},
					{
						'title': 'Líneas de Servicio',
						'icon': 'ui-paginator.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-lineaser',
								icon: 'ui-paginator.png',
								title: "Líneas de Servicio",
								action: "lineaser"
							});
						}
					}
				]
			},
			{
				'title': 'Presupuesto',
				'icon': 'statistics.png',
				'options': [
					{
						'title': 'Administrar',
						'icon': 'statistics.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-presupuesto',
								icon: 'statistics.png',
								title: "Administrar Presupuesto",
								action: "presupuesto"
							});
						}
					},
					/*{
						'title': 'Ejecución',
						'icon': 'advertising.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-ejecucion',
								icon: 'advertising.png',
								title: "Ejecución del Presupuesto",
								action: "ejecucion"
							});
						}
					},*/
					{
						'title': 'Reporte de Ejecución',
						'icon': 'statistics-2.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-ejecucion-pres',
								icon: 'statistics-2.png',
								title: "Reporte de Ejecución",
								action: "ejecucion_pres",
								width: '750px',
								height: '400px'
							});
						}
					}
				]
			},
			{
				'title': 'Tesorería',
				'icon': 'bank.png',
				'options': [
					{
						'title': 'Recibo de Caja',
						'icon': 'credit-card.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-recibo-caja',
								icon: 'credit-card.png',
								title: "Recibo de Caja",
								action: "recibo_caja"
							});
						}
					},
					{
						'title': 'Cheques',
						'icon': 'cheque.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-cheque',
								icon: 'cheque.png',
								title: "Cheques",
								action: "cheque"
							});
						}
					},
					/*{
						'title': 'Transferencias',
						'icon': 'communication.png'
					},*/
					{
						'title': 'Chequeras',
						'icon': 'cheque.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-chequeras',
								icon: 'cheque.png',
								title: "Chequeras",
								action: "chequeras",
								width: '700px',
								height: '450px'
							});
						}
					},
					{
						'title': 'Cuentas Bancarias',
						'icon': 'payment-card.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-cuentas-bancos',
								icon: 'payment-card.png',
								title: "Cuentas Bancarias",
								action: "cuentas_bancos",
								width: '700px',
								height: '450px'
							});
						}
					},
					{
						'title': 'Bancos',
						'icon': 'bank.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-banco',
								icon: 'bank.png',
								title: "Bancos",
								action: "banco",
								width: '700px',
								height: '450px'
							});
						}
					},
					{
						'title': 'Formatos de Cheques',
						'icon': 'formatos.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-formato-cheque',
								icon: 'formatos.png',
								title: "Formatos de Cheques",
								action: "formato_cheque",
								width: '740px',
								height: '470px'
							});
						}
					}
				]
			},
			{
				'title': 'Medios Magnéticos',
				'icon': 'excel-2.png',
				'options': [
					{
						'title': 'Generar Datos',
						'icon': 'report-excel.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-medios',
								icon: 'report-excel.png',
								title: "Generar Datos Medios Magnéticos",
								action: "medios",
								width: '700px',
								height: '400px'
							});
						}
					},
					{
						'title': 'Campos y Rangos Cuentas',
						'icon': 'report-excel.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-campos-medios',
								icon: 'report-excel.png',
								title: "Campos y Rangos de Cuentas",
								action: "campos_medios"
							});
						}
					},
					{
						'title': 'Códigos de Conceptos',
						'icon': 'report-excel.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-codigos-medios',
								icon: 'report-excel.png',
								title: "Códigos de Conceptos",
								action: "codigos_medios"
							});
						}
					},
					{
						'title': 'Formatos',
						'icon': 'report-excel.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-formatos-medios',
								icon: 'report-excel.png',
								title: "Formatos de Medios Magnéticos",
								action: "formatos_medios"
							});
						}
					}
				]
			},
			{
				'title': 'Activos Diferidos',
				'icon': 'home.png',
				'options': [
					{
						'title': 'Activos Diferidos',
						'icon': 'home.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-activos-diferidos',
								icon: 'home.png',
								title: "Mantenimiento de Activos Diferidos",
								action: "diferidos"
							});
						}
					},
					{
						'title': 'Grupos',
						'icon': 'product-193.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-grupos',
								icon: 'product-193.png',
								title: "Grupos de Activos Diferidos",
								action: "grupos_diferidos"
							});
						}
					},
					{
						'title': 'Causación Mensual',
						'icon': 'deprec.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-amortizacion',
								icon: 'deprec.png',
								title: "Causación de Activos Diferidos",
								action: "amortizacion",
								width: '650px',
								height: '300px'
							});
						}
					},
					{
						'title': 'Consultar Causación',
						'icon': 'order-149.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-consulta-causacion',
								icon: 'order-149.png',
								title: "Consultar Causación",
								action: "consulta_causacion",
								width: '700px',
								height: '350px'
							});
						}
					}
				]
			},
			{
				'title': 'Interfaces',
				'icon': 'limited-edition.png',
				'options': [
					{
						'title': 'SIIGO Facturación',
						'icon': 'limited-edition.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-interface-siigo',
								icon: 'limited-edition.png',
								title: "Interface SIIGO Facturación",
								action: "interface_siigo",
								width: '550px',
								height: '300px'
							});
						},
					},
					{
						'title': 'SIIGO Proveedores',
						'icon': 'limited-edition.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-interface-siigo',
								icon: 'limited-edition.png',
								title: "Interface SIIGO Proveedores",
								action: "interface_siigo_pro",
								width: '550px',
								height: '300px'
							});
						},
					}
				]
			},
			{
				'title': 'Cierres',
				'icon': 'limited-edition.png',
				'options': [
					{
						'title': 'Cierre Contable',
						'icon': 'limited-edition.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-cierre-contable',
								icon: 'limited-edition.png',
								title: "Cierre Contable",
								action: "cierre_contable",
								width: '550px',
								height: '350px'
							});
						},
					},
					{
						'title': 'Reabrir Cierre Contable',
						'icon': 'refresh.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-reabrir-mes',
								icon: 'refresh.png',
								title: "Reabrir Cierre Contable",
								action: "reabrir_mes",
								width: '550px',
								height: '350px'
							});
						}
					},
					{
						'title': 'Cerrar Cuentas Retención/IVA',
						'icon': 'switch.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-cierre-cuentas',
								icon: 'switch.png',
								title: "Cerrar Cuentas Retención/IVA",
								action: "cierre_cuentas",
								width: '550px',
								height: '350px'
							});
						},
					},
					{
						'title': 'Reabrir Cuentas Retención/IVA',
						'icon': 'switch.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-reabrir-cuentas',
								icon: 'switch.png',
								title: "Reabrir Cuentas Retención/IVA",
								action: "reabrir_cuentas",
								width: '550px',
								height: '350px'
							});
						},
					},
					{
						'title': 'Comprobante de Cierre Anual',
						'icon': 'special-offer.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-comprobante-cierre',
								icon: 'special-offer.png',
								title: "Comprobante de Cierre Anual",
								action: "comprobante_cierre",
								width: '650px',
								height: '350px'
							});
						}
					},
					{
						'title': 'Cierre Anual',
						'icon': 'limited-edition.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-cierre-anual',
								icon: 'limited-edition.png',
								title: "Cierre Anual",
								action: "cierre_anual",
								width: '650px',
								height: '350px'
							});
						}
					},
					{
						'title': 'Reabrir Año',
						'icon': 'refresh.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-reabrir-ano',
								icon: 'refresh.png',
								title: "Reabrir Año",
								action: "reabrir_ano",
								width: '550px',
								height: '350px'
							});
						}
					}
				]
			}
		]
	},
	{
		'title': 'Básicas',
		'icon': 'wrench-screwdriver.png',
		'options': [
			{
				'title': 'NIIF',
				'icon': 'product.png',
				'options': [
					{
						'title': 'Plan de Cuentas NIIF',
						'icon': 'product.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-cuentas-niif-contab',
								icon: 'product.png',
								title: "Plan de Cuentas NIIF",
								action: "niif",
								width: '800px',
								height: '520px'
							});
						}
					},
					{
						'title': 'NIC',
						'icon': 'box-label.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-nic-contab',
								icon: 'box-label.png',
								title: "NIC",
								action: "nic",
								width: '700px',
								height: '420px'
							});
						}
					}
				]
			},
			{
				'title': 'Cuentas Contables',
				'icon': 'featured.png',
				'options': [
					{
						'title': 'Plan de Cuentas',
						'icon': 'featured.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-cuentas',
								icon: 'featured.png',
								title: "Plan de Cuentas",
								action: "cuentas",
							});
						}
					},
					{
						'title': 'Contabilización por Regímen',
						'icon': 'notebook-share.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-cuentas-regimen',
								icon: 'notebook-share.png',
								title: "Contabilización por Regímen",
								width: '900px',
								height: '520px',
								action: 'regimen_cuentas'
							});
						}
					},
					{
						'title': 'Cuentas de ICA',
						'icon': 'old-versions.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-ica',
								icon: 'old-versions.png',
								title: "Cuentas de ICA",
								width: '800px',
								height: '450px',
								action: 'ica'
							});
						}
					},
					{
						'title': 'Cuentas CREE',
						'icon': 'old-versions.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-cuentas-cree',
								icon: 'old-versions.png',
								title: "Cuentas CREE",
								width: '800px',
								height: '450px',
								action: 'cuentas_cree'
							});
						}
					},
					/*{
						'title': 'Formatos de Comprobantes'
					},*/
					{
						'title': 'Cuentas de Cierre Anual',
						'icon': 'old-versions.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-comcier',
								icon: 'old-versions.png',
								title: "Cuentas de Cierre Anual",
								action: 'comcier'
							});
						}
					},
				]
			},
			{
				'title': 'Terceros',
				'icon': 'business-contact.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-terceros',
						icon: 'business-contact.png',
						title: "Terceros",
						action: 'terceros'
					});
				}
			},
			{
				'title': 'Tipos de Documentos',
				'icon': 'my-account.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-documentos',
						icon: 'my-account.png',
						title: "Tipos de Documentos de Identidad",
						action: 'tipodoc'
					});
				}
			},
			{
				'title': 'Tipos de Comprobantes',
				'icon': 'category.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-comprob',
						icon: 'category.png',
						title: "Tipos de Comprobantes",
						action: 'comprobantes'
					});
				}
			},
			{
				'title': 'Tipos de Diarios',
				'icon': 'cv.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-diarios',
						icon: 'cv.png',
						title: "Diarios",
						width: '800px',
						height: '450px',
						action: 'diarios'
					});
				}
			},
			{
				'title': 'Documentos Contables',
				'icon': 'archives.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-documentos-contables',
						icon: 'archives.png',
						title: "Documentos Contables",
						action: 'documentos'
					});
				}
			},
			{
				'title': 'Formas de Pago',
				'icon': 'credit-card.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-forma-apgo',
						icon: 'credit-card.png',
						title: "Formas de Pago",
						width: '800px',
						height: '450px',
						action: 'formapago'
					});
				}
			},
			{
				'title': 'Centros de Costo',
				'icon': 'centros.png',
				'click': function(){
					icon: 'centros.png',
					Hfos.getApplication().run({
						id: 'win-centros',
						icon: 'centros.png',
						title: "Centros de Costo",
						action: 'centros'
					});
				}
			},
			{
				'title': 'Servidores Consolidado',
				'icon': 'servers.png',
				'click': function(){
					icon: 'servers.png',
					Hfos.getApplication().run({
						id: 'win-consolidados',
						icon: 'servers.png',
						title: "Servidores de Consolidado",
						action: 'consolidados'
					});
				}
			},
			{
				'title': 'Configuración',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-settings-contab',
						icon: 'gear.png',
						title: "Configuración",
						width: '700px',
						height: '650px',
						action: 'settings'
					});
				}
			}
		]
	}
]);
