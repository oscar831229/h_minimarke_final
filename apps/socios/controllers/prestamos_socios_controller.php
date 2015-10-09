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

/**
 * Prestamos_SociosController
 *
 * Controlador de los prestamos de los socios
 *
 */
class Prestamos_SociosController extends HyperFormController {

	static protected $_config = array(
		'model'		=> 'PrestamosSocios',
		'plural'	=> 'Convenios de Socios',
		'single'	=> 'Convenio de Socio',
		'genre'		=> 'M',
		'tabName'	=> 'Convenio',
		'preferedOrder' => 'id DESC',
		'icon'		=> 'cartera.png',
		'fields'	=> array(
			'id' => array(
				'single'	=> 'Código',
				'type'		=> 'int',
				'size'		=> 3,
				'maxlength'	=> 10,
				'primary'	=> true,
				'filters'	=> array('int')
			),
			'socios_id' => array(
				'single' => 'Socio',
				'type' => 'Socio',
				'filters' => array('alpha')
			),
			'cuenta' => array(
				'single' => 'Cuenta',
				'type' => 'Cuenta',
				'filters' => array('int'),
				'notBrowse'	=> true,
			),			
			'cuenta_cruce' => array(
				'single' => 'Cuenta Cruce',
				'type' => 'Cuenta',
				'filters' => array('int'),
				'notBrowse'	=> true,
			),			
			'fecha_prestamo' => array(
				'single'	=> 'Fecha de Prestamo',
				'type'		=> 'date',
				'default'	=> '',
				'useDummy'	=> true,
				'readOnly'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('date')
			),
			'fecha_inicio' => array(
				'single'	=> 'Fecha de inicio financiación',
				'type'		=> 'date',
				'default'	=> '',
				'useDummy'	=> true,
				'notBrowse'	=> true,
				'filters'	=> array('date')
			),
			'valor_financiacion' => array(
				'single'	=> 'Valor financiación',
				'type'		=> 'decimal',
				'size'		=> 10,
				'maxlength'	=> 10,
				'notSearch'	=> true,
				'filters'	=> array('double')
			),
			'numero_cuotas' => array(
				'single'	=> 'Número de cuotas',
				'type'		=> 'int',
				'size'		=> 3,
				'maxlength'	=> 3,
				'filters'	=> array('int')
			),
			'interes_corriente' => array(
				'single'	=> 'Interes Corriente',
				'type'		=> 'decimal',
				'size'		=> 5,
				'maxlength'	=> 6,
				'notBrowse'	=> true,
				'notSearch'	=> true,
				'filters'	=> array('double')
			),
			'estado' => array(
				'single'	=> 'Estado',
				'type'		=> 'closed-domain',
				'size'		=> 1,
				'notNull'	=> true,
				'maxlength'	=> 1,
				'readOnly'	=> true,
				'values'	=> array(
					'D' => 'Debe',
					'P' => 'Pagado'
				),
				'filters'	=> array('onechar')
			),
			'comprob' => array(
				'single'	=> 'Comprobante',
				'type'		=> 'text',
				'size'		=> 3,
				'maxlength'	=> 3,
				'readOnly'	=> true,
				'notBrowse'	=> true,
				'notSearch'	=> true,
				'filters'	=> array('alpha')
			),
			'numero' => array(
				'single'	=> 'Número Comprobante',
				'type'		=> 'int',
				'size'		=> 3,
				'maxlength'	=> 3,
				'readOnly'	=> true,
				'notBrowse'	=> true,
				'notSearch'	=> true,
				'filters'	=> array('int')
			)
		),
		'extras' => array(
			0 => array(
				'partial' => 'amortizacion',
				'tabName' => 'Amortización'
			)
		)
	);

	/**
	 * Metodo que se ejecuta cuando ya se creo el registro de prestamos
	 *
	 * @param transacion $transaction
	 * @param ActiveRecord $record
	 * @return boolean
	 */
	public function beforeInsert($transaction, $record){
		//Validacion
		if($record==false || $record->getSociosId()<=0){
			$transaction->rollback('Debe ingresar el socio');
		}
		$prestamosSocios = EntityManager::get('PrestamosSocios')->setTransaction($transaction)->findFirst(array('conditions'=>'socios_id='.$record->getSociosId().' AND estado="D"'));
		if($prestamosSocios!=false){
			//$transaction->rollback('Existe un prestamo activo de ese socio actualmente');
		}
		$record->setFechaPrestamo(date('Y-m-d'));
		$record->setEstado('D');
		
		return true;
	}

