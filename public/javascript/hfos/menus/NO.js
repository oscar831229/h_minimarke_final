
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
		'title': 'Liquidación',
		'icon': 'ico-informes.png',
		'options': [
			{
				'title': 'Quincenal',
				'icon': 'document-library.png',
				'click': function(){
					Hfos.getApplication().run({
						id: 'win-liquidacion',
						icon: 'document-library.png',
						title: "Liquidación Quincenal",
						action: "liquidacion",
						width: '600px',
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
				'title': 'Empleados',
				'icon': 'user-business.png',
				'options': [
					{
						'title': 'Empleados',
						'icon': 'user-business.png',
						'click': function(){
							Hfos.getApplication().run({
								'id': 'win-empleados',
								'icon': 'user-business.png',
								'title': "Empleados",
								'action': "empleados",
							});
						}
					},
					{
						'title': 'Contratos de Empleados',
						'icon': 'user-business.png',
						'click': function(){
							Hfos.getApplication().run({
								'id': 'win-contratos',
								'icon': 'user-business.png',
								'title': "Contratos de Empleados",
								'action': "contratos",
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
			},
			{
				'title': 'Conceptos',
				'icon': 'blue-document-list.png',
				'options': [
					{
						'title': 'Básicos',
						'icon': 'blue-document-list.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-conceptos-basicos',
								icon: 'blue-document-list.png',
								title: "Conceptos Básicos",
								action: 'conceptos_basicos'
							});
						}
					},
					{
						'title': 'Devengos',
						'icon': 'blue-document-list.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-conceptos-devengos',
								icon: 'blue-document-list.png',
								title: "Conceptos de Devengos",
								action: 'conceptos_devengos'
							});
						}
					},
					{
						'title': 'Deducciones y Descuentos',
						'icon': 'blue-document-list.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-conceptos-descuentos',
								icon: 'blue-document-list.png',
								title: "Conceptos de Deducciones y Descuentos",
								action: 'conceptos_descuentos'
							});
						}
					},
					{
						'title': 'Licencias ó Suspensiones',
						'icon': 'blue-document-list.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-conceptos-licencias',
								icon: 'blue-document-list.png',
								title: "Conceptos de Licencias ó Suspensiones",
								action: 'conceptos_licencias'
							});
						}
					},
					{
						'title': 'Incapacidades',
						'icon': 'blue-document-list.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-conceptos-incapacidades',
								icon: 'blue-document-list.png',
								title: "Conceptos de Incapacidades",
								action: 'conceptos_incapacidades'
							});
						}
					},
					{
						'title': 'Provisiones',
						'icon': 'blue-document-list.png',
						'click': function(){
							Hfos.getApplication().run({
								id: 'win-conceptos-provisiones',
								icon: 'blue-document-list.png',
								title: "Conceptos de Provisiones",
								action: 'conceptos_provisiones'
							});
						}
					}
				]
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
				'title': 'Configuración',
				'icon': 'gear.png',
				'click': function(){
					icon: 'gear.png',
					Hfos.getApplication().run({
						id: 'win-settings-nomina',
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
