// Recorrer objetos 
function $each(objeto,callback){

	if(whatIsIt(objeto)=='Array'){
		for(i = 0;i<objeto.length; i++){
			callback(i,objeto[i]);
		}
	}else if(whatIsIt(objeto)=='Object'){
	     
		// Obteniendo todas las claves del JSON
		for (var clave in objeto){
		  // Controlando que json realmente tenga esa propiedad
		  if (objeto.hasOwnProperty(clave)) {
		    callback(clave,objeto[clave])
		  }

		}

	}else{
		for(i = 0;i<objeto.length; i++){
			callback(i,objeto[i]);
		}
	}
}


// Selector id
function $c(selector){
	
	var typeSelector = selector.substr(0,1)
	var selector = selector.substr(1)

	if(typeSelector == '.')
		return document.getElementsByClassName(selector);

	if(typeSelector == '#')
		return document.getElementById(selector);
	
	
}

// selector individual
function $s(selector){
	return document.querySelector(selector);
}

// Selector all
function $sa(selector){
	return document.querySelectorAll(selector);
}


function normal_tr(element){

	var tr = element.parentNode.parentNode;
	if(element.hasClassName('calendar_date')){
		tr = tr.parentNode.parentNode.parentNode.parentNode;
		tr = tr.parentNode.parentNode.parentNode.parentNode;
	};
	tr.style.background = 'transparent';
	var childs = tr.childElements();
	childs[0].setStyle({
		borderLeft: 'none',
		borderTop: 'none',
		borderBottom: 'none',
		fontWeight: 'normal'
	});

	if(childs[1] != undefined)
		childs[1].setStyle({
			borderRight: 'none',
			borderTop: 'none',
			borderBottom: 'none',
		});
};



function showErrores(errores, all_fields, index_all = ''){

	if(index_all == ''){
		var fiels = Object.keys(all_fields)
	}else{
		var fiels = Object.keys(all_fields[index_all])
	}

	for(var j=0;j<=fiels.length;j++){
		if($(fiels[j])){
			normal_tr($(fiels[j]))
		}
	};

    if(errores.length == 0)
    	return false;		

	var element = $(errores[0]);
	if(element.hasClassName("calendar_date")){
		element = element.parentNode;
	};
	new Effect.ScrollTo(element, {
		duration: 0.5,
		afterFinish: function(errores){
			var windowScroll = WindowUtilities.getWindowScroll(document.body);
    		var pageSize = WindowUtilities.getPageSize(document.body);
    		if(!$("error_list_div")){
				var d = document.createElement("DIV");
				d.id = "error_list_div";
    		} else {
    			d = $("error_list_div");
    		};
			new Effect.Highlight(errores[0], { startcolor: "#ff0000"});
			$(errores[0]).activate();
			d.hide();
			d.innerHTML = "<strong>Se requieren lo siguiente:</strong><br>";
			for(var i=0;i<errores.length;i++){
				var element = $(errores[i]);
				d.innerHTML+= "El campo \""+ nombreCampo(errores[i],index_all)+"\" no puede estar vacio<br>";
				if(i>5){
					d.innerHTML+= "<div align='right' style='color:white'>&nbsp;"+(errores.length-i)+" errores m&aacute;s...</div>";
					break;
				}
			};
			for(var i=0;i<errores.length;i++){
				var element = $(errores[i]);
				highlight_tr(element);
			};
			d.className = "error_list";
			d.style.top = (pageSize.windowHeight-170+windowScroll.top)+"px";
			d.show();
			document.body.appendChild(d);
			d.style.top = (pageSize.windowHeight-d.getHeight()+windowScroll.top)+"px";
			if(showErrorTimeout){
				window.clearTimeout(showErrorTimeout)
			};
			showErrorTimeout = window.setTimeout(function(){
				new Effect.Fade($("error_list_div"), { duration: 0.5 })
			}, 7000);
		}.bind(this, errores)
	});
};



