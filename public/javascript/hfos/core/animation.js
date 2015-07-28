
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

var CoreAnimation = Class.create({

	/**
	 * Loop de visualización basado en Intervalos para iteraciones compuestas
	 */
	complexLoop: function(element, duration, render, options){
		var start = (new Date).getTime(), finish = start+duration;
		var interval = setInterval(function(){
			var time = (new Date).getTime(), position = time>finish ? 1 : (time-start)/duration;
			render(element, position, options);
			if(time>finish){
				clearInterval(interval);
				if(typeof options.afterFinish == "function"){
					options.afterFinish.element = element;
					options.afterFinish();
				}
			}
		}, 10);
	},

	/**
	 * Loop de visualización basado en Intervalos para iteraciones simples
	 */
	loop: function(element, limit, duration, render, options){
		var start = (new Date).getTime(), finish = start+duration;
		var interval = setInterval(function(){
			var time = (new Date).getTime(), pos = time>finish ? 1 : (time-start)/duration;
			var position = 1-((1-limit)*pos);
			render(element, position)
			if(time>finish){
				clearInterval(interval);
				if(typeof options.afterFinish == "function"){
					options.afterFinish.element = element;
					options.afterFinish();
				}
			}
		}, 10);
	},

	/**
	 * Modifica los estilos CSS de un objeto apartir de sus actuales
	 *
	 * @this {CoreAnimation}
	 */
	morph: function(element, options){
		var duration = options.duration*1000;
		element = $(element);
		if(options.style != "undefined"){
			var i = 0;
			var to = [], from = [], styles = [];
			for(var style in options.style){
				styles.push(style);
				to.push(parseInt(options.style[style], 10));
				from.push(parseInt(element.style[style], 10));
			};
			delete options.style;
			options.styles = styles;
			options.to = to;
			options.from = from;
			var render = function(element, position, options){
				element.style.display = "none";
				var length = options.styles.length, to = options.to,
					from = options.from, styles = options.styles;
				for(var i=0;i<length;i++){
					var value = from[i]-((from[i]-to[i])*position);
					element.style[styles[i]] = value+'px';
				}
				element.style.display = "";
			};
			this.complexLoop(element, duration, render, options);
		}
		return this;
	},

	/**
	 * Cambia la opacidad de un objeto de acuerdo a un porcentaje
	 *
	 * @this {CoreAnimation}
	 */
	opaque: function(element, options){
		element = $(element);
		var duration = options.duration*1000;
		var from = parseInt(element.style.opacity, 10);
		var render = function(element, value){
			element.style.opacity = value;
		};
		this.loop(element, options.to, duration, render, options);
		return this;
	},

	/**
	 * Escala el tamaño de un objeto de acuerdo a un porcentaje
	 *
	 * @this {CoreAnimation}
	 */
	scale: function(element, options){
		element = $(element);
		var duration = options.duration*1000;
		element.style.MozTransformOrigin = 'center center';
		var render = function(element, value){
			element.style.MozTransform = 'scale('+value+', '+value+')';
		};
		this.loop(element, options.to, duration, render, options);
		return this;
	},

	/**
	 * Rota un numero de grados un elemento
	 *
	 * @this {CoreAnimation}
	 */
	rotate: function(element, options){
		element = $(element);
		var duration = options.duration*1000;
		var render = function(element, value){
			element.setStyle('-moz-transform: rotate('+value+'deg)');
		};
		this.loop(element, options.to, duration, render, options);
		return this;
	},

	/**
	 *
	 * @this {CoreAnimation}
	 */
	skewX: function(element, options){
		element = $(element);
		var duration = options.duration*1000;
		this.loop(element, options.to, duration, function(element, value){
			element.setStyle('-moz-transform: skewX('+value+'deg)');
		});
		return this;
	},

	traslate3d: function(element){
		element.style.webkitTransform = 'translate3d(0, 0, 100px)';
	},

	scale2: function(element, to){
		element.style.MozTransitionDuration = '4s';
	},

	morph2: function(element, options){
		element.style.MozTransitionProperty = 'width';
		element.style.MozTransitionDuration = '5s';
		element.style.MozTransitionDelay = '0s';
		element.style.width = '900px';
	},

	flipX: function(element, options){
		element.addClassName('flipped-x');
		window.setTimeout(function(element, options){
			if(typeof options.afterFinish != "undefined"){
				options.afterFinish(this);
			};
			element.removeClassName('flipped-x');
		}.bind(this, element, options), 700)
	},

	flipY: function(element){
		element.addClassName('flipped-y');
	}

});
