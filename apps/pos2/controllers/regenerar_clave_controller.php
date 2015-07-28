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

class Regenerar_ClaveController extends ApplicationController {

	public $scaffold = true;

	public function initialize(){
		$this->setTemplateAfter("admin_menu");
	}

	public function indexAction(){
		$this->loadModel('UsuariosPos');
	}

	public function cambiarAction(){
		$usuarioId = $this->getPostParam('usuarioId', 'int');
		$usuario = $this->UsuariosPos->findFirst($usuarioId);
		if($usuario!=false){
			do {
				$numeroClave = sprintf("%04s", rand(0, 9999));
				$clave = sha1($numeroClave);
		  		$usuario->clave = $clave;
		  		$exists = $this->UsuariosPos->count("clave='{$clave}' AND estado='A'");
			} while($exists);
			if($usuario->save()==false){
				foreach($usuario->getMessages() as $message){
					Flash::error($message->getMessage);
				}
		  	}
	  		Flash::success('La clave del usuario es "'.$numeroClave.'", por favor no la olvide');
		}
		return $this->routeTo('action: index');
	}
}
