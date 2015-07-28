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

Core::importFromLibrary('Hfos/Invoicing','InvoicingException.php');

/**
 * AuraController
 *
 * Web Service para utilizar Invoicing desde otras aplicaciones
 */
class InvoicingController extends WebServiceController implements Transactionable {


	/**
	* instancia de adapter de invoicer
	* @var Object
	*/
	private $_invoicingApp = '';

	/**
	* Facturas realizadas durante la transacción
	* @var array
	*/
	private $_facturas = array();

	/**
	 * Graba una factura en invoicer devolviendo el consecutivo generado
	 *
	 * @param	array $options
	 * @return	int
	 */
	public function saveAction($options){

		if(!isset($options['apps']) || empty($options['apps'])){
			throw new InvoicingException('Es necesario ingresar index de apps en array');
		}
		$appsCode = $this->filter($options['apps'], 'alpha');

		$this->_invoicingApp = Invoicing::factory($appsCode);
		$this->_invoicingApp->addInvoicer($options);
		$facturaId 	= $options['facturasId'];
		if(!$facturaId){
			throw new InvoicingException('No se puedo generar la factura.'.print_r($facturaId, true));
		}
		$this->_facturas[$facturaId] = true;

		return $options;
	}

	/**
	 * Genera un rollback local cuando el servicio es parte de una transacción remota
	 *
	 */
	public function onRollbackAction(){
		/*if(is_array($this->_facturas) && count($this->_facturas)){
			foreach($this->_facturas as $facturaId => $flag){
				$this->_invoicingApp->disableInvoicer($facturaId);
			}
			$this->_facturas = array();
		}*/
		return true;
	}

	/**
	 * Limpia las facturas grabadas para evitar que sean borrados en un futuro por el rollback
	 *
	 * @return boolean
	 */
	public function onCommitAction(){
		$this->_facturas = array();
		return true;
	}

	/**
	 * Anula una factura en invoicer devolviendo el consecutivo actual
	 *
	 * @param	array $options
	 * @return	boolean
	 */
	public function anulaAction($options){
		if(!isset($options['apps']) || empty($options['apps'])){
			throw new InvoicingException('Es necesario ingresar index de apps en array');
		}
		$appsCode = $this->filter($options['apps'], 'alpha');

		$this->_invoicingApp = Invoicing::factory($appsCode);

		$this->_invoicingApp->disableInvoicer($options);

		return true;
	}

	/**
	 * Imprime una factura en invoicer devolviendo el nombre de archivo generado
	 *
	 * @param	array $options
	 * @return	boolean
	 */
	public function printAction($options){
		Core::importFromLibrary('Hfos/Invoicing','InvoicingReport.php');

		if(!isset($options['apps']) || empty($options['apps'])){
			throw new InvoicingException('Es necesario ingresar index de apps en array');
		}
		$appsCode = $this->filter($options['apps'], 'alpha');

		$invoicing = InvoicingReport::factory($options['apps']);
		return $invoicing->getPrint($options);
	}

	/**
	 * Metodo que obtiene el consecutivo actual de facturación (FC) 
	 * 
	*/
	public function getConsecutivoAction($options) {
		if(!isset($options['apps']) || empty($options['apps'])){
			throw new InvoicingException('Es necesario ingresar index de apps en array');
		}
		$appsCode = $this->filter($options['apps'], 'alpha');

		$this->_invoicingApp = Invoicing::factory($appsCode);
		$consecutivo = $this->_invoicingApp->getConsecutivo($options);
		if(!$consecutivo){
			throw new InvoicingException('No se puedo obtener el consecutivo de las facturas.');
		}

		return $consecutivo;
	}

	/**
	 * Metodo que aumenta el consecutivo actual de facturación (FC) 
	 * 
	*/
	public function aumentarConsecutivo($options) {
		if(!isset($options['apps']) || empty($options['apps'])){
			throw new InvoicingException('Es necesario ingresar index de apps en array');
		}
		$appsCode = $this->filter($options['apps'], 'alpha');

		$this->_invoicingApp = Invoicing::factory($appsCode);
		$consecutivo = $this->_invoicingApp->getConsecutivo($options);
		if(!$consecutivo){
			throw new InvoicingException('No se puedo obtener el consecutivo de las facturas.');
		}
		$nuevoConsecutivo = $consecutivo + 1;
		$options['nuevoConsecutivo'] = $nuevoConsecutivo;
		$this->_invoicingApp->setConsecutivo($options);
		return $consecutivo;
	}

	/**
	 * Imprime un estado de cuenta en invoicer
	 *
	 * @param	array $options
	 * @return	boolean
	 */
	public function estadoCuentaAction($options){
		Core::importFromLibrary('Hfos/Invoicing','InvoicingReport.php');

		if(!isset($options['apps']) || empty($options['apps'])){
			throw new InvoicingException('Es necesario ingresar index de apps en array');
		}
		$appsCode = $this->filter($options['apps'], 'alpha');

		$invoicing = InvoicingReport::factory($options['apps']);
		return $invoicing->getEstadoCuenta($options);
	}
}
