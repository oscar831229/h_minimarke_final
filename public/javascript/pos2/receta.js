
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

var CKEDITOR_BASEPATH = $Kumbia.path+'/javascript/ckeditor/';

var Receta = {

	detail: [],
	id: 0,
	editId: -1,
	precio_costo: 0,
	lastEntry: '',
	items: [],
	recetas: [],
	format: {},
	validator: {},
	validatorMaster: {},
	divDetailName: 'divDetail',
	dataDetailName: 'dataDetail',
	tableDetailName: 'tableDetail',
	register: {
		'tipol'		: {'type': 'domain','caption': 'Tipo','domain': {'I': 'REFERENCIA','R': 'RECETA'}},
		'item'		: {'type': 'domain','caption': 'Item','domain': {'@': 'Seleccione el tipo...'},'use_dummy': false,'width': '59%','fieldWidth': '100%'},
		'unidad'	: {'type': 'string','caption': 'Unidad','size': 7,'ignore': true},
		'divisor'	: {'type': 'int','caption': 'Divisor','size': 4,'format': 'numeric'},
		'cantidad'	: {'type': 'decimal','caption': 'Cantidad','size': 6,'format': 'numeric'},
		'valor'		: {'type': 'decimal','caption': 'Valor','size': 10,'format': 'numeric'}
	},

	setPostDataDetail: function(){
		var html = '';
		for(var i=0;i<Receta.detail.length;i++) {
			var elem = Receta.detail[i];
			for(key in elem) {
				html+= '<input type="text" id="' + key + '[]" name="' + key + '[]" value="' + elem[key] + '" />';
			}
		}
		$('precio_costo').value = Receta.format.execute('deFormat',$F('precio_costo'),'numeric');
		$(Receta.dataDetailName).innerHTML = html;
	},

	calculePrecioVenta: function(porc_costo){
		Receta.lastEntry = 'porc_costo';
		if(porc_costo == 0 || porc_costo == ''){
			return 0;
		}
		var format = new Format({'type': 'numeric', 'properties': {'decimals': 2, 'puntoDec': '.', 'sepMiles': ''}});
		var precio_venta = Receta.precio_costo / porc_costo;
		return format.numeric(precio_venta * 100);
	},

	calculePorcCosto: function(precio_venta){
		Receta.lastEntry = 'precio_venta';
		if(precio_venta == 0 || precio_venta == ''){
			return 0;
		};
		var porc_costo = Receta.precio_costo / precio_venta;
		return (porc_costo * 100).toFixed(2);
	},

	calculeCosto: function () {
		var format = new Format({'type': 'numeric', 'properties': {'decimals': 2, 'puntoDec': '.', 'sepMiles': ''}});
		var costo = 0;
		for(var i=0;i<Receta.detail.length;i++) {
			var elem = Receta.detail[i];
			if(elem['action'] != 'add') {
				continue;
			}
			costo += parseFloat(elem['valor']);
		}
		if($F('porc_varios').blank()){
			$('porc_varios').value = format.numeric(0);
		};
		var por_var = parseFloat($F('porc_varios'));
		costo = format.numeric((1 + (por_var / 100)) * costo);
		Receta.precio_costo = costo;
		$('precio_costo').value = Receta.format.numeric(costo);
		if(Receta.lastEntry == 'porc_costo') {
			$('precio_venta').value = Receta.calculePrecioVenta($F('porc_costo'));
		} else if(Receta.lastEntry == 'precio_venta') {
			$('porc_costo').value = Receta.calculePorcCosto($F('precio_venta'));
		}
		var por_cos = Receta.calculePorcCosto($F('precio_venta'));
		var venta = Receta.calculePrecioVenta(Receta.calculePorcCosto($F('precio_venta')),true);
		$('p_costo').update(format.numeric(por_cos));
		$('p_venta').update(format.numeric(venta));
		$('p_utilidad').update(format.numeric(100 - por_cos));
		$('pesos_utilidad').update(format.numeric(venta - costo));
	},

	createDetail: function () {
		var div = $(Receta.divDetailName);
		var html = "<table class='lista_res' id='" + Receta.tableDetailName + "' cellspacing='0' " +
		"align='center'><thead><tr>";
		var fields = '<th>&nbsp;</th></tr><tr>';
		for(key in Receta.register) {
			var item = Receta.register[key];
			var width = item['width'] != undefined ? " width='" + item['width'] + "'" : "";
			html += "<th" + width + ">" + item['caption'] + "</th>";
			fields += "<td>" + Receta.getFieldHTML(item,key) + "</td>";
		}
		html += fields + "<td><img src='" + $Kumbia.path + "img/pos2/apply.png' id='applyImg' width='21px' " +
		"height='21px' /></td></tr></thead><tbody></tbody></table>";
		div.innerHTML = html;
		$('tipol').observe('change', function(){
			Receta.getItems(this.value);
		});
		$('item').observe('change', function(){
			Receta.putDefaults();
		});
		$('divisor').observe('change', function(){
			Receta.calculeValor($F('cantidad'),this.value);
		});
		$('cantidad').observe('change', function(){
			Receta.calculeValor(this.value,$F('divisor'));
		});
		$('valor').setAttribute('readonly',true);
		$('valor').observe('keydown', function(evt){
			evt = (evt) ? evt : ((window.event) ? window.event : null);
			var key = (document.all) ? event.keyCode : evt.keyCode;
			if(key==Event.KEY_RETURN || key==Event.KEY_TAB) {
				Receta.addDetail();
				if(document.all) {
					evt.returnValue = false
				} else evt.preventDefault()
			}
		});
		$('applyImg').observe('click', function(evt){
			Receta.addDetail();
		});
		Receta.addValidator();
	},

	addValidator: function () {
		for(key in Receta.register) {
			var item = Receta.register[key];
			if(item['ignore']==true){
				continue;
			}
			var type;
			switch(item['type']){
				case 'int': type = 'number'; break;
				case 'domain': type = 'select'; break;
				default: type = item['type']; break;
			};
			Receta.validator.addField(key,type,null,{'alias': item['caption']});
		}
	},

	addValidatorMaster: function () {
		Receta.validatorMaster.addField('nombre','text',null);
		Receta.validatorMaster.addField('num_personas','number',null,{'alias': 'N&uacute;mero de Personas'});
	},

	getItems: function (tipo) {
		if((tipo == 'I' && Receta.items.length == 0) || (tipo == 'R' && Receta.recetas.length == 0)) {
			Utility.getFromAjax(['tipol'],'getReferencias','Receta.loadItems','receta');
		} else if (tipo == '@') {
			$('item').innerHTML = '<option value="@">Seleccione el tipo...</option>';
		} else {
			var items = tipo == 'I' ? Receta.items : Receta.recetas;
			Receta.loadItems(items);
		}
	},

	loadItems: function (items) {
		if($F('tipol') == 'I' && Receta.items.length == 0) {
			Receta.items = items;
		} else if($F('tipol') == 'R' && Receta.recetas.length == 0) {
			Receta.recetas = items;
		};
		if(items['type'] == 'success') {
			var html = '<option value="@">Seleccione...</option>';
			var elems = items['data'];
			for(var i=0;i<elems.length;i++) {
				var elem = elems[i];
				html+= "<option value='" + elem['id'] + "'>" + elem['detalle'] + "</option>";
			}
			$('item').innerHTML = html;
		} else {
			Receta.showMessage(items['msg'],items['type']);
		}
	},

	getFieldHTML: function(item, id){
		var html = '';
		if(item['type'] == 'domain'){
			var width = item['fieldWidth'] != undefined ? " style='width:" + item['fieldWidth'] + "'" : "";
			html = "<select id = '" + id + "'" + width + ">";
			if(item['use_dummy']!==false) {
				html+= "<option value='@'>Seleccione...</option>";
			}
			for(key in item['domain']){
				html+= "<option value='" + key + "'>" + item['domain'][key] + "</option>"
			}
			html+= "</select>";
		} else {
			if(item['type'] == 'int' || item['type'] == 'decimal'){
				html = "<input type='text' id='" + id + "' size='" + item['size'] + "' onkeydown='valNumeric(event)' />";
			} else {
				if(item['type'] == 'string'){
					html = "<input type='text' id='" + id + "' size='" + item['size'] + "' />";
				}
			}
		}
		return html;
	},

	addDetail: function(){
		if(!Receta.validator.valide()){
			errors = Receta.validator.getErrorMessages();
			var field = '';
			var msg = '';
			for(var i=0;i<errors.length;i++){
				if(field == ''){
					field = errors[i].name;
				}
				msg += errors[i].msg + "<br />";
			};
			Receta.showMessage(msg,'error');
			$(field).focus();
			return;
		};
		var table = $$('#' + Receta.tableDetailName + ' tbody')[0];
		if(Receta.editId > 0){
			var html = '<tr id="tr' + Receta.editId + '">';
		} else {
			var html = '<tr id="tr' + Receta.id + '">';
		};
		var register = {}
		for(key in Receta.register) {
			var item = Receta.register[key];
			var align = item['type'].match(/int|decimal/) ? 'right' : 'left';
			html += "<td align='" + align + "'>" + Receta.getTextValue(key) + "</td>";
			if(item['ignore']!=true){
				register[key] = $F(key);
			}
			$(key).value = item['type'] == 'domain' ? '@' : '';
		}
		register['action'] = 'add';
		if(Receta.editId > 0) {
			for(var i=0;i<Receta.detail.length;i++) {
				if(Receta.detail[i]['id'] == Receta.editId) {
					register['id'] = Receta.editId;
					Object.extend(Receta.detail[i],register);
					Receta.editId = -1;
					break;
				}
			}
		}else {
			register['id'] = Receta.id++;
			Receta.detail.push(register);
		}
		html += '<td><img src="' + $Kumbia.path + 'img/delete.gif" id="img' + register['id'] + '" /></td></tr>';
		table.innerHTML += html;
		for(var i=0;i<Receta.id;i++) {
			if($('img' + i) == undefined) {
				continue;
			}
			$('img' + i).observe('click',function () {
				Receta.removeItem(this.id.replace('img',''));
			});
			if($('tr' + i) == undefined) {
				continue;
			}
			$('tr' + i).observe('dblclick',function () {
				Receta.editItem(this.id.replace('tr',''));
			});
		}
		Receta.getItems('@');
		Receta.calculeCosto();
		$('tipol').focus();
	},

	getTextValue: function (field) {
		if(Receta.register[field]['type']=='domain') {
			return $(field).options[$(field).selectedIndex].text;
		} else {
			var format = Receta.register[field]['format'];
			if(format != undefined) {
				return Receta.format.execute(format,$F(field));
			}
			return $F(field);
		}
	},

	putDefaults: function () {
		var items;
		$('divisor').value = '1';
		$('cantidad').value = '1';
		if($F('tipol') == 'R') {
			items = Receta.recetas['data'];
		} else {
			items = Receta.items['data'];
		}
		for(var i=0;i<items.length;i++) {
			if(items[i]['id'] == $F('item')) {
				elem = items[i];
				break;
			}
		}
		$('divisor').value = elem['divisor'];
		$('valor').value = elem['costo'];
		$('unidad').value = elem['unidad'];
		if($F('valor') == 0) {
			Utility.getFromAjax(['item','tipol'], 'getCosto', 'Receta.setCosto','receta');
		}
		$('divisor').focus();
	},

	setCosto: function (response) {
		if(response['type'] == 'success') {
			if($F('tipol') == 'R') {
				items = Receta.recetas['data'];
			} else {
				items = Receta.items['data'];
			}
			for(var i=0;i<items.length;i++) {
				if(items[i]['id'] == $F('item')) {
					elem = items[i];
					break;
				}
			}
			elem['costo'] = response['data']['costo'];
			$('valor').value = elem['costo'];
		} else {
			Receta.showMessage(response['msg'],response['type']);
			setTimeout('$("growler").hide()',3000);
		}
	},

	loadRecetal: function (response) {
		if(response['type'] == 'success') {
			var items = response['data'];
			var table = $$('#' + Receta.tableDetailName + ' tbody')[0];
			var html = '';
			for(var i=0;i<items.length;i++) {
				var register = {}
				html+= '<tr id="tr' + i + '">';
				for(key in Receta.register) {
					var item = Receta.register[key];
					var align = item['type'].match(/int|decimal/) ? 'right' : 'left';
					html += "<td align='" + align + "'>" + items[i][key]['detail'] + "</td>";
					if(item['ignore']==true){
						continue;
					}
					register[key] = items[i][key]['value'];
				}
				register['action'] = 'add';
				register['id'] = i;
				html += '<td><img src="' + $Kumbia.path + 'img/delete.gif" id="img' + i + '" /></td></tr>';
				Receta.detail.push(register);
				Receta.id++;
			}
			table.innerHTML += html;
			for(var i=0;i<items.length;i++) {
				$('img' + i).observe('click',function () {
					Receta.removeItem(this.id.replace('img',''));
				});
			}
			//$('growler').hide();
			Receta.calculeCosto();
		} else {
			Receta.showMessage(response['msg'],response['type']);
		}
	},

	removeItem: function (numItem) {
		$('tr' + numItem).remove();
		for(var i=0;i<Receta.detail.length;i++) {
			if(Receta.detail[i]['id'] == numItem) {
				Receta.detail[i]['action'] = 'del';
				break;
			}
		}
		Receta.calculeCosto();
		$('tipol').activate();
	},

	editItem: function (numItem) {
		$('tr' + numItem).remove();
		for(var i=0;i<Receta.detail.length;i++) {
			if(Receta.detail[i]['id'] == numItem) {
				Receta.detail[i]['action'] = 'edt';
				break;
			}
		}
		Receta.getItems(Receta.detail[i]['tipol']);
		for(key in Receta.register){
			$(key).value = Receta.detail[i][key];
		}
		Receta.showMessage('Editando Registro','INFO');
		Receta.editId = numItem;
		Receta.calculeCosto();
		$('tipol').activate();
	},

	calculeValor: function(cantidad, divisor){
		if($F('tipol') == '@' || $F('item') == '@') {
			return;
		};
		var items = [];
		if($F('tipol') == 'R'){
			items = Receta.recetas['data'];
		} else {
			items = Receta.items['data'];
		};
		for(var i=0;i<items.length;i++){
			if(items[i]['id'] == $F('item')){
				var elem = items[i];
				break;
			}
		};
		$('valor').value = parseFloat(elem['costo']) * parseFloat(cantidad) / parseFloat(divisor);
	},

	/**
	 * Cuadro
	 */
	loadSalonValores: function(response){
		if(response['type'] == 'success') {
			var datos = response['data'];
			Receta.precio_costo = $F('precio_costo');
			if(Object.isArray(datos)){
				return;
			};
			for(var i in datos){
				var tr = document.createElement("TR");
				tr.addClassName('tr_' + i);
				var costo = $F('precio_costo');
				var venta = datos[i];
				var por_cos = Receta.calculePorcCosto(venta);
				var td = document.createElement("TD");
				td.update(i);
				tr.appendChild(td);
				td = document.createElement("TD");
				td.setAttribute('align', 'right');
				td.update(por_cos);
				tr.appendChild(td);
				td = document.createElement("TD");
				td.setAttribute('align', 'right');
				td.update(venta);
				tr.appendChild(td);
				td = document.createElement("TD");
				td.setAttribute('align', 'right');
				td.update(100 - por_cos);
				tr.appendChild(td);
				td = document.createElement("TD");
				td.setAttribute('align', 'right');
				td.update((venta - costo).toFixed(2));
				tr.appendChild(td);
				$('container_salon').appendChild(tr);
			}
		} else {
			Receta.showMessage(response['msg'], response['type']);
		}
	},

	showMessage: function(message, type){
		try {
			var pageSize = WindowUtilities.getPageSize(document.body);
			var d = $('growler');
			if(!d){
				var d = document.createElement("DIV");
				d.id = "growler";
				var width = '350px';
				d.setStyle({
					position: 'fixed',
					width: width,
				});
				document.body.appendChild(d);
			};
			d.innerHTML = "<b>" + type.toUpperCase() + ":&nbsp;</b>" + message;
			var height = d.getHeight() + 10;
			d.setStyle({
				top: (pageSize.windowHeight-height)+"px",
			});
			d.show();
			window.setTimeout(function(){
				this.hide();
			}.bind(d), 2000);
		}
		catch(e){
			alert(e)
		}
	},

	showTab: function (p, obj) {
		for(var i=1;i<4;i++){
			$('tab_'+i).hide();
			$('tabdiv_'+i).removeClassName('tab_active');
			$('tabdiv_'+i).addClassName('tab_inactive');
		};
		$('tab_'+p).show();
		$('tabdiv_'+p).removeClassName('tab_inactive');
		$('tabdiv_'+p).addClassName('tab_active');
		var field = $$('#tab_'+p+' .field')[0];
		if(field != undefined) {
			field.focus();
		}
	},

	changeTab: function(id){
		if(id=="co"){
			$('label_calculo').update('% Costo');
			$('porc_costo_lab').show();
			$('porc_costo').show();
			$('precio_venta_lab').hide();
			$('precio_venta').hide();
			$(id).addClassName('active');
			$('pr').removeClassName('active');
		} else {
			$('label_calculo').update('Precio de Venta');
			$('porc_costo_lab').hide();
			$('porc_costo').hide();
			$('precio_venta_lab').show();
			$('precio_venta').show();
			$(id).addClassName('active');
			$('co').removeClassName('active');
		}
	},

	changeSearch: function(){
		if($('normalSearch').visible()==false){
			$('itemSearch').hide();
			$('normalSearch').show();
			$('item').value = '@';
			$('num_receta').focus();
		} else {
			$('normalSearch').hide();
			$('normalSearch').select('input')[0].value = '';
			$('itemSearch').show();
			$('item').focus();
		}
	},

	initializeIndex: function (){
		$('num_receta').focus();
		$('tabdiv_1').observe('click', function(){
			if(this.hasClassName('tab_inactive')){
				this.addClassName('tab_active');
				this.removeClassName('tab_inactive');
				$('tabdiv_2').addClassName('tab_inactive');
				$('tabdiv_2').removeClassName('tab_active');
				Receta.changeSearch();
			}
		});
		$('tabdiv_2').observe('click', function(){
			if(this.hasClassName('tab_inactive')){
				this.addClassName('tab_active');
				this.removeClassName('tab_inactive');
				$('tabdiv_1').addClassName('tab_inactive');
				$('tabdiv_1').removeClassName('tab_active');
				Receta.changeSearch();
			}
		});
	},

	initializeEdit: function () {
		Receta.initialize();
		//Receta.showMessage('Cargando componentes de la Receta','info');
		Utility.getFromAjax(['numero_rec'], 'getRecetal', 'Receta.loadRecetal', 'receta');
		Utility.getFromAjax(['numero_rec'], 'getSalonValores', 'Receta.loadSalonValores', 'receta');
	},

	initialize: function() {
		Receta.format = new Format();
		Receta.validator = new Validator();
		Receta.validatorMaster = new Validator();
		$('nombre').focus();
		$$('.tab_td_rec').each(function(elem){
			elem.observe('click',function (){
				Receta.changeTab(this.id);
			});
		});
		$('porc_costo').observe('change',function() {
			$('precio_venta').value = Receta.calculePrecioVenta(this.value);
			Receta.calculeCosto();
		});
		$('precio_venta').observe('change',function() {
			$('porc_costo').value = Receta.calculePorcCosto(this.value);
			Receta.calculeCosto();
		});
		$('recetaForm').observe('submit',function(evt) {
			if(!Receta.validatorMaster.valide()) {
				errors = Receta.validator.getErrorMessages();
				var field = '';
				var msg = '';
				for(var i=0;i<errors.length;i++){
					if(field == ''){
						field = errors[i].name;
					}
					msg += errors[i].msg + "<br />";
				}
				Receta.showMessage(msg,'error');
				$(field).focus();
				evt.stop();
			}
			Receta.setPostDataDetail();
		});
		$('tabdiv_1').observe('click',function(){
			Receta.showTab(1, this);
		});
		$('tabdiv_2').observe('click',function(){
			Receta.showTab(2, this);
		});
		$('tabdiv_3').observe('click',function(){
			Receta.showTab(3, this);
		});
		Receta.createDetail();
		Receta.addValidatorMaster();
		CKEDITOR.replace('preparacion', {
			language: 'es',
        	toolbar: [
        		['Styles', 'Format'],
				['Bold', 'Italic', 'Strike'],
				['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
				['Link', 'Unlink', 'Anchor'],
				'/',
				['Cut', 'Copy', 'Paste','PasteText','PasteFromWord','-','Scayt'],
				['Undo', 'Redo', '-', 'Find','Replace','-','SelectAll','RemoveFormat'],
				['Image', 'Table', 'HorizontalRule','SpecialChar','PageBreak']
			]
		});
	},

	initializeFindComponent: function(){
		$('allIn').observe('click',function(){
			$$('.checks').each(function(element){
				if(this.checked == true){
					element.checked = true;
				} else {
					element.checked = false;
				}
			}.bind($('allIn')));
		});
		$('expand').observe('click',function(){
			$('form').request({
				onSuccess: function(transport){
					var data = transport.responseText.evalJSON();
					try{
						$('allIn').checked = false;
						for(item in data){
							var tr = $('tr'+item);
							tr.select('input.checks')[0].checked = false;
							if(data[item].length == 0) continue;
							for(var i=0;i<data[item].length;i++){
								var element = $('tr'+data[item][i].numero_rec);
								if(Object.isElement(element)) continue;
								element = tr.cloneNode(true);
								element.id = 'tr' + data[item][i].numero_rec;
								element.select('input.checks')[0].value = data[item][i].numero_rec;
								element.select('td')[1].update(data[item][i].almacen);
								element.select('td')[2].update(data[item][i].numero_rec);
								element.select('td')[3].update(data[item][i].nombre);
								element.select('td')[4].update(data[item][i].num_personas);
								element.select('td')[5].update(data[item][i].precio_costo);
								element.select('button.controlButton')[0].setAttribute('onclick','window.location=' +
									'Utils.getKumbiaURL(\"receta/editar/' + data[item][i].almacen +'/' +
									data[item][i].numero_rec + '\")');
								Element.insert(tr, {'after': element});
							}
						}
					}
					catch(e){
						console.log(e);
						console.log(element);
					}
				}
			});
		});
	}

}
