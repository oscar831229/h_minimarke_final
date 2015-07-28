
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
 * Facturas
 *
 * Cada instancia de generar facturas
 */
var Facturas = Class.create(HfosProcessContainer, {

	_key: null,

	_movimientos: [],

	_activeMov: 0,

	initialize: function(container){
		this.setContainer(container);
		this._setNewCallbacks();
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
		};
		var movimientoSeleccion = this.getStatusBarElement('movimientoSeleccion');
		if(numberChecked>0){
			movimientoSeleccion.show();
		} else {
			movimientoSeleccion.hide();
		};
	},

	/**
	 * Selecciona una fila del movimiento
	 */
	_selectRowForma: function(checkElement){
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

	_saveFactura: function(){
		var grabarForm = this.getElement('grabarForm')
		new HfosAjax.JsonFormRequest(grabarForm, {
			onLoading: function(grabarForm){
				this.getElement('headerSpinner').show();
				grabarForm.disable();
			}.bind(this, grabarForm),
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success(response.message);
					window.open($Kumbia.path+'temp/'+response.uri);
					Hfos.getApplication().getWorkspace().getWindowManager().getActiveWindow().close();
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

		var saveButton = this.getElement('saveButton');
		saveButton.observe('click', this._saveFactura.bind(this, saveButton));

		var descuentoGeneral = this.selectOne('#descuentoGeneral');
		descuentoGeneral.observe('blur', this._setDescuento.bind(this, descuentoGeneral));

		var queryPedido = this.getElement('queryPedido');
		queryPedido.observe('click', this.queryPedido.bind(this, queryPedido));

		var numeroPedido = this.selectOne('input#numeroPedido');
		if(numeroPedido){
			numeroPedido.observe('blur', this.checkLoadPedido.bind(this, numeroPedido));	
		}

		this.selectOne('input#nitFacturar').activate();

		new HfosTabs(this, 'tabbed', {
			//onChange: this._onChangeTab.bind(this)
		});

		this._addGridCallbacks(1);
		this._addGridFormCallbacks(1);

		this._showTotalBar();

	},

	_setDescuento: function(descuentoGeneral){
		var porcentajeDescuento = 0;
		var precios = this.select('input.precio');
		
		if(descuentoGeneral.getValue().strip()!=''){
			porcentajeDescuento = parseFloat(descuentoGeneral.getValue(), 10);
		};
		
		for(var i=0;i<precios.length;i++){

			var trElement = precios[i].up(1);
			var tdItem = trElement.getElement('item');
			var tdCantidad = trElement.getElement('cantidad');
			var tdItemIvaVenta = trElement.getElement('itemIvaVenta');
			var tdItemPrecioVenta = trElement.getElement('itemPrecioVenta');
			

			this._onBlurItem(tdItem);
			this._onBlurCantidad(tdCantidad, tdItemIvaVenta, tdItemPrecioVenta);
			
		};

		this._totalizeFactura();
	},

	_showTotalBar: function(){

		//Mostrar barra de estado
		var html = '<table width="100%" class="movimientoStatusBar"><tr>';
		html+='<td align="left" style="display:none" class="movimientoSeleccion">Opciones de la Selección <select class="opcionesSeleccion">';
		html+='<option value="@">Seleccione...</option><option value="D">Eliminar</option>'
		html+='</select></td><td class="totalFactura" align="right"></td></tr></table>';
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

		this._totalFactura = this.getStatusBarElement('totalFactura');
		this._totalFactura.update('');

	},

	/**
	 * Elimina las filas seleccionadas del movimiento
	 */
	_deleteRows: function(){
		var numberChecked = 0;
		var checkElements = this.select('.detalle-grid input[type="checkbox"]');
		for(var i=0;i<checkElements.length;i++){
			if(checkElements[i].checked){
				numberChecked++;
			}
		};
		if(numberChecked>0){
			if(checkElements.length==numberChecked){
				new HfosModal.alert({
					title: 'Facturas',
					message: 'La factura debe tener al menos un item'
				});
			} else {
				new HfosModal.confirm({
					title: 'Facturas',
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
						this._totalizeFactura();
					}.bind(this)
				});
			}
		}
	},

	_pushRowForUpdate: function(element, afterPushRow){

		var position = parseInt(element.up(1).retrieve('position'),10);
		var nextPosition = position+1;
		var nextLinea = this.getElement('linea'+nextPosition);

		if(nextLinea==null && nextPosition>0){

			var trElement = document.createElement('TR');
			trElement.addClassName('linea'+nextPosition);
			trElement.addClassName('detalle-linea');

			var tdNumero = document.createElement('TD');
			tdNumero.addClassName('numero');
			tdNumero.update(nextPosition);
			trElement.appendChild(tdNumero);

			var tdCheck = document.createElement('TD');
			tdCheck.update('<input type="checkbox" class="itemCheck" id="itemCheck'+nextPosition+'"/>');
			trElement.appendChild(tdCheck);

			var tdItem = document.createElement('TD');
			tdItem.addClassName('numero');
			tdItem.update('<input type="text" class="item numeric" name="item[]" id="item'+nextPosition+'" size="7"/>');

			var tdItemIvaVenta = document.createElement('DIV');
			tdItemIvaVenta.addClassName('numero');
			tdItemIvaVenta.update('<input type="hidden" class="itemIvaVenta" name="itemIvaVenta[]" id="itemIvaVenta'+nextPosition+'" size="7"/>');
			tdItem.appendChild(tdItemIvaVenta);
			
			var tdItemPrecioVenta = document.createElement('DIV');
			tdItemPrecioVenta.addClassName('numero');
			tdItemPrecioVenta.update('<input type="hidden" class="itemPrecioVenta" name="itemPrecioVenta[]" id="itemPrecioVenta'+nextPosition+'" size="7"/>');
			tdItem.appendChild(tdItemPrecioVenta);

			trElement.appendChild(tdItem);

			var tdDescripcion = document.createElement('TD');
			tdDescripcion.addClassName('numero');
			tdDescripcion.update('<input type="text" class="descripcion" name="descripcion[]" id="descripcion'+nextPosition+'" size="20"/>');
			trElement.appendChild(tdDescripcion);

			var tdCantidad = document.createElement('TD');
			tdCantidad.update('<input type="text" class="cantidad numeric" name="cantidad[]" id="cantidad'+nextPosition+'" size="7"/>');
			trElement.appendChild(tdCantidad);

			var tdTipo = document.createElement('TD');
			var primerTipo = this.select('select.tipo')[0];
			tdTipo.appendChild(primerTipo.cloneNode(true));
			trElement.appendChild(tdTipo);

			var tdprecio = document.createElement('TD');
			tdprecio.update('<input type="text" class="precio numeric" name="precio[]" id="precio'+nextPosition+'" size="9" readonly/>');
			trElement.appendChild(tdprecio);

			var tdTotal = document.createElement('TD');
			tdTotal.update('<input type="text" class="total numeric" name="total[]" id="total'+nextPosition+'" size="9" readonly/>');
			trElement.appendChild(tdTotal);

			this.getElement('ordenes-body').appendChild(trElement);

			this._addGridCallbacks(nextPosition);

			if(typeof afterPushRow == "undefined"){
				if(element.hasClassName('valor')){
					window.setTimeout(function(){
						var itemElement = this.select('input.item')[nextPosition-1];
						itemElement.activate();
					}.bind(this), 100);
				};
				this.scrollToBottom();
				this._notifyContentChange();
			} else {
				if(typeof afterPushRow == "function"){
					afterPushRow();
				}
			}

			this._onBlurCantidad(tdCantidad, tdItemIvaVenta, tdItemPrecioVenta);
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

		//Descripcion de referencias
		var descripcionElement = lineaElement.getElement('descripcion');
		descripcionElement.observe('blur', this._onBlurDescripcion.bind(this, descripcionElement));

		var tipoElement = lineaElement.getElement('tipo');
		tipoElement.observe('change', this._onChangeTipo.bind(this, tipoElement, position))

		//Item de referencias
		var itemElement = lineaElement.getElement('item');
		itemElement.observe('blur', this._onBlurItem.bind(this, itemElement, position));

		new HfosAutocompleter(descripcionElement, 'referencias/queryByName', {
			paramName: 'nombre',
			afterUpdateElement: this._updateCodigoItem.bind(this, itemElement)
		});

		var itemIvaVentaElement = lineaElement.getElement('itemIvaVenta');
		var itemPrecioVentaElement = lineaElement.getElement('itemPrecioVenta');
		
		var cantidadElement = lineaElement.getElement('cantidad');
		cantidadElement.observe('blur', this._onBlurCantidad.bind(this, cantidadElement, itemIvaVentaElement, itemPrecioVentaElement));


	},

	_addGridFormCallbacks: function(position){

		var lineaElement = this.getElement('lineaForma'+position);
		lineaElement.store('position', position);

		var checkElement = lineaElement.getElement('itemCheck');
		checkElement.observe('change', this._selectRowForma.bind(this, checkElement));

		var valorElement = lineaElement.getElement('valorForma');
		valorElement.observe('blur', this._onBlurValorForma.bind(this, valorElement));

	},

	_queryItemPrice: function(trElement, codigoItem, tipo){
		if(codigoItem!=''){
			controllerRequest = 'referencias/queryByItem';
			
			var nitFacturar = this.selectOne('#nitFacturar').getValue();
			var numeroContrato = this.selectOne('#numeroContrato').getValue();
			 
			new HfosAjax.JsonRequest(controllerRequest, {
				method: 'GET',
				parameters: 'codigo='+codigoItem+'&contrato='+numeroContrato+'&nit='+nitFacturar,
				onSuccess: function(trElement, response){
					if(response.status=='OK'){

						trElement.lang = response.precio;

						var itemElement = trElement.getElement('item');

						var descripcionElement = trElement.getElement('descripcion');
						descripcionElement.setValue(response.nombre);

						var cantidadElement = trElement.getElement('cantidad');
						if(cantidadElement.getValue()==''||cantidadElement.getValue()=='0'){
							cantidadElement.setValue(1);
						};

						var descuentoGeneral = this.selectOne('#descuentoGeneral');
						
						var itemIvaVentaElement = trElement.getElement('itemIvaVenta');
						itemIvaVentaElement.setValue(response.ivaVenta);

						var valorPrecionSinDescuento = parseFloat(response.precio, 10);
						var valorPrecio = parseFloat(response.precio, 10);
						if(descuentoGeneral && descuentoGeneral.getValue()>0){
							valorPrecio -= valorPrecio * parseFloat(descuentoGeneral.getValue(), 10) / 100;
						}
						var precioElement = trElement.getElement('precio');
						precioElement.setValue(valorPrecio);

						var total = valorPrecio * parseFloat(cantidadElement.getValue(), 10) ;

						//alert('valor: '+valorPrecio+', total '+total);

						var totalSinDescuento = valorPrecionSinDescuento * parseFloat(cantidadElement.getValue(), 10) ;
						if(response.ivaVenta){
							var oldTotal = total;
							var totalIva = total;
							//alert('total: '+oldTotal+', ivaVenta: '+ivaVenta+', nuevoTotal: '+totalIva+', descuento: '+total);
						}

						var totalElement = trElement.getElement('total');
						totalElement.setValue(Math.round(total));

						this.getMessages().setDefault();
						this._pushRowForUpdate(itemElement);

						this._totalizeFactura();
						
					} else {
						if(response.status=='FAILED'){
							this.getMessages().error(response.message)
						}
					}
				}.bind(this, trElement)
			});
		}
	},

	_onBlurItem: function(itemElement){
		var trElement = itemElement.up(1);
		this._queryItemPrice(trElement, trElement.getElement('item').getValue(), trElement.getElement('tipo').getValue());
	},

	_onChangeTipo: function(tipoElement){
		var trElement = tipoElement.up(1);
		if(tipoElement.getValue()=='N'){
			trElement.getElement('precio').readOnly = false;
		} else {
			trElement.getElement('precio').readOnly = true;
		};
		this._queryItemPrice(trElement, trElement.getElement('item').getValue(), trElement.getElement('tipo').getValue());
	},

	_onBlurDescripcion: function(descripcionElement){
		if(descripcionElement.getValue()!=''){
			this._pushRowForUpdate(descripcionElement);
		}
	},

	_onBlurCantidad: function(cantidadElement, itemIvaVentaElement, tdItemPrecioVenta){
		var trElement = cantidadElement.up(1);
		if(trElement.lang==''){
			if(!trElement.getElement('precio')){
				trElement.getElement('total').setValue('0.00');
				trElement.getElement('precio').setValue('0.00');	
				trElement.getElement('itemIvaVenta').setValue('0.00');
				trElement.getElement('itemPrecioVenta').setValue('0.00');
			}
		} else {
			//alert('trElement.lang :'+trElement.lang)
			var descuentoElement = this.selectOne('input#descuentoGeneral');
			
			var valorIvaVenta = 1;
			//var valorPrecioSinDescuento = parseFloat(trElement.lang, 10);
			var valorPrecioSinDescuento = parseFloat(trElement.lang, 10);
			var valorPrecio = valorPrecioSinDescuento;
			var descuento = 0;

			if(descuentoElement && descuentoElement.getValue()){
				var descuento = parseFloat(descuentoElement.getValue(), 10);
				if(descuento>0){
					valorPrecio -= (valorPrecio * descuento / 100);
				}
			}

			var valorTotal =  valorPrecio * parseFloat(cantidadElement.getValue(), 10);

			trElement.getElement('total').setValue(valorTotal);
			trElement.getElement('precio').setValue(valorPrecio);
		};

		this._pushRowForUpdate(cantidadElement);

		this._totalizeFactura();
	},

	_onBlurDescuento: function(descuentoElement){
		var trElement = descuentoElement.up(1);
		var cantidadElement = trElement.getElement('cantidad');
		if(!cantidadElement){
			alert('not found cantidadElement');
		}
		var itemIvaVentaElement = trElement.getElement('itemIvaVenta');
		var itemPrecioVentaElement = trElement.getElement('itemPrecioVenta');
		//alert('in _onBlurDescuento> cantidad: '+cantidadElement.getValue()+' itemIvaVenta: '+itemIvaVentaElement.getValue())
		this._onBlurCantidad(cantidadElement, itemIvaVentaElement, itemPrecioVentaElement);
		this._pushRowForUpdate(descuentoElement);
		this._totalizeFactura();
	},

	_onBlurValorForma: function(valorElement){
		this._totalizeFactura();
	},

	_totalizeFactura: function(){

		var totalGeneral = 0;
		var totalFormas = 0;

		var totalElements = this.select('input.total');
		var descuentosElements = this.select('input.descuento');
		for(var i=0;i<totalElements.length;i++){
			if(totalElements[i].getValue()!=''){
				var total = parseFloat(totalElements[i].getValue(), 10);
				totalGeneral+=Math.round(total);
			}
		};
		var totalFormasElements = this.select('input.valorForma');
		for(var i=0;i<totalFormasElements.length;i++){
			if(totalFormasElements[i].getValue()!=''){
				totalFormas+=parseFloat(totalFormasElements[i].getValue(), 10);
			}
		};
		totalGeneral = Utils.numberFormat(totalGeneral);
		totalFormas = Utils.numberFormat(totalFormas);
		this._totalFactura.update('<table class="sumasTable"><tr>'+
		'<td>Total Factura <b>'+totalGeneral+'</b></td>'+
		'<td>Total Formas Pago <b>'+totalFormas+'</b></td>'+
		'</tr></table>');
	},

	checkLoadPedido: function(numeroPedido){
		var detalleLineas = this.select('.detalle-linea');
		if(detalleLineas.length>1){
			new HfosModal.confirm({
				title: 'Facturas',
				message: 'Se han agregado referencias al detalle de la factura, ¿Desea ignorarlos y cargar el pedido?',
				onAccept: function(numeroPedido, detalleLineas){
					for(var j=1;j<detalleLineas.length;j++){
						detalleLineas[j].remove();
					};
					this.loadPedido(numeroPedido);
		
				}.bind(this, numeroPedido, detalleLineas)
			});
		} else {
			this.loadPedido(numeroPedido);
		}
	},

	_addLoadRow: function(data, n){
		n++;
		if(data.length>n){
			var fields = [];
			var fieldNames = ['item', 'descripcion', 'cantidad', 'precio', 'itemIvaVenta'];
			var descuentoGeneral = parseFloat(this.selectOne('input#descuentoGeneral').getValue(), 10);
			for(var j=0;j<fieldNames.length;j++){
				var nameField = fieldNames[j];

				if(nameField=='itemIvaVenta'){
					var value = data[n]['ivaVenta'];
				} else { 
					var value = data[n][fieldNames[j]];
				}

				fields[fieldNames[j]] = this.select('.'+nameField)[n];

				if(fields[fieldNames[j]]){
					
					//Si es el precio y hay decuento apliquelo al valor unitario
					if(nameField=='precio' && value){
						var valorUnit = parseFloat(value, 10);
						if(descuentoGeneral>0){
							valorUnit = valorUnit - (valorUnit * descuentoGeneral / 100);
						}
						value = valorUnit;
					}

					fields[fieldNames[j]].setValue(value);

				} else {
					alert('No se encontro campo con selector '+'.'+nameField)
				}
				
			};
			var total = this.select('.total')[n];
			total.setValue(fields['cantidad'].getValue()*fields['precio'].getValue());
			this._pushRowForUpdate(fields['item'], this._addLoadRow.bind(this, data, n));
		} else {
			this._totalizeFactura();				
			this._notifyContentChange();
		}
	},

	/**
	 * Carga un pedido de inventario en facturador
	 * 
	 */
	loadPedido: function(numeroPedido){
		var nitFacturar	= this.selectOne('#nitFacturar').getValue();
		var contrato 	= this.selectOne('#numeroContrato').getValue();
		var pedido 		= parseInt(numeroPedido.getValue(), 10);
		
		if (pedido>0) {
			new HfosAjax.JsonRequest('tatico/getPedido', {
				method: 'GET',
				checkAcl: true,
				parameters: {
					'numeroPedido': pedido,
					'nit': nitFacturar,
					'contrato': contrato
				},
				onSuccess: function(response){
					if(response.status=='OK'){
						this._addLoadRow(response.data.movilin, -1);
					} else {
						this.getMessages().error(response.message);
					}
				}.bind(this)
			});
		}
	},

	queryPedido: function(queryElement){
		var hfosWindow = HfosCommon.findWindow(queryElement);
		new HfosModalForm(hfosWindow, 'pedidos/consultar', {
			notSubmit: true,
			style: {
				'width': '650px'
			},
			afterShow: function(hfosWindow, form){
				this.addQueryPedidoCallbacks(form, hfosWindow);
				var consultarButton = form.getElement('consultarButton');
				consultarButton.observe('click', function(form){
					var pedidoForm = form.getElement('pedidoForm');
					new HfosAjax.FormRequest(pedidoForm, {
						onLoading: function(form){
							form.getElement('formSpinner').show();
						}.bind(this, form),
						onSuccess: function(form, hfosWindow, transport){
							form.getElement('pedidos').update(transport.responseText);
							this.addQueryPedidoCallbacks(form, hfosWindow);
						}.bind(this, form, hfosWindow),
						onComplete: function(form){
							form.getElement('formSpinner').hide();
						}.bind(this, form)
					});
				}.bind(this, form));
			}.bind(this, hfosWindow)
		});
	},

	/**
	 * Agrega callabacks que permiten seleccionar un pedido de la lista de pedidos
	 */
	addQueryPedidoCallbacks: function(form, hfosWindow){
		var pedidosTable = form.selectOne('table#pedidosTable');
		if(pedidosTable!=null){
			var browse = new HfosBrowseData(form);
			browse.fromHtmlTable(form, pedidosTable, 5);
			var seleccionarButtons = pedidosTable.select('input.seleccionarButton');
			for(var i=0;i<seleccionarButtons.length;i++){
				seleccionarButtons[i].lang = seleccionarButtons[i].title;
				seleccionarButtons[i].title = 'Seleccionar el pedido';
				seleccionarButtons[i].observe('click', function(element, form, hfosWindow){
					form.close();
					var pedido = element.lang.split('-');
					var numeroPedido = hfosWindow.selectOne('input#numeroPedido');
					numeroPedido.setValue(pedido[1]);
					numeroPedido.activate();
				}.bind(this, seleccionarButtons[i], form, hfosWindow));
			};
		} else {
			form.getMessages().notice('No se encontraron pedidos');
		}
	}

});

HfosBindings.late('win-facturas', 'afterCreate', function(hfosWindow){
	var facturas = new Facturas(hfosWindow);
});
