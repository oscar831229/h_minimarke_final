
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

var Fisico = Class.create(HfosProcessContainer, {

	_subirForm: null,

	initialize: function(container)
	{
		this.setContainer(container);
		if (container.wasRestored() == false) {
			this._setIndexCallbacks();
		} else {
			switch (container.getState()) {
				case 'index':
					this._setIndexCallbacks();
					break;
				case 'query':
					this._setQueryCallbacks();
					break;
			}
		}
	},

	_setIndexCallbacks: function()
	{
		this._setBuscarFormCallbacks();
		this.setState('index');
	},

	_setBuscarFormCallbacks: function()
	{
		var buscarButton = this.getElement('consultarButton');
		if (buscarButton !== null) {
			buscarButton.observe('click', function(buscarButton){
				this.getElement("formSpinner").show();
				this.go('fisico/consultar', {
					parameters: buscarButton.form.serialize(true),
					onSuccess: this._setQueryCallbacks.bind(this)
				});
			}.bind(this, buscarButton));
		}
	},

	_setQueryCallbacks: function()
	{
		var backButton  = this.getElement('backSearchButton');
		var printButton = this.getElement('printButton');
		var saveButton  = this.getElement('saveButton');
		if (this.getElement('hyBrowseTab') !== null) {
			var cantidadInput = this.getElement('hyBrowseTab').selectOne('.cantidadesFisicas');
			if(cantidadInput!==null){
				cantidadInput.activate();
			}
		}
		if (printButton!==null) {
			printButton.observe('click', this._printFisico.bind(this, saveButton));
		}
		if (saveButton!==null) {
			saveButton.observe('click', this._saveFisico.bind(this, saveButton));
		}
		if (backButton!==null) {
			backButton.observe('click', this._backConsultar.bind(this));
		};
		this._setBuscarFormCallbacks();
		this.setState('query');
	},

	_backConsultar: function()
	{
		this.go('fisico/index', {
			onSuccess: this._setIndexCallbacks.bind(this)
		});
	},

	/**
	 * Imprime los datos del conteo físico vs conteo actual
	 */
	_printFisico: function(printButton)
	{
		printButton.disable();
		this.getElement("headerSpinner").show();
		var form = this.getElement('saveForm');
		console.log($(form).serialize());
		new HfosAjax.JsonRequest('fisico/print', {
			parameters: $(form).serialize(true),
			checkAcl: true,
			onSuccess: function(printButton, response){
				if(response.status=='OK'){
					this.getMessages().success(response.message);
					window.open(Utils.getURL(response.file));
				} else {
					this.getMessages().error(response.message);
					printButton.enable();
				};
			}.bind(this, printButton),
			onComplete: function(printButton){
				this.getElement("headerSpinner").hide();
				printButton.enable();
			}.bind(this, printButton)
		});
	},

	/**
	 * Guarda los datos del conteo físico
	 */
	_saveFisico: function(saveButton)
	{
		saveButton.disable();
		this.getElement("headerSpinner").show();
		var form = this.getElement('saveForm');
		new HfosAjax.JsonRequest('fisico/guardar', {
			parameters: form.serialize(true),
			checkAcl: true,
			onSuccess: function(saveButton, response){
				if(response.status=='OK'){
					this.getMessages().success(response.message);
				} else {
					this.getMessages().error(response.message);
					saveButton.enable();
				};
			}.bind(this, saveButton),
			onComplete: function(saveButton){
				this.getElement("headerSpinner").hide();
				saveButton.enable();
			}.bind(this, saveButton)
		});
	}

});

HfosBindings.late('win-fisico', 'afterCreateOrRestore', function(hfosWindow){
	var fisico = new Fisico(hfosWindow);
});
