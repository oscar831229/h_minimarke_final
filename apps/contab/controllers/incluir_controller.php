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

	private $transaction = null;

	public function initialize()
	{
		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isAjax()){
			View::setRenderLevel(View::LEVEL_LAYOUT);
		}
		parent::initialize();
	}

	public function indexAction()
	{
		$this->setParamToView('message', 'Adjunte un archivo para ser incluido como parte del movimiento de este mes');
	}

	private function _getFormatDate($transaction, $fecha, $numberLine)
	{
		if (preg_match('#([0-9]{2})/([0-9]{2})/([0-9]{4})#', $fecha, $matches)) {
			return $matches[3] . '-' . $matches[1] . '-' . $matches[2];
		} else {
			if (trim($fecha)) {
				if (!preg_match('#^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$#', $fecha)) {
					throw new Exception('El formato de fecha (mm/dd/yyyy) ' . $fecha . ' es incorrecto en la lÃ­nea ' . $numberLine . ' del archivo');
				}
			}
		}
		return null;
	}

	public function subirAction()
	{
		$this->setResponse('view');

		try {

			$archivo = $this->getFileParam('archivo');
			if ($archivo==false) {
				Flash::error('El archivo no se pudo subir a la contabilidad');
				return;
			}

			$this->transaction = TransactionManager::getUserTransaction();

			if ($archivo->getFileType()!='text/csv') {
				$message = 'Tipo de archivo incorrecto debe ser un CSV separado por "|" ' . $archivo->getFileType();
				$this->transaction->rollback($message);
			}

			$destino = $this->getPost("destino");
			if ($destino == "M") {
				$this->importToMovi($archivo);
			} else {
				if ($destino == "N") {
					$this->importToMoviNiif($archivo);
				}
			}
		} catch (Exception $e) {
			Flash::error($e->getMessage());
		}

	}

	/**
	 * Importa el archivo a la tabla movi usando Aura
	 *
	 * @param  $_FILE $archivo
	 */
	private function importToMovi($archivo)
	{

		try {
			$numberLine = 1;
			$lines = array();
			$content = $archivo->getContentData();

			foreach (explode("\n", $content) as $line) {
				if (trim($line)) {

					$fields = explode('|', $line);
					if (count($fields) != 14 && count($fields) != 15) {
						$this->transaction->rollback('El número de colúmnas es incorrecto en la línea ' . $numberLine . ' del archivo ' . count($fields));
					}

					$fields[2]  = $this->_getFormatDate($transaction, $fields[2], $numberLine);
					$fields[13] = $this->_getFormatDate($transaction, $fields[13], $numberLine);
					$lines[]    = $fields;

				}
				$numberLine++;
				unset($line);
			}

			$numberLine = 1;
			$deleteStatus = $this->Recep->setTransaction($transaction)->deleteAll();

			foreach ($lines as $line) {
				$recep = new Recep();
				$recep->setTransaction($this->transaction);
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
						throw new Exception($message->getMessage() . ' en la lÃ­nea ' . $numberLine . print_r($line, true));
					}
				}
				$numberLine++;
				unset($line,$recep,$fVence);
			}

			$lastKey = '';
			$numberLine = 1;
			$fechaAnterior = '';

			$receps = $this->Recep->setTransaction($this->transaction)->find(array('order' => 'comprob,numero,fecha'));

			foreach ($receps as $recep) {

				$key = $recep->getComprob() . "-" . $recep->getNumero();

				if ($lastKey!=$key) {
					if (isset($aura)) {
						$aura->save();
						Flash::success('Se grabó el comprobante ' . $key);
					}
					$lastKey = $key;

					//$aura = new Aura($recep->getComprob(), $recep->getNumero(), (string) $recep->getFecha(), Aura::OP_CREATE);
					$aura = new Aura($recep->getComprob(), $recep->getNumero(), (string) $recep->getFecha());
					$aura->setActiveLine($numberLine);
				}

				$aura->addMovement(array(
					'Nit'			=> $recep->getNit(),
					'Fecha' 		=> (string) $recep->getFecha(),
					'Valor' 		=> $recep->getValor(),
					'Cuenta' 		=> $recep->getCuenta(),
					'DebCre' 		=> $recep->getDebCre(),
					'BaseGrasb' 	=> $recep->getBaseGrab(),
					'FechaVence' 	=> (string) $recep->getFVence(),
					'Conciliado' 	=> $recep->getConciliado(),
					'Descripcion' 	=> $recep->getDescripcion(),
					'CentroCosto' 	=> $recep->getCentroCosto(),
					'TipoDocumento' => $recep->getTipoDoc(),
					'NumeroDocumento' => $recep->getNumeroDoc()
				));

				$numberLine++;
			}
			if (isset($aura)) {
				$aura->save();
				Flash::success('Se grabó el comprobante ' . $key);
			}
			$this->transaction->commit();
		} catch(Exception $e) {
			Flash::error($e->getMessage());
		}
	}

	/**
	 * Importa el archivo a la tabla movi usando Aura
	 *
	 * @param  $_FILE $archivo
	 */
	private function importToMoviNiif($archivo)
	{
		try {
			$numberLine = 1;
			$lines = array();
			$content = $archivo->getContentData();

			foreach (explode("\n", $content) as $line) {
				if (trim($line)) {

					$fields = explode('|', $line);
					if (count($fields) != 14 && count($fields) != 15) {
						$this->transaction->rollback('El número de colúmnas es incorrecto en la línea ' . $numberLine . ' del archivo ' . count($fields));
					}

					$fields[2]  = $this->_getFormatDate($this->transaction, $fields[2], $numberLine);
					$fields[13] = $this->_getFormatDate($this->transaction, $fields[13], $numberLine);

					$lines[]= $fields;

				}
				$numberLine++;
				unset($line);
			}

			$numberLine = 1;

			$deleteStatus = EntityManager::get("Recepniif")->setTransaction($this->transaction)->deleteAll();

			foreach ($lines as $line) {

				$cuentas = BackCacher::getCuentaNiif($line[3]);
				if (!$cuentas) {
					throw new \Exception("La cuenta NIIF '{$line[3]}' no existe", 1);
				} else {
					if ($cuentas->getEsAuxiliar() == 'N') {
						throw new \Exception("La cuenta NIIF '{$line[3]}' no es auxiliar", 1);
					}
				}

				$recep = new Recepniif();
				$recep->setTransaction($this->transaction);
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
						throw new Exception($message->getMessage() . ' en la lÃ­nea ' . $numberLine . print_r($line, true));
					}
				}
				$numberLine++;
				unset($line,$recep,$fVence);
			}

			$lastKey = '';
			$numberLine = 1;
			$fechaAnterior = '';

			$receps = $this->Recepniif->setTransaction($this->transaction)->find(array('order' => 'comprob,numero,fecha', 'group' => 'comprob,numero'));

			foreach ($receps as $recep) {
				$this->insertMoviNiif($recep->getComprob(), $recep->getNumero());
				$numberLine++;
			}
			$this->transaction->commit();
		} catch(Exception $e) {
   			Flash::error($e->getMessage());
   		}
	}

	/**
	 * Inserta un comprobante en movi_niif
	 *
	 * @param  string $comprob
	 * @param  integer $numero
	 */
	private function insertMoviNiif($comprob, $numero)
	{
		$key = $comprob . "-" . $numero;
		$this->removeMoviNiif($comprob, $numero);

		$recepNiifs = $this->Recepniif->setTransaction($this->transaction)->find(
			"comprob='" . $comprob . "' AND numero='" . $numero . "'"
		);

		foreach ($recepNiifs as $recepNiif) {
			$moviNiif = new MoviNiif();
			foreach ($recepNiif->getAttributes() as $field) {
				$moviNiif->writeAttribute($field, $recepNiif->readAttribute($field));
			}
			$moviNiif->save();
		}
		Flash::success('Se grabó el comprobante ' . $key);
	}

	/**
	 * Borra un comprobante en movi_niif si existe
	 *
	 * @param  string $comprob
	 * @param  integer $numero
	 */
	private function removeMoviNiif($comprob, $numero)
	{
		$moviNiifs = $this->MoviNiif->setTransaction($this->transaction)->find(
			"comprob='" . $comprob . "' AND numero='" . $numero . "'"
		);

		if (count($moviNiifs)) {
			throw new Exception("Actualmente existe el comprobante '$comprob-$numero'");
		}

		foreach ($moviNiifs as $moviNiif) {
			$moviNiif->delete();
		}
	}
}
