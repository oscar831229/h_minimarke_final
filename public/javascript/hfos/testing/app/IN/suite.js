
var inveTest = new UnitTest();
inveTest.setSuite({

	//Crear un centro de costo
	'abrirBasicas-1': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirCentros-1': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Centros de Costo").simulate("click");
		}
	},
	'crearCentros-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Crear Centro de costo").simulate("click");
		}
	},
	'intentarGrabarCentro-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarGrabarCentro-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El código y el nombre son requeridos');
			this.simulateKeyEntry(this.getField('codigo'), '1000');
			this.simulateKeyEntry(this.getField('nom_centro'), 'CENTRO PRUEBA 1');
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarGrabarEstadoCentro-1': {
		'delay': 2000,
		'action': function(){
			this.assertErrorMessage('El estado debe ser "ACTIVO" ó "INACTIVO"');
			this.setComboValue(this.getField('estado'), 'ACTIVO');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarCentros-1': {
		'delay': 500,
		'action': function(){
			this.assertNoticeMessage('Visualizando un centro de costo');
			this.closeWindow('Centros de Costo');
		}
	},

	//Crear un almacen
	'abrirBasicas-2': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirAlmacenes-1': {
		'delay': 200,
		'action': function(){
			this.getSubMenu("Almacenes").simulate("click");
		}
	},
	'crearAlmacen-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Crear Almacén").simulate("click");
		}
	},
	'intentarGrabarAlmacen-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarCentroAlmacen-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El centro de costo no existe o no es auxiliar');
			this.setComboValue(this.getField('centro_costo'), 'CENTRO PRUEBA 1');
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarGrabarAlmacen-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El código, el nombre y el usuarios son requeridos');
			this.simulateKeyEntry(this.getField('codigo'), '1');
			this.simulateKeyEntry(this.getField('nom_almacen'), 'ALMACEN PRINCIPAL');
			this.setComboValue(this.getField('usuarios_id'), 'ADMINISTRADOR SOLUTION');
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarGrabarEstadoAlmacen-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El campo "Estado" debe ser "ACTIVO" ó "INACTIVO", La "Clase de Almacén" debe ser "PRINCIPAL" ó "DEPENDENCIA"');
			this.setComboValue(this.getField('estado'), 'ACTIVO');
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarGrabarClaseAlmacen-1': {
		'delay': 2000,
		'action': function(){
			this.assertErrorMessage('La "Clase de Almacén" debe ser "PRINCIPAL" ó "DEPENDENCIA"');
			this.setComboValue(this.getField('clase_almacen'), 'PRINCIPAL');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarAlmacen-1': {
		'delay': 500,
		'action': function(){
			this.assertNoticeMessage('Visualizando un almacén');
			this.closeWindow('Almacenes');
		}
	},

	//Crear una línea de producto
	'abrirBasicas-3': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirMenuReferencias-1': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Referencias").simulate("click");
		}
	},
	'abrirLineas-1': {
		'delay': 1000,
		'action': function(){
			this.getSubOption(1).simulate("click");
		}
	},
	'crearLinea-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Crear Línea").simulate("click");
		}
	},
	'intentarGrabarLinea-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarAlmacenLinea-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El almacén no es válido');
			this.setComboValue(this.getField('almacen'), 'ALMACEN PRINCIPAL');
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarRequeridosLinea-1': {
		'delay': 2000,
		'action': function(){
			this.assertErrorMessage('El código, el nombre y el recibe referencias? son requeridos');
			this.simulateKeyEntry(this.getField('linea'), '01');
			this.simulateKeyEntry(this.getField('nombre'), 'LINEA 01');
			this.setComboValue(this.getField('es_auxiliar'), 'NO');
			this.getButton("Grabar").simulate("click");
		}
	},
	'volverLinea-1': {
		'delay': 500,
		'action': function(){
			this.assertNoticeMessage('Visualizando una línea');
			this.getButton("Volver").simulate("click");
		}
	},
	'crearLinea-2': {
		'delay': 500,
		'action': function(){
			this.getButton("Crear Línea").simulate("click");
		}
	},
	'llenarRequeridosLinea-2': {
		'delay': 500,
		'action': function(){
			this.setComboValue(this.getField('almacen'), 'ALMACEN PRINCIPAL');
			this.simulateKeyEntry(this.getField('linea'), '01');
			this.simulateKeyEntry(this.getField('nombre'), 'LINEA 01');
			this.setComboValue(this.getField('es_auxiliar'), 'SI');
			this.getButton("Grabar").simulate("click");
		}
	},
	'verificarRequeridosLinea-2': {
		'delay': 2000,
		'action': function(){
			this.assertErrorMessage('Una línea ya existe con esos almacén y código');
			this.simulateKeyEntry(this.getField('linea'), '0101');
			this.simulateKeyEntry(this.getField('nombre'), 'LINEA 0101');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarLinea-2': {
		'delay': 500,
		'action': function(){
			this.assertNoticeMessage('Visualizando una línea');
			this.closeWindow('Líneas de Referencias');
		}
	},

	//Crear una unidad
	'abrirBasicas-4': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirMenuUnidad-1': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Unidades").simulate("click");
		}
	},
	'abrirUnidades-1': {
		'delay': 1000,
		'action': function(){
			this.getSubOption(0).simulate("click");
		}
	},
	'crearUnidad-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Crear Unidad de medida").simulate("click");
		}
	},
	'intentarGrabarUnidad-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarMagnitudUnidad-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('La magnitud no es válida');
			this.setComboValue(this.getField('magnitud'), 'CANTIDAD');
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarRequeridosUnidad-1': {
		'delay': 2000,
		'action': function(){
			this.assertErrorMessage('El código y el nombre son requeridos');
			this.simulateKeyEntry(this.getField('codigo'), '1');
			this.simulateKeyEntry(this.getField('nom_unidad'), 'UNIDAD 01');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarUnidad-1': {
		'delay': 500,
		'action': function(){
			this.assertNoticeMessage('Visualizando una unidad de medida');
			this.closeWindow('Unidades de Medida');
		}
	},

	//Crear una referencia
	'abrirBasicas-5': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirMenuReferencias-2': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Referencias").simulate("click");
		}
	},
	'abrirReferencias-1': {
		'delay': 1000,
		'action': function(){
			this.getSubOption(0).simulate("click");
		}
	},
	'crearReferencia-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Crear Referencia").simulate("click");
		}
	},
	'intentarGrabarReferencia-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarRequeridosReferencia-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El código, el descripción, la línea de producto y la unidad son requeridos');
			this.simulateKeyEntry(this.getField('item'), '010101');
			this.simulateKeyEntry(this.getField('descripcion'), 'REFERENCIA 010101');
			this.setComboValue(this.getField('linea'), 'LINEA 01');
			this.setComboValue(this.getField('unidad'), 'UNIDAD 01');
			this.getButton("Grabar").simulate("click");
		}
	},
	'verificarRequeridosReferencia-1': {
		'delay': 2000,
		'action': function(){
			this.assertErrorMessage('La línea de productos "01" asignada a la referencia no recibe referencias');
			this.setComboValue(this.getField('linea'), 'LINEA 0101');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarReferencia-1': {
		'delay': 500,
		'action': function(){
			this.assertNoticeMessage('Visualizando una referencia');
			this.closeWindow('Referencias');
		}
	},

	//Crear una forma de pago
	'abrirBasicas-6': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirFormaPago-1': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Formas de Pago").simulate("click");
		}
	},
	'crearFormaPago-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Crear Forma de Pago").simulate("click");
		}
	},
	'intentarGrabarFormaPago-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarRequeridosFormaPago-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El código, el descripción y la cuenta asociada son requeridos');
			this.simulateKeyEntry(this.getField('codigo'), '2');
			this.simulateKeyEntry(this.getField('descripcion'), 'PROVEEDORES NACIONALES');
			this.simulateKeyEntry(this.getField('cta_contable'), '1');
			this.getButton("Grabar").simulate("click");
		}
	},
	'verificarCuentaFormaPago-1': {
		'delay': 2000,
		'action': function(){
			this.assertErrorMessage('La cuenta contable no existe o no es auxiliar');
			this.simulateKeyEntry(this.getField('cta_contable'), '220515');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarFormaPago-1': {
		'delay': 500,
		'action': function(){
			this.assertNoticeMessage('Visualizando una Forma de Pago');
			this.closeWindow('Formas de Pago');
		}
	},

	//Crear un proveedor
	'abrirBasicas-7': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirMenuProveedores-1': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Proveedores").simulate("click");
		}
	},
	'abrirTerceros-1': {
		'delay': 1500,
		'action': function(){
			this.getSubOption(0).simulate("click");
		}
	},
	'crearTerceros-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Crear Tercero").simulate("click");
		}
	},
	'intentarGrabarTerceros-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarTipoDocumentoTerceros-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El tipo de documento no es válido');
			this.setComboValue(this.getField('tipodoc'), 'CEDULA DE CIUDADANIA');
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarRequeridosTerceros-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El documento y el nombre son requeridos');
			this.simulateKeyEntry(this.getField('nit'), 'A00000001');
			this.simulateKeyEntry(this.getField('nombre'), 'PROVEEDOR 1');
			this.getButton("Grabar").simulate("click");
		}
	},
	'llenarClaseTerceros-1': {
		'delay': 2000,
		'action': function(){
			this.assertErrorMessage('El campo "Clase" debe ser "CLIENTE", "EMPRESA" ó "EXTRANJERO"');
			this.setComboValue(this.getField('clase'), 'CLIENTE');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarTerceros-1': {
		'delay': 500,
		'action': function(){
			this.assertNoticeMessage('Visualizando un tercero');
			this.closeWindow('Terceros');
		}
	},

	//Crear Orden de Compra
	'abrirEntradas-1': {
		'delay': 500,
		'action': function(){
			this.getMenu("Entradas").simulate("click");
		}
	},
	'abrirOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Órdenes de Compra").simulate("click");
		}
	},
	'crearOrdenes-1': {
		'delay': 1000,
		'action': function(){
			this.getButton("Crear Orden de compra").simulate("click");
		}
	},
	'intentarGrabarOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.getButton("Grabar").simulate("click");
		}
	},
	'verificarFormaPagoOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('La orden de compra debe tener una forma de pago asociada');
			this.setComboValue(this.getField('forma_pago'), 'PROVEEDORES NACIONALES');
			this.getButton("Grabar").simulate("click");
		}
	},
	'verificarProveedoresOrdenes-1': {
		'delay': 1000,
		'action': function(){
			this.assertErrorMessage('La orden de compra debe tener un proveedor asociado');
			this.simulateKeyEntry(this.getField('nit'), 'A00000001');
			this.getField('nit').simulate("blur");
			this.getField('nit').simulate("change");
		}
	},

	//Ir a cambiar el tipo de regimen del proveedor
	'verificarProveedorRegimenOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('No se ha definido el tipo de regímen del proveedor');
		}
	},
	'abrirBasicas-8': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirMenuProveedores-2': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Proveedores").simulate("click");
		}
	},
	'abrirTerceros-2': {
		'delay': 1000,
		'action': function(){
			this.getSubOption(0).simulate("click");
		}
	},
	'buscarTerceros-2': {
		'delay': 500,
		'action': function(){
			this.simulateKeyEntry(this.getField('nit'), 'A00000001');
			this.getButton("Consultar").simulate("click");
		}
	},
	'seleccionarTerceros-2': {
		'delay': 500,
		'action': function(){
			this.getElement("hySortRow").simulate("dblclick");
		}
	},
	'editarTerceros-2': {
		'delay': 500,
		'action': function(){
			this.getButton("Editar").simulate("click");
		}
	},
	'cambiarRegimenTerceros-2': {
		'delay': 2000,
		'action': function(){
			this.setComboValue(this.getField('estado_nit'), 'COMUN');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarTerceros-2': {
		'delay': 2000,
		'action': function(){
			this.assertNoticeMessage('Visualizando el tercero 1 de  1');
			this.closeWindow('Terceros');
		}
	},
	'refrescarProveedor1TerceroOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.simulateKeyEntry(this.getField('nit'), 'x');
			this.getField('nit').simulate("blur");
			this.getField('nit').simulate("change");
		}
	},
	'refrescarProveedor2TerceroOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.simulateKeyEntry(this.getField('nit'), 'A00000001');
			this.getField('nit').simulate("blur");
			this.getField('nit').simulate("change");
		}
	},
	'verificarHotelTerceroOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El hotel no ha sido creado como un tercero');
		}
	},
	'abrirBasicas-9': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirMenuProveedores-3': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Proveedores").simulate("click");
		}
	},
	'abrirTerceros-3': {
		'delay': 1000,
		'action': function(){
			this.getSubOption(0).simulate("click");
		}
	},
	'crearTerceros-3': {
		'delay': 500,
		'action': function(){
			this.getButton("Crear Tercero").simulate("click");
		}
	},
	'llenarRequeridosTerceros-3': {
		'delay': 1500,
		'action': function(){
			this.assertNoticeMessage('Ingrese los datos en los campos y presione "Grabar"');
			this.setComboValue(this.getField('tipodoc'), 'NIT');
			this.simulateKeyEntry(this.getField('nit'), 'A1234567');
			this.simulateKeyEntry(this.getField('nombre'), 'HOTEL TEST');
			this.setComboValue(this.getField('clase'), 'EMPRESA');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarTerceros-3': {
		'delay': 1500,
		'action': function(){
			this.assertNoticeMessage('Visualizando un tercero');
			this.closeWindow('Terceros');
		}
	},
	'refrescarProveedor3TerceroOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.simulateKeyEntry(this.getField('nit'), 'x');
			this.getField('nit').simulate("blur");
			this.getField('nit').simulate("change");
		}
	},
	'refrescarProveedor4TerceroOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.simulateKeyEntry(this.getField('nit'), 'A00000001');
			this.getField('nit').simulate("blur");
			this.getField('nit').simulate("change");
		}
	},

	'verificarHotelTerceroOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('El hotel no ha sido creado como un tercero');
		}
	},
	'abrirBasicas-9': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirMenuProveedores-3': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Proveedores").simulate("click");
		}
	},
	'abrirTerceros-3': {
		'delay': 1000,
		'action': function(){
			this.getSubOption(0).simulate("click");
		}
	},
	'crearTerceros-3': {
		'delay': 500,
		'action': function(){
			this.getButton("Crear Tercero").simulate("click");
		}
	},
	'llenarRequeridosTerceros-3': {
		'delay': 1500,
		'action': function(){
			this.assertNoticeMessage('Ingrese los datos en los campos y presione "Grabar"');
			this.setComboValue(this.getField('tipodoc'), 'NIT');
			this.simulateKeyEntry(this.getField('nit'), 'A1234567');
			this.simulateKeyEntry(this.getField('nombre'), 'HOTEL TEST');
			this.setComboValue(this.getField('clase'), 'EMPRESA');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarTerceros-3': {
		'delay': 1500,
		'action': function(){
			this.assertNoticeMessage('Visualizando un tercero');
			this.closeWindow('Terceros');
		}
	},
	'refrescarProveedor3TerceroOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.simulateKeyEntry(this.getField('nit'), 'x');
			this.getField('nit').simulate("blur");
			this.getField('nit').simulate("change");
		}
	},
	'refrescarProveedor4TerceroOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.simulateKeyEntry(this.getField('nit'), 'A00000001');
			this.getField('nit').simulate("blur");
			this.getField('nit').simulate("change");
		}
	},

	//Ir a cambiar el tipo de regimen del hotel
	'verificarHotelRegimenOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('No se ha definido el tipo de regímen del hotel');
		}
	},
	'abrirBasicas-10': {
		'delay': 500,
		'action': function(){
			this.getMenu("Básicas").simulate("click");
		}
	},
	'abrirMenuProveedores-4': {
		'delay': 500,
		'action': function(){
			this.getSubMenu("Proveedores").simulate("click");
		}
	},
	'abrirTerceros-4': {
		'delay': 1000,
		'action': function(){
			this.getSubOption(0).simulate("click");
		}
	},
	'buscarTerceros-4': {
		'delay': 500,
		'action': function(){
			this.simulateKeyEntry(this.getField('nombre'), 'hotel');
			this.getButton("Consultar").simulate("click");
		}
	},
	'seleccionarTerceros-4': {
		'delay': 500,
		'action': function(){
			this.getElement("hySortRow").simulate("dblclick");
		}
	},
	'editarTerceros-4': {
		'delay': 500,
		'action': function(){
			this.getButton("Editar").simulate("click");
		}
	},
	'cambiarRegimenTerceros-4': {
		'delay': 2000,
		'action': function(){
			this.setComboValue(this.getField('estado_nit'), 'COMUN');
			this.getButton("Grabar").simulate("click");
		}
	},
	'cerrarTerceros-4': {
		'delay': 2000,
		'action': function(){
			this.assertNoticeMessage('Visualizando el tercero 1 de  1');
			this.closeWindow('Terceros');
		}
	},
	'refrescarProveedor5TerceroOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.simulateKeyEntry(this.getField('nit'), 'x');
			this.getField('nit').simulate("blur");
			this.getField('nit').simulate("change");
		}
	},
	'refrescarProveedor6TerceroOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.simulateKeyEntry(this.getField('nit'), 'A00000001');
			this.getField('nit').simulate("blur");
			this.getField('nit').simulate("change");
		}
	},

	//Intentar Grabar
	'intentarGrabarOrdenes-1-2': {
		'delay': 500,
		'action': function(){
			this.assertNoticeMessage('Indique el proveedor y forma de pago de la orden de compra');
			this.getButton("Grabar").simulate("click");
		}
	},
	'verificarReferenciasOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('Debe ingresar las referencias de la orden de compra');
			this.getProcess().getTabs().changeTabByLabel('Detalle')
		}
	},
	'agregarReferenciasOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.assertNoticeMessage('Indique referencias y haga click en "Agregar" para adicionarla a la lista');
			this.simulateKeyEntry(this.getField('item'), '1234');
			this.getField('item').simulate("blur");
			this.getField('item').simulate("change");
		}
	},
	'verificarReferenciaOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('La referencia 1234 no existe');
			this.simulateKeyEntry(this.getField('item'), '010101');
			this.getField('item').simulate("blur");
			this.getField('item').simulate("change");
		}
	},
	'verificarCamposOrdenes-1': {
		'delay': 700,
		'action': function(){
			this.assertNoticeMessage('Indique el proveedor y forma de pago de la orden de compra');
			this.assertFieldValue('item_det', 'REFERENCIA 010101');
			this.assertFieldValue('unidad', 'UNIDAD 01');
			this.assertFieldValue('cantidad', '1');
			this.assertFieldValue('valor', '0.00');
			this.assertFieldValue('iva', '0 %');
			this.getButton('Agregar').simulate("click");
		}
	},
	'intentarGrabarOrdenes-1-3': {
		'delay': 500,
		'action': function(){
			this.getButton("Grabar").simulate("click");
		}
	},
	'verificarValorOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.assertErrorMessage('La referencia 010101 en la línea 1 debe estar valorizada');
			this.getElement('hyGridEdtImage').simulate("click");
		}
	},
	'verificarGrillaEditOrdenes-1': {
		'delay': 500,
		'action': function(){
			this.assertFieldValue('item', '010101');
			this.assertFieldValue('item_det', 'REFERENCIA 010101');
			this.assertFieldValue('unidad', 'UNIDAD 01');
			this.assertFieldValue('cantidad', '1');
			this.assertFieldValue('valor', '0.00');
			this.assertFieldValue('iva', '0 %');
			this.simulateKeyEntry(this.getField('cantidad'), '10');
			this.simulateKeyEntry(this.getField('valor'), '10000');
			this.getButton('Agregar').simulate("click");
			this.getProcess().getTabs().changeTabByLabel('Totales')
		}
	},
	'verificarTotalesOrdenes-1-3': {
		'delay': 500,
		'action': function(){
			this.assertFieldValue('total_neto', '10000');
			this.assertFieldValue('total', '10000');
			this.getButton("Grabar").simulate("click");
		}
	}

});

/*new Event.observe(window, "load", function(){
	window.setTimeout(function(){
		new HfosAjax.JsonRequest('testunit/prepareUnitTesting', {
			onSuccess: function(response){
				if(response!==null){
					if(response.status=='OK'){
						inveTest.runTestSuite();
					}
				};
			}
		});
	}, 1000)
});*/