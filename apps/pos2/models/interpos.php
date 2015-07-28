<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Interpos extends ActiveRecord {

	public function initialize(){
		$config = CoreConfig::readFromActiveApplication("app.ini", 'ini');
		if(isset($config->pos->ramocol)){
			$this->setSchema($config->pos->ramocol);
		} else {
			$this->setSchema("ramocol");
		}
	}

}
