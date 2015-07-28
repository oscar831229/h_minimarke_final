
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

var Tatico = {

	/**
	 * Obtiene el costo y saldo actual de una receta
	 */
	getReferenciaOrReceta: function(codigoAlmacen, codigoItem, tipoDetalle, onSuccessProcedure)
	{
		new HfosAjax.JsonRequest('tatico/getReferenciaOrReceta', {
			method: 'GET',
			checkAcl: true,
			parameters: {
				'almacen': codigoAlmacen,
				'codigoItem': codigoItem,
                'tipoDetalle': tipoDetalle
			},
			onSuccess: onSuccessProcedure
		});
	},

	/**
	 * Metodo que usa tatico controllar para obtener informacion de una referencia y si se desea se filtra por almacen
	 */
	getReferencia: function(codigoItem, onSuccessProcedure,almacen)
	{
		if (!almacen) {
			almacen = '';
		};
		new HfosAjax.JsonRequest('tatico/getReferencia', {
			method: 'GET',
			checkAcl: true,
			parameters: {
				'codigoItem': codigoItem,
				'almacen': almacen
			},
			onSuccess: onSuccessProcedure
		});
	},

	/**
	 * Trae el saldo de una referencia en un almacen
	 */
	getSaldoReferencia: function(codigoItem, almacen, onSuccessProcedure)
	{
		new HfosAjax.JsonRequest('tatico/getSaldoReferencia', {
			method: 'GET',
			checkAcl: true,
			parameters: {
				'codigoItem': codigoItem,
				'almacen': almacen
			},
			onSuccess: onSuccessProcedure
		});
	},

	getOrdenDeCompra: function(nAlmacen, nPedido, onSuccessProcedure)
	{
		new HfosAjax.JsonRequest('tatico/getOrdenDeCompra', {
			method: 'GET',
			checkAcl: true,
			parameters: {
				'nAlmacen': nAlmacen,
				'nPedido': nPedido
			},
			onSuccess: onSuccessProcedure
		});
	},

	getPedido: function(nAlmacen, nPedido, onSuccessProcedure)
	{
		new HfosAjax.JsonRequest('tatico/getPedido', {
			method: 'GET',
			checkAcl: true,
			parameters: {
				'nAlmacen': nAlmacen,
				'nPedido': nPedido
			},
			onSuccess: onSuccessProcedure
		});
	},

	getTaxes: function(parameters, onSuccessProcedure)
	{
		new HfosAjax.JsonRequest('tatico/getTaxes', {
			parameters: parameters,
			checkAcl: true,
			onSuccess: onSuccessProcedure
		});
	},

	getCalcularTransformacion: function(parameters, onSuccessProcedure)
	{
		new HfosAjax.JsonRequest('tatico/getCalcularTransformacion', {
			parameters: parameters,
			checkAcl: true,
			onSuccess: onSuccessProcedure
		});
	},

	/**
	 * Muestra una pantalla de tomar ordenes de compra
	 */
	queryOrden: function(queryElement)
	{
		var hfosWindow = HfosCommon.findWindow(queryElement);
		var hyperForm = hfosWindow.getSubprocess();
		var activeSection = hyperForm.getActiveSection();
		new HfosModalForm(hyperForm, 'ordenes/consultar', {
			notSubmit: true,
			parameters: {
				'almacen': activeSection.selectOne('#almacen').getValue()
			},
			style: {
				'width': '650px'
			},
			afterShow: function(hyperForm, activeSection, form){
				Tatico.addQueryOrdenCallbacks(form, hyperForm, activeSection);
				var consultarButton = form.getElement('consultarButton');
				consultarButton.observe('click', function(){
					var ordenForm = this.getElement('ordenForm');
					new HfosAjax.FormRequest(ordenForm, {
						onLoading: function(){
							this.getElement('formSpinner').show();
						}.bind(this),
						onSuccess: function(form, hyperForm, activeSection, transport){
							this.getElement('ordenes').update(transport.responseText);
							Tatico.addQueryOrdenCallbacks(form, hyperForm, activeSection);
						}.bind(this, form, hyperForm, activeSection),
						onComplete: function(){
							this.getElement('formSpinner').hide();
						}.bind(this)
					});
				}.bind(form));
			}.bind(this, hyperForm, activeSection)
		});
	},

	/**
	 * Agrega callabacks que permiten seleccionar una orden de la lista de ordenes
	 */
	addQueryOrdenCallbacks: function(form, hyperForm, activeSection)
	{
		var pedidosTable = form.selectOne('table#ordenesTable');
		if (pedidosTable != null) {
			var browse = new HfosBrowseData(form);
			browse.fromHtmlTable(form, pedidosTable, 5);
			var seleccionarButtons = pedidosTable.select('input.seleccionarButton');
			for(var i = 0; i < seleccionarButtons.length; i++) {
				seleccionarButtons[i].lang = seleccionarButtons[i].title;
				seleccionarButtons[i].title = 'Seleccionar la orden de compra';
				seleccionarButtons[i].observe('click', function(element, form, hyperForm, activeSection){
					var nAlmacen = activeSection.selectOne('#almacen');
					var nPedido = activeSection.selectOne('#n_pedido');
					var pedido = element.lang.split('-');
					nAlmacen.setValue(pedido[0]);
					nPedido.setValue(pedido[1]);
					form.close();
					nPedido.activate();
					hyperForm.fire('ordenUpdated');
				}.bind(this, seleccionarButtons[i], form, hyperForm, activeSection));
			};
		} else {
			form.getMessages().notice('No se encontraron orden de compra');
		}
	},

	/**
	 * Muestra una pantalla de tomar pedidos
	 */
	queryPedido: function(queryElement)
	{
		var hfosWindow = HfosCommon.findWindow(queryElement);
		var hyperForm = hfosWindow.getSubprocess();
		var activeSection = hyperForm.getActiveSection();
		new HfosModalForm(hyperForm, 'pedidos/consultar', {
			notSubmit: true,
			parameters: {
				'almacen': activeSection.selectOne('#almacen').getValue()
			},
			style: {
				'width': '650px'
			},
			afterShow: function(hyperForm, activeSection, form){
				Tatico.addQueryPedidoCallbacks(form, hyperForm, activeSection);
				var consultarButton = form.getElement('consultarButton');
				consultarButton.observe('click', function(){
					var pedidoForm = this.getElement('pedidoForm');
					new HfosAjax.FormRequest(pedidoForm, {
						onLoading: function(){
							this.getElement('formSpinner').show();
						}.bind(this),
						onSuccess: function(form, hyperForm, activeSection, transport){
							this.getElement('pedidos').update(transport.responseText);
							Tatico.addQueryPedidoCallbacks(form, hyperForm, activeSection);
						}.bind(this, form, hyperForm, activeSection),
						onComplete: function(){
							this.getElement('formSpinner').hide();
						}.bind(this)
					});
				}.bind(form));
			}.bind(this, hyperForm, activeSection)
		});
	},

	/**
	 * Agrega callabacks que permiten seleccionar un pedido de la lista de pedidos
	 */
	addQueryPedidoCallbacks: function(form, hyperForm, activeSection)
	{
		var pedidosTable = form.selectOne('table#pedidosTable');
		if (pedidosTable != null) {
			var browse = new HfosBrowseData(form);
			browse.fromHtmlTable(form, pedidosTable, 5);
			var seleccionarButtons = pedidosTable.select('input.seleccionarButton');
			for (var i = 0; i < seleccionarButtons.length; i++) {
				seleccionarButtons[i].lang = seleccionarButtons[i].title;
				seleccionarButtons[i].title = 'Seleccionar el pedido';
				seleccionarButtons[i].observe('click', function(element, form, hyperForm, activeSection){
					var nAlmacen = activeSection.selectOne('#almacen');
					var nPedido = activeSection.selectOne('#n_pedido');
					var pedido = element.lang.split('-');
					nAlmacen.setValue(pedido[0]);
					nPedido.setValue(pedido[1]);
					form.close();
					nPedido.activate();
					hyperForm.fire('pedidoUpdated');
				}.bind(this, seleccionarButtons[i], form, hyperForm, activeSection));
			};
		} else {
			form.getMessages().notice('No se encontraron pedidos');
		}
	},

	getRodizioReferencias: function(onSuccess)
	{
		new HfosAjax.JsonRequest('tatico/getRodizioReferencias', {
			checkAcl:   true,
			onSuccess:  onSuccess
		});
	}

};
