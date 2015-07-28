<?php

class UpgradeController extends ApplicationController {

	public function to60Action(){
		/*foreach($this->Nits->find() as $nit){
			$nit->setNombre(str_replace('M-q', 'Ñ', $nit->getNombre()));
			if($nit->save()==false){

			}
		}*/
		$upgrade = new Upgrade();
		$upgrade->checkVersion();
	}

}