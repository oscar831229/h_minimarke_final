/*
  Proto!TextboxList 0.1
  - Prototype version required: 6.0

  Credits:
  - Idea: Facebook + Apple Mail
  - Caret position method: Diego Perini <http://javascript.nwbox.com/cursor_position/cursor.js>
  - Guillermo Rauch: Original MooTools script

  Changelog:
  - 0.1: translation of MooTools script
*/

/* Copyright: InteRiders <http://interiders.com/> - Distributed under MIT - Keep this message! */

var ResizableTextbox = Class.create({

  options: $H({
    min: 5,
    max: 500,
    step: 7
  }),

  initialize: function(element, options) {
    var that = this;
    //this.setOptions(options);
    this.options.update(options);
    this.el = $(element);
    this.width = this.el.offsetWidth;
    this.el.observe('keydown', function() {
        this.cacheData('rt-value', $F(this).length);
      });
    this.el.observe(
      'keyup', function() {
        var newsize = that.options.get('step') * $F(this).length;
        if(newsize <= that.options.get('min')) newsize = that.width;
        if(! ($F(this).length == this.retrieveData('rt-value') || newsize <= that.options.min || newsize >= that.options.max))
          this.setStyle({'width': newsize});
      });
  }
});

var TextboxList = Class.create({

  options: $H({/*
    onFocus: $empty,
    onBlur: $empty,
    onInputFocus: $empty,
    onInputBlur: $empty,
    onBoxFocus: $empty,
    onBoxBlur: $empty,
    onBoxDispose: $empty,*/
    resizable: {},
    className: 'bit',
    separator: '###',
    extrainputs: true,
    startinput: true,
    hideempty: true
  }),

  initialize: function(element, options) {
    this.options.update(options);
    this.element = $(element).setStyle({'display': 'none'});
    this.bits = new Hash();
    this.events = new Hash();
    this.count = 0;
    this.current = false;
    this.maininput = this.createInput({'class': 'maininput'});
    this.holder = new Element('ul', {
      'class': 'holder'
    }).insert(this.maininput);
    this.element.insert({'before':this.holder});
    this.holder.observe('click', function(event){
          Event.stop(event);
          if(this.maininput != this.current) this.focus(this.maininput);
    }.bind(this));
    this.makeResizable(this.maininput);
    this.setEvents();
  },

  setEvents: function() {
    document.observe(Prototype.Browser.IE ? 'keydown' : 'keypress', function(e) {
      if(! this.current) return;
      if(this.current.retrieveData('type') == 'box' && e.keyCode == Event.KEY_BACKSPACE) Event.stop(e);
    }.bind(this));

    document.observe(
      'keyup', function(e) {
        Event.stop(e);
        if(! this.current) return;
        switch(e.keyCode){
          case Event.KEY_LEFT: return this.move('left');
          case Event.KEY_RIGHT: return this.move('right');
          case Event.KEY_BACKSPACE: return this.moveDispose();
        }
      }.bind(this));
    document.observe(
      'click', function() { document.fire('blur'); }.bindAsEventListener(this)
    );
  },

  update: function() {
    this.element.value = this.bits.values().join(this.options.get('separator'));
    return this;
  },

  add: function(text, html) {
    var id = this.options.get('className') + '-' + this.count++;
    var el = this.createBox($pick(html, text), {'id': id});
    (this.current || this.maininput).insert({'before':el});
    el.observe('click', function(e) {
      Event.stop(e);
      this.focus(el);
    }.bind(this));
    this.bits.set(id, text);
    if(this.options.get('extrainputs') && (this.options.get('startinput') || el.previous())) this.addSmallInput(el,'before');
    return el;
  },

  addSmallInput: function(el, where) {
    var input = this.createInput({'class': 'smallinput'});
    el.insert({}[where] = input);
    input.cacheData('small', true);
    this.makeResizable(input);
    if(this.options.get('hideempty')) input.setStyle({'display': 'none'});
    return input;
  },

  dispose: function(el) {
    this.bits.unset(el.id);
    if(el.previous() && el.previous().retrieveData('small')) el.previous().remove();
    if(this.current == el) this.focus(el.next());
    if(el.retrieveData('type') == 'box') el.onBoxDispose(this);
    el.remove();
    return this;
  },

  focus: function(el, nofocus) {
    if(! this.current) el.fire('focus');
    else if(this.current == el) return this;
    this.blur();
    el.addClassName(this.options.get('className') + '-' + el.retrieveData('type') + '-focus');
    if(el.retrieveData('small')) el.setStyle({'display': 'block'});
    if(el.retrieveData('type') == 'input') {
      el.onInputFocus(this);
      if(! nofocus) this.callEvent(el.retrieveData('input'), 'focus');
    }
    else el.fire('onBoxFocus');
    this.current = el;
    return this;
  },

  blur: function(noblur) {
    if(! this.current) return this;
    if(this.current.retrieveData('type') == 'input') {
      var input = this.current.retrieveData('input');
      if(! noblur) this.callEvent(input, 'blur');
      input.onInputBlur(this);
    }
    else this.current.fire('onBoxBlur');
    if(this.current.retrieveData('small') && ! input.get('value') && this.options.get('hideempty'))
      this.current.setStyle({'display': 'none'});
    this.current.removeClassName(this.options.get('className') + '-' + this.current.retrieveData('type') + '-focus');
    this.current = false;
    return this;
  },

  createBox: function(text, options) {
    return new Element('li', options).addClassName(this.options.get('className') + '-box').update(text).cacheData('type', 'box');
  },

  createInput: function(options) {
    var li = new Element('li', {'class': this.options.get('className') + '-input'});
    var el = new Element('input', Object.extend(options,{'type': 'text'}));
    el.observe('click', function(e) { Event.stop(e); });
    el.observe('focus', function(e) { if(! this.isSelfEvent('focus')) this.focus(li, true); }.bind(this));
    el.observe('blur', function() { if(! this.isSelfEvent('blur')) this.blur(true); }.bind(this));
    el.observe('keydown', function(e) { this.cacheData('lastvalue', this.value).cacheData('lastcaret', this.getCaretPosition()); });
    var tmp = li.cacheData('type', 'input').cacheData('input', el).insert(el);
    return tmp;
  },

  callEvent: function(el, type) {
    this.events.set(type, el);
    el[type]();
  },

  isSelfEvent: function(type) {
    return (this.events.get(type)) ? !! this.events.unset(type) : false;
  },

  makeResizable: function(li) {
    var el = li.retrieveData('input');
    el.cacheData('resizable', new ResizableTextbox(el, Object.extend(this.options.get('resizable'),{min: el.offsetWidth, max: (this.element.getWidth()?this.element.getWidth():0)})));
    return this;
  },

  checkInput: function() {
    var input = this.current.retrieveData('input');
    return (! input.retrieveData('lastvalue') || (input.getCaretPosition() === 0 && input.retrieveData('lastcaret') === 0));
  },

  move: function(direction) {
    var el = this.current[(direction == 'left' ? 'previous' : 'next')]();
    if(el && (! this.current.retrieveData('input') || ((this.checkInput() || direction == 'right')))) this.focus(el);
    return this;
  },

  moveDispose: function() {
    if(this.current.retrieveData('type') == 'box') return this.dispose(this.current);
    if(this.checkInput() && this.bits.keys().length && this.current.previous()) return this.focus(this.current.previous());
  }

});

