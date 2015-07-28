<?php

class TransferenciaProvController extends ApplicationController {

	public function indexAction(){
		GarbageCollector::freeAllMetaData();
		$this->setTemplateAfter(array('main', 'menu'));
		/*Tag::displayTo('cuenta', 111);
		Tag::displayTo('detalleCuenta', 'CUENTA');
		Tag::displayTo('nit2', '57025256');
		Tag::displayTo('valor2', 100);
		Tag::displayTo('valor', 0);*/
	}

	public function queryByNitAction(){
		$this->setResponse('json');
		$nitNumero = $this->getPostParam('nit', 'alpha');
		$nit = $this->Nits->findFirst("nit='$nitNumero'");
		if($nit!=false){
			return array(
				'status' => 'OK',
				'nombre' => $nit->getNombre()
			);
		} else {
			return array(
				'status' => 'NF',
				'nombre' => ''
			);
		}
	}

	public function queryByCuentaAction(){
		$this->setResponse('json');
		$cuentaNumero = $this->getPostParam('cuenta', 'cuentas');
		$cuenta = $this->Cuentas->findFirst("cuenta='$cuentaNumero'");
		if($cuenta!=false){
			return array(
				'status' => 'OK',
				'nombre' => $cuenta->getNombre()
			);
		} else {
			return array(
				'status' => 'NF',
				'nombre' => ''
			);
		}
	}

	public function queryTercerosAction(){
		$this->setResponse('view');
		echo '<ul>';
		$nombre = $this->getPostParam('nombre');
		foreach($this->Nits->find("nombre LIKE '%$nombre%'") as $nit){
			echo '<li id="', $nit->getNit(), '">', utf8_decode($nit->getNombre()), '</li>';
		}
		echo '</ul>';
	}

	public function queryCuentasAction(){
		$this->setResponse('view');
		echo '<ul>';
		$nombre = $this->getPostParam('detalleCuenta');
		foreach($this->Cuentas->find("nombre LIKE '%$nombre%' AND es_auxiliar='S'") as $cuenta){
			echo '<li id="', $cuenta->getCuenta(), '">', $cuenta->getNombre(), '</li>';
		}
		echo '</ul>';
	}

	public function nextChequeAction(){
		$this->setResponse('json');
		$chequeraId = $this->getPostParam('chequeraId', 'int');
		$chequera = $this->Chequeras->findFirst($chequeraId);
		if($chequera==false){
			return array(
				'status' => 'NF',
				'numero' => ''
			);
		} else {
			$numeroCheque = 0;
			for($i=$chequera->getNumeroInicial();$i<$chequera->getNumeroFinal();++$i){
				if($this->Cheque->count("chequeras_id='$chequeraId' AND estado NOT IN ('E', 'I')")==0){
					$numeroCheque = $i;
					break;
				}
			}
			return array(
				'status' => 'OK',
				'numero' => $numeroCheque
			);
		}
	}

