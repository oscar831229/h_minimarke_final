/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package		Back-Office
 * @copyright	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

var Socios = {

	/**
	* Attributo que almacena la información del hyperForm actual
	*/ 
	_active: null,

	/**
	 * Cambia el attributo _active con un nuevo hyperForm
	 */
	setActive: function(hyperForm){
		Socios._active = new Socio(hyperForm);
	},

	/**
	 * Valida si existe o no información en attributo _active
	 */
	hasActive: function(){
		return Socios._active!==null;
	},

	/**
	 * Obtiene el hyperForm almacenado en attributo _active
	 */
	getActive: function(){
		return Socios._active;
	},

	/**
	 * Borra valor en attributo _active
	 */
	unsetActive: function(){
		Socios._active = null;
	},
};

/**
 * Clase Socios
 *
 * Cada formulario de Socios en pantalla tiene asociado una instancia de esta clase
 */
var Socio = Class.create({
	
	_hyperForm: null,

	/**
	 * Constructor of Socios
	 */
	initialize: function(hyperForm){
		this._hyperForm = hyperForm;
		//On create/Edit
		hyperForm.observe('beforeInput', this._addValidations.bind(this));
	},
	
	/**
	 * Add validations on sheets
	 */
	_addValidations: function(){
		var activeSection	= this._hyperForm.getActiveSection();

		console.log(activeSection);
		//Add Estudios
		var addEstudio		= activeSection.selectOne('img.addEstudio');
		if(addEstudio){
			addEstudio.observe('click', this._addEstudioRow.bind(this));
		}
		//Add Experiencia laboral
		var addExpLaboral	= activeSection.selectOne('img.addExpLaboral');
		if(addExpLaboral){
			addExpLaboral.observe('click', this._addExpLaboralRow.bind(this));
		}
		//Add Asociacion con otros Socios
		var addOtrosSocios	= activeSection.selectOne('img.addOtrosSocios');
		if(addOtrosSocios){
			addOtrosSocios.observe('click', this._addOtrosSociosRow.bind(this));
		}
		//Add Correspondencia Socios
		var subirArchivosButton	= activeSection.selectOne('input.subirArchivosButton');
		if(subirArchivosButton){
			subirArchivosButton.observe('click', this._subirArchivosModal.bind(this,subirArchivosButton));
		}
		//Delete row observer
		$$('img.delRowTemp').each(function(element){
			element.observe('click', this._delRowTemp.bind(this, element));
		}.bind(this));
		//More addons to create/edit socios
		var state = this._hyperForm.getCurrentState();
		if(state=='edit'||state=='new'){
			//alert('state: '+state);
			var titularId	= activeSection.selectOne('input#titular_id');
			if(titularId){
				titularId.observe('change', this._getInfoByTitular.bind(this, titularId));
			}
		}
	},
	/**
	 * Add info of titular on form
	 */
	_getInfoByTitular: function(titularField){
		//alert(titularId.getValue())
		var titularId = titularField.getValue();
		var hyperForm = this._hyperForm;
		var activeSection = this._hyperForm.getActiveSection();
		if(titularId){
			new HfosModal.confirm({
				title: 'Asignación de Titular',
				message: 'Desea asignar la información del titular?',
				onAccept: function(){
					new HfosAjax.JsonRequest('socios/getInfoSocios', {
						parameters: {
							'sociosId': titularId
						},
						onSuccess: function(response){
							if(response.status=='FAILED'){
								hyperForm.getMessages().error(response.message);
							}else{
								
							}
						}
					});
				}.bind(this)
			});
		}else{
			alert('No encontro codigo');
		}  
	},
	/**
	 * Add new experiencia laboral
	 */
	_addOtrosSociosRow: function(){
		var activeSection			= this._hyperForm.getActiveSection();
		var otrosSociosId			= activeSection.selectOne('input#otrosSociosId1');
		var otrosSociosIdDet		= activeSection.selectOne('input#otrosSociosId1_det');
		var tipoAsociacionSocioId	= activeSection.selectOne('select#tipoAsociacionSocioId1');
		if(otrosSociosId){
			var configRows = [{
				'asignacionSocioId'		: {'type': 'hidden', 'value': null},
				'otrosSociosId'			: {'type': 'hidden', 'value': otrosSociosId.getValue()},
				'otrosSociosIdDet'		: {'type': 'text', 'value': otrosSociosIdDet.getValue()},
				'tipoAsociacionSocioId'	: {'type': 'select', 'value': tipoAsociacionSocioId.getValue(), 'text': tipoAsociacionSocioId.options[tipoAsociacionSocioId.selectedIndex].text}
			}];
			var requiredFields = {
				'otrosSociosId'			: {'name': 'Otro Socio'}, 
				'tipoAsociacionSocioId'	: {'name': 'Tipo de Asociación con Socio'}
			};
			this._makeRow({'mainTable': 'mainTableOtrosSocios', 'configRows': configRows, 'required': requiredFields});
		}
	},
	
	/**
	 * Add new experiencia laboral
	 */
	_addExpLaboralRow: function(){
		var activeSection			= this._hyperForm.getActiveSection();
		var expLaboralesEmpresa		= activeSection.selectOne('input#expLaboralesEmpresa1');
		var expLaboralesDireccion	= activeSection.selectOne('input#expLaboralesDireccion1');
		var expLaboralesCargo		= activeSection.selectOne('input#expLaboralesCargo1');
		var expLaboralesTelefono	= activeSection.selectOne('input#expLaboralesTelefono1');
		var expLaboralesFax			= activeSection.selectOne('input#expLaboralesFax1');
		var expLaboralesFecha		= activeSection.selectOne('input#expLaboralesFecha1');
		if(expLaboralesEmpresa){
			var configRows = [{
				'expLaboralesId'		: {'type': 'hidden', 'value': null},
				'expLaboralesEmpresa'	: {'type': 'text', 'value': expLaboralesEmpresa.getValue()},
				'expLaboralesDireccion'	: {'type': 'text', 'value': expLaboralesDireccion.getValue()},
				'expLaboralesCargo'		: {'type': 'text', 'value': expLaboralesCargo.getValue()},
				'expLaboralesTelefono'	: {'type': 'text', 'value': expLaboralesTelefono.getValue()},
				'expLaboralesFax'		: {'type': 'text', 'value': expLaboralesFax.getValue()},
				'expLaboralesFecha'		: {'type': 'text', 'value': expLaboralesFecha.getValue()},
				'expLaboralesDummy'		: {'type': 'text', 'value': ''}
			}];
			var requiredFields = {
				'expLaboralesEmpresa'	: {'name': 'Empresa'}, 
				'expLaboralesDireccion'	: {'name': 'Dirección'}, 
				'expLaboralesCargo'		: {'name': 'Cargo'}, 
				'expLaboralesTelefono'	: {'name': 'Teléfono'}
			};
			this._makeRow({'mainTable': 'mainTableExpLaboral', 'configRows': configRows, 'required': requiredFields});
		}
	},

	/**
	 * Add new estudio
	 */
	_addEstudioRow: function(){
		var activeSection		= this._hyperForm.getActiveSection();
		var estudiosInstitucion	= activeSection.selectOne('input#estudiosInstitucion1');
		var estudiosFechaGrado	= activeSection.selectOne('input#estudiosFechaGrado1');
		var estudiosTitulo		= activeSection.selectOne('input#estudiosTitulo1');
		var estudiosCiudadId	= activeSection.selectOne('input#estudiosCiudadId1');
		var estudiosCiudadDet	= activeSection.selectOne('input#estudiosCiudadId1_det');
		if(estudiosInstitucion){
			var configRows = [{
				'estudiosId'			: {'type': 'hidden', 'value': null},
				'estudiosInstitucion'	: {'type': 'text', 'value': estudiosInstitucion.getValue()},
				'estudiosCiudadId'		: {'type': 'hidden', 'value': estudiosCiudadId.getValue()},
				'estudiosCiudadId_det'	: {'type': 'text', 'value': estudiosCiudadDet.getValue()},
				'estudiosFechaGrado'	: {'type': 'text', 'value': estudiosFechaGrado.getValue()},
				'estudiosTitulo'		: {'type': 'text', 'value': estudiosTitulo.getValue()}
			}];
			var requiredFields = {
				'estudiosInstitucion'	: {'name': 'Intitución'}, 
				'estudiosFechaGrado'	: {'name': 'Fecha de Grado'}, 
				'estudiosTitulo'		: {'name': 'Titulo'}, 
				'estudiosCiudadId'		: {'name': 'Ciudad'}
			};
			this._makeRow({'mainTable': 'mainTableEstudios', 'configRows': configRows, 'required': requiredFields});
			
			//Delete row observer
			$$('img.delRowTemp').each(function(element){
				element.observe('click', this._delRowTemp.bind(this, element));
			}.bind(this));
		}
	},

	/**
	 * Delete the tr tag html to remove row
	 */
	_delRowTemp: function(element){
		var tdTag = element.parentNode;
		if(tdTag){
			var trTag = tdTag.parentNode;
			if(trTag){
				trTag.remove();
			}
		}
		
	},

	/**
	 * Make row by hash
	 */
	_makeRow: function(config){
		if(!config){return false;}
		var activeSection	= this._hyperForm.getActiveSection();
		var mainTable 		= activeSection.selectOne('#'+config.mainTable+' tbody');
		var html 			= '';
		var position 		= 0;
		var count 			= 0;
		if(!mainTable){
			return false;
		}
		trs = activeSection.select('#'+config.mainTable+' tbody tr');
		if(trs.length>2){
			position = trs.length-2;
		}else{
			position = 1;
		}
		//alert('position: '+position);
		//Iterator of row in Array
		$(config.configRows).each(function(rowObj){
			var row = rowObj;
			//validate if have required fields
			var validate = this._validateRequired({'required': config.required, 'row': row});
			if(validate.bool==false){
				this._hyperForm.getMessages().error(validate.msg);
				return false;
			}
			//Iterator of cols in Hash
			html+='<tr>';
			html+='<td align="center">'+position+'</td>';
			$H(row).each(function(colObj, position){
				var colConf	= colObj.value;
				var colName	= colObj.key;
				if(colConf.type=='text'){
					html+='<td align="center"><input type="hidden" name="'+colName+'[]" id="'+colName+'" value="'+colConf.value+'"/>'+colConf.value+'</td>';
				}
				if(colConf.type=='select'){
					var text = colConf.value;
					if(colConf.text){
						text = colConf.text;
					}
					html+='<td align="center"><input type="hidden" name="'+colName+'[]" id="'+colName+'" value="'+colConf.value+'"/>'+text+'</td>';
				}
				if(colConf.type=='hidden'){
					html+='<input type="hidden" name="'+colName+'[]" id="'+colName+'" value="'+colConf.value+'"/>';
				}
			}.bind(this));
			html+='<td align="center"><img title="Borrar linea" alt="" class="delRowTemp" src="/h/img/backoffice/delete-l.gif"></td>';
			html+='</tr>';
			position++;
		}.bind(this));
		//adding html into main table 
		mainTable.insert(html);
	},
	
	/**
	 * Validate the required fields in row of makeRow
	 * @return boolean
	 */
	_validateRequired: function(config){
		var row			= config.row;//hash
		var required	= config.required;//hash
		var ret			= {};
		ret.bool		= true;
		ret.fields		= [];
		ret.msg			= '';
		//iterator of row
		$H(row).each(function(colObj, position){
			var colConf	= colObj.value;
			var colName	= colObj.key;
			//iterator of required fields
			$H(required).each(function(field, position){
				var fieldName = field.key;
				var fieldConf = field.value;
				//If is the same field and vlaue is empty add message
				if(colName==fieldName){
					if(!colConf.value || colConf.value=='@'){
						ret.fields.push(fieldConf.name);
						ret.bool = false;
					}
				}
			});
		});
		ret.msg = 'Los campos '+ret.fields.join(', ')+' son requeridos';
		return ret;
	},

	/**
	* Abre un modal de un partial para subir archivos
	*/
	_subirArchivosModal: function(hyperForm, response){
		var hyperForm = this._hyperForm
		var activeSection = this._hyperForm.getActiveSection();
		
		var sociosIdField = activeSection.selectOne('#socios_id');
		if(!sociosIdField && response && response.data){
			//si existe un valor en contrato asociado es un cambio de contrato cogemos ese para la amortización
			var contratoAsociado = response.data.last().value;
			if(contratoAsociado){
				contratoAsociadoArray = contratoAsociado.split('/');
				contratoAsociadoId = parseInt(contratoAsociadoArray[0]);
				sociosId = contratoAsociadoId;
			}else{
				//alert($H(response.data.last()).inspect());
				sociosId = response.data[0].value;
			}
		}else{
			sociosId = sociosIdField.getValue();
		}
		if(sociosId>0) {
			new HfosModalForm(this, 'socios/subirCorrespondencia', {
				notSubmit: true,
				style: "width: 50%;",
				parameters: {
					'sociosId': sociosId,
				},
				messageDefault: 'Al subir el archivo cierre la ventana para terminar el proceso',
				beforeClose: function(sociosId, form, canceled, response){
					this._actualizarCorrespondencia(sociosId );
				}.bind(this, sociosId)
			});
		}
	},

	/**
	 * Genera la ajustePagos
	 */
	_subirCorrespondencia: function(subirCorrespondenciaButton){
		var activeSection = this._hyperForm.getActiveSection();
		var subirCorrespondenciaForm = activeSection.selectOne('#correspondenciaSociosFormId');
		if (subirCorrespondenciaForm) {
			this.fileUpload(subirCorrespondenciaForm, $Kumbia.path+'socios/socios/correspondenciaSocios', 'archivo');
			subirCorrespondenciaForm.enable();
		} else {
			alert("correspondenciaSociosForm not found");
		}
	},

	_actualizarCorrespondencia: function(sociosId ) {
		var activeSection = this._hyperForm.getActiveSection();
		
		new HfosAjax.Request('socios/correspondencia', {
			parameters: {'sociosId': sociosId },
	       	onSuccess: function(transport){ 
	        	activeSection.selectOne('#mainTableCorrespondencia').update(transport.responseText);
	      	}.bind(this)
		});
	},

	/**
	* Subir archivos de correspondencia de socios
	*/
	fileUpload: function(form, action_url, div_id) {
		var activeSection	= this._hyperForm();

		 // Create the iframe...
	    var iframe = document.createElement("iframe");
	    iframe.setAttribute("id", "upload_iframe");
	    iframe.setAttribute("name", "upload_iframe");
	    iframe.setAttribute("width", "0");
	    iframe.setAttribute("height", "0");
	    iframe.setAttribute("border", "0");
	    iframe.setAttribute("style", "width: 0; height: 0; border: none;");
	 
	    // Add to document...
	    form.parentNode.appendChild(iframe);
	    window.frames['upload_iframe'].name = "upload_iframe";
	 
	    iframeId = document.getElementById("upload_iframe");
	 
	    // Add event...
	    var eventHandler = function () {
	 
            if (iframeId.detachEvent) iframeId.detachEvent("onload", eventHandler);
            else iframeId.removeEventListener("load", eventHandler, false);
 
            // Message from server...
            if (iframeId.contentDocument) {
                content = iframeId.contentDocument.body.innerHTML;
            } else if (iframeId.contentWindow) {
                content = iframeId.contentWindow.document.body.innerHTML;
            } else if (iframeId.document) {
                content = iframeId.document.body.innerHTML;
            }
 
            document.getElementById(div_id).innerHTML = content;
 
            // Del the iframe...
            setTimeout('iframeId.parentNode.removeChild(iframeId)', 250);
            
            var response = content.evalJSON(true);
            
            if(response.status=='FAILED'){
				activeSection.getMessages().error(response.message);
			} else {
				activeSection.getMessages().success('Se subio correspondencia correctamente');
			}
			activeSection.getElement('headerSpinner').hide();
			activeSection.getElement('importButton').enable();
        }.bind(this);
	 
	    if (iframeId.addEventListener) iframeId.addEventListener("load", eventHandler, true);
	    if (iframeId.attachEvent) iframeId.attachEvent("onload", eventHandler);
	 
	    // Set properties of form...
	    form.setAttribute("target", "upload_iframe");
	    form.setAttribute("action", action_url);
	    form.setAttribute("method", "post");
	    form.setAttribute("enctype", "multipart/form-data");
	    form.setAttribute("encoding", "multipart/form-data");
	 
	    // Submit the form...
	    form.submit();
	 
	    document.getElementById(div_id).innerHTML = "Uploading...";
	    activeSection.getElement('headerSpinner').show();
	  	activeSection.getElement('importButton').disable();
	},
});

HyperFormManager.lateBinding('socios', 'afterInitialize', function(){
	Socios.setActive(this);
	var socio = Socios.getActive();
});
