
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
 * HyperGridData
 *
 * Hash donde se almacenan los datos temporales de la grilla
 */
var HyperGridData = {

};

/**
 * HyperGrid
 *
 * Grilla par formularios HyperForm
 */
var HyperGrid = Class.create({

	//Formulario HyperForm asociado a la Grilla
   	_hyperForm: null,

	//Datos almacenados en la grilla
	_rows: [],

   	_idGrid: 1,

   	_editId: 0,

   	//Indica si se están cargando varias filas en bloque
   	_bulkLoad: false,

   	//Número de cambios hechos sobre la grilla
   	_numberChanged: 0,

   	//Elemento DOM para referenciar la grilla
   	_element: null,

   	//Configuración de la grilla
   	_field: {},

   	/**
	 * Constructor de HyperGrid
	 *
	 * @constructor
	 */
	initialize: function(hyperForm, fields, restore)
	{

		this._fields = $H(fields);
		this._hyperForm = hyperForm;

		//Agregar evento antes de enviar el formulario
		hyperForm.observe('beforeSendForm', this._setPostDataDetail.bind(this));

		//Preparar el formulario para entrada al ingresar en nuevo/editar
		hyperForm.observe('beforeInput', this._prepareForInput.bind(this));

		//Consultar si hay eventos para la grilla
		var gridEvents = hyperForm.getLateGridBindings();
		if(!Object.isUndefined(gridEvents)){
			$H(gridEvents).each(function(eventProcedure){
				this.observe(eventProcedure[0], eventProcedure[1])
			}.bind(this))
		};

		if(restore==false){
			this.fire('afterInitialize');
		} else {
			this._prepareForInput();
			this.clear();
			this.restoreState();
		}
	},

   	/**
   	 * Agrega inputs para guardar el formulario
   	 *
   	 * @this {HyperGrid}
   	 */
   	_setPostDataDetail: function()
   	{
		var html = '';
		var fields = this._fields.keys();
		for(var i = 0; i < fields.length; i++){
			this.getField(fields[i]).disable();
		};
		var dataDetail = this._hyperForm.getActiveSection().getElement('hyGridDataDetail');
		dataDetail.update(html);
		return true;
	},

	/**
	 * Carga datos base del formulario
	 *
   	 * @this {HyperGrid}
	 */
	loadBaseData: function(dataIndex, fields)
	{
		if (typeof HyperGridData[dataIndex] != "undefined") {
			this._bulkLoad = true;
			var data = HyperGridData[dataIndex];
			for (var i = 0; i < data.length; i++) {
				var row = data[i];
				for (var j = 0; j<fields.length; j++) {
					var fieldName = fields[j];
					var field = this.getField(fieldName);
					if (field !== null) {
						field.setValue(row[fieldName]);
					}
				};
				this.addRow(true);
			};
			delete HyperGridData[dataIndex];
			this._storeRows();
			this._bulkLoad = false;
		};
	},

	/**
	 * Cambia la inserción de filas a bulk-load
	 *
   	 * @this {HyperGrid}
	 */
	setBulkLoad: function(bulkLoad)
	{
		this._bulkLoad = bulkLoad;
	},

	/**
	 * Agrega una fila a la grilla
	 *
   	 * @this {HyperGrid}
	 */
	addRow: function(isBaseData){

		if(this._bulkLoad==false){
			if(this.fire('beforeValidate')===false){
				return;
			};
			if(this._validateEntry()===false){
				return;
			};
			if(this._editId > 0){
				if(this.fire('beforeUpdate')===false){
					return;
				}
			} else {
				if(this.fire('beforeInsert')===false){
					return;
				}
			};
		};

		var table = this._element.getElement('hyGridBodyTable');
		var trElement = document.createElement("TR");
		trElement.addClassName("hySortRow");
		if (this._editId > 0) {
			trElement.id = "tr" + this._editId;
		} else {
	 		trElement.id = "tr" + this._idGrid;
		};

		var tdElement = document.createElement("TD");
		tdElement.addClassName("row-number");
		tdElement.update('<span class="row-number-span">0</span><input type="hidden" name="action[]" value="add"/>');
		trElement.appendChild(tdElement);

		var row = $H({});
		var fields = this._fields.keys();
		for (var i = 0;i < fields.length; i++) {

			var fieldName = fields[i];
			var item = this._fields.get(fieldName);
			if(typeof item['align'] != "undefined"){
				var align = item['align'];
			} else {
				var align = item['type'].match(/int|decimal/) ? 'right' : 'left';
			};

			var tdElement = document.createElement("TD");
			var html = this._getTextValue(fieldName)+"<input type='hidden' name='"+fieldName+"[]' class='"+fieldName+"' value='"+this.getField(fieldName).getValue()+"'/>";
			tdElement.update(html);
			tdElement.setAttribute('align', align);
			tdElement.addClassName(fieldName);
			trElement.appendChild(tdElement);
	 		if(item['ignore']!=true){
				row.set(fieldName, this.getField(fieldName).getValue());
			};

			this.getField(fieldName).setValue(item['type'].match(/domain|relation/) ? '@' : '');
			var detalle = this.getField(fieldName + '_det');
			if(detalle != null){
				row.set(fieldName+'_det', $F(detalle));
				detalle.setValue('');
			};

		};

		var isEdit = false;
		row.set('action', 'add');
		if(this._editId>0){
			for(var i=0;i<this._rows.length;i++){
				if(this._rows[i].get('idGrid')==this._editId){
					row.set('idGrid', parseInt(this._editId, 10));
					this._rows[i] = row;
					isEdit = false;
					break;
				}
			}
		} else {
			row.set('idGrid', this._idGrid++);
			this._rows.push(row);
		};

		//Agregar opción de editar
		var tdElement = document.createElement("TD");
		tdElement.addClassName('hyGridEdtImage');
		tdElement.update('<img src="' + $Kumbia.path+'img/backoffice/edit.png" id="img'+row.get('idGrid')+'" title="Editar"/>');
		tdElement.observe('click', this._editItem.bind(this, row.get('idGrid')));
		trElement.appendChild(tdElement);

		//Agregar opción de borrar fila
		var tdElement = document.createElement("TD");
		tdElement.addClassName('hyGridDelImage');
		tdElement.update('<img src="' + $Kumbia.path+'img/backoffice/delete.gif" id="img'+row.get('idGrid')+'" title="Eliminar"/>');
		tdElement.observe('click', this._removeItem.bind(this, row.get('idGrid')));
		trElement.appendChild(tdElement);

		if (this._editId > 0) {
			var rows = table.select('tr');
			if (rows.length < this._editId) {
				table.appendChild(trElement);
			} else {
				rows[this._editId - 1].insert({
					before: trElement
				});
			}
		} else {
			table.appendChild(trElement);
		};
		this._editId = 0;

		if(this._bulkLoad==false){
			//this._hyperForm.getMessages().clear();
			this._activateFirstField();
		};

		var number = 1;
		var rowNumbers = $$('span.row-number-span');
		for (var i = 0; i < rowNumbers.length; i++) {
			rowNumbers[i].update(number);
			number++;
		};

		if (this._bulkLoad == false) {
			this._hyperForm._notifyContentChange();
		};

		if (isBaseData == false) {
			this._numberChanged++;
			this._storeRows();
		};

		if (this._bulkLoad == false) {
			if(isEdit===true){
				if(this.fire('afterUpdate')===false){
					return;
				}
			} else {
				if(this.fire('afterInsert')===false){
					return;
				}
			};
			this.fire('afterModify');
		}
	},

	/**
	 * Almacena los registros guardados para ser utilizados en la restauración
	 *
   	 * @this {HyperGrid}
	 */
	_storeRows: function()
	{
		var storage = Hfos.getApplication().getStorage();
		if (storage !== null) {
			storage.save('HyperGrids', {
				'id': this._hyperForm.getName(),
				'rows': this._rows
			});
		};
	},

	/**
	 * Obtiene los valores en la grilla
	 *
   	 * @this {HyperGrid}
	 */
	getRowGroup: function()
	{
		var group = new HyperGridGroup();
		for (var i=0;i<this._rows.length;i++){
			var row = new HyperGridRow();
			this._rows[i].each(function(element){
				row.setValue(element[0], element[1], false);
			});
			group.addRow(row, true);
		};
		return group;
	},

	/**
	 * Establece los valores de la grilla
	 *
   	 * @this {HyperGrid}
	 */
	setRowGroup: function(group){
		group.eachRowChanged(function(row){
			row.eachColumnChanged(function(column){
				alert(column.value)
			});
			/*var idGrid = row[0];
			this._rows.each(function(element){
				if(element.get('idGrid')==idGrid){
					var trElement = this.selectOne('tr#tr'+idGrid);
					row[1].each(function(field){
						var tdElement = trElement.getElement(field[0]);
						if(tdElement!==null){
							tdElement.update('x');
						}
					}.bind(this));
				}
			}.bind(this));*/
		}.bind(this));
	},

	/**
	 * Indica si la grilla está vacía
	 *
   	 * @this {HyperGrid}
	 */
	isEmpty: function(){
		return this._rows.length==0;
	},

	/**
	 * Número de cambios realizados sobre la grilla
	 *
   	 * @this {HyperGrid}
	 */
	getNumberChanged: function(){
		return this._numberChanged;
	},

	/**
	 * Elimina una fila previamente insertada
	 *
   	 * @this {HyperGrid}
	 */
	_removeItem: function(numItem)
	{
		if(this.fire('beforeDelete')===false){
			return false;
		};
		var row = this._element.selectOne('#tr'+numItem);
		if(row!==null){
			row.erase();
			this._hyperForm._notifyContentChange();
		};
		for(var i=0;i<this._rows.length;i++){
			if(this._rows[i].get('idGrid')==numItem){
				this._rows[i].set('action', 'del');
				break;
			}
		};
		this._numberChanged++;
		if(this.fire('afterDelete')===false){
			return false;
		};
		this.fire('afterModify');
		return true;
	},

	/**
	 * Elimina todas las filas insertadas
	 *
   	 * @this {HyperGrid}
	 */
	_removeItemAll: function(){
		if(this.fire('beforeDelete')===false){
			return;
		};
		if(typeof this._rows !== "undefined"){
			for(var i=0;i<this._rows.length;i++){
				var element = this._element.selectOne('#tr'+i);
				if(element){
					this._element.selectOne('#tr'+i).erase();
					if(this._rows[i].get('idGrid')){
						this._rows[i].set('action', 'del');
						this._numberChanged++;
					}
				}
			};
		};
		if(this.fire('afterDelete')===false){
			return;
		};
		this.fire('afterModify');
	},

	/**
	 * Edita una fila previamente insertada
	 *
	 * @param {string} numItem
   	 * @this {HyperGrid}
	 */
	_editItem: function(numItem){
		if(this.fire('beforeEdit')===false){
			return;
		};
		var row = this._element.selectOne('#tr'+numItem);
		if(row){
			row.erase();
			if(typeof this._rows != "undefined"){
				for(var i=0;i<this._rows.length;i++){
					if(this._rows[i].get('idGrid')==numItem){
						this._rows[i].set('action', 'edt');
						break;
					}
				};
				var fields = this._fields.keys();
				for(var j=0;j<fields.length;j++){
					var fieldName = fields[j];
					if(typeof this._rows[i] != "undefined"){
						this.getField(fieldName).setValue(this._rows[i].get(fieldName));
						if(this.getField(fieldName+'_det') != null){
							this.getField(fieldName+'_det').setValue(this._rows[i].get(fieldName+'_det'));
						}
					}
				};
				this._editId = numItem;
				this._activateFirstField();
				this._numberChanged++;
				if(this.fire('afterEdit')===false){
					return;
				}
			};
		};
	},

	/**
	 * Actualiza valores en la grilla de una referencia
	 *
   	 * @this {HyperGrid}
	 */
	_updateRowOnGrid: function(datos){
		var data = {};
		//recorremos datos a cambiar
		if(typeof(datos) != 'object'){
			return false;
		};
		if(typeof this._rows != 'object'){
			return false;
		};
		if($H(datos).length<=0){
			return false;
		};
		$H(datos).each(function(newDato){
			var dato = newDato[1];
			if(dato.item){
				for(var i=0;i<this._rows.length;i++){
					if(typeof this._rows[i] != "undefined" && this._rows[i] !== null){
						var oldItem = this._rows[i].get('item');
						var newItem = (dato.item).toString();
						if(oldItem==newItem){
							var idGrid = this._rows[i].get("idGrid");
							this._rows[i].set('valor', dato.valor);
							this._rows[i].set('action', 'add');
							var rowHTML = this._element.selectOne('#tr'+idGrid+" td.valor");
							if(rowHTML){
								rowHTML.update(dato.valor);
							}
						}
					}
				};
			}
		}.bind(this));
	},

	/**
	 * Devuelve el texto de un campo
	 *
   	 * @this {HyperGrid}
	 */
	_getTextValue: function(fieldName)
	{
		var fieldDefinition = this._fields.get(fieldName);
		if (fieldDefinition['type']=='domain' || fieldDefinition['type']=='relation') {
			var field = this.getField(fieldName);
			return field.options[field.selectedIndex].text;
		} else {
			if(typeof fieldDefinition['format'] != "undefined"){
				var format = fieldDefinition['format'];
				return this._format.execute(format, this.getField(fieldName).getValue());
			} else {
				if(this.getField(fieldName + '_det') != null){
					return $F(this.getField(fieldName)) + ' - ' + $F(this.getField(fieldName + '_det'));
				}
			};
			return this.getField(fieldName).getValue();
		}
	},

	/**
	 * Activa el primer campo de la grilla
	 *
   	 * @this {HyperGrid}
	 */
	_activateFirstField: function(){
		this.getField(this._fields.keys()[0]).activate();
	},

	/**
	 * Valida que los campos a insertar sean válidos
	 *
   	 * @this {HyperGrid}
	 */
	_validateEntry: function(){

		var fields = this._fields.keys();
		var invalidFields = [];
		var numberErrors = 0;
		var messages = [];

		for(var i=0;i<fields.length;i++){
			var fieldName = fields[i];
			var fieldDefinition = this._fields.get(fieldName);
			var value = $F(this.getField(fieldName));
			if(!Object.isUndefined(fieldDefinition.notNull)){
				if(fieldDefinition.notNull == true && value.blank()){
					invalidFields.push(fieldName);
					messages.push("El campo "+fieldDefinition.single+" es requerido");
					numberErrors++;
				}
			}
		};

		//Arreglar
		/*for(field in fields){
			var value = '';
			var allFields = '';
			fields.each(function(element){
				value += $F(element);
				allFields+= this._fields[element].single + ' ';
			});
			for(var i=0;i<this._rows.size();i++){
				var value2 = '';
				fields.each(function(element){
					value2+= this._rows[i][element];
				});
				if(value == value2){
					invalidFields.push(arrayKeys[0]);
					message += "El valor de los campos "+allFields+"no puede estar duplicado.<br />";
					numberErrors++;
				}
			}
		}*/

		if(numberErrors==0){
			return true;
		} else {
			this.getField(invalidFields[0]).activate();
			this._hyperForm.getMessages().error(messages.join('<br/>'));
			return false;
		}
	},

	/**
	 * Prepara el formulario para su uso
	 *
   	 * @this {HyperGrid}
	 */
	_prepareForInput: function(){

		//Referencia el elemento hyGridTable
		this._activeSection = this._hyperForm.getActiveSection();
		this._element = this._activeSection.getElement('hyGridTable');

		//Agregar handler al botón de agregar
		var hyGridAdd = this._activeSection.getElement('hyGridAdd');
		hyGridAdd.observe('click', this.addRow.bind(this, false));

		this._activateFirstField();

		//Agregar eventos a campos
		this._fields.each(function(element){
			this.getField(element[0]).observe('keydown', function(event){
				if(event.keyCode==Event.KEY_RETURN){
					this.addRow(false);
					new Event.stop(event);
				}
			}.bind(this));
		}.bind(this));

		return true;
	},

	/**
	 * Agrega un evento a un determinado evento
	 *
   	 * @this {HyperGrid}
	 */
	observe: function(eventName, procedure){
		this['_'+eventName] = procedure;
	},

	/**
	 * Ejecuta un evento del formulario
	 *
   	 * @this {HyperGrid}
	 */
	fire: function(eventName){
		if(!Object.isUndefined(this['_'+eventName])){
			return this['_'+eventName](this);
		} else {
			return true;
		}
	},

	/**
	 * Obtiene un elemento DOM debajo de this._formElement
	 *
   	 * @this {HyperGrid}
	 */
	getElement: function(selector){
		return this._element.getElement(selector);
	},

	/**
	 * Obtiene un elemento DOM apartir de un selector
	 *
   	 * @this {HyperGrid}
	 */
	selectOne: function(selector){
		return this._activeSection.selectOne(selector);
	},

	/**
	 * Devuelve el elemento DOM de un campo del formulario
	 *
   	 * @this {HyperGrid}
	 */
	getField: function(fieldName){
		return this._element.selectOne('#'+fieldName);
	},

	/**
	 * Devuelve la suma de todos los elementos de la columna especificada
	 *
   	 * @this {HyperGrid}
	 */
	getSummatory: function(fieldName){
		var summatory = 0;
		var elements = this._element.select('input.'+fieldName);
		for(var i=0;i<elements.length;i++){
			if(elements[i].value!==''){
				summatory+=parseFloat(elements[i].value, 10);
			}
		};
		return summatory;
	},

	/**
	 * Devuelve un arreglo con los valores de las columnas que están activas
	 *
   	 * @this {HyperGrid}
	 */
	getColumn: function(fieldName){
		var fieldDefinition = this._fields.get(fieldName);
		var column = [];
		if(Object.isUndefined(fieldDefinition)){
			return column;
		};
		for(var i=0;i<this._rows.length;i++){
			if(this._rows[i].get('action')!='add'){
				continue;
			};
			column.push(this._rows[i].get(fieldName));
		};
		return column;
	},

	/**
	 * Devuelve un arreglo con los valores de las columnas que están activas
	 *
   	 * @this {HyperGrid}
	 */
	getRow: function(idRow){
		for(var i=0;i<this._rows.length;i++){
			if(this._rows[i].get('idGrid')==idRow){
				return this._rows[i];
			}
		};
		return false;
	},

	/**
	 * Devuelve un arreglo con los valores de las columnas que están activas
	 *
   	 * @this {HyperGrid}
	 */
	getEditRow: function(){
		if(this._editId>0){
			for(var i=0;i<this._rows.length;i++){
				if(this._rows[i].get('idGrid')==this._editId){
					return this._rows[i];
				}
			}
		};
		return false;
	},

	/**
	 * Obtiene el elemento DOM del formulario activo
	 *
   	 * @this {HyperGrid}
	 */
	getActiveForm: function(){
		var hyperForm = this._hyperGrid.getHyperForm();
		var activeSection = hyperForm.getActiveSection();
		return activeSection.getElement('hySaveFrom');
	},

	/**
	 * Obtiene el hyperForm asociado a la grilla
	 *
   	 * @this {HyperGrid}
	 */
	getHyperForm: function(){
		return this._hyperForm;
	},

	/**
	 * Elimina los datos de la grilla
	 *
   	 * @this {HyperGrid}
	 */
	clearData: function(){
		this._rows.clear();
	},

	/**
	 * Elimina los datos de la grilla
	 *
   	 * @this {HyperGrid}
	 */
	clear: function(){
		var hyGridTable = this._element.getElement('hyGridBodyTable');
		hyGridTable.select('TR').each(function(trElement){
			trElement.erase();
		});
		this._rows.clear();
	},

	/**
	 * Restaura el estado de la grilla después de una terminación inesperada
	 *
   	 * @this {HyperGrid}
	 */
	restoreState: function(){
		var storage = Hfos.getApplication().getStorage();
		if(storage!==null){
			storage.findFirst('HyperGrids', this._hyperForm.getName(), function(hyGrid){
				if(typeof hyGrid != "undefined"){
					if(typeof hyGrid.rows != "undefined"){
						var fields = this._fields.keys();
						for(var j=0;j<hyGrid.rows.length;j++){
							var row = hyGrid.rows[j];
							for(var i=0;i<fields.length;i++){
								var fieldName = fields[i];
								var field = this.getField(fieldName);
								if(field!==null){
									field.setValue(row._object[fieldName]);
								};
								fieldName = fieldName+'_det';
								var field = this.getField(fieldName);
								if(field!==null){
									field.setValue(row._object[fieldName]);
								};
							};
							this.addRow(true);
						};
					};
				};
				this.fire('afterRestore');
			}.bind(this));
		} else {
			this.fire('afterRestore');
		};
	}

});
