<?php

class TestController extends ApplicationController {

	public function indexAction(){

		ClaraMacro::setSymbolValue('primeraquincena', 'number', 1);
		ClaraMacro::setSymbolValue('empleado', 'struct', null);
		ClaraMacro::setSymbolValue('empleado.nombre', 'string', 'laura maria');
		ClaraMacro::setSymbolValue('empleado.trayectoria', 'interval', 792979200/3600);
		ClaraMacro::setSymbolValue('salario', 'number', 140000);
		ClaraMacro::setSymbolValue('empleado.sueldo', 'number', 140000);
		ClaraMacro::setSymbolValue('incapacidadesnoremuneradas', 'struct', null);
		ClaraMacro::setSymbolValue('incapacidadesnoremuneradas.total', 'number', 10000);

		$macro = 'si el nombre del empleado es "laura" entonces
			sueldo basico = 10000
		sino
			sueldo basico = 10
		fin';

		//$macro = 'sueldo básico es igual a 10000 por 10%';
		//$macro = 'sueldobasico = 10000 * 0.10 ';

		//print_r(ClaraMacro::getOpCode());
		ClaraMacro::parse($macro, 'sueldo básico');
		ClaraMacro::run();

		Flash::success(ClaraMacro::getSymbolValue('sueldobasico'));
	}

	public function upgradeAction(){

		$hoy = new Date();
		$fecha18 = Date::diffInterval($hoy, 18, Date::INTERVAL_YEAR);
		$this->Contratos->deleteAll();
		$this->Empleados->deleteAll();
		foreach($this->Maestro->find(array("order" => "f_ingreso")) as $maestro){
			$empleado = $this->Empleados->findFirst(array("cedula='{$maestro->getCedula()}'"));
			if($empleado==false){
				$empleado = new Empleados();
				$empleado->setCedula($maestro->getCedula());
			}
			$empleado->setPrimerApellido($maestro->getPrimerApellido());
			$empleado->setSegundoApellido($maestro->getSegundApellido());
			$empleado->setNombre($maestro->getNombre());
			$empleado->setDireccion($maestro->getDireccion());
			$empleado->setTelefono($maestro->getTelefono());
			$empleado->setSexo($maestro->getSexo());
			$empleado->setEstadoCivil($maestro->getECivil());
			$empleado->setFechaNace((string) $maestro->getFNace());
			if(!$maestro->getFRetiro()){
				$empleado->setEstado('A');
			} else {
				$empleado->setEstado('I');
			}
			if($empleado->save()==false){
				foreach($empleado->getMessages() as $message){
					Flash::error($message->getMessage());
				}
			} else {
				$contrato = new Contratos();
				$contrato->setEmpleadosId($empleado->getId());
				$contrato->setCargo($maestro->getCargo());
				$contrato->setCentroCosto($maestro->getCentroCosto());
				$contrato->setFondoCes($maestro->getFondoCes());
				$contrato->setFondoPension($maestro->getFondoPens());
				$contrato->setEps($maestro->getEps());
				$contrato->setUbica($maestro->getUbica());
				$contrato->setFechaIngreso((string)$maestro->getFIngreso());
				$contrato->setFechaRetiro((string)$maestro->getFRetiro());
				$contrato->setFormaPago($maestro->getFormaPago());
				$contrato->setSueldo($maestro->getSueldo());
				if($maestro->getAuxilio()>0){
					$contrato->setTransporte('S');
				} else {
					$contrato->setTransporte('N');
				}
				switch($maestro->getContrato()){
					case '1':
						$contrato->setTipoContrato('L');
						break;
					case '2':
						$contrato->setTipoContrato('F');
						break;
					case '3':
						$contrato->setTipoContrato('M');
						break;
					case '4':
						$contrato->setTipoContrato('I');
						break;
					case '0':
					default:
						$contrato->setTipoContrato('S');
						break;
				}
				if(!$maestro->getFRetiro()){
					$contrato->setEstado('A');
				} else {
					$contrato->setEstado('I');
				}
				if($contrato->save()==false){
					foreach($contrato->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}
			}
		}

		return;

		$this->ConceptosBasicos->deleteAll();
		foreach($this->Concepto->find("codigo<14") as $concepto){
			$conceptoBasico = new ConceptosBasicos();
			$conceptoBasico->setCodigo($concepto->getCodigo());
			$conceptoBasico->setNombre($concepto->getNomConcepto());
			$conceptoBasico->setVacaciones($concepto->getVacaciones());
			$conceptoBasico->setParafiscales($concepto->getAportes());
			$conceptoBasico->setPrima($concepto->getPrestacion());
			$conceptoBasico->setSalud($concepto->getBaseIss());
			$conceptoBasico->setRetencion($concepto->getRetencion());
			$conceptoBasico->setPorcRetencion($concepto->getPorcRet());
			$conceptoBasico->setCesantias($concepto->getSalario());
			$conceptoBasico->setProvision($concepto->getNetea());
			$conceptoBasico->setCuenta($concepto->getCuenta());
			$conceptoBasico->setEstado('A');
			if($conceptoBasico->save()==false){
				foreach($conceptoBasico->getMessages() as $message){
					Flash::error($message->getMessage());
				}
			}
		}
		$this->ConceptosDevengos->deleteAll();
		foreach($this->Concepto->find("codigo>=14 AND codigo<=99") as $concepto){
			$conceptoDevengo = new ConceptosDevengos();
			$conceptoDevengo->setCodigo($concepto->getCodigo());
			$conceptoDevengo->setNombre($concepto->getNomConcepto());
			$conceptoDevengo->setTipo('H');
			$conceptoDevengo->setVacaciones($concepto->getVacaciones());
			$conceptoDevengo->setParafiscales($concepto->getAportes());
			$conceptoDevengo->setPrima($concepto->getPrestacion());
			$conceptoDevengo->setSalud($concepto->getBaseIss());
			$conceptoDevengo->setRetencion($concepto->getRetencion());
			$conceptoDevengo->setPorcRetencion($concepto->getPorcRet());
			$conceptoDevengo->setCesantias($concepto->getSalario());
			$conceptoDevengo->setProvision($concepto->getNetea());
			$conceptoDevengo->setCuenta($concepto->getCuenta());
			$conceptoDevengo->setFormula('valorHora');
			$conceptoDevengo->setPorcRecargo($concepto->getRecargo());
			$conceptoDevengo->setEstado('A');
			if($conceptoDevengo->save()==false){
				foreach($conceptoDevengo->getMessages() as $message){
					Flash::error('DEV '.$concepto->getCodigo().' - '.$concepto->getNomConcepto().': '.$message->getMessage());
				}
			}
		}
		$this->ConceptosDescuentos->deleteAll();
		foreach($this->Concepto->find("codigo>99 AND codigo<300") as $concepto){
			$conceptoDescuento = new ConceptosDescuentos();
			$conceptoDescuento->setCodigo($concepto->getCodigo());
			$conceptoDescuento->setNombre($concepto->getNomConcepto());
			$conceptoDescuento->setTipo('H');
			$conceptoDescuento->setCuenta($concepto->getCuenta());
			$conceptoDescuento->setEstado('A');
			if($conceptoDescuento->save()==false){
				$conceptoDescuento->setEstado('I');
				if($conceptoDescuento->save()==false){
					foreach($conceptoDescuento->getMessages() as $message){
						Flash::error('DES '.$concepto->getCodigo().' - '.$concepto->getNomConcepto().': '.$message->getMessage());
					}
				}
			}
		}
	}

}