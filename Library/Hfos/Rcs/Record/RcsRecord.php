<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * RcsRecord
 *
 * Los modelos que generen revisiones automáticas deben heredar de esta
 * clase
 *
 * @category	Hfos
 * @package		Rcs
 * @subpackage 	Record
 * @access		public
 */
class RcsRecord extends ActiveRecord
{

	protected function beforeCreate()
	{
		return Rcs::beforeCreate($this);
	}

	protected function afterCreate()
	{
		return Rcs::afterCreate($this);
	}

	protected function beforeUpdate()
	{
		return Rcs::beforeUpdate($this);
	}

	protected function afterUpdate()
	{
		return Rcs::afterUpdate($this);
	}

	protected function beforeDelete()
	{
		return Rcs::beforeDelete($this);
	}

}
