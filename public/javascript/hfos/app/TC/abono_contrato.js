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

var AContrato = {
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
		var hfosWindow = AContrato.getActive();	  
		var sociosId = hfosWindow.selectOne('#sociosId');
		var recibosPagoButtonList = hfosWindow.selectOne('#recibosPagoButtonList');
		if(recibosPagoButtonList && sociosId){  
			recibosPagoButtonList.onClick = function(){
				this._imprimeRecibosPagosLista(sociosId);
			}
		}
	}
};

var AbonoContrato = Class.create(HfosProcessContainer, {

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
						this.getMessages().notice('No se encontraron contratos');
						break;

					case '1':
						this._key = response.key;
						this.go('abono_contrato/ver', {
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
		this.go('abono_contrato/ver', {
			parameters: this._key,
			onSuccess: this._setDetailsCallbacks.bind(this,this._key)
		});
		this.hideStatusBar();
	},
	
	/**
	 * Metodo que crea los parametros segun existencias para el formulario get formato por hash
	 */
	_makeParamsReport: function(config){
		var params = {};
		params.tipo = 'C';
		if(config.sociosId){
			params.reservasId = config.sociosId;
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
			new HfosModalForm(this, 'abono_contrato/getFormato', {
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
	* Abre el dialogo de tipo de reporte de amortizacion
	* @param hash config{ reservasId: '...', urlAction: '...'}
	*/
	_getFormErrado: function(key){
		if(key){
			new HfosModalForm(this, 'abono_contrato/getFormErrado', {
				parameters	: key,
				beforeClose	: function(key, form, canceled, response){
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
				}.bind(this, key)
			});
		}
	},
	/**
	* Abre el dialogo de detalle del abono realizado
	* @param hash config{ reservasId: '...', urlAction: '...'}
	*/
	_getDetalleAbono: function(key){
		if(key){
			new HfosModalForm(this, 'abono_contrato/getDetalleAbono', {
				parameters	: {'id': key},
				beforeClose	: function(key, form, canceled, response){
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
				}.bind(this, key)
			});
		}
	},
	/**
	 * Metodo que ejecuta reporte de listado de recibos de caja
	 * @param int id: Es el id de reservas
	 */
	_imprimeRecibosPagosLista: function(id){
		var config = {};
		config.urlAction = 'abono_contrato/getListaRecibosPagos';
		config.sociosId = config.id = id;
		this._getReporteFormato(config);
	},
	/**
	 * Metodo que imprime un recibo de caja
	 * 
	 * @param int rcId: Es el id del recibo de caja
	 */
	_imprimeRecibosPagos: function(rcId){
		var config = {};
		config.urlAction = 'abono_contrato/getReciboPago';
		config.rcId = rcId;
		this._getReporteFormato(config);
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
				new HfosAjax.JsonRequest('abono_contrato/anular', {
					checkAcl: true,
					parameters: {'rcId': rcId},
					onSuccess: function(response){
						if(response.status=='FAILED'){
							this.getMessages().error(response.message);
						} else {
							this.getMessages().success(response.message);
							var params = 'id='+response.sociosId+'&n=0&rcId='+response.id+'&tipo=C';
							this.go('abono_contrato/ver', {
								parameters: params,
								onSuccess: this._setDetailsCallbacks.bind(this, params)
							});
						};
					}.bind(this)
				});
			}.bind(this)
		});
	},
	/**
	 * Metodo que despliega el popup de pago errado
	 */
	_nuevoAbonoErrado: function(){
		var key = '';
		var sociosIdObj = this.selectOne('#sociosId');
		if(sociosIdObj){
			key = 'id='+sociosIdObj.getValue();
		}
		this._getFormErrado(key);
	},

	/**
	 * Metodo que se ejecuta en cada callback
	 */
	_setDetailsCallbacks: function(key){
		//alert('key: '+key)
		this.hideStatusBar();
		var searchButton = this.getElement('backButton');
		if(searchButton){
			searchButton.observe('click', this._backToSearch.bind(this));
		}
		var printButton = this.getElement('printButton');
		if(printButton!==null){
			printButton.hide();
			var estadoSocio = this.selectOne('#estadoContrato');
			var estadoSocioMov = this.selectOne('#estadoMovimiento');
			var newButton = this.getElement('newButton');
			var abonoPosteriorButton = this.getElement('abonoPosteriorButton');
			var abonoOtrosButton = this.getElement('abonoOtrosButton');
			var abonoErradoButton = this.getElement('abonoErradoButton');
			var abonoCapitalButton = this.getElement('abonoCapitalButton');
			if(abonoErradoButton){
				abonoErradoButton.observe('click', this._nuevoAbonoErrado.bind(this));
			}
			//Si esta activo un contrato y no esta 100% pago se puede abonar :)
			if(estadoSocio && estadoSocio.getValue()=='A' && estadoSocioMov.getValue()!='P'){
				if(newButton){
					newButton.observe('click', this._nuevoAbono.bind(this, key));
				}
				if(abonoPosteriorButton){
					abonoPosteriorButton.observe('click', this._nuevoAbonoPosterior.bind(this, key));
				}
				if(abonoCapitalButton){
					abonoCapitalButton.observe('click', this._nuevoAbonoCapital.bind(this, key));
				}
			}else{
				if(newButton){
					newButton.hide();
				}
				if(abonoPosteriorButton){
					abonoPosteriorButton.hide();
				}
				if(abonoCapitalButton){
					abonoCapitalButton.hide();
				}
				//if(abonoErradoButton){
					//abonoErradoButton.hide();
				//}
			}
			var deleteButton = this.getElement('deleteButton');
			if(deleteButton){
				deleteButton.hide();
			}
			var recibosPagoButtonList = this.selectOne('#recibosPagoButtonList'); 
			if(recibosPagoButtonList){
				recibosPagoButtonList.observe('click', this._imprimeRecibosPagosLista.bind(this, recibosPagoButtonList.alt));
				this.hideStatusBar();
			}
			//Botones de imprimir recibo de caja
			$$('.recibosPagoButton').each(function(element){
				element.observe('click', this._imprimeRecibosPagos.bind(this, element.alt));
			}.bind(this));
			//Botones de anular recibo de caja
			$$('.anularRecibosPagoButton').each(function(element){
				element.observe('click', this._anularRecibosPagos.bind(this, element.alt));
			}.bind(this));
			//Botones de detalle recibo de caja
			$$('.detalleRecibosPagoButton').each(function(element){
				element.observe('click', this._getDetalleAbono.bind(this, element.alt));
			}.bind(this));
			//Abono a otros
			if(abonoOtrosButton){
				abonoOtrosButton.observe('click', this._nuevoAbonoOtros.bind(this, key));
			}
		}
		//Change status account
		var fechaPagoSelectorY = this.selectOne('#fechaPagoSelectorYear');
		if(fechaPagoSelectorY){
			var fechaPagoSelectorM = this.selectOne('#fechaPagoSelectorMonth');
			var fechaPagoSelectorD = this.selectOne('#fechaPagoSelectorDay');

			if(fechaPagoSelectorY && fechaPagoSelectorY.getValue()=='@'){
				return false;
			}
			if(fechaPagoSelectorM && fechaPagoSelectorM.getValue()=='@'){
				return false;
			}
			if(fechaPagoSelectorD && fechaPagoSelectorD.getValue()=='@'){
				return false;
			}

			fechaPagoSelectorY.observe('change', this._changeEstadoCuenta.bind(this, key));
			fechaPagoSelectorM.observe('change', this._changeEstadoCuenta.bind(this, key));
			fechaPagoSelectorD.observe('change', this._changeEstadoCuenta.bind(this, key));
		}
		
		new HfosTabs(this, 'tabbed', {
			onChange: this._onChangeTab.bind(this)
		});
	},

	_changeEstadoCuenta: function(key){
		var estadoCuentaDiv = this.selectOne('#estadoCuentaDiv');
		if(estadoCuentaDiv){
			var fechaPagoSelectorY = this.selectOne('#fechaPagoSelectorYear');
			var fechaPagoSelectorM = this.selectOne('#fechaPagoSelectorMonth');
			var fechaPagoSelectorD = this.selectOne('#fechaPagoSelectorDay');
		
			var fechaPago = fechaPagoSelectorY.options[fechaPagoSelectorY.selectedIndex].value;
			fechaPago += '-';
			fechaPago += fechaPagoSelectorM.options[fechaPagoSelectorM.selectedIndex].value;
			fechaPago += '-';
			fechaPago += fechaPagoSelectorD.options[fechaPagoSelectorD.selectedIndex].value;
			
			new HfosAjax.Request('abono_contrato/getEstadoCuenta', {
				parameters: key+'&fechaPago='+fechaPago,
				onSuccess: function(transport){
					alert(fechaPago)
					estadoCuentaDiv.update(transport.responseText);
				}.bind(this)
			});
		}
	},
	/**
	* habre vista de nuevo abono de contrato
	*/
	_nuevoAbono: function(key){
		if(key && key.toString().indexOf('id=')==-1){
			key = 'id='+key;
		}
		this.go('abono_contrato/nuevo', {
			parameters: key,
			onSuccess: this._setNewCallbacks.bind(this)
		});
	},
	/**
	* habre vista de nuevo abono de contrato para abonos posteriores
	*/
	_nuevoAbonoPosterior: function(key){
		//alert('key: '+key)
		if(key && key.toString().indexOf('id=')==-1){
			key = 'id='+key;
		}
		key = key+'&tipo=P';
		this.go('abono_contrato/nuevo',{
			parameters: key,
			onSuccess: this._setNewCallbacks.bind(this)
		});
	},
	/**
	* habre vista de nuevo abono de contrato para abonos a capital
	*/
	_nuevoAbonoCapital: function(key){
		//alert('key: '+key)
		if(key && key.toString().indexOf('id=')==-1){
			key = 'id='+key;
		}
		key = key+'&tipo=K';
		this.go('abono_contrato/nuevo',{
			parameters: key,
			onSuccess: this._setNewCallbacks.bind(this)
		});
	},
	/**
	* habre vista de nuevo abono de contrato para abonos a otros
	*/
	_nuevoAbonoOtros: function(key){
		//alert('key: '+key)
		if(key && key.toString().indexOf('id=')==-1){
			key = 'id='+key;
		}
		key = key+'&tipo=O';
		this.go('abono_contrato/nuevo',{
			parameters: key,
			onSuccess: this._setNewCallbacks.bind(this)
		});
	},
	
	_onChangeTab: function(name){},
	/**
	 * Metodo que alista los campos cuando crea
	 */
	_setNewCallbacks: function(){
		var searchButton = this.getElement('backButton');
		if(searchButton){
			searchButton.observe('click', this._backToSearch.bind(this));
		}
		//Desactivamos fecha de recibo
		var frDay	= this.selectOne('#fechaReciboDay');
		var frMonth	= this.selectOne('#fechaReciboMonth');
		var frYear	= this.selectOne('#fechaReciboYear');
		if(frDay){
			frDay.disable();
		}
		if(frMonth){
			frMonth.disable();
		}
		if(frYear){
			frYear.disable();
		}
		var saveButton = this.getElement('saveButton');
		if(saveButton){
			saveButton.observe('click', this._grabarAbono.bind(this));
			this._addGridCallbacks(1);
			new HfosTabs(this, 'tabbed', {
				onChange: this._onChangeTab.bind(this)
			});
		}
		this._showTotalBar();
		this._totalizeAbono();
	},
	/**
	* Graba un abono de contrato (recibos_pagos y detalle_recibos_pagos)
	*/
	_grabarAbono: function(){
		var formGrabar = this.getElement('formGrabar');
		new HfosAjax.JsonFormRequest(formGrabar, {
			onSuccess: function(response){
				if(response.status=='FAILED'){
					this.getMessages().error(response.message);
				} else {
					if(response.status=='OK'){
						this.getMessages().success(response.message);
						this._key = response.sociosId;
						this.hideStatusBar();
						var params = 'id='+response.sociosId+'&rcId='+response.id+'&tipo=C';
						this.go('abono_contrato/ver', {
							parameters: this._key+'&'+params,
							onSuccess: this._setDetailsCallbacks.bind(this, this._key)
						});
						this._imprimeRecibosPagos(response.id);
						this.hideStatusBar();
					}
				}
			}.bind(this)
		});
	},
	
	/**
	 * Metodo que devuleve a la vista de consulta
	 */
	_backToSearch: function(){
		this.go('abono_contrato/index', {
			onSuccess: this._setIndexCallbacks.bind(this)
		});
		this.hideStatusBar();
	},
	
	/**
	 * Metodo que reclacula el valor total en el detalle de un abono 
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
	},
	/**
	 * Metodo que agrega los eventos a lso campos de detalle de abono
	 */
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
	/**
	 * Metodo que agrega un nueva linea los detalles de formas de pagos
	 */
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
			this.getElement('abonos-contrato-body').appendChild(trElement);
			this._addGridCallbacks(nextPosition);
			this._notifyContentChange();
			this.scrollToBottom();
			this._totalizeAbono();
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
		/*var movimientoSeleccion = this.getStatusBarElement('movimientoSeleccion');
		if(numberChecked>0){
			movimientoSeleccion.show();
		} else {
			movimientoSeleccion.hide();
		};*/
	}
});

HfosBindings.late('win-abono-contrato-tpc', 'afterCreate', function(hfosWindow){
	var abonoContrato = new AbonoContrato(hfosWindow);
	AContrato.setActive(hfosWindow);
});
