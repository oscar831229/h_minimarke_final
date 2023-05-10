var Prototype={Version:"1.7",Browser:function(){var a=navigator.userAgent,b="[object Opera]"==Object.prototype.toString.call(window.opera);return{IE:!!window.attachEvent&&!b,Opera:b,WebKit:-1<a.indexOf("AppleWebKit/"),Gecko:-1<a.indexOf("Gecko")&&-1===a.indexOf("KHTML"),MobileSafari:/Apple.*Mobile/.test(a)}}(),BrowserFeatures:{XPath:!!document.evaluate,SelectorsAPI:!!document.querySelector,ElementExtensions:function(){var a=window.Element||window.HTMLElement;return!(!a||!a.prototype)}(),SpecificElementExtensions:function(){if("undefined"!==
typeof window.HTMLDivElement)return!0;var a=document.createElement("div"),b=document.createElement("form"),c=!1;a.__proto__&&a.__proto__!==b.__proto__&&(c=!0);return c}()},ScriptFragment:"<script[^>]*>([\\S\\s]*?)<\/script>",JSONFilter:/^\/\*-secure-([\s\S]*)\*\/\s*$/,emptyFunction:function(){},K:function(a){return a}};if(Prototype.Browser.MobileSafari)Prototype.BrowserFeatures.SpecificElementExtensions=!1;
var Abstract={},Try={these:function(){for(var a,b=0,c=arguments.length;b<c;b++){var d=arguments[b];try{a=d();break}catch(e){}}return a}},Class=function(){function a(){}var b=function(){for(var a in{toString:1})if("toString"===a)return!1;return!0}();return{create:function(){function b(){this.initialize.apply(this,arguments)}var d=null,e=$A(arguments);Object.isFunction(e[0])&&(d=e.shift());Object.extend(b,Class.Methods);b.superclass=d;b.subclasses=[];if(d)a.prototype=d.prototype,b.prototype=new a,d.subclasses.push(b);
for(var d=0,f=e.length;d<f;d++)b.addMethods(e[d]);if(!b.prototype.initialize)b.prototype.initialize=Prototype.emptyFunction;return b.prototype.constructor=b},Methods:{addMethods:function(a){var d=this.superclass&&this.superclass.prototype,e=Object.keys(a);b&&(a.toString!=Object.prototype.toString&&e.push("toString"),a.valueOf!=Object.prototype.valueOf&&e.push("valueOf"));for(var f=0,g=e.length;f<g;f++){var j=e[f],h=a[j];if(d&&Object.isFunction(h)&&"$super"==h.argumentNames()[0]){var m=h,h=function(a){return function(){return d[a].apply(this,
arguments)}}(j).wrap(m);h.valueOf=m.valueOf.bind(m);h.toString=m.toString.bind(m)}this.prototype[j]=h}return this}}}}();
(function(){function a(a){switch(a){case null:return m;case void 0:return o}switch(typeof a){case "boolean":return l;case "number":return x;case "string":return q}return r}function b(a,b){for(var c in b)a[c]=b[c];return a}function c(a){return d("",{"":a},[])}function d(b,c,g){var c=c[b],e=typeof c;a(c)===r&&"function"===typeof c.toJSON&&(c=c.toJSON(b));b=h.call(c);switch(b){case w:case s:case y:c=c.valueOf()}switch(c){case null:return"null";case !0:return"true";case !1:return"false"}switch(typeof c){case "string":return c.inspect(!0);
case "number":return isFinite(c)?""+c:"null";case "object":for(var e=0,j=g.length;e<j;e++)if(g[e]===c)throw new TypeError;g.push(c);var f=[];if(b===A){e=0;for(j=c.length;e<j;e++){var m=d(e,c,g);f.push("undefined"===typeof m?"null":m)}f="["+f.join(",")+"]"}else{for(var o=Object.keys(c),e=0,j=o.length;e<j;e++)b=o[e],m=d(b,c,g),"undefined"!==typeof m&&f.push(b.inspect(!0)+":"+m);f="{"+f.join(",")+"}"}g.pop();return f}}function e(a){return JSON.stringify(a)}function f(b){if(a(b)!==r)throw new TypeError;
var c=[],d;for(d in b)b.hasOwnProperty(d)&&c.push(d);return c}function g(a){return h.call(a)===A}function j(a){return"undefined"===typeof a}var h=Object.prototype.toString,m="Null",o="Undefined",l="Boolean",x="Number",q="String",r="Object",s="[object Boolean]",w="[object Number]",y="[object String]",A="[object Array]",D=window.JSON&&"function"===typeof JSON.stringify&&"0"===JSON.stringify(0)&&"undefined"===typeof JSON.stringify(Prototype.K);if("function"==typeof Array.isArray&&Array.isArray([])&&
!Array.isArray({}))g=Array.isArray;b(Object,{extend:b,inspect:function(a){try{return j(a)?"undefined":null===a?"null":a.inspect?a.inspect():""+a}catch(b){if(b instanceof RangeError)return"...";throw b;}},toJSON:D?e:c,toQueryString:function(a){return $H(a).toQueryString()},toHTML:function(a){return a&&a.toHTML?a.toHTML():String.interpret(a)},keys:Object.keys||f,values:function(a){var b=[],c;for(c in a)b.push(a[c]);return b},clone:function(a){return b({},a)},isElement:function(a){return!!(a&&1==a.nodeType)},
isArray:g,isHash:function(a){return a instanceof Hash},isFunction:function(a){return"[object Function]"===h.call(a)},isString:function(a){return h.call(a)===y},isNumber:function(a){return h.call(a)===w},isDate:function(a){return"[object Date]"===h.call(a)},isUndefined:j})})();
Object.extend(Function.prototype,function(){function a(a,b){for(var c=a.length,g=b.length;g--;)a[c+g]=b[g];return a}function b(b,e){b=c.call(b,0);return a(b,e)}var c=Array.prototype.slice;return{argumentNames:function(){var a=this.toString().match(/^[\s\(]*function[^(]*\(([^)]*)\)/)[1].replace(/\/\/.*?[\r\n]|\/\*(?:.|[\r\n])*?\*\//g,"").replace(/\s+/g,"").split(",");return 1==a.length&&!a[0]?[]:a},bind:function(a){if(2>arguments.length&&Object.isUndefined(arguments[0]))return this;var e=this,f=c.call(arguments,
1);return function(){var c=b(f,arguments);return e.apply(a,c)}},bindAsEventListener:function(b){var e=this,f=c.call(arguments,1);return function(c){c=a([c||window.event],f);return e.apply(b,c)}},curry:function(){if(!arguments.length)return this;var a=this,e=c.call(arguments,0);return function(){var c=b(e,arguments);return a.apply(this,c)}},delay:function(a){var b=this,f=c.call(arguments,1);return window.setTimeout(function(){return b.apply(b,f)},1E3*a)},defer:function(){return this.delay.apply(this,
a([0.01],arguments))},wrap:function(b){var c=this;return function(){var f=a([c.bind(this)],arguments);return b.apply(this,f)}},methodize:function(){if(this._methodized)return this._methodized;var b=this;return this._methodized=function(){var c=a([this],arguments);return b.apply(null,c)}}}}());
(function(a){function b(){return this.getUTCFullYear()+"-"+(this.getUTCMonth()+1).toPaddedString(2)+"-"+this.getUTCDate().toPaddedString(2)+"T"+this.getUTCHours().toPaddedString(2)+":"+this.getUTCMinutes().toPaddedString(2)+":"+this.getUTCSeconds().toPaddedString(2)+"Z"}function c(){return this.toISOString()}if(!a.toISOString)a.toISOString=b;if(!a.toJSON)a.toJSON=c})(Date.prototype);RegExp.prototype.match=RegExp.prototype.test;
RegExp.escape=function(a){return(""+a).replace(/([.*+?^=!:${}()|[\]\/\\])/g,"\\$1")};
var PeriodicalExecuter=Class.create({initialize:function(a,b){this.callback=a;this.frequency=b;this.currentlyExecuting=!1;this.registerCallback()},registerCallback:function(){this.timer=setInterval(this.onTimerEvent.bind(this),1E3*this.frequency)},execute:function(){this.callback(this)},stop:function(){if(this.timer)clearInterval(this.timer),this.timer=null},onTimerEvent:function(){if(!this.currentlyExecuting)try{this.currentlyExecuting=!0,this.execute(),this.currentlyExecuting=!1}catch(a){throw this.currentlyExecuting=
!1,a;}}});Object.extend(String,{interpret:function(a){return null==a?"":""+a},specialChar:{"\u0008":"\\b","\t":"\\t","\n":"\\n","\u000c":"\\f","\r":"\\r","\\":"\\\\"}});
Object.extend(String.prototype,function(){function a(a){if(Object.isFunction(a))return a;var b=new Template(a);return function(a){return b.evaluate(a)}}function b(){return this.replace(/^\s+/,"").replace(/\s+$/,"")}function c(a){var b=this.strip().match(/([^?#]*)(#.*)?$/);return!b?{}:b[1].split(a||"&").inject({},function(a,b){if((b=b.split("="))[0]){var c=decodeURIComponent(b.shift()),d=1<b.length?b.join("="):b[0];void 0!=d&&(d=decodeURIComponent(d));c in a?(Object.isArray(a[c])||(a[c]=[a[c]]),a[c].push(d)):
a[c]=d}return a})}function d(a){var b=this.unfilterJSON(),c=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;c.test(b)&&(b=b.replace(c,function(a){return"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)}));try{if(!a||b.isJSON())return eval("("+b+")")}catch(d){}throw new SyntaxError("Badly formed JSON string: "+this.inspect());}function e(){var a=this.unfilterJSON();return JSON.parse(a)}var f=window.JSON&&"function"===typeof JSON.parse&&
JSON.parse('{"test": true}').test;return{gsub:function(b,c){var d="",e=this,f,c=a(c);Object.isString(b)&&(b=RegExp.escape(b));if(!b.length&&!b.source)return c=c(""),c+e.split("").join(c)+c;for(;0<e.length;)(f=e.match(b))?(d+=e.slice(0,f.index),d+=String.interpret(c(f)),e=e.slice(f.index+f[0].length)):(d+=e,e="");return d},sub:function(b,c,d){c=a(c);d=Object.isUndefined(d)?1:d;return this.gsub(b,function(a){return 0>--d?a[0]:c(a)})},scan:function(a,b){this.gsub(a,b);return""+this},truncate:function(a,
b){a=a||30;b=Object.isUndefined(b)?"...":b;return this.length>a?this.slice(0,a-b.length)+b:""+this},strip:String.prototype.trim||b,stripTags:function(){return this.replace(/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?>|<\/\w+>/gi,"")},stripScripts:function(){return this.replace(RegExp(Prototype.ScriptFragment,"img"),"")},extractScripts:function(){var a=RegExp(Prototype.ScriptFragment,"im");return(this.match(RegExp(Prototype.ScriptFragment,"img"))||[]).map(function(b){return(b.match(a)||["",""])[1]})},evalScripts:function(){return this.extractScripts().map(function(a){return eval(a)})},
escapeHTML:function(){return this.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")},unescapeHTML:function(){return this.stripTags().replace(/&lt;/g,"<").replace(/&gt;/g,">").replace(/&amp;/g,"&")},toQueryParams:c,parseQuery:c,toArray:function(){return this.split("")},succ:function(){return this.slice(0,this.length-1)+String.fromCharCode(this.charCodeAt(this.length-1)+1)},times:function(a){return 1>a?"":Array(a+1).join(this)},camelize:function(){return this.replace(/-+(.)?/g,function(a,
b){return b?b.toUpperCase():""})},capitalize:function(){return this.charAt(0).toUpperCase()+this.substring(1).toLowerCase()},underscore:function(){return this.replace(/::/g,"/").replace(/([A-Z]+)([A-Z][a-z])/g,"$1_$2").replace(/([a-z\d])([A-Z])/g,"$1_$2").replace(/-/g,"_").toLowerCase()},dasherize:function(){return this.replace(/_/g,"-")},inspect:function(a){var b=this.replace(/[\x00-\x1f\\]/g,function(a){return a in String.specialChar?String.specialChar[a]:"\\u00"+a.charCodeAt().toPaddedString(2,
16)});return a?'"'+b.replace(/"/g,'\\"')+'"':"'"+b.replace(/'/g,"\\'")+"'"},unfilterJSON:function(a){return this.replace(a||Prototype.JSONFilter,"$1")},isJSON:function(){var a=this;if(a.blank())return!1;a=a.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@");a=a.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]");a=a.replace(/(?:^|:|,)(?:\s*\[)+/g,"");return/^[\],:{}\s]*$/.test(a)},evalJSON:f?e:d,include:function(a){return-1<this.indexOf(a)},startsWith:function(a){return 0===
this.lastIndexOf(a,0)},endsWith:function(a){var b=this.length-a.length;return 0<=b&&this.indexOf(a,b)===b},empty:function(){return""==this},blank:function(){return/^\s*$/.test(this)},interpolate:function(a,b){return(new Template(this,b)).evaluate(a)}}}());
var Template=Class.create({initialize:function(a,b){this.template=a.toString();this.pattern=b||Template.Pattern},evaluate:function(a){a&&Object.isFunction(a.toTemplateReplacements)&&(a=a.toTemplateReplacements());return this.template.gsub(this.pattern,function(b){if(null==a)return b[1]+"";var c=b[1]||"";if("\\"==c)return b[2];var d=a,e=b[3],f=/^([^.[]+|\[((?:.*?[^\\])?)\])(\.|\[|$)/,b=f.exec(e);if(null==b)return c;for(;null!=b;){var g=b[1].startsWith("[")?b[2].replace(/\\\\]/g,"]"):b[1],d=d[g];if(null==
d||""==b[3])break;e=e.substring("["==b[3]?b[1].length:b[0].length);b=f.exec(e)}return c+String.interpret(d)})}});Template.Pattern=/(^|.|\r|\n)(#\{(.*?)\})/;
var $break={},Enumerable=function(){function a(a,b){var a=a||Prototype.K,c=!0;this.each(function(d,g){c=c&&!!a.call(b,d,g);if(!c)throw $break;});return c}function b(a,b){var a=a||Prototype.K,c=!1;this.each(function(d,g){if(c=!!a.call(b,d,g))throw $break;});return c}function c(a,b){var a=a||Prototype.K,c=[];this.each(function(d,g){c.push(a.call(b,d,g))});return c}function d(a,b){var c;this.each(function(d,g){if(a.call(b,d,g))throw c=d,$break;});return c}function e(a,b){var c=[];this.each(function(d,
g){a.call(b,d,g)&&c.push(d)});return c}function f(a){if(Object.isFunction(this.indexOf)&&-1!=this.indexOf(a))return!0;var b=!1;this.each(function(c){if(c==a)throw b=!0,$break;});return b}function g(){return this.map()}return{each:function(a,b){var c=0;try{this._each(function(d){a.call(b,d,c++)})}catch(d){if(d!=$break)throw d;}return this},eachSlice:function(a,b,c){var d=-a,g=[],e=this.toArray();if(1>a)return e;for(;(d+=a)<e.length;)g.push(e.slice(d,d+a));return g.collect(b,c)},all:a,every:a,any:b,
some:b,collect:c,map:c,detect:d,findAll:e,select:e,filter:e,grep:function(a,b,c){var b=b||Prototype.K,d=[];Object.isString(a)&&(a=RegExp(RegExp.escape(a)));this.each(function(g,e){a.match(g)&&d.push(b.call(c,g,e))});return d},include:f,member:f,inGroupsOf:function(a,b){b=Object.isUndefined(b)?null:b;return this.eachSlice(a,function(c){for(;c.length<a;)c.push(b);return c})},inject:function(a,b,c){this.each(function(d,g){a=b.call(c,a,d,g)});return a},invoke:function(a){var b=$A(arguments).slice(1);
return this.map(function(c){return c[a].apply(c,b)})},max:function(a,b){var a=a||Prototype.K,c;this.each(function(d,g){d=a.call(b,d,g);if(null==c||d>=c)c=d});return c},min:function(a,b){var a=a||Prototype.K,c;this.each(function(d,g){d=a.call(b,d,g);if(null==c||d<c)c=d});return c},partition:function(a,b){var a=a||Prototype.K,c=[],d=[];this.each(function(g,e){(a.call(b,g,e)?c:d).push(g)});return[c,d]},pluck:function(a){var b=[];this.each(function(c){b.push(c[a])});return b},reject:function(a,b){var c=
[];this.each(function(d,g){a.call(b,d,g)||c.push(d)});return c},sortBy:function(a,b){return this.map(function(c,d){return{value:c,criteria:a.call(b,c,d)}}).sort(function(a,b){var c=a.criteria,d=b.criteria;return c<d?-1:c>d?1:0}).pluck("value")},toArray:g,entries:g,zip:function(){var a=Prototype.K,b=$A(arguments);Object.isFunction(b.last())&&(a=b.pop());var c=[this].concat(b).map($A);return this.map(function(b,d){return a(c.pluck(d))})},size:function(){return this.toArray().length},inspect:function(){return"#<Enumerable:"+
this.toArray().inspect()+">"},find:d}}();function $A(a){if(!a)return[];if("toArray"in Object(a))return a.toArray();for(var b=a.length||0,c=Array(b);b--;)c[b]=a[b];return c}function $w(a){return!Object.isString(a)?[]:(a=a.strip())?a.split(/\s+/):[]}Array.from=$A;
(function(){function a(a,b){for(var c=0,d=this.length>>>0;c<d;c++)c in this&&a.call(b,this[c],c,this)}function b(){return g.call(this,0)}function c(a,b){b||(b=0);var c=this.length;for(0>b&&(b=c+b);b<c;b++)if(this[b]===a)return b;return-1}function d(a,b){var b=isNaN(b)?this.length:(0>b?this.length+b:b)+1,c=this.slice(0,b).reverse().indexOf(a);return 0>c?c:b-c-1}function e(){for(var a=g.call(this,0),b,c=0,d=arguments.length;c<d;c++)if(b=arguments[c],Object.isArray(b)&&!("callee"in b))for(var e=0,f=
b.length;e<f;e++)a.push(b[e]);else a.push(b);return a}var f=Array.prototype,g=f.slice,j=f.forEach;j||(j=a);Object.extend(f,Enumerable);if(!f._reverse)f._reverse=f.reverse;Object.extend(f,{_each:j,clear:function(){this.length=0;return this},first:function(){return this[0]},last:function(){return this[this.length-1]},compact:function(){return this.select(function(a){return null!=a})},flatten:function(){return this.inject([],function(a,b){if(Object.isArray(b))return a.concat(b.flatten());a.push(b);return a})},
without:function(){var a=g.call(arguments,0);return this.select(function(b){return!a.include(b)})},reverse:function(a){return(!1===a?this.toArray():this)._reverse()},uniq:function(a){return this.inject([],function(b,c,d){(0==d||(a?b.last()!=c:!b.include(c)))&&b.push(c);return b})},intersect:function(a){return this.uniq().findAll(function(b){return a.detect(function(a){return b===a})})},clone:b,toArray:b,size:function(){return this.length},inspect:function(){return"["+this.map(Object.inspect).join(", ")+
"]"}});if(function(){return 1!==[].concat(arguments)[0][0]}(1,2))f.concat=e;if(!f.indexOf)f.indexOf=c;if(!f.lastIndexOf)f.lastIndexOf=d})();function $H(a){return new Hash(a)}
var Hash=Class.create(Enumerable,function(){function a(){return Object.clone(this._object)}return{initialize:function(a){this._object=Object.isHash(a)?a.toObject():Object.clone(a)},_each:function(a){for(var c in this._object){var d=this._object[c],e=[c,d];e.key=c;e.value=d;a(e)}},set:function(a,c){return this._object[a]=c},get:function(a){if(this._object[a]!==Object.prototype[a])return this._object[a]},unset:function(a){var c=this._object[a];delete this._object[a];return c},toObject:a,toTemplateReplacements:a,
keys:function(){return this.pluck("key")},values:function(){return this.pluck("value")},index:function(a){var c=this.detect(function(c){return c.value===a});return c&&c.key},merge:function(a){return this.clone().update(a)},update:function(a){return(new Hash(a)).inject(this,function(a,b){a.set(b.key,b.value);return a})},toQueryString:function(){return this.inject([],function(a,c){var d=encodeURIComponent(c.key),e=c.value;if(e&&"object"==typeof e){if(Object.isArray(e)){for(var f=[],g=0,j=e.length,h;g<
j;g++)h=e[g],f.push(Object.isUndefined(h)?d:d+"="+encodeURIComponent(String.interpret(h)));return a.concat(f)}}else a.push(Object.isUndefined(e)?d:d+"="+encodeURIComponent(String.interpret(e)));return a}).join("&")},inspect:function(){return"#<Hash:{"+this.map(function(a){return a.map(Object.inspect).join(": ")}).join(", ")+"}>"},toJSON:a,clone:function(){return new Hash(this)}}}());Hash.from=$H;
Object.extend(Number.prototype,function(){return{toColorPart:function(){return this.toPaddedString(2,16)},succ:function(){return this+1},times:function(a,b){$R(0,this,!0).each(a,b);return this},toPaddedString:function(a,b){var c=this.toString(b||10);return"0".times(a-c.length)+c},abs:function(){return Math.abs(this)},round:function(){return Math.round(this)},ceil:function(){return Math.ceil(this)},floor:function(){return Math.floor(this)}}}());function $R(a,b,c){return new ObjectRange(a,b,c)}
var ObjectRange=Class.create(Enumerable,function(){return{initialize:function(a,b,c){this.start=a;this.end=b;this.exclusive=c},_each:function(a){for(var b=this.start;this.include(b);)a(b),b=b.succ()},include:function(a){return a<this.start?!1:this.exclusive?a<this.end:a<=this.end}}}()),Ajax={getTransport:function(){return Try.these(function(){return new XMLHttpRequest},function(){return new ActiveXObject("Msxml2.XMLHTTP")},function(){return new ActiveXObject("Microsoft.XMLHTTP")})||!1},activeRequestCount:0,
Responders:{responders:[],_each:function(a){this.responders._each(a)},register:function(a){this.include(a)||this.responders.push(a)},unregister:function(a){this.responders=this.responders.without(a)},dispatch:function(a,b,c,d){this.each(function(e){if(Object.isFunction(e[a]))try{e[a].apply(e,[b,c,d])}catch(f){}})}}};Object.extend(Ajax.Responders,Enumerable);Ajax.Responders.register({onCreate:function(){Ajax.activeRequestCount++},onComplete:function(){Ajax.activeRequestCount--}});
Ajax.Base=Class.create({initialize:function(a){this.options={method:"post",asynchronous:!0,contentType:"application/x-www-form-urlencoded",encoding:"UTF-8",parameters:"",evalJSON:!0,evalJS:!0};Object.extend(this.options,a||{});this.options.method=this.options.method.toLowerCase();if(Object.isHash(this.options.parameters))this.options.parameters=this.options.parameters.toObject()}});
Ajax.Request=Class.create(Ajax.Base,{_complete:!1,initialize:function($super,b,c){$super(c);this.transport=Ajax.getTransport();this.request(b)},request:function(a){this.url=a;this.method=this.options.method;a=Object.isString(this.options.parameters)?this.options.parameters:Object.toQueryString(this.options.parameters);if(!["get","post"].include(this.method))a+=(a?"&":"")+"_method="+this.method,this.method="post";a&&"get"===this.method&&(this.url+=(this.url.include("?")?"&":"?")+a);this.parameters=
a.toQueryParams();try{var b=new Ajax.Response(this);if(this.options.onCreate)this.options.onCreate(b);Ajax.Responders.dispatch("onCreate",this,b);this.transport.open(this.method.toUpperCase(),this.url,this.options.asynchronous);this.options.asynchronous&&this.respondToReadyState.bind(this).defer(1);this.transport.onreadystatechange=this.onStateChange.bind(this);this.setRequestHeaders();this.body="post"==this.method?this.options.postBody||a:null;this.transport.send(this.body);if(!this.options.asynchronous&&
this.transport.overrideMimeType)this.onStateChange()}catch(c){this.dispatchException(c)}},onStateChange:function(){var a=this.transport.readyState;1<a&&!(4==a&&this._complete)&&this.respondToReadyState(this.transport.readyState)},setRequestHeaders:function(){var a={"X-Requested-With":"XMLHttpRequest","X-Prototype-Version":Prototype.Version,Accept:"text/javascript, text/html, application/xml, text/xml, */*"};if("post"==this.method&&(a["Content-type"]=this.options.contentType+(this.options.encoding?
"; charset="+this.options.encoding:""),this.transport.overrideMimeType&&2005>(navigator.userAgent.match(/Gecko\/(\d{4})/)||[0,2005])[1]))a.Connection="close";if("object"==typeof this.options.requestHeaders){var b=this.options.requestHeaders;if(Object.isFunction(b.push))for(var c=0,d=b.length;c<d;c+=2)a[b[c]]=b[c+1];else $H(b).each(function(b){a[b.key]=b.value})}for(var e in a)this.transport.setRequestHeader(e,a[e])},success:function(){var a=this.getStatus();return!a||200<=a&&300>a||304==a},getStatus:function(){try{return 1223===
this.transport.status?204:this.transport.status||0}catch(a){return 0}},respondToReadyState:function(a){var a=Ajax.Request.Events[a],b=new Ajax.Response(this);if("Complete"==a){try{this._complete=!0,(this.options["on"+b.status]||this.options["on"+(this.success()?"Success":"Failure")]||Prototype.emptyFunction)(b,b.headerJSON)}catch(c){this.dispatchException(c)}var d=b.getHeader("Content-type");("force"==this.options.evalJS||this.options.evalJS&&this.isSameOrigin()&&d&&d.match(/^\s*(text|application)\/(x-)?(java|ecma)script(;.*)?\s*$/i))&&
this.evalResponse()}try{(this.options["on"+a]||Prototype.emptyFunction)(b,b.headerJSON),Ajax.Responders.dispatch("on"+a,this,b,b.headerJSON)}catch(e){this.dispatchException(e)}if("Complete"==a)this.transport.onreadystatechange=Prototype.emptyFunction},isSameOrigin:function(){var a=this.url.match(/^\s*https?:\/\/[^\/]*/);return!a||a[0]=="#{protocol}//#{domain}#{port}".interpolate({protocol:location.protocol,domain:document.domain,port:location.port?":"+location.port:""})},getHeader:function(a){try{return this.transport.getResponseHeader(a)||
null}catch(b){return null}},evalResponse:function(){try{return eval((this.transport.responseText||"").unfilterJSON())}catch(a){this.dispatchException(a)}},dispatchException:function(a){(this.options.onException||Prototype.emptyFunction)(this,a);Ajax.Responders.dispatch("onException",this,a)}});Ajax.Request.Events=["Uninitialized","Loading","Loaded","Interactive","Complete"];
Ajax.Response=Class.create({initialize:function(a){this.request=a;var a=this.transport=a.transport,b=this.readyState=a.readyState;if(2<b&&!Prototype.Browser.IE||4==b)this.status=this.getStatus(),this.statusText=this.getStatusText(),this.responseText=String.interpret(a.responseText),this.headerJSON=this._getHeaderJSON();if(4==b)a=a.responseXML,this.responseXML=Object.isUndefined(a)?null:a,this.responseJSON=this._getResponseJSON()},status:0,statusText:"",getStatus:Ajax.Request.prototype.getStatus,getStatusText:function(){try{return this.transport.statusText||
""}catch(a){return""}},getHeader:Ajax.Request.prototype.getHeader,getAllHeaders:function(){try{return this.getAllResponseHeaders()}catch(a){return null}},getResponseHeader:function(a){return this.transport.getResponseHeader(a)},getAllResponseHeaders:function(){return this.transport.getAllResponseHeaders()},_getHeaderJSON:function(){var a=this.getHeader("X-JSON");if(!a)return null;a=decodeURIComponent(escape(a));try{return a.evalJSON(this.request.options.sanitizeJSON||!this.request.isSameOrigin())}catch(b){this.request.dispatchException(b)}},
_getResponseJSON:function(){var a=this.request.options;if(!a.evalJSON||"force"!=a.evalJSON&&!(this.getHeader("Content-type")||"").include("application/json")||this.responseText.blank())return null;try{return this.responseText.evalJSON(a.sanitizeJSON||!this.request.isSameOrigin())}catch(b){this.request.dispatchException(b)}}});
Ajax.Updater=Class.create(Ajax.Request,{initialize:function($super,b,c,d){this.container={success:b.success||b,failure:b.failure||(b.success?null:b)};var d=Object.clone(d),e=d.onComplete;d.onComplete=function(b,c){this.updateContent(b.responseText);Object.isFunction(e)&&e(b,c)}.bind(this);$super(c,d)},updateContent:function(a){var b=this.container[this.success()?"success":"failure"],c=this.options;c.evalScripts||(a=a.stripScripts());if(b=$(b))if(c.insertion)if(Object.isString(c.insertion)){var d=
{};d[c.insertion]=a;b.insert(d)}else c.insertion(b,a);else b.update(a)}});
Ajax.PeriodicalUpdater=Class.create(Ajax.Base,{initialize:function($super,b,c,d){$super(d);this.onComplete=this.options.onComplete;this.frequency=this.options.frequency||2;this.decay=this.options.decay||1;this.updater={};this.container=b;this.url=c;this.start()},start:function(){this.options.onComplete=this.updateComplete.bind(this);this.onTimerEvent()},stop:function(){this.updater.options.onComplete=void 0;clearTimeout(this.timer);(this.onComplete||Prototype.emptyFunction).apply(this,arguments)},
updateComplete:function(a){if(this.options.decay)this.decay=a.responseText==this.lastText?this.decay*this.options.decay:1,this.lastText=a.responseText;this.timer=this.onTimerEvent.bind(this).delay(this.decay*this.frequency)},onTimerEvent:function(){this.updater=new Ajax.Updater(this.container,this.url,this.options)}});
function $(a){if(1<arguments.length){for(var b=0,c=[],d=arguments.length;b<d;b++)c.push($(arguments[b]));return c}Object.isString(a)&&(a=document.getElementById(a));return Element.extend(a)}if(Prototype.BrowserFeatures.XPath)document._getElementsByXPath=function(a,b){for(var c=[],d=document.evaluate(a,$(b)||document,null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null),e=0,f=d.snapshotLength;e<f;e++)c.push(Element.extend(d.snapshotItem(e)));return c};if(!Node)var Node={};
Node.ELEMENT_NODE||Object.extend(Node,{ELEMENT_NODE:1,ATTRIBUTE_NODE:2,TEXT_NODE:3,CDATA_SECTION_NODE:4,ENTITY_REFERENCE_NODE:5,ENTITY_NODE:6,PROCESSING_INSTRUCTION_NODE:7,COMMENT_NODE:8,DOCUMENT_NODE:9,DOCUMENT_TYPE_NODE:10,DOCUMENT_FRAGMENT_NODE:11,NOTATION_NODE:12});
(function(a){var b=function(){try{var a=document.createElement('<input name="x">');return"input"===a.tagName.toLowerCase()&&"x"===a.name}catch(b){return!1}}(),c=a.Element;a.Element=function(a,c){var c=c||{},a=a.toLowerCase(),f=Element.cache;if(b&&c.name)return a="<"+a+' name="'+c.name+'">',delete c.name,Element.writeAttribute(document.createElement(a),c);f[a]||(f[a]=Element.extend(document.createElement(a)));f=!("select"===a||"type"in c)?f[a].cloneNode(!1):document.createElement(a);return Element.writeAttribute(f,
c)};Object.extend(a.Element,c||{});if(c)a.Element.prototype=c.prototype})(this);Element.idCounter=1;Element.cache={};Element._purgeElement=function(a){var b=a._prototypeUID;if(b)Element.stopObserving(a),a._prototypeUID=void 0,delete Element.Storage[b]};
Element.Methods={visible:function(a){return"none"!=$(a).style.display},toggle:function(a){a=$(a);Element[Element.visible(a)?"hide":"show"](a);return a},hide:function(a){a=$(a);a.style.display="none";return a},show:function(a){a=$(a);a.style.display="";return a},remove:function(a){a=$(a);a.parentNode.removeChild(a);return a},update:function(){var a=function(){var a=document.createElement("select"),b=!0;a.innerHTML='<option value="test">test</option>';a.options&&a.options[0]&&(b="OPTION"!==a.options[0].nodeName.toUpperCase());
return b}(),b=function(){try{var a=document.createElement("table");if(a&&a.tBodies)return a.innerHTML="<tbody><tr><td>test</td></tr></tbody>","undefined"==typeof a.tBodies[0]}catch(b){return!0}}(),c=function(){try{var a=document.createElement("div");a.innerHTML="<link>";return 0===a.childNodes.length}catch(b){return!0}}(),d=a||b||c,e=function(){var a=document.createElement("script"),b=!1;try{a.appendChild(document.createTextNode("")),b=!a.firstChild||a.firstChild&&3!==a.firstChild.nodeType}catch(c){b=
!0}return b}();return function(a,b){for(var a=$(a),j=Element._purgeElement,h=a.getElementsByTagName("*"),m=h.length;m--;)j(h[m]);b&&b.toElement&&(b=b.toElement());if(Object.isElement(b))return a.update().insert(b);b=Object.toHTML(b);j=a.tagName.toUpperCase();if("SCRIPT"===j&&e)return a.text=b,a;if(d)if(j in Element._insertionTranslations.tags){for(;a.firstChild;)a.removeChild(a.firstChild);Element._getContentFromAnonymousElement(j,b.stripScripts()).each(function(b){a.appendChild(b)})}else if(c&&Object.isString(b)&&
-1<b.indexOf("<link")){for(;a.firstChild;)a.removeChild(a.firstChild);Element._getContentFromAnonymousElement(j,b.stripScripts(),!0).each(function(b){a.appendChild(b)})}else a.innerHTML=b.stripScripts();else a.innerHTML=b.stripScripts();b.evalScripts.bind(b).defer();return a}}(),replace:function(a,b){a=$(a);if(b&&b.toElement)b=b.toElement();else if(!Object.isElement(b)){var b=Object.toHTML(b),c=a.ownerDocument.createRange();c.selectNode(a);b.evalScripts.bind(b).defer();b=c.createContextualFragment(b.stripScripts())}a.parentNode.replaceChild(b,
a);return a},insert:function(a,b){a=$(a);if(Object.isString(b)||Object.isNumber(b)||Object.isElement(b)||b&&(b.toElement||b.toHTML))b={bottom:b};var c,d,e,f;for(f in b)c=b[f],f=f.toLowerCase(),d=Element._insertionTranslations[f],c&&c.toElement&&(c=c.toElement()),Object.isElement(c)?d(a,c):(c=Object.toHTML(c),e=("before"==f||"after"==f?a.parentNode:a).tagName.toUpperCase(),e=Element._getContentFromAnonymousElement(e,c.stripScripts()),("top"==f||"after"==f)&&e.reverse(),e.each(d.curry(a)),c.evalScripts.bind(c).defer());
return a},wrap:function(a,b,c){a=$(a);Object.isElement(b)?$(b).writeAttribute(c||{}):b=Object.isString(b)?new Element(b,c):new Element("div",b);a.parentNode&&a.parentNode.replaceChild(b,a);b.appendChild(a);return b},inspect:function(a){var a=$(a),b="<"+a.tagName.toLowerCase();$H({id:"id",className:"class"}).each(function(c){var d=c.first(),c=c.last();(d=(a[d]||"").toString())&&(b+=" "+c+"="+d.inspect(!0))});return b+">"},recursivelyCollect:function(a,b,c){for(var a=$(a),c=c||-1,d=[];(a=a[b])&&!(1==
a.nodeType&&d.push(Element.extend(a)),d.length==c););return d},ancestors:function(a){return Element.recursivelyCollect(a,"parentNode")},descendants:function(a){return Element.select(a,"*")},firstDescendant:function(a){for(a=$(a).firstChild;a&&1!=a.nodeType;)a=a.nextSibling;return $(a)},immediateDescendants:function(a){for(var b=[],a=$(a).firstChild;a;)1===a.nodeType&&b.push(Element.extend(a)),a=a.nextSibling;return b},previousSiblings:function(a){return Element.recursivelyCollect(a,"previousSibling")},
nextSiblings:function(a){return Element.recursivelyCollect(a,"nextSibling")},siblings:function(a){a=$(a);return Element.previousSiblings(a).reverse().concat(Element.nextSiblings(a))},match:function(a,b){a=$(a);return Object.isString(b)?Prototype.Selector.match(a,b):b.match(a)},up:function(a,b,c){a=$(a);if(1==arguments.length)return $(a.parentNode);var d=Element.ancestors(a);return Object.isNumber(b)?d[b]:Prototype.Selector.find(d,b,c)},down:function(a,b,c){a=$(a);return 1==arguments.length?Element.firstDescendant(a):
Object.isNumber(b)?Element.descendants(a)[b]:Element.select(a,b)[c||0]},previous:function(a,b,c){a=$(a);Object.isNumber(b)&&(c=b,b=!1);Object.isNumber(c)||(c=0);return b?Prototype.Selector.find(a.previousSiblings(),b,c):a.recursivelyCollect("previousSibling",c+1)[c]},next:function(a,b,c){a=$(a);Object.isNumber(b)&&(c=b,b=!1);Object.isNumber(c)||(c=0);if(b)return Prototype.Selector.find(a.nextSiblings(),b,c);Object.isNumber(c);return a.recursivelyCollect("nextSibling",c+1)[c]},select:function(a){var a=
$(a),b=Array.prototype.slice.call(arguments,1).join(", ");return Prototype.Selector.select(b,a)},adjacent:function(a){var a=$(a),b=Array.prototype.slice.call(arguments,1).join(", ");return Prototype.Selector.select(b,a.parentNode).without(a)},identify:function(a){var a=$(a),b=Element.readAttribute(a,"id");if(b)return b;do b="anonymous_element_"+Element.idCounter++;while($(b));Element.writeAttribute(a,"id",b);return b},readAttribute:function(a,b){a=$(a);if(Prototype.Browser.IE){var c=Element._attributeTranslations.read;
if(c.values[b])return c.values[b](a,b);c.names[b]&&(b=c.names[b]);if(b.include(":"))return!a.attributes||!a.attributes[b]?null:a.attributes[b].value}return a.getAttribute(b)},writeAttribute:function(a,b,c){var a=$(a),d={},e=Element._attributeTranslations.write;"object"==typeof b?d=b:d[b]=Object.isUndefined(c)?!0:c;for(var f in d)b=e.names[f]||f,c=d[f],e.values[f]&&(b=e.values[f](a,c)),!1===c||null===c?a.removeAttribute(b):!0===c?a.setAttribute(b,b):a.setAttribute(b,c);return a},getHeight:function(a){return Element.getDimensions(a).height},
getWidth:function(a){return Element.getDimensions(a).width},classNames:function(a){return new Element.ClassNames(a)},hasClassName:function(a,b){if(a=$(a)){var c=a.className;return 0<c.length&&(c==b||RegExp("(^|\\s)"+b+"(\\s|$)").test(c))}},addClassName:function(a,b){if(a=$(a))return Element.hasClassName(a,b)||(a.className+=(a.className?" ":"")+b),a},removeClassName:function(a,b){if(a=$(a))return a.className=a.className.replace(RegExp("(^|\\s+)"+b+"(\\s+|$)")," ").strip(),a},toggleClassName:function(a,
b){return!(a=$(a))?void 0:Element[Element.hasClassName(a,b)?"removeClassName":"addClassName"](a,b)},cleanWhitespace:function(a){for(var a=$(a),b=a.firstChild;b;){var c=b.nextSibling;3==b.nodeType&&!/\S/.test(b.nodeValue)&&a.removeChild(b);b=c}return a},empty:function(a){return $(a).innerHTML.blank()},descendantOf:function(a,b){a=$(a);b=$(b);if(a.compareDocumentPosition)return 8===(a.compareDocumentPosition(b)&8);if(b.contains)return b.contains(a)&&b!==a;for(;a=a.parentNode;)if(a==b)return!0;return!1},
scrollTo:function(a){var a=$(a),b=Element.cumulativeOffset(a);window.scrollTo(b[0],b[1]);return a},getStyle:function(a,b){var a=$(a),b="float"==b?"cssFloat":b.camelize(),c=a.style[b];if(!c||"auto"==c)c=(c=document.defaultView.getComputedStyle(a,null))?c[b]:null;return"opacity"==b?c?parseFloat(c):1:"auto"==c?null:c},getOpacity:function(a){return $(a).getStyle("opacity")},setStyle:function(a,b){var a=$(a),c=a.style;if(Object.isString(b))return a.style.cssText+=";"+b,b.include("opacity")?a.setOpacity(b.match(/opacity:\s*(\d?\.?\d*)/)[1]):
a;for(var d in b)"opacity"==d?a.setOpacity(b[d]):c["float"==d||"cssFloat"==d?Object.isUndefined(c.styleFloat)?"cssFloat":"styleFloat":d]=b[d];return a},setOpacity:function(a,b){a=$(a);a.style.opacity=1==b||""===b?"":1.0E-5>b?0:b;return a},makePositioned:function(a){var a=$(a),b=Element.getStyle(a,"position");if("static"==b||!b)if(a._madePositioned=!0,a.style.position="relative",Prototype.Browser.Opera)a.style.top=0,a.style.left=0;return a},undoPositioned:function(a){a=$(a);if(a._madePositioned)a._madePositioned=
void 0,a.style.position=a.style.top=a.style.left=a.style.bottom=a.style.right="";return a},makeClipping:function(a){a=$(a);if(a._overflow)return a;a._overflow=Element.getStyle(a,"overflow")||"auto";if("hidden"!==a._overflow)a.style.overflow="hidden";return a},undoClipping:function(a){a=$(a);if(!a._overflow)return a;a.style.overflow="auto"==a._overflow?"":a._overflow;a._overflow=null;return a},clonePosition:function(a,b,c){var c=Object.extend({setLeft:!0,setTop:!0,setWidth:!0,setHeight:!0,offsetTop:0,
offsetLeft:0},c||{}),b=$(b),d=Element.viewportOffset(b),e=[0,0],f=null,a=$(a);"absolute"==Element.getStyle(a,"position")&&(f=Element.getOffsetParent(a),e=Element.viewportOffset(f));f==document.body&&(e[0]-=document.body.offsetLeft,e[1]-=document.body.offsetTop);if(c.setLeft)a.style.left=d[0]-e[0]+c.offsetLeft+"px";if(c.setTop)a.style.top=d[1]-e[1]+c.offsetTop+"px";if(c.setWidth)a.style.width=b.offsetWidth+"px";if(c.setHeight)a.style.height=b.offsetHeight+"px";return a}};
Object.extend(Element.Methods,{getElementsBySelector:Element.Methods.select,childElements:Element.Methods.immediateDescendants});Element._attributeTranslations={write:{names:{className:"class",htmlFor:"for"},values:{}}};
if(Prototype.Browser.Opera)Element.Methods.getStyle=Element.Methods.getStyle.wrap(function(a,b,c){switch(c){case "height":case "width":if(!Element.visible(b))return null;var d=parseInt(a(b,c),10);return d!==b["offset"+c.capitalize()]?d+"px":("height"===c?["border-top-width","padding-top","padding-bottom","border-bottom-width"]:["border-left-width","padding-left","padding-right","border-right-width"]).inject(d,function(c,d){var g=a(b,d);return null===g?c:c-parseInt(g,10)})+"px";default:return a(b,
c)}}),Element.Methods.readAttribute=Element.Methods.readAttribute.wrap(function(a,b,c){return"title"===c?b.title:a(b,c)});else if(Prototype.Browser.IE)Element.Methods.getStyle=function(a,b){var a=$(a),b="float"==b||"cssFloat"==b?"styleFloat":b.camelize(),c=a.style[b];!c&&a.currentStyle&&(c=a.currentStyle[b]);return"opacity"==b?(c=(a.getStyle("filter")||"").match(/alpha\(opacity=(.*)\)/))&&c[1]?parseFloat(c[1])/100:1:"auto"==c?("width"==b||"height"==b)&&"none"!=a.getStyle("display")?a["offset"+b.capitalize()]+
"px":null:c},Element.Methods.setOpacity=function(a,b){var a=$(a),c=a.currentStyle;if(c&&!c.hasLayout||!c&&"normal"==a.style.zoom)a.style.zoom=1;var c=a.getStyle("filter"),d=a.style;if(1==b||""===b)return(c=c.replace(/alpha\([^\)]*\)/gi,""))?d.filter=c:d.removeAttribute("filter"),a;1.0E-5>b&&(b=0);d.filter=c.replace(/alpha\([^\)]*\)/gi,"")+"alpha(opacity="+100*b+")";return a},Element._attributeTranslations=function(){var a="className",b="for",c=document.createElement("div");c.setAttribute(a,"x");"x"!==
c.className&&(c.setAttribute("class","x"),"x"===c.className&&(a="class"));c=null;c=document.createElement("label");c.setAttribute(b,"x");"x"!==c.htmlFor&&(c.setAttribute("htmlFor","x"),"x"===c.htmlFor&&(b="htmlFor"));c=null;return{read:{names:{"class":a,className:a,"for":b,htmlFor:b},values:{_getAttr:function(a,b){return a.getAttribute(b)},_getAttr2:function(a,b){return a.getAttribute(b,2)},_getAttrNode:function(a,b){var c=a.getAttributeNode(b);return c?c.value:""},_getEv:function(){var a=document.createElement("div"),
b;a.onclick=Prototype.emptyFunction;a=a.getAttribute("onclick");-1<(""+a).indexOf("{")?b=function(a,b){b=a.getAttribute(b);if(!b)return null;b=b.toString();b=b.split("{")[1];b=b.split("}")[0];return b.strip()}:""===a&&(b=function(a,b){b=a.getAttribute(b);return!b?null:b.strip()});a=null;return b}(),_flag:function(a,b){return $(a).hasAttribute(b)?b:null},style:function(a){return a.style.cssText.toLowerCase()},title:function(a){return a.title}}}}}(),Element._attributeTranslations.write={names:Object.extend({cellpadding:"cellPadding",
cellspacing:"cellSpacing"},Element._attributeTranslations.read.names),values:{checked:function(a,b){a.checked=!!b},style:function(a,b){a.style.cssText=b?b:""}}},Element._attributeTranslations.has={},$w("colSpan rowSpan vAlign dateTime accessKey tabIndex encType maxLength readOnly longDesc frameBorder").each(function(a){Element._attributeTranslations.write.names[a.toLowerCase()]=a;Element._attributeTranslations.has[a.toLowerCase()]=a}),function(a){Object.extend(a,{href:a._getAttr2,src:a._getAttr2,
type:a._getAttr,action:a._getAttrNode,disabled:a._flag,checked:a._flag,readonly:a._flag,multiple:a._flag,onload:a._getEv,onunload:a._getEv,onclick:a._getEv,ondblclick:a._getEv,onmousedown:a._getEv,onmouseup:a._getEv,onmouseover:a._getEv,onmousemove:a._getEv,onmouseout:a._getEv,onfocus:a._getEv,onblur:a._getEv,onkeypress:a._getEv,onkeydown:a._getEv,onkeyup:a._getEv,onsubmit:a._getEv,onreset:a._getEv,onselect:a._getEv,onchange:a._getEv})}(Element._attributeTranslations.read.values),Prototype.BrowserFeatures.ElementExtensions&&
function(){Element.Methods.down=function(a,b,c){var a=$(a),d;if(1==arguments.length)d=a.firstDescendant();else if(Object.isNumber(b)){d=a.getElementsByTagName("*");for(var e=[],f=0,g;g=d[f];f++)"!"!==g.tagName&&e.push(g);d=e[b]}else d=Element.select(a,b)[c||0];return d}}();else if(Prototype.Browser.Gecko&&/rv:1\.8\.0/.test(navigator.userAgent))Element.Methods.setOpacity=function(a,b){a=$(a);a.style.opacity=1==b?0.999999:""===b?"":1.0E-5>b?0:b;return a};else if(Prototype.Browser.WebKit)Element.Methods.setOpacity=
function(a,b){a=$(a);a.style.opacity=1==b||""===b?"":1.0E-5>b?0:b;if(1==b)if("IMG"==a.tagName.toUpperCase()&&a.width)a.width++,a.width--;else try{var c=document.createTextNode(" ");a.appendChild(c);a.removeChild(c)}catch(d){}return a};
if("outerHTML"in document.documentElement)Element.Methods.replace=function(a,b){a=$(a);b&&b.toElement&&(b=b.toElement());if(Object.isElement(b))return a.parentNode.replaceChild(b,a),a;var b=Object.toHTML(b),c=a.parentNode,d=c.tagName.toUpperCase();if(Element._insertionTranslations.tags[d]){var e=a.next(),d=Element._getContentFromAnonymousElement(d,b.stripScripts());c.removeChild(a);e?d.each(function(a){c.insertBefore(a,e)}):d.each(function(a){c.appendChild(a)})}else a.outerHTML=b.stripScripts();b.evalScripts.bind(b).defer();
return a};Element._returnOffset=function(a,b){var c=[a,b];c.left=a;c.top=b;return c};Element._getContentFromAnonymousElement=function(a,b,c){var d=new Element("div"),a=Element._insertionTranslations.tags[a],e=!1;a?e=!0:c&&(e=!0,a=["","",0]);if(e){d.innerHTML="&nbsp;"+a[0]+b+a[1];d.removeChild(d.firstChild);for(b=a[2];b--;)d=d.firstChild}else d.innerHTML=b;return $A(d.childNodes)};
Element._insertionTranslations={before:function(a,b){a.parentNode.insertBefore(b,a)},top:function(a,b){a.insertBefore(b,a.firstChild)},bottom:function(a,b){a.appendChild(b)},after:function(a,b){a.parentNode.insertBefore(b,a.nextSibling)},tags:{TABLE:["<table>","</table>",1],TBODY:["<table><tbody>","</tbody></table>",2],TR:["<table><tbody><tr>","</tr></tbody></table>",3],TD:["<table><tbody><tr><td>","</td></tr></tbody></table>",4],SELECT:["<select>","</select>",1]}};
(function(){var a=Element._insertionTranslations.tags;Object.extend(a,{THEAD:a.TBODY,TFOOT:a.TBODY,TH:a.TD})})();Element.Methods.Simulated={hasAttribute:function(a,b){var b=Element._attributeTranslations.has[b]||b,c=$(a).getAttributeNode(b);return!(!c||!c.specified)}};Element.Methods.ByTag={};Object.extend(Element,Element.Methods);
(function(a){if(!Prototype.BrowserFeatures.ElementExtensions&&a.__proto__)window.HTMLElement={},window.HTMLElement.prototype=a.__proto__,Prototype.BrowserFeatures.ElementExtensions=!0})(document.createElement("div"));
Element.extend=function(){function a(a,b){for(var c in b){var d=b[c];Object.isFunction(d)&&!(c in a)&&(a[c]=d.methodize())}}var b=function(a){if("undefined"!=typeof window.Element){var b=window.Element.prototype;if(b){var c="_"+(Math.random()+"").slice(2),a=document.createElement(a);b[c]="x";a="x"!==a[c];delete b[c];return a}}return!1}("object");if(Prototype.BrowserFeatures.SpecificElementExtensions)return b?function(b){if(b&&"undefined"==typeof b._extendedByPrototype){var c=b.tagName;c&&/^(?:object|applet|embed)$/i.test(c)&&
(a(b,Element.Methods),a(b,Element.Methods.Simulated),a(b,Element.Methods.ByTag[c.toUpperCase()]))}return b}:Prototype.K;var c={},d=Element.Methods.ByTag,b=Object.extend(function(b){if(!b||"undefined"!=typeof b._extendedByPrototype||1!=b.nodeType||b==window)return b;var f=Object.clone(c),g=b.tagName.toUpperCase();d[g]&&Object.extend(f,d[g]);a(b,f);b._extendedByPrototype=Prototype.emptyFunction;return b},{refresh:function(){Prototype.BrowserFeatures.ElementExtensions||(Object.extend(c,Element.Methods),
Object.extend(c,Element.Methods.Simulated))}});b.refresh();return b}();Element.hasAttribute=document.documentElement.hasAttribute?function(a,b){return a.hasAttribute(b)}:Element.Methods.Simulated.hasAttribute;
Element.addMethods=function(a){function b(b){b=b.toUpperCase();Element.Methods.ByTag[b]||(Element.Methods.ByTag[b]={});Object.extend(Element.Methods.ByTag[b],a)}function c(a,b,c){var c=c||!1,d;for(d in a){var g=a[d];if(Object.isFunction(g)&&(!c||!(d in b)))b[d]=g.methodize()}}function d(a){var b,c={OPTGROUP:"OptGroup",TEXTAREA:"TextArea",P:"Paragraph",FIELDSET:"FieldSet",UL:"UList",OL:"OList",DL:"DList",DIR:"Directory",H1:"Heading",H2:"Heading",H3:"Heading",H4:"Heading",H5:"Heading",H6:"Heading",
Q:"Quote",INS:"Mod",DEL:"Mod",A:"Anchor",IMG:"Image",CAPTION:"TableCaption",COL:"TableCol",COLGROUP:"TableCol",THEAD:"TableSection",TFOOT:"TableSection",TBODY:"TableSection",TR:"TableRow",TH:"TableCell",TD:"TableCell",FRAMESET:"FrameSet",IFRAME:"IFrame"};c[a]&&(b="HTML"+c[a]+"Element");if(window[b])return window[b];b="HTML"+a+"Element";if(window[b])return window[b];b="HTML"+a.capitalize()+"Element";if(window[b])return window[b];a=document.createElement(a);return a.__proto__||a.constructor.prototype}
var e=Prototype.BrowserFeatures,f=Element.Methods.ByTag;a||(Object.extend(Form,Form.Methods),Object.extend(Form.Element,Form.Element.Methods),Object.extend(Element.Methods.ByTag,{FORM:Object.clone(Form.Methods),INPUT:Object.clone(Form.Element.Methods),SELECT:Object.clone(Form.Element.Methods),TEXTAREA:Object.clone(Form.Element.Methods),BUTTON:Object.clone(Form.Element.Methods)}));if(2==arguments.length)var g=a,a=arguments[1];g?Object.isArray(g)?g.each(b):b(g):Object.extend(Element.Methods,a||{});
g=window.HTMLElement?HTMLElement.prototype:Element.prototype;e.ElementExtensions&&(c(Element.Methods,g),c(Element.Methods.Simulated,g,!0));if(e.SpecificElementExtensions)for(var j in Element.Methods.ByTag)e=d(j),Object.isUndefined(e)||c(f[j],e.prototype);Object.extend(Element,Element.Methods);delete Element.ByTag;Element.extend.refresh&&Element.extend.refresh();Element.cache={}};
document.viewport={getDimensions:function(){return{width:this.getWidth(),height:this.getHeight()}},getScrollOffsets:function(){return Element._returnOffset(window.pageXOffset||document.documentElement.scrollLeft||document.body.scrollLeft,window.pageYOffset||document.documentElement.scrollTop||document.body.scrollTop)}};
(function(a){function b(b){e||(e=c.WebKit&&!d.evaluate?document:c.Opera&&9.5>window.parseFloat(window.opera.version())?document.body:document.documentElement);f[b]="client"+b;a["get"+b]=function(){return e[f[b]]};return a["get"+b]()}var c=Prototype.Browser,d=document,e,f={};a.getWidth=b.curry("Width");a.getHeight=b.curry("Height")})(document.viewport);Element.Storage={UID:1};
Element.addMethods({getStorage:function(a){if(a=$(a)){if(a===window)a=0;else{if("undefined"===typeof a._prototypeUID)a._prototypeUID=Element.Storage.UID++;a=a._prototypeUID}Element.Storage[a]||(Element.Storage[a]=$H());return Element.Storage[a]}},store:function(a,b,c){if(a=$(a))return 2===arguments.length?Element.getStorage(a).update(b):Element.getStorage(a).set(b,c),a},retrieve:function(a,b,c){if(a=$(a)){var a=Element.getStorage(a),d=a.get(b);Object.isUndefined(d)&&(a.set(b,c),d=c);return d}},clone:function(a,
b){if(a=$(a)){var c=a.cloneNode(b);c._prototypeUID=void 0;if(b)for(var d=Element.select(c,"*"),e=d.length;e--;)d[e]._prototypeUID=void 0;return Element.extend(c)}},purge:function(a){if(a=$(a)){var b=Element._purgeElement;b(a);for(var a=a.getElementsByTagName("*"),c=a.length;c--;)b(a[c]);return null}}});
(function(){function a(a,b,c){var d=null;Object.isElement(a)&&(d=a,a=d.getStyle(b));if(null===a)return null;if(/^(?:-)?\d+(\.\d+)?(px)?$/i.test(a))return window.parseFloat(a);var e=a.include("%"),f=c===document.viewport;if(/\d/.test(a)&&d&&d.runtimeStyle&&(!e||!f))return c=d.style.left,b=d.runtimeStyle.left,d.runtimeStyle.left=d.currentStyle.left,d.style.left=a||0,a=d.style.pixelLeft,d.style.left=c,d.runtimeStyle.left=b,a;return d&&e?(c=c||d.parentNode,a=a.match(/^(\d+)%?$/i),a=!a?null:Number(a[1])/
100,e=null,d.getStyle("position"),d=b.include("left")||b.include("right")||b.include("width"),b=b.include("top")||b.include("bottom")||b.include("height"),c===document.viewport?d?e=document.viewport.getWidth():b&&(e=document.viewport.getHeight()):d?e=$(c).measure("width"):b&&(e=$(c).measure("height")),null===e?0:e*a):0}function b(a){a=$(a);if(a.nodeType===Node.DOCUMENT_NODE||e(a)||"BODY"===a.nodeName.toUpperCase()||"HTML"===a.nodeName.toUpperCase())return $(document.body);if("inline"!==Element.getStyle(a,
"display")&&a.offsetParent)return $(a.offsetParent);for(;(a=a.parentNode)&&a!==document.body;)if("static"!==Element.getStyle(a,"position"))return"HTML"===a.nodeName.toUpperCase()?$(document.body):$(a);return $(document.body)}function c(a){var a=$(a),b=0,c=0;if(a.parentNode){do b+=a.offsetTop||0,c+=a.offsetLeft||0,a=a.offsetParent;while(a)}return new Element.Offset(c,b)}function d(a){var a=$(a),b=a.getLayout(),c=0,d=0;do if(c+=a.offsetTop||0,d+=a.offsetLeft||0,a=a.offsetParent){if("BODY"===a.nodeName.toUpperCase())break;
if("static"!==Element.getStyle(a,"position"))break}while(a);d-=b.get("margin-top");c-=b.get("margin-left");return new Element.Offset(d,c)}function e(a){return a!==document.body&&!Element.descendantOf(a,document.body)}var f=Prototype.K;"currentStyle"in document.documentElement&&(f=function(a){if(!a.currentStyle.hasLayout)a.style.zoom=1;return a});Element.Layout=Class.create(Hash,{initialize:function($super,a,b){$super();this.element=$(a);Element.Layout.PROPERTIES.each(function(a){this._set(a,null)},
this);if(b)this._preComputing=!0,this._begin(),Element.Layout.PROPERTIES.each(this._compute,this),this._end(),this._preComputing=!1},_set:function(a,b){return Hash.prototype.set.call(this,a,b)},set:function(){throw"Properties of Element.Layout are read-only.";},get:function($super,a){var b=$super(a);return null===b?this._compute(a):b},_begin:function(){if(!this._prepared){var b=this.element,c;a:{for(c=b;c&&c.parentNode;){if("none"===c.getStyle("display")){c=!1;break a}c=$(c.parentNode)}c=!0}if(!c){b.store("prototype_original_styles",
{position:b.style.position||"",width:b.style.width||"",visibility:b.style.visibility||"",display:b.style.display||""});c=b.getStyle("position");var d=b.getStyle("width");if("0px"===d||null===d)b.style.display="block",d=b.getStyle("width");var e="fixed"===c?document.viewport:b.parentNode;b.setStyle({position:"absolute",visibility:"hidden",display:"block"});var f=b.getStyle("width");c=d&&f===d?a(b,"width",e):"absolute"===c||"fixed"===c?a(b,"width",e):$(b.parentNode).getLayout().get("width")-this.get("margin-left")-
this.get("border-left")-this.get("padding-left")-this.get("padding-right")-this.get("border-right")-this.get("margin-right");b.setStyle({width:c+"px"})}this._prepared=!0}},_end:function(){var a=this.element,b=a.retrieve("prototype_original_styles");a.store("prototype_original_styles",null);a.setStyle(b);this._prepared=!1},_compute:function(a){var b=Element.Layout.COMPUTATIONS;if(!(a in b))throw"Property not found.";return this._set(a,b[a].call(this,this.element))},toObject:function(){var a=$A(arguments),
b={};(0===a.length?Element.Layout.PROPERTIES:a.join(" ").split(" ")).each(function(a){if(Element.Layout.PROPERTIES.include(a)){var c=this.get(a);null!=c&&(b[a]=c)}},this);return b},toHash:function(){var a=this.toObject.apply(this,arguments);return new Hash(a)},toCSS:function(){var a=$A(arguments),b={};(0===a.length?Element.Layout.PROPERTIES:a.join(" ").split(" ")).each(function(a){if(Element.Layout.PROPERTIES.include(a)&&!Element.Layout.COMPOSITE_PROPERTIES.include(a)){var c=this.get(a);if(null!=
c){var d=b;a.include("border")&&(a+="-width");a=a.camelize();d[a]=c+"px"}}},this);return b},inspect:function(){return"#<Element.Layout>"}});Object.extend(Element.Layout,{PROPERTIES:$w("height width top left right bottom border-left border-right border-top border-bottom padding-left padding-right padding-top padding-bottom margin-top margin-bottom margin-left margin-right padding-box-width padding-box-height border-box-width border-box-height margin-box-width margin-box-height"),COMPOSITE_PROPERTIES:$w("padding-box-width padding-box-height margin-box-width margin-box-height border-box-width border-box-height"),
COMPUTATIONS:{height:function(){this._preComputing||this._begin();var a=this.get("border-box-height");if(0>=a)return this._preComputing||this._end(),0;var b=this.get("border-top"),c=this.get("border-bottom"),d=this.get("padding-top"),e=this.get("padding-bottom");this._preComputing||this._end();return a-b-c-d-e},width:function(){this._preComputing||this._begin();var a=this.get("border-box-width");if(0>=a)return this._preComputing||this._end(),0;var b=this.get("border-left"),c=this.get("border-right"),
d=this.get("padding-left"),e=this.get("padding-right");this._preComputing||this._end();return a-b-c-d-e},"padding-box-height":function(){var a=this.get("height"),b=this.get("padding-top"),c=this.get("padding-bottom");return a+b+c},"padding-box-width":function(){var a=this.get("width"),b=this.get("padding-left"),c=this.get("padding-right");return a+b+c},"border-box-height":function(a){this._preComputing||this._begin();a=a.offsetHeight;this._preComputing||this._end();return a},"border-box-width":function(a){this._preComputing||
this._begin();a=a.offsetWidth;this._preComputing||this._end();return a},"margin-box-height":function(){var a=this.get("border-box-height"),b=this.get("margin-top"),c=this.get("margin-bottom");return 0>=a?0:a+b+c},"margin-box-width":function(){var a=this.get("border-box-width"),b=this.get("margin-left"),c=this.get("margin-right");return 0>=a?0:a+b+c},top:function(a){return a.positionedOffset().top},bottom:function(a){var b=a.positionedOffset(),a=a.getOffsetParent().measure("height"),c=this.get("border-box-height");
return a-c-b.top},left:function(a){return a.positionedOffset().left},right:function(a){var b=a.positionedOffset(),a=a.getOffsetParent().measure("width"),c=this.get("border-box-width");return a-c-b.left},"padding-top":function(b){return a(b,"paddingTop")},"padding-bottom":function(b){return a(b,"paddingBottom")},"padding-left":function(b){return a(b,"paddingLeft")},"padding-right":function(b){return a(b,"paddingRight")},"border-top":function(b){return a(b,"borderTopWidth")},"border-bottom":function(b){return a(b,
"borderBottomWidth")},"border-left":function(b){return a(b,"borderLeftWidth")},"border-right":function(b){return a(b,"borderRightWidth")},"margin-top":function(b){return a(b,"marginTop")},"margin-bottom":function(b){return a(b,"marginBottom")},"margin-left":function(b){return a(b,"marginLeft")},"margin-right":function(b){return a(b,"marginRight")}}});"getBoundingClientRect"in document.documentElement&&Object.extend(Element.Layout.COMPUTATIONS,{right:function(a){var b=f(a.getOffsetParent()),a=a.getBoundingClientRect();
return(b.getBoundingClientRect().right-a.right).round()},bottom:function(a){var b=f(a.getOffsetParent()),a=a.getBoundingClientRect();return(b.getBoundingClientRect().bottom-a.bottom).round()}});Element.Offset=Class.create({initialize:function(a,b){this.left=a.round();this.top=b.round();this[0]=this.left;this[1]=this.top},relativeTo:function(a){return new Element.Offset(this.left-a.left,this.top-a.top)},inspect:function(){return"#<Element.Offset left: #{left} top: #{top}>".interpolate(this)},toString:function(){return"[#{left}, #{top}]".interpolate(this)},
toArray:function(){return[this.left,this.top]}});Prototype.Browser.IE?(b=b.wrap(function(a,b){b=$(b);if(b.nodeType===Node.DOCUMENT_NODE||e(b)||"BODY"===b.nodeName.toUpperCase()||"HTML"===b.nodeName.toUpperCase())return $(document.body);var c=b.getStyle("position");if("static"!==c)return a(b);b.setStyle({position:"relative"});var d=a(b);b.setStyle({position:c});return d}),d=d.wrap(function(a,b){b=$(b);if(!b.parentNode)return new Element.Offset(0,0);var c=b.getStyle("position");if("static"!==c)return a(b);
var d=b.getOffsetParent();d&&"fixed"===d.getStyle("position")&&f(d);b.setStyle({position:"relative"});d=a(b);b.setStyle({position:c});return d})):Prototype.Browser.Webkit&&(c=function(a){var a=$(a),b=0,c=0;do{b+=a.offsetTop||0;c+=a.offsetLeft||0;if(a.offsetParent==document.body&&"absolute"==Element.getStyle(a,"position"))break;a=a.offsetParent}while(a);return new Element.Offset(c,b)});Element.addMethods({getLayout:function(a,b){return new Element.Layout(a,b)},measure:function(a,b){return $(a).getLayout().get(b)},
getDimensions:function(a){var a=$(a),b=Element.getStyle(a,"display");if(b&&"none"!==b)return{width:a.offsetWidth,height:a.offsetHeight};var b=a.style,b={visibility:b.visibility,position:b.position,display:b.display},c={visibility:"hidden",display:"block"};if("fixed"!==b.position)c.position="absolute";Element.setStyle(a,c);c={width:a.offsetWidth,height:a.offsetHeight};Element.setStyle(a,b);return c},getOffsetParent:b,cumulativeOffset:c,positionedOffset:d,cumulativeScrollOffset:function(a){var b=0,
c=0;do b+=a.scrollTop||0,c+=a.scrollLeft||0,a=a.parentNode;while(a);return new Element.Offset(c,b)},viewportOffset:function(a){$(e);var b=0,c=0,d=document.body,e=a;do if(b+=e.offsetTop||0,c+=e.offsetLeft||0,e.offsetParent==d&&"absolute"==Element.getStyle(e,"position"))break;while(e=e.offsetParent);e=a;do e!=d&&(b-=e.scrollTop||0,c-=e.scrollLeft||0);while(e=e.parentNode);return new Element.Offset(c,b)},absolutize:function(a){a=$(a);if("absolute"===Element.getStyle(a,"position"))return a;var c=b(a),
d=a.viewportOffset(),c=c.viewportOffset(),d=d.relativeTo(c),c=a.getLayout();a.store("prototype_absolutize_original_styles",{left:a.getStyle("left"),top:a.getStyle("top"),width:a.getStyle("width"),height:a.getStyle("height")});a.setStyle({position:"absolute",top:d.top+"px",left:d.left+"px",width:c.get("width")+"px",height:c.get("height")+"px"});return a},relativize:function(a){a=$(a);if("relative"===Element.getStyle(a,"position"))return a;var b=a.retrieve("prototype_absolutize_original_styles");b&&
a.setStyle(b);return a}});"getBoundingClientRect"in document.documentElement&&Element.addMethods({viewportOffset:function(a){a=$(a);if(e(a))return new Element.Offset(0,0);var a=a.getBoundingClientRect(),b=document.documentElement;return new Element.Offset(a.left-b.clientLeft,a.top-b.clientTop)}})})();window.$$=function(){var a=$A(arguments).join(", ");return Prototype.Selector.select(a,document)};
Prototype.Selector=function(){function a(a){for(var b=0,e=a.length;b<e;b++)Element.extend(a[b]);return a}var b=Prototype.K;return{select:function(){throw Error('Method "Prototype.Selector.select" must be defined.');},match:function(){throw Error('Method "Prototype.Selector.match" must be defined.');},find:function(a,b,e){var e=e||0,f=Prototype.Selector.match,g=a.length,j=0,h;for(h=0;h<g;h++)if(f(a[h],b)&&e==j++)return Element.extend(a[h])},extendElements:Element.extend===b?b:a,extendElement:Element.extend}}();
Prototype._original_property=window.Sizzle;
(function(){function a(a,b,c,d,e,f){for(var e="previousSibling"==a&&!f,k=0,g=d.length;k<g;k++){var h=d[k];if(h){if(e&&1===h.nodeType)h.sizcache=c,h.sizset=k;for(var h=h[a],j=!1;h;){if(h.sizcache===c){j=d[h.sizset];break}if(1===h.nodeType&&!f)h.sizcache=c,h.sizset=k;if(h.nodeName===b){j=h;break}h=h[a]}d[k]=j}}}function b(a,b,c,d,e,f){for(var e="previousSibling"==a&&!f,k=0,g=d.length;k<g;k++){var h=d[k];if(h){if(e&&1===h.nodeType)h.sizcache=c,h.sizset=k;for(var h=h[a],m=!1;h;){if(h.sizcache===c){m=
d[h.sizset];break}if(1===h.nodeType){if(!f)h.sizcache=c,h.sizset=k;if("string"!==typeof b){if(h===b){m=!0;break}}else if(0<j.filter(b,[h]).length){m=h;break}}h=h[a]}d[k]=m}}}var c=/((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^[\]]*\]|['"][^'"]*['"]|[^[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?((?:.|\r|\n)*)/g,d=0,e=Object.prototype.toString,f=!1,g=!0;[0,0].sort(function(){g=!1;return 0});var j=function(a,b,d,f){var d=d||[],g=b=b||document;if(1!==b.nodeType&&9!==b.nodeType)return[];if(!a||"string"!==
typeof a)return d;for(var n=[],k,u,p,o,E=!0,B=s(b),v=a;null!==(c.exec(""),k=c.exec(v));)if(v=k[3],n.push(k[1]),k[2]){o=k[3];break}if(1<n.length&&m.exec(a))if(2===n.length&&h.relative[n[0]])u=w(n[0]+n[1],b);else for(u=h.relative[n[0]]?[b]:j(n.shift(),b);n.length;)a=n.shift(),h.relative[a]&&(a+=n.shift()),u=w(a,u);else if(!f&&1<n.length&&9===b.nodeType&&!B&&h.match.ID.test(n[0])&&!h.match.ID.test(n[n.length-1])&&(k=j.find(n.shift(),b,B),b=k.expr?j.filter(k.expr,k.set)[0]:k.set[0]),b){k=f?{expr:n.pop(),
set:l(f)}:j.find(n.pop(),1===n.length&&("~"===n[0]||"+"===n[0])&&b.parentNode?b.parentNode:b,B);u=k.expr?j.filter(k.expr,k.set):k.set;for(0<n.length?p=l(u):E=!1;n.length;){var t=n.pop();k=t;h.relative[t]?k=n.pop():t="";null==k&&(k=b);h.relative[t](p,k,B)}}else p=[];p||(p=u);if(!p)throw"Syntax error, unrecognized expression: "+(t||a);if("[object Array]"===e.call(p))if(E)if(b&&1===b.nodeType)for(a=0;null!=p[a];a++)p[a]&&(!0===p[a]||1===p[a].nodeType&&r(b,p[a]))&&d.push(u[a]);else for(a=0;null!=p[a];a++)p[a]&&
1===p[a].nodeType&&d.push(u[a]);else d.push.apply(d,p);else l(p,d);o&&(j(o,g,d,f),j.uniqueSort(d));return d};j.uniqueSort=function(a){if(q&&(f=g,a.sort(q),f))for(var b=1;b<a.length;b++)a[b]===a[b-1]&&a.splice(b--,1);return a};j.matches=function(a,b){return j(a,null,null,b)};j.find=function(a,b,c){var d,e;if(!a)return[];for(var f=0,k=h.order.length;f<k;f++){var g=h.order[f];if(e=h.leftMatch[g].exec(a)){var j=e[1];e.splice(1,1);if("\\"!==j.substr(j.length-1)&&(e[1]=(e[1]||"").replace(/\\/g,""),d=h.find[g](e,
b,c),null!=d)){a=a.replace(h.match[g],"");break}}}d||(d=b.getElementsByTagName("*"));return{set:d,expr:a}};j.filter=function(a,b,c,d){for(var e=a,f=[],k=b,g,j,m=b&&b[0]&&s(b[0]);a&&b.length;){for(var l in h.filter)if(null!=(g=h.match[l].exec(a))){var o=h.filter[l],v,t;j=!1;k==f&&(f=[]);if(h.preFilter[l])if(g=h.preFilter[l](g,k,c,f,d,m)){if(!0===g)continue}else j=v=!0;if(g)for(var q=0;null!=(t=k[q]);q++)if(t){v=o(t,g,q,k);var r=d^!!v;c&&null!=v?r?j=!0:k[q]=!1:r&&(f.push(t),j=!0)}if(void 0!==v){c||
(k=f);a=a.replace(h.match[l],"");if(!j)return[];break}}if(a==e){if(null==j)throw"Syntax error, unrecognized expression: "+a;break}e=a}return k};var h=j.selectors={order:["ID","NAME","TAG"],match:{ID:/#((?:[\w\u00c0-\uFFFF-]|\\.)+)/,CLASS:/\.((?:[\w\u00c0-\uFFFF-]|\\.)+)/,NAME:/\[name=['"]*((?:[\w\u00c0-\uFFFF-]|\\.)+)['"]*\]/,ATTR:/\[\s*((?:[\w\u00c0-\uFFFF-]|\\.)+)\s*(?:(\S?=)\s*(['"]*)(.*?)\3|)\s*\]/,TAG:/^((?:[\w\u00c0-\uFFFF\*-]|\\.)+)/,CHILD:/:(only|nth|last|first)-child(?:\((even|odd|[\dn+-]*)\))?/,
POS:/:(nth|eq|gt|lt|first|last|even|odd)(?:\((\d*)\))?(?=[^-]|$)/,PSEUDO:/:((?:[\w\u00c0-\uFFFF-]|\\.)+)(?:\((['"]*)((?:\([^\)]+\)|[^\2\(\)]*)+)\2\))?/},leftMatch:{},attrMap:{"class":"className","for":"htmlFor"},attrHandle:{href:function(a){return a.getAttribute("href")}},relative:{"+":function(a,b,c){var d="string"===typeof b,e=d&&!/\W/.test(b),d=d&&!e;e&&!c&&(b=b.toUpperCase());for(var c=0,e=a.length,f;c<e;c++)if(f=a[c]){for(;(f=f.previousSibling)&&1!==f.nodeType;);a[c]=d||f&&f.nodeName===b?f||
!1:f===b}d&&j.filter(b,a,!0)},">":function(a,b,c){var d="string"===typeof b;if(d&&!/\W/.test(b))for(var b=c?b:b.toUpperCase(),c=0,e=a.length;c<e;c++){var f=a[c];if(f)d=f.parentNode,a[c]=d.nodeName===b?d:!1}else{c=0;for(e=a.length;c<e;c++)(f=a[c])&&(a[c]=d?f.parentNode:f.parentNode===b);d&&j.filter(b,a,!0)}},"":function(c,e,f){var g=d++,h=b;if(!/\W/.test(e))var j=e=f?e:e.toUpperCase(),h=a;h("parentNode",e,g,c,j,f)},"~":function(c,e,f){var g=d++,h=b;if("string"===typeof e&&!/\W/.test(e))var j=e=f?e:
e.toUpperCase(),h=a;h("previousSibling",e,g,c,j,f)}},find:{ID:function(a,b,c){if("undefined"!==typeof b.getElementById&&!c)return(a=b.getElementById(a[1]))?[a]:[]},NAME:function(a,b){if("undefined"!==typeof b.getElementsByName){for(var c=[],d=b.getElementsByName(a[1]),e=0,f=d.length;e<f;e++)d[e].getAttribute("name")===a[1]&&c.push(d[e]);return 0===c.length?null:c}},TAG:function(a,b){return b.getElementsByTagName(a[1])}},preFilter:{CLASS:function(a,b,c,d,e,f){a=" "+a[1].replace(/\\/g,"")+" ";if(f)return a;
for(var f=0,g;null!=(g=b[f]);f++)g&&(e^(g.className&&0<=(" "+g.className+" ").indexOf(a))?c||d.push(g):c&&(b[f]=!1));return!1},ID:function(a){return a[1].replace(/\\/g,"")},TAG:function(a,b){for(var c=0;!1===b[c];c++);return b[c]&&s(b[c])?a[1]:a[1].toUpperCase()},CHILD:function(a){if("nth"==a[1]){var b=/(-?)(\d*)n((?:\+|-)?\d*)/.exec("even"==a[2]&&"2n"||"odd"==a[2]&&"2n+1"||!/\D/.test(a[2])&&"0n+"+a[2]||a[2]);a[2]=b[1]+(b[2]||1)-0;a[3]=b[3]-0}a[0]=d++;return a},ATTR:function(a,b,c,d,e,f){b=a[1].replace(/\\/g,
"");!f&&h.attrMap[b]&&(a[1]=h.attrMap[b]);"~="===a[2]&&(a[4]=" "+a[4]+" ");return a},PSEUDO:function(a,b,d,e,f){if("not"===a[1])if(1<(c.exec(a[3])||"").length||/^\w/.test(a[3]))a[3]=j(a[3],null,null,b);else return a=j.filter(a[3],b,d,1^f),d||e.push.apply(e,a),!1;else if(h.match.POS.test(a[0])||h.match.CHILD.test(a[0]))return!0;return a},POS:function(a){a.unshift(!0);return a}},filters:{enabled:function(a){return!1===a.disabled&&"hidden"!==a.type},disabled:function(a){return!0===a.disabled},checked:function(a){return!0===
a.checked},selected:function(a){return!0===a.selected},parent:function(a){return!!a.firstChild},empty:function(a){return!a.firstChild},has:function(a,b,c){return!!j(c[3],a).length},header:function(a){return/h\d/i.test(a.nodeName)},text:function(a){return"text"===a.type},radio:function(a){return"radio"===a.type},checkbox:function(a){return"checkbox"===a.type},file:function(a){return"file"===a.type},password:function(a){return"password"===a.type},submit:function(a){return"submit"===a.type},image:function(a){return"image"===
a.type},reset:function(a){return"reset"===a.type},button:function(a){return"button"===a.type||"BUTTON"===a.nodeName.toUpperCase()},input:function(a){return/input|select|textarea|button/i.test(a.nodeName)}},setFilters:{first:function(a,b){return 0===b},last:function(a,b,c,d){return b===d.length-1},even:function(a,b){return 0===b%2},odd:function(a,b){return 1===b%2},lt:function(a,b,c){return b<c[3]-0},gt:function(a,b,c){return b>c[3]-0},nth:function(a,b,c){return c[3]-0==b},eq:function(a,b,c){return c[3]-
0==b}},filter:{PSEUDO:function(a,b,c,d){var e=b[1],f=h.filters[e];if(f)return f(a,c,b,d);if("contains"===e)return 0<=(a.textContent||a.innerText||"").indexOf(b[3]);if("not"===e){b=b[3];c=0;for(d=b.length;c<d;c++)if(b[c]===a)return!1;return!0}},CHILD:function(a,b){var c=b[1],d=a;switch(c){case "only":case "first":for(;d=d.previousSibling;)if(1===d.nodeType)return!1;if("first"==c)return!0;d=a;case "last":for(;d=d.nextSibling;)if(1===d.nodeType)return!1;return!0;case "nth":var c=b[2],e=b[3];if(1==c&&
0==e)return!0;var f=b[0],g=a.parentNode;if(g&&(g.sizcache!==f||!a.nodeIndex)){for(var h=0,d=g.firstChild;d;d=d.nextSibling)if(1===d.nodeType)d.nodeIndex=++h;g.sizcache=f}d=a.nodeIndex-e;return 0==c?0==d:0==d%c&&0<=d/c}},ID:function(a,b){return 1===a.nodeType&&a.getAttribute("id")===b},TAG:function(a,b){return"*"===b&&1===a.nodeType||a.nodeName===b},CLASS:function(a,b){return-1<(" "+(a.className||a.getAttribute("class"))+" ").indexOf(b)},ATTR:function(a,b){var c=b[1],c=h.attrHandle[c]?h.attrHandle[c](a):
null!=a[c]?a[c]:a.getAttribute(c),d=c+"",e=b[2],f=b[4];return null==c?"!="===e:"="===e?d===f:"*="===e?0<=d.indexOf(f):"~="===e?0<=(" "+d+" ").indexOf(f):!f?d&&!1!==c:"!="===e?d!=f:"^="===e?0===d.indexOf(f):"$="===e?d.substr(d.length-f.length)===f:"|="===e?d===f||d.substr(0,f.length+1)===f+"-":!1},POS:function(a,b,c,d){var e=h.setFilters[b[2]];if(e)return e(a,c,b,d)}}},m=h.match.POS,o;for(o in h.match)h.match[o]=RegExp(h.match[o].source+/(?![^\[]*\])(?![^\(]*\))/.source),h.leftMatch[o]=RegExp(/(^(?:.|\r|\n)*?)/.source+
h.match[o].source);var l=function(a,b){a=Array.prototype.slice.call(a,0);return b?(b.push.apply(b,a),b):a};try{Array.prototype.slice.call(document.documentElement.childNodes,0)}catch(x){l=function(a,b){var c=b||[];if("[object Array]"===e.call(a))Array.prototype.push.apply(c,a);else if("number"===typeof a.length)for(var d=0,f=a.length;d<f;d++)c.push(a[d]);else for(d=0;a[d];d++)c.push(a[d]);return c}}var q;document.documentElement.compareDocumentPosition?q=function(a,b){if(!a.compareDocumentPosition||
!b.compareDocumentPosition)return a==b&&(f=!0),0;var c=a.compareDocumentPosition(b)&4?-1:a===b?0:1;0===c&&(f=!0);return c}:"sourceIndex"in document.documentElement?q=function(a,b){if(!a.sourceIndex||!b.sourceIndex)return a==b&&(f=!0),0;var c=a.sourceIndex-b.sourceIndex;0===c&&(f=!0);return c}:document.createRange&&(q=function(a,b){if(!a.ownerDocument||!b.ownerDocument)return a==b&&(f=!0),0;var c=a.ownerDocument.createRange(),d=b.ownerDocument.createRange();c.setStart(a,0);c.setEnd(a,0);d.setStart(b,
0);d.setEnd(b,0);c=c.compareBoundaryPoints(Range.START_TO_END,d);0===c&&(f=!0);return c});(function(){var a=document.createElement("div"),b="script"+(new Date).getTime();a.innerHTML="<a name='"+b+"'/>";var c=document.documentElement;c.insertBefore(a,c.firstChild);if(document.getElementById(b))h.find.ID=function(a,b,c){if("undefined"!==typeof b.getElementById&&!c)return(b=b.getElementById(a[1]))?b.id===a[1]||"undefined"!==typeof b.getAttributeNode&&b.getAttributeNode("id").nodeValue===a[1]?[b]:void 0:
[]},h.filter.ID=function(a,b){var c="undefined"!==typeof a.getAttributeNode&&a.getAttributeNode("id");return 1===a.nodeType&&c&&c.nodeValue===b};c.removeChild(a);c=a=null})();(function(){var a=document.createElement("div");a.appendChild(document.createComment(""));if(0<a.getElementsByTagName("*").length)h.find.TAG=function(a,b){var c=b.getElementsByTagName(a[1]);if("*"===a[1]){for(var d=[],e=0;c[e];e++)1===c[e].nodeType&&d.push(c[e]);c=d}return c};a.innerHTML="<a href='#'></a>";if(a.firstChild&&"undefined"!==
typeof a.firstChild.getAttribute&&"#"!==a.firstChild.getAttribute("href"))h.attrHandle.href=function(a){return a.getAttribute("href",2)};a=null})();document.querySelectorAll&&function(){var a=j,b=document.createElement("div");b.innerHTML="<p class='TEST'></p>";if(!(b.querySelectorAll&&0===b.querySelectorAll(".TEST").length)){j=function(b,c,d,e){c=c||document;if(!e&&9===c.nodeType&&!s(c))try{return l(c.querySelectorAll(b),d)}catch(f){}return a(b,c,d,e)};for(var c in a)j[c]=a[c];b=null}}();document.getElementsByClassName&&
document.documentElement.getElementsByClassName&&function(){var a=document.createElement("div");a.innerHTML="<div class='test e'></div><div class='test'></div>";if(0!==a.getElementsByClassName("e").length&&(a.lastChild.className="e",1!==a.getElementsByClassName("e").length))h.order.splice(1,0,"CLASS"),h.find.CLASS=function(a,b,c){if("undefined"!==typeof b.getElementsByClassName&&!c)return b.getElementsByClassName(a[1])},a=null}();var r=document.compareDocumentPosition?function(a,b){return a.compareDocumentPosition(b)&
16}:function(a,b){return a!==b&&(a.contains?a.contains(b):!0)},s=function(a){return 9===a.nodeType&&"HTML"!==a.documentElement.nodeName||!!a.ownerDocument&&"HTML"!==a.ownerDocument.documentElement.nodeName},w=function(a,b){for(var c=[],d="",e,f=b.nodeType?[b]:b;e=h.match.PSEUDO.exec(a);)d+=e[0],a=a.replace(h.match.PSEUDO,"");a=h.relative[a]?a+"*":a;e=0;for(var g=f.length;e<g;e++)j(a,f[e],c);return j.filter(d,c)};window.Sizzle=j})();
(function(a){var b=Prototype.Selector.extendElements;Prototype.Selector.engine=a;Prototype.Selector.select=function(c,d){return b(a(c,d||document))};Prototype.Selector.match=function(b,d){return 1==a.matches(d,[b]).length}})(Sizzle);window.Sizzle=Prototype._original_property;delete Prototype._original_property;
var Form={reset:function(a){a=$(a);a.reset();return a},serializeElements:function(a,b){if("object"!=typeof b)b={hash:!!b};else if(Object.isUndefined(b.hash))b.hash=!0;var c,d,e=!1,f=b.submit,g,j;b.hash?(j={},g=function(a,b,c){b in a?(Object.isArray(a[b])||(a[b]=[a[b]]),a[b].push(c)):a[b]=c;return a}):(j="",g=function(a,b,c){return a+(a?"&":"")+encodeURIComponent(b)+"="+encodeURIComponent(c)});return a.inject(j,function(a,b){if(!b.disabled&&b.name&&(c=b.name,d=$(b).getValue(),null!=d&&"file"!=b.type&&
("submit"!=b.type||!e&&!1!==f&&(!f||c==f)&&(e=!0))))a=g(a,c,d);return a})}};
Form.Methods={serialize:function(a,b){return Form.serializeElements(Form.getElements(a),b)},getElements:function(a){for(var a=$(a).getElementsByTagName("*"),b,c=[],d=Form.Element.Serializers,e=0;b=a[e];e++)c.push(b);return c.inject([],function(a,b){d[b.tagName.toLowerCase()]&&a.push(Element.extend(b));return a})},getInputs:function(a,b,c){a=$(a);a=a.getElementsByTagName("input");if(!b&&!c)return $A(a).map(Element.extend);for(var d=0,e=[],f=a.length;d<f;d++){var g=a[d];b&&g.type!=b||c&&g.name!=c||
e.push(Element.extend(g))}return e},disable:function(a){a=$(a);Form.getElements(a).invoke("disable");return a},enable:function(a){a=$(a);Form.getElements(a).invoke("enable");return a},findFirstElement:function(a){var a=$(a).getElements().findAll(function(a){return"hidden"!=a.type&&!a.disabled}),b=a.findAll(function(a){return a.hasAttribute("tabIndex")&&0<=a.tabIndex}).sortBy(function(a){return a.tabIndex}).first();return b?b:a.find(function(a){return/^(?:input|select|textarea)$/i.test(a.tagName)})},
focusFirstElement:function(a){var a=$(a),b=a.findFirstElement();b&&b.activate();return a},request:function(a,b){a=$(a);b=Object.clone(b||{});var c=b.parameters,d=a.readAttribute("action")||"";if(d.blank())d=window.location.href;b.parameters=a.serialize(!0);c&&(Object.isString(c)&&(c=c.toQueryParams()),Object.extend(b.parameters,c));if(a.hasAttribute("method")&&!b.method)b.method=a.method;return new Ajax.Request(d,b)}};
Form.Element={focus:function(a){$(a).focus();return a},select:function(a){$(a).select();return a}};
Form.Element.Methods={serialize:function(a){a=$(a);if(!a.disabled&&a.name){var b=a.getValue();if(void 0!=b){var c={};c[a.name]=b;return Object.toQueryString(c)}}return""},getValue:function(a){var a=$(a),b=a.tagName.toLowerCase();return Form.Element.Serializers[b](a)},setValue:function(a,b){var a=$(a),c=a.tagName.toLowerCase();Form.Element.Serializers[c](a,b);return a},clear:function(a){$(a).value="";return a},present:function(a){return""!=$(a).value},activate:function(a){a=$(a);try{a.focus(),a.select&&
("input"!=a.tagName.toLowerCase()||!/^(?:button|reset|submit)$/i.test(a.type))&&a.select()}catch(b){}return a},disable:function(a){a=$(a);a.disabled=!0;return a},enable:function(a){a=$(a);a.disabled=!1;return a}};var Field=Form.Element,$F=Form.Element.Methods.getValue;
Form.Element.Serializers=function(){function a(a,b){if(Object.isUndefined(b))return a.checked?a.value:null;a.checked=!!b}function b(a,b){if(Object.isUndefined(b))return a.value;a.value=b}function c(a){var b=a.selectedIndex;return 0<=b?e(a.options[b]):null}function d(a){var b,c=a.length;if(!c)return null;var d=0;for(b=[];d<c;d++){var m=a.options[d];m.selected&&b.push(e(m))}return b}function e(a){return Element.hasAttribute(a,"value")?a.value:a.text}return{input:function(c,d){switch(c.type.toLowerCase()){case "checkbox":case "radio":return a(c,
d);default:return b(c,d)}},inputSelector:a,textarea:b,select:function(a,b){if(Object.isUndefined(b))return("select-one"===a.type?c:d)(a);for(var e,h,m=!Object.isArray(b),o=0,l=a.length;o<l;o++)if(e=a.options[o],h=this.optionValue(e),m){if(h==b){e.selected=!0;break}}else e.selected=b.include(h)},selectOne:c,selectMany:d,optionValue:e,button:b}}();
Abstract.TimedObserver=Class.create(PeriodicalExecuter,{initialize:function($super,b,c,d){$super(d,c);this.element=$(b);this.lastValue=this.getValue()},execute:function(){var a=this.getValue();if(Object.isString(this.lastValue)&&Object.isString(a)?this.lastValue!=a:""+this.lastValue!=""+a)this.callback(this.element,a),this.lastValue=a}});Form.Element.Observer=Class.create(Abstract.TimedObserver,{getValue:function(){return Form.Element.getValue(this.element)}});
Form.Observer=Class.create(Abstract.TimedObserver,{getValue:function(){return Form.serialize(this.element)}});
Abstract.EventObserver=Class.create({initialize:function(a,b){this.element=$(a);this.callback=b;this.lastValue=this.getValue();"form"==this.element.tagName.toLowerCase()?this.registerFormCallbacks():this.registerCallback(this.element)},onElementEvent:function(){var a=this.getValue();if(this.lastValue!=a)this.callback(this.element,a),this.lastValue=a},registerFormCallbacks:function(){Form.getElements(this.element).each(this.registerCallback,this)},registerCallback:function(a){if(a.type)switch(a.type.toLowerCase()){case "checkbox":case "radio":Event.observe(a,
"click",this.onElementEvent.bind(this));break;default:Event.observe(a,"change",this.onElementEvent.bind(this))}}});Form.Element.EventObserver=Class.create(Abstract.EventObserver,{getValue:function(){return Form.Element.getValue(this.element)}});Form.EventObserver=Class.create(Abstract.EventObserver,{getValue:function(){return Form.serialize(this.element)}});
(function(){function a(a,b){return a.which?a.which===b+1:a.button===b}function b(a,b){return a.button===w[b]}function c(a,b){switch(b){case 0:return 1==a.which&&!a.metaKey;case 1:return 2==a.which||1==a.which&&a.metaKey;case 2:return 3==a.which;default:return!1}}function d(a){var b=document.documentElement,c=document.body||{scrollLeft:0};return a.pageX||a.clientX+(b.scrollLeft||c.scrollLeft)-(b.clientLeft||0)}function e(a){var b=document.documentElement,c=document.body||{scrollTop:0};return a.pageY||
a.clientY+(b.scrollTop||c.scrollTop)-(b.clientTop||0)}function f(a,b,c){var d=Element.retrieve(a,"prototype_event_registry");Object.isUndefined(d)&&(z.push(a),d=Element.retrieve(a,"prototype_event_registry",$H()));var e=d.get(b);Object.isUndefined(e)&&(e=[],d.set(b,e));if(e.pluck("handler").include(c))return!1;var f;if(b.include(":"))f=function(d){if(Object.isUndefined(d.eventName)||d.eventName!==b)return!1;l.extend(d,a);c.call(a,d)};else if(!q&&("mouseenter"===b||"mouseleave"===b)){if("mouseenter"===
b||"mouseleave"===b)f=function(b){l.extend(b,a);for(var d=b.relatedTarget;d&&d!==a;)try{d=d.parentNode}catch(e){d=a}d!==a&&c.call(a,b)}}else f=function(b){l.extend(b,a);c.call(a,b)};f.handler=c;e.push(f);return f}function g(){for(var a=0,b=z.length;a<b;a++)l.stopObserving(z[a]),z[a]=null}function j(a,b,c){a=$(a);c=f(a,b,c);if(!c)return a;b.include(":")?a.addEventListener?a.addEventListener("dataavailable",c,!1):(a.attachEvent("ondataavailable",c),a.attachEvent("onlosecapture",c)):(b=C(b),a.addEventListener?
a.addEventListener(b,c,!1):a.attachEvent("on"+b,c));return a}function h(a,b,c){var a=$(a),d=Element.retrieve(a,"prototype_event_registry");if(!d)return a;if(!b)return d.each(function(b){h(a,b.key)}),a;var e=d.get(b);if(!e)return a;if(!c)return e.each(function(c){h(a,b,c.handler)}),a;for(var f=e.length,g;f--;)if(e[f].handler===c){g=e[f];break}if(!g)return a;b.include(":")?a.removeEventListener?a.removeEventListener("dataavailable",g,!1):(a.detachEvent("ondataavailable",g),a.detachEvent("onlosecapture",
g)):(c=C(b),a.removeEventListener?a.removeEventListener(c,g,!1):a.detachEvent("on"+c,g));d.set(b,e.without(g));return a}function m(a,b,c,d){a=$(a);Object.isUndefined(d)&&(d=!0);if(a==document&&document.createEvent&&!a.dispatchEvent)a=document.documentElement;var e;document.createEvent?(e=document.createEvent("HTMLEvents"),e.initEvent("dataavailable",d,!0)):(e=document.createEventObject(),e.eventType=d?"ondataavailable":"onlosecapture");e.eventName=b;e.memo=c||{};document.createEvent?a.dispatchEvent(e):
a.fireEvent(e.eventType,e);return l.extend(e)}function o(a,b,c,d){a=$(a);Object.isFunction(c)&&Object.isUndefined(d)&&(d=c,c=null);return(new l.Handler(a,b,c,d)).start()}var l={KEY_BACKSPACE:8,KEY_TAB:9,KEY_RETURN:13,KEY_ESC:27,KEY_LEFT:37,KEY_UP:38,KEY_RIGHT:39,KEY_DOWN:40,KEY_DELETE:46,KEY_HOME:36,KEY_END:35,KEY_PAGEUP:33,KEY_PAGEDOWN:34,KEY_INSERT:45,cache:{}},x=document.documentElement,q="onmouseenter"in x&&"onmouseleave"in x,r=function(){return!1};window.attachEvent&&(r=window.addEventListener?
function(a){return!(a instanceof window.Event)}:function(){return!0});var s,w={"0":1,1:4,2:2};s=window.attachEvent?window.addEventListener?function(c,d){return r(c)?b(c,d):a(c,d)}:b:Prototype.Browser.WebKit?c:a;l.Methods={isLeftClick:function(a){return s(a,0)},isMiddleClick:function(a){return s(a,1)},isRightClick:function(a){return s(a,2)},element:function(a){var a=l.extend(a),b=a.target,c=a.type;if((a=a.currentTarget)&&a.tagName&&("load"===c||"error"===c||"click"===c&&"input"===a.tagName.toLowerCase()&&
"radio"===a.type))b=a;if(b.nodeType==Node.TEXT_NODE)b=b.parentNode;return Element.extend(b)},findElement:function(a,b){var c=l.element(a);if(!b)return c;for(;c;){if(Object.isElement(c)&&Prototype.Selector.match(c,b))return Element.extend(c);c=c.parentNode}},pointer:function(a){return{x:d(a),y:e(a)}},pointerX:d,pointerY:e,stop:function(a){l.extend(a);a.preventDefault();a.stopPropagation();a.stopped=!0}};var y=Object.keys(l.Methods).inject({},function(a,b){a[b]=l.Methods[b].methodize();return a});if(window.attachEvent){var A=
function(a){switch(a.type){case "mouseover":case "mouseenter":a=a.fromElement;break;case "mouseout":case "mouseleave":a=a.toElement;break;default:return null}return Element.extend(a)},D={stopPropagation:function(){this.cancelBubble=!0},preventDefault:function(){this.returnValue=!1},inspect:function(){return"[object Event]"}};l.extend=function(a,b){if(!a)return!1;if(!r(a)||a._extendedByPrototype)return a;a._extendedByPrototype=Prototype.emptyFunction;var c=l.pointer(a);Object.extend(a,{target:a.srcElement||
b,relatedTarget:A(a),pageX:c.x,pageY:c.y});Object.extend(a,y);Object.extend(a,D);return a}}else l.extend=Prototype.K;if(window.addEventListener)l.prototype=window.Event.prototype||document.createEvent("HTMLEvents").__proto__,Object.extend(l.prototype,y);var z=[];Prototype.Browser.IE&&window.attachEvent("onunload",g);Prototype.Browser.WebKit&&window.addEventListener("unload",Prototype.emptyFunction,!1);var C=Prototype.K,n={mouseenter:"mouseover",mouseleave:"mouseout"};q||(C=function(a){return n[a]||
a});l.Handler=Class.create({initialize:function(a,b,c,d){this.element=$(a);this.eventName=b;this.selector=c;this.callback=d;this.handler=this.handleEvent.bind(this)},start:function(){l.observe(this.element,this.eventName,this.handler);return this},stop:function(){l.stopObserving(this.element,this.eventName,this.handler);return this},handleEvent:function(a){var b=l.findElement(a,this.selector);b&&this.callback.call(this.element,a,b)}});Object.extend(l,l.Methods);Object.extend(l,{fire:m,observe:j,stopObserving:h,
on:o});Element.addMethods({fire:m,observe:j,stopObserving:h,on:o});Object.extend(document,{fire:m.methodize(),observe:j.methodize(),stopObserving:h.methodize(),on:o.methodize(),loaded:!1});window.Event?Object.extend(window.Event,l):window.Event=l})();
(function(){function a(){if(!document.loaded)d&&window.clearTimeout(d),document.loaded=!0,document.fire("dom:loaded")}function b(){"complete"===document.readyState&&(document.stopObserving("readystatechange",b),a())}function c(){try{document.documentElement.doScroll("left")}catch(b){d=c.defer();return}a()}var d;document.addEventListener?document.addEventListener("DOMContentLoaded",a,!1):(document.observe("readystatechange",b),window==top&&(d=c.defer()));Event.observe(window,"load",a)})();Element.addMethods();
Hash.toQueryString=Object.toQueryString;var Toggle={display:Element.toggle};Element.Methods.childOf=Element.Methods.descendantOf;
var Insertion={Before:function(a,b){return Element.insert(a,{before:b})},Top:function(a,b){return Element.insert(a,{top:b})},Bottom:function(a,b){return Element.insert(a,{bottom:b})},After:function(a,b){return Element.insert(a,{after:b})}},$continue=Error('"throw $continue" is deprecated, use "return" instead'),Position={includeScrollOffsets:!1,prepare:function(){this.deltaX=window.pageXOffset||document.documentElement.scrollLeft||document.body.scrollLeft||0;this.deltaY=window.pageYOffset||document.documentElement.scrollTop||
document.body.scrollTop||0},within:function(a,b,c){if(this.includeScrollOffsets)return this.withinIncludingScrolloffsets(a,b,c);this.xcomp=b;this.ycomp=c;this.offset=Element.cumulativeOffset(a);return c>=this.offset[1]&&c<this.offset[1]+a.offsetHeight&&b>=this.offset[0]&&b<this.offset[0]+a.offsetWidth},withinIncludingScrolloffsets:function(a,b,c){var d=Element.cumulativeScrollOffset(a);this.xcomp=b+d[0]-this.deltaX;this.ycomp=c+d[1]-this.deltaY;this.offset=Element.cumulativeOffset(a);return this.ycomp>=
this.offset[1]&&this.ycomp<this.offset[1]+a.offsetHeight&&this.xcomp>=this.offset[0]&&this.xcomp<this.offset[0]+a.offsetWidth},overlap:function(a,b){if(!a)return 0;if("vertical"==a)return(this.offset[1]+b.offsetHeight-this.ycomp)/b.offsetHeight;if("horizontal"==a)return(this.offset[0]+b.offsetWidth-this.xcomp)/b.offsetWidth},cumulativeOffset:Element.Methods.cumulativeOffset,positionedOffset:Element.Methods.positionedOffset,absolutize:function(a){Position.prepare();return Element.absolutize(a)},relativize:function(a){Position.prepare();
return Element.relativize(a)},realOffset:Element.Methods.cumulativeScrollOffset,offsetParent:Element.Methods.getOffsetParent,page:Element.Methods.viewportOffset,clone:function(a,b,c){c=c||{};return Element.clonePosition(b,a,c)}};
if(!document.getElementsByClassName)document.getElementsByClassName=function(a){function b(a){return a.blank()?null:"[contains(concat(' ', @class, ' '), ' "+a+" ')]"}a.getElementsByClassName=Prototype.BrowserFeatures.XPath?function(a,d){var d=d.toString().strip(),e=/\s/.test(d)?$w(d).map(b).join(""):b(d);return e?document._getElementsByXPath(".//*"+e,a):[]}:function(a,b){var b=b.toString().strip(),e=[],f=/\s/.test(b)?$w(b):null;if(!f&&!b)return e;for(var g=$(a).getElementsByTagName("*"),b=" "+b+" ",
j=0,h,m;h=g[j];j++)h.className&&(m=" "+h.className+" ")&&(m.include(b)||f&&f.all(function(a){return!a.toString().blank()&&m.include(" "+a+" ")}))&&e.push(Element.extend(h));return e};return function(a,b){return $(b||document.body).getElementsByClassName(a)}}(Element.Methods);Element.ClassNames=Class.create();
Element.ClassNames.prototype={initialize:function(a){this.element=$(a)},_each:function(a){this.element.className.split(/\s+/).select(function(a){return 0<a.length})._each(a)},set:function(a){this.element.className=a},add:function(a){this.include(a)||this.set($A(this).concat(a).join(" "))},remove:function(a){this.include(a)&&this.set($A(this).without(a).join(" "))},toString:function(){return $A(this).join(" ")}};Object.extend(Element.ClassNames.prototype,Enumerable);
(function(){window.Selector=Class.create({initialize:function(a){this.expression=a.strip()},findElements:function(a){return Prototype.Selector.select(this.expression,a)},match:function(a){return Prototype.Selector.match(a,this.expression)},toString:function(){return this.expression},inspect:function(){return"#<Selector: "+this.expression+">"}});Object.extend(Selector,{matchElements:function(a,b){for(var c=Prototype.Selector.match,d=[],e=0,f=a.length;e<f;e++){var g=a[e];c(g,b)&&d.push(Element.extend(g))}return d},
findElement:function(a,b,c){for(var c=c||0,d=0,e,f=0,g=a.length;f<g;f++)if(e=a[f],Prototype.Selector.match(e,b)&&c===d++)return Element.extend(e)},findChildElements:function(a,b){var c=b.toArray().join(", ");return Prototype.Selector.select(c,a||document)}})})();var Scriptaculous={Version:"1.9.0",require:function(a){try{document.write('<script type="text/javascript" src="'+a+'"><\/script>')}catch(b){var c=document.createElement("script");c.type="text/javascript";c.src=a;document.getElementsByTagName("head")[0].appendChild(c)}},REQUIRED_PROTOTYPE:"1.6.0.3",load:function(){function a(a){var b=a.replace(/_.*|\./g,""),b=parseInt(b+"0".times(4-b.length));return-1<a.indexOf("_")?b-1:b}if("undefined"==typeof Prototype||"undefined"==typeof Element||"undefined"==
typeof Element.Methods||a(Prototype.Version)<a(Scriptaculous.REQUIRED_PROTOTYPE))throw"script.aculo.us requires the Prototype JavaScript framework >= "+Scriptaculous.REQUIRED_PROTOTYPE;var b=/scriptaculous\.js(\?.*)?$/;$$("script[src]").findAll(function(a){return a.src.match(b)}).each(function(a){var d=a.src.replace(b,""),a=a.src.match(/\?.*load=([a-z,]*)/);(a?a[1]:"builder,effects,dragdrop,controls,slider,sound").split(",").each(function(a){Scriptaculous.require(d+a+".js")})})}};Scriptaculous.load();var Builder={NODEMAP:{AREA:"map",CAPTION:"table",COL:"table",COLGROUP:"table",LEGEND:"fieldset",OPTGROUP:"select",OPTION:"select",PARAM:"object",TBODY:"table",TD:"table",TFOOT:"table",TH:"table",THEAD:"table",TR:"table"},node:function(a,b,c){var a=a.toUpperCase(),d=document.createElement(this.NODEMAP[a]||"div");try{d.innerHTML="<"+a+"></"+a+">"}catch(e){}var f=d.firstChild||null;f&&f.tagName.toUpperCase()!=a&&(f=f.getElementsByTagName(a)[0]);f||(f=document.createElement(a));if(f){if(b)if(this._isStringOrNumber(b)||
b instanceof Array||b.tagName)this._children(f,b);else{var g=this._attributes(b);if(g.length){try{d.innerHTML="<"+a+" "+g+"></"+a+">"}catch(j){}f=d.firstChild||null;if(!f)for(attr in f=document.createElement(a),b)f["class"==attr?"className":attr]=b[attr];f.tagName.toUpperCase()!=a&&(f=d.getElementsByTagName(a)[0])}}c&&this._children(f,c);return $(f)}},_text:function(a){return document.createTextNode(a)},ATTR_MAP:{className:"class",htmlFor:"for"},_attributes:function(a){var b=[];for(attribute in a)b.push((attribute in
this.ATTR_MAP?this.ATTR_MAP[attribute]:attribute)+'="'+a[attribute].toString().escapeHTML().gsub(/"/,"&quot;")+'"');return b.join(" ")},_children:function(a,b){b.tagName?a.appendChild(b):"object"==typeof b?b.flatten().each(function(b){"object"==typeof b?a.appendChild(b):Builder._isStringOrNumber(b)&&a.appendChild(Builder._text(b))}):Builder._isStringOrNumber(b)&&a.appendChild(Builder._text(b))},_isStringOrNumber:function(a){return"string"==typeof a||"number"==typeof a},build:function(a){var b=this.node("div");
$(b).update(a.strip());return b.down()},dump:function(a){"object"!=typeof a&&"function"!=typeof a&&(a=window);"A ABBR ACRONYM ADDRESS APPLET AREA B BASE BASEFONT BDO BIG BLOCKQUOTE BODY BR BUTTON CAPTION CENTER CITE CODE COL COLGROUP DD DEL DFN DIR DIV DL DT EM FIELDSET FONT FORM FRAME FRAMESET H1 H2 H3 H4 H5 H6 HEAD HR HTML I IFRAME IMG INPUT INS ISINDEX KBD LABEL LEGEND LI LINK MAP MENU META NOFRAMES NOSCRIPT OBJECT OL OPTGROUP OPTION P PARAM PRE Q S SAMP SCRIPT SELECT SMALL SPAN STRIKE STRONG STYLE SUB SUP TABLE TBODY TD TEXTAREA TFOOT TH THEAD TITLE TR TT U UL VAR".split(/\s+/).each(function(b){a[b]=
function(){return Builder.node.apply(Builder,[b].concat($A(arguments)))}})}};String.prototype.parseColor=function(a){var b="#";if("rgb("==this.slice(0,4)){var c=this.slice(4,this.length-1).split(","),d=0;do b+=parseInt(c[d]).toColorPart();while(3>++d)}else if("#"==this.slice(0,1)){if(4==this.length)for(d=1;4>d;d++)b+=(this.charAt(d)+this.charAt(d)).toLowerCase();7==this.length&&(b=this.toLowerCase())}return 7==b.length?b:a||this};
Element.collectTextNodes=function(a){return $A($(a).childNodes).collect(function(a){return 3==a.nodeType?a.nodeValue:a.hasChildNodes()?Element.collectTextNodes(a):""}).flatten().join("")};Element.collectTextNodesIgnoreClass=function(a,b){return $A($(a).childNodes).collect(function(a){return 3==a.nodeType?a.nodeValue:a.hasChildNodes()&&!Element.hasClassName(a,b)?Element.collectTextNodesIgnoreClass(a,b):""}).flatten().join("")};
Element.setContentZoom=function(a,b){a=$(a);a.setStyle({fontSize:b/100+"em"});Prototype.Browser.WebKit&&window.scrollBy(0,0);return a};Element.getInlineOpacity=function(a){return $(a).style.opacity||""};Element.forceRerendering=function(a){try{var a=$(a),b=document.createTextNode(" ");a.appendChild(b);a.removeChild(b)}catch(c){}};
var Effect={_elementDoesNotExistError:{name:"ElementDoesNotExistError",message:"The specified DOM element does not exist, but is required for this effect to operate"},Transitions:{linear:Prototype.K,sinoidal:function(a){return-Math.cos(a*Math.PI)/2+0.5},reverse:function(a){return 1-a},flicker:function(a){a=-Math.cos(a*Math.PI)/4+0.75+Math.random()/4;return 1<a?1:a},wobble:function(a){return-Math.cos(a*Math.PI*9*a)/2+0.5},pulse:function(a,b){return-Math.cos(2*a*((b||5)-0.5)*Math.PI)/2+0.5},spring:function(a){return 1-
Math.cos(4.5*a*Math.PI)*Math.exp(6*-a)},none:function(){return 0},full:function(){return 1}},DefaultOptions:{duration:1,fps:100,sync:!1,from:0,to:1,delay:0,queue:"parallel"},tagifyText:function(a){var b="position:relative";Prototype.Browser.IE&&(b+=";zoom:1");a=$(a);$A(a.childNodes).each(function(c){3==c.nodeType&&(c.nodeValue.toArray().each(function(d){a.insertBefore((new Element("span",{style:b})).update(" "==d?String.fromCharCode(160):d),c)}),Element.remove(c))})},multiple:function(a,b,c){var a=
("object"==typeof a||Object.isFunction(a))&&a.length?a:$(a).childNodes,d=Object.extend({speed:0.1,delay:0},c||{}),e=d.delay;$A(a).each(function(a,c){new b(a,Object.extend(d,{delay:c*d.speed+e}))})},PAIRS:{slide:["SlideDown","SlideUp"],blind:["BlindDown","BlindUp"],appear:["Appear","Fade"]},toggle:function(a,b,c){a=$(a);b=(b||"appear").toLowerCase();return Effect[Effect.PAIRS[b][a.visible()?1:0]](a,Object.extend({queue:{position:"end",scope:a.id||"global",limit:1}},c||{}))}};
Effect.DefaultOptions.transition=Effect.Transitions.sinoidal;
Effect.ScopedQueue=Class.create(Enumerable,{initialize:function(){this.effects=[];this.interval=null},_each:function(a){this.effects._each(a)},add:function(a){var b=(new Date).getTime();switch(Object.isString(a.options.queue)?a.options.queue:a.options.queue.position){case "front":this.effects.findAll(function(a){return"idle"==a.state}).each(function(b){b.startOn+=a.finishOn;b.finishOn+=a.finishOn});break;case "with-last":b=this.effects.pluck("startOn").max()||b;break;case "end":b=this.effects.pluck("finishOn").max()||
b}a.startOn+=b;a.finishOn+=b;(!a.options.queue.limit||this.effects.length<a.options.queue.limit)&&this.effects.push(a);if(!this.interval)this.interval=setInterval(this.loop.bind(this),15)},remove:function(a){this.effects=this.effects.reject(function(b){return b==a});if(0==this.effects.length)clearInterval(this.interval),this.interval=null},loop:function(){for(var a=(new Date).getTime(),b=0,c=this.effects.length;b<c;b++)this.effects[b]&&this.effects[b].loop(a)}});
Effect.Queues={instances:$H(),get:function(a){return!Object.isString(a)?a:this.instances.get(a)||this.instances.set(a,new Effect.ScopedQueue)}};Effect.Queue=Effect.Queues.get("global");
Effect.Base=Class.create({position:null,start:function(a){if(a&&!1===a.transition)a.transition=Effect.Transitions.linear;this.options=Object.extend(Object.extend({},Effect.DefaultOptions),a||{});this.currentFrame=0;this.state="idle";this.startOn=1E3*this.options.delay;this.finishOn=this.startOn+1E3*this.options.duration;this.fromToDelta=this.options.to-this.options.from;this.totalTime=this.finishOn-this.startOn;this.totalFrames=this.options.fps*this.options.duration;this.render=function(){function a(b,
d){if(b.options[d+"Internal"])b.options[d+"Internal"](b);if(b.options[d])b.options[d](b)}return function(c){if("idle"===this.state)this.state="running",a(this,"beforeSetup"),this.setup&&this.setup(),a(this,"afterSetup");if("running"===this.state)this.position=c=this.options.transition(c)*this.fromToDelta+this.options.from,a(this,"beforeUpdate"),this.update&&this.update(c),a(this,"afterUpdate")}}();this.event("beforeStart");this.options.sync||Effect.Queues.get(Object.isString(this.options.queue)?"global":
this.options.queue.scope).add(this)},loop:function(a){if(a>=this.startOn)if(a>=this.finishOn)this.render(1),this.cancel(),this.event("beforeFinish"),this.finish&&this.finish(),this.event("afterFinish");else{var a=(a-this.startOn)/this.totalTime,b=(a*this.totalFrames).round();if(b>this.currentFrame)this.render(a),this.currentFrame=b}},cancel:function(){this.options.sync||Effect.Queues.get(Object.isString(this.options.queue)?"global":this.options.queue.scope).remove(this);this.state="finished"},event:function(a){if(this.options[a+
"Internal"])this.options[a+"Internal"](this);if(this.options[a])this.options[a](this)},inspect:function(){var a=$H();for(property in this)Object.isFunction(this[property])||a.set(property,this[property]);return"#<Effect:"+a.inspect()+",options:"+$H(this.options).inspect()+">"}});
Effect.Parallel=Class.create(Effect.Base,{initialize:function(a,b){this.effects=a||[];this.start(b)},update:function(a){this.effects.invoke("render",a)},finish:function(a){this.effects.each(function(b){b.render(1);b.cancel();b.event("beforeFinish");b.finish&&b.finish(a);b.event("afterFinish")})}});
Effect.Tween=Class.create(Effect.Base,{initialize:function(a,b,c){var a=Object.isString(a)?$(a):a,d=$A(arguments),e=d.last(),d=5==d.length?d[3]:null;this.method=Object.isFunction(e)?e.bind(a):Object.isFunction(a[e])?a[e].bind(a):function(b){a[e]=b};this.start(Object.extend({from:b,to:c},d||{}))},update:function(a){this.method(a)}});Effect.Event=Class.create(Effect.Base,{initialize:function(a){this.start(Object.extend({duration:0},a||{}))},update:Prototype.emptyFunction});
Effect.Opacity=Class.create(Effect.Base,{initialize:function(a,b){this.element=$(a);if(!this.element)throw Effect._elementDoesNotExistError;Prototype.Browser.IE&&!this.element.currentStyle.hasLayout&&this.element.setStyle({zoom:1});this.start(Object.extend({from:this.element.getOpacity()||0,to:1},b||{}))},update:function(a){this.element.setOpacity(a)}});
Effect.Move=Class.create(Effect.Base,{initialize:function(a,b){this.element=$(a);if(!this.element)throw Effect._elementDoesNotExistError;this.start(Object.extend({x:0,y:0,mode:"relative"},b||{}))},setup:function(){this.element.makePositioned();this.originalLeft=parseFloat(this.element.getStyle("left")||"0");this.originalTop=parseFloat(this.element.getStyle("top")||"0");"absolute"==this.options.mode&&(this.options.x-=this.originalLeft,this.options.y-=this.originalTop)},update:function(a){this.element.setStyle({left:(this.options.x*
a+this.originalLeft).round()+"px",top:(this.options.y*a+this.originalTop).round()+"px"})}});Effect.MoveBy=function(a,b,c,d){return new Effect.Move(a,Object.extend({x:c,y:b},d||{}))};
Effect.Scale=Class.create(Effect.Base,{initialize:function(a,b,c){this.element=$(a);if(!this.element)throw Effect._elementDoesNotExistError;this.start(Object.extend({scaleX:!0,scaleY:!0,scaleContent:!0,scaleFromCenter:!1,scaleMode:"box",scaleFrom:100,scaleTo:b},c||{}))},setup:function(){this.restoreAfterFinish=this.options.restoreAfterFinish||!1;this.elementPositioning=this.element.getStyle("position");this.originalStyle={};["top","left","width","height","fontSize"].each(function(a){this.originalStyle[a]=
this.element.style[a]}.bind(this));this.originalTop=this.element.offsetTop;this.originalLeft=this.element.offsetLeft;var a=this.element.getStyle("font-size")||"100%";["em","px","%","pt"].each(function(b){if(0<a.indexOf(b))this.fontSize=parseFloat(a),this.fontSizeType=b}.bind(this));this.factor=(this.options.scaleTo-this.options.scaleFrom)/100;this.dims=null;if("box"==this.options.scaleMode)this.dims=[this.element.offsetHeight,this.element.offsetWidth];if(/^content/.test(this.options.scaleMode))this.dims=
[this.element.scrollHeight,this.element.scrollWidth];if(!this.dims)this.dims=[this.options.scaleMode.originalHeight,this.options.scaleMode.originalWidth]},update:function(a){a=this.options.scaleFrom/100+this.factor*a;this.options.scaleContent&&this.fontSize&&this.element.setStyle({fontSize:this.fontSize*a+this.fontSizeType});this.setDimensions(this.dims[0]*a,this.dims[1]*a)},finish:function(){this.restoreAfterFinish&&this.element.setStyle(this.originalStyle)},setDimensions:function(a,b){var c={};
if(this.options.scaleX)c.width=b.round()+"px";if(this.options.scaleY)c.height=a.round()+"px";if(this.options.scaleFromCenter){var d=(a-this.dims[0])/2,e=(b-this.dims[1])/2;if("absolute"==this.elementPositioning){if(this.options.scaleY)c.top=this.originalTop-d+"px";if(this.options.scaleX)c.left=this.originalLeft-e+"px"}else{if(this.options.scaleY)c.top=-d+"px";if(this.options.scaleX)c.left=-e+"px"}}this.element.setStyle(c)}});
Effect.Highlight=Class.create(Effect.Base,{initialize:function(a,b){this.element=$(a);if(!this.element)throw Effect._elementDoesNotExistError;this.start(Object.extend({startcolor:"#ffff99"},b||{}))},setup:function(){if("none"==this.element.getStyle("display"))this.cancel();else{this.oldStyle={};if(!this.options.keepBackgroundImage)this.oldStyle.backgroundImage=this.element.getStyle("background-image"),this.element.setStyle({backgroundImage:"none"});if(!this.options.endcolor)this.options.endcolor=
this.element.getStyle("background-color").parseColor("#ffffff");if(!this.options.restorecolor)this.options.restorecolor=this.element.getStyle("background-color");this._base=$R(0,2).map(function(a){return parseInt(this.options.startcolor.slice(2*a+1,2*a+3),16)}.bind(this));this._delta=$R(0,2).map(function(a){return parseInt(this.options.endcolor.slice(2*a+1,2*a+3),16)-this._base[a]}.bind(this))}},update:function(a){this.element.setStyle({backgroundColor:$R(0,2).inject("#",function(b,c,d){return b+
(this._base[d]+this._delta[d]*a).round().toColorPart()}.bind(this))})},finish:function(){this.element.setStyle(Object.extend(this.oldStyle,{backgroundColor:this.options.restorecolor}))}});Effect.ScrollTo=function(a,b){var c=b||{},d=document.viewport.getScrollOffsets(),e=$(a).cumulativeOffset();c.offset&&(e[1]+=c.offset);return new Effect.Tween(null,d.top,e[1],c,function(a){scrollTo(d.left,a.round())})};
Effect.Fade=function(a,b){var a=$(a),c=a.getInlineOpacity(),d=Object.extend({from:a.getOpacity()||1,to:0,afterFinishInternal:function(a){0==a.options.to&&a.element.hide().setStyle({opacity:c})}},b||{});return new Effect.Opacity(a,d)};
Effect.Appear=function(a,b){var a=$(a),c=Object.extend({from:"none"==a.getStyle("display")?0:a.getOpacity()||0,to:1,afterFinishInternal:function(a){a.element.forceRerendering()},beforeSetup:function(a){a.element.setOpacity(a.options.from).show()}},b||{});return new Effect.Opacity(a,c)};
Effect.Puff=function(a,b){var a=$(a),c={opacity:a.getInlineOpacity(),position:a.getStyle("position"),top:a.style.top,left:a.style.left,width:a.style.width,height:a.style.height};return new Effect.Parallel([new Effect.Scale(a,200,{sync:!0,scaleFromCenter:!0,scaleContent:!0,restoreAfterFinish:!0}),new Effect.Opacity(a,{sync:!0,to:0})],Object.extend({duration:1,beforeSetupInternal:function(a){Position.absolutize(a.effects[0].element)},afterFinishInternal:function(a){a.effects[0].element.hide().setStyle(c)}},
b||{}))};Effect.BlindUp=function(a,b){a=$(a);a.makeClipping();return new Effect.Scale(a,0,Object.extend({scaleContent:!1,scaleX:!1,restoreAfterFinish:!0,afterFinishInternal:function(a){a.element.hide().undoClipping()}},b||{}))};
Effect.BlindDown=function(a,b){var a=$(a),c=a.getDimensions();return new Effect.Scale(a,100,Object.extend({scaleContent:!1,scaleX:!1,scaleFrom:0,scaleMode:{originalHeight:c.height,originalWidth:c.width},restoreAfterFinish:!0,afterSetup:function(a){a.element.makeClipping().setStyle({height:"0px"}).show()},afterFinishInternal:function(a){a.element.undoClipping()}},b||{}))};
Effect.SwitchOff=function(a,b){var a=$(a),c=a.getInlineOpacity();return new Effect.Appear(a,Object.extend({duration:0.4,from:0,transition:Effect.Transitions.flicker,afterFinishInternal:function(a){new Effect.Scale(a.element,1,{duration:0.3,scaleFromCenter:!0,scaleX:!1,scaleContent:!1,restoreAfterFinish:!0,beforeSetup:function(a){a.element.makePositioned().makeClipping()},afterFinishInternal:function(a){a.element.hide().undoClipping().undoPositioned().setStyle({opacity:c})}})}},b||{}))};
Effect.DropOut=function(a,b){var a=$(a),c={top:a.getStyle("top"),left:a.getStyle("left"),opacity:a.getInlineOpacity()};return new Effect.Parallel([new Effect.Move(a,{x:0,y:100,sync:!0}),new Effect.Opacity(a,{sync:!0,to:0})],Object.extend({duration:0.5,beforeSetup:function(a){a.effects[0].element.makePositioned()},afterFinishInternal:function(a){a.effects[0].element.hide().undoPositioned().setStyle(c)}},b||{}))};
Effect.Shake=function(a,b){var a=$(a),c=Object.extend({distance:20,duration:0.5},b||{}),d=parseFloat(c.distance),e=parseFloat(c.duration)/10,f={top:a.getStyle("top"),left:a.getStyle("left")};return new Effect.Move(a,{x:d,y:0,duration:e,afterFinishInternal:function(a){new Effect.Move(a.element,{x:2*-d,y:0,duration:2*e,afterFinishInternal:function(a){new Effect.Move(a.element,{x:2*d,y:0,duration:2*e,afterFinishInternal:function(a){new Effect.Move(a.element,{x:2*-d,y:0,duration:2*e,afterFinishInternal:function(a){new Effect.Move(a.element,
{x:2*d,y:0,duration:2*e,afterFinishInternal:function(a){new Effect.Move(a.element,{x:-d,y:0,duration:e,afterFinishInternal:function(a){a.element.undoPositioned().setStyle(f)}})}})}})}})}})}})};
Effect.SlideDown=function(a,b){var a=$(a).cleanWhitespace(),c=a.down().getStyle("bottom"),d=a.getDimensions();return new Effect.Scale(a,100,Object.extend({scaleContent:!1,scaleX:!1,scaleFrom:window.opera?0:1,scaleMode:{originalHeight:d.height,originalWidth:d.width},restoreAfterFinish:!0,afterSetup:function(a){a.element.makePositioned();a.element.down().makePositioned();window.opera&&a.element.setStyle({top:""});a.element.makeClipping().setStyle({height:"0px"}).show()},afterUpdateInternal:function(a){a.element.down().setStyle({bottom:a.dims[0]-
a.element.clientHeight+"px"})},afterFinishInternal:function(a){a.element.undoClipping().undoPositioned();a.element.down().undoPositioned().setStyle({bottom:c})}},b||{}))};
Effect.SlideUp=function(a,b){var a=$(a).cleanWhitespace(),c=a.down().getStyle("bottom"),d=a.getDimensions();return new Effect.Scale(a,window.opera?0:1,Object.extend({scaleContent:!1,scaleX:!1,scaleMode:"box",scaleFrom:100,scaleMode:{originalHeight:d.height,originalWidth:d.width},restoreAfterFinish:!0,afterSetup:function(a){a.element.makePositioned();a.element.down().makePositioned();window.opera&&a.element.setStyle({top:""});a.element.makeClipping().show()},afterUpdateInternal:function(a){a.element.down().setStyle({bottom:a.dims[0]-
a.element.clientHeight+"px"})},afterFinishInternal:function(a){a.element.hide().undoClipping().undoPositioned();a.element.down().undoPositioned().setStyle({bottom:c})}},b||{}))};Effect.Squish=function(a){return new Effect.Scale(a,window.opera?1:0,{restoreAfterFinish:!0,beforeSetup:function(a){a.element.makeClipping()},afterFinishInternal:function(a){a.element.hide().undoClipping()}})};
Effect.Grow=function(a,b){var a=$(a),c=Object.extend({direction:"center",moveTransition:Effect.Transitions.sinoidal,scaleTransition:Effect.Transitions.sinoidal,opacityTransition:Effect.Transitions.full},b||{}),d={top:a.style.top,left:a.style.left,height:a.style.height,width:a.style.width,opacity:a.getInlineOpacity()},e=a.getDimensions(),f,g,j,h;switch(c.direction){case "top-left":f=g=j=h=0;break;case "top-right":f=e.width;g=h=0;j=-e.width;break;case "bottom-left":f=j=0;g=e.height;h=-e.height;break;
case "bottom-right":f=e.width;g=e.height;j=-e.width;h=-e.height;break;case "center":f=e.width/2,g=e.height/2,j=-e.width/2,h=-e.height/2}return new Effect.Move(a,{x:f,y:g,duration:0.01,beforeSetup:function(a){a.element.hide().makeClipping().makePositioned()},afterFinishInternal:function(a){new Effect.Parallel([new Effect.Opacity(a.element,{sync:!0,to:1,from:0,transition:c.opacityTransition}),new Effect.Move(a.element,{x:j,y:h,sync:!0,transition:c.moveTransition}),new Effect.Scale(a.element,100,{scaleMode:{originalHeight:e.height,
originalWidth:e.width},sync:!0,scaleFrom:window.opera?1:0,transition:c.scaleTransition,restoreAfterFinish:!0})],Object.extend({beforeSetup:function(a){a.effects[0].element.setStyle({height:"0px"}).show()},afterFinishInternal:function(a){a.effects[0].element.undoClipping().undoPositioned().setStyle(d)}},c))}})};
Effect.Shrink=function(a,b){var a=$(a),c=Object.extend({direction:"center",moveTransition:Effect.Transitions.sinoidal,scaleTransition:Effect.Transitions.sinoidal,opacityTransition:Effect.Transitions.none},b||{}),d={top:a.style.top,left:a.style.left,height:a.style.height,width:a.style.width,opacity:a.getInlineOpacity()},e=a.getDimensions(),f,g;switch(c.direction){case "top-left":f=g=0;break;case "top-right":f=e.width;g=0;break;case "bottom-left":f=0;g=e.height;break;case "bottom-right":f=e.width;g=
e.height;break;case "center":f=e.width/2,g=e.height/2}return new Effect.Parallel([new Effect.Opacity(a,{sync:!0,to:0,from:1,transition:c.opacityTransition}),new Effect.Scale(a,window.opera?1:0,{sync:!0,transition:c.scaleTransition,restoreAfterFinish:!0}),new Effect.Move(a,{x:f,y:g,sync:!0,transition:c.moveTransition})],Object.extend({beforeStartInternal:function(a){a.effects[0].element.makePositioned().makeClipping()},afterFinishInternal:function(a){a.effects[0].element.hide().undoClipping().undoPositioned().setStyle(d)}},
c))};Effect.Pulsate=function(a,b){var a=$(a),c=b||{},d=a.getInlineOpacity(),e=c.transition||Effect.Transitions.linear;return new Effect.Opacity(a,Object.extend(Object.extend({duration:2,from:0,afterFinishInternal:function(a){a.element.setStyle({opacity:d})}},c),{transition:function(a){return 1-e(-Math.cos(2*a*(c.pulses||5)*Math.PI)/2+0.5)}}))};
Effect.Fold=function(a,b){var a=$(a),c={top:a.style.top,left:a.style.left,width:a.style.width,height:a.style.height};a.makeClipping();return new Effect.Scale(a,5,Object.extend({scaleContent:!1,scaleX:!1,afterFinishInternal:function(){new Effect.Scale(a,1,{scaleContent:!1,scaleY:!1,afterFinishInternal:function(a){a.element.hide().undoClipping().setStyle(c)}})}},b||{}))};
Effect.Morph=Class.create(Effect.Base,{initialize:function(a,b){this.element=$(a);if(!this.element)throw Effect._elementDoesNotExistError;var c=Object.extend({style:{}},b||{});if(Object.isString(c.style))if(c.style.include(":"))this.style=c.style.parseStyle();else{this.element.addClassName(c.style);this.style=$H(this.element.getStyles());this.element.removeClassName(c.style);var d=this.element.getStyles();this.style=this.style.reject(function(a){return a.value==d[a.key]});c.afterFinishInternal=function(a){a.element.addClassName(a.options.style);
a.transforms.each(function(b){a.element.style[b.style]=""})}}else this.style=$H(c.style);this.start(c)},setup:function(){function a(a){if(!a||["rgba(0, 0, 0, 0)","transparent"].include(a))a="#ffffff";a=a.parseColor();return $R(0,2).map(function(c){return parseInt(a.slice(2*c+1,2*c+3),16)})}this.transforms=this.style.map(function(b){var c=b[0],b=b[1],d=null;"#zzzzzz"!=b.parseColor("#zzzzzz")?(b=b.parseColor(),d="color"):"opacity"==c?(b=parseFloat(b),Prototype.Browser.IE&&!this.element.currentStyle.hasLayout&&
this.element.setStyle({zoom:1})):Element.CSS_LENGTH.test(b)&&(d=b.match(/^([\+\-]?[0-9\.]+)(.*)$/),b=parseFloat(d[1]),d=3==d.length?d[2]:null);var e=this.element.getStyle(c);return{style:c.camelize(),originalValue:"color"==d?a(e):parseFloat(e||0),targetValue:"color"==d?a(b):b,unit:d}}.bind(this)).reject(function(a){return a.originalValue==a.targetValue||"color"!=a.unit&&(isNaN(a.originalValue)||isNaN(a.targetValue))})},update:function(a){for(var b={},c,d=this.transforms.length;d--;)b[(c=this.transforms[d]).style]=
"color"==c.unit?"#"+Math.round(c.originalValue[0]+(c.targetValue[0]-c.originalValue[0])*a).toColorPart()+Math.round(c.originalValue[1]+(c.targetValue[1]-c.originalValue[1])*a).toColorPart()+Math.round(c.originalValue[2]+(c.targetValue[2]-c.originalValue[2])*a).toColorPart():(c.originalValue+(c.targetValue-c.originalValue)*a).toFixed(3)+(null===c.unit?"":c.unit);this.element.setStyle(b,!0)}});
Effect.Transform=Class.create({initialize:function(a,b){this.tracks=[];this.options=b||{};this.addTracks(a)},addTracks:function(a){a.each(function(a){var a=$H(a),c=a.values().first();this.tracks.push($H({ids:a.keys().first(),effect:Effect.Morph,options:{style:c}}))}.bind(this));return this},play:function(){return new Effect.Parallel(this.tracks.map(function(a){var b=a.get("ids"),c=a.get("effect"),d=a.get("options");return[$(b)||$$(b)].flatten().map(function(a){return new c(a,Object.extend({sync:!0},
d))})}).flatten(),this.options)}});Element.CSS_PROPERTIES=$w("backgroundColor backgroundPosition borderBottomColor borderBottomStyle borderBottomWidth borderLeftColor borderLeftStyle borderLeftWidth borderRightColor borderRightStyle borderRightWidth borderSpacing borderTopColor borderTopStyle borderTopWidth bottom clip color fontSize fontWeight height left letterSpacing lineHeight marginBottom marginLeft marginRight marginTop markerOffset maxHeight maxWidth minHeight minWidth opacity outlineColor outlineOffset outlineWidth paddingBottom paddingLeft paddingRight paddingTop right textIndent top width wordSpacing zIndex");
Element.CSS_LENGTH=/^(([\+\-]?[0-9\.]+)(em|ex|px|in|cm|mm|pt|pc|\%))|0$/;String.__parseStyleElement=document.createElement("div");
String.prototype.parseStyle=function(){var a,b=$H();Prototype.Browser.WebKit?a=(new Element("div",{style:this})).style:(String.__parseStyleElement.innerHTML='<div style="'+this+'"></div>',a=String.__parseStyleElement.childNodes[0].style);Element.CSS_PROPERTIES.each(function(c){a[c]&&b.set(c,a[c])});Prototype.Browser.IE&&this.include("opacity")&&b.set("opacity",this.match(/opacity:\s*((?:0|1)?(?:\.\d*)?)/)[1]);return b};
Element.getStyles=document.defaultView&&document.defaultView.getComputedStyle?function(a){var b=document.defaultView.getComputedStyle($(a),null);return Element.CSS_PROPERTIES.inject({},function(a,d){a[d]=b[d];return a})}:function(a){var a=$(a),b=a.currentStyle,c;c=Element.CSS_PROPERTIES.inject({},function(a,c){a[c]=b[c];return a});if(!c.opacity)c.opacity=a.getOpacity();return c};
Effect.Methods={morph:function(a,b,c){a=$(a);new Effect.Morph(a,Object.extend({style:b},c||{}));return a},visualEffect:function(a,b,c){a=$(a);b=b.dasherize().camelize();b=b.charAt(0).toUpperCase()+b.substring(1);new Effect[b](a,c);return a},highlight:function(a,b){a=$(a);new Effect.Highlight(a,b);return a}};
$w("fade appear grow shrink fold blindUp blindDown slideUp slideDown pulsate shake puff squish switchOff dropOut").each(function(a){Effect.Methods[a]=function(b,c){b=$(b);Effect[a.charAt(0).toUpperCase()+a.substring(1)](b,c);return b}});$w("getInlineOpacity forceRerendering setContentZoom collectTextNodes collectTextNodesIgnoreClass getStyles").each(function(a){Effect.Methods[a]=Element[a]});Element.addMethods(Effect.Methods);if(Object.isUndefined(Effect))throw"dragdrop.js requires including script.aculo.us' effects.js library";
var Droppables={drops:[],remove:function(a){this.drops=this.drops.reject(function(b){return b.element==$(a)})},add:function(a,b){var a=$(a),c=Object.extend({greedy:!0,hoverclass:null,tree:!1},b||{});if(c.containment){c._containers=[];var d=c.containment;Object.isArray(d)?d.each(function(a){c._containers.push($(a))}):c._containers.push($(d))}if(c.accept)c.accept=[c.accept].flatten();Element.makePositioned(a);c.element=a;this.drops.push(c)},findDeepestChild:function(a){deepest=a[0];for(i=1;i<a.length;++i)Element.isParent(a[i].element,
deepest.element)&&(deepest=a[i]);return deepest},isContained:function(a,b){var c;c=b.tree?a.treeNode:a.parentNode;return b._containers.detect(function(a){return c==a})},isAffected:function(a,b,c){return c.element!=b&&(!c._containers||this.isContained(b,c))&&(!c.accept||Element.classNames(b).detect(function(a){return c.accept.include(a)}))&&Position.within(c.element,a[0],a[1])},deactivate:function(a){a.hoverclass&&Element.removeClassName(a.element,a.hoverclass);this.last_active=null},activate:function(a){a.hoverclass&&
Element.addClassName(a.element,a.hoverclass);this.last_active=a},show:function(a,b){if(this.drops.length){var c,d=[];this.drops.each(function(c){Droppables.isAffected(a,b,c)&&d.push(c)});0<d.length&&(c=Droppables.findDeepestChild(d));this.last_active&&this.last_active!=c&&this.deactivate(this.last_active);if(c){Position.within(c.element,a[0],a[1]);if(c.onHover)c.onHover(b,c.element,Position.overlap(c.overlap,c.element));c!=this.last_active&&Droppables.activate(c)}}},fire:function(a,b){if(this.last_active&&
(Position.prepare(),this.isAffected([Event.pointerX(a),Event.pointerY(a)],b,this.last_active)&&this.last_active.onDrop))return this.last_active.onDrop(b,this.last_active.element,a),!0},reset:function(){this.last_active&&this.deactivate(this.last_active)}},Draggables={drags:[],observers:[],register:function(a){if(0==this.drags.length)this.eventMouseUp=this.endDrag.bindAsEventListener(this),this.eventMouseMove=this.updateDrag.bindAsEventListener(this),this.eventKeypress=this.keyPress.bindAsEventListener(this),
Event.observe(document,"mouseup",this.eventMouseUp),Event.observe(document,"mousemove",this.eventMouseMove),Event.observe(document,"keypress",this.eventKeypress);this.drags.push(a)},unregister:function(a){this.drags=this.drags.reject(function(b){return b==a});0==this.drags.length&&(Event.stopObserving(document,"mouseup",this.eventMouseUp),Event.stopObserving(document,"mousemove",this.eventMouseMove),Event.stopObserving(document,"keypress",this.eventKeypress))},activate:function(a){a.options.delay?
this._timeout=setTimeout(function(){Draggables._timeout=null;window.focus();Draggables.activeDraggable=a}.bind(this),a.options.delay):(window.focus(),this.activeDraggable=a)},deactivate:function(){this.activeDraggable=null},updateDrag:function(a){if(this.activeDraggable){var b=[Event.pointerX(a),Event.pointerY(a)];if(!(this._lastPointer&&this._lastPointer.inspect()==b.inspect()))this._lastPointer=b,this.activeDraggable.updateDrag(a,b)}},endDrag:function(a){if(this._timeout)clearTimeout(this._timeout),
this._timeout=null;if(this.activeDraggable)this._lastPointer=null,this.activeDraggable.endDrag(a),this.activeDraggable=null},keyPress:function(a){this.activeDraggable&&this.activeDraggable.keyPress(a)},addObserver:function(a){this.observers.push(a);this._cacheObserverCallbacks()},removeObserver:function(a){this.observers=this.observers.reject(function(b){return b.element==a});this._cacheObserverCallbacks()},notify:function(a,b,c){0<this[a+"Count"]&&this.observers.each(function(d){if(d[a])d[a](a,b,
c)});if(b.options[a])b.options[a](b,c)},_cacheObserverCallbacks:function(){["onStart","onEnd","onDrag"].each(function(a){Draggables[a+"Count"]=Draggables.observers.select(function(b){return b[a]}).length})}},Draggable=Class.create({initialize:function(a,b){var c={handle:!1,reverteffect:function(a,b,c){var g=0.02*Math.sqrt(Math.abs(b^2)+Math.abs(c^2));new Effect.Move(a,{x:-c,y:-b,duration:g,queue:{scope:"_draggable",position:"end"}})},endeffect:function(a){var b=Object.isNumber(a._opacity)?a._opacity:
1;new Effect.Opacity(a,{duration:0.2,from:0.7,to:b,queue:{scope:"_draggable",position:"end"},afterFinish:function(){Draggable._dragging[a]=!1}})},zindex:1E3,revert:!1,quiet:!1,scroll:!1,scrollSensitivity:20,scrollSpeed:15,snap:!1,delay:0};(!b||Object.isUndefined(b.endeffect))&&Object.extend(c,{starteffect:function(a){a._opacity=Element.getOpacity(a);Draggable._dragging[a]=!0;new Effect.Opacity(a,{duration:0.2,from:a._opacity,to:0.7})}});c=Object.extend(c,b||{});this.element=$(a);if(c.handle&&Object.isString(c.handle))this.handle=
this.element.down("."+c.handle,0);if(!this.handle)this.handle=$(c.handle);if(!this.handle)this.handle=this.element;if(c.scroll&&!c.scroll.scrollTo&&!c.scroll.outerHTML)c.scroll=$(c.scroll),this._isScrollChild=Element.childOf(this.element,c.scroll);Element.makePositioned(this.element);this.options=c;this.dragging=!1;this.eventMouseDown=this.initDrag.bindAsEventListener(this);Event.observe(this.handle,"mousedown",this.eventMouseDown);Draggables.register(this)},destroy:function(){Event.stopObserving(this.handle,
"mousedown",this.eventMouseDown);Draggables.unregister(this)},currentDelta:function(){return[parseInt(Element.getStyle(this.element,"left")||"0"),parseInt(Element.getStyle(this.element,"top")||"0")]},initDrag:function(a){if(Object.isUndefined(Draggable._dragging[this.element])||!Draggable._dragging[this.element])if(Event.isLeftClick(a)&&(!(tag_name=Event.element(a).tagName.toUpperCase())||!("INPUT"==tag_name||"SELECT"==tag_name||"OPTION"==tag_name||"BUTTON"==tag_name||"TEXTAREA"==tag_name))){var b=
[Event.pointerX(a),Event.pointerY(a)],c=this.element.cumulativeOffset();this.offset=[0,1].map(function(a){return b[a]-c[a]});Draggables.activate(this);Event.stop(a)}},startDrag:function(a){this.dragging=!0;if(!this.delta)this.delta=this.currentDelta();if(this.options.zindex)this.originalZ=parseInt(Element.getStyle(this.element,"z-index")||0),this.element.style.zIndex=this.options.zindex;if(this.options.ghosting)this._clone=this.element.cloneNode(!0),(this._originallyAbsolute="absolute"==this.element.getStyle("position"))||
Position.absolutize(this.element),this.element.parentNode.insertBefore(this._clone,this.element);if(this.options.scroll)if(this.options.scroll==window){var b=this._getWindowScroll(this.options.scroll);this.originalScrollLeft=b.left;this.originalScrollTop=b.top}else this.originalScrollLeft=this.options.scroll.scrollLeft,this.originalScrollTop=this.options.scroll.scrollTop;Draggables.notify("onStart",this,a);this.options.starteffect&&this.options.starteffect(this.element)},updateDrag:function(a,b){this.dragging||
this.startDrag(a);this.options.quiet||(Position.prepare(),Droppables.show(b,this.element));Draggables.notify("onDrag",this,a);this.draw(b);this.options.change&&this.options.change(this);if(this.options.scroll){this.stopScrolling();var c;if(this.options.scroll==window)with(this._getWindowScroll(this.options.scroll))c=[left,top,left+width,top+height];else c=Position.page(this.options.scroll).toArray(),c[0]+=this.options.scroll.scrollLeft+Position.deltaX,c[1]+=this.options.scroll.scrollTop+Position.deltaY,
c.push(c[0]+this.options.scroll.offsetWidth),c.push(c[1]+this.options.scroll.offsetHeight);var d=[0,0];b[0]<c[0]+this.options.scrollSensitivity&&(d[0]=b[0]-(c[0]+this.options.scrollSensitivity));b[1]<c[1]+this.options.scrollSensitivity&&(d[1]=b[1]-(c[1]+this.options.scrollSensitivity));b[0]>c[2]-this.options.scrollSensitivity&&(d[0]=b[0]-(c[2]-this.options.scrollSensitivity));b[1]>c[3]-this.options.scrollSensitivity&&(d[1]=b[1]-(c[3]-this.options.scrollSensitivity));this.startScrolling(d)}Prototype.Browser.WebKit&&
window.scrollBy(0,0);Event.stop(a)},finishDrag:function(a,b){this.dragging=!1;if(this.options.quiet){Position.prepare();var c=[Event.pointerX(a),Event.pointerY(a)];Droppables.show(c,this.element)}if(this.options.ghosting)this._originallyAbsolute||Position.relativize(this.element),delete this._originallyAbsolute,Element.remove(this._clone),this._clone=null;c=!1;b&&((c=Droppables.fire(a,this.element))||(c=!1));if(c&&this.options.onDropped)this.options.onDropped(this.element);Draggables.notify("onEnd",
this,a);var d=this.options.revert;d&&Object.isFunction(d)&&(d=d(this.element));var e=this.currentDelta();d&&this.options.reverteffect?(0==c||"failure"!=d)&&this.options.reverteffect(this.element,e[1]-this.delta[1],e[0]-this.delta[0]):this.delta=e;if(this.options.zindex)this.element.style.zIndex=this.originalZ;this.options.endeffect&&this.options.endeffect(this.element);Draggables.deactivate(this);Droppables.reset()},keyPress:function(a){a.keyCode==Event.KEY_ESC&&(this.finishDrag(a,!1),Event.stop(a))},
endDrag:function(a){this.dragging&&(this.stopScrolling(),this.finishDrag(a,!0),Event.stop(a))},draw:function(a){var b=this.element.cumulativeOffset();if(this.options.ghosting){var c=Position.realOffset(this.element);b[0]+=c[0]-Position.deltaX;b[1]+=c[1]-Position.deltaY}c=this.currentDelta();b[0]-=c[0];b[1]-=c[1];this.options.scroll&&this.options.scroll!=window&&this._isScrollChild&&(b[0]-=this.options.scroll.scrollLeft-this.originalScrollLeft,b[1]-=this.options.scroll.scrollTop-this.originalScrollTop);
c=[0,1].map(function(c){return a[c]-b[c]-this.offset[c]}.bind(this));this.options.snap&&(c=Object.isFunction(this.options.snap)?this.options.snap(c[0],c[1],this):Object.isArray(this.options.snap)?c.map(function(a,b){return(a/this.options.snap[b]).round()*this.options.snap[b]}.bind(this)):c.map(function(a){return(a/this.options.snap).round()*this.options.snap}.bind(this)));var d=this.element.style;if(!this.options.constraint||"horizontal"==this.options.constraint)d.left=c[0]+"px";if(!this.options.constraint||
"vertical"==this.options.constraint)d.top=c[1]+"px";if("hidden"==d.visibility)d.visibility=""},stopScrolling:function(){if(this.scrollInterval)clearInterval(this.scrollInterval),this.scrollInterval=null,Draggables._lastScrollPointer=null},startScrolling:function(a){if(a[0]||a[1])this.scrollSpeed=[a[0]*this.options.scrollSpeed,a[1]*this.options.scrollSpeed],this.lastScrolled=new Date,this.scrollInterval=setInterval(this.scroll.bind(this),10)},scroll:function(){var a=new Date,b=a-this.lastScrolled;
this.lastScrolled=a;if(this.options.scroll==window)with(this._getWindowScroll(this.options.scroll)){if(this.scrollSpeed[0]||this.scrollSpeed[1])a=b/1E3,this.options.scroll.scrollTo(left+a*this.scrollSpeed[0],top+a*this.scrollSpeed[1])}else this.options.scroll.scrollLeft+=this.scrollSpeed[0]*b/1E3,this.options.scroll.scrollTop+=this.scrollSpeed[1]*b/1E3;Position.prepare();Droppables.show(Draggables._lastPointer,this.element);Draggables.notify("onDrag",this);if(this._isScrollChild)Draggables._lastScrollPointer=
Draggables._lastScrollPointer||$A(Draggables._lastPointer),Draggables._lastScrollPointer[0]+=this.scrollSpeed[0]*b/1E3,Draggables._lastScrollPointer[1]+=this.scrollSpeed[1]*b/1E3,0>Draggables._lastScrollPointer[0]&&(Draggables._lastScrollPointer[0]=0),0>Draggables._lastScrollPointer[1]&&(Draggables._lastScrollPointer[1]=0),this.draw(Draggables._lastScrollPointer);this.options.change&&this.options.change(this)},_getWindowScroll:function(a){var b,c,d;with(a.document){if(a.document.documentElement&&
documentElement.scrollTop)b=documentElement.scrollTop,c=documentElement.scrollLeft;else if(a.document.body)b=body.scrollTop,c=body.scrollLeft;a.innerWidth?(d=a.innerWidth,a=a.innerHeight):a.document.documentElement&&documentElement.clientWidth?(d=documentElement.clientWidth,a=documentElement.clientHeight):(d=body.offsetWidth,a=body.offsetHeight)}return{top:b,left:c,width:d,height:a}}});Draggable._dragging={};
var SortableObserver=Class.create({initialize:function(a,b){this.element=$(a);this.observer=b;this.lastValue=Sortable.serialize(this.element)},onStart:function(){this.lastValue=Sortable.serialize(this.element)},onEnd:function(){Sortable.unmark();this.lastValue!=Sortable.serialize(this.element)&&this.observer(this.element)}}),Sortable={SERIALIZE_RULE:/^[^_\-](?:[A-Za-z0-9\-\_]*)[_](.*)$/,sortables:{},_findRootElement:function(a){for(;"BODY"!=a.tagName.toUpperCase();){if(a.id&&Sortable.sortables[a.id])return a;
a=a.parentNode}},options:function(a){a=Sortable._findRootElement($(a));return!a?void 0:Sortable.sortables[a.id]},destroy:function(a){a=$(a);if(a=Sortable.sortables[a.id])Draggables.removeObserver(a.element),a.droppables.each(function(a){Droppables.remove(a)}),a.draggables.invoke("destroy"),delete Sortable.sortables[a.element.id]},create:function(a,b){var a=$(a),c=Object.extend({element:a,tag:"li",dropOnEmpty:!1,tree:!1,treeTag:"ul",overlap:"vertical",constraint:"vertical",containment:a,handle:!1,
only:!1,delay:0,hoverclass:null,ghosting:!1,quiet:!1,scroll:!1,scrollSensitivity:20,scrollSpeed:15,format:this.SERIALIZE_RULE,elements:!1,handles:!1,onChange:Prototype.emptyFunction,onUpdate:Prototype.emptyFunction},b||{});this.destroy(a);var d={revert:!0,quiet:c.quiet,scroll:c.scroll,scrollSpeed:c.scrollSpeed,scrollSensitivity:c.scrollSensitivity,delay:c.delay,ghosting:c.ghosting,constraint:c.constraint,handle:c.handle};if(c.starteffect)d.starteffect=c.starteffect;if(c.reverteffect)d.reverteffect=
c.reverteffect;else if(c.ghosting)d.reverteffect=function(a){a.style.top=0;a.style.left=0};if(c.endeffect)d.endeffect=c.endeffect;if(c.zindex)d.zindex=c.zindex;var e={overlap:c.overlap,containment:c.containment,tree:c.tree,hoverclass:c.hoverclass,onHover:Sortable.onHover},f={onHover:Sortable.onEmptyHover,overlap:c.overlap,containment:c.containment,hoverclass:c.hoverclass};Element.cleanWhitespace(a);c.draggables=[];c.droppables=[];if(c.dropOnEmpty||c.tree)Droppables.add(a,f),c.droppables.push(a);(c.elements||
this.findElements(a,c)||[]).each(function(b,f){var h=c.handles?$(c.handles[f]):c.handle?$(b).select("."+c.handle)[0]:b;c.draggables.push(new Draggable(b,Object.extend(d,{handle:h})));Droppables.add(b,e);if(c.tree)b.treeNode=a;c.droppables.push(b)});c.tree&&(Sortable.findTreeElements(a,c)||[]).each(function(b){Droppables.add(b,f);b.treeNode=a;c.droppables.push(b)});this.sortables[a.identify()]=c;Draggables.addObserver(new SortableObserver(a,c.onUpdate))},findElements:function(a,b){return Element.findChildren(a,
b.only,b.tree?!0:!1,b.tag)},findTreeElements:function(a,b){return Element.findChildren(a,b.only,b.tree?!0:!1,b.treeTag)},onHover:function(a,b,c){if(!Element.isParent(b,a)&&!(0.33<c&&0.66>c&&Sortable.options(b).tree))if(0.5<c){if(Sortable.mark(b,"before"),b.previousSibling!=a){c=a.parentNode;a.style.visibility="hidden";b.parentNode.insertBefore(a,b);if(b.parentNode!=c)Sortable.options(c).onChange(a);Sortable.options(b.parentNode).onChange(a)}}else{Sortable.mark(b,"after");var d=b.nextSibling||null;
if(d!=a){c=a.parentNode;a.style.visibility="hidden";b.parentNode.insertBefore(a,d);if(b.parentNode!=c)Sortable.options(c).onChange(a);Sortable.options(b.parentNode).onChange(a)}}},onEmptyHover:function(a,b,c){var d=a.parentNode,e=Sortable.options(b);if(!Element.isParent(b,a)){var f=Sortable.findElements(b,{tag:e.tag,only:e.only}),g=null;if(f)for(var j=Element.offsetSize(b,e.overlap)*(1-c),c=0;c<f.length;c+=1)if(0<=j-Element.offsetSize(f[c],e.overlap))j-=Element.offsetSize(f[c],e.overlap);else{g=0<=
j-Element.offsetSize(f[c],e.overlap)/2?c+1<f.length?f[c+1]:null:f[c];break}b.insertBefore(a,g);Sortable.options(d).onChange(a);e.onChange(a)}},unmark:function(){Sortable._marker&&Sortable._marker.hide()},mark:function(a,b){var c=Sortable.options(a.parentNode);if(!c||c.ghosting){if(!Sortable._marker)Sortable._marker=($("dropmarker")||Element.extend(document.createElement("DIV"))).hide().addClassName("dropmarker").setStyle({position:"absolute"}),document.getElementsByTagName("body").item(0).appendChild(Sortable._marker);
var d=a.cumulativeOffset();Sortable._marker.setStyle({left:d[0]+"px",top:d[1]+"px"});"after"==b&&("horizontal"==c.overlap?Sortable._marker.setStyle({left:d[0]+a.clientWidth+"px"}):Sortable._marker.setStyle({top:d[1]+a.clientHeight+"px"}));Sortable._marker.show()}},_tree:function(a,b,c){for(var d=Sortable.findElements(a,b)||[],e=0;e<d.length;++e){var f=d[e].id.match(b.format);f&&(f={id:encodeURIComponent(f?f[1]:null),element:a,parent:c,children:[],position:c.children.length,container:$(d[e]).down(b.treeTag)},
f.container&&this._tree(f.container,b,f),c.children.push(f))}return c},tree:function(a,b){var a=$(a),c=this.options(a),c=Object.extend({tag:c.tag,treeTag:c.treeTag,only:c.only,name:a.id,format:c.format},b||{});return Sortable._tree(a,c,{id:null,parent:null,children:[],container:a,position:0})},_constructIndex:function(a){var b="";do a.id&&(b="["+a.position+"]"+b);while(null!=(a=a.parent));return b},sequence:function(a,b){var a=$(a),c=Object.extend(this.options(a),b||{});return $(this.findElements(a,
c)||[]).map(function(a){return a.id.match(c.format)?a.id.match(c.format)[1]:""})},setSequence:function(a,b,c){var a=$(a),d=Object.extend(this.options(a),c||{}),e={};this.findElements(a,d).each(function(a){a.id.match(d.format)&&(e[a.id.match(d.format)[1]]=[a,a.parentNode]);a.parentNode.removeChild(a)});b.each(function(a){var b=e[a];b&&(b[1].appendChild(b[0]),delete e[a])})},serialize:function(a,b){var a=$(a),c=Object.extend(Sortable.options(a),b||{}),d=encodeURIComponent(b&&b.name?b.name:a.id);return c.tree?
Sortable.tree(a,b).children.map(function(a){return[d+Sortable._constructIndex(a)+"[id]="+encodeURIComponent(a.id)].concat(a.children.map(arguments.callee))}).flatten().join("&"):Sortable.sequence(a,b).map(function(a){return d+"[]="+encodeURIComponent(a)}).join("&")}};Element.isParent=function(a,b){return!a.parentNode||a==b?!1:a.parentNode==b?!0:Element.isParent(a.parentNode,b)};
Element.findChildren=function(a,b,c,d){if(!a.hasChildNodes())return null;d=d.toUpperCase();b&&(b=[b].flatten());var e=[];$A(a.childNodes).each(function(a){a.tagName&&a.tagName.toUpperCase()==d&&(!b||Element.classNames(a).detect(function(a){return b.include(a)}))&&e.push(a);c&&(a=Element.findChildren(a,b,c,d))&&e.push(a)});return 0<e.length?e.flatten():[]};Element.offsetSize=function(a,b){return a["offset"+("vertical"==b||"height"==b?"Height":"Width")]};if("undefined"==typeof Effect)throw"controls.js requires including script.aculo.us' effects.js library";var Autocompleter={};
Autocompleter.Base=Class.create({baseInitialize:function(a,b,c){this.element=a=$(a);this.update=$(b);this.active=this.changed=this.hasFocus=!1;this.entryCount=this.index=0;this.oldElementValue=this.element.value;this.setOptions?this.setOptions(c):this.options=c||{};this.options.paramName=this.options.paramName||this.element.name;this.options.tokens=this.options.tokens||[];this.options.frequency=this.options.frequency||0.4;this.options.minChars=this.options.minChars||1;this.options.onShow=this.options.onShow||
function(a,b){if(!b.style.position||"absolute"==b.style.position)b.style.position="absolute",Position.clone(a,b,{setHeight:!1,offsetTop:a.offsetHeight});Effect.Appear(b,{duration:0.15})};this.options.onHide=this.options.onHide||function(a,b){new Effect.Fade(b,{duration:0.15})};if("string"==typeof this.options.tokens)this.options.tokens=Array(this.options.tokens);this.options.tokens.include("\n")||this.options.tokens.push("\n");this.observer=null;this.element.setAttribute("autocomplete","off");Element.hide(this.update);
Event.observe(this.element,"blur",this.onBlur.bindAsEventListener(this));Event.observe(this.element,"keydown",this.onKeyPress.bindAsEventListener(this))},show:function(){if("none"==Element.getStyle(this.update,"display"))this.options.onShow(this.element,this.update);if(!this.iefix&&Prototype.Browser.IE&&"absolute"==Element.getStyle(this.update,"position"))new Insertion.After(this.update,'<iframe id="'+this.update.id+'_iefix" style="display:none;position:absolute;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);" src="javascript:false;" frameborder="0" scrolling="no"></iframe>'),
this.iefix=$(this.update.id+"_iefix");this.iefix&&setTimeout(this.fixIEOverlapping.bind(this),50)},fixIEOverlapping:function(){Position.clone(this.update,this.iefix,{setTop:!this.update.style.height});this.iefix.style.zIndex=1;this.update.style.zIndex=2;Element.show(this.iefix)},hide:function(){this.stopIndicator();if("none"!=Element.getStyle(this.update,"display"))this.options.onHide(this.element,this.update);this.iefix&&Element.hide(this.iefix)},startIndicator:function(){this.options.indicator&&
Element.show(this.options.indicator)},stopIndicator:function(){this.options.indicator&&Element.hide(this.options.indicator)},onKeyPress:function(a){if(this.active)switch(a.keyCode){case Event.KEY_TAB:case Event.KEY_RETURN:this.selectEntry(),Event.stop(a);case Event.KEY_ESC:this.hide();this.active=!1;Event.stop(a);return;case Event.KEY_LEFT:case Event.KEY_RIGHT:return;case Event.KEY_UP:this.markPrevious();this.render();Event.stop(a);return;case Event.KEY_DOWN:this.markNext();this.render();Event.stop(a);
return}else if(a.keyCode==Event.KEY_TAB||a.keyCode==Event.KEY_RETURN||0<Prototype.Browser.WebKit&&0==a.keyCode)return;this.hasFocus=this.changed=!0;this.observer&&clearTimeout(this.observer);this.observer=setTimeout(this.onObserverEvent.bind(this),1E3*this.options.frequency)},activate:function(){this.changed=!1;this.hasFocus=!0;this.getUpdatedChoices()},onHover:function(a){var b=Event.findElement(a,"LI");if(this.index!=b.autocompleteIndex)this.index=b.autocompleteIndex,this.render();Event.stop(a)},
onClick:function(a){this.index=Event.findElement(a,"LI").autocompleteIndex;this.selectEntry();this.hide()},onBlur:function(){setTimeout(this.hide.bind(this),250);this.active=this.hasFocus=!1},render:function(){if(0<this.entryCount){for(var a=0;a<this.entryCount;a++)this.index==a?Element.addClassName(this.getEntry(a),"selected"):Element.removeClassName(this.getEntry(a),"selected");if(this.hasFocus)this.show(),this.active=!0}else this.active=!1,this.hide()},markPrevious:function(){0<this.index?this.index--:
this.index=this.entryCount-1;this.getEntry(this.index).scrollIntoView(!0)},markNext:function(){this.index<this.entryCount-1?this.index++:this.index=0;this.getEntry(this.index).scrollIntoView(!1)},getEntry:function(a){return this.update.firstChild.childNodes[a]},getCurrentEntry:function(){return this.getEntry(this.index)},selectEntry:function(){this.active=!1;this.updateElement(this.getCurrentEntry())},updateElement:function(a){if(this.options.updateElement)this.options.updateElement(a);else{var b=
"";if(this.options.select){var c=$(a).select("."+this.options.select)||[];0<c.length&&(b=Element.collectTextNodes(c[0],this.options.select))}else b=Element.collectTextNodesIgnoreClass(a,"informal");c=this.getTokenBounds();if(-1!=c[0]){var d=this.element.value.substr(0,c[0]),e=this.element.value.substr(c[0]).match(/^\s+/);e&&(d+=e[0]);this.element.value=d+b+this.element.value.substr(c[1])}else this.element.value=b;this.oldElementValue=this.element.value;this.element.focus();this.options.afterUpdateElement&&
this.options.afterUpdateElement(this.element,a)}},updateChoices:function(a){if(!this.changed&&this.hasFocus){this.update.innerHTML=a;Element.cleanWhitespace(this.update);Element.cleanWhitespace(this.update.down());if(this.update.firstChild&&this.update.down().childNodes){this.entryCount=this.update.down().childNodes.length;for(a=0;a<this.entryCount;a++){var b=this.getEntry(a);b.autocompleteIndex=a;this.addObservers(b)}}else this.entryCount=0;this.stopIndicator();this.index=0;1==this.entryCount&&this.options.autoSelect?
(this.selectEntry(),this.hide()):this.render()}},addObservers:function(a){Event.observe(a,"mouseover",this.onHover.bindAsEventListener(this));Event.observe(a,"click",this.onClick.bindAsEventListener(this))},onObserverEvent:function(){this.changed=!1;this.tokenBounds=null;this.getToken().length>=this.options.minChars?this.getUpdatedChoices():(this.active=!1,this.hide());this.oldElementValue=this.element.value},getToken:function(){var a=this.getTokenBounds();return this.element.value.substring(a[0],
a[1]).strip()},getTokenBounds:function(){if(null!=this.tokenBounds)return this.tokenBounds;var a=this.element.value;if(a.strip().empty())return[-1,0];for(var b=arguments.callee.getFirstDifferencePos(a,this.oldElementValue),c=b==this.oldElementValue.length?1:0,d=-1,e=a.length,f,g=0,j=this.options.tokens.length;g<j;++g)f=a.lastIndexOf(this.options.tokens[g],b+c-1),f>d&&(d=f),f=a.indexOf(this.options.tokens[g],b+c),-1!=f&&f<e&&(e=f);return this.tokenBounds=[d+1,e]}});
Autocompleter.Base.prototype.getTokenBounds.getFirstDifferencePos=function(a,b){for(var c=Math.min(a.length,b.length),d=0;d<c;++d)if(a[d]!=b[d])return d;return c};
Ajax.Autocompleter=Class.create(Autocompleter.Base,{initialize:function(a,b,c,d){this.baseInitialize(a,b,d);this.options.asynchronous=!0;this.options.onComplete=this.onComplete.bind(this);this.options.defaultParams=this.options.parameters||null;this.url=c},getUpdatedChoices:function(){this.startIndicator();var a=encodeURIComponent(this.options.paramName)+"="+encodeURIComponent(this.getToken());this.options.parameters=this.options.callback?this.options.callback(this.element,a):a;this.options.defaultParams&&
(this.options.parameters+="&"+this.options.defaultParams);new Ajax.Request(this.url,this.options)},onComplete:function(a){this.updateChoices(a.responseText)}});
Autocompleter.Local=Class.create(Autocompleter.Base,{initialize:function(a,b,c,d){this.baseInitialize(a,b,d);this.options.array=c},getUpdatedChoices:function(){this.updateChoices(this.options.selector(this))},setOptions:function(a){this.options=Object.extend({choices:10,partialSearch:!0,partialChars:2,ignoreCase:!0,fullSearch:!1,selector:function(a){for(var c=[],d=[],e=a.getToken(),f=0;f<a.options.array.length&&c.length<a.options.choices;f++)for(var g=a.options.array[f],j=a.options.ignoreCase?g.toLowerCase().indexOf(e.toLowerCase()):
g.indexOf(e);-1!=j;){if(0==j&&g.length!=e.length){c.push("<li><strong>"+g.substr(0,e.length)+"</strong>"+g.substr(e.length)+"</li>");break}else if(e.length>=a.options.partialChars&&a.options.partialSearch&&-1!=j&&(a.options.fullSearch||/\s/.test(g.substr(j-1,1)))){d.push("<li>"+g.substr(0,j)+"<strong>"+g.substr(j,e.length)+"</strong>"+g.substr(j+e.length)+"</li>");break}j=a.options.ignoreCase?g.toLowerCase().indexOf(e.toLowerCase(),j+1):g.indexOf(e,j+1)}d.length&&(c=c.concat(d.slice(0,a.options.choices-
c.length)));return"<ul>"+c.join("")+"</ul>"}},a||{})}});Field.scrollFreeActivate=function(a){setTimeout(function(){Field.activate(a)},1)};
Ajax.InPlaceEditor=Class.create({initialize:function(a,b,c){this.url=b;this.element=a=$(a);this.prepareOptions();this._controls={};arguments.callee.dealWithDeprecatedOptions(c);Object.extend(this.options,c||{});if(!this.options.formId&&this.element.id&&(this.options.formId=this.element.id+"-inplaceeditor",$(this.options.formId)))this.options.formId="";if(this.options.externalControl)this.options.externalControl=$(this.options.externalControl);if(!this.options.externalControl)this.options.externalControlOnly=
!1;this._originalBackground=this.element.getStyle("background-color")||"transparent";this.element.title=this.options.clickToEditText;this._boundCancelHandler=this.handleFormCancellation.bind(this);this._boundComplete=(this.options.onComplete||Prototype.emptyFunction).bind(this);this._boundFailureHandler=this.handleAJAXFailure.bind(this);this._boundSubmitHandler=this.handleFormSubmission.bind(this);this._boundWrapperHandler=this.wrapUp.bind(this);this.registerListeners()},checkForEscapeOrReturn:function(a){this._editing&&
!a.ctrlKey&&!a.altKey&&!a.shiftKey&&(Event.KEY_ESC==a.keyCode?this.handleFormCancellation(a):Event.KEY_RETURN==a.keyCode&&this.handleFormSubmission(a))},createControl:function(a,b,c){var d=this.options[a+"Control"],b=this.options[a+"Text"];if("button"==d){c=document.createElement("input");c.type="submit";c.value=b;c.className="editor_"+a+"_button";if("cancel"==a)c.onclick=this._boundCancelHandler;this._form.appendChild(c);this._controls[a]=c}else if("link"==d)d=document.createElement("a"),d.href=
"#",d.appendChild(document.createTextNode(b)),d.onclick="cancel"==a?this._boundCancelHandler:this._boundSubmitHandler,d.className="editor_"+a+"_link",c&&(d.className+=" "+c),this._form.appendChild(d),this._controls[a]=d},createEditField:function(){var a=this.options.loadTextURL?this.options.loadingText:this.getText(),b;if(1>=this.options.rows&&!/\r|\n/.test(this.getText())){b=document.createElement("input");b.type="text";var c=this.options.size||this.options.cols||0;if(0<c)b.size=c}else b=document.createElement("textarea"),
b.rows=1>=this.options.rows?this.options.autoRows:this.options.rows,b.cols=this.options.cols||40;b.name=this.options.paramName;b.value=a;b.className="editor_field";if(this.options.submitOnBlur)b.onblur=this._boundSubmitHandler;this._controls.editor=b;this.options.loadTextURL&&this.loadExternalText();this._form.appendChild(this._controls.editor)},createForm:function(){function a(a,d){var e=b.options["text"+a+"Controls"];e&&!1!==d&&b._form.appendChild(document.createTextNode(e))}var b=this;this._form=
$(document.createElement("form"));this._form.id=this.options.formId;this._form.addClassName(this.options.formClassName);this._form.onsubmit=this._boundSubmitHandler;this.createEditField();"textarea"==this._controls.editor.tagName.toLowerCase()&&this._form.appendChild(document.createElement("br"));if(this.options.onFormCustomization)this.options.onFormCustomization(this,this._form);a("Before",this.options.okControl||this.options.cancelControl);this.createControl("ok",this._boundSubmitHandler);a("Between",
this.options.okControl&&this.options.cancelControl);this.createControl("cancel",this._boundCancelHandler,"editor_cancel");a("After",this.options.okControl||this.options.cancelControl)},destroy:function(){if(this._oldInnerHTML)this.element.innerHTML=this._oldInnerHTML;this.leaveEditMode();this.unregisterListeners()},enterEditMode:function(a){if(!this._saving&&!this._editing)this._editing=!0,this.triggerCallback("onEnterEditMode"),this.options.externalControl&&this.options.externalControl.hide(),this.element.hide(),
this.createForm(),this.element.parentNode.insertBefore(this._form,this.element),this.options.loadTextURL||this.postProcessEditField(),a&&Event.stop(a)},enterHover:function(){this.options.hoverClassName&&this.element.addClassName(this.options.hoverClassName);this._saving||this.triggerCallback("onEnterHover")},getText:function(){return this.element.innerHTML.unescapeHTML()},handleAJAXFailure:function(a){this.triggerCallback("onFailure",a);if(this._oldInnerHTML)this.element.innerHTML=this._oldInnerHTML,
this._oldInnerHTML=null},handleFormCancellation:function(a){this.wrapUp();a&&Event.stop(a)},handleFormSubmission:function(a){var b=this._form,c=$F(this._controls.editor);this.prepareSubmission();b=this.options.callback(b,c)||"";Object.isString(b)&&(b=b.toQueryParams());b.editorId=this.element.id;this.options.htmlResponse?(c=Object.extend({evalScripts:!0},this.options.ajaxOptions),Object.extend(c,{parameters:b,onComplete:this._boundWrapperHandler,onFailure:this._boundFailureHandler}),new Ajax.Updater({success:this.element},
this.url,c)):(c=Object.extend({method:"get"},this.options.ajaxOptions),Object.extend(c,{parameters:b,onComplete:this._boundWrapperHandler,onFailure:this._boundFailureHandler}),new Ajax.Request(this.url,c));a&&Event.stop(a)},leaveEditMode:function(){this.element.removeClassName(this.options.savingClassName);this.removeForm();this.leaveHover();this.element.style.backgroundColor=this._originalBackground;this.element.show();this.options.externalControl&&this.options.externalControl.show();this._editing=
this._saving=!1;this._oldInnerHTML=null;this.triggerCallback("onLeaveEditMode")},leaveHover:function(){this.options.hoverClassName&&this.element.removeClassName(this.options.hoverClassName);this._saving||this.triggerCallback("onLeaveHover")},loadExternalText:function(){this._form.addClassName(this.options.loadingClassName);this._controls.editor.disabled=!0;var a=Object.extend({method:"get"},this.options.ajaxOptions);Object.extend(a,{parameters:"editorId="+encodeURIComponent(this.element.id),onComplete:Prototype.emptyFunction,
onSuccess:function(a){this._form.removeClassName(this.options.loadingClassName);a=a.responseText;this.options.stripLoadedTextTags&&(a=a.stripTags());this._controls.editor.value=a;this._controls.editor.disabled=!1;this.postProcessEditField()}.bind(this),onFailure:this._boundFailureHandler});new Ajax.Request(this.options.loadTextURL,a)},postProcessEditField:function(){var a=this.options.fieldPostCreation;if(a)$(this._controls.editor)["focus"==a?"focus":"activate"]()},prepareOptions:function(){this.options=
Object.clone(Ajax.InPlaceEditor.DefaultOptions);Object.extend(this.options,Ajax.InPlaceEditor.DefaultCallbacks);[this._extraDefaultOptions].flatten().compact().each(function(a){Object.extend(this.options,a)}.bind(this))},prepareSubmission:function(){this._saving=!0;this.removeForm();this.leaveHover();this.showSaving()},registerListeners:function(){this._listeners={};var a;$H(Ajax.InPlaceEditor.Listeners).each(function(b){a=this[b.value].bind(this);this._listeners[b.key]=a;this.options.externalControlOnly||
this.element.observe(b.key,a);this.options.externalControl&&this.options.externalControl.observe(b.key,a)}.bind(this))},removeForm:function(){if(this._form)this._form.remove(),this._form=null,this._controls={}},showSaving:function(){this._oldInnerHTML=this.element.innerHTML;this.element.innerHTML=this.options.savingText;this.element.addClassName(this.options.savingClassName);this.element.style.backgroundColor=this._originalBackground;this.element.show()},triggerCallback:function(a,b){if("function"==
typeof this.options[a])this.options[a](this,b)},unregisterListeners:function(){$H(this._listeners).each(function(a){this.options.externalControlOnly||this.element.stopObserving(a.key,a.value);this.options.externalControl&&this.options.externalControl.stopObserving(a.key,a.value)}.bind(this))},wrapUp:function(a){this.leaveEditMode();this._boundComplete(a,this.element)}});Object.extend(Ajax.InPlaceEditor.prototype,{dispose:Ajax.InPlaceEditor.prototype.destroy});
Ajax.InPlaceCollectionEditor=Class.create(Ajax.InPlaceEditor,{initialize:function($super,b,c,d){this._extraDefaultOptions=Ajax.InPlaceCollectionEditor.DefaultOptions;$super(b,c,d)},createEditField:function(){var a=document.createElement("select");a.name=this.options.paramName;a.size=1;this._controls.editor=a;this._collection=this.options.collection||[];this.options.loadCollectionURL?this.loadCollection():this.checkForExternalText();this._form.appendChild(this._controls.editor)},loadCollection:function(){this._form.addClassName(this.options.loadingClassName);
this.showLoadingText(this.options.loadingCollectionText);var a=Object.extend({method:"get"},this.options.ajaxOptions);Object.extend(a,{parameters:"editorId="+encodeURIComponent(this.element.id),onComplete:Prototype.emptyFunction,onSuccess:function(a){a=a.responseText.strip();if(!/^\[.*\]$/.test(a))throw"Server returned an invalid collection representation.";this._collection=eval(a);this.checkForExternalText()}.bind(this),onFailure:this.onFailure});new Ajax.Request(this.options.loadCollectionURL,a)},
showLoadingText:function(a){this._controls.editor.disabled=!0;var b=this._controls.editor.firstChild;if(!b)b=document.createElement("option"),b.value="",this._controls.editor.appendChild(b),b.selected=!0;b.update((a||"").stripScripts().stripTags())},checkForExternalText:function(){this._text=this.getText();this.options.loadTextURL?this.loadExternalText():this.buildOptionList()},loadExternalText:function(){this.showLoadingText(this.options.loadingText);var a=Object.extend({method:"get"},this.options.ajaxOptions);
Object.extend(a,{parameters:"editorId="+encodeURIComponent(this.element.id),onComplete:Prototype.emptyFunction,onSuccess:function(a){this._text=a.responseText.strip();this.buildOptionList()}.bind(this),onFailure:this.onFailure});new Ajax.Request(this.options.loadTextURL,a)},buildOptionList:function(){this._form.removeClassName(this.options.loadingClassName);this._collection=this._collection.map(function(a){return 2===a.length?a:[a,a].flatten()});var a="value"in this.options?this.options.value:this._text,
b=this._collection.any(function(b){return b[0]==a}.bind(this));this._controls.editor.update("");var c;this._collection.each(function(d,e){c=document.createElement("option");c.value=d[0];c.selected=b?d[0]==a:0==e;c.appendChild(document.createTextNode(d[1]));this._controls.editor.appendChild(c)}.bind(this));this._controls.editor.disabled=!1;Field.scrollFreeActivate(this._controls.editor)}});
Ajax.InPlaceEditor.prototype.initialize.dealWithDeprecatedOptions=function(a){function b(b,d){b in a||void 0===d||(a[b]=d)}a&&(b("cancelControl",a.cancelLink?"link":a.cancelButton?"button":!1==(a.cancelLink==a.cancelButton)?!1:void 0),b("okControl",a.okLink?"link":a.okButton?"button":!1==(a.okLink==a.okButton)?!1:void 0),b("highlightColor",a.highlightcolor),b("highlightEndColor",a.highlightendcolor))};
Object.extend(Ajax.InPlaceEditor,{DefaultOptions:{ajaxOptions:{},autoRows:3,cancelControl:"link",cancelText:"cancel",clickToEditText:"Click to edit",externalControl:null,externalControlOnly:!1,fieldPostCreation:"activate",formClassName:"inplaceeditor-form",formId:null,highlightColor:"#ffff99",highlightEndColor:"#ffffff",hoverClassName:"",htmlResponse:!0,loadingClassName:"inplaceeditor-loading",loadingText:"Loading...",okControl:"button",okText:"ok",paramName:"value",rows:1,savingClassName:"inplaceeditor-saving",
savingText:"Saving...",size:0,stripLoadedTextTags:!1,submitOnBlur:!1,textAfterControls:"",textBeforeControls:"",textBetweenControls:""},DefaultCallbacks:{callback:function(a){return Form.serialize(a)},onComplete:function(a,b){new Effect.Highlight(b,{startcolor:this.options.highlightColor,keepBackgroundImage:!0})},onEnterEditMode:null,onEnterHover:function(a){a.element.style.backgroundColor=a.options.highlightColor;a._effect&&a._effect.cancel()},onFailure:function(a){alert("Error communication with the server: "+
a.responseText.stripTags())},onFormCustomization:null,onLeaveEditMode:null,onLeaveHover:function(a){a._effect=new Effect.Highlight(a.element,{startcolor:a.options.highlightColor,endcolor:a.options.highlightEndColor,restorecolor:a._originalBackground,keepBackgroundImage:!0})}},Listeners:{click:"enterEditMode",keydown:"checkForEscapeOrReturn",mouseover:"enterHover",mouseout:"leaveHover"}});Ajax.InPlaceCollectionEditor.DefaultOptions={loadingCollectionText:"Loading options..."};
Form.Element.DelayedObserver=Class.create({initialize:function(a,b,c){this.delay=b||0.5;this.element=$(a);this.callback=c;this.timer=null;this.lastValue=$F(this.element);Event.observe(this.element,"keyup",this.delayedListener.bindAsEventListener(this))},delayedListener:function(){if(this.lastValue!=$F(this.element))this.timer&&clearTimeout(this.timer),this.timer=setTimeout(this.onTimerEvent.bind(this),1E3*this.delay),this.lastValue=$F(this.element)},onTimerEvent:function(){this.timer=null;this.callback(this.element,
$F(this.element))}});if(!Control)var Control={};
Control.Slider=Class.create({initialize:function(a,b,c){var d=this;this.handles=Object.isArray(a)?a.collect(function(a){return $(a)}):[$(a)];this.track=$(b);this.options=c||{};this.axis=this.options.axis||"horizontal";this.increment=this.options.increment||1;this.step=parseInt(this.options.step||"1");this.range=this.options.range||$R(0,1);this.value=0;this.values=this.handles.map(function(){return 0});this.spans=this.options.spans?this.options.spans.map(function(a){return $(a)}):!1;this.options.startSpan=
$(this.options.startSpan||null);this.options.endSpan=$(this.options.endSpan||null);this.restricted=this.options.restricted||!1;this.maximum=this.options.maximum||this.range.end;this.minimum=this.options.minimum||this.range.start;this.alignX=parseInt(this.options.alignX||"0");this.alignY=parseInt(this.options.alignY||"0");this.trackLength=this.maximumOffset()-this.minimumOffset();this.handleLength=this.isVertical()?0!=this.handles[0].offsetHeight?this.handles[0].offsetHeight:this.handles[0].style.height.replace(/px$/,
""):0!=this.handles[0].offsetWidth?this.handles[0].offsetWidth:this.handles[0].style.width.replace(/px$/,"");this.disabled=this.dragging=this.active=!1;this.options.disabled&&this.setDisabled();if(this.allowedValues=this.options.values?this.options.values.sortBy(Prototype.K):!1)this.minimum=this.allowedValues.min(),this.maximum=this.allowedValues.max();this.eventMouseDown=this.startDrag.bindAsEventListener(this);this.eventMouseUp=this.endDrag.bindAsEventListener(this);this.eventMouseMove=this.update.bindAsEventListener(this);
this.handles.each(function(a,b){b=d.handles.length-1-b;d.setValue(parseFloat((Object.isArray(d.options.sliderValue)?d.options.sliderValue[b]:d.options.sliderValue)||d.range.start),b);a.makePositioned().observe("mousedown",d.eventMouseDown)});this.track.observe("mousedown",this.eventMouseDown);document.observe("mouseup",this.eventMouseUp);document.observe("mousemove",this.eventMouseMove);this.initialized=!0},dispose:function(){var a=this;Event.stopObserving(this.track,"mousedown",this.eventMouseDown);
Event.stopObserving(document,"mouseup",this.eventMouseUp);Event.stopObserving(document,"mousemove",this.eventMouseMove);this.handles.each(function(b){Event.stopObserving(b,"mousedown",a.eventMouseDown)})},setDisabled:function(){this.disabled=!0},setEnabled:function(){this.disabled=!1},getNearestValue:function(a){if(this.allowedValues){if(a>=this.allowedValues.max())return this.allowedValues.max();if(a<=this.allowedValues.min())return this.allowedValues.min();var b=Math.abs(this.allowedValues[0]-a),
c=this.allowedValues[0];this.allowedValues.each(function(d){var e=Math.abs(d-a);e<=b&&(c=d,b=e)});return c}return a>this.range.end?this.range.end:a<this.range.start?this.range.start:a},setValue:function(a,b){if(!this.active)this.activeHandleIdx=b||0,this.activeHandle=this.handles[this.activeHandleIdx],this.updateStyles();b=b||this.activeHandleIdx||0;this.initialized&&this.restricted&&(0<b&&a<this.values[b-1]&&(a=this.values[b-1]),b<this.handles.length-1&&a>this.values[b+1]&&(a=this.values[b+1]));
a=this.getNearestValue(a);this.values[b]=a;this.value=this.values[0];this.handles[b].style[this.isVertical()?"top":"left"]=this.translateToPx(a);this.drawSpans();(!this.dragging||!this.event)&&this.updateFinished()},setValueBy:function(a,b){this.setValue(this.values[b||this.activeHandleIdx||0]+a,b||this.activeHandleIdx||0)},translateToPx:function(a){return Math.round((this.trackLength-this.handleLength)/(this.range.end-this.range.start)*(a-this.range.start))+"px"},translateToValue:function(a){return a/
(this.trackLength-this.handleLength)*(this.range.end-this.range.start)+this.range.start},getRange:function(a){var b=this.values.sortBy(Prototype.K),a=a||0;return $R(b[a],b[a+1])},minimumOffset:function(){return this.isVertical()?this.alignY:this.alignX},maximumOffset:function(){return this.isVertical()?(0!=this.track.offsetHeight?this.track.offsetHeight:this.track.style.height.replace(/px$/,""))-this.alignY:(0!=this.track.offsetWidth?this.track.offsetWidth:this.track.style.width.replace(/px$/,""))-
this.alignX},isVertical:function(){return"vertical"==this.axis},drawSpans:function(){var a=this;this.spans&&$R(0,this.spans.length-1).each(function(b){a.setSpan(a.spans[b],a.getRange(b))});this.options.startSpan&&this.setSpan(this.options.startSpan,$R(0,1<this.values.length?this.getRange(0).min():this.value));this.options.endSpan&&this.setSpan(this.options.endSpan,$R(1<this.values.length?this.getRange(this.spans.length-1).max():this.value,this.maximum))},setSpan:function(a,b){this.isVertical()?(a.style.top=
this.translateToPx(b.start),a.style.height=this.translateToPx(b.end-b.start+this.range.start)):(a.style.left=this.translateToPx(b.start),a.style.width=this.translateToPx(b.end-b.start+this.range.start))},updateStyles:function(){this.handles.each(function(a){Element.removeClassName(a,"selected")});Element.addClassName(this.activeHandle,"selected")},startDrag:function(a){if(Event.isLeftClick(a)){if(!this.disabled){this.active=!0;var b=Event.element(a),c=[Event.pointerX(a),Event.pointerY(a)];if(b==this.track)b=
this.track.cumulativeOffset(),this.event=a,this.setValue(this.translateToValue((this.isVertical()?c[1]-b[1]:c[0]-b[0])-this.handleLength/2)),b=this.activeHandle.cumulativeOffset(),this.offsetX=c[0]-b[0],this.offsetY=c[1]-b[1];else{for(;-1==this.handles.indexOf(b)&&b.parentNode;)b=b.parentNode;if(-1!=this.handles.indexOf(b))this.activeHandle=b,this.activeHandleIdx=this.handles.indexOf(this.activeHandle),this.updateStyles(),b=this.activeHandle.cumulativeOffset(),this.offsetX=c[0]-b[0],this.offsetY=
c[1]-b[1]}}Event.stop(a)}},update:function(a){if(this.active){if(!this.dragging)this.dragging=!0;this.draw(a);Prototype.Browser.WebKit&&window.scrollBy(0,0);Event.stop(a)}},draw:function(a){var b=[Event.pointerX(a),Event.pointerY(a)],c=this.track.cumulativeOffset();b[0]-=this.offsetX+c[0];b[1]-=this.offsetY+c[1];this.event=a;this.setValue(this.translateToValue(this.isVertical()?b[1]:b[0]));if(this.initialized&&this.options.onSlide)this.options.onSlide(1<this.values.length?this.values:this.value,this)},
endDrag:function(a){this.active&&this.dragging&&(this.finishDrag(a,!0),Event.stop(a));this.dragging=this.active=!1},finishDrag:function(){this.dragging=this.active=!1;this.updateFinished()},updateFinished:function(){if(this.initialized&&this.options.onChange)this.options.onChange(1<this.values.length?this.values:this.value,this);this.event=null}});



/**
 * Kumbia Enterprise Framework
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@loudertechnology.com so we can send you a copy immediately.
 *
 * @category 	Kumbia
 * @package 	Tag
 * @copyright	Copyright (c) 2008-2012 Louder Technology COL. (http://www.loudertechnology.com)
 * @license 	New BSD License
 * @version 	$Id$
 */

var Base = {

	PROTOTYPE: 1,
	JQUERY: 2,
	EXT: 3,
	MOOTOOLS: 4,

	framework: 0,

	bind: function(){
        var _func = arguments[0] || null;
        var _obj = arguments[1] || this;
        var i = 0;
        var _args = [];
        for(var i=0;i<arguments.length;i++){
        	if(i>1){
        		_args[_args.length] = arguments[i];
        	};
        	i++;
        };
        return function(){
			return _func.apply(_obj, _args);
        };
	},

	_checkFramework: function(){
		if(typeof Prototype != "undefined"){
			Base.activeFramework = Base.PROTOTYPE;
			return;
		};
		if(typeof jQuery != "undefined") {
			Base.activeFramework = Base.JQUERY;
			return;
		};
		if(typeof Ext != "undefined"){
			Base.activeFramework = Base.EXT;
			return;
		};
		if(typeof MooTools != "undefined"){
			Base.activeFramework = Base.MOOTOOLS;
			return;
		};
		return 0;
	},

	$: function(element){
		return document.getElementById(element);
	},

	show: function(element){
		document.getElementById(element).style.display = "";
	},

	hide: function(element){
		document.getElementById(element).style.display = "none";
	},

	setValue: function(element, value){
		document.getElementById(element).value = value;
	},

	getValue: function(element){
		element = document.getElementById(element);
		if(element.tagName=='SELECT'){
			return element.options[element.selectedIndex].value;
		} else {
			return element.value;
		}
	},

	up: function(element, levels){
		var l = 0;
		var finalElement = element;
		while(finalElement){
			finalElement = finalElement.parentNode;
			if(l>=levels){
				return finalElement;
			}
			l++;
		};
		return finalElement;
	}

};

var NumericField = {

	maskNum: function(evt){
		evt = (evt) ? evt : ((window.event) ? window.event : null);
		var kc = evt.keyCode;
		if(!evt.ctrlKey&&!evt.metaKey){
			var ev = (evt.altKey==false)&&(evt.shiftKey==false)&&((kc>=48&&kc<=57)||(kc>=96&&kc<=105)||(kc==8)||(kc==9)||(kc==13)||(kc==17)||(kc==36)||(kc==35)||(kc==37)||(kc==46)||(kc==39)||(kc==190)||(kc==110));
			if(!ev){
				ev = (evt.shiftKey==true&&(kc==9||(kc>=35&&kc<=39)));
				if(!ev){
					ev = (evt.altKey==true&&(kc==84||kc==82));
				};
			};
			if(!ev){
				evt.preventDefault();
	    		evt.stopPropagation();
	    		evt.stopped = true;
	    		return false;
			}
		};
		return true;
	},

	format: function(element){
		if(element.value!==''){
			var integerPart = '';
			var decimalPart = '';
			var decimalPosition = element.value.indexOf('.');
			if(decimalPosition!=-1){
				decimalPart = element.value.substr(decimalPosition);
				integerPart = element.value.substr(0, decimalPosition);
			} else {
				integerPart = element.value;
			};
			document.title = integerPart+' '+decimalPart;
		};
	}

};

var DateCalendar = {

	build: function(element, name, value){
		var year = parseInt(value.substr(0, 4), 10);
		var month = parseInt(value.substr(5, 2), 10);
		var day = parseInt(value.substr(8, 2), 10);
		DateCalendar._buildMonth(element, year, month, value);
	},

	_buildMonth: function(element, year, month, activeDate){
		var numberDays = DateField.getNumberDays(year, month);
		var firstDate = new Date(year, month-1, 1);
		var lastDate = new Date(year, month-1, numberDays);
		var html = '<table class="calendarTable" cellspacing="0">';
		html+='<tr><td class="arrowPrev"><img src="'+$Kumbia.path+'img/prevw.gif"/></td>';
		html+='<td colspan="5" class="monthName">'+DateCalendar.getMonthName(month)+'</td>';
		html+='<td class="arrowNext"><img src="'+$Kumbia.path+'img/nextw.gif"/></td></tr>';
		html+='<tr><th>Dom</th><th>Lun</th><th>Mar</th><th>Mie</th><th>Jue</th><th>Vie</th><th>Sáb</th></tr>';
		html+='<tr>';
		if(month==1){
			var numberDaysPast = DateField.getNumberDays(year-1, 12);
		} else {
			var numberDaysPast = DateField.getNumberDays(year-1, month-1);
		};
		var dayOfWeek = firstDate.getDay();
		for(var i=(numberDaysPast-dayOfWeek+1);i<numberDaysPast;i++){
			html+='<td class="outMonthDay">'+(i+1)+'</td>';
		};
		var numberDay = 1;
		var date;
		while(numberDay<=numberDays){
			if(month<10){
				date = year+'-0'+month+'-'+numberDay;
			} else {
				date = year+'-'+month+'-'+numberDay;
			}
			if(activeDate==date){
				html+='<td class="selectedDay" title="'+date+'">'+numberDay+'</td>';
			} else {
				html+='<td title="'+date+'">'+numberDay+'</td>';
			};
			if(dayOfWeek==6){
				html+='</tr><tr>';
				dayOfWeek = 0;
			} else {
				dayOfWeek++;
			};
			numberDay++;
		};
		numberDay = 1;
		if(dayOfWeek<7){
			for(var i=dayOfWeek;i<7;i++){
				html+='<td class="outMonthDay">'+numberDay+'</td>';
				numberDay++;
			};
		};
		html+='</tr></table>';

		var position = element.up(1).cumulativeOffset();
		var calendarDiv = document.getElementById('calendarDiv');
		if(calendarDiv){
			calendarDiv.parentNode.removeChild(calendarDiv);
		};
		calendarDiv = document.createElement('DIV');
		calendarDiv.id = 'calendarDiv';
		calendarDiv.addClassName('calendar');
		calendarDiv.update(html);
		calendarDiv.style.top = (position[1]+22)+'px';
		calendarDiv.style.left = (position[0])+'px';
		document.body.appendChild(calendarDiv);
		window.setTimeout(function(){
			new Event.observe(window, 'click', DateCalendar.removeCalendar);
		}, 150);
	},

	removeCalendar: function(event){
		if(event.target.tagName!='INPUT'&&event.target.tagName!='SELECT'){
			var calendarDiv = document.getElementById('calendarDiv');
			if(calendarDiv){
				calendarDiv.parentNode.removeChild(calendarDiv);
			};
			new Event.stopObserving(window, 'click', DateCalendar.removeCalendar);
		}
	},

	getMonthName: function(month){
		switch(month){
			case 1:
				return 'Enero';
			case 2:
				return 'Febrero';
			case 3:
				return 'Marzo';
			case 4:
				return 'Abril';
			case 5:
				return 'Mayo';
			case 6:
				return 'Junio';
			case 7:
				return 'Julio';
			case 8:
				return 'Agosto';
			case 9:
				return 'Septiembre';
		}
	}

};

var DateField = {

	_monthTable: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],

	_listeners: {},

	observe: function(element, eventName, procedure){
		if(typeof DateField._listeners[eventName] == "undefined"){
			DateField._listeners[eventName] = {};
		};
		DateField._listeners[eventName][element.id] = {
			'element': element,
			'procedure': procedure
		};
	},

	fire: function(element, eventName, elementValue){
		if(typeof DateField._listeners[eventName] != "undefined"){
			if(typeof DateField._listeners[eventName][element.id] != "undefined"){
				var handler = DateField._listeners[eventName][element.id];
				if(element==handler['element']){
					handler['procedure'](elementValue);
				}
			}
		};
		return false;
	},

	getNumberDays: function(year, month){
		var numberDays = DateField._monthTable[month-1];
		if(month==2){
			if(parseInt(year, 10)%4==0){
				numberDays = 29;
			}
		};
		return numberDays;
	},

	getElement: function(name, context){
		if(typeof context == "undefined"){
			return Base.$(name);
		} else {
			return context.up(4).querySelector('#'+name);
		}
	},

	getValue: function(name, context){
		var element = DateField.getElement(name, context);
		if(element.tagName=='SELECT'){
			return element.options[element.selectedIndex].value;
		} else {
			return element.value;
		}
	},

	refresh: function(name, context){

		var html = '', n, numberDays;
		var year = DateField.getValue(name+'Year', context);
		var month = DateField.getValue(name+'Month', context);
		var day = DateField.getValue(name+'Day', context);
		var daySelect = DateField.getElement(name+'Day', context);

		var value = year+'-'+month+'-'+day;
		var element = DateField.getElement(name, context);
		element.value = value;

		while(daySelect.lastChild){
			daySelect.removeChild(daySelect.lastChild);
		};
		if(month.substr(0, 1)=='0'){
			month = month.substr(1, 1);
		};
		var numberDays = DateField.getNumberDays(year, month);
		for(var i=1;i<=numberDays;++i){
			n = (i < 10) ? '0'+i : i;
			if(n==day){
				html+='<option value="'+n+'" selected="selected">'+n+'</option>';
			} else {
				html+='<option value="'+n+'">'+n+'</option>';
			}
		};
		daySelect.innerHTML = html;
		DateField.fire(element, 'change', value);
	},

	showCalendar: function(element, name){
		DateCalendar.build(element, name, Base.getValue(name));
	}

};

var TimeField = {

	refresh: function(name, context){
		var hour = DateField.getValue(name+'Hour', context);
		var minutes = DateField.getValue(name+'Minutes', context);
		var value = hour+':'+minutes;
		DateField.getElement(name, context).value = value;
	}

};

var Utils = {

	getKumbiaURL: function(url){
		if(typeof url == "undefined"){
			url = "";
		};
		if($Kumbia.app!=""){
			return $Kumbia.path+$Kumbia.app+"/"+url;
		} else {
			return $Kumbia.path+url;
		}
	},

	getAppURL: function(url){
		if(typeof url == "undefined"){
			url = "";
		};
		if($Kumbia.app!=""){
			return $Kumbia.path+$Kumbia.app+"/"+url;
		} else {
			return $Kumbia.path+url;
		}
	},

	getURL: function(url){
		if(typeof url == "undefined"){
			return $Kumbia.path;
		} else {
			return $Kumbia.path+url;
		}
	},

	redirectParentToAction: function(url){
		new Utils.redirectToAction(url, window.parent);
	},

	redirectOpenerToAction: function(url){
		new Utils.redirectToAction(url, window.opener);
	},

	redirectToAction: function(url, win){
		win = win ? win : window;
		win.location = Utils.getKumbiaURL() + url;
	},

	upperCaseFirst: function(str){
		var first = str.substring(0, 1).toUpperCase();
		return first+str.substr(1, str.length-1)
	},

	round: function(number, decimals){
		var decimalPlace = Math.pow(100, decimals);
		return Math.round(number * decimalPlace) / decimalPlace;
	},

	numberFormat: function(number){
		var number = number.toString();
		var decimalPosition = number.indexOf('.');
		if(decimalPosition!=-1){
			var decimals = number.substr(decimalPosition+1);
			var integer = number.substring(0, decimalPosition);
		} else {
			var decimals = '00';
			var integer = number;
		};
		var n = 1;
		var formatedNumber = [];
		var integer = integer.toArray();
		for(var i=0;i<integer.length;i++){
			if((integer.length-i)%3==0){
				if(i!=0){
					formatedNumber.unshift('.');
				};
			};
			formatedNumber.unshift(integer[i])
		};
		return formatedNumber.reverse().join('')+','+decimals.substr(0, 2);
	}

};

function ajaxRemoteForm(form, up, callback){
	if(callback==undefined){
		callback = {};
	};
	new Ajax.Updater(up, form.action, {
		 method: "post",
		 asynchronous: true,
         evalScripts: true,
         onSuccess: function(transport){
			$(up).update(transport.responseText)
		},
		onLoaded: callback.before!=undefined ? callback.before: function(){},
		onComplete: callback.success!=undefined ? callback.success: function(){},
  		parameters: Form.serialize(form)
    });
  	return false;
};

var AJAX = {

	doRequest: function(url, options){
		var framework = Base.activeFramework;
		if(typeof options == 'undefined'){
			options = {};
		};
		switch(framework){
			case Base.PROTOTYPE:
				var callbackMap = {
					'before': 'onLoading',
					'success': 'onSuccess',
					'complete': 'onComplete',
					'error': 'onFailure'
				};
				$H(callbackMap).each(function(callback){
					if(typeof options[callback[0]] != 'undefined'){
						options[callback[1]] = function(procedure, transport){
							procedure.bind(this, transport.responseText)();
						}.bind(this, options[callback[0]]);
					}
				});
				return new Ajax.Request(url, options);
			case Base.JQUERY:
				var paramMap = {
					'method': 'type',
					'parameters': 'data',
					'asynchronous': 'async'
				};
				$.each(paramMap, function(index, value){
					if(typeof options[index] != 'undefined'){
						options[value] = options[index];
					}
				});
				options.url = url;
				return $.ajax(options);
			case Base.EXT:
				var paramMap = {
					'before': 'beforerequest',
					'error': 'failure',
					'parameters': 'params'
				};
				var index;
				for(index in paramMap){
					if(typeof options[index] != 'undefined'){
						options[paramMap[index]] = options[index];
					}
				};
				options.url = url;
				return Ext.Ajax.request(options);
			case Base.MOOTOOLS:
				var paramMap = {
					'parameters': 'data',
					'asynchronous': 'async',
					'before': 'onRequest',
					'success': 'onSuccess',
					'error': 'onFailure',
					'complete': 'onComplete'
				};
				var index;
				for(index in paramMap){
					if(typeof options[index] != 'undefined'){
						options[paramMap[index]] = options[index];
					}
				};
				options.url = url;
				var request = new Request(options);
				request.send();
				return request;
			break;
		};
	},

	update: function(url, element, options){
		if(typeof options == 'undefined'){
			options = {};
		};
		options.success = function(responseText){
			Base.$(element).innerHTML = responseText;
		};
		Base.bind(options.success, element, element);
		return AJAX.doRequest(url, options);
	}

};

AJAX.xmlRequest = function(params){
	var options = {};
	if(typeof params.url == "undefined" && typeof params.action != "undefined"){
		options.url = Utils.getKumbiaURL(params.action);
	};
	return AJAX.doRequest(options.url, options)
};

AJAX.viewRequest = function(params){
	var options = {};
	if(typeof params.url == "undefined" && typeof params.action != "undefined"){
		options.url = Utils.getKumbiaURL(params.action);
	};
	container = params.container;
	options.evalScripts = true;
	if(!document.getElementById(container)){
		throw "CoreError: DOM Container '"+container+"' no encontrado";
	};
	return AJAX.update(container, options.url, options);
};

AJAX.execute = function(params){
	var options = {};
	if(typeof params.url == "undefined" && typeof params.action != "undefined"){
		options.url = Utils.getKumbiaURL(params.action);
	};
	return AJAX.doRequest(options.url, options)
}

AJAX.query = function(queryAction, onSuccess){
	var me;
	new Ajax.Request(Utils.getKumbiaURL(queryAction), {
		method: 'GET',
		asynchronous: false,
		onSuccess: function(transport){
			var xml = transport.responseXML;
			var data = xml.getElementsByTagName("data");
			if(Prototype.Browser.IE){
				xmlValue = data[0].text;
			} else {
				xmlValue = data[0].textContent;
			};
			me = xmlValue;
		}
	});
	return me;
}

if(document.addEventListener){
	document.addEventListener('DOMContentLoaded', Base._checkFramework, false);
} else {
	document.attachEvent('readystatechange', Base._checkFramework);
};



/** Kumbia - PHP Rapid Development Framework ***************************
 *
 * Copyright (C) 2005 Andrs Felipe Gutirrez (andresfelipe at vagoogle.net)
 * NumberFormat: ProWebMasters.net based script
 *
 * This framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *****************************************************************************/

function validaEmail(evt){
    var kc;
	evt = (evt) ? evt : ((window.event) ? window.event : null);
	if(document.all) {
		kc = event.keyCode
	} else {
	 	kc = evt.keyCode
	}
	if(
		(kc>=65&&kc<=90)||
		(kc==50)||
		(kc==8)||
		(kc==9)||
		(kc==17)||
		(kc==16)||
		(kc==35)||
		(kc==36)||
		(kc==46)||
		(kc==109)||
		(kc==189)||
		(kc==190)||
		(kc==189)||
		(kc>=37&&kc<=40)||
		((kc>=48&&kc<=57)&&evt.shiftKey==false&&evt.altKey==false)
		) {
		//Returns
	} else {
	  	if(document.all) evt.returnValue = false
    	else evt.preventDefault()
    }
    //window.status = kc
}

/**
 * Valida que los campos requeridos del formulario contengan datos.
 * Recibe como parametros el objeto formulario y el nombre de los campos que se desean exigir.
 * Retorna true si la validacion es correcta, false en caso contrario.
 *
 * Ej. de uso:
 * form_remote_tag("cotroller/action", "update: div_id", "required: nombre_campo_1,nombre_campo_2")
 *
 * Como se ve en el ejemplo anterior, es necesario incluir el parametro 'required' y luego especificar los
 * nombres de los campos requeridos separados por comas (,). En el ejemplo anterior 'nombre_campo_1' y
 * 'nombre_campo_2' serian los nombres (name) de dos campos requeridos del formulario.
 * @param Object form Objeto formulario.
 * @param Array requiredFields Matriz con los nombres de los campos requeridos.
 * @return boolean false en caso de que se encuentren campos requeridos sin rellenar, true en caso contrario.
 */
function validaForm(form, requiredFields){

   var cont = 0;
   var campos = new Array();

   // Obtiene los campos requeridos que no contienen datos (si los hay)
   for(var i=0;i<requiredFields.length;i++){
   	   if($(requiredFields[i]).value == ''){
   	   	   campos[cont++] = $(requiredFields[i]);
   	   }
   }

   // Si faltan datos requeridos se muestra el efecto de resaltado sobre los campos.
   if(cont >= 1){
	   alert("Es necesario que ingrese los datos que se resaltarán");
	   for(var i=0; i<cont; i++){
	   	   new Effect.Highlight(campos[i].name, {startcolor:'#FF0000', endcolor:"#ffbbbb"});
	   };
	   campos[0].focus();
   }

   // Retorna false si hay campos requeridos sin rellenar; de lo contrario true.
   return cont >= 1 ? false : true;
}


function validaText(evt){
	var kc;
	evt = (evt) ? evt : ((window.event) ? window.event : null);
	kc = evt.keyCode
	window.status = kc
	if(
	(kc>=65&&kc<=90)||
	(kc==50)||
	(kc==8)||
	(kc==9)||
	(kc==17)||
	(kc==16)||
	(kc==32)||
	(kc==186)||
	(kc==190)||
	(kc==192)||
	(kc==222)||
	(kc>=37&&kc<=40) //||
	//((kc>=48&&kc<=57)&&evt.shiftKey==false&&evt.altKey==false)
	) {
		//Returns
	} else {
		if(document.all) evt.returnValue = false
		else evt.preventDefault()
	}
}

function valNumeric(evt){
	evt = (evt) ? evt : ((window.event) ? window.event : null);
	if(
	((evt.keyCode>=48&&evt.keyCode<=57)&&evt.shiftKey==false&&evt.altKey==false)||
	((evt.keyCode>=96&&evt.keyCode<=105)&&evt.shiftKey==false&&evt.altKey==false) ||
	( evt.keyCode==8   ||
	evt.keyCode==9   ||
	evt.keyCode==13  ||
	evt.keyCode==16  ||
	evt.keyCode==17  ||
	evt.keyCode==36  ||
	evt.keyCode==35  ||
	evt.keyCode==46  ||
	evt.keyCode==37  ||
	evt.keyCode==39  ||
	evt.keyCode==110 ||
	evt.keyCode==119 ||
	evt.keyCode==190)
	){
		//Lets that key value pass
	} else {
		if(document.all) {
			evt.returnValue = false
		} else evt.preventDefault()
	}
}

function valDate(){
	if(((event.keyCode!=8&&event.keyCode!=9&&event.keyCode!=36&&event.keyCode!=35&&event.keyCode!=46&&event.keyCode!=37&&event.keyCode!=39&&event.keyCode<48))||(event.keyCode>57&&(event.keyCode<96||(event.keyCode>105&&event.keyCode!=111&&event.keyCode!=189&&event.keyCode!=109)))||(event.shiftKey==true&&event.keyCode!=55)||event.altKey==true) {
		window.event.returnValue = false
	}
}

function keyUpper(obj){
	obj.value = obj.value.toUpperCase();
	saveValue(obj)
}

function keyUpper2(obj){
	obj.value = obj.value.toUpperCase();
}

function keyUpper3(obj){
	obj.value = obj.value.toUpperCase();
}

function checkDate(obj){
	if(!obj.value) return;
	var e = RegExp("([0-9]{4}[/-][0-9]{2}[/-][0-9]{2})", "i");
	if(!obj.value) return;
	if(e.exec(obj.value)==null) {
		window.status = "EL CAMPO TIENE UN FORMATO DE FECHA INCORRECTO";
		obj.className = "iError";
	}
	else {
		d = obj.value.substr(0, 2)
		m = obj.value.substr(3, 2)
		a = obj.value.substr(6, 4)
		if((d<1)||(d>31)){
			window.status = "EL CAMPO TIENE UN FORMATO DE FECHA INCORRECTO";
			obj.className = "iError";
		} else {
			if((m<1)||(m>12)){
				window.status = "EL CAMPO TIENE UN FORMATO DE FECHA INCORRECTO";
				obj.className = "iError";
			} else {
				window.status = "Listo";
				obj.className = "iNormal";
			}
		}
	}
}

function showConfirmPassword(obj){
	if(!$('div_'+obj.name).visible()){
		new Effect.Appear('div_'+obj.name)
	}
}

function nextValidatePassword(obj){
	if(!$('div_'+obj.name).visible()){
		$('div_'+obj.name).focus()
		$('div_'+obj.name).select()
	}
}

function validatePassword(confirma, password){
	if(confirma.value!=$(password).value){
		alert('Los Passwords No son Iguales')
		$(password).focus()
		$(password).select()
	} else {
		new Effect.Fade('div_'+$(password).name)
	}
}

function checkUnique(name, obj){
	var i, n;
	if(!obj.value){
		return;
	}
	if(obj.value=="@"){
		return;
	}
	n = 0;
	for(i=0;i<=Fields.length-1;i++){
		if(Fields[i]==name){
			break;
		}
	}
	for(j=0;j<=Values.length-1;j++){
		if(Values[j][i]==obj.value){
			if(n==1){
				if(obj.tagName=='SELECT'){
					alert('Esta Opción ya fué seleccionada por favor elija otra diferente');
				}
				obj.className = "iError";
				if(obj.tagName=='INPUT'){
					obj.select();
				}
				obj.focus();
				return;
			} else {
				n++;
			}
		}
	}
	obj.className = 'iNormal'
}

function nextField(evt, oname){
	var kc;
	evt = (evt) ? evt : ((window.event) ? window.event : null);
	kc = evt.keyCode
	if(kc==13){
		for(i=0;i<=Fields.length-1;i++) {
			if(oname==Fields[i]){
				if(i==(Fields.length-1)){
					if((document.getElementById("flid_"+Fields[0]).style.visibility!='hidden')&&
					(document.getElementById("flid_"+Fields[0]).readOnly==false)&&
					(document.getElementById("flid_"+Fields[0]).type!='hidden'))
					document.getElementById("fl_id"+Fields[0]).focus()
				} else {
					if( (document.getElementById("flid_"+Fields[i+1]).style.visibility!='hidden')&&
					(document.getElementById("flid_"+Fields[i+1]).readOnly==false)&&
					(document.getElementById("flid_"+Fields[i+1]).type!='hidden')){
						document.getElementById("flid_"+Fields[i+1]).focus()
					}
				}
				return
			}
		}
	}
}

function saveEmail(obj) {
	document.getElementById("flid_"+obj).value = document.getElementById(obj+"_email1").value + "@" + document.getElementById(obj+"_email2").value
}



/**
 * Kumbia Enterprise Framework
 * Window Object Manipulation Base Functions
 *
 * @copyright	Copyright (c) 2008-2010 Louder Technology COL. (http://www.loudertechnology.com)
 **/

var WindowUtilities = {

	getWindowScroll: function(parent) {
		var T, L, W, H;
		parent = parent || document.body;
		if (parent != document.body) {
			T = parent.scrollTop;
			L = parent.scrollLeft;
			W = parent.scrollWidth;
			H = parent.scrollHeight;
		}
		else {
			var w = window;
			T = w.document.body.scrollTop;
			L = w.document.body.scrollLeft;
			W = w.innerWidth;
			H = w.innerHeight;
		}
		return { top: T, left: L, width: W, height: H };
	},

	getPageSize: function(parent){
		parent = parent || document.body;
		var windowWidth, windowHeight;
		var pageHeight, pageWidth;
		if (parent != document.body) {
			windowWidth = parent.getWidth();
			windowHeight = parent.getHeight();
			pageWidth = parent.scrollWidth;
			pageHeight = parent.scrollHeight;
		}
		else {
			var xScroll, yScroll;

			if (window.innerHeight && window.scrollMaxY) {
				xScroll = document.body.scrollWidth;
				yScroll = window.innerHeight + window.scrollMaxY;
			} else if (document.body.scrollHeight > document.body.offsetHeight){
				xScroll = document.body.scrollWidth;
				yScroll = document.body.scrollHeight;
			} else {
				xScroll = document.body.offsetWidth;
				yScroll = document.body.offsetHeight;
			}
			if (self.innerHeight) {
				windowWidth = self.innerWidth;
				windowHeight = self.innerHeight;
			} else if (document.documentElement && document.documentElement.clientHeight) {
				windowWidth = document.documentElement.clientWidth;
				windowHeight = document.documentElement.clientHeight;
			} else if (document.body) {
				windowWidth = document.body.clientWidth;
				windowHeight = document.body.clientHeight;
			}
			if(yScroll < windowHeight){
				pageHeight = windowHeight;
			} else {
				pageHeight = yScroll;
			}
			if(xScroll < windowWidth){
				pageWidth = windowWidth;
			} else {
				pageWidth = xScroll;
			}
		}
		return {pageWidth: pageWidth ,pageHeight: pageHeight , windowWidth: windowWidth, windowHeight: windowHeight};
	}
};

$W = function(objectName) {
	return document.frames('openWindow').document.getElementById(objectName)
}

var WINDOW = {

	open: function(properties){
		if($('myWindow')){
			return;
		};
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
		var pageSize = WindowUtilities.getPageSize(document.body);
		var obj = document.createElement("DIV");
		if(!properties.title){
			properties.title = ""
		};
		if(!properties.url){
			properties.url = properties.action
		};
		left = parseInt((pageSize.windowWidth - (parseInt(properties.width)+36))/2);
		left += windowScroll.left;
		obj.style.left = left+"px"
		if(typeof properties.width != "undefined"){
			obj.style.width = properties.width;
		};
		if(typeof properties.height != "undefined"){
			obj.style.height = (parseInt(properties.height)+10)+"px";
		};
		obj.hide();
		var html = "<table cellspacing='0' cellpadding='0' width='100%'><tr>"+
		"<td align='center' id='myWindowTitle'>"+properties.title+"</td></tr>"+
		"<tr><td id='myWindowData'></td></tr></table>";
		obj.innerHTML = html;
		obj.id = "myWindow"
		document.body.appendChild(obj);
		//new Draggable(obj.id);
		if (typeof properties.onclose != "undefined") {
			WINDOW.onclose = properties.onclose;
		};
		if (typeof properties.onbeforeclose != "undefined") {
			WINDOW.onbeforeclose = properties.onbeforeclose;
		};
		obj.close = function(action){
			var myWindow = $("myWindow");
			var shadowWin = $('shadow_win');
			if(typeof properties.onbeforeclose != "undefined"){
				if(properties.onbeforeclose.call(this, action)==false){
					return;
				}
			};
			if(typeof myWindow.onclose != "undefined"){
				properties.onclose = myWindow.onclose;
			};
			document.body.removeChild(myWindow);
			if (shadowWin) {
				document.body.removeChild(shadowWin);
			};
			if (typeof properties.onclose != "undefined") {
				if (properties.onclose) {
					if (typeof properties.onclose.call != "undefined") {
						properties.onclose.call(this, action);
					}
				}
			}
		};
		new Ajax.Request(Utils.getKumbiaURL(properties.url), {
			method: 'GET',
			onSuccess: function(properties, t){
				$('myWindowData').update(t.responseText);
				if(typeof properties.afterRender != "undefined"){
					properties.afterRender();
				};
				var div = document.createElement("DIV")
				div.id = "shadow_win";
				$(div).setOpacity(0.1);
				document.body.appendChild(div);
				$('myWindow').show();
			}.bind(this, properties)
		});
	}
}



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

var Modal = {

	confirm: function(message, yesCallback){
		document.body.scrollTop = 0;
		new WINDOW.open({
			url: "context",
			title: "Confirmación",
			width: "500px",
			height: "200px",
			afterRender: function(message){
				$('contextMessage').update(message);
				//$('okModal').activate();
				$('okModal').observe('click', function(yesCallback){
					$('myWindow').close();
					yesCallback();
				}.bind(this, yesCallback));
				$('noModal').observe('click', function(){
					$('myWindow').close();
				});
			}.bind(this, message, yesCallback)
		});
	}

};

var Growler = {

	timeout: null,

	addTimeout: function(d){
		if(Growler.timeout!=null){
			window.clearTimeout(Growler.timeout);
			Growler.timeout = null;
		};
		Growler.timeout = window.setTimeout(function(d){
			document.body.removeChild(d);
			Growler.timeout = null;
		}.bind(this, d), 3500)
	},

	show: function(msg){
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
	    var pageSize = WindowUtilities.getPageSize(document.body);
	    var d = $('growler');
	    if(!d){
			var d = document.createElement("DIV");
			d.id = "growler";
			d.innerHTML = msg;
			d.hide();
			document.body.appendChild(d);
			d.setStyle({
				top: (pageSize.windowHeight-(d.getHeight()+20)+windowScroll.top)+"px",
				left: (pageSize.windowWidth-270+windowScroll.left)+"px"
			});
			d.show();
			Growler.addTimeout(d);
	    } else {
	    	d.innerHTML = msg;
	    	Growler.addTimeout(d);
	    	new Effect.Shake(d, {duration:0.5});
	    }
	}
};


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

function addToNumber(obj, event){
	if(event.keyCode!=Event.KEY_RETURN){
	  	$('number').value+=obj.value;
	  	if($('number').value!=""){
		    $('okButton').disabled = false;
		};
		$('number').select();
	}
}

function dropNumber(){
	new Effect.Shake("number", {
		duration: 0.5
	});
	new Effect.Morph("number", {
		duration: 0.5,
		style: {
			color: "#FFFFFF"
		},
		afterFinish: function(){
			$('number').value = "";
			$('number').style.color = "#000000";
		}
	})
	$('okButton').disabled = true;
}

function cancelNumber(){
	$('myWindow').close('cancel');
}

function acceptNumber(){
  	$('myWindow').close('ok');
}

function big(obj){
	$(obj).className = "bigButton2";
	$(obj).style.fontSize = "44px";
	new Effect.Morph(obj, {
		duration: 0.3,
		style: {
			fontSize: "30px"
		},
		afterFinish: function(){
			$(obj).className = "bigButton";
			$(obj).style.width = "70px";
			$(obj).style.height = "70px";
			$(obj).style.fontSize = "30px";
		}
	})
}

new Event.observe(window, "keyup", function(event){
	try 
	{
		if($("myWindow")){
			$('number').select();
			var code = 0;
			var ev = parseInt(event.keyCode);
			if(ev==Event.KEY_BACKSPACE||ev==Event.KEY_ESC){
				dropNumber();
				new Event.stop(event);
				return;
			};
			if(ev==Event.KEY_RETURN){
				$("okButton").click();
				new Event.stop(event);
				return;
			};
			if(ev==190||ev==110){
	  			if($('number').value.indexOf('.')==-1){
					var punto = new Object();
					if($('number').value.length==0){
						punto.value = "0.";
					} else {
						punto.value = ".";
					};
					addToNumber(punto, event);
					new Event.stop(event);
				}
			}
			if(ev>=48&&ev<=57){
				code = event.keyCode - 48;
				if($("b"+code)){
					big($("b"+code));
					addToNumber($("b"+code), event);
					new Event.stop(event);
					return;
				}
			};
			if(ev>=96&&ev<=105){
				code = event.keyCode - 96;
				if($("b"+code)){
					big($("b"+code));
					addToNumber($("b"+code), event);
					new Event.stop(event);
					return;
				}
			};
		}
	}
	catch(e){
		
	}
})



/*
 * A JavaScript implementation of the Secure Hash Algorithm, SHA-1, as defined
 * in FIPS PUB 180-1
 * Version 2.1a Copyright Paul Johnston 2000 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for details.
 */

/*
 * Configurable variables. You may need to tweak these to be compatible with
 * the server-side, but the defaults work in most cases.
 */
var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */

/*
 * These are the functions you'll usually want to call
 * They take string arguments and return either hex or base-64 encoded strings
 */
function hex_sha1(s){return binb2hex(core_sha1(str2binb(s),s.length * chrsz));}
function b64_sha1(s){return binb2b64(core_sha1(str2binb(s),s.length * chrsz));}
function str_sha1(s){return binb2str(core_sha1(str2binb(s),s.length * chrsz));}
function hex_hmac_sha1(key, data){ return binb2hex(core_hmac_sha1(key, data));}
function b64_hmac_sha1(key, data){ return binb2b64(core_hmac_sha1(key, data));}
function str_hmac_sha1(key, data){ return binb2str(core_hmac_sha1(key, data));}

/*
 * Perform a simple self-test to see if the VM is working
 */
function sha1_vm_test()
{
  return hex_sha1("abc") == "a9993e364706816aba3e25717850c26c9cd0d89d";
}

/*
 * Calculate the SHA-1 of an array of big-endian words, and a bit length
 */
function core_sha1(x, len)
{
  /* append padding */
  x[len >> 5] |= 0x80 << (24 - len % 32);
  x[((len + 64 >> 9) << 4) + 15] = len;

  var w = Array(80);
  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;
  var e = -1009589776;

  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;
    var olde = e;

    for(var j = 0; j < 80; j++)
    {
      if(j < 16) w[j] = x[i + j];
      else w[j] = rol(w[j-3] ^ w[j-8] ^ w[j-14] ^ w[j-16], 1);
      var t = safe_add(safe_add(rol(a, 5), sha1_ft(j, b, c, d)),
                       safe_add(safe_add(e, w[j]), sha1_kt(j)));
      e = d;
      d = c;
      c = rol(b, 30);
      b = a;
      a = t;
    }

    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
    e = safe_add(e, olde);
  }
  return Array(a, b, c, d, e);

}

/*
 * Perform the appropriate triplet combination function for the current
 * iteration
 */
function sha1_ft(t, b, c, d)
{
  if(t < 20) return (b & c) | ((~b) & d);
  if(t < 40) return b ^ c ^ d;
  if(t < 60) return (b & c) | (b & d) | (c & d);
  return b ^ c ^ d;
}

/*
 * Determine the appropriate additive constant for the current iteration
 */
function sha1_kt(t)
{
  return (t < 20) ?  1518500249 : (t < 40) ?  1859775393 :
         (t < 60) ? -1894007588 : -899497514;
}

/*
 * Calculate the HMAC-SHA1 of a key and some data
 */
function core_hmac_sha1(key, data)
{
  var bkey = str2binb(key);
  if(bkey.length > 16) bkey = core_sha1(bkey, key.length * chrsz);

  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++)
  {
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }

  var hash = core_sha1(ipad.concat(str2binb(data)), 512 + data.length * chrsz);
  return core_sha1(opad.concat(hash), 512 + 160);
}

/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function safe_add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}

/*
 * Bitwise rotate a 32-bit number to the left.
 */
function rol(num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}

/*
 * Convert an 8-bit or 16-bit string to an array of big-endian words
 * In 8-bit function, characters >255 have their hi-byte silently ignored.
 */
function str2binb(str)
{
  var bin = Array();
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < str.length * chrsz; i += chrsz)
    bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (32 - chrsz - i%32);
  return bin;
}

/*
 * Convert an array of big-endian words to a string
 */
function binb2str(bin)
{
  var str = "";
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < bin.length * 32; i += chrsz)
    str += String.fromCharCode((bin[i>>5] >>> (32 - chrsz - i%32)) & mask);
  return str;
}

/*
 * Convert an array of big-endian words to a hex string.
 */
function binb2hex(binarray)
{
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i++)
  {
    str += hex_tab.charAt((binarray[i>>2] >> ((3 - i%4)*8+4)) & 0xF) +
           hex_tab.charAt((binarray[i>>2] >> ((3 - i%4)*8  )) & 0xF);
  }
  return str;
}

/*
 * Convert an array of big-endian words to a base-64 string
 */
function binb2b64(binarray)
{
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i += 3)
  {
    var triplet = (((binarray[i   >> 2] >> 8 * (3 -  i   %4)) & 0xFF) << 16)
                | (((binarray[i+1 >> 2] >> 8 * (3 - (i+1)%4)) & 0xFF) << 8 )
                |  ((binarray[i+2 >> 2] >> 8 * (3 - (i+2)%4)) & 0xFF);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > binarray.length * 32) str += b64pad;
      else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
    }
  }
  return str;
}



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

