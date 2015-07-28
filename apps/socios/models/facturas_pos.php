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

class FacturaPos extends ActiveRecord {

	public function initialize()
	{
		$config = CoreConfig::readFromActiveApplication('config.ini', 'ini');
		if(isset($config->hfos->pos_db)){
			$this->setSchema($config->hfos->pos_db);
		} else {
			$this->setSchema('pos');
		}
		$this->setSource('factura');
	}

}
