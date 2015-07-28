
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

var HfosBindings = {

	_objectBindings: {},

	late: function(objectId, eventName, procedure){
		if(typeof HfosBindings._objectBindings[objectId] == "undefined"){
			HfosBindings._objectBindings[objectId] = {};
		};
		if(typeof HfosBindings._objectBindings[objectId][eventName] == "undefined"){
			HfosBindings._objectBindings[objectId][eventName] = [];
		};
		HfosBindings._objectBindings[objectId][eventName].push(procedure);
	},

	get: function(objectId){
		if(typeof HfosBindings._objectBindings[objectId] != "undefined"){
			return HfosBindings._objectBindings[objectId];
		} else {
			return null;
		}
	},

	fire: function(objectId, eventName){
		/*var eventHandlers = HfosBindings.get(objectId, eventName);
		if(eventHandlers!==null){

		}*/
	}

};