var Tabs = {

	setActiveTab: function(element, number){
		if(element.hasClassName("active_tab")){
			return;
		} else {
			element.removeClassName("inactive_tab");
		}
		$$(".active_tab").each(function(tab_element){
			tab_element.removeClassName("active_tab");
		});
		$$(".tab_basic").each(function(tab_element){
			if(tab_element==element){
				tab_element.addClassName("active_tab");
			} else {
				tab_element.addClassName("inactive_tab");
			}
		});
		$$(".tab_content").each(function(tab_content){
			if(tab_content.id!="tab"+number){
				tab_content.hide();
			}
		});
		$("tab"+number).show();
	}

};


window.cancelNumber = function(){
	new Utils.redirectToAction('appmenu')
}

try {
	window.acceptNumber = function(){
		if($F("number").strip()!=''){
			new Ajax.Request("anula_factura/exists/"+$F("number")+"/"+$F("salon_id"), {
				onSuccess: function(transport){
					var response = transport.responseText.evalJSON();
					if(response=='yes'){
						if(confirm("¿Seguro desea anular la orden/factura No. "+$F("number")+"?")){
							new Utils.redirectToAction('anula_factura/anula/'+hex_sha1($("number").value)+"/"+$F("salon_id")+"/"+$F("tipo_venta"))
						}
					} else {
						alert("No existe la factura/orden "+$("number").value+" en el ambiente indicado");
					}
				}
			});
		}
	}
}
catch(e){
	alert(e.stack)
}


