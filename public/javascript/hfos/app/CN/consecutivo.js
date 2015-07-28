
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
 * Clase Cheque
 *
 * Cada formulario de Chques en pantalla tiene asociado una instancia de esta clase
 */
var Cheque = Class.create(HfosProcessContainer, {

	_key: null,

	_movimientos: [],

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
	 * @this {Cheque}
	 */
	_setIndexCallbacks: function(){

		var newButton = this.getElement('newButton');
		if(newButton!==null){
			newButton.observe('click', this._newCheque.bind(this));

			//Formulario de buscar
			new HfosForm(this, 'buscarForm', {
				update: 'resultados',
				onSuccess: function(response){

					switch(response.number){
						case '0':
							this.getMessages().notice('No se encontraron cheques');
							break;

						case '1':
							this._key = response.key;
							this.go('cheque/ver', {
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
	 * @this {Cheque}
	 */
	_detailsHandler: function(element){
		this._key = element.retrieve('primary');
		this.go('cheque/ver', {
			parameters: this._key,
			onSuccess: this._setDetailsCallbacks.bind(this)
		});
	},

	/**
	 * @this {Cheque}
	 */
	_newCheque: function(){
		this.go('cheque/nuevo', {
			onSuccess: this._setNewCallbacks.bind(this)
		});
	},

	/**
	 * @this {Cheque}
	 */
	_setDetailsCallbacks: function(){

		var searchButton = this.getElement('backButton');
		searchButton.observe('click', this._backToSearch.bind(this));

		var printButton = this.getElement('printButton');
		if(printButton!==null){
			printButton.observe('click', this._printCheque.bind(this));

			var deleteButton = this.getElement('deleteButton');
			deleteButton.observe('click', this._anulaCheque.bind(this));

			var verComprobButton = this.getElement('verComprob');
			if(verComprobButton!==null){
				verComprobButton.observe('click', Movimientos.abrir.bind(this, verComprobButton.title));
			} else {
				deleteButton.hide();
			}

		}
	},

	/**
	 * @this {Cheque}
	 */
	abrirComprobante: function(){

	},

	/**
	 * @this {Cheque}
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
				new HfosAjax.JsonRequest('cheque/getCartera', {
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
								html+='<th class="sortcol">F. Emisión</th>';
								html+='<th class="sortcol">F. Vence</th>';
								html+='<th class="sortcol">Saldo</th>';
								html+='<th class="sortcol">Abono</th>';
								html+='</thead><tbody>';
								for(var i=0;i<response.documentos.length;i++){
									var key = response.documentos[i].tipoDoc+'_'+response.documentos[i].numeroDoc;
									html+='<tr><td><input type="checkbox" name="numeroDoc[]" value="'+key+'"/></td>';
									html+='<td>'+response.documentos[i].tipoDoc+'</td>';
									html+='<td align="right">'+response.documentos[i].numeroDoc+'</td>';
									html+='<td>'+response.documentos[i].fEmision+'</td>';
									html+='<td>'+response.documentos[i].fVence+'</td>';
									html+='<td align="right">'+response.documentos[i].saldo+'</td>';
									html+='<td align="right"><input type="text" name="abono'+key+'" class="abono numeric" value="'+response.documentos[i].saldoValor+'" size="9" maxlength="12"></td>';
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
	 * @this {Cheque}
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
	 * @this {Cheque}
	 */
	_selectNumeroDoc: function(checkElement){
		checkElement.up(1).toggleClassName('selected');
		this._calculateSumas();
	},

	/**
	 * @this {Cheque}
	 */
	_updateDebitosDoc: function(){
		this._calculateSumas();
	},

	/**
	 * @this {Cheque}
	 */
	_onChangeTab: function(name){
		if(name=="Cartera"){
			this._updateCartera();
		}
	},

	/**
	 * @this {Cheque}
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
	 * @this {Cheque}
	 */
	_setNewCallbacks: function(){

		var searchButton = this.getElement('backButton');
		searchButton.observe('click', this._backToSearch.bind(this));

		var saveButton = this.getElement('saveButton');
		saveButton.observe('click', this._grabarCheque.bind(this));

		new HfosTabs(this, 'tabbed', {
			onChange: this._onChangeTab.bind(this)
		});

		HfosCommon.addCuentaCompleter('cuenta');
		HfosCommon.addTerceroCompleter('nit', true);
		HfosCommon.addTerceroCompleter('nit2');

		var nitNombreElement = this.selectOne('input#nit_det');
		nitNombreElement.observe('blur', this._updateBeneficiario.bind(this));

		var chequeraElement = this.selectOne('select#chequeraId');
		chequeraElement.observe('change', this._onChangeChequera.bind(this, chequeraElement));

		var valorElement = this.selectOne('input#valor');
		valorElement.observe('blur', this._calculateSumas.bind(this));

		var addMoviElement = this.selectOne('input#addMovi');
		addMoviElement.observe('click', this._addMovimiento.bind(this, addMoviElement));

		this._showStatusBar();
		this._updateSumas(0, 0);

		this._movimientos = [];
		this._activeMov = 0;

	},

	/**
	 * @this {Cheque}
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
	 * @this {Cheque}
	 */
	_grabarCheque: function(){
		var total = 0;
		var debitos = 0;
		var valorElement = this.selectOne('input#valor');
		if(valorElement.getValue()===''){
			creditos = 0;
		} else {
			var creditos = parseFloat(valorElement.getValue());
		};
		for(var i=0;i<this._movimientos.length;i++){
			if(this._movimientos[i].estado=='A'){
				if(this._movimientos[i].naturaleza=='D'){
					debitos+=this._movimientos[i].valor2;
				} else {
					creditos+=this._movimientos[i].valor2;
				}
			}
		};
		var checkElements = this.getElement('carteraContent').select('input[type="checkbox"]');
		var abonosElements = this.getElement('carteraContent').select('input.abono');
		for(var i=0;i<checkElements.length;i++){
			if(checkElements[i].checked){
				for(var j=0;j<this._documentos.length;j++){
					var documentoCheck = checkElements[i].getValue().split("_");
					if(this._documentos[j].numeroDoc==documentoCheck[1]){
						var valor = abonosElements[j].getValue();
						if(valor!==''){
							debitos+=parseFloat(valor, 10);
						}
					}
				};
			}
		};
		if(debitos!=creditos){
			new HfosModal.alert({
				title: 'Generar Cheques',
				message: "Movimientos contables descuadrados, por favor revise"
			});
		} else {
			var nitElement = this.selectOne('input#nit');
			if(nitElement.value.strip()==''||nitElement.value=='0'){
				new HfosModal.alert({
					title: 'Generar Cheques',
					message: 'Debe indicar el tercero del comprobante'
				});
				nitElement.activate();
				return;
			};
			var beneficiarioElement = this.selectOne('input#beneficiario');
			if(beneficiarioElement.getValue()==''){
				this._updateBeneficiario();
			};
			var chequeraId = this.selectOne('select#chequeraId').getValue();
			if(chequeraId=="@"){
				new HfosModal.alert({
					title: 'Generar Cheques',
					message: 'Debe indicar la chequera'
				});
				return;
			};
			var valorElement = this.selectOne('input#valor');
			if(valorElement.getValue()<=0||valorElement.getValue()==""){
				new HfosModal.alert({
					title: 'Generar Cheques',
					message: 'Debe indicar el valor del cheque'
				});
				return;
			};
			var observacionElement = this.selectOne('textarea#observacion');
			if(observacionElement.value.strip()==''||observacionElement.value=='0'){
				new HfosModal.alert({
					title: 'Generar Cheques',
					message: 'Debe indicar la observación ó nota del cheque'
				});
				nitElement.activate();
				return;
			};
			var formGrabar = this.getElement('formGrabar');
			formGrabar.select('input.movementHidden').each(function(movementHidden){
				movementHidden.erase();
			});
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
			new HfosAjax.JsonFormRequest(formGrabar, {
				onSuccess: function(response){
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					} else {
						if(response.status=='OK'){
							this._key = response.id;
							this.go('cheque/ver', {
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
	 * @this {Cheque}
	 */
	_addMovimiento: function(addMoviElement){
		try {
			var cuentaElement = this.selectOne('input#cuenta');
			if(cuentaElement.getValue()==""){
				new HfosModal.alert({
					title: 'Generar Cheques',
					message: 'Debe indicar la cuenta'
				});
				cuentaElement.activate();
				return;
			};
			var valorElement = this.selectOne('input#valor2');
			if(valorElement.getValue()==""||valorElement.getValue()=="0"){
				new HfosModal.alert({
					title: 'Generar Cheques',
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
			nitNombreElement.setValue('');
			cuentaElement.setValue('');
			naturalezaElement.setValue('');
			descripcionElement.setValue('');
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
	 * @this {Cheque}
	 */
	_generarTabla: function(){
		var tBodyMovimiento = this.selectOne('tbody#cmovi');
		tBodyMovimiento.innerHTML = "";
		var total = 0;
		var debitos = 0;
		var valorElement = this.selectOne('input#valor');
		if(valorElement.getValue()===''){
			creditos = 0;
		} else {
			var creditos = parseFloat(valorElement.getValue(), 10);
		};
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
					debitos+=this._movimientos[i].valor2;
				} else {
					creditos+=this._movimientos[i].valor2;
				}
			}
		};
		var hyDetails = tBodyMovimiento.select('div.hyDetails');
		for(var i=0;i<hyDetails.length;i++){
			hyDetails[i].observe('click', this._editar.bind(this, i));
		};

		this._updateSumas(debitos, creditos);

		return total;
	},

	/**
	 * @this {Cheque}
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
	 * @this {Cheque}
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
	 * @this {Cheque}
	 */
	_eliminar: function(){
		movimiento[index].estado = 'B';
		generaTabla();
	},

	/**
	 * @this {Cheque}
	 */
	_calculateSumas: function(){
		var debitos = 0;
		var valorElement = this.selectOne('input#valor');
		if(valorElement.getValue()===''){
			creditos = 0;
		} else {
			var creditos = parseFloat(valorElement.getValue(), 10);
		};
		for(var i=0;i<this._movimientos.length;i++){
			if(this._movimientos[i].estado=='A'){
				if(this._movimientos[i].naturaleza=='D'){
					debitos+=this._movimientos[i].valor2;
				} else {
					creditos+=this._movimientos[i].valor2;
				}
			}
		};
		var checkElements = this.getElement('carteraContent').select('input[type="checkbox"]');
		var abonosElements = this.getElement('carteraContent').select('input.abono');
		for(var i=0;i<checkElements.length;i++){
			if(checkElements[i].checked){
				for(var j=0;j<this._documentos.length;j++){
					var documentoCheck = checkElements[i].getValue().split("_");
					if(this._documentos[j].numeroDoc==documentoCheck[1]){
						var valor = abonosElements[j].getValue();
						if(valor!==''){
							debitos+=parseFloat(valor, 10);
						}
					}
				};
			}
		};
		this._updateSumas(debitos, creditos);
	},

	/**
	 * @this {Cheque}
	 */
	_onChangeChequera: function(chequeraElement){
		if(chequeraElement.getValue()!='@'){
			var numeroChequeElement = this.selectOne('select#numeroCheque');
			numeroChequeElement.selectedIndex = 0;
			new HfosAjax.JsonRequest('cheque/nextCheque', {
				parameters: {
					"chequeraId": chequeraElement.getValue()
				},
				onSuccess: function(numeroChequeElement, response){
					numeroChequeElement.innerHTML = "";
					if(response.status=='OK'){
						var html = "";
						response.lista.each(function(numero){
							html+="<option value='"+numero+"'>"+numero;
						});
						numeroChequeElement.innerHTML = html;
					} else {
						new HfosModal.alert({
							title: 'Generar Cheques',
							message: 'La chequera tiene todos sus cheques emitidos'
						});
					}
				}.bind(this, numeroChequeElement)
			});
		};
		chequeraElement.blur();
	},

	/**
	 * @this {Cheque}
	 */
	_backToSearch: function(){
		this._oldTercero = null;
		this.go('cheque/index', {
			onSuccess: this._setIndexCallbacks.bind(this)
		});
		this.hideStatusBar();
	},

	/**
	 * @this {Cheque}
	 */
	_printCheque: function(){
		window.open(Utils.getKumbiaURL('cheque/imprimir?'+this._key))
	},

	/**
	 * @this {Cheque}
	 */
	_anulaCheque: function(){
		new HfosModal.confirm({
			title: 'Cheques',
			message: 'Seguro desea anular el cheque?',
			onAccept: function(){
				new HfosAjax.JsonRequest('cheque/anular', {
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

HfosBindings.late('win-cheque', 'afterCreate', function(hfosWindow){
	var cheque = new Cheque(hfosWindow);
});