function highlight_tr(element) {
	var tr = element.parentNode.parentNode;
	if (element.hasClassName('calendar_date')) {
		tr = tr.parentNode.parentNode.parentNode.parentNode;
		tr = tr.parentNode.parentNode.parentNode.parentNode;
	};
	tr.style.background = '#D4E0F1';
	var childs = tr.childElements();
	childs[0].setStyle({
		borderLeft: '1px solid #D4E0F1',
		borderTop: '1px solid #D4E0F1',
		borderBottom: '1px solid #D4E0F1',
		fontWeight: 'bold'
	});
	if(childs[1] != undefined)
		childs[1].setStyle({
			borderRight: '1px solid #D4E0F1',
			borderTop: '1px solid #D4E0F1',
			borderBottom: '1px solid #D4E0F1',
		});
};

function nombreCampo(index,index_all){
	
	if(index_all == '')
		return all_fields[index]
	else{
		return all_fields[index_all][index]
	}
}



function valNumeric(evt){
	evt = (evt) ? evt : ((window.event) ? window.event : null);
	var kc = evt.keyCode;
	var ev = (evt.altKey==false)&&(evt.shiftKey==false)&&((kc>=48&&kc<=57)||(kc>=96&&kc<=105)||(kc==8)||(kc==9)||(kc==13)||(kc==17)||(kc==36)||(kc==35)||(kc==37)||(kc==46)||(kc==39)||(kc==190));
	if(!ev){
		new Event.stop(evt);
	}
};

function valAlphaNum(evt){
	evt = (evt) ? evt : ((window.event) ? window.event : null);
	var kc = evt.keyCode;
	if(evt.shiftKey==true&&kc==53){
		return;
	}
	var ev = (evt.altKey==false)&&(evt.shiftKey==false)&&((kc>=65&&kc<=90)||(kc>=48&&kc<=57)||(kc>=96&&kc<=105)||(kc==8)||(kc==9)||(kc==13)||(kc==17)||(kc==36)||(kc==35)||(kc==37)||(kc==39)||(kc==46));
	if(!ev){
		new Event.stop(evt);
	}
};


function saveMasterData(action, index_all = ''){

	var obj;
	if(emptydata.length>0){
		if(!confirm("El Formulario tiene errores\nDesea Continuar?")) {
			return;
		}
	};

	if(index_all == '')
		var Fields = Object.keys(all_fields);
	else 
		var Fields = Object.keys(all_fields[index_all]);

	for(var i=0;i<Fields.length;i++){
		if($(Fields[i])){
			obj = document.createElement("INPUT");
			obj.type = "hidden";
			obj.name = Fields[i];
			if($(Fields[i]).type=='checkbox'){
				obj.value = $(Fields[i]).checked;
			} else {
				obj.value = $(Fields[i]).value;
			};
			document.saveDataForm.appendChild(obj);
		}
	};
    
    //Action
	obj = document.createElement("INPUT");
	obj.type = "hidden";
	obj.name = "subaction";
	obj.value = action;

	document.saveDataForm.appendChild(obj);

	obj = document.createElement("INPUT");
	obj.type = "hidden";
	obj.name = "form";
	obj.value = index_all;

	document.saveDataForm.appendChild(obj);

	document.saveDataForm.submit();

};


function whatIsIt(object) {


	var stringConstructor = "test".constructor;
	var arrayConstructor = [].constructor;
	var objectConstructor = {}.constructor;

    if (object === null) {
        return "null";
    }
    else if (object === undefined) {
        return "undefined";
    }
    else if (object.constructor === stringConstructor) {
        return "String";
    }
    else if (object.constructor === arrayConstructor) {
        return "Array";
    }
    else if (object.constructor === objectConstructor) {
        return "Object";
    }
    else {
        return "don't know";
    }
}


function validarDatosObligatorios(salector_name = 'body'){

	emptydata = []

	// Se recorren los campos que estan con la class required
	$each(document.querySelectorAll(salector_name + " .required"),function(index,element){

		if(element.value == "" || element.value == '@'){
			emptydata[emptydata.length] = element.id
		}
	})

	if(salector_name == 'body')
    	showErrores(emptydata,all_fields);
    else
    	showErrores(emptydata,all_fields, salector_name);

    if(emptydata.length>0)
    	return false;	

	return true

}


