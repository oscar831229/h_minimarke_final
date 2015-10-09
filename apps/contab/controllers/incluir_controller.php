<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */

/**
 * IncluirController
 *
 * Incluir Movimiento
 *
 */
class IncluirController extends ApplicationController
{

	public function initialize(){
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction(){
		$this->setParamToView('message', 'Adjunte un archivo para ser incluido como parte del movimiento de este mes');
	}

	private function _getFormatDate($transaction, $fecha, $numberLine){
		if(preg_match('#([0-9]{2})/([0-9]{2})/([0-9]{4})#', $fecha, $matches)){
			return $matches[3].'-'.$matches[1].'-'.$matches[2];
		} else {
			if(trim($fecha)){
				if(!preg_match('#^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$#', $fecha)){
					throw new Exception('El formato de fecha (mm/dd/yyyy) '.$fecha.' es incorrecto en la línea '.$numberLine.' del archivo');
				}
			}
		}
		return null;
	}

	public function subirAction() {
		$this->setResponse('view');

		$archivo = $this->getFileParam('archivo');
		if($archivo==false){
			Flash::error('El archivo no se pudo subir a la contabilidad');
			return;
		}

		try {

			$transaction = TransactionManager::getUserTransaction();

			if($archivo->getFileType()!='text/plain'){
				$transaction->rollback('Tipo de archivo incorrecto');
			}

			$content = $archivo->getContentData();
			$numberLine = 1;
			$lines = array();
			foreach (explode("\n", $content) as $line) {
				if (trim($line)) {

					$fields = explode('|', $line);
					if (count($fields) != 14 && count($fields) != 15) {
						$transaction->rollback('El número de colúmnas es incorrecto en la línea '.$numberLine.' del archivo '.count($fields));
					}

					$fields[2] = $this->_getFormatDate($transaction, $fields[2], $numberLine);
					$fields[13] = $this->_getFormatDate($transaction, $fields[13], $numberLine);
					$lines[] = $fields;

				}
				$numberLine++;
				unset($line);
			}

			$numberLine = 1;

			$deleteStatus = $this->Recep->setTransaction($transaction)->deleteAll();

			foreach ($lines as $line) {
				$recep = new Recep();
				$recep->setTransaction($transaction);
				$recep->setComprob($this->filter($line[0], 'comprob'));
				$recep->setNumero($this->filter($line[1], 'int'));
				$recep->setFecha($this->filter($line[2], 'date'));
				$recep->setCuenta($this->filter($line[3], 'cuentas'));
				$recep->setNit($this->filter($line[4], 'terceros'));
				$recep->setCentroCosto($this->filter($line[5], 'int'));
				$recep->setValor($this->filter($line[6], 'double'));
				$recep->setDebCre($this->filter($line[7], 'onechar'));
				$recep->setDescripcion($this->filter($line[8], 'striptags', 'extraspaces'));
				$recep->setTipoDoc($this->filter($line[9], 'documento'));
				$recep->setNumeroDoc($this->filter($line[10], 'int'));
				$recep->setBaseGrab($this->filter($line[11], 'double'));
				$recep->setConciliado(null);
				$fVence = $line[13];
				if (empty($fVence)) {
					$fVence = $line[2];
				}
				$recep->setFVence($this->filter($fVence, 'date'));

				if ($recep->save()==false) {

					foreach ($recep->getMessages() as $message)	{
						throw new Exception($message->getMessage().' en la línea '.$numberLine.print_r($line, true));
					}
				}
				$numberLine++;
				unset($line,$recep,$fVence);
			}

			$numberLine = 1;
			$fechaAnterior = '';
			$lastKey = '';
			$receps = $this->Recep->setTransaction($transaction)->find(array('order' => 'comprob,numero,fecha'));
			foreach ($receps as $recep) {

				$key = $recep->getComprob()."-".$recep->getNumero();

				if ($lastKey!=$key) {
					if (isset($aura)) {
						$aura->save();
						Flash::success('Se grabó el comprobante '.$key);
					}
					$lastKey = $key;

					//$aura = new Aura($recep->getComprob(), $recep->getNumero(), (string) $recep->getFecha(), Aura::OP_CREATE);
					$aura = new Aura($recep->getComprob(), $recep->getNumero(), (string) $recep->getFecha());
					$aura->setActiveLine($numberLine);
				}

				$aura->addMovement(array(
					'Cuenta' => $recep->getCuenta(),
					'Nit' => $recep->getNit(),
					'Fecha' => (string) $recep->getFecha(),
					'CentroCosto' => $recep->getCentroCosto(),
					'Valor' => $recep->getValor(),
					'DebCre' => $recep->getDebCre(),
					'Descripcion' => $recep->getDescripcion(),
					'TipoDocumento' => $recep->getTipoDoc(),
					'NumeroDocumento' => $recep->getNumeroDoc(),
					'BaseGrasb' => $recep->getBaseGrab(),
					'Conciliado' => $recep->getConciliado(),
					'FechaVence' => (string) $recep->getFVence()
				));
				$numberLine++;
			}
			if(isset($aura)){
				$aura->save();
				Flash::success('Se grabó el comprobante '.$key);
			}
			$transaction->commit();
		}
		catch(Exception $e){
			Flash::error($e->getMessage());
		}

	}

}
