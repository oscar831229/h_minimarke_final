
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

var HfosHttpWorker = {

	toQueryString: function(data){
		var queryString = [];
		for(var key in data){
			queryString.push(key+'='+encodeURIComponentdata[key]);
		};
		return queryString.join('&');
  	},

	request: function(url, options, postMessageWorker){
		var xmlHttpRequest = new XMLHttpRequest();
		if(typeof options == "undefined"){
			options = {};
		};
		if(typeof options.parameters != "undefined"){
			if(typeof options.parameters != "string"){
				options.parameters = HfosHttpWorker.toQueryString(options.parameters);
			}
		};
		if(typeof options.method == "undefined"){
			options.method = "POST";
		} else {
			options.method = options.method.toUpperCase();
			if(options.method=="GET"){
				if(typeof options.parameters != "undefined"){
					url+="?"+options.parameters;
				}
			};
		};
		xmlHttpRequest.open(options.method, url, true);
		xmlHttpRequest.onreadystatechange = HfosHttpWorker._onReadyStateChange.curry(xmlHttpRequest, postMessageWorker);
		xmlHttpRequest.setRequestHeader("Accept", "text/html, application/xml, text/xml, */*");
		xmlHttpRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		if(options.contentTypeJson){
			xmlHttpRequest.setRequestHeader("X-Json-Accept", "text/json");
		};
		if(options.method=="POST"){
			xmlHttpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
		};
		if(typeof options.parameters == "undefined"){
			xmlHttpRequest.send();
		} else {
			if(options.method=="GET"){
				xmlHttpRequest.send();
			} else {
				xmlHttpRequest.send(options.parameters);
			}
		}
	},

	_onReadyStateChange: function(xmlHttpRequest, postMessageWorker){
		if(xmlHttpRequest.readyState > 3){
			postMessageWorker({
				action: 'readyState',
				readyState: xmlHttpRequest.readyState,
				status: xmlHttpRequest.status,
				responseText: xmlHttpRequest.responseText
			});
		} else {
			postMessageWorker({
				action: 'readyState',
				readyState: xmlHttpRequest.readyState
			});
		}
	}

}

