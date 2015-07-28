
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

var AReserva = {
	_active: null,
	setActive: function(hyperForm){
	  this._active = hyperForm;
	},
	getActive: function(){
	  return this._active;
	},
	
	addCallbacksRecibosPagos: function(options){
		if(!options){
		  options = {};
		}
		if(!options.rc){
		 options.rc = null;
		}
		var hfosWindow = AReserva.getActive();
		var reservasId = hfosWindow.selectOne('#reservasId');
		var recibosPagoButtonList = hfosWindow.selectOne('#recibosPagoButtonList');
		if(recibosPagoButtonList && reservasId){  
			recibosPagoButtonList.onClick = function(){
				this._imprimeRecibosPagosLista(recibosPagoButtonList.alt);
			}
		}
	}
};

var AbonoReserva = Class.create(HfosProcessContainer, {

	_key: null,

	_movimientos: [],

	_activeMov: 0,

	_oldTercero: null,

	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){
		//Formulario de buscar
		new HfosForm(this, 'buscarForm', {
			update: 'resultados',
			onSuccess: function(response){

				switch(response.number){
					case '0':
						this.getMessages().notice('No se encontraron reservas');
						break;

					case '1':
						this._key = response.key;
						this.go('abono_reserva/ver', {
							parameters: this._key,
							onSuccess: this._setDetailsCallbacks.bind(this, this._key)
						});
						break;

					case 'n':

						var browse = new HfosBrowseData(this, 8);
						browse.setEnableDeleteButton(false);
						browse.build(this.getElement('resultados'), response);
						var hyDetails = browse.getDetailsButtons();
						for(var i=0;i<hyDetails.length;i++){
							hyDetails[i].store('primary', hyDetails[i].title);
							hyDetails[i].title = 'Abonar';
							hyDetails[i].observe('click', this._detailsHandler.bind(this, hyDetails[i]));
						};
						this.scrollToBottom();
						this._notifyContentChange();
						break;

				}
			}.bind(this)
		});
	},

	/**
	* Ver informe de pagos
	*/
	_detailsHandler: function(element){
		this._key = element.retrieve('primary');
		this.go('abono_reserva/ver', {
			parameters: this._key,
			onSuccess: this._setDetailsCallbacks.bind(this,this._key)
		});
	},
	
	/**
	 * Metodo que crea los parametros segun existencias para el formulario get formato por hash
	 */
	_makeParamsReport: function(config){
		var params = {};
		params.tipo = 'R';
		if(config.reservasId){
			params.reservasId = config.reservasId;
		}
		if(config.urlAction){
			params.urlAction = config.urlAction;
		}
		if(config.id){
			params.id = config.id;
		}
		if(config.rcId){
			params.rcId = config.rcId
		}
		return params;
	},
	/**
	* Abre el dialogo de tipo de reporte de amortizacion
	* @param hash config{ reservasId: '...', urlAction: '...'}
	*/
	_getReporteFormato: function(config){
		if(config){
			var params = this._makeParamsReport(config);
			new HfosModalForm(this, 'abono_reserva/getFormato', {
				parameters: params,
				beforeClose: function(params, form, canceled, response){
					if(canceled==false){
						if(response.status=='OK'){
							if(typeof response.file != "undefined"){
								window.open($Kumbia.path+response.file);
							}
						}else{
							if(response.status=='FAILED'){
								this._active.getMessages().error(response.message);
							}
						}
					}
				}.bind(this, params)
			});
		}
	},
	/**
	 * Metodo que ejecuta reporte de listado de recibos de caja
	 * @param int id: Es el id de reservas
	 */
	_imprimeRecibosPagosLista: function(id){
		var config = {};
		config.urlAction = 'abono_reserva/getListaRecibosPagos';
		config.reservasId = config.id = id;
		this._getReporteFormato(config);
	},
	_imprimeRecibosPagos: function(rcId){
		var config = {};
		config.urlAction = 'abono_reserva/getReciboPago';
		config.rcId = rcId;
		this._getReporteFormato(config);
	},
	
	_setDetailsCallbacks: function(key){
		//alert('_setDetailsCallbacks: (key)'+key);
		var searchButton = this.getElement('backButton');
		searchButton.observe('click', this._backToSearch.bind(this));

		var printButton = this.getElement('printButton');
		if(printButton!==null){
			printButton.hide();

			var newButton = this.getElement('newButton');
			var deleteButton = this.getElement('deleteButton');
			deleteButton.hide();
			
			var estadoReserva = this.selectOne('#estadoReserva');
			if(estadoReserva && estadoReserva.getValue()=='A'){
				newButton.observe('click', this._nuevoAbono.bind(this, key));
			}else{
				newButton.hide();
			}
			var recibosPagoButtonList = this.selectOne('#recibosPagoButtonList'); 
			if(recibosPagoButtonList){
				recibosPagoButtonList.observe('click', this._imprimeRecibosPagosLista.bind(this, recibosPagoButtonList.alt));
			}
			$$('.recibosPagoButton').each(function(element){
				element.observe('click', this._imprimeRecibosPagos.bind(this, element.alt));
			}.bind(this));
			//Botones de anular recibo de caja
			$$('.anularRecibosPagoButton').each(function(element){
				element.observe('click', this._anularRecibosPagos.bind(this, element.alt));
			}.bind(this));
		}
		new HfosTabs(this, 'tabbed', {
			onChange: this._onChangeTab.bind(this)
		});
	},

	/**
	* habre vista de nuevo abono de Reserva
	*/
	_nuevoAbono: function(key){
		//alert('_nuevoAbono: (key)'+key);
		this.go('abono_reserva/nuevo', {
			parameters: key,
			onSuccess: this._setNewCallbacks.bind(this)
		});
	},

	_onChangeTab: function(name){
		
	},

	_setNewCallbacks: function(){

		var searchButton = this.getElement('backButton');
		if(searchButton){
		  searchButton.observe('click', this._backToSearch.bind(this));
		}

		var saveButton = this.getElement('saveButton');
		if(saveButton){
			saveButton.observe('click', this._grabarAbono.bind(this));
			/*recibosPagoButtonArray.each(function(index,obj){
				
			});*/
			this._addGridCallbacks(1);
			new HfosTabs(this, 'tabbed', {
				onChange: this._onChangeTab.bind(this)
			});
			this._showTotalBar();
			this._totalizeAbono();
		}
	},

	_updateBeneficiario: function(){
		var nitNombreElement = this.selectOne('input#nit_det');
		var beneficiarioElement = this.selectOne('input#beneficiario');
		if(beneficiarioElement.getValue()==""){
			if(nitNombreElement.getValue().include('NO EXISTE')==false){
				beneficiarioElement.setValue(nitNombreElement.getValue());
			}
		}
	},

	/**
	* Graba un abono de reserva en (recibos_pagos y detalle_recibos_pagos)
	*/
	_grabarAbono: function(){
		var formGrabar = this.getElement('formGrabar');
		new HfosAjax.JsonFormRequest(formGrabar, {
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
				}else{
					if(response.status=='OK'){
						this.getMessages().success(response.message);
						this._key = response.reservasId;
						this.hideStatusBar();
						var params = 'id='+response.reservasId+'&rcId='+response.id+'&tipo=R';
						this.go('abono_reserva/ver', {
							parameters: params,
							onSuccess: this._setDetailsCallbacks.bind(this, params)
						});
						this._imprimeRecibosPagos(response.id);
					}
				}
			}.bind(this)
		});
	},

	_backToSearch: function(){
		this.go('abono_reserva/index', {
			onSuccess: this._setIndexCallbacks.bind(this)
		});
		this.hideStatusBar();
	},

	/**
	 * Metodo que anula un recibo de caja
	 * 
	 * @param int rcId: Es el id del recibo de caja
	 */
	_anularRecibosPagos: function(rcId){
		new HfosModal.confirm({
			title: 'Recibos de caja',
			message: 'Se recalculará para reversar el pago. Seguro desea anular el recibo de caja?.',
			onAccept: function(){
				new HfosAjax.JsonRequest('abono_reserva/anular', {
					checkAcl: true,
					parameters: {'rcId': rcId},
					onSuccess: function(response){
						if(response.status=='FAILED'){
							this.getMessages().error(response.message);
						} else {
							this.getMessages().success(response.message);
							var params = 'id='+response.reservasId+'&n=0&rcId='+response.id+'&tipo=R';
							this.go('abono_reserva/ver', {
								parameters: params,
								onSuccess: this._setDetailsCallbacks.bind(this, params)
							});
						};
					}.bind(this)
				});
			}.bind(this)
		});
	},

	_addGridCallbacks: function(position){

		var lineaElement = this.getElement('linea'+position);
		lineaElement.store('position', position);

		var checkElement = lineaElement.getElement('itemCheck');
		checkElement.observe('change', this._selectRow.bind(this, checkElement));

		var formaPagoElement = lineaElement.getElement('formaPago');
		formaPagoElement.observe('blur', this._onBlurFormaPago.bind(this, formaPagoElement))

		var numeroFormaElement = lineaElement.getElement('numeroForma');
		numeroFormaElement.observe('blur', this._onBlurNumeroForma.bind(this, numeroFormaElement));

		var valorElement = lineaElement.getElement('valor');
		valorElement.observe('blur', this._onBlurValor.bind(this, valorElement));
		
	},

	_onBlurFormaPago: function(formaPagoElement){
		this._pushRowForUpdate(formaPagoElement);
	},

	_onBlurNumeroForma: function(numeroFormaElement){
		this._pushRowForUpdate(numeroFormaElement);
	},

	_onBlurValor: function(valorElement){
		this._pushRowForUpdate(valorElement);
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

			var tdFormaPago = document.createElement('TD');
			tdFormaPago.addClassName('formaPago');

			var primerFormaPago = this.select('select.formaPago')[0];
			tdFormaPago.appendChild(primerFormaPago.cloneNode(true));
			trElement.appendChild(tdFormaPago);

			var tdNumeroForma = document.createElement('TD');
			tdNumeroForma.addClassName('numeroForma');
			tdNumeroForma.update('<input type="text" class="numeroForma" name="numeroForma[]" id="numeroForma'+nextPosition+'" size="5" maxlength="4"/>');
			trElement.appendChild(tdNumeroForma);

			var tdValor = document.createElement('TD');
			tdValor.update('<input type="text" class="valor numeric" name="valor[]" id="valor'+nextPosition+'" size="12"/>');
			trElement.appendChild(tdValor);

			this.getElement('abonos-reserva-body').appendChild(trElement);

			this._addGridCallbacks(nextPosition);

			this._notifyContentChange();
			this._totalizeAbono();
			this.scrollToBottom();
		}

	},

	/**
	 * Selecciona una fila de forma de pago
	 */
	_selectRow: function(checkElement){
		var trElement = checkElement.up(1);
		if(!trElement.hasClassName('lineaError')){
			if(checkElement.checked){
				trElement.addClassName('selectedRow');
			}else{
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
		/*var movimientoSeleccion = this.getStatusBarElement('movimientoSeleccion');
		if(numberChecked>0){
			movimientoSeleccion.show();
		}else{
			movimientoSeleccion.hide();
		};*/
	},
	/**
	 * 
	 */
	_totalizeAbono: function(){
		var valorElements = this.select('input.valor');
		var total = 0;
		for(var i=0;i<valorElements.length;i++){
			if(valorElements[i].getValue()!=''){
				total+=parseFloat(valorElements[i].getValue(), 10);
			}
		};
		total = Utils.numberFormat(total);
		this._totalOrden.update('<table class="sumasTable"><tr><td>Total Abono <b>'+total+'</b></td></tr></table>');
	},

	/**
	 * Metodo que visualiza un total de un abono
	 */
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
	}
});

HfosBindings.late('win-abono-reserva-tpc', 'afterCreate', function(hfosWindow){
	var abonoReserva = new AbonoReserva(hfosWindow);
	AReserva.setActive(hfosWindow);
});
