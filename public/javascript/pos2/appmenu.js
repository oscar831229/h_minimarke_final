
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

function openTables(){
	Clave.authForModule('tables');
}

function cashIntro(){
	Clave.authForModule('cashintro');
}

function cashOuttro(){
	Clave.authForModule('cashouttro');
}

function pay(){
	Clave.authForModule('pay');
}

function audit(){
	Clave.authForModule('audit');
}

function cancelDineIn(){
	Clave.authForModule('cancel');
}

function checkDineIn(){
	Clave.authForModule('check');
}

function dineInStatus(){
	Clave.authForModule('status');
}

function goReports(){
	Clave.authForModule('reports');
}

function closeDay(){
	Modal.confirm('¿Está seguro de hacer el cierre del día en el Sistema?', function(){
		Clave.authForModule('close');
	});
}

function revertDay(){
	Modal.confirm('¿Está seguro de devolver la fecha del sistema al día anterior?', function(){
		Clave.authForModule('revert');
	});
}

function cancelFactura(){
	Clave.authForModule('anula_factura');
}

function notaCredito(){
	Clave.authForModule('nota_credito');
}

function ReprocesarFacturaraElectronica(){
	Clave.authForModule('reprocesar_factura_electronica');
}

function ReprocesarNotaElectronica(){
	Clave.authForModule('reprocesar_nota_electronica');
}

function reimprimirFactura(){
	Clave.authForModule('reimprimir');
}

function admin(){
	new Utils.redirectToAction('admin')
}
