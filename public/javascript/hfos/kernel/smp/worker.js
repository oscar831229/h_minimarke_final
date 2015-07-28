
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

var HfosWorker = {

	initialize: function(path){
		importScripts(path+'javascript/hfos/kernel/network/ajax-worker.js');
	},

	curry: function(){
		var _func = this;
        var _args = [];
        for(var i=0;i<arguments.length;i++){
        	_args[_args.length] = arguments[i];
        };
        return function(){
			return _func.apply(_func, _args);
        };
	},

	decode: function(data){
		return JSON.parse(data);
	},

	encode: function(data){
		return JSON.stringify(data);
	},

	postMessage: function(message){
		postMessage(HfosWorker.encode(message));
	},

	onMessage: function(event){
		var data = HfosWorker.decode(event.data);
		switch(data.action){
			case 'initialize':
				HfosWorker.initialize(data.path);
				break;
			case 'request':
				HfosHttpWorker.request(data.url, data.options, HfosWorker.postMessage);
				break;
		};
	}

};

Function.prototype.curry = HfosWorker.curry;
onmessage = HfosWorker.onMessage;