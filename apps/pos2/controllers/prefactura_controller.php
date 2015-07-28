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

class PrefacturaController extends ApplicationController {

	public $preview = false;
	public $current_master;
	public $numero_cuenta;

	/**
	 * Muestra la prefactura
	 *
	 * @param integer $id
	 * @param boolean $preview
	 */
	public function indexAction($id = null, $preview = false){
		$this->current_master = Session::get("current_master");
		$this->numero_cuenta = Session::get("numero_cuenta");
		$this->preview = $preview;
	}

}
