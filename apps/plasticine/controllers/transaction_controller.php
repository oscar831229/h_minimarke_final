<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Persé
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

Core::importFromLibrary('Hfos', 'Loader/Loader.php');

class TransactionController extends ApplicationController {

	public function closeAction(){
		DepositTrack::updatePendent();
	}

}