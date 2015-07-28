
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

var Excluir = Class.create(HfosProcessContainer, {

	_subirForm: null,

	/**
	 *
	 * @constructor
	 */
	initialize: function(container){
		this.setContainer(container);
		
		new HfosTabs(this, 'tabbed');
		
		new HfosForm(this, 'excluirForm', {
			update: 'resultados',
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success('Se generó el movimiento correctamente');
					window.open(Utils.getURL(response.file));
				} else {
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
					}
				}
			}
		});
		
		new HfosForm(this, 'excluirForm2', {
			update: 'resultados',
			onSuccess: function(response){
				if(response.status=='OK'){
					this.getMessages().success('Se generó el movimiento correctamente');
				} else {
					if(response.status=='FAILED'){
						this.getMessages().error(response.message);
						if(typeof response.file != "undefined"){
							window.open(Utils.getURL(response.file));
						}
					}
				}
			}
		});
	}

});

HfosBindings.late('win-excluir', 'afterCreateOrRestore', function(hfosWindow){
	var excluir = new Excluir(hfosWindow);
});
