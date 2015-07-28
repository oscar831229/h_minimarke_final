
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

//Esta funcion $() es más rápida por que solo toma un elemento
function $(element){
	if(Object.isString(element)){
    	element = document.getElementById(element);
	};
  	return Element.extend(element);
};

//Reemplaza el método select por querySelectorAll
if(typeof document.querySelectorAll != "undefined"){
	Element.prototype.select = function(cssSelector){
		var nodeList = this.querySelectorAll(cssSelector);
		if(nodeList===null){
			return [];
		} else {
			return nodeList;
		}
	};
};

//Aplicar un estilo CSS
Element.prototype.setStyle = function(styles){
	if(typeof styles == "string"){
		this.style.cssText+=';'+styles;
	} else {
		for(var property in styles){
			this.style[property] = styles[property];
		}
	}
};

//Elimina un elemento del DOM
Element.prototype.erase = function(){
	if(this.parentNode!==null){
		this.parentNode.removeChild(this);
		return true;
	};
	return false;
};

/**
 * Busca el primer elemento que coincida con una clase CSS
 *
 * @param {string} className
 * @return {Element}
 */
Element.prototype.getElement = function(className){
	return this.querySelector('.'+className);
};

//Verifica que exista el querySelector
if(typeof document.querySelector != "undefined"){
	Element.prototype.selectOne = function(elementID){
		return this.querySelector(elementID);
	}
} else {
	Element.prototype.selectOne = function(elementID){
		return this.select(elementID)[0];
	}
};

//Devuelve un ID
Element.prototype.getId = function(){
	return this.id;
};

//Obtiene la altura de un elemento
Element.prototype.getHeight = function(){
	return this.offsetHeight;
};

//Obtiene el ancho de un elemento
Element.prototype.getWidth = function(){
	return this.offsetWidth;
};

//Realiza una petición Ajax en un objeto
Element.prototype.load = function(url, options){
	new HfosAjax.Updater(this, url, options);
};

//Obtiene la posición real de un elemento
Element.prototype.realOffset = function(){
	var currentLeft = 0, currentTop = 0;
	var element = this;
	if(element.offsetParent){
		do {
			if(element.scrollTop==0){
				currentTop += element.offsetTop;
			} else {
				currentTop += (element.offsetTop-element.scrollTop);
			};
			currentLeft += element.offsetLeft;
		} while(element = element.offsetParent);
	};
	return [currentLeft, currentTop];
};

//Hace invisible un elemento manteniendo su espacio en pantalla
Element.prototype.invisible = function(){
	this.style.visibility = "hidden";
};

Element.prototype.put = function(){

};

Element.prototype.obtain = function(){

}

//Agrega argumentos sin perder el contexto original de la función
Function.prototype.shift = function(){
    var _context = this;
    var _args = arguments;
	return function(){
		return _context.apply(_context, _args);
	};
};

//Evita la propagación de un evento
Event.cancelBubble = function(event){
	event.cancelBubble = true;
	event.returnValue = false;
	if(event.stopPropagation){
		event.stopPropagation();
		event.preventDefault();
	};
};


/**
 * Metodo que visualiza el input file para subir un archivo
 *
 * @param {string} name
 */
var enable_upload_file = function(name){
	if(name){
		var inputUploadFile = $(name+'_span');
		var inputUploadFileLink = $(name+'_up');
		if(inputUploadFile && inputUploadFileLink){
			inputUploadFile.show();
			inputUploadFileLink.hide();
		}
	}
}

/**
 * Metodo que visualiza el subir imagen y oculta el input file
 *
 * @param {string} name
 */
var cancel_upload_file = function(name){
	if(name){
		var inputUploadFile = $(name+'_span');
		var inputUploadFileLink = $(name+'_up');
		if(inputUploadFile && inputUploadFileLink){
			inputUploadFile.hide();
			inputUploadFileLink.show();
		}
	}
}

/**
 * Metodo que asigna el string de la imagen a el campo de imagen
 */
var upload_file = function(name){
	if(name){
		var inputUploadFile = $(name+'_span');
		var inputFile = $(name);
		if(inputFile && inputUploadFile){
			inputFile.value = inputUploadFile.value;
		}
	}
}