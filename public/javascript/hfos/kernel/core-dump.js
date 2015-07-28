
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
 * HfosCoreDump
 *
 * Realiza un volcado de la memoria del navegador
 */
var HfosCoreDump = {

	_dumpSymbol: function(symbol, limit, count){
		if(limit<32){
			if(!Object.isElement(symbol)){
				if(typeof symbol == "object"){
					if(Object.isArray(symbol)){
						return 'Array';
					} else {
						if(typeof symbol._debuggable != "undefined"){
							var length = 0;
							var inspect = {};
							 $H(symbol).each(function(subSymbol){
								if(typeof subSymbol[1] != "function"){
									if(!Object.isElement(subSymbol[1])){
										if(typeof subSymbol[1] == "object"){
											if(Object.isArray(subSymbol[1])){
												inspect[subSymbol[0]] = 'Array';
											} else {
												if(typeof subSymbol[1]._debuggable != "undefined"){
													inspect[subSymbol[0]] = Object.inspect($H(subSymbol[1]))
												} else {
													limit++;
													inspect[subSymbol[0]] = HfosCoreDump._dumpSymbol(subSymbol[1], limit);
													limit--;
												}
											}
										} else {
											inspect[subSymbol[0]] = Object.inspect(subSymbol[1])
										}
									} else {
										inspect[subSymbol[0]] = '#<Element:'+subSymbol[1].tagName+' '+subSymbol[1].id+'>';
									};
									length++;
									if(length>31){
										inspect['_incomplete'] = true;
										return inspect;
									}
								};
							});
							return inspect;
						} else {
							return Object.inspect(symbol)
						}
					}
				} else {
					return Object.inspect(symbol);
				}
			} else {
				return '#<Element:'+symbol.tagName+' '+symbol.id+'>';
			}
		} else {
			return 'RangeError '+limit;
		}
	},

	dump: function(){
		var dump = {};
		var applications = Hfos.getApplications();
		dump['apps'] = HfosCoreDump._dumpSymbol(applications, 0, 0);
		return JSON.stringify(dump);
	}

}