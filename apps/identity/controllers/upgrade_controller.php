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
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class UpgradeController extends ApplicationController {

	public function indexAction(){
		$this->setResponse('view');

		$appCode = $this->getPostParam('appCode', 'alpha');
		$this->setParamToView('appCode', $appCode);
	}

	public function doUpgradeAction(){
		//sleep(10);
		//return;
		try {
			$transaction = TransactionManager::getUserTransaction();
			$this->setResponse('json');
			$upgrade = new Upgrade();
			$upgrade->checkVersion();
			sleep(10);
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		return array(
			'status' => 'OK'
		);
	}

	public function dumpDBAction(){
		Upgrade::dumpDB();
	}

}