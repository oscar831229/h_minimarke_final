
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
 * Clase ReciboCaja
 *
 * Cada formulario de Chques en pantalla tiene asociado una instancia de esta clase
 */
var ReciboCaja = Class.create(HfosProcessContainer, {

	_key: null,

	_movimientos: [],

	_formaPago: [],

	_activeMov: 0,

	_oldTercero: null,

	/**
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	/**
	 * @this {ReciboCaja}
	 */
	_setIndexCallbacks: function(){

		var newButton = this.getElement('newButton');
		if(newButton!==null){
			newButton.observe('click', this._newReciboCaja.bind(this));

			//Formulario de buscar
			new HfosForm(this, 'buscarReccajForm', {
				update: 'resultados',
				onSuccess: function(response){

					switch(response.number){
						case '0':
							this.getMessages().notice('No se encontraron recibos de caja');
							break;

						case '1':
							this._key = response.key;
							this.go('recibo_caja/ver', {
								parameters: this._key,
								onSuccess: this._setDetailsCallbacks.bind(this)
							});
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
		}
	},

	/**
	 * @this {ReciboCaja}
	 */
	_detailsHandler: function(element){
		this._key = element.retrieve('primary');
		this.go('recibo_caja/ver', {
			parameters: this._key,
			onSuccess: this._setDetailsCallbacks.bind(this)
		});
	},

	/**
	 * @this {ReciboCaja}
	 */
	_newReciboCaja: function(){
		this.go('recibo_caja/nuevo', {
			onSuccess: this._setNewCallbacks.bind(this)
		});
	},

	/**
	 * @this {ReciboCaja}
	 */
	_setDetailsCallbacks: function(){

		var searchButton = this.getElement('backButton');
		searchButton.observe('click', this._backToSearch.bind(this));

		var printButton = this.getElement('printButton');
		if(printButton!==null){
			printButton.observe('click', this._printReciboCaja.bind(this));

			var deleteButton = this.getElement('deleteButton');
			deleteButton.observe('click', this._anulaReciboCaja.bind(this));

			var verComprobButton = this.getElement('verComprob');
			if(verComprobButton!==null){
				verComprobButton.observe('click', Movimientos.abrir.bind(this, verComprobButton.title));
			} else {
				deleteButton.hide();
			}

		}
	},

	/**
	 * @this {ReciboCaja}
	 */
	abrirComprobante: function(){

	},

	/**
	 * @this {ReciboCaja}
	 */
	_updateCartera: function(){
		var nitElement = this.selectOne('input#nit');
		if(this._oldTercero!=nitElement.value.strip()){
			if(nitElement.value.strip()==''||nitElement.getValue()=='0'){
				this.getElement('infoTercero').update('Seleccione un tercero para visualizar su cartera');
				this.getElement('infoTercero').show();
				this.getElement('carteraContent').hide();
			} else {
				this._documentos = [];
				new HfosAjax.JsonRequest('recibo_caja/getCartera', {
					parameters: 'nit='+nitElement.getValue(),
					onSuccess: function(response){
						if(response.status=='OK'){
							if(response.documentos.length==0){
								this.getElement('infoTercero').update('El tercero no tiene documentos en cartera asociados');
								this.getElement('infoTercero').show();
								this.getElement('carteraContent').hide();
							} else {
								this.getElement('infoTercero').hide();
								var html = '<table align="center" width="50%" class="hyBrowseTab zebraSt sortable" cellspacing="0">';
								html+='<thead><th class="nosort sortasc"></th>';
								html+='<th class="sortcol">Tipo</th>';
								html+='<th class="sortcol">Número</th>';
								html+='<th class="sortcol">Cuenta</th>';
								html+='<th class="sortcol">F. Emisión</th>';
								html+='<th class="sortcol">F. Vence</th>';
								html+='<th class="sortcol">Saldo</th>';
								html+='<th class="sortcol">Abono</th>';
								html+='</thead><tbody>';
								for(var i=0;i<response.documentos.length;i++){
									var key = response.documentos[i].tipoDoc+'_'+response.documentos[i].numeroDoc+'_'+response.documentos[i].cuenta;
									html+='<tr><td><input type="checkbox" name="numeroDoc[]" value="'+key+'" id="'+key+'"/></td>';
									html+='<td>'+response.documentos[i].tipoDoc+'</td>';
									html+='<td align="right">'+response.documentos[i].numeroDoc+'</td>';
									html+='<td align="right">'+response.documentos[i].cuenta+'</td>';
									html+='<td>'+response.documentos[i].fEmision+'</td>';
									html+='<td>'+response.documentos[i].fVence+'</td>';
									html+='<td align="right">'+response.documentos[i].saldo+'</td>';
									html+='<td align="right"><input type="text" name="abono'+key+'[]" class="abono numeric" value="'+response.documentos[i].saldoValor+'" size="9" maxlength="12"></td>';
									html+='</tr>'
								};
								html+='</tbody></table>';
								this.getElement('carteraContent').update(html);
								this.getElement('carteraContent').show();
								this._addDocumentoCallbacks();
								this._documentos = response.documentos;
							}
						}
					}.bind(this)
				});
			};
			this._oldTercero = nitElement.value.strip();
		}
	},

	/**
	 * @this {ReciboCaja}
	 */
	_addDocumentoCallbacks: function(){
		var checkElements = this.getElement('carteraContent').select('input[type="checkbox"]');
		for(var i=0;i<checkElements.length;i++){
			checkElements[i].observe('click', this._selectNumeroDoc.bind(this, checkElements[i]));
		};
		var abonosElements = this.getElement('carteraContent').select('input.abono');
		for(var i=0;i<abonosElements.length;i++){
			abonosElements[i].observe('blur', this._updateDebitosDoc.bind(this));
		};
	},

	/**
	 * @this {ReciboCaja}
	 */
	_selectNumeroDoc: function(checkElement){
		checkElement.up(1).toggleClassName('selected');
		this._calculateSumas();
	},

	/**
	 * @this {ReciboCaja}
	 */
	_updateDebitosDoc: function(){
		this._calculateSumas();
	},

	/**
	 * @this {ReciboCaja}
	 */
	_onChangeTab: function(name){
		if(name=="Cartera"){
			this._updateCartera();
		}
	},

	/**
	 * @this {ReciboCaja}
	 */
	_showStatusBar: function(){

		//Mostrar barra de estado
		var html = '<table width="100%" class="movimientoStatusBar"><tr>';
		html+='<td align="left" style="display:none" class="movimientoSeleccion">Opciones de la Selección <select class="opcionesSeleccion">';
		html+='<option value="@">Seleccione...</option><option value="D">Eliminar</option>'
		html+='</select><td><td class="totalOrden" align="right"></td></tr></table>';
		this.showStatusBar(html);

		this._sumasIguales = this.getStatusBarElement('totalOrden');
		this._sumasIguales.update('');

	},

	/**
	 * @this {ReciboCaja}
	 */
	_setNewCallbacks: function(){

		var searchButton = this.getElement('backButton');
		searchButton.observe('click', this._backToSearch.bind(this));

		var saveButton = this.getElement('saveButton');
		saveButton.observe('click', this._grabarReciboCaja.bind(this));

		new HfosTabs(this, 'tabbed', {
			onChange: this._onChangeTab.bind(this)
		});

		HfosCommon.addCuentaCompleter('cuenta');
		HfosCommon.addTerceroCompleter('nit', true);
		HfosCommon.addTerceroCompleter('nit2');
		HfosCommon.addComprobCompleter('comprob', true);

		var nitNombreElement = this.selectOne('input#nit_det');
		nitNombreElement.observe('blur', this._updateBeneficiario.bind(this));

		var addMoviElement = this.selectOne('input#addMovi');
		addMoviElement.observe('click', this._addMovimiento.bind(this, addMoviElement));

		var addFormaPagoElement = this.selectOne('input#addFormaPago');
		addFormaPagoElement.observe('click', this._addFormaPago.bind(this, addFormaPagoElement));

		this._showStatusBar();
		this._updateSumas(0, 0);

		this._movimientos = [];
		this._activeMov = 0;

	},

	/**
	 * @this {ReciboCaja}
	 */
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
	 * @this {ReciboCaja}
	 */
	_grabarReciboCaja: function(){
		var total = 0;
		var debitos = this._getTotalFormaPago();
		var valorElement = this.selectOne('input#valor');
		var creditos =  0;
		for(var i=0;i<this._movimientos.length;i++){
			if(this._movimientos[i].estado=='A'){
				if(this._movimientos[i].naturaleza=='D'){
					debitos+=parseFloat(this._movimientos[i].valor2,10);
				} else {
					creditos+=parseFloat(this._movimientos[i].valor2,10);
				}
			}
		};

		var valCartera = this._sumCartera();
		if(valCartera>-1){
			creditos += valCartera;
		} else {
			debitos += valCartera;
		}

		if(debitos!=creditos){
			new HfosModal.alert({
				title: 'Generar ReciboCajas',
				message: "Movimientos contables descuadrados, por favor revise"
			});
		} else {
			var nitElement = this.selectOne('input#nit');
			if(nitElement.value.strip()==''||nitElement.value=='0'){
				new HfosModal.alert({
					title: 'Generar ReciboCajas',
					message: 'Debe indicar el tercero del comprobante'
				});
				nitElement.activate();
				return;
			};
			var beneficiarioElement = this.selectOne('input#beneficiario');
			if(beneficiarioElement.getValue()==''){
				this._updateBeneficiario();
			};
			var observacionElement = this.selectOne('textarea#observacion');
			if(observacionElement.value.strip()==''||observacionElement.value=='0'){
				new HfosModal.alert({
					title: 'Generar ReciboCajas',
					message: 'Debe indicar la observación ó nota del ReciboCaja'
				});
				nitElement.activate();
				return;
			};
			var formGrabar = this.getElement('formGrabar');
			formGrabar.select('input.movementHidden').each(function(movementHidden){
				movementHidden.erase();
			});

			//Add contablizacion to form 
			var fields = ['nit2', 'nombre2', 'cuenta', 'naturaleza', 'descripcion', 'centroCosto', 'valor2'];
			for(var i=0;i<this._movimientos.length;i++){
				if(this._movimientos[i].estado=='A'){
					fields.each(function(id){
						var input = document.createElement('INPUT');
						input.type = "hidden";
						input.className = "movementHidden";
						input.name = "f_"+id+"[]";
						input.value = this._movimientos[i][id];
						formGrabar.appendChild(input);
					}.bind(this));
				}
			};

			var formGrabar = this.getElement('formGrabar');
			formGrabar.select('input.formaPagoHidden').each(function(formaPagoHidden){
				formaPagoHidden.erase();
			});

			//Add forma de pago to form 
			var fields = ['formaPago', 'formaPago_det', 'numero', 'descripcion', 'valor'];
			for(var i=0;i<this._formaPago.length;i++){
				if(this._formaPago[i].estado=='A'){
					fields.each(function(id){
						var input = document.createElement('INPUT');
						input.type = "hidden";
						input.className = "formaPagoHidden";
						input.name = "fp_"+id+"[]";
						input.value = this._formaPago[i][id];
						formGrabar.appendChild(input);
					}.bind(this));
				}
			};

			new HfosAjax.JsonFormRequest(formGrabar, {
				onSuccess: function(response){
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					} else {
						if(response.status=='OK'){
							this._key = response.id;
							this._movimientos = [];
							this._formaPago = [];
							this.go('recibo_caja/ver', {
								parameters: this._key+'&new=1',
								onSuccess: this._setDetailsCallbacks.bind(this)
							});
						}
					}
				}.bind(this)
			});
		}
	},

	/**
	 * @this {ReciboCaja}
	 */
	_addMovimiento: function(addMoviElement){
		try {
			var cuentaElement = this.selectOne('input#cuenta');
			if(cuentaElement.getValue()==""){
				new HfosModal.alert({
					title: 'Generar ReciboCajas',
					message: 'Debe indicar la cuenta'
				});
				cuentaElement.activate();
				return;
			};
			var valorElement = this.selectOne('input#valor2');
			if(valorElement.getValue()==""||valorElement.getValue()=="0"){
				new HfosModal.alert({
					title: 'Generar ReciboCajas',
					message: 'Debe indicar el valor'
				});
				valorElement.activate();
				return;
			};

			var centroCostoElement = this.selectOne('select#centroCosto');
			var nitElement = this.selectOne('input#nit2');
			var nitNombreElement = this.selectOne('input#nit2_det');
			var naturalezaElement = this.selectOne('select#naturaleza');
			var descripcionElement = this.selectOne('input#descripcion');
			var cuentaNombreElement = this.selectOne('input#cuenta_det');

			var index;
			if(this._activeMov==-1){
				index = this._movimientos.length;
			} else {
				index = this._activeMov;
			};
			this._movimientos[index] = {
				'cuenta': cuentaElement.getValue(),
				'detalleCuenta': cuentaNombreElement.getValue(),
				'naturaleza': naturalezaElement.getValue(),
				'descripcion': descripcionElement.getValue(),
				'centroCosto': centroCostoElement.getValue(),
				'textoCentro': centroCostoElement.options[centroCostoElement.selectedIndex].text,
				'nit2': nitElement.getValue(),
				'nombre2': nitNombreElement.getValue(),
				'valor2': parseFloat(valorElement.getValue(), 10),
				'estado': 'A'
			};
			this._generarTabla();
			nitElement.setValue('');
			//nitNombreElement.setValue('');
			//cuentaElement.setValue('');
			naturalezaElement.setValue('');
			//descripcionElement.setValue('');
			centroCostoElement.setValue('');
			cuentaNombreElement.setValue('');
			valorElement.setValue('');
			this._activeMov = -1;
			this.selectOne('input#nuevoMovi').hide();
			addMoviElement.value = "Agregar";
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * @this {ReciboCaja}
	 */
	_addFormaPago: function(addFormaPagoElement){
		try {
			var formaPagoElement = this.selectOne('select#fp_formaPago');
			if(formaPagoElement.getValue()==""){
				new HfosModal.alert({
					title: 'Generar Recibo de Caja',
					message: 'Debe indicar la forma de pago'
				});
				cuentaElement.activate();
				return;
			};
			var numeroValue = '';
			var numeroElement = this.selectOne('input#fp_numero');
			if(numeroElement){
				numeroValue = numeroElement.getValue();
			}
			var descripcionElement = this.selectOne('input#fp_descripcion');
			if(descripcionElement){
				descripcionValue = descripcionElement.getValue();
			}
			var valorElement = this.selectOne('input#fp_valor');
			if(valorElement.getValue()==""||valorElement.getValue()=="0"){
				new HfosModal.alert({
					title: 'Generar Recibo de Caja',
					message: 'Debe indicar el valor'
				});
				valorElement.activate();
				return;
			};

			var index;
			if(this._activeFormaPago==-1){
				index = this._formaPago.length;
			} else {
				index = this._activeFormaPago;
			};
			if(index==undefined){
				index = 0;
			}
			this._formaPago[index] = {
				'formaPago': formaPagoElement.getValue(),
				'formaPago_det': formaPagoElement.options[formaPagoElement.selectedIndex].text,
				'numero': numeroValue,
				'descripcion': descripcionValue,
				'valor': valorElement.getValue(),
				'naturaleza': 'D',
				'estado': 'A'
			};
			this._generarTablaFp();

			formaPagoElement.setValue('');
			numeroElement.setValue('');
			valorElement.setValue('');
			this._activeFormaPago = -1;
			this.selectOne('input#nuevoFormaPago').hide();
			addFormaPagoElement.value = "Agregar";
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	* Sum all checked values in cartera to actual debit total
	* 
	* @return double
	*/
	_sumCartera: function(){
		var debitos = 0;
		var checkElements = this.getElement('carteraContent').select('input[type="checkbox"]:checked');
		var abonosElements = this.getElement('carteraContent').select('input.abono');
		for(var i=0;i<checkElements.length;i++){
			if(checkElements[i].checked){
				for(var j=0;j<this._documentos.length;j++){
					var documentoCheck = checkElements[i].getValue().split("_");
					if(this._documentos[j].tipoDoc==documentoCheck[0] && this._documentos[j].numeroDoc==documentoCheck[1] && this._documentos[j].cuenta==documentoCheck[2]){
						var valor = abonosElements[j].getValue();
						if(valor!==''){
							debitos+=parseFloat(valor, 10);
						}
					}
				};
			}
		};

		return debitos;
	},

	/**
	 * @this {ReciboCaja}
	 */
	_generarTabla: function(){
		var tBodyMovimiento = this.selectOne('tbody#cmovi');
		tBodyMovimiento.innerHTML = "";
		var total = 0;
		var debitos =  this._getTotalFormaPago();
		var creditos = 0;
		for(var i=0;i<this._movimientos.length;i++){
			if(this._movimientos[i].estado=='A'){
				var html = "<tr>"+
				"<td align='center'>"+(i+1)+"</td>"+
				"<td>"+this._movimientos[i].cuenta+"</td>"+
				"<td>"+this._movimientos[i].detalleCuenta+"</td>"+
				"<td>"+this._movimientos[i].textoCentro+"</td>"+
				"<td align='right'>"+this._movimientos[i].valor2+"</td>"+
				"<td align='center'><div class='hyDetails'></div></td>"+
				"<td align='center'><div class='hyDelete'></div></td>"+
				"</tr>";
				tBodyMovimiento.innerHTML+=html;
				total+=this._movimientos[i].valor2;
				if(this._movimientos[i].naturaleza=='D'){
					debitos+=parseFloat(this._movimientos[i].valor2,10);
				} else {
					creditos+=parseFloat(this._movimientos[i].valor2,10);
				}
			}
		};
		var hyDetails = tBodyMovimiento.select('div.hyDetails');
		for(var i=0;i<hyDetails.length;i++){
			hyDetails[i].observe('click', this._editar.bind(this, i));
		};

		var hyDelete = tBodyMovimiento.select('div.hyDelete');
		for(var i=0;i<hyDelete.length;i++){
			hyDelete[i].observe('click', this._eliminar.bind(this, i));
		};

		var valCartera = this._sumCartera();
		if(valCartera>-1){
			creditos += valCartera;
		} else {
			debitos += valCartera;
		}

		this._updateSumas(debitos, creditos);

		return total;
	},

	_getTotalFormaPago: function(){
		var total = 0.00;
		for(var i=0;i<this._formaPago.length;i++){
			if(this._formaPago[i] && this._formaPago[i].estado && this._formaPago[i].estado=='A'){
				total+=parseFloat(this._formaPago[i].valor, 10);
			}
		}
		return total;
	},

	_getTotalMovi: function(){
		var debitos = 0;
		var creditos = 0;

		for(var i=0;i<this._movimientos.length;i++){
			if(this._movimientos[i].estado=='A'){
				if(this._movimientos[i].naturaleza=='D'){
					debitos+=parseFloat(this._movimientos[i].valor2, 10);
				} else {
					creditos+=parseFloat(this._movimientos[i].valor2, 10);
				}
			}
		}

		for(var i=0;i<this._formaPago.length;i++){
			if(this._formaPago[i].estado=='A'){
				if(this._formaPago[i].naturaleza=='D'){
					debitos+=parseFloat(this._formaPago[i].valor, 10);
				} else {
					creditos+=parseFloat(this._formaPago[i].valor, 10);
				}
			}
		}

		var valCartera = this._sumCartera();
		if(valCartera>-1){
			creditos += valCartera;
		} else {
			debitos += valCartera;			
		}

		return {'debitos': debitos,'creditos': creditos};
	},


	/**
	 * @this {ReciboCaja} Forma de Pago
	 */
	_generarTablaFp: function(){
		var tBodyFormaPago = this.selectOne('tbody#cFormaPago');
		tBodyFormaPago.innerHTML = "";
		var total = 0;
		var debitos = 0;
		var totalFormaPago = this._getTotalFormaPago();
		if(!totalFormaPago){
			creditos = 0;
		} else {
			var creditos = parseFloat(totalFormaPago, 10);
		};
		for(var i=0;i<this._formaPago.length;i++){
			if(this._formaPago[i].estado=='A'){
				var html = "<tr>"+
				"<td align='center'>"+(i+1)+"</td>"+
				"<td>"+this._formaPago[i].formaPago_det+"</td>"+
				"<td>"+this._formaPago[i].numero+"</td>"+
				"<td>"+this._formaPago[i].descripcion+"</td>"+
				"<td align='right'>"+this._formaPago[i].valor+"</td>"+
				"<td align='center'><div class='hyDetails'></div></td>"+
				"<td align='center'><div class='hyDelete'></div></td>"+
				"</tr>";
				tBodyFormaPago.innerHTML+=html;
				total+=this._formaPago[i].valor;
			}
		};
		var hyDetails = tBodyFormaPago.select('div.hyDetails');
		for(var i=0;i<hyDetails.length;i++){
			hyDetails[i].observe('click', this._editarFp.bind(this, i));
		};

		var hyDelete = tBodyFormaPago.select('div.hyDelete');
		for(var i=0;i<hyDelete.length;i++){
			hyDelete[i].observe('click', this._eliminarFp.bind(this, i));
		};

		sumasContab = this._getTotalMovi();

		this._updateSumas(parseFloat(sumasContab.debitos,10), parseFloat(sumasContab.creditos,10));

		return total;
	},

	/**
	 * @this {ReciboCaja}
	 */
	_updateSumas: function(debitos, creditos){
		if(isNaN(creditos)){
			creditos = 0;
		};
		if(isNaN(debitos)){
			debitos = 0;
		};
		var html = '<table class="sumasTable"><tr><td>Débitos <b>'+debitos+'</b></td>';
		html+='<td>Créditos <b>'+creditos+'</b></td>';
		if((debitos-creditos)!=0){
			html+='<td>Diferencia <b class="descuadre">'+(debitos-creditos)+'</b></td>';
		} else {
			html+='<td>Diferencia <b>'+(debitos-creditos)+'</b></td>';
		};
		html+='</tr></table>';
		this._sumasIguales.update(html);
	},

	/**
	 * @this {ReciboCaja}
	 */
	_editar: function(index){
		this.selectOne('input#nit2').setValue(this._movimientos[index].nit2);
		this.selectOne('input#nit2_det').setValue(this._movimientos[index].nombre2);
		this.selectOne('input#cuenta').setValue(this._movimientos[index].cuenta);
		this.selectOne('input#cuenta_det').setValue(this._movimientos[index].detalleCuenta);
		this.selectOne('select#naturaleza').setValue(this._movimientos[index].naturaleza);
		this.selectOne('input#descripcion').setValue(this._movimientos[index].descripcion);
		this.selectOne('select#centroCosto').setValue(this._movimientos[index].centroCosto);
		this.selectOne('input#valor2').setValue(this._movimientos[index].valor2);
		this._activeMov = index;
		this.selectOne('input#addMovi').value = "Guardar";
		this.selectOne('input#nuevoMovi').show();
	},

	/**
	 * @this {ReciboCaja} Forma de pago
	 */
	_editarFp: function(index){
		this.selectOne('select#fp_formaPago').setValue(this._formaPago[index].formaPago);
		this.selectOne('input#fp_numero').setValue(this._formaPago[index].numero);
		this.selectOne('input#fp_descripcion').setValue(this._formaPago[index].descripcion);
		this.selectOne('input#fp_valor').setValue(this._formaPago[index].valor);
		this._activeFormaPago = index;
		this.selectOne('input#addFormaPago').value = "Guardar";
		this.selectOne('input#nuevoFormaPago').show();
	},

	/**
	 * @this {ReciboCaja}
	 */
	_eliminar: function(index){
		this._movimientos[index].estado = 'B';
		this._generarTabla();
	},

	/**
	 * @this {ReciboCaja}
	 */
	_eliminarFp: function(index){
		this._formaPago[index].estado = 'B';
		this._generarTablaFp();
	},


	/**
	 * @this {ReciboCaja}
	 */
	_calculateSumas: function(){
		var debitos = 0;
		var valorElement = this.selectOne('input#valor');
		if(!valorElement || valorElement.getValue()===''){
			creditos = 0;
		} else {
			var creditos = parseFloat(valorElement.getValue(), 10);
		};

		//Count Contab movement
		for(var i=0;i<this._movimientos.length;i++){
			if(this._movimientos[i].estado=='A'){
				if(this._movimientos[i].naturaleza=='D'){
					debitos+=parseFloat(this._movimientos[i].valor2,10);
				} else {
					creditos+=parseFloat(this._movimientos[i].valor2,10);
				}
			}
		};

		//Count forma pago movement
		for(var i=0;i<this._formaPago.length;i++){
			if(this._formaPago[i].estado=='A'){
				debitos+=parseFloat(this._formaPago[i].valor,10);
			}
		}
		
		var valCartera = this._sumCartera();
		if(valCartera>-1){
			creditos += valCartera;
		} else {
			debitos += valCartera;
		}

		this._updateSumas(debitos, creditos);
	},

	/**
	 * @this {ReciboCaja}
	 */
	_onChangeReciboCajara: function(ReciboCajaraElement){
		if(ReciboCajaraElement.getValue()!='@'){
			var numeroReciboCajaElement = this.selectOne('select#numeroReciboCaja');
			numeroReciboCajaElement.selectedIndex = 0;
			new HfosAjax.JsonRequest('recibo_caja/nextReciboCaja', {
				parameters: {
					"ReciboCajaraId": ReciboCajaraElement.getValue()
				},
				onSuccess: function(numeroReciboCajaElement, response){
					numeroReciboCajaElement.innerHTML = "";
					if(response.status=='OK'){
						var html = "";
						response.lista.each(function(numero){
							html+="<option value='"+numero+"'>"+numero;
						});
						numeroReciboCajaElement.innerHTML = html;
					} else {
						new HfosModal.alert({
							title: 'Generar ReciboCajas',
							message: 'La ReciboCajara tiene todos sus ReciboCajas emitidos'
						});
					}
				}.bind(this, numeroReciboCajaElement)
			});
		};
		ReciboCajaraElement.blur();
	},

	/**
	 * @this {ReciboCaja}
	 */
	_backToSearch: function(){
		this._oldTercero = null;
		this.go('recibo_caja/index', {
			onSuccess: this._setIndexCallbacks.bind(this)
		});
		this.hideStatusBar();
	},

	/**
	 * @this {ReciboCaja}
	 */
	_printReciboCaja: function(){
		window.open(Utils.getKumbiaURL('recibo_caja/imprimir?'+this._key))
	},

	/**
	 * @this {ReciboCaja}
	 */
	_anulaReciboCaja: function(){
		new HfosModal.confirm({
			title: 'ReciboCajas',
			message: 'Seguro desea anular el ReciboCaja?',
			onAccept: function(){
				new HfosAjax.JsonRequest('recibo_caja/anular', {
					checkAcl: true,
					parameters: this._key,
					onSuccess: function(response){
						if(response.status=='FAILED'){
							this.getMessages().error(response.message);
						} else {
							this.getMessages().success(response.message);
						};
						window.setTimeout(function(){
							this._backToSearch();
						}.bind(this), 2000);
					}.bind(this)
				});
			}.bind(this)
		});
	}

});

HfosBindings.late('win-recibo-caja', 'afterCreate', function(hfosWindow){
	var reciboCaja = new ReciboCaja(hfosWindow);
});