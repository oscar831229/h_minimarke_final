
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
 * HfosBrowseData
 *
 * Permite convertir una tabla o hash de datos en una tabla paginada ordenable
 */
var HfosBrowseData = Class.create({

	/**
	 * Contenedor donde se encuentra la tabla páginada
	 */
	_container: null,

	/**
	 * Identificador único del paginador
	 */
	_id: null,

	/**
	 * Almacena un entero a la pagina que se visualiza actualmente
	 *
	 * @type {number}
	 */
	_pagePointer: 1,

	/**
	 * Puntero al record seleccionado
	 *
	 * @type {number}
	 */
	_rowPointer: 0,

	/**
	 * Almacena los resultados de las consultas hechas en cada formulario
	 *
	 * @type {number}
	 */
	_numberResults: 0,

	/**
	 * Número de paginas que tiene el paginador
	 *
	 * @type {number}
	 */
	_numberPages: 0,

	/**
	 * Número de registros por página
	 *
	 * @type {number}
	 */
	_rowsPerPage: 0,

	/**
	 * Indica si se debe agregar el botón de editar a cada fila
	 *
	 * @type {boolean}
	 */
	_enableDetailsButton: true,

	/**
	 * Indica si se debe agregar el botón de eliminar a cada fila
	 *
	 * @type {boolean}
	 */
	_enableDeleteButton: true,

	/**
	 * Indica si el número de filas debe ser calculado de acuerdo al espacio disponible en la ventana
	 *
	 * @type {boolean}
	 */
	_autoRowsPerPage: false,

	/**
	 * Constructor de HfosBrowseData
	 *
	 * @constructor
	 */
	initialize: function(container, rowsPerPage){
		if(typeof container.notifyContentChange != "undefined"){
			this._container = container;
		} else {
			this._container = container.getContainer();
		};
		if(typeof this._container != "undefined"){
			this._id = this._container.getId();
		};
		if(typeof rowsPerPage == "undefined"){
			this._rowsPerPage = 19;
		} else {
			this._rowsPerPage = rowsPerPage;
		}
	},

	/**
	 * Establece si se debe agregar el botón de editar a cada fila
	 *
	 * @this {HfosBrowseData}
	 * @public
	 */
	setEnableDetailsButton: function(enableDetailsButton){
		this._enableDetailsButton = enableDetailsButton;
	},

	/**
	 * Establece si se debe agregar el botón de eliminar a cada fila
	 *
	 * @this {HfosBrowseData}
	 * @public
	 */
	setEnableDeleteButton: function(enableDeleteButton){
		this._enableDeleteButton = enableDeleteButton;
	},

	/**
	 * Establece si el número de filas debe ser calculado de acuerdo al espacio disponible en la ventana
	 *
	 * @this {HfosBrowseData}
	 * @public
	 */
	setAutoRowsPerPage: function(autoRowsPerPage){
		this._autoRowsPerPage = autoRowsPerPage;
	},

	/**
	 * Construye la tabla de visualizar en el elemento dado
	 *
	 * @this {HfosBrowseData}
	 * @public
	 */
	build: function(element, data){

		if(this._autoRowsPerPage==true){

			var html = '<table class="hyBrowseTab zebraSt sortable" cellspacing="0" width="95%" align="center"><tbody><tr>';
			for(var i=0;i<data.results.data.length;i++){
				var row = data.results.data[i];
				html+='<tr class="hySortRow">';
				for(var j=0;j<row.data.length;j++){
					html+='<td>'+row.data[j].value+'</td>';
				};
				html+='</tr>';
			};
			html+='</tbody></table>';
			element.update(html);

			var tdElement = element.selectOne('td');
			var activeWindow = Hfos.getApplication().getWorkspace().getWindowManager().getActiveWindow();
			var contentElement = activeWindow.getContentElement();
			var offset = element.positionedOffset();
			this._rowsPerPage = parseInt((contentElement.offsetHeight-offset[1])/(tdElement.getHeight()+3), 10)-3;
			if(this._rowsPerPage<5){
				this._rowsPerPage = 5;
			};
		};

		var classField = [];
		var align = [];
		var position = 0;
		var html = '<table class="hyBrowseTab zebraSt sortable" cellspacing="0" width="95%" align="center"><thead><tr>';
		for(var i=0;i<data.results.headers.length;i++){
			var header = data.results.headers[i];
			if(header.ordered=='S'){
				html+='<th class="sortcol sortasc">'+header.name+'</th>';
			} else {
				html+='<th class="sortcol">'+header.name+'</th>';
			};
			if(typeof header.type == "undefined"){
				align[position] = 'left';
			} else {
				if(header.type=='int'||header.type=='decimal'){
					align[position] = 'right';
				} else {
					align[position] = 'left';
				}
			};
			if(header.class){
				classField[position] = header.class;
			}
			position++;
		};
		html+='</th>';
		if(this._enableDetailsButton==true){
			html+='<th class="nosort">&nbsp;</th>';
		};
		if(this._enableDeleteButton==true){
			html+='<th class="nosort">&nbsp;</th>';
		}
		html+='</tr></thead><tbody>';

		var number = 0;
		for(var i=0;i<data.results.data.length;i++){
			var row = data.results.data[i];
			if(Object.isArray(row.primary)){
				row.primary = row.primary.join('&');
			};
			if(number<this._rowsPerPage){
				html+='<tr class="hySortRow" title="'+row.primary+'&n='+number+'">';
			} else {
				html+='<tr class="hySortRow" style="display:none" title="'+row.primary+'&n='+number+'">';
			};
			for(var j=0;j<row.data.length;j++){
				var classStr = '';
				if(classField[j]){
					classStr = 'class="'+classField[j]+'"';
				};
				html+='<td align="'+align[j]+'" '+classStr+'>'+row.data[j].value+'</td>';
			};
			if(this._enableDetailsButton==true){
				html+='<td class="hyDetailsTd"><div class="hyDetails" title="'+row.primary+'&n='+number+'"></div></td>';
			};
			if(this._enableDeleteButton==true){
				html+='<td class="hyDeleteTd"><div class="hyDelete" title="'+row.primary+'&n='+number+'"></div></td>';
			};
			html+='</tr>';
			number++;
		};

		//Calcular número de páginas
		var base = data.numberResults/this._rowsPerPage;
		var mod = data.numberResults % this._rowsPerPage;
		var numberPages = parseInt(base + (mod > 0?1:0), 10);


		//Si hay más de this._rowsPerPage registros se página el resultado
		if(number>this._rowsPerPage){

			//Si hay más de 20 páginas se saltan los números de página
			html+='</tbody><tbody>';
			for(var i=data.numberResults;i<=(numberPages*this._rowsPerPage);i++){
				html+='<tr class="hyDummyRow" style="display:none"><td colspan="'+(data.results.headers.length+2)+'">&nbsp;</td></tr>';
			};
			html+='<tr class="hyTrPager"><td align="right" colspan="'+(data.results.headers.length+2)+'">'+
			'<table class="hyPager" cellspacing="0"><tr><td><div class="hyPagePrev"></div></td>';

			var minPage, maxPage;
			var mediumMinPage, mediumMaxPage;
			if(numberPages>=20){
				minPage = 5;
				maxPage = numberPages-5;
				mediumMinPage = parseInt(numberPages*.5, 10);
				mediumMaxPage = parseInt(numberPages*.5, 10);
			} else {
				minPage = numberPages;
				maxPage = 1;
				mediumMinPage = numberPages
				mediumMaxPage = 1;
			};

			var formElementId = this._container.getId();
			for(var i=1;i<=(numberPages+2);i++){
				if(i==1){
					html+='<td><div class="hyPage hyPageSelected" id="'+formElementId+'hyPage'+i+'">'+i+'</div></td>';
				} else {
					if(i<minPage){
						html+='<td><div class="hyPage hyPageMin" id="'+formElementId+'hyPage'+i+'">'+i+'</div></td>';
					} else {
						if(i>maxPage){
							html+='<td><div class="hyPage hyPageMax" id="'+formElementId+'hyPage'+i+'">'+i+'</div></td>';
						} else {
							if(i>mediumMinPage&&i<mediumMaxPage){
								html+='<td><div class="hyPage hyPageMedium" id="'+formElementId+'hyPage'+i+'">'+i+'</div></td>';
							} else {
								html+='<td style="display:none"><div class="hyPage hyPageHidden" id="'+formElementId+'hyPage'+i+'">'+i+'</div></td>';
							}
						}
					}
				}
			};
			html+='<td><div class="hyPageNext"></div></td>';
			html+='</tr></table></td></tr>';

		};
		html+='</tbody></table>';
		element.update(html);

		if(number>this._rowsPerPage){
			this._addPageHandlers(this._container, numberPages);
		};

		this._numberPages = numberPages;
		this._numberResults = data.numberResults;
		this._pagePointer = 1;
		this._rowPointer = 0;

		this._addRowHandlers();

		//Hacer la tabla ordenable
		new HfosTableSort(this._container.getElement("hyBrowseTab"));

	},

	/**
	 * Agrega los handlers de paginado a la tabla
	 *
	 * @this {HfosBrowseData}
	 */
	_addPageHandlers: function(element, numberPages){

		//Agregar handlers a los botones de numeros de paginas
		var hyPages = element.select('div.hyPage');
		for(var i=0;i<hyPages.length;i++){
			hyPages[i].observe('click', function(element){
				this._showPage(parseInt(element.innerHTML, 10));
			}.bind(this, hyPages[i]));
		};

		//Agregar handler a el botón de atrás
		var hyPagePrev = element.getElement('hyPagePrev');
		if(hyPagePrev!==null){
			if(numberPages>2){
				hyPagePrev.observe('click', this.moveToPrevPage.bind(this));
			} else {
				hyPagePrev.hide();
			};
		};

		//Agregar handler a el botón de adelante
		var hyPageNext = element.getElement('hyPageNext');
		if(hyPageNext!==null){
			if(numberPages>2){
				hyPageNext.observe('click', this.moveToNextPage.bind(this));
			} else {
				hyPageNext.hide();
			};
		};

	},

	/**
	 * Agrega los handlers a cada fila del paginador
	 *
	 * @this {HfosBrowseData}
	 * @private
	 */
	_addRowHandlers: function(){
		this._markPageRows();
		for(var i=0;i<this._pageResultsRows.length;i++){
			this._pageResultsRows[i].observe('click', this._selectRow.bind(this, i));
		};
	},

	/**
	 * Quita la clase CSS a las filas seleccionadas
	 *
	 * @this {HfosBrowseData}
	 * @private
	 */
	_removeSelected: function(){
		var tRowsSelected = this._container.select('table.hyBrowseTab tr.selected');
		for(var i=0;i<tRowsSelected.length;i++){
			tRowsSelected[i].removeClassName('selected');
		};
	},

	/**
	 * Selecciona una fila del paginado
	 *
	 * @this {HfosBrowseData}
	 * @private
	 */
	_selectRow: function(number){
		this._removeSelected();
		this._pageResultsRows[number].addClassName('selected');
		this._rowPointer = number;
	},

	/**
	 * Cambia la vista actual a la página anterior
	 *
	 * @this {HfosBrowseData}
	 */
	moveToPrevPage: function(){
		if(this._pagePointer>1){
			this._pagePointer--;
			this._showPage(this._pagePointer);
		};
	},

	/**
	 * Cambia la vista actual a la página siguiente
	 *
	 * @this {HfosBrowseData}
	 */
	moveToNextPage: function(){
		if(this._pagePointer<parseInt(this._numberPages)){
			this._pagePointer++;
			this._showPage(this._pagePointer);
		};
	},

	/**
	 * Mueve el registro seleccionado al anterior arriba
	 *
	 * @this {HfosBrowseData}
	 */
	moveRecordUp: function(){
		if(this._rowPointer>1){
			this._rowPointer--;
			var residue = this._rowPointer%(this._rowsPerPage);
			if(residue==0){
				var page = parseInt(this._rowPointer/this._rowsPerPage, 10);
				this._showPage(page);
			};
			this._selectRow(this._rowPointer);
		};
	},

	/**
	 * Mueve el registro seleccionado al siguiente abajo
	 *
	 * @this {HfosBrowseData}
	 */
	moveRecordDown: function(){
		if(this._rowPointer<this._numberResults){
			this._rowPointer++;
			var residue = this._rowPointer%(this._rowsPerPage+1);
			if(residue==0){
				var page = parseInt(this._rowPointer/this._rowsPerPage, 10)+1;
				this._showPage(page);
			};
			this._selectRow(this._rowPointer);
		};
	},

	/**
	 * Construye una tabla paginada a partir de una tabla real
	 *
	 * @this {HfosBrowseData}
	 */
	fromHtmlTable: function(container, element, numberRows){

		this._container = container;
		var tBodyElement = element.selectOne('tbody');
		var trRows = tBodyElement.select('tr');
		if(numberRows<trRows.length){
			for(var i=0;i<trRows.length;i++){
				trRows[i].addClassName('hySortRow');
				if(numberRows<=i){
					trRows[i].hide();
				}
			};
			var numberColumns = element.select('th').length;
			var html = '';
			
			var base = trRows.length/numberRows;
			var mod = trRows.length % numberRows;
			var numberPages = parseInt(base + (mod > 0?1:0), 10);


			if(numberPages==0){
				numberPages = 1;
			};
			/*for(var i=numberRows;i<=numberPages*numberRows;i++){
				html+='<tr class="hyDummyRow" style="display:none"><td colspan="'+numberColumns+'">&nbsp;</td></tr>';
			};*/
			html = '<tbody><tr class="hyTrPager"><td align="right" colspan="'+numberColumns+'">'+
			'<table class="hyPager" cellspacing="0"><tr><td><div class="hyPagePrev"></div></td>';

			var minPage, maxPage;
			var mediumMaxPage, mediumMinPage;

			if(numberPages>=20){
				minPage = 5;
				maxPage = numberPages-5;
				mediumMinPage = parseInt(numberPages*.5, 10);
				mediumMaxPage = parseInt(numberPages*.5, 10);
			} else {
				minPage = numberPages;
				maxPage = 1;
				mediumMinPage = numberPages
				mediumMaxPage = 1;
			};

			var formElementId = element.id;
			for(var i=1;i<=numberPages;i++){
				if(i==1){
					html+='<td><div class="hyPage hyPageSelected" id="'+formElementId+'hyPage'+i+'">'+i+'</div></td>';
				} else {
					if(i<minPage){
						html+='<td><div class="hyPage hyPageMin" id="'+formElementId+'hyPage'+i+'">'+i+'</div></td>';
					} else {
						if(i>maxPage){
							html+='<td><div class="hyPage hyPageMax" id="'+formElementId+'hyPage'+i+'">'+i+'</div></td>';
						} else {
							if(i>mediumMinPage&&i<mediumMaxPage){
								html+='<td><div class="hyPage hyPageMedium" id="'+formElementId+'hyPage'+i+'">'+i+'</div></td>';
							} else {
								html+='<td style="display:none"><div class="hyPage hyPageHidden" id="'+formElementId+'hyPage'+i+'">'+i+'</div></td>';
							}
						}
					}
				}
			};
			html+='<td><div class="hyPageNext"></div></td>';
			html+='</tr></table></td></tr></tbody>';
			element.innerHTML+=html;
			this._addPageHandlers(element, numberPages);
		};

		this._id = element.id;
		this._rowsPerPage = numberRows;
	},

	/**
	 * Marca las filas que hacen parte del paginador
	 *
	 * @this {HfosBrowseData}
	 * @private
	 */
	_markPageRows: function(){
		if(this._pageResultsRows==null){
			this._pageResultsRows = this._container.select('table.hyBrowseTab tr');
		}
	},

	/**
	 * Cambia una página en el visualizar
	 *
	 * @this {HfosBrowseData}
	 * @private
	 */
	_showPage: function(number){
		try {
			this._markPageRows();
			var lastPageSelected = this._container.getElement('hyPageSelected');
			if(lastPageSelected){
				lastPageSelected.removeClassName('hyPageSelected');
				var pageSelected = $(this._id+'hyPage'+number);
				if(pageSelected){
					pageSelected.addClassName('hyPageSelected');
					if(pageSelected.hasClassName('hyPageHidden')){
						var minVisiblePage, maxVisiblePage;
						var lastPageNumber = lastPageSelected.id.replace(this._id+'hyPage', '');
						if(lastPageNumber<number){
							minVisiblePage = number;
							maxVisiblePage = number+4;
						} else {
							minVisiblePage = number-4;
							maxVisiblePage = number;
						};
						var hyPagesMedium = this._container.select('div.hyPageMedium');
						for(var i=0;i<hyPagesMedium.length;i++){
							hyPagesMedium[i].parentNode.hide();
						};
						var hyPagesHidden = this._container.select('div.hyPageHidden');
						for(var i=0;i<hyPagesHidden.length;i++){
							hyPagesHidden[i].parentNode.hide();
						};
						for(var i=minVisiblePage;i<=maxVisiblePage;i++){
							$(this._id+'hyPage'+i).parentNode.show();
						};
					};
					this._pagePointer = number;
					this._paginateResult();
					this._container.notifyContentChange();
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Pagina los resultados en páginas con registros this._rowsPerPage cada una
	 *
	 * @this {HfosBrowseData}
	 * @private
	 */
	_paginateResult: function(){
		var numberRows = 1;
		var visibleRows = 0;
		var firstRow = (this._rowsPerPage+1)*(this._pagePointer-1)+1;
		var length = this._pageResultsRows.length-1;
		for(var i=0;i<=length;i++){
			if(numberRows>1){
				if(numberRows>=firstRow&&visibleRows<this._rowsPerPage){
					this._pageResultsRows[i].show();
					visibleRows++;
				} else {
					if(length>numberRows){
						this._pageResultsRows[i].hide();
					}
				}
			};
			numberRows++;
		};
	},

	/**
	 * Elimina una fila
	 *
	 * @this {HfosBrowseData}
	 */
	deleteRow: function(element){

		this._markPageRows();

		//Elimina la fila y repagina el resultado
		element.erase();
		this._paginateResult();

		//Mostrando de nuevo la vista si quedan resultados si no retorna a buscar
		this._numberResults--;

		if(this._pagePointer<this._numberPages){
			this._showPage(this._pagePointer);
		} else {
			//Si al eliminar el registro solo queda una pagina se oculta el tr de paginar
			var pager = this._container.getElement('hyTrPager');
			if(pager!==null){
				pager.hide();
			}
		};

		this._removeSelected();
		this._rowPointer = 0;
	},

	/**
	 * Cachea el último resultado de doSearch para ser paginado
	 *
	 * @this {HfosBrowseData}
	 */
	setNumberResults: function(numberResults){
		this._numberResults = numberResults;
	},

	/**
	 * Obtiene el número de resultados de un formulario
	 *
	 * @this {HfosBrowseData}
	 */
	getNumberResults: function(){
		return parseInt(this._numberResults, 10);
	},

	/**
	 * Entero a la página actualmente visualizada
	 *
	 * @this {HfosBrowseData}
	 */
	setPagePointer: function(pagePointer){
		this._pagePointer = pagePointer;
	},

	/**
	 * Obtiene la pagina actual en el paginador
	 *
	 * @this {HfosBrowseData}
	 */
	getPagePointer: function(){
		return this._pagePointer;
	},

	/**
	 * Obtiene el indice del registro seleccionado actualmente
	 *
	 * @this {HfosBrowseData}
	 */
	getRowPointer: function(){
		return this._rowPointer;
	},

	/**
	 * Obtiene la fila que está seleccionada en el paginador
	 *
	 * @this {HfosBrowseData}
	 * @public
	 */
	getSelectedRow: function(){
		return this._container.selectOne('table.hyBrowseTab tr.selected');
	},

	/**
	 * Devuelve los elementos DOM de cada fila del browseData
	 *
	 * @this {HfosBrowseData}
	 * @public
	 */
	getRows: function(){
		return this._container.select('tr.hySortRow');
	},

	/**
	 * Devuelve los elementos DOM de los botones de editar ó detallles
	 *
	 * @this {HfosBrowseData}
	 * @public
	 */
	getDetailsButtons: function(){
		return this._container.select('div.hyDetails');
	},

	/**
	 * Devuelve los elementos DOM de los botones de eliminar
	 *
	 * @this {HfosBrowseData}
	 * @public
	 */
	getDeleteButtons: function(){
		return this._container.select('div.hyDelete');
	},

	/**
	 * Restaura una tabla de visualización
	 *
	 * @this {HfosBrowseData}
	 * @public
	 */
	restore: function(element){
		this._element = element;
		var hyBrowseTable = this._container.selectOne('table.hyBrowseTab');
		if(hyBrowseTable!==null){
			var rows = hyBrowseTable.select('tr.hySortRow');
			this._numberResults = rows.length;
			var numberVisible = 0;
			var firstVisible = -1;
			for(var i=0;i<rows.length;i++){
				if(rows[i].visible()){
					numberVisible++;
					if(firstVisible==-1){
						firstVisible = i;
					}
				}
			};
			var numberPages = 1;
			if(numberVisible>0){
				for(var i=numberVisible;i<rows.length;i+=numberVisible){
					if(firstVisible<i){
						this._pagePointer = numberPages;
						break;
					} else {
						numberPages++;
					}
				}
			} else {
				this._pagePointer = 1;
			};
			this._rowsPerPage = numberVisible;
			this._addPageHandlers(element, parseInt(rows.length/numberVisible, 10));
		};
	},

	/**
	 * Resetea el paginador de datos
	 *
	 * @this {HfosBrowseData}
	 * @public
	 */
	reset: function(){
		delete this._pageResultsRows;
		this._pageResultsRows = null;
		this._numberResults = 0;
		this._pagePointer = 1;
		this._rowsPerPage = 0;
	}

});
