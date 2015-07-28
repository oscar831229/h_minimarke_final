
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

var WindowUtilities = {

	// From dragdrop.js
	getWindowScroll: function(parent) {
		var T, L, W, H;
		parent = parent || document.body;
		if (parent != document.body) {
			T = parent.scrollTop;
			L = parent.scrollLeft;
			W = parent.scrollWidth;
			H = parent.scrollHeight;
		} else {
			var w = window;
			if(w.document.documentElement && w.document.documentElement.scrollTop){
				T = w.document.documentElement.scrollTop;
				L = w.document.documentElement.scrollLeft;
			} else {
				if(w.document.body){
					T = w.document.body.scrollTop;
					L = w.document.body.scrollLeft;
				};
			};
			if(w.innerWidth){
				W = w.innerWidth;
				H = w.innerHeight;
			} else {
				if (w.document.documentElement && w.document.documentElement.clientWidth) {
					W = w.document.documentElement.clientWidth;
					H = w.document.documentElement.clientHeight;
				} else {
					W = w.document.body.offsetWidth;
					H = w.document.body.offsetHeight
				};
			};
		};
		return { top: T, left: L, width: W, height: H };
	},
	//
	// getPageSize()
	// Returns array with page width, height and window width, height
	// Core code from - quirksmode.org
	// Edit for Firefox by pHaez
	//
	getPageSize: function(parent){
		parent = parent || document.body;
		var windowWidth, windowHeight;
		var pageHeight, pageWidth;
		if (parent != document.body) {
			windowWidth = parent.getWidth();
			windowHeight = parent.getHeight();
			pageWidth = parent.scrollWidth;
			pageHeight = parent.scrollHeight;
		} else {
			var xScroll, yScroll;
			if (window.innerHeight && window.scrollMaxY) {
				xScroll = document.body.scrollWidth;
				yScroll = window.innerHeight + window.scrollMaxY;
			} else {
				if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
					xScroll = document.body.scrollWidth;
					yScroll = document.body.scrollHeight;
				} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
					xScroll = document.body.offsetWidth;
					yScroll = document.body.offsetHeight;
				};
			};
			if (self.innerHeight) {  // all except Explorer
				windowWidth = self.innerWidth;
				windowHeight = self.innerHeight;
			} else {
				// Explorer 6 Strict Mode
				if (document.documentElement && document.documentElement.clientHeight) {
					windowWidth = document.documentElement.clientWidth;
					windowHeight = document.documentElement.clientHeight;
				} else {
					if(document.body){ // other Explorers
						windowWidth = document.body.clientWidth;
						windowHeight = document.body.clientHeight;
					};
				};
			};

			// for small pages with total height less then height of the viewport
			if(yScroll < windowHeight){
				pageHeight = windowHeight;
			} else {
				pageHeight = yScroll;
			};

			// for small pages with total width less then width of the viewport
			if(xScroll < windowWidth){
				pageWidth = windowWidth;
			} else {
				pageWidth = xScroll;
			};
		};
		return {pageWidth: pageWidth ,pageHeight: pageHeight , windowWidth: windowWidth, windowHeight: windowHeight};
	}

};
