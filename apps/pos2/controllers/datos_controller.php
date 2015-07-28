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

class DatosController extends StandardForm
{

	public $scaffold = true;

	public function initialize()
	{
		$this->setTemplateAfter('admin_menu');
		$this->setFormCaption('Datos del Sistema');
		$this->setSize('direccion', 40);
		$this->setSize('telefonos', 40);
		$this->setSize('fax', 20);
		$this->setSize('po_box', 20);
		$this->setTypeTextarea('nota_contribuyentes');
		$this->setQueryOnly('fecha');
		$this->setQueryOnly('version');
		$this->setCaption('direccion', 'Direcci&oacute;n');
		$this->setCaption('print_server', 'Servidor de Impresi&oacute;n');
		$this->setTextUpper(
			'nombre_hotel',
			'nombre_cadena',
			'direccion',
			'ciudad',
			'pais',
			'moneda',
			'centavos'
		);
		$this->notBrowse(
			'ciudad',
			'pais',
			'nombre_cadena',
			'po_box',
			'fax',
			'direccion',
			'telefonos',
			'documento',
			'entidad',
			'moneda',
			'centavos',
			'nota_contribuyentes'
		);
		$this->setComboStatic('print_type', array(
			array('C', 'CLIENTE'),
			array('S', 'SERVIDOR'),
			array('J', 'COLA LOCAL')
		));
	}

}
