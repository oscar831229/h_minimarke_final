<?php

class ForgotController extends ApplicationController
{

	public function initialize(){
		$this->setTemplateAfter('main');
	}

	public function indexAction(){

		$userInfo = SessionNamespace::get('userInfo');

		/*folio.fecsal > ('{$_SESSION['fecsis']}' - INTERVAL 3 DAY) AND
	folio.corregir = 'N' AND
	folio.walkin <> 'A' AND
	folio.numhab <> '0' AND
	folio.estado = 'O'*/
	}

}
