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
 * AuraController
 *
 * Web Service para utilizar Aura desde otras aplicaciones
 */
class AuraController extends WebServiceController implements Transactionable {

	private $_comprobs = array();

	/**
	 * Obtiene el número de comprobante que se empezó a grabar según la fecha
	 *
	 * @param 	string $tipoComprob
	 * @param 	string $fecha
	 * @return	int
	 */
	public function getComprobByDateAction($tipoComprob, $fecha){
		$tipoComprob = $this->filter($tipoComprob, 'comprob');
		$fecha = $this->filter($fecha, 'date');
		$movi = $this->Movi->findFirst(array("comprob='$tipoComprob' AND fecha='$fecha'", 'columns' => 'numero'));
		if($movi==false){
			return 0;
		} else {
			return $movi->getNumero();
		}
	}

	/**
	 * Sincroniza en batch los datos de terceros usados para grabar un comprobante
	 *
	 * @param	array $terceros
	 * @return	int
	 */
	public function syncTercerosAction($terceros){
		foreach($terceros as $tercero){
			$nit = $this->Nits->findFirst("nit='{$tercero['Nit']}'");
			if($nit==false){
				$nit = new Nits();
				$nit->setNit($tercero['Nit']);
			}
			if(is_numeric($tercero['Tipdoc'])){
				$nit->setTipodoc(13);
			} else {
				switch($tercero['Tipdoc']){
					case 'P':
						$nit->setTipodoc(41);
						break;
					case 'E':
						$nit->setTipodoc(22);
						break;
					case 'E':
						$nit->setTipodoc(42);
						break;
					case 'N':
						$nit->setTipodoc(31);
						break;
					case 'C':
					default:
						$nit->setTipodoc(13);
						break;
				}
			}
			$nit->setNombre(utf8_decode($tercero['Nombre']));
			$nit->setClase($tercero['Clase']);
			if (isset($tercero['Telefono']) && !$tercero['Telefono']) {
				$tercero['Telefono'] = '3111111';
			}
			$nit->setTelefono($tercero['Telefono']);
			if (isset($tercero['Direccion']) && !$tercero['Direccion']) {
				$tercero['Direccion'] = 'Cll. 01 #01-01';
			}
			$nit->setDireccion($tercero['Direccion']);
			$nit->setFax($tercero['Fax']);
			if (isset($tercero['Locciu']) && !$tercero['Locciu']) {
				$tercero['Locciu'] = 127591;
			}
			$nit->setLocciu($tercero['Locciu']);
			$nit->setAutoret($tercero['Autoret']);
			if($nit->save()==false){
				foreach($nit->getMessages() as $message){
					throw new AuraException('Tercero:'.$message->getMessage().' '.print_r($tercero, true));
				}
			}
		}
		return true;
	}

	/**
	 * Graba un comprobante devolviendo el consecutivo generado
	 *
	 * @param	string $tipoComprob
	 * @param	int $numero
	 * @param	array $movements
	 * @return	int
	 */
	public function saveAction($tipoComprob, $numero, $movements, $action=null){
		$tipoComprob = $this->filter($tipoComprob, 'comprob');
		$numero = $this->filter($numero, 'int');
		set_time_limit(0);
		$aura = new Aura($tipoComprob, $numero, null, $action);
		foreach($movements as $movement){
			$movement = $aura->sanizite($movement);
			$aura->addMovement($movement);
		}
		$aura->save();
		$numero = $aura->getConsecutivo();
		$this->_comprobs[$tipoComprob][$numero] = true;
		return $numero;
	}

	/**
	 * Genera un rollback local cuando el servicio es parte de una transacción remota
	 *
	 */
	public function onRollbackAction(){
		foreach($this->_comprobs as $tipoComprob => $comprobNumero){
			foreach($comprobNumero as $numero => $one){
				$aura = new Aura($tipoComprob, $numero, null, Aura::OP_DELETE);
				$aura->delete();
			}
		}
		$this->_comprobs = array();
		return true;
	}

	/**
	 * Limpia los comprobantes grabados para evitar que sean borrados en un futuro rollback
	 *
	 * @return boolean
	 */
	public function onCommitAction(){
		$this->_comprobs = array();
		return true;
	}

	public function recalculateBalancesAction()
	{
		$auraUtils = new AuraUtils();
		$auraUtils->recalculaPeriodoActual();
	}

	public function corrigeUnaLineaAction()
	{
		$auraUtils = new AuraUtils();
		$auraUtils->corrigeUnaLinea();
	}

    /**
     * Recalcula saldosn de todo el movimiento
     */
    public function recalculateSaldosnAction()
    {
        $auraUtils = new AuraUtils();
        $auraUtils->recalculateSaldosnAll();
    }

}