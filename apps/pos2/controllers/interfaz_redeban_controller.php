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

class Interfaz_RedebanController extends StandardForm
{

	public $scaffold = true;

	public function initialize()
	{

		$this->setTemplateAfter('admin_menu');
		$this->setTitleImage('pos2/pay.png');
		$this->setCaption('formas_pago_id', 'Forma de pago');
		$this->setCaption('operacion', 'Operación redeban');

		$this->setComboStatic('operacion', array(
			array(0, 'COMPRA'),
			array(4, 'REDENCIÓN BONO')
		));
	}
	
}