function getDatosSend(var_object){

	var Fields = Object.keys(var_object);

	var response = {};

	for(var i=0; i<Fields.length; i++){
		if($(Fields[i])){
			response[Fields[i]] = $(Fields[i]).value
		}
	};

	return response;

}


function number_format(number,decimals,dec_point,thousands_sep) {
    number  = number*1;//makes sure `number` is numeric value
    var str = number.toFixed(decimals?decimals:0).toString().split('.');
    var parts = [];
    for ( var i=str[0].length; i>0; i-=3 ) {
        parts.unshift(str[0].substring(Math.max(0,i-3),i));
    }
    str[0] = parts.join(thousands_sep?thousands_sep:',');
    return str.join(dec_point?dec_point:'.');
}


function Notification(htmlElement) {
    
    this.htmlElement = htmlElement;
    this.text = htmlElement.querySelector('.text');
    this.isRunning = false;
    this.timeout;
    
    this.bindEvents();
};

Notification.prototype.bindEvents = function() {
	var self = this;
   
}

Notification.prototype.info = function(message) {
    if(this.isRunning) return false;
    
    this.text.innerHTML = message;
	this.htmlElement.className = 'notification info';
    
    this.show();
}

Notification.prototype.warning = function(message) {
    if(this.isRunning) return false;
    
    this.text.innerHTML = message;
	this.htmlElement.className = 'notification warning';
    
    this.show();
}

Notification.prototype.error = function(message) {
    if(this.isRunning) return false;
    
    this.text.innerHTML = message;
	 this.htmlElement.className = 'notification error';
    
     this.show();
}

Notification.prototype.success = function(message) {

    if(this.isRunning) return false;
    
    this.text.innerHTML = message;
	 this.htmlElement.className = 'notification success';
     
     this.show();
}

Notification.prototype.show = function() {
    if(!this.htmlElement.classList.contains('visible'))
        this.htmlElement.classList.add('visible');
    
    this.isRunning = true;
    this.autoReset();
};
    
Notification.prototype.autoReset = function() {
	var self = this;
    this.timeout = window.setTimeout(function() {
        self.reset();
    }, 5000);
}

Notification.prototype.reset = function() {
	this.htmlElement.className = "notification";
    this.isRunning = false;
};


var formatNumber = {

	separador: ".", // separador para los miles
	sepDecimal: ',', // separador para los decimales

	formatear:function (num){
		num +='';
		var splitStr = num.split('.');
		var splitLeft = splitStr[0];
		var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
		var regx = /(\d+)(\d{3})/;
		while (regx.test(splitLeft)) {
			splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
		}
		return this.simbol + splitLeft +splitRight;
	},
	new:function(num, simbol){
		this.simbol = simbol ||'';
		return this.formatear(num);
	},

	round : function (number, max = 2) {

		if (typeof number !== 'number' || isNaN(number)) {
		  throw new TypeError('Número inválido: ' + number);  
		}
		
		if (typeof max !== 'number' || isNaN(max)) {
		  throw new TypeError('Máximo de dígitos inválido: ' + max); 
		}
		
		let fractionalPart = number.toString().split('.')[1];
		
		if (!fractionalPart || fractionalPart.length <= 2) {
		  return number;
		}
		
		return Number(number.toFixed(max));

	},

}

btn = {

    loading : function(element){

		var loadingText = $(element).data('loading-text') == undefined ? '<i class="fa fa-spinner fa-spin"></i>' : $(element).data('loading-text');

        if ($(element).html() !== loadingText) {
            $(element).data('original-text', $(element).html());
            $(element).html(loadingText);
            $(element).prop( "disabled", true );
        }
    },

    reset : function(element){
        $(element).html($(element).data('original-text'));
        $(element).prop( "disabled", false );
    }

}
