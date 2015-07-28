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

class IndexController extends ApplicationController {

	public function indexAction(){
		if(SessionNamespace::exists('guestInfo')){
			Router::routeTo(array('controller' => 'accounts'));
		}
	}

	public function notFoundAction(){
		if(SessionNamespace::exists('guestInfo')){
			Flash::notice('Lo sentimos, página no encontrada');
			Router::routeTo(array('controller' => 'accounts'));
		}
	}

}

