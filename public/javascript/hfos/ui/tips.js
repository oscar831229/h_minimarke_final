
// Prototip 2.2.3 - 11-01-2011
// Copyright (c) 2008-2011 Nick Stakenburg (http://www.nickstakenburg.com)
//
// Licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 Unported License
// http://creativecommons.org/licenses/by-nc-nd/3.0/
// More information on this project:
// http://www.nickstakenburg.com/projects/prototip2/

var Prototip = {
	Version: '2.2.3'
};

var Tips = {
	options: {
		paths: { // paths can be relative to this file or an absolute url
			images: $Kumbia.path+'img/prototip/',
			javascript: ''
		},
		zIndex: 6000 // raise if required
	}
};

Prototip.Styles = {
'default': {
		border: 6,
		borderColor: '#606060',
		className: 'default',
		closeButton: false,
		hideAfter: false,
		hideOn: 'mouseleave',
		hook: false,
		//images: 'styles/creamy/',    // Example: different images. An absolute url or relative to the images url defined above.
		radius: 6,
		showOn: 'mousemove',
		stem: {
			//position: 'topLeft',       // Example: optional default stem position, this will also enable the stem
			height: 12,
			width: 15
		}
	}
};

Object.extend(Prototip, {
	REQUIRED_Prototype: "1.7",
	support: {
		canvas: !!document.createElement("canvas").getContext
	},
	insertScript: function (d) {
		try {
			document.write("<script type='text/javascript' src='" + d + "'><\/script>")
		} catch(c) {
			$$("head")[0].insert(new Element("script", {
				src: d,
				type: "text/javascript"
			}))
		}
	},
	start: function () {
		this.require("Prototype");
		var b = /prototip([\w\d-_.]+)?\.js(.*)/;
		this.path = (($$("script[src]").find(function (a) {
			return a.src.match(b)
		}) || {}).src || "").replace(b, ""),
		Tips.paths = function (c) {
			return {
				images: /^(https?:\/\/|\/)/.test(c.images) ? c.images: this.path + c.images,
				javascript: /^(https?:\/\/|\/)/.test(c.javascript) ? c.javascript: this.path + c.javascript
			}
		}.bind(this)(Tips.options.paths),
		Prototip.Styles || this.insertScript(Tips.paths.javascript + "styles.js"),
		this.support.canvas || (document.documentMode < 8 || document.namespaces.ns_vml ? document.observe("dom:loaded", function () {
			var c = document.createStyleSheet();
			c.cssText = "ns_vml\\:*{behavior:url(#default#VML)}"
		}) : document.namespaces.add("ns_vml", "urn:schemas-microsoft-com:vml", "#default#VML")),
		Tips.initialize(),
		Element.observe(window, "unload", this.unload)
	},
	require: function (b) {
		if (typeof window[b] == "undefined" || this.convertVersionString(window[b].Version) < this.convertVersionString(this["REQUIRED_" + b])) {
			throw "Prototip requires " + b + " >= " + this["REQUIRED_" + b]
		}
	},
	convertVersionString: function (d) {
		var c = d.replace(/_.*|\./g, "");
		c = parseInt(c + "0".times(4 - c.length));
		return d.indexOf("_") > -1 ? c - 1 : c
	},
	toggleInt: function (b) {
		return b > 0 ? -1 * b: b.abs()
	},
	unload: function () {
		Tips.removeAll()
	}
}),
Object.extend(Tips, function () {
	function b(c) {
		c && (c.deactivate(), c.tooltip && (c.wrapper.remove(), Tips.fixIE && c.iframeShim.remove()), Tips.tips = Tips.tips.without(c))
	}
	return {
		tips: [],
		visible: [],
		initialize: function () {
			this.zIndexTop = this.zIndex
		},
		_inverse: {
			left: "right",
			right: "left",
			top: "bottom",
			bottom: "top",
			middle: "middle",
			horizontal: "vertical",
			vertical: "horizontal"
		},
		_stemTranslation: {
			width: "horizontal",
			height: "vertical"
		},
		inverseStem: function (c) {
			return ! arguments[1] ? c: this._inverse[c]
		},
		fixIE: function (d) {
			var c = (new RegExp("MSIE ([\\d.]+)")).exec(d);
			return c ? parseFloat(c[1]) < 7 : !1
		} (navigator.userAgent),
		WebKit419: Prototype.Browser.WebKit && !document.evaluate,
		add: function (c) {
			this.tips.push(c)
		},
		remove: function (a) {
			var l, k = [];
			for (var j = 0, i = this.tips.length; j < i; j++) {
				var h = this.tips[j];
				l || h.element != $(a) ? h.element.parentNode || k.push(h) : l = h
			}
			b(l);
			for (var j = 0, i = k.length; j < i; j++) {
				var h = k[j];
				b(h)
			}
			a.prototip = null
		},
		removeAll: function () {
			for (var a = 0, d = this.tips.length; a < d; a++) {
				b(this.tips[a])
			}
		},
		raise: function (e) {
			if (e != this._highest) {
				if (this.visible.length === 0) {
					this.zIndexTop = this.options.zIndex;
					for (var d = 0, f = this.tips.length; d < f; d++) {
						this.tips[d].wrapper.setStyle({
							zIndex: this.options.zIndex
						})
					}
				}
				e.wrapper.setStyle({
					zIndex: this.zIndexTop++
				}),
				e.loader && e.loader.setStyle({
					zIndex: this.zIndexTop
				}),
				this._highest = e
			}
		},
		addVisibile: function (c) {
			this.removeVisible(c),
			this.visible.push(c)
		},
		removeVisible: function (c) {
			this.visible = this.visible.without(c)
		},
		hideAll: function () {
			Tips.visible.invoke("hide")
		},
		hook: function (v, u) {
			v = $(v),
			u = $(u);
			var t = Object.extend({
				offset: {
					x: 0,
					y: 0
				},
				position: !1
			},
			arguments[2] || {}),
			s = t.mouse || u.cumulativeOffset();
			s.left += t.offset.x,
			s.top += t.offset.y;
			var r = t.mouse ? [0, 0] : u.cumulativeScrollOffset(),
			q = document.viewport.getScrollOffsets(),
			p = t.mouse ? "mouseHook": "target";
			s.left += -1 * (r[0] - q[0]),
			s.top += -1 * (r[1] - q[1]);
			if (t.mouse) {
				var o = [0, 0];
				o.width = 0,
				o.height = 0
			}
			var n = {
				element: v.getDimensions()
			},
			m = {
				element: Object.clone(s)
			};
			n[p] = t.mouse ? o: u.getDimensions(),
			m[p] = Object.clone(s);
			for (var l in m) {
				switch (t[l]) {
				case "topRight":
				case "rightTop":
					m[l].left += n[l].width;
					break;
				case "topMiddle":
					m[l].left += n[l].width / 2;
					break;
				case "rightMiddle":
					m[l].left += n[l].width,
					m[l].top += n[l].height / 2;
					break;
				case "bottomLeft":
				case "leftBottom":
					m[l].top += n[l].height;
					break;
				case "bottomRight":
				case "rightBottom":
					m[l].left += n[l].width,
					m[l].top += n[l].height;
					break;
				case "bottomMiddle":
					m[l].left += n[l].width / 2,
					m[l].top += n[l].height;
					break;
				case "leftMiddle":
					m[l].top += n[l].height / 2
				}
			}
			s.left += -1 * (m.element.left - m[p].left),
			s.top += -1 * (m.element.top - m[p].top),
			t.position && v.setStyle({
				left: s.left + "px",
				top: s.top + "px"
			});
			return s
		}
	}
} ()),

