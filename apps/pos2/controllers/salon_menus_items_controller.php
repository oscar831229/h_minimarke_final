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

class Salon_menus_itemsController extends StandardForm {

	public $scaffold = true;

	public function beforeInsert(){

		$menus_items_id = $this->getRequestParam('fl_menus_items_id', 'int');
		$salon_id = $this->getRequestParam('fl_salon_id', 'int');

		$existe = $this->SalonMenusItems->count("menus_items_id = '$menus_items_id' AND salon_id = '$salon_id'");
		if($existe){
			Flash::error('Este item ya está relacionado en el ambiente');
			return false;
		}
	}

	public function initialize(){

		$this->setTemplateAfter('admin_menu');
		$this->setFormCaption('Items en Ambientes');
		$this->setTitleImage('pos2/food.png');

		$this->setCaption('salon_id', 'Ambiente');
		$this->setCaption('menus_items_id', 'Item');
		$this->setCaption('conceptos_id', 'Concepto Grabación Recepción');
		$this->setCaption('valor', 'Precio Venta');
		$this->setCaption('printers_id', 'Impresora Producción');
		$this->setCaption('printers_id2', 'Impresora Confirmación');
		$this->setCaption('screens_id', 'Pantalla Producción');

		$this->setComboStatic('estado', array(
			array('A', 'ACTIVO'),
			array('I', 'INACTIVO')
		));

		$this->setComboStatic('descarga', array(
			array('S', 'SI'),
			array('N', 'NO')
		));

		$this->setComboDynamic('field: printers_id2', 'detail_field: nombre',
		'relation: printers', 'column_relation: id');

	}

}
