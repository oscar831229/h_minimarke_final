
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
 * OrdenesServicio
 *
 * Cada instancia de ordenes de servicio
 */
var OrdenesServicio = Class.create(HfosProcessContainer, {

	_key: null,

	_movimientos: [],

	_activeMov: 0,

	/**
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){

		var newButton = this.getElement('newButton');
		if(newButton!==null){
			newButton.observe('click', this._newOrden.bind(this));

			//Formulario de buscar
			new HfosForm(this, 'buscarForm', {
				update: 'resultados',
				onSuccess: function(response){

					switch(response.number){
						case '0':
							this.getMessages().notice('No se encontraron ordenes de servicio');
							break;

						case '1':
							this._key = response.key;
							this.go('ordenes/editar', {
								parameters: this._key,
								onSuccess: this._setEditCallbacks.bind(this)
							});
							break;

							break;
						case 'n':

							var browse = new HfosBrowseData(this, 7);

							browse.setEnableDeleteButton(false);
							browse.build(this.getElement('resultados'), response);

							var hyDetails = browse.getDetailsButtons();
							for(var i=0;i<hyDetails.length;i++){
								hyDetails[i].store('primary', hyDetails[i].title);
								hyDetails[i].title = 'Ver/Editar';
								hyDetails[i].observe('click', this._detailsHandler.bind(this, hyDetails[i]));
							};

							this.scrollToBottom();
							this._notifyContentChange();

							break;

					}
				}.bind(this)
			});
		};

		this.hideStatusBar();
	},

	_detailsHandler: function(element){
		this._key = element.retrieve('primary');
		this.go('ordenes/editar', {
			parameters: this._key,
			onSuccess: this._setEditCallbacks.bind(this)
		});
	},

	_newOrden: function(){
		this.go('ordenes/nueva', {
			onSuccess: this._setNewCallbacks.bind(this)
		});
	},

	_setEditCallbacks: function(){

		var searchButton = this.getElement('backButton');
		searchButton.observe('click', this._backToSearch.bind(this));

		var printButton = this.getElement('printButton');
		if(printButton!==null){
			printButton.observe('click', this._printOrden.bind(this));
		};

		var deleteButton = this.getElement('deleteButton');
		if(deleteButton!==null){
			deleteButton.observe('click', this._eliminaOrden.bind(this));

			var loadButton = this.getElement('loadButton');
			loadButton.observe('click', this._contabilizaOrden.bind(this));

			var saveButton = this.getElement('saveButton');
			saveButton.observe('click', this._saveOrden.bind(this, saveButton));

			this.selectOne('input#nit').activate();

			var lineas = this.select('tr.orden-linea');
			for(var i=0;i<lineas.length;i++){
				this._addGridCallbacks(i+1);
			};

			this._notifyContentChange();
			this.scrollToBottom();

			this._showTotalBar();
			this._totalizeOrden();

		};

	},

	/**
	 * Selecciona una fila del movimiento
	 */
	_selectRow: function(checkElement){
		var trElement = checkElement.up(1);
		if(!trElement.hasClassName('lineaError')){
			if(checkElement.checked){
				trElement.addClassName('selectedRow');
			} else {
				trElement.removeClassName('selectedRow');
			}
		};
		var numberChecked = 0;
		var checkElements = this.select('input[type="checkbox"]');
		for(var i=0;i<checkElements.length;i++){
			if(checkElements[i].checked){
				numberChecked++;
			}
		}
		var movimientoSeleccion = this.getStatusBarElement('movimientoSeleccion');
		if(numberChecked>0){
			movimientoSeleccion.show();
		} else {
			movimientoSeleccion.hide();
		};
	},

	abrirComprobante: function(){

	},

	_printOrden: function(){
		window.open(Utils.getKumbiaURL('ordenes/imprimir?codigoComprob='+this.selectOne('input#codigoComprob').getValue()+'&numero='+this.selectOne('input#numero').getValue()))
	},

	_onChangeFecha: function(){

	},

	_saveOrden: function(){
		var grabarForm = this.getElement('grabarForm')
		new HfosAjax.JsonFormRequest(grabarForm, {
			onCreate: function(grabarForm){
				this.getElement('headerSpinner').show();
				grabarForm.disable();
			}.bind(this, grabarForm),
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success(response.message);
				} else {
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					}
				}
			}.bind(this),
			onComplete: function(grabarForm){
				this.getElement('headerSpinner').hide();
				grabarForm.enable();
			}.bind(this, grabarForm)
		});
	},

	_setNewCallbacks: function(){

		var searchButton = this.getElement('backButton');
		searchButton.observe('click', this._backToSearch.bind(this));

		DateField.observe('fechaOrden', 'change', this._onChangeFecha.bind(this));

		var saveButton = this.getElement('saveButton');
		saveButton.observe('click', this._saveOrden.bind(this, saveButton));

		this.selectOne('input#nit').activate();

		this._addGridCallbacks(1);

		this._showTotalBar();

	},

	_showTotalBar: function(){

		//Mostrar barra de estado
		var html = '<table width="100%" class="movimientoStatusBar"><tr>';
		html+='<td align="left" style="display:none" class="movimientoSeleccion">Opciones de la Selección <select class="opcionesSeleccion">';
		html+='<option value="@">Seleccione...</option><option value="D">Eliminar</option>'
		html+='</select><td><td class="totalOrden" align="right"></td></tr></table>';
		this.showStatusBar(html);

		var opcionesSeleccion = this.getStatusBarElement('opcionesSeleccion');
		opcionesSeleccion.observe('change', function(element){
			try {
				switch($F(element)){
					case 'D':
						this._deleteRows();
						break;
				};
				element.setValue('@');
			}
			catch(e){
				HfosException.show(e);
			}
		}.bind(this, opcionesSeleccion));

		this._totalOrden = this.getStatusBarElement('totalOrden');
		this._totalOrden.update('');

	},

	/**
	 * Elimina las filas seleccionadas del movimiento
	 */
	_deleteRows: function(){
		var numberChecked = 0;
		var checkElements = this.select('input[type="checkbox"]');
		for(var i=0;i<checkElements.length;i++){
			if(checkElements[i].checked){
				numberChecked++;
			}
		};
		if(numberChecked>0){
			if(checkElements.length==numberChecked){
				new HfosModal.alert({
					title: 'Ordenes de servicio',
					message: 'La orden de servicio debe tener al menos un item'
				});
			} else {
				new HfosModal.confirm({
					title: 'Ordenes de servicio',
					message: 'Seguro desea eliminar los items seleccionados?',
					onAccept: function(){
						var rows = [];
						var checkElements = this.select('input[type="checkbox"]');
						for(var i=0;i<checkElements.length;i++){
							if(checkElements[i].checked){
								rows.push(checkElements[i].up(1).retrieve('position'));
							}
						};
						for(var i=0;i<rows.length;i++){
							var movimientoTr = this.getElement('linea'+rows[i]);
							movimientoTr.erase();
						};
						this._totalizeOrden();
					}.bind(this)
				});
			}
		}
	},

	_pushRowForUpdate: function(element){

		var position = element.up(1).retrieve('position');
		var nextPosition = position+1;
		var nextLinea = this.getElement('linea'+nextPosition);

		if(nextLinea===null){

			var trElement = document.createElement('TR');
			trElement.addClassName('linea'+nextPosition);

			var tdNumero = document.createElement('TD');
			tdNumero.addClassName('numero');
			tdNumero.update(nextPosition);
			trElement.appendChild(tdNumero);

			var tdCheck = document.createElement('TD');
			tdCheck.update('<input type="checkbox" class="itemCheck" id="itemCheck'+nextPosition+'"/>');
			trElement.appendChild(tdCheck);

			var tdItem = document.createElement('TD');
			tdItem.addClassName('numero');
			tdItem.update('<input type="text" class="item" name="item[]" id="item'+nextPosition+'" size="10"/>');
			trElement.appendChild(tdItem);

			var tdDescripcion = document.createElement('TD');
			tdDescripcion.addClassName('numero');
			tdDescripcion.update('<input type="text" class="descripcion" name="descripcion[]" id="descripcion'+nextPosition+'" size="27"/>');
			trElement.appendChild(tdDescripcion);

			var tdCentro = document.createElement('TD');
			tdCentro.addClassName('centros');

			var primerCentros = this.select('select.centroCosto')[0];
			tdCentro.appendChild(primerCentros.cloneNode(true));
			trElement.appendChild(tdCentro);

			var tdValor = document.createElement('TD');
			tdValor.update('<input type="text" class="valor numeric" name="valor[]" id="valor'+nextPosition+'" size="15"/>');
			trElement.appendChild(tdValor);

			this.getElement('ordenes-body').appendChild(trElement);

			this._addGridCallbacks(nextPosition);

			if(element.hasClassName('valor')){
				window.setTimeout(function(){
					var itemElement = this.select('input.item')[nextPosition-1];
					itemElement.activate();
				}.bind(this), 100);
			};

			this._notifyContentChange();
			this.scrollToBottom();
		}

	},

	_updateCodigoItem: function(elementItem, option){
		if(elementItem){
			elementItem.setValue(option.value);
		}
	},

	_addGridCallbacks: function(position){

		var lineaElement = this.getElement('linea'+position);
		lineaElement.store('position', position);

		var checkElement = lineaElement.getElement('itemCheck');
		checkElement.observe('change', this._selectRow.bind(this, checkElement));

		var descripcionElement = lineaElement.getElement('descripcion');
		descripcionElement.observe('blur', this._onBlurDescripcion.bind(this, descripcionElement));

		var itemElement = lineaElement.getElement('item');
		itemElement.observe('blur', this._onBlurItem.bind(this, itemElement, descripcionElement))

		new HfosAutocompleter(descripcionElement, 'refe/queryByDescription', {
			paramName: 'descripcion',
			afterUpdateElement: this._updateCodigoItem.bind(this, itemElement)
		});

		var valorElement = lineaElement.getElement('valor');
		valorElement.observe('blur', this._onBlurValor.bind(this, valorElement));

		var centrosElement = lineaElement.getElement('centroCosto');
		centrosElement.observe('blur', this._onChangeCentro.bind(this, centrosElement));

	},

	_onBlurItem: function(itemElement, descripcionElement){
		if(itemElement.getValue()!=''){
			new HfosAjax.JsonRequest('refe/getItem', {
				parameters: 'item='+itemElement.getValue(),
				onSuccess: function(itemElement, descripcionElement, response){
					if(response.status=='OK'){
						descripcionElement.setValue(response.descripcion);
						this.getMessages().setDefault();
						this._pushRowForUpdate(itemElement);
					} else {
						if(response.status=='FAILED'){
							this.getMessages().error(response.message)
						}
					}
				}.bind(this, itemElement, descripcionElement)
			});
		}
	},

	_onBlurDescripcion: function(descripcionElement){
		if(descripcionElement.getValue()!=''){
			this._pushRowForUpdate(descripcionElement);
		}
	},

	_onBlurValor: function(valorElement){
		if(valorElement.getValue()!=''){
			this._pushRowForUpdate(valorElement);
		};
		this._totalizeOrden();
	},

	_onChangeCentro: function(centroElement){
		this._pushRowForUpdate(centroElement);
	},

	_eliminaOrden: function(){
		new HfosModal.confirm({
			title: 'Ordenes de Servicio',
			message: 'Seguro desea eliminar la orden de servicio?',
			onAccept: function(){
				new HfosAjax.JsonRequest('ordenes/eliminar', {
					checkAcl: true,
					parameters: this._key,
					onSuccess: function(response){
						if(response.status=='FAILED'){
							this.getMessages().error(response.message);
						} else {
							this.getMessages().success(response.message);
							window.setTimeout(function(){
								this.go('ordenes/index', {
									onSuccess: this._setIndexCallbacks.bind(this)
								});
							}.bind(this), 1000);
						}
					}.bind(this)
				});
			}.bind(this)
		});
	},

	_contabilizaOrden: function(){
		new HfosModal.confirm({
			title: 'Ordenes de Servicio',
			message: 'Seguro desea contabilizar la orden de servicio?',
			onAccept: function(){
				new HfosAjax.JsonRequest('ordenes/contabilizar', {
					checkAcl: true,
					parameters: this._key,
					onSuccess: function(response){
						if(response.status=='FAILED'){
							this.getMessages().error(response.message);
						} else {
							this.getMessages().success(response.message);
						}
					}.bind(this)
				});
			}.bind(this)
		});
	},

	_totalizeOrden: function(){
		var valorElements = this.select('input.valor');
		var total = 0;
		for(var i=0;i<valorElements.length;i++){
			if(valorElements[i].getValue()!=''){
				total+=parseFloat(valorElements[i].getValue(), 10);
			}
		};
		total = Utils.numberFormat(total);
		this._totalOrden.update('<table class="sumasTable"><tr><td>Total Orden <b>'+total+'</b></td></tr></table>');
	},

	_backToSearch: function(){
		this.go('ordenes/index', {
			onSuccess: this._setIndexCallbacks.bind(this)
		});
	}

});

HfosBindings.late('win-ordenes-servicio', 'afterCreate', function(hfosWindow){
	var ordenesServicio = new OrdenesServicio(hfosWindow);
});
