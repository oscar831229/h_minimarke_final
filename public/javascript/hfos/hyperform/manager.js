
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

var HyperFormManager = {

	/**
	 * Todos los hyperForms
	 */
	_hyperForms: {},

	/**
	 * Eventos de ejecución tardía para todos los formularios
	 */
	_lateEvents: {},

	/**
	 * Eventos de ejecución tardía para todas las grillas de los formularios
	 */
	_lateGridEvents: {},

	/**
	 * Crea un formulario HyperForm
	 */
	add: function(formName, plural, single, genre, gridConfig, restored)
	{
		try {
			HyperFormManager._hyperForms[formName] = new HyperForm(formName, plural, single, genre, gridConfig, restored);
			if(restored==false){
				var storage = Hfos.getApplication().getStorage();
				if(storage!==null){
					storage.save('HyperForms', {
						'id': formName,
						'plural': plural,
						'single': single,
						'genre': genre,
						'gridConfig': gridConfig
					});
				};
			};
		}
		catch(e){
			HfosException.show(e);
		}
	},

	get: function(formName)
	{
		return HyperFormManager._hyperForms[formName];
	},

	restore: function(formName){
		var storage = Hfos.getApplication().getStorage();
		if(storage!==null){
			storage.findFirst('HyperForms', formName, function(restoreData){
				if(restoreData){
					HyperFormManager._hyperForms[formName] = new HyperForm(restoreData.id, restoreData.plural, restoreData.single, restoreData.genre, restoreData.gridConfig, true);
				};
			});
		} else {
			alert(null)
		}
	},

	/**
	 * Agrega un evento con ejecución tardía al maestro del formulario
	 */
	lateBinding: function(formName, eventName, procedure)
	{
		if(typeof HyperFormManager._lateEvents[formName] == "undefined"){
			HyperFormManager._lateEvents[formName] = {};
		};
		if(typeof HyperFormManager._lateEvents[formName][eventName] == "undefined"){
			HyperFormManager._lateEvents[formName][eventName] = [];
		}
		HyperFormManager._lateEvents[formName][eventName].push(procedure);
	},

	/**
	 * Agrega un evento con ejecución tardía a la grilla detalle del formulario
	 */
	lateGridBinding: function(formName, eventName, procedure)
	{
		if(typeof HyperFormManager._lateGridEvents[formName] == "undefined"){
			HyperFormManager._lateGridEvents[formName] = {};
		};
		HyperFormManager._lateGridEvents[formName][eventName] = procedure;
	},

	/**
	 * Obtiene los eventos asignados a la grilla del formulario
	 */
	getLateGridBindings: function(formName)
	{
		return HyperFormManager._lateGridEvents[formName];
	},

	/**
	 * Obtiene los eventos asignados al maestro del formulario
	 */
	getLateBindings: function(formName)
	{
		return HyperFormManager._lateEvents[formName];
	},

	/**
	 * Notifica el cierre de un formulario
	 */
	notifyClosedForm: function(hyperForm){
		delete HyperFormManager._hyperForms[hyperForm.getName()];
		var storage = Hfos.getApplication().getStorage();
		if(storage!==null){
			storage.remove('HyperSearch', hyperForm.getName());
			storage.remove('HyperForms', hyperForm.getName());
			storage.remove('HyperGrids', hyperForm.getName());
			storage.findAllBy('InputFields', 'containerKey', hyperForm.getName(), function(storage, inputFields){
				for(var j=0;j<inputFields.length;j++){
					storage.remove('InputFields', inputFields[j].id)
				}
			}.bind(this, storage));
			storage.free();
		}
	}

}

