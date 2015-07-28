
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2013
 * @version		$Id$
 */

/**
 * HfosStorageDb
 *
 * Permite a las aplicaciones tener un almacenamiento local usando IndexedDB
 */
var HfosStorageDb = Class.create({

	_application: null,

	_db: null,

	/**
	 * @constructor
	 */
	initialize: function(application, onSuccess){
		this._application = application;
		this._openDatabase(onSuccess);
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	_openDatabase: function(onSuccess){
		if(this._db==null){
			if(typeof window.mozIndexedDB == "undefined"){
				var shouldUpdateNotify = window.sessionStorage.getItem('shouldUpdateNotify');
				if(shouldUpdateNotify===null){
					/*new HfosModal.customDialog({
						icon: 'firefox48',
						title: 'Navegador Incompatible',
						message: 'Debido a que su navegador es antiguo el sistema no podrá recuperar su trabajo en caso'+
							' que su equipo se apague inesperadamente, por favor instale ó actualice a Mozilla Firefox 17.0',
						buttons: {
							'Omitir': {
								'action': function(modal){
									modal.acceptDefault();
								}
							},
							'Descargar': {
								'action': function(){
									window.location = 'http://www.mozilla.com/es-AR/firefox/';
								}
							}
						}
					});*/
					window.sessionStorage.setItem('shouldUpdateNotify', true);
				};
				this._application.setStorage(null);
			} else {
				var token = this._application.getVirtualUser().getToken();
				var name = 'hfos-ux10-'+this._application.getCode()+'-'+token;
				var request = window.mozIndexedDB.open(name, 1, "HFOS recovery workspace data");
				request.onupgradeneeded = this._onUpgradeNeeded.bind(this, request);
				request.onsuccess = this._onOpenDatabase.bind(this, request, onSuccess);
				request.onerror = this._onError.bind(this, request);
			}
		};
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	_onUpgradeNeeded: function(request, event)
	{
		if (event.newVersion === 1) {
			if (event.target.transaction) {
				var db = event.target.transaction.db;
				var objects = db.createObjectStore('Objects', { keyPath: "id" });
				objects.createIndex('id', 'id', { unique: true });
				var hyperForms = db.createObjectStore('HyperForms', { keyPath: "id" });
				hyperForms.createIndex('id', 'id', { unique: true });
				var hyperGrids = db.createObjectStore('HyperGrids', { keyPath: "id" });
				hyperGrids.createIndex('id', 'id', { unique: true });
				var hyperSearch = db.createObjectStore('HyperSearch', { keyPath: "id" });
				hyperSearch.createIndex('id', 'id', { unique: true });
				var inputFields = db.createObjectStore('InputFields', { keyPath: "id" });
				inputFields.createIndex('id', 'id', { unique: true });
				inputFields.createIndex('containerKey', 'containerKey', { unique: false });
			}
		}
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	_onOpenDatabase: function(request, onSuccess, event)
	{
		var db = event.target.result;
		if (typeof db.setVersion == "function") {
			this._application.setStorage(null);
		} else {
			this._db = db;
			this._application.setStorage(this);
			request.onerror = this._onGenericError.bind(this);
			if(typeof onSuccess == "function"){
				onSuccess();
			};
		}
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	_onGenericError: function(event)
	{
		var code = 0;
		if (typeof event.errorCode != "undefined") {
			code = event.errorCode;
		} else {
			code = 100;
		};
		if (code > 1) {
			if(typeof event.target != "undefined"){
				HfosException.showSilent('Silent-Exception: ' + $H(event.target).inspect().replace('<', ''));
				event.preventDefault();
			} else {
				HfosException.showSilent(event);
			}
		};
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	_onError: function(event)
	{
		new HfosModal.confirm({
			title: this._application.getName(),
			message: 'No autorizó el almacenamiento local para el sistema. Si su equipo se apaga inesperadamente no podrá recuperar su trabajo. ¿Desea autorizarlo ahora?',
			onAccept: function(){
				this._application.setStorage(null);
				this._openDatabase();
			}.bind(this)
		});
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	save: function(objectStoreName, row, onSuccess)
	{
		if (this._db !== null) {
			if (typeof IDBTransaction.READ_WRITE != "undefined") {
				var transaction = this._db.transaction(objectStoreName, IDBTransaction.READ_WRITE);
			} else {
				var transaction = this._db.transaction(objectStoreName, "readwrite");
			};
			transaction.onerror = this._onGenericError.bind(this);
			var objectStore = transaction.objectStore(objectStoreName);
			var request = objectStore.put(row);
			request.onerror = this._onGenericError.bind(this);
			if (typeof onSuccess == "function") {
				request.onsuccess = onSuccess;
			}
		} else {
			if(typeof onSuccess == "function"){
				onSuccess();
			}
		}
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	remove: function(objectStoreName, recordId, onSuccess)
	{
		if (this._db !== null) {
			if (typeof IDBTransaction.READ_WRITE != "undefined") {
				var transaction = this._db.transaction(objectStoreName, IDBTransaction.READ_WRITE);
			} else {
				var transaction = this._db.transaction(objectStoreName, "readwrite");
			};
			transaction.onerror = this._onGenericError.bind(this);
			var request = transaction.objectStore(objectStoreName).delete(recordId)
			request.onerror = this._onGenericError.bind(this);
			if (typeof onSuccess == "function") {
				request.onsuccess = onSuccess;
			}
		} else {
			if (typeof onSuccess == "function") {
				onSuccess();
			}
		}
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	findFirst: function(objectStoreName, value, onSuccess){
		if(this._db!==null){
			var request = this._db.transaction(objectStoreName).objectStore(objectStoreName).get(value);
			request.onerror = this._onGenericError.bind(this);
			if(typeof onSuccess == "function"){
				request.onsuccess = function(onSuccess, request, event){
					try {
						onSuccess(request.result);
					}
					catch(e){
						HfosException.show(e)
					}
				}.bind(this, onSuccess, request)
			}
		};
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	findAll: function(objectStoreName, onSuccess)
	{
		if (this._db !== null) {
			var records = [];
			var cursor = this._db.transaction(objectStoreName).objectStore(objectStoreName).openCursor();
			cursor.onerror = this._onGenericError.bind(this);
			cursor.onsuccess = function(onSuccess, records, event)
			{
				try {
					var cursor = event.target.result;
					if(cursor){
						records.push(cursor.value);
						cursor.continue();
					} else {
						if(typeof onSuccess == "function"){
							onSuccess(records);
						}
					}
				}
				catch(e){
					HfosException.show(e)
				}
			}.bind(this, onSuccess, records);
		};
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	findAllBy: function(objectStoreName, indexName, referenceValue, onSuccess)
	{
		if (this._db!==null) {
			var records = [];
			var singleKeyRange = IDBKeyRange.only(referenceValue);
			var cursor = this._db.transaction(objectStoreName).objectStore(objectStoreName).index(indexName).openCursor(singleKeyRange);
			cursor.onsuccess = function(referenceValue, records, onSuccess, event)
			{
				try {
					var cursor = event.target.result;
					if (cursor) {
						records.push(cursor.value);
						cursor.continue();
					} else {
						if(typeof onSuccess == "function"){
							onSuccess(records);
						}
					}
				}
				catch (e) {
					HfosException.show(e)
				}
			}.bind(this, referenceValue, records, onSuccess);
		}
	},

	/**
	 *
	 * @this {HfosStorageDb}
	 */
	free: function()
	{

	}

});