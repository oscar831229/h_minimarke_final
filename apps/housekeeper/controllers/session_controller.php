<?php

class SessionController extends ApplicationController {

	public function indexAction(){

	}

	public function startAction(){

		$login = $this->getPostParam('login', 'alpha');

		if($login){

			$password = sha1($this->getPostParam('password'));

			$usuario = $this->Usuarios->findFirst("login='$login' AND pass='$password' AND estado='A'");
			if($usuario==false){
				Tag::displayTo('password', '');
				$this->setParamToView('message', 'Clave incorrecta');
				return $this->routeTo(array('controller' => 'index'));
			}

			$camarera = $this->Camarera->findFirst("codusu={$usuario->getCodusu()} AND estado='A'");
			if($camarera==false){
				Tag::displayTo('password', '');
				$this->setParamToView('message', 'No hay camareras asignadas al usuario');
				return $this->routeTo(array('controller' => 'index'));
			}

			$dathot = $this->Dathot->findFirst();

			$user = SessionNamespace::add('userInfo');
			$user->setCodusu($usuario->getCodusu());
			$user->setCodcam($camarera->getCodcam());
			$user->setFecha($dathot->getFecha());

			return $this->redirect('status');
		}

		Tag::displayTo('password', '');
		return $this->routeTo(array('controller' => 'index'));

	}

	public function endAction(){
		SessionNamespace::drop('userInfo');
		return $this->redirect('index');
	}

}