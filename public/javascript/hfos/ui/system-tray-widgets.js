
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

var HfosSystemTrayWidgets = [
	{
		'title': 'Cambiar Aplicación',
		'icon': 'change-apps.png',
		'options': [
			{
				'title': 'Contabilidad',
				'icon': 'document-library.png',
				'click': function(){
					Hfos.bootApp('Contabilidad', 'CO', 'contab', 'document-library.png', true)
				}
			},
			{
				'title': 'Inventarios',
				'icon': 'shipping.png',
				'click': function(){
					Hfos.bootApp('Inventarios', 'IN', 'inve', 'shipping.png', true)
				}
			},
			/*{
				'title': 'Nomina',
				'icon': 'suppliers.png',
				'click': function(){
					Hfos.bootApp('Nomina', 'NO', 'nomina', 'suppliers.png', true)
				}
			},*/
			{
				'title': 'Facturador',
				'icon': 'invoice.png',
				'click': function(){
					Hfos.bootApp('Facturador', 'FC', 'invoicer', 'invoice.png', true)
				}
			}
			,
			{
				'title': 'Tiempo Compartido',
				'icon': 'suppliers.png',
				'click': function(){
					Hfos.bootApp('Tiempo Compartido', 'TC', 'tpc', 'suppliers.png', true)
				}
			},
			{
				'title': 'Socios',
				'icon': 'users.png',
				'click': function(){
					Hfos.bootApp('Socios', 'SO', 'socios', 'users.png', true)
				}
			},
			/*{
				'title': 'Consecutivos',
				'icon': 'building.png',
				'click': function(){
					Hfos.bootApp('Consecutivos', 'CN', 'sequences', 'building.png', true)
				}
			},*/
			{
				'title': 'Administración',
				'icon': 'administrative-docs.png',
				'click': function(){
					Hfos.bootApp('Administración', 'IM', 'identity', 'administrative-docs.png', true)
				}
			}
		],
		'initialize': function(systemTray, widgetOptions, widgetElement){
			var toolbar = systemTray.getWorkspace().getToolbar();
			var subMenu = new HfosSubmenu(toolbar, widgetOptions, widgetElement);
			widgetElement.observe('mouseover', subMenu.show.bind(subMenu));
			toolbar.addSubmenu(subMenu);
		}
	},
	/*{
		'title': 'Buscar',
		'icon': 'search-icon.png',
		'initialize': function(systemTray, widgetOptions, widgetElement){

		}
	},*/
	{
		'title': 'Usuario',
		'icon': 'user-icon.png',
		'options': [
			{
				'title': 'Datos de la Cuenta',
				'icon': 'user.png',
				'click': function(){
					Hfos.showAccountData();
				}
			},
			{
				'title': 'Consola de Soporte',
				'icon': 'terminal.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-console',
						title: "Consola de Soporte",
						icon: 'terminal.png',
						height: 450,
						width: 750,
						externAction: "identity/console/index"
					});
				}
			},
			{
				'title': 'Ayuda',
				'icon': 'consulting.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-magenta',
						title: "Soporte Técnico",
						icon: 'key.png',
						height: 270,
						width: 500,
						externAction: "identity/magenta/index"
					});
				}
			},
			{
				'title': 'Salida Segura',
				'icon': 'door-open-out.png',
				'click': function(){
					Hfos.closeApp();
				}
			}
		],
		'initialize': function(systemTray, widgetOptions, widgetElement){
			var toolbar = systemTray.getWorkspace().getToolbar();
			var subMenu = new HfosSubmenu(toolbar, widgetOptions, widgetElement);
			widgetElement.observe('mouseover', subMenu.show.bind(subMenu));
			toolbar.addSubmenu(subMenu);
		}
	}
];
