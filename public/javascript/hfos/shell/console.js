
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

var HfosConsole = Class.create(HfosProcessContainer, {

	_contentElement: null,

	_commandElement: null,

	_commandListElement: null,

	_commands: [],

	_pointer: 0,

	/**
	 * @constructor
	 */
	initialize: function(container){
		this._contentElement = container.getContentElement();
		this._contentElement.addClassName('consoleBackground');
		this._contentElement.observe('click', this._onFocusConsole.bind(this));
		this.setContainer(container);
		this._setIndexCallbacks();
	},

	/**
	 *
	 * @this {HfosConsole}
	 */
	_setIndexCallbacks: function(){
		this._commandElement = this.selectOne('input#command');
		this._commandListElement = this.selectOne('div#commands');
		this._commandElement.observe('keyup', this._sendCommand.bind(this));
		this._commandElement.activate();

		var consoleCommands = window.sessionStorage.getItem("consoleCommands");
		if(consoleCommands!==null){
			this._commands = JSON.parse(consoleCommands);
			this._pointer = this._commands.length;
		};

	},

	/**
	 *
	 * @this {HfosConsole}
	 */
	_onFocusConsole: function(instant){
		this._commandElement.activate();
	},

	/**
	 *
	 * @this {HfosConsole}
	 */
	_updateConsole: function(content){
		var div = document.createElement('DIV');
		div.className = 'history';
		div.update(content);
		this._commandListElement.appendChild(div);
		var preTags = this._commandListElement.select('pre');
		var maxWidth = this._container.getWidth()-50;
		for(var i=0;i<preTags.length;i++){
			if(preTags[i].getWidth()>maxWidth){
				preTags[i].setStyle({
					'width': maxWidth+'px'
				});
			}
		};
		this._contentElement.scrollTop = this._contentElement.scrollHeight;
		this._notifyContentChange();
	},

	/**
	 *
	 * @this {HfosConsole}
	 */
	_sendCommand: function(event){
		if(event.keyCode==Event.KEY_UP){
			if(typeof this._commands[this._pointer-1] != "undefined"){
				this._pointer--;
				this._commandElement.setValue(this._commands[this._pointer]);
			} else {
				this._commandElement.setValue('');
			}
		};
		if(event.keyCode==Event.KEY_DOWN){
			if(typeof this._commands[this._pointer+1] != "undefined"){
				this._pointer++;
				this._commandElement.setValue(this._commands[this._pointer]);
			} else {
				this._commandElement.setValue('');
			}
		};
		if(event.keyCode==Event.KEY_RETURN){
			var command = this._commandElement.getValue().strip();
			if(command==''){
				this._updateConsole('<span class="prompt">HFOS> </span><br/>');
				this._commandElement.setValue('');
				return;
			};
			if(command=='exit'){
				Hfos.getApplication().getWorkspace().getWindowManager().getActiveWindow().close();
				return;
			} else {
				if(command=='clear'){
					this._commandListElement.innerHTML = '';
					this._commandElement.setValue('');
					return;
				} else {
					if(command=='history'){
						for(var i=0;i<this._commands.length;i++){
							this._updateConsole(i+' '+this._commands[i]);
						};
						this._commandElement.setValue('');
						return;
					}
				}
			}
			this._updateConsole('<span class="prompt">HFOS> </span>'+command+'<br/>');
			new HfosAjax.ApplicationRequest('identity/console/execute', {
				parameters: {
					'command': command
				},
				onLoading: function(){
					this._commandElement.setValue('Ejecutando...');
					this._commandElement.disable();
				}.bind(this),
				onSuccess: function(transport){
					this._updateConsole(transport.responseText);
				}.bind(this),
				onComplete: function(command){
					this._commandElement.enable();
					this._commandElement.setValue('');

					this._commands.push(command);
					this._pointer = this._commands.length;
					window.sessionStorage.setItem("consoleCommands", JSON.stringify(this._commands));
				}.bind(this, command),
			});

		};
	}

});

HfosBindings.late('win-console', 'afterCreateOrRestore', function(hfosWindow){
	var hfosConsole = new HfosConsole(hfosWindow);
});