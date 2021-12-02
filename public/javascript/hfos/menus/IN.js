
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
		'title': 'Entradas',
		'icon': 'entradas.png',
		'options': [
			{
				'title': 'Órdenes de Compra',
				'icon': 'ordenes.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-ordenes',
						icon: 'ordenes.png',
						title: "Órdenes de Compra",
						width: '900px',
						height: '520px',
						action: 'ordenes'
					});
				}
			},
			{
				'title': 'Entradas al Almacén',
				'icon': 'entradas.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-entradas',
						icon: 'entradas.png',
						title: "Entradas al Almacén",
						width: '970px',
						height: '520px',
						action: 'entradas'
					});
				}
			},
			{
				'title': 'Ajuste a Inventarios',
				'icon': 'ajustes.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-ajustes',
						icon: 'ajustes.png',
						title: "Ajustes a Inventarios",
						width: '900px',
						height: '520px',
						action: 'ajustes'
					});
				}
			},
			{
				'title': 'Traslado entre almacenes',
				'icon': 'arrow-out.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-traslados',
						icon: 'arrow-out.png',
						title: "Traslados",
						width: '900px',
						height: '520px',
						action: 'traslados'
					});
				}
			},
			{
				'title': 'Conteo Físico',
				'icon': 'clipboard-task.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-fisico',
						icon: 'clipboard-task.png',
						title: "Conteo Físico",
						width: '900px',
						height: '520px',
						action: 'fisico'
					});
				}
			}
		]
	},
	{
		'title': 'Salidas',
		'icon': 'salidas.png',
		'options': [
			{
				'title': 'Salidas de Almacén',
				'icon': 'salidas.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-salidas',
						icon: 'salidas.png',
						title: "Salidas del Almacén",
						width: '900px',
						height: '520px',
						action: 'salidas'
					});
				}
			},
			{
				'title': 'Salidas por Buffet',
				'icon': 'salidas.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-salidas-buffet',
						icon: 'salidas.png',
						title: "Salidas por Buffet",
						width: '900px',
						height: '520px',
						action: 'salidas_buffet'
					});
				}
			},
			{
				'title': 'Pedidos al Almacén',
				'icon': 'order-149.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-pedido',
						icon: 'order-149.png',
						title: "Pedidos al Almacén",
						width: '900px',
						height: '520px',
						action: 'pedidos'
					});
				}
			},
			{
				'title': 'Transformaciones',
				'icon': 'sitemap.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-transformaciones',
						icon: 'sitemap.png',
						title: "Transformaciones",
						width: '900px',
						height: '520px',
						action: 'transformaciones'
					});
				}
			}
		]
	},
	{
		'title': 'Informes',
		'icon': 'documents-text.png',
		'options': [
			{
				'title': 'Kardex de Inventarios',
				'icon': 'order-149.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-kardex',
						icon: 'order-149.png',
						title: "Kardex de Inventario",
						width: '700px',
						height: '350px',
						action: "kardex"
					});
				}
			},
			{
				'title': 'Saldos y Stocks',
				'icon': 'archives.png',
				'options': [
					{
						'title': 'Saldos por Almacén',
						'icon': 'archives.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-saldos-almacen',
								icon: 'archives.png',
								title: "Saldos por Almacén",
								width: '730px',
								height: '400px',
								action: 'saldos_almacen'
							});
						}
					},
					{
						'title': 'Saldos por Almacén Consolidado',
						'icon': 'archives.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-saldos-almacen-consolidado',
								icon: 'archives.png',
								title: "Saldos por Almacén Consolidado",
								width: '730px',
								height: '350px',
								action: 'saldos_almacen_consolidado'
							});
						}
					},
					{
						'title': 'Stocks Altos y Bajos',
						'icon': 'archives.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-stocks',
								icon: 'archives.png',
								title: "Stocks Altos y Bajos",
								width: '700px',
								height: '350px',
								action: 'stocks'
							});
						}
					}
				]
			},
			{
				'title': 'Costo',
				'icon': 'archives.png',
				'options': [
					{
						'title': 'Comportamiento del Costo',
						'icon': 'ad.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-comportamiento',
								icon: 'ad.png',
								title: "Comportamiento del Costo",
								width: '820px',
								height: '470px',
								action: 'comportamiento'
							});
						}
					},
				]
			},
			{
				'title': 'Movimientos',
				'icon': 'archives.png',
				'options': [
					{
						'title': 'Movimientos de Inventarios',
						'icon': 'archives.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-movimientos-inve',
								icon: 'archives.png',
								title: "Movimientos de Inventarios",
								width: '700px',
								height: '470px',
								action: 'movimientos'
							});
						}
					},
					{
						'title': 'Consecutivos de Inventarios',
						'icon': 'archives.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-consecutivos',
								icon: 'archives.png',
								title: "Consecutivos de Inventarios",
								width: '600px',
								height: '300px',
								action: 'consecutivos'
							});
						}
					},
					{
						'title': 'Movimiento Almacén por Grupo',
						'icon': 'archives.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-trasunto',
								icon: 'archives.png',
								title: "Movimiento de Almacén por Grupo",
								width: '700px',
								height: '350px',
								action: 'trasunto'
							});
						}
					},
					{
						'title': 'Consumos/Pedidos por Centro de Costo',
						'icon': 'archives.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-consumos',
								icon: 'archives.png',
								title: "Consumos/Pedidos por Centro de Costo",
								width: '800px',
								height: '500px',
								action: 'consumos'
							});
						}
					},
					{
						'title': 'Impresión Continua de Movimientos',
						'icon': 'archives.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-impresion',
								icon: 'archives.png',
								title: "Impresión Continua de Movimientos",
								width: '650px',
								height: '420px',
								action: 'impresion'
							});
						}
					}
				]
			},
			{
				'title': 'Retefuentes Productos Hortifrutículas',
				'icon': 'archives.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-horti',
						icon: 'archives.png',
						title: "Retefuentes Productos Hortifrutículas",
						width: '700px',
						height: '350px',
						action: 'horti'
					});
				}
			},
			{
				'title': 'Reporte de movimientos por referencia',
				'icon': 'archives.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-movinventario',
						icon: 'archives.png',
						title: "Reporte de movimientos por referencia",
						width: '700px',
						height: '470px',
						action: 'movinventario'
					});
				}
			},
			{
				'title': 'Reporte analisis de compras',
				'icon': 'archives.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-analisiscompras',
						icon: 'archives.png',
						title: "Reporte de analisis de compras",
						width: '700px',
						height: '470px',
						action: 'analisiscompras'
					});
				}
			}
		]
	},
	{
		'title': 'Cierres',
		'icon': 'limited-edition.png',
		'options': [
			{
				'title': 'Cierre de Inventarios',
				'icon': 'limited-edition.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-cerrar',
						icon: 'limited-edition.png',
						title: "Cerrar el Periodo",
						width: '650px',
						height: '350px',
						action: 'cerrar'
					});
				}
			},
			{
				'title': 'Reabrir un Mes Cerrado',
				'icon': 'refresh.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-reabrir',
						icon: 'refresh.png',
						title: "Reabrir Mes Cerrado",
						width: '650px',
						height: '350px',
						action: 'reabrir'
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
				'title': 'Referencias',
				'icon': 'beans.png',
				'options': [
					{
						'title': 'Referencias',
						'icon': 'beans.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-referencias',
								icon: 'beans.png',
								title: "Referencias",
								width: '900px',
								height: '540px',
								action: 'referencias'
							});
						}
					},
					{
						'title': 'Líneas de Referencias',
						'icon': 'box-label.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-lineas',
								icon: 'box-label.png',
								title: "Líneas de Referencias",
								width: '900px',
								height: '520px',
								action: 'lineas'
							});
						}
					},
					{
						'title': 'Tipos de Referencias',
						'icon': 'price-tag.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-producto',
								icon: 'price-tag.png',
								title: "Tipos de Referencias",
								width: '900px',
								height: '520px',
								action: 'producto'
							});
						}
					},
					{
						'title': 'Listado de Referencias',
						'icon': 'archives.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-listado-referencias',
								icon: 'archives.png',
								title: "Listado de Referencias",
								width: '700px',
								height: '350px',
								action: 'listado_referencias'
							});
						}
					}
				]
			},
			{
				'title': 'Almacenes',
				'icon': 'almacen.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-almacenes',
						icon: 'almacen.png',
						title: "Almacenes",
						width: '900px',
						height: '520px',
						action: 'almacenes'
					});
				}
			},
			/*{
				'title': 'Centros de Costo',
				'icon': 'centros.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-centros',
						icon: 'centros.png',
						title: "Centros de Costo",
						width: '900px',
						height: '520px',
						action: 'centros'
					});
				}
			},*/
			{
				'title': 'Formas de Pago',
				'icon': 'credit-card.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-formapago',
						icon: 'credit-card.png',
						title: "Formas de Pago",
						width: '900px',
						height: '520px',
						action: 'formapago'
					});
				}
			},
			{
				'title': 'Proveedores',
				'icon': 'business-contact.png',
				'options': [
					{
						'title': 'Terceros',
						'icon': 'business-contact.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-terceros',
								icon: 'business-contact.png',
								title: "Terceros",
								width: '900px',
								height: '520px',
								action: 'terceros'
							});
						}
					},
					{
						'title': 'Matriz de Proveedores',
						'icon': 'layout-4.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-matriz-proveedores',
								icon: 'layout-4.png',
								title: "Matriz de Proveedores",
								width: '700px',
								height: '520px',
								action: 'matriz_proveedores'
							});
						}
					},
					{
						'title': 'Criterios de Calificación',
						'icon': 'property.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-criterios',
								icon: 'property.png',
								title: "Criterios de Calificación",
								width: '900px',
								height: '520px',
								action: 'criterios'
							});
						}
					},
					{
						'title': 'Listado Proveedores por Referencia',
						'icon': 'archives.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-listado-proveedores',
								icon: 'archives.png',
								title: "Listado de Proveedores",
								width: '750px',
								height: '400px',
								action: 'listado_proveedores'
							});
						}
					}
				]
			},
			{
				'title': 'Unidades',
				'icon': 'unidades.png',
				'options': [
					{
						'title': 'Unidades',
						'icon': 'unidades.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-unidad',
								icon: 'unidades.png',
								title: "Unidades de Medida",
								width: '900px',
								height: '520px',
								action: 'unidades'
							});
						}
					},
					{
						'title': 'Magnitudes',
						'icon': 'ruler-triangle.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-magnitud',
								icon: 'ruler-triangle.png',
								title: "Magnitudes",
								width: '900px',
								height: '520px',
								action: 'magnitudes'
							});
						}
					},
					{
						'title': 'Conversión de Unidades',
						'icon': 'calculator.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-conversion',
								icon: 'calculator.png',
								title: "Conversión de Unidades",
								width: '900px',
								height: '520px',
								action: 'conversion'
							});
						}
					}
				]
			},
			{
				'title': 'Cuentas por Regímen',
				'icon': 'featured.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-regimen-cuentas',
						icon: 'featured.png',
						title: "Cuentas Regimen Tributario",
						width: '750px',
						height: '450px',
						action: 'regimen_cuentas'
					});
				}
			},
			{
				'title': 'Retención de Compras',
				'icon': 'advertising.png',
				'click': function(){
					icon: 'advertising.png',
					Hfos.getApplication().run({
						id: 'win-retecompras',
						icon: 'advertising.png',
						title: "Retención de Compras",
						width: '750px',
						height: '450px',
						action: 'retecompras'
					});
				}
			},
			{
				'title': 'Consumos Internos',
				'icon': 'featured.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-consumos-internos',
						icon: 'featured.png',
						title: "Consumos Internos",
						width: '750px',
						height: '450px',
						action: 'consumos_internos'
					});
				}
			},
			{
				'title': 'Configuración',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-settings-inve',
						icon: 'gear.png',
						title: "Configuración",
						width: '700px',
						height: '450px',
						action: 'settings'
					});
				}
			}
		]
	}
]);
