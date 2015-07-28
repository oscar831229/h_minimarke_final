
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
 * Clase Matriz Proveedores
 *
 * Cada formulario de Matriz de Proveedores en pantalla tiene asociado una instancia de esta clase
 */
var MatrizProveedores = Class.create(HfosProcessContainer, {

	initialize: function(container){
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	_setIndexCallbacks: function(){

		var referenciaId = this.selectOne('input#referenciaId');
		var referenciaIdDet = this.selectOne('input#referenciaId_det');
		var submitButton = this.getElement('saveButton');

		referenciaId.observe('change', this._showMatriz.bind(this, referenciaId, referenciaIdDet));
		referenciaIdDet.observe('change', this._showMatriz.bind(this, referenciaId, referenciaIdDet));
		submitButton.observe('click', this._saveMatriz.bind(this));

		referenciaId.activate();

		this.getMessages().notice("Seleccione una referencia para ver sus proveedores", false);
	},

	/**
	 * Muestra/Oculta campos segun seleccion de Matriz de proveedores
	 */
	_showMatriz: function(referenciaId, referenciaIdDet){
		if(referenciaId.getValue() && referenciaIdDet.getValue().indexOf("NO EXISTE")<0){
			this.getElement('chooseReferencia').hide();
			this.getElement("matrizProveedoresContent").hide();
			this._generarMatriz();
		} else {
			this.getElement('chooseReferencia').show();
			this.getElement('saveButton').hide();
		};
	},

	/**
	 * Genera matriz de Proveedores por referencia
	 */
	_generarMatriz: function(){
		new HfosAjax.Request('matriz_proveedores/generarMatriz', {
			parameters: {
				'referenciaId': referenciaId.getValue()
			},
			onCreate: function(){
				this.getElement('headerSpinner').show();
			}.bind(this),
			onSuccess: function(transport){

				var matrizProveedoresContent = this.getElement("matrizProveedoresContent");
				matrizProveedoresContent.update(transport.responseText);
				matrizProveedoresContent.show();

				var addButton = this.getElement("addButton");
				if(addButton){
					addButton.observe('click', this._addProveedor.bind(this));
				};

				this._updateMessage();
				this._observeDelete();
				this._addSortable();
				this._notifyContentChange();
			}.bind(this),
			onComplete: function(){
				this.getElement('headerSpinner').hide();
				this.getElement('saveButton').show();
			}.bind(this)
		});
	},

	/**
	 * Metodo que agrega un nuevo proveedor a matriz de proveedores
	 */
	_addProveedor: function(){

		var nitInput = this.selectOne("input#nuevoProveedor");
		var nitNombreInput = this.selectOne("input#nuevoProveedor_det");
		if(nitInput.getValue()==''||nitNombreInput.getValue()==''){
			nitInput.activate();
			this.getMessages().error("Indique los datos del proveedor a agregar", true);
			return;
		};

		var proveedorOrder = this.getElement('proveedorOrder');
		var inputNits = proveedorOrder.select('input[type="hidden"]');
		for(var i=0;i<inputNits.length;i++){
			if(inputNits[i].getValue()==nitInput.getValue()){
				this.getMessages().error("El proveedor ya había sido agregado anteriormente", true);
				return;
			}
		};

		var numero = inputNits.length+1;
		proveedorOrder.insert('<li id="proveedor_'+numero+'"><table width="80%" cellspacing="0" cellpadding="0" class="hyBrowseTab zebraSt sortable"><tr>'+
		'<td width="7"><img src="'+$Kumbia.path+'img/backoffice/grippy.png"/></td>'+
		'<td width="100"><input type="hidden" name="nit[]" value="'+nitInput.getValue()+'">'+nitInput.getValue()+'</td>'+
		'<td align="left">'+nitNombreInput.getValue()+'</td>'+
		'<td width="20"><img src="'+$Kumbia.path+'img/backoffice/delete.gif" class="delete"/></td>'+
		'</tr></table></li>');

		nitInput.setValue('');
		nitNombreInput.setValue('');

		this._updateMessage();
		this._observeDelete();
		this._addSortable();
		this._makeZebra();
		this._notifyContentChange();

	},

	_observeDelete: function(){
		var proveedorOrder = this.getElement('proveedorOrder');
		var deleteImgs = proveedorOrder.select('img.delete');
		for(var i=0;i<deleteImgs.length;i++){
			if(!deleteImgs[i].retrieve('observed')){
				deleteImgs[i].observe('click', function(element){
					element.up(4).erase();
					this._updateMessage();
				}.bind(this, deleteImgs[i]));
				deleteImgs[i].store('observed', true);
			}
		}
	},

	/**
	 * Actualiza el mensaje
	 */
	_updateMessage: function(){
		var proveedorOrder = this.getElement('proveedorOrder');
		var inputNits = proveedorOrder.select('input[type="hidden"]');
		if(inputNits.length==0){
			this.getMessages().notice("Agregue proveedores a los que le compre esta referencia", false);
		} else {
			if(inputNits.length==1){
				this.getMessages().notice("Agregue otros proveedores a los que le compre esta referencia", false);
			} else {
				this.getMessages().notice("Agregue otros proveedores y arrástrelos de una posición a otra para indicar su preferencia. Entre más arriba mayor será su relevancia", false);
			}
		};
	},

	/**
	 * Convierte la lista de proveedores en una lista ordenable
	 */
	_addSortable: function(){
		var proveedorOrder = this.getElement('proveedorOrder');
		Sortable.destroy(proveedorOrder);
		Sortable.create(proveedorOrder, {
			onUpdate: this._makeZebra.bind(this)
		});
	},

	/**
	 * Modifica los colores de las filas de los proveedores
	 */
	_makeZebra: function(){
		var proveedorOrder = this.getElement('proveedorOrder');
		var trRows = proveedorOrder.select('tr');
		for(var i=0;i<trRows.length;i++){
			if((i+1)%2==0){
				trRows[i].setStyle('background:#E5EEFF');
			} else {
				trRows[i].setStyle('background:#ffffff');
			}
		}
	},

	/**
	* Metodo que agrega proveedores a matriz proveedores
	*/
	_saveMatriz: function(){
		var matrizForm = this.getElement('matrizForm');
		new HfosAjax.JsonFormRequest(matrizForm, {
			onCreate: function(){
				this.getElement('headerSpinner').show();
			}.bind(this),
			onSuccess: function(response){
				if(response.status=="OK"){
					this.getMessages().success(response.message);
				} else {
					this.getMessages().error(response.message);
				}
			}.bind(this),
			onComplete: function(){
				this.getElement('headerSpinner').hide();
			}.bind(this)
		});
	}


});

HfosBindings.late('win-matriz-proveedores', 'afterCreateOrRestore', function(hfosWindow){
	var matrizProveedores = new MatrizProveedores(hfosWindow);
});
