
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

/**
 * TransaccionInve
 *
 * Clase base para todas las transacciones de inventarios
 */
var TransaccionInve = Class.create({

	// Referencia al hyperGrid asociado a la Orden de Compra
	_hyperGrid: null,

	// Referencia al HyperForm
	_hyperForm: null,

	// Sección de HyperForm donde se se encuentra el formulario activo en
	// pantalla
	_activeSection: null,

	// Formulario activo en pantalla
	_form: null,

	// Listado temporal de los items cargados, es usado para descomponer una
	// receta estandar
	_items: [],

	// Cachea temporalmente el item digitado
	_data: {},

	// Tipo de Transacción
	_type: null,

	// Campos de Impuestos
	_totales: [
		'iva16r',
		'iva16d',
		'iva10r',
		'iva10d',
		'iva5r',
		'iva5d',
		'retencion',
		'ica',
		'horti',
		'cree',
		'impo',
		'saldo',
		'total'
	],

	// Indica si fue cargado desde una
	_wasLoaded: false,

	// Indica si el formulario fue restaurado o cargado en limpio
	_restored: false,

	//Bloquea la carga de impuestos al traer la orden de compra
	_blockTaxes: false,

	//Referencia a la barra de estado al editar o crear
	_statusBar: null,

	//Nombre del almacén que estaba cargado anteriormente
	_almacenAnterior: null,

	//almacena si puedo o no grabar la linea en la grilla
	_puedoGrabar: true,

	/**
	 * Este método se llama al crear un formulario hyperForm con una grilla
	 * Agrega los eventos a la grilla y al maestro
	 *
	 * @param string type
	 * @param HyperGrid hyperGrid
	 * @param boolean restored
	 */
	_initializeTransaction: function(type, hyperGrid, restored)
	{

		// Asignar tipo de transacción
		this._type = type;

		// Asignar hyperGrid
		this._hyperGrid = hyperGrid;

		// Eventos de la grilla
		var hyperGrid = this._hyperGrid;
		hyperGrid.observe('beforeInsert', this._beforeInsert.bind(this));
		hyperGrid.observe('afterEdit', this._afterEdit.bind(this));
		hyperGrid.observe('afterModify', this._afterModify.bind(this));
		hyperGrid.observe('afterDelete', this._afterDelete.bind(this));

		// Eventos del maestro
		var hyperForm = hyperGrid.getHyperForm();
		this._hyperForm = hyperForm;

		// Eventos de la ventana
		hyperForm.getWindow().observe('beforeClose', this._beforeClose.bind(this));

		//Eventos de la pestaña maestra
		hyperForm.observe('beforeInput', this._prepareForInput.bind(this));
		hyperForm.observe('afterDetails', this._addPrintButton.bind(this));
		hyperForm.observe('afterBack', this._hideStatusBar.bind(this));
		hyperForm.observe('beforeRecordPreview', this._addReopenButton.bind(this));
		hyperForm.observe('pedidoUpdated', this._loadPedido.bind(this));
		hyperForm.observe('ordenUpdated', this._loadOrden.bind(this));
		hyperForm.observe('onKeyPress', this._onKeyPress.bind(this));

		this._restored = restored;
		if (this._restored == true) {
			this._restoreTransaction();
		}
	},

	/**
	 * Indica si la ventana puede ser cerrada cuando hay cambios sobre el formulario
	 */
	_beforeClose: function()
	{
		var currentState = this._hyperForm.getCurrentState();
		if (currentState == 'new' || currentState == 'edit') {
			if (this._hyperGrid.getNumberChanged() > 0) {
				new HfosModal.confirm({
					title: this._hyperForm.getWindow().getTitle(),
					message: '¿Desea descartar los cambios?',
					onAccept: function(){
						this._hyperForm.getWindow().close(true);
					}.bind(this)
				});
				return false;
			}
		};
		return true;
	},

	/**
	 * Recibe los eventos de teclado enviados a la ventana
	 */
	_onKeyPress: function(eventName, event)
	{
		var currentState = this._hyperForm.getCurrentState();
		if (currentState == 'new' || currentState == 'edit') {
			if (event.keyCode == Event.KEY_RETURN||event.keyCode==Event.KEY_F7) {
				if (this._hyperGrid.getNumberChanged() == 0) {
					return true;
				} else {
					new HfosModal.confirm({
						title: this._hyperForm.getWindow().getTitle(),
						message: '¿Desea guardar ahora?',
						onAccept: function(){
							this._hyperForm.save();
						}.bind(this)
					});
					return false;
				}
			};
		}
		return true;
	},

	/**
	 * Evento al cambiar entre pestañas
	 */
	_onChangeTab: function(eventName, tabName, tabContent, position)
	{
		if (position == 0) {
			this._showDefaultMessage();
		} else {
			var message = '';
			switch (tabName) {

				case 'Detalle':
					message = 'Indique referencias y haga click en "Agregar" para adicionarla a la lista';
					break;

				case 'Criterios':
					var nitElement = this._form.selectOne('#nit');
					if (nitElement.getValue() != '') {
						message = 'Califique al proveedor aumentando ó reduciendo los puntos en cada criterio';
					} else {
						message = 'No ha indicado el proveedor. Los criterios se cargan de acuerdo al proveedor asociado';
					};
					break;

				case 'Totales':
					var nitElement = this._form.selectOne('#nit');
					if(nitElement.getValue()!=''){
						message = 'Los impuestos se calculan automáticamente al ingresar las referencias e indicar el proveedor.';
						message += 'De cualquier manera puede modificar los valores en las casillas al finalizar el ingreso de referencias.'
					} else {
						message = 'No ha indicado el proveedor. Los impuestos solo se calculan al indicar el proveedor e ingresar las referencias';
					};
					break;

				default:
					return;
			};
			this._hyperForm.getMessages().notice(message, false);
		};
		var firstElement = tabContent.selectOne('input[type="text"]');
		if (firstElement !== null) {
			firstElement.activate();
		}
	},

	/**
	 * Coloca un mensaje predeterminado de acuerdo a la transacción activa
	 */
	_showDefaultMessage: function()
	{
		var message = 'Ingrese los datos en los campos y presione "Grabar"';
		switch (this._type) {
			case 'O':
				message = 'Indique el proveedor y forma de pago de la orden de compra';
				break;
			case 'E':
				message = 'Indique el proveedor y forma de pago de la orden de compra';
				break;
			case 'C':
			case 'A':
			case 'T':
			case 'P':
			case 'R':
				break;
		};
		this._hyperForm.getMessages().notice(message, false);
	},

	/**
	 * Obtiene el campo donde se digita la cantidad
	 */
	_getQuantityField: function()
	{
		switch(this._type){
			/*case 'E':
				return 'cantidad_rec';*/
			default:
				return 'cantidad';
		}
	},

	/**
	 * Obtiene el tipo de controlador que administra la transacción
	 */
	_getTransactionType: function()
	{
		switch (this._type) {
			case 'O':
				return 'ordenes';
			case 'E':
				return 'entradas';
			case 'C':
				return 'salidas';
			case 'A':
				return 'ajustes';
			case 'T':
				return 'traslados';
			case 'P':
				return 'pedidos';
			case 'R':
				return 'transformaciones';
		}
	},

	/**
	 * Este metodo se ejecuta al empezar un Crear ó Actualizar en HyperForm
	 */
	_prepareForInput: function()
	{

        var tipoDetElement = this._hyperGrid.getField('item_tipo');

        var itemElement = this._hyperGrid.getField('item');
		itemElement.observe('change', this._getReferenciaOrReceta.bind(this));

		var itemDetElement = this._hyperGrid.getField('item_det');
		itemDetElement.observe('change', this._getReferenciaOrReceta.bind(this));
		itemDetElement.observe('external:changed', this._getReferenciaOrReceta.bind(this));

		var unidadElement = this._hyperGrid.getField('unidad');
		unidadElement.setAttribute('readOnly', 'readonly');

		var cantidadField = this._getQuantityField();
		var cantidadElement = this._hyperGrid.getField(cantidadField);
		cantidadElement.observe('change', this._actualizeValor.bind(this, cantidadElement, itemElement));

		this._hyperGrid.clearData();

		this._statusBar = null;
		this._activeSection = this._hyperForm.getActiveSection();
		this._form = this._activeSection.getElement('hySaveForm');

		window.setTimeout(function(){

			this._preLoad = true;

			// Si es una entrada o una orden de compra
			if(this._type=='E'||this._type=='O'){

				// Proveedor
				var nitElement = this._form.selectOne('#nit');
				nitElement.observe('change', this._calculaTaxes.bind(this));

				// Nombre del Proveedor
				var nitDetElement = this._form.selectOne('#nit_det');
				nitDetElement.observe('change', this._calculaTaxes.bind(this));
				nitDetElement.observe('external:changed', this._calculaTaxes.bind(this));

				// Totales, se les agrega un listener para actualizar el saldo
				for(var i=0;i<this._totales.length;i++){
					var field = this._form.selectOne('#'+this._totales[i]);
					if(field){
						field.store('changed', false);
						field.observe('change', this._updateTotales.bind(this, field));
					}
				};

				// Otros Totales
				var totales = ['total_neto', 'saldo', 'total'];
				for(var i = 0;i < totales.length; i++) {
					var field = this._form.selectOne('#' + totales[i]);
					if (field) {
						field.addClassName('readOnlyOnTab');
						field.setAttribute('readonly', true);
					}
				};

				//Agregar botón de detalle del calculo
				var totalesTab = this._form.selectOne('#Totales');
				if(totalesTab!=null){

					var detailedElement = this._form.selectOne('a#detailedCalculation');
					if(detailedElement===null){

						var element = document.createElement('DIV');
						element.update("<div align='right' class='detailed-calculation'><a href='#' id='detailedCalculation'>Detalle del Calculo</a></div>");
						totalesTab.appendChild(element);

						var detailedElement = this._form.selectOne('a#detailedCalculation');
						detailedElement.observe('click', this._detailedCalculation.bind(this));

					} else {
						detailedElement.observe('click', this._detailedCalculation.bind(this));
					}
				};

				this._updateTotales();
			};

			// Transacciones que solicitan ordenes ó pedidos
			var nAlmacenElement = this._form.selectOne('#almacen');
			if(this._type=='E'||this._type=='C'||this._type=='T'){

				// Cargar Orden de Compra ó Pedido
				var nPedidoElement = this._form.selectOne('#n_pedido');
				if(this._hyperForm.getCurrentState()=='new'){
					if(this._type=='E'){
						nAlmacenElement.observe('change', this._getOrden.bind(this, nAlmacenElement, nPedidoElement));
						nPedidoElement.observe('change', this._getOrden.bind(this, nAlmacenElement, nPedidoElement));
					} else {
						if(this._type=='C'||this._type=='T'){
							nAlmacenElement.observe('change', this._getPedido.bind(this, nAlmacenElement, nPedidoElement));
							nPedidoElement.observe('change', this._getPedido.bind(this, nAlmacenElement, nPedidoElement));
						}
					};
					nPedidoElement.activate();
				} else {
					nPedidoElement.setAttribute('readonly', true);
				};
			};

			//Actualizar último saldo al cambiar el almacen
			if(this._type=='C'||this._type=='A'||this._type=='T'){
				if(nAlmacenElement!==null){
					if(typeof nAlmacenElement.selectedIndex != "undefined"){
						this._almacenAnterior = nAlmacenElement.options[nAlmacenElement.selectedIndex].text;
						nAlmacenElement.observe('change', this._actualizaLastSaldo.bind(this, nAlmacenElement));
					}
				};
			};

			// Transformaciones
			if(this._type=='R'){

				//Total
				var vTotalElement = this._form.selectOne('#v_total');
				vTotalElement.setAttribute('readonly', true);
				vTotalElement.observe('change', transformacionCosto);

				var unidadObjetivoElement = this._form.selectOne('#unidad_objetivo');
				unidadObjetivoElement.disable();

				var itemObjetivoElement = this._form.selectOne('#item_objetivo');
				var cantidadObjetivoElement = this._form.selectOne('#cantidad_objetivo');

				var transformacionCosto = this._getTransformacionCosto.bind(this, itemObjetivoElement, unidadObjetivoElement, cantidadObjetivoElement, vTotalElement);
				itemObjetivoElement.observe('change', transformacionCosto);
				cantidadObjetivoElement.observe('change', transformacionCosto);

			};

			// Cargando los datos de la pestaña detalle
			var transactionType = this._getTransactionType(this._type);
			//var fields = ['item', 'item_det', 'unidad', 'cantidad', 'cantidad_rec', 'valor', 'iva'];
			if (this._type=='O') {
				var fields = ['item', 'item_det', 'descripcion2', 'unidad', 'cantidad', 'valor', 'iva'];
			} else {
				var fields = ['item', 'item_det', 'unidad', 'cantidad', 'valor', 'iva'];
			}

			this._hyperGrid.loadBaseData(transactionType, fields);

			//Eventos de las pestañas del maestro
			this._hyperForm.getTabs().observe('tabChanged', this._onChangeTab.bind(this));

			//Totalizar la base de la grilla
			this._totalize();

			//Totalizar impuestos
			if(this._type=='E'||this._type=='O'){
				this._updateTotales();
			};

			this._preLoad = false;
		}.bind(this), 50);

		return true;
	},

	/**
	 * Muestra una pantalla con el detalle del calculo de impuestos
	 */
	_detailedCalculation: function(){
		var numberChanged = this._form.select('input.changedOnTab').length;
		if(numberChanged>0){
			new HfosModal.confirm({
				title: 'Detalle del Calculo',
				message: 'Algunos totales de impuestos han sido modificados manualmente. El detalle del calculo automático podría ser diferente. ¿Desea continuar?',
				onAccept: function(){
					this._getDetailedCalculation();
				}.bind(this)
			});
		} else {
			this._getDetailedCalculation();
		}
	},

	_getDetailedCalculation: function(){
		var controller = this._getTransactionType();
		var parameters = {};
		parameters['tipo'] = this._type;
		parameters['nit'] =this._form.selectOne('#nit').getValue();
		parameters['almacen'] = this._form.selectOne('#almacen').getValue();
		parameters['items'] = Json.encode(this._hyperGrid.getColumn('item'));
		parameters['iva'] = Json.encode(this._hyperGrid.getColumn('iva'));
		parameters['valor'] = Json.encode(this._hyperGrid.getColumn('valor'));
		new HfosAjax.JsonRequest('tatico/getDetailedCalculation', {
			'parameters': parameters,
			'onSuccess': function(response){
				if(response.status=='FAILED'){
					this._hyperForm.getMessages().error(response.message);
				} else {
					if(typeof response.file != "undefined"){
						window.open($Kumbia.path+response.file);
					}
				}
			}.bind(this)
		});
	},

	/**
	 * Totaliza la transacción en la barra de estado
	 */
	_totalize: function(){
		var total = this._hyperGrid.getSummatory('valor');
		if(this._type=='E'||this._type=='O'){
			this._form.selectOne('#total_neto').setValue(total);
		};
		total = Utils.numberFormat(total);
		this._showStatus(total, 'total');
	},

	/**
	 * Agrega el botón de imprimir
	 */
	_addPrintButton: function(){
		this._hyperForm.addControlButton({
			className: "printButton",
			value: "Imprimir",
			onClick: this._printTransaction.bind(this)
		});
	},

	/**
	 * Agrega el de re-abrir orden
	 */
	_addReopenButton: function(eventName, response){
		if(this._type=='O'||this._type=='P'){
			this._hyperForm.removeControlButton("reopenButton");
			this._hyperForm.removeControlButton("sendButton");
			var record = new HyperRecordData(response.data);
			var estado = record.getValueFromName("estado");
			if(estado=="CERRADA"||estado=="CERRADO"){
				this._hyperForm.addControlButton({
					className: "reopenButton",
					value: "Re-Abrir",
					onClick: this._reOpenTransaction.bind(this, record)
				});
			} else {
				this._hyperForm.addControlButton({
					className: "sendButton",
					value: "Enviar Correo",
					onClick: this._sendTransaction.bind(this, record)
				});
			}
		}
	},

	/**
	 * Muestra un mensaje en una parte de la barra de estado
	 */
	_showStatus: function(message, where){
		if(this._statusBar===null){
			var html = '<table cellspacing="0" width="100%" class="totales"><tr><td class="saldo"></td><td align="right"><b>Total</b> <span class="total">0.00</span></tr></table>';
			this._hyperForm.showStatusBar(html);
			this._statusBar = true;
		};
		this._hyperForm.getStatusBarElement(where).update(message);
	},

	/**
	 * Oculta la barra de mensajes
	 */
	_hideStatusBar: function(){
		this._hyperForm.hideStatusBar();
	},

	/**
	 * Obtiene el costo total de la transformación y la unidad
	 */
	_getTransformacionCosto: function(itemElement, unidadElement, cantidadElement, vTotalElement){
		var almacenElement = this._form.selectOne('#almacen');
		new Tatico.getReferencia(itemElement.getValue(), function(unidadElement, cantidadElement, vTotalElement, response){
			if(response.status=='FAILED'){
				this._hyperForm.getMessages().error(response.message);
			} else {
				this._showDefaultMessage();
				unidadElement.setValue(response.data.unidad);
				if(cantidadElement.getValue()!=''){
					var cantidad = parseFloat(cantidadElement.getValue(), 10);
					vTotalElement.setValue(Utils.round(cantidad*response.data.costo, 2));
				} else {
					vTotalElement.setValue(response.data.costo);
				};
			};
			unidadElement.disable();
			vTotalElement.disable();
		}.bind(this, unidadElement, cantidadElement, vTotalElement), almacenElement.getValue());
	},

	/**
	 * Se ejecuta al cambiar el valor del campo item de la grilla Del servidor
	 * se obtiene el costo, iva, nombre, etc.
	 */
	_getReferenciaOrReceta: function(){
        var tipoDetElement = this._hyperGrid.getField('item_tipo');
        var tipoDet = "I";
        if (tipoDetElement) {
            tipoDet = tipoDetElement.getValue();
        }
        var itemElement = this._hyperGrid.getField('item');
        itemElement.setValue(itemElement.getValue().toUpperCase());
		if(itemElement.getValue().blank()){
			return;
		};
        this._form.disable();
		var almacenElement = this._form.selectOne('#almacen');
		Tatico.getReferenciaOrReceta(almacenElement.getValue(), itemElement.getValue(), tipoDet, function(response){
			this._form.enable();
			if(response.status=='OK'){
				this._showData(response.data);
				this._showDefaultMessage();
			} else {
				this._hyperForm.getMessages().error(response.message);
				var fields = ['item', 'item_det', 'unidad', 'iva'];
				for(var i=0;i<fields.length;i++){
					var field = this._hyperGrid.getField(fields[i]);
					if(field!==null){
						field.setValue('');
					}
				};
				//fields = ['cantidad', 'cantidad_rec', 'valor'];
				fields = ['cantidad', 'valor'];
				for(var i=0;i<fields.length;i++){
					var field = this._hyperGrid.getField(fields[i]);
					if(field!==null){
						if(field.getValue()!=''){
							field.setValue('');
						}
					}
				};
			};
		}.bind(this));
	},

	/**
	 * Carga los datos traidos desde el servidor de los items o las recetas En
	 * la grilla detalle
	 */
	_showData: function(data)
	{

		var unidadElement = this._hyperGrid.getField('unidad');
		if (unidadElement!==null){
			unidadElement.setValue(data.unidad);
		};

		var valorElement = this._hyperGrid.getField('valor');
		if (valorElement!==null){
			valorElement.setValue(data.costo);
		};

		var ivaElement = this._hyperGrid.getField('iva');
		if (ivaElement!==null){
			ivaElement.setValue(data.iva);
		};

		var cantidadElement = this._hyperGrid.getField('cantidad');
		if (cantidadElement.getValue() == '' || cantidadElement.getValue() == 0) {
			cantidadElement.setValue(1);
			cantidadElement.activate();
		};

		/*var cantidadRecElement = this._hyperGrid.getField('cantidad_rec');
		if(cantidadRecElement!==null){
			if(cantidadRecElement.getValue()==''||cantidadRecElement.getValue()==0){
				cantidadRecElement.setValue(1);
				if(this._type=='C'){
					if(this._wasLoaded==true){
						cantidadRecElement.activate();
					}
				}
			};
		};*/

		if (data.type == 'I') {
			this._tipo = 'I';
			this._items.push(data.data);
		} else {
			this._tipo = 'R';
			for(var item in data.components){
				data.components[item]['item'] = item;
				this._items.push(data.components[item]);
			}
		};
		this._data = data;
		this._updateMessageSaldo(data);
		this._disableCustomFields();
	},

	/**
	 * Actualiza el mensaje del saldo
	 */
	_updateMessageSaldo: function(data)
	{
		if (this._type == 'O' || this._type == 'C' || this._type == 'T' || this._type == 'A') {
			if (typeof data.saldo != "undefined") {
				if (data.saldo.status == 'OK') {
					this._showStatus('<div class="con-saldo">'+data.saldo.message+'</div>', 'saldo');
				} else {
					if (data.saldo.status=='FAILED') {
						this._showStatus('<div class="sin-saldo">'+data.saldo.message+'</div>', 'saldo');
					} else {
						this._showStatus('', 'saldo');
					}
				}
			} else {
				this._showStatus('', 'saldo');
			}
		};
	},

	/**
	 * Multiplica el valor del costo del item seleccionado por la cantidad
	 */
	_actualizeValor: function(cantidadElement, itemElement)
	{
		var agregarButton = this._form.selectOne('input.hyGridAdd');
		agregarButton.disable();
		var cantidad = cantidadElement.getValue();
		/*ENTRADAS*/
		if (this._type == 'E') {
			if (cantidad == '') {
				return;
			} else {
				var cantidadBase = this._hyperGrid.getField('cantidad').getValue();
				if (cantidad > cantidadBase) {
					if (this._wasLoaded == true) {
						this._hyperForm.getMessages().error('La cantidad a entregar no puede superar la cantidad pedida');
						cantidad = cantidadBase;
						cantidadElement.setValue(cantidad);
						window.setTimeout(function(){
							cantidadElement.activate();
						}, 20);
					};
				}
			}
		}
		/*SALIDAS*/
		if (this._type == 'C') {
			if (cantidad == '') {
				return;
			} else {
				this._checkExistenciasSalidas();
			}
		}

		if (cantidad == '') {
			return;
		};
		this._hyperGrid.getField('valor').setValue(Utils.round(this._data.costo*cantidad, 2));
		agregarButton.enable();
	},

	/**
	 * Verifica si la existencias alcansan al hacer la salida
	 * @param  {[type]} cantidadElement [description]
	 * @param  {[type]} itemElement     [description]
	 * @return {[type]}                 [description]
	 */
	_checkExistenciasSalidas: function()
	{
		//alert("Verificando saldos.....");
		var itemElement = this._hyperGrid.getField('item');
		if(!itemElement){
			alert("Item not found");
			return;
		}
		var cantidad = this._hyperGrid.getField('cantidad').getValue();
		var almacen = this._form.selectOne('#almacen').getValue();
		/*new HfosModal.alert({
			title: 'Cantidad Excedida',
			message: "item: "+itemElement.getValue()+", cantidad: "+cantidad+", almacen: "+almacen
		});
		return;*/
		Tatico.getReferenciaOrReceta(almacen, itemElement.getValue(), function(response){
			this._form.enable();
			if(response.status=='OK'){
				if (response.data && response.data.existencias){
					var existencia = Utils.round(response.data.existencias, 3);
					if (existencia < cantidad) {
						new HfosModal.alert({
							title: 'Cantidad Excedida',
							message: "La cantidad ingresada excede las existencis actuales (" + existencia + ")"
						});

						fields = ['cantidad', 'valor'];
						for(var i = 0; i < fields.length;i++){
							var field = this._hyperGrid.getField(fields[i]);
							if(field!==null){
								if(field.getValue()!=''){
									field.setValue('');
								}
							}
						};
						this._hyperGrid.getField('cantidad').setValue(existencia);
					}
					this._hyperGrid.getField('valor').setValue(Utils.round(this._data.costo*cantidad, 2));
				}
			}
		}.bind(this));

	},

	/**
	 * Quita el último saldo cargado al cambiar el almacén
	 */
	_actualizaLastSaldo: function(nAlmacenElement)
	{
		var saldoStatusBar = this._hyperForm.getStatusBarElement('saldo');
		if (saldoStatusBar.innerHTML != '') {
			this._showStatus('', 'saldo');
			if(this._hyperGrid.isEmpty()==false){
				new HfosModal.confirm({
					title: this._hyperForm.getWindow().getTitle(),
					message: 'Los costos que están cargados en el detalle del movimiento fueron '+
					'tomados del almácen "' + this._almacenAnterior + '", estos costos pueden '+
					'ser diferentes a los del almacén "'+nAlmacenElement.options[nAlmacenElement.selectedIndex].text+'". '+
					'¿Desea continuar?'
				});
			};
		}
	},

	/**
	 * Se ejecuta antes de editar una fila del detalle deshabilitando el campo
	 * unidad
	 */
	_afterEdit: function(hyperGrid)
	{

		this._disableCustomFields();
		var editRow = hyperGrid.getEditRow();
		if(!editRow || typeof editRow.get != "function"){
			new HfosModal.alert({
				title: 'Inventarios',
				message: 'Ocurrió un problema al editar el movimiento'
			});
			return;
		};

		if(this._type=='O'||this._type=='C'||this._type=='A'||this._type=='T'){
			var almacenElement = this._form.selectOne('#almacen');
			new Tatico.getSaldoReferencia(editRow.get('item'), almacenElement.getValue(), function(response){
				if(response.status=='FAILED'){
					this._hyperForm.getMessages().error(response.message);
				} else {
					this._updateMessageSaldo(response.data);
				}
			}.bind(this));
		}

		this._data.costo = Utils.round(editRow.get('valor') / editRow.get('cantidad'), 2);
		this._totalize();
	},

	/**
	 * Deshabilita campos en la grilla detalle que no deben ser modificados
	 * según el tipo de transacción
	 */
	_disableCustomFields: function()
	{
		this._hyperGrid.getField('unidad').disable();
		if (this._type=='E'){
			this._hyperGrid.getField('iva').disable();
		} else {
			if (this._type=='C'||this._type=='T'||this._type=='P'){
				this._hyperGrid.getField('valor').disable();
			} else {
				if (this._type=='R'){
					this._form.selectOne('#unidad_objetivo').disable();
				}
			}
		};
		if(this._type=='E'){
			if(this._wasLoaded==true){
				this._hyperGrid.getField('cantidad').disable();
			} else {
				this._hyperGrid.getField('cantidad').enable();
			}
		}
	},

	/**
	 * Se ejecuta antes de agregar una fila al detalle cargando los items en
	 * caso de ser una receta
	 */
	_beforeInsert: function()
	{
		if (this._tipo == 'R') {
			this._preLoad = true;
			this._cantidad = this._hyperGrid.getField('cantidad').getValue();
			this._loadDetail();
			this._preLoad = false;
			this._afterModify();
			return false;
		}
	},

	/**
	 * Carga los items de una receta estandar
	 */
	_loadDetail: function()
	{
		this._cantidad = parseFloat(this._cantidad, 10);
		this._tipo = 'I';
		this._items.each(function(element){
			if (typeof element != "undefined") {
                var cantidad = parseFloat(element.cantidad, 10) * this._cantidad;
                var valor = parseFloat(element.costo, 10) * this._cantidad;
                cantidad = Utils.round(cantidad, 3);
                valor = Utils.round(valor, 2);
                this._hyperGrid.getField('item_tipo').setValue(element.item_tipo);
                this._hyperGrid.getField('item').setValue(element.item);
				this._hyperGrid.getField('item_det').setValue(element.descripcion);
				this._hyperGrid.getField('unidad').setValue(element.unidad);
				this._hyperGrid.getField('cantidad').setValue(cantidad);
				this._hyperGrid.getField('valor').setValue(valor);
				var ivaElement = this._hyperGrid.getField('iva');
				if(ivaElement!==null){
					ivaElement.setValue(element.iva);
				};
				this._hyperGrid.addRow();
			}
		}.bind(this));
		this._items.clear();
	},

	/**
	 * Ejecuta las acciones despues de que se adiciona un elemento a la grilla
	 */
	_afterModify: function()
	{
		if (this._preLoad == true) {
			return true;
		};
		if (this._type == 'E' || this._type == 'O') {
			this._calculaTaxes();
		};
		if (this._type == 'R') {
			this._calcularTransformacion();
		};
		this._totalize();
	},

	/**
	 * Se ejecuta al borrar una fila de grilla
	 */
	_afterDelete: function()
	{
		this._totalize();
	},

	/**
	 * Calcula los impuestos de la orden de compra o la entrada
	 */
	_calculaTaxes: function()
	{
		if (this._blockTaxes == false) {
			var parameters = {};
			var nitElement = this._form.selectOne('#nit');
			if (nitElement.getValue() == '') {
				if(this._type=='O'){
					this._hyperForm.getMessages().notice('Indique un proveedor para calcular los impuestos de la orden de compra');
				} else {
					if(this._type=='E'){
						this._hyperForm.getMessages().notice('Indique un proveedor para calcular los impuestos de la entrada al almacén');
					}
				}
			} else {
				parameters['tipo'] = this._type;
				parameters['nit'] = nitElement.getValue();
				parameters['almacen'] = this._form.selectOne('#almacen').getValue();
				parameters['items'] = Json.encode(this._hyperGrid.getColumn('item'));
				parameters['iva'] = Json.encode(this._hyperGrid.getColumn('iva'));
				parameters['valor'] = Json.encode(this._hyperGrid.getColumn('valor'));
				Tatico.getTaxes(parameters, function(response){

					if (response.status == 'FAILED') {
						this._hyperForm.getMessages().error(response.message);
					} else {

						//Cargar totales
						for (var i = 0; i < this._totales.length; i++) {
							var field = this._form.selectOne('#' + this._totales[i]);
							if (field !== null) {
								//if(field.retrieve('changed')==false){
									field.setValue(response[this._totales[i]]);
								//}
							}
						};

						//Actualizar saldos
						this._updateTotales();

						if (typeof response.criterios != "undefined") {
							$H(response.criterios).each(function(criterio){
								var criterioField = this._form.selectOne('#criterio' + criterio[0]);
								if(criterioField!==null){
									criterioField.setValue(criterio[1]);
								}
							}.bind(this));
						};

						this._showDefaultMessage();

						this._blockTaxes = false;
					};
				}.bind(this));
			};
			this._hyperGrid.getField('iva').enable();
			this._hyperGrid.getField('item_det').enable();
		}
	},

	/**
	 * Actualiza los totales al mover algún impuesto
	 */
	_updateTotales: function(element)
	{
		var impuestos = 0, total = 0;
		var totalNeto = this._hyperGrid.getSummatory('valor');

		// No colocar el IMPO aqui
		var impuestosDebito = ['iva16d', 'iva10d', 'iva5d'];
		for (var i = 0; i < impuestosDebito.length; i++) {
			var field = this._form.selectOne('#' + impuestosDebito[i]);
			if (field !== null){
				if (field.getValue() != '') {
					impuestos += parseFloat(field.getValue(), 10);
				};
			} else {
				alert('No encontrado en la vista: ' + impuestosDebito[i])
			}
		};

		// No colocar el IMPO aqui
		var impuestosCredito = ['iva16r', 'iva10r', 'iva5r'];
		for (var i = 0; i < impuestosCredito.length; i++) {
			var field = this._form.selectOne('#' + impuestosCredito[i]);
			if (field !== null) {
				if (field.getValue() != '') {
					impuestos -= parseFloat(field.getValue(), 10);
				}
			} else {
				alert('No encontrado en la vista: ' + impuestosCredito[i])
			}
		};

		this._form.selectOne('#saldo').setValue(impuestos);

		if (typeof element != "undefined") {
			element.addClassName('changedOnTab', true)
			element.store('changed', true);
		}
	},

	/**
	 * Fuerza que se cargue una orden de compra
	 */
	_loadOrden: function()
	{
		if (this._activeSection!==null) {
			var currentState = this._hyperForm.getCurrentState();
			var nAlmacenElement = this._activeSection.selectOne('#almacen');
			var nOrdenElement = this._activeSection.selectOne('#n_pedido');
			if (currentState == 'new') {
				this._getOrden(nAlmacenElement, nOrdenElement);
			} else {
				if (currentState == 'edit') {
					nOrdenElement.setValue('');
					new HfosModal.alert({
						title: 'Entradas al Almacén',
						message: 'La orden no puede ser cargada porque la entrada al almacén no puede ser modificada'
					});
				}
			}
		}
	},

	/**
	 * Consulta la orden de compra
	 */
	_getOrden: function(nAlmacenElement, nPedidoElement){
		if(parseInt(nPedidoElement.getValue(), 10)>0){
			this._blockTaxes = true;
			this._form.disable();
			Tatico.getOrdenDeCompra(nAlmacenElement.getValue(), nPedidoElement.getValue(), function(response){
				this._form.enable();
				if(response.status=='OK'){
					this._hyperForm.getMessages().notice('Se cargó la orden de compra correctamente', false);
					if(this._hyperGrid.isEmpty()==true){
						this._showDataOrden(response.data);
					} else {
						new HfosModal.confirm({
							title: 'Entradas al almacén',
							message: 'La entrada al almacén ya tiene referencias cargadas. Desea reemplazarlas?',
							onAccept: function(response){
								this._hyperGrid.clear();
								this._showDataOrden(response.data);
							}.bind(this, response)
						});
					};
				} else {
					this._hyperForm.getMessages().error(response.message);
				};
				this._blockTaxes = false;
			}.bind(this));
		}
	},

	/**
	 * Carga los datos de la orden de compra en sus respectivos campos
	 */
	_showDataOrden: function(data){

		// Asignando los valores a los elementos
		var nitElement = this._form.selectOne('#nit');
		nitElement.setValue(data.nit);

		var nitDetElement = this._form.selectOne('#nit_det');
		nitDetElement.setValue(data.nit_det);

		var observacionesElement = this._form.selectOne('#observaciones');
		if(observacionesElement!==null){
			observacionesElement.setValue(data.observaciones);
		};

		// Cargar totales
		for(var i=0;i<this._totales.length;i++){
			var field = this._form.selectOne('#'+this._totales[i]);
			if(field!==null){
				field.addClassName('changedOnTab', true);
				field.store('changed', true);
				field.setValue(data.taxes[this._totales[i]]);
			}
		};

		// Cargando el detalle con los elementos del movilin de la orden de compra
		for(var i=0;i<data.movilin.length;i++){
			var row = data.movilin[i];
			this._hyperGrid.getField('item').setValue(row.item);
			this._hyperGrid.getField('item_det').setValue(row.descripcion);
			this._hyperGrid.getField('unidad').setValue(row.unidad);
			this._hyperGrid.getField('cantidad').setValue(row.cantidad);
			//this._hyperGrid.getField('cantidad_rec').setValue(row.cantidad);
			this._hyperGrid.getField('valor').setValue(row.valor);
			if(this._type=='E'||this._type=='O'){
				this._hyperGrid.getField('iva').setValue(row.iva);
				//to6.1.12
				if (typeof row.descripcion2 != "undefined" && this._hyperGrid.getField('descripcion2')) {
					this._hyperGrid.getField('descripcion2').setValue(row.descripcion2);
				}
			};
			this._hyperGrid.addRow();
		};

		// Focus al campo factura_c
		var facturaCElement = this._form.selectOne('#factura_c');
		facturaCElement.activate();

		//Calcular totales
		window.setTimeout(function(){
			this._updateTotales();
		}.bind(this), 700)

	},

	/**
	 * Fuerza que se cargue un pedido
	 */
	_loadPedido: function(){
		if(this._activeSection!==null){
			var currentState = this._hyperForm.getCurrentState();
			var nAlmacenElement = this._activeSection.selectOne('#almacen');
			var nPedidoElement = this._activeSection.selectOne('#n_pedido');
			if(currentState=='new'){
				this._getPedido(nAlmacenElement, nPedidoElement);
			} else {
				if(currentState=='edit'){
					nPedidoElement.setValue('');
					if(this._type=='T'){
						new HfosModal.alert({
							title: 'Traslados',
							message: 'El pedido no puede ser cargado porque el traslado no puede ser modificado'
						});
					} else {
						if(this._type=='C'){
							new HfosModal.alert({
								title: 'Salidas',
								message: 'El pedido no puede ser cargado porque la salida no puede ser modificada'
							});
						}
					}
				}
			}
		}
	},

	/**
	 * Consulta un pedido
	 */
	_getPedido: function(nAlmacenElement, nPedidoElement){
		var nPedido = parseInt(nPedidoElement.getValue(), 10);
		var nAlmacen = parseInt(nAlmacenElement.getValue(), 10);
		if(nPedido>0&&nAlmacen>0){
			this._form.disable();
			this._wasLoaded = false;
			Tatico.getPedido(nAlmacen, nPedido, function(nPedido, response){
				this._form.enable();
				if(response.status=='OK'){
					this._wasLoaded = true;
					this._showDataPedido(response.data);
					this._hyperForm.getMessages().notice('Se cargó el pedido "'+nPedido+'" correctamente');
				} else {
					this._hyperForm.getMessages().error(response.message);
				}
			}.bind(this, nPedido));
		}
	},

	/**
	 * Carga un pedido en pantalla
	 */
	_showDataPedido: function(data){

		if(this._type=='C'){

			var nitElement = this._form.selectOne('#nit');
			nitElement.setValue(data.nit);

			var nitDetElement = this._form.selectOne('#nit_det');
			nitDetElement.setValue(data.nit_det);

		};

		var observacionesElement = this._form.selectOne('#observaciones');
		if(observacionesElement!==null){
			observacionesElement.setValue(data.observaciones);
		};

		var almacenDestinoElement = this._form.selectOne('#almacen_destino');
		if(almacenDestinoElement!==null){
			almacenDestinoElement.setValue(data.almacen_destino);
		};

		var centroCostoElement = this._form.selectOne('#centro_costo');
		if(centroCostoElement!==null){
			centroCostoElement.setValue(data.centro_costo);
		};

		// Cargando el detalle con los elementos del movilin del pedido
		for(var i=0;i<data.movilin.length;i++){
			var row = data.movilin[i];
			this._hyperGrid.getField('item').setValue(row.item);
			this._hyperGrid.getField('item_det').setValue(row.descripcion);
			//to6.1.12
			if (typeof row.descripcion2 != "undefined" && this._hyperGrid.getField('descripcion2')) {
				this._hyperGrid.getField('descripcion2').setValue(row.descripcion2);
			}
			this._hyperGrid.getField('unidad').setValue(row.unidad);
			this._hyperGrid.getField('cantidad').setValue(row.cantidad);
			this._hyperGrid.getField('valor').setValue(row.valor);
			this._hyperGrid.addRow();
		}

		// Poniendo el focus en el tipo de consumo
		if(this._type=='C'){
			var tipoElement = this._form.selectOne('#nota');
			tipoElement.activate();
		};

		this._preLoad = false;
	},

	/**
	 * Imprime la transacción en pantalla
	 */
	_printTransaction: function(){
		var controller = this._getTransactionType();
		var primary = this._hyperForm.getLastPrimary();
		new HfosModalForm(this, 'impresion/getFormato', {
			parameters: primary+'&controller='+controller,
			beforeClose: function(primary, form, canceled, response){
				if(canceled==false){
					if(response.status=='OK'){
						if(typeof response.file != "undefined"){
							window.open($Kumbia.path+response.file);
						}
					} else {
						if(response.status=='FAILED'){
							this._hyperForm.getMessages().error(response.message);
						}
					}
				}
			}.bind(this, primary)
		});
	},

	/**
	 * Intenta reabrir la orden de compra o el pedido en pantalla
	 */
	_reOpenTransaction: function(record){
		var almacen = record.getValueFromName("almacen");
		var numero = record.getValueFromName("numero");
		if(this._type=='O'){
			new HfosAjax.JsonRequest('ordenes/puedeReAbrir', {
				parameters: {
					almacen: almacen,
					numero: numero
				},
				onSuccess: function(almacen, numero, response){
					if(response.status=='OK'){
						new HfosModalForm(this, 'ordenes/getFechaLimite', {
							checkAcl: true,
							parameters: {
								almacen: almacen,
								numero: numero
							},
							beforeClose: function(form, canceled, response){
								if(canceled==false){
									this._hyperForm.back(2);
									this._hyperForm.getMessages().notice('Se re-abrió correctamente la orden de compra', true);
								}
							}.bind(this)
						});
					} else {
						if(response.status=='FAILED'){
							this._hyperForm.getMessages().error(response.message);
						}
					}
				}.bind(this, almacen, numero)
			});
		} else {
			new HfosAjax.JsonRequest('pedidos/puedeReAbrir', {
				parameters: {
					almacen: almacen,
					numero: numero
				},
				onSuccess: function(almacen, numero, response){
					if (response.status=='OK'){
						new HfosModalForm(this, 'pedidos/getFechaLimite', {
							checkAcl: true,
							parameters: {
								almacen: almacen,
								numero: numero
							},
							beforeClose: function(form, canceled, response){
								if(canceled==false){
									this._hyperForm.back(2);
									this._hyperForm.getMessages().notice('Se re-abrió correctamente el pedido', true);
								}
							}.bind(this)
						});
					} else {
						if(response.status=='FAILED'){
							this._hyperForm.getMessages().error(response.message);
						}
					}
				}.bind(this, almacen, numero)
			});
		}
	},

	/**
	 * Enviar orden por correo
	 */
	_sendTransaction: function(record){
		var almacen = record.getValueFromName("almacen");
		var numero = record.getValueFromName("numero");
		var controller = "";
		if(this._type=='O'){
			controller = "ordenes";
		}
		if(this._type=='P'){
			controller = "pedidos";
		}
		new HfosAjax.JsonRequest(controller + '/send', {
			parameters: {
				almacen: almacen,
				numero: numero
			},
			onSuccess: function(almacen, numero, response){
				if(response.status=='OK'){
					this._hyperForm.getMessages().success(response.message);
				} else {
					if(response.status=='FAILED'){
						this._hyperForm.getMessages().error(response.message);
					}
				}
			}.bind(this, almacen, numero)
		});
	},

	/**
	 * Carga los items de una receta estandar
	 */
	_calcularTransformacion: function()
	{

		//Base form
		this._activeSection = this._hyperForm.getActiveSection();
		var itemBase = this._form.selectOne('#item_objetivo');
		var vTotal = this._form.selectOne('#v_total');
		var nota = this._form.selectOne('#nota');
		var cantidadObjetivo = this._form.selectOne('#cantidad_objetivo');

		var vItemBase = 0;
		if (!itemBase) {
			new HfosModal.alert({
				title: 'Transformaciones',
				message: 'El itemBase no se pudo leer'
			});
			return false;
		} else {
			vItemBase = itemBase.getValue();
		};

		var vcantidadObjetivo = 0;
		if (!cantidadObjetivo){
			new HfosModal.alert({
				title: 'Transformaciones',
				message: 'La cantidad base no se pudo leer'
			});
			return false;
		} else {
			if (cantidadObjetivo.getValue()==''){
				vcantidadObjetivo = 0;
			} else {
				vcantidadObjetivo = cantidadObjetivo.getValue();
			}
		};

		if(!vTotal){
			vTotalI = 0;
		} else {
			vTotalI = vTotal.getValue();
		};

		var vNota = nota.getValue();
		if(vNota=="@"){
			this._hyperForm.getMessages().notice("Ingrese el tipo de transformación");
			return false;
		};

		var data = {
			"itemBase": vItemBase,
			"cantidad_objetivo": vcantidadObjetivo,
			"items": Json.encode(this._hyperGrid.getColumn("item")),
			"cantidades": Json.encode(this._hyperGrid.getColumn("cantidad")),
			"nota": vNota,
			"valorBase": vTotalI
		};
		new Tatico.getCalcularTransformacion(data, function(response){
			if(response.status=="FAILED"){
				this._hyperForm.getMessages().error(response.message);
			} else {
				if(response.datos){
					this._hyperGrid._updateRowOnGrid(response.datos);
				}
			}
		}.bind(this));
	},

	/**
	 * Restaura los callbacks de la transacción
	 */
	_restoreTransaction: function()
	{
		var currentState = this._hyperForm.getCurrentState();
		if(currentState=='new'||currentState=='edit'){
			this._prepareForInput();
			this._showDefaultMessage();
			if(this._type=='E'||this._type=='O'){
				this._updateTotales();
			};
		}
	}

});
