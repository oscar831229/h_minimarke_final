
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
		'icon': 'wrench-screwdriver.png',
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
		'icon': 'wrench-screwdriver.png',
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
		'icon': 'wrench-screwdriver.png',
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
				'title': 'Departamentos',
				'icon': 'user-business.png',
				'click': function(){
					Hfos.getApplication().run({
						'id': 'win-departamentos',
						'icon': 'user-business.png',
						'title': "Departamentos del Hotel",
						'action': "departamentos",
					});
				}
			}
		]
	}
]);