//helper functions
Element.addMethods({
  getCaretPosition: function() {
    if (this.createTextRange) {
      var r = document.selection.createRange().duplicate();
        r.moveEnd('character', this.value.length);
        if (r.text === '') return this.value.length;
        return this.value.lastIndexOf(r.text);
    } else return this.selectionStart;
  },
  cacheData: function(element, key, value) {
    if (Object.isUndefined(this[$(element).identify()]) || !Object.isHash(this[$(element).identify()]))
        this[$(element).identify()] = $H();
    this[$(element).identify()].set(key,value);
    return element;
  },
  retrieveData: function(element,key) {
    return this[$(element).identify()].get(key);
  }
});

function $pick(){for(var B=0,A=arguments.length;B<A;B++){if(!Object.isUndefined(arguments[B])){return arguments[B];}}return null;}


/*
  Proto!TextboxList + Autocomplete 0.1
  - Prototype version required: 6.0

  Credits:
  - Idea: Facebook
  - Guillermo Rauch: Original MooTools script

  Changelog:
  - 0.1: translation of MooTools script
*/

/* Copyright: InteRiders <http://interiders.com/> - Distributed under MIT - Keep this message! */

var FacebookList = Class.create(TextboxList, {

  loptions: $H({
    autocomplete: {
      'opacity': 0.8,
      'maxresults': 10,
      'minchars': 1
    }
  }),

  initialize: function($super,element, autoholder, options) {
    $super(element, options);
    this.data = [];
		this.autoholder = $(autoholder).setOpacity(this.loptions.get('autocomplete').opacity);
		this.autoresults = this.autoholder.select('ul').first();
		var children = this.autoresults.select('li');
    children.each(function(el) { this.add(el.innerHTML); }, this);
  },

  autoShow: function(search) {
    this.autoholder.setStyle({'display': 'block'});
    this.autoholder.descendants().each(function(e) { e.setStyle({'display': 'none'}) });
    if(! search || ! search.strip() || (! search.length || search.length < this.loptions.get('autocomplete').minchars))
    {
      this.autoholder.select('.default').first().setStyle({'display': 'block'});
      this.resultsshown = false;
    } else {
      this.resultsshown = true;
      this.autoresults.setStyle({'display': 'block'}).update('');
      this.data.filter(function(str) { return str ? new RegExp(search,'i').test(str) : false; }).each(function(result, ti) {
        if(ti >= this.loptions.get('autocomplete').maxresults) return;
        var that = this;
        var el = new Element('li').update(this.autoHighlight(result, search));
        el.observe('mouseover',function() {
            that.autoFocus(this);
        });
        el.observe('click',function(e) {
            Event.stop(e);
            that.autoAdd(this);
        });
        this.autoresults.insert(el);
        el.cacheData('result', result,1);
        if(ti == 0) this.autoFocus(el);
      }, this);
    }
    return this;
  },

  autoHighlight: function(html, highlight) {
    return html.gsub(new RegExp(highlight,'i'), function(match) {
      return '<em>' + match[0] + '</em>';
    });
  },

  autoHide: function() {
    this.resultsshown = false;
    this.autoholder.setStyle({'display': 'none'});
    return this;
  },

  autoFocus: function(el) {
    if(! el) return;
    if(this.autocurrent) this.autocurrent.removeClassName('auto-focus');
    this.autocurrent = el.addClassName('auto-focus');
    return this;
  },

  autoMove: function(direction) {
    if(!this.resultsshown) return;
    this.autoFocus(this.autocurrent[(direction == 'up' ? 'previous' : 'next')]());
    return this;
  },

  autoFeed: function(text) {
    if (this.data.indexOf(text) == -1)
        this.data.push(text);
    return this;
  },

  autoAdd: function(el) {
    if(!el || ! el.retrieveData('result')) return;
    this.add(el.retrieveData('result'));
    delete this.data[this.data.indexOf(el.retrieveData('result'))];
    this.autoHide();
    var input = this.lastinput || this.current.retrieveData('input');
    input.clear().focus();
    return this;
  },

  createInput: function($super,options) {
    var li = $super(options);
    var input = li.retrieveData('input');
    input.observe('keydown', function(e) {
        this.dosearch = false;
        switch(e.keyCode) {
          case Event.KEY_UP: return this.autoMove('up');
          case Event.KEY_DOWN: return this.autoMove('down');
          case Event.KEY_RETURN:
            if(! this.autocurrent) break;
            this.autoAdd(this.autocurrent);
            this.autocurrent = false;
            this.autoenter = true;
            break;
          case Event.KEY_ESC:
            this.autoHide();
            if(this.current && this.current.retrieveData('input'))
              this.current.retrieveData('input').clear();
            break;
          default: this.dosearch = true;
        }
    }.bind(this));
    input.observe('keyup',function() {
        if(this.dosearch) this.autoShow(input.value);
    }.bind(this));
    input.observe(Prototype.Browser.IE ? 'keydown' : 'keypress', function(e) {
      if(this.autoenter) Event.stop(e);
      this.autoenter = false;
    }.bind(this));
    return li;
  },

  createBox: function($super,text, options) {
    var li = $super(text, options);
    li.observe('mouseover',function() {
        this.addClassName('bit-hover');
    });
    li.observe('mouseout',function() {
        this.removeClassName('bit-hover')
    });
    var a = new Element('a', {
      'href': '#',
      'class': 'closebutton'
      }
    );
    a.observe('click',function(e) {
          Event.stop(e);
          if(! this.current) this.focus(this.maininput);
          this.dispose(li);
    }.bind(this));
    li.insert(a).cacheData('text', text);
    return li;
  }

});

Element.addMethods({
    onBoxDispose: function(item,obj) { obj.autoFeed(item.retrieveData('text')); },
    onInputFocus: function(el,obj) { obj.autoShow(); },
    onInputBlur: function(el,obj) {
      obj.lastinput = el;
      obj.blurhide = obj.autoHide.bind(obj).delay(0.1);
    },
    filter:function(D,E){var C=[];for(var B=0,A=this.length;B<A;B++){if(D.call(E,this[B],B,this)){C.push(this[B]);}}return C;}
});
