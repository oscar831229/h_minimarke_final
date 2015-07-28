
/*var test = new UnitTest();
test.testSuite({
	'abrirMenu': {
		'delay': 700,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirFormulario': {
		'delay': 700,
		'action': function(){
			this.getSubMenu("Formas de Pago").simulate("click");
		}
	},
	'entrarACrear': {
		'delay': 700,
		'action': function(){
			this.getButton("Crear Forma de Pago").simulate("click");
		}
	},
	'intentarGrabar': {
		'delay': 700,
		'action': function(){
			this.getButton("Grabar").simulate("click");
			this.assertErrorMessage('El código, el descripción y la cuenta asociada son requeridos');
		}
	},
	'llenarFormularioCrear': {
		'delay': 700,
		'action': function(){
			this.simulateKeyEntry(this.getField('codigo'), this.getRandomInteger(1, 100));
			this.simulateKeyEntry(this.getField('descripcion'), this.getRandomString(100));
			this.simulateKeyEntry(this.getField('cta_contable'), '1');
			this.getButton("Grabar").simulate("click");
			this.assertErrorMessage('La cuenta contable no existe o no es auxiliar');
		}
	},
	'colocarCuentaCrear': {
		'delay': 2000,
		'action': function(){
			this.simulateKeyEntry(this.getField('cta_contable'), '220515');
			this.getButton("Grabar").simulate("click");
		}
	},
	'entrarAEditar': {
		'delay': 700,
		'action': function(){
			this.getButton("Editar").simulate("click");
		}
	},
	'llenarFormularioEditar': {
		'delay': 700,
		'action': function(){
			this.simulateKeyEntry(this.getField('descripcion'), this.getRandomString(100));
			this.simulateKeyEntry(this.getField('cta_contable'), '1');
			this.getButton("Grabar").simulate("click");
			this.assertErrorMessage('La cuenta contable no existe o no es auxiliar');
		}
	},
	'colocarCuentaEditar': {
		'delay': 2000,
		'action': function(){
			this.simulateKeyEntry(this.getField('cta_contable'), '220515');
			this.getButton("Grabar").simulate("click");
		}
	},
});*/