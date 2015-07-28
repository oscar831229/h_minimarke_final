
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
 * HfosDraggable
 *
 * Convierte un elemento DOM en arrastrable y controla los eventos sobre él
 */
var HfosDraggable = Class.create({

	_element: null,

	_mouseDownEvent: null,

	_mouseUpEvent: null,

	_mouseMoveEvent: null,

	_options: null,

	_active: false,

	_dragElement: null,

	/**
	 * @constructor
	 */
	initialize: function(element, options){
		this._element = element;
		if(typeof options != "undefined"){
			this._options = options;
			this._dragElement = options.handle;
		} else {
			this._options = {};
			this._dragElement = element;
		};
		this._mouseDownEvent = this._startDrag.bind(this);
		this._mouseMoveEvent = this._updateDrag.bind(this);
		this._mouseUpEvent = this._stopDrag.bind(this);
		this._mouseLeaveEvent = this._stopDrag.bind(this);
		this._dragElement.observe('mousedown', this._mouseDownEvent);
		this._element.observe('mousemove', this._mouseMoveEvent);
		this._element.observe('mouseup', this._mouseUpEvent);
		this._element.observe('mouseleave', this._mouseLeaveEvent);
	},

	/**
	 * @this {HfosDraggable}
	 */
	_startDrag: function(event){
		if(Event.isLeftClick(event)){
			if(event.target.tagName!='INPUT'&&event.target.tagName!='SELECT'){
				var pointer = [Event.pointerX(event), Event.pointerY(event)];
				var pos = this._element.positionedOffset();
				this._offset = [];
				this._offset[0] = pointer[0] - pos[0];
				this._offset[1] = pointer[1] - pos[1];
				this._active = true;
			}
		};
	},

	/**
	 * @this {HfosDraggable}
	 */
	_updateDrag: function(event){
		if(this._active==true){
			var pointer = [Event.pointerX(event), Event.pointerY(event)];
			this._element.setStyle({
				left: (pointer[0]-this._offset[0])+'px',
				top: (pointer[1]-this._offset[1])+'px'
			});
		}
	},

	/**
	 * @this {HfosDraggable}
	 */
	_stopDrag: function(){
		this._active = false;
		if(typeof this._options.onEnd == "function"){
			this._options.onEnd({
				element: this._element
			});
		}
	},

	/**
	 * @this {HfosDraggable}
	 */
	_leaveDrag: function(event){
		if(this._active==true){
			var pointer = [Event.pointerX(event), Event.pointerY(event)];
			if(pointer[1]>(this._element.offsetTop-200)){
				this._active = false;
			}
		}
	}

})