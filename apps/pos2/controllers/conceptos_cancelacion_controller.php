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

class Conceptos_CancelacionController extends StandardForm
{

	public $scaffold = true;

	public function initialize()
	{
		$this->setTemplateAfter('admin_menu');
		$this->setFormCaption('Conceptos de Cancelación');
		$this->setSize('nombre', 30);
	}
}
