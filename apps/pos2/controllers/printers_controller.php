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

class PrintersController extends StandardForm
{

	public $scaffold = true;

	public function initialize()
	{
		$this->setTemplateAfter('admin_menu');
		$this->setFormCaption('Impresoras de Producción');
		$this->setSize('ubicacion', 40);
		$this->setCaption('ubicacion', 'Ubicación');
		$this->setTitleImage('pos2/printerb.png');
		$this->setComboStatic('estado', array(
			array('A', 'ACTIVO'),
			array('I', 'INACTIVO')
		));
	}

}

