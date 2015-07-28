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

class ConsoleController  extends ApplicationController {

	public function indexAction(){
		$this->setResponse('view');
	}

	public function executeAction(){

		$this->setResponse('view');

		$command = $this->getPostParam('command', 'extraspaces');
		$command = stripslashes($command);

		$console = new HfosConsole();
		$console->executeCommand($command);
	}

}