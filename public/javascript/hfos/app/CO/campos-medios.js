
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
 * CamposMedios
 *
 *
 */
var CamposMedios = Class.create(HfosProcessContainer, {

	/**
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	/**
	 * @this {CamposMedios}
	 */
	_setIndexCallbacks: function(){
		var magforElement = this.selectOne('select#magfor');
		magforElement.observe('change', this._showCampos.bind(this, magforElement));

		var saveButton = this.getElement('saveButton');
		saveButton.observe('click', this._guardar.bind(this));

	},

	/**
	 * @this {CamposMedios}
	 */
	_guardar: function(){
		var camposCuentasForm = this.getElement('camposCuentasForm');
		new HfosAjax.JsonFormRequest(camposCuentasForm, {
			onCreate: function(){
				this.getElement('headerSpinner').show();
			}.bind(this),
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success(response.message);
				} else {
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					}
				}
			}.bind(this),
			onComplete: function(){
				this.getElement('headerSpinner').hide();
			}.bind(this)
		})
	},

	/**
	 * @this {CamposMedios}
	 */
	_showCampos: function(magforElement){
		if(magforElement.getValue()!='@'){
			new HfosAjax.Request('campos_medios/getCampos', {
				parameters: 'codigoFormato='+magforElement.getValue(),
				onSuccess: function(transport){
					this.getElement('campos').update(transport.responseText);
					this._camposCallbacks();
					this._notifyContentChange();
				}.bind(this)
			});
		} else {
			this.getElement('campos').update('');
			this._notifyContentChange();
		}
	},

	/**
	 * @this {CamposMedios}
	 */
	_camposCallbacks: function(){
		var campos = this.getElement('camposDisponibles').select('input[type="checkbox"]');
		for(var i=0;i<campos.length;i++){
			campos[i].observe('change', this._selectCampo.bind(this, campos[i]));
		};
		this._sortCampos();

		var camposCuentasTable = this.getElement('camposCuentasTable');
		var trElements = camposCuentasTable.tBodies[0].select('tr');
		for(var i=0;i<trElements.length;i++){
			this._addRowHandler(trElements[i]);
		};

		new HfosTabs(this, 'tabbed');
	},

	/**
	 * @this {CamposMedios}
	 */
	_addRowHandler: function(trElement){
		var divAccept = trElement.selectOne('div.hyAccept');
		divAccept.title = 'Duplicar';
		divAccept.observe('click', this._addCuentasRow.bind(this, trElement));
		var divDelete = trElement.selectOne('div.hyDelete');
		if(divDelete!==null){
			divDelete.observe('click', this._removeCuentasRow.bind(this, trElement));
		}
	},

	/**
	 * @this {CamposMedios}
	 */
	_addCuentasRow: function(trElement){
		var trClone = trElement.cloneNode(true);
		trClone.appendChild(new Element('TD').update('<div class="hyDelete"></div>'));
		trElement.parentNode.appendChild(trClone);
		this._addRowHandler(trClone);
	},

	/**
	 * @this {CamposMedios}
	 */
	_removeCuentasRow: function(trElement){
		trElement.erase();
	},

	/**
	 * @this {CamposMedios}
	 */
	_sortCampos: function(){
		var camposSeleccionados = this.getElement('camposSeleccionados');
		Sortable.destroy(camposSeleccionados);
		Sortable.create(camposSeleccionados);
	},

	/**
	 * @this {CamposMedios}
	 */
	_selectCampo: function(campo){
		var camposSeleccionados = this.getElement('camposSeleccionados');
		var campoId = campo.id.replace('campo_');
		campo.up().toggleClassName('selected');
		if(campo.checked==true){
			var campoNombre = campo.adjacent('span')[0];
			var li = document.createElement('LI');
			li.setAttribute('id', 'campo_'+campoId);
			li.update(campoNombre.innerHTML);
			camposSeleccionados.appendChild(li);
		} else {
			var campoSel = camposSeleccionados.selectOne('li#campo_'+campoId);
			camposSeleccionados.removeChild(campoSel);
		};
		this._sortCampos();
	}

});

HfosBindings.late('win-campos-medios', 'afterCreate', function(hfosWindow){
	var camposMedios = new CamposMedios(hfosWindow);
});