	private function _makeAmortizacion($transaction, $record)
	{
		Core::importFromLibrary('Hfos/Socios','SociosFactura.php');
		
		$config = array(
			'prestamosSociosId'	=> $record->getId(),
			'valorFinanciacion'	=> $record->getValorFinanciacion(),
			'fechaCompra'		=> $record->getFechaInicio()->getDate(),
			'plazoMeses'		=> $record->getNumeroCuotas(),
			'tasaMesVencido'	=> $record->getInteresCorriente()
		);
		$sociosFactura = new SociosFactura();
		$sociosFactura->crearAmortizacion($config);
		
		#creamos movimiento
		//$sociosFactura->makePrestamosAura($transaction, $record);
		
	}

	/**
	 * Metodo que se ejecuta cuando ya se creo el registro de prestamos
	 *
	 * @param transacion $transaction
	 * @param ActiveRecord $record
	 * @return boolean
	 */
	public function afterInsert($transaction, $record){
		try
		{
			$this->_makeAmortizacion($transaction, $record);
			return true;
		}
		catch(Exception $e) {
			$this->appendMessage($e->getMessage());
			return false;
		}
	}

	/**
	 * Metodo que se ejecuta cuando ya se creo el registro de prestamos
	 *
	 * @param transacion $transaction
	 * @param ActiveRecord $record
	 * @return boolean
	 */
	public function afterUpdate($transaction, $record){
		try 
		{
			$this->_makeAmortizacion($transaction, $record);
			return true;
		}
		catch(Exception $e) {
			$this->appendMessage($e->getMessage());
			return false;
		}
	}

	public function estadoCuentaAction(){
		$this->setResponse('view');
		
		$prestamoId = $this->getPostParam('id', 'int');

		if ($prestamoId) {
			$prestamosSocios = $this->PrestamosSocios->findFirst($prestamoId);
			
			if ($prestamosSocios==false) {
				echo Flash::error('El prestamo no existe');
				return false;
			}

			$comprobFinanciacion = Settings::get('comprob_financiacion', 'SO');
			if (!$comprobFinanciacion) {
				throw new Exception('Configuración: No se ha configurado el comprobante de financiación');
			}

			//revisar estado de amortizacion
			$transaction = TransactionManager::getUserTransaction();
			Core::importFromLibrary('Hfos/Socios','SociosFactura.php');
			$sociosFactura = new SociosFactura();
			$sociosFactura->revisarConvenios($prestamosSocios); 
			
			$sociosId = $prestamosSocios->getSociosId();
			$socios = BackCacher::getSocios($sociosId);
			if ($socios) {
				$nit = $socios->getIdentificacion();
				$moviObj = $this->Movi->setTransaction($transaction)->find(array("cuenta='{$prestamosSocios->getCuenta()}' AND nit='$nit' AND deb_cre='C'",'columns'=>'deb_cre,valor,fecha,comprob,numero'));
				$this->setParamToView('moviObj', $moviObj);
			}
			$this->setParamToView('valorInicial', $prestamosSocios->getValorFinanciacion());
			$this->setParamToView('prestamoId', $prestamoId);
			
			$transaction->commit();

		}
	}

	public function beforeEdit($record){
		$this->setParamToView('prestamosSociosId', $record->getId());
	}

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	/**
	* Action oculto que actualiza el estado de las amortizaciones de los convenios
	*/
	public function checkAllConveniosAction()
	{
		$this->setResponse('view');
		
		$prestamosSociosObj = $this->PrestamosSocios->find();
		
		foreach ($prestamosSociosObj as $prestamosSocios)
		{				
			
			//revisar estado de amortizacion
			$transaction = TransactionManager::getUserTransaction();
			
			Core::importFromLibrary('Hfos/Socios','SociosFactura.php');
			$sociosFactura = new SociosFactura();
			$sociosFactura->revisarConvenios($prestamosSocios); 
			
			$transaction->commit();
		}
	}
}