	public function generarAction(){
		$rules = array(
			'nit' => array(
				'message' => 'Debe indicar el tercero'
			),
			'chequeraId' => array(
				'message' => 'Debe indicar la chequera',
				'nullValue' => '@'
			)
		);
		if($this->validateRequired($rules)==false){
			foreach($this->getValidationMessages() as $message){
				Flash::error($message->getMessage());
			}
		} else {
			try {
				$transaction = TransactionManager::getUserTransaction();
				$nitNumero = $this->getPostParam('nit', 'alpha');
				$nit = $this->Nits->findFirst("nit='$nitNumero'");
				if($nit==false){
					$nombre = $this->getPostParam('nombre');
					if($nombre==""){
						Flash::error("El tercero no existe, Debe indicar el nombre del tercero");
						$transaction->rollback();
					}
					$nit = new Nits();
					$nit->setTransaction($transaction);
					$nit->setNit($nitNumero);
					$nit->setClase('A');
					$nit->setNombre($nombre);
					$nit->setDireccion('');
					$nit->setTelefono('');
					$nit->setCiudad('');
					$nit->setAutoret('N');
					$nit->setEstadoNit('A');
					if($nit->save()==false){
						foreach($nit->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				}

				$valor = $this->getPostParam('valor', 'float');
				if($valor<=0){
					Flash::error("El valor del cheque es inválido");
					$transaction->rollback();
				}

				$numeroCheque = $this->getPostParam('numeroCheque', 'int');
				if($numeroCheque<=0){
					Flash::error('El número de cheque no es válido');
					$transaction->rollback();
				}

				$chequeraId = $this->getPostParam('chequeraId', 'int');
				$chequera = $this->Chequeras->findFirst($chequeraId);
				if($chequera==false){
					Flash::error('La chequera es inválida');
					$transaction->rollback();
				}

				//$this->Cheque->setDebug(true);
				$existe = $this->Cheque->count("chequeras_id='$chequeraId' AND numero_cheque='$numeroCheque' AND estado='E'");
				if($existe){
					Flash::error('El cheque '.$numeroCheque.' ya fué emitido en la chequera');
					$transaction->rollback();
				}

				$cuentaBanco = $chequera->getCuentasBancos();

				$this->Comprob->setTransaction($transaction);
				$comprob = $this->Comprob->findFirst("codigo='{$cuentaBanco->getComprob()}'");
				$comprob->setConsecutivo($comprob->getConsecutivo()+1);

				$fechaHoy = Date::getCurrentDate();

				$fCuentas = $this->getPostParam('f_cuenta');
				$fCentroCostos = $this->getPostParam('f_centroCosto');
				$fValor2 = $this->getPostParam('f_valor2');
				$fNaturaleza = $this->getPostParam('f_naturaleza');
				$fNit = $this->getPostParam('f_nit2');
				$fDescripcion = $this->getPostParam('f_descripcion');
				$numeroContras = count($fCuentas);
				for($i=0;$i<$numeroContras;$i++){
					$movi = new Movi();
					$movi->setTransaction($transaction);
					$movi->setComprob($comprob->getCodigo());
					$movi->setNumero($comprob->getConsecutivo());
					$movi->setFecha($fechaHoy);
					$movi->setCuenta($fCuentas[$i]);
					$movi->setNit($fNit[$i]);
					$movi->setCentroCosto($fCentroCostos[$i]);
					$movi->setDescripcion($fDescripcion[$i]);
					$movi->setValor($fValor2[$i]);
					$movi->setDebCre($fNaturaleza[$i]);
					if($movi->save()==false){
						foreach($movi->getMessages() as $message){
							Flash::error($message->getMessage());
						}
						$transaction->rollback();
					}
				}

				$movi = new Movi();
				$movi->setTransaction($transaction);
				$movi->setComprob($comprob->getCodigo());
				$movi->setNumero($comprob->getConsecutivo());
				$movi->setFecha($fechaHoy);
				$movi->setCuenta($cuentaBanco->getCuenta());
				$movi->setCentroCosto($cuentaBanco->getCentroCosto());
				$movi->setNit($nitNumero);
				$movi->setDescripcion('CHEQUE# '.$numeroCheque);
				$movi->setDebCre('D');
				$movi->setValor($valor);
				if($movi->save()==false){
					foreach($movi->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}

				//print_r($_POST);
				$fecha = $this->getPostParam('fecha', 'date');
				$beneficiario = $this->getPostParam('beneficiario');
				$cheque = new Cheque();
				$cheque->setTransaction($transaction);
				$cheque->setChequerasId($chequeraId);
				$cheque->setComprob($comprob->getCodigo());
				$cheque->setNumero($comprob->getConsecutivo());
				$cheque->setNit($nitNumero);
				$cheque->setNumeroCheque($numeroCheque);
				$cheque->setFecha($fechaHoy);
				$cheque->setHora(Date::getCurrentTime());
				$cheque->setFechaCheque($fecha);
				$cheque->setValor($valor);
				$cheque->setBeneficiario($beneficiario);
				$cheque->setEstado('E');
				if($cheque->save()==false){
					foreach($cheque->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
				if($comprob->save()==false){
					foreach($comprob->getMessages() as $message){
						Flash::error($message->getMessage());
					}
					$transaction->rollback();
				}
				Flash::success('Se creó el egreso '.$comprob->getCodigo().'-'.$comprob->getConsecutivo());
				$transaction->commit();
				return $this->routeTo('action: mostrar');
			}
			catch(TransactionFailed $e){

			}
		}
		$this->routeTo('action: index');
	}

	public function mostrarAction(){

	}

}