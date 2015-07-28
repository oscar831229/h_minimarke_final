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

class ModifiersController extends StandardForm {

	public $scaffold = true;

	public function initialize(){

		$this->setTemplateAfter("admin_menu");
		$this->setFormCaption('Modificadores de Items');
		$this->setTitleImage('pos2/french.png');

		$this->setComboStatic('tipo', array(
			array('W', 'CON'),
			array('S', 'SIN'),
			array('P', 'PORCIÓN'),
			array('A', 'ADICIONAL'),
			array('U', 'DE USUARIO')
		));

		$this->setComboStatic('tipo_costo', array(
			array('N', 'NO DESCARGA'),
			array('I', 'INVENTARIO/REFERENCIA'),
			array('R', 'RECETA ESTÁNDAR')
		));

		$this->setComboStatic('descontar', array(
			array('N', 'NORMAL'),
			array('T', 'TRAGO')
		));

		$this->setHidden('nombre_pedido');

		$this->setHelpContext('field: codigo_referencia');

		$this->setComboStatic('estado', array(
			array('A', 'ACTIVO'),
			array('I', 'INACTIVO')
		));
	}

}
