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

class SocorroController extends ApplicationController {

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){

	}

	public function crashAction(){

	}

	public function wasCrashAction(){

	}

	public function sendReportAction(){
		$this->setResponse('json');

		$report = $this->getPostParam('report');

		$report = str_replace(" ", "+", $report);
		$report = base64_decode($report, false);

		$report = str_replace("\\n", "", $report);
		$report = str_replace("\\t", "", $report);
		$report = stripslashes($report);

		unset($_POST['report']);
		unset($_REQUEST['report']);

		Core::importFromLibrary('Hfos', 'Socorro/Socorro.php');
		Socorro::sendReportFromClient('Back-Office Exception', $report);

		return array(
			'status' => 'OK'
		);
	}

}