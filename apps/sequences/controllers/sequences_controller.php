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
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */
Core::importFromLibrary('Hfos/Sequences','Sequences.php');

/**
 * SequencesController
 *
 * Web Service para utilizar Consecutivos desde otras aplicaciones
 */
class SequencesController extends WebServiceController implements Transactionable {

	private $_consecutivo = array();

	/**
	 * Obtiene el número de un consecutivo
	 *
	 * @param 	string $prefijo
	 * @return	int $numero
	 */
	public function getConsecutivoAction($prefijo){
		//$sequences = new Sequences();
		//return $sequences->getConsecutivo($prefijo);
		return 'fuck';
	}

	/**
	 * aumentar el número de consecutivo de un prefijo y retorna el nuevo consecutivo
	 *
	 * @param 	string $prefijo
	 * @return	int $numero
	 */
	public function getAumentarConsecutivoAction($prefijo){
		//$sequences = new Sequences();
		//return $sequences->aumentarConsecutivo($prefijo);
		return 22;
	}

	/**
	 * Genera un rollback local cuando el servicio es parte de una transacción remota
	 *
	 */
	public function onRollbackAction(){
		foreach($this->_consecutivo as $prefijo => $numero){
			$consecutivo = $this->Consecutivo->findFirst(array("prefijo='$prefijo'"));
			if($consecutivo!=false){
				$consecutivo->setNumero($numero);
				$consecutivo->save();
			}
		}
		$this->_consecutivo = array();
		return true;
	}

	/**
	 * Limpia los consecutivos grabados para evitar que sean borrados en un futuro rollback
	 *
	 * @return boolean
	 */
	public function onCommitAction(){
		$this->_consecutivo = array();
		return true;
	}

}