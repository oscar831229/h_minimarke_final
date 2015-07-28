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
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class AccountModifiers extends ActiveRecord {

	public function initialize(){
		$this->belongsTo('Account');
		$this->belongsTo('Modifiers');
	}

}
