
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
		'title': 'Carta',
		'icon': 'cake.png',
		'options': [
			{
				'title': 'Menús',
				'icon': 'cake.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-menus',
						icon: 'cake.png',
						title: "Menús",
						action: "menus",
						height: '570px'
					});
				}
			}
		]
	},
	{
		'title': 'Costo',
		'icon': 'cake.png',
		'options': [
			{
				'title': 'Menús',
				'icon': 'cake.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-menus',
						icon: 'cake.png',
						title: "Menús",
						action: "menus",
						height: '570px'
					});
				}
			}
		]
	},
	{
		'title': 'Receta',
		'icon': 'cake.png',
		'options': [
			{
				'title': 'Menús',
				'icon': 'cake.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-menus',
						icon: 'cake.png',
						title: "Menús",
						action: "menus",
						height: '570px'
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
				'title': 'Empleados',
				'icon': 'user-business.png',
				'click': function(){
					Hfos.getApplication().run({
						'id': 'win-empleados',
						'icon': 'user-business.png',
						'title': "Maestro de Empleados",
						'action': "empleados",
					});
				}
			},
			{
				'title': 'Cargos de Empleados',
				'icon': 'user-business.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-cargos',
						title: "Cargos de Empleados",
						action: "cargos",
					});
				}
			},
			{
				'title': 'Fondos',
				'icon': 'user-business.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-fondos',
						title: "Fondos",
						action: 'fondos'
					});
				}
			},
			{
				'title': 'Conceptos de Pagos y Descuentos',
				'icon': 'user-business.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-conceptos',
						title: "Conceptos de Pagos y Descuentos",
						action: 'conceptos'
					});
				}
			},
			{
				'title': 'Valores Retención en la Fuente',
				'icon': 'user-business.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-retencion',
						title: "Valores Retención en la Fuente",
						action: 'retencion'
					});
				}
			},
			{
				'title': 'Cuentas por Centro de Costo',
				'icon': 'user-business.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-concentro',
						title: "Cuentas por Centro de Costo",
						action: 'concentro'
					});
				}
			},
			{
				'title': 'Ubicación de Empleados',
				'icon': 'user-business.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-ubicacione',
						title: "Ubicación de Empleados",
						action: 'ubicacion'
					});
				}
			}
		]
	}
]);
