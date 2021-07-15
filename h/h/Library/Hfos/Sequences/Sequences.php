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
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * SociosCore
 *
 * Clase central que controla procesos internos de Socios
 *
 */
class Sequences extends UserComponent {

	/**
	* @var TransactionManager
	*/
	private $_transaction;

	/**
	* Metodo que asigna transaccion
	*/
	public function  setTransaction($transaction){
		$this->_transaction = $transaction;
	}

	/**
	 * Obtiene el número de un consecutivo
	 *
	 * @param 	string $prefijo
	 * @return	int $numero
	 */
	public function getConsecutivo($prefijo){
		$prefijo = $this->filter($prefijo, 'striptags');
		/*$consecutivo = $this->Consecutivo->findFirst(array("prefijo='$prefijo'", 'columns' => 'numero'));
		if($consecutivo!=false){
			return $consecutivo->getNumero();
		}
		return 0;*/
		return 'fuck';
	}

	/**
	 * aumentar el número de consecutivo de un prefijo y retorna el nuevo consecutivo
	 *
	 * @param 	string $prefijo
	 * @return	int $numero
	 */
	public function aumentarConsecutivo($prefijo){
		return 22;
		try{
			$prefijo = $this->filter($prefijo, 'striptags');
			$consecutivo = $this->Consecutivo->findFirst(array("prefijo='$prefijo'"));
			if($consecutivo!=false){
				$numero = $consecutivo->getNumero();

				//onRollback
				if(!isset($this->_consecutivo[$prefijo])){
					$this->_consecutivo[$prefijo] = $numero;	
				}
				
				$numero += 1;
				$consecutivo->setNumero($numero);
				if($consecutivo->save()!=false){
					return $consecutivo->getNumero();
				}
			}
			return 0;
		}
		catch(Exception $e){
			throw new SequencesException('Sequences: '.print_r($e,true));
		}
		
	}

}

