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

class UpdaterController extends WebServiceController {

	private $_auth = false;

	public function startSessionAction($key=''){
		if($key==sha1('HFOS-'.date('ymd'))){
			$this->_auth = true;
		} else {
			$this->_auth = false;
		}
		return $this->_auth;
	}

	public function pullUpdateAction(){
		if($this->_auth==false){
			throw new WebServiceException('No está autenticado');
		}
		system('hg pull --update >> hg-pull.log');
		return base64_encode(file_get_contents('hg-pull.log'));
	}

}

