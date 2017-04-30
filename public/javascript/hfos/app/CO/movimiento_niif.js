
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

var MovimientosNiif = {

	/**
	 * Abre un movimiento con el key dado
	 * codigoComprobante=V11&numero=100
	 */
	abrir: function(key){
		Hfos.getApplication().run({
			id: 'win-movimiento-niif',
			icon: 'document-library.png',
			title: "Movimiento Contable Niif",
			action: "movimiento_niif",
			height: '570px',
			onStartup: MovimientosNiif._openDocument.bind(this, key)
		});
	},

	_openDocument: function(key, _window){
		_window.fire('onCustomMessage', 'open', key);
	}

};

var MovimientoNiif = Class.create(HfosProcessContainer, {

	next: {
		'cuenta': 'nombreCuenta',
		'nombreCuenta': 'descripcion',
		'descripcion': 'valor',
		'valor': 'naturaleza'
	},

	prev: {
		'cuenta': 'naturaleza',
		'nombreCuenta': 'cuenta',
		'descripcion': 'nombreCuenta',
		'valor': 'descripcion'
	},

	//Comprobante que se está editando actualmente
	_key: null,

	//Encabezados de columnas
	_columnHeaders: [],

	//Cuerpo de la tabla grilla
	_sortTableBody: null,

	//Total de filas de la grilla de edición
	_totalRows: 0,

	//Registros que cambiaron y deben ser actualizados
	_rowsForUpdate: [],

	//Sumas iguales del comprobante
	_sumasComprobante: null,

	/**
	 * Constructor de Movimiento
	 *
	 * @constructor
	 */
	initialize: function(container){

		this.setContainer(container);

		container.observe('beforeClose', this._beforeCloseCallback.bind(this));
		container.observe('onInactive', this._onInactiveCallback.bind(this));
		container.observe('onKeyPress', this._onKeyPress.bind(this));
		container.observe('onCustomMessage', this._onCustomMessage.bind(this));

		if (container.wasRestored()==false){
			this._setIndexCallbacks();
		} else {
			var state = container.getState();
			switch(state){
				case 'index':
					this._setIndexCallbacks();
					break;
				case 'new':
					this._restoreNew();
					break;
			}
		};

		var containerElement = this.getContentElement();
		containerElement.observe('scroll', function(){
			HfosCuentasSelectores2.removeAll();
			this._removeDetails();
		}.bind(this));

		container.fire('onReady');

	},

	/**
	 * Establece la llave del comprobante que se está editando
	 */
	setKey: function(key){
		this._key = key;
		this.getContentElement().lang = key;
	},

	/**
	 * Indica si el modulo está listo para recibir peticiones
	 */
	isReady: function(){
		return this._isReady;
	},

	/**
	 * Destructor del objeto
	 */
	__destruct: function(){
		this._removeDetails();
		if (this.getState()=='edit'||this.getState()=='new'){
			var key = this._getKeyToClean();
			new HfosAjax.Request('movimiento_niif/clean', {
				parameters: key,
				checkAcl: true
			});
		};
		delete this._key;
	},

	/**
	 * Callback al cerrar la ventana
	 */
	_beforeCloseCallback: function(){
		if (this.getState()=='edit'||this.getState()=='new'){
			var changed = false;
			if (this.getState()=='edit'){
				changed = this.select('tr.lineaChanged').length;
			} else {
				changed = this.select('table.movimiento-grid tr').length;
			};
			if (changed){
				new HfosModal.confirm({
					title: 'Movimiento Contable Niif',
					message: 'Hay movimiento sin guardar. ¿Desea continuar?',
					onAccept: function(){
						this.__destruct();
						this.getContainer().close(true);
					}.bind(this)
				});
				return false;
			} else {
				this.__destruct();
				return true;
			}
		} else {
			this.__destruct();
			return true;
		}
	},

	/**
	 * Callback al volverse inactiva la ventana
	 */
	_onInactiveCallback: function(){
		this._removeDetails();
		HfosCuentasSelectores2.removeAll();
	},

	/**
	 * Recibe las teclas enviadas a la ventana
	 */
	_onKeyPress: function(container, event){
		var state = this.getState();
		if (state=='index'){
			if (event.ctrlKey&&event.keyCode==Event.KEY_N){
				this._newComprobante()
				new Event.stop(event);
				new Event.cancelBubble(event);
				return false;
			};
			return true;
		};
		if (state=='edit'||state=='new'){
			if (event.keyCode==Event.KEY_F7){
				var saveButton = this.getElement('saveButton');
				if (saveButton!==null){
					this._saveComprobante.bind(this, saveButton)();
				};
				new Event.stop(event);
				new Event.cancelBubble(event);
				return false;
			};
			if (event.keyCode==Event.KEY_F10){
				this._backComprobante();
				new Event.stop(event);
				new Event.cancelBubble(event);
				return false;
			};
		};
		if (state=='edit'){
			if (event.keyCode==Event.KEY_F2){
				this._cambiarFechaComprobante();
				new Event.stop(event);
				new Event.cancelBubble(event);
				return false;
			};
			if (event.keyCode==Event.KEY_F4){
				this._duplicaMovimiento();
				new Event.stop(event);
				new Event.cancelBubble(event);
				return false;
			};
			if (event.keyCode==Event.KEY_F8){
				this._printComprobante();
				new Event.stop(event);
				new Event.cancelBubble(event);
				return false;
			};
		};
		return true;
	},

	_abrirMovimiento: function(key){
		Hfos.getUI().blockInput();
		this.setKey(key);
		this.go('movimiento_niif/editar', {
			checkAcl: true,
			parameters: key,
			onSuccess: this._setEditCallbacks.bind(this)
		});
	},

	/**
	 * Ejecuta acciones de acuerdo a mensajes personalizados
	 */
	_onCustomMessage: function(_window, type, key){
		switch(type){
			case 'open':
				if (this.getState()=='edit'||this.getState()=='new'){
					var changed = false;
					if (this.getState()=='edit'){
						changed = this.select('tr.lineaChanged').length;
					} else {
						changed = this.select('table.movimiento-grid tr').length;
					};
					if (changed){
						new HfosModal.confirm({
							title: 'Movimiento Contable',
							message: 'Hay movimiento sin guardar, ¿Desea cargar el comprobante y descargar los cambios sin guardar?',
							onAccept: function(key){
								this._abrirMovimiento(key)
							}.bind(this, key)
						});
						return false;
					} else {
						this._abrirMovimiento(key)
					}
				} else {
					this._abrirMovimiento(key);
				};
				break;
			default:
				new HfosModal.alert({
					title: 'Movimiento Contable',
					message: 'No se ha implementado el tipo de mensaje "'+type+'"'
				});
		}
	},

	/**
	 * Callbacks de la pantalla de inicio
	 */
	_setIndexCallbacks: function(){

		var newButton = this.getElement('newButton');
		if (newButton !== null) {
			newButton.observe('click', this._newComprobante.bind(this));
		}

		//Formulario de buscar
		new HfosForm(this, 'buscarForm', {
			update: 'resultados',
			onSuccess: function(response){
				switch(response.number){
					case '0':
						this.getMessages().notice('No se encontraron movimientos');
						break;
					case '1':
						Hfos.getUI().blockInput();
						this.setKey(response.key);
						this.go('movimiento_niif/editar', {
							checkAcl: true,
							parameters: response.key,
							onSuccess: this._setEditCallbacks.bind(this)
						});
						break;
					case 'n':
						var browse = new HfosBrowseData(this, 7);
						browse.setEnableDeleteButton(false);
						resultados = this.getElement('resultados');
						if (resultados !== null) {
							browse.build(resultados, response);
							var hyDetails = browse.getDetailsButtons();
							for(var i=0;i<hyDetails.length;i++){
								hyDetails[i].store('primary', hyDetails[i].title);
								hyDetails[i].title = 'Ver/Editar';
								hyDetails[i].observe('click', this._detailsHandler.bind(this, hyDetails[i]));
							};
							var hyRows = browse.getRows();
							for(var i=0;i<hyRows.length;i++){
								hyRows[i].store('primary', hyRows[i].title);
								hyRows[i].title = '';
								hyRows[i].observe('dblclick', this._detailsHandler.bind(this, hyRows[i]));
							};
						}
						this._notifyContentChange();
						this.scrollToBottom();
						break;
				}
			}.bind(this)
		});

		this.setState('index');

		this._dropDetails();
		HfosCuentasSelectores2.removeAll();

		this.hideStatusBar();
	},

	/**
	 * Botón de editar al buscar
	 */
	_detailsHandler: function(hyDetail){
		Hfos.getUI().blockInput();
		var key = hyDetail.retrieve('primary');
		this.setKey(key);
		this.go('movimiento_niif/editar', {
			parameters: key,
			checkAcl: true,
			onSuccess: this._setEditCallbacks.bind(this)
		});
	},

	/**
	 * Obtiene y actualiza las sumas en pantalla
	 */
	_getSumas: function(){
		new HfosAjax.JsonRequest('movimiento_niif/getSumas', {
			parameters: this._key,
			checkAcl: true,
			onSuccess: function(response){
				this._updateSumas(response);
			}.bind(this)
		});
	},

	/**
	 * Agrega los callbacks en la función de "editar"
	 */
	_setEditCallbacks: function(){

		var backButton = this.getElement('backButton');
		if (backButton!==null){
			backButton.observe('click', this._backComprobante.bind(this));

			var saveButton = this.getElement('saveButton');
			if (saveButton!==null){
				saveButton.observe('click', this._saveComprobante.bind(this, saveButton));

				var deleteButton = this.getElement('deleteButton');
				if (deleteButton !== null) {
					deleteButton.observe('click', this._deleteComprobante.bind(this, deleteButton));
				}

				var revisionButton = this.getElement('revisionButton');
				if (revisionButton !== null) {
					revisionButton.observe('click', this._revisionesComprobante.bind(this));
				}

				var copyButton = this.getElement('copyButton');
				if (copyButton !== null) {
					copyButton.observe('click', this._copyComprobante.bind(this));
				}

				var printButton = this.getElement('printButton');
				if (printButton !== null) {
					printButton.observe('click', this._printComprobante.bind(this));
				}

				var dateButton = this.getElement('dateButton');
				if (dateButton !== null) {
					dateButton.observe('click', this._cambiarFechaComprobante.bind(this));
				}

				this._addGridCallbacks();
				this.scrollToTop();
				this._getSumas();
				this.setState('edit');
			}
		};

		Hfos.getUI().unblockInput();
	},

	/**
	 * Agrega los callbacks en la función de "nuevo"
	 */
	_setNewCallbacks: function(){

		var backButton = this.getElement('backButton');
		if (backButton !== null) {
			backButton.observe('click', this._backComprobante.bind(this));
		}

		var comprobante = this.selectOne('select#codigoComprobante');
		if (comprobante===null){
			return;
		} else {
			comprobante.observe('change', this._getComprobanteConsecutivo.bind(this, comprobante));
		};

		var saveButton = this.getElement('saveButton');
		if (saveButton !== null) {
			if (comprobante.getValue()=='@'){
				saveButton.hide();
			} else {
				saveButton.observe('click', this._saveComprobante.bind(this, saveButton));
			};
		}

		DateField.observe(this.selectOne('input#fecha'), 'change', this._onChangeFecha.bind(this));

		//Si existe la grilla agregar los callbacks
		var movimientoTable = this.getElement('movimiento-grid');
		if (movimientoTable!==null){
			var numero = this.selectOne('input#numero');
			this.setKey('codigoComprobante='+comprobante.getValue()+'&numero='+numero.getValue());
			this._addGridCallbacks();
			window.setTimeout(function(){
				var cuenta = this.select('input.cuenta')[0];
				cuenta.focus();
			}.bind(this), 100);
		} else {
			comprobante.focus();
		};

		this.setState('new');
	},

	/**
	 * Nuevo comprobante
	 */
	_newComprobante: function(){
		this.go('movimiento_niif/nuevo', {
			checkAcl: true,
			onSuccess: this._setNewCallbacks.bind(this)
		});
	},

	/**
	 * Obtiene la llave del temp que debe ser eliminada
	 */
	_getKeyToClean: function(){
		var comprobanteElement = this.selectOne('select#codigoComprobante');
		if (comprobanteElement!==null){
			var numeroElement = this.selectOne('input#numero');
			return 'codigoComprobante='+comprobanteElement.getValue()+'&numero='+numeroElement.getValue();
		} else {
			return this._key;
		}
	},

	/**
	 * Volver al inicio
	 */
	_backComprobante: function(){
		var key = this._getKeyToClean();
		this.go('movimiento_niif/index', {
			checkAcl: true,
			parameters: 'clean=1&'+key,
			onSuccess: this._setIndexCallbacks.bind(this)
		});
	},

	/**
	 * Guardar el comprobante
	 */
	_saveComprobante: function(saveButton){
		saveButton.disable();
		this._removeDetails();
		HfosCuentasSelectores2.removeAll();
		this.setIgnoreTermSignal(true);
		this.getElement("headerSpinner").show();
		new HfosAjax.JsonRequest('movimiento_niif/guardar', {
			parameters: this._key,
			checkAcl: true,
			onSuccess: function(saveButton, response){
				if (response.status=='OK'){
					window.setTimeout(function(message){
						var spinner = this.getElement("headerSpinner");
						if (spinner) {
							spinner.hide();
						};
						this.go('movimiento_niif/index', {
							checkAcl: true,
							parameters: this._key,
							onSuccess: function(){
								if (this.getState()=='new'){
									if (message) {
										this.getMessages().success(message);
									} else {
										this.getMessages().success('Se creó el comprobante correctamente');
									}

								} else {
									this.getMessages().success('Se actualizó el comprobante correctamente');
								};
								this._setIndexCallbacks();
							}.bind(this)
						});
					}.bind(this,response.message), 100)
				} else {
					this.getMessages().error(response.message);
					saveButton.enable();
				};
			}.bind(this, saveButton),
			onComplete: function(){
				var spinner = this.getElement("headerSpinner");
				if (spinner) {
					spinner.hide();
				};
				this.setIgnoreTermSignal(false);
			}.bind(this)
		});
	},

	/**
	 * Duplica un movimiento
	 */
	_duplicaMovimiento: function(){
		var numberChecked = 0;
		var checkElements = this.select('input[type="checkbox"]');
		for(var i=0;i<checkElements.length;i++){
			if (checkElements[i].checked){
				numberChecked++;
			}
		};
		if (numberChecked>0){
			if (checkElements.length==numberChecked){
				new HfosModal.alert({
					title: 'Movimiento Contable',
					message: 'El comprobante debe tener al menos un movimiento'
				});
			} else {
				new HfosModal.confirm({
					title: 'Movimiento Contable',
					message: 'Seguro desea copiar los movimientos seleccionados?',
					onAccept: function(){
						var rows = [];
						var checkElements = this.select('input[type="checkbox"]');
						for (var i = 0; i < checkElements.length; i++) {
							if (checkElements[i].checked) {
								rows.push(checkElements[i].up(1).retrieve('position'));
							}
						};
						new HfosAjax.JsonRequest('movimiento_niif/copiarLineas', {
							checkAcl: true,
							parameters: 'lineas=' + rows.join(',') + '&' + this._key,
							onSuccess: function(rows, response){
								if (response.status == 'OK') {
									if (this.getState() == 'new') {
										this.go('movimiento_niif/nuevo', {
											checkAcl: true,
											parameters: this._key,
											onSuccess: this._setNewCallbacks.bind(this)
										});
									} else {
										/*if (this.getState()=='edit'){
											var next = checkElements.length;
											var fields = ['cuenta','nombreCuenta','descripcion','valor','naturaleza'];
											for(row in rows) {
												this._addRow();
												var trElement = this.getElement("movimiento"+row);
												if (trElement) {
													for (field in fields) {

													}
												}
											}
										}*/
									}
								} else {
									this.getMessages().error(response.message);
								}
							}.bind(this, rows)
						});
					}.bind(this)
				});
			}
		}
	},

	/**
	 * Eliminar el comprobante
	 */
	_deleteComprobante: function(deleteButton)
	{
		new HfosModal.confirm({
			title: 'Movimiento Contable',
			message: 'Seguro desea eliminar el comprobante?',
			onAccept: function(){
				new HfosAjax.JsonRequest('movimiento_niif/eliminar', {
					parameters: this._key,
					checkAcl: true,
					onCreate: function(){
						this.setIgnoreTermSignal(true);
						this.getElement("headerSpinner").show();
						Hfos.getUI().blockInput();
					}.bind(this),
					onSuccess: function(response){
						if (response.status=='OK'){
							this.go('movimiento_niif/index', {
								checkAcl: true,
								parameters: this._key,
								onSuccess: function(){
									this._setIndexCallbacks();
									this.getMessages().success('Se eliminó el comprobante correctamente');
								}.bind(this)
							});
						} else {
							this.getMessages().error(response.message);
						}
					}.bind(this),
					onComplete: function(){
						var spinner = this.getElement("headerSpinner");
						this.setIgnoreTermSignal(false);
						if (spinner) {
							spinner.hide();
						};
						Hfos.getUI().unblockInput();
					}.bind(this)
				});
			}.bind(this)
		});
	},

	/**
	 * Copia un comprobante
	 */
	_copyComprobante: function(){
		new HfosModalForm(this, 'movimiento_niif/copiar', {
			checkAcl: true,
			parameters: this._key,
			beforeClose: function(form, canceled, response){
				if (canceled == false) {
					this.go('movimiento_niif/nuevo', {
						checkAcl: true,
						parameters: response.key,
						onSuccess: this._setNewCallbacks.bind(this)
					});
				}
			}.bind(this)
		});
	},

	/**
	 * Imprime el comprobante en pantalla
	 */
	_printComprobante: function()
	{
		this._removeDetails();
		HfosCuentasSelectores2.removeAll();
		var	changed = this.select('tr.lineaChanged').length;
		if (changed) {
			new HfosModal.confirm({
				title: 'Movimiento Contable',
				message: 'Hay movimiento sin guardar, este no se incluirá en el reporte. Desea continuar?',
				onAccept: function(){
					this._printInternal();
				}.bind(this)
			});
			return false;
		} else {
			this._printInternal();
		}
	},

	/**
	 * Imprime el comprobante en pantalla
	 */
	_printInternal: function()
	{
		new HfosModalForm(this, 'movimiento_niif/imprimir', {
			checkAcl: true,
			parameters: this._key,
			beforeClose: function(form, canceled, response){
				if (canceled == false) {
					if (typeof response.file != "undefined") {
						window.open($Kumbia.path+response.file);
					}
				}
			}.bind(this)
		});
	},

	/**
	 * Cambiar fecha al comprobante
	 */
	_cambiarFechaComprobante: function()
	{
		new HfosModalForm(this, 'movimiento_niif/cambiarFecha', {
			checkAcl: true,
			parameters: this._key,
			beforeClose: function(form, canceled, response) {
				if (canceled == false) {
					this.getMessages().success('Se realizó el cambio de fecha, para terminar haga click en "Guardar"', true);
				}
			}.bind(this)
		});
	},

	/**
	 * Obtiene el consecutivo del comprobante seleccionado
	 */
	_getComprobanteConsecutivo: function(element)
	{
		this.go('movimiento_niif/nuevo', {
			checkAcl: true,
			parameters: 'codigoComprobante=' + element.getValue(),
			onCreate: function(){
				Hfos.getUI().blockInput();
			},
			onSuccess: this._setNewCallbacks.bind(this),
			onComplete: function(){
				Hfos.getUI().unblockInput();
			}
		});
	},

	/**
	 * Agrega a una cola los registros que han cambiado para su actualización
	 */
	_pushRowForUpdate: function(element, position)
	{
		if (typeof position != "number") {
			var position = element.up(1).retrieve('consecutive');
		};
		if (typeof position == "undefined") {
			return;
		};
		var cuentaElement = this.select('input.cuenta')[position];
		if (typeof cuentaElement == "undefined") {
			return;
		};

		var cuenta = cuentaElement.getValue();
		if (cuenta == '') {
			return;
		};
		try {
			var row = {};
			var fechaElement = this.selectOne('input#fecha');
			if (fechaElement !== null) {
				row['fecha'] = fechaElement.getValue();
			};
			row['consecutivo'] = position;
			row['cuenta'] = cuenta;
			row['descripcion'] = this.select('input.descripcion')[position].getValue();
			row['valor'] = this.select('input.valor')[position].getValue();
			row['naturaleza'] = this.select('select.naturaleza')[position].getValue();
			var detailElement = this._getDetailElement(position);
			if (detailElement!=null){
				var nitElement = detailElement.selectOne('input#nit');
				if (nitElement!==null){
					row['nit'] = nitElement.getValue();
				};
				var centroCostoElement = detailElement.selectOne('select#centroCosto');
				if (centroCostoElement!==null){
					row['centroCosto'] = centroCostoElement.getValue();
				};
				var tipoDocumentoElement = detailElement.selectOne('select#tipoDocumento');
				if (tipoDocumentoElement!==null){
					row['tipoDocumento'] = tipoDocumentoElement.getValue();
				};
				var numeroDocumentoElement = detailElement.selectOne('input#numeroDocumento');
				if (numeroDocumentoElement!==null){
					row['numeroDocumento'] = numeroDocumentoElement.getValue();
				};
				var fechaVenceElement = detailElement.selectOne('input#fechaVence');
				if (fechaVenceElement!==null){
					row['fechaVence'] = fechaVenceElement.getValue();
				};
				var baseGravableElement = detailElement.selectOne('input#baseGravable');
				if (baseGravableElement!==null){
					row['baseGravable'] = baseGravableElement.getValue();
				};
			};

			if ((position+1)==this._totalRows){
				this._addRow();
			};
		}
		catch(e){
			HfosException.show(e);
		};
		var saveButton = this.getElement('saveButton');
		if (saveButton !== null) {
			saveButton.disable();
		}
		new HfosAjax.JsonRequest('movimiento_niif/guardarLinea?'+this._key, {
			parameters: row,
			checkAcl: true,
			onCreate: function(){
				Hfos.getUI().blockInput();
			},
			onException: function(e, transport){
				HfosException.show(e, transport);
			},
			onSuccess: function(saveButton, position, response){
				var cuentaElement = this.select('input.cuenta')[position];
				if (typeof cuentaElement != "undefined"){
					var trNode = cuentaElement.up(1);
					if (response.status=='FAILED'){
						if (position==trNode.retrieve('position')){
							trNode.addClassName('lineaError');
						};
						this.getMessages().error(response.message);
					} else {
						if (position==trNode.retrieve('position')){
							if (trNode.hasClassName('lineaError')){
								trNode.removeClassName('lineaError');
							}
						};
						if (this.getState()=='edit'){
							if (response.changed=='1'){
								trNode.addClassName('lineaChanged');
							} else {
								trNode.removeClassName('lineaChanged');
							};
						};
						this._updateSumas(response);
						this.getMessages().setDefault();
					};
					saveButton.enable();
				}
			}.bind(this, saveButton, position),
			onComplete: function(){
				Hfos.getUI().unblockInput();
			}
		});
	},

	/**
	 * Actualiza la barra de estado con las sumas de debitos y creditos
	 */
	_updateSumas: function(sumas){
		var html = '<table class="sumasTable"><tr><td>Débitos <b>'+sumas.debitos+'</b></td>';
		html+='<td>Créditos <b>'+sumas.creditos+'</b></td>';
		if (sumas.descuadre){
			html+='<td>Diferencia <b class="descuadre">'+sumas.diferencia+'</b></td>';
		} else {
			html+='<td>Diferencia <b>'+sumas.diferencia+'</b></td>';
		}
		html+='</tr></table>';
		this._sumasComprobante.update(html);
	},

	/**
	 * Cambio de Fecha
	 */
	_onChangeFecha: function(fecha)
	{
		new HfosAjax.JsonRequest('movimiento_niif/validarFecha', {
			parameters: this._key + '&fecha=' + fecha,
			checkAcl: true,
			onSuccess: function(response) {
				if (response.status != 'OK') {
					this.getMessages().error(response.message);
				} else {
					this.getMessages().setDefault();
				}
			}.bind(this)
		});
	},

	/**
	 * Agrega una fila
	 */
	_addRow: function()
	{

		var numero = this._totalRows;

		//Fila
		var trNode = document.createElement('TR');
		trNode.addClassName('movimiento'+numero);
		trNode.addClassName('naturalezaD');
		trNode.store('position', numero);
		trNode.store('consecutive', numero);

		//Número
		var tdElement = document.createElement('TD');
		tdElement.addClassName('numero');
		tdElement.store('position', numero);
		tdElement.update(numero+1);
		tdElement.observe('click', this._onClickRowNumber.bind(this, tdElement));
		trNode.appendChild(tdElement);

		//Cuenta Check
		tdElement = document.createElement('TD');
		tdElement.update('<input id="cuentaCheck" type="checkbox" name="cuentaCheck">');
		trNode.appendChild(tdElement);

		//Cuenta
		tdElement = document.createElement('TD');
		var cuentaElement = document.createElement('INPUT');
		cuentaElement.setAttribute('type', 'text');
		cuentaElement.setAttribute('id', 'cuenta'+numero);
		cuentaElement.setAttribute('name', 'cuenta'+numero);
		cuentaElement.setAttribute('size', 10);
		cuentaElement.addClassName('cuenta');
		cuentaElement.observe('keydown', NumericField.maskNum);
		cuentaElement.observe('focus', this._onFocusCuenta.bind(this, cuentaElement));
		tdElement.appendChild(cuentaElement);
		trNode.appendChild(tdElement);

		//Nombre Cuenta
		tdElement = document.createElement('TD');
		var nombreCuentaElement = document.createElement('INPUT');
		nombreCuentaElement.setAttribute('type', 'text');
		nombreCuentaElement.setAttribute('id', 'nombreCuenta'+numero);
		nombreCuentaElement.setAttribute('name', 'nombreCuenta'+numero);
		nombreCuentaElement.setAttribute('size', 25);
		nombreCuentaElement.addClassName('nombreCuenta');
		nombreCuentaElement.observe('keydown', this._onKeyDownNombreCuenta.bind(this, nombreCuentaElement));
		nombreCuentaElement.observe('blur', this._onBlurNombreCuenta.bind(this, nombreCuentaElement));
		tdElement.appendChild(nombreCuentaElement);
		trNode.appendChild(tdElement);

		//Descripción
		tdElement = document.createElement('TD');
		var descripcionElement = document.createElement('INPUT');
		descripcionElement.setAttribute('type', 'text');
		descripcionElement.setAttribute('id', 'descripcion'+numero);
		descripcionElement.setAttribute('name', 'descripcion'+numero);
		descripcionElement.setAttribute('size', 25);
		descripcionElement.setAttribute('autocomplete', 'off');
		descripcionElement.addClassName('descripcion');
		descripcionElement.observe('focus', this._onFocusDescripcion.bind(this, descripcionElement));
		tdElement.appendChild(descripcionElement);
		trNode.appendChild(tdElement);

		//Valor
		tdElement = document.createElement('TD');
		var valorElement = document.createElement('INPUT');
		valorElement.setAttribute('type', 'text');
		valorElement.setAttribute('id', 'valor'+numero);
		valorElement.setAttribute('name', 'valor'+numero);
		valorElement.setAttribute('size', 13);
		valorElement.setAttribute('maxlength', 17);
		valorElement.setAttribute('autocomplete', 'off');
		valorElement.addClassName('valor');
		tdElement.appendChild(valorElement);
		trNode.appendChild(tdElement);

		//Naturaleza
		tdElement = document.createElement('TD');
		var naturalezaElement = document.createElement('SELECT');
		naturalezaElement.setAttribute('id', 'naturaleza'+numero);
		naturalezaElement.setAttribute('name', 'naturaleza'+numero);
		naturalezaElement.addClassName('naturaleza');
		naturalezaElement.update('<option value="D">DEBITO</option><option value="C">CREDITO</option>');
		naturalezaElement.observe('change', this._onChangeNaturaleza.bind(this, naturalezaElement));
		naturalezaElement.observe('blur', this._onBlurNaturaleza.bind(this, naturalezaElement));
		tdElement.appendChild(naturalezaElement);
		trNode.appendChild(tdElement);

		this._sortTableBody.appendChild(trNode);

		//Todas las cajas y combos
		var inputElements = trNode.select('input[type="text"], select');
		for(var i=0;i<inputElements.length;i++){
			inputElements[i].store('navEnabled', true);
			inputElements[i].observe('blur', this._pushRowForUpdate.bind(this, inputElements[i]));
			inputElements[i].observe('focus', this._checkForActiveDetails.bind(this, inputElements[i]));
			inputElements[i].observe('keyup', this._onKeyUpTextBox.bind(this, null, inputElements[i]));
		};
		this._totalRows++;
		this._notifyContentChange();
	},

	/**
	 * Selecciona una fila del movimiento
	 */
	_selectRow: function(checkElement){
		var trElement = checkElement.up(1);
		if (!trElement.hasClassName('lineaError')){
			if (checkElement.checked){
				trElement.addClassName('selectedRow');
			} else {
				trElement.removeClassName('selectedRow');
			}
		};
		var numberChecked = 0;
		var checkElements = this.select('input[type="checkbox"]');
		for(var i=0;i<checkElements.length;i++){
			if (checkElements[i].checked){
				numberChecked++;
			}
		}
		var movimientoSeleccion = this.getStatusBarElement('movimientoSeleccion');
		if (numberChecked>0){
			movimientoSeleccion.show();
		} else {
			movimientoSeleccion.hide();
		};
		this._removeDetails();
		HfosCuentasSelectores2.removeAll();
	},

	/**
	 * Elimina las filas seleccionadas del movimiento
	 */
	_deleteRows: function(){
		var numberChecked = 0;
		var checkElements = this.select('input[type="checkbox"]');
		for(var i=0;i<checkElements.length;i++){
			if (checkElements[i].checked){
				numberChecked++;
			}
		};
		if (numberChecked>0){
			if (checkElements.length==numberChecked){
				new HfosModal.alert({
					title: 'Movimiento Contable',
					message: 'El comprobante debe tener al menos un movimiento'
				});
			} else {
				new HfosModal.confirm({
					title: 'Movimiento Contable',
					message: 'Seguro desea eliminar los movimientos seleccionados?',
					onAccept: function(){
						var rows = [];
						var checkElements = this.select('input[type="checkbox"]');
						for(var i=0;i<checkElements.length;i++){
							if (checkElements[i].checked){
								rows.push(checkElements[i].up(1).retrieve('position'));
							}
						};
						new HfosAjax.JsonRequest('movimiento_niif/borrarLineas', {
							checkAcl: true,
							parameters: 'lineas='+rows.join(',')+'&'+this._key,
							onSuccess: function(rows, response){
								if (response.status=='OK'){
									HfosCuentasSelectores2.removeAll();
									this._removeDetails();
									for(var i=0;i<rows.length;i++){
										var movimientoTr = this.getElement('movimiento'+rows[i]);
										if (movimientoTr!==null){
											movimientoTr.hide();
										}
									};
									var tdNumeros = this.select('td[class="numero"]');
									for(var i=0;i<tdNumeros.length;i++){
										tdNumeros[i].update(i+1);
									};
									this._updateSumas(response);
									this.getStatusBarElement('movimientoSeleccion').hide();
									this.getMessages().setDefault();
								} else {
									this.getMessages().error(response.message);
								}
							}.bind(this, rows)
						});
					}.bind(this)
				});
			}
		}
	},

	/**
	 * Agrega los eventos a los elementos de la grilla
	 */
	_addGridCallbacks: function(){

		//Posiciones de las filas
		var movimientoTable = this.getElement('movimiento-grid');
		if (movimientoTable){
			var trRows = movimientoTable.tBodies[0].select('tr');
			this._totalRows = trRows.length;
			for(var i=0;i<this._totalRows;i++){
				trRows[i].store('position', i);
				trRows[i].store('consecutive', i);
			}
		};

		//Números de las filas
		var numerosTds = this.select('td.numero');
		for(var i=0;i<numerosTds.length;i++){
			numerosTds[i].store('position', i);
			numerosTds[i].observe('click', this._onClickRowNumber.bind(this, numerosTds[i]));
		};
		//delete numeroTds;

		//Todas las cajas y combos
		var inputElements = this.select('input[type="text"], select');
		for(var i=0;i<inputElements.length;i++){
			inputElements[i].store('navEnabled', true);
			inputElements[i].setAttribute('autocomplete', 'off');
			inputElements[i].observe('blur', this._pushRowForUpdate.bind(this, inputElements[i]));
			inputElements[i].observe('focus', this._checkForActiveDetails.bind(this, inputElements[i]));
			inputElements[i].observe('keyup', this._onKeyUpTextBox.bind(this, null, inputElements[i]));
		};
		//delete inputElements;

		//checkElements
		var checkElements = this.select('input[type="checkbox"]');
		for(var i=0;i<checkElements.length;i++){
			checkElements[i].observe('change', this._selectRow.bind(this, checkElements[i]));
		};
		//delete checkElements;

		//Cuentas
		var cuentas = this.select('input.cuenta');
		for(var i=0;i<cuentas.length;i++){
			cuentas[i].observe('focus', this._onFocusCuenta.bind(this, cuentas[i]));
		};
		//delete cuentas;

		//Descripciones
		var descripciones = this.select('input.descripcion');
		for(var i=0;i<descripciones.length;i++){
			descripciones[i].observe('focus', this._onFocusDescripcion.bind(this, descripciones[i]));
		};
		//delete descripciones;

		//Naturalezas
		var naturalezas = this.select('select.naturaleza');
		for(var i=0;i<naturalezas.length;i++){
			naturalezas[i].observe('change', this._onChangeNaturaleza.bind(this, naturalezas[i]));
			naturalezas[i].observe('blur', this._onBlurNaturaleza.bind(this, naturalezas[i]));
		};
		//delete naturalezas;

		//Nombre de Cuentas
		var nombreCuentas = this.select('input.nombreCuenta');
		for(var i=0;i<nombreCuentas.length;i++){
			nombreCuentas[i].observe('keydown', this._onKeyDownNombreCuenta.bind(this, nombreCuentas[i]));
			nombreCuentas[i].observe('blur', this._onBlurNombreCuenta.bind(this, nombreCuentas[i]));
		};
		//delete nombreCuentas;

		//Ordenamiento de encabezados de columnas
		var columnHeaders = this.select('th');
		this._columnHeaders = [];
		for(var i=0;i<columnHeaders.length;i++){
			if (columnHeaders[i].hasClassName('sortcol')){
				columnHeaders[i].observe('click', this._columnSortHandler.bind(this, columnHeaders[i], i));
				this._columnHeaders.push(columnHeaders[i]);
			}
		};
		this._sortTableBody = movimientoTable.tBodies[0];
		//delete columnHeaders;

		//Mostrar barra de estado
		var html = '<table width="100%" class="movimientoStatusBar"><tr>';
		html+='<td align="left" style="display:none" class="movimientoSeleccion">Opciones de la Selección <select class="opcionesSeleccion">';
		html+='<option value="@">Seleccione...</option><option value="D">Eliminar</option><option value="C">Copiar</option>'
		html+='</select><td><td class="sumasComprobante" align="right"></td></tr></table>';
		this.showStatusBar(html);

		var opcionesSeleccion = this.getStatusBarElement('opcionesSeleccion');
		opcionesSeleccion.observe('change', function(element){
			try {
				switch($F(element)){
					case 'D':
						this._deleteRows();
						break;
					case 'C':
						this._duplicaMovimiento();
						break;
				};
				element.setValue('@');
			}
			catch(e){
				HfosException.show(e);
			}
		}.bind(this, opcionesSeleccion));

		this._sumasComprobante = this.getStatusBarElement('sumasComprobante');
		this._sumasComprobante.update('');

	},

	/**
	 * Evento al hacer click en el número de una fila
	 */
	_onClickRowNumber: function(element){
		var position = parseInt(element.retrieve('position'), 10);
		var cuentaElement = this.select('input.cuenta')[position];
		if (typeof cuentaElement != "undefined"){
			cuentaElement.focus();
		}
	},

	/**
	 * Evento al cambiar el valor de naturaleza
	 */
	_onChangeNaturaleza: function(element){
		var trNode = element.up(1);
		if (element.getValue()=='D'){
			trNode.removeClassName('naturalezaC');
			trNode.addClassName('naturalezaD');
		} else {
			trNode.removeClassName('naturalezaD');
			trNode.addClassName('naturalezaC');
		};
		element.focus();
	},

	/**
	 * Evento al dejar el campo naturaleza
	 */
	_onBlurNaturaleza: function(element)
	{
		var position = parseInt(element.up(1).retrieve('position'), 10);
		position++;
		var cuentaElement = this.select('input.cuenta')[position];
		if (typeof cuentaElement != "undefined"){
			cuentaElement.focus();
		}
	},

	_columnSortHandler: function(columnHeader, columnNumber)
	{
		HfosCuentasSelectores2.removeAll();
		this._removeDetails();
		var wasAscending = columnHeader.hasClassName('sortasc');
		for(var i=0;i<this._columnHeaders.length;i++){
			this._columnHeaders[i].removeClassName('sortasc');
			this._columnHeaders[i].removeClassName('sortdesc');
		};
		if (wasAscending){
			this._sortColumn(columnNumber, false);
			columnHeader.addClassName('sortdesc');
		} else {
			this._sortColumn(columnNumber, true);
			columnHeader.addClassName('sortasc');
		}
	},

	/**
	 * Handlers de navegación en la grilla
	 */
	_onKeyUpTextBox: function(observer, element, event)
	{
		if (event.keyCode!=Event.KEY_UP && event.keyCode!=Event.KEY_DOWN&&
			event.keyCode!=Event.KEY_LEFT && event.keyCode!=Event.KEY_RIGHT&&
			event.keyCode!=Event.KEY_PAGEUP && event.keyCode!=Event.KEY_PAGEDOWN&&
			event.keyCode!=Event.KEY_RETURN) {
			return;
		};
		if (element.retrieve('navEnabled')===false) {
			return;
		};
		if (event.keyCode==Event.KEY_UP) {
			var position = parseInt(element.up(1).retrieve('position'), 10);
			position--;
			var focusElement = this.select('.'+element.className)[position];
			if (focusElement) {
				this._activate(focusElement);
			};
			if (position<=0) {
				this.scrollToTop();
			};
			new Event.stop(event);
			return;
		};
		if (event.keyCode==Event.KEY_DOWN){
			var position = parseInt(element.up(1).retrieve('position'), 10);
			if (element.hasClassName('cuenta')||element.hasClassName('nombreCuenta')){
				var detailElements = this.getSpaceElement().select('div.movimiento-detail');
				for(var i=0;i<detailElements.length;i++){
					if (detailElements[i].retrieve('position')==position){
						if (detailElements[i].visible()==true){
							var nitElement = detailElements[i].selectOne('input#nit');
							if (nitElement!==null){
								nitElement.focus();
								new Event.stop(event);
								return;
							};
							var centroCostoElement = detailElements[i].selectOne('select#centroCosto');
							if (centroCostoElement!==null){
								centroCostoElement.focus();
								new Event.stop(event);
								return;
							};
							break;
						}
					}
				}
			};
			position++;
			var focusElement = this.select('.'+element.className)[position];
			if (focusElement){
				this._activate(focusElement);
			};
			new Event.stop(event);
			return;
		};
		if (event.keyCode==Event.KEY_RIGHT){
			if (element.visible()==true){
				if (element.selectionStart==element.value.length){
					if (element.retrieve('moveNext')==1){
						var position = parseInt(element.up(1).retrieve('position'), 10);
						var focusElement = this.select('.'+this.next[element.className])[position];
						if (focusElement){
							this._activate(focusElement);
						};
						new Event.stop(event);
					} else {
						element.store('moveNext', 1);
					};
					return;
				}
			}
		};
		if (event.keyCode==Event.KEY_LEFT){
			if (element.visible()==true){
				if (element.selectionStart==0){
					if (element.retrieve('movePrev')==1){
						var position = parseInt(element.up(1).retrieve('position'), 10);
						var focusElement = this.select('.'+this.prev[element.className])[position];
						if (focusElement){
							this._activate(focusElement);
						};
						new Event.stop(event);return;
					} else {
						element.store('movePrev', 1);
					}
					return;
				}
			}
		};
		if (event.keyCode == Event.KEY_RETURN){
			var next, position = parseInt(element.up(1).retrieve('position'), 10);
			if (typeof this.next[element.className] == "undefined"){
				next = 'cuenta';
				position++;
			} else {
				next = this.next[element.className];
			};
			var focusElement = this.select('.'+next)[position];
			if (focusElement){
				this._activate(focusElement);
			};
			new Event.stop(event);
		};
		if (event.keyCode == Event.KEY_PAGEUP){
			var position = parseInt(element.up(1).retrieve('position'), 10);
			position-=13;
			if (position < 0){
				position = 0;
			};
			var focusElement = this.select('.'+element.className)[position];
			if (focusElement){
				this._activate(focusElement);
			};
			if (position <= 5){
				this.scrollToTop();
			};
			new Event.stop(event);
			return;
		};
		if (event.keyCode == Event.KEY_PAGEDOWN){
			var position = parseInt(element.up(1).retrieve('position'), 10);
			position += 13;
			if (position > this._totalRows){
				position = this._totalRows - 1;
			};
			var focusElement = this.select('.'+element.className)[position];
			if (focusElement){
				this._activate(focusElement);
			};
			new Event.stop(event);
			return;
		};
		element.store('moveNext', undefined);
		element.store('movePrev', undefined);
	},

	/**
	 * Realiza el foco sobre un elemento de la grilla movimiento el scroll del contenedor también
	 */
	_activate: function(element)
	{
		var position = element.positionedOffset();
		var contentElement = this.getContentElement();
		var scrollTop = position[1] - (contentElement.getHeight() * 0.77);
		if (scrollTop > 0){
			contentElement.scrollTop = scrollTop;
		};
		element.activate();
		this._removeDetails();
	},

	/**
	 * Ordena una columna
	 */
	_sortColumn: function(columnNumber, ascending)
	{
		var order = [];
		var tdElements = this._sortTableBody.select('td:nth-of-type('+(columnNumber+2)+')');
		for (var i = 0;i < tdElements.length; i++){
			order.push(tdElements[i]);
		};
		if (ascending == true){
			order.sort(this._ascendingSort);
		} else {
			order.sort(this._descendingSort);
		};
		for (var i = 0; i < order.length; i++) {
			var trNode = order[i].parentNode;
			trNode.store('position', i);
			this._sortTableBody.appendChild(trNode);
		}
	},

	/**
	 * Callback para ordenamiento ascendente
	 */
	_ascendingSort: function(ae, be)
	{
		if (ae.firstDescendant() && be.firstDescendant()){
			var a = ae.firstDescendant().getValue();
			var b = be.firstDescendant().getValue();
			if (a==''){
				return 1;
			} else {
				if (b==''){
					return -1;
				}
			};
			if (a < b){
				return -1;
			} else {
				if (a > b){
					return 1;
				} else {
					return 0;
				}
			}
		} else {
			return 0;
		}
	},

	/**
	 * Callback para ordenamiento descendente
	 */
	_descendingSort: function(ae, be)
	{
		if (ae.firstDescendant() && be.firstDescendant()){
			var a = ae.firstDescendant().getValue();
			var b = be.firstDescendant().getValue();
			if (a > b){
				return -1;
			} else {
				if (a < b){
					return 1;
				} else {
					return 0;
				}
			}
		} else {
			return 0;
		}
	},

	_getDetailElement: function(position)
	{
		return this.getSpaceElement().selectOne('div#detail'+position);
	},

	/**
	 * Oculta todos los pop-up de detalles
	 */
	_removeDetails: function()
	{
		var detailElements = this.getSpaceElement().select('div.movimiento-detail');
		for (var i = 0; i < detailElements.length; i++) {
			detailElements[i].hide();
		};
	},

	/**
	 * Elimina todos los pop-up de detalles
	 */
	_dropDetails: function()
	{
		var detailElements = this.getSpaceElement().select('div.movimiento-detail');
		for (var i = 0; i < detailElements.length; i++) {
			detailElements[i].erase();
		};
	},

	/**
	 * Evento al cambiar el valor del selector de cuentas
	 */
	_changeCuentaSelector: function(selector, element){
		this._removeDetails();
		this._pushRowForUpdate(element);
	},

	/**
	 * Verifica si hay cuadros de detalle abiertos y los oculta
	 */
	_checkForActiveDetails: function(element){
		var position = parseInt(element.up(1).retrieve('position'), 10);
		var detailElements = this.getSpaceElement().select('div.movimiento-detail');
		for(var i=0;i<detailElements.length;i++){
			if (position!=detailElements[i].retrieve('position')){
				detailElements[i].hide();
			}
		}
	},

	/**
	 * Evento al ubicarse en cualquiera de los campos descripción de la grilla
	 */
	_onFocusDescripcion: function(element)
	{
		if (element.value.strip()==''){
			window.setTimeout(function(){
				var position = parseInt(element.up(1).retrieve('position'), 10);
				var nombreCuentaElement = this.select('input.nombreCuenta')[position];
				if (typeof nombreCuentaElement != "undefined"){
					var nombreCuenta = nombreCuentaElement.getValue();
					if (nombreCuenta.include('EXISTE')==false){
						element.setValue(nombreCuenta);
					}
				}
			}.bind(this), 100);
		}
	},

	_onFocusCuenta: function(element){

		this._removeDetails();

		if (element.getValue()!=''){
			var detailElement = this._getDetailElement(position);
			if (!detailElement){
				this._showDetalles(element);
			} else {
				detailElement.show();
			}
		};

		var position = parseInt(element.up(1).retrieve('position'), 10);
		var elementDetalle = this.select('input.nombreCuenta')[position];
		var cuentaSelector = new HfosCuentasSelector2(element, elementDetalle);

		cuentaSelector.observe('change', this._changeCuentaSelector.bind(this));
		cuentaSelector.observe('leave', this._leaveCuentaSelector.bind(this));
		cuentaSelector.observe('keyup', this._onKeyUpTextBox.bind(this));

		element.store('selector', cuentaSelector);
	},

	/**
	 * Obtiene los atributos extendidos de un elemento
	 */
	_showDetalles: function(element)
	{
		var position = parseInt(element.up(1).retrieve('position'), 10);
		new HfosAjax.Request('movimiento_niif/getDetalles', {
			method: 'GET',
			checkAcl: true,
			parameters: 'cuenta='+element.getValue()+'&consecutivo='+position+'&'+this._key,
			onSuccess: function(element, transport){
				if (transport.responseText!=''){

					var contentElement = this.getContentElement();
					var position = parseInt(element.up(1).retrieve('position'), 10);

					var detailElement = this._getDetailElement(position);
					if (!detailElement){
						detailElement = document.createElement('DIV');
						detailElement.setAttribute('id', 'detail'+position);
						detailElement.store('position', position);
						detailElement.addClassName('movimiento-detail');
						this.getSpaceElement().appendChild(detailElement);
						new HfosDraggable(detailElement);
					} else {
						detailElement.show();
					};
					detailElement.update(transport.responseText);

					//Establecer navegación y foco en caso que esté presente el tercero
					var nitElement = detailElement.selectOne('input#nit');
					if (nitElement!==null){

						var nitNombreElement = detailElement.selectOne('input#nitNombre');
						nitElement.store('position', position);
						nitElement.observe('blur', this._queryByNit.bind(this, nitElement, nitNombreElement));
						nitElement.observe('keydown', this._nitNavigation.bind(this, nitElement, nitNombreElement, element));

						nitNombreElement.store('position', position);
						nitNombreElement.observe('blur', this._nitNombreNext.bind(this, nitNombreElement));
						nitNombreElement.observe('keydown', this._nitNombreNavigation.bind(this, nitElement, nitNombreElement, element));

						var crearNitElement = detailElement.selectOne('input#crearNit');
						crearNitElement.store('position', position);
						crearNitElement.observe('click', this._showCrearNit.bind(this, crearNitElement));

						var activeElement = document.activeElement;
						if (activeElement!=nitElement){
							if (nitElement.getValue()==''){
								window.setTimeout(function(nitElement){
									nitElement.focus();
								}.bind(this, nitElement), 50);
							}
						}

					};

					//Establecer navegación y foco en caso que esté presente el centro de costo
					var centroCostoElement = detailElement.selectOne('select#centroCosto');
					if (centroCostoElement!==null){
						centroCostoElement.store('position', position);
						centroCostoElement.observe('blur', this._centroCostoNavigation.bind(this, centroCostoElement));

						if (nitElement===null){
							if (centroCostoElement.getValue()=="@"){
								centroCostoElement.focus();
							}
						}
					};

					//Establecer navegación y foco en caso que esté presente el tipo de documento
					var tipoDocumentoElement = detailElement.selectOne('select#tipoDocumento');
					if (tipoDocumentoElement!==null){

						tipoDocumentoElement.store('position', position);
						tipoDocumentoElement.observe('blur', this._tipoDocumentoNavigation.bind(this, tipoDocumentoElement));

						var numeroDocumentoElement = detailElement.selectOne('input#numeroDocumento');
						numeroDocumentoElement.store('position', position);
						numeroDocumentoElement.observe('blur', this._numeroDocumentoNavigation.bind(this, numeroDocumentoElement));

						var verCarteraElement = detailElement.selectOne('input#verCartera');
						if (nitElement!==null){
							if (nitElement.getValue()!==''){
								verCarteraElement.show();
							} else {
								verCarteraElement.hide();
							};
							verCarteraElement.store('position', position);
							verCarteraElement.observe('click', this._verCartera.bind(this, verCarteraElement));
						} else {
							verCarteraElement.hide();
						};

						if (centroCostoElement===null){
							if (tipoDocumentoElement.getValue()=="@"){
								tipoDocumentoElement.focus();
							}
						}

					};

					//Establecer navegación y foco en caso que esté presente la base gravable
					var baseGravableElement = detailElement.selectOne('input#baseGravable');
					if (baseGravableElement!==null){

						baseGravableElement.store('position', position);
						baseGravableElement.observe('blur', this._baseGravableNavigation.bind(this, baseGravableElement));

						if (centroCostoElement===null){
							if (baseGravableElement.getValue()=="@"){
								baseGravableElement.focus();
							}
						}
					}

					var container = this.getContainer();
					var positionedOffset = element.up().positionedOffset();
					var contentOffset = contentElement.positionedOffset();
					var top =  positionedOffset[1]-contentElement.scrollTop+container.getTop()+4;
					var left = positionedOffset[0]+container.getLeft();
					detailElement.setStyle({
						'top': (top+element.getHeight())+'px',
						'left': left+'px'
					});

				}
			}.bind(this, element)
		});
	},

	/**
	 * Verifica si se puede visualizar el botón de Ver Cartera
	 */
	_showCarteraButton: function(){

	},

	/**
	 * Actualiza el número del NIT desde el autocompleter de nombres
	 */
	_updateNumeroNit: function(nitNombre, nitNumero, option)
	{
		if (nitNumero){
			nitNumero.setValue(option.value);
			nitNombre.store('completer', undefined);
		}
	},

	/**
	 * Muestra el listado de cartera del tercero seleccionado
	 */
	_verCartera: function(verCartera)
	{

		var position = parseInt(verCartera.retrieve('position'), 10);
		var detailElement = this._getDetailElement(position);
		var nitElement = detailElement.selectOne('input#nit');
		var cuentaElement = this.select('input.cuenta')[position];

		if (typeof nitElement == "undefined") {
			return null;
		}
		if (typeof cuentaElement == "undefined") {
			return null;
		}

		if (nitElement.getValue() !== '') {
			new HfosModalForm(this, 'movimiento_niif/verCartera', {
				parameters: {
					'nit': nitElement.getValue(),
					'cuenta': cuentaElement.getValue()
				},
				style: {
					'width': '600px'
				},
				afterShow: function(form){
					var carteraTable = form.selectOne('table#carteraTable');
					if (carteraTable != null) {
						var browse = new HfosBrowseData(this);
						browse.fromHtmlTable(form, carteraTable, 12);
					};
				}.bind(this),
				beforeClose: function(detailElement, position, form, canceled, response){
					if (canceled == false) {
						if (response.status == 'OK') {
							if (typeof response.cartera != "undefined") {

								var tipoDocumentoElement = detailElement.selectOne('select#tipoDocumento');
								tipoDocumentoElement.setValue(response.cartera.tipoDoc);

								var numeroDocumentoElement = detailElement.selectOne('input#numeroDocumento');
								numeroDocumentoElement .setValue(response.cartera.numeroDoc);

								var valorElement = this.select('input.valor')[position];
								valorElement.setValue(Math.abs(response.cartera.valor));

								var naturalezaElement = this.select('select.naturaleza')[position];
								if (response.cartera.valor<0){
									naturalezaElement.setValue('D');
								} else {
									naturalezaElement.setValue('C');
								};

								this._pushRowForUpdate(numeroDocumentoElement, position);
							}
						}
					}
				}.bind(this, detailElement, position)
			});
		} else {
			this.getMessages().error('Indique un tercero en el movimiento para consultar la cartera');
		}
	},

	/**
	 * Muestra el formulario de crear un Tercero
	 */
	_showCrearNit: function(crearNit){
		var position = parseInt(crearNit.retrieve('position'), 10);
		var detailElement = this._getDetailElement(position);
		var nitElement = detailElement.selectOne('input#nit');
		var nitNombreElement = detailElement.selectOne('input#nitNombre');
		var nombre = nitNombreElement.getValue();
		if (nombre.include('NO EXISTE')){
			nombre = '';
		};
		new HfosModalForm(this, 'terceros/crear', {
			defaults: {
				'nit': nitElement.getValue(),
				'nombre': nombre
			},
			afterShow: HfosCommon.afterShowNitCrear,
			beforeClose: function(nitElement, nitNombreElement, crearNit, form, canceled){
				var nitCrearElement = form.selectOne('input#nit');
				var nitNombreCrearElement = form.selectOne('input#nombre');
				nitElement.setValue(nitCrearElement.getValue());
				nitNombreElement.setValue(nitNombreCrearElement.getValue());
				nitElement.focus();
				if (canceled==true){
					crearNit.show();
				} else {
					crearNit.hide();
				}
			}.bind(this, nitElement, nitNombreElement, crearNit)
		});
	},

	/**
	 * Evento al salir del campo nombre del tercero
	 */
	_nitNombreNext: function(element, event){

		var position = parseInt(element.retrieve('position'), 10);
		var detailElement = this._getDetailElement(position);

		if (element.getValue()!=''){

			//Establecer foco en caso que esté presente el centro de costo
			var centroCostoElement = detailElement.selectOne('select#centroCosto');
			if (centroCostoElement!==null){
				centroCostoElement.focus();
				return;
			};

			//Establecer foco en caso que esté presente el tipo de documento
			var tipoDocumentoElement = detailElement.selectOne('select#tipoDocumento');
			if (tipoDocumentoElement!==null){
				tipoDocumentoElement.focus();
				return;
			};

			//Establecer foco en caso que esté presente la base gravable
			var baseGravableElement = detailElement.selectOne('input#baseGravable');
			if (baseGravableElement!==null){
				baseGravableElement.focus();
				return;
			};

			//Ir a Descripción
			var focusElement = this.select('input.descripcion')[position];
			if (focusElement){
				window.setTimeout(function(){
					focusElement.focus();
				}.bind(this, focusElement), 100)
			}

		}

	},

	/**
	 * Navegación cuando se está ubicado en el campo de NIT
	 */
	_nitNavigation: function(nitElement, nitNombreElement, cuentaElement, event){

		if (event.keyCode==Event.KEY_UP){
			var selector = cuentaElement.retrieve('selector');
			if (typeof selector != "undefined"){
				selector.focus();
			} else {
				cuentaElement.focus();
			};
			new Event.stop(event);
			return;
		};

		if (event.keyCode==Event.KEY_DOWN){

			var position = parseInt(cuentaElement.up(1).retrieve('position'), 10);
			var detailElement = this._getDetailElement(position);
			if (detailElement!==null){
				var centroCostoElement = detailElement.selectOne('select#centroCosto');
				if (centroCostoElement!==null){
					new Event.stop(event);
					centroCostoElement.focus();
					return;
				};
			};

			position++;
			var focusElement = this.select('input.cuenta')[position];
			if (focusElement){
				window.setTimeout(function(){
					focusElement.focus();
				}.bind(this, focusElement), 200)
			};
			new Event.stop(event);
			return;
		};

		if ((event.keyCode==Event.KEY_RIGHT&&nitElement.selectionStart==nitElement.value.length)||(event.keyCode==Event.KEY_RETURN)){
			nitNombreElement.focus();
			new Event.stop(event);
			return;
		};

		if (nitElement.getValue()==''){
			var position = parseInt(cuentaElement.up(1).retrieve('position'), 10);
			var detailElement = this._getDetailElement(position);
			detailElement.selectOne('input#crearNit').show();
		}

	},

	/**
	 * Navegación cuando se está ubicado en el campo de nombre NIT
	 */
	_nitNombreNavigation: function(nitElement, nitNombreElement, cuentaElement, event){

		var completer = nitNombreElement.retrieve('completer');

		if (event.keyCode!=Event.KEY_RETURN&&event.keyCode!=Event.KEY_TAB&&
			event.keyCode!=Event.KEY_DOWN&&event.keyCode!=Event.KEY_UP&&
			event.keyCode!=Event.KEY_LEFT&&event.keyCode!=Event.KEY_RIGHT&&
			event.keyCode!=Event.KEY_PAGEUP&&event.keyCode!=Event.KEY_PAGEDOWN&&
			event.keyCode!=224){
			if (typeof completer == "undefined"){
				nitNombreElement.store('completer', new HfosAutocompleter(nitNombreElement, 'terceros/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: this._updateNumeroNit.bind(this, nitNombreElement, nitElement)
				}));
			};
			nitNombreElement.observe('external:changed', function(nitElement, nitNombreElement){
				var detailElement = nitElement.up(6);
				var verCarteraElement = detailElement.selectOne('input#verCartera');
				var crearNitElement = detailElement.selectOne('input#crearNit');
				crearNitElement.hide();
				if (verCarteraElement!==null){
					verCarteraElement.show();
				}
			}.bind(this, nitElement, nitNombreElement));
		};

		if (typeof completer == "undefined"){
			if (event.keyCode==Event.KEY_LEFT&&nitNombreElement.selectionStart==0){
				nitElement.focus();
				new Event.stop(event);
				return;
			};
			if (event.keyCode==Event.KEY_UP){
				var position = parseInt(cuentaElement.up(1).retrieve('position'), 10);
				var focusElement = this.select('input.nombreCuenta')[position];
				if (focusElement){
					window.setTimeout(function(){
						focusElement.focus();
					}.bind(this, focusElement), 100)
				};
				return;
			};
			if (event.keyCode==Event.KEY_DOWN){
				var detailElement = nitElement.up(6);
				var centroCostoElement = detailElement.selectOne('select#centroCosto');
				if (centroCostoElement!==null){
					window.setTimeout(function(centroCostoElement){
						centroCostoElement.activate();
					}.bind(this, centroCostoElement), 100);
					return;
				};
				var tipoDocumentoElement = detailElement.selectOne('select#tipoDocumento');
				if (tipoDocumentoElement!==null){
					window.setTimeout(function(tipoDocumentoElement){
						tipoDocumentoElement.activate();
					}.bind(this, tipoDocumentoElement), 100);
					return;
				};
				var baseGravableElement = detailElement.selectOne('input#baseGravable');
				if (baseGravableElement!==null){
					window.setTimeout(function(baseGravableElement){
						baseGravableElement.activate();
					}.bind(this, baseGravableElement), 100);
					return;
				};
				var position = parseInt(cuentaElement.up(1).retrieve('position'), 10);
				position++;
				var focusElement = this.select('input.cuenta')[position];
				if (focusElement){
					window.setTimeout(function(focusElement){
						focusElement.focus();
					}.bind(this, focusElement), 100)
				};
				new Event.stop(event);
				return;
			};
		};
	},

	/**
	 * Establecer la navegación del campo Centro de Costo
	 */
	_centroCostoNavigation: function(element)
	{
		var position = parseInt(element.retrieve('position'), 10);
		this._pushRowForUpdate(element, position);

		var detailElement = this._getDetailElement(position);
		if (detailElement !== null) {
			var tipoDocumentoElement = detailElement.selectOne('select#tipoDocumento');
			if (tipoDocumentoElement !== null) {
				window.setTimeout(function(tipoDocumentoElement){
					tipoDocumentoElement.activate();
				}.bind(this, tipoDocumentoElement), 200);
				return;
			};

			var baseGravableElement = detailElement.selectOne('input#baseGravable');
			if (baseGravableElement!==null){
				window.setTimeout(function(baseGravableElement){
					baseGravableElement.activate();
				}.bind(this, baseGravableElement), 200);
				return;
			};
		};

		var focusElement = this.select('input.descripcion')[position];
		if (focusElement){
			window.setTimeout(function(){
				focusElement.focus();
			}.bind(this, focusElement), 200)
		};
	},

	/**
	 * Establecer la navegación del campo Tipo de Documento
	 */
	_tipoDocumentoNavigation: function(element)
	{
		var position = parseInt(element.retrieve('position'), 10);
		this._pushRowForUpdate(element, position);

		var detailElement = this._getDetailElement(position);
		if (detailElement!==null){
			var numeroDocumentoElement = detailElement.selectOne('input#numeroDocumento');
			if (numeroDocumentoElement!==null){
				window.setTimeout(function(numeroDocumentoElement){
					numeroDocumentoElement.activate();
				}.bind(this, numeroDocumentoElement), 200);
				return;
			};
		};
		var focusElement = this.select('input.descripcion')[position];
		if (focusElement){
			window.setTimeout(function(){
				focusElement.focus();
			}.bind(this, focusElement), 100)
		};
	},

	/**
	 * Establecer la navegación del campo Número de Documento
	 */
	_numeroDocumentoNavigation: function(element)
	{
		var position = parseInt(element.retrieve('position'), 10);
		this._pushRowForUpdate(element, position);

		var detailElement = this._getDetailElement(position);
		var baseGravableElement = detailElement.selectOne('input#baseGravable');
		if (baseGravableElement !== null) {
			window.setTimeout(function(baseGravableElement){
				baseGravableElement.activate();
			}.bind(this, baseGravableElement), 200);
			return;
		};

		var focusElement = this.select('input.descripcion')[position];
		if (focusElement) {
			window.setTimeout(function(){
				focusElement.focus();
			}.bind(this, focusElement), 100)
		};
	},

	/**
	 * Establecer la navegación del campo Base Gravable
	 */
	_baseGravableNavigation: function(element)
	{
		var contentElement = this.getContentElement();
		var position = parseInt(element.retrieve('position'), 10);
		var detailElement = this._getDetailElement(position);

		var valorElement = this.selectOne('input#valor' + position);
		var cuentaPorcIvaElement = detailElement.selectOne('input#cuentaPorcIva');
		if (valorElement && cuentaPorcIvaElement) {
			var valor = parseFloat(element.getValue());
			if (valor) {
				new HfosModal.confirm({
					title: 'Base Gravable',
					message: 'Desea que el sistema calcule la base gravable según el porcentaje de la cuenta?',
					onAccept: function() {
						var porcIva = parseFloat(cuentaPorcIvaElement.getValue());
						if (porcIva) {
							var baseCalc = valor * porcIva / 100;
							valorElement.setValue(baseCalc);
						} else {
							alert("No se ha definido el porcentaje de iva de esta cuenta que pide base, por favor ingresarla");
						}
					}
				});
			}
		}

		this._pushRowForUpdate(element, position);

		var focusElement = this.select('input.descripcion')[position];
		if (focusElement){
			window.setTimeout(function(){
				focusElement.focus();
			}.bind(this, focusElement), 100)
		};
	},

	/**
	 * Consulta si un tercero existe al salir del campo del nit
	 */
	_queryByNit: function(nitElement, nitNombreElement)
	{
		if (nitElement.getValue() != '') {
			this._pushRowForUpdate(nitElement, nitElement.retrieve('position'));
			new HfosAjax.JsonRequest('terceros/queryByNit', {
				method: 'GET',
				checkAcl: true,
				parameters: 'nit=' + nitElement.getValue(),
				onSuccess: function(nitElement, nitNombreElement, response){
					var position = nitElement.retrieve('position');
					var detailElement = this._getDetailElement(position);
					if (detailElement!==null){
						var verCarteraElement = detailElement.selectOne('input#verCartera');
						var crearNitElement = detailElement.selectOne('input#crearNit');
						if (response.status=='OK'){
							nitNombreElement.setValue(response.nombre);
							crearNitElement.hide();
							if (verCarteraElement!==null){
								verCarteraElement.show();
							}
						} else {
							nitNombreElement.setValue(response.message);
							var activeElement = document.activeElement;
							if (activeElement==nitElement){
								nitNombreElement.activate();
							};
							var trElement = this.select('input.cuenta')[position].up(1);
							trElement.addClassName('lineaError');
							crearNitElement.show();
							if (verCarteraElement!==null){
								verCarteraElement.hide();
							}
						}
					};
				}.bind(this, nitElement, nitNombreElement)
			});
		} else {
			var position = nitElement.retrieve('position');
			var detailElement = this._getDetailElement(position);
			var crearNitElement = detailElement.selectOne('input#crearNit');
			crearNitElement.show();
		}
	},

	_onBlurNombreCuenta: function(element, event)
	{
		var completer = element.retrieve('completer');
		if (typeof completer != "undefined") {
			completer.destroy();
			element.store('completer', undefined);
			element.store('navEnabled', true);
		}
	},

	_onKeyDownNombreCuenta: function(element, event)
	{
		if (event.keyCode != Event.KEY_RETURN && event.keyCode != Event.KEY_TAB &&
			event.keyCode != Event.KEY_DOWN && event.keyCode != Event.KEY_UP &&
			event.keyCode != Event.KEY_LEFT && event.keyCode != Event.KEY_RIGHT &&
			event.keyCode != Event.KEY_PAGEUP && event.keyCode != Event.KEY_PAGEDOWN &&
			event.keyCode != 224){

			//remove cuentas selectores
			HfosCuentasSelectores2.removeAll();
			this._removeDetails();

			//agregar autocompleter
			var completer = element.retrieve('completer');
			if (typeof completer == "undefined") {
				element.store('completer', new HfosAutocompleter(element, 'cuentas/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: this._updateCodigoCuenta.bind(this, element)
				}));
				element.store('navEnabled', false);
			}
		}
	},

	_updateCodigoCuenta: function(element, option)
	{
		var position = parseInt(element.up(1).retrieve('position'), 10);
		var elementCuenta = this.select('input.cuenta')[position];
		if (elementCuenta) {
			elementCuenta.setValue(option.value);
		}
	},

	/**
	 * Evento al salir del selector de cuentas
	 */
	_leaveCuentaSelector: function(cuentaSelector, element){
		var cuenta = cuentaSelector.getCuenta();
		cuentaSelector.destroy();
		if (element.getValue()!=''){
			var position = element.up(1).retrieve('position');
			var detailElement = this._getDetailElement(position);
			if (!detailElement){
				this._showDetalles(element);
			} else {
				detailElement.show();
			};
			this._pushRowForUpdate(element, position);
		};
		element.store('selector', undefined);
	},

	/**
	 * Restaura la pantalla de nuevo
	 */
	_restoreNew: function()
	{
		var key = this.getContentElement().lang;
		if (key != '') {
			this.go('movimiento_niif/nuevo', {
				checkAcl: true,
				parameters: key,
				onCreate: function(){
					Hfos.getUI().blockInput();
				},
				onSuccess: this._setNewCallbacks.bind(this),
				onComplete: function(){
					Hfos.getUI().unblockInput();
				}
			});
		} else {
			this._setNewCallbacks();
		}
	},

	/**
	* Visualiza una ventana modal para ver las modificaciones del un comprobante
	*/
	_revisionesComprobante: function()
	{
		var params = this.getContentElement().lang;
		if (params != '') {
			new HfosModalForm(this, 'movimiento_niif/revisiones', {
				parameters: params,
				beforeClose: function(params, form, canceled, response){
					/*if (canceled==false){
						if (response.status=='OK'){
							if (typeof response.file != "undefined"){
								window.open($Kumbia.path+response.file);
							}
						}else{
							if (response.status=='FAILED'){
								this._active.getMessages().error(response.message);
							}
						}
					}*/
				}.bind(this, params)
			});
		}
	}

});

HfosBindings.late('win-movimiento-niif', 'afterCreateOrRestore', function(hfosWindow) {
	var movimientoNiif = new MovimientoNiif(hfosWindow);
});
