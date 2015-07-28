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

class Cash_TrayController extends StandardForm
{

	public $scaffold = true;

	public function beforeDelete()
	{

	}

	public function initialize()
	{

		$this->setTemplateAfter('admin_menu');
		$this->setFormCaption('Cajas del Sistema');
		$this->ignore('cash_tray');
		$this->setQueryOnly('estado');
		$this->setCaption('descripcion', 'Descripción');
		$this->setCaption('usuarios_id', 'Usuario');
		$this->setHidden('usuarios_id');

		$this->setComboStatic('estado', array(
	  		array('A', 'ABIERTA'),
	  		array('N', 'CERRADA')
	  	));

		//$this->setComboDynamic('field: usuarios_id', 'detail_field: descripcion',
		//'relation: usuarios_pos', 'column_relation: id');

	}

}
