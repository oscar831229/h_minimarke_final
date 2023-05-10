<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

class Resolucion_FacturaController extends StandardForm
{

	public $scaffold = true;

	public function initialize(){

		$this->setTemplateAfter('admin_menu');
		$this->setFormCaption('Resoluciones facturación');
		$this->setCaption('salon_id', 'Ambiente de facturación');
		$this->setCaption('tipo_factura', 'Tipo de factura');
		$this->setCaption('autorizacion', 'Resolución Facturación');
		$this->setCaption('consecutivo_inicial', 'Consecutivo inicial facturación');
		$this->setCaption('consecutivo_final', 'Consecutivo final facturación');

		$this->setCaption('prefijo_facturacion', 'Prefijo Facturación');
		$this->setCaption('fecha_autorizacion', 'Fecha Autorización');
		$this->setCaption('fecha_fin_autorizacion', 'Resolución hasta');
		$this->setCaption('consecutivo_facturacion', 'Consecutivo Facturación actual');

		$this->setCaption('prefi_nota_credi', 'Prefijo nota credito');	
		$this->setCaption('fecha_ini_nota_credi', 'Fecha inicial Nota credito');
		$this->setCaption('fecha_fin_nota_credi', 'Fecha final Nota credito');
		$this->setCaption('consec_inici_nota_credi', 'Consecutivo inicial nota credito');
		$this->setCaption('consec_final_nota_credi', 'Consecutivo final nota credito');


		$this->setComboStatic('tipo_factura', array(
			array('P', 'FACTURACION POS'),
			array('E', 'FACTURACION ELECTRÓNICA')
		));

		$this->setComboStatic('estado', array(
			array('A', 'ACTIVO'),
			array('I', 'INACTIVO')
		));


	}

}
