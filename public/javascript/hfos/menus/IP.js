
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
		'title': 'Historia Clinica',
		'icon': 'users.png',
		'options': [
			
		]
	},
	{
		'title': 'Facturación',
		'icon': 'finance-2.png',
		'options': [
			
		],



	},
	{
		'title': 'Informes',
		'icon': 'finance-2.png',
		'options': [
			
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
				'title': 'Configuración',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-settings-socios',
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
