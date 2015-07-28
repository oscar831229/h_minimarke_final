
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

var HfosCommon = {

	/**
	 * Obtiene el contexto en el que se ejecuta una acción javascript
	 * Retorna la ventana activa o un elemento dentro de la ventana activa
	 */
	getContext: function(context){
		if(Object.isElement(context)){
			return context;
		} else {
			var application = Hfos.getApplication();
			if(application!==null){
				var activeWindow = application.getWorkspace().getWindowManager().getActiveWindow();
				if(typeof context != "undefined"){
					return activeWindow.getElement(context);
				} else {
					return activeWindow;
				}
			} else {
				return document.body;
			}
		}
	},

	/**
	 * CUENTAS
	 */

	/**
	 * Agrega un autocompleter para cuentas contables en la ventana actual
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	_queryByCuenta: function(cuentaElement, cuentaNombreElement){
		if(cuentaElement.getValue()!=''){
			new HfosAjax.JsonRequest('cuentas/queryByCuenta', {
				method: 'GET',
				parameters: 'cuenta='+cuentaElement.getValue(),
				onSuccess: function(cuentaElement, cuentaNombreElement, response){
					if(response.status=='OK'){
						cuentaNombreElement.setValue(response.nombre);
					} else {
						cuentaNombreElement.setValue(response.message);
					}
				}.bind(this, cuentaElement, cuentaNombreElement)
			});
		} else {
			cuentaNombreElement.setValue('');
		}
	},

	_updateCodigoCuenta: function(elementCuenta, option){
		elementCuenta.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para cuentas contables en la ventana actual
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	addCuentaCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var cuentaElement = context.selectOne('input#'+name);
			if(cuentaElement){
				var cuentaNombreElement = context.selectOne('input#'+name+'_det');
				new HfosAutocompleter(cuentaNombreElement, 'cuentas/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: HfosCommon._updateCodigoCuenta.bind(this, cuentaElement)
				});
				cuentaElement.observe('blur', HfosCommon._queryByCuenta.bind(this, cuentaElement, cuentaNombreElement));
				if(cuentaElement.getValue()!=''&&cuentaNombreElement.getValue()==''){
					HfosCommon._queryByCuenta.bind(this, cuentaElement, cuentaNombreElement)();
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},


	/**
	 * NIIF
	 */

	/**
	 * Agrega un autocompleter para niif contables en la ventana actual
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	_queryByNiif: function(niifElement, niifNombreElement){
		if(niifElement.getValue()!=''){
			new HfosAjax.JsonRequest('niif/queryByNiif', {
				method: 'GET',
				parameters: 'cuenta=' + niifElement.getValue(),
				onSuccess: function(niifElement, niifNombreElement, response){
					if(response.status=='OK'){
						niifNombreElement.setValue(response.nombre);
					} else {
						niifNombreElement.setValue(response.message);
					}
				}.bind(this, niifElement, niifNombreElement)
			});
		} else {
			cuentaNombreElement.setValue('');
		}
	},

	_updateCodigoNiif: function(elementNiif, option){
		elementNiif.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para niif contables en la ventana actual
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	addNiifCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var niifElement = context.selectOne('input#'+name);
			if(niifElement){
				var niifNombreElement = context.selectOne('input#'+name+'_det');
				new HfosAutocompleter(niifNombreElement, 'cuentas/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: HfosCommon._updateCodigoNiif.bind(this, niifElement)
				});
				niifElement.observe('blur', HfosCommon._queryByNiif.bind(this, niifElement, niifNombreElement));
				if(niifElement.getValue()!='' && niifNombreElement.getValue()==''){
					HfosCommon._queryByNiif.bind(this, niifElement, niifNombreElement)();
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * CENTROS
	 */

	/**
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	_queryByCentro: function(centroElement, centroNombreElement){
		if(centroElement.getValue()!=''){
			new HfosAjax.JsonRequest('centros/queryByCentro', {
				method: 'GET',
				parameters: 'centro='+centroElement.getValue(),
				onSuccess: function(centroElement, centroNombreElement, response){
					if(response.status=='OK'){
						centroNombreElement.setValue(response.nombre);
					} else {
						centroNombreElement.setValue(response.message);
					}
				}.bind(this, centroElement, centroNombreElement)
			});
		} else {
			centroNombreElement.setValue('');
		}
	},

	_updateCodigoCentro: function(elementCentro, option){
		elementCentro.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para centros de costos en la ventana actual
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	addCentroCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var centroElement = context.selectOne('input#'+name);
			if(centroElement){
				var centroNombreElement = context.selectOne('input#'+name+'_det');
				new HfosAutocompleter(centroNombreElement, 'centros/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: HfosCommon._updateCodigoCentro.bind(this, centroElement)
				});
				centroElement.observe('blur', HfosCommon._queryByCentro.bind(this, centroElement, centroNombreElement));
				if(centroElement.getValue()!='' && centroNombreElement.getValue()==''){
					HfosCommon._queryByCentro.bind(this, centroElement, centroNombreElement)();
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * SOCIOS
	 */

	/**
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	_queryBySocio: function(socioElement, socioNombreElement){
		if(socioElement.getValue()!=''){
			new HfosAjax.JsonRequest('socios/queryBySocios', {
				method: 'GET',
				parameters: 'socio='+socioElement.getValue(),
				onSuccess: function(socioElement, socioNombreElement, response){
				    if(response.status=='OK'){
				        socioNombreElement.setValue(response.nombre);
					} else {
					    socioNombreElement.setValue(response.message);
					}
				}.bind(this, socioElement, socioNombreElement)
			});
		} else {
			socioNombreElement.setValue('');
		}
	},

	_updateCodigoSocio: function(elementSocio, option){
		elementSocio.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para socios en la ventana actual
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	addSocioCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var socioElement = context.selectOne('input#'+name);
			if(socioElement){
				var socioNombreElement = context.selectOne('input#'+name+'_det');
				new HfosAutocompleter(socioNombreElement, 'socios/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: HfosCommon._updateCodigoSocio.bind(this, socioElement)
				});
				socioElement.observe('blur', HfosCommon._queryBySocio.bind(this, socioElement, socioNombreElement));
				if(socioElement.getValue()!=''&&socioNombreElement.getValue()==''){
					HfosCommon._queryBySocio.bind(this, socioElement, socioNombreElement)();
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * TPC
	 */

	_queryBySocioTc: function(socioElement, socioNombreElement){
		if(socioElement.getValue()!=''){
			new HfosAjax.JsonRequest('contratos/queryBySocios', {
				method: 'GET',
				parameters: 'socio='+socioElement.getValue(),
				onSuccess: function(socioElement, socioNombreElement, response){
				    if(response.status=='OK'){
				        socioNombreElement.setValue(response.nombre);
					} else {
					    socioNombreElement.setValue(response.message);
					}
				}.bind(this, socioElement, socioNombreElement)
			});
		} else {
			socioNombreElement.setValue('');
		}
	},

	_updateCodigoSocioTc: function(elementSocio, option){
		elementSocio.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para socios en la ventana actual
	 *
	 * @private
	 * @this {HfosCommon}
	 */
	addSocioTcCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var socioElement = context.selectOne('input#'+name);
			if(socioElement){
				var socioNombreElement = context.selectOne('input#'+name+'_det');
				new HfosAutocompleter(socioNombreElement, 'contratos/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: HfosCommon._updateCodigoSocioTc.bind(this, socioElement)
				});
				socioElement.observe('blur', HfosCommon._queryBySocioTc.bind(this, socioElement, socioNombreElement));
				if(socioElement.getValue()!=''&&socioNombreElement.getValue()==''){
					HfosCommon._queryBySocioTc.bind(this, socioElement, socioNombreElement)();
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * COMPROBS
	 */

	/**
	 *
	 * @this {HfosCommon}
	 */
	_queryByComprob: function(comprobElement, comprobNombreElement){
		if(comprobElement.getValue()!=''){
			new HfosAjax.JsonRequest('comprobantes/queryByComprob', {
				method: 'GET',
				parameters: 'comprob='+comprobElement.getValue(),
				onSuccess: function(comprobElement, comprobNombreElement, response){
					if(response.status=='OK'){
						comprobNombreElement.setValue(response.nombre);
					} else {
						comprobNombreElement.setValue(response.message);
					}
				}.bind(this, comprobElement, comprobNombreElement)
			});
		} else {
			comprobNombreElement.setValue('');
		}
	},

	_updateCodigoComprob: function(elementComprob, option){
		elementComprob.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para comprobantes en la ventana actual
	 *
	 * @this {HfosCommon}
	 */
	addComprobCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var comprobElement = context.selectOne('input#'+name);
			if(comprobElement){
				var comprobNombreElement = context.selectOne('input#'+name+'_det');
				new HfosAutocompleter(comprobNombreElement, 'comprobantes/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: HfosCommon._updateCodigoComprob.bind(this, comprobElement)
				});
				comprobElement.observe('blur', HfosCommon._queryByComprob.bind(this, comprobElement, comprobNombreElement));
				if(comprobElement.getValue()!=''&&comprobNombreElement.getValue()==''){
					HfosCommon._queryByComprob.bind(this, comprobElement, comprobNombreElement)();
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * NIT
	 */

	/**
	 * Consulta un Tercero por Documento
	 *
	 * @this {HfosCommon}
	 */
	_queryByNit: function(nitElement, nitNombreElement, nitCrearElement){
		if(nitElement.getValue()!=''){
			new HfosAjax.JsonRequest('terceros/queryByNit', {
				method: 'GET',
				parameters: 'nit='+nitElement.getValue(),
				onSuccess: function(nitElement, nitNombreElement, nitCrearElement, response){
					if(response.status=='OK'){
						nitNombreElement.setValue(response.nombre);
						if(typeof nitCrearElement != "undefined"){
							nitCrearElement.hide();
						}
					} else {
						nitNombreElement.setValue(response.message);
						if(typeof nitCrearElement != "undefined"){
							nitCrearElement.show();
						}
					}
				}.bind(this, nitElement, nitNombreElement, nitCrearElement)
			});
		}
	},

	/**
	 * Agrega callbacks después de mostrar el formulario de crear terceros
	 *
	 * @this {HfosCommon}
	 */
	afterShowNitCrear: function(form){
		var claseElement = form.selectOne('select#clase');
		claseElement.observe('change', function(claseElement){
			var nombreLabel = this.selectOne('label#nombreLabel');
			var clase = claseElement.getValue();
			if(clase=='A'){
				nombreLabel.update('Razón Social');
			} else {
				nombreLabel.update('Apellidos y Nombres');
			};
			new HfosAjax.JsonRequest('tipodoc/queryByClase', {
				parameters: 'clase='+clase,
				onSuccess: function(response){
					var tipodocElement = this.selectOne('select#tipodoc');
					tipodocElement.innerHTML = "";
					for(var i=0;i<response.length;i++){
						var option = document.createElement('OPTION');
						option.value = response[i].value;
						option.innerHTML = response[i].text;
						tipodocElement.appendChild(option);
					}
				}.bind(this)
			})
		}.bind(form, claseElement));
	},

	/**
	 * TERCEROS
	 */

	/**
	 * Muestra el formulario de crear terceros
	 *
	 * @this {HfosCommon}
	 */
	_createTercero: function(nitElement, nitNombreElement, nitCrearElement){
		var nombre = nitNombreElement.getValue();
		if(nombre.include('NO EXISTE')){
			nombre = '';
		};
		new HfosModalForm(this, 'terceros/crear', {
			defaults: {
				'nit': nitElement.getValue(),
				'nombre': nombre
			},
			beforeClose: function(nitElement, nitNombreElement, nitCrearElement, form, canceled){
				var nitNombreCrearElement = form.selectOne('input#nombre');
				nitCrearElement = form.selectOne('input#nit');
				nitElement.setValue(nitCrearElement.getValue());
				nitNombreElement.setValue(nitNombreCrearElement.getValue());
				nitElement.focus();
				if(canceled==true){
					nitCrearElement.show();
				} else {
					nitCrearElement.hide();
				}
			}.bind(this, nitElement, nitNombreElement, nitCrearElement)
		});
	},

	/**
	 * Actualiza el tercero consultado en el completer
	 */
	_updateNitTercero: function(elementTercero, option){
		elementTercero.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para terceros en la ventana actual
	 *
	 * @this {HfosCommon}
	 */
	addTerceroCompleter: function(name, showCreate, context){
		try {
			context = HfosCommon.getContext(context);
			if (context) {
				var nitCrearElement;
				var nitElement = context.selectOne('input#'+name);
				if(nitElement){
					var nitNombreElement = context.selectOne('input#'+name+'_det');
					if(typeof showCreate != "undefined"){
						if(showCreate==true){
							nitCrearElement = context.selectOne('input#'+name+'_create');
							nitCrearElement.observe('click', HfosCommon._createTercero.bind(this, nitElement, nitNombreElement, nitCrearElement));
							nitCrearElement.hide();
						}
					};
					new HfosAutocompleter(nitNombreElement, 'terceros/queryByName', {
						paramName: 'nombre',
						afterUpdateElement: HfosCommon._updateNitTercero.bind(this, nitElement)
					});
					nitElement.observe('blur', HfosCommon._queryByNit.bind(this, nitElement, nitNombreElement, nitCrearElement));
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},


	/*############# COMPROBANTES ################*/

	/**
	 * Actualiza el comprobante consultado en el completer
	 */
	_updateCodigoComprob: function(elementComprobante, option){
		elementComprobante.setValue(option.value);
	},


	/**
	 * Consulta un COmprobante por Codigo
	 *
	 * @this {HfosCommon}
	 */
	_queryByCodigoComprob: function(comprobElement, comprobNombreElement, comprobCrearElement){
		if(comprobElement.getValue()!=''){
			new HfosAjax.JsonRequest('comprobantes/queryByCodigo', {
				method: 'GET',
				parameters: 'codigo='+comprobElement.getValue(),
				onSuccess: function(comprobElement, comprobNombreElement, comprobCrearElement, response){
					if(response.status=='OK'){
						comprobNombreElement.setValue(response.nombre);
						if(typeof comprobCrearElement != "undefined"){
							comprobCrearElement.hide();
						}
					} else {
						comprobNombreElement.setValue(response.message);
						if(typeof comprobCrearElement != "undefined"){
							comprobCrearElement.show();
						}
					}
				}.bind(this, comprobElement, comprobNombreElement, comprobCrearElement)
			});
		}
	},

	/**
	 * Agrega un autocompleter para comprobantes en la ventana actual
	 *
	 * @this {HfosCommon}
	 */
	addComprobCompleter: function(name, showCreate, context){
		try {
			context = HfosCommon.getContext(context);
			var comprobCrearElement;
			var comprobElement = context.selectOne('input#'+name);
			if(comprobElement){
				var comprobNombreElement = context.selectOne('input#'+name+'_det');
				/*if(typeof showCreate != "undefined"){
					if(showCreate==true){
						comprobCrearElement = context.selectOne('input#'+name+'_create');
						comprobCrearElement.observe('click', HfosCommon._createTercero.bind(this, nitElement, nitNombreElement, nitCrearElement));
						comprobCrearElement.hide();
					}
				};*/
				new HfosAutocompleter(comprobNombreElement, 'comprobantes/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: HfosCommon._updateCodigoComprob.bind(this, comprobElement)
				});
				comprobElement.observe('blur', HfosCommon._queryByCodigoComprob.bind(this, comprobElement, comprobNombreElement, comprobCrearElement));
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},


	/*############# END COMPROBANTES ############*/

	/**
	 * Muestra el formulario de crear Usuario
	 *
	 * @this {HfosCommon}
	 */
	_createUsuario: function(nitElement, nitNombreElement, nitCrearElement){
		var nombre = nitNombreElement.getValue();
		if(nombre.include('NO EXISTE')){
			nombre = '';
		};
		new HfosModalForm(this, 'Usuarios/crear', {
			defaults: {
				'nit': nitElement.getValue(),
				'nombre': nombre
			},
			beforeClose: function(nitElement, nitNombreElement, nitCrearElement, form, canceled){
				//var nitCrearElement = form.selectOne('input#nit');
				var nitNombreCrearElement = form.selectOne('input#nombres');
				//nitElement.setValue(nitCrearElement.getValue());
				nitNombreElement.setValue(nitNombreCrearElement.getValue());
				nitElement.focus();
				if(canceled==true){
					nitCrearElement.show();
				} else {
					nitCrearElement.hide();
				}
			}.bind(this, nitElement, nitNombreElement, nitCrearElement)
		});
	},

	/**
	 * Actualiza el usuario consultado en el completer
	 */
	_updateNitUsuario: function(elementUsuario, option){
		elementUsuario.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para terceros en la ventana actual
	 *
	 * @this {HfosCommon}
	 */
	addUsuarioCompleter: function(name, showCreate, context){
		try {
			context = HfosCommon.getContext(context);
			var nitCrearElement;
			var nitElement = context.selectOne('input#'+name);
			if(nitElement){
				var nitNombreElement = context.selectOne('input#'+name+'_det');
				if(typeof showCreate != "undefined"){
					if(showCreate==true){
						nitCrearElement = context.selectOne('input#'+name+'_create');
						nitCrearElement.observe('click', HfosCommon._createUsuario.bind(this, nitElement, nitNombreElement, nitCrearElement));
						nitCrearElement.hide();
					}
				};
				new HfosAutocompleter(nitNombreElement, 'usuarios/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: HfosCommon._updateNitUsuario.bind(this, nitElement)
				});
				nitElement.observe('blur', HfosCommon._queryByNit.bind(this, nitElement, nitNombreElement, nitCrearElement));
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Consulta un Activo por Código
	 *
	 * @this {HfosCommon}
	 */
	_queryByActivo: function(activoElement, activoNombreElement){
		if(activoElement.getValue()!=''){
			new HfosAjax.JsonRequest('activos/queryByCodigo', {
				method: 'GET',
				parameters: 'codigo='+activoElement.getValue(),
				onSuccess: function(activoElement, activoNombreElement, response){
					if(response.status=='OK'){
						activoNombreElement.setValue(response.nombre);
					} else {
						activoNombreElement.setValue(response.message);
					}
				}.bind(this, activoElement, activoNombreElement)
			});
		}
	},

	/**
	 * Actualiza el activo consultado en el completer
	 */
	_updateCodigoActivo: function(elementActivo, option){
		elementActivo.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para terceros en la ventana actual
	 *
	 * @this {HfosCommon}
	 */
	addActivoCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var activoElement = context.selectOne('input#'+name);
			if(activeElement){
				var activoNombreElement = context.selectOne('input#'+name+'_det');
				if( activoElement && activoNombreElement ){
					new HfosAutocompleter(activoNombreElement, 'activos/queryByName', {
						paramName: 'nombre',
						afterUpdateElement: HfosCommon._updateCodigoActivo.bind(this, activoElement)
					});
					activoElement.observe('blur', HfosCommon._queryByActivo.bind(this, activoElement, activoNombreElement));
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Consulta un Item por Código
	 *
	 * @this {HfosCommon}
	 */
	_queryByItem: function(itemElement, itemNombreElement){
		if(itemElement.getValue()!=''){
			new HfosAjax.JsonRequest('referencias/queryByItem', {
				method: 'GET',
				parameters: 'codigo='+itemElement.getValue(),
				onSuccess: function(itemElement, itemNombreElement, response){
					if(response.status=='OK'){
						itemNombreElement.setValue(response.nombre);
					} else {
						itemNombreElement.setValue(response.message);
					}
				}.bind(this, itemElement, itemNombreElement)
			});
		}
	},

    /**
     * Consulta un Item por Código
     *
     * @this {HfosCommon}
     */
    _queryByItemReceta: function(itemElement, itemNombreElement, tipoDetElement){
        if(itemElement.getValue()!=''){
            new HfosAjax.JsonRequest('referencias/queryByItemReceta', {
                method: 'GET',
                parameters: 'codigo=' + itemElement.getValue() + '&tipoDet=' + tipoDetElement.getValue(),
                onSuccess: function(itemElement, itemNombreElement, response){
                    if(response.status=='OK'){
                        console.log(response);
                        itemNombreElement.setValue(response.nombre);
                    } else {
                        itemNombreElement.setValue(response.message);
                    }
                }.bind(this, itemElement, itemNombreElement)
            });
        }
    },

	/**
	 * Consulta un Activo por Código
	 *
	 * @this {HfosCommon}
	 */
	_queryByDiferidos: function(diferidosElement, diferidosNombreElement){
		if(diferidosElement.getValue()!=''){
			new HfosAjax.JsonRequest('diferidos/queryByCodigo', {
				method: 'GET',
				parameters: 'codigo='+diferidosElement.getValue(),
				onSuccess: function(diferidosElement, diferidosNombreElement, response){
					if(response.status=='OK'){
						diferidosNombreElement.setValue(response.nombre);
					} else {
						diferidosNombreElement.setValue(response.message);
					}
				}.bind(this, diferidosElement, diferidosNombreElement)
			});
		}
	},

	/**
	 * Actualiza el activo consultado en el completer
	 */
	_updateCodigoDiferidos: function(elementDiferidos, option){
		elementDiferidos.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para diferidos en la ventana actual
	 *
	 * @this {HfosCommon}
     */
	addDiferidosCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var diferidosElement = context.selectOne('input#'+name);
			if(diferidosElement){
				var diferidosNombreElement = context.selectOne('input#'+name+'_det');
				if(diferidosElement && diferidosNombreElement){
					new HfosAutocompleter(diferidosNombreElement, 'diferidos/queryByName', {
						paramName: 'nombre',
						afterUpdateElement: HfosCommon._updateCodigoDiferidos.bind(this, diferidosElement)
					});
					diferidosElement.observe('blur', HfosCommon._queryByDiferidos.bind(this, diferidosElement, diferidosNombreElement));
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Actualiza el item consultado en el completer
	 */
	_updateItem: function(elementItem, option){
		elementItem.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para items en la ventana actual
	 *
	 * @this {HfosCommon}
	 */
	addItemCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var itemElement = context.selectOne('input#'+name);
			if(itemElement){
				var itemNombreElement = context.selectOne('input#'+name+'_det');
				new HfosAutocompleter(itemNombreElement, 'referencias/queryByName', {
					paramName: 'nombre',
					afterUpdateElement: HfosCommon._updateItem.bind(this, itemElement)
				});
				itemElement.observe('blur', HfosCommon._queryByItem.bind(this, itemElement, itemNombreElement));
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

    /**
     * Agrega un autocompleter para items o recetas en la ventana actual
     *
     * @this {HfosCommon}
     */
    addItemRecetaCompleter: function(name, context){
        try {
            context = HfosCommon.getContext(context);
            var itemElement = context.selectOne('input#'+name);
            if(itemElement){
                var tipoDetName = name + '_tipo';
                var tipoDetElement = context.selectOne('select#' +tipoDetName);
                var itemNombreElement = context.selectOne('input#'+name+'_det');
                new HfosAutocompleter(itemNombreElement, 'referencias/queryByNameItemReceta', {
                    paramName: 'nombre',
                    paramNames: [tipoDetName],
                    afterUpdateElement: HfosCommon._updateItem.bind(this, itemElement)
                });
                itemElement.observe('blur', HfosCommon._queryByItemReceta.bind(this, itemElement, itemNombreElement, tipoDetElement));
            }
        }
        catch(e){
            HfosException.show(e);
        }
    },

	/**
	 * Agrega un autocompleter para terceros en la ventana actual
	 *
	 * @this {HfosCommon}
	 */
	addLocationCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var locationElement = context.selectOne('input#'+name);
			if(locationElement){
				var locationDetElement = context.selectOne('input#'+name+'_det');
				var detailElement = context.selectOne('div#'+name+'_choices');
				var url = $Kumbia.path+'common/getLocations';
				if(locationDetElement.getWidth()<300){
					url+='?short'
				};
				new Ajax.Autocompleter(locationDetElement, detailElement, url, {
					paramName: "id",
					minChars: 2,
					afterUpdateElement: function(locationElement, obj, li){
						locationElement.setValue(li.id);
					}.bind(this, locationElement)
				});
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Consulta un Empleado por Código
	 *
	 * @this {HfosCommon}
	 */
	_queryByEmpleado: function(empleadoElement, empleadoNombreElement){
		if(empleadoElement.getValue()!=''){
			new HfosAjax.JsonRequest('empleados/queryByEmpleado', {
				method: 'GET',
				parameters: 'codigo='+empleadoElement.getValue(),
				onSuccess: function(empleadoElement, empleadoNombreElement, response){
					if(response.status=='OK'){
						empleadoNombreElement.setValue(response.nombre);
					} else {
						empleadoNombreElement.setValue(response.message);
					}
				}.bind(this, empleadoElement, empleadoNombreElement)
			});
		}
	},

	/**
	 * Actualiza el activo consultado en el completer
	 */
	_updateEmpleadoActivo: function(elementEmpleado, option){
		elementEmpleado.setValue(option.value);
	},

	/**
	 * Actualiza el activo consultado en el completer
	 */
	_updateCodigoEmpleado: function(elementEmpleado, option){
		elementEmpleado.setValue(option.value);
	},

	/**
	 * Agrega un autocompleter para terceros en la ventana actual
	 *
	 * @this {HfosCommon}
	 */
	addEmpleadoCompleter: function(name, context){
		try {
			context = HfosCommon.getContext(context);
			var empleadoElement = context.selectOne('input#'+name);
			if(empleadoElement){
				var empleadoNombreElement = context.selectOne('input#'+name+'_det');
				if(empleadoElement && empleadoNombreElement){
					new HfosAutocompleter(empleadoNombreElement, 'empleados/queryByName', {
						paramName: 'nombre',
						afterUpdateElement: HfosCommon._updateCodigoEmpleado.bind(this, empleadoElement)
					});
					empleadoElement.observe('blur', HfosCommon._queryByEmpleado.bind(this, empleadoElement, empleadoNombreElement));
				}
			}
		}
		catch(e){
			HfosException.show(e);
		}
	},

	/**
	 * Obtiene el objeto HfosWindow donde se encuentra un objeto
	 */
	findWindow: function(element){
		var ascestors = element.ancestors();
		for(var i=0;i<ascestors.length;i++){
			if(ascestors[i].hasClassName('window-main')){
				var application = Hfos.getApplication();
				if(application!==null){
					return application.getWorkspace().getWindowManager().getWindow(ascestors[i].id);
				} else {
					return document.body;
				}
			}
		}
		return null;
	},

	/**
	 * Restaura los auto-completers en un contexto
	 */
	restoreCompleters: function(context){
		var completers = ['tercero', 'item', 'cuenta'];
		for(var i=0;i<completers.length;i++){
			var elements = context.select('.'+completers[i]+'Completer');
			for(var j=0;j<elements.length;j++){
				var elementDetalle = elements[j].getElement(completers[i]+'Detalle');
				if(elementDetalle){
					var completerId = elementDetalle.id.replace('_det', '');
					var completerContext = elementDetalle.up(2);
					switch(completers[i]){
						case 'tercero':
							HfosCommon.addTerceroCompleter(completerId, false, completerContext);
							break;
						case 'item':
							HfosCommon.addItemCompleter(completerId, completerContext);
							break;
						case 'cuenta':
							HfosCommon.addCuentaCompleter(completerId, completerContext);
							break;
					}
				}
			}
		}
	}

};
