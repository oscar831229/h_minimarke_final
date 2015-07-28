<?php

/**
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 * @access public
 **/
class ControllerBase {

	public function init(){
		Router::routeTo(array('controller' => 'index'));
	}

	public function beforeFilter(){
		if(!SessionNamespace::exists('userInfo')){
			$acl = array(
				'status' => 1,
				'forgot' => 1,
				'rack' => 1
			);
			if(isset($acl[Router::getController()])){
				Router::routeTo(array('controller' => 'index'));
				return false;
			}
		} else {
			return true;
		}
	}

}

