<?php

class MigraController extends ApplicationController {

	public function xAction(){
		$this->MenusItems->findFirst(64);
		print $this->MenusItems->nombre;
		#$this->MenusItems->nombre = "ACOMPAÑAMIENTOS";
		#$this->MenusItems->save();
	}

}