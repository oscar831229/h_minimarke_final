
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
		'title': 'Envios',
		'icon': 'users.png',
		'options': [
			{
				'title': 'Recibos',
				'icon': 'refresh.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-recibos-postales',
						icon: 'refresh.png',
						title: "Recibos",
						action: "recibos",
						width: '950px',
						height: '650px'
					});
				}
			}
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
						id: 'win-cierre-postales',
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
						id: 'win-reabrir-postales',
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
				'title': 'Destinos',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-destinos-postales',
						icon: 'gear.png',
						title: "Destinos",
						width: '700px',
						height: '450px',
						action: 'destinos'
					});
				}
			},
			{
				'title': 'Terceros',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-terceros-postales',
						icon: 'gear.png',
						title: "Terceros",
						width: '700px',
						height: '450px',
						action: 'terceros'
					});
				}
			},
			{
				'title': 'Sucursales',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-sucursales-postales',
						icon: 'gear.png',
						title: "Sucursales",
						width: '700px',
						height: '400px',
						action: 'sucursales'
					});
				}
			},
			{
				'title': 'Usuarios de Sucursales',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-usuarios-sucursales-postales',
						icon: 'gear.png',
						title: "Usuarios de Sucursales",
						width: '700px',
						height: '400px',
						action: 'usuarios_sucursales'
					});
				}
			},			
			{
				'title': 'Configuración',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-settings-postales',
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