Tips.initialize();

var Tip = Class.create({
	initialize: function (g, f) {
		this.element = $(g);
		if(!this.element){
			throw "Prototip: Element not available, cannot create a tooltip."
		}
		Tips.remove(this.element);
		var j = Object.isString(f) || Object.isElement(f),
		i = j ? arguments[2] || [] : f;
		this.content = j ? f: null,
		i.style && (i = Object.extend(Object.clone(Prototip.Styles[i.style]), i)),
		this.options = Object.extend(Object.extend({
			ajax: !1,
			border: 0,
			borderColor: "#000000",
			radius: 0,
			className: Tips.options.className,
			closeButton: Tips.options.closeButtons,
			delay: !i.showOn || i.showOn != "click" ? 0.14 : !1,
			hideAfter: !1,
			hideOn: "mouseleave",
			hideOthers: !1,
			hook: i.hook,
			offset: i.hook ? {
				x: 0,
				y: 0
			}: {
				x: 16,
				y: 16
			},
			fixed: i.hook && !i.hook.mouse ? !0 : !1,
			showOn: "mousemove",
			stem: !1,
			style: "default",
			target: this.element,
			title: !1,
			viewport: i.hook && !i.hook.mouse ? !1 : !0,
			width: !1
		},
		Prototip.Styles["default"]), i),
		this.target = $(this.options.target),
		this.radius = this.options.radius,
		this.border = this.radius > this.options.border ? this.radius: this.options.border,
		this.options.images ? this.images = this.options.images.include("://") ? this.options.images: Tips.paths.images + this.options.images: this.images = Tips.paths.images + "styles/" + (this.options.style || "") + "/",
		this.images.endsWith("/") || (this.images += "/"),
		Object.isString(this.options.stem) && (this.options.stem = {
			position: this.options.stem
		}),
		this.options.stem.position && (this.options.stem = Object.extend(Object.clone(Prototip.Styles[this.options.style].stem) || {},
		this.options.stem), this.options.stem.position = [this.options.stem.position.match(/[a-z]+/)[0].toLowerCase(), this.options.stem.position.match(/[A-Z][a-z]+/)[0].toLowerCase()], this.options.stem.orientation = ["left", "right"].member(this.options.stem.position[0]) ? "horizontal": "vertical", this.stemInverse = {
			horizontal: !1,
			vertical: !1
		}),
		this.options.ajax && (this.options.ajax.options = Object.extend({
			onComplete: Prototype.emptyFunction
		},
		this.options.ajax.options || {}));
		if (this.options.hook.mouse) {
			var h = this.options.hook.tip.match(/[a-z]+/)[0].toLowerCase();
			this.mouseHook = Tips._inverse[h] + Tips._inverse[this.options.hook.tip.match(/[A-Z][a-z]+/)[0].toLowerCase()].capitalize()
		}
		this.fixSafari2 = Tips.WebKit419 && this.radius,
		this.setup(),
		Tips.add(this),
		this.activate(),
		Prototip.extend(this)
	},
	setup: function () {
		this.wrapper = (new Element("div", {
			className: "prototip"
		})).setStyle({
			zIndex: Tips.options.zIndex
		}),
		this.fixSafari2 && (this.wrapper.hide = function () {
			this.setStyle("left:-9500px;top:-9500px;visibility:hidden;");
			return this
		},
		this.wrapper.show = function () {
			this.setStyle("visibility:visible");
			return this
		},
		this.wrapper.visible = function () {
			return this.getStyle("visibility") == "visible" && parseFloat(this.getStyle("top").replace("px", "")) > -9500
		}),
		this.wrapper.hide(),
		Tips.fixIE && (this.iframeShim = (new Element("iframe", {
			className: "iframeShim",
			src: "javascript:false;",
			frameBorder: 0
		})).setStyle({
			display: "none",
			zIndex: Tips.options.zIndex - 1,
			opacity: 0
		})),
		this.options.ajax && (this.showDelayed = this.showDelayed.wrap(this.ajaxShow)),
		this.tip = new Element("div", {
			className: "content"
		}),
		this.title = (new Element("div", {
			className: "title"
		})).hide();
		if (this.options.closeButton || this.options.hideOn.element && this.options.hideOn.element == "closeButton") {
			this.closeButton = (new Element("div", {
				className: "close"
			})).setPngBackground(this.images + "close.png")
		}
	},
	build: function () {
		if (document.loaded) {
			this._build(),
			this._isBuilding = !0;
			return ! 0
		}
		if (!this._isBuilding) {
			document.observe("dom:loaded", this._build);
			return ! 1
		}
	},
	_build: function () {
		$(document.body).insert(this.wrapper),
		Tips.fixIE && $(document.body).insert(this.iframeShim),
		this.options.ajax && $(document.body).insert(this.loader = (new Element("div", {
			className: "prototipLoader"
		})).setPngBackground(this.images + "loader.gif").hide());
		var i = "wrapper";
		if (this.options.stem.position) {
			this.stem = (new Element("div", {
				className: "prototip_Stem"
			})).setStyle({
				height: this.options.stem[this.options.stem.orientation == "vertical" ? "height": "width"] + "px"
			});
			var h = this.options.stem.orientation == "horizontal";
			this[i].insert(this.stemWrapper = (new Element("div", {
				className: "prototip_StemWrapper clearfix"
			})).insert(this.stemBox = new Element("div", {
				className: "prototip_StemBox clearfix"
			}))),
			this.stem.insert(this.stemImage = (new Element("div", {
				className: "prototip_StemImage"
			})).setStyle({
				height: this.options.stem[h ? "width": "height"] + "px",
				width: this.options.stem[h ? "height": "width"] + "px"
			})),
			Tips.fixIE && !this.options.stem.position[1].toUpperCase().include("MIDDLE") && this.stemImage.setStyle({
				display: "inline"
			}),
			i = "stemBox"
		}
		if (this.border) {
			var n = this.border,
			m;
			this[i].insert(this.borderFrame = (new Element("ul", {
				className: "borderFrame"
			})).insert(this.borderTop = (new Element("li", {
				className: "borderTop borderRow"
			})).setStyle("height: " + n + "px").insert((new Element("div", {
				className: "prototip_CornerWrapper prototip_CornerWrapperTopLeft"
			})).insert(new Element("div", {
				className: "prototip_Corner"
			}))).insert(m = (new Element("div", {
				className: "prototip_BetweenCorners"
			})).setStyle({
				height: n + "px"
			}).insert((new Element("div", {
				className: "prototip_Between"
			})).setStyle({
				margin: "0 " + n + "px",
				height: n + "px"
			}))).insert((new Element("div", {
				className: "prototip_CornerWrapper prototip_CornerWrapperTopRight"
			})).insert(new Element("div", {
				className: "prototip_Corner"
			})))).insert(this.borderMiddle = (new Element("li", {
				className: "borderMiddle borderRow"
			})).insert(this.borderCenter = (new Element("div", {
				className: "borderCenter"
			})).setStyle("padding: 0 " + n + "px"))).insert(this.borderBottom = (new Element("li", {
				className: "borderBottom borderRow"
			})).setStyle("height: " + n + "px").insert((new Element("div", {
				className: "prototip_CornerWrapper prototip_CornerWrapperBottomLeft"
			})).insert(new Element("div", {
				className: "prototip_Corner"
			}))).insert(m.cloneNode(!0)).insert((new Element("div", {
				className: "prototip_CornerWrapper prototip_CornerWrapperBottomRight"
			})).insert(new Element("div", {
				className: "prototip_Corner"
			}))))),
			i = "borderCenter";
			var l = this.borderFrame.select(".prototip_Corner");
			$w("tl tr bl br").each(function (d, c) {
				this.radius > 0 ? Prototip.createCorner(l[c], d, {
					backgroundColor: this.options.borderColor,
					border: n,
					radius: this.options.radius
				}) : l[c].addClassName("prototip_Fill"),
				l[c].setStyle({
					width: n + "px",
					height: n + "px"
				}).addClassName("prototip_Corner" + d.capitalize())
			}.bind(this)),
			this.borderFrame.select(".prototip_Between", ".borderMiddle", ".prototip_Fill").invoke("setStyle", {
				backgroundColor: this.options.borderColor
			})
		}
		this[i].insert(this.tooltip = (new Element("div", {
			className: "tooltip " + this.options.className
		})).insert(this.toolbar = (new Element("div", {
			className: "toolbar"
		})).insert(this.title)));
		if (this.options.width) {
			var k = this.options.width;
			Object.isNumber(k) && (k += "px"),
			this.tooltip.setStyle("width:" + k)
		}
		if (this.stem) {
			var j = {};
			j[this.options.stem.orientation == "horizontal" ? "top": "bottom"] = this.stem,
			this.wrapper.insert(j),
			this.positionStem()
		}
		this.tooltip.insert(this.tip),
		this.options.ajax || this._update({
			title: this.options.title,
			content: this.content
		})
	},
	_update: function (g) {
		var f = this.wrapper.getStyle("visibility");
		this.wrapper.setStyle("height:auto;width:auto;visibility:hidden").show(),
		this.border && (this.borderTop.setStyle("height:0"), this.borderTop.setStyle("height:0")),
		g.title ? (this.title.show().update(g.title), this.toolbar.show()) : this.closeButton || (this.title.hide(), this.toolbar.hide()),
		Object.isElement(g.content) && g.content.show(),
		(Object.isString(g.content) || Object.isElement(g.content)) && this.tip.update(g.content),
		this.tooltip.setStyle({
			width: this.tooltip.getWidth() + "px"
		}),
		this.wrapper.setStyle("visibility:visible").show(),
		this.tooltip.show();
		var j = this.tooltip.getDimensions(),
		i = {
			width: j.width + "px"
		},
		h = [this.wrapper];
		Tips.fixIE && h.push(this.iframeShim),
		this.closeButton && (this.title.show().insert({
			top: this.closeButton
		}), this.toolbar.show()),
		(g.title || this.closeButton) && this.toolbar.setStyle("width: 100%"),
		i.height = null,
		this.wrapper.setStyle({
			visibility: f
		}),
		this.tip.addClassName("clearfix"),
		(g.title || this.closeButton) && this.title.addClassName("clearfix"),
		this.border && (this.borderTop.setStyle("height:" + this.border + "px"), this.borderTop.setStyle("height:" + this.border + "px"), i = "width: " + (j.width + 2 * this.border) + "px", h.push(this.borderFrame)),
		h.invoke("setStyle", i),
		this.stem && (this.positionStem(), this.options.stem.orientation == "horizontal" && this.wrapper.setStyle({
			width: this.wrapper.getWidth() + this.options.stem.height + "px"
		})),
		this.wrapper.hide()
	},
	activate: function () {
		this.eventShow = this.showDelayed.bindAsEventListener(this),
		this.eventHide = this.hide.bindAsEventListener(this),
		this.options.fixed && this.options.showOn == "mousemove" && (this.options.showOn = "mouseover"),
		this.options.showOn && this.options.showOn == this.options.hideOn && (this.eventToggle = this.toggle.bindAsEventListener(this), this.element.observe(this.options.showOn, this.eventToggle)),
		this.closeButton && this.closeButton.observe("mouseover", function (b) {
			b.setPngBackground(this.images + "close_hover.png")
		}.bind(this, this.closeButton)).observe("mouseout", function (b) {
			b.setPngBackground(this.images + "close.png")
		}.bind(this, this.closeButton));
		var e = {
			element: this.eventToggle ? [] : [this.element],
			target: this.eventToggle ? [] : [this.target],
			tip: this.eventToggle ? [] : [this.wrapper],
			closeButton: [],
			none: []
		},
		d = this.options.hideOn.element;
		this.hideElement = d || (this.options.hideOn ? "element": "none"),
		this.hideTargets = e[this.hideElement],
		!this.hideTargets && d && Object.isString(d) && (this.hideTargets = this.tip.select(d)),
		$w("show hide").each(function (h) {
			var g = h.capitalize(),
			i = this.options[h + "On"].event || this.options[h + "On"];
			i == "mouseover" ? i == "mouseenter": i == "mouseout" && i == "mouseleave",
			this[h + "Action"] = i
		}.bind(this)),
		!this.eventToggle && this.options.showOn && this.element.observe(this.options.showOn, this.eventShow),
		this.hideTargets && this.options.hideOn && this.hideTargets.invoke("observe", this.hideAction, this.eventHide),
		!this.options.fixed && this.options.showOn == "click" && (this.eventPosition = this.position.bindAsEventListener(this), this.element.observe("mousemove", this.eventPosition)),
		this.buttonEvent = this.hide.wrap(function (h, g) {
			var i = g.findElement(".close");
			i && (i.blur(), g.stop(), h(g))
		}).bindAsEventListener(this),
		(this.closeButton || this.options.hideOn && this.options.hideOn.element == ".close") && this.wrapper.observe("click", this.buttonEvent),
		this.options.showOn != "click" && this.hideElement != "element" && (this.eventCheckDelay = function () {
			this.clearTimer("show")
		}.bindAsEventListener(this), this.element.observe("mouseleave", this.eventCheckDelay));
		if (this.options.hideOn || this.options.hideAfter) {
			var f = [this.element, this.wrapper];
			this.activityEnter = function () {
				Tips.raise(this),
				this.cancelHideAfter()
			}.bindAsEventListener(this),
			this.activityLeave = this.hideAfter.bindAsEventListener(this),
			f.invoke("observe", "mouseenter", this.activityEnter).invoke("observe", "mouseleave", this.activityLeave)
		}
		this.options.ajax && this.options.showOn != "click" && (this.ajaxHideEvent = this.ajaxHide.bindAsEventListener(this), this.element.observe("mouseleave", this.ajaxHideEvent))
	},
	deactivate: function () {
		this.options.showOn && this.options.showOn == this.options.hideOn ? this.element.stopObserving(this.options.showOn, this.eventToggle) : (this.options.showOn && this.element.stopObserving(this.options.showOn, this.eventShow), this.hideTargets && this.options.hideOn && this.hideAction && this.eventHide && this.hideTargets.invoke("stopObserving", this.hideAction, this.eventHide)),
		this.eventPosition && this.element.stopObserving("mousemove", this.eventPosition),
		this.eventCheckDelay && this.element.stopObserving("mouseout", this.eventCheckDelay),
		this.wrapper.stopObserving(),
		(this.options.hideOn || this.options.hideAfter) && this.element.stopObserving("mouseenter", this.activityEnter).stopObserving("mouseleave", this.activityLeave),
		this.ajaxHideEvent && this.element.stopObserving("mouseleave", this.ajaxHideEvent)
	},
	ajaxShow: function (g, f) {
		if (!this.tooltip) {
			if (!this.build()) {
				return
			}
		}
		this.position(f);
		if (!this.ajaxContentLoading) {
			if (this.ajaxContentLoaded) {
				g(f);
				return
			}
			this.ajaxContentLoading = !0;
			var j = {
				fakePointer: {
					pointerX: 0,
					pointerY: 0
				}
			};
			if (f.pointer) {
				var i = f.pointer(),
				j = {
					fakePointer: {
						pointerX: i.x,
						pointerY: i.y
					}
				}
			} else {
				f.fakePointer && (j.fakePointer = f.fakePointer)
			}
			var h = Object.clone(this.options.ajax.options);
			h.onComplete = h.onComplete.wrap(function (d, c) {
				this._update({
					title: this.options.title,
					content: c.responseText
				}),
				this.position(j),
				function () {
					d(c);
					var a = this.loader && this.loader.visible();
					this.loader && (this.clearTimer("loader"), this.loader.remove(), this.loader = null),
					a && this.show(),
					this.ajaxContentLoaded = !0,
					this.ajaxContentLoading = null
				}.bind(this).delay(0.6)
			}.bind(this)),
			this.loaderTimer = Element.show.delay(this.options.delay, this.loader),
			this.wrapper.hide(),
			this.ajaxContentLoading = !0,
			this.loader.show(),
			this.ajaxTimer = function () {
				new Ajax.Request(this.options.ajax.url, h)
			}.bind(this).delay(this.options.delay);
			return ! 1
		}
	},
	ajaxHide: function () {
		this.clearTimer("loader")
	},
	showDelayed: function (b) {
		if (!this.tooltip) {
			if (!this.build()) {
				return
			}
		}
		this.position(b);
		this.wrapper.visible() || (this.clearTimer("show"), this.showTimer = this.show.bind(this).delay(this.options.delay))
	},
	clearTimer: function (b) {
		this[b + "Timer"] && clearTimeout(this[b + "Timer"])
	},
	show: function () {
		this.wrapper.visible() || (Tips.fixIE && this.iframeShim.show(), this.options.hideOthers && Tips.hideAll(), Tips.addVisibile(this), this.tooltip.show(), this.wrapper.show(), this.stem && this.stem.show(), this.element.fire("prototip:shown"))
	},
	hideAfter: function (b) {
		this.options.ajax && (this.loader && this.options.showOn != "click" && this.loader.hide());
		this.options.hideAfter && (this.cancelHideAfter(), this.hideAfterTimer = this.hide.bind(this).delay(this.options.hideAfter))
	},
	cancelHideAfter: function () {
		this.options.hideAfter && this.clearTimer("hideAfter")
	},
	hide: function () {
		this.clearTimer("show"),
		this.clearTimer("loader");
		this.wrapper.visible() && this.afterHide()
	},
	afterHide: function () {
		Tips.fixIE && this.iframeShim.hide(),
		this.loader && this.loader.hide(),
		this.wrapper.hide(),
		(this.borderFrame || this.tooltip).show(),
		Tips.removeVisible(this),
		this.element.fire("prototip:hidden")
	},
	toggle: function (b) {
		this.wrapper && this.wrapper.visible() ? this.hide(b) : this.showDelayed(b)
	},
	positionStem: function () {
		var h = this.options.stem,
		g = arguments[0] || this.stemInverse,
		l = Tips.inverseStem(h.position[0], g[h.orientation]),
		k = Tips.inverseStem(h.position[1], g[Tips._inverse[h.orientation]]),
		j = this.radius || 0;
		this.stemImage.setPngBackground(this.images + l + k + ".png");
		if (h.orientation == "horizontal") {
			var i = l == "left" ? h.height: 0;
			this.stemWrapper.setStyle("left: " + i + "px;"),
			this.stemImage.setStyle({
				"float": l
			}),
			this.stem.setStyle({
				left: 0,
				top: k == "bottom" ? "100%": k == "middle" ? "50%": 0,
				marginTop: (k == "bottom" ? -1 * h.width: k == "middle" ? -0.5 * h.width: 0) + (k == "bottom" ? -1 * j: k == "top" ? j: 0) + "px"
			})
		} else {
			this.stemWrapper.setStyle(l == "top" ? "margin: 0; padding: " + h.height + "px 0 0 0;": "padding: 0; margin: 0 0 " + h.height + "px 0;"),
			this.stem.setStyle(l == "top" ? "top: 0; bottom: auto;": "top: auto; bottom: 0;"),
			this.stemImage.setStyle({
				margin: 0,
				"float": k != "middle" ? k: "none"
			}),
			k == "middle" ? this.stemImage.setStyle("margin: 0 auto;") : this.stemImage.setStyle("margin-" + k + ": " + j + "px;"),
			Tips.WebKit419 && (l == "bottom" ? (this.stem.setStyle({
				position: "relative",
				clear: "both",
				top: "auto",
				bottom: "auto",
				"float": "left",
				width: "100%",
				margin: -1 * h.height + "px 0 0 0"
			}), this.stem.style.display = "block") : this.stem.setStyle({
				position: "absolute",
				"float": "none",
				margin: 0
			}))
		}
		this.stemInverse = g
	},
	position: function (z) {
		if (!this.tooltip) {
			if (!this.build()) {
				return
			}
		}
		Tips.raise(this);
		if (Tips.fixIE) {
			var y = this.wrapper.getDimensions();
			(!this.iframeShimDimensions || this.iframeShimDimensions.height != y.height || this.iframeShimDimensions.width != y.width) && this.iframeShim.setStyle({
				width: y.width + "px",
				height: y.height + "px"
			}),
			this.iframeShimDimensions = y
		}
		if (this.options.hook) {
			var x, w;
			if (this.mouseHook) {
				var v = document.viewport.getScrollOffsets(),
				u = z.fakePointer || {},
				t,
				s = 2;
				switch (this.mouseHook.toUpperCase()) {
				case "LEFTTOP":
				case "TOPLEFT":
					t = {
						x: 0 - s,
						y: 0 - s
					};
					break;
				case "TOPMIDDLE":
					t = {
						x: 0,
						y: 0 - s
					};
					break;
				case "TOPRIGHT":
				case "RIGHTTOP":
					t = {
						x: s,
						y: 0 - s
					};
					break;
				case "RIGHTMIDDLE":
					t = {
						x: s,
						y: 0
					};
					break;
				case "RIGHTBOTTOM":
				case "BOTTOMRIGHT":
					t = {
						x: s,
						y: s
					};
					break;
				case "BOTTOMMIDDLE":
					t = {
						x: 0,
						y: s
					};
					break;
				case "BOTTOMLEFT":
				case "LEFTBOTTOM":
					t = {
						x: 0 - s,
						y: s
					};
					break;
				case "LEFTMIDDLE":
					t = {
						x: 0 - s,
						y: 0
					}
				}
				t.x += this.options.offset.x,
				t.y += this.options.offset.y,
				x = Object.extend({
					offset: t
				},
				{
					element: this.options.hook.tip,
					mouseHook: this.mouseHook,
					mouse: {
						top: u.pointerY || Event.pointerY(z) - v.top,
						left: u.pointerX || Event.pointerX(z) - v.left
					}
				}),
				w = Tips.hook(this.wrapper, this.target, x);
				if (this.options.viewport) {
					var r = this.getPositionWithinViewport(w),
					q = r.stemInverse;
					w = r.position,
					w.left += q.vertical ? 2 * Prototip.toggleInt(t.x - this.options.offset.x) : 0,
					w.top += q.vertical ? 2 * Prototip.toggleInt(t.y - this.options.offset.y) : 0,
					this.stem && (this.stemInverse.horizontal != q.horizontal || this.stemInverse.vertical != q.vertical) && this.positionStem(q)
				}
				w = {
					left: w.left + "px",
					top: w.top + "px"
				},
				this.wrapper.setStyle(w)
			} else {
				x = Object.extend({
					offset: this.options.offset
				},
				{
					element: this.options.hook.tip,
					target: this.options.hook.target
				}),
				w = Tips.hook(this.wrapper, this.target, Object.extend({
					position: !0
				},
				x)),
				w = {
					left: w.left + "px",
					top: w.top + "px"
				}
			}
			if (this.loader) {
				var p = Tips.hook(this.loader, this.target, Object.extend({
					position: !0
				},
				x))
			}
			Tips.fixIE && this.iframeShim.setStyle(w)
		} else {
			var o = this.target.cumulativeOffset(),
			u = z.fakePointer || {},
			w = {
				left: (this.options.fixed ? o[0] : u.pointerX || Event.pointerX(z)) + this.options.offset.x,
				top: (this.options.fixed ? o[1] : u.pointerY || Event.pointerY(z)) + this.options.offset.y
			};
			if (!this.options.fixed && this.element !== this.target) {
				var n = this.element.cumulativeOffset();
				w.left += -1 * (n[0] - o[0]),
				w.top += -1 * (n[1] - o[1])
			}
			if (!this.options.fixed && this.options.viewport) {
				var r = this.getPositionWithinViewport(w),
				q = r.stemInverse;
				w = r.position,
				this.stem && (this.stemInverse.horizontal != q.horizontal || this.stemInverse.vertical != q.vertical) && this.positionStem(q)
			}
			w = {
				left: w.left + "px",
				top: w.top + "px"
			},
			this.wrapper.setStyle(w),
			this.loader && this.loader.setStyle(w),
			Tips.fixIE && this.iframeShim.setStyle(w)
		}
	},
	getPositionWithinViewport: function (i) {
		var h = {
			horizontal: !1,
			vertical: !1
		},
		n = this.wrapper.getDimensions(),
		m = document.viewport.getScrollOffsets(),
		l = document.viewport.getDimensions(),
		k = {
			left: "width",
			top: "height"
		};
		for (var j in k) {
			i[j] + n[k[j]] - m[j] > l[k[j]] && (i[j] = i[j] - (n[k[j]] + 2 * this.options.offset[j == "left" ? "x": "y"]), this.stem && (h[Tips._stemTranslation[k[j]]] = !0))
		}
		return {
			position: i,
			stemInverse: h
		}
	}
});
Object.extend(Prototip, {
	createCorner: function (t, s) {
		var r = arguments[2] || this.options,
		q = r.radius,
		p = r.border,
		o = {
			top: s.charAt(0) == "t",
			left: s.charAt(1) == "l"
		};
		if (this.support.canvas) {
			var n = new Element("canvas", {
				className: "cornerCanvas" + s.capitalize(),
				width: p + "px",
				height: p + "px"
			});
			t.insert(n);
			var m = n.getContext("2d");
			m.fillStyle = r.backgroundColor,
			m.arc(o.left ? q: p - q, o.top ? q: p - q, q, 0, Math.PI * 2, !0),
			m.fill(),
			m.fillRect(o.left ? q: 0, 0, p - q, p),
			m.fillRect(0, o.top ? q: 0, p, p - q)
		} else {
			var l;
			t.insert(l = (new Element("div")).setStyle({
				width: p + "px",
				height: p + "px",
				margin: 0,
				padding: 0,
				display: "block",
				position: "relative",
				overflow: "hidden"
			}));
			var k = (new Element("ns_vml:roundrect", {
				fillcolor: r.backgroundColor,
				strokeWeight: "1px",
				strokeColor: r.backgroundColor,
				arcSize: (q / p * 0.5).toFixed(2)
			})).setStyle({
				width: 2 * p - 1 + "px",
				height: 2 * p - 1 + "px",
				position: "absolute",
				left: (o.left ? 0 : -1 * p) + "px",
				top: (o.top ? 0 : -1 * p) + "px"
			});
			l.insert(k),
			k.outerHTML = k.outerHTML
		}
	}
}),
Element.addMethods({
	setPngBackground: function (e, d) {
		e = $(e);
		var f = Object.extend({
			align: "top left",
			repeat: "no-repeat",
			sizingMethod: "scale",
			backgroundColor: ""
		},
		arguments[2] || {});
		e.setStyle(Tips.fixIE ? {
			filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + d + "'', sizingMethod='" + f.sizingMethod + "')"
		}: {
			background: f.backgroundColor + " url(" + d + ") " + f.align + " " + f.repeat
		});
		return e
	}
}),
Prototip.Methods = {
	hold: function (b) {
		if (b.element && !b.element.parentNode) {
			return ! 0
		}
		return ! 1
	},
	show: function () {
		if (!Prototip.Methods.hold(this)) {
			Tips.raise(this),
			this.cancelHideAfter();
			var f = {};
			if (this.options.hook && !this.options.hook.mouse) {
				f.fakePointer = {
					pointerX: 0,
					pointerY: 0
				}
			} else {
				var e = this.target.cumulativeOffset(),
				h = this.target.cumulativeScrollOffset(),
				g = document.viewport.getScrollOffsets();
				e.left += -1 * (h[0] - g[0]),
				e.top += -1 * (h[1] - g[1]),
				f.fakePointer = {
					pointerX: e.left,
					pointerY: e.top
				}
			}
			this.options.ajax && !this.ajaxContentLoaded ? this.ajaxShow(this.showDelayed, f) : this.showDelayed(f),
			this.hideAfter()
		}
	}
},
Prototip.extend = function(b){
	b.element.prototip = {},
	Object.extend(b.element.prototip, {
		show: Prototip.Methods.show.bind(b),
		hide: b.hide.bind(b),
		remove: Tips.remove.bind(Tips, b.element)
	})
},
Prototip.start();
