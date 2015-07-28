
var MenusItems = {

	getCostoDetalle: function(){
		if($F('flid_tipo_costo')=='R'||$F('flid_tipo_costo')=='I'){
			if($F('flid_codigo_referencia')!=''){
				new Ajax.Request(Utils.getKumbiaURL('menus_items/getDetalle'), {
					method: 'GET',
					parameters: {
						'tipo_costo': $F('flid_tipo_costo'),
						'codigo': $F('flid_codigo_referencia')
					},
					onSuccess: function(transport){
						var response = transport.responseText.evalJSON();
						$('flid_codigo_referencia_det').value = response.message;
					},
					onFailure: function(transport){
						alert(transport.responseText)
					}
				});
			} else {
				$('flid_codigo_referencia_det').value = '';
			}
		} else {
			$('flid_codigo_referencia_det').value = '';
		}
	},

	initialize: function(){
		MenusItems.getCostoDetalle();
		$('flid_tipo_costo').observe('change', MenusItems.getCostoDetalle);

		var flCodigoReferencia = $('flid_codigo_referencia');
		flCodigoReferencia.observe('blur', MenusItems.getCostoDetalle);

		new Ajax.Autocompleter("flid_codigo_referencia_det", "codigo_referencia_choices", Utils.getKumbiaURL('menus_items/queryReferencias'), {
			callback: function(){
				return 'tipo_costo='+$F('flid_tipo_costo')+'&nombre='+$F('flid_codigo_referencia_det')
			},
			afterUpdateElement: function(detail, selected){
				var flCodigoReferencia = $('flid_codigo_referencia');
				if(flCodigoReferencia.disabled==false){
					flCodigoReferencia.setValue(selected.id)
				}
			}
		});

	}

};

window.after_form_load = MenusItems.initialize;