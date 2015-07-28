
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
 * @const
 */
var CKEDITOR_BASEPATH = $Kumbia.path+'javascript/ckeditor/';

/**
 *
 */
var HfosMail = Class.create({

	_element: null,

	_badgeElement: null,

	_inboxElement: null,

	_productivity: null,

	_editorLoaded: false,

	_lastMessageId: null,

	/**
	 * @constructor
	 */
	initialize: function(productivity){

		this._productivity = productivity;

		this._inboxElement = productivity.getLeftPanel().getElement('inbox-div');
		this._badgeElement = this._inboxElement.getElement('badge');
		this._inboxElement.observe('click', this._getInbox.bind(this));

		this._composeElement = productivity.getLeftPanel().getElement('compose-div');
		this._composeElement.observe('click', this._getCompose.bind(this, null, null));

		this._sentElement = productivity.getLeftPanel().getElement('sent-div');
		this._sentElement.observe('click', this._getSent.bind(this));

		this._trashElement = productivity.getLeftPanel().getElement('trash-div');
		this._trashElement.observe('click', this._getTrash.bind(this));

		this._getMessageCount();
	},

	/**
	 * @this {HfosMail}
	 */
	_getRightPanel: function(){
		return this._productivity.getRightPanel();
	},

	/**
	 * @this {HfosMail}
	 */
	_getInbox: function(){
		new HfosAjax.ApplicationRequest('identity/mail/getInbox', {
			checkAcl: true,
			onSuccess: function(transport){
				this._getRightPanel().update(transport.responseText);
				this._addBoxCallbacks();
				this._adjustPanels();
				window.setTimeout(function(){
					this._getMessageCount();
				}.bind(this), 2000)
			}.bind(this)
		});
	},

	/**
	 * @this {HfosMail}
	 */
	_getCompose: function(action, messageId){
		new HfosAjax.ApplicationRequest('identity/mail/getCompose', {
			checkAcl: true,
			parameters: {
				'action': action,
				'messageId': messageId
			},
			onSuccess: function(transport){
				this._getRightPanel().update(transport.responseText);
				this._addComposeCallbacks();
				this._initializeEditor();
				this._adjustPanels();
			}.bind(this)
		});
	},

	/**
	 * @this {HfosMail}
	 */
	_getSent: function(){
		new HfosAjax.ApplicationRequest('identity/mail/getSent', {
			checkAcl: true,
			onSuccess: function(transport){
				this._getRightPanel().update(transport.responseText);
				this._addBoxCallbacks();
				this._adjustPanels();
			}.bind(this)
		});
	},

	/**
	 * @this {HfosMail}
	 */
	_getTrash: function(){
		new HfosAjax.ApplicationRequest('identity/mail/getTrash', {
			checkAcl: true,
			onSuccess: function(transport){
				this._getRightPanel().update(transport.responseText);
				this._addBoxCallbacks();
				this._adjustPanels();
			}.bind(this)
		});
	},

	/**
	 * @this {HfosMail}
	 */
	_initializeEditor: function(){
		if(this._editorLoaded==false){
			var script = document.createElement('SCRIPT');
			script.src = $Kumbia.path+'javascript/ckeditor/ckeditor.js';
			script.observe('load', function(){
				this._setEditorToContent();
				this._editorLoaded = true;
			}.bind(this));
			document.body.appendChild(script);
		} else {
			this._setEditorToContent();
		}
	},

	/**
	 * @this {HfosMail}
	 */
	_setEditorToContent: function(){
		if($('content')){
			if(typeof CKEDITOR.instances['content'] != "undefined"){
				delete CKEDITOR.instances['content'];
			};
			var pageSize = WindowUtilities.getPageSize(document.body);
			CKEDITOR.replace('content', {
				language: 'es',
				uiColor: '#EAEAEA',
				height: (pageSize.windowHeight-353),
	        	toolbar: [
	        		['Styles', 'Format'],
			        ['Bold', 'Italic', 'Strike'],
			        ['Cut', 'Copy', 'Paste','PasteText','PasteFromWord'],
			        ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
			        ['Link', 'Unlink', 'Anchor'],
			        '/',
			        ['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
			        ['Image', 'Table', 'HorizontalRule','Smiley','SpecialChar','PageBreak']
			    ]
			});
		}
	},

	/**
	 * @this {HfosMail}
	 */
	_addBoxCallbacks: function(){

		var rightPanel = this._getRightPanel();

		var windowClose = rightPanel.getElement('window-close');
		if(windowClose !== null){
			windowClose.observe('click', this._closePanel.bind(this));

			var seleccionarSelect = rightPanel.getElement('seleccionar');
			seleccionarSelect.observe('click', this._selectMessages.bind(this, seleccionarSelect));

			var seleccionarCheck = rightPanel.getElement('seleccionarCheck');
			seleccionarCheck.observe('click', this._selectMessagesCheck.bind(this, seleccionarCheck));

			var responderButton = rightPanel.getElement('responder');
			if(responderButton){
				responderButton.observe('click', this._answerMessage.bind(this));
			}

			var reenviarButton = rightPanel.getElement('reenviar');
			if(reenviarButton){
				reenviarButton.observe('click', this._forwardMessage.bind(this));
			}

			var suprimirButton = rightPanel.getElement('suprimir');
			if(suprimirButton){
				suprimirButton.observe('click', this._deleteMessage.bind(this));
			}

			var suprimirDefinitivoButton = rightPanel.getElement('suprimirDefinitivo');
			if(suprimirDefinitivoButton){
				suprimirDefinitivoButton.observe('click', this._deleteDefinitiveMessage.bind(this));
			}

			var restaurarButton = rightPanel.getElement('restaurar');
			if(restaurarButton){
				restaurarButton.observe('click', this._restoreMessage.bind(this));
			}

			var marcarLeidoButton = rightPanel.getElement('marcarLeido');
			marcarLeidoButton.observe('click', this._markReadMessage.bind(this))

			var marcarNoLeidoButton = rightPanel.getElement('marcarNoLeido');
			marcarNoLeidoButton.observe('click', this._markUnreadMessage.bind(this))

			var mailTable = rightPanel.getElement('mailTable');
			var mailsTr = mailTable.select('tbody tr')
			for(var i=0;i<mailsTr.length;i++){

				var mailsTd = mailsTr[i].select('td');
				for(var j=1;j<=5;j++){
					mailsTd[j].observe('click', this._openMessage.bind(this, mailsTr[i]));
				};

				var checkTr = mailsTr[i].selectOne('input[type="checkbox"]');
				checkTr.observe('click', this._selectRow.bind(this, checkTr, mailsTr[i]));

				var deleteImg = mailsTr[i].selectOne('img.delete');
				if(deleteImg){
					deleteImg.observe('click', this._deleteThisMessage.bind(this, mailsTr[i]));
				}
			}
		}
	},

	/**
	 * @this {HfosMail}
	 */
	_addComposeCallbacks: function(){

		var fields = ["to", "cc"];
		var rightPanel = this._getRightPanel();

		var windowClose = rightPanel.getElement('window-close');
		if(windowClose){
			windowClose.observe('click', this._closePanel.bind(this));

			var sendButton = rightPanel.getElement('send');
			sendButton.observe('click', this._sendMessage.bind(this));

			var discardButton = rightPanel.getElement('discard');
			discardButton.observe('click', this._closePanel.bind(this));

			for(var i=0;i<fields.length;i++){
				var field = rightPanel.selectOne('#'+fields[i]);
				new Ajax.Autocompleter(field, fields[i]+"_choices", Utils.getURL('identity/mail/getDirectory'), {
				 	paramName: "id",
				 	minChars: 2,
				 	tokens: [","],
				 	afterUpdateElement: function(field){
			 			$(field+"_choices").removeClassName('autocomplete');
			 			$(field+"_choices").addClassName('autocomplete');
			 		}.bind(this, fields[i])
				});
				field.observe('keyup', function(){
					var length = this.value.strip().length;
					var height = (parseInt(length/95)+1)*23;
					new Effect.Morph(this, {
						style: {
							height: height+"px"
						}
					});
				});
			};
		}
	},

	/**
	 * @this {HfosMail}
	 */
	_sendMessage: function(){

		var rightPanel = this._getRightPanel();

		var toElement = rightPanel.selectOne('textarea#to');
		if(toElement.getValue().strip()==''){
			new HfosModal.alert({
				title: 'Redactar Mensaje',
				message: 'Indique al menos un destinatario del correo'
			});
			return;
		};

		var subjectElement = rightPanel.selectOne('input#subject');
		if(subjectElement.getValue().strip()==''){
			new HfosModal.confirm({
				title: 'Redactar Mensaje',
				message: 'El mensaje no tiene asunto. ¿Desea confirmar de todas maneras?',
				onAccept: function(){
					this._deliveryMessage();
				}.bind(this)
			});
			return;
		} else {
			this._deliveryMessage();
		}

	},

	/**
	 * @this {HfosMail}
	 */
	_deliveryMessage: function(){
		var rightPanel = this._getRightPanel();
		new HfosAjax.JsonApplicationRequest('identity/mail/delivery', {
			checkAcl: true,
			parameters: {
				'to': rightPanel.selectOne('textarea#to').getValue(),
				'cc': rightPanel.selectOne('textarea#cc').getValue(),
				'subject': rightPanel.selectOne('input#subject').getValue(),
				'content': CKEDITOR.instances['content'].document.getBody().getHtml()
			},
			onSuccess: function(response){
				if(response.status=='OK'){
					this._getInbox();
				} else {
					if(response.status=='FAILED'){
						new HfosModal.alert({
							title: 'Redactar Mensaje',
							message: response.message
						});
					}
				}
			}.bind(this)
		})
	},

	/**
	 * @this {HfosMail}
	 */
	_selectMessagesCheck: function(element){
		if(element.checked){
			this._selectAll();
		} else {
			this._selectNone();
		}
	},

	/**
	 * @this {HfosMail}
	 */
	_selectMessages: function(element){
		switch(element.getValue()){
			case 'A':
				this._selectAll();
				break;
			case 'N':
				this._selectNone();
				break;
			case 'R':
				this._selectRead();
				break;
			case 'U':
				this._selectUnread();
				break;
			default:
				this._selectNone();
				break;
		}
	},

	/**
	 * @this {HfosMail}
	 */
	_selectAll: function(){
		var mailTable = this._getRightPanel().getElement('mailTable');
		var mailsTr = mailTable.select('tbody tr')
		for(var i=0;i<mailsTr.length;i++){
			var checkTr = mailsTr[i].selectOne('input[type="checkbox"]');
			checkTr.checked = true;
			this._selectRow.bind(this, checkTr, mailsTr[i])();
		};
	},

	/**
	 * @this {HfosMail}
	 */
	_selectNone: function(){
		var mailTable = this._getRightPanel().getElement('mailTable');
		var mailsTr = mailTable.select('tbody tr')
		for(var i=0;i<mailsTr.length;i++){
			var checkTr = mailsTr[i].selectOne('input[type="checkbox"]');
			checkTr.checked = false;
			this._selectRow.bind(this, checkTr, mailsTr[i])();
		};
	},

	/**
	 * @this {HfosMail}
	 */
	_selectRead: function(){
		var mailTable = this._getRightPanel().getElement('mailTable');
		var mailsTr = mailTable.select('tbody tr')
		for(var i=0;i<mailsTr.length;i++){
			var checkTr = mailsTr[i].selectOne('input[type="checkbox"]');
			if(!mailsTr[i].hasClassName('unread')){
				checkTr.checked = true;
			} else {
				checkTr.checked = false;
			}
			this._selectRow.bind(this, checkTr, mailsTr[i])();
		};
	},

	/**
	 * @this {HfosMail}
	 */
	_selectUnread: function(){
		var mailTable = this._getRightPanel().getElement('mailTable');
		var mailsTr = mailTable.select('tbody tr')
		for(var i=0;i<mailsTr.length;i++){
			var checkTr = mailsTr[i].selectOne('input[type="checkbox"]');
			if(mailsTr[i].hasClassName('unread')){
				checkTr.checked = true;
			} else {
				checkTr.checked = false;
			};
			this._selectRow.bind(this, checkTr, mailsTr[i])();
		};
	},

	/**
	 * @this {HfosMail}
	 */
	_answerMessage: function(){
		if(this._lastMessageId!==null){
			this._getCompose('answer', this._lastMessageId);
		}
	},

	/**
	 * @this {HfosMail}
	 */
	_forwardMessage: function(){
		if(this._lastMessageId!==null){
			this._getCompose('forward', this._lastMessageId);
		}
	},

	/**
	 * @this {HfosMail}
	 */
	_deleteThisMessage: function(mailTr){
		var rows = [mailTr.id];
		var parameters = "messages[]="+mailTr.id;
		new HfosAjax.JsonApplicationRequest('identity/mail/delete', {
			checkAcl: true,
			parameters: parameters,
			onSuccess: this._onDeleteOrRestoreMessages.bind(this, rows)
		})
	},

	/**
	 * @this {HfosMail}
	 */
	_deleteMessage: function(){
		var rows = this._getRowsChecked();
		if(rows.length>0){
			var parameters = this._getRowsCheckedQueryString();
		} else {
			var selectedRow = this._getRightPanel().getElement('selected-this');
			var parameters = "messages[]="+selectedRow.id;
		};
		new HfosAjax.JsonApplicationRequest('identity/mail/delete', {
			parameters: parameters,
			checkAcl: true,
			onSuccess: this._onDeleteOrRestoreMessages.bind(this, rows)
		})
	},

	/**
	 * @this {HfosMail}
	 */
	_restoreMessage: function(){
		var rows = this._getRowsChecked();
		if(rows.length>0){
			var parameters = this._getRowsCheckedQueryString();
		} else {
			var selectedRow = this._getRightPanel().getElement('selected-this');
			var parameters = "messages[]="+selectedRow.id;
		}
		new HfosAjax.JsonApplicationRequest('identity/mail/restore', {
			parameters: parameters,
			checkAcl: true,
			onSuccess: this._onDeleteOrRestoreMessages.bind(this, rows)
		})
	},

	/**
	 * @this {HfosMail}
	 */
	_deleteDefinitiveMessage: function(){
		var rows = this._getRowsChecked();
		if(rows.length>0){
			var parameters = this._getRowsCheckedQueryString();
		} else {
			var selectedRow = this._getRightPanel().getElement('selected-this');
			var parameters = "messages[]="+selectedRow.id;
		}
		new HfosAjax.JsonApplicationRequest('identity/mail/deleteDefinitive', {
			checkAcl: true,
			parameters: parameters,
			onSuccess: this._onDeleteOrRestoreMessages.bind(this, rows)
		});
	},

	/**
	 * @this {HfosMail}
	 */
	_markReadMessage: function(){
		var rows = this._getRowsChecked();
		if(rows.length>0){
			var parameters = this._getRowsCheckedQueryString();
		} else {
			var selectedRow = this._getRightPanel().getElement('selected-this');
			var parameters = "messages[]="+selectedRow.id;
		}
		new HfosAjax.JsonApplicationRequest('identity/mail/markRead', {
			checkAcl: true,
			parameters: parameters,
			onSuccess: this._onMarkReadMessages.bind(this, rows)
		})
	},

	/**
	 * @this {HfosMail}
	 */
	_markUnreadMessage: function(){
		var rows = this._getRowsChecked();
		if(rows.length>0){
			var parameters = this._getRowsCheckedQueryString();
		} else {
			var selectedRow = this._getRightPanel().getElement('selected-this');
			var parameters = "messages[]="+selectedRow.id;
		}
		new HfosAjax.JsonApplicationRequest('identity/mail/markUnread', {
			checkAcl: true,
			parameters: parameters,
			onSuccess: this._onMarkUnreadMessages.bind(this, rows)
		})
	},

	/**
	 * @this {HfosMail}
	 */
	_onMarkReadMessages: function(rows){
		if(rows.length>0){
			for(var i=0;i<rows.length;i++){
				var element = $(rows[i]);
				if(element){
					element.removeClassName('unread');
				}
			};
		} else {
			var selectedRow = this._getRightPanel().getElement('selected-this');
			if(selectedRow!=null){
				selectedRow.removeClassName('unread');
			}
		};
		window.setTimeout(function(){
			this._getMessageCount();
		}.bind(this), 2000);
	},

	/**
	 * @this {HfosMail}
	 */
	_onMarkUnreadMessages: function(rows){
		if(rows.length>0){
			for(var i=0;i<rows.length;i++){
				var element = $(rows[i]);
				if(element){
					element.addClassName('unread');
				}
			};
		} else {
			var selectedRow = this._getRightPanel().getElement('selected-this');
			if(selectedRow!=null){
				selectedRow.addClassName('unread');
			}
		};
		window.setTimeout(function(){
			this._getMessageCount();
		}.bind(this), 2000);
	},

	/**
	 * @this {HfosMail}
	 */
	_onDeleteOrRestoreMessages: function(rows){
		if(rows.length>0){
			for(var i=0;i<rows.length;i++){
				var element = $(rows[i]);
				if(element){
					element.erase();
				}
			};
		} else {
			var selectedRow = this._getRightPanel().getElement('selected-this');
			if(selectedRow!=null){
				selectedRow.erase();
			}
		};

		var mailContent = this._getRightPanel().getElement('mailContent');
		mailContent.update('');
		this._adjustPanels();
		this._enableButtons();

		window.setTimeout(function(){
			this._getMessageCount();
		}.bind(this), 2000);
	},

	/**
	 * @this {HfosMail}
	 */
	_selectThisRow: function(mailTr){
		var mailTable = this._getRightPanel().getElement('mailTable');
		var mailsTr = mailTable.select('tbody tr.selected-this');
		for(var i=0;i<mailsTr.length;i++){
			mailsTr[i].removeClassName('selected-this');
		};
		mailTr.addClassName('selected-this');
	},

	/**
	 * @this {HfosMail}
	 */
	_openMessage: function(mailTr){
		this._lastMessageId = mailTr.id;
		mailTr.removeClassName('unread');
		this._selectThisRow(mailTr);
		new HfosAjax.ApplicationRequest('identity/mail/openMessage', {
			checkAcl: true,
			parameters: {
				'sid': mailTr.id
			},
			onSuccess: function(transport){
				var mailContent = this._getRightPanel().getElement('mailContent');
				mailContent.update(transport.responseText);
				this._adjustPanels();
				this._enableButtons();
				window.setTimeout(function(){
					this._getMessageCount();
				}.bind(this), 2000)
			}.bind(this)
		});
	},

	/**
	 * @this {HfosMail}
	 */
	_getRowsChecked: function(){
		var rows = [];
		var mailTable = this._getRightPanel().getElement('mailTable');
		var mailCheckbox = mailTable.select('tbody input[type="checkbox"]');
		for(var i=0;i<mailCheckbox.length;i++){
			if(mailCheckbox[i].checked){
				rows.push(mailCheckbox[i].up(1).id);
			}
		};
		return rows;
	},

	/**
	 * @this {HfosMail}
	 */
	_getRowsCheckedQueryString: function(){
		var rows = [];
		var mailTable = this._getRightPanel().getElement('mailTable');
		var mailCheckbox = mailTable.select('tbody input[type="checkbox"]');
		for(var i=0;i<mailCheckbox.length;i++){
			if(mailCheckbox[i].checked){
				rows.push("messages[]="+mailCheckbox[i].up(1).id);
			}
		};
		return rows.join('&');
	},

	/**
	 * @this {HfosMail}
	 */
	_enableButtons: function(){
		var rightPanel = this._getRightPanel();
		var mailContent = rightPanel.getElement('mailContent');
		var numberChecked = this._getRowsChecked().length;
		var fields = ['responder', 'reenviar', 'restaurar', 'suprimirDefinitivo', 'suprimir', 'marcarLeido', 'marcarNoLeido'];
		if(mailContent.innerHTML!=''||numberChecked>=1){
			for(var i=0;i<fields.length;i++){
				var fieldElement = rightPanel.getElement(fields[i]);
				if(fieldElement){
					fieldElement.enable();
				}
			};
		} else {
			for(var i=0;i<fields.length;i++){
				var fieldElement = rightPanel.getElement(fields[i]);
				if(fieldElement){
					fieldElement.disable();
				}
			};
		}
	},

	/**
	 * @this {HfosMail}
	 */
	_selectRow: function(element, mailTr, event){
		if(element.checked){
			mailTr.addClassName('selected');
		} else {
			mailTr.removeClassName('selected');
		};
		this._enableButtons();
	},

	/**
	 * @this {HfosMail}
	 */
	_closePanel: function(){
		this._getRightPanel().update('');
		this._productivity.selectNone();
	},

	/**
	 * @this {HfosMail}
	 */
	_adjustPanels: function(){
		var pageSize = WindowUtilities.getPageSize(document.body);
		var mailContent = this._getRightPanel().getElement('mailContent');
		if(mailContent){
			var mailPreviews = this._getRightPanel().getElement('mailPreviews');
			if(mailContent.innerHTML==""){
				mailContent.hide();
				mailPreviews.setStyle({
					'height': (pageSize.windowHeight-103)+'px'
				});
			} else {
				mailContent.show();
				mailPreviews.setStyle({
					'height': ((pageSize.windowHeight/2)-99)+'px'
				});
				mailContent.setStyle({
					'height': ((pageSize.windowHeight/2)-11)+'px'
				});
				window.setTimeout(function(){
					var iframeContent = this._getRightPanel().getElement('iframeContent');
					var frameSize = iframeContent.contentWindow.document.body;
					iframeContent.setStyle({
						'overflowY': 'hidden',
						'height': (frameSize.offsetHeight+70)+'px'
					});
				}.bind(this), 500);
			}
		} else {
			var mailCompose = this._getRightPanel().getElement('mailCompose');
			if(mailCompose){
				mailCompose.setStyle({
					'height': (pageSize.windowHeight-103)+'px'
				});
			}
		}
	},

	/**
	 * @this {HfosMail}
	 */
	_getMessageCount: function(){
		new HfosAjax.JsonApplicationRequest('identity/mail/getMessageCount', {
			onSuccess: function(response){
				if(response.status=='OK'){
					if(response.unread==0){
						new Effect.Fade(this._badgeElement);
					} else {
						this._badgeElement.update(response.unread);
						new Effect.Appear(this._badgeElement);
					}
					if(response.trash==0){
						this._trashElement.setStyle({
							'backgroundImage': 'url('+$Kumbia.path+'img/backoffice/bin-metal.png)'
						});
					} else {
						this._trashElement.setStyle({
							'backgroundImage': 'url('+$Kumbia.path+'img/backoffice/bin-metal-full.png)'
						});
					}
					if(response.inbox==0){
						this._inboxElement.setStyle({
							'backgroundImage': 'url('+$Kumbia.path+'img/backoffice/inbox.png)'
						});
					} else {
						this._inboxElement.setStyle({
							'backgroundImage': 'url('+$Kumbia.path+'img/backoffice/inbox-document.png)'
						});
					}
				}
			}.bind(this)
		});
	}

});