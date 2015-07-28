<?php

class RackController extends ApplicationController {

	public function initialize(){
		$this->setTemplateAfter('main');
	}

	public function indexAction(){
		$this->setParamToView('habitaciones', $this->Habitacion->find(array("tipo='V' AND estado='A'", "order" => "CONCAT(SPACE(6-LENGTH(numhab)), numhab)")));
	}

	public function detailsAction($number){

		$number = $this->filter($number, 'alpha');
		if(!$number){
			return $this->routeToAction('index');
		}

		$userInfo = SessionNamespace::get('userInfo');

		$habitacion = $this->Habitacion->findFirst("numhab='$number' AND tipo='V' AND estado='A'");
		if($habitacion==false){
			return $this->routeToAction('index');
		}

		if($habitacion->getCodest()==3||$habitacion->getCodest()==4){

			$ocupantes = array();

			$folio = $this->Folio->findFirst("numhab='{$habitacion->getNumhab()}' AND estado='I' AND corregir='N'");
			if($folio==false){
				return $this->routeToAction('index');
			}

			$ocupantes[] = $folio->getClientes()->getNombre();
			foreach($this->Apofol->find("numfol='{$folio->getNumfol()}' AND estado='I'") as $apofol){
				$ocupantes[] = $apofol->getNombre();
			}

			$this->setParamToView('ocupantes', $ocupantes);
			$this->setParamToView('folio', $folio);

		}

		$this->setParamToView('habitacion', $habitacion);

	}

}
