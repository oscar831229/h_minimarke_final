
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
		'title': 'Usuarios',
		'icon': 'user.png',
		'options': [
			{
				'title': 'Usuarios',
				'icon': 'user.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-usuarios',
						icon: 'user.png',
						title: "Usuarios del Sistema",
						action: "usuarios"
					});
				}
			},
			{
				'title': 'Sucursales',
				'icon': 'building.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-sucursal',
						icon: 'building.png',
						title: "Sucursales",
						action: "sucursal"
					});
				}
			},
			{
				'title': 'Perfiles',
				'icon': 'users.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-perfiles',
						icon: 'users.png',
						title: "Perfiles",
						action: "perfiles"
					});
				}
			},
			{
				'title': 'Perfiles de Usuarios',
				'icon': 'users.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-perfiles-usuarios',
						icon: 'users.png',
						title: "Perfiles de Usuarios",
						action: "perfiles_usuarios"
					});
				}
			},
			{
				'title': 'Permisos de Perfiles',
				'icon': 'key.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-permisos-perfiles',
						icon: 'key.png',
						title: "Permisos de Perfiles",
						action: "permisos_perfiles"
					});
				}
			}
		]
	},
	{
		'title': 'Controles',
		'icon': 'key.png',
		'options': [
			{
				'title': 'Permisos de Comprobantes',
				'icon': 'key.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-permisos-comprob',
						icon: 'featured.png',
						title: "Permisos de Comprobantes",
						action: "permisos_comprob",
					});
				}
			},
			/*{
				'title': 'Permisos de Usuarios en Centros',
				'icon': 'key.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-permisos-centros',
						icon: 'featured.png',
						title: "Permisos de Usuarios en Centros",
						action: "permisos_centros",
					});
				}
			}*/
		]
	}


]);