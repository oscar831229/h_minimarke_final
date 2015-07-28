
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

var Consultas = Class.create(HfosProcessContainer, {

	_subirForm: null,

	/**
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){
		this._setBuscarFormCallbacks();
	},

	_setBuscarFormCallbacks: function(){
		var buscarButton = this.getElement('buscarButton');
		if(buscarButton!==null){

			buscarButton.observe('click', function(buscarButton){
				this.go('consultas/consultar', {
					parameters: buscarButton.form.serialize(),
					onSuccess: this._setQueryCallbacks.bind(this)
				})
			}.bind(this, buscarButton));

			var tipoElement = this.selectOne('select#tipo');
			var onChangeTipo = this._onChangeTipo.bind(this, tipoElement);
			tipoElement.observe('change', onChangeTipo);
			onChangeTipo();

		}
	},

	_setQueryCallbacks: function(){
		var backButton = this.getElement('backButton');
		if(backButton!==null){
			backButton.observe('click', this._backConsultar.bind(this));
		};
		this._setBuscarFormCallbacks();
	},

	_onChangeTipo: function(tipoElement){
		this.select('tr.consulta-tr').each(function(element){
			element.hide();
		})
		switch(tipoElement.getValue()){
			case 'F':
				this.selectOne('tr#trFechaInicial').show();
				this.selectOne('tr#trFechaFinal').show();
				break;
			case 'N':
				this.selectOne('tr#trNitInicial').show();
				this.selectOne('tr#trNitFinal').show();
				break;
			case 'D':
				this.selectOne('tr#trNumeroInicial').show();
				this.selectOne('tr#trNumeroFinal').show();
				break;
		}
	},

	_backConsultar: function(){
		this.go('consultas/index', {
			onSuccess: this._setIndexCallbacks.bind(this)
		});
	}

});

HfosBindings.late('win-consultas', 'afterCreateOrRestore', function(hfosWindow){
	var consultas = new Consultas(hfosWindow);
});
