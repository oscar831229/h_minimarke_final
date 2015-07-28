
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

var FastSearch = {

	results: [],
	pointer: 0,
	lastSearch: "",
	originalOpacity: 1.0,

	goDown: function(){
		var fs = FastSearch;
		var pointer = fs.pointer+1;
		if(pointer==fs.results.length){
			pointer = 0;
		};
		fs.selectResult(pointer);
		fs.pointer = pointer;
	},

	goUp: function(){
		var pointer = FastSearch.pointer-1;
		if(pointer<0){
			pointer = FastSearch.results.length-1;
		};
		FastSearch.selectResult(pointer);
		FastSearch.pointer = pointer;
	},

	selectResult: function(pointer){
		var p = 0;
		$$('.resultDiv').each(function(element){
			if(p==pointer){
				element.addClassName('resultDivSe');
				$('imgResult').src = 'img/'+FastSearch.results[p].image;
			} else {
				element.removeClassName('resultDivSe');
			};
			p++;
		});
	},

	executeAction: function(){
		var p = FastSearch.pointer;
		if(typeof FastSearch.results[p].action != "undefined"){
			window.location = "?action="+FastSearch.results[p].action;
		}
	},

	addShadow: function(){
		if($Jasmin.high==1){
			FastSearch.originalOpacity = $('mh_div').getOpacity();
			document.body.style.backgroundImage = "url(img/bg-leo541b.jpg)";
			var xTraslation = $('mh_div').getWidth();
			new Effect.Move('left_dock', {
				x: -xTraslation
			});
			new Effect.Move('mh_div', {
				x: -xTraslation
			});
			new Effect.Opacity('mh_div', {
				to: 0.15,
				duration: 0.5
			});
		} else {
			$('left_dock').hide();
			$('mh_div').hide();
		}
	},

	dropShadow: function(){
		if($Jasmin.high==1){
			var xTraslation = $('mh_div').getWidth();
			new Effect.Move('left_dock', {
				x: xTraslation
			});
			new Effect.Move('mh_div', {
				x: xTraslation
			});
			new Effect.Opacity('mh_div', {
				to: FastSearch.originalOpacity,
				duration: 0.3
			});
			document.body.style.backgroundImage = "url(img/bg-leo541.jpg)";
		} else {
			$('left_dock').show();
			$('mh_div').show();
		}
	},

	scrollEvent: function(){
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
    	$('fSearchDiv').style.top = (55+windowScroll.top)+'px';
    	$('fSearchResults').style.top = (120+windowScroll.top)+'px';
	},

	initialize: function(){
		FastSearch.addShadow();
		var d = document.createElement('DIV');
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
		d.id = "fSearchContainer";
		d.innerHTML = '<div id="fSearchDiv" style="display:none">'+
		'<img src="img/search.png" id="imgResult">'+
		'<div id="searchBoxDiv"><table><tr>'+
		'<td width="150"></td><td><input type="text" id="searchBoxInput"></td>'+
		'<td><img src="img/al.gif" style="display:none" id="alLoad"/></td></tr></table>'+
		'</div></div><div id="fSearchResults" style="display:none"></div>';
		document.body.appendChild(d);
		$('fSearchDiv').setStyle({
			"top": (55+windowScroll.top)+'px'
		});
		$('fSearchResults').setStyle({
			"top": (120+windowScroll.top)+'px'
		});
		new Event.observe(window, 'scroll', FastSearch.scrollEvent);
		$('searchBoxInput').observe('keyup', function(event){
			if(event.keyCode==Event.KEY_DOWN){
				FastSearch.goDown();
				new Event.stop(event);
				return;
			} else {
				if(event.keyCode==Event.KEY_UP){
					FastSearch.goUp();
					new Event.stop(event);
					return;
				} else {
					if(event.keyCode==Event.KEY_RETURN){
						FastSearch.executeAction();
						new Event.stop(event);
						return;
					} else {
						if(event.keyCode==Event.KEY_ESC){
							FastSearch.dropShadow();
							new Event.stopObserving(window, 'scroll', FastSearch.scrollEvent);
							document.body.removeChild($('fSearchContainer'));
						}
					}
				}
			};
			if(FastSearch.lastSearch!=this.value){
				FastSearch.lastSearch = this.value;
				if(this.value.length>1){
					new Ajax.Request('dispatch.php?action=search&q='+this.value, {
						onLoading: function(){
							$('alLoad').show();
						},
						onSuccess: function(t){
							var html;
							var results = t.responseText.evalJSON();
							var fSearchResults = $('fSearchResults');
							fSearchResults.innerHTML = "";
							FastSearch.pointer = 0;
							FastSearch.results = results;
							if(results.length>0){
								fSearchResults.show();
								for(var i=0;i<results.length;i++){
									if(i==0){
										html = "<div class='resultDiv resultDivSe'>"+
										"<table><tr><td><img class='resultImage' src='img/"+results[i].image+"'>"+
										"</td><td>"+results[i].text+"<br><span class='underResult'>"+results[i].under+"</span>"+
										"</td></tr></table></div>";
										$('imgResult').src = 'img/'+results[i].image;
									} else {
										html = "<div class='resultDiv'><table><tr><td><img class='resultImage' src='img/"+results[i].image+"'>"+
										"</td><td>"+results[i].text+"<br><span class='underResult'>"+results[i].under+"</span>"+
										"</td></tr></table></div>";
									};
									fSearchResults.innerHTML+=html;
								};
								var height = results.length*55;
								if($Jasmin.high==1){
									new Effect.Morph(fSearchResults, {
										duration: 0.5,
										style: {
											"height": height+"px"
										},
										afterFinish: function(){
											$('alLoad').hide();
										}
									});
								} else {
									fSearchResults.style.height = height+"px";
								}
							} else {
								fSearchResults.hide();
								$('imgResult').src = 'img/search.png';
							};
						},
						onFailure: function(t){
							var fSearchResults = $('fSearchResults');
							fSearchResults.innerHTML = t.responseText;
							fSearchResults.style.height = "300px";
						},
						onComplete: function(){
							$('alLoad').hide();
						}
					});
				} else {
					var fSearchResults = $('fSearchResults');
					fSearchResults.innerHTML = "";
					fSearchResults.hide();
					$('imgResult').src = 'img/search.png';
					$('alLoad').hide();
				};
			}
		});
		if($Jasmin.high==1){
			new Effect.Appear('fSearchDiv', {
				duration: 0.5,
				afterFinish: function(){
					$('searchBoxInput').activate();
				}
			});
		} else {
			$('fSearchDiv').show();
			$('searchBoxInput').activate();
		};
		window.setTimeout(function(){
			Favorites.removeDialog();
		}, 700);
	}

};

function ss(){
	FastSearch.initialize();
}