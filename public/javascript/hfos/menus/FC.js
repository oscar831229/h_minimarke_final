
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
		'title': 'Facturas',
		'icon': 'billing.png',
		'options': [
			{
				'title': 'Generar Facturas',
				'icon': 'invoice.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-facturas',
						icon: 'invoice.png',
						title: "Generar Facturas",
						action: "facturas",
						width: '950px',
						height: '570px'
					});
				}
			},
			{
				'title': 'Reimprimir Facturas',
				'icon': 'invoice.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-reimprimir',
						icon: 'invoice.png',
						title: "Reimprimir Facturas",
						action: "reimprimir",
						width: '300px',
						height: '210px'
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
				'title': 'Consecutivos',
				'icon': 'user-business.png',
				'click': function(){
					Hfos.getApplication().run({
						'id': 'win-consecutivos-facturador',
						'icon': 'user-business.png',
						'title': "Consecutivos de Facturación",
						'action': "consecutivos",
					});
				}
			},
			{
				'title': 'Lista de Precios',
				'icon': 'credit-card.png',
				'click': function(){
					Hfos.getApplication().run({
						'id': 'win-lista-precios-invoicer',
						'icon': 'credit-card.png',
						'title': "Lista de Precios",
						'action': "lista_precios",
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
						height: '350px',
						action: 'settings'
					});
				}
			}
		]
	}
]);
