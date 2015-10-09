
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

/**
 * HyperForm
 *
 * ABM para crear formularios, maestros y maestro-detalle
 */
var HyperForm = Class.create({

	/**
	 * Nombre del controlador del formulario
	 */
	_formName: null,

	/**
	 * Plural de la entidad que mantiene el formulario
	 */
	_plural: null,

	/**
	 * Palabra singular de la entidad que mantiene el formulario
	 */
	_single: null,

	/**
	 * Referencia el elemento DOM del formulario
	 */
	_formElement: null,

	/**
	 * Configuración de la grilla detalle
	 */
	_gridConfig: null,

	/**
	 * Ventana donde está contenido el formulario
	 */
	_window: null,

	/**
	 * Último resultado del formulario
	 */
	_lastResultset: [],

	/**
	 * Almacena un entero al registro que se visualiza actualmente
	 */
	_recordPointer: 0,

	/**
	 * Ultimo selector en visualizar
	 */
	_pageResultsRows: null,

	/**
	 * Referencia a HyperBrowseData del formulario
	 */
	_browse: null,

	/**
	 * Indica si el contenido de los campos del paginado ha cambiado
	 */
	_browseChanged: null,

	/*
	 * Referencia a HyperMessages del formulario
	 */
	_messages: null,

	/**
	 * Referencia a HyperTabs
	 */
	_tabs: null,

	/**
	 * Llave del registro en pantalla
	 */
	_lastPrimary: null,

	/**
	 * Ultimo registro visualizado
	 */
	_lastResponse: null,

	/**
	 * Grilla detalle del formulario
	 */
	_grid: null,

	/**
	 * Grilla detalle del formulario
	 */
	_currentState: null,

	/**
	 * Campo seleccionado para editar
	 */
	_currentRecord: null,

	/**
	 * Constructor de HyperForm
	 *
   	 * @constructor
	 */
	initialize: function(controllerName, plural, single, genre, gridConfig, restored)
	{

		this._formName = controllerName;
		this._plural = plural;
		this._single = single;
		this._genre = genre;
		this._gridConfig = gridConfig;

		this._formElement = $(controllerName+'Form');
		if(this._formElement===null){
			new HfosModal.alert({
				title: this._plural,
				message: 'El módulo podría no haberse ejecutado correctamente, si es necesario intente abrirlo nuevamente'
			});
			return;
		};

		//Buscar ventana
		var ascestors = this._formElement.ancestors();
		for (var i = 0; i < ascestors.length; i++) {
			if (ascestors[i].hasClassName('window-main')) {
				this._window = Hfos.getApplication().getWorkspace().getWindowManager().getWindow(ascestors[i].id);
				this._window.setSubprocess(this);
				this._window.observe('afterClose', HyperFormManager.notifyClosedForm.bind(window, this));
				this._window.observe('onKeyPress', this._onContainerKeyPress.bind(this));
				break;
			}
		};

		//Agregar Eventos
		this._addCallbacks();

		//Agregar eventos con late-binding
		var masterEvents = this.getLateBindings();
		if (!Object.isUndefined(masterEvents)) {
			$H(masterEvents).each(function(masterEvent) {
				for (var i = 0; i < masterEvent[1].length; i++) {
					this.observe(masterEvent[0], masterEvent[1][i]);
				}
			}.bind(this));
		}

		//Messages
		this._messages = new HyperMessages(this);

		//Browse
		this._browse = new HyperBrowseData(this);
		this._browse.setAutoRowsPerPage(true);

		//ReportType
		HfosReportType.observeReportType(this);

		if(restored==false){

			//Evento afterInitialize
			this.fire('afterInitialize');

			//Crear grilla si es necesario
			if(gridConfig!=null){
				this._grid = new HyperGrid(this, gridConfig, false);
			};

			//Observar cambios en los inputs
			this._observeInputChanges();

			//State actual
			this.setCurrentState('index');
		} else {

			//Restaurar formulario
			this._restoreState();
			this.fire('afterRestore');
		}

	},

	/**
	 * Agrega eventos a los elementos del formulario
	 *
   	 * @this {HyperForm}
	 */
	_addCallbacks: function(){

		var searchButton = this.getElement('searchButton');
		searchButton.observe('click', this._doSearch.bind(this));

		var newButton = this.getElement('newButton');
		newButton.observe('click', this._doNew.bind(this));

		var editButton = this.getElement('editButton');
		editButton.observe('click', this._doEdit.bind(this));

		var saveButton = this.getElement('saveButton');
		saveButton.observe('click', this._sendForm.bind(this, saveButton));

		var deleteButton = this.getElement('deleteButton');
		deleteButton.observe('click', this._doDelete.bind(this));

		var revisionButton = this.getElement('revisionButton');
		revisionButton.observe('click', this._doRevision.bind(this));

		var backButton = this.getElement('backButton');
		backButton.observe('click', this._doBack.bind(this));

		var importButton = this.getElement('importButton');
		if(Object.isElement(importButton)){
			importButton.observe('click', this._doImport.bind(this));
		};

		var loadButton = this.getElement('loadButton');
		loadButton.observe('click', this._loadImport.bind(this, loadButton));

		var searchForm = this.getElement('hySearchForm');
		var firstElement = searchForm.findFirstElement();
		if(firstElement!=null){
			firstElement.activate();
		};
	},

	/**
	 * Recibe los eventos hechos por teclado en la ventana o contenedor del formulario
	 *
   	 * @this {HyperForm}
	 */
	_onContainerKeyPress: function(container, event){
		if(this.fire('onKeyPress', event)===false){
			new Event.stop(event);
			new Event.cancelBubble(event);
			return false;
		};
		switch(this._currentState){
			case 'index':
				if(event.keyCode==Event.KEY_RETURN){
					this._doSearch();
					new Event.stop(event);
					return false;
				};
				if(event.ctrlKey&&event.keyCode==Event.KEY_N){
					this._doNew();
					new Event.stop(event);
					new Event.cancelBubble(event);
					return false;
				};
				break;
			case 'new':
			case 'edit':
				if(event.keyCode==Event.KEY_RETURN||event.keyCode==Event.KEY_F7){
					var element = document.activeElement;
					if(element&&element.tagName!='TEXTAREA'){
						this.save();
						new Event.stop(event);
						new Event.cancelBubble(event);
						return false;
					} else {
						return true;
					};
				};
				if(event.ctrlKey&&event.keyCode==Event.KEY_LEFT){
					var hyperTabs = this.getTabs();
					if(hyperTabs!==null){
						hyperTabs.movePrev();
						new Event.stop(event);
						new Event.cancelBubble(event);
						return false;
					}
				};
				if(event.ctrlKey&&event.keyCode==Event.KEY_RIGHT){
					var hyperTabs = this.getTabs();
					if(hyperTabs!==null){
						hyperTabs.moveNext();
						new Event.stop(event);
						new Event.cancelBubble(event);
						return false;
					}
				};
				break;
			case 'browse':
				if(event.keyCode==Event.KEY_RETURN){
					var selectedRow = this._browse.getSelectedRow();
					if(selectedRow!==null){
						var primary = selectedRow.lang;
						this._showRecordToScreen(primary);
						new Event.stop(event);
						return false;
					};
				};
				if(event.keyCode==Event.KEY_UP){
					this._browse.moveRecordUp();
					new Event.stop(event);
					return false;
				};
				if(event.keyCode==Event.KEY_DOWN){
					this._browse.moveRecordDown();
					new Event.stop(event);
					return false;
				};
				if(event.keyCode==Event.KEY_RIGHT){
					this._browse.moveToNextPage();
					new Event.stop(event);
					return false;
				};
				if(event.keyCode==Event.KEY_LEFT){
					this._browse.moveToPrevPage();
					new Event.stop(event);
					return false;
				};
				if(event.ctrlKey&&event.keyCode==Event.KEY_N){
					this._doNew();
					new Event.stop(event);
					new Event.cancelBubble(event);
					return false;
				};
				break;
			case 'detail':
				if(event.keyCode==Event.KEY_RIGHT){
					this._showNextRecord();
					new Event.stop(event)
					return false;
				};
				if(event.keyCode==Event.KEY_LEFT){
					this._showPrevRecord();
					new Event.stop(event)
					return false;
				};
				if(event.keyCode==Event.KEY_SUPR){
					this._doDelete();
					new Event.stop(event)
					return false;
				};
				if((event.ctrlKey&&event.keyCode==Event.KEY_E)||event.keyCode==Event.KEY_RETURN){
					this._doEdit();
					new Event.stop(event);
					new Event.cancelBubble(event);
					return false;
				};
				break;
		};
		if(event.keyCode==Event.KEY_F10){
			this._doBack();
			new Event.stop(event);
			new Event.cancelBubble(event);
			return false;
		};
		return true;
	},

	/**
	 * Sube el scroll de la ventana hasta la parte superior
	 *
   	 * @this {HyperForm}
	 */
	_scrollToTop: function(){
		if(this._window!==null){
			this._window.scrollToTop();
		}
	},

	/**
	 * Notifica la ventana sobre un cambio en el contenido de la misma
	 *
   	 * @this {HyperForm}
	 */
	_notifyContentChange: function(){
		if(this._window!==null){
			this._window.notify('contentChanged')
		}
	},

	/**
	 * Notifica al hyperform sobre un cambio en el contenido de su ventana
	 *
   	 * @this {HyperForm}
	 */
	notifyContentChange: function()
	{
		this._notifyContentChange();
	},

	/**
	 * Muestra el spinner del formulario
	 *
   	 * @this {HyperForm}
	 */
	_showSpinner: function()
	{
		this.getElement('hySpinner').show();
	},

	/**
	 * Oculta el spinner del formulario
	 *
   	 * @this {HyperForm}
	 */
	_hideSpinner: function(){
		this.getElement('hySpinner').hide();
	},

	/**
	 * Muestra el spinner de la barra de botones
	 *
   	 * @this {HyperForm}
	 */
	_showToolbarSpinner: function(){
		this.getElement('hyToolbarSpinner').show();
	},

	/**
	 * Oculta el spinner de la barra de botones
	 *
   	 * @this {HyperForm}
	 */
	_hideToolbarSpinner: function(){
		this.getElement('hyToolbarSpinner').hide();
	},

	/**
	 * Devuelve un fragmento HTML
	 *
   	 * @this {HyperForm}
	 */
	_recordPreview: function(response){

		//Actualizar el record pointer
		this._recordPointer = response.number;
		this.getElement('hyDetailsDiv').lang = response.number;

		if(response.data.length>13){
			var fieldsPerRow = parseInt(this._window.getWidth()/450, 10);
		} else {
			var fieldsPerRow = 1;
		};
		var html = '<table class="hyDetailsTab" cellspacing="0" width="95%" align="center"><tr>';
		for(var i=0;i<response.data.length;i++){
			var element = response.data[i];
			html+='<td align="right" class="hyDetailsTdLeft" width="20%"><label for="'+element.name+'">'+element.caption+'</b></td>'+
			'<td align="left" width="25%">'+element.value+'&nbsp;</td>';
			if(((i+1)%fieldsPerRow)==0){
				html+='</tr><tr>';
			};
		};
		html+='</table>';
		return html;
	},

	/**
	 * Genera la pantalla de detalle de un registro
	 *
   	 * @this {HyperForm}
	 */
	_renderDetails: function(response){

		var html = '<div align="right" class="hyNavBar">'+
		'<input class="firstNavButton" type="button" value="Primero">'+
		'<input class="prevNavButton" type="button" value="Anterior">'+
		'<input class="nextNavButton" type="button" value="Siguiente">'+
		'<input class="lastNavButton" type="button" value="Último">'+
		'</div><div class="hyRecordPreview">'
		html+=this._recordPreview(response);
		html+='</div>';
		this.getElement('hyDetailsDiv').update(html);

		this._addNavigationCallbacks();

		this._notifyContentChange();

		//State actual
		this.setCurrentState('detail');
	},

	/**
	 * Agrega los callbacks de navegación
	 *
   	 * @this {HyperForm}
	 */
	_addNavigationCallbacks: function(){
		var firstNavButton = this.getElement('firstNavButton');
		firstNavButton.observe('click', this._showFirstRecord.bind(this));

		var prevNavButton = this.getElement('prevNavButton');
		prevNavButton.observe('click', this._showPrevRecord.bind(this));

		var nextNavButton = this.getElement('nextNavButton');
		nextNavButton.observe('click', this._showNextRecord.bind(this));

		var lastNavButton = this.getElement('lastNavButton');
		lastNavButton.observe('click', this._showLastRecord.bind(this));
	},

	/**
	 * Actualiza parcialmente un registro
	 *
   	 * @this {HyperForm}
	 */
	_renderPartialDetails: function(response){
		var hyRecordPreview = this.getElement('hyRecordPreview');
		hyRecordPreview.update(this._recordPreview(response));
		this._notifyContentChange();
	},

	/**
	 * Muestra el primer registro del resultset
	 *
   	 * @this {HyperForm}
	 */
	_showFirstRecord: function(){
		try {
			this._getRecordDetails(this._lastResultset[0].primary+'&n=0')
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Muestra el registro anterior al actual
	 *
   	 * @this {HyperForm}
	 */
	_showPrevRecord: function(){
		try {
			if(this._recordPointer>0){
				var pointer = this._recordPointer-1;
				if(typeof this._lastResultset[pointer] != "undefined"){
					this._getRecordDetails(this._lastResultset[pointer].primary+'&n='+pointer);
				}
			} else {
				new HfosModal.alert({
					title: this._plural,
					message: 'Ya está en el primer registro'
				});
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Muestra el registro siguiente al actual
	 *
   	 * @this {HyperForm}
	 */
	_showNextRecord: function(){
		try {
			var numberResults = this._browse.getNumberResults();
			if(this._recordPointer!=(numberResults - 1)){
				var pointer = this._recordPointer + 1;
				if(typeof this._lastResultset[pointer] != "undefined"){
					this._getRecordDetails(this._lastResultset[pointer].primary+'&n='+pointer);
				}
			} else {
				new HfosModal.alert({
					title: this._plural,
					message: 'Ya está en el último registro'
				});
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Muestra el primer registro del resultset
	 *
   	 * @this {HyperForm}
	 */
	_showLastRecord: function(){
		try {
			var numberResults = this._browse.getNumberResults();
			var pointer = numberResults-1;
			this._getRecordDetails(this._lastResultset[pointer].primary+'&n='+pointer);
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Busca pestañas dentro del formulario y agrega los respectivos callbacks
	 *
   	 * @this {HyperForm}
	 */
	_prepareInputTabs: function(){
		this._tabs = new HyperTabs(this);
	},

	/**
	 * Genera la pantalla de nuevo
	 *
   	 * @this {HyperForm}
	 */
	_renderNew: function(responseText){
		this._showFormPanel('hyNewDiv');
		this.getElement('hyNewDiv').update(responseText);
		var hyNewForm = this.getElement('hySaveForm');
		if(hyNewForm!==null){
			this._messages.notice('Ingrese los datos en los campos y presione "Grabar"');
			var inputs = hyNewForm.getInputs();
			if(inputs.length>0){
				inputs[0].activate();
			};

			//State actual
			this.setCurrentState('new');
			this._prepareInputTabs();
			this._observeInputChanges();
			this.fire('beforeInput');
		} else {
			this._messages.hide();
		};
		this.fire('onNew');
		this._notifyContentChange();
	},

	/**
	 *
   	 * @this {HyperForm}
	 */
	_observeInputChanges: function()
	{
		var inputs = this.getActiveSection().select('input, select, textarea');
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].id != '') {
				inputs[i].observe('change', this._storeInputField.bind(this, inputs[i]));
			};
		};
	},

	/**
	 *
   	 * @this {HyperForm}
	 */
	_storeInputField: function(element){
		var storage = Hfos.getApplication().getStorage();
		if(storage!==null){
			var fields = {};
			var inputs = this.getActiveSection().select('input, select, textarea');
			for(var i=0;i<inputs.length;i++){
				if(inputs[i].id!=''){
					if(!inputs[i].id.include('[')){
						fields[inputs[i].id] = inputs[i].getValue();
					};
				};
			};
			storage.save('InputFields', {
				'id': this._formName+'-'+this._currentState,
				'containerKey': this._formName,
				'fields': fields
			});
		};
	},

	/**
	 * Genera la pantalla de importar
	 *
   	 * @this {HyperForm}
	 */
	_renderImport: function(responseText)
	{

		this._showFormPanel('hyImportDiv');

		this.getElement('hyImportDiv').update(responseText);
		this._messages.notice('Adjunte el archivo y haga click en "Cargar"');

		var loadButton = this.getElement('loadButton');
		loadButton.enable();

		var subirFrame = this.getElement('subirFrame');
		subirFrame.hide();
		window.setTimeout(function(subirFrame){
			subirFrame.observe('load', function(subirFrame){
				this._browseChanged = true;
				this.getElement('loadButton').enable();
				this.getElement('subirBar').hide();
				subirFrame.show();
				this._notifyContentChange();
			}.bind(this, subirFrame));
		}.bind(this, subirFrame), 1000);

		this.setCurrentState('import');
		this._notifyContentChange();
	},

	/**
	 * Visualiza la pantalla de editar un registro
	 *
   	 * @this {HyperForm}
	 */
	_renderEdit: function(responseText)
	{

		this._showFormPanel('hyEditDiv');
		this.getElement('hyEditDiv').update(responseText);

		var hyEditForm = this.getElement('hySaveForm');
		if (hyEditForm !== null) {
			var firstInput = hyEditForm.findFirstElement();
			if (firstInput) {
				firstInput.activate();
			};
			this.setCurrentState('edit');
			this._prepareInputTabs();
			this._observeInputChanges();
			this.fire('beforeInput');
		};

		this.fire('onEdit');
		this._notifyContentChange();

	},

	/**
	 * Visualiza las revisiones de un registro
	 *
   	 * @this {HyperForm}
	 */
	_renderRcs: function(responseText){

		this._showFormPanel('hyRcsDiv');
		this.getElement('hyRcsDiv').update(responseText);

		var numberRevisions = this.select('div.record_block').length;
		if(numberRevisions>0){
			if(numberRevisions==1){
				this._messages.notice('Hay una revisión');
			} else {
				this._messages.notice('Hay '+numberRevisions+' revisiones');
			}
		} else {
			this._messages.hide();
		};
		this._notifyContentChange();

		//State actual
		this.setCurrentState('rcs');
	},

	/**
	 * Oculta los botones de control que estén visibles
	 *
   	 * @this {HyperForm}
	 */
	_hideControlButtons: function(){
		this._formElement.select('input.hyControlButton').each(function(element){
			element.parentNode.hide();
		});
	},

	/**
	 * Muestra un botón
	 *
   	 * @this {HyperForm}
	 */
	showControlButton: function(className){
		var button = this.getElement(className);
		if(Object.isElement(button)){
			button.parentNode.show();
		}
	},

	/**
	 * Genera la tabla de visualizar
	 *
   	 * @this {HyperForm}
	 */
	_renderBrowse: function(response){

		var storage = Hfos.getApplication().getStorage();
		if(storage!==null){
			storage.save('HyperSearch', {
				'id': this._formName,
				'results': response.results.data
			})
		};

		this._browse.reset();
		this._browse.build(this.getElement('hyBrowseDiv'), response);

		this._addBrowseCallbacks();

		this._lastResultset = response.results.data;
		this._notifyContentChange();

		//State actual
		this.setCurrentState('browse');
	},

	/**
	 * Agrega los callbacks de editar y borrar al browse
	 *
   	 * @this {HyperForm}
	 */
	_addBrowseCallbacks: function()
	{

		//Agregar handlers a las filas para editar
		var hySortRows = this._browse.getRows();
		for (var i = 0; i < hySortRows.length; i++) {
			if (hySortRows[i].lang == '') {
				hySortRows[i].lang = hySortRows[i].title;
				hySortRows[i].title = '';
			};
			hySortRows[i].observe('dblclick', this._editHandler.bind(this, hySortRows[i]));
		}

		//Agregar handlers a los iconos de editar
		var hyDetails = this._browse.getDetailsButtons();
		for (var i = 0; i < hyDetails.length; i++) {
			if (hyDetails[i].lang==''){
				hyDetails[i].lang = hyDetails[i].title;
				hyDetails[i].title = 'Ver/Editar';
			};
			hyDetails[i].observe('click', this._editHandler.bind(this, hyDetails[i]));
		};

		//Agregar handlers a los iconos de borrar
		var hyDeletes = this._browse.getDeleteButtons();
		for (var i = 0;i < hyDetails.length; i++) {
			if (hyDeletes[i].lang == '') {
				hyDeletes[i].lang  = hyDeletes[i].title;
				hyDeletes[i].title = 'Borrar';
			};
			hyDeletes[i].observe('click', this._deleteHandler.bind(this, hyDeletes[i]));
		};

	},

	/**
	 * Handler para editar un registro
	 *
   	 * @this {HyperForm}
	 */
	_editHandler: function(element, event)
	{
		this._showRecordToScreen(element.lang);
		new Event.stop(event);
	},

	/**
	 * Handler para eliminar un registro en la vista de visualizar
	 *
   	 * @this {HyperForm}
	 */
	_deleteHandler: function(element, event)
	{
		this._deleteRecord(element.lang, element);
		new Event.stop(event);
	},

	/**
	 * Muestra un registro en pantalla para edición
	 *
   	 * @this {HyperForm}
	 */
	_showRecordToScreen: function(primary){
		this._hideControlButtons();
		this.showControlButton('revisionButton');
		this.showControlButton('editButton');
		this.showControlButton('deleteButton');
		this._getRecordDetails(primary);
	},

	/**
	 * Muestra un error en pantalla
	 *
	 */
	_showError: function(response){
		if(typeof response.code != "undefined"){
			response.message+=' <a href="http://localhost/site/kb/es_CO/'+response.code+'" target="_new" class="error-code">Ayuda</a>';
		};
		this._messages.error(response.message);
	},

	/**
	 * Consulta los detalles del registro
	 *
   	 * @this {HyperForm}
	 */
	_getRecordDetails: function(primary){
		this._doJsonRequest(this._formName+'/getRecordDetails', {
			parameters: primary,
			onSuccess: function(primary, response, transport){
				if(response.status=='OK'){
					var numberResults = this._browse.getNumberResults();
					if(numberResults>0){
						this._messages.notice(response.message+' '+numberResults);
					} else {
						this._messages.notice(response.message);
					};
					this._lastPrimary = primary;
					this._lastResponse = response;
					this._formElement.lang = primary;
					this._messages.setActiveToDefault();
					this.fire('beforeRecordPreview', response);
					var hyEditDiv = this.getElement('hyEditDiv');
					if(hyEditDiv.visible()==false){
						this._showFormPanel('hyDetailsDiv');
						this.showControlButton('backButton');
						this._renderDetails(response);
						this.fire('afterDetails');
					} else {
						this._renderPartialDetails(response);
					};
					if(numberResults<2){
						this.getElement('hyNavBar').invisible();
					};
				} else {
					if(response.status=='FAILED'){
						this._showError(response);
					} else {
						this._messages.notice(transport.responseText);
					}
				}
			}.bind(this, primary)
		});
	},

	/**
	 * Carga el archivo
	 *
   	 * @this {HyperForm}
	 */
	_loadImport: function(loadButton){
		loadButton.disable();
		this.getElement('subirBar').hide();
		var fileInput = this.selectOne('input#archivo');
		if(fileInput.files.length==0){
			new HfosModal.alert({
				title: this._plural,
				message: 'Seleccione el archivo a importar'
			});
			loadButton.enable();
		} else {
			if(fileInput.files.length>1){
				new HfosModal.alert({
					title: this._plural,
					message: 'Solo se puede importar un archivo al tiempo'
				});
				loadButton.enable();
			} else {
				var validTypes = [
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'application/vnd.ms-excel',
					'text/csv',
					'text/x-comma-separated-values'
				];
				var validType = false;
				for(var i=0;i<validTypes.length;i++){
					if(validTypes[i]==fileInput.files[0].type){
						validType = true;
						break;
					}
				};
				if(validType==false){
					new HfosModal.alert({
						title: this._plural,
						message: 'El archivo no es de Microsoft Excel, por favor revise'
					});
					loadButton.enable();
				} else {
					this.getElement('importTable').hide();
					this.getElement('subirBar').show();
					this.getElement('subirForm').submit();
					this._notifyContentChange();
				}
			}
		}
	},

	/**
	 * Edita el registro en pantalla
	 *
   	 * @this {HyperForm}
	 */
	_doEdit: function(){
		if(this._lastPrimary==null){
			new HfosModal.alert({
				title: this._plural,
				message: 'No hay un registro en pantalla'
			});
		} else {
			this._showToolbarSpinner();
			this._doRequest(this._formName+'/edit', {
				parameters: this._lastPrimary,
				onSuccess: function(transport){
					this._messages.notice('Ingrese los datos en los campos y presione "Guardar"');
					this._hideControlButtons();
					this.showControlButton('backButton');
					this.showControlButton('saveButton');
					this._renderEdit(transport.responseText);
				},
				onComplete: function(){
					this._hideToolbarSpinner();
				}
			});
		}
	},

	/**
	 * Elimina el registro en pantalla
	 *
   	 * @this {HyperForm}
	 */
	_doDelete: function(){
		if(this._lastPrimary==null){
			new HfosModal.alert({
				title: this._plural,
				message: 'No hay un registro en pantalla'
			});
		} else {
			this._deleteRecord(this._lastPrimary);
		}
	},

	/**
	 * Consulta las revisiones del registro en pantalla
	 *
   	 * @this {HyperForm}
	 */
	_doRevision: function(){
		if(this._lastPrimary==null){
			new HfosModal.alert({
				title: this._plural,
				message: 'No hay un registro en pantalla'
			});
		} else {
			this._showToolbarSpinner();
			this._doRequest(this._formName+'/rcs', {
				parameters: this._lastPrimary,
				onSuccess: function(transport){
					this._renderRcs(transport.responseText);
					this._hideControlButtons();
					this.showControlButton('backButton');
				},
				onComplete: function(){
					this._hideToolbarSpinner();
				}
			});
		}
	},

	/**
	 * Borra un registro
	 *
   	 * @this {HyperForm}
	 */
	_deleteRecord: function(primary, element){
		new HfosModal.confirm({
			title: this._plural,
			message: 'Seguro desea eliminar '+this._single+'?',
			onAccept: function(primary, element){
				this._doJsonRequest(this._formName+'/delete/?'+primary, {
					method: 'GET',
					onSuccess: function(element, response){
						if(response.status=='OK'){
							this._messages.success(response.message);
							if(typeof element != "undefined"){
								this._browse.deleteRow(element.ancestors()[1]);
								var numberResults = this._browse.getNumberResults();
								if(numberResults==0){
									this._doBack();
								}
							} else {
								if(this._currentState=='detail'){
									window.setTimeout(function(){
										//Se busca y se borra el html de la fila borrada
										var hySortRows = this._browse.getRows();
										for(var i=0;i<2;i++){
											if(typeof hySortRows[i] != "undefined"){
												if(hySortRows[i].lang == primary){
													this._browse.deleteRow(hySortRows[i]);
												}
											};
										};
										this._doBack();
									}.bind(this), 1000);
								}
							}
							this._lastPrimary = null;
							this._formElement.lang = '';
						} else {
							this._showError(response);
						};
					}.bind(this, element)
				});
			}.bind(this, primary, element)
		});
	},

	/**
	 * Oculta todos los subpaneles del formulario y muestra el que se indique
	 *
   	 * @this {HyperForm}
	 */
	_showFormPanel: function(panelToShow){
		this._formElement.select('div.hyFormDiv').each(function(panelElement){
			panelElement.hide();
		});
		this.getElement(panelToShow).show();
	},

	/**
	 * Establece el tipo de salida de la consulta manualmente
	 *
   	 * @this {HyperForm}
	 */
	_setReportType: function(reportType){
		HfosReportType.changeReportType(reportType)
	},

	/**
	 * Envia el formulario de búsqueda y visualiza los resultados
	 *
   	 * @this {HyperForm}
	 */
	_doSearch: function(){
		this._showSpinner();
		this._browseChanged = false;
		var hySearchForm = this.getElement('hySearchForm');
		this._doJsonFormRequest(hySearchForm, {
			onSuccess: this._afterSearch,
			onComplete: function(hySearchForm){
				this._hideSpinner();
				hySearchForm.enable();
			}
		});
	},

	/**
	 * Evento al obtener los resultados de la búsqueda
	 *
   	 * @this {HyperForm}
	 */
	_afterSearch: function(hySearchForm, response){
		if(response.status=='OK'){
			if(response.type=='screen'){
				this._messages.notice(response.message);
				if(response.numberResults>0){
					this._showFormPanel('hyBrowseDiv');
					this._renderBrowse(response);
					this._hideControlButtons();
					this.showControlButton('newButton');
					this.showControlButton('importButton');
					this.showControlButton('backButton');
					this.setCurrentState('browse');
				}
			} else {
				if(typeof response.url != "undefined"){
					window.open(response.url);
				} else {
					this._messages.notice(response.message);
				}
			}
		};
	},

	/**
	 * Invoca y muestra el formulario de creación de registros
	 *
   	 * @this {HyperForm}
	 */
	_doNew: function(){
		this._showToolbarSpinner();
		this._doRequest(this._formName+'/new', {
			method: 'GET',
			onSuccess: function(transport){
				this._hideControlButtons();
				this.showControlButton('backButton');
				this.showControlButton('saveButton');
				this._renderNew(transport.responseText);
			},
			onComplete: function(){
				this._hideToolbarSpinner();
			}
		});
	},

	/**
	 * Muestra la pantalla de "importar"
	 *
   	 * @this {HyperForm}
	 */
	_doImport: function(){
		this._browseChanged = false;
		this._showToolbarSpinner();
		this._doRequest(this._formName+'/import', {
			method: 'GET',
			onSuccess: function(transport){
				this._renderImport(transport.responseText);
				this._hideControlButtons();
				this.showControlButton('backButton');
				this.showControlButton('loadButton');
			},
			onComplete: function(){
				this._hideToolbarSpinner();
			}
		});
	},

	/**
	 * Realiza la acción de cancelar cuando se está en "nuevo", "editar" ó en "detalles"
	 *
   	 * @this {HyperForm}
	 */
	_doBack: function(){
		try {
			if(this._currentState=='index'){
				return false;
			};
			this._hideControlButtons();
			switch(this._currentState){
				case 'new':
					this.showControlButton('newButton');
					this.getElement('hyNewDiv').update('');
					var numberResults = this._browse.getNumberResults();
					if(numberResults==0){
						this._restoreSearchMessage();
						this._showFormPanel('hySearchDiv');
						this.showControlButton('importButton');
						this.getElement('hySearchForm').enable();
						this.setCurrentState('index');
					} else {
						if(this._browseChanged==true){
							this._setReportType('screen');
							this._doSearch();
						} else {
							this._messages.setDefault();
							this._showFormPanel('hyBrowseDiv');
							this.showControlButton('backButton');
							this.setCurrentState('browse');
						};
					};
					break;
				case 'browse':
					this._lastResultset = [];
					this._restoreSearchMessage();
					this._showFormPanel('hySearchDiv');
					this.showControlButton('newButton');
					this.showControlButton('importButton');
					this.getElement('hySearchForm').enable();
					this.setCurrentState('index');
					break;
				case 'import':
					if(this._browseChanged==true){
						this._setReportType('screen');
						this._doSearch();
					} else {
						this._lastResultset = [];
						this._restoreSearchMessage();
						this._showFormPanel('hySearchDiv');
						this.showControlButton('newButton');
						this.showControlButton('importButton');
						this.getElement('hySearchForm').enable();
						this.setCurrentState('index');
					};
					break;
				case 'edit':
				case 'rcs':
					this._messages.setDefault();
					this._showFormPanel('hyDetailsDiv');
					this.showControlButton('revisionButton');
					this.showControlButton('editButton');
					this.showControlButton('deleteButton');
					this.showControlButton('backButton');
					this.getElement('hyEditDiv').update('');
					this.getElement('hyRcsDiv').update('');
					this.setCurrentState('detail');
					this.fire('afterDetails');
					if(this._browseChanged==false){
						this.fire('beforeRecordPreview', this._lastResponse);
					};
					break;
				case 'detail':
					if(this._browseChanged==true){
						this._setReportType('screen');
						this._doSearch();
					} else {
						var numberResults = this._browse.getNumberResults();
						if(numberResults>0){
							this._messages.setDefault();
							this.showControlButton('newButton');
							this.showControlButton('importButton');
							this.showControlButton('backButton');
							this._showFormPanel('hyBrowseDiv');
							this.getElement('hyDetailsDiv').update('');
							this.setCurrentState('browse');
						} else {
							this._lastResultset = [];
							this._restoreSearchMessage();
							this._showFormPanel('hySearchDiv');
							this.showControlButton('newButton');
							this.showControlButton('importButton');
							this.getElement('hySearchForm').enable();
							this.setCurrentState('index');
						}
					};
					break;
			};
			this._notifyContentChange();
			this.fire('afterBack');
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Restaura el mensaje de la pantalla de buscar
	 *
   	 * @this {HyperForm}
	 */
	_restoreSearchMessage: function(){
		var message = 'Ingrese un criterio de búsqueda ó presione "Consultar" para ver';
		if(this._genre=='M'){
			this._messages.notice(message+' todos los '+this._plural.toLowerCase());
		} else {
			this._messages.notice(message+' todas las '+this._plural.toLowerCase());
		};
	},

	/**
	 * Envia el formulario de crear o editar
	 *
   	 * @this {HyperForm}
	 */
	save: function(){
		var saveButton = this.getElement('saveButton');
		if(saveButton!==null){
			this._sendForm(saveButton);
		};
	},

	/**
	 * Envia el formulario para "crear" ó "guardar"
	 *
   	 * @this {HyperForm}
	 */
	_sendForm: function(saveButton){
		this._showToolbarSpinner();
		saveButton.disable();
		if(this.fire('beforeSendForm')===false){
			return;
		};
		this._scrollToTop();
		var formElement = this.getElement('hySaveForm');
		this._doJsonFormRequest(formElement, {
			onSuccess: this.sendFormHandler,
			onComplete: function(hySendForm){
				this.getElement('saveButton').enable();
				this._hideToolbarSpinner();
				hySendForm.enable();
			}
		});
	},

	/**
	 * Handler que recibe la respuesta al enviar el formulario al guardar
	 *
   	 * @this {HyperForm}
	 */
	sendFormHandler: function(hySendForm, response){
		this._browseChanged = false;
		if(response.status=='OK'){
			this._messages.notice(response.message);
			window.setTimeout(function(response){
				var numberResults = this._browse.getNumberResults();
				if(numberResults>0){
					if(response.type=='insert'){
						this._showRecordToScreen(response.primary+'&n='+(numberResults-1));
					} else {
						this._showRecordToScreen(response.primary+'&n='+this._recordPointer);
					};
					this._browseChanged = true;
				} else {
					this._showRecordToScreen(response.primary);
				};
				this._showFormPanel('hyDetailsDiv');
				this.showControlButton('backButton');
				this.getElement('hyEditDiv').update('');
				this.getElement('hyNewDiv').update('');
				this.fire('afterDetails');
				this.fire('afterBack');
				this.fire('beforeRecordPreview', response);
			}.bind(this, response), 1000);
		} else {
			if(response.status=='FAILED'){
				this._showError(response);
				if(typeof response.fields != "undefined"){
					if(response.fields.length>0){
						hySendForm.enable();
						this._scrollToTop();
						if(typeof hySendForm[response.fields[0]] != "undefined"){
							hySendForm[response.fields[0]].activate();
						}
					}
				}
			}
		};
		if(this.fire('afterSendForm', response)===false){
			return;
		};
		this._lastPrimary = null;
		this._formElement.lang = '';
	},

	/**
	 * Vuelve atrás el número de veces que indique number
	 *
   	 * @this {HyperForm}
	 */
	back: function(number){
		for(var i=0;i<number;i++){
			this._doBack();
		}
	},

	/**
	 * Agrega un botón de control al formulario
	 *
   	 * @this {HyperForm}
	 */
	addControlButton: function(properties){
		try {
			if(Object.isUndefined(properties.className)){
				var classes =  '';
			} else {
				if(Object.isArray(properties.className)){
					var classes = properties.className.join(' ');
				} else {
					var classes = properties.className;
				}
			};
			var existentButton = this.getElement(classes);
			if(existentButton!==null){
				this.showControlButton(classes);
				return existentButton;
			};
			var tdElement = document.createElement("TD");
			tdElement.addClassName("hyTdLeftControlButton");
			tdElement.update('<input type="button" class="hyControlButton '+classes+'" value="'+properties.value+'"/>');
			this.getElement("hyTrControlButtons").insert({'top': tdElement});
			this.showControlButton(classes);
			var button = this.getElement(classes);
			if(typeof properties.onClick != "undefined"){
				button.observe('click', properties.onClick);
			};
			return button;
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Quita un botón de control al formulario
	 *
   	 * @this {HyperForm}
	 */
	removeControlButton: function(name){
		var controlButton = this.getElement(name);
		if(controlButton){
			controlButton.up().erase();
		}
	},

	/**
	 * Envia un formulario via Ajax
	 *
   	 * @this {HyperForm}
	 */
	_doFormRequest: function(formElement, options){
		options.onLoading = function(formElement){
			formElement.disable();
		}.bind(this, formElement);
		if(typeof options.onSuccess != "undefined"){
			options.onSuccess = options.onSuccess.bind(this, formElement);
		}
		options.checkAcl = true;
		return HfosAjax.FormRequest(formElement, options);
	},

	/**
	 * Ejecuta una petición Ajax que devuelve una salida HTML
	 *
   	 * @this {HyperForm}
	 */
	_doRequest: function(url, options){
		if(typeof options.onSuccess != "undefined"){
			options.onSuccess = options.onSuccess.bind(this);
		};
		if(typeof options.onComplete != "undefined"){
			options.onComplete = options.onComplete.bind(this);
		};
		options.checkAcl = true;
		return new HfosAjax.Request(url, options);
	},

	/**
	 * Envía un formulario via Ajax recibiendo un JSON
	 *
   	 * @this {HyperForm}
	 */
	_doJsonFormRequest: function(formElement, options){
		options.onLoading = function(formElement){
			formElement.disable();
		}.bind(this, formElement);
		if(typeof options.onSuccess != "undefined"){
			options.onSuccess = options.onSuccess.bind(this, formElement);
		};
		if(typeof options.onComplete != "undefined"){
			options.onComplete = options.onComplete.bind(this, formElement);
		};
		options.checkAcl = true;
		console.log(formElement);
		return HfosAjax.JsonFormRequest(formElement, options);
	},

	/**
	 * Ejecuta una petición Ajax que devuelve una salida JSON
	 *
   	 * @this {HyperForm}
	 */
	_doJsonRequest: function(url, options){
		options.checkAcl = true;
		return new HfosAjax.JsonRequest(url, options);
	},

	/**
	 * Restaura la pantalla de visualización
	 *
   	 * @this {HyperForm}
	 */
	_restoreBrowse: function(){
		this._browse.restore(this.getElement('hyBrowseDiv'));
		this._addBrowseCallbacks();

	},

	/**
	 * Restaura la pantalla de detalles
	 *
   	 * @this {HyperForm}
	 */
	_restoreDetails: function(){
		var storage = Hfos.getApplication().getStorage();
		if(storage!==null){
			storage.findFirst('HyperSearch', this._formName, function(hySearchData){
				if(typeof hySearchData != "undefined"){
					this._lastPrimary = this._formElement.lang;
					this._lastResultset = hySearchData.results;
					this._recordPointer = parseInt(this.getElement('hyDetailsDiv').lang, 10);
					if(hySearchData.results.length<2){
						this.getElement('hyNavBar').invisible();
					} else {
						this._addNavigationCallbacks();
					};
				};
			}.bind(this));
		};
	},

	/**
	 * Restaura la captura de datos, pestañas y grilla detalle
	 *
   	 * @this {HyperForm}
	 */
	_restoreInput: function(){
		this._prepareInputTabs();
		this._observeInputChanges();
		if(this._gridConfig!=null){
			this._grid = new HyperGrid(this, this._gridConfig, true);
		};
	},

	/**
	 * Restaura los campos de entrada
	 *
   	 * @this {HyperForm}
	 */
	_restoreInputFields: function(state){
		var storage = Hfos.getApplication().getStorage();
		if(storage!==null){
			var activeSection = this.getActiveSection();
			storage.findFirst('InputFields', this._formName+'-'+state, function(activeSection, inputFields){
				if(typeof inputFields != "undefined"){
					$H(inputFields.fields).each(function(field){
						var fieldElement = activeSection.selectOne('#'+field[0]);
						if(fieldElement){
							fieldElement.setValue(field[1]);
						};
					});
				}
			}.bind(this, activeSection));
		};
	},

	/**
	 * Restaura los autocompleters presentes
	 *
   	 * @this {HyperForm}
	 */
	_restoreCompleters: function(){
		HfosCommon.restoreCompleters(this);
	},

	/**
	 * Restaura el estado de la aplicación
	 *
   	 * @this {HyperForm}
	 */
	_restoreState: function(){
		this._hideSpinner();
		this._hideToolbarSpinner();
		var state = this._window.getState();
		this.setCurrentState(state);
		switch(state){
			case 'index':
				this._restoreInputFields('index');
				break;
			case 'new':
				this._restoreBrowse();
				this._restoreInput();
				this._restoreInputFields('new');
				break;
			case 'browse':
				this._restoreBrowse();
				break;
			case 'detail':
				this._restoreBrowse();
				this._restoreDetails();
				break;
			case 'edit':
				this._restoreBrowse();
				this._restoreDetails();
				this._restoreInput();
				this._restoreInputFields('edit');
				break;
		};
		this._restoreCompleters();
	},

	/**
	 * Invoca un procedimiento externamente
	 *
   	 * @this {HyperForm}
	 */
	externalProcedureCall: function(procedureName){
		switch(procedureName){
			case 'new':
				this._doNew();
				break;
			case 'edit':
			     this._doEdit();
			     break;
		}
	},

	/**
	 * Establece un valor en un campo del formulario activo
	 *
   	 * @this {HyperForm}
	 */
	setFieldValue: function(fieldId, value){
		switch(this._currentState){
			case 'new':
			case 'edit':
			case 'search':
				var field = this.getActiveSection().selectOne('#'+fieldId);
				if(field!==null){
					field.setValue(value);
				};
				break;
		}
	},

	/**
	 * Asigna los valores de un hash al formulario activo
	 *
   	 * @this {HyperForm}
	 */
	setFieldValues: function(data){
		switch(this._currentState){
			case 'new':
			case 'edit':
			case 'search':
				var activeSection = this.getActiveSection();
				$H(data).each(function(item){
					var field = activeSection.selectOne('#'+item[0]);
					if(field!==null){
						field.setValue(item[1]);
					};
				})
				break;
		}
	},

	/**
	 * Agrega un campo adicional a la forma del numero de reserva
	 *
   	 * @this {HyperForm}
	 */
	addFieldInput: function(elementId, caption, value, position){
		var hySaveForm = this.getActiveSection().getElement('hySaveForm');
		var table = hySaveForm.selectOne('table');
		if(table!==null){
			var html = '<tr><td align="right"><label for="'+elementId+'">'+caption+'</label></td>';
			html+='<td><input type="text" id="'+elementId+'" name="'+elementId+'" value="'+value+'" readonly/></td></tr>'
			table.selectOne('tbody').insert({ 'before': html });
		}
	},

	/**
	 * Agrega un campo adicional a la forma del numero de reserva
	 *
   	 * @this {HyperForm}
	 */
	addFieldLabelInput: function(elementId, caption, value, position, label){
		var hySaveForm = this.getActiveSection().getElement('hySaveForm');
		var trOld = this.getActiveSection().selectOne('#tr'+elementId);
		if(trOld){
		    trOld.remove();
		}
		var table = hySaveForm.selectOne('table');
		if(table!==null){
			var html = '<tr id="tr'+elementId+'"><td align="right"><label for="'+elementId+'">'+caption+'</label></td>';
			html+='<td><input type="hidden" id="'+elementId+'" name="'+elementId+'" value="'+value+'"/>';
			html+='<label>'+label+'</label></td></tr>'
			table.selectOne('tbody').insert({ 'before': html });
		}
	},

	/**
	 * Agrega un callback a un determinado evento
	 *
   	 * @this {HyperForm}
	 */
	observe: function(eventName, procedure)
	{
		if (Object.isUndefined(this['_' + eventName])) {
			this['_' + eventName] = [];
		};
		this['_' + eventName].push(procedure);
	},

	/**
	 * Ejecuta un evento del formulario
	 *
   	 * @this {HyperForm}
	 */
	fire: function(eventName)
	{
		try {
			if (!Object.isUndefined(this['_'+eventName])) {
				for(var i = 0;i < this['_'+eventName].length; i++) {
					if (this['_'+eventName][i].apply(this, arguments) === false) {
						return false;
					}
				};
				return true;
			} else {
				return true;
			}
		} catch(e) {
			HfosException.show(e);
		}
	},

	/**
	 * Devuelve el ID único del formulario
	 *
   	 * @this {HyperForm}
	 */
	getId: function(){
		return this._formElement.id;
	},

	/**
	 * Devuelve el nombre del formulario
	 *
   	 * @this {HyperForm}
	 */
	getName: function(){
		return this._formName
	},

	/**
	 * Devuelve el elemento DOM del formulario
	 *
   	 * @this {HyperForm}
	 */
	getFormElement: function(){
		return this._formElement;
	},

	/**
	 * Obtiene un elemento DOM debajo de this._formElement
	 *
   	 * @this {HyperForm}
	 */
	getElement: function(selector){
		return this._formElement.getElement(selector);
	},

	/**
	 * Obtiene el DIV activo de clase hyFormDiv en pantalla
	 *
   	 * @this {HyperForm}
	 */
	getActiveSection: function(){
		switch(this._currentState){
			case 'new':
				return this.getElement('hyNewDiv');
			case 'edit':
				return this.getElement('hyEditDiv');
			case 'search':
				return this.getElement('hySearchDiv');
			case 'detail':
				return this.getElement('hyDetailsDiv');
			default:
				return this._formElement;
		}
	},

	/**
	 * Obtiene la ventana donde se ejecuta el proceso
	 *
   	 * @this {HyperForm}
	 */
	getWindow: function(){
		if(this._window!==null){
			return this._window;
		} else {
			return document.body;
		}
	},

	/**
	 * Devuelve el elemento DOM del cotenido del formulario
	 *
   	 * @this {HyperForm}
	 */
	getContentElement: function(){
		if(this._window!==null){
			return this._window.getContentElement();
		} else {
			return this._formElement;
		}
	},

	/**
	 * Obtiene un conjunto de elementos DOM debajo de this._formElement
	 *
   	 * @this {HyperForm}
	 */
	select: function(selector){
		return this._formElement.select(selector);
	},

	/**
	 * Obtiene un el primer elemento DOM debajo de this._formElement según el selector
	 *
   	 * @this {HyperForm}
	 */
	selectOne: function(selector){
		return this._formElement.selectOne(selector);
	},

	/**
	 * Devuelve el elemento DOM de un campo del formulario
	 *
   	 * @this {HyperForm}
	 */
	getField: function(fieldName){
		return this._formElement.select('#'+fieldName)[0];
	},

	/**
	 * Devuelve la llave primaria del registro en pantalla
	 *
   	 * @this {HyperForm}
	 */
	getLastPrimary: function(){
		return this._lastPrimary;
	},

	/**
	 * Devuelve el objeto de mensajes del formulario
	 *
   	 * @this {HyperForm}
	 */
	getMessages: function(){
		return this._messages;
	},

	/**
	 * Devuelve el objeto que administra las pestañas del formulario
	 *
   	 * @this {HyperForm}
	 */
	getTabs: function(){
		return this._tabs;
	},

	/**
	 * Devuelve los eventos con enlace tardio para la grilla del formulario
	 *
   	 * @this {HyperForm}
	 */
	getLateBindings: function(){
		return HyperFormManager.getLateBindings(this._formName);
	},

	/**
	 * Devuelve los eventos con enlace tardio para la grilla del formulario
	 *
   	 * @this {HyperForm}
	 */
	getLateGridBindings: function(){
		return HyperFormManager.getLateGridBindings(this._formName);
	},

	/**
	 * Devuelve el estado actual del formulario: index, search, new, edit.
	 *
   	 * @this {HyperForm}
	 */
	getCurrentState: function(){
		return this._currentState;
	},

	/**
	 * Asigna el estado actual del formulario: index, search, new, edit.
	 *
   	 * @this {HyperForm}
	 */
	setCurrentState: function(state){
		this._currentState = state;
		if(this._window!==null){
			this._window.setState(this._currentState)
		};
	},

	/**
	 * Oculta la barra de estado
	 *
   	 * @this {HyperForm}
	 */
	hideStatusBar: function(){
		if(typeof this._window.hideStatusBar != "undefined"){
			this._window.hideStatusBar();
		} else {
			var statusBar = $('processStatusBar');
			if(statusBar){
				statusBar.hide();
			}
		}
	},

	/**
	 * Muestra la barra de estado y un mensaje HTML en ella
	 *
   	 * @this {HyperForm}
	 */
	showStatusBar: function(message){
		if(typeof this._window.showStatusBar != "undefined"){
			this._window.showStatusBar(message);
		} else {
			var statusBar = $('processStatusBar');
			if(!statusBar){
				statusBar = document.createElement('DIV');
				statusBar.setAttribute('id', 'processStatusBar');
				document.body.appendChild(statusBar);
			};
			statusBar.update(message);
		}
	},

	/**
	 * Ejecuta un selector CSS en la barra de estado
	 *
   	 * @this {HyperForm}
	 */
	getStatusBarElement: function(selector){
		if(typeof this._window.showStatusBar != "undefined"){
			return this._window.getStatusBar().getElement(selector);
		} else {
			var statusBar = $('processStatusBar');
			if(statusBar){
				return statusBar.getElement(selector);
			} else {
				return null;
			}
		}
	}

});

function hyOL(procedure){
	procedure.call();
}

