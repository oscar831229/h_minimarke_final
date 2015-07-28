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

class LoginController extends ApplicationController {

	public function indexAction(){
		$this->setResponse('view');
		$this->setParamToView('aplicaciones', $this->Aplicaciones->find("tipo='B'"));

		$appCode = $this->getPostParam('appCode', 'alpha');
		$this->setParamToView('appCode', $appCode);
	}

}
