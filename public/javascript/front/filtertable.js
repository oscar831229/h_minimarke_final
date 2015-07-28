
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package		Front-Office
 * @copyright	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

var FilterTable = {

	timeout: null,
	lastSearch: "",
	searchField: null,
	searchRow: null,
	searchTable: null,

	afterSearch: function(){

	},

	filter: function(table, value){
		var rows = [];
		var text = "";
		var trParent;
		value = value.toLowerCase();
		if(FilterTable.lastSearch!=value){
			var tdElements = $$('#'+table+' td[class="sq"]');
			var element;
			for(var i=0;i<tdElements.length;i++){
				element = tdElements[i];
				trParent = element.parentNode;
				if(rows.include(trParent.id)==false){
					text = element.innerHTML.toLowerCase();
					if(text.include(value)){
						rows[rows.length] = trParent.id;
					}
				};
				$(trParent).hide();
			};
			for(var i=0;i<rows.length;i++){
				$(rows[i]).show();
			};
			FilterTable.afterSearch(rows);
			FilterTable.lastSearch = value;
		}
	},

	initialize: function(searchField, searchRow, table){
		FilterTable.searchField = searchField;
		FilterTable.searchRow = searchRow;
		FilterTable.searchTable = table;
		$(searchField).activate();
		$(searchField).observe('keyup', function(event){
			if(FilterTable.timeout!=null){
				window.clearTimeout(FilterTable.timeout);
				FilterTable.timeout = null;
			};
			FilterTable.timeout = window.setTimeout(function(){
				var searchField = $(FilterTable.searchField);
				if(searchField.value==""||searchField.value.length<2){
					$$('.'+FilterTable.searchRow).each(function(element){
						element.show();
					});
					FilterTable.afterSearch([]);
				} else {
					FilterTable.filter(FilterTable.searchTable, searchField.value);
				};
				FilterTable.timeout = null;
			}, 350);
		});
	}

};
