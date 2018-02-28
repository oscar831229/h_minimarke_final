
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

var Pedido = Class.create({

	id: 0,

	_accountMasterId: 0,

	_numeroCuenta: 0,

	_tipoComanda: "M",

	_searchMode: false,

	_searching: false,

	_pedirPersonas: "S",

	_pedirAsientos: "S",

	_numeroAsientos: 0,

	_menuCache: [],

	_modifiersCache: [],

	_menuDetails: null,

	_menuDetailsHeight: '300px',

	_listaPedidoHeight: '300px',

	_menuItemsButtons: [],

	_cuentasElement: null,

	_comandasElement: null,

	_menuItemSelected: null,

	_lastMenuSelected: null,

	getMenuItems: function(element, id, event){

		if(this._lastMenuSelected!=element){
			element.className = "menuButtonSelected";
			if(this._lastMenuSelected!=null){
				this._lastMenuSelected.className = "menuButton";
			};
			this._lastMenuSelected = element;
		};

		if(typeof this._menuCache[id] == "undefined"){
			new Ajax.Request(Utils.getKumbiaURL("order/getMenu/"+id), {
				method: 'GET',
				onSuccess: function(id, transport){
					this._menuCache[id] = JSON.parse(transport.responseText);
					this.updateMenuDetails(this._menuCache[id]);
				}.bind(this, id)
			});
		} else {
			this.updateMenuDetails(this._menuCache[id]);
		}
	},

	adjustItemsList: function(items){
		if(this._menuDetailsElement.style.height==''){
			new Effect.Morph(this._menuDetailsElement, {
				duration: 0.3,
				style: {
					height: this._menuDetailsHeight
				}
			});
		};
		if(items.length>8){
			this._menuDetailsElement.style.overflowY = 'scroll';
		} else {
			this._menuDetailsElement.style.overflowY = 'auto';
		}
	},

	updateMenuDetails: function(items)
	{
		this._menuDetailsElement.innerHTML = '';
		for (var i = 0; i < items.length; i++) {
			var button = document.createElement('BUTTON');
			button.className = 'menuItemButton';
			button.title = 'Precio: '+items[i].valor;
			button.update(items[i].nombre);
			button.observe('click', this.addItemToAccount.bind(this, button, items[i]));
			this._menuDetailsElement.appendChild(button);

			if (items.length == 1) {
				//alert(items.length);
				this.addItemToAccount(button, items[0]);
			}
		};
		
		this.adjustItemsList(items);
	},

	updateModifiersDetails: function(items)
	{
		this._menuDetailsElement.innerHTML = '';
		for (var i = 0;i < items.length; i++) {
			var button = document.createElement('BUTTON');
			button.className = 'menuItemButton';
			if (i == 0) {
				button.observe('click', this.addItemToAccount.bind(this, button, items[i]));
			} else {
				button.addClassName('modifierButton');
				button.observe('click', this.addModifier.bind(this, button, items[i]));
			};
			button.title = 'Precio: '+items[i].valor;
			button.update(items[i].nombre);
			this._menuDetailsElement.appendChild(button);
		};
		this.adjustItemsList(items);
	},

	addItemToAccount: function(element, item)
	{

		if (element != this._menuItemSelected) {
			element.addClassName("menuItemButtonSelected");
			if (this._menuItemSelected) {
				this._menuItemSelected.removeClassName("menuItemButtonSelected")
			};
			this._menuItemSelected = element;
		};

		new Ajax.Request(Utils.getKumbiaURL("order/addToList/"+item.id), {
			method: 'GET',
			onSuccess: function(item, transport){
				this._preOrder.update(transport.responseText);
				if (item.modifiers != 0) {
					this.getModifiers(item.id);
				};
				this.preOrderCallbacks();
			}.bind(this, item),
			onFailure: function(t){
				this._preOrder.update(t.responseText);
			}
		});
	},

	getModifiers: function(id)
	{
		if (typeof this._modifiersCache[id] == "undefined") {
			new Ajax.Request(Utils.getKumbiaURL("order/getModifiers/" + id), {
				method: 'GET',
				onSuccess: function(id, transport){
					this._modifiersCache[id] = JSON.parse(transport.responseText);
					this.updateModifiersDetails(this._modifiersCache[id]);
				}.bind(this, id)
			});
		} else {
			this.updateModifiersDetails(this._modifiersCache[id]);
		}
	},

	addModifier: function(element, item) {
		if (element!=this._menuItemSelected) {
			element.addClassName("menuItemButtonSelected");
			if (this._menuItemSelected) {
				this._menuItemSelected.removeClassName("menuItemButtonSelected")
			};
			this._menuItemSelected = element;
		};
		new Ajax.Request(Utils.getKumbiaURL("order/addModifier/"+item.id), {
			method: 'GET',
			onSuccess: this.refresh.bind(this)
		});
	},

	deleteModifier: function(element) {
		Modal.confirm("¿Seguro desea eliminar el modificador '"+element.innerHTML+"'?", function(accountModifierId){
			new Ajax.Request(Utils.getKumbiaURL("order/deleteModifier/"+accountModifierId), {
				method: 'GET',
				onSuccess: this.refresh.bind(this)
			});
		}.bind(this, element.lang));
	},

	addMenuItemsHotKeys: function(){
		var avalLetters = [];
		for(var i=65;i<91;i++){
			var ch = String.fromCharCode(i).toLowerCase();
			if(!HotKeys.include()){
				avalLetters.push(ch);
			}
		};
		iHotKeys = [];
		MenuItemsHotKeys = [];
		var menuButtons = $$(".menuItemButton");
		for(var j=0;j<menuButtons.length;j++){
			var element = menuButtons[j];
			if(avalLetters.length>j){
				element.innerHTML += "<span class='mcode'>&nbsp;("+avalLetters[j]+")</span>";
				element.id = "mit"+j;
				MenuItemsHotKeys.push(j);
				iHotKeys.push(avalLetters[j]);
			}
			j++;
		};
	},

	refresh: function(type){
		if(typeof type != "string"){
			type = ""
		};
		new Ajax.Request(Utils.getKumbiaURL("order/refresh/"+type), {
			method: 'GET',
			onSuccess: function(transport){
				this._preOrder.update(transport.responseText);
				this.preOrderCallbacks();
			}.bind(this)
		});
	},

	closeRefresh: function(){
		var myWindow = $('myWindow');
		if (myWindow) {
			myWindow.close();
		};
		this.refresh();
	},

	addAutomaticComanda: function() {
		new Ajax.Request(Utils.getKumbiaURL("order/addNextComanda"), {
			method: 'GET',
			onSuccess: function(transport){
				var number = JSON.parse(transport.responseText);
				var option = document.createElement("OPTION");
				option.value = number;
				option.text = number;
				this._comandasElement.appendChild(option);
				this._comandasElement.selectedIndex = this._comandasElement.options.length-1;
				this.pedirPersonas();
			}.bind(this)
		});
	},

	addComanda: function() {
		new WINDOW.open({
			title: "Digite el Número de Comanda",
			action: 'numero',
			width: "220px",
			height: "390px",
			background: "#c0c0c0",
			onbeforeclose: function(action) {
				if(action=='cancel'){
					if(this._comandasElement.options.length==0){
						return this.backToTables();
					}
				};
				var numero = parseInt($('number').value, 10);
				if(numero<=0){
					if(this._comandasElement.options.length==0){
						Growler.show('Debe ingresar un número de Comanda antes de empezar');
						return false;
					} else {
						if(numero==0){
							Growler.show('Debe ingresar un número de Comanda antes de empezar');
						}
						return;
					}
				};
				this._addComandaInternal(numero);
			}.bind(this)
		});
	},

	_addComandaInternal: function(numero){
		new Ajax.Request(Utils.getKumbiaURL("order/existeComanda/"+numero), {
			method: 'GET',
			onSuccess: function(numero, transport){

				var response = JSON.parse(transport.responseText);
				if(response>0){
					Growler.show('Esta comanda ya existe en otra cuenta');
					return false;
				};

				var options = this._comandasElement.options;
				for(var i=0;i<options.length;i++){
					if(options[i].value==numero){
						Growler.show('Esta comanda ya existe');
						return false;
					}
				};

				var option = document.createElement("OPTION");
				option.value = numero;
				option.text = numero;
				this._comandasElement.appendChild(option);
				this.setLastComanda(numero);
				window.setTimeout(this.pedirPersonas.bind(this), 300);
			}.bind(this, numero)
		});
	},

	/**
	 * Elimina una comanda del pedido
	 */
	deleteComanda: function(){
		new Ajax.Request(Utils.getKumbiaURL("order/getNumeroComandas"), {
			method: 'GET',
			onSuccess: function(transport){
				var response = JSON.parse(transport.responseText);
				if(response>1){
					Modal.confirm('¿Esta seguro de eliminar esta comanda?', function(){
						new Ajax.Request(Utils.getKumbiaURL("order/queryComanda/"+this._comandasElement.getValue()), {
							method: 'GET',
							onSuccess: function(transport){
								var response = JSON.parse(transport.responseText);
								if(response>0){
									Growler.show('No se puede borrar esta comanda porque tiene items atendidos');
									return false;
								} else {
									new Ajax.Request(Utils.getKumbiaURL("order/deleteComanda/"+this._comandasElement.getValue()), {
										method: 'GET',
										onSuccess: function(){
											Growler.show("Se eliminó la comanda correctamente");
											this.refresh();
											this.setLastComanda();
										}.bind(this)
									});
								}
							}.bind(this)
						});
					}.bind(this));
				} else {
					Growler.show('No se puede eliminar la comanda porque es la única existente');
					return false;
				}
			}.bind(this)
		});
	},

	pedirPersonas: function(){
		if(this._numeroAsientos==0){
			if(this._pedirPersonas=="S"){
				new WINDOW.open({
					title: "Digite el Número de Personas",
					action:'numero',
					width: "220px",
					height: "390px",
					onbeforeclose: function(action){
						if(action=="cancel"){
							return this.backToTables();
						};
						var number = parseInt($('number').value, 10);
						if(number>14){
							Growler.show("El número de personas es muy alto");
							return false;
						};
						if(number<1){
							Growler.show("El número de personas es muy bajo");
							return false;
						};
						this.addSillas(number);
						this.setNumeroSillas(number);
					}.bind(this)
				});
			}
		}
	},

	setNumeroSillas: function(number){
		new Ajax.Request(Utils.getKumbiaURL("order/setNumberAsientos/"+number), {
			method: 'GET'
		});
	},

	setActiveAsiento: function(number){
		new Ajax.Request(Utils.getKumbiaURL("order/setActiveAsiento/"+number), {
			method: 'GET'
		});
	},

	addSillas: function(number){

		var i = 0;
		var number = parseInt(number, 10)+1;
		for(i=1;i<=number;i++){
			var chair = $("s"+i);
			new Effect.Appear(chair, {
				duration: 0.6
			});
			if(!chair.number){
				new Event.observe(chair, "click", this.changeSelectedSilla.bind(this, i))
			};
			chair.number = i;
			chair.show();
			if(i==number){
				new Effect.Opacity(chair, { duration: 0.5, to: 0.4 });
			}
		};
		if(number>this._numeroAsientos){
			this._numeroAsientos = number;
		}
	},

	changeSelectedSilla: function(number){
		for(var i=1;i<15;i++){
			var silla = $("s"+i);
			if(silla.visible()){
				if(i==number){
					silla.removeClassName("inactiveAsiento");
					silla.addClassName("activeAsiento");
					this.setActiveAsiento(number);
				} else {
					silla.removeClassName("activeAsiento");
					silla.addClassName("inactiveAsiento");
				}
			}
		}
	},

	addCuenta: function(){
		new Ajax.Request(Utils.getKumbiaURL("order/addCuenta"), {
			method: 'GET',
			onSuccess: function(transport){

				var nuevaCuenta = JSON.parse(transport.responseText);
				var options = this._cuentasElement.options;
				for(var i=0;i<options.length;i++){
					if(options[i].value==nuevaCuenta){
						nuevaCuenta++;
						continue;
					}
				};

				var option = document.createElement("OPTION");
				option.value = nuevaCuenta;
				option.text = nuevaCuenta;
				this._cuentasElement.appendChild(option);

				$("documento").value = 0;
				$("nombre_cliente").value = 'PARTICULAR';
				$("nombre_cliente_span").innerHTML = 'PARTICULAR';
				$("habitacion_id").value = 0;
				$("habitacion_id_span").innerHTML = "";
				$("nota").value = "";
				$("nota_span").innerHTML = "";

				this.setLastCuenta();

			}.bind(this)
		});
	},

	setLastCuenta: function(){
		this._cuentasElement.selectedIndex = this._cuentasElement.options.length - 1;
		new Ajax.Request(Utils.getKumbiaURL("order/setCuenta/"+this._cuentasElement.getValue()));
	},

	deleteCuenta: function(){
		new Ajax.Request(Utils.getKumbiaURL("order/getNumeroCuentas"), {
			method: 'GET',
			onSuccess: function(transport){
				var response = JSON.parse(transport.responseText);
				if(response>1){
					Modal.confirm('¿Esta seguro de eliminar esta cuenta?', function(){
						new Ajax.Request(Utils.getKumbiaURL("order/queryCuenta/"+this._cuentasElement.getValue()), {
							method: 'GET',
							onSuccess: function(transport){
								var response = JSON.parse(transport.responseText);
								if(response>0){
									Growler.show('No se puede borrar esta cuenta porque tiene items atendidos');
									return false;
								} else {
									new Ajax.Request(Utils.getKumbiaURL("order/deleteCuenta/"+this._cuentasElement.getValue()), {
										method: 'GET',
										onSuccess: function(){
											Growler.show("Se eliminó la cuenta correctamente");
											this.refresh();
											this.setLastCuenta();
										}.bind(this)
									});
								}
							}.bind(this)
						});
					}.bind(this));
				} else {
					Growler.show('No se puede eliminar la cuenta porque es la única existente');
					return false;
				}
			}.bind(this)
		});
	},

	onChangeCuenta: function(){
		new Ajax.Request(Utils.getKumbiaURL("order/setCuenta/"+this._cuentasElement.getValue()), {
			onSuccess: function(transport){
				var response = JSON.parse(transport.responseText);
				if(response.status=='OK'){
					$("documento").value = response.documento;
					$("nombre_cliente").value = response.cliente;
					$("nombre_cliente_span").innerHTML = response.cliente;
					if(response.habitacion!=-1){
						$("habitacion_id").value = response.habitacion;
						$("habitacion_id_span").innerHTML = "["+response.habitacion+"]";
					} else {
						$("habitacion_id").value = 0;
						$("habitacion_id_span").innerHTML = "";
					};
					if(response.nota!=null){
						$("nota").value = response.nota;
						$("nota_span").innerHTML = "<br><b>Nota:</b>"+response.nota;
					} else {
						$("nota").value = "";
						$("nota_span").innerHTML = "";
					};
					$("tipo_venta").setValue(response.tipo_venta);
					if(response.estado=='B'){
						if(response.tipo_venta=='F'){
							Growler.show("Ya se ha generado la factura en esta cuenta");
						} else {
							Growler.show("Ya se ha generado la orden de servicio en esta cuenta");
						}
					}
				}
			}
		});
	},

	onChangeComanda: function(){
		new Ajax.Request(Utils.getKumbiaURL("order/setComanda/"+this._comandasElement.getValue()), {
			method: 'GET'
		});
		this._comandasElement.blur();
	},

	setLastComanda: function(number){
		this._comandasElement.selectedIndex = this._comandasElement.options.length - 1;
		new Ajax.Request(Utils.getKumbiaURL("order/setComanda/"+number), {
			method: 'GET'
		});
	},

	toggleComanda: function(element){
		var trElement = element.up(1);
		var comanda = element.innerHTML;
		var select = document.createElement('SELECT');
		var options = this._comandasElement.options;
		for(var i=0;i<options.length;i++){
			var option = document.createElement("OPTION");
			option.value = options[i].value;
			option.text = options[i].text;
			if(option.value==comanda){
				option.selected = true;
			};
			select.appendChild(option);
		};
		select.observe('change', this.changeItemComanda.bind(this, trElement.lang, select));
		select.observe('blur', function(span, select){
			span.innerHTML = select.getValue();
			select.parentNode.removeChild(select);
		}.bind(this, element, select));
		element.innerHTML = "";
		element.up().appendChild(select);
		select.activate();
	},

	changeItemComanda: function(accountId, comanda){
		new Ajax.Request(Utils.getKumbiaURL("order/changeItemComanda/"+accountId+"/"+comanda.getValue()), {
			method: 'get',
			onSuccess: this.refresh.bind(this)
		});
	},

 	toggleCuenta: function(element){
 		var trElement = element.up(1);
		var cuenta = element.innerHTML.replace("[", "").replace("]", "");
		var select = document.createElement('SELECT');
		var options = this._cuentasElement.options;
		for(var i=0;i<options.length;i++){
			var option = document.createElement("OPTION");
			option.value = options[i].value;
			option.text = options[i].text;
			if(option.value==cuenta){
				option.selected = true;
			};
			select.appendChild(option);
		};
		select.observe('change', this.changeItemCuenta.bind(this, trElement.lang, select));
		select.observe('blur', function(span, select){
			span.innerHTML = "["+select.getValue()+"]";
			select.parentNode.removeChild(select);
		}.bind(this, element, select));
		element.innerHTML = "";
		element.up().appendChild(select);
		select.activate();
	},

	changeItemCuenta: function(accountId, cuenta){
		new Ajax.Request(Utils.getKumbiaURL("order/changeItemCuenta/"+accountId+"/"+cuenta.getValue()), {
			method: 'get',
			onSuccess: this.refresh.bind(this)
		});
	},

	deleteItem: function(element){
		var trElement = element.up(1);
		var itemName = trElement.querySelector('span.itemName');
		Modal.confirm("¿Seguro desea cancelar la cantidad pendiente de '"+itemName.innerHTML+"'?", function(){
			new Effect.Fade(trElement, {
				duration: 0.3,
				afterFinish: function(trElement){
					new Ajax.Request(Utils.getKumbiaURL("order/deleteItem/"+trElement.lang), {
						method: 'GET',
						onSuccess: this.refresh.bind(this)
					});
				}.bind(this, trElement)
			});
		}.bind(this, trElement))
	},

	changeDiscount: function(element){
		var trElement = element.up(1);
		new WINDOW.open({
			title: "Digite el Porcentaje de Descuento",
			action:'numero',
			width: "220px",
			height: "390px",
			onbeforeclose: function(accountId, action){
				if(action=="cancel"){
					return;
				};
				var number = parseInt($('number').value, 10);
				if(number>100){
					Growler.show("El porcentaje de descuento no puede ser mayor a 100");
					return false;
				};
				new Ajax.Request(Utils.getKumbiaURL("order/changeDiscount/"+accountId+"/"+number), {
					method: 'GET',
					onSuccess: this.refresh.bind(this)
				});
			}.bind(this, trElement.lang)
		});
	},

	changeQuantity: function(element){
		var trElement = element.up(1);
		new WINDOW.open({
			title: "Digite la Cantidad",
			action:'numero',
			width: "220px",
			height: "370px",
			onbeforeclose: function(accountId, action) {
				if(action=="cancel"){
					return;
				};
				var number = parseInt($('number').value, 10);
				if(number=="0"){
					Growler.show("La cantidad debe ser mayor a cero");
					return false;
				};
				new Ajax.Request(Utils.getKumbiaURL("order/changeQuantity/"+accountId+"/"+number), {
					method: 'GET',
					onSuccess: this.refresh.bind(this)
				});
			}.bind(this, trElement.lang)
		});
	},

	showInvoice: function(){
		var type = $F('tipo_venta');
		if($('documento').value!="0"){
			if($('nombre_cliente').value!="PARTICULAR"){
				if(type=='F'){
					Modal.confirm('¿Seguro desea generar la Factura?', function(){
						window.open(Utils.getKumbiaURL('factura/index/'+this._numeroCuenta+'/'+this._accountMasterId), null, 'width=300, height=700, toolbar=no, statusbar=no')
						new Utils.redirectToAction('order/add/'+this._id);
					}.bind(this));
				} else {
					//Socios
					if(type=='S'){
						Modal.confirm('¿Seguro desea asignar estos cargos a la factura de socios?', function(){
							window.open(Utils.getKumbiaURL('factura/index/'+this._numeroCuenta+'/'+this._accountMasterId), null, 'width=300, height=700, toolbar=no, statusbar=no')
							new Utils.redirectToAction('order/add/'+this._id);
						}.bind(this));
					} else {
						Modal.confirm('¿Seguro desea generar la Orden de Servicio?', function(){
							window.open(Utils.getKumbiaURL('factura/index/'+this._numeroCuenta+'/'+this._accountMasterId), null, 'width=300, height=700, toolbar=no, statusbar=no')
							new Utils.redirectToAction('order/add/'+this._id);
						}.bind(this));
					}
				};
			} else {
				if(type=='F'){
					Growler.show('Debe especificar el cliente antes de generar la Factura');
				} else {
					Growler.show('Debe especificar el cliente antes de generar la Orden de Servicio');
				};
				this.setCustomerName();
				return;
			}
		} else {
			if(type=='F'){
				Growler.show('Debe especificar el cliente antes de generar la Factura');
			} else {
				Growler.show('Debe especificar el cliente antes de generar la Orden de Servicio');
			};
			this.setCustomerName();
			return;
		}
	},

	deleteItems: function(){
		var accountItems = [];
		var checkItems = this._preOrder.querySelectorAll('input.checkItem');
		for(var i=0;i<checkItems.length;i++){
			if(checkItems[i].checked==true){
				accountItems.push(checkItems[i].value)
			}
		};
		if(accountItems.length>0){
			Modal.confirm("¿Desea eliminar los items seleccionados?", function(accountItems){
				new Ajax.Request(Utils.getKumbiaURL("order/cancelItems"), {
					parameters: {
						items: accountItems.join(",")
					},
					onSuccess: this.refresh.bind(this)
				});
			}.bind(this, accountItems));
		} else {
			Growler.show('Seleccione un item al menos a eliminar');
		}
	},

	onChangeTipoVenta: function(element, event){
		var numItems = this._preOrder.querySelectorAll('tr.orderRow').length;
		if(numItems>0){
			if(element.getValue()=='F'){
				$('gendoc').update("Imprimir<br>Factura");
			} else {
				$('gendoc').update("Imprimir<br>Order");
			};
			new Ajax.Request(Utils.getKumbiaURL('order/changeTipoVenta/'+element.getValue()), {
				method: 'GET',
				onSuccess: this.refresh.bind(this)
			});
		} else {
			Growler.show('Agregue items a la cuenta antes de indicar el tipo de pedido');
			new Event.stop(event);
		}
	},

	enableShortCuts: function(event){
		if(this._searchMode==false){
			if(!$('myWindow')){
				if(event.ctrlKey==true){
					if(event.keyCode==49){
						this.goToModifiers();
						return;
					};
					if(event.keyCode==50){
						this.setCustomerName();
						return;
					};
					if(event.keyCode==51){
						this.payAccount();
						return;
					};
					if(event.keyCode==52){
						this.showInvoice();
						return;
					};
					if(event.keyCode==48){
						this.showStatement();
						return;
					};
				}
				/*for(var k=0;k<MenuHotKeys.length;k++){
					if(String.fromCharCode(event.keyCode).toLowerCase()==HotKeys[k]){
						$('mi'+MenuHotKeys[k]).click();
						return;
					}
				};
				for(var k=0;k<MenuItemsHotKeys.length;k++){
					if(String.fromCharCode(event.keyCode).toLowerCase()==iHotKeys[k]){
						$('mit'+MenuItemsHotKeys[k]).click();
						return;
					}
				}*/
			}
		}
	},

	selectItem: function(element){
		var itemsSelected = this._preOrder.querySelectorAll('tr.itemSelected');
		for(var i=0;i<itemsSelected.length;i++){
			itemsSelected[i].removeClassName('itemSelected');
		};
		element.addClassName('itemSelected');
		new Ajax.Request(Utils.getKumbiaURL("order/changeSelectedItem/"+element.lang), {
			method: 'GET'
		});
	},

	selectAllItems: function(){
		var checkItems = this._preOrder.querySelectorAll('input.checkItem');
		for(var i=0;i<checkItems.length;i++){
			if(checkItems[i].disabled==false){
				checkItems[i].checked = true;
			}
		}
	},

	prepareBuscarItem: function(){
		var buscarItem = $("buscarItem");
		buscarItem.observe("focus", function(event){
			if(this.value=="Buscar por nombre"){
				this.value = "";
				this.style.color = "#000000";
			}
			this.activate();
		});
		buscarItem.observe("blur", function(event){
			if(this.value==""){
				this.value = "Buscar por nombre";
				this.style.color = "#c0c0c0";
			}
		});
		buscarItem.observe("keydown", function(event){
			this._searchMode = true;
		}.bind(this));
		buscarItem.observe("keyup", function(buscarItem, event){
			if(this._searching==false){
				if(buscarItem.value.length>1){
					this._searching = true;
					new Ajax.Request(Utils.getKumbiaURL("order/searchItem"), {
						parameters: {
							text: buscarItem.getValue()
						},
						onSuccess: function(transport){
							this.updateMenuDetails(JSON.parse(transport.responseText));
							this._searching = false;
						}.bind(this),
						onFailure: function(transport){
							alert(transport.responseText);
							this._searching = false;
						}.bind(this)
					});
				} else {
					this.updateMenuDetails([]);
				}
			};
			this._searchMode = false;
		}.bind(this, buscarItem));
	},

	changePrice: function(element){
		new WINDOW.open({
			title: "Digite el nuevo Precio Unitario",
			action: 'numero',
			width: "220px",
			height: "390px",
			background: "#c0c0c0",
			onbeforeclose: function(element, action){
				if(action=='cancel'){
					return false;
				};
				var trElement = element.up(1);
				var numero = parseInt($('number').value, 10);
				if(numero>-1){
					new Ajax.Request(Utils.getKumbiaURL("order/changePrice/"+trElement.lang+"/"+numero), {
						onSuccess: this.refresh.bind(this)
					});
				}
			}.bind(this, element)
		});


		/*var input = document.createElement('INPUT');
		input.type = 'text';
		input.size = '10';
		input.maxlength = '12';
		input.lang = element.lang;
		input.value = element.innerHTML.replace("(", "").replace(')', '');
		//input.observe('change', changePrice);
		//input.observe('blur', changePrice);
		input.observe('keyup', function(event){
			if(event.keyCode==Event.KEY_RETURN){
				this.blur();
			}
		});
		element.innerHTML = "";
		element.appendChild(input);
		input.activate();*/
	},

	captureServicio: function()
	{
		new WINDOW.open({
			title: "Digite la Propina",
			action:'numero',
			width: "220px",
			height: "370px",
			onbeforeclose: function(action){
				if(action!='cancel'){
					var number = $('number').getValue();
					if(number!=''){
						number = parseFloat(number, 10)
					} else {
						number = 0;
					};
					new Ajax.Request(Utils.getKumbiaURL('order/setPropina'), {
						parameters: {
							valor: number
						},
						onSuccess: function(){
							$('servicio').setValue(number)
						}.bind(this, number)
					});
					window.setTimeout(function(){
						$('servicio').blur();
					}, 200);
				}
			}
		});
	},

	preOrderCallbacks: function()
	{

		var preOrder = this._preOrder;
		var spans = preOrder.querySelectorAll('span.ccom');
		for (var i = 0; i < spans.length;i++){
			var element = spans[i];
			element.title = "Haga Click Para Cambiar Comanda...";
			element.observe('click', this.toggleComanda.bind(this, element));
		};

		var spans = preOrder.querySelectorAll('span.ccue');
		for (var i = 0; i < spans.length;i++){
			var element = spans[i];
			element.title = "Haga Click Para Cambiar la Cuenta...";
			element.observe('click', this.toggleCuenta.bind(this, element));
		};

		var spans = preOrder.querySelectorAll('span.cPrecio');
		for (var i = 0; i < spans.length;i++){
			var element = spans[i];
			if(element.lang=="ch"){
				element.title = "Haga Click Para Cambiar Precio...";
				element.observe('click', this.changePrice.bind(this, element));
			} else {
				element.title = "Este item no permite modificar el precio";
				element.observe('click', function(){
					Growler.show("Este item no permite modificar el precio");
				});
			}
		};

		var orderRows = preOrder.querySelectorAll('tr.orderRow');
		for(var i=0;i<orderRows.length;i++){
			var element = orderRows[i];
			element.observe('click', this.selectItem.bind(this, element));
		};

		var deleteModifiers = preOrder.querySelectorAll('.deleteModifier');
		for(var i=0;i<deleteModifiers.length;i++){
			deleteModifiers[i].observe('click', this.deleteModifier.bind(this, deleteModifiers[i]));
			deleteModifiers[i].title = 'Eliminar este Modificador';
		};

		var changeQuantitys = preOrder.querySelectorAll('.changeQuantity');
		for(var i=0;i<changeQuantitys.length;i++){
			changeQuantitys[i].observe('click', this.changeQuantity.bind(this, changeQuantitys[i]));
			changeQuantitys[i].title = 'Cambiar Cantidad Pedida';
		};

		var changeDiscounts = preOrder.querySelectorAll('.changeDiscount');
		for(var i=0;i<changeDiscounts.length;i++){
			changeDiscounts[i].observe('click', this.changeDiscount.bind(this, changeDiscounts[i]));
			changeDiscounts[i].title = 'Cambiar Descuento';
		};

		var deleteItems = preOrder.querySelectorAll('img.deleteItem');
		for(var i=0;i<deleteItems.length;i++){
			deleteItems[i].observe('click', this.deleteItem.bind(this, deleteItems[i]));
			deleteItems[i].title = 'Eliminar este Item';
		};

		var deleteDiscounts = preOrder.querySelectorAll('a.deleteDiscount');
		for(var i=0;i<deleteDiscounts.length;i++){
			deleteDiscounts[i].observe('click', this.deleteDiscount.bind(this, deleteDiscounts[i].lang));
			deleteDiscounts[i].title = 'Eliminar este descuento';
		};

		this._cuentasElement = $('cuentas');
		this._comandasElement = $('comandas');

		this._cuentasElement.observe('change', this.onChangeCuenta.bind(this));
		this._comandasElement.observe('change', this.onChangeComanda.bind(this));

		var plusComanda = $('plusComanda');
		plusComanda.observe('click', this.addComanda.bind(this));
		plusComanda.title = "Agregar Comanda";

		var minusComanda = $('minusComanda');
		minusComanda.observe('click', this.deleteComanda.bind(this));
		minusComanda.title = "Eliminar Comanda Actual";

		var plusCuenta = $('plusCuenta');
		plusCuenta.observe('click', this.addCuenta.bind(this));
		plusCuenta.title = "Agregar Cuenta";

		var minusComanda = $('minusCuenta');
		minusComanda.observe('click', this.deleteCuenta.bind(this));
		minusComanda.title = "Eliminar Cuenta Actual";

		$('servicio').observe('focus', this.captureServicio.bind(this));
		$('selectAllArrow').observe('click', this.selectAllItems.bind(this));

		var tipoVenta = $('tipo_venta');
		if(tipoVenta.getValue()=='F'){
			$('gendoc').update("Imprimir<br>Factura");
		} else {
			$('gendoc').update("Imprimir<br>Orden");
		};
		tipoVenta.observe('change', this.onChangeTipoVenta.bind(this, tipoVenta));

		if(this._pideAsientos=='N'){
			$('asientosDiv').hide();
		} else {
			if(this._numeroAsientos>0){
				this.addSillas(this._numeroAsientos-1);
			}
		};

		var itemsChanged = this._preOrder.querySelectorAll('tr.itemChanged');
		for(var i=0;i<itemsChanged.length;i++){
			new Effect.Highlight(itemsChanged[i]);
		};

		this.adjustOrderList();

	},

	getDiscounts: function()
	{
		new WINDOW.open({
			action: "order/discount",
			width: "400px",
			height:"400px",
			title: "Aplicar un Descuento",
			afterRender: function(){
				var myWindow = $('myWindow');
				var discountButtons = myWindow.querySelectorAll('button.commandButtonBig');
				for (var i = 0; i < discountButtons.length; i++) {
					discountButtons[i].observe('click', this.applyDiscount.bind(this, discountButtons[i].lang));
				};
			}.bind(this)
		});
	},

	applyDiscount: function(id)
	{
		new Ajax.Request(Utils.getKumbiaURL("order/applyDiscount/" + id), {
			onSuccess: function(){
				$("myWindow").close();
				this.refresh();
			}.bind(this)
		});
	},

	deleteDiscount: function(id)
	{
		new Ajax.Request(Utils.getKumbiaURL("order/deleteDiscount/" + id), {
			onSuccess: this.refresh.bind(this)
		});
	},

	cancelOrder: function()
	{
		Modal.confirm("¿Desea cancelar todas las cuentas en este pedido?", function(){
			new Utils.redirectToAction("cancel/docancel");
		});
		/*new WINDOW.open({
			action: "order/cancelOrder",
			width: "550px",
			height: "420px",
			title: "Cancelar Pedido",
			afterRender: this.addCustomerCallback.bind(this)
		});*/
	},

	setCustomerName: function()
	{
		var numItems = this._preOrder.querySelectorAll('tr.orderRow').length;
		if (numItems > 0) {
			var type = $F('tipo_venta');
			if (type == 'H' || type == 'P') {
				new WINDOW.open({
					action: "order/customerName",
					width: "940px",
					height: "620px",
					title: "Seleccionar Folio",
					afterRender: this.addCustomerCallback.bind(this)
				});
			} else {
				new WINDOW.open({
					action: "order/customerName",
					width: "500px",
					height: "233px",
					title: "Seleccionar Cliente",
					afterRender: this.addCustomerCallback.bind(this)
				});
			}
		} else {
			Growler.show('Debe agregar items a la lista antes de definir el cliente');
		}
	},

	addCustomerCallback: function(){
		var aplSubmit = $("apl_submit");
		if(aplSubmit){
			aplSubmit.observe("click", function(){
				ajaxRemoteForm($("myform"), "customer_messages");
			});
			var habitacion = $('habitacion');
			if(habitacion){
				window.setTimeout(function(){
					$("numHabitacion").activate();
				}, 200);
				habitacion.observe('change', this.getHuespedInfo.bind(this, habitacion));
				var huespedButtons = $$('.huespedButton');
				for(var i=0;i<huespedButtons.length;i++){
					huespedButtons[i].observe('click', this.selectThisFolio.bind(this, huespedButtons[i]))
				}
			} else {
				window.setTimeout(function(){
					if($("nombre")){
						$("nombre").activate();
					};
					new Ajax.Autocompleter("nombre", "nombre_choices", Utils.getKumbiaURL('order/queryCustomers'), {
						afterUpdateElement: function(detail, selected){
							$('documento_cliente').setValue(selected.id);
						}
					});
				}, 200);
			}
		} else {
			var numeroAccion = $('numeroAccion');
			if(numeroAccion){
				window.setTimeout(function(){
					$("numeroAccion").activate();
				}, 200);
				numeroAccion.observe('keyup', this.accionKeyup.bind(this, numeroAccion));
				$('myWindow').setStyle({background: "#fff"});
			} else {
				$('myWindow').setStyle({
					width: "400px",
					height: "135px"
				});
			}
		}
	},

	getHuespedInfo: function(element){
		new Ajax.Request(Utils.getKumbiaURL('order/getHuespedInfo/'+element.getValue()), {
			onSuccess: function(transport){
				$('huesped_info').update(transport.responseText);
			}
		});
	},

	selectThisFolio: function(element){
		$("habitacion").setValue(element.id);
		ajaxRemoteForm($("myform"), "customer_messages");
	},

	getCustomerId: function(iid, xid){
		$('documento_cliente').value = xid.id
	},

	accionKeyup: function(element, event)
	{
		if (element.value.length > 0) {
			try
			{
				new Ajax.Request(Utils.getKumbiaURL('order/querySocios'), {
					parameters: {
						"numeroAccion": $F('numeroAccion'),
						"tipoVenta": $F('tipo_venta')
					},
					onSuccess: function(t){
						$('socios').update(t.responseText);
						$$('.aplSubmit').each(function(element){
							element.lang = element.title;
							element.title = "";
							element.observe('click', function(){
								var folio = this.lang;
								new Ajax.Request(Utils.getKumbiaURL('order/saveSocio'), {
									parameters: {
										"folio": folio
									},
									onSuccess: function(t){
										$('customer_messages').update(t.responseText);
									},
									onFailure: function(t){
										$('customer_messages').update(t.responseText);
									}
								})
							});
						});
					},
					onFailure: function(t){
						alert(t.responseText);
					}
				});
			}
			catch(e) {
				alert(e);
			}
		}
	},

	payAccount: function(){
		var numItems = this._preOrder.querySelectorAll('tr.orderRow').length;
		if(numItems>0){
			new Utils.redirectToAction('pay/index/0:'+this._cuentasElement.getValue())
		} else {
			Growler.show('Aun no puede liquidar la cuenta');
		}
	},

	goToNotes: function(){
		var numItems = this._preOrder.querySelectorAll('tr.orderRow').length;
		if(numItems>0){
			window.location = Utils.getKumbiaURL('order/notes');
		} else {
			Growler.show('Agregue items al pedido antes de agregar notas');
		}
	},

	showStatement: function(){
		window.open(Utils.getKumbiaURL('factura?preview'), null, 'width=300, height=700, toolbar=no, statusbar=no')
	},

	sendToKitchen: function(){
		new Utils.redirectToAction("order/sendToKitchen");
	},

	backToTables: function()
	{
		new Utils.redirectToAction("tables/back/" + this._salonId);
	},

	joinOrders: function()
	{
		new Utils.redirectToAction("tables/chooseTable/" + this._id + "/"+this._salonId+"/joinOrders");
	},

	changeTable: function()
	{
		new Utils.redirectToAction("tables/chooseTable/" + this._id + "/"+this._salonId+"/changeTable");
	},

	adjustOrderList: function()
	{
		var orderItems = $$("orderRow");
		var listaPedido = $("listaPedido");
		if (orderItems.length < 9) {
			listaPedido.style.height = this._listaPedidoHeight;
			listaPedido.style.overflowY = 'auto';
		} else {
			listaPedido.style.height = "380px";
		};
		var height = listaPedido.getHeight();
		var itemsSelected = this._preOrder.querySelectorAll("tr.itemSelected");
		for(var i = 0; i < itemsSelected.length; i++) {
			var offset = itemsSelected[i].positionedOffset();
			var scrollPos = (offset[1] - listaPedido.offsetTop - itemsSelected[i].getHeight() - 20);
			if (scrollPos > 0) {
				listaPedido.scrollTop = scrollPos;
			} else {
				listaPedido.scrollTop = 0;
			};
			break;
		}
	},

	setHeightDimensions: function()
	{
		var height = $('mainTable').getHeight();
		this._menuDetailsHeight = parseInt(height * 0.57, 10) + 'px';
		this._listaPedidoHeight = parseInt(height * 0.55, 10) + 'px';
	},

	setAccountMasterId: function(accountMasterId)
	{
		this._accountMasterId = accountMasterId;
	},

	setNumeroCuenta: function()
	{

	},

	initialize: function(parameters)
	{

		try {

			document.title = parameters.title;
			this._tipoComanda = parameters.tipoComanda;
			this._ventaA = parameters.ventaA;
			this._pedirPersonas = parameters.pedirPersonas;
			this._pedirAsientos = parameters.pedirAsientos;
			this._numeroAsientos = parameters.numeroAsientos;
			this._id = parameters.id;
			this._salonId = parameters.salonId;
			this._accountMasterId = parameters.accountMasterId;
			this._numeroCuenta = parameters.numeroCuenta;

			this._cuentasElement = $('cuentas');
			this._comandasElement = $('comandas');

			this._preOrder = $('preOrder');
			this._menuDetailsElement = $("menuDetails");

			var menuButtons = $$('button.menuButton');
			for (var i = 0; i < menuButtons.length; i++) {
				menuButtons[i].observe('click', this.getMenuItems.bind(this, menuButtons[i], menuButtons[i].lang));
				menuButtons[i].lang = null;
			};

			if (this._comandasElement) {
				if (this._comandasElement.options.length == 0) {
					if (this._tipoComanda == "M") {
						this.addComanda();
					} else {
						this.addAutomaticComanda();
					};
				} else {
					this.pedirPersonas();
				}
			};

			$('showInvoice').observe('click', this.showInvoice.bind(this));
			$('goToNotes').observe('click', this.goToNotes.bind(this));
			$('customerName').observe('click', this.setCustomerName.bind(this));
			$('payAccount').observe('click', this.payAccount.bind(this));
			$('deleteItems').observe('click', this.deleteItems.bind(this));
			$('discounts').observe('click', this.getDiscounts.bind(this));
			$('showStatement').observe('click', this.showStatement.bind(this));
			$('sendToKitchen').observe('click', this.sendToKitchen.bind(this));
			$('cancelOrder').observe('click', this.cancelOrder.bind(this));
			$('joinOrders').observe('click', this.joinOrders.bind(this));
			$('backToTables').observe('click', this.backToTables.bind(this));
			$('changeTable').observe('click', this.changeTable.bind(this));

			this.preOrderCallbacks();
			this.prepareBuscarItem();
			this.setHeightDimensions();

			new Event.observe(window, "keydown", this.enableShortCuts.bind(this));
		} catch (e) {
			alert(e);
		}

	}

});
