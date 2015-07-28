
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

var Incluir = Class.create(HfosProcessContainer, {

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
		this.getElement('saveButton').observe('click', this._uploadFile.bind(this))
	},

	_uploadFile: function(){
		this.setIgnoreTermSignal(true);
		this.getElement('subirArchivo').hide();
		this.getElement('saveButton').disable();
		this.getElement('subirBar').show();
		this.getElement('subirFrame').observe('load', this._loadComplete.bind(this));
		this.getElement('subirForm').submit();
	},

	_loadComplete: function(){
		this.getElement('subirFrame').show();
		this.getElement('saveButton').enable();
		this.getElement('subirArchivo').show();
		this.getElement('subirBar').hide();
		this.setIgnoreTermSignal(false);
	}

});

HfosBindings.late('win-incluir', 'afterCreateOrRestore', function(hfosWindow){
	var incluir = new Incluir(hfosWindow);
});