new Event.observe(window, "load", function(){

});

var nota = {

	data : [],

	formas_pago : [],

	total_pago : 0,

	total_nota : 0,

	total_propina : 0,

	consultarFactura:function(element){

		if($('consecutivo_facturacion').getValue().trim() == ''){
			nota.alert("Debe digitar el número de la factura.", 'warning');
			return false;
		}

		document.querySelector('.loading').style.display = '';
		$s('#consultar').style.display = "none";

		setTimeout(function(){

			new Ajax.Request("reprocesar_factura_electronica/findFactura/"+$('prefijo_facturacion').getValue().trim()+"/"+$('consecutivo_facturacion').getValue().trim(), {
				onSuccess: function(transport){

					var response = transport.responseText.evalJSON();
					document.querySelector('.loading').style.display = 'none';


					if(response.success){
	
						// No se encontro la factura
						if(response.data.factura == false){
							nota.alert('No existe la factura', 'danger');
							return false;
						}
	
						// La busqueda corresponde a orden de servicio
						if(response.data.factura.tipo == 'O'){
							nota.alert('Los datos corresponde a una orden de servicio', 'danger');
							return false;
						}
	
						nota.data = response.data;
						nota.loadDatos();

						$s('#consultar').style.display = "none";
						$s('#nueva').style.display = "";
	
						document.querySelector('#prefijo_facturacion').readOnly = true;
						document.querySelector('#consecutivo_facturacion').readOnly = true;
	
					} else {
						$s('#consultar').style.display = "";
						nota.alert(response.message, 'danger');
					}
				}
			});

		}, 1000);

	},

	alert : function(message, $type = 'success'){

		var notificator = new Notification(document.querySelector('.notification'));

		switch ($type) {
			case 'success':
				notificator.success(message);
				break;

			case 'danger':
				notificator.error(message);				
				break;

			case 'warning':
				notificator.warning(message);
				break;				
		
			default:
				notificator.info(message);
				break;
		}

	},

	loadDatos : function(){
		
		var tr = '';
		var factura = this.data.factura;
		tr  +=  '<tr>'
				+	'	<td class="al-l">'+ factura.nombre +'</td>'
				+	'	<td class="al-c">'+ factura.cedula +'</td>'
				+	'	<td class="al-c">'+ factura.prefijo_facturacion +'</td>'	
				+	'	<td class="al-c">'+ factura.consecutivo_facturacion +'</td>'	
				+	'	<td class="al-c">'+ factura.fecha +'</td>'	
				+	'	<td class="al-r">'+ formatNumber.new(factura.total) +'</td>'
				+	'	<td class="al-c"><button class="btnxml" onclick="nota.xmlGenerate(this, \''+factura.id+'\')">'+ this.data.iconxml +'</button>'+nota.data.xmlloading+'</td>'
				+	'</tr>'

		document.querySelector('#tbldetalle tbody').innerHTML=tr;
		

	},

	xmlGenerate : function(element, factura_id){

		if(confirm("Desea generar el XML Dian ?")){

			var td = element.closest('td');

			td.querySelector('.xmlloading').style.display = '';
			td.querySelector('.btnxml').style.display = 'none';
			
			setTimeout(function(){ 

				new Ajax.Request('reprocesar_factura_electronica/save', {
					method: 'POST',
					parameters: {
						'factura_id': factura_id
					},
					onSuccess: function(transport){
	
						var response = transport.responseText.evalJSON();
						td.querySelector('.xmlloading').style.display = 'none';
	
						if(response.success){
							nota.alert('El xml de la factura se genero exitosamente', 'success');
							window.open(response.data.path, null, "width=300, height=700, toolbar=no, statusbar=no")
						} else {
							nota.alert(response.message, 'danger');
						}

						td.querySelector('.btnxml').style.display = '';
											
					}
				})

				
	
			}, 1000);

		}

	},

	nuevaConsulta : function(){

		$s('#consultar').style.display = "";
		$s('#nueva').style.display = "none";

		document.querySelector('#prefijo_facturacion').readOnly = false;
		document.querySelector('#consecutivo_facturacion').readOnly = false;
		document.querySelector('#tbldetalle tbody').innerHTML = '';
		document.querySelector('#prefijo_facturacion').value = '';
		document.querySelector('#consecutivo_facturacion').value = '';
		document.querySelector('#prefijo_facturacion').focus();
		document.querySelector('.okButton').style.display = '';
		
		nota.formas_pago = [];
		nota.data = [];

	}


}


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
